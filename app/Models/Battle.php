<?php

namespace App\Models;

use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Battle extends Model
{
    use HasFactory, HasUuidV7;

    protected $fillable = [
        'competition_round_id',
        'athlete_category_id',
        'bracket_position',
        'competitor_a_id',
        'competitor_b_id',
        'winner_id',
    ];

    protected function casts(): array
    {
        return [
            'bracket_position' => 'integer',
            'competitor_a_id' => 'json',
            'competitor_b_id' => 'json',
            'winner_id' => 'json',
        ];
    }

    public function competitionRound(): BelongsTo
    {
        return $this->belongsTo(CompetitionRound::class);
    }

    public function athleteCategory(): BelongsTo
    {
        return $this->belongsTo(AthleteCategory::class);
    }
}
