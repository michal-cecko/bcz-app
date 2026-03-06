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
        $superAdmin = $this->createUserWithRole(RoleEnum::SuperAdmin);

        $this->assertTrue((new UserPolicy)->viewAny($superAdmin));
    }

    public function test_admin_can_view_any_users(): void
    {
        $admin = $this->createUserWithRole(RoleEnum::Admin);

        $this->assertTrue((new UserPolicy)->viewAny($admin));
    }

    public function test_coach_can_view_any_users(): void
    {
        $coach = $this->createUserWithRole(RoleEnum::Coach);

        $this->assertTrue((new UserPolicy)->viewAny($coach));
    }

    public function test_editor_can_view_any_users(): void
    {
        $editor = $this->createUserWithRole(RoleEnum::Editor);

        $this->assertTrue((new UserPolicy)->viewAny($editor));
    }

    public function test_athlete_can_view_any_users(): void
    {
        $athlete = $this->createUserWithRole(RoleEnum::Athlete);

        $this->assertTrue((new UserPolicy)->viewAny($athlete));
    }

    public function test_judge_cannot_view_any_users(): void
    {
        $judge = $this->createUserWithRole(RoleEnum::Judge);

        $this->assertFalse((new UserPolicy)->viewAny($judge));
    }

    public function test_customer_cannot_view_any_users(): void
    {
        $customer = $this->createUserWithRole(RoleEnum::Customer);

        $this->assertFalse((new UserPolicy)->viewAny($customer));
    }

    public function test_athlete_can_only_view_other_athletes(): void
    {
        $athlete = $this->createUserWithRole(RoleEnum::Athlete);
        $otherAthlete = $this->createUserWithRole(RoleEnum::Athlete);
        $coach = $this->createUserWithRole(RoleEnum::Coach);

        $policy = new UserPolicy;

        $this->assertTrue($policy->view($athlete, $otherAthlete));
        $this->assertFalse($policy->view($athlete, $coach));
    }

    public function test_coach_can_view_any_user(): void
    {
        $coach = $this->createUserWithRole(RoleEnum::Coach);
        $athlete = $this->createUserWithRole(RoleEnum::Athlete);
        $admin = $this->createUserWithRole(RoleEnum::Admin);

        $policy = new UserPolicy;

        $this->assertTrue($policy->view($coach, $athlete));
        $this->assertTrue($policy->view($coach, $admin));
    }

    public function test_superadmin_can_update_admin(): void
    {
        $superAdmin = $this->createUserWithRole(RoleEnum::SuperAdmin);
        $admin = $this->createUserWithRole(RoleEnum::Admin);

        $this->assertTrue((new UserPolicy)->update($superAdmin, $admin));
    }

    public function test_admin_cannot_update_other_admin(): void
    {
        $admin1 = $this->createUserWithRole(RoleEnum::Admin);
        $admin2 = $this->createUserWithRole(RoleEnum::Admin);

        $this->assertFalse((new UserPolicy)->update($admin1, $admin2));
    }

    public function test_admin_can_update_self(): void
    {
        $admin = $this->createUserWithRole(RoleEnum::Admin);

        $this->assertTrue((new UserPolicy)->update($admin, $admin));
    }

    public function test_admin_can_update_regular_user(): void
    {
        $admin = $this->createUserWithRole(RoleEnum::Admin);
        $coach = $this->createUserWithRole(RoleEnum::Coach);

        $this->assertTrue((new UserPolicy)->update($admin, $coach));
    }

    public function test_coach_cannot_update_user(): void
    {
        $coach = $this->createUserWithRole(RoleEnum::Coach);
        $athlete = $this->createUserWithRole(RoleEnum::Athlete);

        $this->assertFalse((new UserPolicy)->update($coach, $athlete));
    }

    public function test_superadmin_can_delete_admin(): void
    {
        $superAdmin = $this->createUserWithRole(RoleEnum::SuperAdmin);
        $admin = $this->createUserWithRole(RoleEnum::Admin);

        $this->assertTrue((new UserPolicy)->delete($superAdmin, $admin));
    }

    public function test_admin_cannot_delete_other_admin(): void
    {
        $admin1 = $this->createUserWithRole(RoleEnum::Admin);
        $admin2 = $this->createUserWithRole(RoleEnum::Admin);

        $this->assertFalse((new UserPolicy)->delete($admin1, $admin2));
    }

    public function test_admin_cannot_delete_superadmin(): void
    {
        $admin = $this->createUserWithRole(RoleEnum::Admin);
        $superAdmin = $this->createUserWithRole(RoleEnum::SuperAdmin);

        $this->assertFalse((new UserPolicy)->delete($admin, $superAdmin));
    }

    public function test_admin_can_delete_regular_user(): void
    {
        $admin = $this->createUserWithRole(RoleEnum::Admin);
        $coach = $this->createUserWithRole(RoleEnum::Coach);

        $this->assertTrue((new UserPolicy)->delete($admin, $coach));
    }

    public function test_no_one_can_delete_self(): void
    {
        $superAdmin = $this->createUserWithRole(RoleEnum::SuperAdmin);
        $admin = $this->createUserWithRole(RoleEnum::Admin);

        $this->assertFalse((new UserPolicy)->delete($superAdmin, $superAdmin));
        $this->assertFalse((new UserPolicy)->delete($admin, $admin));
    }

    public function test_admin_can_create_users(): void
    {
        $admin = $this->createUserWithRole(RoleEnum::Admin);

        $this->assertTrue((new UserPolicy)->create($admin));
    }

    public function test_coach_cannot_create_users(): void
    {
        $coach = $this->createUserWithRole(RoleEnum::Coach);

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
