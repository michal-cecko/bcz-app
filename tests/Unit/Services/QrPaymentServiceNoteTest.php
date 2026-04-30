<?php

namespace Tests\Unit\Services;

use App\Services\QrPaymentService;
use PHPUnit\Framework\TestCase;

class QrPaymentServiceNoteTest extends TestCase
{
    public function test_qr_platba_payload_appends_msg_when_note_is_provided(): void
    {
        $payload = QrPaymentService::qrPlatbaPayload(
            iban: 'CZ6508000000192000145399',
            amount: 100.0,
            currency: 'CZK',
            variableSymbol: '12345',
            recipientName: 'BCZ%20Club',
            note: 'Ďakujeme',
        );

        // SPAYD spec requires non-safe chars to be percent-encoded; "Ď" (UTF-8 0xC4 0x8E) -> %C4%8E
        $this->assertStringContainsString('*MSG:%C4%8Eakujeme', $payload);
    }

    public function test_qr_platba_payload_omits_msg_when_note_is_null(): void
    {
        $payload = QrPaymentService::qrPlatbaPayload(
            iban: 'CZ6508000000192000145399',
            amount: 100.0,
            note: null,
        );

        $this->assertStringNotContainsString('MSG:', $payload);
    }

    public function test_qr_platba_payload_omits_msg_when_note_is_empty_string(): void
    {
        $payload = QrPaymentService::qrPlatbaPayload(
            iban: 'CZ6508000000192000145399',
            amount: 100.0,
            note: '',
        );

        $this->assertStringNotContainsString('MSG:', $payload);
    }

    public function test_qr_platba_payload_truncates_msg_to_60_utf8_chars_before_encoding(): void
    {
        $payload = QrPaymentService::qrPlatbaPayload(
            iban: 'CZ6508000000192000145399',
            amount: 100.0,
            note: str_repeat('á', 80),
        );

        preg_match('/\*MSG:([^*]+)/u', $payload, $match);

        $this->assertNotNull($match[1] ?? null, 'MSG field missing');
        // "á" (UTF-8 0xC3 0xA1) → "%C3%A1" (6 chars) per SPAYD percent-encoding rule.
        // 60 source chars × 6 encoded chars = 360.
        $this->assertSame(60 * 6, mb_strlen($match[1]));
    }

    public function test_pay_by_square_raw_data_includes_note_at_position_10(): void
    {
        $raw = QrPaymentService::payBySquareRawData(
            iban: 'SK6807200002891987426353',
            amount: 50.0,
            currency: 'EUR',
            variableSymbol: '99999',
            recipientName: 'BCZ Club',
            note: 'Hello world',
        );

        $fields = explode("\t", $raw);

        $this->assertSame('Hello world', $fields[9]);
    }

    public function test_pay_by_square_raw_data_truncates_note_to_140_chars(): void
    {
        $raw = QrPaymentService::payBySquareRawData(
            iban: 'SK6807200002891987426353',
            amount: 50.0,
            note: str_repeat('a', 200),
        );

        $fields = explode("\t", $raw);

        $this->assertSame(140, mb_strlen($fields[9]));
    }

    public function test_pay_by_square_raw_data_note_defaults_to_empty_string(): void
    {
        $raw = QrPaymentService::payBySquareRawData(
            iban: 'SK6807200002891987426353',
            amount: 50.0,
            note: null,
        );

        $fields = explode("\t", $raw);

        $this->assertSame('', $fields[9]);
    }

    public function test_qr_platba_full_pipeline_returns_base64_png_with_note(): void
    {
        $output = QrPaymentService::qrPlatba(
            iban: 'CZ6508000000192000145399',
            amount: 100.0,
            currency: 'CZK',
            variableSymbol: '12345',
            recipientName: 'BCZ Club',
            note: 'Dar',
        );

        $this->assertNotNull($output);
        $this->assertNotEmpty($output);
        $this->assertNotFalse(base64_decode($output, true));
    }

    public function test_epc_qr_payload_contains_required_lines(): void
    {
        $payload = QrPaymentService::epcQrPayload(
            iban: 'SK7111000000001234567890',
            amount: 12.50,
            currency: 'EUR',
            beneficiaryName: 'BCZ Test',
            bic: 'TATRSKBX',
            remittanceText: 'Test SEPA',
        );

        $lines = explode("\n", $payload);

        $this->assertSame('BCD', $lines[0]);                            // Service tag
        $this->assertSame('001', $lines[1]);                            // Version with BIC
        $this->assertSame('1', $lines[2]);                              // UTF-8 charset
        $this->assertSame('SCT', $lines[3]);                            // SEPA Credit Transfer
        $this->assertSame('TATRSKBX', $lines[4]);                       // BIC
        $this->assertSame('BCZ Test', $lines[5]);                       // Beneficiary
        $this->assertSame('SK7111000000001234567890', $lines[6]);       // IBAN
        $this->assertSame('EUR12.50', $lines[7]);                       // Amount
    }

    public function test_epc_qr_payload_uses_version_002_when_bic_missing(): void
    {
        $payload = QrPaymentService::epcQrPayload(
            iban: 'SK7111000000001234567890',
            amount: 10.0,
        );

        $this->assertStringStartsWith("BCD\n002\n", $payload);
    }

    public function test_qr_platba_emits_spayd_for_cz_iban(): void
    {
        $base64 = QrPaymentService::qrPlatba(
            iban: 'CZ6508000000192000145399',
            amount: 100.0,
            currency: 'CZK',
            variableSymbol: '12345',
        );

        $this->assertNotNull($base64);
        // Sanity: SPAYD payload starts with "SPD*1.0" — verify via the payload helper.
        $payload = QrPaymentService::qrPlatbaPayload(
            iban: 'CZ6508000000192000145399',
            amount: 100.0,
            currency: 'CZK',
            variableSymbol: '12345',
        );
        $this->assertStringStartsWith('SPD*1.0', $payload);
    }

    public function test_qr_platba_auto_switches_to_epc_for_non_cz_iban(): void
    {
        // Calling qrPlatba() with a SK IBAN should yield an EPC QR (BCD payload),
        // not SPAYD — because CZ banking apps reject SPAYD with non-CZ IBANs.
        $base64 = QrPaymentService::qrPlatba(
            iban: 'SK7111000000001234567890',
            amount: 12.50,
            currency: 'EUR',
            recipientName: 'BCZ Test',
            note: 'Test SEPA',
        );

        $this->assertNotNull($base64);
        $this->assertNotFalse(base64_decode($base64, true));

        // The auto-switch should produce the same PNG as a direct epcQr() call with
        // mapped parameters.
        $direct = QrPaymentService::epcQr(
            iban: 'SK7111000000001234567890',
            amount: 12.50,
            currency: 'EUR',
            beneficiaryName: 'BCZ Test',
            remittanceText: 'Test SEPA',
        );

        $this->assertSame($direct, $base64);
    }
}
