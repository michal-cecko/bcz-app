<?php

namespace Tests\Feature\Services;

use App\Enums\MembershipStatusEnum;
use App\Enums\RegistrationStatusEnum;
use App\Enums\TrainingPricingTypeEnum;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Membership;
use App\Models\Training;
use App\Models\TrainingRegistration;
use App\Models\User;
use App\Services\RegistrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_already_registered_for_event_detects_a_matching_active_registration(): void
    {
        $user = User::factory()->create(['email' => 'coach@example.com']);
        $event = Event::factory()->competition()->create();
        EventRegistration::factory()->approved()->create(['event_id' => $event->id, 'user_id' => $user->id]);

        $this->assertTrue(RegistrationService::emailAlreadyRegisteredForEvent('coach@example.com', $event->id));
        // Case-insensitive.
        $this->assertTrue(RegistrationService::emailAlreadyRegisteredForEvent('COACH@example.com', $event->id));
        // Different email / different event.
        $this->assertFalse(RegistrationService::emailAlreadyRegisteredForEvent('someone@example.com', $event->id));
        $this->assertFalse(RegistrationService::emailAlreadyRegisteredForEvent('coach@example.com', Event::factory()->competition()->create()->id));
    }

    public function test_cancelled_registration_does_not_block_the_email(): void
    {
        $user = User::factory()->create(['email' => 'coach@example.com']);
        $event = Event::factory()->competition()->create();
        EventRegistration::factory()->create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'status' => RegistrationStatusEnum::Cancelled->value,
        ]);

        $this->assertFalse(RegistrationService::emailAlreadyRegisteredForEvent('coach@example.com', $event->id));
    }

    public function test_email_already_registered_for_training_detects_a_matching_active_registration(): void
    {
        $user = User::factory()->create(['email' => 'coach@example.com']);
        $training = Training::factory()->create();
        TrainingRegistration::factory()->create([
            'training_id' => $training->id,
            'user_id' => $user->id,
            'status' => RegistrationStatusEnum::Approved->value,
        ]);

        $this->assertTrue(RegistrationService::emailAlreadyRegisteredForTraining('coach@example.com', $training->id));
        $this->assertFalse(RegistrationService::emailAlreadyRegisteredForTraining('other@example.com', $training->id));
    }

    public function test_membership_required_training_stays_pending_when_membership_has_not_started_yet(): void
    {
        $user = User::factory()->create();
        $training = Training::factory()->create([
            'pricing_type' => TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED,
        ]);

        // Membership exists and is "active", but its period only starts tomorrow — user has not
        // paid for / entered the current period yet, so registration must not auto-approve.
        Membership::factory()->create([
            'team_id' => $training->team_id,
            'user_id' => $user->id,
            'status' => MembershipStatusEnum::ACTIVE,
            'is_free' => true,
            'starts_at' => now()->addDay(),
            'ends_at' => now()->addMonths(4),
        ]);

        $this->assertSame(
            RegistrationStatusEnum::Pending,
            RegistrationService::determineRegistrationStatus($training, $user)
        );
        $this->assertSame(
            'membership_needed',
            RegistrationService::determinePostRegistrationState($training, $user)
        );
    }

    public function test_membership_required_training_is_approved_when_membership_is_currently_active(): void
    {
        $user = User::factory()->create();
        $training = Training::factory()->create([
            'pricing_type' => TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED,
        ]);

        Membership::factory()->create([
            'team_id' => $training->team_id,
            'user_id' => $user->id,
            'status' => MembershipStatusEnum::ACTIVE,
            'is_free' => true,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addMonths(4),
        ]);

        $this->assertSame(
            RegistrationStatusEnum::Approved,
            RegistrationService::determineRegistrationStatus($training, $user)
        );
        $this->assertSame(
            'membership_valid',
            RegistrationService::determinePostRegistrationState($training, $user)
        );
    }
}
