<?php

namespace Tests\Feature\Events;

use App\Models\AthleteCategory;
use App\Models\Battle;
use App\Models\CompetitionDetail;
use App\Models\CompetitionResult;
use App\Models\CompetitionRound;
use App\Models\Event;
use App\Models\EventRegistration;
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

    public function test_generated_but_unpublished_battles_show_matchups_not_tbd(): void
    {
        $event = Event::factory()->competition()->create(['is_published' => true]);
        $detail = CompetitionDetail::factory()->create(['event_id' => $event->id]);
        $category = AthleteCategory::factory()->create();

        // Battles generated, but the round's scores are NOT published yet.
        $suboje = CompetitionRound::factory()->battle()->create([
            'competition_detail_id' => $detail->id,
            'athlete_category_id' => $category->id,
            'name' => 'Súboje',
            'round_number' => 1,
            'sort_order' => 1,
            'scores_published' => false,
        ]);
        // A points finále follows, so Súboje is a real semifinal (not the bracket "finale").
        CompetitionRound::factory()->qualification()->create([
            'competition_detail_id' => $detail->id,
            'athlete_category_id' => $category->id,
            'name' => 'Finále',
            'round_number' => 2,
            'sort_order' => 2,
            'previous_round_id' => $suboje->id,
        ]);

        // User::name is derived from first_name/last_name via a save hook, so set first_name.
        $f1 = User::factory()->create(['first_name' => 'FighterAlpha']);
        $f2 = User::factory()->create(['first_name' => 'FighterBravo']);
        $f3 = User::factory()->create(['first_name' => 'FighterCharlie']);
        $f4 = User::factory()->create(['first_name' => 'FighterDelta']);
        Battle::factory()->pair($f1, $f2)->create(['competition_round_id' => $suboje->id, 'bracket_position' => 1]);
        Battle::factory()->pair($f3, $f4)->create(['competition_round_id' => $suboje->id, 'bracket_position' => 2]);

        $response = $this->get(route('event.show', $event));

        $response->assertOk();
        // Matchups render as soon as battles exist — no "TBD vs TBD".
        $response->assertSee('FighterAlpha', false);
        $response->assertSee('FighterBravo', false);
        $response->assertSee('FighterCharlie', false);
        $response->assertSee('FighterDelta', false);
    }

    public function test_advancing_ids_are_the_previous_rounds_battle_winners(): void
    {
        $detail = CompetitionDetail::factory()->create();
        $category = AthleteCategory::factory()->create();

        $suboje = CompetitionRound::factory()->battle()->create([
            'competition_detail_id' => $detail->id,
            'athlete_category_id' => $category->id,
            'sort_order' => 1,
        ]);
        $finale = CompetitionRound::factory()->qualification()->create([
            'competition_detail_id' => $detail->id,
            'athlete_category_id' => $category->id,
            'sort_order' => 2,
            'previous_round_id' => $suboje->id,
        ]);

        [$a, $b, $c, $d] = User::factory()->count(4)->create()->all();
        Battle::factory()->pair($a, $b, $a)->create(['competition_round_id' => $suboje->id, 'bracket_position' => 1]);
        Battle::factory()->pair($c, $d, $c)->create(['competition_round_id' => $suboje->id, 'bracket_position' => 2]);

        $ids = $finale->advancingCompetitorIds(collect([$suboje, $finale]));

        // Only the two battle winners advance — not the losers.
        $this->assertEqualsCanonicalizing([$a->id, $c->id], $ids->all());
    }

    public function test_advancing_ids_from_a_score_round_are_the_top_scorers(): void
    {
        $detail = CompetitionDetail::factory()->create();
        $category = AthleteCategory::factory()->create();

        $qual = CompetitionRound::factory()->qualification()->create([
            'competition_detail_id' => $detail->id,
            'athlete_category_id' => $category->id,
            'sort_order' => 1,
        ]);
        // Finále takes the top 2 by score.
        $finale = CompetitionRound::factory()->qualification(2)->create([
            'competition_detail_id' => $detail->id,
            'athlete_category_id' => $category->id,
            'sort_order' => 2,
            'previous_round_id' => $qual->id,
        ]);

        $part = RoundPart::factory()->create(['competition_round_id' => $qual->id, 'name' => ['sk' => 'Statika']]);
        [$u0, $u1, $u2, $u3] = User::factory()->count(4)->create()->all();
        foreach ([[$u0, 10], [$u1, 40], [$u2, 20], [$u3, 30]] as [$user, $score]) {
            CompetitionResult::factory()->create([
                'round_part_id' => $part->id,
                'user_id' => $user->id,
                'score' => $score,
            ]);
        }

        $ids = $finale->advancingCompetitorIds(collect([$qual, $finale]));

        // Top 2 by score, in rank order: u1 (40) then u3 (30).
        $this->assertSame([$u1->id, $u3->id], $ids->all());
    }

    public function test_advancing_ids_are_null_when_the_previous_round_is_not_decided_yet(): void
    {
        $detail = CompetitionDetail::factory()->create();
        $category = AthleteCategory::factory()->create();

        // Battle round with battles but no winner decided → provisional field (null).
        $suboje = CompetitionRound::factory()->battle()->create([
            'competition_detail_id' => $detail->id, 'athlete_category_id' => $category->id, 'sort_order' => 1,
        ]);
        $finaleAfterBattle = CompetitionRound::factory()->qualification(2)->create([
            'competition_detail_id' => $detail->id, 'athlete_category_id' => $category->id, 'sort_order' => 2, 'previous_round_id' => $suboje->id,
        ]);
        [$a, $b] = User::factory()->count(2)->create()->all();
        Battle::factory()->pair($a, $b)->create(['competition_round_id' => $suboje->id, 'bracket_position' => 1]);

        $this->assertNull($finaleAfterBattle->advancingCompetitorIds(collect([$suboje, $finaleAfterBattle])));

        // Score round with no results yet → also provisional field (null), not an empty set.
        $qual = CompetitionRound::factory()->qualification()->create([
            'competition_detail_id' => $detail->id, 'athlete_category_id' => $category->id, 'sort_order' => 3,
        ]);
        $finaleAfterScore = CompetitionRound::factory()->qualification(2)->create([
            'competition_detail_id' => $detail->id, 'athlete_category_id' => $category->id, 'sort_order' => 4, 'previous_round_id' => $qual->id,
        ]);
        RoundPart::factory()->create(['competition_round_id' => $qual->id, 'name' => ['sk' => 'Zostava']]);

        $this->assertNull($finaleAfterScore->advancingCompetitorIds(collect([$qual, $finaleAfterScore])));
    }

    public function test_get_advanced_competitors_returns_only_the_previous_rounds_winners(): void
    {
        // This is what the admin scoring/order views use — it must match the public display.
        $event = Event::factory()->competition()->create(['is_published' => true]);
        $detail = CompetitionDetail::factory()->create(['event_id' => $event->id]);
        $category = AthleteCategory::factory()->create();

        $suboje = CompetitionRound::factory()->battle()->create([
            'competition_detail_id' => $detail->id, 'athlete_category_id' => $category->id, 'name' => 'Súboje', 'sort_order' => 1,
        ]);
        $finale = CompetitionRound::factory()->qualification()->create([
            'competition_detail_id' => $detail->id, 'athlete_category_id' => $category->id, 'name' => 'Finále', 'sort_order' => 2, 'previous_round_id' => $suboje->id,
        ]);

        [$a, $b, $c, $d] = User::factory()->count(4)->create()->all();
        Battle::factory()->pair($a, $b, $a)->create(['competition_round_id' => $suboje->id, 'bracket_position' => 1]);
        Battle::factory()->pair($c, $d, $c)->create(['competition_round_id' => $suboje->id, 'bracket_position' => 2]);
        foreach ([$a, $b, $c, $d] as $u) {
            EventRegistration::factory()->approved()->create(['event_id' => $event->id, 'athlete_category_id' => $category->id, 'user_id' => $u->id]);
        }

        $advancedIds = $finale->getAdvancedCompetitors()->pluck('user_id')->all();

        // Only the two Súboje winners belong in the finále — not all four registrants.
        $this->assertEqualsCanonicalizing([$a->id, $c->id], $advancedIds);
    }
}
