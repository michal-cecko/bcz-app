<?php

namespace Tests\Feature\Commands;

use App\Enums\PaymentStatusEnum;
use App\Enums\RegistrationStatusEnum;
use App\Enums\RoleEnum;
use App\Enums\TrainingPricingTypeEnum;
use App\Models\Event;
use App\Models\EventOrganization;
use App\Models\EventRegistration;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\Team;
use App\Models\Training;
use App\Models\TrainingRegistration;
use App\Models\User;
use App\Notifications\EventRegistrationPaymentDue;
use App\Notifications\MembershipPaymentDue;
use App\Notifications\TrainingRegistrationPaymentDue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SendPaymentDueRemindersTest extends TestCase
{
    use RefreshDatabase;

    private Team $team;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }

        $this->team = Team::factory()->create();
        $this->user = User::factory()->create();

        // Attach as team ATHLETE so the user is a valid membership payer.
        $this->team->members()->attach($this->user, [
            'role' => RoleEnum::ATHLETE->value,
            'is_active' => true,
            'joined_at' => now(),
        ]);
    }

    public function test_sends_membership_reminder_when_deadline_within_threshold(): void
    {
        Notification::fake();

        $membership = Membership::factory()->pending()->create([
            'team_id' => $this->team->id,
            'user_id' => $this->user->id,
            'fee_amount' => 60.00,
            'fee_currency' => 'EUR',
            'payment_deadline_at' => now()->addDays(2),
        ]);

        $this->artisan('payments:send-due-reminders')->assertSuccessful();

        Notification::assertSentTo($this->user, MembershipPaymentDue::class);
        $this->assertNotNull($membership->fresh()->payment_reminder_sent_at);
    }

    public function test_does_not_send_membership_reminder_when_deadline_far_off(): void
    {
        Notification::fake();

        Membership::factory()->pending()->create([
            'team_id' => $this->team->id,
            'user_id' => $this->user->id,
            'fee_amount' => 60.00,
            'payment_deadline_at' => now()->addDays(30),
        ]);

        $this->artisan('payments:send-due-reminders')->assertSuccessful();

        Notification::assertNothingSent();
    }

    public function test_does_not_send_membership_reminder_if_already_completed_payment_exists(): void
    {
        Notification::fake();

        $membership = Membership::factory()->pending()->create([
            'team_id' => $this->team->id,
            'user_id' => $this->user->id,
            'fee_amount' => 60.00,
            'payment_deadline_at' => now()->addDays(2),
        ]);

        Payment::factory()->create([
            'team_id' => $this->team->id,
            'user_id' => $this->user->id,
            'payable_type' => $membership->getMorphClass(),
            'payable_id' => $membership->id,
            'amount' => 60.00,
            'currency' => 'EUR',
            'status' => PaymentStatusEnum::COMPLETED,
        ]);

        $this->artisan('payments:send-due-reminders')->assertSuccessful();

        Notification::assertNothingSent();
    }

    public function test_does_not_send_membership_reminder_for_free_membership(): void
    {
        Notification::fake();

        Membership::factory()->free()->create([
            'team_id' => $this->team->id,
            'user_id' => $this->user->id,
            'payment_deadline_at' => now()->addDays(2),
        ]);

        $this->artisan('payments:send-due-reminders')->assertSuccessful();

        Notification::assertNothingSent();
    }

    public function test_dedupes_membership_reminder_when_already_sent(): void
    {
        Notification::fake();

        Membership::factory()->pending()->create([
            'team_id' => $this->team->id,
            'user_id' => $this->user->id,
            'fee_amount' => 60.00,
            'payment_deadline_at' => now()->addDays(2),
            'payment_reminder_sent_at' => now()->subDay(),
        ]);

        $this->artisan('payments:send-due-reminders')->assertSuccessful();

        Notification::assertNothingSent();
    }

    public function test_sends_training_registration_reminder_when_deadline_within_threshold(): void
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
            'payment_due_at' => now()->addDays(1),
        ]);

        $this->artisan('payments:send-due-reminders')->assertSuccessful();

        Notification::assertSentTo($this->user, TrainingRegistrationPaymentDue::class);
        $this->assertNotNull($registration->fresh()->payment_reminder_sent_at);
    }

    public function test_does_not_send_training_reminder_if_already_completed_payment_exists(): void
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
            'payment_due_at' => now()->addDays(1),
        ]);

        Payment::factory()->create([
            'team_id' => $this->team->id,
            'user_id' => $this->user->id,
            'payable_type' => $registration->getMorphClass(),
            'payable_id' => $registration->id,
            'amount' => 25.00,
            'currency' => 'EUR',
            'status' => PaymentStatusEnum::COMPLETED,
        ]);

        $this->artisan('payments:send-due-reminders')->assertSuccessful();

        Notification::assertNothingSent();
    }

    public function test_sends_event_registration_reminder_when_deadline_within_threshold(): void
    {
        Notification::fake();

        $event = Event::factory()->create(['team_id' => $this->team->id]);
        EventOrganization::factory()->create([
            'event_id' => $event->id,
            'price_amount' => 30.00,
            'price_currency' => 'EUR',
        ]);

        $registration = EventRegistration::factory()->create([
            'event_id' => $event->id,
            'user_id' => $this->user->id,
            'status' => RegistrationStatusEnum::Pending,
            'payment_due_at' => now()->addDays(2),
        ]);

        $this->artisan('payments:send-due-reminders')->assertSuccessful();

        Notification::assertSentTo($this->user, EventRegistrationPaymentDue::class);
        $this->assertNotNull($registration->fresh()->payment_reminder_sent_at);
    }

    public function test_dedupes_training_reminder_when_already_sent(): void
    {
        Notification::fake();

        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'pricing_type' => TrainingPricingTypeEnum::PAID,
            'price_amount' => 25.00,
        ]);

        TrainingRegistration::factory()->pending()->create([
            'training_id' => $training->id,
            'user_id' => $this->user->id,
            'payment_due_at' => now()->addDays(1),
            'payment_reminder_sent_at' => now()->subDay(),
        ]);

        $this->artisan('payments:send-due-reminders')->assertSuccessful();

        Notification::assertNothingSent();
    }

    public function test_membership_payment_due_implements_should_queue(): void
    {
        $membership = Membership::factory()->pending()->create([
            'team_id' => $this->team->id,
            'user_id' => $this->user->id,
        ]);

        $this->assertInstanceOf(ShouldQueue::class, new MembershipPaymentDue($membership));
    }

    public function test_training_payment_due_implements_should_queue(): void
    {
        $training = Training::factory()->create(['team_id' => $this->team->id]);
        $registration = TrainingRegistration::factory()->pending()->create([
            'training_id' => $training->id,
            'user_id' => $this->user->id,
        ]);

        $this->assertInstanceOf(ShouldQueue::class, new TrainingRegistrationPaymentDue($registration));
    }

    public function test_event_payment_due_implements_should_queue(): void
    {
        $event = Event::factory()->create(['team_id' => $this->team->id]);
        $registration = EventRegistration::factory()->create([
            'event_id' => $event->id,
            'user_id' => $this->user->id,
        ]);

        $this->assertInstanceOf(ShouldQueue::class, new EventRegistrationPaymentDue($registration));
    }

    public function test_notifications_use_mail_and_database_channels(): void
    {
        $event = Event::factory()->create(['team_id' => $this->team->id]);
        $training = Training::factory()->create(['team_id' => $this->team->id]);

        $membershipNotification = new MembershipPaymentDue(Membership::factory()->pending()->create([
            'team_id' => $this->team->id,
            'user_id' => $this->user->id,
        ]));
        $trainingNotification = new TrainingRegistrationPaymentDue(
            TrainingRegistration::factory()->pending()->create([
                'training_id' => $training->id,
                'user_id' => $this->user->id,
            ]),
        );
        $eventNotification = new EventRegistrationPaymentDue(
            EventRegistration::factory()->create([
                'event_id' => $event->id,
                'user_id' => $this->user->id,
            ]),
        );

        $this->assertSame(['mail', 'database'], $membershipNotification->via($this->user));
        $this->assertSame(['mail', 'database'], $trainingNotification->via($this->user));
        $this->assertSame(['mail', 'database'], $eventNotification->via($this->user));
    }
}
