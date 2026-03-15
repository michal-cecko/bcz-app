<?php

namespace App\Models;

use App\Enums\RegistrationStatusEnum;
use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class TrainingRegistration extends Model
{
    use HasFactory, HasUuidV7;

    protected $fillable = [
        'training_id',
        'user_id',
        'form_data',
        'status',
        'registered_at',
    ];

    protected function casts(): array
    {
        return [
            'form_data' => 'json',
            'status' => RegistrationStatusEnum::class,
            'registered_at' => 'datetime',
        ];
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
}
