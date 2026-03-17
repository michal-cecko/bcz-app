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
        if (! $training->age_group) {
            return true;
        }

        $age = $user->getAge();
        if ($age === null) {
            return true;
        }

        $ranges = array_map('trim', explode(',', $training->age_group));

        foreach ($ranges as $range) {
            if (str_ends_with($range, '+')) {
                $min = (int) rtrim($range, '+');
                if ($age >= $min) {
                    return true;
                }
            } elseif (str_contains($range, '-')) {
                [$min, $max] = explode('-', $range, 2);
                if ($age >= (int) $min && $age <= (int) $max) {
                    return true;
                }
            } else {
                if ($age === (int) $range) {
                    return true;
                }
            }
        }

        return false;
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
