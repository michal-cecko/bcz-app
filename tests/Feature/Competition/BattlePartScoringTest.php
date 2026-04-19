<?php

namespace Tests\Feature\Competition;

use App\Enums\ScoringFormatEnum;
use App\Filament\Resources\Events\Pages\ViewEvent;
use App\Filament\Resources\Events\RelationManagers\RoundsRelationManager;
use App\Models\AthleteCategory;
use App\Models\Battle;
use App\Models\BattlePartScore;
use App\Models\CompetitionDetail;
use App\Models\CompetitionRound;
use App\Models\Event;
use App\Models\RoundPart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Tests\TestCase;

class BattlePartScoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_persist_battle_part_score_creates_row_and_sets_auto_winner_when_complete(): void
    {
        [$battle, $parts, $users, $rm] = $this->setupBattle();

        $rm->call('persistBattlePartScore', $battle->id, $parts[0]->id, 'a', 60);
        $this->assertNull($battle->fresh()->winner_side);

        $rm->call('persistBattlePartScore', $battle->id, $parts[1]->id, 'a', 30);
        $this->assertNull($battle->fresh()->winner_side);

        $rm->call('persistBattlePartScore', $battle->id, $parts[0]->id, 'b', 50);
        $this->assertNull($battle->fresh()->winner_side);

        $rm->call('persistBattlePartScore', $battle->id, $parts[1]->id, 'b', 30);

        $fresh = $battle->fresh();
        $this->assertSame('a', $fresh->winner_side);
        $this->assertEquals(90.00, $fresh->side_a_score);
        $this->assertEquals(80.00, $fresh->side_b_score);
    }

    public function test_persist_battle_part_score_clears_winner_side_if_incomplete(): void
    {
        [$battle, $parts, , $rm] = $this->setupBattle();

        $rm->call('persistBattlePartScore', $battle->id, $parts[0]->id, 'a', 60);
        $rm->call('persistBattlePartScore', $battle->id, $parts[1]->id, 'a', 30);
        $rm->call('persistBattlePartScore', $battle->id, $parts[0]->id, 'b', 50);

        $this->assertNull($battle->fresh()->winner_side);
    }

    public function test_persist_battle_part_score_deletes_row_when_value_blank(): void
    {
        [$battle, $parts, , $rm] = $this->setupBattle();

        $rm->call('persistBattlePartScore', $battle->id, $parts[0]->id, 'a', 60);
        $this->assertDatabaseHas('battle_part_scores', [
            'battle_id' => $battle->id,
            'round_part_id' => $parts[0]->id,
            'side' => 'a',
        ]);

        $rm->call('persistBattlePartScore', $battle->id, $parts[0]->id, 'a', null);
        $this->assertDatabaseMissing('battle_part_scores', [
            'battle_id' => $battle->id,
            'round_part_id' => $parts[0]->id,
            'side' => 'a',
        ]);
    }

    public function test_total_accessors_sum_part_scores(): void
    {
        [$battle, $parts] = $this->setupBattle();

        BattlePartScore::create(['battle_id' => $battle->id, 'round_part_id' => $parts[0]->id, 'side' => 'a', 'score' => 25.50]);
        BattlePartScore::create(['battle_id' => $battle->id, 'round_part_id' => $parts[1]->id, 'side' => 'a', 'score' => 14.50]);
        BattlePartScore::create(['battle_id' => $battle->id, 'round_part_id' => $parts[0]->id, 'side' => 'b', 'score' => 30.00]);

        $fresh = $battle->fresh()->load('partScores');
        $this->assertEquals(40.00, $fresh->side_a_score);
        $this->assertEquals(30.00, $fresh->side_b_score);
    }

    /**
     * Create an event with a 2-part battle round, one battle with user A vs user B,
     * and return [$battle, [$part1, $part2], [$userA, $userB], $livewireRm].
     *
     * @return array{0: Battle, 1: array<int, RoundPart>, 2: array<int, User>, 3: Testable}
     */
    private function setupBattle(): array
    {
        $event = Event::factory()->competition()->create();
        $detail = CompetitionDetail::factory()->create(['event_id' => $event->id]);
        $category = AthleteCategory::factory()->create();

        $round = CompetitionRound::factory()->battle(2, 1)->create([
            'competition_detail_id' => $detail->id,
            'athlete_category_id' => $category->id,
            'scoring_format' => ScoringFormatEnum::POINTS,
        ]);

        $part1 = RoundPart::factory()->create(['competition_round_id' => $round->id, 'sort_order' => 1]);
        $part2 = RoundPart::factory()->create(['competition_round_id' => $round->id, 'sort_order' => 2]);

        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $battle = Battle::factory()->pair($userA, $userB)->create([
            'competition_round_id' => $round->id,
            'bracket_position' => 1,
        ]);

        $admin = User::factory()->create();
        $this->actingAs($admin);

        $rm = Livewire::test(RoundsRelationManager::class, [
            'ownerRecord' => $event->fresh(),
            'pageClass' => ViewEvent::class,
        ]);

        return [$battle, [$part1, $part2], [$userA, $userB], $rm];
    }
}
