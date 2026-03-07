<?php

namespace Database\Factories;

use App\Enums\GoalStatusEnum;
use App\Models\AthleteGoal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AthleteGoal>
 */
class AthleteGoalFactory extends Factory
{
    protected $model = AthleteGoal::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'icon' => fake()->randomElement(['🎯', '💪', '🏆', '⭐']),
            'heading' => ['sk' => fake()->words(3, true), 'en' => fake()->words(3, true)],
            'description' => ['sk' => fake()->sentence(), 'en' => fake()->sentence()],
            'status' => fake()->randomElement(GoalStatusEnum::cases()),
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
