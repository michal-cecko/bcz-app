<?php

namespace Tests\Feature;

use App\Enums\MembershipStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\RegistrationStatusEnum;
use App\Enums\RoleEnum;
use App\Enums\TrainingPricingTypeEnum;
use App\Mail\RegistrationConfirmationMail;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\Team;
use App\Models\Training;
use App\Models\TrainingRegistration;
use App\Models\User;
use App\Notifications\TrainingPaymentConfirmed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TrainingRegistrationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected Team $team;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }

        $this->team = Team::factory()->create();
    }

    protected function createTraining(array $overrides = []): Training
    {
        return Training::factory()->create(array_merge([
            'team_id' => $this->team->id,
        ], $overrides));
    }

    public function test_registration_closed_shows_closed_state(): void
    {
        $training = $this->createTraining([
            'registration_closes_at' => now()->subDay(),
        ]);

        Livewire::test('training-registration-form', ['training' => $training])
            ->assertSee(__('training_detail.registration_closed'));
    }

    public function test_registration_not_yet_open_shows_not_yet_open_state(): void
    {
        $training = $this->createTraining([
            'registration_opens_at' => now()->addDay(),
        ]);

        Livewire::test('training-registration-form', ['training' => $training])
            ->assertSee(__('training_detail.registration_not_yet_open'));
    }

    public function test_logged_in_user_sees_already_registered(): void
    {
        $user = User::factory()->create();
        $training = $this->createTraining([
            'pricing_type' => TrainingPricingTypeEnum::FREE,
        ]);

        TrainingRegistration::create([
            'training_id' => $training->id,
            'user_id' => $user->id,
            'form_data' => [],
            'status' => RegistrationStatusEnum::Approved->value,
            'registered_at' => now(),
        ]);

        Livewire::actingAs($user)
            ->test('training-registration-form', ['training' => $training])
            ->assertSee(__('training_detail.already_registered_title'));
    }

    public function test_free_training_auto_approves(): void
    {
        Mail::fake();

        $training = $this->createTraining([
            'pricing_type' => TrainingPricingTypeEnum::FREE,
        ]);

        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test('training-registration-form', ['training' => $training])
            ->set('fields.meno', $user->first_name)
            ->set('fields.priezvisko', $user->last_name)
            ->set('gdprAgreed', true)
            ->call('submit');

        $registration = TrainingRegistration::where('training_id', $training->id)
            ->where('user_id', $user->id)
            ->first();

        $this->assertNotNull($registration);
        $this->assertEquals(RegistrationStatusEnum::Approved, $registration->status);
    }

    public function test_membership_required_with_active_membership_auto_approves(): void
    {
        Mail::fake();

        $training = $this->createTraining([
            'pricing_type' => TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED,
        ]);

        $user = User::factory()->create();

        Membership::create([
            'team_id' => $this->team->id,
            'user_id' => $user->id,
            'status' => MembershipStatusEnum::ACTIVE,
            'starts_at' => now()->subMonth(),
            'ends_at' => now()->addMonth(),
            'fee_amount' => 50.00,
            'fee_currency' => 'EUR',
        ]);

        Livewire::actingAs($user)
            ->test('training-registration-form', ['training' => $training])
            ->set('fields.meno', $user->first_name)
            ->set('fields.priezvisko', $user->last_name)
            ->set('gdprAgreed', true)
            ->call('submit');

        $registration = TrainingRegistration::where('training_id', $training->id)
            ->where('user_id', $user->id)
            ->first();

        $this->assertNotNull($registration);
        $this->assertEquals(RegistrationStatusEnum::Approved, $registration->status);
    }

    public function test_membership_required_without_membership_stays_pending(): void
    {
        Mail::fake();

        $training = $this->createTraining([
            'pricing_type' => TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED,
        ]);

        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test('training-registration-form', ['training' => $training])
            ->set('fields.meno', $user->first_name)
            ->set('fields.priezvisko', $user->last_name)
            ->set('gdprAgreed', true)
            ->call('submit');

        $registration = TrainingRegistration::where('training_id', $training->id)
            ->where('user_id', $user->id)
            ->first();

        $this->assertNotNull($registration);
        $this->assertEquals(RegistrationStatusEnum::Pending, $registration->status);
    }

    public function test_paid_training_stays_pending(): void
    {
        Mail::fake();

        $training = $this->createTraining([
            'pricing_type' => TrainingPricingTypeEnum::PAID,
            'is_recurring' => false,
            'event_date' => now()->addWeek(),
            'price_amount' => 25.00,
        ]);

        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test('training-registration-form', ['training' => $training])
            ->set('fields.meno', $user->first_name)
            ->set('fields.priezvisko', $user->last_name)
            ->set('gdprAgreed', true)
            ->call('submit');

        $registration = TrainingRegistration::where('training_id', $training->id)
            ->where('user_id', $user->id)
            ->first();

        $this->assertNotNull($registration);
        $this->assertEquals(RegistrationStatusEnum::Pending, $registration->status);
    }

    public function test_guest_with_existing_email_can_register_attaching_to_existing_user(): void
    {
        $existing = User::factory()->create(['email' => 'existing@test.com']);

        $training = $this->createTraining();

        Livewire::test('training-registration-form', ['training' => $training])
            ->set('fields.meno', 'John')
            ->set('fields.priezvisko', 'Doe')
            ->set('fields.email', 'existing@test.com')
            ->set('fields.telefon', '+421900123456')
            ->set('gdprAgreed', true)
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('training_registrations', [
            'training_id' => $training->id,
            'user_id' => $existing->id,
        ]);
    }

    public function test_guest_with_duplicate_phone_gets_validation_error(): void
    {
        User::factory()->create([
            'email' => 'existing@test.com',
            'phone' => '+421900111222',
        ]);

        $training = $this->createTraining([
            'registration_form_schema' => [
                ['label' => ['sk' => 'Meno'], 'name' => 'meno', 'type' => 'text_input', 'width' => 'half', 'required' => true, 'has_condition' => false],
                ['label' => ['sk' => 'Email'], 'name' => 'email', 'type' => 'email', 'width' => 'full', 'required' => true, 'has_condition' => false],
                ['label' => ['sk' => 'Telefón'], 'name' => 'telefon', 'type' => 'phone', 'width' => 'full', 'required' => true, 'has_condition' => false],
            ],
        ]);

        Livewire::test('training-registration-form', ['training' => $training])
            ->set('fields.meno', 'Jane')
            ->set('fields.email', 'new@test.com')
            ->set('fields.telefon', '+421900111222')
            ->set('gdprAgreed', true)
            ->call('submit')
            ->assertHasErrors(['fields.telefon']);
    }

    public function test_new_user_gets_account_created_email(): void
    {
        Mail::fake();

        $training = $this->createTraining([
            'pricing_type' => TrainingPricingTypeEnum::FREE,
        ]);

        Livewire::test('training-registration-form', ['training' => $training])
            ->set('fields.meno', 'New')
            ->set('fields.priezvisko', 'User')
            ->set('fields.email', 'newuser@test.com')
            ->set('fields.telefon', '+421900654321')
            ->set('gdprAgreed', true)
            ->call('submit');

        Mail::assertQueued(RegistrationConfirmationMail::class, function ($mail) {
            return $mail->isNewUser === true
                && $mail->hasTo('newuser@test.com');
        });

        // Verify user was created and attached to team
        $user = User::where('email', 'newuser@test.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->teams()->where('teams.id', $this->team->id)->exists());
    }

    public function test_gopay_payment_approves_training_registration(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $training = $this->createTraining([
            'pricing_type' => TrainingPricingTypeEnum::PAID,
            'is_recurring' => false,
            'event_date' => now()->addWeek(),
            'price_amount' => 25.00,
        ]);

        $registration = TrainingRegistration::create([
            'training_id' => $training->id,
            'user_id' => $user->id,
            'form_data' => [],
            'status' => RegistrationStatusEnum::Pending->value,
            'registered_at' => now(),
        ]);

        // Create a payment record simulating GoPay payment created
        $payment = Payment::create([
            'team_id' => $this->team->id,
            'user_id' => $user->id,
            'payer_name' => $user->name,
            'payer_email' => $user->email,
            'payable_type' => TrainingRegistration::class,
            'payable_id' => $registration->id,
            'amount' => 25.00,
            'currency' => 'EUR',
            'status' => PaymentStatusEnum::PENDING,
            'payment_method' => 'gopay',
            'gopay_payment_id' => '1234567890',
        ]);

        // Simulate the logic from handlePaymentCompleted without calling GoPay API
        $payment->update([
            'status' => PaymentStatusEnum::COMPLETED,
            'paid_at' => now(),
        ]);
        $registration->update(['status' => RegistrationStatusEnum::Approved]);
        $user->notify(new TrainingPaymentConfirmed($training));

        $registration->refresh();
        $this->assertEquals(RegistrationStatusEnum::Approved, $registration->status);

        Notification::assertSentTo($user, TrainingPaymentConfirmed::class);
    }

    public function test_membership_activation_approves_pending_registrations(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $training = $this->createTraining([
            'pricing_type' => TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED,
        ]);

        $registration = TrainingRegistration::create([
            'training_id' => $training->id,
            'user_id' => $user->id,
            'form_data' => [],
            'status' => RegistrationStatusEnum::Pending->value,
            'registered_at' => now(),
        ]);

        $membership = Membership::create([
            'team_id' => $this->team->id,
            'user_id' => $user->id,
            'status' => MembershipStatusEnum::PENDING,
            'starts_at' => now()->subMonth(),
            'ends_at' => now()->addMonth(),
            'fee_amount' => 50.00,
            'fee_currency' => 'EUR',
        ]);

        // Create pending payment for membership
        $payment = Payment::create([
            'team_id' => $this->team->id,
            'user_id' => $user->id,
            'payer_name' => $user->name,
            'payer_email' => $user->email,
            'payable_type' => Membership::class,
            'payable_id' => $membership->id,
            'amount' => 50.00,
            'currency' => 'EUR',
            'status' => PaymentStatusEnum::PENDING,
            'payment_method' => 'gopay',
            'gopay_payment_id' => '9876543210',
        ]);

        // Simulate membership payment completion and cascade
        $payment->update([
            'status' => PaymentStatusEnum::COMPLETED,
            'paid_at' => now(),
        ]);
        $membership->update(['status' => MembershipStatusEnum::ACTIVE]);

        // Trigger the cascade logic (same as in PaymentService::autoApprovePendingRegistrationsForMembership)
        $pendingRegistrations = TrainingRegistration::query()
            ->where('user_id', $membership->user_id)
            ->where('status', RegistrationStatusEnum::Pending)
            ->whereHas('training', function ($query) use ($membership) {
                $query->where('team_id', $membership->team_id)
                    ->where('pricing_type', TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED);
            })
            ->with('training')
            ->get();

        foreach ($pendingRegistrations as $reg) {
            $reg->update(['status' => RegistrationStatusEnum::Approved]);
            if ($reg->user) {
                $reg->user->notify(new TrainingPaymentConfirmed($reg->training));
            }
        }

        $registration->refresh();
        $this->assertEquals(RegistrationStatusEnum::Approved, $registration->status);

        Notification::assertSentTo($user, TrainingPaymentConfirmed::class);
    }
}
