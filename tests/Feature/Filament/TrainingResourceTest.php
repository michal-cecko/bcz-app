<?php

namespace Tests\Feature\Filament;

use App\Enums\RoleEnum;
use App\Enums\TrainingPricingTypeEnum;
use App\Filament\Resources\Trainings\Pages\CreateTraining;
use App\Filament\Resources\Trainings\Pages\EditTraining;
use App\Filament\Resources\Trainings\Pages\ListTrainings;
use App\Filament\Resources\Trainings\RelationManagers\CoachesRelationManager;
use App\Filament\Resources\Trainings\RelationManagers\RegistrationsRelationManager;
use App\Models\SportCategory;
use App\Models\Team;
use App\Models\Training;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TrainingResourceTest extends TestCase
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

    public function test_can_list_trainings(): void
    {
        $sportCategory = SportCategory::factory()->create(['team_id' => $this->team->id]);
        $trainings = Training::factory()->count(3)->create([
            'team_id' => $this->team->id,
            'sport_category_id' => $sportCategory->id,
        ]);

        Livewire::test(ListTrainings::class)
            ->assertOk()
            ->assertCanSeeTableRecords($trainings);
    }

    public function test_can_create_training(): void
    {
        $sportCategory = SportCategory::factory()->create(['team_id' => $this->team->id]);

        Livewire::test(CreateTraining::class)
            ->fillForm([
                'title.sk' => 'Nový Tréning',
                'sport_category_id' => $sportCategory->id,
                'pricing_type' => TrainingPricingTypeEnum::FREE->value,
                'is_active' => true,
            ])
            ->call('create')
            ->assertNotified()
            ->assertRedirect();

        $this->assertDatabaseHas('trainings', [
            'team_id' => $this->team->id,
            'sport_category_id' => $sportCategory->id,
        ]);
    }

    public function test_can_edit_training(): void
    {
        $sportCategory = SportCategory::factory()->create(['team_id' => $this->team->id]);
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'sport_category_id' => $sportCategory->id,
        ]);

        Livewire::test(EditTraining::class, ['record' => $training->getRouteKey()])
            ->assertOk()
            ->fillForm([
                'title.sk' => 'Upravený Tréning',
            ])
            ->call('save')
            ->assertNotified();

        $training->refresh();
        $this->assertEquals('Upravený Tréning', $training->getTranslation('title', 'sk'));
    }

    public function test_can_delete_training(): void
    {
        $sportCategory = SportCategory::factory()->create(['team_id' => $this->team->id]);
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'sport_category_id' => $sportCategory->id,
        ]);

        Livewire::test(EditTraining::class, ['record' => $training->getRouteKey()])
            ->callAction(DeleteAction::class)
            ->assertNotified();

        $this->assertSoftDeleted($training);
    }

    public function test_sport_category_is_required(): void
    {
        Livewire::test(CreateTraining::class)
            ->fillForm([
                'title.sk' => 'Test',
                'sport_category_id' => null,
                'pricing_type' => TrainingPricingTypeEnum::FREE->value,
            ])
            ->call('create')
            ->assertHasFormErrors(['sport_category_id' => 'required']);
    }

    public function test_coaches_relation_manager_loads(): void
    {
        $sportCategory = SportCategory::factory()->create(['team_id' => $this->team->id]);
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'sport_category_id' => $sportCategory->id,
        ]);

        Livewire::test(EditTraining::class, ['record' => $training->getRouteKey()])
            ->assertSeeLivewire(CoachesRelationManager::class);
    }

    public function test_registrations_relation_manager_loads(): void
    {
        $sportCategory = SportCategory::factory()->create(['team_id' => $this->team->id]);
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'sport_category_id' => $sportCategory->id,
        ]);

        Livewire::test(RegistrationsRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => EditTraining::class,
        ])->assertOk();
    }
}
