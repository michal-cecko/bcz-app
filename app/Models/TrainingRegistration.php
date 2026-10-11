<?php

namespace App\Models;

use App\Contracts\Payable;
use App\Enums\RegistrationFieldTypeEnum;
use App\Enums\RegistrationStatusEnum;
use App\Models\Concerns\HasUuidV7;
use App\Models\Concerns\PurgesPaymentsOnDelete;
use App\Services\RegistrationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class TrainingRegistration extends Model implements Payable
{
    use HasFactory, HasUuidV7, PurgesPaymentsOnDelete;

    protected $fillable = [
        'training_id',
        'user_id',
        'member_id',
        'form_data',
        'status',
        'locale',
        'cancellation_reason',
        'registered_at',
        'payment_due_at',
        'payment_reminder_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'form_data' => 'json',
            'status' => RegistrationStatusEnum::class,
            'registered_at' => 'datetime',
            'payment_due_at' => 'datetime',
            'payment_reminder_sent_at' => 'datetime',
        ];
    }

    public function isPaymentOverdue(): bool
    {
        return $this->payment_due_at !== null && $this->payment_due_at->isPast();
    }

    public function training(): BelongsTo
    {
        return $this->belongsTo(Training::class);
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bookingContact(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    /**
     * Saved form names take priority over later profile edits.
     */
    public function athleteName(): ?string
    {
        $name = trim(($this->formFieldValueOfType(RegistrationFieldTypeEnum::FIRST_NAME) ?? '').' '.($this->formFieldValueOfType(RegistrationFieldTypeEnum::LAST_NAME) ?? ''));

        if ($name === '') {
            $name = $this->formFieldValueOfType(RegistrationFieldTypeEnum::FULL_NAME) ?? '';
        }

        return $name !== '' ? $name : ($this->member?->name ?? $this->user?->name);
    }

    public function athleteFirstName(): ?string
    {
        return $this->formFieldValueOfType(RegistrationFieldTypeEnum::FIRST_NAME) ?? $this->member?->first_name;
    }

    public function athleteLastName(): ?string
    {
        return $this->formFieldValueOfType(RegistrationFieldTypeEnum::LAST_NAME) ?? $this->member?->last_name;
    }

    /**
     * The value submitted for the first field of the given type, resolved
     * against the training's registration form schema.
     */
    protected function formFieldValueOfType(RegistrationFieldTypeEnum $type): ?string
    {
        return RegistrationService::extractFieldValueOfType(
            $this->form_data ?? [],
            $this->training?->registration_form_schema ?? [],
            $type,
        );
    }

    public function getPaymentDescription(): string
    {
        $athleteName = $this->athleteName();
        $title = $this->training?->getTranslation('title', app()->getLocale())
            ?: $this->training?->getTranslation('title', 'sk')
            ?: 'Tréning';

        return $athleteName ? "{$athleteName} - {$title}" : $title;
    }

    public function getTotalPriceAmount(): float
    {
        return (float) ($this->training?->price_amount ?? 0);
    }

    public function getPriceCurrency(): string
    {
        return 'EUR';
    }

    public function getQrPaymentNote(): ?string
    {
        return $this->training?->renderQrPaymentNote(
            $this->user,
            $this->athleteFirstName() ?? ($this->member_id ? '' : null),
            $this->athleteLastName() ?? ($this->member_id ? '' : null),
        );
    }

    public function getPayoutIban(): ?string
    {
        return $this->training?->effectiveBankAccountIban();
    }

    public function getPayoutRecipientName(): ?string
    {
        return $this->training?->effectiveBankAccountName();
    }
}
