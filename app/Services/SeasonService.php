<?php

namespace App\Services;

use App\Enums\MembershipStatusEnum;
use App\Models\Membership;
use App\Models\Team;
use App\Models\TeamSeason;
use App\Models\User;
use App\Notifications\MembershipPaymentDue;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
            $activeMembers = $team->members()
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
                    'user_id' => $member->id,
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
                    $member->notify(new MembershipPaymentDue($membership));
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
    public function findOrCreateMembership(TeamSeason $season, User $user): ?Membership
    {
        $membership = Membership::query()
            ->where('team_id', $season->team_id)
            ->where('user_id', $user->id)
            ->where('team_season_id', $season->id)
            ->first();

        if ($membership || ! $user->participatesInMembershipBilling($season->team)) {
            return $membership;
        }

        $isFree = ! $user->isMembershipPayer($season->team);

        return Membership::create([
            'team_id' => $season->team_id,
            'user_id' => $user->id,
            'team_season_id' => $season->id,
            'status' => $isFree ? MembershipStatusEnum::ACTIVE : MembershipStatusEnum::PENDING,
            'fee_amount' => $isFree ? 0.0 : $season->proratedFee(),
            'fee_currency' => $season->fee_currency ?? 'EUR',
            'is_free' => $isFree,
            'payment_deadline_at' => $isFree ? null : now()->addDays($season->payment_deadline_days ?? 14),
            'starts_at' => $season->starts_at,
            'ends_at' => $season->ends_at,
        ]);
    }

    public function addMidSeasonMember(TeamSeason $season, User $user, ?Carbon $joinDate = null): Membership
    {
        $joinDate = $joinDate ?? now();
        $proratedFee = $season->proratedFee($joinDate);

        return Membership::create([
            'team_id' => $season->team_id,
            'user_id' => $user->id,
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
        $membership->update([
            'is_free' => true,
            'fee_amount' => 0,
            'status' => MembershipStatusEnum::ACTIVE,
            'payment_deadline_at' => null,
        ]);
    }

    public function renewMembership(Membership $cancelledMembership): Membership
    {
        $season = $cancelledMembership->season;

        return Membership::create([
            'team_id' => $cancelledMembership->team_id,
            'user_id' => $cancelledMembership->user_id,
            'team_season_id' => $season?->id,
            'status' => MembershipStatusEnum::PENDING,
            // The member re-opens the same season, so they owe what it was issued at.
            'fee_amount' => $cancelledMembership->fee_amount,
            'fee_currency' => $cancelledMembership->fee_currency,
            'is_free' => false,
            'payment_deadline_at' => now()->addDays($season?->payment_deadline_days ?? 14),
            'starts_at' => now(),
            'ends_at' => $season?->ends_at ?? $cancelledMembership->ends_at,
        ]);
    }
}
