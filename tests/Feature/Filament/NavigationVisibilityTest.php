<?php

namespace Tests\Feature\Filament;

use App\Enums\RoleEnum;
use App\Filament\Clusters\Content\ContentCluster;
use App\Filament\Clusters\Events\EventsCluster;
use App\Filament\Clusters\Trainings\TrainingsCluster;
use App\Filament\Pages\MemberMembership;
use App\Filament\Pages\MemberPayments;
use App\Filament\Pages\MyFamily;
use App\Filament\Pages\MyTrainings;
use App\Filament\Resources\Inquiries\InquiryResource;
use App\Filament\Resources\Teams\TeamResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NavigationVisibilityTest extends TestCase
{
    use RefreshDatabase;

    protected Team $team;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }

        $this->team = Team::factory()->create();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::bootCurrentPanel();
    }

    protected function actingAsMember(): User
    {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::CUSTOMER);
        $user->teams()->attach($this->team, ['role' => RoleEnum::ATHLETE->value]);
        $this->actingAs($user);
        Filament::setTenant($this->team);

        return $user;
    }

    protected function actingAsAdmin(): User
    {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::SUPER_ADMIN);
        $user->teams()->attach($this->team);
        $this->actingAs($user);
        Filament::setTenant($this->team);

        return $user;
    }

    public function test_resources_hidden_from_member(): void
    {
        $this->actingAsMember();

        $this->assertFalse(TeamResource::shouldRegisterNavigation());
        $this->assertFalse(UserResource::shouldRegisterNavigation());
    }

    public function test_resources_visible_for_admin(): void
    {
        $this->actingAsAdmin();

        $this->assertTrue(TeamResource::shouldRegisterNavigation());
        $this->assertTrue(UserResource::shouldRegisterNavigation());
    }

    public function test_clusters_hidden_from_member(): void
    {
        $this->actingAsMember();

        $this->assertFalse(TrainingsCluster::shouldRegisterNavigation());
        $this->assertFalse(EventsCluster::shouldRegisterNavigation());
        $this->assertFalse(ContentCluster::shouldRegisterNavigation());
    }

    public function test_clusters_visible_for_admin(): void
    {
        $this->actingAsAdmin();

        $this->assertTrue(TrainingsCluster::shouldRegisterNavigation());
        $this->assertTrue(EventsCluster::shouldRegisterNavigation());
        $this->assertTrue(ContentCluster::shouldRegisterNavigation());
    }

    public function test_inquiry_resource_hidden_from_member(): void
    {
        $this->actingAsMember();

        $this->assertFalse(InquiryResource::shouldRegisterNavigation());
    }

    public function test_customer_pages_require_customer_role_even_with_a_member_profile(): void
    {
        config(['family.enabled' => true]);

        foreach (RoleEnum::cases() as $role) {
            if ($role === RoleEnum::CUSTOMER) {
                continue;
            }

            $user = User::factory()->create();
            $user->assignRole($role);
            $user->legacyMember();
            $this->actingAs($user);

            $this->assertFalse(MyFamily::canAccess(), $role->value);
            $this->assertFalse(MemberMembership::shouldRegisterNavigation(), $role->value);
            $this->assertFalse(MemberPayments::shouldRegisterNavigation(), $role->value);
        }
    }

    public function test_admin_member_profile_does_not_show_personal_training_navigation(): void
    {
        config(['family.enabled' => true]);
        $admin = $this->actingAsAdmin();
        $admin->legacyMember();

        $this->assertFalse(MyTrainings::shouldRegisterNavigation());
        $this->get(MyFamily::getUrl())->assertForbidden();
    }

    public function test_admin_with_customer_role_sees_customer_pages(): void
    {
        config(['family.enabled' => true]);
        $admin = $this->actingAsAdmin();
        $admin->assignRole(RoleEnum::CUSTOMER);
        $admin->update(['is_parent' => true]);

        $this->assertTrue(MyFamily::canAccess());
        $this->assertTrue(MemberMembership::shouldRegisterNavigation());
        $this->assertTrue(MemberPayments::shouldRegisterNavigation());
        $this->assertTrue(MyTrainings::shouldRegisterNavigation());
        $this->get(MyFamily::getUrl())->assertOk();
    }

    public function test_customer_sees_family_page_only_when_enabled(): void
    {
        $this->actingAsMember()->update(['is_parent' => true]);
        config(['family.enabled' => true]);

        $this->assertTrue(MyFamily::canAccess());
        $this->assertTrue(MemberMembership::shouldRegisterNavigation());
        $this->assertTrue(MemberPayments::shouldRegisterNavigation());

        config(['family.enabled' => false]);

        $this->assertFalse(MyFamily::canAccess());
    }
}
