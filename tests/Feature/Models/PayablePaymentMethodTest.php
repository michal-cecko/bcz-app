<?php

namespace Tests\Feature\Models;

use App\Enums\PaymentMethodEnum;
use App\Models\Event;
use App\Models\PaymentMethod;
use App\Models\Training;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayablePaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    private PaymentMethod $gopay;

    private PaymentMethod $bankTransfer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gopay = PaymentMethod::create([
            'method' => PaymentMethodEnum::GOPAY,
            'title' => ['sk' => 'Platba kartou', 'en' => 'Card payment', 'cs' => 'Platba kartou'],
            'description' => ['sk' => 'Okamžitá platba', 'en' => 'Instant payment', 'cs' => 'Okamžitá platba'],
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $this->bankTransfer = PaymentMethod::create([
            'method' => PaymentMethodEnum::BANK_TRANSFER,
            'title' => ['sk' => 'Bankový prevod', 'en' => 'Bank transfer', 'cs' => 'Bankovní převod'],
            'description' => ['sk' => 'Platba na účet', 'en' => 'Pay to account', 'cs' => 'Platba na účet'],
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }

    public function test_payment_method_stores_translations_on_base_model(): void
    {
        $this->assertSame('Platba kartou', $this->gopay->getTranslation('title', 'sk'));
        $this->assertSame('Card payment', $this->gopay->getTranslation('title', 'en'));
        $this->assertSame('Platba kartou', $this->gopay->getTranslation('title', 'cs'));
    }

    public function test_training_can_attach_payment_method_with_pivot_overrides(): void
    {
        $training = Training::factory()->create();

        $training->paymentMethods()->attach($this->gopay->id, [
            'title' => ['sk' => 'Tréning – kartou', 'en' => 'Training – card'],
            'description' => ['sk' => 'Špeciálny popis'],
            'instructions' => ['sk' => 'Zaplať do 24h'],
            'is_enabled' => true,
            'sort_order' => 0,
        ]);

        $attached = $training->paymentMethods()->first();

        $this->assertNotNull($attached);
        $this->assertSame($this->gopay->id, $attached->id);
        $this->assertSame('Tréning – kartou', $attached->pivot->getTranslation('title', 'sk'));
        $this->assertSame('Training – card', $attached->pivot->getTranslation('title', 'en'));
        $this->assertSame('Špeciálny popis', $attached->pivot->getTranslation('description', 'sk'));
        $this->assertSame('Zaplať do 24h', $attached->pivot->getTranslation('instructions', 'sk'));
        $this->assertTrue($attached->pivot->is_enabled);
    }

    public function test_event_can_attach_payment_method(): void
    {
        $event = Event::factory()->create();

        $event->paymentMethods()->attach($this->bankTransfer->id, [
            'is_enabled' => true,
            'sort_order' => 0,
        ]);

        $this->assertSame(1, $event->paymentMethods()->count());
        $this->assertSame($this->bankTransfer->id, $event->paymentMethods()->first()->id);
    }

    public function test_enabled_payment_methods_scope_filters_disabled_pivot_and_inactive_base(): void
    {
        $training = Training::factory()->create();

        $training->paymentMethods()->attach($this->gopay->id, [
            'is_enabled' => true,
            'sort_order' => 0,
        ]);

        $training->paymentMethods()->attach($this->bankTransfer->id, [
            'is_enabled' => false,
            'sort_order' => 1,
        ]);

        $inactive = PaymentMethod::create([
            'method' => PaymentMethodEnum::CASH,
            'title' => ['sk' => 'Hotovosť'],
            'is_active' => false,
            'sort_order' => 2,
        ]);

        $training->paymentMethods()->attach($inactive->id, [
            'is_enabled' => true,
            'sort_order' => 2,
        ]);

        $enabled = $training->enabledPaymentMethods()->get();

        $this->assertSame(1, $enabled->count());
        $this->assertSame($this->gopay->id, $enabled->first()->id);
    }

    public function test_training_and_event_pivots_do_not_interfere_across_models(): void
    {
        $training = Training::factory()->create();
        $event = Event::factory()->create();

        $training->paymentMethods()->attach($this->gopay->id, ['is_enabled' => true, 'sort_order' => 0]);
        $event->paymentMethods()->attach($this->bankTransfer->id, ['is_enabled' => true, 'sort_order' => 0]);

        $this->assertSame(1, $training->paymentMethods()->count());
        $this->assertSame(1, $event->paymentMethods()->count());
        $this->assertSame($this->gopay->id, $training->paymentMethods()->first()->id);
        $this->assertSame($this->bankTransfer->id, $event->paymentMethods()->first()->id);
    }

    public function test_unique_constraint_prevents_duplicate_method_on_same_payable(): void
    {
        $training = Training::factory()->create();

        $training->paymentMethods()->attach($this->gopay->id, ['is_enabled' => true, 'sort_order' => 0]);

        $this->expectException(QueryException::class);

        $training->paymentMethods()->attach($this->gopay->id, ['is_enabled' => true, 'sort_order' => 1]);
    }
}
