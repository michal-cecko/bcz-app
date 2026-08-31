<?php

namespace Tests\Feature\Filament;

use App\Enums\CoachRoleEnum;
use App\Enums\RoleEnum;
use App\Filament\Resources\Trainings\Pages\EditTraining;
use App\Filament\Resources\Trainings\Pages\ViewTraining;
use App\Filament\Resources\Trainings\RelationManagers\CoachesRelationManager;
use App\Models\SportCategory;
use App\Models\Team;
use App\Models\Training;
use App\Models\User;
use Database\Seeders\ShieldPermissionSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CoachesRelationManagerDetachTest extends TestCase
{
    use RefreshDatabase;

    protected Team $team;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }

        $this->seed(ShieldPermissionSeeder::class);

        $this->team = Team::factory()->create();
    }

    protected function actingAsTenantUser(User $user): void
    {
        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::setTenant($this->team);
        Filament::bootCurrentPanel();
    }

    protected function makeTraining(): Training
    {
        $sportCategory = SportCategory::factory()->create(['team_id' => $this->team->id]);

        return Training::factory()->create([
            'team_id' => $this->team->id,
            'sport_category_id' => $sportCategory->id,
        ]);
    }

    /**
     * This is the exact navigation path every user takes: `TrainingsTable::recordUrl()`
     * always points at the `view` route, never `edit`. Before the fix, Filament's
     * default `isReadOnly()` (true on any ViewRecord page) hid Attach/Detach here
     * regardless of role — including for a SUPER_ADMIN — which is what actually made
     * "detach coach" read as missing.
     */
    public function test_super_admin_can_detach_a_coach_from_the_default_view_page(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::SUPER_ADMIN);

        $training = $this->makeTraining();

        $coach = User::factory()->create();
        $coach->teams()->attach($this->team->id, ['role' => RoleEnum::COACH->value, 'is_active' => true, 'joined_at' => now()]);
        $training->coaches()->attach($coach->id, ['role' => CoachRoleEnum::MAIN->value]);

        $secondCoach = User::factory()->create();
        $secondCoach->teams()->attach($this->team->id, ['role' => RoleEnum::COACH->value, 'is_active' => true, 'joined_at' => now()]);
        $training->coaches()->attach($secondCoach->id, ['role' => CoachRoleEnum::SECONDARY->value]);

        $this->actingAsTenantUser($admin->fresh());

        Livewire::test(CoachesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => ViewTraining::class,
        ])
            ->assertTableActionVisible('detach', $secondCoach)
            ->callTableAction('detach', $secondCoach)
            ->assertNotified();

        $this->assertDatabaseMissing('coach_training', [
            'training_id' => $training->id,
            'user_id' => $secondCoach->id,
        ]);
    }

    public function test_coach_assigned_to_the_training_can_detach_a_coach(): void
    {
        $coach = User::factory()->create();
        $coach->teams()->attach($this->team->id, ['role' => RoleEnum::COACH->value, 'is_active' => true, 'joined_at' => now()]);

        $training = $this->makeTraining();
        $training->coaches()->attach($coach->id, ['role' => CoachRoleEnum::MAIN->value]);

        $secondCoach = User::factory()->create();
        $secondCoach->teams()->attach($this->team->id, ['role' => RoleEnum::COACH->value, 'is_active' => true, 'joined_at' => now()]);
        $training->coaches()->attach($secondCoach->id, ['role' => CoachRoleEnum::SECONDARY->value]);

        $this->actingAsTenantUser($coach->fresh());

        Livewire::test(CoachesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => ViewTraining::class,
        ])
            ->assertTableActionVisible('detach', $secondCoach)
            ->callTableAction('detach', $secondCoach)
            ->assertNotified();

        $this->assertDatabaseMissing('coach_training', [
            'training_id' => $training->id,
            'user_id' => $secondCoach->id,
        ]);
    }

    public function test_team_admin_can_detach_a_coach_from_their_own_teams_training(): void
    {
        $teamAdmin = User::factory()->create();
        $teamAdmin->teams()->attach($this->team->id, ['role' => RoleEnum::TEAM_ADMIN->value, 'is_active' => true, 'joined_at' => now()]);

        $training = $this->makeTraining();

        $coach = User::factory()->create();
        $coach->teams()->attach($this->team->id, ['role' => RoleEnum::COACH->value, 'is_active' => true, 'joined_at' => now()]);
        $training->coaches()->attach($coach->id, ['role' => CoachRoleEnum::MAIN->value]);

        $secondCoach = User::factory()->create();
        $secondCoach->teams()->attach($this->team->id, ['role' => RoleEnum::COACH->value, 'is_active' => true, 'joined_at' => now()]);
        $training->coaches()->attach($secondCoach->id, ['role' => CoachRoleEnum::SECONDARY->value]);

        $this->actingAsTenantUser($teamAdmin->fresh());

        Livewire::test(CoachesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => ViewTraining::class,
        ])
            ->assertTableActionVisible('detach', $secondCoach)
            ->callTableAction('detach', $secondCoach)
            ->assertNotified();

        $this->assertDatabaseMissing('coach_training', [
            'training_id' => $training->id,
            'user_id' => $secondCoach->id,
        ]);
    }

    /**
     * A coach who is not assigned to this particular training (even though they're
     * on the same team, and can therefore view it) must not be able to manage its
     * coaches — this is what stops the fix from opening detach up to everyone.
     */
    public function test_coach_not_assigned_to_the_training_cannot_detach_a_coach(): void
    {
        $coach = User::factory()->create();
        $coach->teams()->attach($this->team->id, ['role' => RoleEnum::COACH->value, 'is_active' => true, 'joined_at' => now()]);

        $training = $this->makeTraining();

        $mainCoach = User::factory()->create();
        $mainCoach->teams()->attach($this->team->id, ['role' => RoleEnum::COACH->value, 'is_active' => true, 'joined_at' => now()]);
        $training->coaches()->attach($mainCoach->id, ['role' => CoachRoleEnum::MAIN->value]);

        $this->actingAsTenantUser($coach->fresh());

        Livewire::test(CoachesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => ViewTraining::class,
        ])
            ->assertTableActionHidden('detach', $mainCoach);

        $this->assertDatabaseHas('coach_training', [
            'training_id' => $training->id,
            'user_id' => $mainCoach->id,
        ]);
    }

    public function test_super_admin_can_still_detach_a_coach_from_the_edit_page(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::SUPER_ADMIN);

        $training = $this->makeTraining();

        $coach = User::factory()->create();
        $coach->teams()->attach($this->team->id, ['role' => RoleEnum::COACH->value, 'is_active' => true, 'joined_at' => now()]);
        $training->coaches()->attach($coach->id, ['role' => CoachRoleEnum::MAIN->value]);

        $secondCoach = User::factory()->create();
        $secondCoach->teams()->attach($this->team->id, ['role' => RoleEnum::COACH->value, 'is_active' => true, 'joined_at' => now()]);
        $training->coaches()->attach($secondCoach->id, ['role' => CoachRoleEnum::SECONDARY->value]);

        $this->actingAsTenantUser($admin->fresh());

        Livewire::test(CoachesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => EditTraining::class,
        ])
            ->assertTableActionVisible('detach', $secondCoach);
    }
}
