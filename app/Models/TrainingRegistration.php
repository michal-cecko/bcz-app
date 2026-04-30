<?php

namespace App\Models;

use App\Contracts\Payable;
use App\Enums\RegistrationStatusEnum;
use App\Models\Concerns\HasUuidV7;
use App\Services\EmailService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class TrainingRegistration extends Model implements Payable
{
    use HasFactory, HasUuidV7;

    protected $fillable = [
        'training_id',
        'user_id',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function getPaymentDescription(): string
    {
        $userName = trim(($this->user?->first_name ?? '').' '.($this->user?->last_name ?? ''));
        $title = $this->training?->getTranslation('title', app()->getLocale()) ?? 'Tréning';

        return $userName ? "{$userName} - {$title}" : $title;
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
        $template = $this->training?->payment_note;

        if (! $template) {
            return null;
        }

        $schedule = $this->training?->schedules?->first();

        return EmailService::replaceVariables($template, [
            'meno' => (string) ($this->user?->first_name ?? ''),
            'priezvisko' => (string) ($this->user?->last_name ?? ''),
            'nazov_treningu' => (string) ($this->training?->getTranslation('title', app()->getLocale()) ?? ''),
            'mesto' => (string) ($this->training?->city?->name ?? ''),
            'miesto' => (string) ($this->training?->getTranslation('place_name', app()->getLocale()) ?? ''),
            'cas' => $schedule?->start_time ? mb_substr((string) $schedule->start_time, 0, 5) : '',
        ]);
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
