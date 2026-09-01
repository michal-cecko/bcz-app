<?php

namespace Tests\Feature;

use App\Models\CoachProfile;
use App\Models\SportCategory;
use App\Models\Team;
use App\Models\Training;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrainingSectionOrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A training that has content for every reorderable section, so all of them
     * render and their positions can be compared.
     *
     * @param  list<string>|null  $sectionOrder
     */
    private function trainingWithEverySection(?array $sectionOrder = null): Training
    {
        $team = Team::factory()->create([
            'name' => ['sk' => 'BCZ Club'],
            'slug' => 'bcz-club',
        ]);

        $training = Training::factory()->create([
            'team_id' => $team->id,
            'sport_category_id' => SportCategory::factory()->create(['team_id' => $team->id])->id,
            'title' => ['sk' => 'Street Workout'],
            'slug' => 'street-workout',
            'is_active' => true,
            'gallery_images' => ['trainings/gallery/photo-one.jpg', 'trainings/gallery/photo-two.jpg'],
            'section_order' => $sectionOrder,
        ]);

        $coach = User::factory()->create(['name' => 'Jozef Trener']);
        CoachProfile::factory()->create([
            'user_id' => $coach->id,
            'biography' => ['sk' => '<p>Bio</p>', 'en' => '<p>Bio</p>'],
        ]);
        $training->coaches()->attach($coach, ['role' => 'main']);

        return $training;
    }

    /**
     * The order the sections actually appear in on the rendered page, derived from
     * the position of each section header label.
     *
     * @return list<string>
     */
    private function renderedSectionOrder(Training $training): array
    {
        $html = $this->get($training->getLinkUrl())->assertOk()->getContent();

        $labels = [
            'info' => __('training_detail.about_label'),
            'location' => __('training_detail.location_label'),
            'coaches' => __('training_detail.coach_label'),
            'gallery' => __('training_detail.gallery_label'),
            'registration' => __('training_detail.form_label'),
        ];

        $positions = [];

        foreach ($labels as $key => $label) {
            $position = strpos($html, '>'.$label.'</span>');
            $this->assertNotFalse($position, 'The '.$key.' section did not render at all.');
            $positions[$key] = $position;
        }

        asort($positions);

        return array_keys($positions);
    }

    public function test_sections_keep_their_original_order_when_nothing_is_configured(): void
    {
        $this->assertSame(
            ['info', 'location', 'coaches', 'gallery', 'registration'],
            $this->renderedSectionOrder($this->trainingWithEverySection()),
            'A training without a configured order must render the layout the page shipped with.'
        );
    }

    /**
     * The reported ask: the gallery and the coaches belong at the bottom of the page,
     * and it has to be doable without a code change.
     */
    public function test_configured_order_moves_the_gallery_and_the_coaches_to_the_bottom(): void
    {
        $training = $this->trainingWithEverySection([
            'info', 'location', 'registration', 'gallery', 'coaches',
        ]);

        $this->assertSame(
            ['info', 'location', 'registration', 'gallery', 'coaches'],
            $this->renderedSectionOrder($training),
            'The page must follow the section order configured on the training.'
        );
    }

    /**
     * A stored order is a hint, never the whole truth: it can predate a section or
     * name one that no longer exists. Every known section still has to render once.
     */
    public function test_unknown_and_missing_keys_fall_back_to_the_default_order(): void
    {
        $training = $this->trainingWithEverySection(['gallery', 'does-not-exist', 'gallery']);

        $this->assertSame(
            ['gallery', 'info', 'location', 'coaches', 'registration'],
            $this->renderedSectionOrder($training),
            'Unknown keys must be dropped and missing sections appended in their default position.'
        );
    }

    public function test_section_order_attribute_is_normalised_on_read(): void
    {
        $training = new Training;

        $this->assertSame(Training::DEFAULT_SECTION_ORDER, $training->section_order);

        $training->section_order = ['registration'];

        $this->assertSame(
            ['registration', 'info', 'location', 'coaches', 'gallery'],
            $training->section_order
        );
    }
}
