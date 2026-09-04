<?php

namespace Tests\Feature\Models;

use App\Enums\TrainingPricingTypeEnum;
use App\Models\Team;
use App\Models\TeamSeason;
use App\Models\Training;
use App\Models\TrainingRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QrPaymentNoteTest extends TestCase
{
    use RefreshDatabase;

    private function training(string $paymentNote): Training
    {
        return Training::factory()->create([
            'pricing_type' => TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED,
            'payment_note' => $paymentNote,
        ]);
    }

    /**
     * @param  array<string, string>  $formData
     */
    private function registration(Training $training, User $user, array $formData): TrainingRegistration
    {
        return TrainingRegistration::factory()->create([
            'training_id' => $training->id,
            'user_id' => $user->id,
            'form_data' => $formData,
        ]);
    }

    public function test_qr_note_uses_the_athlete_named_on_the_registration_form(): void
    {
        $training = $this->training('{{meno}} {{priezvisko}} - clensky prispevok');
        $parent = User::factory()->create(['first_name' => 'Peter', 'last_name' => 'Rodič']);

        $registration = $this->registration($training, $parent, [
            'meno' => 'Ján',
            'priezvisko' => 'Novák',
        ]);

        $this->assertSame('Ján Novák - clensky prispevok', $registration->getQrPaymentNote());
    }

    public function test_two_registrations_on_one_account_each_carry_their_own_athlete(): void
    {
        $training = $this->training('{{meno}} {{priezvisko}} - clensky prispevok');
        $parent = User::factory()->create(['first_name' => 'Peter', 'last_name' => 'Rodič']);

        $first = $this->registration($training, $parent, ['meno' => 'Ján', 'priezvisko' => 'Novák']);
        $second = $this->registration($training, $parent, ['meno' => 'Eva', 'priezvisko' => 'Nováková']);

        $this->assertSame('Ján Novák - clensky prispevok', $first->getQrPaymentNote());
        $this->assertSame('Eva Nováková - clensky prispevok', $second->getQrPaymentNote());
    }

    public function test_qr_note_falls_back_to_the_account_holder_when_the_form_carried_no_name(): void
    {
        $training = $this->training('{{meno}} {{priezvisko}} - clensky prispevok');
        $user = User::factory()->create(['first_name' => 'Peter', 'last_name' => 'Rodič']);

        $registration = $this->registration($training, $user, []);

        $this->assertSame('Peter Rodič - clensky prispevok', $registration->getQrPaymentNote());
    }

    public function test_empty_placeholders_do_not_leave_a_dangling_separator(): void
    {
        $training = $this->training('{{meno}} {{priezvisko}} - clensky prispevok');
        $user = User::factory()->create(['first_name' => '', 'last_name' => '']);

        $registration = $this->registration($training, $user, []);

        $this->assertSame('clensky prispevok', $registration->getQrPaymentNote());
    }

    public function test_a_note_that_resolves_to_nothing_but_separators_is_null(): void
    {
        $training = $this->training('{{meno}} {{priezvisko}}');
        $user = User::factory()->create(['first_name' => '', 'last_name' => '']);

        $registration = $this->registration($training, $user, []);

        $this->assertNull($registration->getQrPaymentNote());
    }

    public function test_a_note_without_placeholders_is_passed_through_verbatim(): void
    {
        $training = $this->training('clensky prispevok - meno a priezvisko');
        $user = User::factory()->create(['first_name' => 'Ján', 'last_name' => 'Novák']);

        $registration = $this->registration($training, $user, ['meno' => 'Ján', 'priezvisko' => 'Novák']);

        $this->assertSame('clensky prispevok - meno a priezvisko', $registration->getQrPaymentNote());
    }

    public function test_the_athlete_name_reaches_a_note_inherited_from_the_season(): void
    {
        $team = Team::factory()->create();
        $season = TeamSeason::factory()->create([
            'team_id' => $team->id,
            'name' => 'Sezóna 2026',
            'payment_note' => '{{meno}} {{priezvisko}} - {{sezona}}',
        ]);
        $training = Training::factory()->create([
            'team_id' => $team->id,
            'team_season_id' => $season->id,
            'pricing_type' => TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED,
            'payment_note' => null,
        ]);
        $parent = User::factory()->create(['first_name' => 'Peter', 'last_name' => 'Rodič']);

        $registration = $this->registration($training, $parent, ['meno' => 'Ján', 'priezvisko' => 'Novák']);

        $this->assertSame('Ján Novák - Sezóna 2026', $registration->getQrPaymentNote());
    }

    public function test_payment_description_names_the_athlete_not_the_account_holder(): void
    {
        $training = Training::factory()->create([
            'pricing_type' => TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED,
            'title' => ['sk' => 'Street Workout'],
        ]);
        $parent = User::factory()->create(['first_name' => 'Peter', 'last_name' => 'Rodič']);

        $registration = $this->registration($training, $parent, ['meno' => 'Ján', 'priezvisko' => 'Novák']);

        $this->assertSame('Ján Novák - Street Workout', $registration->getPaymentDescription());
    }
}
