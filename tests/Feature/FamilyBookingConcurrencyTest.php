<?php

namespace Tests\Feature;

use App\Enums\MembershipStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Models\Member;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\TeamSeason;
use App\Models\Training;
use App\Services\PaymentService;
use App\Services\RegistrationService;
use App\Services\SeasonService;
use Closure;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class FamilyBookingConcurrencyTest extends TestCase
{
    use DatabaseTruncation;

    protected function setUp(): void
    {
        parent::setUp();
        if (DB::getDriverName() !== 'pgsql') {
            $this->markTestSkipped('PostgreSQL is required to verify booking locks.');
        }
        $this->beforeApplicationDestroyed(fn () => $this->truncateTablesForAllConnections());
    }

    public function test_two_simultaneous_bookings_cannot_take_the_last_place(): void
    {
        $trainingId = Training::factory()->free()->create(['max_capacity' => 1])->id;
        $memberIds = Member::factory()->count(2)->create()->pluck('id')->all();
        $tasks = [];
        foreach ($memberIds as $memberId) {
            $tasks[] = static function () use ($trainingId, $memberId): string {
                config(['family.enabled' => true]);
                $member = Member::findOrFail($memberId);
                try {
                    RegistrationService::registerTraining(Training::findOrFail($trainingId), $member->user, $member, [], false);

                    return 'booked';
                } catch (ValidationException $exception) {
                    return 'full';
                }
            };
        }
        $results = $this->runConcurrently($tasks);
        sort($results);
        $this->assertSame(['booked', 'full'], $results);
        $this->assertSame(1, Training::findOrFail($trainingId)->spotHoldingRegistrations()->count());
    }

    public function test_two_simultaneous_payment_requests_create_one_membership_and_one_payment(): void
    {
        $member = Member::factory()->create();
        $season = TeamSeason::factory()->create();
        $member->enroll($season->team);
        $memberId = $member->id;
        $seasonId = $season->id;
        $task = static function () use ($memberId, $seasonId): string {
            config(['family.enabled' => true]);
            $season = TeamSeason::findOrFail($seasonId);

            return app(PaymentService::class)->ensurePendingMembershipPayment(Member::findOrFail($memberId), $season->team, $season)->id;
        };
        $results = $this->runConcurrently([$task, $task]);
        $this->assertSame($results[0], $results[1]);
        $this->assertSame(1, Membership::count());
        $this->assertSame(1, Payment::count());
    }

    public function test_two_simultaneous_renewals_create_one_current_membership(): void
    {
        config(['family.enabled' => true]);
        $member = Member::factory()->create();
        $season = TeamSeason::factory()->create();
        $member->enroll($season->team);
        $old = app(SeasonService::class)->findOrCreateMembership($season, $member);
        $old->update(['status' => MembershipStatusEnum::CANCELLED]);
        $membershipId = $old->id;
        $task = static function () use ($membershipId): string {
            config(['family.enabled' => true]);

            return app(SeasonService::class)->renewMembership(Membership::findOrFail($membershipId))->id;
        };
        $results = $this->runConcurrently([$task, $task]);

        $this->assertSame($results[0], $results[1]);
        $this->assertSame(2, Membership::count());
        $this->assertSame(1, Membership::where('status', MembershipStatusEnum::PENDING)->count());
    }

    public function test_two_admin_saves_return_a_validation_error_instead_of_a_database_conflict(): void
    {
        $member = Member::factory()->create();
        $season = TeamSeason::factory()->create();
        $member->enroll($season->team);
        $memberId = $member->id;
        $seasonId = $season->id;
        $task = static function () use ($memberId, $seasonId): string {
            config(['family.enabled' => true]);
            $season = TeamSeason::findOrFail($seasonId);
            try {
                app(SeasonService::class)->saveMembership(new Membership, [
                    'member_id' => $memberId, 'team_id' => $season->team_id, 'team_season_id' => $seasonId,
                    'status' => MembershipStatusEnum::PENDING, 'fee_amount' => 30, 'fee_currency' => 'EUR',
                    'starts_at' => $season->starts_at, 'ends_at' => $season->ends_at,
                ]);

                return 'created';
            } catch (ValidationException $exception) {
                return array_key_exists('status', $exception->errors()) ? 'conflict' : 'unexpected';
            }
        };
        $results = $this->runConcurrently([$task, $task]);
        sort($results);
        $this->assertSame(['conflict', 'created'], $results);
        $this->assertSame(1, Membership::count());
    }

    public function test_renewal_racing_with_a_late_payment_keeps_one_active_membership(): void
    {
        config(['family.enabled' => true]);
        $member = Member::factory()->create();
        $season = TeamSeason::factory()->create();
        $member->enroll($season->team);
        $payment = app(PaymentService::class)->ensurePendingMembershipPayment($member, $season->team, $season);
        $old = $payment->payable;
        $old->update(['status' => MembershipStatusEnum::CANCELLED]);
        $payment->update(['status' => PaymentStatusEnum::COMPLETED]);
        $membershipId = $old->id;
        $paymentId = $payment->id;

        $this->runConcurrently([
            static function () use ($membershipId): void {
                config(['family.enabled' => true]);
                app(SeasonService::class)->renewMembership(Membership::findOrFail($membershipId));
            },
            static function () use ($paymentId): void {
                config(['family.enabled' => true]);
                app(PaymentService::class)->processPaymentCompleted(Payment::findOrFail($paymentId), false);
            },
        ]);

        $this->assertSame(MembershipStatusEnum::ACTIVE, $old->fresh()->status);
        $this->assertSame(1, Membership::whereIn('status', [MembershipStatusEnum::ACTIVE, MembershipStatusEnum::PENDING])->count());
        $this->assertSame($old->id, $payment->fresh()->payable_id);
    }

    /**
     * @param  array<Closure>  $tasks
     * @return array<mixed>
     */
    private function runConcurrently(array $tasks): array
    {
        $connection = DB::connection()->getConfig();
        $wrapped = [];
        foreach ($tasks as $task) {
            $wrapped[] = static function () use ($connection, $task): mixed {
                config(['database.default' => 'pgsql', 'database.connections.pgsql' => $connection]);
                DB::purge('pgsql');

                return $task();
            };
        }

        return Concurrency::driver('process')->run($wrapped);
    }
}
