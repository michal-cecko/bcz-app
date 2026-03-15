<?php

namespace Database\Factories;

use App\Enums\RegistrationFieldTypeEnum;
use App\Models\EventRegistration;
use App\Models\RegistrationFieldValue;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<RegistrationFieldValue> */
class RegistrationFieldValueFactory extends Factory
{
    protected $model = RegistrationFieldValue::class;

    public function definition(): array
    {
        return [
            'event_registration_id' => EventRegistration::factory(),
            'field_key' => fake()->slug(2),
            'field_type' => RegistrationFieldTypeEnum::TextInput,
            'value' => fake()->sentence(),
        ];
    }
}
