<?php

namespace Tests\Feature\Filament;

use App\Enums\RoleEnum;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\UserResource;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserResourceTest extends TestCase
{
    use RefreshDatabase;

    protected Team $team;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }

        $this->team = Team::factory()->create();
    }

    private function bootAdminPanelAs(User $user): void
    {
        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::setTenant($this->team);
        Filament::bootCurrentPanel();
    }

    public function test_non_admin_cannot_edit_privileged_fields_on_self(): void
    {
        $user = User::factory()->create(['has_free_membership' => false]);
        $user->assignRole(RoleEnum::ATHLETE);
        $user->teams()->attach($this->team, ['role' => RoleEnum::ATHLETE->value]);

        $this->bootAdminPanelAs($user);

        $this->assertFalse(UserForm::canEditPrivilegedFields($user));
    }

    public function test_admin_can_edit_privileged_fields_on_anyone(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::ADMIN);
        $admin->teams()->attach($this->team);

        $target = User::factory()->create();

        $this->bootAdminPanelAs($admin);

        $this->assertTrue(UserForm::canEditPrivilegedFields($target));
        $this->assertTrue(UserForm::canEditPrivilegedFields($admin));
    }

    public function test_team_admin_cannot_edit_privileged_fields_on_self(): void
    {
        $teamAdmin = User::factory()->create();
        $teamAdmin->teams()->attach($this->team, ['role' => RoleEnum::TEAM_ADMIN->value]);

        $this->bootAdminPanelAs($teamAdmin);

        $this->assertFalse(UserForm::canEditPrivilegedFields($teamAdmin));
    }

    public function test_sync_team_scoped_roles_writes_pivot_for_each_team(): void
    {
        $user = User::factory()->create();
        $teamA = Team::factory()->create();
        $teamB = Team::factory()->create();

        $coachRoleId = Role::query()->where('name', RoleEnum::COACH->value)->value('id');

        UserResource::syncTeamScopedRoles($user, [$coachRoleId], [$teamA->id, $teamB->id]);

        $this->assertDatabaseHas('team_user', [
            'user_id' => $user->id,
            'team_id' => $teamA->id,
            'role' => RoleEnum::COACH->value,
        ]);
        $this->assertDatabaseHas('team_user', [
            'user_id' => $user->id,
            'team_id' => $teamB->id,
            'role' => RoleEnum::COACH->value,
        ]);
    }

    public function test_sync_team_scoped_roles_leaves_other_teams_untouched(): void
    {
        $user = User::factory()->create();
        $teamA = Team::factory()->create();
        $teamB = Team::factory()->create();

        // Pre-existing pivot on team B that should NOT be touched.
        DB::table('team_user')->insert([
            'team_id' => $teamB->id,
            'user_id' => $user->id,
            'role' => RoleEnum::ATHLETE->value,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $coachRoleId = Role::query()->where('name', RoleEnum::COACH->value)->value('id');

        UserResource::syncTeamScopedRoles($user, [$coachRoleId], [$teamA->id]);

        $this->assertDatabaseHas('team_user', [
            'user_id' => $user->id,
            'team_id' => $teamA->id,
            'role' => RoleEnum::COACH->value,
        ]);
        $this->assertDatabaseHas('team_user', [
            'user_id' => $user->id,
            'team_id' => $teamB->id,
            'role' => RoleEnum::ATHLETE->value,
        ]);
    }

    public function test_sync_team_scoped_roles_is_no_op_when_team_ids_empty(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::ATHLETE);
        $user->teams()->attach($this->team, ['role' => RoleEnum::ATHLETE->value]);

        $coachRoleId = Role::query()->where('name', RoleEnum::COACH->value)->value('id');

        // Empty team list means "skip pivot sync entirely" — the existing ATHLETE pivot
        // must remain intact. Mirrors what afterSave() does for non-admin self-edits where
        // team_ids never reaches the data array.
        UserResource::syncTeamScopedRoles($user, [$coachRoleId], []);

        $this->assertDatabaseHas('team_user', [
            'user_id' => $user->id,
            'team_id' => $this->team->id,
            'role' => RoleEnum::ATHLETE->value,
        ]);
        $this->assertDatabaseMissing('team_user', [
            'user_id' => $user->id,
            'role' => RoleEnum::COACH->value,
        ]);
    }
}
