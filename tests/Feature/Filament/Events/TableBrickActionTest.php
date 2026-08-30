<?php

namespace Tests\Feature\Filament\Events;

use App\Enums\RoleEnum;
use App\Filament\Resources\Events\Pages\EditEvent;
use App\Models\Event;
use App\Models\Team;
use App\Models\User;
use Awcodes\Mason\Actions\BrickAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TableBrickActionTest extends TestCase
{
    use RefreshDatabase;

    protected Event $event;

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

        $this->event = Event::factory()->create(['team_id' => $team->id]);
    }

    public function test_table_brick_action_opens_without_error(): void
    {
        $this->withoutExceptionHandling();

        Livewire::test(EditEvent::class, ['record' => $this->event->id])
            ->assertOk()
            ->mountFormComponentAction('content.sk', BrickAction::NAME, arguments: [
                'id' => 'table',
                'mode' => 'insert',
            ])
            ->assertFormComponentActionExists('content.sk', BrickAction::NAME);
    }

    public function test_table_brick_action_renders_existing_data_for_editing(): void
    {
        $this->withoutExceptionHandling();

        $config = [
            'headers' => [
                ['label' => ['sk' => 'Stĺpec 1', 'en' => 'Column 1', 'cs' => 'Sloupec 1']],
            ],
            'rows' => [
                [
                    'cells' => [
                        ['value' => ['sk' => 'Hodnota 1', 'en' => 'Value 1', 'cs' => 'Hodnota 1']],
                    ],
                ],
            ],
        ];

        Livewire::test(EditEvent::class, ['record' => $this->event->id])
            ->assertOk()
            ->mountFormComponentAction('content.sk', BrickAction::NAME, arguments: [
                'id' => 'table',
                'mode' => 'edit',
                'blockIndex' => 0,
                'config' => $config,
            ])
            ->callMountedFormComponentAction()
            ->assertHasNoFormComponentActionErrors();
    }
}
