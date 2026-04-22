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
            recipientName: 'BCZ Club',
            note: 'Ďakujeme',
        );

        $this->assertStringContainsString('*MSG:Ďakujeme', $payload);
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

    public function test_qr_platba_payload_truncates_msg_to_60_utf8_chars(): void
    {
        $payload = QrPaymentService::qrPlatbaPayload(
            iban: 'CZ6508000000192000145399',
            amount: 100.0,
            note: str_repeat('á', 80),
        );

        preg_match('/\*MSG:([^*]+)/u', $payload, $match);

        $this->assertNotNull($match[1] ?? null, 'MSG field missing');
        $this->assertSame(60, mb_strlen($match[1]));
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
}
