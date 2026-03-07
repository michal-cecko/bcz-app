<?php

namespace Database\Factories;

use App\Models\AthleteCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<AthleteCategory> */
class AthleteCategoryFactory extends Factory
{
    protected $model = AthleteCategory::class;

    public function definition(): array
    {
        return [
            'name' => ['sk' => fake()->word(), 'en' => fake()->word()],
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
