<?php

namespace App\Services;

use App\Enums\RegistrationStatusEnum;
use App\Models\Training;
use App\Notifications\TrainingSpotAvailable;
use Illuminate\Support\Facades\Notification;

class TrainingCapacityService
{
    public static function isFull(Training $training): bool
    {
        if ($training->max_capacity === null) {
            return false;
        }

        return $training->registrations()
            ->where('status', RegistrationStatusEnum::Approved->value)
            ->count() >= $training->max_capacity;
    }

    public static function handleSpotFreed(Training $training): void
    {
        if (! $training->notify_on_available) {
            return;
        }

        if (self::isFull($training)) {
            return;
        }

        self::notifyWaitlist($training);
    }

    public static function notifyWaitlist(Training $training): void
    {
        $waitlistEntries = $training->waitlistEntries()->with('user')->get();

        if ($waitlistEntries->isEmpty()) {
            return;
        }

        $users = $waitlistEntries->pluck('user')->filter();

        Notification::send($users, new TrainingSpotAvailable($training));

        $training->waitlistEntries()->delete();
    }
}
