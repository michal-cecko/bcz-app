<?php

namespace Database\Factories;

use App\Enums\PayoutStatusEnum;
use App\Models\Team;
use App\Models\TeamPayout;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<TeamPayout> */
class TeamPayoutFactory extends Factory
{
    protected $model = TeamPayout::class;

    public function definition(): array
    {
        $gross = fake()->randomFloat(2, 50, 5000);
        $fee = round($gross * 0.05, 2);

        return [
            'team_id' => Team::factory(),
            'gross_amount' => $gross,
            'fee_amount' => $fee,
            'net_amount' => $gross - $fee,
            'currency' => 'EUR',
            'status' => PayoutStatusEnum::COMPLETED,
            'bank_account_iban' => fake()->iban('SK'),
            'bank_account_name' => fake()->company(),
            'period_from' => now()->subMonth()->startOfMonth(),
            'period_to' => now()->subMonth()->endOfMonth(),
            'paid_at' => now(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => PayoutStatusEnum::PENDING,
            'paid_at' => null,
        ]);
    }
}
