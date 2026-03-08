<?php

namespace Tests\Feature\Filament;

use App\Enums\RoleEnum;
use App\Filament\Resources\FaqCategories\Pages\CreateFaqCategory;
use App\Filament\Resources\FaqCategories\Pages\EditFaqCategory;
use App\Filament\Resources\FaqCategories\Pages\ListFaqCategories;
use App\Models\FaqCategory;
use App\Models\Team;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FaqCategoryResourceTest extends TestCase
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

    public function test_can_list_faq_categories(): void
    {
        $categories = FaqCategory::factory()->count(3)->create();

        Livewire::test(ListFaqCategories::class)
            ->assertOk()
            ->assertCanSeeTableRecords($categories);
    }

    public function test_can_create_faq_category(): void
    {
        Livewire::test(CreateFaqCategory::class)
            ->fillForm([
                'title.sk' => 'Test Category',
                'color' => '#6366f1',
            ])
            ->call('create')
            ->assertNotified()
            ->assertRedirect();

        $this->assertDatabaseHas('faq_categories', [
            'color' => '#6366f1',
        ]);
    }

    public function test_can_edit_faq_category(): void
    {
        $category = FaqCategory::factory()->create();

        Livewire::test(EditFaqCategory::class, ['record' => $category->getRouteKey()])
            ->assertOk()
            ->fillForm([
                'title.sk' => 'Updated Category',
            ])
            ->call('save')
            ->assertNotified();

        $category->refresh();
        $this->assertEquals('Updated Category', $category->getTranslation('title', 'sk'));
    }

    public function test_can_delete_faq_category(): void
    {
        $category = FaqCategory::factory()->create();

        Livewire::test(EditFaqCategory::class, ['record' => $category->getRouteKey()])
            ->callAction(DeleteAction::class)
            ->assertNotified();

        $this->assertDatabaseMissing('faq_categories', ['id' => $category->id]);
    }
}
