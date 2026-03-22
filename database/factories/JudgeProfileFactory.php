<?php

namespace Database\Factories;

use App\Models\JudgeProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JudgeProfile>
 */
class JudgeProfileFactory extends Factory
{
    protected $model = JudgeProfile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'date_started_judging' => fake()->dateTimeBetween('-8 years', '-1 year'),
            'biography' => ['sk' => fake()->paragraphs(2, true), 'en' => fake()->paragraphs(2, true)],
            'disciplines' => [fake()->randomElement(['freestyle', 'speed', 'endurance', 'strength'])],
        ];
    }
}
