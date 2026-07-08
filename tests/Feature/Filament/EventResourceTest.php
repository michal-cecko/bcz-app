<?php

namespace Tests\Feature\Filament;

use App\Enums\RegistrationStatusEnum;
use App\Enums\RoleEnum;
use App\Filament\Resources\Events\Pages\CreateEvent;
use App\Filament\Resources\Events\Pages\EditEvent;
use App\Filament\Resources\Events\Pages\ListEvents;
use App\Filament\Resources\Events\RelationManagers\RegistrationsRelationManager;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventRegistration;
use App\Models\Team;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EventResourceTest extends TestCase
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

    public function test_can_list_events(): void
    {
        $category = EventCategory::factory()->create();
        $events = Event::factory()->count(3)->create([
            'team_id' => $this->team->id,
            'event_category_id' => $category->id,
        ]);

        Livewire::test(ListEvents::class)
            ->assertOk()
            ->assertCanSeeTableRecords($events);
    }

    public function test_can_create_event(): void
    {
        $category = EventCategory::factory()->create();

        Livewire::test(CreateEvent::class)
            ->fillForm([
                'title.sk' => 'Nový Event',
                'event_category_id' => $category->id,
                'date' => '2026-06-15',
            ])
            ->call('create')
            ->assertNotified()
            ->assertRedirect();

        $this->assertDatabaseHas('events', [
            'team_id' => $this->team->id,
            'event_category_id' => $category->id,
        ]);
    }

    public function test_can_edit_event(): void
    {
        $category = EventCategory::factory()->create();
        $event = Event::factory()->create([
            'team_id' => $this->team->id,
            'event_category_id' => $category->id,
        ]);

        Livewire::test(EditEvent::class, ['record' => $event->getRouteKey()])
            ->assertOk()
            ->fillForm([
                'title.sk' => 'Upravený Event',
            ])
            ->call('save')
            ->assertNotified();

        $event->refresh();
        $this->assertEquals('Upravený Event', $event->getTranslation('title', 'sk'));
    }

    public function test_can_delete_event(): void
    {
        $category = EventCategory::factory()->create();
        $event = Event::factory()->create([
            'team_id' => $this->team->id,
            'event_category_id' => $category->id,
        ]);

        Livewire::test(EditEvent::class, ['record' => $event->getRouteKey()])
            ->callAction(DeleteAction::class)
            ->assertNotified();

        $this->assertSoftDeleted($event);
    }

    public function test_event_category_is_required(): void
    {
        Livewire::test(CreateEvent::class)
            ->fillForm([
                'title.sk' => 'Test Event',
                'event_category_id' => null,
                'date' => '2026-06-15',
            ])
            ->call('create')
            ->assertHasFormErrors(['event_category_id' => 'required']);
    }

    public function test_date_is_required(): void
    {
        $category = EventCategory::factory()->create();

        Livewire::test(CreateEvent::class)
            ->fillForm([
                'title.sk' => 'Test Event',
                'event_category_id' => $category->id,
                'date' => null,
            ])
            ->call('create')
            ->assertHasFormErrors(['date' => 'required']);
    }

    public function test_registrations_relation_manager_filters_by_status(): void
    {
        $event = Event::factory()->competition()->create([
            'team_id' => $this->team->id,
            'event_category_id' => EventCategory::factory()->create()->id,
        ]);

        $approved = EventRegistration::factory()->approved()->create(['event_id' => $event->id]);
        $pending = EventRegistration::factory()->create(['event_id' => $event->id]);

        Livewire::test(RegistrationsRelationManager::class, [
            'ownerRecord' => $event,
            'pageClass' => EditEvent::class,
        ])
            ->filterTable('status', RegistrationStatusEnum::Approved->value)
            ->assertCanSeeTableRecords([$approved])
            ->assertCanNotSeeTableRecords([$pending]);
    }

    public function test_registrations_relation_manager_filters_by_weight_range(): void
    {
        $event = Event::factory()->competition()->create([
            'team_id' => $this->team->id,
            'event_category_id' => EventCategory::factory()->create()->id,
        ]);

        $light = EventRegistration::factory()->create(['event_id' => $event->id, 'weight_in' => 60]);
        $heavy = EventRegistration::factory()->create(['event_id' => $event->id, 'weight_in' => 90]);

        Livewire::test(RegistrationsRelationManager::class, [
            'ownerRecord' => $event,
            'pageClass' => EditEvent::class,
        ])
            ->filterTable('weight_in', ['weight_from' => 80, 'weight_to' => 100])
            ->assertCanSeeTableRecords([$heavy])
            ->assertCanNotSeeTableRecords([$light]);
    }

    public function test_registrations_relation_manager_sorts_by_weight(): void
    {
        $event = Event::factory()->competition()->create([
            'team_id' => $this->team->id,
            'event_category_id' => EventCategory::factory()->create()->id,
        ]);

        $light = EventRegistration::factory()->create(['event_id' => $event->id, 'weight_in' => 60]);
        $heavy = EventRegistration::factory()->create(['event_id' => $event->id, 'weight_in' => 90]);

        Livewire::test(RegistrationsRelationManager::class, [
            'ownerRecord' => $event,
            'pageClass' => EditEvent::class,
        ])
            ->sortTable('weight_in')
            ->assertCanSeeTableRecords([$light, $heavy], inOrder: true)
            ->sortTable('weight_in', 'desc')
            ->assertCanSeeTableRecords([$heavy, $light], inOrder: true);
    }
}
