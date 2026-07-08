<?php

namespace Tests\Feature;

use App\Models\EventRegistration;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\TeamSubscription;
use App\Models\TrainingRegistration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentCascadeDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_deleting_an_event_registration_deletes_its_payments(): void
    {
        $registration = EventRegistration::factory()->create();
        $payment = Payment::factory()->for($registration, 'payable')->create();

        $registration->delete();

        $this->assertDatabaseMissing(Payment::class, ['id' => $payment->id]);
    }

    public function test_deleting_a_training_registration_deletes_its_payments(): void
    {
        $registration = TrainingRegistration::factory()->create();
        $payment = Payment::factory()->for($registration, 'payable')->create();

        $registration->delete();

        $this->assertDatabaseMissing(Payment::class, ['id' => $payment->id]);
    }

    public function test_deleting_a_membership_deletes_its_payments(): void
    {
        $membership = Membership::factory()->create();
        $payment = Payment::factory()->for($membership, 'payable')->create();

        $membership->delete();

        $this->assertDatabaseMissing(Payment::class, ['id' => $payment->id]);
    }

    public function test_deleting_a_team_subscription_deletes_its_payments(): void
    {
        $subscription = TeamSubscription::factory()->create();
        $payment = Payment::factory()->for($subscription, 'payable')->create();

        $subscription->delete();

        $this->assertDatabaseMissing(Payment::class, ['id' => $payment->id]);
    }

    public function test_soft_deleting_an_event_keeps_registrations_and_payments(): void
    {
        $registration = EventRegistration::factory()->create();
        $payment = Payment::factory()->for($registration, 'payable')->create();

        $registration->event->delete();

        $this->assertDatabaseHas(EventRegistration::class, ['id' => $registration->id]);
        $this->assertDatabaseHas(Payment::class, ['id' => $payment->id]);
    }

    public function test_force_deleting_an_event_deletes_registrations_and_their_payments(): void
    {
        $registration = EventRegistration::factory()->create();
        $payment = Payment::factory()->for($registration, 'payable')->create();
        $event = $registration->event;

        $event->forceDelete();

        $this->assertDatabaseMissing(EventRegistration::class, ['id' => $registration->id]);
        $this->assertDatabaseMissing(Payment::class, ['id' => $payment->id]);
    }
}
