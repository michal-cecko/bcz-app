<?php

namespace Tests\Feature;

use App\Models\CoachProfile;
use App\Models\SportCategory;
use App\Models\Team;
use App\Models\Training;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrainingCoachBiographyHtmlTest extends TestCase
{
    use RefreshDatabase;

    private const BIOGRAPHY_HTML = '<p>Trener s <strong>desatrocnou</strong> praxou.</p>';

    protected Team $team;

    protected SportCategory $sportCategory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->team = Team::factory()->create(['slug' => 'bcz-club']);
        $this->sportCategory = SportCategory::factory()->create(['team_id' => $this->team->id]);
    }

    private function trainingWithCoach(string $biography): Training
    {
        $training = Training::factory()->create([
            'team_id' => $this->team->id,
            'sport_category_id' => $this->sportCategory->id,
            'title' => ['sk' => 'Street Workout'],
            'slug' => 'street-workout',
            'is_active' => true,
        ]);

        $coach = User::factory()->create(['name' => 'Jozef Trener']);
        CoachProfile::factory()->create([
            'user_id' => $coach->id,
            'biography' => ['sk' => $biography, 'en' => $biography],
        ]);

        $training->coaches()->attach($coach, ['role' => 'main']);

        return $training;
    }

    private function showTraining(Training $training)
    {
        return $this->get('/timy/'.$this->team->slug.'/treningy/'.$training->slug);
    }

    public function test_coach_biography_renders_as_html_not_escaped_tags(): void
    {
        $training = $this->trainingWithCoach(self::BIOGRAPHY_HTML);

        $response = $this->showTraining($training);

        $response->assertStatus(200);
        $response->assertSee(self::BIOGRAPHY_HTML, false);
        $response->assertDontSee('&lt;p&gt;', false);
        $response->assertDontSee('&lt;strong&gt;', false);
    }

    public function test_coach_biography_wrapper_is_a_div_so_paragraphs_keep_their_styling(): void
    {
        $training = $this->trainingWithCoach(self::BIOGRAPHY_HTML);

        $response = $this->showTraining($training);

        $response->assertStatus(200);
        $response->assertSee('<div class="text-[#888888] text-[15px] leading-[1.7] space-y-4">'.self::BIOGRAPHY_HTML.'</div>', false);
    }

    public function test_plain_text_coach_biography_still_renders(): void
    {
        $training = $this->trainingWithCoach('Trener bez HTML znaciek.');

        $response = $this->showTraining($training);

        $response->assertStatus(200);
        $response->assertSee('Trener bez HTML znaciek.', false);
    }
}
