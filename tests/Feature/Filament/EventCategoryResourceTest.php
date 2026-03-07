<?php

namespace Tests\Feature\Filament;

use App\Enums\RoleEnum;
use App\Filament\Resources\EventCategories\Pages\CreateEventCategory;
use App\Filament\Resources\EventCategories\Pages\EditEventCategory;
use App\Filament\Resources\EventCategories\Pages\ListEventCategories;
use App\Models\EventCategory;
use App\Models\Team;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EventCategoryResourceTest extends TestCase
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

    public function test_can_list_event_categories(): void
    {
        $categories = EventCategory::factory()->count(3)->create();

        Livewire::test(ListEventCategories::class)
            ->assertOk()
            ->assertCanSeeTableRecords($categories);
    }

    public function test_can_create_event_category(): void
    {
        Livewire::test(CreateEventCategory::class)
            ->fillForm([
                'title.sk' => 'Vystúpenia',
                'is_active' => true,
            ])
            ->call('create')
            ->assertNotified()
            ->assertRedirect();

        $this->assertDatabaseHas('event_categories', [
            'slug' => 'vystupenia',
        ]);
    }

    public function test_can_edit_event_category(): void
    {
        $category = EventCategory::factory()->create();

        Livewire::test(EditEventCategory::class, ['record' => $category->getRouteKey()])
            ->assertOk()
            ->fillForm([
                'title.sk' => 'Updated Title',
            ])
            ->call('save')
            ->assertNotified();

        $category->refresh();
        $this->assertEquals('Updated Title', $category->getTranslation('title', 'sk'));
    }

    public function test_can_delete_event_category(): void
    {
        $category = EventCategory::factory()->create();

        Livewire::test(EditEventCategory::class, ['record' => $category->getRouteKey()])
            ->callAction(DeleteAction::class)
            ->assertNotified();

        $this->assertDatabaseMissing('event_categories', ['id' => $category->id]);
    }

    public function test_title_sk_is_required(): void
    {
        Livewire::test(CreateEventCategory::class)
            ->fillForm([
                'title.sk' => null,
            ])
            ->call('create')
            ->assertHasFormErrors(['title.sk' => 'required']);
    }
}
