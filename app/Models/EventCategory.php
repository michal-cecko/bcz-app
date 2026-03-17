<?php

namespace App\Models;

use App\Models\Concerns\HasCreator;
use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class EventCategory extends Model implements HasMedia
{
    use HasCreator, HasFactory, HasSlug, HasTranslations, HasUuidV7, InteractsWithMedia;

    /** @var list<string> */
    public array $translatable = [
        'title', 'card_subtitle', 'card_description', 'detail_title',
        'about_title', 'about_description', 'types_section_title',
        'types_section_subtitle', 'cta_title', 'cta_description',
    ];

    protected $fillable = [
        'title',
        'slug',
        'color',
        'card_subtitle',
        'card_description',
        'card_image',
        'detail_image',
        'detail_title',
        'hero_image',
        'about_title',
        'about_description',
        'about_image',
        'types_section_title',
        'types_section_subtitle',
        'types_cards',
        'stats',
        'cta_title',
        'cta_description',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'types_cards' => 'json',
            'stats' => 'json',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('card_image')->singleFile();
        $this->addMediaCollection('detail_image')->singleFile();
        $this->addMediaCollection('hero_image')->singleFile();
        $this->addMediaCollection('about_image')->singleFile();
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(fn (EventCategory $model) => $model->getTranslation('title', 'sk'))
            ->saveSlugsTo('slug');
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
