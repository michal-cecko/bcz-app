<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Member;
use App\Models\User;

class MemberPolicy
{
    public function view(User $user, Member $member): bool
    {
        return $member->user_id === $user->id;
    }

    public function create(User $user, ?User $owner = null): bool
    {
        $owner ??= $user;

        return config('family.enabled') && $owner->is_parent
            && ($owner->id === $user->id
                ? $user->hasRole(RoleEnum::CUSTOMER)
                : $user->isGlobalAdmin() && $user->can('update', $owner));
    }

    public function update(User $user, Member $member): bool
    {
        return config('family.enabled') && $this->view($user, $member);
    }

    public function delete(User $user, Member $member): bool
    {
        return $member->user !== null && $this->create($user, $member->user);
    }
}
