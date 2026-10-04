<?php

namespace Tests\Feature\Filament;

use App\Enums\RoleEnum;
use App\Filament\Resources\Memberships\Pages\ListMemberships;
use App\Models\Membership;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MembershipsTableTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_amount_column_says_free_instead_of_zero(): void
    {
        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }

        $team = Team::factory()->create();
        $free = Membership::factory()->free()->create(['team_id' => $team->id]);
        $paying = Membership::factory()->pending()->create(['team_id' => $team->id, 'fee_amount' => 60.00, 'fee_currency' => 'EUR']);

        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::SUPER_ADMIN);
        $admin->teams()->attach($team);
        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::setTenant($team);
        Filament::bootCurrentPanel();

        Livewire::test(ListMemberships::class)
            ->assertTableColumnFormattedStateSet('fee_amount', 'Zadarmo', $free)
            ->assertTableColumnFormattedStateSet('fee_amount', '60.00 EUR', $paying)
            ->assertTableColumnDoesNotExist('is_free');
    }
}
