<?php

namespace App\Models;

use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Models\Concerns\HasCreator;
use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\DB;

class Payment extends Model
{
    use HasCreator, HasFactory, HasUuidV7;

    protected $fillable = [
        'team_id',
        'user_id',
        'payer_name',
        'payer_email',
        'payable_type',
        'payable_id',
        'amount',
        'currency',
        'status',
        'payment_method',
        'gopay_payment_id',
        'gopay_order_number',
        'variable_symbol',
        'notes',
        'paid_at',
        'refunded_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'status' => PaymentStatusEnum::class,
            'payment_method' => PaymentMethodEnum::class,
            'sequence_number' => 'integer',
            'paid_at' => 'datetime',
            'refunded_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $payment): void {
            if (! empty($payment->sequence_number)) {
                return;
            }

            if (DB::connection()->getDriverName() === 'pgsql') {
                $payment->sequence_number = (int) DB::selectOne(
                    "SELECT nextval('payments_sequence_number_seq') AS seq"
                )->seq;

                return;
            }

            $payment->sequence_number = ((int) static::max('sequence_number')) + 1;
        });
    }

    /**
     * 8-digit zero-padded variable symbol derived from the payment's sequence_number.
     * Returns null until the payment has been persisted and has a sequence_number.
     */
    public function formattedVariableSymbol(): ?string
    {
        if (empty($this->sequence_number)) {
            return null;
        }

        return str_pad((string) $this->sequence_number, 8, '0', STR_PAD_LEFT);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getPayableNameAttribute(): string
    {
        $payable = $this->payable;

        if (! $payable) {
            return '-';
        }

        return match ($this->payable_type) {
            'membership' => $payable->season?->name ?? 'Členstvo',
            'training_registration' => $payable->training?->getTranslation('title', 'sk') ?? 'Tréning',
            'competition_registration', 'event_registration' => $payable->event?->getTranslation('title', 'sk') ?? 'Podujatie',
            default => '-',
        };
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->user?->name ?? $this->payer_name ?? '-';
    }

    public function getDisplayEmailAttribute(): string
    {
        return $this->user?->email ?? $this->payer_email ?? '-';
    }
}
