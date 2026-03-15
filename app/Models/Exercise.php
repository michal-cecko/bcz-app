<?php

namespace App\Models;

use App\Enums\ComplexityLevelEnum;
use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Exercise extends Model implements HasMedia
{
    use HasFactory, HasTranslations, HasUuidV7, InteractsWithMedia;

    /** @var list<string> */
    public array $translatable = ['name', 'description'];

    protected $fillable = [
        'team_id',
        'name',
        'description',
        'complexity',
        'image',
        'exercise_category_id',
    ];

    protected function casts(): array
    {
        return [
            'complexity' => ComplexityLevelEnum::class,
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function exerciseCategory(): BelongsTo
    {
        return $this->belongsTo(ExerciseCategory::class);
    }

    public function athleteExercises(): HasMany
    {
        return $this->hasMany(AthleteExercise::class);
    }
}
