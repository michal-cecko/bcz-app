<?php

namespace Tests\Feature\Filament;

use App\Enums\PaymentMethodEnum;
use App\Enums\RoleEnum;
use App\Filament\Resources\PaymentMethods\Pages\EditPaymentMethod;
use App\Filament\Resources\PaymentMethods\Pages\ListPaymentMethods;
use App\Models\PaymentMethod;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PaymentMethodResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Team $team;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }

        $this->team = Team::factory()->create();
        $this->admin = User::factory()->create();
        $this->admin->assignRole(RoleEnum::SUPER_ADMIN);
        $this->admin->teams()->attach($this->team);

        $this->actingAs($this->admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::setTenant($this->team);
        Filament::bootCurrentPanel();
    }

    public function test_can_list_payment_methods(): void
    {
        PaymentMethod::create([
            'method' => PaymentMethodEnum::GOPAY->value,
            'title' => 'GoPay',
        ]);

        Livewire::test(ListPaymentMethods::class)
            ->assertOk()
            ->assertCanSeeTableRecords(PaymentMethod::all());
    }

    public function test_can_edit_payment_method(): void
    {
        $method = PaymentMethod::create([
            'method' => PaymentMethodEnum::GOPAY->value,
            'title' => 'GoPay',
            'description' => 'Original description',
        ]);

        Livewire::test(EditPaymentMethod::class, ['record' => $method->getRouteKey()])
            ->assertOk()
            ->fillForm([
                'title' => 'Platba kartou cez GoPay',
                'description' => 'Updated description',
            ])
            ->call('save')
            ->assertNotified();

        $method->refresh();
        $this->assertEquals('Platba kartou cez GoPay', $method->title);
        $this->assertStringContainsString('Updated description', $method->description);
    }

    public function test_team_can_have_payment_methods_via_pivot(): void
    {
        $gopay = PaymentMethod::create([
            'method' => PaymentMethodEnum::GOPAY->value,
            'title' => 'GoPay',
        ]);
        $bankTransfer = PaymentMethod::create([
            'method' => PaymentMethodEnum::BANK_TRANSFER->value,
            'title' => 'Bankovy prevod',
        ]);

        $this->team->paymentMethods()->attach($gopay, ['is_enabled' => true, 'sort_order' => 0]);
        $this->team->paymentMethods()->attach($bankTransfer, ['is_enabled' => false, 'sort_order' => 1]);

        $this->assertCount(2, $this->team->paymentMethods);
        $this->assertCount(1, $this->team->enabledPaymentMethods);
        $this->assertEquals('gopay', $this->team->enabledPaymentMethods->first()->method->value);
    }

    public function test_get_enabled_payment_method_keys(): void
    {
        $gopay = PaymentMethod::create([
            'method' => PaymentMethodEnum::GOPAY->value,
            'title' => 'GoPay',
            'is_active' => true,
        ]);
        $cash = PaymentMethod::create([
            'method' => PaymentMethodEnum::CASH->value,
            'title' => 'Cash',
            'is_active' => true,
        ]);
        $inactive = PaymentMethod::create([
            'method' => PaymentMethodEnum::BANK_TRANSFER->value,
            'title' => 'Bank',
            'is_active' => false,
        ]);

        $this->team->paymentMethods()->attach($gopay, ['is_enabled' => true, 'sort_order' => 0]);
        $this->team->paymentMethods()->attach($cash, ['is_enabled' => true, 'sort_order' => 1]);
        $this->team->paymentMethods()->attach($inactive, ['is_enabled' => true, 'sort_order' => 2]);

        $keys = $this->team->getEnabledPaymentMethodKeys();

        $this->assertContains('gopay', $keys);
        $this->assertContains('cash', $keys);
        $this->assertNotContains('bank_transfer', $keys);
    }
}
