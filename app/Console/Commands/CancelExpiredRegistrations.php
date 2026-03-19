<?php

namespace App\Console\Commands;

use App\Enums\PaymentStatusEnum;
use App\Enums\RegistrationStatusEnum;
use App\Models\TrainingRegistration;
use App\Services\TrainingCapacityService;
use Illuminate\Console\Command;

class CancelExpiredRegistrations extends Command
{
    protected $signature = 'registrations:cancel-expired';

    protected $description = 'Cancel unpaid training registrations that have passed their payment due date';

    public function handle(): int
    {
        $expiredRegistrations = TrainingRegistration::query()
            ->where('status', RegistrationStatusEnum::Pending)
            ->whereNotNull('payment_due_at')
            ->where('payment_due_at', '<', now())
            ->whereDoesntHave('payments', fn ($q) => $q->where('status', PaymentStatusEnum::COMPLETED))
            ->with('training')
            ->get();

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

            $trainingsToCheck->push($registration->training);
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
