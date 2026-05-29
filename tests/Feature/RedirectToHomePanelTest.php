<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Filament\Resources\Users\UserResource;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RedirectToHomePanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_teamless_customer_hitting_admin_deep_link_is_redirected_to_same_page_on_customer_panel(): void
    {
        $customer = User::factory()->create();
        $team = Team::factory()->create(); // a team the customer does NOT belong to

        $adminUrl = UserResource::getUrl('edit', ['record' => $customer], panel: 'admin', tenant: $team);

        $this->actingAs($customer)
            ->get($adminUrl)
            ->assertRedirect(UserResource::getUrl('edit', ['record' => $customer], panel: 'customer'));
    }

    public function test_team_member_hitting_customer_deep_link_is_redirected_to_admin_panel_with_tenant(): void
    {
        $team = Team::factory()->create();
        $member = User::factory()->create();
        $member->teams()->attach($team->id, [
            'role' => RoleEnum::ATHLETE->value,
            'is_active' => true,
            'joined_at' => now(),
        ]);

        $customerUrl = UserResource::getUrl('edit', ['record' => $member], panel: 'customer');

        $this->actingAs($member)
            ->get($customerUrl)
            ->assertRedirect(UserResource::getUrl('edit', ['record' => $member], panel: 'admin', tenant: $team));
    }
}
