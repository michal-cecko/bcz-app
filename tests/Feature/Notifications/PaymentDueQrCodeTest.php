<?php

namespace Tests\Feature\Notifications;

use App\Models\Membership;
use App\Models\Team;
use App\Models\User;
use App\Notifications\MembershipPaymentDue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentDueQrCodeTest extends TestCase
{
    use RefreshDatabase;

    public function test_membership_payment_due_email_embeds_qr_code_to_the_left_of_payment_details(): void
    {
        $team = Team::factory()->create([
            'bank_account_iban' => 'SK6807200002891987426353',
            'bank_account_name' => 'BCZ Club',
        ]);
        $user = User::factory()->create();
        $membership = Membership::factory()->pending()->create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'fee_amount' => 42.50,
            'fee_currency' => 'EUR',
        ]);

        $mailMessage = (new MembershipPaymentDue($membership))->toMail($user);

        $html = $mailMessage->render();

        // The QR PNG was embedded and rendered (render() previews cid: refs as data URIs).
        $this->assertStringContainsString('data:image/png;base64,', $html);

        // The QR image markup must sit to the left of ("before", in table-cell
        // reading order) the "Detail platby" box, not inside it.
        $qrPosition = strpos($html, 'data:image/png;base64,');
        $detailsPosition = strpos($html, 'Detail platby');

        $this->assertNotFalse($qrPosition);
        $this->assertNotFalse($detailsPosition);
        $this->assertLessThan($detailsPosition, $qrPosition);
    }

    public function test_membership_payment_due_email_omits_qr_code_when_team_has_no_iban(): void
    {
        $team = Team::factory()->create([
            'bank_account_iban' => null,
        ]);
        $user = User::factory()->create();
        $membership = Membership::factory()->pending()->create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'fee_amount' => 42.50,
            'fee_currency' => 'EUR',
        ]);

        $mailMessage = (new MembershipPaymentDue($membership))->toMail($user);

        $html = $mailMessage->render();

        $this->assertStringNotContainsString('data:image/png;base64,', $html);
        $this->assertStringContainsString('Detail platby', $html);
    }
}
