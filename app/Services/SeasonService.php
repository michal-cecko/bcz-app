<?php

namespace App\Services;

use App\Enums\MembershipStatusEnum;
use App\Models\Member;
use App\Models\Membership;
use App\Models\Team;
use App\Models\TeamSeason;
use App\Models\User;
use App\Notifications\MembershipPaymentDue;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SeasonService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function createSeasonWithMemberships(Team $team, array $data): TeamSeason
    {
        return DB::transaction(function () use ($team, $data) {
            $season = $team->seasons()->create($data);

            // members() returns one row per team role, so a coach who also trains
            // as an athlete would otherwise be billed twice.
            $activeMembers = (config('family.enabled') ? $team->enrolledMembers() : $team->members())
                ->wherePivot('is_active', true)
                ->get()
                ->unique('id');

            foreach ($activeMembers as $member) {
                // Non-billable roles (admins, editors, judges) skip billing entirely.
                if (! $member->participatesInMembershipBilling($team)) {
                    continue;
                }

                $isFree = ! $member->isMembershipPayer($team);
                $joinedAt = $member->pivot->joined_at;
                $feeAmount = $isFree ? 0.0 : $season->proratedFee($joinedAt ? Carbon::parse($joinedAt) : now());

                $membership = Membership::create([
                    'team_id' => $team->id,
                    'user_id' => $member instanceof Member ? $member->user_id : $member->id,
                    'member_id' => $member instanceof Member ? $member->id : null,
                    'team_season_id' => $season->id,
                    'status' => $isFree ? MembershipStatusEnum::ACTIVE : MembershipStatusEnum::PENDING,
                    'fee_amount' => $feeAmount,
                    'fee_currency' => $season->fee_currency,
                    'is_free' => $isFree,
                    'payment_deadline_at' => $isFree ? null : now()->addDays($season->payment_deadline_days),
                    'starts_at' => $season->starts_at,
                    'ends_at' => $season->ends_at,
                ]);

                if (! $isFree) {
                    ($member instanceof Member ? $member->user : $member)->notify((new MembershipPaymentDue($membership))->afterCommit());
                }
            }

            return $season;
        });
    }

    /**
     * The user's membership for this season, created on first use with the same
     * billing rules as {@see self::createSeasonWithMemberships()}: users outside
     * membership billing get none, users with a free membership get an active
     * free one, everyone else a pending one at the season fee for the months
     * left, counting the current month in full.
     */
    public function findOrCreateMembership(TeamSeason $season, User|Member $user): ?Membership
    {
        if ($user instanceof User && config('family.enabled')) {
            if (! $user->participatesInMembershipBilling($season->team)) {
                return null;
            }
            $user = app(MemberProfileService::class)->resolveMember($user);
            if (! $user) {
                return null;
            }
            if (! $user->participatesInMembershipBilling($season->team)) {
                $user->enroll($season->team);
            }
        }

        return DB::transaction(function () use ($season, $user): ?Membership {
            $user->newQuery()->whereKey($user->id)->lockForUpdate()->firstOrFail();
            $membership = Membership::query()->where('team_id', $season->team_id)
                ->where($user instanceof Member ? 'member_id' : 'user_id', $user->id)
                ->where('team_season_id', $season->id)->orderByRaw("CASE WHEN status IN ('active', 'pending') THEN 0 ELSE 1 END")->orderByDesc('created_at')->first();
            if ($membership || ! $user->participatesInMembershipBilling($season->team)) {
                return $membership;
            }
            $isFree = ! $user->isMembershipPayer($season->team);

            return Membership::create([
                'team_id' => $season->team_id, 'user_id' => $user instanceof Member ? $user->user_id : $user->id,
                'member_id' => $user instanceof Member ? $user->id : null, 'team_season_id' => $season->id,
                'status' => $isFree ? MembershipStatusEnum::ACTIVE : MembershipStatusEnum::PENDING,
                'fee_amount' => $isFree ? 0.0 : $season->proratedFee(), 'fee_currency' => $season->fee_currency ?? 'EUR',
                'is_free' => $isFree, 'payment_deadline_at' => $isFree ? null : now()->addDays($season->payment_deadline_days ?? 14),
                'starts_at' => $season->starts_at, 'ends_at' => $season->ends_at,
            ]);
        });
    }

    public function addMidSeasonMember(TeamSeason $season, User|Member $user, ?Carbon $joinDate = null): Membership
    {
        $joinDate = $joinDate ?? now();
        $proratedFee = $season->proratedFee($joinDate);

        return Membership::create([
            'team_id' => $season->team_id,
            'user_id' => $user instanceof Member ? $user->user_id : $user->id,
            'member_id' => $user instanceof Member ? $user->id : null,
            'team_season_id' => $season->id,
            'status' => MembershipStatusEnum::PENDING,
            'fee_amount' => $proratedFee,
            'fee_currency' => $season->fee_currency,
            'is_free' => false,
            'payment_deadline_at' => now()->addDays($season->payment_deadline_days),
            'starts_at' => $joinDate->toDateString(),
            'ends_at' => $season->ends_at,
        ]);
    }

    public function markMembershipFree(Membership $membership): void
    {
        $this->saveMembership($membership, [
            'is_free' => true,
            'fee_amount' => 0,
            'status' => MembershipStatusEnum::ACTIVE,
            'payment_deadline_at' => null,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function saveMembership(Membership $membership, array $data, string $statusField = 'status'): Membership
    {
        return DB::transaction(function () use ($membership, $data, $statusField): Membership {
            $memberId = $data['member_id'] ?? $membership->member_id;
            if ($memberId) {
                Member::whereKey($memberId)->lockForUpdate()->firstOrFail();
            }
            if ($membership->exists) {
                $membership->refresh();
            }
            $membership->fill($data);
            $membership->unsetRelation('season');

            if ($membership->member_id && in_array($membership->status, [MembershipStatusEnum::ACTIVE, MembershipStatusEnum::PENDING], true)) {
                $current = Membership::forSameMemberAndSeason($membership)
                    ->whereIn('status', [MembershipStatusEnum::ACTIVE, MembershipStatusEnum::PENDING]);
                if ($membership->exists) {
                    $current->whereKeyNot($membership->id);
                }
                if ($current->exists()) {
                    throw ValidationException::withMessages([$statusField => __('family.membership_exists')]);
                }
            }
            $membership->save();

            return $membership;
        }, 3);
    }

    public function renewMembership(Membership $cancelledMembership): Membership
    {
        return DB::transaction(function () use ($cancelledMembership): Membership {
            $participant = $cancelledMembership->member ?? $cancelledMembership->user;
            $participant->newQuery()->whereKey($participant->id)->lockForUpdate()->firstOrFail();
            $cancelledMembership = Membership::whereKey($cancelledMembership->id)->lockForUpdate()->firstOrFail();
            $current = Membership::forSameMemberAndSeason($cancelledMembership)
                ->whereIn('status', [MembershipStatusEnum::ACTIVE, MembershipStatusEnum::PENDING])->first();
            if ($current) {
                return $current;
            }
            $season = $cancelledMembership->season;

            return Membership::create([
                'team_id' => $cancelledMembership->team_id,
                'user_id' => $cancelledMembership->user_id,
                'member_id' => $cancelledMembership->member_id,
                'team_season_id' => $season?->id,
                'status' => MembershipStatusEnum::PENDING,
                // Reopening the same season keeps the fee originally issued.
                'fee_amount' => $cancelledMembership->fee_amount,
                'fee_currency' => $cancelledMembership->fee_currency,
                'is_free' => false,
                'payment_deadline_at' => now()->addDays($season?->payment_deadline_days ?? 14),
                'starts_at' => now(),
                'ends_at' => $season?->ends_at ?? $cancelledMembership->ends_at,
            ]);
        });
    }
}
