<?php

namespace App\Models;

use App\Models\Concerns\HasCreator;
use App\Models\Concerns\HasUuidV7;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeamSeason extends Model
{
    use HasCreator, HasFactory, HasUuidV7;

    protected $fillable = [
        'team_id',
        'name',
        'starts_at',
        'ends_at',
        'fee_amount',
        'fee_currency',
        'prorate_fee',
        'payment_note',
        'max_capacity',
        'payment_deadline_days',
        'renewal_notified_at',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'prorate_fee' => true,
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'date',
            'ends_at' => 'date',
            'fee_amount' => 'decimal:2',
            'prorate_fee' => 'boolean',
            'max_capacity' => 'integer',
            'payment_deadline_days' => 'integer',
            'renewal_notified_at' => 'datetime',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    public function trainings(): HasMany
    {
        return $this->hasMany(Training::class, 'team_season_id');
    }

    public function isActive(): bool
    {
        return $this->starts_at->lte(today()) && $this->ends_at->gte(today());
    }

    public function isFuture(): bool
    {
        return $this->starts_at->gt(now());
    }

    public function isPast(): bool
    {
        return $this->ends_at->lt(today());
    }

    /**
     * The number of calendar months the season touches, both ends included.
     *
     * A season running 1 September to 31 December is four months, and one running
     * 1 January to 31 December is twelve. Used for prorating the season fee.
     */
    public function totalMonths(): int
    {
        return self::calendarMonthsBetween($this->starts_at, $this->ends_at);
    }

    /**
     * The season length rounded to whole months, for display purposes.
     *
     * Unlike {@see self::totalMonths()}, which counts every calendar month the
     * season touches and is used for prorating, this rounds the actual length:
     * a season running 1 January to 31 December is twelve months, while one
     * running only ten days is zero.
     */
    public function lengthInWholeMonths(): int
    {
        return (int) round($this->starts_at->diffInMonths($this->ends_at));
    }

    /**
     * The season fee spread evenly across every month of the season.
     *
     * Returns null when no unambiguous monthly figure can be derived - a season
     * shorter than half a month, or one with no fee stored.
     */
    public function monthlyFee(): ?float
    {
        $months = $this->lengthInWholeMonths();

        if ($months <= 0 || $this->fee_amount === null) {
            return null;
        }

        return round((float) $this->fee_amount / $months, 2);
    }

    /**
     * The calendar months a member joining on the given date still has to pay for.
     *
     * The joining month counts as a whole month: joining on any day of October
     * in a September-December season leaves October, November and December.
     */
    public function remainingMonths(?Carbon $fromDate = null): int
    {
        $from = ($fromDate ?? now())->copy()->startOfDay();

        if ($from->lte($this->starts_at)) {
            return $this->totalMonths();
        }

        if ($from->gt($this->ends_at)) {
            return 0;
        }

        return min($this->totalMonths(), self::calendarMonthsBetween($from, $this->ends_at));
    }

    /**
     * The season fee a member joining on the given date owes.
     *
     * Seasons charge the full fee regardless of the joining date unless the admin
     * opted the season into prorating, in which case the fee is spread evenly over
     * the season's calendar months and only the remaining ones are charged.
     */
    public function proratedFee(?Carbon $fromDate = null): float
    {
        if (! $this->prorate_fee) {
            return (float) $this->fee_amount;
        }

        $total = $this->totalMonths();

        if ($total <= 0) {
            return (float) $this->fee_amount;
        }

        $remaining = $this->remainingMonths($fromDate);

        return round(((float) $this->fee_amount / $total) * $remaining, 2);
    }

    private static function calendarMonthsBetween(Carbon $from, Carbon $to): int
    {
        return max(0, ($to->year - $from->year) * 12 + ($to->month - $from->month) + 1);
    }

    public function hasCapacity(): bool
    {
        if ($this->max_capacity === null) {
            return true;
        }

        return $this->memberships()->count() < $this->max_capacity;
    }
}
