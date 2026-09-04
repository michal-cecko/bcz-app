<?php

namespace App\Models;

use App\Contracts\Payable;
use App\Enums\RegistrationFieldTypeEnum;
use App\Enums\RegistrationStatusEnum;
use App\Models\Concerns\HasUuidV7;
use App\Models\Concerns\PurgesPaymentsOnDelete;
use App\Services\RegistrationService;
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

    /**
     * The athlete's own name as entered on THIS registration's form.
     *
     * One account (matched by e-mail) can register many different athletes — a
     * parent or coach reusing a single address — so the per-registration form
     * data, not the shared linked user, is the source of truth for who this is.
     * Falls back to the linked account holder's name when the form carried no
     * name fields. Mirrors EventRegistration::athleteName(), which reads the
     * event side's field-value rows instead of this side's form_data JSON.
     */
    public function athleteName(): ?string
    {
        $name = trim(($this->athleteFirstName() ?? '').' '.($this->athleteLastName() ?? ''));

        if ($name === '') {
            $name = $this->formFieldValueOfType(RegistrationFieldTypeEnum::FULL_NAME) ?? '';
        }

        return $name !== '' ? $name : $this->user?->name;
    }

    public function athleteFirstName(): ?string
    {
        return $this->formFieldValueOfType(RegistrationFieldTypeEnum::FIRST_NAME);
    }

    public function athleteLastName(): ?string
    {
        return $this->formFieldValueOfType(RegistrationFieldTypeEnum::LAST_NAME);
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
        $title = $this->training?->getTranslation('title', app()->getLocale()) ?? 'Tréning';

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
            $this->athleteFirstName(),
            $this->athleteLastName(),
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
