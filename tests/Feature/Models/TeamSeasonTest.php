<?php

namespace Tests\Feature\Models;

use App\Models\Membership;
use App\Models\TeamSeason;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        $this->assertEquals(8, $season->totalMonths());
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
        $season = TeamSeason::factory()->create([
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

        // The truncating count behind prorating reports eleven-and-a-bit months.
        $this->assertSame(11, $season->totalMonths());
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
