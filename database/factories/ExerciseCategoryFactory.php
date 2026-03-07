<?php

namespace Database\Factories;

use App\Models\ExerciseCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExerciseCategory>
 */
class ExerciseCategoryFactory extends Factory
{
    protected $model = ExerciseCategory::class;

    public function definition(): array
    {
        return [
            'name' => ['sk' => fake()->words(2, true), 'en' => fake()->words(2, true)],
            'description' => ['sk' => fake()->sentence(), 'en' => fake()->sentence()],
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
