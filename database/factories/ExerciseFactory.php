<?php

namespace Database\Factories;

use App\Enums\ComplexityLevelEnum;
use App\Models\Exercise;
use App\Models\ExerciseCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Exercise>
 */
class ExerciseFactory extends Factory
{
    protected $model = Exercise::class;

    public function definition(): array
    {
        return [
            'name' => ['sk' => fake()->words(3, true), 'en' => fake()->words(3, true)],
            'description' => ['sk' => fake()->sentence(), 'en' => fake()->sentence()],
            'complexity' => fake()->randomElement(ComplexityLevelEnum::cases()),
            'exercise_category_id' => ExerciseCategory::factory(),
        ];
    }
}
