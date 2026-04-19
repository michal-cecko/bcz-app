<?php

namespace Database\Factories;

use App\Models\Training;
use App\Models\TrainingSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TrainingSchedule>
 */
class TrainingScheduleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'training_id' => Training::factory(),
            'day' => fake()->randomElement(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']),
            'start_time' => fake()->time('H:i'),
            'sort_order' => 0,
        ];
    }
}
