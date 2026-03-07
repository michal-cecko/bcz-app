<?php

namespace Tests\Feature\Filament;

use App\Enums\RoleEnum;
use App\Filament\Resources\Sponsors\Pages\CreateSponsor;
use App\Filament\Resources\Sponsors\Pages\EditSponsor;
use App\Filament\Resources\Sponsors\Pages\ListSponsors;
use App\Models\Sponsor;
use App\Models\Team;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SponsorResourceTest extends TestCase
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

    public function test_can_list_sponsors(): void
    {
        $sponsors = Sponsor::factory()->count(3)->create();

        Livewire::test(ListSponsors::class)
            ->assertOk()
            ->assertCanSeeTableRecords($sponsors);
    }

    public function test_can_create_sponsor(): void
    {
        Livewire::test(CreateSponsor::class)
            ->fillForm([
                'name' => 'Test Sponsor',
                'tag' => 'main_sponsor',
                'is_visible' => true,
            ])
            ->call('create')
            ->assertNotified()
            ->assertRedirect();

        $this->assertDatabaseHas('sponsors', [
            'name' => 'Test Sponsor',
        ]);
    }

    public function test_can_edit_sponsor(): void
    {
        $sponsor = Sponsor::factory()->create();

        Livewire::test(EditSponsor::class, ['record' => $sponsor->getRouteKey()])
            ->assertOk()
            ->fillForm([
                'name' => 'Updated Sponsor',
            ])
            ->call('save')
            ->assertNotified();

        $sponsor->refresh();
        $this->assertEquals('Updated Sponsor', $sponsor->name);
    }

    public function test_can_delete_sponsor(): void
    {
        $sponsor = Sponsor::factory()->create();

        Livewire::test(EditSponsor::class, ['record' => $sponsor->getRouteKey()])
            ->callAction(DeleteAction::class)
            ->assertNotified();

        $this->assertDatabaseMissing('sponsors', ['id' => $sponsor->id]);
    }
}
