<?php

namespace Tests\Feature\Filament;

use App\Enums\RoleEnum;
use App\Models\Team;
use App\Models\User;
use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Guards the post-registration login path. A teamless customer (created via
 * event registration, password set through the setup wizard) must be able to
 * log in. The trap: the canonical /login link used to point at the admin panel,
 * which rejects teamless customers in canAccessPanel() — Filament reports that
 * as "these credentials do not match", making a correct password look broken.
 */
class CustomerLoginAccessTest extends TestCase
{
    use RefreshDatabase;

    private const PASSWORD = 'Password123!';

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => RoleEnum::CUSTOMER->value, 'guard_name' => 'web']);
    }

    private function teamlessCustomer(): User
    {
        $user = User::factory()->create([
            'email' => 'customer@test.com',
            'password' => Hash::make(self::PASSWORD),
            'password_set_at' => now(),
        ]);
        $user->assignRole(RoleEnum::CUSTOMER->value);

        return $user;
    }

    private function teamMember(): User
    {
        $user = User::factory()->create([
            'email' => 'member@test.com',
            'password' => Hash::make(self::PASSWORD),
            'password_set_at' => now(),
        ]);
        $user->teams()->attach(Team::factory()->create()->id, [
            'role' => RoleEnum::ATHLETE->value,
            'is_active' => true,
            'joined_at' => now(),
            'continuous_membership' => false,
        ]);

        return $user;
    }

    public function test_login_route_redirects_to_customer_panel(): void
    {
        // Customers (the common registrant) can always access the customer panel,
        // so the universal login link must land there — not the admin panel.
        $this->get('/login')->assertRedirect('/customer/login');
    }

    public function test_teamless_customer_can_log_in_at_customer_panel(): void
    {
        $user = $this->teamlessCustomer();
        Filament::setCurrentPanel(Filament::getPanel('customer'));

        Livewire::test(Login::class)
            ->fillForm(['email' => $user->email, 'password' => self::PASSWORD])
            ->call('authenticate')
            ->assertHasNoFormErrors()
            ->assertRedirect('/customer');

        $this->assertAuthenticatedAs($user);
    }

    public function test_team_member_is_routed_to_admin_after_login(): void
    {
        $user = $this->teamMember();
        Filament::setCurrentPanel(Filament::getPanel('customer'));

        Livewire::test(Login::class)
            ->fillForm(['email' => $user->email, 'password' => self::PASSWORD])
            ->call('authenticate')
            ->assertHasNoFormErrors()
            ->assertRedirect('/admin');

        $this->assertAuthenticatedAs($user);
    }

    public function test_teamless_customer_at_admin_login_is_redirected_to_their_panel(): void
    {
        // Fallback: someone reaching /admin/login directly (bookmark, old link)
        // with valid credentials but no admin access is logged in and forwarded
        // to their home panel instead of seeing "credentials do not match".
        $user = $this->teamlessCustomer();
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(\App\Filament\Auth\Login::class)
            ->fillForm(['email' => $user->email, 'password' => self::PASSWORD])
            ->call('authenticate')
            ->assertHasNoFormErrors()
            ->assertRedirect('/customer');

        $this->assertAuthenticatedAs($user);
    }

    public function test_wrong_password_at_admin_login_still_errors(): void
    {
        // The fallback must never mask a genuinely wrong password.
        $user = $this->teamlessCustomer();
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(\App\Filament\Auth\Login::class)
            ->fillForm(['email' => $user->email, 'password' => 'wrong-password'])
            ->call('authenticate')
            ->assertHasFormErrors(['email']);

        $this->assertGuest();
    }

    public function test_header_sign_in_links_to_login_route_not_admin_panel(): void
    {
        // The site header's "Sign In" link must use the canonical login route
        // (which lands on the customer panel), not a hardcoded /admin/login that
        // rejects teamless customers.
        $html = view('components.header')->render();

        $this->assertStringContainsString(route('login'), $html);
        $this->assertStringNotContainsString('/admin/login', $html);
    }

    public function test_wrong_password_is_rejected(): void
    {
        $user = $this->teamlessCustomer();
        Filament::setCurrentPanel(Filament::getPanel('customer'));

        Livewire::test(Login::class)
            ->fillForm(['email' => $user->email, 'password' => 'wrong-password'])
            ->call('authenticate')
            ->assertHasFormErrors(['email']);

        $this->assertGuest();
    }
}
