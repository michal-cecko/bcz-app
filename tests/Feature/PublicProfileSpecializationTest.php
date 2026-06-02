<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\AthleteProfile;
use App\Models\CoachProfile;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicProfileSpecializationTest extends TestCase
{
    use RefreshDatabase;

    private function memberWithRole(RoleEnum $role): User
    {
        $team = Team::factory()->create();
        $user = User::factory()->create();
        $user->teams()->attach($team->id, [
            'role' => $role->value,
            'is_active' => true,
            'joined_at' => now(),
        ]);

        return $user;
    }

    public function test_coach_profile_shows_custom_specialization(): void
    {
        $user = $this->memberWithRole(RoleEnum::COACH);
        $user->update(['coach_profile_approved_at' => now()]);
        CoachProfile::factory()->create([
            'user_id' => $user->id,
            'specialization' => ['sk' => 'Kalistenika & Mobilita'],
        ]);

        $response = $this->get(route('coach.show', $user));

        $response->assertStatus(200);
        $response->assertSee('Tréner Kalistenika &amp; Mobilita', false);
    }

    public function test_coach_profile_falls_back_to_default_specialization(): void
    {
        $user = $this->memberWithRole(RoleEnum::COACH);
        $user->update(['coach_profile_approved_at' => now()]);
        CoachProfile::factory()->create([
            'user_id' => $user->id,
            'specialization' => null,
        ]);

        $response = $this->get(route('coach.show', $user));

        $response->assertStatus(200);
        $response->assertSee('Tréner Parkour &amp; Street Workout', false);
    }

    public function test_athlete_profile_shows_custom_specialization(): void
    {
        $user = $this->memberWithRole(RoleEnum::ATHLETE);
        $user->update(['athlete_profile_approved_at' => now()]);
        AthleteProfile::factory()->create([
            'user_id' => $user->id,
            'specialization' => ['sk' => 'Freerunning'],
        ]);

        $response = $this->get(route('athlete.show', $user));

        $response->assertStatus(200);
        $response->assertSee('Freerunning', false);
    }

    public function test_athlete_profile_falls_back_to_default_specialization(): void
    {
        $user = $this->memberWithRole(RoleEnum::ATHLETE);
        $user->update(['athlete_profile_approved_at' => now()]);
        AthleteProfile::factory()->create([
            'user_id' => $user->id,
            'specialization' => null,
        ]);

        $response = $this->get(route('athlete.show', $user));

        $response->assertStatus(200);
        $response->assertSee('Street Workout', false);
    }
}
