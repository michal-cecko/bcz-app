<?php

namespace Database\Factories;

use App\Models\EventCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EventCategory>
 */
class EventCategoryFactory extends Factory
{
    protected $model = EventCategory::class;

    public function definition(): array
    {
        return [
            'title' => ['sk' => fake()->words(2, true), 'en' => fake()->words(2, true)],
            'color' => fake()->randomElement(['#FF6B35', '#2EC4B6', '#9B5DE5', '#FF6B6B', '#FF2D2D', '#3B82F6', '#22C55E', '#F59E0B', '#EC4899', '#6B7280', '#14B8A6', '#8B5CF6']),
            'card_subtitle' => ['sk' => fake()->sentence(), 'en' => fake()->sentence()],
            'card_description' => ['sk' => fake()->paragraph(), 'en' => fake()->paragraph()],
            'about_title' => ['sk' => fake()->words(3, true), 'en' => fake()->words(3, true)],
            'about_description' => ['sk' => fake()->paragraph(), 'en' => fake()->paragraph()],
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
