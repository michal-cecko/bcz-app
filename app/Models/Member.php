<?php

namespace App\Models;

use App\Enums\GenderEnum;
use App\Enums\MembershipStatusEnum;
use App\Models\Concerns\HasUuidV7;
use Database\Factories\MemberFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    /** @use HasFactory<MemberFactory> */
    use HasFactory, HasUuidV7;

    protected $fillable = [
        'user_id', 'legacy_user_id', 'name', 'first_name', 'last_name',
        'birth_date', 'gender', 'has_free_membership',
    ];

    protected function casts(): array
    {
        return ['birth_date' => 'date', 'gender' => GenderEnum::class, 'has_free_membership' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::saving(function (Member $member): void {
            if ($member->isDirty(['first_name', 'last_name'])) {
                $name = trim(($member->first_name ?? '').' '.($member->last_name ?? ''));
                if ($name !== '') {
                    $member->name = $name;
                }
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)->using(MemberTeam::class)
            ->withPivot('is_active', 'joined_at', 'continuous_membership')->withTimestamps();
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    public function trainingRegistrations(): HasMany
    {
        return $this->hasMany(TrainingRegistration::class);
    }

    public function waitlistEntries(): HasMany
    {
        return $this->hasMany(TrainingWaitlist::class);
    }

    public function participatesInMembershipBilling(?Team $team = null): bool
    {
        return $team !== null && $this->teams()->whereKey($team->id)->exists();
    }

    public function isMembershipPayer(?Team $team = null): bool
    {
        return $this->participatesInMembershipBilling($team) && ! $this->has_free_membership;
    }

    public function hasActiveMembershipForTeam(string $teamId, ?TeamSeason $season = null): bool
    {
        return $this->memberships()->where('team_id', $teamId)->where('status', MembershipStatusEnum::ACTIVE)
            ->when($season?->isFuture(), fn ($query) => $query->where('team_season_id', $season->id),
                fn ($query) => $query->where('starts_at', '<=', now())->where('ends_at', '>=', today()))
            ->exists();
    }

    public function getAge(): ?int
    {
        return $this->birth_date ? (int) $this->birth_date->diffInYears(now(), absolute: true) : null;
    }

    public function enroll(Team $team, bool $continuous = true): void
    {
        $existing = $this->teams()->whereKey($team->id)->first();
        $this->teams()->syncWithoutDetaching([$team->id => [
            'is_active' => true, 'joined_at' => $existing?->pivot->joined_at ?? today(),
            'continuous_membership' => $continuous,
        ]]);
    }
}
