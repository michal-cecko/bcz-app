<?php

namespace Tests\Feature\Events;

use App\Models\AthleteCategory;
use App\Models\Battle;
use App\Models\CompetitionDetail;
use App\Models\CompetitionResult;
use App\Models\CompetitionRound;
use App\Models\Event;
use App\Models\RoundPart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicResultsTabTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Kvalifikácia (score) → Súboje (battle) → Finále (score): the battle is only a
     * semifinal, so its bracket must NOT be dressed up as the competition finale.
     */
    public function test_score_finale_after_a_battle_is_not_labelled_as_the_bracket_finale(): void
    {
        $event = Event::factory()->competition()->create(['is_published' => true]);
        $detail = CompetitionDetail::factory()->create(['event_id' => $event->id]);
        $category = AthleteCategory::factory()->create();

        $qual = CompetitionRound::factory()->qualification()->create([
            'competition_detail_id' => $detail->id,
            'athlete_category_id' => $category->id,
            'name' => 'Kvalifikácia',
            'round_number' => 1,
            'sort_order' => 1,
            'scores_published' => true,
        ]);
        $battle = CompetitionRound::factory()->battle()->create([
            'competition_detail_id' => $detail->id,
            'athlete_category_id' => $category->id,
            'name' => 'Súboje',
            'round_number' => 2,
            'sort_order' => 2,
            'scores_published' => true,
            'previous_round_id' => $qual->id,
        ]);
        $finale = CompetitionRound::factory()->qualification()->create([
            'competition_detail_id' => $detail->id,
            'athlete_category_id' => $category->id,
            'name' => 'Finále',
            'round_number' => 3,
            'sort_order' => 3,
            'scores_published' => true,
            'previous_round_id' => $battle->id,
        ]);

        [$a, $b, $c, $d] = User::factory()->count(4)->create();
        Battle::factory()->pair($a, $b, $a)->create([
            'competition_round_id' => $battle->id,
            'bracket_position' => 1,
        ]);
        Battle::factory()->pair($c, $d, $c)->create([
            'competition_round_id' => $battle->id,
            'bracket_position' => 2,
        ]);

        $part = RoundPart::factory()->create([
            'competition_round_id' => $finale->id,
            'name' => ['sk' => 'Statika'],
        ]);
        CompetitionResult::factory()->create([
            'round_part_id' => $part->id,
            'user_id' => $a->id,
            'score' => 90,
            'place' => 1,
        ]);

        $response = $this->get(route('event.show', $event));

        $response->assertOk();

        // Each stage is its own round-named tab, in sequence.
        $response->assertSee('Kvalifikácia', false);
        $response->assertSee('Súboje', false);
        $response->assertSee('Finále', false);

        // The Súboje bracket must NOT claim the finale / medal treatment.
        $response->assertDontSee('Finále — o 1. miesto', false);
        $response->assertDontSee('Battle o 3. miesto', false);
    }

    /**
     * When the competition genuinely ends in a battle bracket, that bracket keeps its
     * "final / 3rd-place" labels.
     */
    public function test_battle_finale_keeps_its_final_and_third_place_labels(): void
    {
        $event = Event::factory()->competition()->create(['is_published' => true]);
        $detail = CompetitionDetail::factory()->create(['event_id' => $event->id]);
        $category = AthleteCategory::factory()->create();

        $qual = CompetitionRound::factory()->qualification()->create([
            'competition_detail_id' => $detail->id,
            'athlete_category_id' => $category->id,
            'name' => 'Kvalifikácia',
            'round_number' => 1,
            'sort_order' => 1,
            'scores_published' => true,
        ]);
        $finaleBattle = CompetitionRound::factory()->battle()->create([
            'competition_detail_id' => $detail->id,
            'athlete_category_id' => $category->id,
            'name' => 'Finále',
            'round_number' => 2,
            'sort_order' => 2,
            'scores_published' => true,
            'previous_round_id' => $qual->id,
        ]);

        [$a, $b, $c, $d] = User::factory()->count(4)->create();
        Battle::factory()->pair($a, $b, $a)->create([
            'competition_round_id' => $finaleBattle->id,
            'bracket_position' => 1,
        ]);
        Battle::factory()->pair($c, $d, $c)->create([
            'competition_round_id' => $finaleBattle->id,
            'bracket_position' => 2,
        ]);

        $response = $this->get(route('event.show', $event));

        $response->assertOk();
        $response->assertSee('Finále — o 1. miesto', false);
    }
}
