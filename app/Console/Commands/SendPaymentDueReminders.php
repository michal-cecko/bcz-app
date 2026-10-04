<?php

namespace App\Console\Commands;

use App\Contracts\Payable;
use App\Enums\MembershipStatusEnum;
use App\Enums\RegistrationStatusEnum;
use App\Enums\TrainingPricingTypeEnum;
use App\Models\EventRegistration;
use App\Models\Membership;
use App\Models\TrainingRegistration;
use App\Notifications\EventRegistrationPaymentDue;
use App\Notifications\MembershipPaymentDue;
use App\Notifications\TrainingRegistrationPaymentDue;
use App\Services\PaymentService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SendPaymentDueReminders extends Command
{
    protected $signature = 'payments:send-due-reminders {--days-before=3 : Send reminder when deadline is within this many days}';

    protected $description = 'Send payment-due reminder emails for unpaid or partly paid memberships, training registrations, and event registrations';

    private PaymentService $payments;

    public function handle(PaymentService $payments): int
    {
        $this->payments = $payments;
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
            ->with('user')
            ->get();

        $sent = 0;

        foreach ($memberships as $membership) {
            if (! $membership->user || $this->isPaidInFull($membership)) {
                continue;
            }

            if (! $membership->user->isMembershipPayer($membership->team)) {
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
        // Membership-required trainings owe the membership fee, which has its
        // own reminder; the registration itself has no price to ask for.
        $registrations = TrainingRegistration::query()
            ->where('status', RegistrationStatusEnum::Pending)
            ->whereHas('training', fn ($query) => $query->where('pricing_type', TrainingPricingTypeEnum::PAID))
            ->whereNotNull('payment_due_at')
            ->where('payment_due_at', '<=', $threshold)
            ->whereNull('payment_reminder_sent_at')
            ->with(['user', 'training'])
            ->get();

        $sent = 0;

        foreach ($registrations as $registration) {
            if (! $registration->user || $this->isPaidInFull($registration)) {
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
            ->with(['user', 'event.organization', 'registrationFee'])
            ->get();

        $sent = 0;

        foreach ($registrations as $registration) {
            if (! $registration->user || $this->isPaidInFull($registration)) {
                continue;
            }

            $registration->user->notify(new EventRegistrationPaymentDue($registration));
            $registration->update(['payment_reminder_sent_at' => now()]);
            $sent++;
        }

        return $sent;
    }

    /**
     * Partly paid payables are reminded about the rest. One with no price keeps
     * the old behaviour, since isFullyPaid() treats a zero price as paid.
     */
    private function isPaidInFull(Payable $payable): bool
    {
        return $payable->getTotalPriceAmount() > 0 && $this->payments->isFullyPaid($payable);
    }
}
