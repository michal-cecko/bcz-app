<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<City>
 */
class CityFactory extends Factory
{
    protected $model = City::class;

    public function definition(): array
    {
        return [
            'name' => ['sk' => fake()->city(), 'en' => fake()->city()],
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
