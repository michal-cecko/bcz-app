<?php

namespace App\Models;

use App\Contracts\Linkable;
use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Judge extends Model implements HasMedia, Linkable
{
    use HasFactory, HasSlug, HasTranslations, HasUuidV7, InteractsWithMedia;

    /** @var list<string> */
    public array $translatable = ['biography'];

    protected $fillable = [
        'name',
        'slug',
        'biography',
        'disciplines',
        'date_started_judging',
        'socials',
    ];

    protected function casts(): array
    {
        return [
            'date_started_judging' => 'date',
            'disciplines' => 'array',
            'socials' => 'array',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('hero_image')->singleFile();
        $this->addMediaCollection('profile_image')->singleFile();
        $this->addMediaCollection('gallery');
    }

    public function certifications(): MorphMany
    {
        return $this->morphMany(Certification::class, 'certifiable');
    }

    public function judgedCompetitionDetails(): BelongsToMany
    {
        return $this->belongsToMany(CompetitionDetail::class, 'competition_judges')
            ->withPivot('discipline_id');
    }

    public function getLinkUrl(): string
    {
        return '/rozhodcovia/'.$this->slug;
    }

    public function getLinkLabel(): string
    {
        return $this->name;
    }

    /**
     * @return Collection<string, string>
     */
    public static function linkableOptions(): Collection
    {
        return static::query()
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (Judge $j) => [$j->id => $j->getLinkLabel()]);
    }
}
