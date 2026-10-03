<?php

namespace Tests\Feature\Notifications;

use App\Enums\PaymentStatusEnum;
use App\Enums\RoleEnum;
use App\Enums\TrainingPricingTypeEnum;
use App\Mail\RegistrationConfirmationMail;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\Team;
use App\Models\TeamSeason;
use App\Models\Training;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * A registration for a membership-required training sends exactly one email —
 * the registration confirmation — carrying the membership fee QR code, its
 * payment details and pay link, and (for new users) the sign-in link.
 */
class RegistrationConfirmationMembershipPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected Team $team;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }

        // Registrations (and so their confirmation emails) take the app locale.
        app()->setLocale('sk');

        $this->team = Team::factory()->create([
            'bank_account_iban' => 'SK6807200002891987426353',
            'bank_account_name' => 'BCZ Club',
        ]);

        TeamSeason::factory()->create([
            'team_id' => $this->team->id,
            'name' => 'Sezóna 2026',
            'fee_amount' => 120.00,
            'fee_currency' => 'EUR',
        ]);
    }

    protected function membershipTraining(array $overrides = []): Training
    {
        return Training::factory()->create(array_merge([
            'team_id' => $this->team->id,
            'pricing_type' => TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED,
        ], $overrides));
    }

    protected function register(Training $training, string $email, string $phone = '+421900111222'): void
    {
        Livewire::test('training-registration-form', ['training' => $training])
            ->set('fields.meno', 'Jana')
            ->set('fields.priezvisko', 'Nová')
            ->set('fields.email', $email)
            ->set('fields.telefon', $phone)
            ->set('gdprAgreed', true)
            ->call('submit')
            ->assertHasNoErrors();
    }

    protected function membershipFeeFor(User $user): Payment
    {
        return Payment::query()
            ->where('user_id', $user->id)
            ->where('payable_type', (new Membership)->getMorphClass())
            ->where('status', PaymentStatusEnum::PENDING)
            ->sole();
    }

    protected function queuedConfirmation(): RegistrationConfirmationMail
    {
        Mail::assertQueuedCount(1);

        return Mail::queued(RegistrationConfirmationMail::class)->sole();
    }

    public function test_existing_user_gets_the_membership_fee_qr_and_pay_link_in_the_confirmation(): void
    {
        Mail::fake();
        Notification::fake();

        $user = User::factory()->create(['email' => 'existing@test.com']);

        $this->register($this->membershipTraining(), 'existing@test.com');

        $mail = $this->queuedConfirmation();
        $fee = $this->membershipFeeFor($user);

        $this->assertTrue($mail->hasTo('existing@test.com'));
        $this->assertFalse($mail->isNewUser);
        $this->assertTrue($mail->membershipPayment?->is($fee));

        $html = $mail->render();

        // QR code (CID-embedded, previewed as a data URI by render()) to the
        // left of the payment details.
        $this->assertStringContainsString('data:image/png;base64,', $html);
        $this->assertStringContainsString('Detail platby', $html);
        $this->assertLessThan(strpos($html, 'Detail platby'), strpos($html, 'data:image/png;base64,'));
        $this->assertStringContainsString('SK6807200002891987426353', $html);
        $this->assertStringContainsString($fee->formattedVariableSymbol(), $html);
        $this->assertStringContainsString('Zaplatiť členské', $html);
        $this->assertStringContainsString(e(route('payment.page', ['payment' => $fee->id], false)), $html);
        $this->assertStringContainsString(e($mail->membershipPaymentUrl), $html);

        // Existing users are not sent a magic sign-in link.
        $this->assertStringNotContainsString('magic-login', $html);

        Notification::assertNothingSent();
    }

    public function test_new_user_gets_exactly_one_email_with_both_the_sign_in_link_and_the_membership_fee_qr(): void
    {
        Mail::fake();
        Notification::fake();

        $this->register($this->membershipTraining(), 'jana@test.com');

        $user = User::where('email', 'jana@test.com')->sole();
        $mail = $this->queuedConfirmation();

        $this->assertTrue($mail->isNewUser);
        $this->assertTrue($mail->membershipPayment?->is($this->membershipFeeFor($user)));

        $html = $mail->render();

        $this->assertStringContainsString(e($mail->magicUrl), $html);
        $this->assertStringContainsString('data:image/png;base64,', $html);
        $this->assertStringContainsString('Detail platby', $html);
        $this->assertStringContainsString('Zaplatiť členské', $html);

        // The separate welcome email is no longer sent from this flow.
        Notification::assertNothingSent();
    }

    public function test_membership_payment_note_follows_the_training_payment_note(): void
    {
        Mail::fake();

        User::factory()->create(['email' => 'peter@test.com', 'first_name' => 'Peter', 'last_name' => 'Malý']);

        $this->register(
            $this->membershipTraining(['payment_note' => '{{meno}} {{priezvisko}} - clensky prispevok']),
            'peter@test.com',
        );

        $mail = $this->queuedConfirmation();

        $this->assertSame(
            $mail->membershipPayment->payable->getQrPaymentNote(),
            'Peter Malý - clensky prispevok',
        );
        $this->assertStringContainsString('Peter Malý - clensky prispevok', $mail->render());
    }

    public function test_confirmation_has_no_membership_section_when_the_user_is_already_a_paid_up_member(): void
    {
        Mail::fake();

        $user = User::factory()->create(['email' => 'member@test.com']);
        Membership::factory()->create([
            'team_id' => $this->team->id,
            'user_id' => $user->id,
            'team_season_id' => $this->team->currentSeason->id,
        ]);

        $this->register($this->membershipTraining(), 'member@test.com');

        $mail = $this->queuedConfirmation();

        $this->assertNull($mail->membershipPayment);
        $this->assertStringNotContainsString('Detail platby', $mail->render());
    }

    public function test_free_training_confirmation_has_no_membership_section(): void
    {
        Mail::fake();
        Notification::fake();

        $this->register($this->membershipTraining(['pricing_type' => TrainingPricingTypeEnum::FREE]), 'free@test.com');

        $mail = $this->queuedConfirmation();

        $this->assertNull($mail->membershipPayment);
        $this->assertStringNotContainsString('Detail platby', $mail->render());
        Notification::assertNothingSent();
    }
}
