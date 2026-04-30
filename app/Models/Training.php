<?php

namespace App\Models;

use App\Contracts\Linkable;
use App\Enums\GenderEnum;
use App\Enums\RegistrationStatusEnum;
use App\Enums\TrainingPricingTypeEnum;
use App\Models\Concerns\HasCreator;
use App\Models\Concerns\HasResolvedPaymentMethods;
use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Training extends Model implements HasMedia, Linkable
{
    use HasCreator, HasFactory, HasResolvedPaymentMethods, HasSlug, HasTranslations, HasUuidV7, InteractsWithMedia, SoftDeletes;

    /** @var list<string> */
    public array $translatable = ['title', 'description', 'place_name', 'gathering_place'];

    protected $fillable = [
        'sport_category_id',
        'team_id',
        'team_season_id',
        'city_id',
        'title',
        'slug',
        'description',
        'min_age',
        'max_age',
        'gender',
        'duration_minutes',
        'start_time',
        'place_name',
        'place_address',
        'latitude',
        'longitude',
        'gathering_place',
        'max_capacity',
        'notify_on_available',
        'pricing_type',
        'price_amount',
        'payment_note',
        'bank_account_iban',
        'bank_account_name',
        'registration_form_schema',
        'gallery_images',
        'is_active',
        'is_recurring_across_seasons',
        'is_recurring',
        'event_date',
        'registration_opens_at',
        'registration_closes_at',
        'sort_order',
        'confirmation_email_content',
    ];

    protected function casts(): array
    {
        return [
            'min_age' => 'integer',
            'max_age' => 'integer',
            'gender' => GenderEnum::class,
            'pricing_type' => TrainingPricingTypeEnum::class,
            'registration_form_schema' => 'json',
            'gallery_images' => 'json',
            'is_active' => 'boolean',
            'is_recurring_across_seasons' => 'boolean',
            'is_recurring' => 'boolean',
            'event_date' => 'date',
            'notify_on_available' => 'boolean',
            'duration_minutes' => 'integer',
            'max_capacity' => 'integer',
            'price_amount' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'sort_order' => 'integer',
            'registration_opens_at' => 'datetime',
            'registration_closes_at' => 'datetime',
            'confirmation_email_content' => 'json',
        ];
    }

    public function isRegistrationOpen(): bool
    {
        if ($this->registration_opens_at && now()->lt($this->registration_opens_at)) {
            return false;
        }

        if ($this->registration_closes_at && now()->gt($this->registration_closes_at)) {
            return false;
        }

        return true;
    }

    /**
     * @return 'open'|'not_yet_open'|'closed'
     */
    public function registrationStatus(): string
    {
        if ($this->registration_opens_at && now()->lt($this->registration_opens_at)) {
            return 'not_yet_open';
        }

        if ($this->registration_closes_at && now()->gt($this->registration_closes_at)) {
            return 'closed';
        }

        return 'open';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('email_attachments');
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(fn (Training $model) => $model->getTranslation('title', 'sk'))
            ->saveSlugsTo('slug');
    }

    public function getAgeRangeAttribute(): ?string
    {
        if ($this->min_age === null && $this->max_age === null) {
            return null;
        }

        if ($this->max_age === null) {
            return $this->min_age.'+';
        }

        if ($this->min_age === null) {
            return 'do '.$this->max_age;
        }

        return $this->min_age.'-'.$this->max_age;
    }

    public function getLinkUrl(): string
    {
        return '/timy/'.$this->team->slug.'/treningy/'.$this->slug;
    }

    public function getLinkLabel(): string
    {
        return $this->getTranslation('title', app()->getLocale())
            ?: $this->getTranslation('title', 'sk');
    }

    public static function linkableOptions(): Collection
    {
        return static::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->mapWithKeys(fn (Training $t) => [$t->id => $t->getLinkLabel()]);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(TeamSeason::class, 'team_season_id');
    }

    public function isInActiveSeason(): bool
    {
        if (! $this->team_season_id) {
            return true;
        }

        return $this->season?->isActive() ?? false;
    }

    public function isInEndedSeason(): bool
    {
        if (! $this->team_season_id) {
            return false;
        }

        return $this->season?->isPast() ?? false;
    }

    public function scopeCurrent($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('team_season_id')
                ->orWhereHas('season', fn ($s) => $s->where('ends_at', '>=', now()));
        });
    }

    public function scopeArchived($query)
    {
        return $query->whereHas('season', fn ($s) => $s->where('ends_at', '<', now()));
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function sportCategory(): BelongsTo
    {
        return $this->belongsTo(SportCategory::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function effectiveBankAccountIban(): ?string
    {
        return $this->bank_account_iban ?: $this->team?->bank_account_iban;
    }

    public function effectiveBankAccountName(): ?string
    {
        return $this->bank_account_name ?: $this->team?->bank_account_name;
    }

    public function coaches(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'coach_training')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(TrainingSchedule::class)->orderBy('sort_order');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(TrainingRegistration::class);
    }

    public function waitlistEntries(): HasMany
    {
        return $this->hasMany(TrainingWaitlist::class);
    }

    public function waitlistUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'training_waitlist')
            ->withPivot('created_at');
    }

    public function isFull(): bool
    {
        if ($this->max_capacity === null) {
            return false;
        }

        return $this->registrations()
            ->where('status', RegistrationStatusEnum::Approved->value)
            ->count() >= $this->max_capacity;
    }

    public function paymentMethods(): MorphToMany
    {
        return $this->morphToMany(PaymentMethod::class, 'payable', 'payable_payment_method')
            ->using(PayablePaymentMethod::class)
            ->withPivot(['id', 'title', 'description', 'instructions', 'is_enabled', 'sort_order'])
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    public function enabledPaymentMethods(): MorphToMany
    {
        return $this->paymentMethods()
            ->wherePivot('is_enabled', true)
            ->where('is_active', true);
    }
}
