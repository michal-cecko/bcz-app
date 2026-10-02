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

class RegistrationFormHelperTextNestingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }

        $team = Team::factory()->create();
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::SUPER_ADMIN);
        $admin->teams()->attach($team);

        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::setTenant($team);
        Filament::bootCurrentPanel();
    }

    public function test_editing_a_link_inside_a_helper_text_list_is_accepted(): void
    {
        $training = Training::factory()->create([
            'team_id' => Filament::getTenant()->id,
            'registration_form_schema' => [
                [
                    'name' => 'note',
                    'type' => RegistrationFieldTypeEnum::TEXT_INPUT->value,
                    'label' => ['sk' => 'Poznámka'],
                    'helper_text' => ['sk' => '<ul><li><p><a href="https://bcz-club.com">Pravidlá</a></p></li></ul>'],
                    'required' => false,
                    'has_condition' => false,
                ],
            ],
        ]);

        $component = Livewire::test(EditTraining::class, ['record' => $training->id]);
        $itemKey = array_key_first($component->get('data.registration_form_schema'));

        // doc > bulletList > listItem > paragraph > text > link mark: 17 path segments.
        $component
            ->set("data.registration_form_schema.{$itemKey}.helper_text.sk.content.0.content.0.content.0.content.0.marks.0.attrs.target", '_blank')
            ->assertOk();
    }
}
