<?php

namespace App\Models;

use App\Contracts\Linkable;
use App\Enums\PageStatusEnum;
use App\Models\Concerns\HasCreator;
use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Page extends Model implements HasMedia, Linkable
{
    use HasCreator, HasFactory, HasSlug, HasTranslations, HasUuidV7, InteractsWithMedia, SoftDeletes;

    /** @var list<string> */
    public array $translatable = ['title', 'meta_title', 'meta_description'];

    protected $fillable = [
        'title', 'slug', 'content', 'meta_title', 'meta_description',
        'featured_image', 'status', 'is_system', 'system_key',
        'published_at', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'status' => PageStatusEnum::class,
            'is_system' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured_image')->singleFile();
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(fn (Page $model) => $model->getTranslation('title', 'sk'))
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', PageStatusEnum::Published);
    }

    public function getLinkUrl(): string
    {
        if ($this->slug === '/') {
            return '/';
        }

        return '/'.$this->slug;
    }

    public function getLinkLabel(): string
    {
        return $this->getTranslation('title', app()->getLocale())
            ?: $this->getTranslation('title', 'sk');
    }

    public static function linkableOptions(): Collection
    {
        return static::query()
            ->published()
            ->orderBy('sort_order')
            ->get()
            ->mapWithKeys(fn (Page $page) => [$page->id => $page->getLinkLabel()]);
    }

    protected static function booted(): void
    {
        static::deleting(function (Page $page) {
            if ($page->is_system) {
                return false;
            }
        });
    }
}
