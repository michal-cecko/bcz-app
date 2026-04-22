<?php

namespace Tests\Feature\Filament\Events;

use App\Enums\PairingStrategyEnum;
use App\Filament\Resources\Events\Pages\ViewEvent;
use App\Filament\Resources\Events\RelationManagers\RoundsRelationManager;
use App\Models\AthleteCategory;
use App\Models\Battle;
use App\Models\CompetitionDetail;
use App\Models\CompetitionResult;
use App\Models\CompetitionRound;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\RoundPart;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RoundsRelationManagerGenerateTest extends TestCase
{
    use RefreshDatabase;

    public function test_manual_generate_action_creates_battles(): void
    {
        [$event, $detail, $category] = $this->makeContext();

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

        $this->actingAs(User::factory()->create());

        Livewire::test(RoundsRelationManager::class, [
            'ownerRecord' => $event->fresh(),
            'pageClass' => ViewEvent::class,
        ])
            ->callAction(
                [
                    TestAction::make('competitorOrder')->table($battleRound),
                    TestAction::make('generateBattles'),
                ],
                ['pairing_strategy' => PairingStrategyEnum::RANDOM->value],
            )
            ->assertNotified();

        $this->assertSame(1, $battleRound->fresh()->battles()->count());
    }

    public function test_manual_generate_overwrites_existing_battles(): void
    {
        [$event, $detail, $category] = $this->makeContext();

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

        Battle::factory()->count(3)->create(['competition_round_id' => $battleRound->id]);

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

        $this->actingAs(User::factory()->create());

        Livewire::test(RoundsRelationManager::class, [
            'ownerRecord' => $event->fresh(),
            'pageClass' => ViewEvent::class,
        ])->callAction(
            [
                TestAction::make('competitorOrder')->table($battleRound),
                TestAction::make('generateBattles'),
            ],
            ['pairing_strategy' => PairingStrategyEnum::RANDOM->value],
        );

        $this->assertSame(1, $battleRound->fresh()->battles()->count());
    }

    public function test_manual_generate_surfaces_error_when_competitor_count_invalid(): void
    {
        [$event, $detail, $category] = $this->makeContext();

        $qual = CompetitionRound::factory()->qualification()->create([
            'competition_detail_id' => $detail->id,
            'athlete_category_id' => $category->id,
            'sort_order' => 1,
        ]);
        RoundPart::factory()->create(['competition_round_id' => $qual->id]);

        $battleRound = CompetitionRound::factory()->battle(3, 2)->create([
            'competition_detail_id' => $detail->id,
            'athlete_category_id' => $category->id,
            'sort_order' => 2,
        ]);
        $battleRound->update(['previous_round_id' => $qual->id]);

        $this->actingAs(User::factory()->create());

        Livewire::test(RoundsRelationManager::class, [
            'ownerRecord' => $event->fresh(),
            'pageClass' => ViewEvent::class,
        ])
            ->callAction(
                [
                    TestAction::make('competitorOrder')->table($battleRound),
                    TestAction::make('generateBattles'),
                ],
                ['pairing_strategy' => PairingStrategyEnum::RANDOM->value],
            );

        $this->assertSame(0, $battleRound->fresh()->battles()->count());
    }

    /** @return array{0: Event, 1: CompetitionDetail, 2: AthleteCategory} */
    private function makeContext(): array
    {
        $event = Event::factory()->competition()->create();
        $detail = CompetitionDetail::factory()->create(['event_id' => $event->id]);
        $category = AthleteCategory::factory()->create();

        return [$event, $detail, $category];
    }
}
