<?php

namespace App\Console\Commands;

use App\Enums\PaymentStatusEnum;
use App\Enums\RegistrationStatusEnum;
use App\Models\TrainingRegistration;
use App\Services\PaymentService;
use App\Services\TrainingCapacityService;
use Illuminate\Console\Command;

class CancelExpiredRegistrations extends Command
{
    protected $signature = 'registrations:cancel-expired';

    protected $description = 'Cancel unpaid and partly paid training registrations that have passed their payment due date';

    public function handle(PaymentService $payments): int
    {
        $expiredRegistrations = TrainingRegistration::query()
            ->where('status', RegistrationStatusEnum::Pending)
            ->whereNotNull('payment_due_at')
            ->where('payment_due_at', '<', now())
            ->with('training')
            ->get()
            // A partly paid registration is cancelled too; one already paid in full only missed its approval.
            ->reject(fn (TrainingRegistration $registration): bool => $registration->getTotalPriceAmount() > 0
                && $payments->isFullyPaid($registration));

        if ($expiredRegistrations->isEmpty()) {
            $this->info('No expired registrations found.');

            return self::SUCCESS;
        }

        $count = 0;
        $trainingsToCheck = collect();

        foreach ($expiredRegistrations as $registration) {
            $registration->update([
                'status' => RegistrationStatusEnum::Cancelled,
                'cancellation_reason' => 'Automaticky zrušená — platba nebola prijatá v stanovenej lehote.',
            ]);
            $registration->payments()
                ->where('status', PaymentStatusEnum::PENDING)
                ->update(['status' => PaymentStatusEnum::CANCELLED->value]);

            // The training may have been soft-deleted after the registration was
            // created, in which case the relation resolves to null. Skip it rather
            // than passing null into a strictly-typed Training parameter below.
            if ($registration->training) {
                $trainingsToCheck->push($registration->training);
            }

            $count++;
        }

        // Notify waitlisted users for freed spots
        $trainingsToCheck->unique('id')->each(function ($training) {
            TrainingCapacityService::handleSpotFreed($training);
        });

        $this->info("Cancelled {$count} expired registration(s).");

        return self::SUCCESS;
    }
}
