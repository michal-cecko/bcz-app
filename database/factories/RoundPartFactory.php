<?php

namespace Database\Factories;

use App\Models\CompetitionRound;
use App\Models\RoundPart;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<RoundPart> */
class RoundPartFactory extends Factory
{
    protected $model = RoundPart::class;

    public function definition(): array
    {
        return [
            'competition_round_id' => CompetitionRound::factory(),
            'name' => ['sk' => fake()->word(), 'en' => fake()->word()],
            'duration_seconds' => fake()->optional()->numberBetween(30, 120),
            'sort_order' => fake()->numberBetween(0, 5),
        ];
    }
}
