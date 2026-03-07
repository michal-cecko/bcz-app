<?php

namespace Tests\Feature\Filament;

use App\Enums\RoleEnum;
use App\Filament\Resources\Competitions\Pages\CreateCompetition;
use App\Filament\Resources\Competitions\Pages\EditCompetition;
use App\Filament\Resources\Competitions\Pages\ListCompetitions;
use App\Filament\Resources\Competitions\RelationManagers\RegistrationsRelationManager;
use App\Filament\Resources\Competitions\RelationManagers\RoundsRelationManager;
use App\Filament\Resources\Competitions\RelationManagers\TimetableRelationManager;
use App\Models\Competition;
use App\Models\Team;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CompetitionResourceTest extends TestCase
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

    public function test_can_list_competitions(): void
    {
        $competitions = Competition::factory()->count(3)->create([
            'organizer_team_id' => $this->team->id,
        ]);

        Livewire::test(ListCompetitions::class)
            ->assertOk()
            ->assertCanSeeTableRecords($competitions);
    }

    public function test_can_create_competition(): void
    {
        Livewire::test(CreateCompetition::class)
            ->fillForm([
                'name.sk' => 'BCZ Championship 2026',
                'date_start' => '2026-09-15',
                'organizer_team_id' => $this->team->id,
            ])
            ->call('create')
            ->assertNotified()
            ->assertRedirect();

        $this->assertDatabaseHas('competitions', [
            'organizer_team_id' => $this->team->id,
        ]);
    }

    public function test_can_edit_competition(): void
    {
        $competition = Competition::factory()->create([
            'organizer_team_id' => $this->team->id,
        ]);

        Livewire::test(EditCompetition::class, ['record' => $competition->getRouteKey()])
            ->assertOk()
            ->fillForm([
                'name.sk' => 'Updated Competition',
            ])
            ->call('save')
            ->assertNotified();

        $competition->refresh();
        $this->assertEquals('Updated Competition', $competition->getTranslation('name', 'sk'));
    }

    public function test_can_delete_competition(): void
    {
        $competition = Competition::factory()->create([
            'organizer_team_id' => $this->team->id,
        ]);

        Livewire::test(EditCompetition::class, ['record' => $competition->getRouteKey()])
            ->callAction(DeleteAction::class)
            ->assertNotified();

        $this->assertSoftDeleted($competition);
    }

    public function test_date_start_is_required(): void
    {
        Livewire::test(CreateCompetition::class)
            ->fillForm([
                'name.sk' => 'Test Competition',
                'date_start' => null,
            ])
            ->call('create')
            ->assertHasFormErrors(['date_start' => 'required']);
    }

    public function test_registrations_relation_manager_loads(): void
    {
        $competition = Competition::factory()->create([
            'organizer_team_id' => $this->team->id,
        ]);

        Livewire::test(EditCompetition::class, ['record' => $competition->getRouteKey()])
            ->assertSeeLivewire(RegistrationsRelationManager::class);
    }

    public function test_rounds_relation_manager_loads(): void
    {
        $competition = Competition::factory()->create([
            'organizer_team_id' => $this->team->id,
        ]);

        Livewire::test(RoundsRelationManager::class, [
            'ownerRecord' => $competition,
            'pageClass' => EditCompetition::class,
        ])->assertOk();
    }

    public function test_timetable_relation_manager_loads(): void
    {
        $competition = Competition::factory()->create([
            'organizer_team_id' => $this->team->id,
        ]);

        Livewire::test(TimetableRelationManager::class, [
            'ownerRecord' => $competition,
            'pageClass' => EditCompetition::class,
        ])->assertOk();
    }

    public function test_competition_status_accessor(): void
    {
        $draft = Competition::factory()->draft()->create();
        $this->assertEquals('hidden', $draft->status);

        $upcoming = Competition::factory()->create([
            'date_start' => now()->addMonth(),
            'is_published' => true,
            'show_countdown' => true,
        ]);
        $this->assertEquals('countdown', $upcoming->status);

        $finished = Competition::factory()->create([
            'date_start' => now()->subWeek(),
            'date_end' => now()->subDay(),
            'is_published' => true,
        ]);
        $this->assertEquals('finished', $finished->status);
    }
}
