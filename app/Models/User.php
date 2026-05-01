<?php

namespace App\Models;

use App\Contracts\Linkable;
use App\Enums\GenderEnum;
use App\Enums\MembershipStatusEnum;
use App\Enums\ProfileTypeEnum;
use App\Enums\RoleEnum;
use App\Models\Concerns\HasUuidV7;
use App\Notifications\ResetPassword;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Carbon\Carbon;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class User extends Authenticatable implements FilamentUser, HasAvatar, HasLocalePreference, HasMedia, HasTenants, Linkable
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
        'birth_date',
        'gender',
        'has_free_membership',
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
            'birth_date' => 'date',
            'gender' => GenderEnum::class,
            'has_free_membership' => 'boolean',
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
            ->withPivot('role', 'is_active', 'joined_at', 'continuous_membership')
            ->withTimestamps();
    }

    /**
     * Per-instance memo of team_id => list<role string>. Filament list pages
     * trigger policy checks 100+ times per render; a fresh EXISTS query for
     * each one was the dominant N+1 in production. Loaded once on first call,
     * lives only as long as the User model instance.
     *
     * @var array<string, list<string>>|null
     */
    private ?array $teamRolesCache = null;

    /**
     * @return list<string>
     */
    private function cachedTeamRoles(string $teamId): array
    {
        if ($this->teamRolesCache === null) {
            $cache = [];
            // Eagerly load all team-role rows for this user in a single query.
            // Goes through the BelongsToMany so soft-deleted teams are excluded
            // (matching the prior `where teams.deleted_at is null` semantic).
            // The pivot's `role` column is cast to RoleEnum on TeamUser, so
            // unwrap to its string value to keep the cache comparable via
            // array_intersect against scalar role values.
            foreach ($this->teams()->select('teams.id')->get() as $team) {
                $role = $team->pivot->role;
                $cache[$team->id][] = $role instanceof RoleEnum ? $role->value : (string) $role;
            }
            $this->teamRolesCache = $cache;
        }

        return $this->teamRolesCache[$teamId] ?? [];
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

        $roleValues = collect(Arr::wrap($roles))
            ->map(fn ($r) => $r instanceof RoleEnum ? $r->value : $r)
            ->all();

        return (bool) array_intersect($roleValues, $this->cachedTeamRoles($team->id));
    }

    /**
     * Drop the in-memory team-roles cache. Call after attaching/detaching a
     * team_user pivot if the same User instance will be reused for an
     * authorization check within the same request.
     */
    public function flushTeamRolesCache(): void
    {
        $this->teamRolesCache = null;
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

        if ($this->hasRole([RoleEnum::ADMIN, RoleEnum::EDITOR])) {
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
        // Global admins can switch into any team's context, even teams they don't belong to.
        if ($this->isGlobalAdmin()) {
            return Team::query()->orderBy('name')->get();
        }

        return $this->teams;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        if ($this->isGlobalAdmin()) {
            return true;
        }

        return $this->teams()->whereKey($tenant)->exists();
    }

    /**
     * SUPER_ADMIN / ADMIN globally. Used by tenant-scoping overrides so resources
     * can opt out of the current-team restriction for platform admins.
     */
    public function isGlobalAdmin(): bool
    {
        return $this->hasRole([RoleEnum::SUPER_ADMIN->value, RoleEnum::ADMIN->value]);
    }

    public function athleteProfile(): HasOne
    {
        return $this->hasOne(AthleteProfile::class);
    }

    public function coachProfile(): HasOne
    {
        return $this->hasOne(CoachProfile::class);
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

    public function athleteExercises(): HasMany
    {
        return $this->hasMany(AthleteExercise::class);
    }

    public function athleteGoals(): HasMany
    {
        return $this->hasMany(AthleteGoal::class);
    }

    public function certifications(): MorphMany
    {
        return $this->morphMany(Certification::class, 'certifiable');
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
        $adminGlobalRoles = [RoleEnum::SUPER_ADMIN, RoleEnum::ADMIN, RoleEnum::EDITOR];
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
     * Whether this user should be billed for team memberships.
     * Only users holding CUSTOMER or ATHLETE role are potential payers, and only
     * when they don't have the `has_free_membership` flag.
     */
    public function isMembershipPayer(?Team $team = null): bool
    {
        if (! $this->participatesInMembershipBilling($team)) {
            return false;
        }

        if ($this->has_free_membership) {
            return false;
        }

        return true;
    }

    /**
     * Only CUSTOMER and ATHLETE are eligible for membership records. Admins and editors
     * never have a membership — no record is created for them when a season opens.
     * If a team is given, also accepts users attached to that team with the ATHLETE pivot role.
     */
    public function participatesInMembershipBilling(?Team $team = null): bool
    {
        if ($this->hasRole([RoleEnum::CUSTOMER->value, RoleEnum::ATHLETE->value])) {
            return true;
        }

        if ($team && $this->hasTeamRole([RoleEnum::ATHLETE], $team)) {
            return true;
        }

        return false;
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

    public function preferredLocale(): ?string
    {
        return $this->locale;
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPassword($token));
    }
}
