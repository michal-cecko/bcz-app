<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class MagicLoginTest extends TestCase
{
    use RefreshDatabase;

    private function magicLink(User $user): string
    {
        return URL::temporarySignedRoute('magic-login', now()->addDays(7), ['user' => $user->id]);
    }

    private function attachToTeam(User $user): void
    {
        $user->teams()->attach(Team::factory()->create()->id, [
            'role' => RoleEnum::ATHLETE->value,
            'is_active' => true,
            'joined_at' => now(),
            'continuous_membership' => false,
        ]);
    }

    public function test_teamless_user_without_password_lands_on_customer_setup_wizard(): void
    {
        $user = User::factory()->create(['password_set_at' => null]);

        $response = $this->get($this->magicLink($user));

        $response->assertRedirect(route('filament.customer.auth.setup-wizard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_already_logged_in_different_user_is_replaced_by_magic_link_user(): void
    {
        $existing = User::factory()->create();
        $target = User::factory()->create(['password_set_at' => null]);

        $this->actingAs($existing);
        $this->assertAuthenticatedAs($existing);

        $response = $this->get($this->magicLink($target));

        $response->assertRedirect(route('filament.customer.auth.setup-wizard'));
        $this->assertAuthenticatedAs($target);
    }

    public function test_teamless_user_with_password_lands_on_customer_panel(): void
    {
        $user = User::factory()->create(['password_set_at' => now()]);

        $response = $this->get($this->magicLink($user));

        // Teamless users go to the tenant-free customer panel, never /admin.
        $response->assertRedirect('/customer');
        $this->assertAuthenticatedAs($user);
    }

    public function test_team_member_with_password_lands_on_admin_panel(): void
    {
        $user = User::factory()->create(['password_set_at' => now()]);
        $this->attachToTeam($user);

        $response = $this->get($this->magicLink($user));

        $response->assertRedirect('/admin');
        $this->assertAuthenticatedAs($user);
    }

    public function test_teamless_user_can_load_customer_panel_without_tenant_error(): void
    {
        $user = User::factory()->create(['password_set_at' => now()]);

        // Previously this threw UrlGenerationException (missing {tenant}); the
        // tenant-free customer panel must render for a user with no team.
        $this->actingAs($user)->get('/customer')->assertSuccessful();
    }
}
