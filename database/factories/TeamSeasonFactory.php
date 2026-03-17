<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\TeamSeason;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<TeamSeason> */
class TeamSeasonFactory extends Factory
{
    protected $model = TeamSeason::class;

    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'name' => 'Sezóna '.fake()->year(),
            'starts_at' => now()->startOfMonth(),
            'ends_at' => now()->addMonths(9)->endOfMonth(),
            'fee_amount' => fake()->randomFloat(2, 20, 100),
            'fee_currency' => 'EUR',
            'max_capacity' => null,
            'payment_deadline_days' => 14,
        ];
    }

    public function past(): static
    {
        return $this->state(fn () => [
            'name' => 'Sezóna '.(now()->year - 1),
            'starts_at' => now()->subYear()->startOfYear()->month(3)->startOfMonth(),
            'ends_at' => now()->subYear()->startOfYear()->month(11)->endOfMonth(),
        ]);
    }

    public function future(): static
    {
        return $this->state(fn () => [
            'name' => 'Sezóna '.(now()->year + 1),
            'starts_at' => now()->addYear()->startOfYear()->month(3)->startOfMonth(),
            'ends_at' => now()->addYear()->startOfYear()->month(11)->endOfMonth(),
        ]);
    }
}
