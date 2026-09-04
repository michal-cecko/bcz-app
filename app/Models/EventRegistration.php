<?php

namespace App\Models;

use App\Contracts\Payable;
use App\Enums\RegistrationFieldTypeEnum;
use App\Enums\RegistrationStatusEnum;
use App\Models\Concerns\HasUuidV7;
use App\Models\Concerns\PurgesPaymentsOnDelete;
use App\Services\EmailService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class EventRegistration extends Model implements Payable
{
    use HasFactory, HasUuidV7, PurgesPaymentsOnDelete;

    protected $fillable = [
        'event_id',
        'user_id',
        'athlete_category_id',
        'registration_fee_id',
        'status',
        'locale',
        'registered_at',
        'payment_due_at',
        'payment_reminder_sent_at',
        'weight_in',
    ];

    protected function casts(): array
    {
        return [
            'status' => RegistrationStatusEnum::class,
            'registered_at' => 'datetime',
            'payment_due_at' => 'datetime',
            'payment_reminder_sent_at' => 'datetime',
            'weight_in' => 'decimal:2',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function athleteCategory(): BelongsTo
    {
        return $this->belongsTo(AthleteCategory::class);
    }

    public function registrationFee(): BelongsTo
    {
        return $this->belongsTo(RegistrationFee::class);
    }

    public function fieldValues(): HasMany
    {
        return $this->hasMany(RegistrationFieldValue::class);
    }

    /**
     * The athlete's own name as entered on THIS registration's form.
     *
     * One account (matched by email) can register many different athletes — a
     * coach/parent using a single email — so the per-registration form data,
     * not the shared linked user, is the source of truth for who this is.
     * Falls back to the linked account holder's name when the form carried no
     * name fields.
     */
    public function athleteName(): ?string
    {
        $name = trim(($this->athleteFirstName() ?? '').' '.($this->athleteLastName() ?? ''));

        if ($name === '') {
            $name = $this->fieldValueOfType(RegistrationFieldTypeEnum::FULL_NAME) ?? '';
        }

        return $name !== '' ? $name : $this->user?->name;
    }

    public function athleteFirstName(): ?string
    {
        return $this->fieldValueOfType(RegistrationFieldTypeEnum::FIRST_NAME);
    }

    public function athleteLastName(): ?string
    {
        return $this->fieldValueOfType(RegistrationFieldTypeEnum::LAST_NAME);
    }

    /**
     * The email entered on this registration's form, falling back to the linked
     * account holder's email.
     */
    public function athleteEmail(): ?string
    {
        return $this->fieldValueOfType(RegistrationFieldTypeEnum::EMAIL) ?? $this->user?->email;
    }

    /**
     * The (trimmed, non-empty) value of the first form field of the given type,
     * or null. Reads the loaded fieldValues relation to avoid per-call queries.
     */
    protected function fieldValueOfType(RegistrationFieldTypeEnum $type): ?string
    {
        $value = $this->fieldValues
            ->first(fn (RegistrationFieldValue $fieldValue): bool => $fieldValue->field_type === $type)?->value;

        return is_string($value) && trim($value) !== '' ? trim($value) : null;
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function getPaymentDescription(): string
    {
        $athleteName = $this->athleteName();
        $title = $this->event?->getTranslation('title', app()->getLocale()) ?? 'Podujatie';

        return $athleteName ? "{$athleteName} - {$title}" : $title;
    }

    public function getTotalPriceAmount(): float
    {
        if ($this->registrationFee) {
            return (float) $this->registrationFee->amount;
        }

        return (float) ($this->event?->organization?->price_amount ?? 0);
    }

    public function getPriceCurrency(): string
    {
        if ($this->registrationFee?->currency) {
            return $this->registrationFee->currency;
        }

        return $this->event?->organization?->price_currency ?? 'EUR';
    }

    public function getQrPaymentNote(): ?string
    {
        $template = $this->event?->organization?->payment_note;

        if (! $template) {
            return null;
        }

        return EmailService::renderPaymentNote($template, [
            'meno' => (string) ($this->athleteFirstName() ?? $this->user?->first_name ?? ''),
            'priezvisko' => (string) ($this->athleteLastName() ?? $this->user?->last_name ?? ''),
            'nazov_eventu' => (string) ($this->event?->getTranslation('title', app()->getLocale()) ?? ''),
            'datum_eventu' => $this->event?->date?->format('d.m.Y') ?? '',
            'miesto' => (string) ($this->event?->place_name ?? ''),
        ]);
    }

    public function getPayoutIban(): ?string
    {
        return $this->event?->organization?->effectiveBankAccountIban()
            ?: $this->event?->team?->bank_account_iban;
    }

    public function getPayoutRecipientName(): ?string
    {
        return $this->event?->organization?->effectiveBankAccountName()
            ?: $this->event?->team?->bank_account_name;
    }
}
