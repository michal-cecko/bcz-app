<?php

namespace App\Models;

use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class SportCategory extends Model implements HasMedia
{
    use HasFactory, HasSlug, HasTranslations, HasUuidV7, InteractsWithMedia;

    /** @var list<string> */
    public array $translatable = ['name', 'description'];

    protected $fillable = [
        'team_id',
        'name',
        'slug',
        'description',
        'hero_image',
        'is_active',
        'sort_order',
        'page_content',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'page_content' => 'json',
            'sort_order' => 'integer',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('hero_image')->singleFile();
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(fn (SportCategory $model) => $model->getTranslation('name', 'sk'))
            ->saveSlugsTo('slug');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function exerciseCategories(): BelongsToMany
    {
        return $this->belongsToMany(ExerciseCategory::class);
    }

    public function trainings(): HasMany
    {
        return $this->hasMany(Training::class);
    }
}
