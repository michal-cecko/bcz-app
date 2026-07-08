<?php

namespace Tests\Feature\Filament\Events;

use App\Enums\RoleEnum;
use App\Enums\RoundAdvancementTypeEnum;
use App\Filament\Resources\Events\Pages\EditEvent;
use App\Filament\Resources\Events\RelationManagers\RoundsRelationManager;
use App\Models\AthleteCategory;
use App\Models\CompetitionDetail;
use App\Models\CompetitionRound;
use App\Models\Discipline;
use App\Models\Event;
use App\Models\RoundPart;
use App\Models\Team;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoundsRelationManagerPartsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Team $team;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }

        $this->team = Team::factory()->create();
        $this->admin = User::factory()->create();
        $this->admin->assignRole(RoleEnum::SUPER_ADMIN);
        $this->admin->teams()->attach($this->team);

        $this->actingAs($this->admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::setTenant($this->team);
        Filament::bootCurrentPanel();
    }

    public function test_creating_a_round_persists_parts_with_translatable_names(): void
    {
        [$event, $detail, $category] = $this->makeContext();

        Livewire::test(RoundsRelationManager::class, [
            'ownerRecord' => $event->fresh(),
            'pageClass' => EditEvent::class,
        ])->callAction(TestAction::make('create')->table(), [
            'name' => 'Kvalifikácia',
            'round_number' => 1,
            'athlete_category_id' => $category->id,
            'advancement_type' => RoundAdvancementTypeEnum::TOP_BY_POINTS->value,
            'parts' => [
                ['name' => ['sk' => 'Statika'], 'duration_seconds' => 30],
                ['name' => ['sk' => 'Dynamika'], 'duration_seconds' => null],
            ],
        ]);

        $round = CompetitionRound::where('competition_detail_id', $detail->id)->firstOrFail();

        $this->assertSame(2, $round->parts()->count());

        $parts = $round->parts()->orderBy('sort_order')->get();
        $this->assertSame('Statika', $parts[0]->getTranslation('name', 'sk'));
        $this->assertSame(30, $parts[0]->duration_seconds);
        $this->assertSame('Dynamika', $parts[1]->getTranslation('name', 'sk'));
        $this->assertTrue($parts[0]->sort_order < $parts[1]->sort_order);
    }

    public function test_parts_default_to_all_competition_disciplines_on_create(): void
    {
        [$event, $detail, $category] = $this->makeContext();

        $statics = Discipline::factory()->create(['name' => ['sk' => 'Statika'], 'sort_order' => 1]);
        $dynamics = Discipline::factory()->create(['name' => ['sk' => 'Dynamika'], 'sort_order' => 2]);
        $detail->disciplines()->attach([$statics->id, $dynamics->id]);

        // No 'parts' key passed — the repeater default (all disciplines) must seed them.
        Livewire::test(RoundsRelationManager::class, [
            'ownerRecord' => $event->fresh(),
            'pageClass' => EditEvent::class,
        ])->callAction(TestAction::make('create')->table(), [
            'name' => 'Kvalifikácia',
            'round_number' => 1,
            'athlete_category_id' => $category->id,
            'advancement_type' => RoundAdvancementTypeEnum::TOP_BY_POINTS->value,
        ]);

        $round = CompetitionRound::where('competition_detail_id', $detail->id)->firstOrFail();

        $partNames = $round->parts()->orderBy('sort_order')->get()
            ->map(fn (RoundPart $p) => $p->getTranslation('name', 'sk'))
            ->all();

        $this->assertSame(['Statika', 'Dynamika'], $partNames);
    }

    /** @return array{0: Event, 1: CompetitionDetail, 2: AthleteCategory} */
    private function makeContext(): array
    {
        $event = Event::factory()->competition()->create(['team_id' => $this->team->id]);
        $detail = CompetitionDetail::factory()->create(['event_id' => $event->id]);
        $category = AthleteCategory::factory()->create();

        return [$event, $detail, $category];
    }
}
