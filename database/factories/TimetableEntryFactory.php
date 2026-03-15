<?php

namespace Database\Factories;

use App\Enums\TimetableEntryStatusEnum;
use App\Models\CompetitionDetail;
use App\Models\TimetableEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<TimetableEntry> */
class TimetableEntryFactory extends Factory
{
    protected $model = TimetableEntry::class;

    public function definition(): array
    {
        return [
            'competition_detail_id' => CompetitionDetail::factory(),
            'title' => ['sk' => fake()->sentence(3), 'en' => fake()->sentence(3)],
            'scheduled_time' => fake()->dateTimeBetween('+1 month', '+6 months'),
            'status' => TimetableEntryStatusEnum::PENDING,
            'sort_order' => fake()->numberBetween(0, 20),
        ];
    }
}
