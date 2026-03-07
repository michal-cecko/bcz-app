<?php

namespace Database\Factories;

use App\Enums\SponsorTagEnum;
use App\Models\Sponsor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sponsor>
 */
class SponsorFactory extends Factory
{
    protected $model = Sponsor::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'tag' => fake()->randomElement(SponsorTagEnum::cases()),
            'link' => fake()->url(),
            'is_visible' => true,
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
