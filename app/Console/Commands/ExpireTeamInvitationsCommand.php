<?php

namespace App\Console\Commands;

use App\Enums\InvitationStatusEnum;
use App\Models\TeamInvitation;
use Illuminate\Console\Command;

class ExpireTeamInvitationsCommand extends Command
{
    protected $signature = 'team-invitations:expire';

    protected $description = 'Mark pending team invitations past their expiry date as expired';

    public function handle(): int
    {
        $count = TeamInvitation::where('status', InvitationStatusEnum::Pending)
            ->where('expires_at', '<', now())
            ->update(['status' => InvitationStatusEnum::Expired]);

        $this->info("Expired {$count} invitation(s).");

        return self::SUCCESS;
    }
}
