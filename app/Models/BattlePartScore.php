<?php

namespace App\Models;

use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BattlePartScore extends Model
{
    use HasFactory, HasUuidV7;

    protected $fillable = [
        'battle_id',
        'round_part_id',
        'side',
        'score',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
        ];
    }

    public function battle(): BelongsTo
    {
        return $this->belongsTo(Battle::class);
    }

    public function roundPart(): BelongsTo
    {
        return $this->belongsTo(RoundPart::class);
    }
}
