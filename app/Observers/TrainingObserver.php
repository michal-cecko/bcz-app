<?php

namespace App\Observers;

use App\Models\Training;
use App\Services\TrainingCapacityService;

class TrainingObserver
{
    /**
     * In "updated" the original still holds the value from before the save.
     */
    public function updated(Training $training): void
    {
        if (! $training->wasChanged('max_capacity')) {
            return;
        }

        $oldCapacity = $training->getOriginal('max_capacity');
        $newCapacity = $training->max_capacity;

        if ($newCapacity === null || ($oldCapacity !== null && $newCapacity > $oldCapacity)) {
            TrainingCapacityService::handleSpotFreed($training);
        }
    }
}
