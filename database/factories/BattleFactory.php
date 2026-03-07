<?php

namespace Database\Factories;

use App\Models\Battle;
use App\Models\CompetitionRound;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Battle> */
class BattleFactory extends Factory
{
    protected $model = Battle::class;

    public function definition(): array
    {
        return [
            'competition_round_id' => CompetitionRound::factory(),
            'bracket_position' => fake()->numberBetween(1, 16),
        ];
    }
}
