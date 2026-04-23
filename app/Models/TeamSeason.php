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
        'payment_note',
        'max_capacity',
        'payment_deadline_days',
        'renewal_notified_at',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'date',
            'ends_at' => 'date',
            'fee_amount' => 'decimal:2',
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
        return $this->starts_at->lte(now()) && $this->ends_at->gte(now());
    }

    public function isFuture(): bool
    {
        return $this->starts_at->gt(now());
    }

    public function isPast(): bool
    {
        return $this->ends_at->lt(now());
    }

    public function totalMonths(): int
    {
        return (int) $this->starts_at->diffInMonths($this->ends_at);
    }

    public function remainingMonths(?Carbon $fromDate = null): int
    {
        $from = $fromDate ?? now();
        $total = $this->totalMonths();

        if ($from->lte($this->starts_at)) {
            return $total;
        }

        if ($from->gte($this->ends_at)) {
            return 0;
        }

        return max(1, (int) $from->diffInMonths($this->ends_at));
    }

    public function proratedFee(?Carbon $fromDate = null): float
    {
        $total = $this->totalMonths();

        if ($total === 0) {
            return (float) $this->fee_amount;
        }

        $remaining = $this->remainingMonths($fromDate);

        return round(((float) $this->fee_amount / $total) * $remaining, 2);
    }

    public function hasCapacity(): bool
    {
        if ($this->max_capacity === null) {
            return true;
        }

        return $this->memberships()->count() < $this->max_capacity;
    }
}
