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
     * Compose the EPC remittanceText as a pure CZ-banking VS reference:
     *   "/VS{vs}[/SS{ss}][/KS{ks}]"
     *
     * CZ banking apps + Revolut parse this prefix to pre-fill the variable-
     * symbol input. The Poznámka is intentionally NOT appended here — adding
     * free-text after the slash directives breaks parsing in some apps.
     * Truncated to 140 UTF-8 chars (EPC spec limit).
     */
    private static function buildCzechSepaRemittance(
        string $variableSymbol,
        string $specificSymbol,
        string $constantSymbol,
    ): string {
        $segments = [];
        if ($variableSymbol !== '') {
            $segments[] = '/VS'.$variableSymbol;
        }
        if ($specificSymbol !== '') {
            $segments[] = '/SS'.$specificSymbol;
        }
        if ($constantSymbol !== '') {
            $segments[] = '/KS'.$constantSymbol;
        }

        return mb_substr(implode('', $segments), 0, 140);
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
