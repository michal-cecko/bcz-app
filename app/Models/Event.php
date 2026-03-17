<?php

namespace App\Models;

use App\Contracts\Linkable;
use App\Enums\EventTypeEnum;
use App\Models\Concerns\HasCreator;
use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Event extends Model implements HasMedia, Linkable
{
    use HasCreator, HasFactory, HasSlug, HasTranslations, HasUuidV7, InteractsWithMedia, SoftDeletes;

    /** @var list<string> */
    public array $translatable = ['title', 'card_description', 'content'];

    protected $fillable = [
        'event_type',
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
        'place_name',
        'place_address',
        'latitude',
        'longitude',
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
            'event_type' => EventTypeEnum::class,
            'date' => 'date',
            'date_end' => 'date',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'attendee_count' => 'integer',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('card_image')->singleFile();
        $this->addMediaCollection('detail_image')->singleFile();
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(fn (Event $model) => $model->getTranslation('title', 'sk'))
            ->saveSlugsTo('slug');
    }

    /**
     * Computed status: hidden, countdown, registering, in_progress, upcoming, finished.
     */
    protected function status(): Attribute
    {
        return Attribute::get(function (): string {
            if (! $this->is_published) {
                return 'hidden';
            }

            $now = now();

            if ($this->date_end && $now->greaterThan($this->date_end)) {
                return 'finished';
            }

            if ($this->date && $now->greaterThanOrEqualTo($this->date)) {
                return 'in_progress';
            }

            $org = $this->organization;

            if ($org) {
                if ($org->registration_opens_at && $now->greaterThanOrEqualTo($org->registration_opens_at)) {
                    if (! $org->registration_closes_at || $now->lessThanOrEqualTo($org->registration_closes_at)) {
                        return 'registering';
                    }
                }

                if ($org->show_countdown) {
                    return 'countdown';
                }
            }

            return 'upcoming';
        });
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

    public function organization(): HasOne
    {
        return $this->hasOne(EventOrganization::class);
    }

    public function competitionDetail(): HasOne
    {
        return $this->hasOne(CompetitionDetail::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }
}
