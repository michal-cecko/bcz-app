<?php

namespace App\Services;

use App\Models\Payment;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;

class QrPaymentService
{
    public function generateQrCode(Payment $payment, string $country = 'SK'): ?string
    {
        return match (strtoupper($country)) {
            'SK' => $this->generatePayBySquare($payment),
            'CZ' => $this->generateQrPlatba($payment),
            default => null,
        };
    }

    /**
     * Generate Slovak Pay by Square QR code.
     * Format: binary-encoded payment data → lzma compressed → base64 → QR code
     */
    public function generatePayBySquare(Payment $payment): ?string
    {
        $team = $payment->team;

        if (! $team->bank_account_iban) {
            return null;
        }

        $iban = str_replace(' ', '', $team->bank_account_iban);
        $amount = number_format((float) $payment->amount, 2, '.', '');
        $currency = $payment->currency;
        $variableSymbol = $payment->variable_symbol ?? '';
        $recipientName = $team->bank_account_name ?? '';

        // Pay by Square data format (tab-separated)
        $data = implode("\t", [
            '',           // Invoice ID
            '1',          // Payments count
            '1',          // Payment options (regular)
            $amount,      // Amount
            $currency,    // Currency
            '',           // Payment due date
            $variableSymbol, // Variable symbol
            '',           // Constant symbol
            '',           // Specific symbol
            '',           // Note
            '1',          // Bank accounts count
            $iban,        // IBAN
            '',           // BIC/SWIFT
            '0',          // Standing order
            '0',          // Direct debit
            $recipientName, // Beneficiary name
            '',           // Beneficiary address line 1
            '',           // Beneficiary address line 2
        ]);

        // Compute CRC32 and prepend
        $crc = pack('V', crc32($data));
        $dataWithCrc = $crc.$data;

        // Try to compress with XZ (LZMA2) if available, otherwise use raw data
        $compressed = $this->lzmaCompress($dataWithCrc);
        if ($compressed === null) {
            // Fallback: use raw base64 if LZMA is not available
            $qrData = base64_encode($dataWithCrc);
        } else {
            // Prepend header: 2 bytes for data length (little-endian)
            $header = pack('v', strlen($dataWithCrc));
            $qrData = $this->byteStringToBinaryString($header.$compressed);
        }

        return $this->renderQrCode($qrData);
    }

    /**
     * Generate Czech QR Platba (Short Payment Descriptor) QR code.
     * Format: SPD*1.0*ACC:IBAN*AM:amount*CC:currency*X-VS:variableSymbol*MSG:message
     */
    public function generateQrPlatba(Payment $payment): ?string
    {
        $team = $payment->team;

        if (! $team->bank_account_iban) {
            return null;
        }

        $iban = str_replace(' ', '', $team->bank_account_iban);
        $amount = number_format((float) $payment->amount, 2, '.', '');

        $parts = [
            'SPD*1.0',
            'ACC:'.$iban,
            'AM:'.$amount,
            'CC:'.$payment->currency,
        ];

        if ($payment->variable_symbol) {
            $parts[] = 'X-VS:'.$payment->variable_symbol;
        }

        if ($team->bank_account_name) {
            $parts[] = 'RN:'.$team->bank_account_name;
        }

        $spdString = implode('*', $parts);

        return $this->renderQrCode($spdString);
    }

    private function renderQrCode(string $data): string
    {
        $result = Builder::create()
            ->writer(new PngWriter)
            ->data($data)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::Medium)
            ->size(300)
            ->margin(10)
            ->build();

        return base64_encode($result->getString());
    }

    private function lzmaCompress(string $data): ?string
    {
        // Try using xz command-line tool
        $process = proc_open(
            'xz --format=raw --lzma2 -c',
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
     * Convert binary data to a binary string representation for Pay by Square.
     */
    private function byteStringToBinaryString(string $data): string
    {
        $binaryString = '';

        for ($i = 0; $i < strlen($data); $i++) {
            $binaryString .= str_pad(decbin(ord($data[$i])), 8, '0', STR_PAD_LEFT);
        }

        // Pad to multiple of 5 and convert to base32-like encoding
        $padding = (5 - (strlen($binaryString) % 5)) % 5;
        $binaryString .= str_repeat('0', $padding);

        $result = '';
        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUV';

        for ($i = 0; $i < strlen($binaryString); $i += 5) {
            $chunk = substr($binaryString, $i, 5);
            $index = bindec($chunk);
            $result .= $chars[$index];
        }

        return $result;
    }
}
