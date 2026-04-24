<?php

namespace Tests\Feature;

use App\Enums\MembershipStatusEnum;
use App\Enums\RoleEnum;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\Team;
use App\Models\TeamPayout;
use App\Models\TeamSeason;
use App\Models\User;
use App\Policies\MembershipPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\TeamPayoutPolicy;
use App\Policies\TeamPolicy;
use App\Policies\UserPolicy;
use Database\Seeders\ShieldPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TeamAdminPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected Team $teamA;

    protected Team $teamB;

    protected User $teamAdminA;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }

        $this->seed(ShieldPermissionSeeder::class);

        $this->teamA = Team::factory()->create();
        $this->teamB = Team::factory()->create();

        $this->teamAdminA = User::factory()->create();
        $this->teamAdminA->assignRole(RoleEnum::TEAM_ADMIN->value);
        $this->teamAdminA->teams()->attach($this->teamA->id, [
            'role' => RoleEnum::TEAM_ADMIN->value,
            'is_active' => true,
            'joined_at' => now(),
        ]);
    }

    public function test_team_admin_can_update_own_team(): void
    {
        $this->assertTrue((new TeamPolicy)->update($this->teamAdminA, $this->teamA));
    }

    public function test_team_admin_cannot_update_other_team(): void
    {
        $this->assertFalse((new TeamPolicy)->update($this->teamAdminA, $this->teamB));
    }

    public function test_team_admin_cannot_delete_other_team(): void
    {
        $this->assertFalse((new TeamPolicy)->delete($this->teamAdminA, $this->teamB));
    }

    public function test_team_admin_can_view_own_team_payment(): void
    {
        $payment = $this->makePayment($this->teamA);

        $this->assertTrue((new PaymentPolicy)->view($this->teamAdminA, $payment));
    }

    public function test_team_admin_cannot_view_other_team_payment(): void
    {
        $payment = $this->makePayment($this->teamB);

        $this->assertFalse((new PaymentPolicy)->view($this->teamAdminA, $payment));
    }

    public function test_team_admin_cannot_update_other_team_payment(): void
    {
        $payment = $this->makePayment($this->teamB);

        $this->assertFalse((new PaymentPolicy)->update($this->teamAdminA, $payment));
    }

    public function test_team_admin_can_view_own_team_membership(): void
    {
        $membership = $this->makeMembership($this->teamA);

        $this->assertTrue((new MembershipPolicy)->view($this->teamAdminA, $membership));
    }

    public function test_team_admin_cannot_view_other_team_membership(): void
    {
        $membership = $this->makeMembership($this->teamB);

        $this->assertFalse((new MembershipPolicy)->view($this->teamAdminA, $membership));
    }

    public function test_team_admin_cannot_update_other_team_membership(): void
    {
        $membership = $this->makeMembership($this->teamB);

        $this->assertFalse((new MembershipPolicy)->update($this->teamAdminA, $membership));
    }

    public function test_team_admin_can_view_own_team_payout(): void
    {
        $payout = $this->makePayout($this->teamA);

        $this->assertTrue((new TeamPayoutPolicy)->view($this->teamAdminA, $payout));
    }

    public function test_team_admin_cannot_view_other_team_payout(): void
    {
        $payout = $this->makePayout($this->teamB);

        $this->assertFalse((new TeamPayoutPolicy)->view($this->teamAdminA, $payout));
    }

    public function test_team_admin_can_update_user_on_own_team_when_permission_granted(): void
    {
        // TEAM_ADMIN does not have Update:User by default. Grant it to exercise the team-scope fallback.
        Role::findByName(RoleEnum::TEAM_ADMIN->value)->givePermissionTo('Update:User');
        $this->teamAdminA->forgetCachedPermissions();

        $sharedUser = User::factory()->create();
        $sharedUser->teams()->attach($this->teamA->id, [
            'role' => RoleEnum::ATHLETE->value,
            'is_active' => true,
            'joined_at' => now(),
        ]);

        $this->assertTrue((new UserPolicy)->update($this->teamAdminA->fresh(), $sharedUser));
    }

    public function test_team_admin_cannot_update_user_on_other_team_even_with_permission(): void
    {
        Role::findByName(RoleEnum::TEAM_ADMIN->value)->givePermissionTo('Update:User');
        $this->teamAdminA->forgetCachedPermissions();

        $otherTeamUser = User::factory()->create();
        $otherTeamUser->teams()->attach($this->teamB->id, [
            'role' => RoleEnum::ATHLETE->value,
            'is_active' => true,
            'joined_at' => now(),
        ]);

        $this->assertFalse((new UserPolicy)->update($this->teamAdminA->fresh(), $otherTeamUser));
    }

    public function test_global_admin_bypasses_team_check_on_other_team(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::ADMIN);

        $payment = $this->makePayment($this->teamB);
        $membership = $this->makeMembership($this->teamB);
        $payout = $this->makePayout($this->teamB);

        $this->assertTrue((new TeamPolicy)->update($admin, $this->teamB));
        $this->assertTrue((new PaymentPolicy)->view($admin, $payment));
        $this->assertTrue((new MembershipPolicy)->view($admin, $membership));
        $this->assertTrue((new TeamPayoutPolicy)->view($admin, $payout));
    }

    protected function makePayment(Team $team): Payment
    {
        $membership = $this->makeMembership($team);

        return Payment::factory()->create([
            'team_id' => $team->id,
            'user_id' => $membership->user_id,
            'payable_type' => 'membership',
            'payable_id' => $membership->id,
        ]);
    }

    protected function makeMembership(Team $team): Membership
    {
        $user = User::factory()->create();
        $season = TeamSeason::factory()->create(['team_id' => $team->id]);

        return Membership::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'team_season_id' => $season->id,
            'status' => MembershipStatusEnum::ACTIVE,
            'fee_amount' => 0,
            'fee_currency' => 'EUR',
            'starts_at' => $season->starts_at,
            'ends_at' => $season->ends_at,
        ]);
    }

    protected function makePayout(Team $team): TeamPayout
    {
        return TeamPayout::factory()->create(['team_id' => $team->id]);
    }
}
