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
            'color' => fake()->randomElement(['#6366f1', '#3b82f6', '#22c55e', '#f59e0b', '#ef4444', '#6b7280', '#ec4899', '#f97316', '#14b8a6']),
            'icon' => fake()->randomElement(['heroicon-o-question-mark-circle', 'heroicon-o-academic-cap', 'heroicon-o-trophy', 'heroicon-o-calendar']),
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
