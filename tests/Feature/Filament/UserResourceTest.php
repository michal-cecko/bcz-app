<?php

namespace Tests\Feature\Filament;

use App\Enums\RoleEnum;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\UserResource;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserResourceTest extends TestCase
{
    use RefreshDatabase;

    protected Team $team;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }
        Role::firstOrCreate(['name' => 'panel_user', 'guard_name' => 'web']);

        $this->team = Team::factory()->create();
    }

    private function bootAdminPanelAs(User $user): void
    {
        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::setTenant($this->team);
        Filament::bootCurrentPanel();
    }

    public function test_non_admin_cannot_edit_privileged_fields_on_self(): void
    {
        $user = User::factory()->create(['has_free_membership' => false]);
        $user->assignRole(RoleEnum::ATHLETE);
        $user->teams()->attach($this->team, ['role' => RoleEnum::ATHLETE->value]);

        $this->bootAdminPanelAs($user);

        $this->assertFalse(UserForm::canEditPrivilegedFields($user));
    }

    public function test_admin_can_edit_privileged_fields_on_anyone(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::ADMIN);
        $admin->teams()->attach($this->team);

        $target = User::factory()->create();

        $this->bootAdminPanelAs($admin);

        $this->assertTrue(UserForm::canEditPrivilegedFields($target));
        $this->assertTrue(UserForm::canEditPrivilegedFields($admin));
    }

    public function test_team_admin_cannot_edit_privileged_fields_on_self(): void
    {
        $teamAdmin = User::factory()->create();
        $teamAdmin->teams()->attach($this->team, ['role' => RoleEnum::TEAM_ADMIN->value]);

        $this->bootAdminPanelAs($teamAdmin);

        $this->assertFalse(UserForm::canEditPrivilegedFields($teamAdmin));
    }

    public function test_sync_team_scoped_roles_writes_pivot_for_each_team(): void
    {
        $user = User::factory()->create();
        $teamA = Team::factory()->create();
        $teamB = Team::factory()->create();

        $coachRoleId = Role::query()->where('name', RoleEnum::COACH->value)->value('id');

        UserResource::syncTeamScopedRoles($user, [$coachRoleId], [$teamA->id, $teamB->id]);

        $this->assertDatabaseHas('team_user', [
            'user_id' => $user->id,
            'team_id' => $teamA->id,
            'role' => RoleEnum::COACH->value,
        ]);
        $this->assertDatabaseHas('team_user', [
            'user_id' => $user->id,
            'team_id' => $teamB->id,
            'role' => RoleEnum::COACH->value,
        ]);
    }

    public function test_sync_team_scoped_roles_leaves_other_teams_untouched(): void
    {
        $user = User::factory()->create();
        $teamA = Team::factory()->create();
        $teamB = Team::factory()->create();

        // Pre-existing pivot on team B that should NOT be touched.
        DB::table('team_user')->insert([
            'team_id' => $teamB->id,
            'user_id' => $user->id,
            'role' => RoleEnum::ATHLETE->value,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $coachRoleId = Role::query()->where('name', RoleEnum::COACH->value)->value('id');

        UserResource::syncTeamScopedRoles($user, [$coachRoleId], [$teamA->id]);

        $this->assertDatabaseHas('team_user', [
            'user_id' => $user->id,
            'team_id' => $teamA->id,
            'role' => RoleEnum::COACH->value,
        ]);
        $this->assertDatabaseHas('team_user', [
            'user_id' => $user->id,
            'team_id' => $teamB->id,
            'role' => RoleEnum::ATHLETE->value,
        ]);
    }

    public function test_sync_team_scoped_roles_preserves_hidden_panel_user_role(): void
    {
        $user = User::factory()->create();
        $user->assignRole('panel_user');
        $user->assignRole(RoleEnum::CUSTOMER);

        $athleteRoleId = Role::query()->where('name', RoleEnum::ATHLETE->value)->value('id');

        UserResource::syncTeamScopedRoles($user, [$athleteRoleId], [$this->team->id]);

        $user->refresh();

        // panel_user is a hidden bookkeeping role — must survive the form save.
        $this->assertTrue($user->hasRole('panel_user'));
        // Form-managed global role is replaced by the new selection (none of those given here).
        $this->assertFalse($user->hasRole(RoleEnum::CUSTOMER));
    }

    public function test_sync_team_scoped_roles_preserves_hidden_super_admin_role(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::SUPER_ADMIN);

        $editorRoleId = Role::query()->where('name', RoleEnum::EDITOR->value)->value('id');

        UserResource::syncTeamScopedRoles($user, [$editorRoleId], []);

        $user->refresh();

        $this->assertTrue($user->hasRole(RoleEnum::SUPER_ADMIN));
        $this->assertTrue($user->hasRole(RoleEnum::EDITOR));
    }

    public function test_sync_team_scoped_roles_is_no_op_when_team_ids_empty(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::ATHLETE);
        $user->teams()->attach($this->team, ['role' => RoleEnum::ATHLETE->value]);

        $coachRoleId = Role::query()->where('name', RoleEnum::COACH->value)->value('id');

        // Empty team list means "skip pivot sync entirely" — the existing ATHLETE pivot
        // must remain intact. Mirrors what afterSave() does for non-admin self-edits where
        // team_ids never reaches the data array.
        UserResource::syncTeamScopedRoles($user, [$coachRoleId], []);

        $this->assertDatabaseHas('team_user', [
            'user_id' => $user->id,
            'team_id' => $this->team->id,
            'role' => RoleEnum::ATHLETE->value,
        ]);
        $this->assertDatabaseMissing('team_user', [
            'user_id' => $user->id,
            'role' => RoleEnum::COACH->value,
        ]);
    }

    /**
     * Regression test for Sentry BCZ-APP-R: uploading a profile_image larger than Spatie
     * Media Library's configured max_file_size (10MB, see config/media-library.php) used to
     * bubble up as an uncaught Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig
     * instead of a normal Filament validation error, because SpatieMediaLibraryFileUpload
     * had no ->maxSize() matching that limit. The file must physically exceed the limit on
     * disk (not just report an inflated size) so this also exercises Spatie's own
     * filesize()-based guard in FileAdder::toMediaCollection() if the fix regresses.
     */
    public function test_oversized_profile_image_fails_form_validation_instead_of_crashing(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::SUPER_ADMIN);
        $admin->teams()->attach($this->team);

        $this->bootAdminPanelAs($admin);

        $oversizedImage = UploadedFile::fake()->createWithContent(
            'too-big.jpg',
            str_repeat('0', 11 * 1024 * 1024) // 11MB > Spatie's configured 10MB max_file_size
        );

        Livewire::test(EditUser::class, ['record' => $admin->getRouteKey()])
            ->assertFormFieldExists('profile_image')
            ->fillForm(['profile_image' => [$oversizedImage]])
            ->call('save')
            ->assertHasFormErrors(['profile_image']);

        $this->assertEmpty($admin->fresh()->getMedia('profile_image'));
    }
}
