<?php

namespace Database\Factories;

use App\Models\Certification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Certification>
 */
class CertificationFactory extends Factory
{
    protected $model = Certification::class;

    public function definition(): array
    {
        return [
            'certifiable_id' => User::factory(),
            'certifiable_type' => User::class,
            'name' => ['sk' => fake()->words(3, true), 'en' => fake()->words(3, true)],
            'description' => ['sk' => fake()->sentence(), 'en' => fake()->sentence()],
            'year_of_issue' => fake()->numberBetween(2015, 2026),
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
