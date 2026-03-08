<?php

namespace Tests\Feature\Filament;

use App\Enums\RoleEnum;
use App\Filament\Resources\Disciplines\Pages\CreateDiscipline;
use App\Filament\Resources\Disciplines\Pages\EditDiscipline;
use App\Filament\Resources\Disciplines\Pages\ListDisciplines;
use App\Models\Discipline;
use App\Models\Team;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DisciplineResourceTest extends TestCase
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

    public function test_can_list_disciplines(): void
    {
        $disciplines = Discipline::factory()->count(3)->create();

        Livewire::test(ListDisciplines::class)
            ->assertOk()
            ->assertCanSeeTableRecords($disciplines);
    }

    public function test_can_create_discipline(): void
    {
        Livewire::test(CreateDiscipline::class)
            ->fillForm([
                'name.sk' => 'Statics',
            ])
            ->call('create')
            ->assertNotified()
            ->assertRedirect();

        $this->assertDatabaseCount('disciplines', 1);
    }

    public function test_can_edit_discipline(): void
    {
        $discipline = Discipline::factory()->create();

        Livewire::test(EditDiscipline::class, ['record' => $discipline->getRouteKey()])
            ->assertOk()
            ->fillForm([
                'name.sk' => 'Updated Discipline',
            ])
            ->call('save')
            ->assertNotified();

        $discipline->refresh();
        $this->assertEquals('Updated Discipline', $discipline->getTranslation('name', 'sk'));
    }

    public function test_can_delete_discipline(): void
    {
        $discipline = Discipline::factory()->create();

        Livewire::test(EditDiscipline::class, ['record' => $discipline->getRouteKey()])
            ->callAction(DeleteAction::class)
            ->assertNotified();

        $this->assertDatabaseMissing('disciplines', ['id' => $discipline->id]);
    }

    public function test_name_sk_is_required(): void
    {
        Livewire::test(CreateDiscipline::class)
            ->fillForm([
                'name.sk' => null,
            ])
            ->call('create')
            ->assertHasFormErrors(['name.sk' => 'required']);
    }
}
