<?php

namespace Tests\Feature\Services;

use App\Enums\PaymentMethodEnum;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\Team;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VariableSymbolTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_receives_sequential_sequence_number(): void
    {
        $first = Payment::factory()->create();
        $second = Payment::factory()->create();
        $third = Payment::factory()->create();

        $this->assertNotNull($first->sequence_number);
        $this->assertSame($first->sequence_number + 1, $second->sequence_number);
        $this->assertSame($second->sequence_number + 1, $third->sequence_number);
    }

    public function test_variable_symbol_is_eight_digit_zero_padded_sequence(): void
    {
        $payment = Payment::factory()->create();

        $vs = app(PaymentService::class)->variableSymbolFor($payment);

        $this->assertSame(8, strlen($vs));
        $this->assertMatchesRegularExpression('/^\d{8}$/', $vs);
        $this->assertSame(str_pad((string) $payment->sequence_number, 8, '0', STR_PAD_LEFT), $vs);
    }

    public function test_record_manual_payment_populates_variable_symbol_for_bank_transfer(): void
    {
        $team = Team::factory()->create();
        $user = User::factory()->create();
        $user->teams()->attach($team);
        $membership = Membership::factory()->for($team)->for($user)->create();

        $payment = app(PaymentService::class)->recordManualPayment(
            user: $user,
            team: $team,
            payable: $membership,
            amount: 50.0,
            currency: 'EUR',
            paymentMethod: PaymentMethodEnum::BANK_TRANSFER,
            notify: false,
        );

        $this->assertNotNull($payment->sequence_number);
        $this->assertSame(
            str_pad((string) $payment->sequence_number, 8, '0', STR_PAD_LEFT),
            $payment->variable_symbol,
        );
    }

    public function test_record_manual_payment_leaves_variable_symbol_null_for_cash(): void
    {
        $team = Team::factory()->create();
        $user = User::factory()->create();
        $user->teams()->attach($team);
        $membership = Membership::factory()->for($team)->for($user)->create();

        $payment = app(PaymentService::class)->recordManualPayment(
            user: $user,
            team: $team,
            payable: $membership,
            amount: 50.0,
            currency: 'EUR',
            paymentMethod: PaymentMethodEnum::CASH,
            notify: false,
        );

        $this->assertNull($payment->variable_symbol);
        $this->assertNotNull($payment->sequence_number);
    }
}
