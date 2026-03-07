<?php

namespace Database\Factories;

use App\Models\SportCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SportCategory>
 */
class SportCategoryFactory extends Factory
{
    protected $model = SportCategory::class;

    public function definition(): array
    {
        return [
            'name' => ['sk' => fake()->words(2, true), 'en' => fake()->words(2, true)],
            'description' => ['sk' => fake()->sentence(), 'en' => fake()->sentence()],
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
