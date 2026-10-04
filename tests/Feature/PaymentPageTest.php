<?php

namespace Tests\Feature;

use App\Enums\PaymentStatusEnum;
use App\Livewire\PaymentPage;
use App\Models\Payment;
use App\Services\GoPayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PaymentPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_page_left_open_does_not_start_a_new_gopay_charge_once_the_payment_is_settled(): void
    {
        $payment = Payment::factory()->create(['status' => PaymentStatusEnum::PENDING]);

        $this->mock(GoPayService::class)->shouldNotReceive('createPayment');

        $page = Livewire::test(PaymentPage::class, ['payment' => $payment])
            ->set('selectedMethod', 'gopay');

        $payment->update(['status' => PaymentStatusEnum::COMPLETED, 'paid_at' => now()]);

        $page->call('pay')
            ->assertNoRedirect()
            ->assertSet('isCompleted', true);
    }
}
