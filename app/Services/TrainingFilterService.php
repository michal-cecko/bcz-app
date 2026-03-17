<?php

namespace App\Services;

use App\Models\Training;
use App\Models\User;

class TrainingFilterService
{
    public function matchesUserProfile(Training $training, User $user): bool
    {
        return $this->matchesAge($training, $user) && $this->matchesGender($training, $user);
    }

    public function matchesAge(Training $training, User $user): bool
    {
        if ($training->min_age === null && $training->max_age === null) {
            return true;
        }

        $age = $user->getAge();
        if ($age === null) {
            return true;
        }

        if ($training->min_age !== null && $age < $training->min_age) {
            return false;
        }

        if ($training->max_age !== null && $age > $training->max_age) {
            return false;
        }

        return true;
    }

    public function matchesGender(Training $training, User $user): bool
    {
        if (! $training->gender) {
            return true;
        }

        if (! $user->gender) {
            return true;
        }

        return $training->gender === $user->gender;
    }
}
