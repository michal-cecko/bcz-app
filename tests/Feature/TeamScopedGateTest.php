<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Team;
use App\Models\User;
use Database\Seeders\ShieldPermissionSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TeamScopedGateTest extends TestCase
{
    use RefreshDatabase;

    protected Team $team;

    protected User $teamAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }

        $this->seed(ShieldPermissionSeeder::class);

        // TEAM_ADMIN does not hold Event abilities by default; grant them so the
        // team-scoped gate bridge is what resolves authorization here.
        Role::findByName(RoleEnum::TEAM_ADMIN->value)
            ->givePermissionTo(['View:Event', 'Update:Event']);

        $this->team = Team::factory()->create();

        $this->teamAdmin = User::factory()->create();
        $this->teamAdmin->teams()->attach($this->team->id, [
            'role' => RoleEnum::TEAM_ADMIN->value,
            'is_active' => true,
            'joined_at' => now(),
        ]);

        Filament::setTenant($this->team, isQuiet: true);
    }

    public function test_team_scoped_role_grants_permission_via_gate(): void
    {
        $this->assertTrue($this->teamAdmin->can('View:Event'));
        $this->assertTrue($this->teamAdmin->can('Update:Event'));
    }

    public function test_team_scoped_gate_does_not_grant_ungranted_ability(): void
    {
        // An ability no team-scoped role holds — the gate must defer (return
        // null), leaving the user unauthorized rather than falsely granting.
        $this->assertFalse($this->teamAdmin->can('Manage:NonExistentResource'));
    }

    public function test_user_without_team_role_is_not_granted(): void
    {
        $outsider = User::factory()->create();

        Filament::setTenant($this->team, isQuiet: true);

        $this->assertFalse($outsider->can('View:Event'));
    }

    public function test_repeated_authorization_checks_do_not_issue_per_check_queries(): void
    {
        // Warm any unrelated one-time lookups (Spatie permission cache, etc.)
        // so the measured window only covers the team-scoped gate resolution.
        $this->teamAdmin->can('View:Event');

        DB::flushQueryLog();
        DB::enableQueryLog();

        for ($i = 0; $i < 25; $i++) {
            $this->teamAdmin->can('View:Event');
            $this->teamAdmin->can('Update:Event');
        }

        $queries = DB::getQueryLog();

        $teamRoleQueries = collect($queries)->filter(
            fn (array $q) => str_contains($q['query'], 'team_user') && str_contains($q['query'], 'role')
        );

        $permissionQueries = collect($queries)->filter(
            fn (array $q) => str_contains($q['query'], 'role_has_permissions')
        );

        // Both lookups are memoized on the User instance, so 50 authorization
        // checks must not re-issue either query (the old gate ran both per check).
        $this->assertCount(0, $teamRoleQueries, 'team_user role lookup was not memoized across authorization checks');
        $this->assertCount(0, $permissionQueries, 'permission lookup was not memoized across authorization checks');
    }
}
