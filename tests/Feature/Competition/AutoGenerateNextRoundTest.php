<?php

namespace Tests\Feature\Competition;

use App\Enums\PairingStrategyEnum;
use App\Enums\ScoringFormatEnum;
use App\Filament\Resources\Events\Pages\ViewEvent;
use App\Filament\Resources\Events\RelationManagers\RoundsRelationManager;
use App\Models\AthleteCategory;
use App\Models\Battle;
use App\Models\CompetitionDetail;
use App\Models\CompetitionRound;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\RoundPart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AutoGenerateNextRoundTest extends TestCase
{
    use RefreshDatabase;

    public function test_next_round_battles_auto_generate_when_qualification_complete(): void
    {
        [$event, $detail, $category] = $this->makeContext();

        $qual = CompetitionRound::factory()->qualification()->create([
            'competition_detail_id' => $detail->id,
            'athlete_category_id' => $category->id,
            'sort_order' => 1,
            'scoring_format' => ScoringFormatEnum::POINTS,
        ]);
        $part = RoundPart::factory()->create(['competition_round_id' => $qual->id]);

        $final = CompetitionRound::factory()->battle(2, 1, PairingStrategyEnum::SEEDED)->create([
            'competition_detail_id' => $detail->id,
            'athlete_category_id' => $category->id,
            'sort_order' => 2,
        ]);
        $final->update(['previous_round_id' => $qual->id]);

        $users = User::factory()->count(4)->create();
        foreach ($users as $user) {
            EventRegistration::factory()->approved()->create([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'athlete_category_id' => $category->id,
            ]);
        }

        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        $rm = Livewire::test(RoundsRelationManager::class, [
            'ownerRecord' => $event->fresh(),
            'pageClass' => ViewEvent::class,
        ]);

        $rm->call('persistScore', $part->id, $users[0]->id, 90);
        $rm->call('persistScore', $part->id, $users[1]->id, 80);
        $rm->call('persistScore', $part->id, $users[2]->id, 70);
        $this->assertSame(0, $final->battles()->count());

        $rm->call('persistScore', $part->id, $users[3]->id, 60);

        $this->assertSame(1, $final->fresh()->battles()->count());
    }

    public function test_auto_generation_does_not_overwrite_existing_battles(): void
    {
        [$event, $detail, $category] = $this->makeContext();

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

        $existingBattle = Battle::factory()->create(['competition_round_id' => $final->id]);

        $users = User::factory()->count(2)->create();
        foreach ($users as $user) {
            EventRegistration::factory()->approved()->create([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'athlete_category_id' => $category->id,
            ]);
        }

        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        Livewire::test(RoundsRelationManager::class, [
            'ownerRecord' => $event->fresh(),
            'pageClass' => ViewEvent::class,
        ])
            ->call('persistScore', $part->id, $users[0]->id, 90)
            ->call('persistScore', $part->id, $users[1]->id, 80);

        $this->assertSame(1, $final->battles()->count());
        $this->assertTrue($final->battles()->first()->is($existingBattle));
    }

    public function test_auto_generation_skips_when_next_round_is_not_battle(): void
    {
        [$event, $detail, $category] = $this->makeContext();

        $qual = CompetitionRound::factory()->qualification()->create([
            'competition_detail_id' => $detail->id,
            'athlete_category_id' => $category->id,
            'sort_order' => 1,
        ]);
        $part = RoundPart::factory()->create(['competition_round_id' => $qual->id]);

        $secondQual = CompetitionRound::factory()->qualification()->create([
            'competition_detail_id' => $detail->id,
            'athlete_category_id' => $category->id,
            'sort_order' => 2,
        ]);
        $secondQual->update(['previous_round_id' => $qual->id]);

        $users = User::factory()->count(2)->create();
        foreach ($users as $user) {
            EventRegistration::factory()->approved()->create([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'athlete_category_id' => $category->id,
            ]);
        }

        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        Livewire::test(RoundsRelationManager::class, [
            'ownerRecord' => $event->fresh(),
            'pageClass' => ViewEvent::class,
        ])
            ->call('persistScore', $part->id, $users[0]->id, 90)
            ->call('persistScore', $part->id, $users[1]->id, 80);

        $this->assertSame(0, $secondQual->battles()->count());
    }

    /** @return array{0: Event, 1: CompetitionDetail, 2: AthleteCategory} */
    private function makeContext(): array
    {
        $event = Event::factory()->competition()->create();
        $detail = CompetitionDetail::factory()->create(['event_id' => $event->id]);
        $category = AthleteCategory::factory()->create();

        return [$event, $detail, $category];
    }

    private function makeAdmin(): User
    {
        return User::factory()->create();
    }
}
