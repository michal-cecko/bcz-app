<?php

namespace App\Services;

use App\Models\Training;
use App\Notifications\TrainingSpotAvailable;

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
        $waitlistEntries = $training->waitlistEntries()->with(['user', 'member'])->get();

        if ($waitlistEntries->isEmpty()) {
            return;
        }

        foreach ($waitlistEntries as $entry) {
            $entry->user?->notify(new TrainingSpotAvailable($training, $entry->member));
        }

        $training->waitlistEntries()->delete();
    }
}
