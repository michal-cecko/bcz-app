<?php

namespace App\Models;

use App\Contracts\Payable;
use App\Enums\MembershipStatusEnum;
use App\Models\Concerns\HasCreator;
use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Membership extends Model implements Payable
{
    use HasCreator, HasFactory, HasUuidV7;

    protected $fillable = [
        'team_id',
        'user_id',
        'team_season_id',
        'status',
        'fee_amount',
        'fee_currency',
        'is_free',
        'payment_deadline_at',
        'starts_at',
        'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => MembershipStatusEnum::class,
            'fee_amount' => 'decimal:2',
            'is_free' => 'boolean',
            'payment_deadline_at' => 'datetime',
            'starts_at' => 'date',
            'ends_at' => 'date',
        ];
    }

    public function isActive(): bool
    {
        return $this->status === MembershipStatusEnum::ACTIVE
            && $this->ends_at->isFuture();
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(TeamSeason::class, 'team_season_id');
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function getPaymentDescription(): string
    {
        $userName = trim(($this->user?->first_name ?? '').' '.($this->user?->last_name ?? ''));
        $seasonName = $this->season?->name ?? 'Členstvo';

        return $userName ? "{$userName} - {$seasonName}" : $seasonName;
    }
}
