<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Exercise;
use App\Models\ExerciseCategory;
use App\Models\MediaItem;
use App\Models\Team;
use App\Models\Training;
use App\Models\User;
use App\Policies\ExerciseCategoryPolicy;
use App\Policies\ExercisePolicy;
use App\Policies\MediaItemPolicy;
use App\Policies\TrainingPolicy;
use Database\Seeders\ShieldPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CoachPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected Team $teamA;

    protected Team $teamB;

    protected User $coachA;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }

        $this->seed(ShieldPermissionSeeder::class);

        $this->teamA = Team::factory()->create();
        $this->teamB = Team::factory()->create();

        $this->coachA = User::factory()->create();
        $this->coachA->assignRole(RoleEnum::COACH->value);
        $this->coachA->teams()->attach($this->teamA->id, [
            'role' => RoleEnum::COACH->value,
            'is_active' => true,
            'joined_at' => now(),
        ]);
    }

    public function test_coach_can_update_training_they_are_assigned_to(): void
    {
        $training = Training::factory()->create(['team_id' => $this->teamA->id]);
        $training->coaches()->attach($this->coachA->id, ['role' => 'main']);

        $this->assertTrue((new TrainingPolicy)->update($this->coachA->fresh(), $training));
    }

    public function test_coach_cannot_update_team_training_they_are_not_assigned_to(): void
    {
        $training = Training::factory()->create(['team_id' => $this->teamA->id]);

        $this->assertFalse((new TrainingPolicy)->update($this->coachA->fresh(), $training));
    }

    public function test_coach_cannot_update_other_team_training(): void
    {
        $training = Training::factory()->create(['team_id' => $this->teamB->id]);

        $this->assertFalse((new TrainingPolicy)->update($this->coachA->fresh(), $training));
    }

    public function test_coach_can_update_own_team_exercise(): void
    {
        $category = ExerciseCategory::factory()->create(['team_id' => $this->teamA->id]);
        $exercise = Exercise::factory()->create(['team_id' => $this->teamA->id, 'exercise_category_id' => $category->id]);

        $this->assertTrue((new ExercisePolicy)->update($this->coachA->fresh(), $exercise));
    }

    public function test_coach_cannot_update_other_team_exercise(): void
    {
        $category = ExerciseCategory::factory()->create(['team_id' => $this->teamB->id]);
        $exercise = Exercise::factory()->create(['team_id' => $this->teamB->id, 'exercise_category_id' => $category->id]);

        $this->assertFalse((new ExercisePolicy)->update($this->coachA->fresh(), $exercise));
    }

    public function test_coach_can_update_own_team_exercise_category(): void
    {
        $category = ExerciseCategory::factory()->create(['team_id' => $this->teamA->id]);

        $this->assertTrue((new ExerciseCategoryPolicy)->update($this->coachA->fresh(), $category));
    }

    public function test_coach_cannot_update_other_team_exercise_category(): void
    {
        $category = ExerciseCategory::factory()->create(['team_id' => $this->teamB->id]);

        $this->assertFalse((new ExerciseCategoryPolicy)->update($this->coachA->fresh(), $category));
    }

    public function test_coach_cannot_update_media_item_on_own_team(): void
    {
        $media = MediaItem::factory()->create(['team_id' => $this->teamA->id]);

        $this->assertFalse((new MediaItemPolicy)->update($this->coachA->fresh(), $media));
    }

    public function test_global_admin_bypasses_all_coach_scoping(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::ADMIN);

        $training = Training::factory()->create(['team_id' => $this->teamB->id]);
        $category = ExerciseCategory::factory()->create(['team_id' => $this->teamB->id]);
        $exercise = Exercise::factory()->create(['team_id' => $this->teamB->id, 'exercise_category_id' => $category->id]);
        $media = MediaItem::factory()->create(['team_id' => $this->teamB->id]);

        $this->assertTrue((new TrainingPolicy)->update($admin, $training));
        $this->assertTrue((new ExercisePolicy)->update($admin, $exercise));
        $this->assertTrue((new ExerciseCategoryPolicy)->update($admin, $category));
        $this->assertTrue((new MediaItemPolicy)->update($admin, $media));
    }

    public function test_team_admin_can_update_any_training_on_own_team(): void
    {
        $teamAdmin = User::factory()->create();
        $teamAdmin->assignRole(RoleEnum::TEAM_ADMIN->value);
        $teamAdmin->teams()->attach($this->teamA->id, [
            'role' => RoleEnum::TEAM_ADMIN->value,
            'is_active' => true,
            'joined_at' => now(),
        ]);

        $training = Training::factory()->create(['team_id' => $this->teamA->id]);

        $this->assertTrue((new TrainingPolicy)->update($teamAdmin->fresh(), $training));
    }
}
