<?php

namespace Tests\Feature\Models;

use App\Enums\MembershipStatusEnum;
use App\Enums\RoleEnum;
use App\Models\Membership;
use App\Models\Team;
use App\Models\TeamSeason;
use App\Models\User;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TeamSeasonTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_active_returns_true_for_current_season(): void
    {
        $season = TeamSeason::factory()->create([
            'starts_at' => now()->subMonth(),
            'ends_at' => now()->addMonth(),
        ]);

        $this->assertTrue($season->isActive());
    }

    public function test_is_active_returns_false_for_past_season(): void
    {
        $season = TeamSeason::factory()->past()->create();

        $this->assertFalse($season->isActive());
    }

    public function test_is_future_returns_true_for_future_season(): void
    {
        $season = TeamSeason::factory()->future()->create();

        $this->assertTrue($season->isFuture());
    }

    public function test_is_past_returns_true_for_past_season(): void
    {
        $season = TeamSeason::factory()->past()->create();

        $this->assertTrue($season->isPast());
    }

    public function test_total_months_calculates_correctly(): void
    {
        $season = TeamSeason::factory()->create([
            'starts_at' => now()->startOfYear()->month(3)->startOfMonth(),
            'ends_at' => now()->startOfYear()->month(11)->endOfMonth(),
        ]);

        // March to November inclusive: nine calendar months.
        $this->assertEquals(9, $season->totalMonths());
    }

    public function test_remaining_months_from_start(): void
    {
        $season = TeamSeason::factory()->create([
            'starts_at' => now()->subMonths(2)->startOfMonth(),
            'ends_at' => now()->addMonths(6)->endOfMonth(),
        ]);

        $remaining = $season->remainingMonths(now());
        $this->assertGreaterThan(0, $remaining);
        $this->assertLessThanOrEqual($season->totalMonths(), $remaining);
    }

    public function test_remaining_months_before_start_returns_total(): void
    {
        $season = TeamSeason::factory()->create([
            'starts_at' => now()->addMonth()->startOfMonth(),
            'ends_at' => now()->addMonths(9)->endOfMonth(),
        ]);

        $this->assertEquals($season->totalMonths(), $season->remainingMonths(now()));
    }

    public function test_remaining_months_after_end_returns_zero(): void
    {
        $season = TeamSeason::factory()->past()->create();

        $this->assertEquals(0, $season->remainingMonths(now()));
    }

    public function test_prorated_fee_at_start_returns_full_fee(): void
    {
        $season = TeamSeason::factory()->create([
            'starts_at' => now()->addDay(),
            'ends_at' => now()->addMonths(8),
            'fee_amount' => 80.00,
        ]);

        $this->assertEquals(80.00, $season->proratedFee(now()));
    }

    public function test_prorated_fee_mid_season(): void
    {
        $season = TeamSeason::factory()->prorated()->create([
            'starts_at' => now()->subMonths(4)->startOfMonth(),
            'ends_at' => now()->addMonths(4)->endOfMonth(),
            'fee_amount' => 80.00,
        ]);

        $prorated = $season->proratedFee(now());
        $this->assertLessThan(80.00, $prorated);
        $this->assertGreaterThan(0, $prorated);
    }

    public function test_length_in_whole_months_rounds_a_full_calendar_year_to_twelve(): void
    {
        $season = TeamSeason::factory()->create([
            'starts_at' => now()->startOfYear(),
            'ends_at' => now()->endOfYear()->startOfDay(),
        ]);

        $this->assertSame(12, $season->totalMonths());
        $this->assertSame(12, $season->lengthInWholeMonths());
    }

    public function test_monthly_fee_spreads_the_fee_across_the_season_months(): void
    {
        $season = TeamSeason::factory()->create([
            'starts_at' => now()->startOfMonth(),
            'ends_at' => now()->startOfMonth()->addMonths(9)->endOfMonth(),
            'fee_amount' => 100.00,
        ]);

        $this->assertSame(10, $season->lengthInWholeMonths());
        $this->assertSame(10.0, $season->monthlyFee());
    }

    public function test_monthly_fee_is_null_for_a_season_shorter_than_a_month(): void
    {
        $season = TeamSeason::factory()->create([
            'starts_at' => now()->startOfMonth(),
            'ends_at' => now()->startOfMonth()->addDays(10),
            'fee_amount' => 90.00,
        ]);

        $this->assertSame(0, $season->lengthInWholeMonths());
        $this->assertNull($season->monthlyFee());
    }

    /**
     * A member joining on 2 October owes October, November and December. The
     * old month count charged 53.33 here (80 / 3 truncated months * 2).
     */
    public function test_season_fee_is_prorated_by_default_mid_season(): void
    {
        $this->travelTo('2026-10-02 16:16:00');

        $season = TeamSeason::create([
            'team_id' => Team::factory()->create()->id,
            'name' => 'Jesenná sezóna 2026',
            'starts_at' => '2026-09-01',
            'ends_at' => '2026-12-31',
            'fee_amount' => 80.00,
            'payment_deadline_days' => 14,
        ]);

        $this->assertSame(60.0, $season->fresh()->proratedFee());
    }

    public function test_season_with_prorating_switched_off_charges_the_full_fee_mid_season(): void
    {
        $this->travelTo('2026-10-02 16:16:00');

        $season = TeamSeason::factory()->fullFee()->create([
            'starts_at' => '2026-09-01',
            'ends_at' => '2026-12-31',
            'fee_amount' => 80.00,
        ]);

        $this->assertSame(80.0, $season->fresh()->proratedFee());
    }

    public function test_membership_payment_charges_the_remaining_months_mid_season(): void
    {
        $this->travelTo('2026-10-02 16:16:00');

        $team = Team::factory()->create();
        $season = TeamSeason::factory()->create([
            'team_id' => $team->id,
            'starts_at' => '2026-09-01',
            'ends_at' => '2026-12-31',
            'fee_amount' => 80.00,
        ]);

        $member = User::factory()->create();
        $member->assignRole(Role::firstOrCreate(['name' => RoleEnum::CUSTOMER->value, 'guard_name' => 'web']));

        $payment = app(PaymentService::class)->ensurePendingMembershipPayment($member, $team, $season->fresh());

        $this->assertEquals(60.00, (float) $payment->amount);
        $this->assertEquals(60.00, (float) $payment->payable->fee_amount);
    }

    public function test_membership_payment_charges_the_fee_the_existing_membership_was_issued_with(): void
    {
        $this->travelTo('2026-11-15 10:00:00');

        $team = Team::factory()->create();
        $user = User::factory()->create();
        $season = TeamSeason::factory()->prorated()->create([
            'team_id' => $team->id,
            'starts_at' => '2026-09-01',
            'ends_at' => '2026-12-31',
            'fee_amount' => 80.00,
        ]);

        // Issued in full when the season was created, before the member signed up.
        Membership::factory()->create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'team_season_id' => $season->id,
            'status' => MembershipStatusEnum::PENDING,
            'fee_amount' => 80.00,
            'fee_currency' => 'EUR',
        ]);

        $payment = app(PaymentService::class)->ensurePendingMembershipPayment($user, $team, $season->fresh());

        $this->assertEquals(80.00, (float) $payment->amount);
    }

    public function test_new_season_prorates_by_default(): void
    {
        $season = TeamSeason::create([
            'team_id' => Team::factory()->create()->id,
            'name' => 'Nová sezóna',
            'starts_at' => '2026-09-01',
            'ends_at' => '2026-12-31',
            'fee_amount' => 80.00,
            'payment_deadline_days' => 14,
        ]);

        $this->assertTrue($season->fresh()->prorate_fee);
    }

    public function test_total_months_counts_calendar_months_inclusive(): void
    {
        $season = TeamSeason::factory()->make([
            'starts_at' => '2026-09-01',
            'ends_at' => '2026-12-31',
        ]);

        $this->assertSame(4, $season->totalMonths());
    }

    /**
     * @return array<string, array{string, float}>
     */
    public static function proratedJoinDates(): array
    {
        return [
            'before the season' => ['2026-08-15', 80.00],
            'first day' => ['2026-09-01', 80.00],
            'late september' => ['2026-09-24', 80.00],
            'early october' => ['2026-10-02', 60.00],
            'last day of october' => ['2026-10-31', 60.00],
            'first day of november' => ['2026-11-01', 40.00],
            'last day of the season' => ['2026-12-31', 20.00],
            'after the season' => ['2027-01-01', 0.00],
        ];
    }

    #[DataProvider('proratedJoinDates')]
    public function test_prorated_fee_counts_the_joining_month_as_a_whole_month(string $joinDate, float $expected): void
    {
        $this->travelTo($joinDate.' 16:16:00');

        $season = TeamSeason::factory()->prorated()->create([
            'starts_at' => '2026-09-01',
            'ends_at' => '2026-12-31',
            'fee_amount' => 80.00,
        ])->fresh();

        $this->assertSame($expected, $season->proratedFee());
    }

    public function test_prorated_fee_for_a_full_year_season(): void
    {
        $season = TeamSeason::factory()->prorated()->create([
            'starts_at' => '2026-01-01',
            'ends_at' => '2026-12-31',
            'fee_amount' => 120.00,
        ])->fresh();

        $this->assertSame(12, $season->remainingMonths(Carbon::parse('2026-01-20')));
        $this->assertSame(6, $season->remainingMonths(Carbon::parse('2026-07-15')));
        $this->assertSame(60.0, $season->proratedFee(Carbon::parse('2026-07-15')));
    }

    public function test_has_capacity_unlimited(): void
    {
        $season = TeamSeason::factory()->create(['max_capacity' => null]);

        $this->assertTrue($season->hasCapacity());
    }

    public function test_has_capacity_with_limit(): void
    {
        $season = TeamSeason::factory()->create(['max_capacity' => 2]);

        $this->assertTrue($season->hasCapacity());

        Membership::factory()->count(2)->forSeason($season)->create();

        $this->assertFalse($season->fresh()->hasCapacity());
    }
}
