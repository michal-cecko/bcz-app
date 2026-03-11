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
        $team = $payment->team;

        if (! $team->bank_account_iban) {
            return null;
        }

        return self::payBySquare(
            iban: $team->bank_account_iban,
            amount: (float) $payment->amount,
            currency: $payment->currency,
            variableSymbol: $payment->variable_symbol ?? '',
            recipientName: $team->bank_account_name ?? '',
        );
    }

    /**
     * Generate Czech QR Platba from a Payment model.
     */
    public function generateQrPlatbaForPayment(Payment $payment): ?string
    {
        $team = $payment->team;

        if (! $team->bank_account_iban) {
            return null;
        }

        return self::qrPlatba(
            iban: $team->bank_account_iban,
            amount: (float) $payment->amount,
            currency: $payment->currency,
            variableSymbol: $payment->variable_symbol ?? '',
            recipientName: $team->bank_account_name ?? '',
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
    ): ?string {
        $iban = str_replace(' ', '', $iban);

        if (empty($iban)) {
            return null;
        }

        // Pay by Square tab-separated data format
        $data = implode("\t", [
            '',                                          // Invoice ID
            '1',                                         // Payments count
            '1',                                         // Payment type (regular)
            $amount !== null ? $amount : '',              // Amount (empty = open)
            $currency,                                   // Currency
            '',                                          // Due date
            $variableSymbol,                             // Variable symbol
            '',                                          // Constant symbol
            '',                                          // Specific symbol
            '',                                          // Note
            '1',                                         // Bank accounts count
            $iban,                                       // IBAN
            '',                                          // BIC/SWIFT
            '0',                                         // Standing order
            '0',                                         // Direct debit
            $recipientName,                              // Beneficiary name
            '',                                          // Beneficiary address 1
            '',                                          // Beneficiary address 2
        ]);

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
     * Generate Czech QR Platba (Short Payment Descriptor) QR code from raw data.
     * Format: SPD*1.0*ACC:IBAN*AM:amount*CC:currency*X-VS:variableSymbol*RN:recipientName
     *
     * @param  float|null  $amount  Optional — omit for donation QR without fixed amount.
     */
    /**
     * @param  string  $iban  IBAN or Czech account number (e.g. "1503666677/5500").
     */
    public static function qrPlatba(
        string $iban,
        ?float $amount = null,
        string $currency = 'CZK',
        string $variableSymbol = '',
        string $recipientName = '',
    ): ?string {
        $iban = str_replace(' ', '', $iban);

        if (empty($iban)) {
            return null;
        }

        // Convert Czech account number to IBAN if needed
        if (str_contains($iban, '/')) {
            $iban = self::czechAccountToIban($iban);
        }

        $parts = [
            'SPD*1.0',
            'ACC:'.$iban,
        ];

        if ($amount !== null) {
            $parts[] = 'AM:'.number_format($amount, 2, '.', '');
        }

        $parts[] = 'CC:'.$currency;

        if ($variableSymbol) {
            $parts[] = 'X-VS:'.$variableSymbol;
        }

        if ($recipientName) {
            $parts[] = 'RN:'.mb_substr($recipientName, 0, 35);
        }

        return self::buildQrPng(implode('*', $parts));
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
