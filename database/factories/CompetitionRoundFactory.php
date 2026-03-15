<?php

namespace Database\Factories;

use App\Enums\RoundAdvancementTypeEnum;
use App\Models\CompetitionDetail;
use App\Models\CompetitionRound;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CompetitionRound> */
class CompetitionRoundFactory extends Factory
{
    protected $model = CompetitionRound::class;

    public function definition(): array
    {
        return [
            'competition_detail_id' => CompetitionDetail::factory(),
            'round_number' => fake()->numberBetween(1, 5),
            'name' => fake()->randomElement(['Qualification', 'Semi-final', 'Final']),
            'advancement_type' => fake()->randomElement(RoundAdvancementTypeEnum::cases()),
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
