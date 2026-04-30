<?php

namespace App\Services;

use App\Models\Payment;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;

class QrPaymentService
{
    public function generateQrCode(Payment $payment, string $country = 'SK'): ?string
    {
        return match (strtoupper($country)) {
            'SK' => $this->generatePayBySquareForPayment($payment),
            'CZ' => $this->generateQrPlatbaForPayment($payment),
            default => null,
        };
    }

    /**
     * Generate Slovak Pay by Square QR code from a Payment model.
     */
    public function generatePayBySquareForPayment(Payment $payment): ?string
    {
        $iban = $payment->payable?->getPayoutIban();

        if (! $iban) {
            return null;
        }

        return self::payBySquare(
            iban: $iban,
            amount: (float) $payment->amount,
            currency: $payment->currency,
            variableSymbol: $payment->variable_symbol ?? '',
            recipientName: $payment->payable?->getPayoutRecipientName() ?? '',
            note: $payment->payable?->getQrPaymentNote(),
        );
    }

    /**
     * Generate Czech QR Platba from a Payment model.
     */
    public function generateQrPlatbaForPayment(Payment $payment): ?string
    {
        $iban = $payment->payable?->getPayoutIban();

        if (! $iban) {
            return null;
        }

        return self::qrPlatba(
            iban: $iban,
            amount: (float) $payment->amount,
            currency: $payment->currency,
            variableSymbol: $payment->variable_symbol ?? '',
            recipientName: $payment->payable?->getPayoutRecipientName() ?? '',
            note: $payment->payable?->getQrPaymentNote(),
        );
    }

    /**
     * Generate Slovak Pay by Square QR code from raw data.
     * Implements the Pay by Square standard: tab-separated data → CRC32 → LZMA1 compress → base32 encode.
     *
     * @param  float|null  $amount  Optional — omit for donation QR without fixed amount.
     */
    public static function payBySquare(
        string $iban,
        ?float $amount = null,
        string $currency = 'EUR',
        string $variableSymbol = '',
        string $recipientName = '',
        ?string $note = null,
    ): ?string {
        $iban = str_replace(' ', '', $iban);

        if (empty($iban)) {
            return null;
        }

        $data = self::payBySquareRawData($iban, $amount, $currency, $variableSymbol, $recipientName, $note);

        // CRC32 checksum prepended to data
        $crc = strrev(hash('crc32b', $data, true));
        $dataWithCrc = $crc.$data;

        // LZMA1 compression via system xz
        $compressed = self::lzmaCompress($dataWithCrc);
        if ($compressed === null) {
            return null;
        }

        // Header: 2 zero bytes + 2 bytes data length (little-endian) + compressed data
        $payload = "\x00\x00".pack('v', strlen($dataWithCrc)).$compressed;

        // Convert to base32-like encoding per Pay by Square spec
        $qrData = self::binaryToBase32($payload);

        return self::buildQrPng($qrData);
    }

    /**
     * Generate a QR readable by Czech (and EU) banking apps.
     *
     * Auto-switches by IBAN country, since CZ bank apps read both formats
     * through the same scanner but reject SPAYD with non-CZ IBANs:
     *  - CZ IBAN → SPAYD / QR Platba (native domestic format)
     *  - non-CZ IBAN → EPC QR (EPC069-12, the European SEPA standard)
     *
     * @param  string  $iban  IBAN or Czech account number (e.g. "1503666677/5500").
     * @param  float|null  $amount  Optional — omit for open-amount donation QR (SPAYD only).
     */
    public static function qrPlatba(
        string $iban,
        ?float $amount = null,
        string $currency = 'CZK',
        string $variableSymbol = '',
        string $recipientName = '',
        ?string $note = null,
        ?string $bic = null,
    ): ?string {
        $normalizedIban = strtoupper(str_replace(' ', '', $iban));
        $isCzAccount = str_contains($normalizedIban, '/') || str_starts_with($normalizedIban, 'CZ');

        if (! $isCzAccount && $amount !== null) {
            return self::epcQr(
                iban: $normalizedIban,
                amount: $amount,
                currency: $currency,
                beneficiaryName: $recipientName,
                bic: $bic,
                remittanceText: $note,
            );
        }

        $payload = self::qrPlatbaPayload($iban, $amount, $currency, $variableSymbol, $recipientName, $note, $bic);

        if ($payload === null) {
            return null;
        }

        return self::buildQrPng($payload);
    }

    /**
     * Build the raw SPAYD payload string that qrPlatba() encodes into the QR.
     * Extracted for testability.
     */
    public static function qrPlatbaPayload(
        string $iban,
        ?float $amount = null,
        string $currency = 'CZK',
        string $variableSymbol = '',
        string $recipientName = '',
        ?string $note = null,
        ?string $bic = null,
    ): ?string {
        $iban = str_replace(' ', '', $iban);

        if (empty($iban)) {
            return null;
        }

        if (str_contains($iban, '/')) {
            $iban = self::czechAccountToIban($iban);
        }

        $acc = $iban;
        if ($bic !== null && $bic !== '') {
            $acc .= '+'.str_replace(' ', '', $bic);
        }

        $parts = [
            'SPD*1.0',
            'ACC:'.$acc,
        ];

        if ($amount !== null) {
            $parts[] = 'AM:'.number_format($amount, 2, '.', '');
        }

        $parts[] = 'CC:'.$currency;

        if ($variableSymbol) {
            $parts[] = 'X-VS:'.self::spaydEncode($variableSymbol);
        }

        if ($recipientName) {
            $parts[] = 'RN:'.self::spaydEncode(mb_substr($recipientName, 0, 35));
        }

        if ($note !== null && $note !== '') {
            $parts[] = 'MSG:'.self::spaydEncode(mb_substr($note, 0, 60));
        }

        return implode('*', $parts);
    }

    /**
     * Generate an EPC QR Code (EPC069-12, also known as GiroCode) for a SEPA
     * Credit Transfer. This is the European standard for cross-border SEPA QR
     * payments — read by most EU banking apps (incl. Raiffeisen CZ) through
     * the same scanner that reads SPAYD/QR Platba.
     *
     * Use this when the recipient IBAN is not a domestic CZ account but the
     * payer is in CZ (or any SEPA country) — SPAYD won't work for that case.
     */
    public static function epcQr(
        string $iban,
        float $amount,
        string $currency = 'EUR',
        string $beneficiaryName = '',
        ?string $bic = null,
        ?string $remittanceText = null,
        ?string $remittanceReference = null,
    ): ?string {
        $payload = self::epcQrPayload($iban, $amount, $currency, $beneficiaryName, $bic, $remittanceText, $remittanceReference);

        if ($payload === null) {
            return null;
        }

        return self::buildQrPng($payload);
    }

    /**
     * Build the raw EPC QR payload (12 LF-separated lines per spec).
     * Total max 331 bytes; field length limits enforced via mb_substr.
     */
    public static function epcQrPayload(
        string $iban,
        float $amount,
        string $currency = 'EUR',
        string $beneficiaryName = '',
        ?string $bic = null,
        ?string $remittanceText = null,
        ?string $remittanceReference = null,
    ): ?string {
        $iban = strtoupper(str_replace(' ', '', $iban));
        if ($iban === '') {
            return null;
        }

        $bic = $bic !== null ? strtoupper(str_replace(' ', '', $bic)) : '';
        $name = mb_substr($beneficiaryName, 0, 70);

        // EPC permits either structured reference OR unstructured text, not both.
        $reference = $remittanceReference !== null ? mb_substr($remittanceReference, 0, 25) : '';
        $text = ($remittanceText !== null && $reference === '') ? mb_substr($remittanceText, 0, 140) : '';

        // Version 002 makes BIC optional; 001 requires it. We always include BIC if provided.
        $version = $bic === '' ? '002' : '001';

        $lines = [
            'BCD',                                                 // Service tag
            $version,                                              // Version
            '1',                                                   // Charset 1 = UTF-8
            'SCT',                                                 // SEPA Credit Transfer
            $bic,                                                  // BIC
            $name,                                                 // Beneficiary name
            $iban,                                                 // IBAN
            $currency.number_format($amount, 2, '.', ''),          // e.g. EUR12.50
            '',                                                    // Purpose (4-char code, optional)
            $reference,                                            // Structured remittance reference
            $text,                                                 // Unstructured remittance text
        ];

        // Trim trailing empty lines (spec allows omitting unused trailing fields).
        while (count($lines) > 0 && end($lines) === '') {
            array_pop($lines);
        }

        return implode("\n", $lines);
    }

    /**
     * Percent-encode a SPAYD value. Per the Short Payment Descriptor spec,
     * only [A-Za-z0-9+\-./:] are safe inside values; everything else (including
     * space, asterisk, diacritics) must be %XX-encoded.
     */
    private static function spaydEncode(string $value): string
    {
        $encoded = preg_replace_callback(
            '/[^A-Za-z0-9+\-.\/:]/',
            fn (array $m): string => '%'.strtoupper(bin2hex($m[0])),
            $value,
        );

        return $encoded ?? $value;
    }

    /**
     * Build the raw tab-separated data string that payBySquare() compresses into the QR.
     * Extracted for testability.
     */
    public static function payBySquareRawData(
        string $iban,
        ?float $amount = null,
        string $currency = 'EUR',
        string $variableSymbol = '',
        string $recipientName = '',
        ?string $note = null,
    ): string {
        $noteField = mb_substr((string) ($note ?? ''), 0, 140);

        return implode("\t", [
            '',                                          // Invoice ID
            '1',                                         // Payments count
            '1',                                         // Payment type (regular)
            $amount !== null ? $amount : '',              // Amount (empty = open)
            $currency,                                   // Currency
            '',                                          // Due date
            $variableSymbol,                             // Variable symbol
            '',                                          // Constant symbol
            '',                                          // Specific symbol
            $noteField,                                  // Note
            '1',                                         // Bank accounts count
            $iban,                                       // IBAN
            '',                                          // BIC/SWIFT
            '0',                                         // Standing order
            '0',                                         // Direct debit
            $recipientName,                              // Beneficiary name
            '',                                          // Beneficiary address 1
            '',                                          // Beneficiary address 2
        ]);
    }

    private static function buildQrPng(string $data): string
    {
        $result = (new Builder(
            data: $data,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: 300,
            margin: 10,
        ))->build();

        return base64_encode($result->getString());
    }

    /**
     * LZMA1 compression using system xz binary (required for Pay by Square).
     */
    private static function lzmaCompress(string $data): ?string
    {
        $process = proc_open(
            "/usr/bin/xz '--format=raw' '--lzma1=lc=3,lp=0,pb=2,dict=128KiB' '-c' '-'",
            [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes,
        );

        if (! is_resource($process)) {
            return null;
        }

        fwrite($pipes[0], $data);
        fclose($pipes[0]);

        $compressed = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        return $exitCode === 0 ? $compressed : null;
    }

    /**
     * Convert binary data to base32-like encoding per Pay by Square specification.
     */
    private static function binaryToBase32(string $data): string
    {
        $hex = bin2hex($data);
        $binary = '';

        for ($i = 0, $len = strlen($hex); $i < $len; $i++) {
            $binary .= str_pad(base_convert($hex[$i], 16, 2), 4, '0', STR_PAD_LEFT);
        }

        // Pad to multiple of 5 bits
        $remainder = strlen($binary) % 5;
        if ($remainder > 0) {
            $binary .= str_repeat('0', 5 - $remainder);
        }

        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUV';
        $result = '';

        for ($i = 0, $len = strlen($binary) / 5; $i < $len; $i++) {
            $result .= $chars[bindec(substr($binary, $i * 5, 5))];
        }

        return $result;
    }

    /**
     * Convert Czech account number (e.g. "1503666677/5500") to IBAN.
     */
    private static function czechAccountToIban(string $accountNumber): string
    {
        $parts = explode('/', $accountNumber);
        $account = $parts[0] ?? '';
        $bankCode = $parts[1] ?? '';

        $prefix = '0';
        if (str_contains($account, '-')) {
            [$prefix, $account] = explode('-', $account);
        }

        $prefix = str_pad($prefix, 6, '0', STR_PAD_LEFT);
        $account = str_pad($account, 10, '0', STR_PAD_LEFT);

        $bban = $bankCode.$prefix.$account;
        $checkBase = $bban.'123500';
        $remainder = bcmod($checkBase, '97');
        $checkDigits = str_pad((string) (98 - (int) $remainder), 2, '0', STR_PAD_LEFT);

        return 'CZ'.$checkDigits.$bban;
    }
}
