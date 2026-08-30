<?php

namespace Tests\Feature\Commands;

use App\Enums\RegistrationStatusEnum;
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

    public function test_does_not_cancel_registration_with_completed_payment(): void
    {
        $registration = TrainingRegistration::factory()->pending()->create([
            'payment_due_at' => now()->subDay(),
        ]);

        Payment::factory()->forTrainingRegistration($registration)->create();

        $this->artisan('registrations:cancel-expired')
            ->expectsOutputToContain('No expired registrations found.')
            ->assertExitCode(0);

        $this->assertEquals(RegistrationStatusEnum::Pending, $registration->fresh()->status);
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
