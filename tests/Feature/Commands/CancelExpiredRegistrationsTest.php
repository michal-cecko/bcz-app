<?php

namespace Tests\Feature\Commands;

use App\Enums\PaymentStatusEnum;
use App\Enums\RegistrationStatusEnum;
use App\Enums\TrainingPricingTypeEnum;
use App\Models\Payment;
use App\Models\Training;
use App\Models\TrainingRegistration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CancelExpiredRegistrationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_cancels_pending_registrations_past_payment_due_date(): void
    {
        $registration = TrainingRegistration::factory()->pending()->create([
            'payment_due_at' => now()->subDay(),
        ]);

        $this->artisan('registrations:cancel-expired')
            ->expectsOutputToContain('Cancelled 1 expired registration(s).')
            ->assertExitCode(0);

        $this->assertEquals(RegistrationStatusEnum::Cancelled, $registration->fresh()->status);
    }

    public function test_does_not_cancel_before_payment_due_date(): void
    {
        $registration = TrainingRegistration::factory()->pending()->create([
            'payment_due_at' => now()->addDay(),
        ]);

        $this->artisan('registrations:cancel-expired')
            ->expectsOutputToContain('No expired registrations found.')
            ->assertExitCode(0);

        $this->assertEquals(RegistrationStatusEnum::Pending, $registration->fresh()->status);
    }

    public function test_does_not_cancel_registration_without_payment_due_date(): void
    {
        $registration = TrainingRegistration::factory()->pending()->create([
            'payment_due_at' => null,
        ]);

        $this->artisan('registrations:cancel-expired')
            ->expectsOutputToContain('No expired registrations found.')
            ->assertExitCode(0);

        $this->assertEquals(RegistrationStatusEnum::Pending, $registration->fresh()->status);
    }

    public function test_does_not_cancel_a_registration_paid_in_full(): void
    {
        $registration = $this->expiredPaidTrainingRegistration();

        Payment::factory()->forTrainingRegistration($registration)->create(['amount' => 25.00, 'currency' => 'EUR']);

        $this->artisan('registrations:cancel-expired')
            ->expectsOutputToContain('No expired registrations found.')
            ->assertExitCode(0);

        $this->assertEquals(RegistrationStatusEnum::Pending, $registration->fresh()->status);
    }

    public function test_cancels_a_partly_paid_registration_and_its_open_payment(): void
    {
        $registration = $this->expiredPaidTrainingRegistration();

        Payment::factory()->forTrainingRegistration($registration)->create(['amount' => 10.00, 'currency' => 'EUR']);
        $open = Payment::factory()->forTrainingRegistration($registration)->create([
            'amount' => 15.00,
            'currency' => 'EUR',
            'status' => PaymentStatusEnum::PENDING,
        ]);

        $this->artisan('registrations:cancel-expired')
            ->expectsOutputToContain('Cancelled 1 expired registration(s).')
            ->assertExitCode(0);

        $this->assertEquals(RegistrationStatusEnum::Cancelled, $registration->fresh()->status);
        $this->assertEquals(PaymentStatusEnum::CANCELLED, $open->fresh()->status);
    }

    public function test_leaves_an_open_gopay_payment_alone(): void
    {
        $registration = $this->expiredPaidTrainingRegistration();

        $gopay = Payment::factory()->gopay()->forTrainingRegistration($registration)->create([
            'amount' => 25.00,
            'currency' => 'EUR',
            'status' => PaymentStatusEnum::PENDING,
        ]);

        $this->artisan('registrations:cancel-expired')->assertExitCode(0);

        $this->assertEquals(RegistrationStatusEnum::Cancelled, $registration->fresh()->status);
        $this->assertEquals(PaymentStatusEnum::PENDING, $gopay->fresh()->status);
    }

    private function expiredPaidTrainingRegistration(): TrainingRegistration
    {
        $training = Training::factory()->create([
            'pricing_type' => TrainingPricingTypeEnum::PAID,
            'price_amount' => 25.00,
        ]);

        return TrainingRegistration::factory()->pending()->create([
            'training_id' => $training->id,
            'payment_due_at' => now()->subDay(),
        ]);
    }

    /**
     * Regression test for Sentry BCZ-APP-E: a soft-deleted training left an
     * expired pending registration behind. The command loaded `$registration
     * ->training` (null once the training is soft-deleted) and passed it into
     * TrainingCapacityService::handleSpotFreed(), which requires a non-null
     * Training, throwing a TypeError and exiting non-zero.
     */
    public function test_cancels_expired_registration_when_training_was_soft_deleted(): void
    {
        $training = Training::factory()->create();

        $registration = TrainingRegistration::factory()->pending()->create([
            'training_id' => $training->id,
            'payment_due_at' => now()->subDay(),
        ]);

        $training->delete();

        $this->artisan('registrations:cancel-expired')
            ->expectsOutputToContain('Cancelled 1 expired registration(s).')
            ->assertExitCode(0);

        $this->assertEquals(RegistrationStatusEnum::Cancelled, $registration->fresh()->status);
    }
}
