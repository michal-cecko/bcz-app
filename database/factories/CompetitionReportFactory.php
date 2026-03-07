<?php

namespace Database\Factories;

use App\Models\Competition;
use App\Models\CompetitionReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CompetitionReport> */
class CompetitionReportFactory extends Factory
{
    protected $model = CompetitionReport::class;

    public function definition(): array
    {
        return [
            'competition_id' => Competition::factory(),
            'user_id' => User::factory(),
            'title' => ['sk' => fake()->sentence(4), 'en' => fake()->sentence(4)],
            'is_published' => true,
            'published_at' => now(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_published' => false,
            'published_at' => null,
        ]);
    }
}
