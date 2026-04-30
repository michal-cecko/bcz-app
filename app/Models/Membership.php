<?php

namespace App\Models;

use App\Contracts\Payable;
use App\Enums\MembershipStatusEnum;
use App\Models\Concerns\HasCreator;
use App\Models\Concerns\HasUuidV7;
use App\Services\EmailService;
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
        'payment_reminder_sent_at',
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
            'payment_reminder_sent_at' => 'datetime',
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

    public function getTotalPriceAmount(): float
    {
        return (float) $this->fee_amount;
    }

    public function getPriceCurrency(): string
    {
        return $this->fee_currency ?? 'EUR';
    }

    public function getQrPaymentNote(): ?string
    {
        $template = $this->season?->payment_note;

        if (! $template) {
            return null;
        }

        return EmailService::replaceVariables($template, [
            'meno' => (string) ($this->user?->first_name ?? ''),
            'priezvisko' => (string) ($this->user?->last_name ?? ''),
            'sezona' => (string) ($this->season?->name ?? ''),
            'nazov_timu' => (string) ($this->team?->getTranslation('name', app()->getLocale()) ?? ''),
        ]);
    }

    public function getPayoutIban(): ?string
    {
        return $this->team?->bank_account_iban;
    }

    public function getPayoutRecipientName(): ?string
    {
        return $this->team?->bank_account_name;
    }
}
