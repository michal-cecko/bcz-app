<?php

namespace Database\Factories;

use App\Enums\BannerTypeEnum;
use App\Models\Banner;
use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Banner>
 */
class BannerFactory extends Factory
{
    protected $model = Banner::class;

    public function definition(): array
    {
        return [
            'name' => ['sk' => fake()->words(3, true), 'en' => fake()->words(3, true)],
            'type' => fake()->randomElement(BannerTypeEnum::cases()),
            'placement' => 'all',
            'page_id' => null,
            'content' => null,
            'is_active' => false,
            'active_from' => null,
            'active_to' => null,
            'sort_order' => 0,
        ];
    }

    public function topbar(): static
    {
        return $this->state(fn () => ['type' => BannerTypeEnum::Topbar]);
    }

    public function floating(): static
    {
        return $this->state(fn () => ['type' => BannerTypeEnum::Floating]);
    }

    public function popup(): static
    {
        return $this->state(fn () => ['type' => BannerTypeEnum::Popup]);
    }

    public function active(): static
    {
        return $this->state(fn () => ['is_active' => true]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    public function global(): static
    {
        return $this->state(fn () => ['placement' => 'all', 'page_id' => null]);
    }

    public function forPage(Page $page): static
    {
        return $this->state(fn () => ['placement' => 'specific', 'page_id' => $page->id]);
    }
}
