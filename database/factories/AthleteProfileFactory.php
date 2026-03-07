<?php

namespace Database\Factories;

use App\Models\AthleteProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AthleteProfile>
 */
class AthleteProfileFactory extends Factory
{
    protected $model = AthleteProfile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'date_started_working_out' => fake()->dateTimeBetween('-10 years', '-1 year'),
            'journey_text' => ['sk' => fake()->paragraphs(2, true), 'en' => fake()->paragraphs(2, true)],
        ];
    }
}
