<?php

namespace Database\Factories;

use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPlanPrice;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SubscriptionPlanPrice> */
class SubscriptionPlanPriceFactory extends Factory
{
    protected $model = SubscriptionPlanPrice::class;

    public function definition(): array
    {
        $monthly = fake()->randomFloat(2, 10, 200);

        return [
            'subscription_plan_id' => SubscriptionPlan::factory(),
            'currency_code' => 'EUR',
            'price_monthly' => $monthly,
            'price_yearly' => round($monthly * 12 * 0.83, 2),
        ];
    }
}
