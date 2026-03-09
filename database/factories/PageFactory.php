<?php

namespace Database\Factories;

use App\Enums\PageStatusEnum;
use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    protected $model = Page::class;

    public function definition(): array
    {
        return [
            'title' => ['sk' => fake()->words(3, true), 'en' => fake()->words(3, true)],
            'content' => ['sk' => []],
            'meta_title' => ['sk' => fake()->sentence(4), 'en' => fake()->sentence(4)],
            'meta_description' => ['sk' => fake()->sentence(), 'en' => fake()->sentence()],
            'status' => PageStatusEnum::Draft,
            'is_system' => false,
            'sort_order' => 0,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => PageStatusEnum::Published,
            'published_at' => now(),
        ]);
    }

    public function system(string $key): static
    {
        return $this->state(fn () => [
            'is_system' => true,
            'system_key' => $key,
            'status' => PageStatusEnum::Published,
            'published_at' => now(),
        ]);
    }
}
