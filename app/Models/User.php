<?php

namespace App\Models;

use App\Contracts\Linkable;
use App\Models\Concerns\HasUuidV7;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class User extends Authenticatable implements FilamentUser, HasAvatar, HasMedia, HasTenants, Linkable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasPanelShield, HasRoles, HasSlug, HasUuidV7, InteractsWithMedia, Notifiable;

    protected $fillable = [
        'name',
        'slug',
        'first_name',
        'last_name',
        'email',
        'phone',
        'locale',
        'socials',
        'country_code',
        'contact_email',
        'contact_phone',
        'profile_image',
        'password',
        'has_public_profile',
        'public_profile_approved_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile_image')
            ->singleFile()
            ->useDisk('public');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'socials' => 'json',
            'has_public_profile' => 'boolean',
            'public_profile_approved_at' => 'datetime',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)
            ->withPivot('is_active', 'joined_at')
            ->withTimestamps();
    }

    public function getTenants(Panel $panel): Collection
    {
        return $this->teams;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->teams()->whereKey($tenant)->exists();
    }

    public function athleteProfile(): HasOne
    {
        return $this->hasOne(AthleteProfile::class);
    }

    public function coachProfile(): HasOne
    {
        return $this->hasOne(CoachProfile::class);
    }

    public function athleteExercises(): HasMany
    {
        return $this->hasMany(AthleteExercise::class);
    }

    public function athleteGoals(): HasMany
    {
        return $this->hasMany(AthleteGoal::class);
    }

    public function certifications(): HasMany
    {
        return $this->hasMany(Certification::class);
    }

    public function coachedTrainings(): BelongsToMany
    {
        return $this->belongsToMany(Training::class, 'coach_training')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Alias for coachedTrainings — used by Filament's AttachAction inverse resolution.
     */
    public function trainings(): BelongsToMany
    {
        return $this->coachedTrainings();
    }

    public function trainingRegistrations(): HasMany
    {
        return $this->hasMany(TrainingRegistration::class);
    }

    public function eventRegistrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function competitionResults(): HasMany
    {
        return $this->hasMany(CompetitionResult::class);
    }

    public function judgedCompetitionDetails(): BelongsToMany
    {
        return $this->belongsToMany(CompetitionDetail::class, 'competition_judges')
            ->withPivot('discipline_id');
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->getFirstMediaUrl('profile_image') ?: null;
    }

    public function getProfileImageUrl(): ?string
    {
        return $this->getFilamentAvatarUrl();
    }

    public function getInitials(): string
    {
        $parts = explode(' ', trim($this->name));

        return mb_strtoupper(
            mb_substr($parts[0] ?? '', 0, 1).mb_substr($parts[1] ?? '', 0, 1)
        );
    }

    public function getLinkUrl(): string
    {
        return '/treneri/'.$this->slug;
    }

    public function getLinkLabel(): string
    {
        return $this->name;
    }

    public static function linkableOptions(): Collection
    {
        return static::query()
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (User $u) => [$u->id => $u->getLinkLabel()]);
    }

    /**
     * @return Collection<string, string>
     */
    public static function linkableOptionsForRole(string $role): Collection
    {
        return static::role($role)
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (User $u) => [$u->id => $u->getLinkLabel()]);
    }
}
