<?php

namespace App\Console\Commands;

use App\Enums\MembershipStatusEnum;
use App\Models\Membership;
use Illuminate\Console\Command;

class CancelUnpaidMembershipsCommand extends Command
{
    protected $signature = 'memberships:cancel-unpaid';

    protected $description = 'Cancel pending memberships past their payment deadline';

    public function handle(): int
    {
        $count = Membership::where('status', MembershipStatusEnum::PENDING)
            ->where('is_free', false)
            ->whereNotNull('payment_deadline_at')
            ->where('payment_deadline_at', '<', now())
            ->update(['status' => MembershipStatusEnum::CANCELLED]);

        $this->info("Cancelled {$count} unpaid membership(s).");

        return self::SUCCESS;
    }
}
