<?php

namespace App\Models;

use App\Enums\BillingPeriodEnum;
use App\Enums\SubscriptionStatusEnum;
use App\Models\Concerns\HasCreator;
use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class TeamSubscription extends Model
{
    use HasCreator, HasFactory, HasUuidV7;

    protected $fillable = [
        'team_id',
        'subscription_plan_id',
        'status',
        'billing_period',
        'amount',
        'currency',
        'stripe_subscription_id',
        'starts_at',
        'ends_at',
        'trial_ends_at',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => SubscriptionStatusEnum::class,
            'billing_period' => BillingPeriodEnum::class,
            'amount' => 'decimal:2',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'trial_ends_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function isActive(): bool
    {
        return $this->status === SubscriptionStatusEnum::ACTIVE
            || $this->status === SubscriptionStatusEnum::TRIALING;
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }
}
