<?php

namespace Tests\Feature\Filament;

use App\Enums\RoleEnum;
use App\Filament\Pages\ProfileApprovals;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProfileApprovalsAccessTest extends TestCase
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

    public function test_athlete_cannot_open_profile_approvals(): void
    {
        $athlete = User::factory()->create();
        $athlete->assignRole(RoleEnum::CUSTOMER->value);
        $athlete->teams()->attach($this->team, ['role' => RoleEnum::ATHLETE->value]);

        $this->actingAs($athlete)
            ->get(ProfileApprovals::getUrl(panel: 'admin', tenant: $this->team))
            ->assertForbidden();
    }

    public function test_coach_cannot_open_profile_approvals(): void
    {
        $coach = User::factory()->create();
        $coach->teams()->attach($this->team, ['role' => RoleEnum::COACH->value]);

        $this->actingAs($coach)
            ->get(ProfileApprovals::getUrl(panel: 'admin', tenant: $this->team))
            ->assertForbidden();
    }

    public function test_team_admin_can_open_profile_approvals(): void
    {
        $teamAdmin = User::factory()->create();
        $teamAdmin->teams()->attach($this->team, ['role' => RoleEnum::TEAM_ADMIN->value]);

        $this->actingAs($teamAdmin)
            ->get(ProfileApprovals::getUrl(panel: 'admin', tenant: $this->team))
            ->assertSuccessful();
    }

    public function test_admin_can_open_profile_approvals(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::ADMIN->value);
        $admin->teams()->attach($this->team, ['role' => RoleEnum::TEAM_ADMIN->value]);

        $this->actingAs($admin)
            ->get(ProfileApprovals::getUrl(panel: 'admin', tenant: $this->team))
            ->assertSuccessful();
    }
}
