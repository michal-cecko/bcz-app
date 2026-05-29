<?php

namespace Tests\Feature\Filament;

use App\Enums\RoleEnum;
use App\Filament\Resources\Users\UserResource;
use App\Models\Team;
use App\Models\User;
use Database\Seeders\ShieldPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserProfileSelfAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }
    }

    public function test_customer_can_open_their_own_profile_edit_on_customer_panel(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole(RoleEnum::CUSTOMER->value);

        $this->actingAs($customer)
            ->get(UserResource::getUrl('edit', ['record' => $customer], panel: 'customer'))
            ->assertSuccessful();
    }

    public function test_customer_cannot_open_another_users_profile_edit(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole(RoleEnum::CUSTOMER->value);
        $other = User::factory()->create();

        $this->actingAs($customer)
            ->get(UserResource::getUrl('edit', ['record' => $other], panel: 'customer'))
            ->assertForbidden();
    }

    public function test_customer_cannot_open_the_user_list(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole(RoleEnum::CUSTOMER->value);

        $this->actingAs($customer)
            ->get(UserResource::getUrl('index', panel: 'customer'))
            ->assertForbidden();
    }

    public function test_admin_with_permission_can_open_the_user_list(): void
    {
        // The list stays gated by ViewAny:User; a privileged admin still passes.
        // (Tested on the admin panel — a global admin's home panel is admin, so
        // RedirectToHomePanel would bounce them off the customer panel.)
        $this->seed(ShieldPermissionSeeder::class);

        $team = Team::factory()->create();
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::SUPER_ADMIN->value);

        $this->actingAs($admin)
            ->get(UserResource::getUrl('index', panel: 'admin', tenant: $team))
            ->assertSuccessful();
    }
}
