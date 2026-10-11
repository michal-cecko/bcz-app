<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\Training;
use App\Models\TrainingRegistration;
use App\Models\User;
use Dotenv\Dotenv;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\Uid\Uuid;
use Tests\TestCase;

class BackfillMembersTest extends TestCase
{
    use RefreshDatabase;

    public function test_example_configuration_keeps_family_mode_disabled_until_cutover(): void
    {
        $defaults = Dotenv::parse(file_get_contents(base_path('.env.example')));
        $this->assertSame('false', $defaults['FAMILY_MEMBERSHIPS_ENABLED']);
        $this->assertSame('en_US', $defaults['APP_FAKER_LOCALE']);
    }

    public function test_dry_run_does_not_change_records_and_apply_is_repeatable(): void
    {
        $membership = Membership::factory()->create();
        $this->artisan('members:backfill')->assertSuccessful();
        $this->assertSame(0, Member::count());
        $this->assertNull($membership->fresh()->member_id);
        $this->artisan('members:backfill', ['--apply' => true])->assertSuccessful();
        $memberId = $membership->fresh()->member_id;
        $this->assertNotNull($memberId);
        $count = Member::count();
        $this->artisan('members:backfill', ['--apply' => true])->assertSuccessful();
        $this->assertSame($count, Member::count());
        $this->assertSame($memberId, $membership->fresh()->member_id);
        $this->assertSame($membership->fee_amount, $membership->fresh()->fee_amount);
    }

    public function test_shared_account_requires_mapping_and_rolls_back_without_it(): void
    {
        $parent = User::factory()->create(['first_name' => 'Parent']);
        $training = Training::factory()->create();
        $registration = TrainingRegistration::factory()->create(['user_id' => $parent->id, 'training_id' => $training->id,
            'form_data' => ['meno' => 'Child', 'priezvisko' => 'One']]);
        $this->artisan('members:backfill', ['--apply' => true])->assertFailed();
        $this->assertSame(0, Member::count());
        $this->assertNull($registration->fresh()->member_id);
    }

    public function test_explicit_mapping_keeps_payment_ids_and_credits_a_membership_once(): void
    {
        $parent = User::factory()->create(['first_name' => 'Parent']);
        $membership = Membership::factory()->create(['user_id' => $parent->id]);
        $training = Training::factory()->create(['team_id' => $membership->team_id]);
        $registration = TrainingRegistration::factory()->create(['user_id' => $parent->id, 'training_id' => $training->id,
            'form_data' => ['meno' => 'Anna', 'priezvisko' => 'Child']]);
        $payment = Payment::factory()->create(['user_id' => $parent->id,
            'team_id' => $membership->team_id, 'payable_type' => $membership->getMorphClass(), 'payable_id' => $membership->id]);
        $memberId = (string) Uuid::v7();
        $path = tempnam(sys_get_temp_dir(), 'family-mapping-');
        file_put_contents($path, json_encode([
            'members' => [['id' => $memberId, 'user_id' => $parent->id, 'name' => 'Anna Child', 'first_name' => 'Anna', 'last_name' => 'Child']],
            'records' => ['memberships' => [$membership->id => $memberId], 'training_registrations' => [$registration->id => $memberId]],
            'enrolments' => [['member_id' => $memberId, 'team_id' => $membership->team_id, 'is_active' => true, 'continuous_membership' => true]],
        ], JSON_THROW_ON_ERROR));
        try {
            $this->artisan('members:backfill', ['--apply' => true, '--mapping' => $path])->assertSuccessful();
            $this->artisan('members:backfill', ['--apply' => true, '--mapping' => $path])->assertSuccessful();
        } finally {
            unlink($path);
        }
        $this->assertSame($memberId, $membership->fresh()->member_id);
        $this->assertSame($memberId, $registration->fresh()->member_id);
        $this->assertSame(1, Member::where('user_id', $parent->id)->count());
        $this->assertSame($payment->payable_id, $payment->fresh()->payable_id);
        $this->assertSame($payment->amount, $payment->fresh()->amount);
        $this->assertSame(1, Payment::count());
        $this->assertSame(['meno' => 'Anna', 'priezvisko' => 'Child'], $registration->fresh()->form_data);
        $this->artisan('members:check')->assertSuccessful();
    }

    public function test_cutover_check_rejects_unmapped_current_memberships(): void
    {
        Membership::factory()->create();
        $this->artisan('members:check')->assertFailed();
    }
}
