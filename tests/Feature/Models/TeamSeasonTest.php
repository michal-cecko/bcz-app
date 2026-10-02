<?php

namespace Tests\Feature\Models;

use App\Models\Membership;
use App\Models\Team;
use App\Models\TeamSeason;
use App\Models\User;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
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
     * Regression: a 80 EUR September-December season was charging 53.33 EUR to
     * members registering on 2 October (80 / 3 truncated months * 2 truncated months).
     */
    public function test_season_fee_is_not_prorated_by_default_mid_season(): void
    {
        $this->travelTo('2026-10-02 16:16:00');

        $season = TeamSeason::factory()->create([
            'starts_at' => '2026-09-01',
            'ends_at' => '2026-12-31',
            'fee_amount' => 80.00,
        ]);

        $this->assertSame(80.0, $season->fresh()->proratedFee());
    }

    public function test_membership_payment_charges_full_season_fee_mid_season_by_default(): void
    {
        $this->travelTo('2026-10-02 16:16:00');

        $team = Team::factory()->create();
        $season = TeamSeason::factory()->create([
            'team_id' => $team->id,
            'starts_at' => '2026-09-01',
            'ends_at' => '2026-12-31',
            'fee_amount' => 80.00,
        ]);

        $payment = app(PaymentService::class)->ensurePendingMembershipPayment(User::factory()->create(), $team, $season->fresh());

        $this->assertEquals(80.00, (float) $payment->amount);
        $this->assertEquals(80.00, (float) $payment->payable->fee_amount);
    }

    public function test_new_season_defaults_to_full_fee(): void
    {
        $season = TeamSeason::factory()->create();

        $this->assertFalse($season->fresh()->prorate_fee);
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
