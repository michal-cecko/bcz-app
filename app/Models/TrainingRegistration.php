<?php

namespace App\Models;

use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
