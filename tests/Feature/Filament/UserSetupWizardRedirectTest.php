<?php

namespace Tests\Feature\Filament;

use App\Enums\RoleEnum;
use App\Filament\Pages\Auth\UserSetupWizard;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserSetupWizardRedirectTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => RoleEnum::CUSTOMER->value, 'guard_name' => 'web']);
    }

    private function customer(): User
    {
        $user = User::factory()->create(['password_set_at' => null]);
        $user->assignRole(RoleEnum::CUSTOMER->value);

        return $user;
    }

    public function test_completing_wizard_in_customer_panel_redirects_to_customer_panel(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('customer'));

        Livewire::actingAs($this->customer());

        Livewire::test(UserSetupWizard::class)
            ->set('data.password', 'Password123!')
            ->set('data.passwordConfirmation', 'Password123!')
            ->set('data.locale', 'sk')
            ->call('save')
            ->assertRedirect('/customer');
    }

    public function test_completing_wizard_in_admin_panel_redirects_to_admin_panel(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::actingAs($this->customer());

        Livewire::test(UserSetupWizard::class)
            ->set('data.password', 'Password123!')
            ->set('data.passwordConfirmation', 'Password123!')
            ->set('data.locale', 'sk')
            ->call('save')
            ->assertRedirect('/admin');
    }
}
