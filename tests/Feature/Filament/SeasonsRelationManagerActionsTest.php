<?php

namespace Tests\Feature\Filament;

use App\Enums\RoleEnum;
use App\Filament\Resources\Teams\Pages\EditTeam;
use App\Filament\Resources\Teams\Pages\ViewTeam;
use App\Filament\Resources\Teams\RelationManagers\SeasonsRelationManager;
use App\Models\Team;
use App\Models\TeamSeason;
use App\Models\User;
use Database\Seeders\ShieldPermissionSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Regression test for https://github.com/michal-cecko/bcz-app/issues/32:
 * "Seasons" relation manager actions (create/delete) were visible on `EditTeam`
 * but missing on `ViewTeam` for the exact same team, because Filament's default
 * `isReadOnly()` is true on any `ViewRecord` page.
 */
class SeasonsRelationManagerActionsTest extends TestCase
{
    use RefreshDatabase;

    protected Team $team;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }

        $this->seed(ShieldPermissionSeeder::class);

        $this->team = Team::factory()->create();
    }

    protected function actingAsTenantUser(User $user): void
    {
        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::setTenant($this->team);
        Filament::bootCurrentPanel();
    }

    public function test_super_admin_can_see_create_and_delete_season_actions_from_the_view_page(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::SUPER_ADMIN);

        $season = TeamSeason::factory()->create(['team_id' => $this->team->id]);

        $this->actingAsTenantUser($admin->fresh());

        Livewire::test(SeasonsRelationManager::class, [
            'ownerRecord' => $this->team,
            'pageClass' => ViewTeam::class,
        ])
            ->assertTableActionVisible('create')
            ->assertTableActionVisible('delete', $season);
    }

    public function test_team_admin_can_see_create_and_delete_season_actions_from_the_view_page(): void
    {
        $teamAdmin = User::factory()->create();
        $teamAdmin->teams()->attach($this->team->id, ['role' => RoleEnum::TEAM_ADMIN->value, 'is_active' => true, 'joined_at' => now()]);

        $season = TeamSeason::factory()->create(['team_id' => $this->team->id]);

        $this->actingAsTenantUser($teamAdmin->fresh());

        Livewire::test(SeasonsRelationManager::class, [
            'ownerRecord' => $this->team,
            'pageClass' => ViewTeam::class,
        ])
            ->assertTableActionVisible('create')
            ->assertTableActionVisible('delete', $season);
    }

    public function test_super_admin_can_still_see_create_and_delete_season_actions_from_the_edit_page(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::SUPER_ADMIN);

        $season = TeamSeason::factory()->create(['team_id' => $this->team->id]);

        $this->actingAsTenantUser($admin->fresh());

        Livewire::test(SeasonsRelationManager::class, [
            'ownerRecord' => $this->team,
            'pageClass' => EditTeam::class,
        ])
            ->assertTableActionVisible('create')
            ->assertTableActionVisible('delete', $season);
    }

    /**
     * A user who cannot manage this team at all (no admin role, not a team admin
     * of this team) must not gain create/delete access just by viewing it — this
     * is what stops the fix from opening these actions up to everyone.
     */
    public function test_unrelated_member_cannot_see_create_or_delete_season_actions_from_the_view_page(): void
    {
        $member = User::factory()->create();
        $member->teams()->attach($this->team->id, ['role' => RoleEnum::ATHLETE->value, 'is_active' => true, 'joined_at' => now()]);

        $season = TeamSeason::factory()->create(['team_id' => $this->team->id]);

        $this->actingAsTenantUser($member->fresh());

        Livewire::test(SeasonsRelationManager::class, [
            'ownerRecord' => $this->team,
            'pageClass' => ViewTeam::class,
        ])
            ->assertTableActionHidden('create')
            ->assertTableActionHidden('delete', $season);
    }
}
