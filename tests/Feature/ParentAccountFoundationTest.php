<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Member;
use App\Models\User;
use App\Services\MemberProfileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ParentAccountFoundationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['family.enabled' => true]);
        Role::firstOrCreate(['name' => RoleEnum::CUSTOMER->value, 'guard_name' => 'web']);
    }

    public function test_normal_customer_has_one_member_but_parent_has_none(): void
    {
        $service = app(MemberProfileService::class);
        $customer = User::factory()->create(['first_name' => 'Account', 'last_name' => 'Holder']);
        $customer->assignRole(RoleEnum::CUSTOMER);
        $member = $service->resolveMember($customer);

        $this->assertSame('Account Holder', $member->name);
        $this->assertSame($member->id, $service->resolveMember($customer)->id);
        $this->assertSame(1, $customer->members()->count());

        $parent = User::factory()->create(['is_parent' => true]);
        $parent->assignRole(RoleEnum::CUSTOMER);
        $this->assertNull($service->resolveMember($parent));
        $this->assertSame(0, $parent->members()->count());
    }

    public function test_editor_keeps_account_identity_and_parent_cannot_drop_multiple_profiles(): void
    {
        $service = app(MemberProfileService::class);
        $customer = User::factory()->create(['first_name' => 'Account', 'last_name' => 'Holder']);
        $customer->assignRole(RoleEnum::CUSTOMER);
        $service->saveAccount($customer, [], ['first_name' => 'Training', 'last_name' => 'Member']);
        $this->assertSame('Account Holder', $customer->fresh()->name);
        $this->assertSame('Training Member', $customer->members()->sole()->name);

        $parent = User::factory()->create(['is_parent' => true]);
        Member::factory()->count(2)->create(['user_id' => $parent->id]);
        $this->expectException(ValidationException::class);
        $service->saveAccount($parent, ['is_parent' => false]);
    }
}
