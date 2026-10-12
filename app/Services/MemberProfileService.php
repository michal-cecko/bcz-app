<?php

namespace App\Services;

use App\Models\Member;
use App\Models\TeamJoinRequest;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MemberProfileService
{
    public function resolveMember(User $user): ?Member
    {
        return DB::transaction(function () use ($user): ?Member {
            // Parents can access a team through a child without their own team enrolment.
            $owner = $user->newQueryWithoutScopes()->whereKey($user->id)->lockForUpdate()->firstOrFail();
            $members = $owner->members()->limit(2)->get();
            if ($members->count() === 1) {
                return $members->first();
            }
            if ($owner->is_parent || $members->isNotEmpty()) {
                return null;
            }

            return $owner->members()->create([
                'legacy_user_id' => $owner->id,
                'first_name' => $owner->first_name, 'last_name' => $owner->last_name, 'name' => $owner->name,
                'birth_date' => $owner->birth_date, 'gender' => $owner->gender,
                'has_free_membership' => (bool) $owner->has_free_membership,
            ]);
        });
    }

    /** @param array<string, mixed> $data */
    public function createMember(User $user, array $data): Member
    {
        return DB::transaction(function () use ($user, $data): Member {
            $owner = $user->newQueryWithoutScopes()->whereKey($user->id)->lockForUpdate()->firstOrFail();
            if (! $owner->is_parent && $owner->members()->exists()) {
                throw ValidationException::withMessages(['is_parent' => __('family.single_member_limit')]);
            }

            return $owner->members()->create(Arr::only($data, ['first_name', 'last_name', 'birth_date', 'gender']));
        });
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>|null  $participant
     */
    public function saveAccount(User $user, array $data, ?array $participant = null, bool $canManageExemption = false, string $parentField = 'is_parent'): User
    {
        return DB::transaction(function () use ($user, $data, $participant, $canManageExemption, $parentField): User {
            $owner = $user->newQueryWithoutScopes()->whereKey($user->id)->lockForUpdate()->firstOrFail();
            if (config('family.enabled')) {
                if (array_key_exists('is_parent', $data) && ! $data['is_parent'] && $owner->members()->count() > 1) {
                    throw ValidationException::withMessages([$parentField => __('family.keep_parent_mode')]);
                }
                if ($owner->usesMemberProfiles() || $participant !== null) {
                    $data = Arr::except($data, ['birth_date', 'gender']);
                }
                $data = Arr::except($data, ['has_free_membership']);
            } else {
                $data = Arr::except($data, ['is_parent']);
            }
            $owner->update($data);
            if (config('family.enabled') && ! $owner->is_parent && $participant !== null) {
                $member = $this->resolveMember($owner);
                if (! $member) {
                    throw ValidationException::withMessages([$parentField => __('family.keep_parent_mode')]);
                }
                $this->saveMember($member, $participant, $canManageExemption);
            }

            return $user->refresh();
        });
    }

    /** @param array<string, mixed> $data */
    public function saveMember(Member $member, array $data, bool $canManageExemption = false): void
    {
        $fields = ['first_name', 'last_name', 'birth_date', 'gender'];
        if ($canManageExemption) {
            $fields[] = 'has_free_membership';
        }
        $member->update(Arr::only($data, $fields));
    }

    public function deleteMember(Member $member): bool
    {
        return DB::transaction(function () use ($member): bool {
            $owner = User::withoutGlobalScopes()->whereKey($member->user_id)->lockForUpdate()->firstOrFail();
            abort_unless(config('family.enabled') && $owner->is_parent, 403);
            $profile = $owner->members()->whereKey($member->id)->lockForUpdate()->firstOrFail();

            if ($profile->teams()->withoutGlobalScopes()->exists()
                || $profile->memberships()->withoutGlobalScopes()->exists()
                || $profile->trainingRegistrations()->withoutGlobalScopes()->exists()
                || $profile->waitlistEntries()->withoutGlobalScopes()->exists()
                || TeamJoinRequest::withoutGlobalScopes()->where('member_id', $profile->id)->exists()) {
                return false;
            }

            return (bool) $profile->delete();
        });
    }
}
