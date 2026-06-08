<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Team;
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
}
