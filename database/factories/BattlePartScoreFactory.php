<?php

namespace Database\Factories;

use App\Models\Battle;
use App\Models\BattlePartScore;
use App\Models\RoundPart;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<BattlePartScore> */
class BattlePartScoreFactory extends Factory
{
    protected $model = BattlePartScore::class;

    public function definition(): array
    {
        return [
            'battle_id' => Battle::factory(),
            'round_part_id' => RoundPart::factory(),
            'side' => fake()->randomElement(['a', 'b']),
            'score' => fake()->randomFloat(2, 0, 100),
        ];
    }
}
