<?php

namespace App\Services;

use App\Models\Payment;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;

class QrPaymentService
{
    /**
     * Generate a SPAYD ("CZ Platba") QR code from a Payment model. Read by SK,
     * CZ and most EU bank apps; pre-fills the variable symbol via three layers:
     *   - native SPAYD X-VS tag,
     *   - ISO 11649 RF structured creditor reference,
     *   - "/VS{vs}" prefix in MSG (Czech banking convention for SEPA mode).
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

        $msg = self::buildMessage($note, $variableSymbol, $specificSymbol, $constantSymbol);
        if ($msg !== '') {
            $parts[] = 'MSG:'.self::spaydEncode($msg);
        }

        return implode('*', $parts);
    }

    /**
     * Compose the SPAYD MSG content. The /VS/SS/KS prefix lets SEPA-mode bank
     * apps fill the "payment reference" field even when they ignore X-VS.
     */
    private static function buildMessage(
        ?string $note,
        string $variableSymbol,
        string $specificSymbol,
        string $constantSymbol,
    ): string {
        $prefixSegments = [];
        if ($variableSymbol !== '') {
            $prefixSegments[] = '/VS'.$variableSymbol;
        }
        if ($specificSymbol !== '') {
            $prefixSegments[] = '/SS'.$specificSymbol;
        }
        if ($constantSymbol !== '') {
            $prefixSegments[] = '/KS'.$constantSymbol;
        }

        $prefix = implode('', $prefixSegments);
        $note = $note ?? '';

        if ($prefix === '' && $note === '') {
            return '';
        }

        if ($prefix === '') {
            return mb_substr($note, 0, 60);
        }

        if (mb_strlen($prefix) >= 60) {
            return mb_substr($prefix, 0, 60);
        }

        if ($note === '') {
            return $prefix;
        }

        $remaining = 60 - mb_strlen($prefix) - 1; // -1 for the separating space

        if ($remaining <= 0) {
            return $prefix;
        }

        return $prefix.' '.mb_substr($note, 0, $remaining);
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
