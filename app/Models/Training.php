<?php

namespace App\Models;

use App\Contracts\Linkable;
use App\Enums\GenderEnum;
use App\Enums\TrainingPricingTypeEnum;
use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Training extends Model implements Linkable
{
    use HasFactory, HasSlug, HasTranslations, HasUuidV7, SoftDeletes;

    /** @var list<string> */
    public array $translatable = ['title', 'description', 'place_name', 'gathering_place'];

    protected $fillable = [
        'sport_category_id',
        'team_id',
        'title',
        'slug',
        'description',
        'age_group',
        'gender',
        'duration_minutes',
        'start_time',
        'schedule_days',
        'place_name',
        'place_address',
        'latitude',
        'longitude',
        'gathering_place',
        'max_capacity',
        'notify_on_available',
        'pricing_type',
        'price_amount',
        'registration_form_schema',
        'gallery_images',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'gender' => GenderEnum::class,
            'pricing_type' => TrainingPricingTypeEnum::class,
            'schedule_days' => 'json',
            'registration_form_schema' => 'json',
            'gallery_images' => 'json',
            'is_active' => 'boolean',
            'notify_on_available' => 'boolean',
            'duration_minutes' => 'integer',
            'max_capacity' => 'integer',
            'price_amount' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'sort_order' => 'integer',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(fn (Training $model) => $model->getTranslation('title', 'sk'))
            ->saveSlugsTo('slug');
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

    public function sportCategory(): BelongsTo
    {
        return $this->belongsTo(SportCategory::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function coaches(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'coach_training')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(TrainingRegistration::class);
    }
}
