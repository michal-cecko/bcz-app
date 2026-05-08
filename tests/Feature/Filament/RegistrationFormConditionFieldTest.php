<?php

namespace Tests\Feature\Filament;

use App\Enums\RegistrationFieldTypeEnum;
use App\Enums\RoleEnum;
use App\Filament\Resources\Trainings\Pages\EditTraining;
use App\Models\Team;
use App\Models\Training;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RegistrationFormConditionFieldTest extends TestCase
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

    public function test_condition_field_options_include_existing_saved_fields(): void
    {
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'registration_form_schema' => [
                [
                    'name' => 'first_name',
                    'type' => RegistrationFieldTypeEnum::FIRST_NAME->value,
                    'label' => ['sk' => 'Meno'],
                    'required' => true,
                    'has_condition' => false,
                ],
                [
                    'name' => 'gender',
                    'type' => RegistrationFieldTypeEnum::SELECT->value,
                    'label' => ['sk' => 'Pohlavie'],
                    'required' => true,
                    'has_condition' => false,
                    'options' => [
                        ['value' => 'male', 'label' => ['sk' => 'Muž']],
                        ['value' => 'female', 'label' => ['sk' => 'Žena']],
                    ],
                ],
                [
                    'name' => 'pregnancy_week',
                    'type' => RegistrationFieldTypeEnum::NUMBER_INPUT->value,
                    'label' => ['sk' => 'Týždeň tehotenstva'],
                    'required' => false,
                    'has_condition' => true,
                    'condition_field' => 'gender',
                    'condition_values' => ['female'],
                ],
            ],
        ]);

        $component = Livewire::test(EditTraining::class, ['record' => $training->id])
            ->assertOk();

        // The Repeater state should round-trip through the form.
        $data = $component->get('data');
        $this->assertIsArray($data['registration_form_schema'] ?? null);
        $this->assertCount(3, $data['registration_form_schema']);

        // Each item should retain its name and label so the condition_field
        // dropdown can compose its option list.
        $names = collect($data['registration_form_schema'])->pluck('name')->all();
        $this->assertSame(['first_name', 'gender', 'pregnancy_week'], $names);
    }
}
