<?php

namespace Tests\Feature\Services;

use App\Enums\MembershipStatusEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\RegistrationStatusEnum;
use App\Enums\RoleEnum;
use App\Enums\TrainingPricingTypeEnum;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\RegistrationFee;
use App\Models\Team;
use App\Models\Training;
use App\Models\TrainingRegistration;
use App\Models\User;
use App\Notifications\PaymentConfirmed;
use App\Notifications\TrainingPaymentConfirmed;
use App\Services\PaymentService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PaymentServiceManualPaymentTest extends TestCase
{
    use RefreshDatabase;

    private Team $team;

    private User $user;

    private PaymentService $paymentService;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }

        $this->team = Team::factory()->create();
        $this->user = User::factory()->create();
        $this->paymentService = app(PaymentService::class);
    }

    public function test_full_membership_payment_activates_and_notifies(): void
    {
        Notification::fake();

        $membership = Membership::factory()->pending()->create([
            'team_id' => $this->team->id,
            'user_id' => $this->user->id,
            'fee_amount' => 60.00,
            'fee_currency' => 'EUR',
        ]);

        $this->paymentService->recordManualPayment(
            $this->user,
            $this->team,
            $membership,
            60.00,
            'EUR',
            PaymentMethodEnum::CASH,
        );

        $membership->refresh();
        $this->assertSame(MembershipStatusEnum::ACTIVE, $membership->status);
        Notification::assertSentTo($this->user, PaymentConfirmed::class);
    }

    public function test_partial_membership_payment_does_not_activate_but_still_notifies(): void
    {
        Notification::fake();

        $membership = Membership::factory()->pending()->create([
            'team_id' => $this->team->id,
            'user_id' => $this->user->id,
            'fee_amount' => 60.00,
            'fee_currency' => 'EUR',
        ]);

        $this->paymentService->recordManualPayment(
            $this->user,
            $this->team,
            $membership,
            20.00,
            'EUR',
            PaymentMethodEnum::CASH,
        );

        $membership->refresh();
        $this->assertSame(MembershipStatusEnum::PENDING, $membership->status);
        Notification::assertSentTo($this->user, PaymentConfirmed::class);
    }

    public function test_three_partial_payments_summing_to_full_price_activates_membership(): void
    {
        Notification::fake();

        $membership = Membership::factory()->pending()->create([
            'team_id' => $this->team->id,
            'user_id' => $this->user->id,
            'fee_amount' => 60.00,
            'fee_currency' => 'EUR',
        ]);

        foreach ([20.00, 20.00, 20.00] as $part) {
            $this->paymentService->recordManualPayment(
                $this->user,
                $this->team,
                $membership,
                $part,
                'EUR',
                PaymentMethodEnum::CASH,
            );
        }

        $membership->refresh();
        $this->assertSame(MembershipStatusEnum::ACTIVE, $membership->status);
        Notification::assertSentToTimes($this->user, PaymentConfirmed::class, 3);
    }

    public function test_notify_false_suppresses_email(): void
    {
        Notification::fake();

        $membership = Membership::factory()->pending()->create([
            'team_id' => $this->team->id,
            'user_id' => $this->user->id,
            'fee_amount' => 60.00,
            'fee_currency' => 'EUR',
        ]);

        $this->paymentService->recordManualPayment(
            $this->user,
            $this->team,
            $membership,
            60.00,
            'EUR',
            PaymentMethodEnum::CASH,
            notify: false,
        );

        $membership->refresh();
        $this->assertSame(MembershipStatusEnum::ACTIVE, $membership->status);
        Notification::assertNothingSent();
    }

    public function test_full_training_registration_payment_approves_and_notifies(): void
    {
        Notification::fake();

        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'pricing_type' => TrainingPricingTypeEnum::PAID,
            'price_amount' => 25.00,
        ]);

        $registration = TrainingRegistration::factory()->pending()->create([
            'training_id' => $training->id,
            'user_id' => $this->user->id,
            'payment_due_at' => now()->addDays(7),
        ]);

        $this->paymentService->recordManualPayment(
            $this->user,
            $this->team,
            $registration,
            25.00,
            'EUR',
            PaymentMethodEnum::BANK_TRANSFER,
        );

        $registration->refresh();
        $this->assertSame(RegistrationStatusEnum::Approved, $registration->status);
        $this->assertNull($registration->payment_due_at);
        Notification::assertSentTo($this->user, PaymentConfirmed::class);
    }

    public function test_partial_training_registration_payment_does_not_approve(): void
    {
        Notification::fake();

        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'pricing_type' => TrainingPricingTypeEnum::PAID,
            'price_amount' => 25.00,
        ]);

        $registration = TrainingRegistration::factory()->pending()->create([
            'training_id' => $training->id,
            'user_id' => $this->user->id,
            'payment_due_at' => now()->addDays(7),
        ]);

        $this->paymentService->recordManualPayment(
            $this->user,
            $this->team,
            $registration,
            10.00,
            'EUR',
            PaymentMethodEnum::CASH,
        );

        $registration->refresh();
        $this->assertSame(RegistrationStatusEnum::Pending, $registration->status);
        $this->assertNotNull($registration->payment_due_at);
        Notification::assertSentTo($this->user, PaymentConfirmed::class);
    }

    public function test_full_event_registration_payment_approves_and_notifies(): void
    {
        Notification::fake();

        $event = Event::factory()->create([
            'team_id' => $this->team->id,
        ]);

        $fee = RegistrationFee::factory()->create([
            'amount' => 30.00,
            'currency' => 'EUR',
        ]);

        $registration = EventRegistration::factory()->create([
            'event_id' => $event->id,
            'user_id' => $this->user->id,
            'registration_fee_id' => $fee->id,
            'status' => 'pending',
        ]);

        $this->paymentService->recordManualPayment(
            $this->user,
            $this->team,
            $registration,
            30.00,
            'EUR',
            PaymentMethodEnum::CASH,
        );

        $registration->refresh();
        $this->assertSame(RegistrationStatusEnum::Approved, $registration->status);
        Notification::assertSentTo($this->user, PaymentConfirmed::class);
    }

    public function test_membership_activation_auto_approves_dependent_training_registrations_and_notifies(): void
    {
        Notification::fake();

        $membership = Membership::factory()->pending()->create([
            'team_id' => $this->team->id,
            'user_id' => $this->user->id,
            'fee_amount' => 60.00,
            'fee_currency' => 'EUR',
        ]);

        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'pricing_type' => TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED,
        ]);

        $pendingReg = TrainingRegistration::factory()->pending()->create([
            'training_id' => $training->id,
            'user_id' => $this->user->id,
        ]);

        $this->paymentService->recordManualPayment(
            $this->user,
            $this->team,
            $membership,
            60.00,
            'EUR',
            PaymentMethodEnum::CASH,
        );

        $pendingReg->refresh();
        $this->assertSame(RegistrationStatusEnum::Approved, $pendingReg->status);
        Notification::assertSentTo($this->user, PaymentConfirmed::class);
        Notification::assertSentTo($this->user, TrainingPaymentConfirmed::class);
    }

    public function test_membership_activation_with_notify_false_suppresses_dependent_notifications_too(): void
    {
        Notification::fake();

        $membership = Membership::factory()->pending()->create([
            'team_id' => $this->team->id,
            'user_id' => $this->user->id,
            'fee_amount' => 60.00,
            'fee_currency' => 'EUR',
        ]);

        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'pricing_type' => TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED,
        ]);

        TrainingRegistration::factory()->pending()->create([
            'training_id' => $training->id,
            'user_id' => $this->user->id,
        ]);

        $this->paymentService->recordManualPayment(
            $this->user,
            $this->team,
            $membership,
            60.00,
            'EUR',
            PaymentMethodEnum::CASH,
            notify: false,
        );

        Notification::assertNothingSent();
    }

    public function test_payment_confirmed_notification_uses_mail_and_database_channels(): void
    {
        Notification::fake();

        $membership = Membership::factory()->pending()->create([
            'team_id' => $this->team->id,
            'user_id' => $this->user->id,
            'fee_amount' => 30.00,
            'fee_currency' => 'EUR',
        ]);

        $this->paymentService->recordManualPayment(
            $this->user,
            $this->team,
            $membership,
            30.00,
            'EUR',
            PaymentMethodEnum::CASH,
        );

        Notification::assertSentTo(
            $this->user,
            PaymentConfirmed::class,
            function (PaymentConfirmed $notification) {
                $channels = $notification->via($this->user);

                return in_array('mail', $channels, true) && in_array('database', $channels, true);
            },
        );
    }

    public function test_payment_confirmed_notification_implements_should_queue(): void
    {
        $this->assertInstanceOf(
            ShouldQueue::class,
            new PaymentConfirmed(Payment::factory()->make()),
        );
    }
}
