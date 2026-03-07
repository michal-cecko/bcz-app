<?php

namespace Database\Factories;

use App\Models\AthleteExercise;
use App\Models\Exercise;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AthleteExercise>
 */
class AthleteExerciseFactory extends Factory
{
    protected $model = AthleteExercise::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'exercise_id' => Exercise::factory(),
            'duration' => fake()->randomElement(['10s', '30s', '1min', '5min']),
            'description' => ['sk' => fake()->sentence(), 'en' => fake()->sentence()],
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
