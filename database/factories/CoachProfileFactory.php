<?php

namespace Database\Factories;

use App\Models\CoachProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CoachProfile>
 */
class CoachProfileFactory extends Factory
{
    protected $model = CoachProfile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'date_started_coaching' => fake()->dateTimeBetween('-8 years', '-1 year'),
            'biography' => ['sk' => fake()->paragraphs(2, true), 'en' => fake()->paragraphs(2, true)],
        ];
    }
}
