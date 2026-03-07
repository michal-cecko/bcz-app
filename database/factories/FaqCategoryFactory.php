<?php

namespace Database\Factories;

use App\Models\FaqCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FaqCategory>
 */
class FaqCategoryFactory extends Factory
{
    protected $model = FaqCategory::class;

    public function definition(): array
    {
        return [
            'title' => ['sk' => fake()->words(2, true), 'en' => fake()->words(2, true)],
            'color' => fake()->hexColor(),
            'icon' => fake()->randomElement(['heroicon-o-question-mark-circle', 'heroicon-o-academic-cap', 'heroicon-o-trophy', 'heroicon-o-calendar']),
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
