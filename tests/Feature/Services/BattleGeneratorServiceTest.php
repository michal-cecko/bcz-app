<?php

namespace Tests\Feature\Services;

use App\Enums\PairingStrategyEnum;
use App\Exceptions\BattleGenerationException;
use App\Models\AthleteCategory;
use App\Models\Battle;
use App\Models\CompetitionDetail;
use App\Models\CompetitionResult;
use App\Models\CompetitionRound;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\RoundPart;
use App\Models\User;
use App\Services\BattleGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BattleGeneratorServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_throws_when_round_is_not_battle_type(): void
    {
        $round = CompetitionRound::factory()->qualification(4)->create();

        $this->expectException(BattleGenerationException::class);

        app(BattleGeneratorService::class)->generate($round);
    }

    public function test_generate_throws_when_competitor_count_not_divisible_by_slots(): void
    {
        [$event, $detail, $category] = $this->makeCompetitionContext();

        $previousRound = CompetitionRound::factory()->qualification()->create([
            'competition_detail_id' => $detail->id,
            'athlete_category_id' => $category->id,
            'sort_order' => 1,
        ]);
        $round = CompetitionRound::factory()->battle(6, 2)->create([
            'competition_detail_id' => $detail->id,
            'athlete_category_id' => $category->id,
            'sort_order' => 2,
        ]);
        $round->update(['previous_round_id' => $previousRound->id]);

        $this->expectException(BattleGenerationException::class);
        app(BattleGeneratorService::class)->generate($round);
    }

    public function test_generate_creates_1v1_battles_from_qualification(): void
    {
        [$event, $detail, $category] = $this->makeCompetitionContext();

        $qual = CompetitionRound::factory()->qualification()->create([
            'competition_detail_id' => $detail->id,
            'athlete_category_id' => $category->id,
            'sort_order' => 1,
        ]);
        $part = RoundPart::factory()->create(['competition_round_id' => $qual->id]);

        $battleRound = CompetitionRound::factory()->battle(4, 1)->create([
            'competition_detail_id' => $detail->id,
            'athlete_category_id' => $category->id,
            'sort_order' => 2,
        ]);
        $battleRound->update(['previous_round_id' => $qual->id]);

        $users = User::factory()->count(4)->create();
        foreach ($users as $i => $user) {
            EventRegistration::factory()->approved()->create([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'athlete_category_id' => $category->id,
            ]);
            CompetitionResult::create([
                'round_part_id' => $part->id,
                'user_id' => $user->id,
                'score' => 100 - $i * 10,
            ]);
        }

        $battles = app(BattleGeneratorService::class)->generate(
            $battleRound,
            PairingStrategyEnum::SEEDED,
        );

        $this->assertCount(2, $battles);

        $battleRound->load('battles.sideA.user', 'battles.sideB.user');
        $first = $battleRound->battles->firstWhere('bracket_position', 1);
        $second = $battleRound->battles->firstWhere('bracket_position', 2);

        $this->assertSame($users[0]->id, $first->sideA->first()->user_id);
        $this->assertSame($users[3]->id, $first->sideB->first()->user_id);
        $this->assertSame($users[1]->id, $second->sideA->first()->user_id);
        $this->assertSame($users[2]->id, $second->sideB->first()->user_id);
    }

    public function test_generate_creates_2v2_team_battles(): void
    {
        [$event, $detail, $category] = $this->makeCompetitionContext();

        $firstRound = CompetitionRound::factory()->qualification()->create([
            'competition_detail_id' => $detail->id,
            'athlete_category_id' => $category->id,
            'sort_order' => 1,
        ]);
        $part = RoundPart::factory()->create(['competition_round_id' => $firstRound->id]);

        $battleRound = CompetitionRound::factory()->battle(8, 2, PairingStrategyEnum::SEEDED)->create([
            'competition_detail_id' => $detail->id,
            'athlete_category_id' => $category->id,
            'sort_order' => 2,
        ]);
        $battleRound->update(['previous_round_id' => $firstRound->id]);

        $users = User::factory()->count(8)->create();
        foreach ($users as $i => $user) {
            EventRegistration::factory()->approved()->create([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'athlete_category_id' => $category->id,
            ]);
            CompetitionResult::create([
                'round_part_id' => $part->id,
                'user_id' => $user->id,
                'score' => 100 - $i * 5,
            ]);
        }

        $battles = app(BattleGeneratorService::class)->generate($battleRound);

        $this->assertCount(2, $battles);

        $battleRound->load('battles.sideA', 'battles.sideB');
        $first = $battleRound->battles->firstWhere('bracket_position', 1);
        $this->assertCount(2, $first->sideA);
        $this->assertCount(2, $first->sideB);

        $sideAIds = $first->sideA->pluck('user_id')->all();
        $sideBIds = $first->sideB->pluck('user_id')->all();
        $this->assertSame([$users[0]->id, $users[1]->id], $sideAIds);
        $this->assertSame([$users[6]->id, $users[7]->id], $sideBIds);
    }

    public function test_generate_throws_if_battles_exist_without_overwrite(): void
    {
        [$event, $detail, $category] = $this->makeCompetitionContext();

        $previousRound = CompetitionRound::factory()->qualification()->create([
            'competition_detail_id' => $detail->id,
            'athlete_category_id' => $category->id,
            'sort_order' => 1,
        ]);
        $round = CompetitionRound::factory()->battle(2, 1)->create([
            'competition_detail_id' => $detail->id,
            'athlete_category_id' => $category->id,
            'sort_order' => 2,
        ]);
        $round->update(['previous_round_id' => $previousRound->id]);

        Battle::factory()->create(['competition_round_id' => $round->id]);

        $this->expectException(BattleGenerationException::class);
        app(BattleGeneratorService::class)->generate($round);
    }

    public function test_generate_overwrites_when_flag_set(): void
    {
        [$event, $detail, $category] = $this->makeCompetitionContext();

        $qual = CompetitionRound::factory()->qualification()->create([
            'competition_detail_id' => $detail->id,
            'athlete_category_id' => $category->id,
            'sort_order' => 1,
        ]);
        $part = RoundPart::factory()->create(['competition_round_id' => $qual->id]);

        $battleRound = CompetitionRound::factory()->battle(2, 1)->create([
            'competition_detail_id' => $detail->id,
            'athlete_category_id' => $category->id,
            'sort_order' => 2,
        ]);
        $battleRound->update(['previous_round_id' => $qual->id]);

        $users = User::factory()->count(2)->create();
        foreach ($users as $i => $user) {
            EventRegistration::factory()->approved()->create([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'athlete_category_id' => $category->id,
            ]);
            CompetitionResult::create([
                'round_part_id' => $part->id,
                'user_id' => $user->id,
                'score' => 100 - $i,
            ]);
        }

        Battle::factory()->create(['competition_round_id' => $battleRound->id]);
        $this->assertSame(1, $battleRound->battles()->count());

        app(BattleGeneratorService::class)->generate($battleRound, null, overwrite: true);

        $this->assertSame(1, $battleRound->battles()->count());
    }

    public function test_generate_with_third_place_creates_final_and_bronze_battles(): void
    {
        [$event, $detail, $category] = $this->makeCompetitionContext();

        $semi = CompetitionRound::factory()->battle(4, 1)->create([
            'competition_detail_id' => $detail->id,
            'athlete_category_id' => $category->id,
            'sort_order' => 1,
        ]);
        $final = CompetitionRound::factory()->battle(4, 1)->create([
            'competition_detail_id' => $detail->id,
            'athlete_category_id' => $category->id,
            'sort_order' => 2,
        ]);
        $final->update(['previous_round_id' => $semi->id]);

        $usersA = User::factory()->count(2)->create();
        $usersB = User::factory()->count(2)->create();
        $winnerA = $usersA[0];
        $loserA = $usersA[1];
        $winnerB = $usersB[0];
        $loserB = $usersB[1];

        Battle::factory()
            ->pair($winnerA, $loserA, $winnerA)
            ->create([
                'competition_round_id' => $semi->id,
                'athlete_category_id' => $category->id,
                'bracket_position' => 1,
            ]);
        Battle::factory()
            ->pair($winnerB, $loserB, $winnerB)
            ->create([
                'competition_round_id' => $semi->id,
                'athlete_category_id' => $category->id,
                'bracket_position' => 2,
            ]);

        $battles = app(BattleGeneratorService::class)->generate($final);

        $this->assertCount(2, $battles);

        $final->load('battles.sideA', 'battles.sideB');
        $finalBattle = $final->battles->firstWhere('bracket_position', 1);
        $thirdBattle = $final->battles->firstWhere('bracket_position', 2);

        $this->assertSame($winnerA->id, $finalBattle->sideA->first()->user_id);
        $this->assertSame($winnerB->id, $finalBattle->sideB->first()->user_id);

        $this->assertSame($loserA->id, $thirdBattle->sideA->first()->user_id);
        $this->assertSame($loserB->id, $thirdBattle->sideB->first()->user_id);
    }

    public function test_is_downstream_battle_stale_detects_score_changes_in_qualification(): void
    {
        [$event, $detail, $category] = $this->makeCompetitionContext();

        $qual = CompetitionRound::factory()->qualification()->create([
            'competition_detail_id' => $detail->id,
            'athlete_category_id' => $category->id,
            'sort_order' => 1,
        ]);
        $part = RoundPart::factory()->create(['competition_round_id' => $qual->id]);

        $final = CompetitionRound::factory()->battle(2, 1)->create([
            'competition_detail_id' => $detail->id,
            'athlete_category_id' => $category->id,
            'sort_order' => 2,
        ]);
        $final->update(['previous_round_id' => $qual->id]);

        $users = User::factory()->count(4)->create();
        foreach ($users as $i => $user) {
            EventRegistration::factory()->approved()->create([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'athlete_category_id' => $category->id,
            ]);
            CompetitionResult::create([
                'round_part_id' => $part->id,
                'user_id' => $user->id,
                'score' => 100 - $i * 10,
            ]);
        }

        $service = app(BattleGeneratorService::class);
        $service->generate($final, PairingStrategyEnum::SEEDED);

        $this->assertFalse($service->isDownstreamBattleStale($qual));

        CompetitionResult::query()
            ->where('round_part_id', $part->id)
            ->where('user_id', $users[0]->id)
            ->update(['score' => 200]);

        $this->assertFalse($service->isDownstreamBattleStale($qual));

        CompetitionResult::query()
            ->where('round_part_id', $part->id)
            ->where('user_id', $users[1]->id)
            ->update(['score' => 5]);

        $this->assertTrue($service->isDownstreamBattleStale($qual));
    }

    public function test_get_competitors_resolves_from_previous_battle_round_winners_and_losers(): void
    {
        [$event, $detail, $category] = $this->makeCompetitionContext();

        $semi = CompetitionRound::factory()->battle(4, 1)->create([
            'competition_detail_id' => $detail->id,
            'athlete_category_id' => $category->id,
            'sort_order' => 1,
        ]);
        $final = CompetitionRound::factory()->battle(4, 1)->create([
            'competition_detail_id' => $detail->id,
            'athlete_category_id' => $category->id,
            'sort_order' => 2,
        ]);
        $final->update(['previous_round_id' => $semi->id]);

        $usersA = User::factory()->count(2)->create();
        $usersB = User::factory()->count(2)->create();

        $battle1Winner = $usersA[0];
        $battle1Loser = $usersA[1];
        $battle2Winner = $usersB[0];
        $battle2Loser = $usersB[1];

        Battle::factory()
            ->pair($usersA[0], $usersA[1], $battle1Winner)
            ->create([
                'competition_round_id' => $semi->id,
                'athlete_category_id' => $category->id,
                'bracket_position' => 1,
            ]);
        Battle::factory()
            ->pair($usersB[0], $usersB[1], $battle2Winner)
            ->create([
                'competition_round_id' => $semi->id,
                'athlete_category_id' => $category->id,
                'bracket_position' => 2,
            ]);

        $competitors = app(BattleGeneratorService::class)->getCompetitorsForRound($final);

        $this->assertCount(4, $competitors);
        $this->assertSame(
            [$battle1Winner->id, $battle2Winner->id, $battle1Loser->id, $battle2Loser->id],
            $competitors->pluck('id')->all(),
        );
    }

    /** @return array{0: Event, 1: CompetitionDetail, 2: AthleteCategory} */
    private function makeCompetitionContext(): array
    {
        $event = Event::factory()->competition()->create();
        $detail = CompetitionDetail::factory()->create(['event_id' => $event->id]);
        $category = AthleteCategory::factory()->create();

        return [$event, $detail, $category];
    }
}
