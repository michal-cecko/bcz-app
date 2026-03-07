<?php

namespace App\Models;

use App\Enums\ScoringFormatEnum;
use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

class Discipline extends Model
{
    use HasFactory, HasTranslations, HasUuidV7;

    /** @var list<string> */
    public array $translatable = ['name', 'description', 'scoring_description'];

    protected $fillable = [
        'name',
        'description',
        'scoring_description',
        'icon',
        'image',
        'scoring_format',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'scoring_format' => ScoringFormatEnum::class,
            'sort_order' => 'integer',
        ];
    }

    public function competitions(): BelongsToMany
    {
        return $this->belongsToMany(Competition::class, 'competition_discipline');
    }
}
