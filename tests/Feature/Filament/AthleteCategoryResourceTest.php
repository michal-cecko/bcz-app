<?php

namespace Tests\Feature\Filament;

use App\Enums\GenderEnum;
use App\Enums\RoleEnum;
use App\Filament\Resources\AthleteCategories\Pages\CreateAthleteCategory;
use App\Filament\Resources\AthleteCategories\Pages\EditAthleteCategory;
use App\Filament\Resources\AthleteCategories\Pages\ListAthleteCategories;
use App\Models\AthleteCategory;
use App\Models\Team;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AthleteCategoryResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Team $team;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }

        $this->team = Team::factory()->create();
        $this->admin = User::factory()->create();
        $this->admin->assignRole(RoleEnum::SUPER_ADMIN);
        $this->admin->teams()->attach($this->team);

        $this->actingAs($this->admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::setTenant($this->team);
        Filament::bootCurrentPanel();
    }

    public function test_can_list_athlete_categories(): void
    {
        $categories = AthleteCategory::factory()->count(3)->create();

        Livewire::test(ListAthleteCategories::class)
            ->assertOk()
            ->assertCanSeeTableRecords($categories);
    }

    public function test_can_create_athlete_category(): void
    {
        Livewire::test(CreateAthleteCategory::class)
            ->fillForm([
                'name.sk' => 'Muži do 70kg',
                'gender' => GenderEnum::MALE->value,
                'max_weight' => 70,
            ])
            ->call('create')
            ->assertNotified()
            ->assertRedirect();

        $this->assertDatabaseCount('athlete_categories', 1);
    }

    public function test_can_create_child_category(): void
    {
        $parent = AthleteCategory::factory()->create();

        Livewire::test(CreateAthleteCategory::class)
            ->fillForm([
                'name.sk' => 'Child Category',
                'parent_id' => $parent->id,
            ])
            ->call('create')
            ->assertNotified()
            ->assertRedirect();

        $this->assertDatabaseHas('athlete_categories', [
            'parent_id' => $parent->id,
        ]);
    }

    public function test_can_edit_athlete_category(): void
    {
        $category = AthleteCategory::factory()->create();

        Livewire::test(EditAthleteCategory::class, ['record' => $category->getRouteKey()])
            ->assertOk()
            ->fillForm([
                'name.sk' => 'Updated Category',
            ])
            ->call('save')
            ->assertNotified();

        $category->refresh();
        $this->assertEquals('Updated Category', $category->getTranslation('name', 'sk'));
    }

    public function test_can_delete_athlete_category(): void
    {
        $category = AthleteCategory::factory()->create();

        Livewire::test(EditAthleteCategory::class, ['record' => $category->getRouteKey()])
            ->callAction(DeleteAction::class)
            ->assertNotified();

        $this->assertDatabaseMissing('athlete_categories', ['id' => $category->id]);
    }

    public function test_name_sk_is_required(): void
    {
        Livewire::test(CreateAthleteCategory::class)
            ->fillForm([
                'name.sk' => null,
            ])
            ->call('create')
            ->assertHasFormErrors(['name.sk' => 'required']);
    }
}
