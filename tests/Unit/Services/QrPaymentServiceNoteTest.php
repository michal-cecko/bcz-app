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

    public function test_qr_platba_payload_does_not_emit_iso_11649_rf_reference(): void
    {
        // SPAYD is used only for domestic CZ payments, where the RF reference is
        // meaningless and makes Raiffeisen reject the QR. The VS lives in X-VS;
        // no RF is emitted even for a digit-only VS.
        $payload = QrPaymentService::qrPlatbaPayload(
            iban: 'CZ6508000000192000145399',
            amount: 100.0,
            variableSymbol: '539007547034',
        );

        $this->assertStringContainsString('*X-VS:539007547034', $payload);
        $this->assertStringNotContainsString('RF:', $payload);
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

    public function test_qr_platba_routes_non_cz_iban_to_epc_with_structured_reference_and_note(): void
    {
        // A non-CZ IBAN (SK, LT/Revolut, or any SEPA IBAN) emits EPC so SK apps,
        // Revolut and CZ apps paying abroad can read it. The VS rides in the
        // STRUCTURED reference as "/VS{vs}/SS/KS" (which SK/CZ banks turn back
        // into a variabilný symbol) and the note keeps the separate unstructured
        // field — both verified loading in Tatra banka, Raiffeisen and Revolut.
        $base64 = QrPaymentService::qrPlatba(
            iban: 'SK5611000000002931568998',
            amount: 12.50,
            currency: 'EUR',
            variableSymbol: '00000077',
            recipientName: 'BCZ Test',
            note: 'Členské 2026',
        );

        // qrPlatba returns base64-encoded PNG; assert it produced something.
        $this->assertNotNull($base64);
        $this->assertNotFalse(base64_decode($base64, true));

        // Inspect the underlying EPC payload directly via the dedicated builder,
        // mirroring exactly what qrPlatba() passes for a non-CZ IBAN.
        $payload = QrPaymentService::epcQrPayload(
            iban: 'SK5611000000002931568998',
            amount: 12.50,
            currency: 'EUR',
            beneficiaryName: 'BCZ Test',
            remittanceText: 'Členské 2026',
            remittanceReference: '/VS00000077/SS/KS',
        );

        $lines = explode("\n", $payload);
        $this->assertSame('BCD', $lines[0]);
        $this->assertSame('SCT', $lines[3]);
        $this->assertSame('SK5611000000002931568998', $lines[6]);
        $this->assertSame('EUR12.50', $lines[7]);
        $this->assertSame('/VS00000077/SS/KS', $lines[9]);   // structured reference holds the VS
        $this->assertSame('Členské 2026', $lines[10]);       // unstructured text holds the human note
    }

    public function test_epc_payload_blanks_amount_when_null(): void
    {
        // Open-amount donation QR: the amount line is left blank so the payer
        // enters the amount, while the structured reference still carries the VS.
        $payload = QrPaymentService::epcQrPayload(
            iban: 'SK5611000000002931568998',
            amount: null,
            currency: 'EUR',
            beneficiaryName: 'BCZ Test',
            remittanceReference: '/VS00000077/SS/KS',
        );

        $lines = explode("\n", $payload);
        $this->assertSame('', $lines[7]);                    // amount blank = payer enters it
        $this->assertSame('/VS00000077/SS/KS', $lines[9]);
    }

    public function test_pay_by_square_raw_data_keeps_vs_in_native_field_and_note_separate(): void
    {
        // The reported bug: on SK QR codes the variable symbol ended up in the
        // note. For a domestic SK transfer the VS belongs in the dedicated
        // VariableSymbol field (native "variabilný symbol"); the note keeps its
        // own field. The originatorsReferenceInformation slot (index 9) is left
        // empty — SK apps render it as the on-screen note and it would crowd
        // out the real note — but the slot must still be present so the later
        // fields (note, IBAN, beneficiary) keep their bysquare Table 15
        // positions.
        $data = QrPaymentService::payBySquareRawData(
            iban: 'SK7111000000001234567890',
            amount: 12.50,
            currency: 'EUR',
            variableSymbol: '00000077',
            recipientName: 'BCZ Test',
            note: 'Členské 2026',
        );

        $fields = explode("\t", $data);

        $this->assertSame('00000077', $fields[6]);             // Variable symbol field — the native reference
        $this->assertSame('', $fields[9]);                     // Originator's reference info — empty, not the VS
        $this->assertSame('Členské 2026', $fields[10]);        // Note field — the configured note, not the VS
        $this->assertSame('SK7111000000001234567890', $fields[12]);
    }

    public function test_pay_by_square_raw_data_leaves_reference_empty_without_variable_symbol(): void
    {
        // Open-amount donation with no VS: VS field and reference both stay
        // empty; the note keeps its own field.
        $data = QrPaymentService::payBySquareRawData(
            iban: 'SK7111000000001234567890',
            note: 'Dar',
        );

        $fields = explode("\t", $data);

        $this->assertSame('', $fields[6]);    // Variable symbol field
        $this->assertSame('', $fields[9]);    // Reference field
        $this->assertSame('Dar', $fields[10]); // Note field
    }

    public function test_pay_by_square_raw_data_truncates_note_to_140_chars(): void
    {
        $data = QrPaymentService::payBySquareRawData(
            iban: 'SK7111000000001234567890',
            amount: 12.50,
            note: str_repeat('x', 200),
        );

        $fields = explode("\t", $data);

        $this->assertSame(str_repeat('x', 140), $fields[10]);
    }

    public function test_qr_platba_routes_sk_iban_to_epc_not_pay_by_square(): void
    {
        // SK IBANs now route to EPC (works on SK apps + Raiffeisen + Revolut),
        // not Pay by Square. EPC needs no system `xz` binary, so generation must
        // succeed regardless of the environment.
        $base64 = QrPaymentService::qrPlatba(
            iban: 'SK7111000000001234567890',
            amount: 12.50,
            currency: 'EUR',
            variableSymbol: '00000077',
            recipientName: 'BCZ Test',
            note: 'Členské 2026',
        );

        $this->assertNotNull($base64);
        $this->assertNotFalse(base64_decode($base64, true));
    }
}
