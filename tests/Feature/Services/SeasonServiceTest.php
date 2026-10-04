<?php

namespace Tests\Feature\Services;

use App\Enums\MembershipStatusEnum;
use App\Enums\RoleEnum;
use App\Models\Membership;
use App\Models\Team;
use App\Models\TeamSeason;
use App\Models\User;
use App\Notifications\MembershipPaymentDue;
use App\Services\SeasonService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SeasonServiceTest extends TestCase
{
    use RefreshDatabase;

    private SeasonService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(SeasonService::class);
    }

    public function test_create_season_with_memberships(): void
    {
        Notification::fake();

        $team = Team::factory()->create(['membership_enabled' => true]);
        $members = User::factory()->count(3)->create();

        foreach ($members as $member) {
            $team->members()->attach($member, [
                'role' => RoleEnum::ATHLETE->value,
                'is_active' => true,
                'joined_at' => now(),
            ]);
        }

        $season = $this->service->createSeasonWithMemberships($team, [
            'name' => 'Test Sezóna',
            'starts_at' => now()->startOfMonth(),
            'ends_at' => now()->addMonths(8)->endOfMonth(),
            'fee_amount' => 100.00,
            'fee_currency' => 'EUR',
            'payment_deadline_days' => 14,
        ]);

        $this->assertDatabaseHas('team_seasons', ['name' => 'Test Sezóna']);
        $this->assertCount(3, $season->memberships);

        foreach ($season->memberships as $membership) {
            $this->assertEquals(MembershipStatusEnum::PENDING, $membership->status);
            $this->assertEquals(100.00, (float) $membership->fee_amount);
            $this->assertNotNull($membership->payment_deadline_at);
        }

        Notification::assertSentTo($members, MembershipPaymentDue::class);
    }

    public function test_create_season_skips_inactive_members(): void
    {
        Notification::fake();

        $team = Team::factory()->create(['membership_enabled' => true]);
        $activeUser = User::factory()->create();
        $inactiveUser = User::factory()->create();

        $team->members()->attach($activeUser, ['role' => RoleEnum::ATHLETE->value, 'is_active' => true, 'joined_at' => now()]);
        $team->members()->attach($inactiveUser, ['role' => RoleEnum::ATHLETE->value, 'is_active' => false, 'joined_at' => now()]);

        $season = $this->service->createSeasonWithMemberships($team, [
            'name' => 'Test',
            'starts_at' => now(),
            'ends_at' => now()->addMonths(6),
            'fee_amount' => 50.00,
            'fee_currency' => 'EUR',
            'payment_deadline_days' => 14,
        ]);

        $this->assertCount(1, $season->memberships);
    }

    public function test_create_season_bills_a_member_with_two_team_roles_once(): void
    {
        Notification::fake();

        $team = Team::factory()->create(['membership_enabled' => true]);
        $coachWhoTrains = User::factory()->create();

        $team->members()->attach($coachWhoTrains, ['role' => RoleEnum::ATHLETE->value, 'is_active' => true, 'joined_at' => now()]);
        $team->members()->attach($coachWhoTrains, ['role' => RoleEnum::COACH->value, 'is_active' => true, 'joined_at' => now()]);

        $season = $this->service->createSeasonWithMemberships($team, [
            'name' => 'Test',
            'starts_at' => now(),
            'ends_at' => now()->addMonths(6),
            'fee_amount' => 50.00,
            'fee_currency' => 'EUR',
            'payment_deadline_days' => 14,
        ]);

        $this->assertCount(1, $season->memberships);
        Notification::assertSentToTimes($coachWhoTrains, MembershipPaymentDue::class, 1);
    }

    public function test_find_or_create_membership_follows_the_season_billing_rules(): void
    {
        Role::firstOrCreate(['name' => RoleEnum::CUSTOMER->value, 'guard_name' => 'web']);

        $team = Team::factory()->create();
        $season = TeamSeason::factory()->create(['team_id' => $team->id, 'fee_amount' => 80.00]);

        $payer = User::factory()->create();
        $payer->assignRole(RoleEnum::CUSTOMER->value);
        $freeMember = User::factory()->create(['has_free_membership' => true]);
        $freeMember->assignRole(RoleEnum::CUSTOMER->value);
        $coachOnly = User::factory()->create();
        $team->members()->attach($coachOnly, ['role' => RoleEnum::COACH->value, 'is_active' => true]);

        $paid = $this->service->findOrCreateMembership($season, $payer);
        $this->assertSame(MembershipStatusEnum::PENDING, $paid->status);
        $this->assertEquals(80.00, (float) $paid->fee_amount);
        $this->assertTrue($paid->is($this->service->findOrCreateMembership($season, $payer)));

        $free = $this->service->findOrCreateMembership($season, $freeMember);
        $this->assertSame(MembershipStatusEnum::ACTIVE, $free->status);
        $this->assertTrue($free->is_free);
        $this->assertEquals(0.0, (float) $free->fee_amount);

        $this->assertNull($this->service->findOrCreateMembership($season, $coachOnly));
        $this->assertSame(0, Membership::where('user_id', $coachOnly->id)->count());
    }

    public function test_season_created_mid_season_bills_existing_members_for_the_months_left(): void
    {
        Notification::fake();
        $this->travelTo('2026-10-03 10:00:00');

        $team = Team::factory()->create(['membership_enabled' => true]);
        $member = User::factory()->create();
        $team->members()->attach($member, ['role' => RoleEnum::ATHLETE->value, 'is_active' => true, 'joined_at' => now()]);

        $season = $this->service->createSeasonWithMemberships($team, [
            'name' => 'Jesenná sezóna 2026',
            'starts_at' => '2026-09-01',
            'ends_at' => '2026-12-31',
            'fee_amount' => 80.00,
            'fee_currency' => 'EUR',
            'payment_deadline_days' => 14,
        ]);

        $this->assertEquals(60.00, (float) $season->memberships()->sole()->fee_amount);
    }

    public function test_add_mid_season_member(): void
    {
        $season = TeamSeason::factory()->prorated()->create([
            'starts_at' => now()->subMonths(4)->startOfMonth(),
            'ends_at' => now()->addMonths(4)->endOfMonth(),
            'fee_amount' => 80.00,
        ]);

        $user = User::factory()->create();

        $membership = $this->service->addMidSeasonMember($season, $user);

        $this->assertEquals(MembershipStatusEnum::PENDING, $membership->status);
        $this->assertLessThan(80.00, (float) $membership->fee_amount);
        $this->assertEquals($season->id, $membership->team_season_id);
        $this->assertNotNull($membership->payment_deadline_at);
    }

    public function test_add_mid_season_member_pays_full_fee_when_season_is_not_prorated(): void
    {
        $season = TeamSeason::factory()->fullFee()->create([
            'starts_at' => now()->subMonths(4)->startOfMonth(),
            'ends_at' => now()->addMonths(4)->endOfMonth(),
            'fee_amount' => 80.00,
        ]);

        $membership = $this->service->addMidSeasonMember($season, User::factory()->create());

        $this->assertEquals(80.00, (float) $membership->fee_amount);
    }

    public function test_mark_membership_free(): void
    {
        $membership = Membership::factory()->pending()->create([
            'fee_amount' => 50.00,
        ]);

        $this->service->markMembershipFree($membership);

        $membership->refresh();

        $this->assertTrue($membership->is_free);
        $this->assertEquals(0, (float) $membership->fee_amount);
        $this->assertEquals(MembershipStatusEnum::ACTIVE, $membership->status);
        $this->assertNull($membership->payment_deadline_at);
    }

    public function test_renew_membership(): void
    {
        $season = TeamSeason::factory()->create([
            'starts_at' => now()->subMonth(),
            'ends_at' => now()->addMonths(5),
            'fee_amount' => 60.00,
        ]);

        $cancelled = Membership::factory()->cancelled()->forSeason($season)->create();

        $renewed = $this->service->renewMembership($cancelled);

        $this->assertEquals(MembershipStatusEnum::PENDING, $renewed->status);
        $this->assertEquals($season->id, $renewed->team_season_id);
        $this->assertNotNull($renewed->payment_deadline_at);
        $this->assertNotEquals($cancelled->id, $renewed->id);
    }
}
