<?php

namespace Database\Factories;

use App\Models\CompetitionDetail;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CompetitionDetail> */
class CompetitionDetailFactory extends Factory
{
    protected $model = CompetitionDetail::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
        ];
    }
}
