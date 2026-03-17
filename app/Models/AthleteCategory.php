<?php

namespace App\Models;

use App\Enums\GenderEnum;
use App\Models\Concerns\HasCreator;
use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class AthleteCategory extends Model
{
    use HasCreator, HasFactory, HasTranslations, HasUuidV7;

    /** @var list<string> */
    public array $translatable = ['name', 'description'];

    protected $fillable = [
        'name',
        'parent_id',
        'description',
        'gender',
        'min_weight',
        'max_weight',
        'min_age',
        'max_age',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'gender' => GenderEnum::class,
            'min_weight' => 'decimal:2',
            'max_weight' => 'decimal:2',
            'min_age' => 'integer',
            'max_age' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function competitionDetails(): BelongsToMany
    {
        return $this->belongsToMany(CompetitionDetail::class, 'competition_athlete_category');
    }
}
