<?php

namespace App\Models;

use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class RoundPart extends Model
{
    use HasFactory, HasTranslations, HasUuidV7;

    /** @var list<string> */
    public array $translatable = ['name'];

    protected $fillable = [
        'competition_round_id',
        'name',
        'duration_seconds',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'duration_seconds' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function competitionRound(): BelongsTo
    {
        return $this->belongsTo(CompetitionRound::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(CompetitionResult::class, 'round_part_id');
    }

    public function battlePartScores(): HasMany
    {
        return $this->hasMany(BattlePartScore::class, 'round_part_id');
    }
}
