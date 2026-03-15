<?php

namespace Database\Factories;

use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\Team;
use App\Models\TrainingRegistration;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Payment> */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'user_id' => User::factory(),
            'payable_type' => 'membership',
            'payable_id' => Membership::factory(),
            'amount' => fake()->randomFloat(2, 5, 200),
            'currency' => 'EUR',
            'status' => PaymentStatusEnum::COMPLETED,
            'payment_method' => PaymentMethodEnum::CASH,
            'paid_at' => now(),
        ];
    }

    public function stripe(): static
    {
        return $this->state(fn () => [
            'payment_method' => PaymentMethodEnum::STRIPE,
            'stripe_payment_id' => 'pi_'.fake()->regexify('[a-zA-Z0-9]{24}'),
            'stripe_checkout_session_id' => 'cs_'.fake()->regexify('[a-zA-Z0-9]{24}'),
        ]);
    }

    public function refunded(): static
    {
        return $this->state(fn () => [
            'status' => PaymentStatusEnum::REFUNDED,
            'refunded_at' => now(),
        ]);
    }

    public function bankTransfer(): static
    {
        return $this->state(fn () => [
            'payment_method' => PaymentMethodEnum::BANK_TRANSFER,
            'variable_symbol' => (string) fake()->numerify('##########'),
        ]);
    }

    public function cash(): static
    {
        return $this->state(fn () => [
            'payment_method' => PaymentMethodEnum::CASH,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => PaymentStatusEnum::PENDING,
            'paid_at' => null,
        ]);
    }

    public function forTrainingRegistration(TrainingRegistration $registration): static
    {
        return $this->state(fn () => [
            'team_id' => $registration->training?->team_id,
            'user_id' => $registration->user_id,
            'payable_type' => 'training_registration',
            'payable_id' => $registration->id,
        ]);
    }
}
