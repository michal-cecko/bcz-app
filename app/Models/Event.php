<?php

namespace App\Models;

use App\Contracts\Linkable;
use App\Enums\EventTypeEnum;
use App\Enums\TimetableEntryStatusEnum;
use App\Models\Concerns\HasCreator;
use App\Models\Concerns\HasUuidV7;
use Carbon\Carbon;
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
    public array $translatable = ['title', 'card_description', 'content', 'report_content'];

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
        'timezone',
        'detail_image',
        'content',
        'report_content',
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
        $this->addMediaCollection('email_attachments');
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

            // Finished: past the end date, or past the start date if no end date
            $endDate = $this->date_end ?? $this->date;
            if ($endDate && $now->greaterThan($endDate)) {
                return 'finished';
            }

            // Competition: finished when all timetable entries are done
            if ($this->event_type === EventTypeEnum::Competition && $this->competitionDetail) {
                $entries = $this->competitionDetail->timetableEntries;
                if ($entries->isNotEmpty() && $entries->every(fn (TimetableEntry $e) => $e->status === TimetableEntryStatusEnum::FINISHED)) {
                    return 'finished';
                }
            }

            // In progress: started but not yet finished (only when date_end exists)
            if ($this->date_end && $this->date && $now->greaterThanOrEqualTo($this->date) && $now->lessThanOrEqualTo($this->date_end)) {
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

    /**
     * Get the event's timezone identifier.
     */
    public function getTimezone(): string
    {
        return $this->timezone ?? 'Europe/Bratislava';
    }

    /**
     * Convert a UTC datetime to the event's local timezone.
     */
    public function toLocalTime(?Carbon $utcTime): ?Carbon
    {
        return $utcTime?->copy()->setTimezone($this->getTimezone());
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
