<?php

namespace App\Models;

use App\Contracts\Payable;
use App\Enums\RegistrationStatusEnum;
use App\Models\Concerns\HasUuidV7;
use App\Services\EmailService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class EventRegistration extends Model implements Payable
{
    use HasFactory, HasUuidV7;

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

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function getPaymentDescription(): string
    {
        $userName = trim(($this->user?->first_name ?? '').' '.($this->user?->last_name ?? ''));
        $title = $this->event?->getTranslation('title', app()->getLocale()) ?? 'Podujatie';

        return $userName ? "{$userName} - {$title}" : $title;
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

        return EmailService::replaceVariables($template, [
            'meno' => (string) ($this->user?->first_name ?? ''),
            'priezvisko' => (string) ($this->user?->last_name ?? ''),
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
