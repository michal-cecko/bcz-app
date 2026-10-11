<?php

namespace Tests\Feature;

use App\Enums\MembershipStatusEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\RegistrationStatusEnum;
use App\Enums\TrainingPricingTypeEnum;
use App\Models\Member;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\TeamSeason;
use App\Models\Training;
use App\Notifications\PaymentConfirmed;
use App\Services\GoPayService;
use App\Services\PaymentService;
use App\Services\RegistrationService;
use App\Services\SeasonService;
use GoPay\Http\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use RuntimeException;
use Tests\TestCase;

class FamilyMembershipPaymentTest extends TestCase
{
    use RefreshDatabase;

    private Member $child;

    private TeamSeason $season;

    protected function setUp(): void
    {
        parent::setUp();
        config(['family.enabled' => true]);
        Notification::fake();
        $this->child = Member::factory()->create();
        $this->season = TeamSeason::factory()->create(['fee_amount' => 100, 'prorate_fee' => false]);
        $this->child->enroll($this->season->team);
    }

    public function test_repeated_renewal_reuses_the_current_membership(): void
    {
        $old = app(SeasonService::class)->findOrCreateMembership($this->season, $this->child);
        $old->update(['status' => MembershipStatusEnum::CANCELLED]);
        $first = app(SeasonService::class)->renewMembership($old);
        $second = app(SeasonService::class)->renewMembership($old);

        $this->assertSame($first->id, $second->id);
        $this->assertSame(2, $this->child->memberships()->count());
        $this->assertEquals(100, $first->fee_amount);
        $this->assertSame(MembershipStatusEnum::CANCELLED, $old->fresh()->status);
        $first->update(['status' => MembershipStatusEnum::ACTIVE]);
        $this->assertSame($first->id, app(SeasonService::class)->renewMembership($old)->id);
    }

    public function test_repeated_renewal_without_a_season_reuses_the_current_membership(): void
    {
        $old = Membership::factory()->cancelled()->create([
            'member_id' => $this->child->id, 'user_id' => $this->child->user_id, 'team_id' => $this->season->team_id,
        ]);
        $first = app(SeasonService::class)->renewMembership($old);
        $second = app(SeasonService::class)->renewMembership($old);
        $this->assertSame($first->id, $second->id);
        $this->assertNull($first->team_season_id);
    }

    public function test_late_payment_activates_the_old_membership_and_cancels_the_unpaid_renewal(): void
    {
        $payment = $this->membershipPayment();
        $old = $payment->payable;
        $old->update(['status' => MembershipStatusEnum::CANCELLED]);
        $renewed = app(SeasonService::class)->renewMembership($old);
        $renewalRequest = app(PaymentService::class)->ensurePendingPaymentFor($this->child->user,
            $this->season->team, $renewed, 100);
        $sibling = Member::factory()->create(['user_id' => $this->child->user_id]);
        $training = Training::factory()->create(['team_id' => $this->season->team_id,
            'team_season_id' => $this->season->id, 'pricing_type' => TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED]);
        $registration = RegistrationService::registerTraining($training, $this->child->user, $this->child, [], false);
        $otherRegistration = RegistrationService::registerTraining($training, $sibling->user, $sibling, [], false);
        $payment->update(['status' => PaymentStatusEnum::COMPLETED]);

        app(PaymentService::class)->processPaymentCompleted($payment, false);
        app(PaymentService::class)->processPaymentCompleted($payment->fresh(), false);

        $this->assertSame(MembershipStatusEnum::ACTIVE, $old->fresh()->status);
        $this->assertSame(MembershipStatusEnum::CANCELLED, $renewed->fresh()->status);
        $this->assertSame(PaymentStatusEnum::CANCELLED, $renewalRequest->fresh()->status);
        $this->assertSame($old->id, $payment->fresh()->payable_id);
        $this->assertSame(RegistrationStatusEnum::Approved, $registration->fresh()->status);
        $this->assertSame(RegistrationStatusEnum::Pending, $otherRegistration->fresh()->status);
        $this->assertSame(1, $this->child->memberships()->where('status', MembershipStatusEnum::ACTIVE)->count());

        app(PaymentService::class)->refund($payment);
        $this->assertSame(MembershipStatusEnum::PENDING, $old->fresh()->status);
        $this->assertSame(MembershipStatusEnum::CANCELLED, $renewed->fresh()->status);
    }

    public function test_late_payment_keeps_an_already_active_renewal(): void
    {
        $payment = $this->membershipPayment();
        $old = $payment->payable;
        $old->update(['status' => MembershipStatusEnum::CANCELLED]);
        $renewed = app(SeasonService::class)->renewMembership($old);
        app(PaymentService::class)->recordManualPayment($this->child->user, $this->season->team, $renewed,
            100, 'EUR', PaymentMethodEnum::CASH, notify: false);
        $payment->update(['status' => PaymentStatusEnum::COMPLETED]);

        app(PaymentService::class)->processPaymentCompleted($payment, false);

        $this->assertSame(MembershipStatusEnum::CANCELLED, $old->fresh()->status);
        $this->assertSame(MembershipStatusEnum::ACTIVE, $renewed->fresh()->status);
        $this->assertSame($old->id, $payment->fresh()->payable_id);
    }

    public function test_partial_late_payment_does_not_cancel_the_pending_renewal(): void
    {
        $payment = $this->membershipPayment();
        $old = $payment->payable;
        $old->update(['status' => MembershipStatusEnum::CANCELLED]);
        $renewed = app(SeasonService::class)->renewMembership($old);
        $payment->update(['status' => PaymentStatusEnum::COMPLETED, 'amount' => 30]);

        app(PaymentService::class)->processPaymentCompleted($payment, false);

        $this->assertSame(MembershipStatusEnum::CANCELLED, $old->fresh()->status);
        $this->assertSame(MembershipStatusEnum::PENDING, $renewed->fresh()->status);
        $this->assertEquals(70, app(PaymentService::class)->amountStillOwed($old));
    }

    public function test_late_payment_without_a_season_cannot_create_two_active_memberships(): void
    {
        $old = Membership::factory()->cancelled()->create(['member_id' => $this->child->id,
            'user_id' => $this->child->user_id, 'team_id' => $this->season->team_id, 'fee_amount' => 100]);
        $renewed = app(SeasonService::class)->renewMembership($old);
        $payment = app(PaymentService::class)->createPendingPayment($this->child->user, $this->season->team, $old, 100);
        $payment->update(['status' => PaymentStatusEnum::COMPLETED]);
        app(PaymentService::class)->processPaymentCompleted($payment, false);
        $this->assertSame(MembershipStatusEnum::ACTIVE, $old->fresh()->status);
        $this->assertSame(MembershipStatusEnum::CANCELLED, $renewed->fresh()->status);
    }

    public function test_failed_gateway_completion_rolls_back_and_can_be_retried(): void
    {
        $payment = $this->membershipPayment();
        $payment->update(['gopay_payment_id' => '12345']);
        $response = new Response;
        $response->statusCode = 200;
        $response->json = ['state' => 'PAID'];
        $this->mock(GoPayService::class)->shouldReceive('getPaymentStatus')->with(12345)->twice()->andReturn($response);
        $failed = false;
        Membership::updating(function (Membership $membership) use (&$failed): void {
            if (! $failed && $membership->status === MembershipStatusEnum::ACTIVE) {
                $failed = true;
                throw new RuntimeException('Activation failed.');
            }
        });

        try {
            app(PaymentService::class)->handleGoPayNotification(12345);
            $this->fail('The failed activation was ignored.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Activation failed.', $exception->getMessage());
        }
        $this->assertSame(PaymentStatusEnum::PENDING, $payment->fresh()->status);
        $this->assertNull($payment->fresh()->paid_at);

        app(PaymentService::class)->handleGoPayNotification(12345);
        $this->assertSame(PaymentStatusEnum::COMPLETED, $payment->fresh()->status);
        $this->assertSame(MembershipStatusEnum::ACTIVE, $payment->fresh()->payable->status);
    }

    public function test_repeated_late_gateway_callback_completes_the_fee_once(): void
    {
        $payment = $this->membershipPayment();
        $old = $payment->payable;
        $old->update(['status' => MembershipStatusEnum::CANCELLED]);
        $renewed = app(SeasonService::class)->renewMembership($old);
        $payment->update(['gopay_payment_id' => '12345']);
        $response = new Response;
        $response->statusCode = 200;
        $response->json = ['state' => 'PAID'];
        $this->mock(GoPayService::class)->shouldReceive('getPaymentStatus')->with(12345)->twice()->andReturn($response);

        $first = app(PaymentService::class)->handleGoPayNotification(12345);
        $second = app(PaymentService::class)->handleGoPayNotification(12345);

        $this->assertSame($first->id, $second->id);
        $this->assertSame(PaymentStatusEnum::COMPLETED, $payment->fresh()->status);
        $this->assertSame(MembershipStatusEnum::ACTIVE, $old->fresh()->status);
        $this->assertSame(MembershipStatusEnum::CANCELLED, $renewed->fresh()->status);
        Notification::assertSentToTimes($this->child->user, PaymentConfirmed::class, 1);
    }

    private function membershipPayment(): Payment
    {
        return app(PaymentService::class)->ensurePendingMembershipPayment($this->child, $this->season->team, $this->season);
    }
}
