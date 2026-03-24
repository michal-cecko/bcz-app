<?php

namespace Database\Factories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Team>
 */
class TeamFactory extends Factory
{
    protected $model = Team::class;

    public function definition(): array
    {
        return [
            'name' => ['sk' => fake()->company(), 'en' => fake()->company()],
            'story' => ['sk' => fake()->paragraphs(2, true), 'en' => fake()->paragraphs(2, true)],
            'achievements' => ['sk' => fake()->sentence(), 'en' => fake()->sentence()],
            'socials' => [
                'instagram' => 'https://instagram.com/'.fake()->userName(),
                'facebook' => 'https://facebook.com/'.fake()->userName(),
            ],
            'is_active' => true,
            'payment_methods_enabled' => ['gopay', 'bank_transfer', 'cash'],
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
