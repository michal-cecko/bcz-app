<?php

namespace App\Models;

use App\Contracts\Linkable;
use App\Enums\GenderEnum;
use App\Enums\MembershipStatusEnum;
use App\Enums\ProfileTypeEnum;
use App\Enums\RoleEnum;
use App\Models\Concerns\HasUuidV7;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Carbon\Carbon;
use Database\Factories\UserFactory;
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
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class User extends Authenticatable implements FilamentUser, HasAvatar, HasMedia, HasTenants, Linkable
{
    /** @use HasFactory<UserFactory> */
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
        'coach_profile_approved_at',
        'athlete_profile_approved_at',
        'judge_profile_approved_at',
        'birth_date',
        'gender',
        'password_set_at',
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
            'coach_profile_approved_at' => 'datetime',
            'athlete_profile_approved_at' => 'datetime',
            'judge_profile_approved_at' => 'datetime',
            'birth_date' => 'date',
            'gender' => GenderEnum::class,
            'password_set_at' => 'datetime',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['first_name', 'last_name'])
            ->saveSlugsTo('slug');
    }

    protected static function booted(): void
    {
        static::saving(function (User $user) {
            if ($user->isDirty(['first_name', 'last_name'])) {
                $user->name = trim(($user->first_name ?? '').' '.($user->last_name ?? ''));
            }
        });
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)
            ->using(TeamUser::class)
            ->withPivot('role', 'is_active', 'joined_at')
            ->withTimestamps();
    }

    /**
     * Check if user has a team-scoped role in the given team (defaults to current Filament tenant).
     */
    public function hasTeamRole(RoleEnum|array $roles, ?Team $team = null): bool
    {
        $team ??= filament()->getTenant();
        if (! $team) {
            return false;
        }

        $roles = Arr::wrap($roles);
        $roleValues = collect($roles)->map(fn ($r) => $r instanceof RoleEnum ? $r->value : $r);

        return $this->teams()
            ->where('teams.id', $team->id)
            ->wherePivotIn('role', $roleValues)
            ->exists();
    }

    /**
     * Check if user has ANY of the given roles (global via Spatie OR team-scoped via pivot).
     */
    public function hasAnyAppRole(RoleEnum|array $roles, ?Team $team = null): bool
    {
        $roles = Arr::wrap($roles);

        $globalRoles = array_filter($roles, fn ($r) => $r instanceof RoleEnum && $r->isGlobal());
        $teamRoles = array_filter($roles, fn ($r) => $r instanceof RoleEnum && $r->isTeamScoped());

        if (! empty($globalRoles) && $this->hasRole($globalRoles)) {
            return true;
        }

        if (! empty($teamRoles) && $this->hasTeamRole($teamRoles, $team)) {
            return true;
        }

        return false;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($this->hasRole(RoleEnum::SUPER_ADMIN)) {
            return true;
        }

        if ($this->hasRole([RoleEnum::ADMIN, RoleEnum::EDITOR, RoleEnum::JUDGE])) {
            return true;
        }

        // Any user with a team-scoped role (TEAM_ADMIN, COACH, ATHLETE)
        if ($this->teams()->exists()) {
            return true;
        }

        return false;
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

    public function judgeProfile(): HasOne
    {
        return $this->hasOne(JudgeProfile::class);
    }

    public function profileGalleryItems(): HasMany
    {
        return $this->hasMany(ProfileGalleryItem::class);
    }

    public function coachGalleryItems(): HasMany
    {
        return $this->profileGalleryItems()->where('profile_type', ProfileTypeEnum::Coach);
    }

    public function athleteGalleryItems(): HasMany
    {
        return $this->profileGalleryItems()->where('profile_type', ProfileTypeEnum::Athlete);
    }

    public function judgeGalleryItems(): HasMany
    {
        return $this->profileGalleryItems()->where('profile_type', ProfileTypeEnum::Judge);
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

    public function hasActiveMembershipForTeam(string $teamId): bool
    {
        return $this->memberships()
            ->where('team_id', $teamId)
            ->where('status', MembershipStatusEnum::ACTIVE)
            ->where('ends_at', '>=', now())
            ->exists();
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
        return mb_strtoupper(
            mb_substr($this->first_name ?? '', 0, 1).mb_substr($this->last_name ?? '', 0, 1)
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

    public function getAge(): ?int
    {
        if (! $this->birth_date) {
            return null;
        }

        return (int) $this->birth_date->diffInYears(Carbon::now(), absolute: true);
    }

    /**
     * Returns true when the user has no admin-level roles — i.e. is a regular member (ATHLETE/CUSTOMER).
     */
    public function isMemberLevel(?Team $team = null): bool
    {
        $adminGlobalRoles = [RoleEnum::SUPER_ADMIN, RoleEnum::ADMIN, RoleEnum::EDITOR, RoleEnum::JUDGE];
        $adminTeamRoles = [RoleEnum::TEAM_ADMIN, RoleEnum::COACH];

        if ($this->hasRole($adminGlobalRoles)) {
            return false;
        }

        if ($this->hasTeamRole($adminTeamRoles, $team)) {
            return false;
        }

        return true;
    }

    public function canImpersonate(): bool
    {
        return $this->hasRole([RoleEnum::SUPER_ADMIN, RoleEnum::ADMIN]);
    }

    /**
     * SUPERADMIN cannot be impersonated by anyone.
     * ADMIN cannot be impersonated by other ADMINs (only by SUPERADMIN).
     */
    public function canBeImpersonated(): bool
    {
        if ($this->hasRole(RoleEnum::SUPER_ADMIN)) {
            return false;
        }

        if ($this->hasRole(RoleEnum::ADMIN)) {
            /** @var User|null $current */
            $current = auth()->user();

            return $current && $current->hasRole(RoleEnum::SUPER_ADMIN);
        }

        return true;
    }

    public function isProfileIncomplete(): bool
    {
        return $this->phone === null
            || $this->birth_date === null
            || $this->gender === null;
    }

    public function isProfileApproved(ProfileTypeEnum $type): bool
    {
        return match ($type) {
            ProfileTypeEnum::Coach => $this->coach_profile_approved_at !== null,
            ProfileTypeEnum::Athlete => $this->athlete_profile_approved_at !== null,
            ProfileTypeEnum::Judge => $this->judge_profile_approved_at !== null,
        };
    }

    /**
     * Returns which profile types the user can have based on their roles.
     *
     * @return list<ProfileTypeEnum>
     */
    public function getProfileableRoles(): array
    {
        $types = [];

        if ($this->hasRole(RoleEnum::JUDGE)) {
            $types[] = ProfileTypeEnum::Judge;
        }

        if ($this->teams()->wherePivot('role', RoleEnum::COACH->value)->exists()) {
            $types[] = ProfileTypeEnum::Coach;
        }

        if ($this->teams()->wherePivot('role', RoleEnum::ATHLETE->value)->exists()) {
            $types[] = ProfileTypeEnum::Athlete;
        }

        return $types;
    }

    /**
     * @return Collection<string, string>
     */
    public static function linkableOptionsForRole(string $role): Collection
    {
        $roleEnum = RoleEnum::tryFrom($role);

        if ($roleEnum && $roleEnum->isTeamScoped()) {
            return static::whereHas('teams', fn ($q) => $q->where('team_user.role', $role))
                ->orderBy('name')
                ->get()
                ->mapWithKeys(fn (User $u) => [$u->id => $u->getLinkLabel()]);
        }

        return static::role($role)
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (User $u) => [$u->id => $u->getLinkLabel()]);
    }
}
