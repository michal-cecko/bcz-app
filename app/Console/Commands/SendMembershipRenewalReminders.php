<?php

namespace App\Console\Commands;

use App\Enums\MembershipStatusEnum;
use App\Enums\RoleEnum;
use App\Models\Membership;
use App\Models\TeamSeason;
use App\Models\User;
use App\Notifications\MembershipRenewalReminder;
use Illuminate\Console\Command;

class SendMembershipRenewalReminders extends Command
{
    protected $signature = 'memberships:send-renewal-reminders';

    protected $description = 'Send renewal reminders to team members for upcoming seasons starting within 2 weeks';

    public function handle(): int
    {
        $seasons = TeamSeason::query()
            ->whereNull('renewal_notified_at')
            ->where('starts_at', '>', now())
            ->where('starts_at', '<=', now()->addWeeks(2))
            ->with('team')
            ->get();

        if ($seasons->isEmpty()) {
            $this->info('No upcoming seasons requiring renewal reminders.');

            return self::SUCCESS;
        }

        $totalNotified = 0;

        foreach ($seasons as $season) {
            $team = $season->team;

            // Get all ATHLETE members of this team
            $memberUserIds = $team->members()
                ->wherePivot('role', RoleEnum::ATHLETE->value)
                ->wherePivot('is_active', true)
                ->pluck('users.id');

            if ($memberUserIds->isEmpty()) {
                $season->update(['renewal_notified_at' => now()]);

                continue;
            }

            // Find members who do NOT have an active membership for this season
            $membersWithActiveMembership = Membership::where('team_id', $team->id)
                ->where('team_season_id', $season->id)
                ->whereIn('status', [MembershipStatusEnum::ACTIVE, MembershipStatusEnum::PENDING])
                ->pluck('user_id');

            $membersToNotify = User::whereIn('id', $memberUserIds)
                ->whereNotIn('id', $membersWithActiveMembership)
                ->get();

            $paymentUrl = url('/admin/'.$team->slug.'/member-membership');

            foreach ($membersToNotify as $user) {
                $user->notify(new MembershipRenewalReminder($season, $paymentUrl));
                $totalNotified++;
            }

            $season->update(['renewal_notified_at' => now()]);

            $this->info("Season '{$season->name}' ({$team->getTranslation('name', 'sk')}): notified {$membersToNotify->count()} member(s).");
        }

        $this->info("Total: {$totalNotified} renewal reminder(s) sent.");

        return self::SUCCESS;
    }
}
