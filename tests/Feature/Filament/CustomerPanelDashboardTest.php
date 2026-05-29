<?php

namespace Tests\Feature\Filament;

use App\Enums\RoleEnum;
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\MemberEvents;
use App\Filament\Pages\MemberMembership;
use App\Filament\Pages\MemberPayments;
use App\Filament\Pages\MyTrainings;
use App\Filament\Widgets\RecentPaymentsWidget;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Payment;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Exercises the tenant-free customer panel for a teamless customer — the area
 * that previously hid a customer's own events/payments because its queries
 * scoped to Filament::getTenant(), which is null here.
 */
class CustomerPanelDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }

        Filament::setCurrentPanel(Filament::getPanel('customer'));
        Filament::bootCurrentPanel();
    }

    /** A teamless customer, as created from public event/training registration. */
    protected function actingAsCustomer(): User
    {
        $customer = User::factory()->create();
        $customer->assignRole(RoleEnum::CUSTOMER);
        $this->actingAs($customer);

        return $customer;
    }

    public function test_member_events_lists_the_customers_registration(): void
    {
        $customer = $this->actingAsCustomer();

        $registered = Event::factory()->create([
            'is_published' => true,
            'date' => now()->addWeek(),
            'title' => ['sk' => 'Moje zaregistrovane podujatie'],
        ]);
        EventRegistration::factory()->create([
            'event_id' => $registered->id,
            'user_id' => $customer->id,
            'status' => 'pending',
        ]);

        $notRegistered = Event::factory()->create([
            'is_published' => true,
            'date' => now()->addWeek(),
            'title' => ['sk' => 'Cudzie podujatie'],
        ]);

        Livewire::test(MemberEvents::class)
            ->assertOk()
            ->assertSee('Moje zaregistrovane podujatie')
            ->assertDontSee('Cudzie podujatie');
    }

    public function test_member_events_past_tab_shows_past_registrations(): void
    {
        $customer = $this->actingAsCustomer();

        $past = Event::factory()->create([
            'is_published' => true,
            'date' => now()->subMonth(),
            'title' => ['sk' => 'Minule podujatie'],
        ]);
        EventRegistration::factory()->create([
            'event_id' => $past->id,
            'user_id' => $customer->id,
            'status' => 'approved',
        ]);

        Livewire::test(MemberEvents::class)
            ->set('tab', 'past')
            ->assertOk()
            ->assertSee('Minule podujatie');
    }

    public function test_member_payments_lists_the_customers_payment_and_hides_others(): void
    {
        $customer = $this->actingAsCustomer();

        $mine = Payment::factory()->create(['user_id' => $customer->id]);
        $someoneElses = Payment::factory()->create();

        Livewire::test(MemberPayments::class)
            ->assertOk()
            ->assertCanSeeTableRecords([$mine])
            ->assertCanNotSeeTableRecords([$someoneElses]);
    }

    public function test_recent_payments_widget_shows_the_customers_payment(): void
    {
        $customer = $this->actingAsCustomer();

        $mine = Payment::factory()->create(['user_id' => $customer->id]);
        $someoneElses = Payment::factory()->create();

        Livewire::test(RecentPaymentsWidget::class)
            ->assertOk()
            ->assertCanSeeTableRecords([$mine])
            ->assertCanNotSeeTableRecords([$someoneElses]);
    }

    public function test_member_pages_render_for_teamless_customer(): void
    {
        $this->actingAsCustomer();

        Livewire::test(MemberEvents::class)->assertOk();
        Livewire::test(MemberPayments::class)->assertOk();
        Livewire::test(MyTrainings::class)->assertOk();
        Livewire::test(MemberMembership::class)->assertOk();
        Livewire::test(Dashboard::class)->assertOk();
    }

    public function test_membership_page_prompts_teamless_customer_to_join_a_team(): void
    {
        $this->actingAsCustomer();

        Livewire::test(MemberMembership::class)
            ->assertOk()
            ->assertSee(__('member.membership.no_team_heading'));
    }

    public function test_event_not_registered_for_is_hidden_even_when_published(): void
    {
        $this->actingAsCustomer();

        Event::factory()->create([
            'is_published' => true,
            'date' => now()->addWeek(),
            'title' => ['sk' => 'Verejne podujatie bez registracie'],
        ]);

        Livewire::test(MemberEvents::class)
            ->assertOk()
            ->assertDontSee('Verejne podujatie bez registracie');
    }
}
