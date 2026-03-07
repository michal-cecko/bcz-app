<?php

namespace Database\Factories;

use App\Models\Competition;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Competition> */
class CompetitionFactory extends Factory
{
    protected $model = Competition::class;

    public function definition(): array
    {
        $dateStart = fake()->dateTimeBetween('+1 month', '+6 months');

        return [
            'name' => ['sk' => fake()->words(3, true), 'en' => fake()->words(3, true)],
            'date_start' => $dateStart,
            'date_end' => fake()->dateTimeBetween($dateStart, '+7 months'),
            'place_name' => fake()->company(),
            'country' => 'SK',
            'city' => fake()->city(),
            'organizer_team_id' => Team::factory(),
            'is_public_registration' => true,
            'is_published' => true,
            'published_at' => now(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_published' => false,
            'published_at' => null,
        ]);
    }
}
