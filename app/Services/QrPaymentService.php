<?php

namespace App\Services;

use App\Models\Payment;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;

class QrPaymentService
{
    /**
     * Generate a payment QR for the given Payment model. Auto-switches format
     * by IBAN country, since CZ banking apps + Revolut reject SPAYD with
     * non-CZ IBANs (they expect a SEPA-style payload):
     *  - CZ IBAN → SPAYD / QR Platba (native domestic format, read by CZ/SK apps)
     *  - non-CZ IBAN → EPC QR (EPC069-12, the European SEPA standard,
     *    read by Revolut, CZ Raiffeisen, SK apps, and any EU SEPA app)
     *
     * In SPAYD, MSG carries only the raw "Poznámka" — VS goes in X-VS and (when
     * digits-only) ISO 11649 RF. In EPC, the same VS is exposed via the structured
     * remittanceReference field so receiving apps pre-fill the reference field.
     */
    public function generateQrForPayment(Payment $payment): ?string
    {
        $iban = $payment->payable?->getPayoutIban();

        if (! $iban) {
            return null;
        }

        return self::qrPlatba(
            iban: $iban,
            amount: (float) $payment->amount,
            currency: $payment->currency,
            variableSymbol: $payment->formattedVariableSymbol() ?? $payment->variable_symbol ?? '',
            recipientName: $payment->payable?->getPayoutRecipientName() ?? '',
            note: $payment->payable?->getQrPaymentNote(),
        );
    }

    /**
     * Generate a SPAYD QR readable by Czech, Slovak and EU banking apps.
     *
     * @param  string  $iban  IBAN or Czech account number (e.g. "1503666677/5500").
     * @param  float|null  $amount  Optional — omit for open-amount donation QR.
     */
    public static function qrPlatba(
        string $iban,
        ?float $amount = null,
        string $currency = 'EUR',
        string $variableSymbol = '',
        string $recipientName = '',
        ?string $note = null,
        ?string $bic = null,
        string $specificSymbol = '',
        string $constantSymbol = '',
    ): ?string {
        $normalizedIban = strtoupper(str_replace(' ', '', $iban));
        $isCzAccount = str_contains($normalizedIban, '/') || str_starts_with($normalizedIban, 'CZ');

        // Slovak IBANs → Pay by Square, the SK-native format. Unlike EPC and
        // SPAYD it has dedicated variable-symbol, structured-reference AND note
        // fields, so the VS goes into the reference ("/VS{vs}/SS/KS") while the
        // configured payment note keeps its own field instead of being crowded
        // out of a single remittance field.
        if (str_starts_with($normalizedIban, 'SK')) {
            return self::payBySquare(
                iban: $normalizedIban,
                amount: $amount,
                currency: $currency,
                variableSymbol: $variableSymbol,
                recipientName: $recipientName,
                note: $note,
            );
        }

        // Non-CZ IBANs (incl. SK teams collecting via Revolut etc.): emit EPC
        // QR, the European SEPA standard. CZ apps + Revolut reject SPAYD when
        // the IBAN isn't Czech, but read EPC reliably via the same scanner.
        //
        // VS/SS/KS go into the unstructured remittance text as the Czech
        // banking convention "/VS{vs}[/SS{ss}][/KS{ks}]" — CZ bank apps parse
        // this prefix and pre-fill the variable-symbol input. We avoid the
        // structured ISO 11649 RF reference because apps display it verbatim
        // ("RF59…") instead of extracting the underlying VS digits.
        if (! $isCzAccount && $amount !== null) {
            $remittanceText = self::buildCzechSepaRemittance($variableSymbol, $specificSymbol, $constantSymbol);

            return self::epcQr(
                iban: $normalizedIban,
                amount: $amount,
                currency: $currency,
                beneficiaryName: $recipientName,
                bic: $bic,
                remittanceText: $remittanceText !== '' ? $remittanceText : null,
            );
        }

        $payload = self::qrPlatbaPayload(
            $iban,
            $amount,
            $currency,
            $variableSymbol,
            $recipientName,
            $note,
            $bic,
            $specificSymbol,
            $constantSymbol,
        );

        if ($payload === null) {
            return null;
        }

        return self::buildQrPng($payload);
    }

    /**
     * Generate a Slovak Pay by Square QR code.
     * Implements the Pay by Square standard: tab-separated data → CRC32 →
     * LZMA1 compress (via system xz) → base32 encode. Pay by Square carries the
     * variable symbol and the note in separate, dedicated fields.
     *
     * @param  float|null  $amount  Optional — omit for an open-amount donation QR.
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

        // CRC32 checksum prepended to data.
        $crc = strrev(hash('crc32b', $data, true));
        $dataWithCrc = $crc.$data;

        // LZMA1 compression via system xz.
        $compressed = self::lzmaCompress($dataWithCrc);
        if ($compressed === null) {
            return null;
        }

        // Header: 2 zero bytes + 2 bytes data length (little-endian) + compressed data.
        $payload = "\x00\x00".pack('v', strlen($dataWithCrc)).$compressed;

        // Convert to base32-like encoding per Pay by Square spec.
        $qrData = self::binaryToBase32($payload);

        return self::buildQrPng($qrData);
    }

    /**
     * Build the raw tab-separated Pay by Square data string, following the
     * bysquare Table 15 field order exactly. Two consecutive fields are easy
     * to confuse — originatorsReferenceInformation sits *between*
     * specificSymbol and paymentNote; omitting it shifts every later field
     * (note, IBAN, beneficiary) by one and makes banking apps misread the QR.
     *
     * The variable symbol is emitted two ways SK banking apps understand: the
     * dedicated VariableSymbol field (prefills "variabilný symbol" for a
     * domestic transfer) and the OriginatorsReferenceInformation field, which
     * carries the SEPA structured payment reference "/VS{vs}/SS/KS". The note
     * keeps its own PaymentNote field, so the VS never crowds into the note.
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
        $reference = self::buildCzechSepaRemittance($variableSymbol, '', '');

        return implode("\t", [
            '',                                          // Invoice ID
            '1',                                         // Payments count
            '1',                                         // Payment type (regular)
            $amount !== null ? $amount : '',             // Amount (empty = open)
            $currency,                                   // Currency
            '',                                          // Due date
            $variableSymbol,                             // Variable symbol
            '',                                          // Constant symbol
            '',                                          // Specific symbol
            $reference,                                  // Originator's reference info ("/VS{vs}/SS/KS")
            $noteField,                                  // Payment note
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

    /**
     * Generate an EPC QR Code (EPC069-12, also known as GiroCode) for a SEPA
     * Credit Transfer. This is the European standard for cross-border SEPA QR
     * payments — read by most EU banking apps (incl. Revolut and Raiffeisen CZ)
     * through the same scanner that reads SPAYD/QR Platba.
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
     * Build the raw EPC QR payload (LF-separated lines per EPC069-12 spec).
     * Per spec, EPC permits either a structured remittance reference (ISO
     * 11649) OR an unstructured remittance text — not both. When VS is set,
     * we prefer the structured reference so receiving apps pre-fill the
     * payment reference field.
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
     * Build the raw SPAYD payload string that qrPlatba() encodes into the QR.
     *
     * MSG capacity is 60 UTF-8 chars total. When VS/SS/KS are present, a
     * "/VS{vs}[/SS{ss}][/KS{ks}]" prefix is prepended to the note before
     * truncation — the prefix is preserved, only the note tail is cut.
     */
    public static function qrPlatbaPayload(
        string $iban,
        ?float $amount = null,
        string $currency = 'EUR',
        string $variableSymbol = '',
        string $recipientName = '',
        ?string $note = null,
        ?string $bic = null,
        string $specificSymbol = '',
        string $constantSymbol = '',
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

        if ($variableSymbol !== '') {
            $parts[] = 'X-VS:'.self::spaydEncode($variableSymbol);
        }

        if ($specificSymbol !== '') {
            $parts[] = 'X-SS:'.self::spaydEncode($specificSymbol);
        }

        if ($constantSymbol !== '') {
            $parts[] = 'X-KS:'.self::spaydEncode($constantSymbol);
        }

        // ISO 11649 structured creditor reference — used by SEPA-strict apps
        // (e.g. CZ bank app paying in EUR which interprets SPAYD as SEPA).
        if ($variableSymbol !== '' && ctype_digit($variableSymbol)) {
            $parts[] = 'RF:'.self::iso11649Reference($variableSymbol);
        }

        if ($recipientName !== '') {
            $parts[] = 'RN:'.self::spaydEncode(mb_substr($recipientName, 0, 35));
        }

        // MSG carries only the user-supplied "Poznámka". VS/SS/KS go in their
        // dedicated SPAYD tags (X-VS / X-SS / X-KS) and the ISO 11649 RF
        // structured reference — never in MSG.
        if ($note !== null && $note !== '') {
            $parts[] = 'MSG:'.self::spaydEncode(mb_substr($note, 0, 60));
        }

        return implode('*', $parts);
    }

    /**
     * Compose the EPC remittanceText as a CZ-banking VS reference:
     *   "/VS{vs}/SS{ss}/KS{ks}"
     *
     * Always emits all three segments (with empty SS / KS when not supplied)
     * — Czech banking apps and Revolut detect this fixed shape to parse the
     * variable symbol; an isolated "/VS…" without trailing /SS/KS slips
     * through some parsers and ends up displayed as plain message text
     * instead of populating the VS field.
     *
     * Returns "" when no VS/SS/KS at all (no slashes emitted then).
     * Truncated to 140 UTF-8 chars (EPC spec limit).
     */
    private static function buildCzechSepaRemittance(
        string $variableSymbol,
        string $specificSymbol,
        string $constantSymbol,
    ): string {
        if ($variableSymbol === '' && $specificSymbol === '' && $constantSymbol === '') {
            return '';
        }

        $text = '/VS'.$variableSymbol.'/SS'.$specificSymbol.'/KS'.$constantSymbol;

        return mb_substr($text, 0, 140);
    }

    /**
     * Compute an ISO 11649 RF{checksum}{reference} structured creditor
     * reference. Algorithm: append "RF00" to the reference, convert letters
     * (A=10..Z=35), take mod 97, subtract from 98 → 2-digit checksum.
     */
    public static function iso11649Reference(string $reference): string
    {
        $reference = strtoupper(preg_replace('/\s+/', '', $reference) ?? '');

        $expanded = '';
        foreach (str_split($reference.'RF00') as $char) {
            if (ctype_digit($char)) {
                $expanded .= $char;
            } elseif (ctype_alpha($char)) {
                $expanded .= (string) (ord($char) - ord('A') + 10);
            }
        }

        $remainder = bcmod($expanded, '97');
        $check = str_pad((string) (98 - (int) $remainder), 2, '0', STR_PAD_LEFT);

        return 'RF'.$check.$reference;
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
     * LZMA1 compression using the system xz binary (required for Pay by Square).
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
     * Convert binary data to base32-like encoding per the Pay by Square spec.
     */
    private static function binaryToBase32(string $data): string
    {
        $hex = bin2hex($data);
        $binary = '';

        for ($i = 0, $len = strlen($hex); $i < $len; $i++) {
            $binary .= str_pad(base_convert($hex[$i], 16, 2), 4, '0', STR_PAD_LEFT);
        }

        // Pad to multiple of 5 bits.
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
