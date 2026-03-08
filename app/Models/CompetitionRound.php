<?php

namespace App\Models;

use App\Enums\RoundAdvancementTypeEnum;
use App\Enums\ScoringFormatEnum;
use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompetitionRound extends Model
{
    use HasFactory, HasUuidV7;

    protected $fillable = [
        'competition_id',
        'athlete_category_id',
        'round_number',
        'name',
        'scoring_format',
        'advancement_type',
        'advance_count',
        'battle_size',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'scoring_format' => ScoringFormatEnum::class,
            'advancement_type' => RoundAdvancementTypeEnum::class,
            'round_number' => 'integer',
            'advance_count' => 'integer',
            'battle_size' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function athleteCategory(): BelongsTo
    {
        return $this->belongsTo(AthleteCategory::class);
    }

    public function parts(): HasMany
    {
        return $this->hasMany(RoundPart::class)->orderBy('sort_order');
    }

    public function battles(): HasMany
    {
        return $this->hasMany(Battle::class)->orderBy('bracket_position');
    }
}
