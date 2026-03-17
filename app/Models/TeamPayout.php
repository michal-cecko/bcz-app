<?php

namespace App\Models;

use App\Enums\PayoutStatusEnum;
use App\Models\Concerns\HasCreator;
use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamPayout extends Model
{
    use HasCreator, HasFactory, HasUuidV7;

    protected $fillable = [
        'team_id',
        'gross_amount',
        'fee_amount',
        'net_amount',
        'currency',
        'status',
        'bank_account_iban',
        'bank_account_name',
        'reference',
        'notes',
        'period_from',
        'period_to',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'gross_amount' => 'decimal:2',
            'fee_amount' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'status' => PayoutStatusEnum::class,
            'period_from' => 'date',
            'period_to' => 'date',
            'paid_at' => 'datetime',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
