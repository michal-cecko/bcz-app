<?php

namespace Tests\Feature\Filament;

use App\Enums\RoleEnum;
use App\Filament\Pages\MemberMembership;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PanelTenantRoutingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }
    }

    private function teamMember(): User
    {
        $user = User::factory()->create();
        $user->teams()->attach(Team::factory()->create()->id, [
            'role' => RoleEnum::ATHLETE->value,
            'is_active' => true,
            'joined_at' => now(),
            'continuous_membership' => false,
        ]);

        return $user;
    }

    public function test_teamless_user_is_redirected_from_admin_panel_to_customer_panel(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin')->assertRedirect('/customer');
    }

    public function test_team_member_is_redirected_from_customer_panel_to_admin_panel(): void
    {
        // The middleware maps the request to the same page on the home panel, so
        // a team member hitting the customer dashboard lands on their tenant's
        // admin dashboard (/admin/{tenant}) rather than bare /admin.
        $this->actingAs($this->teamMember())->get('/customer')->assertRedirectContains('/admin');
    }

    public function test_teamless_user_stays_on_customer_panel(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/customer')->assertSuccessful();
    }

    public function test_team_member_is_not_redirected_to_customer_panel_from_admin(): void
    {
        // Filament resolves the tenant and redirects to /admin/{tenant}; the key
        // assertion is that our middleware did not bounce them to /customer.
        $response = $this->actingAs($this->teamMember())->get('/admin');

        $response->assertRedirectContains('/admin');
    }

    public function test_membership_page_prompts_teamless_user_to_join_a_team(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('customer'));

        $user = User::factory()->create();
        $user->assignRole(RoleEnum::CUSTOMER->value);

        Livewire::actingAs($user);

        Livewire::test(MemberMembership::class)
            ->assertSee(__('member.membership.no_team_heading'));
    }

    public function test_membership_page_shows_membership_ui_for_team_member(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::CUSTOMER->value);
        $team = Team::factory()->create();
        $user->teams()->attach($team->id, [
            'role' => RoleEnum::ATHLETE->value,
            'is_active' => true,
            'joined_at' => now(),
            'continuous_membership' => false,
        ]);

        Livewire::actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::setTenant($team, isQuiet: true);

        Livewire::test(MemberMembership::class)
            ->assertDontSee(__('member.membership.no_team_heading'))
            ->assertSee(__('member.membership.past_seasons'));
    }
}
