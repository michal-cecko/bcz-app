<?php

namespace Tests\Feature\Filament;

use App\Enums\RoleEnum;
use App\Filament\Clusters\Content\ContentCluster;
use App\Filament\Clusters\Events\EventsCluster;
use App\Filament\Clusters\Trainings\TrainingsCluster;
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
}
