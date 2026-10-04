<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Event;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamDetailPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_detail_renders_with_organized_competitions(): void
    {
        $team = Team::factory()->create([
            'name' => ['sk' => 'BCZ Club'],
            'slug' => 'bcz-club',
            'story' => ['sk' => 'Náš príbeh.'],
        ]);

        // Regression: the competitions card previously called getTranslation('name'/'description')
        // on an Event (whose translatable keys are title/card_description), throwing a 500
        // whenever a team actually had an organized competition.
        $competition = Event::factory()->competition()->create([
            'team_id' => $team->id,
            'title' => ['sk' => 'MSR Street Workout 2024'],
            'card_description' => ['sk' => 'Majstrovstvá Slovenska.'],
        ]);

        $response = $this->get('/timy/'.$team->slug);

        $response->assertStatus(200);
        $response->assertSee('MSR Street Workout 2024', false);
    }

    public function test_team_detail_renders_without_competitions(): void
    {
        $team = Team::factory()->create([
            'name' => ['sk' => 'Empty Team'],
            'slug' => 'empty-team',
        ]);

        $response = $this->get('/timy/'.$team->slug);

        $response->assertStatus(200);
        $response->assertSee('"@type":"SportsTeam"', false);
    }

    public function test_team_pages_list_coaches_and_publicly_approved_athletes_only(): void
    {
        $team = Team::factory()->create(['slug' => 'bcz-club']);

        $coach = User::factory()->create(['first_name' => 'Coach', 'last_name' => 'Visible']);
        $publicAthlete = User::factory()->create(['first_name' => 'Athlete', 'last_name' => 'Public', 'athlete_profile_approved_at' => now()]);
        $privateAthlete = User::factory()->create(['first_name' => 'Athlete', 'last_name' => 'Private', 'athlete_profile_approved_at' => null]);
        $inactiveCoach = User::factory()->create(['first_name' => 'Coach', 'last_name' => 'Gone']);
        $teamAdmin = User::factory()->create(['first_name' => 'Admin', 'last_name' => 'Hidden']);

        $team->members()->attach($coach, ['role' => RoleEnum::COACH->value, 'is_active' => true]);
        $team->members()->attach($coach, ['role' => RoleEnum::ATHLETE->value, 'is_active' => true]);
        $team->members()->attach($publicAthlete, ['role' => RoleEnum::ATHLETE->value, 'is_active' => true]);
        $team->members()->attach($privateAthlete, ['role' => RoleEnum::ATHLETE->value, 'is_active' => true]);
        $team->members()->attach($inactiveCoach, ['role' => RoleEnum::COACH->value, 'is_active' => false]);
        $team->members()->attach($teamAdmin, ['role' => RoleEnum::TEAM_ADMIN->value, 'is_active' => true]);

        foreach (['/timy/bcz-club', '/timy/bcz-club/clenovia'] as $url) {
            $this->get($url)
                ->assertOk()
                ->assertSee('Coach Visible')
                ->assertSee('Athlete Public')
                ->assertDontSee('Athlete Private')
                ->assertDontSee('Coach Gone')
                ->assertDontSee('Admin Hidden');
        }

        $this->get('/timy/bcz-club/clenovia')->assertSee('2 členovia');
    }
}
