<?php

namespace Tests\Feature\Models;

use App\Enums\RegistrationStatusEnum;
use App\Enums\TrainingPricingTypeEnum;
use App\Models\Membership;
use App\Models\Team;
use App\Models\TeamSeason;
use App\Models\Training;
use App\Models\TrainingRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Reproduces the bug from testing-notes item 13 (task-hash dc11fc93): right after
 * registering, the QR note comes from Training::renderQrPaymentNote() (training note
 * wins over season note). But once the user pays later from the profile via
 * /platba/{payment}, PaymentPage reads Payment::payable->getQrPaymentNote() - and for
 * membership_required trainings the payable is a Membership, whose getQrPaymentNote()
 * only ever looked at the season's note, never the training's. In production every
 * team_season.payment_note is empty, so the profile payment page showed no note at all
 * even though the training itself has one.
 */
class MembershipQrPaymentNoteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Same shape as production: a membership_required training carries its own
     * payment_note, the season note is empty, and the user's TrainingRegistration
     * (created by the membership_needed post-registration flow) is what connects the
     * Membership back to that training.
     */
    public function test_membership_qr_note_uses_driving_trainings_note_when_season_note_is_empty(): void
    {
        $team = Team::factory()->create(['name' => ['sk' => 'BCZ Team', 'en' => 'BCZ Team']]);

        $season = TeamSeason::factory()->create([
            'team_id' => $team->id,
            'name' => 'Sezona 2026',
            'payment_note' => '',
        ]);

        $training = Training::factory()->create([
            'team_id' => $team->id,
            'team_season_id' => $season->id,
            'pricing_type' => TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED,
            'title' => ['sk' => 'Letny Workout', 'en' => 'Letny Workout'],
            'payment_note' => '{{meno}} {{priezvisko}} - clensky prispevok',
        ]);

        $user = User::factory()->create([
            'first_name' => 'Jan',
            'last_name' => 'Novak',
        ]);

        TrainingRegistration::create([
            'training_id' => $training->id,
            'user_id' => $user->id,
            'form_data' => [],
            'status' => RegistrationStatusEnum::Pending,
            'registered_at' => now(),
        ]);

        $membership = Membership::factory()->pending()->create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'team_season_id' => $season->id,
        ]);

        $this->assertSame('Jan Novak - clensky prispevok', $membership->getQrPaymentNote());
    }

    public function test_membership_qr_note_falls_back_to_season_note_when_no_driving_training_has_its_own_note(): void
    {
        $team = Team::factory()->create(['name' => ['sk' => 'BCZ Team', 'en' => 'BCZ Team']]);

        $season = TeamSeason::factory()->create([
            'team_id' => $team->id,
            'name' => 'Sezona 2026',
            'payment_note' => 'Clenske {{meno}} {{priezvisko}}',
        ]);

        $user = User::factory()->create(['first_name' => 'Jan', 'last_name' => 'Novak']);

        $membership = Membership::factory()->pending()->create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'team_season_id' => $season->id,
        ]);

        $this->assertSame('Clenske Jan Novak', $membership->getQrPaymentNote());
    }
}
