<?php

namespace App\Observers;

use App\Models\Training;
use App\Services\TrainingCapacityService;

class TrainingObserver
{
    public function updating(Training $training): void
    {
        if (! $training->isDirty('max_capacity')) {
            return;
        }

        $oldCapacity = $training->getOriginal('max_capacity');
        $newCapacity = $training->max_capacity;

        if ($newCapacity === null || ($oldCapacity !== null && $newCapacity > $oldCapacity)) {
            // Capacity increased or set to unlimited — check waitlist after save
            $training->shouldCheckWaitlist = true;
        }
    }

    public function updated(Training $training): void
    {
        if (! empty($training->shouldCheckWaitlist)) {
            TrainingCapacityService::handleSpotFreed($training);
            unset($training->shouldCheckWaitlist);
        }
    }
}
