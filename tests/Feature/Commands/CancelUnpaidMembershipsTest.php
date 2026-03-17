<?php

namespace Tests\Feature\Commands;

use App\Enums\MembershipStatusEnum;
use App\Models\Membership;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CancelUnpaidMembershipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_cancels_overdue_pending_memberships(): void
    {
        $overdue = Membership::factory()->pending()->create([
            'payment_deadline_at' => now()->subDay(),
            'is_free' => false,
        ]);

        $this->artisan('memberships:cancel-unpaid')
            ->expectsOutputToContain('Cancelled 1 unpaid membership(s)')
            ->assertExitCode(0);

        $this->assertEquals(MembershipStatusEnum::CANCELLED, $overdue->fresh()->status);
    }

    public function test_does_not_cancel_before_deadline(): void
    {
        $future = Membership::factory()->pending()->create([
            'payment_deadline_at' => now()->addDay(),
            'is_free' => false,
        ]);

        $this->artisan('memberships:cancel-unpaid')
            ->expectsOutputToContain('Cancelled 0 unpaid membership(s)')
            ->assertExitCode(0);

        $this->assertEquals(MembershipStatusEnum::PENDING, $future->fresh()->status);
    }

    public function test_does_not_cancel_free_memberships(): void
    {
        Membership::factory()->pending()->create([
            'payment_deadline_at' => now()->subDay(),
            'is_free' => true,
        ]);

        $this->artisan('memberships:cancel-unpaid')
            ->expectsOutputToContain('Cancelled 0 unpaid membership(s)')
            ->assertExitCode(0);
    }

    public function test_does_not_cancel_active_memberships(): void
    {
        Membership::factory()->create([
            'status' => MembershipStatusEnum::ACTIVE,
            'payment_deadline_at' => now()->subDay(),
            'is_free' => false,
        ]);

        $this->artisan('memberships:cancel-unpaid')
            ->expectsOutputToContain('Cancelled 0 unpaid membership(s)')
            ->assertExitCode(0);
    }

    public function test_does_not_cancel_without_deadline(): void
    {
        Membership::factory()->pending()->create([
            'payment_deadline_at' => null,
            'is_free' => false,
        ]);

        $this->artisan('memberships:cancel-unpaid')
            ->expectsOutputToContain('Cancelled 0 unpaid membership(s)')
            ->assertExitCode(0);
    }
}
