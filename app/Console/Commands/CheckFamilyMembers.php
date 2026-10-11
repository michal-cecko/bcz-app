<?php

namespace App\Console\Commands;

use App\Models\Membership;
use App\Models\TeamJoinRequest;
use App\Models\TrainingRegistration;
use App\Models\TrainingWaitlist;
use Illuminate\Console\Command;

class CheckFamilyMembers extends Command
{
    protected $signature = 'members:check';

    protected $description = 'Check member mapping and team enrolment before enabling family memberships';

    public function handle(): int
    {
        $checks = [
            'Unmapped current memberships' => Membership::whereIn('status', ['active', 'pending'])->whereNull('member_id')->count(),
            'Unmapped current account registrations' => TrainingRegistration::whereNotNull('user_id')->whereNull('member_id')
                ->where('status', '!=', 'cancelled')->whereHas('training', fn ($query) => $query->current())->count(),
            'Unmapped waitlist entries' => TrainingWaitlist::whereNull('member_id')->count(),
            'Unmapped account join requests' => TeamJoinRequest::whereNotNull('user_id')->whereNull('member_id')->where('status', 'pending')->count(),
            'Membership contact mismatch' => Membership::whereNotNull('member_id')->whereHas('member',
                fn ($query) => $query->whereColumn('members.user_id', '!=', 'memberships.user_id'))->count(),
            'Membership season team mismatch' => Membership::whereHas('season',
                fn ($query) => $query->whereColumn('team_seasons.team_id', '!=', 'memberships.team_id'))->count(),
            'Missing member enrolments' => Membership::whereNotNull('member_id')->whereDoesntHave('member.teams',
                fn ($query) => $query->whereColumn('teams.id', 'memberships.team_id'))->count(),
        ];
        foreach ($checks as $label => $count) {
            $this->line($label.': '.$count);
        }
        if (array_sum($checks) > 0) {
            $this->error('Keep family writes disabled until these records are reviewed.');

            return self::FAILURE;
        }
        $this->info('Member mapping checks passed. Keep old workers and registration writes paused during cutover.');

        return self::SUCCESS;
    }
}
