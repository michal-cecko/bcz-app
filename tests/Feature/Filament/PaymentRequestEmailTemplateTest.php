<?php

namespace Tests\Feature\Filament;

use App\Enums\PaymentStatusEnum;
use App\Enums\RoleEnum;
use App\Enums\TrainingPricingTypeEnum;
use App\Filament\Actions\SendEmailAction;
use App\Filament\Resources\Memberships\Pages\ListMemberships;
use App\Filament\Resources\Trainings\Pages\EditTraining;
use App\Filament\Resources\Trainings\RelationManagers\RegistrationsRelationManager;
use App\Mail\AdminEmail;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\Team;
use App\Models\Training;
use App\Models\TrainingRegistration;
use App\Models\User;
use App\Notifications\MembershipPaymentDue;
use App\Notifications\TrainingRegistrationPaymentDue;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PaymentRequestEmailTemplateTest extends TestCase
{
    use RefreshDatabase;

    private Team $team;

    private User $member;

    private User $otherMember;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }

        $this->team = Team::factory()->create();
        $this->member = User::factory()->create();
        $this->otherMember = User::factory()->create();
        foreach ([$this->member, $this->otherMember] as $member) {
            $this->team->members()->attach($member, ['role' => RoleEnum::ATHLETE->value, 'is_active' => true, 'joined_at' => now()]);
        }

        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::SUPER_ADMIN);
        $admin->teams()->attach($this->team);
        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::setTenant($this->team);
        Filament::bootCurrentPanel();

        Notification::fake();
        Mail::fake();
    }

    private function pendingMembership(?User $user = null): Membership
    {
        return Membership::factory()->pending()->create([
            'team_id' => $this->team->id,
            'user_id' => ($user ?? $this->member)->id,
            'fee_amount' => 60.00,
            'fee_currency' => 'EUR',
        ]);
    }

    private function paymentFor(Membership|TrainingRegistration $payable, float $amount, PaymentStatusEnum $status = PaymentStatusEnum::PENDING): Payment
    {
        return Payment::factory()->create([
            'team_id' => $this->team->id,
            'user_id' => $payable->user_id,
            'payable_type' => $payable->getMorphClass(),
            'payable_id' => $payable->id,
            'amount' => $amount,
            'currency' => 'EUR',
            'status' => $status,
            'paid_at' => $status === PaymentStatusEnum::COMPLETED ? now() : null,
        ]);
    }

    private function pendingRegistrationFor(Training $training): TrainingRegistration
    {
        return TrainingRegistration::factory()->pending()->create([
            'training_id' => $training->id,
            'user_id' => $this->member->id,
            'payment_due_at' => now()->addDays(7),
        ]);
    }

    public function test_the_payment_request_template_sends_the_amount_still_owed(): void
    {
        $membership = $this->pendingMembership();
        $this->paymentFor($membership, 20.00, PaymentStatusEnum::COMPLETED);
        $this->paymentFor($membership, 40.00);

        Livewire::test(ListMemberships::class)
            ->callAction(TestAction::make('send_email')->table($membership), [
                'template_id' => SendEmailAction::PAYMENT_REQUEST_TEMPLATE,
            ])
            ->assertHasNoActionErrors()
            ->assertNotified('Výzva na platbu odoslaná: 1');

        Notification::assertSentTo($this->member, MembershipPaymentDue::class,
            fn (MembershipPaymentDue $notification): bool => $notification->toMail($this->member)->viewData['feeAmount'] === '40.00');
        Mail::assertNothingQueued();
    }

    public function test_bulk_payment_request_skips_paid_cancelled_and_free_memberships(): void
    {
        $unpaid = $this->pendingMembership();

        $paid = $this->pendingMembership($this->otherMember);
        $this->paymentFor($paid, 60.00, PaymentStatusEnum::COMPLETED);

        $cancelled = Membership::factory()->cancelled()->create(['team_id' => $this->team->id, 'user_id' => $this->member->id, 'fee_amount' => 60.00]);
        $free = Membership::factory()->free()->create(['team_id' => $this->team->id, 'user_id' => $this->member->id]);

        Livewire::test(ListMemberships::class)
            ->selectTableRecords([$unpaid->id, $paid->id, $cancelled->id, $free->id])
            ->callAction(TestAction::make('send_email_bulk')->table()->bulk(), [
                'template_id' => SendEmailAction::PAYMENT_REQUEST_TEMPLATE,
            ])
            ->assertHasNoActionErrors()
            ->assertNotified('Výzva na platbu odoslaná: 1');

        Notification::assertSentToTimes($this->member, MembershipPaymentDue::class, 1);
        Notification::assertNotSentTo($this->otherMember, MembershipPaymentDue::class);
    }

    public function test_a_paid_training_registration_gets_the_training_payment_request(): void
    {
        $training = Training::factory()->create(['team_id' => $this->team->id, 'pricing_type' => TrainingPricingTypeEnum::PAID, 'price_amount' => 25.00]);
        $registration = $this->pendingRegistrationFor($training);
        $this->paymentFor($registration, 25.00);

        Livewire::test(RegistrationsRelationManager::class, ['ownerRecord' => $training, 'pageClass' => EditTraining::class])
            ->callAction(TestAction::make('send_email')->table($registration), [
                'template_id' => SendEmailAction::PAYMENT_REQUEST_TEMPLATE,
            ])
            ->assertHasNoActionErrors();

        Notification::assertSentTo($this->member, TrainingRegistrationPaymentDue::class);
    }

    public function test_a_membership_training_registration_has_no_training_payment_to_request(): void
    {
        $training = Training::factory()->create(['team_id' => $this->team->id, 'pricing_type' => TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED, 'price_amount' => 25.00]);
        $registration = $this->pendingRegistrationFor($training);

        Livewire::test(RegistrationsRelationManager::class, ['ownerRecord' => $training, 'pageClass' => EditTraining::class])
            ->callAction(TestAction::make('send_email')->table($registration), [
                'template_id' => SendEmailAction::PAYMENT_REQUEST_TEMPLATE,
            ])
            ->assertNotified('Výzva na platbu odoslaná: 0');

        Notification::assertNothingSent();
    }

    public function test_a_normal_template_still_sends_the_composed_email(): void
    {
        $membership = $this->pendingMembership();

        Livewire::test(ListMemberships::class)
            ->callAction(TestAction::make('send_email')->table($membership), [
                'subject' => 'Ahoj {{meno}}',
                'content' => [['type' => 'masonBrick', 'attrs' => ['id' => 'email-rich-text', 'config' => ['content' => '<p>Text</p>']]]],
            ])
            ->assertHasNoActionErrors();

        Mail::assertQueued(AdminEmail::class);
        Notification::assertNothingSent();
    }
}
