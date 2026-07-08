<?php

namespace Tests\Feature\Filament;

use App\Enums\InquiryStatusEnum;
use App\Enums\RoleEnum;
use App\Filament\Resources\Inquiries\Pages\ListInquiries;
use App\Filament\Resources\Inquiries\Pages\ViewInquiry;
use App\Models\Inquiry;
use App\Models\Team;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InquiryResourceTest extends TestCase
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

    public function test_can_list_inquiries(): void
    {
        $inquiries = Inquiry::factory()->count(3)->create(['team_id' => $this->team->id]);

        Livewire::test(ListInquiries::class)
            ->assertOk()
            ->assertCanSeeTableRecords($inquiries);
    }

    public function test_can_view_inquiry_and_marks_as_in_progress(): void
    {
        $inquiry = Inquiry::factory()->create([
            'team_id' => $this->team->id,
            'status' => InquiryStatusEnum::NEW,
        ]);

        $this->assertEquals(InquiryStatusEnum::NEW, $inquiry->status);

        Livewire::test(ViewInquiry::class, ['record' => $inquiry->getRouteKey()])
            ->assertOk();

        $inquiry->refresh();
        $this->assertEquals(InquiryStatusEnum::IN_PROGRESS, $inquiry->status);
    }

    public function test_can_delete_inquiry(): void
    {
        $inquiry = Inquiry::factory()->create(['team_id' => $this->team->id]);

        Livewire::test(ViewInquiry::class, ['record' => $inquiry->getRouteKey()])
            ->callAction(DeleteAction::class)
            ->assertNotified();

        $this->assertDatabaseMissing('inquiries', ['id' => $inquiry->id]);
    }

    /**
     * Mounting the bulk e-mail action builds the recipient placeholder, which reads
     * the selected records. Before the fix this called the non-existent getRecords()
     * and threw BadMethodCallException the moment the slide-over opened.
     */
    public function test_bulk_email_action_mounts_and_lists_selected_recipients(): void
    {
        $inquiries = Inquiry::factory()->count(2)->create(['team_id' => $this->team->id]);

        Livewire::test(ListInquiries::class)
            ->mountTableBulkAction('send_email_bulk', $inquiries)
            ->assertActionMounted(TestAction::make('send_email_bulk')->table()->bulk())
            ->assertSee($inquiries[0]->email)
            ->assertSee($inquiries[1]->email);
    }
}
