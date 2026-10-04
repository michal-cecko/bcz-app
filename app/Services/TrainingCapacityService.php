<?php

namespace App\Services;

use App\Models\Training;
use App\Notifications\TrainingSpotAvailable;
use Illuminate\Support\Facades\Notification;

class TrainingCapacityService
{
    public static function handleSpotFreed(Training $training): void
    {
        if (! $training->notify_on_available) {
            return;
        }

        if ($training->isFull()) {
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
