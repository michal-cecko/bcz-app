<?php

namespace App\Models;

use App\Models\Concerns\HasUuidV7;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Team extends Model implements HasAvatar
{
    use HasFactory, HasSlug, HasTranslations, HasUuidV7, SoftDeletes;

    /** @var list<string> */
    public array $translatable = ['name', 'story', 'achievements'];

    protected $fillable = [
        'name',
        'slug',
        'story',
        'achievements',
        'socials',
        'logo',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'socials' => 'json',
            'is_active' => 'boolean',
        ];
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if (! $this->logo) {
            return null;
        }

        $mediaLibraryItem = MediaLibraryItem::find($this->logo);

        if (! $mediaLibraryItem) {
            return null;
        }

        return rescue(fn () => $mediaLibraryItem->getItem()?->getUrl(), null, false);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(fn (Team $model) => $model->getTranslation('name', 'sk'))
            ->saveSlugsTo('slug');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('is_active', 'joined_at')
            ->withTimestamps();
    }

    public function sportCategories(): HasMany
    {
        return $this->hasMany(SportCategory::class);
    }

    public function exerciseCategories(): HasMany
    {
        return $this->hasMany(ExerciseCategory::class);
    }

    public function exercises(): HasMany
    {
        return $this->hasMany(Exercise::class);
    }

    public function trainings(): HasMany
    {
        return $this->hasMany(Training::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function organizedCompetitions(): HasMany
    {
        return $this->hasMany(Competition::class, 'organizer_team_id');
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(TeamInvitation::class);
    }
}
