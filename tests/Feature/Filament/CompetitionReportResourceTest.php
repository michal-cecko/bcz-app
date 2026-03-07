<?php

namespace Tests\Feature\Filament;

use App\Enums\RoleEnum;
use App\Filament\Resources\CompetitionReports\Pages\CreateCompetitionReport;
use App\Filament\Resources\CompetitionReports\Pages\EditCompetitionReport;
use App\Filament\Resources\CompetitionReports\Pages\ListCompetitionReports;
use App\Models\Competition;
use App\Models\CompetitionReport;
use App\Models\Team;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CompetitionReportResourceTest extends TestCase
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

    public function test_can_list_competition_reports(): void
    {
        $competition = Competition::factory()->create(['organizer_team_id' => $this->team->id]);
        $reports = CompetitionReport::factory()->count(3)->create([
            'competition_id' => $competition->id,
            'user_id' => $this->admin->id,
        ]);

        Livewire::test(ListCompetitionReports::class)
            ->assertOk()
            ->assertCanSeeTableRecords($reports);
    }

    public function test_can_create_competition_report(): void
    {
        $competition = Competition::factory()->create(['organizer_team_id' => $this->team->id]);

        Livewire::test(CreateCompetitionReport::class)
            ->fillForm([
                'competition_id' => $competition->id,
                'user_id' => $this->admin->id,
                'title.sk' => 'Správa zo súťaže',
            ])
            ->call('create')
            ->assertNotified()
            ->assertRedirect();

        $this->assertDatabaseHas('competition_reports', [
            'competition_id' => $competition->id,
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_can_edit_competition_report(): void
    {
        $competition = Competition::factory()->create(['organizer_team_id' => $this->team->id]);
        $report = CompetitionReport::factory()->create([
            'competition_id' => $competition->id,
            'user_id' => $this->admin->id,
        ]);

        Livewire::test(EditCompetitionReport::class, ['record' => $report->getRouteKey()])
            ->assertOk()
            ->fillForm([
                'title.sk' => 'Updated Report',
            ])
            ->call('save')
            ->assertNotified();

        $report->refresh();
        $this->assertEquals('Updated Report', $report->getTranslation('title', 'sk'));
    }

    public function test_can_delete_competition_report(): void
    {
        $competition = Competition::factory()->create(['organizer_team_id' => $this->team->id]);
        $report = CompetitionReport::factory()->create([
            'competition_id' => $competition->id,
            'user_id' => $this->admin->id,
        ]);

        Livewire::test(EditCompetitionReport::class, ['record' => $report->getRouteKey()])
            ->callAction(DeleteAction::class)
            ->assertNotified();

        $this->assertDatabaseMissing('competition_reports', ['id' => $report->id]);
    }

    public function test_competition_is_required(): void
    {
        Livewire::test(CreateCompetitionReport::class)
            ->fillForm([
                'competition_id' => null,
                'user_id' => $this->admin->id,
                'title.sk' => 'Test',
            ])
            ->call('create')
            ->assertHasFormErrors(['competition_id' => 'required']);
    }
}
