<?php

namespace App\Models;

use App\Enums\EventPricingTypeEnum;
use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventOrganization extends Model
{
    use HasFactory, HasUuidV7;

    protected $fillable = [
        'event_id',
        'max_capacity',
        'pricing_type',
        'price_amount',
        'price_currency',
        'registration_form_schema',
        'registration_opens_at',
        'registration_closes_at',
        'is_public_registration',
        'show_countdown',
        'external_link',
    ];

    protected function casts(): array
    {
        return [
            'max_capacity' => 'integer',
            'pricing_type' => EventPricingTypeEnum::class,
            'price_amount' => 'decimal:2',
            'registration_form_schema' => 'json',
            'registration_opens_at' => 'datetime',
            'registration_closes_at' => 'datetime',
            'is_public_registration' => 'boolean',
            'show_countdown' => 'boolean',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
