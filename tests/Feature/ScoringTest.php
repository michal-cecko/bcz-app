<?php

namespace Tests\Feature;

use App\Enums\RoundAdvancementTypeEnum;
use App\Enums\ScoringFormatEnum;
use App\Models\AthleteCategory;
use App\Models\Battle;
use App\Models\BattlePartScore;
use App\Models\CompetitionDetail;
use App\Models\CompetitionResult;
use App\Models\CompetitionRound;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\RoundPart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_qualification_round_scores_can_be_saved_and_retrieved(): void
    {
        $event = Event::factory()->competition()->create();
        $detail = CompetitionDetail::factory()->create(['event_id' => $event->id]);
        $category = AthleteCategory::factory()->create();

        $round = CompetitionRound::factory()->create([
            'competition_detail_id' => $detail->id,
            'athlete_category_id' => $category->id,
            'advancement_type' => RoundAdvancementTypeEnum::TOP_BY_POINTS,
        ]);

        $part = RoundPart::factory()->create(['competition_round_id' => $round->id]);

        $user = User::factory()->create();
        EventRegistration::factory()->approved()->create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'athlete_category_id' => $category->id,
        ]);

        CompetitionResult::create([
            'round_part_id' => $part->id,
            'user_id' => $user->id,
            'score' => 85.50,
            'place' => 1,
        ]);

        $this->assertTrue($round->isQualification());
        $this->assertFalse($round->isBattle());
        $this->assertEquals(85.50, $round->getTotalScoreForUser($user->id));

        $result = CompetitionResult::where('round_part_id', $part->id)
            ->where('user_id', $user->id)
            ->first();
        $this->assertEquals(1, $result->place);
    }

    public function test_battle_scores_can_be_saved(): void
    {
        $round = CompetitionRound::factory()->battle(2, 1)->create([
            'scoring_format' => ScoringFormatEnum::POINTS,
        ]);

        $part1 = RoundPart::factory()->create(['competition_round_id' => $round->id]);
        $part2 = RoundPart::factory()->create(['competition_round_id' => $round->id]);

        $userA = User::factory()->create(['first_name' => 'Player', 'last_name' => 'A']);
        $userB = User::factory()->create(['first_name' => 'Player', 'last_name' => 'B']);

        $battle = Battle::factory()
            ->pair($userA, $userB)
            ->create([
                'competition_round_id' => $round->id,
                'bracket_position' => 1,
            ]);

        foreach (['a' => [60.00, 30.00], 'b' => [50.00, 35.50]] as $side => [$s1, $s2]) {
            BattlePartScore::create(['battle_id' => $battle->id, 'round_part_id' => $part1->id, 'side' => $side, 'score' => $s1]);
            BattlePartScore::create(['battle_id' => $battle->id, 'round_part_id' => $part2->id, 'side' => $side, 'score' => $s2]);
        }

        $battle->load(['partScores', 'competitionRound.parts']);
        $battle->updateAutoWinner();
        $battle->refresh()->load(['partScores', 'competitionRound.parts']);

        $this->assertTrue($round->isBattle());
        $this->assertEquals(90.00, $battle->side_a_score);
        $this->assertEquals(85.50, $battle->side_b_score);
        $this->assertSame($userA->name, $battle->getCompetitorALabel());
        $this->assertSame($userB->name, $battle->getCompetitorBLabel());
        $this->assertSame('a', $battle->winner_side);
        $this->assertTrue($battle->hasCompleteScoring());
        $this->assertCount(1, $battle->sideA);
        $this->assertCount(1, $battle->sideB);
    }

    public function test_scores_published_toggle_works(): void
    {
        $round = CompetitionRound::factory()->create([
            'advancement_type' => RoundAdvancementTypeEnum::TOP_BY_POINTS,
            'scores_published' => false,
        ]);

        $this->assertFalse($round->scores_published);

        $round->update(['scores_published' => true]);
        $round->refresh();

        $this->assertTrue($round->scores_published);
    }

    public function test_round_total_score_sums_across_parts(): void
    {
        $event = Event::factory()->competition()->create();
        $detail = CompetitionDetail::factory()->create(['event_id' => $event->id]);
        $category = AthleteCategory::factory()->create();

        $round = CompetitionRound::factory()->create([
            'competition_detail_id' => $detail->id,
            'athlete_category_id' => $category->id,
            'advancement_type' => RoundAdvancementTypeEnum::TOP_BY_POINTS,
        ]);

        $part1 = RoundPart::factory()->create(['competition_round_id' => $round->id]);
        $part2 = RoundPart::factory()->create(['competition_round_id' => $round->id]);

        $user = User::factory()->create();

        CompetitionResult::create([
            'round_part_id' => $part1->id,
            'user_id' => $user->id,
            'score' => 40.00,
        ]);
        CompetitionResult::create([
            'round_part_id' => $part2->id,
            'user_id' => $user->id,
            'score' => 35.50,
        ]);

        $this->assertEquals(75.50, $round->getTotalScoreForUser($user->id));
    }

    public function test_ordered_competitors_returns_approved_registrations(): void
    {
        $event = Event::factory()->competition()->create();
        $detail = CompetitionDetail::factory()->create(['event_id' => $event->id]);
        $category = AthleteCategory::factory()->create();

        $round = CompetitionRound::factory()->create([
            'competition_detail_id' => $detail->id,
            'athlete_category_id' => $category->id,
            'advancement_type' => RoundAdvancementTypeEnum::TOP_BY_POINTS,
        ]);

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        EventRegistration::factory()->approved()->create([
            'event_id' => $event->id,
            'user_id' => $user1->id,
            'athlete_category_id' => $category->id,
            'registered_at' => now()->subHour(),
        ]);
        EventRegistration::factory()->approved()->create([
            'event_id' => $event->id,
            'user_id' => $user2->id,
            'athlete_category_id' => $category->id,
            'registered_at' => now(),
        ]);

        // Pending registration should not appear
        EventRegistration::factory()->create([
            'event_id' => $event->id,
            'user_id' => User::factory()->create()->id,
            'athlete_category_id' => $category->id,
            'status' => 'pending',
        ]);

        $competitors = $round->getOrderedCompetitors();
        $this->assertCount(2, $competitors);
        $this->assertTrue($competitors->first()->user->is($user1));
    }
}
