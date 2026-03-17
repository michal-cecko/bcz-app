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

            $activeMembers = $team->members()
                ->wherePivot('is_active', true)
                ->get();

            foreach ($activeMembers as $member) {
                $isFree = false;
                $feeAmount = (float) $season->fee_amount;

                $membership = Membership::create([
                    'team_id' => $team->id,
                    'user_id' => $member->id,
                    'team_season_id' => $season->id,
                    'status' => MembershipStatusEnum::PENDING,
                    'fee_amount' => $feeAmount,
                    'fee_currency' => $season->fee_currency,
                    'is_free' => $isFree,
                    'payment_deadline_at' => now()->addDays($season->payment_deadline_days),
                    'starts_at' => $season->starts_at,
                    'ends_at' => $season->ends_at,
                ]);

                $member->notify(new MembershipPaymentDue($membership));
            }

            return $season;
        });
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
            'fee_amount' => $season ? $season->proratedFee() : $cancelledMembership->fee_amount,
            'fee_currency' => $cancelledMembership->fee_currency,
            'is_free' => false,
            'payment_deadline_at' => now()->addDays($season?->payment_deadline_days ?? 14),
            'starts_at' => now(),
            'ends_at' => $season?->ends_at ?? $cancelledMembership->ends_at,
        ]);
    }
}
