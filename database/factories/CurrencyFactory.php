<?php

namespace Database\Factories;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Currency> */
class CurrencyFactory extends Factory
{
    protected $model = Currency::class;

    public function definition(): array
    {
        return [
            'code' => fake()->unique()->currencyCode(),
            'name' => ['sk' => fake()->word(), 'en' => fake()->word()],
            'symbol' => fake()->randomElement(['€', '$', '£', 'Kč']),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
