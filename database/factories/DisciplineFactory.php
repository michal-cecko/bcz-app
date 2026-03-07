<?php

namespace Database\Factories;

use App\Enums\ScoringFormatEnum;
use App\Models\Discipline;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Discipline> */
class DisciplineFactory extends Factory
{
    protected $model = Discipline::class;

    public function definition(): array
    {
        return [
            'name' => ['sk' => fake()->word(), 'en' => fake()->word()],
            'description' => ['sk' => fake()->sentence(), 'en' => fake()->sentence()],
            'scoring_format' => fake()->randomElement(ScoringFormatEnum::cases()),
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
