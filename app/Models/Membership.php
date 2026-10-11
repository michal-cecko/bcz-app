<?php

namespace App\Models;

use App\Contracts\Payable;
use App\Enums\MembershipStatusEnum;
use App\Enums\RegistrationStatusEnum;
use App\Enums\TrainingPricingTypeEnum;
use App\Models\Concerns\HasCreator;
use App\Models\Concerns\HasUuidV7;
use App\Models\Concerns\PurgesPaymentsOnDelete;
use App\Services\EmailService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Validation\ValidationException;

class Membership extends Model implements Payable
{
    use HasCreator, HasFactory, HasUuidV7, PurgesPaymentsOnDelete;

    protected $fillable = [
        'team_id',
        'user_id',
        'member_id',
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

    protected static function booted(): void
    {
        static::saving(function (Membership $membership): void {
            if ($membership->member_id) {
                $member = Member::findOrFail($membership->member_id);
                $membership->user_id = $member->user_id;
                if ($membership->team_season_id && $membership->season?->team_id !== $membership->team_id) {
                    throw ValidationException::withMessages(['team_season_id' => 'The season must belong to the membership team.']);
                }
            } elseif (config('family.enabled') && ! $membership->exists) {
                throw ValidationException::withMessages(['member_id' => __('family.choose')]);
            }
        });
    }

    public function isActive(): bool
    {
        return $this->status === MembershipStatusEnum::ACTIVE
            && $this->ends_at->gte(today());
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function scopeManagedBy(Builder $query, User $user): void
    {
        $query->where(function ($query) use ($user): void {
            $query->whereHas('member', fn ($member) => $member->where('user_id', $user->id))
                ->orWhere(fn ($legacy) => $legacy->whereNull('member_id')->where('user_id', $user->id));
        });
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function scopeForSameMemberAndSeason(Builder $query, Membership $membership): void
    {
        $query->where('team_id', $membership->team_id)
            ->where($membership->member_id ? 'member_id' : 'user_id', $membership->member_id ?? $membership->user_id)
            ->when(! $membership->member_id, fn (Builder $query) => $query->whereNull('member_id'))
            ->where('team_season_id', $membership->team_season_id);
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
        $userName = $this->member?->name ?? trim(($this->member ? ($this->member->first_name ?? '') : ($this->user?->first_name ?? '')).' '.($this->member ? ($this->member->last_name ?? '') : ($this->user?->last_name ?? '')));
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

    /**
     * The QR note for a membership payment: if there's a training in this
     * season the member registered for that requires this membership and has
     * its own payment note, that note wins - same specific-over-broad idiom
     * as Training::effectivePaymentNoteTemplate(). Otherwise falls back to
     * the season's own note.
     */
    public function getQrPaymentNote(): ?string
    {
        if ($training = $this->drivingTraining()) {
            return $training->renderQrPaymentNote($this->user, $this->member ? ($this->member->first_name ?? '') : null, $this->member ? ($this->member->last_name ?? '') : null);
        }

        $template = $this->season?->payment_note;

        if (! $template) {
            return null;
        }

        return EmailService::renderPaymentNote($template, [
            'meno' => (string) ($this->member ? ($this->member->first_name ?? '') : ($this->user?->first_name ?? '')),
            'priezvisko' => (string) ($this->member ? ($this->member->last_name ?? '') : ($this->user?->last_name ?? '')),
            'sezona' => (string) ($this->season?->name ?? ''),
            'nazov_timu' => (string) ($this->team?->getTranslation('name', app()->getLocale()) ?? ''),
        ]);
    }

    /**
     * A membership can cover several trainings for one member, team and season.
     * Prefer the latest registration with its own payment note, including
     * recurring trainings without a season. Legacy rows use the account.
     */
    private function drivingTraining(): ?Training
    {
        return TrainingRegistration::query()
            ->where($this->member_id ? 'member_id' : 'user_id', $this->member_id ?? $this->user_id)
            ->where('status', '!=', RegistrationStatusEnum::Cancelled)
            ->whereHas('training', function ($query): void {
                $query->where('team_id', $this->team_id)
                    ->where('pricing_type', TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED)
                    ->where(function ($seasonQuery): void {
                        $seasonQuery->whereNull('team_season_id')
                            ->orWhere('team_season_id', $this->team_season_id);
                    });
            })
            ->with('training')
            ->latest('registered_at')
            ->get()
            ->map(fn (TrainingRegistration $registration): ?Training => $registration->training)
            ->first(fn (?Training $training): bool => filled($training?->payment_note));
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
