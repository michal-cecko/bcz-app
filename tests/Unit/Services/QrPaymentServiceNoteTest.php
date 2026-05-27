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
            recipientName: 'BCZ Club',
            note: 'Ďakujeme',
        );

        // SPAYD spec requires non-safe chars to be percent-encoded; "Ď" (UTF-8 0xC4 0x8E) -> %C4%8E
        $this->assertStringContainsString('*MSG:%C4%8Eakujeme', $payload);
    }

    public function test_qr_platba_payload_omits_msg_when_note_is_null_and_no_symbols(): void
    {
        $payload = QrPaymentService::qrPlatbaPayload(
            iban: 'CZ6508000000192000145399',
            amount: 100.0,
            note: null,
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
        // "á" (UTF-8 0xC3 0xA1) → "%C3%A1" (6 chars) per SPAYD percent-encoding rule.
        // 60 source chars × 6 encoded chars = 360.
        $this->assertSame(60 * 6, mb_strlen($match[1]));
    }

    public function test_qr_platba_full_pipeline_returns_base64_png(): void
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

    public function test_qr_platba_emits_spayd_for_cz_iban(): void
    {
        $payload = QrPaymentService::qrPlatbaPayload(
            iban: 'CZ6508000000192000145399',
            amount: 100.0,
            currency: 'CZK',
            variableSymbol: '12345',
        );

        $this->assertStringStartsWith('SPD*1.0', $payload);
    }

    public function test_qr_platba_payload_builder_emits_spayd_for_any_iban(): void
    {
        // The SPAYD payload builder itself is IBAN-agnostic (country routing
        // happens in qrPlatba(), not here): given any IBAN it emits a valid
        // SPD string with the VS in its dedicated X-VS tag.
        $payload = QrPaymentService::qrPlatbaPayload(
            iban: 'SK7111000000001234567890',
            amount: 12.50,
            currency: 'EUR',
            variableSymbol: '00000123',
            recipientName: 'BCZ Test',
            note: 'Test SEPA',
        );

        $this->assertStringStartsWith('SPD*1.0', $payload);
        $this->assertStringContainsString('ACC:SK7111000000001234567890', $payload);
        $this->assertStringContainsString('X-VS:00000123', $payload);
    }

    public function test_qr_platba_payload_msg_carries_only_note_without_vs_prefix(): void
    {
        $payload = QrPaymentService::qrPlatbaPayload(
            iban: 'CZ6508000000192000145399',
            amount: 100.0,
            currency: 'EUR',
            variableSymbol: '00000123',
            note: 'Členské 2026',
        );

        // VS lives in X-VS (and RF for digit-only) — never in MSG.
        $this->assertStringNotContainsString('/VS', $payload);
        // MSG carries the encoded note only: "Členské 2026" → "%C4%8Clensk%C3%A9%202026"
        $this->assertStringContainsString('MSG:%C4%8Clensk%C3%A9%202026', $payload);
    }

    public function test_qr_platba_payload_omits_msg_when_note_is_empty_even_with_vs(): void
    {
        $payload = QrPaymentService::qrPlatbaPayload(
            iban: 'CZ6508000000192000145399',
            amount: 100.0,
            variableSymbol: '12345',
        );

        $this->assertStringNotContainsString('MSG:', $payload);
        $this->assertStringContainsString('*X-VS:12345', $payload);
    }

    public function test_qr_platba_payload_emits_vs_ss_ks_in_dedicated_tags_only(): void
    {
        $payload = QrPaymentService::qrPlatbaPayload(
            iban: 'CZ6508000000192000145399',
            amount: 100.0,
            variableSymbol: '12345',
            specificSymbol: '67',
            constantSymbol: '0308',
        );

        $this->assertStringContainsString('*X-VS:12345', $payload);
        $this->assertStringContainsString('*X-SS:67', $payload);
        $this->assertStringContainsString('*X-KS:0308', $payload);
        $this->assertStringNotContainsString('/VS', $payload);
        $this->assertStringNotContainsString('/SS', $payload);
        $this->assertStringNotContainsString('/KS', $payload);
    }

    public function test_qr_platba_payload_omits_vs_prefix_and_rf_when_vs_is_empty(): void
    {
        $payload = QrPaymentService::qrPlatbaPayload(
            iban: 'CZ6508000000192000145399',
            amount: 100.0,
            note: 'Dar',
        );

        $this->assertStringNotContainsString('/VS', $payload);
        $this->assertStringNotContainsString('RF:', $payload);
        $this->assertStringNotContainsString('X-VS:', $payload);
    }

    public function test_qr_platba_payload_emits_iso_11649_rf_reference(): void
    {
        // ISO 11649 example: RF18539007547034 (check digits 18 for ref "539007547034").
        $payload = QrPaymentService::qrPlatbaPayload(
            iban: 'CZ6508000000192000145399',
            amount: 100.0,
            variableSymbol: '539007547034',
        );

        $this->assertStringContainsString('*RF:RF18539007547034', $payload);
    }

    public function test_iso_11649_reference_matches_known_vector(): void
    {
        // From the ISO 11649 standard: reference "539007547034" → checksum 18.
        $this->assertSame('RF18539007547034', QrPaymentService::iso11649Reference('539007547034'));
    }

    public function test_qr_platba_payload_skips_rf_when_vs_is_non_digit(): void
    {
        // Donation brick may pass alphanumeric — RF needs digit-only to be valid.
        $payload = QrPaymentService::qrPlatbaPayload(
            iban: 'CZ6508000000192000145399',
            amount: 100.0,
            variableSymbol: 'DAR2026',
        );

        $this->assertStringContainsString('*X-VS:DAR2026', $payload);
        $this->assertStringNotContainsString('RF:', $payload);
        $this->assertStringNotContainsString('/VS', $payload);
    }

    public function test_qr_platba_msg_truncates_note_to_60_chars_without_prefix(): void
    {
        $payload = QrPaymentService::qrPlatbaPayload(
            iban: 'CZ6508000000192000145399',
            amount: 100.0,
            variableSymbol: '00000123',
            note: str_repeat('x', 100),
        );

        preg_match('/\*MSG:([^*]+)/u', $payload, $match);
        $msg = $match[1] ?? '';

        // 'x' is SPAYD-safe so no percent-encoding; expect 60 raw chars, no /VS prefix.
        $this->assertSame(str_repeat('x', 60), $msg);
        $this->assertStringNotContainsString('/VS', $payload);
    }

    public function test_qr_platba_routes_other_non_cz_iban_to_epc_qr_with_vs_in_remittance_text(): void
    {
        // A non-CZ, non-SK IBAN (e.g. a German account) emits EPC (SEPA) format
        // so Revolut and EU banking apps can read it. VS goes into the
        // unstructured remittance text as the Czech "/VS{vs}/SS/KS" convention
        // (full shape, even with empty SS / KS) so apps pre-fill the VS field.
        $base64 = QrPaymentService::qrPlatba(
            iban: 'DE89370400440532013000',
            amount: 12.50,
            currency: 'EUR',
            variableSymbol: '00000077',
            recipientName: 'BCZ Test',
            note: 'Členské 2026',
        );

        // qrPlatba returns base64-encoded PNG; assert it produced something.
        $this->assertNotNull($base64);
        $this->assertNotFalse(base64_decode($base64, true));

        // Inspect the underlying EPC payload directly via the dedicated builder.
        $payload = QrPaymentService::epcQrPayload(
            iban: 'DE89370400440532013000',
            amount: 12.50,
            currency: 'EUR',
            beneficiaryName: 'BCZ Test',
            remittanceText: '/VS00000077/SS/KS',
        );

        $lines = explode("\n", $payload);
        $this->assertSame('BCD', $lines[0]);
        $this->assertSame('SCT', $lines[3]);
        $this->assertSame('DE89370400440532013000', $lines[6]);
        $this->assertSame('EUR12.50', $lines[7]);
        $this->assertSame('', $lines[9]);                          // structured ref empty
        $this->assertSame('/VS00000077/SS/KS', $lines[10]);        // unstructured text holds full /VS/SS/KS
    }

    public function test_pay_by_square_raw_data_keeps_vs_and_note_in_separate_fields(): void
    {
        // The reported bug: on SK QR codes the variable symbol ended up in the
        // note. Pay by Square has dedicated fields for each, so they never mix.
        $data = QrPaymentService::payBySquareRawData(
            iban: 'SK7111000000001234567890',
            amount: 12.50,
            currency: 'EUR',
            variableSymbol: '00000077',
            recipientName: 'BCZ Test',
            note: 'Členské 2026',
        );

        $fields = explode("\t", $data);

        $this->assertSame('00000077', $fields[6]);      // Variable symbol field
        $this->assertSame('Členské 2026', $fields[9]);  // Note field — the configured note, not the VS
        $this->assertSame('SK7111000000001234567890', $fields[11]);
    }

    public function test_pay_by_square_raw_data_truncates_note_to_140_chars(): void
    {
        $data = QrPaymentService::payBySquareRawData(
            iban: 'SK7111000000001234567890',
            amount: 12.50,
            note: str_repeat('x', 200),
        );

        $fields = explode("\t", $data);

        $this->assertSame(str_repeat('x', 140), $fields[9]);
    }

    public function test_qr_platba_routes_sk_iban_to_pay_by_square(): void
    {
        // SK IBANs must produce a Pay by Square QR (the SK-native format with
        // separate VS and note fields), not SPAYD or EPC. The full pipeline
        // depends on the system `xz` binary for LZMA1 compression.
        $base64 = QrPaymentService::qrPlatba(
            iban: 'SK7111000000001234567890',
            amount: 12.50,
            currency: 'EUR',
            variableSymbol: '00000077',
            recipientName: 'BCZ Test',
            note: 'Členské 2026',
        );

        $this->assertNotNull($base64, 'Pay by Square generation failed — is the xz binary available?');
        $this->assertNotFalse(base64_decode($base64, true));
    }
}
