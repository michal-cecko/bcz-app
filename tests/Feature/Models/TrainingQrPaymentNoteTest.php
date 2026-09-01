<?php

namespace Tests\Feature\Models;

use App\Models\Team;
use App\Models\TeamSeason;
use App\Models\Training;
use App\Models\TrainingRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrainingQrPaymentNoteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The training's own payment note takes priority over the season note, so a
     * QR rendered for a training registration carries the note the coach set on
     * the training itself.
     */
    public function test_training_payment_note_wins_over_season_payment_note(): void
    {
        $training = $this->makeTraining(
            trainingNote: 'Treningovka {{meno}} {{priezvisko}}',
            seasonNote: 'Clenske {{sezona}}',
        );

        $registration = $this->makeRegistration($training);

        $this->assertSame('Treningovka Jan Novak', $training->renderQrPaymentNote($registration->user));
        $this->assertSame('Treningovka Jan Novak', $registration->getQrPaymentNote());
    }

    /**
     * With no note on the training, the season note is the fallback - and its own
     * variables ({{sezona}}, {{nazov_timu}}) must be substituted too, so the QR
     * never carries raw {{...}} placeholders.
     */
    public function test_falls_back_to_season_payment_note_with_season_variables_substituted(): void
    {
        $training = $this->makeTraining(
            trainingNote: null,
            seasonNote: 'Clenske {{meno}} {{priezvisko}} {{sezona}} {{nazov_timu}}',
        );

        $note = $this->makeRegistration($training)->getQrPaymentNote();

        $this->assertSame('Clenske Jan Novak Sezona 2026 BCZ Team', $note);
        $this->assertStringNotContainsString('{{', (string) $note);
    }

    public function test_falls_back_to_season_payment_note_when_training_note_is_empty_string(): void
    {
        $training = $this->makeTraining(trainingNote: '', seasonNote: 'Clenske {{sezona}}');

        $this->assertSame('Clenske Sezona 2026', $training->renderQrPaymentNote());
    }

    public function test_returns_null_when_neither_training_nor_season_has_a_note(): void
    {
        $training = $this->makeTraining(trainingNote: null, seasonNote: null);

        $this->assertNull($this->makeRegistration($training)->getQrPaymentNote());
    }

    /**
     * The training-scoped variables keep working - this is the substitution the
     * note path already did before the precedence change.
     */
    public function test_training_scoped_variables_are_still_substituted(): void
    {
        $training = $this->makeTraining(
            trainingNote: '{{meno}} {{priezvisko}} {{nazov_treningu}} {{miesto}}',
            seasonNote: null,
        );

        $this->assertSame(
            'Jan Novak Street Workout Telocvicna',
            $this->makeRegistration($training)->getQrPaymentNote(),
        );
    }

    private function makeTraining(?string $trainingNote, ?string $seasonNote): Training
    {
        $team = Team::factory()->create(['name' => ['sk' => 'BCZ Team', 'en' => 'BCZ Team']]);

        $season = TeamSeason::factory()->create([
            'team_id' => $team->id,
            'name' => 'Sezona 2026',
            'payment_note' => $seasonNote,
        ]);

        return Training::factory()->create([
            'team_id' => $team->id,
            'team_season_id' => $season->id,
            'title' => ['sk' => 'Street Workout', 'en' => 'Street Workout'],
            'place_name' => ['sk' => 'Telocvicna', 'en' => 'Telocvicna'],
            'payment_note' => $trainingNote,
        ]);
    }

    private function makeRegistration(Training $training): TrainingRegistration
    {
        $user = User::factory()->create([
            'first_name' => 'Jan',
            'last_name' => 'Novak',
        ]);

        return TrainingRegistration::create([
            'training_id' => $training->id,
            'user_id' => $user->id,
            'form_data' => [],
        ]);
    }
}
