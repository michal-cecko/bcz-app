<?php

namespace App\Models;

use App\Enums\RegistrationFieldTypeEnum;
use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistrationFieldValue extends Model
{
    use HasFactory, HasUuidV7;

    protected $fillable = [
        'event_registration_id',
        'field_key',
        'field_type',
        'value',
    ];

    protected function casts(): array
    {
        return [
            'field_type' => RegistrationFieldTypeEnum::class,
        ];
    }

    public function eventRegistration(): BelongsTo
    {
        return $this->belongsTo(EventRegistration::class);
    }
}
