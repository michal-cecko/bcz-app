<?php

namespace Tests\Feature\Filament;

use App\Enums\RegistrationStatusEnum;
use App\Enums\RoleEnum;
use App\Enums\TrainingPricingTypeEnum;
use App\Filament\Resources\Trainings\Pages\CreateTraining;
use App\Filament\Resources\Trainings\Pages\EditTraining;
use App\Filament\Resources\Trainings\Pages\ListTrainings;
use App\Filament\Resources\Trainings\RelationManagers\CoachesRelationManager;
use App\Filament\Resources\Trainings\RelationManagers\RegistrationsRelationManager;
use App\Models\City;
use App\Models\SportCategory;
use App\Models\Team;
use App\Models\Training;
use App\Models\TrainingRegistration;
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
        $city = City::factory()->create();

        Livewire::test(CreateTraining::class)
            ->fillForm($this->validTrainingFormData($sportCategory->id, $city->id))
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

    public function test_map_is_centred_on_the_stored_coordinates_when_editing(): void
    {
        $sportCategory = SportCategory::factory()->create(['team_id' => $this->team->id]);
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'sport_category_id' => $sportCategory->id,
            'place_address' => 'M. R. Štefánika 2007/14, 022 01 Čadca, Slovensko',
            'latitude' => 49.4305426,
            'longitude' => 18.7895393,
        ]);

        Livewire::test(EditTraining::class, ['record' => $training->getRouteKey()])
            ->assertOk()
            ->assertFormSet([
                'location' => ['lat' => 49.4305426, 'lng' => 18.7895393],
            ]);
    }

    public function test_untouched_map_does_not_store_its_default_location_as_coordinates(): void
    {
        $sportCategory = SportCategory::factory()->create(['team_id' => $this->team->id]);
        $city = City::factory()->create();

        Livewire::test(CreateTraining::class)
            ->fillForm([
                ...$this->validTrainingFormData($sportCategory->id, $city->id),
                // The map component writes its default centre back into the form state
                // as soon as it boots without coordinates, without any user interaction.
                'location' => ['lat' => 48.1486, 'lng' => 17.1077],
            ])
            ->call('create')
            ->assertNotified()
            ->assertRedirect();

        $training = Training::query()->where('sport_category_id', $sportCategory->id)->sole();

        $this->assertNull($training->latitude);
        $this->assertNull($training->longitude);
    }

    public function test_picking_a_location_on_the_map_stores_its_coordinates(): void
    {
        $sportCategory = SportCategory::factory()->create(['team_id' => $this->team->id]);
        $city = City::factory()->create();

        Livewire::test(CreateTraining::class)
            ->fillForm([
                ...$this->validTrainingFormData($sportCategory->id, $city->id),
                'location' => ['lat' => 49.4305426, 'lng' => 18.7895393],
            ])
            ->call('create')
            ->assertNotified()
            ->assertRedirect();

        $training = Training::query()->where('sport_category_id', $sportCategory->id)->sole();

        $this->assertEquals(49.4305426, (float) $training->latitude);
        $this->assertEquals(18.7895393, (float) $training->longitude);
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

    public function test_attach_coach_action_options_only_include_team_admins_and_coaches(): void
    {
        $sportCategory = SportCategory::factory()->create(['team_id' => $this->team->id]);
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'sport_category_id' => $sportCategory->id,
        ]);

        $coach = User::factory()->create();
        $coach->teams()->attach($this->team->id, ['role' => RoleEnum::COACH->value]);

        $teamAdmin = User::factory()->create();
        $teamAdmin->teams()->attach($this->team->id, ['role' => RoleEnum::TEAM_ADMIN->value]);

        // No explicit attach: creating a User while a Filament tenant is active
        // auto-syncs it to the tenant with the default (ATHLETE) pivot role, via
        // Filament's tenant-ownership model-creation hook. This user must NOT
        // appear in the "attach coach" options, since it holds no TEAM_ADMIN/COACH role.
        $athlete = User::factory()->create();

        $test = Livewire::test(CoachesRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => EditTraining::class,
        ])
            ->mountTableAction('attach')
            ->assertTableActionMounted('attach');

        // Reach into the mounted action's schema to read the actual resolved
        // options of the "Tréner" select, rather than asserting on rendered
        // HTML (searchable selects don't inline their full option list as
        // plain text). This is the exact code path that previously blew up
        // in production with `SQLSTATE[42703]: column "pivot_in" does not
        // exist` — before the fix, mounting this action throws instead of
        // reaching this assertion at all.
        $userIdField = $test->instance()->getMountedTableActionForm()->getComponent('user_id');

        $this->assertNotNull($userIdField);
        $this->assertEqualsCanonicalizing(
            [$coach->id, $teamAdmin->id],
            array_keys($userIdField->getOptions())
        );
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

    public function test_registrations_relation_manager_filters_by_status(): void
    {
        $sportCategory = SportCategory::factory()->create(['team_id' => $this->team->id]);
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'sport_category_id' => $sportCategory->id,
        ]);

        $approved = TrainingRegistration::factory()->approved()->create(['training_id' => $training->id]);
        $pending = TrainingRegistration::factory()->pending()->create(['training_id' => $training->id]);

        Livewire::test(RegistrationsRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => EditTraining::class,
        ])
            ->filterTable('status', RegistrationStatusEnum::Approved->value)
            ->assertCanSeeTableRecords([$approved])
            ->assertCanNotSeeTableRecords([$pending]);
    }

    public function test_registrations_relation_manager_sorts_by_status(): void
    {
        $sportCategory = SportCategory::factory()->create(['team_id' => $this->team->id]);
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'sport_category_id' => $sportCategory->id,
        ]);

        $approved = TrainingRegistration::factory()->approved()->create(['training_id' => $training->id]);
        $pending = TrainingRegistration::factory()->pending()->create(['training_id' => $training->id]);

        Livewire::test(RegistrationsRelationManager::class, [
            'ownerRecord' => $training,
            'pageClass' => EditTraining::class,
        ])
            ->sortTable('status')
            ->assertCanSeeTableRecords([$approved, $pending], inOrder: true)
            ->sortTable('status', 'desc')
            ->assertCanSeeTableRecords([$pending, $approved], inOrder: true);
    }

    public function test_create_form_offers_every_section_in_the_default_order(): void
    {
        $state = Livewire::test(CreateTraining::class)
            ->assertOk()
            ->get('data.section_order');

        $this->assertSame(
            Training::DEFAULT_SECTION_ORDER,
            array_column($state, 'key'),
            'A new training must start from the order the page shipped with.'
        );
    }

    public function test_sections_can_be_reordered_from_the_edit_form(): void
    {
        $sportCategory = SportCategory::factory()->create(['team_id' => $this->team->id]);
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'sport_category_id' => $sportCategory->id,
        ]);

        $component = Livewire::test(EditTraining::class, ['record' => $training->getRouteKey()])->assertOk();

        $hydrated = $component->get('data.section_order');

        $this->assertSame(
            Training::DEFAULT_SECTION_ORDER,
            array_column($hydrated, 'key'),
            'A training that has never been touched must still offer every section to drag.'
        );

        // Drag the gallery and the coaches below the registration form.
        $reordered = [];

        foreach (['info', 'location', 'registration', 'gallery', 'coaches'] as $key) {
            $uuid = collect($hydrated)->search(fn (array $item): bool => $item['key'] === $key);
            $reordered[$uuid] = $hydrated[$uuid];
        }

        $component->set('data.section_order', $reordered)
            ->call('save')
            ->assertNotified()
            ->assertHasNoFormErrors();

        $this->assertSame(
            ['info', 'location', 'registration', 'gallery', 'coaches'],
            $training->refresh()->section_order
        );
    }

    /** @return array<string, mixed> */
    private function validTrainingFormData(string $sportCategoryId, string $cityId): array
    {
        return [
            'title.sk' => 'Nový Tréning',
            'sport_category_id' => $sportCategoryId,
            'city_id' => $cityId,
            'pricing_type' => TrainingPricingTypeEnum::FREE->value,
            'is_active' => true,
            'registration_form_schema' => [
                ['name' => 'meno', 'type' => 'first_name', 'label' => ['sk' => 'Meno', 'en' => 'First name', 'cs' => 'Jméno'], 'required' => true, 'width' => 'half'],
                ['name' => 'priezvisko', 'type' => 'last_name', 'label' => ['sk' => 'Priezvisko', 'en' => 'Last name', 'cs' => 'Příjmení'], 'required' => true, 'width' => 'half'],
                ['name' => 'email', 'type' => 'email', 'label' => ['sk' => 'Email', 'en' => 'Email', 'cs' => 'Email'], 'required' => true, 'width' => 'half'],
                ['name' => 'telefon', 'type' => 'phone', 'label' => ['sk' => 'Telefón', 'en' => 'Phone', 'cs' => 'Telefon'], 'required' => true, 'width' => 'half'],
            ],
        ];
    }
}
