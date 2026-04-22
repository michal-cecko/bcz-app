<?php

namespace Tests\Feature\User;

use App\Enums\MembershipStatusEnum;
use App\Enums\RoleEnum;
use App\Filament\Resources\Users\UserResource;
use App\Models\Team;
use App\Models\User;
use App\Notifications\MembershipPaymentDue;
use App\Services\SeasonService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MembershipBillingGateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }
    }

    public function test_admin_user_attached_to_team_is_skipped_from_season_membership_creation(): void
    {
        Notification::fake();

        $team = Team::factory()->create(['membership_enabled' => true]);
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::ADMIN->value);
        $team->members()->attach($admin, [
            'role' => RoleEnum::TEAM_ADMIN->value,
            'is_active' => true,
            'joined_at' => now(),
        ]);

        $athlete = User::factory()->create();
        $team->members()->attach($athlete, [
            'role' => RoleEnum::ATHLETE->value,
            'is_active' => true,
            'joined_at' => now(),
        ]);

        $season = app(SeasonService::class)->createSeasonWithMemberships($team, [
            'name' => 'Skip-admin Sezóna',
            'starts_at' => now()->startOfMonth(),
            'ends_at' => now()->addMonths(6)->endOfMonth(),
            'fee_amount' => 120.00,
            'fee_currency' => 'EUR',
            'payment_deadline_days' => 14,
        ]);

        // Only the athlete gets a membership; the admin is skipped entirely.
        $this->assertCount(1, $season->memberships);
        $this->assertEquals($athlete->id, $season->memberships->first()->user_id);

        Notification::assertSentTo($athlete, MembershipPaymentDue::class);
        Notification::assertNotSentTo($admin, MembershipPaymentDue::class);
    }

    public function test_has_free_membership_creates_active_free_record_without_notification(): void
    {
        Notification::fake();

        $team = Team::factory()->create(['membership_enabled' => true]);
        $payer = User::factory()->create();
        $exempt = User::factory()->create(['has_free_membership' => true]);

        foreach ([$payer, $exempt] as $user) {
            $team->members()->attach($user, [
                'role' => RoleEnum::ATHLETE->value,
                'is_active' => true,
                'joined_at' => now(),
            ]);
        }

        $season = app(SeasonService::class)->createSeasonWithMemberships($team, [
            'name' => 'Free-flag Sezóna',
            'starts_at' => now()->startOfMonth(),
            'ends_at' => now()->addMonths(6)->endOfMonth(),
            'fee_amount' => 90.00,
            'fee_currency' => 'EUR',
            'payment_deadline_days' => 14,
        ]);

        $payerMembership = $season->memberships->firstWhere('user_id', $payer->id);
        $exemptMembership = $season->memberships->firstWhere('user_id', $exempt->id);

        $this->assertNotNull($payerMembership);
        $this->assertNotNull($exemptMembership);

        $this->assertEquals(MembershipStatusEnum::PENDING, $payerMembership->status);
        $this->assertFalse((bool) $payerMembership->is_free);
        $this->assertEquals(90.00, (float) $payerMembership->fee_amount);
        $this->assertNotNull($payerMembership->payment_deadline_at);

        $this->assertEquals(MembershipStatusEnum::ACTIVE, $exemptMembership->status);
        $this->assertTrue((bool) $exemptMembership->is_free);
        $this->assertEquals(0.0, (float) $exemptMembership->fee_amount);
        $this->assertNull($exemptMembership->payment_deadline_at);

        Notification::assertSentTo($payer, MembershipPaymentDue::class);
        Notification::assertNotSentTo($exempt, MembershipPaymentDue::class);
    }

    public function test_sync_team_scoped_roles_preserves_pivot_rows_on_other_teams(): void
    {
        $user = User::factory()->create();
        $teamA = Team::factory()->create();
        $teamB = Team::factory()->create();

        // User is a COACH on team A and an ATHLETE on team B.
        DB::table('team_user')->insert([
            [
                'team_id' => $teamA->id,
                'user_id' => $user->id,
                'role' => RoleEnum::COACH->value,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'team_id' => $teamB->id,
                'user_id' => $user->id,
                'role' => RoleEnum::ATHLETE->value,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $coachRoleId = Role::where('name', RoleEnum::COACH->value)->value('id');

        // Re-sync only team A's roles (same as before — COACH on team A).
        UserResource::syncTeamScopedRoles($user, [$coachRoleId], $teamA->id);

        // Team A: still COACH.
        $this->assertTrue(
            DB::table('team_user')
                ->where('user_id', $user->id)
                ->where('team_id', $teamA->id)
                ->where('role', RoleEnum::COACH->value)
                ->exists()
        );

        // Team B: untouched — ATHLETE pivot row must survive.
        $this->assertTrue(
            DB::table('team_user')
                ->where('user_id', $user->id)
                ->where('team_id', $teamB->id)
                ->where('role', RoleEnum::ATHLETE->value)
                ->exists(),
            'Pivot row on another team was destroyed by syncTeamScopedRoles.'
        );
    }

    public function test_sync_team_scoped_roles_replaces_only_the_given_team_row_set(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();

        // User previously had COACH on this team.
        DB::table('team_user')->insert([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'role' => RoleEnum::COACH->value,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $athleteRoleId = Role::where('name', RoleEnum::ATHLETE->value)->value('id');

        // Swap the role to ATHLETE on the same team.
        UserResource::syncTeamScopedRoles($user, [$athleteRoleId], $team->id);

        $rows = DB::table('team_user')
            ->where('user_id', $user->id)
            ->where('team_id', $team->id)
            ->pluck('role');

        $this->assertSame([RoleEnum::ATHLETE->value], $rows->all());
    }
}
