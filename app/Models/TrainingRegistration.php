<?php

namespace App\Models;

use App\Contracts\Payable;
use App\Enums\RegistrationStatusEnum;
use App\Models\Concerns\HasUuidV7;
use App\Models\Concerns\PurgesPaymentsOnDelete;
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
        return $this->training?->renderQrPaymentNote($this->user);
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
