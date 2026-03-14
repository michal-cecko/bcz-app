<?php

namespace App\Models;

use App\Contracts\Linkable;
use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Event extends Model implements Linkable
{
    use HasFactory, HasSlug, HasTranslations, HasUuidV7, SoftDeletes;

    /** @var list<string> */
    public array $translatable = ['title', 'card_description', 'content'];

    protected $fillable = [
        'event_category_id',
        'team_id',
        'title',
        'slug',
        'card_description',
        'card_image',
        'date',
        'date_end',
        'country',
        'city',
        'detail_image',
        'content',
        'attendee_count',
        'client',
        'is_published',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'date_end' => 'date',
            'attendee_count' => 'integer',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(fn (Event $model) => $model->getTranslation('title', 'sk'))
            ->saveSlugsTo('slug');
    }

    public function getLinkUrl(): string
    {
        return '/eventy/'.$this->slug;
    }

    public function getLinkLabel(): string
    {
        return $this->getTranslation('title', app()->getLocale())
            ?: $this->getTranslation('title', 'sk');
    }

    public static function linkableOptions(): Collection
    {
        return static::query()
            ->where('is_published', true)
            ->orderByDesc('date')
            ->get()
            ->mapWithKeys(fn (Event $e) => [$e->id => $e->getLinkLabel()]);
    }

    public function eventCategory(): BelongsTo
    {
        return $this->belongsTo(EventCategory::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
