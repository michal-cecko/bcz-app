<?php

namespace Tests\Feature\Notifications;

use App\Enums\MembershipStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\RegistrationStatusEnum;
use App\Enums\RoleEnum;
use App\Enums\TrainingPricingTypeEnum;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\Team;
use App\Models\TeamSeason;
use App\Models\Training;
use App\Models\TrainingRegistration;
use App\Models\User;
use App\Notifications\WelcomeToApp;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WelcomeEmailMembershipQrTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }
    }

    /**
     * Register a user for a membership-required training and issue the season's
     * membership fee, mirroring what the public registration flow leaves behind.
     */
    protected function memberOwingTheSeasonFee(Team $team): User
    {
        $season = TeamSeason::factory()->create([
            'team_id' => $team->id,
            'name' => 'Sezóna 2026',
            'fee_amount' => 120.00,
            'fee_currency' => 'EUR',
        ]);

        $training = Training::factory()->create([
            'team_id' => $team->id,
            'team_season_id' => $season->id,
            'pricing_type' => TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED,
        ]);

        $user = User::factory()->create();

        TrainingRegistration::create([
            'training_id' => $training->id,
            'user_id' => $user->id,
            'form_data' => [],
            'status' => RegistrationStatusEnum::Pending->value,
            'registered_at' => now(),
        ]);

        $membership = Membership::factory()->pending()->forSeason($season)->create([
            'user_id' => $user->id,
        ]);

        app(PaymentService::class)->createPendingPayment($user, $team, $membership, 120.00);

        return $user;
    }

    public function test_welcome_email_embeds_membership_qr_code_to_the_left_of_payment_details(): void
    {
        $team = Team::factory()->create([
            'bank_account_iban' => 'SK6807200002891987426353',
            'bank_account_name' => 'BCZ Club',
        ]);

        $user = $this->memberOwingTheSeasonFee($team);

        $html = (new WelcomeToApp)->toMail($user)->render();

        // render() previews the embedded cid: reference as a data URI.
        $this->assertStringContainsString('data:image/png;base64,', $html);
        $this->assertStringContainsString('Detail platby', $html);
        $this->assertStringContainsString('SK6807200002891987426353', $html);

        // "obrazok vlavo" — the QR cell must precede the details cell in table
        // reading order, so it renders to the left of the payment details.
        $this->assertLessThan(
            strpos($html, 'Detail platby'),
            strpos($html, 'data:image/png;base64,'),
        );
    }

    public function test_welcome_email_keeps_payment_details_but_drops_the_qr_when_the_team_has_no_iban(): void
    {
        $team = Team::factory()->create([
            'bank_account_iban' => null,
            'bank_account_name' => null,
        ]);

        $user = $this->memberOwingTheSeasonFee($team);

        $html = (new WelcomeToApp)->toMail($user)->render();

        $this->assertStringNotContainsString('data:image/png;base64,', $html);
        $this->assertStringContainsString('Detail platby', $html);
        $this->assertStringContainsString('Vitaj v BCZ App', $html);
    }

    public function test_welcome_email_has_no_membership_section_when_the_fee_is_unrelated_to_a_membership_training(): void
    {
        $team = Team::factory()->create([
            'bank_account_iban' => 'SK6807200002891987426353',
            'bank_account_name' => 'BCZ Club',
        ]);

        $season = TeamSeason::factory()->create(['team_id' => $team->id]);
        $user = User::factory()->create();

        $membership = Membership::factory()->pending()->forSeason($season)->create([
            'user_id' => $user->id,
            'status' => MembershipStatusEnum::PENDING,
        ]);

        app(PaymentService::class)->createPendingPayment($user, $team, $membership, 120.00);

        $html = (new WelcomeToApp)->toMail($user)->render();

        $this->assertStringNotContainsString('Detail platby', $html);
        $this->assertStringNotContainsString('data:image/png;base64,', $html);
        $this->assertStringContainsString('Vitaj v BCZ App', $html);
    }

    public function test_registering_for_a_membership_required_training_welcomes_the_new_user_and_issues_the_fee(): void
    {
        Mail::fake();
        Notification::fake();

        $team = Team::factory()->create([
            'bank_account_iban' => 'SK6807200002891987426353',
            'bank_account_name' => 'BCZ Club',
        ]);

        TeamSeason::factory()->create([
            'team_id' => $team->id,
            'fee_amount' => 120.00,
            'fee_currency' => 'EUR',
        ]);

        $training = Training::factory()->create([
            'team_id' => $team->id,
            'pricing_type' => TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED,
        ]);

        Livewire::test('training-registration-form', ['training' => $training])
            ->set('fields.meno', 'Jana')
            ->set('fields.priezvisko', 'Nová')
            ->set('fields.email', 'jana@test.com')
            ->set('fields.telefon', '+421900333444')
            ->set('gdprAgreed', true)
            ->call('submit')
            ->assertHasNoErrors();

        $user = User::where('email', 'jana@test.com')->sole();

        Notification::assertSentTo($user, WelcomeToApp::class);

        $this->assertTrue(
            Payment::query()
                ->where('user_id', $user->id)
                ->where('payable_type', (new Membership)->getMorphClass())
                ->where('status', PaymentStatusEnum::PENDING)
                ->exists(),
        );
    }

    public function test_registering_for_a_free_training_does_not_send_the_welcome_email(): void
    {
        Mail::fake();
        Notification::fake();

        $team = Team::factory()->create();

        $training = Training::factory()->create([
            'team_id' => $team->id,
            'pricing_type' => TrainingPricingTypeEnum::FREE,
        ]);

        Livewire::test('training-registration-form', ['training' => $training])
            ->set('fields.meno', 'Peter')
            ->set('fields.priezvisko', 'Voľný')
            ->set('fields.email', 'peter@test.com')
            ->set('fields.telefon', '+421900555666')
            ->set('gdprAgreed', true)
            ->call('submit')
            ->assertHasNoErrors();

        Notification::assertNothingSent();
    }
}
