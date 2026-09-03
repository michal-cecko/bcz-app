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
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Membership extends Model implements Payable
{
    use HasCreator, HasFactory, HasUuidV7, PurgesPaymentsOnDelete;

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

    /**
     * The QR note for a membership payment: if there's a training in this
     * season the user registered for that requires this membership and has
     * its own payment note, that note wins - same specific-over-broad idiom
     * as Training::effectivePaymentNoteTemplate(). Otherwise falls back to
     * the season's own note.
     */
    public function getQrPaymentNote(): ?string
    {
        if ($training = $this->drivingTraining()) {
            return $training->renderQrPaymentNote($this->user);
        }

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

    /**
     * The training whose payment note should drive this membership's QR
     * note, if any: the most recently registered non-cancelled registration
     * this user has for a membership_required training in this team and
     * season (or a season-independent, i.e. recurring-across-seasons,
     * training - see Training::isInActiveSeason()) that carries its own
     * payment note. Memberships have no FK to a specific Training - they're
     * scoped to team + user + season, and in principle could back
     * registrations for several trainings - so this resolves the ambiguity
     * by preferring the most recent registration among those that actually
     * set a note.
     */
    private function drivingTraining(): ?Training
    {
        return TrainingRegistration::query()
            ->where('user_id', $this->user_id)
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
