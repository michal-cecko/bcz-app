<?php

namespace Database\Factories;

use App\Models\Competition;
use App\Models\RegistrationFee;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<RegistrationFee> */
class RegistrationFeeFactory extends Factory
{
    protected $model = RegistrationFee::class;

    public function definition(): array
    {
        return [
            'competition_id' => Competition::factory(),
            'amount' => fake()->randomFloat(2, 5, 50),
            'currency' => 'EUR',
            'description' => fake()->optional()->sentence(),
        ];
    }
}
