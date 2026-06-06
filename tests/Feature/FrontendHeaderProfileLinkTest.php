<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Filament\Resources\Users\UserResource;
use App\Models\Page;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontendHeaderProfileLinkTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Page::factory()->published()->create([
            'title' => ['sk' => 'Domov'],
            'slug' => '/',
            'is_system' => true,
            'system_key' => 'homepage',
        ]);
    }

    public function test_teamless_customer_loads_frontend_and_gets_customer_panel_profile_link(): void
    {
        // Regression: the header built the tenant-scoped admin users.edit URL
        // with a null tenant for teamless customers, throwing
        // UrlGenerationException and 500ing every frontend page.
        $customer = User::factory()->create();

        $expectedUrl = UserResource::getUrl('edit', ['record' => $customer], panel: 'customer');

        $response = $this->actingAs($customer)->get('/');

        $response->assertStatus(200);
        $response->assertSee($expectedUrl, false);
    }

    public function test_guest_sees_login_link_in_both_desktop_and_mobile_nav(): void
    {
        // Regression: the mobile dropdown's guest branch had no login link at
        // all (only the commented-out join-us button), so guests on small
        // screens could not reach the login page from the header.
        $response = $this->get('/');

        $response->assertStatus(200);

        $loginLinkCount = substr_count($response->getContent(), 'href="'.route('login').'"');

        $this->assertSame(2, $loginLinkCount, 'Expected a login link in both the desktop nav and the mobile dropdown.');
    }

    public function test_team_member_gets_admin_panel_profile_link(): void
    {
        $team = Team::factory()->create();
        $member = User::factory()->create();
        $member->teams()->attach($team->id, [
            'role' => RoleEnum::ATHLETE->value,
            'is_active' => true,
            'joined_at' => now(),
        ]);

        $expectedUrl = UserResource::getUrl('edit', ['record' => $member], panel: 'admin', tenant: $team);

        $response = $this->actingAs($member)->get('/');

        $response->assertStatus(200);
        $response->assertSee($expectedUrl, false);
    }
}
