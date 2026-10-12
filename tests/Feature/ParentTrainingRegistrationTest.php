<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Filament\Pages\MemberMembership;
use App\Filament\Pages\MemberPayments;
use App\Filament\Pages\MyFamily;
use App\Filament\Pages\MyTrainings;
use App\Models\Member;
use App\Models\Membership;
use App\Models\Team;
use App\Models\Training;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ParentTrainingRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['family.enabled' => true]);
        Notification::fake();
        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }
        Filament::setCurrentPanel(Filament::getPanel('customer'));
    }

    private function customer(bool $isParent = false): User
    {
        $user = User::factory()->create(['is_parent' => $isParent, 'password_set_at' => now()]);
        $user->assignRole(RoleEnum::CUSTOMER);

        return $user;
    }

    public function test_normal_customer_sees_the_selected_profile_without_family_management_or_a_fee(): void
    {
        $user = $this->customer();
        Livewire::actingAs($user)->test('training-registration-form', ['training' => Training::factory()->free()->create()])
            ->assertSet('memberId', fn ($id) => $id === $user->members()->sole()->id)
            ->assertSeeHtml('id="family-member"')->assertSee($user->members()->sole()->name)
            ->assertDontSee(__('family.add'));

        $this->assertFalse(MyFamily::canAccess());
        $this->assertTrue(MemberMembership::shouldRegisterNavigation());
        $this->assertTrue(MemberPayments::shouldRegisterNavigation());
        $this->assertSame(1, $user->members()->count());
        $this->assertSame(0, Membership::count());
        $this->get(MyFamily::getUrl())->assertForbidden();
    }

    public function test_parent_adds_two_children_without_creating_a_parent_participant(): void
    {
        $user = $this->customer(true);
        Livewire::actingAs($user)->test('training-registration-form', ['training' => Training::factory()->free()->create()])
            ->assertSet('memberId', null)->assertSeeHtml('id="family-member"')
            ->assertSee(__('family.empty'))->assertSee(__('family.add'))
            ->call('submit')->assertHasErrors(['memberId' => 'required']);
        $this->assertSame(0, Member::count());

        $page = Livewire::test(MyFamily::class);
        foreach (['Anna', 'Peter'] as $name) {
            $page->callAction(TestAction::make('addMember')->table(), [
                'first_name' => $name, 'last_name' => 'Child', 'birth_date' => '2015-01-01',
            ])->assertHasNoActionErrors();
        }
        Livewire::test('training-registration-form', ['training' => Training::factory()->free()->create()])
            ->assertSet('memberId', null)->assertSeeHtml('id="family-member"');
        $this->assertSame(2, Member::count());
        $this->assertSame(1, User::count());
        $this->assertSame(0, Membership::count());
    }

    public function test_parent_with_one_child_sees_it_selected_in_the_public_form(): void
    {
        $user = $this->customer(true);
        $member = Member::factory()->create(['user_id' => $user->id]);
        Livewire::actingAs($user)->test('training-registration-form', ['training' => Training::factory()->free()->create()])
            ->assertSet('memberId', $member->id)->assertSeeHtml('id="family-member"')->assertSee($member->name);
        $this->assertTrue(MyFamily::canAccess());
    }

    public function test_parent_can_open_training_membership_and_payment_pages_without_selecting_a_child(): void
    {
        $user = $this->customer(true);
        $team = Team::factory()->create();
        $members = Member::factory()->count(2)->create(['user_id' => $user->id]);
        $members[0]->enroll($team);
        $this->assertSame(0, $user->teams()->count());
        $this->actingAs($user)->withoutExceptionHandling();

        foreach ([MyTrainings::class, MemberMembership::class, MemberPayments::class] as $page) {
            $this->get($page::getUrl(panel: 'admin', tenant: $team))->assertOk();
        }
    }

    public function test_new_guest_account_has_one_member_and_existing_parent_mode_cannot_be_changed_before_login(): void
    {
        $training = Training::factory()->free()->create();
        Livewire::test('training-registration-form', ['training' => $training])
            ->set('contactName', 'Adult Person')->set('contactEmail', 'adult@example.test')
            ->set('registrationFor', 'self')
            ->call('requestFamilyLogin')->assertHasNoErrors();
        $user = User::where('email', 'adult@example.test')->sole();
        $this->assertFalse($user->is_parent);
        $this->assertTrue($user->hasRole(RoleEnum::CUSTOMER));
        $this->assertSame('Adult Person', $user->members()->sole()->name);
        $parent = $this->customer(true);
        Livewire::test('training-registration-form', ['training' => $training])
            ->set('contactName', 'Different Name')->set('contactEmail', $parent->email)
            ->set('registrationFor', 'self')->call('requestFamilyLogin');
        $this->assertTrue($parent->fresh()->is_parent);
        $this->assertSame(0, $parent->members()->count());
        Livewire::test('training-registration-form', ['training' => $training])
            ->set('contactName', 'Different Name')->set('contactEmail', $user->email)
            ->set('registrationFor', 'parent')->call('requestFamilyLogin')->assertHasNoErrors();
        $this->assertFalse($user->fresh()->is_parent);
        $this->assertSame('Adult Person', $user->members()->sole()->name);
        $this->assertGuest();
    }

    public function test_guest_must_choose_parent_or_self_before_an_account_is_created(): void
    {
        $training = Training::factory()->free()->create();
        foreach ([[null, 'required'], ['other', 'in']] as [$choice, $rule]) {
            Livewire::test('training-registration-form', ['training' => $training])
                ->assertSee(__('family.is_parent'))->assertSee(__('family.register_self'))
                ->set('contactName', 'Guest Person')->set('contactEmail', 'guest@example.test')
                ->set('registrationFor', $choice)->call('requestFamilyLogin')
                ->assertHasErrors(['registrationFor' => $rule]);
            $this->assertDatabaseMissing('users', ['email' => 'guest@example.test']);
        }
        Notification::assertNothingSent();
    }
}
