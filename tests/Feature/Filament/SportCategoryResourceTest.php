<?php

namespace Tests\Feature\Filament;

use App\Enums\RoleEnum;
use App\Filament\Resources\SportCategories\Pages\CreateSportCategory;
use App\Filament\Resources\SportCategories\Pages\EditSportCategory;
use App\Filament\Resources\SportCategories\Pages\ListSportCategories;
use App\Models\SportCategory;
use App\Models\Team;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SportCategoryResourceTest extends TestCase
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

    public function test_can_list_sport_categories(): void
    {
        $categories = SportCategory::factory()->count(3)->create(['team_id' => $this->team->id]);

        Livewire::test(ListSportCategories::class)
            ->assertOk()
            ->assertCanSeeTableRecords($categories);
    }

    public function test_can_create_sport_category(): void
    {
        Livewire::test(CreateSportCategory::class)
            ->fillForm([
                'name.sk' => 'Parkour',
                'is_active' => true,
            ])
            ->call('create')
            ->assertNotified()
            ->assertRedirect();

        $this->assertDatabaseHas('sport_categories', [
            'team_id' => $this->team->id,
        ]);
    }

    public function test_can_edit_sport_category(): void
    {
        $category = SportCategory::factory()->create(['team_id' => $this->team->id]);

        Livewire::test(EditSportCategory::class, ['record' => $category->getRouteKey()])
            ->assertOk()
            ->fillForm([
                'name.sk' => 'Updated Category',
            ])
            ->call('save')
            ->assertNotified();

        $category->refresh();
        $this->assertEquals('Updated Category', $category->getTranslation('name', 'sk'));
    }

    public function test_can_delete_sport_category(): void
    {
        $category = SportCategory::factory()->create(['team_id' => $this->team->id]);

        Livewire::test(EditSportCategory::class, ['record' => $category->getRouteKey()])
            ->callAction(DeleteAction::class)
            ->assertNotified();

        $this->assertDatabaseMissing('sport_categories', ['id' => $category->id]);
    }
}
