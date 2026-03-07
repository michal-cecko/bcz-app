<?php

namespace Database\Factories;

use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Faq>
 */
class FaqFactory extends Factory
{
    protected $model = Faq::class;

    public function definition(): array
    {
        return [
            'faq_category_id' => FaqCategory::factory(),
            'question' => ['sk' => fake()->sentence().'?', 'en' => fake()->sentence().'?'],
            'answer' => ['sk' => fake()->paragraph(2), 'en' => fake()->paragraph(2)],
            'sort_order' => fake()->numberBetween(0, 10),
            'is_published' => true,
        ];
    }
}
