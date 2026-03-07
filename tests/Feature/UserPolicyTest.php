<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }
    }

    public function test_superadmin_can_view_any_users(): void
    {
        $superAdmin = $this->createUserWithRole(RoleEnum::SUPER_ADMIN);

        $this->assertTrue((new UserPolicy)->viewAny($superAdmin));
    }

    public function test_admin_can_view_any_users(): void
    {
        $admin = $this->createUserWithRole(RoleEnum::ADMIN);

        $this->assertTrue((new UserPolicy)->viewAny($admin));
    }

    public function test_coach_can_view_any_users(): void
    {
        $coach = $this->createUserWithRole(RoleEnum::COACH);

        $this->assertTrue((new UserPolicy)->viewAny($coach));
    }

    public function test_editor_can_view_any_users(): void
    {
        $editor = $this->createUserWithRole(RoleEnum::EDITOR);

        $this->assertTrue((new UserPolicy)->viewAny($editor));
    }

    public function test_athlete_can_view_any_users(): void
    {
        $athlete = $this->createUserWithRole(RoleEnum::ATHLETE);

        $this->assertTrue((new UserPolicy)->viewAny($athlete));
    }

    public function test_judge_cannot_view_any_users(): void
    {
        $judge = $this->createUserWithRole(RoleEnum::JUDGE);

        $this->assertFalse((new UserPolicy)->viewAny($judge));
    }

    public function test_customer_cannot_view_any_users(): void
    {
        $customer = $this->createUserWithRole(RoleEnum::CUSTOMER);

        $this->assertFalse((new UserPolicy)->viewAny($customer));
    }

    public function test_athlete_can_only_view_other_athletes(): void
    {
        $athlete = $this->createUserWithRole(RoleEnum::ATHLETE);
        $otherAthlete = $this->createUserWithRole(RoleEnum::ATHLETE);
        $coach = $this->createUserWithRole(RoleEnum::COACH);

        $policy = new UserPolicy;

        $this->assertTrue($policy->view($athlete, $otherAthlete));
        $this->assertFalse($policy->view($athlete, $coach));
    }

    public function test_coach_can_view_any_user(): void
    {
        $coach = $this->createUserWithRole(RoleEnum::COACH);
        $athlete = $this->createUserWithRole(RoleEnum::ATHLETE);
        $admin = $this->createUserWithRole(RoleEnum::ADMIN);

        $policy = new UserPolicy;

        $this->assertTrue($policy->view($coach, $athlete));
        $this->assertTrue($policy->view($coach, $admin));
    }

    public function test_superadmin_can_update_admin(): void
    {
        $superAdmin = $this->createUserWithRole(RoleEnum::SUPER_ADMIN);
        $admin = $this->createUserWithRole(RoleEnum::ADMIN);

        $this->assertTrue((new UserPolicy)->update($superAdmin, $admin));
    }

    public function test_admin_cannot_update_other_admin(): void
    {
        $admin1 = $this->createUserWithRole(RoleEnum::ADMIN);
        $admin2 = $this->createUserWithRole(RoleEnum::ADMIN);

        $this->assertFalse((new UserPolicy)->update($admin1, $admin2));
    }

    public function test_admin_can_update_self(): void
    {
        $admin = $this->createUserWithRole(RoleEnum::ADMIN);

        $this->assertTrue((new UserPolicy)->update($admin, $admin));
    }

    public function test_admin_can_update_regular_user(): void
    {
        $admin = $this->createUserWithRole(RoleEnum::ADMIN);
        $coach = $this->createUserWithRole(RoleEnum::COACH);

        $this->assertTrue((new UserPolicy)->update($admin, $coach));
    }

    public function test_coach_cannot_update_user(): void
    {
        $coach = $this->createUserWithRole(RoleEnum::COACH);
        $athlete = $this->createUserWithRole(RoleEnum::ATHLETE);

        $this->assertFalse((new UserPolicy)->update($coach, $athlete));
    }

    public function test_superadmin_can_delete_admin(): void
    {
        $superAdmin = $this->createUserWithRole(RoleEnum::SUPER_ADMIN);
        $admin = $this->createUserWithRole(RoleEnum::ADMIN);

        $this->assertTrue((new UserPolicy)->delete($superAdmin, $admin));
    }

    public function test_admin_cannot_delete_other_admin(): void
    {
        $admin1 = $this->createUserWithRole(RoleEnum::ADMIN);
        $admin2 = $this->createUserWithRole(RoleEnum::ADMIN);

        $this->assertFalse((new UserPolicy)->delete($admin1, $admin2));
    }

    public function test_admin_cannot_delete_superadmin(): void
    {
        $admin = $this->createUserWithRole(RoleEnum::ADMIN);
        $superAdmin = $this->createUserWithRole(RoleEnum::SUPER_ADMIN);

        $this->assertFalse((new UserPolicy)->delete($admin, $superAdmin));
    }

    public function test_admin_can_delete_regular_user(): void
    {
        $admin = $this->createUserWithRole(RoleEnum::ADMIN);
        $coach = $this->createUserWithRole(RoleEnum::COACH);

        $this->assertTrue((new UserPolicy)->delete($admin, $coach));
    }

    public function test_no_one_can_delete_self(): void
    {
        $superAdmin = $this->createUserWithRole(RoleEnum::SUPER_ADMIN);
        $admin = $this->createUserWithRole(RoleEnum::ADMIN);

        $this->assertFalse((new UserPolicy)->delete($superAdmin, $superAdmin));
        $this->assertFalse((new UserPolicy)->delete($admin, $admin));
    }

    public function test_admin_can_create_users(): void
    {
        $admin = $this->createUserWithRole(RoleEnum::ADMIN);

        $this->assertTrue((new UserPolicy)->create($admin));
    }

    public function test_coach_cannot_create_users(): void
    {
        $coach = $this->createUserWithRole(RoleEnum::COACH);

        $this->assertFalse((new UserPolicy)->create($coach));
    }

    public function test_all_roles_are_seeded(): void
    {
        $this->assertCount(count(RoleEnum::cases()), Role::all());

        foreach (RoleEnum::cases() as $role) {
            $this->assertTrue(Role::where('name', $role->value)->exists());
        }
    }

    protected function createUserWithRole(RoleEnum $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }
}
