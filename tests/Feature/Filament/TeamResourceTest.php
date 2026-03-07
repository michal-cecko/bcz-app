<?php

namespace Tests\Feature\Filament;

use App\Enums\RoleEnum;
use App\Filament\Resources\Teams\Pages\CreateTeam;
use App\Filament\Resources\Teams\Pages\EditTeam;
use App\Filament\Resources\Teams\Pages\ListTeams;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TeamResourceTest extends TestCase
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

    public function test_can_list_teams(): void
    {
        Livewire::test(ListTeams::class)
            ->assertOk()
            ->assertCanSeeTableRecords(Team::all());
    }

    public function test_can_create_team(): void
    {
        Livewire::test(CreateTeam::class)
            ->fillForm([
                'name.sk' => 'Test Tím',
                'is_active' => true,
            ])
            ->call('create')
            ->assertNotified()
            ->assertRedirect();

        $this->assertDatabaseHas('teams', [
            'slug' => 'test-tim',
        ]);
    }

    public function test_can_edit_team(): void
    {
        $team = Team::factory()->create();

        Livewire::test(EditTeam::class, ['record' => $team->getRouteKey()])
            ->assertOk()
            ->fillForm([
                'name.sk' => 'Upravený Tím',
            ])
            ->call('save')
            ->assertNotified();

        $team->refresh();
        $this->assertEquals('Upravený Tím', $team->getTranslation('name', 'sk'));
    }

    public function test_name_sk_is_required(): void
    {
        Livewire::test(CreateTeam::class)
            ->fillForm([
                'name.sk' => null,
            ])
            ->call('create')
            ->assertHasFormErrors(['name.sk' => 'required']);
    }
}
