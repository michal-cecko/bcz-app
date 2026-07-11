<?php

namespace Tests\Feature\Services;

use App\Enums\RegistrationStatusEnum;
use App\Models\Event;
use App\Models\EventRegistration;
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
}
