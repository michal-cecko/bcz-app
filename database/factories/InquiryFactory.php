<?php

namespace Database\Factories;

use App\Enums\InquiryReasonEnum;
use App\Enums\InquiryStatusEnum;
use App\Models\Inquiry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Inquiry>
 */
class InquiryFactory extends Factory
{
    protected $model = Inquiry::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'message' => fake()->paragraph(3),
            'reason' => fake()->randomElement(InquiryReasonEnum::cases()),
            'status' => fake()->randomElement(InquiryStatusEnum::cases()),
        ];
    }
}
