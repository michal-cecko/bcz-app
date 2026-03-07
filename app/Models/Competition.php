<?php

namespace App\Models;

use App\Models\Concerns\HasUuidV7;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Competition extends Model
{
    use HasFactory, HasSlug, HasTranslations, HasUuidV7, SoftDeletes;

    /** @var list<string> */
    public array $translatable = ['name', 'description'];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'date_start',
        'date_end',
        'place_name',
        'place_address',
        'country',
        'city',
        'latitude',
        'longitude',
        'organizer_team_id',
        'external_link',
        'registration_opens_at',
        'registration_closes_at',
        'show_countdown',
        'is_public_registration',
        'is_published',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'description' => 'json',
            'date_start' => 'date',
            'date_end' => 'date',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'registration_opens_at' => 'datetime',
            'registration_closes_at' => 'datetime',
            'show_countdown' => 'boolean',
            'is_public_registration' => 'boolean',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(fn (Competition $model) => $model->getTranslation('name', 'sk'))
            ->saveSlugsTo('slug');
    }

    /**
     * Computed status: hidden, countdown, registering, in_progress, finished.
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

            if ($now->greaterThanOrEqualTo($this->date_start)) {
                return 'in_progress';
            }

            if ($this->registration_opens_at && $now->greaterThanOrEqualTo($this->registration_opens_at)) {
                if (! $this->registration_closes_at || $now->lessThanOrEqualTo($this->registration_closes_at)) {
                    return 'registering';
                }
            }

            if ($this->show_countdown) {
                return 'countdown';
            }

            return 'upcoming';
        });
    }

    /**
     * Computed delay from timetable entries.
     */
    protected function delay(): Attribute
    {
        return Attribute::get(function (): ?int {
            $inProgress = $this->timetableEntries()
                ->where('status', 'in_progress')
                ->whereNotNull('actual_start_time')
                ->first();

            if (! $inProgress) {
                return null;
            }

            return (int) $inProgress->actual_start_time->diffInMinutes($inProgress->scheduled_time, false);
        });
    }

    public function organizerTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'organizer_team_id');
    }

    public function disciplines(): BelongsToMany
    {
        return $this->belongsToMany(Discipline::class, 'competition_discipline');
    }

    public function athleteCategories(): BelongsToMany
    {
        return $this->belongsToMany(AthleteCategory::class, 'competition_athlete_category');
    }

    public function judges(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'competition_judges')
            ->withPivot('discipline_id');
    }

    public function timetableEntries(): HasMany
    {
        return $this->hasMany(TimetableEntry::class)->orderBy('sort_order');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(CompetitionRegistration::class);
    }

    public function registrationFees(): HasMany
    {
        return $this->hasMany(RegistrationFee::class);
    }

    public function rounds(): HasMany
    {
        return $this->hasMany(CompetitionRound::class)->orderBy('sort_order');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(CompetitionReport::class);
    }
}
