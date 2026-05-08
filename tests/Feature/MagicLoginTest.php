<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class MagicLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_anonymous_user_logs_in_via_magic_link_and_lands_on_setup_wizard(): void
    {
        $user = User::factory()->create(['password_set_at' => null]);

        $response = $this->get(URL::temporarySignedRoute('magic-login', now()->addDays(7), ['user' => $user->id]));

        $response->assertRedirect(route('filament.admin.auth.setup-wizard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_already_logged_in_different_user_is_replaced_by_magic_link_user(): void
    {
        $existing = User::factory()->create();
        $target = User::factory()->create(['password_set_at' => null]);

        $this->actingAs($existing);
        $this->assertAuthenticatedAs($existing);

        $response = $this->get(URL::temporarySignedRoute('magic-login', now()->addDays(7), ['user' => $target->id]));

        $response->assertRedirect(route('filament.admin.auth.setup-wizard'));
        $this->assertAuthenticatedAs($target);
    }

    public function test_user_with_password_set_lands_on_admin(): void
    {
        $user = User::factory()->create(['password_set_at' => now()]);

        $response = $this->get(URL::temporarySignedRoute('magic-login', now()->addDays(7), ['user' => $user->id]));

        $response->assertRedirect('/admin');
        $this->assertAuthenticatedAs($user);
    }
}
