<?php

namespace App\Console\Commands;

use App\Enums\MembershipStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\RegistrationStatusEnum;
use App\Models\EventRegistration;
use App\Models\Membership;
use App\Models\TrainingRegistration;
use App\Notifications\EventRegistrationPaymentDue;
use App\Notifications\MembershipPaymentDue;
use App\Notifications\TrainingRegistrationPaymentDue;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SendPaymentDueReminders extends Command
{
    protected $signature = 'payments:send-due-reminders {--days-before=3 : Send reminder when deadline is within this many days}';

    protected $description = 'Send payment-due reminder emails for unpaid memberships, training registrations, and event registrations';

    public function handle(): int
    {
        $daysBefore = (int) $this->option('days-before');
        $threshold = now()->addDays($daysBefore);

        $membershipCount = $this->remindMemberships($threshold);
        $trainingCount = $this->remindTrainingRegistrations($threshold);
        $eventCount = $this->remindEventRegistrations($threshold);

        $total = $membershipCount + $trainingCount + $eventCount;

        $this->info("Sent {$total} payment-due reminder(s): {$membershipCount} membership, {$trainingCount} training, {$eventCount} event.");

        return self::SUCCESS;
    }

    private function remindMemberships(Carbon $threshold): int
    {
        $memberships = Membership::query()
            ->where('status', MembershipStatusEnum::PENDING)
            ->where('is_free', false)
            ->whereNotNull('payment_deadline_at')
            ->where('payment_deadline_at', '<=', $threshold)
            ->whereNull('payment_reminder_sent_at')
            ->whereDoesntHave('payments', fn ($q) => $q->where('status', PaymentStatusEnum::COMPLETED))
            ->with('user')
            ->get();

        $sent = 0;

        foreach ($memberships as $membership) {
            if (! $membership->user) {
                continue;
            }

            $membership->user->notify(new MembershipPaymentDue($membership));
            $membership->update(['payment_reminder_sent_at' => now()]);
            $sent++;
        }

        return $sent;
    }

    private function remindTrainingRegistrations(Carbon $threshold): int
    {
        $registrations = TrainingRegistration::query()
            ->where('status', RegistrationStatusEnum::Pending)
            ->whereNotNull('payment_due_at')
            ->where('payment_due_at', '<=', $threshold)
            ->whereNull('payment_reminder_sent_at')
            ->whereDoesntHave('payments', fn ($q) => $q->where('status', PaymentStatusEnum::COMPLETED))
            ->with(['user', 'training'])
            ->get();

        $sent = 0;

        foreach ($registrations as $registration) {
            if (! $registration->user) {
                continue;
            }

            $registration->user->notify(new TrainingRegistrationPaymentDue($registration));
            $registration->update(['payment_reminder_sent_at' => now()]);
            $sent++;
        }

        return $sent;
    }

    private function remindEventRegistrations(Carbon $threshold): int
    {
        $registrations = EventRegistration::query()
            ->where('status', RegistrationStatusEnum::Pending)
            ->whereNotNull('payment_due_at')
            ->where('payment_due_at', '<=', $threshold)
            ->whereNull('payment_reminder_sent_at')
            ->whereDoesntHave('payments', fn ($q) => $q->where('status', PaymentStatusEnum::COMPLETED))
            ->with(['user', 'event.organization', 'registrationFee'])
            ->get();

        $sent = 0;

        foreach ($registrations as $registration) {
            if (! $registration->user) {
                continue;
            }

            $registration->user->notify(new EventRegistrationPaymentDue($registration));
            $registration->update(['payment_reminder_sent_at' => now()]);
            $sent++;
        }

        return $sent;
    }
}
