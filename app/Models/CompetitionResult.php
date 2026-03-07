<?php

namespace App\Models;

use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitionResult extends Model
{
    use HasFactory, HasUuidV7;

    protected $fillable = [
        'round_part_id',
        'user_id',
        'score',
        'place',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'place' => 'integer',
        ];
    }

    public function roundPart(): BelongsTo
    {
        return $this->belongsTo(RoundPart::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
