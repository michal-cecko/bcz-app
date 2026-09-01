<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Certification;
use App\Models\CoachProfile;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoachCertificationsPageTest extends TestCase
{
    use RefreshDatabase;

    private function approvedCoach(): User
    {
        $team = Team::factory()->create();
        $user = User::factory()->create();
        $user->teams()->attach($team->id, [
            'role' => RoleEnum::COACH->value,
            'is_active' => true,
            'joined_at' => now(),
        ]);
        $user->update(['coach_profile_approved_at' => now()]);
        CoachProfile::factory()->create(['user_id' => $user->id]);

        return $user;
    }

    public function test_coach_detail_page_renders_for_coach_with_a_certification_without_an_icon(): void
    {
        $user = $this->approvedCoach();

        Certification::factory()->create([
            'certifiable_id' => $user->id,
            'certifiable_type' => User::class,
            'name' => ['sk' => 'Kondičný tréner IV. kvalifikačného stupňa'],
            'icon' => null,
            'sort_order' => 0,
        ]);

        $response = $this->get(route('coach.show', $user));

        $response->assertStatus(200);
        $response->assertSee('Kondičný tréner IV. kvalifikačného stupňa', false);
    }

    public function test_coach_detail_page_renders_the_certification_icon_column(): void
    {
        $user = $this->approvedCoach();

        Certification::factory()->create([
            'certifiable_id' => $user->id,
            'certifiable_type' => User::class,
            'name' => ['sk' => 'Tréner parkouru'],
            'icon' => 'heroicon-o-academic-cap',
            'sort_order' => 0,
        ]);

        $response = $this->get(route('coach.show', $user));

        $response->assertStatus(200);
        $response->assertSee('Tréner parkouru', false);
    }
}
