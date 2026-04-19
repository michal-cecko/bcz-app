<?php

namespace Database\Factories;

use App\Models\Battle;
use App\Models\BattleCompetitor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<BattleCompetitor> */
class BattleCompetitorFactory extends Factory
{
    protected $model = BattleCompetitor::class;

    public function definition(): array
    {
        return [
            'battle_id' => Battle::factory(),
            'side' => fake()->randomElement(['a', 'b']),
            'user_id' => User::factory(),
            'user_name' => fake()->name(),
            'position' => 0,
        ];
    }
}
