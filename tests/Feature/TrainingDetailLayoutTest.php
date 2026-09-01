<?php

namespace Tests\Feature;

use App\Models\CoachProfile;
use App\Models\SportCategory;
use App\Models\Team;
use App\Models\Training;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The public training detail page ships one fixed section order — hero, info,
 * location, registration, coaches, gallery — plus an in-page navigation built
 * from the sections that actually rendered.
 */
class TrainingDetailLayoutTest extends TestCase
{
    use RefreshDatabase;

    protected Team $team;

    protected SportCategory $sportCategory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->team = Team::factory()->create([
            'name' => ['sk' => 'BCZ Club'],
            'slug' => 'bcz-club',
        ]);

        $this->sportCategory = SportCategory::factory()->create(['team_id' => $this->team->id]);
    }

    /** @param  array<string, mixed>  $attributes */
    private function training(array $attributes = []): Training
    {
        return Training::factory()->create([
            'team_id' => $this->team->id,
            'sport_category_id' => $this->sportCategory->id,
            'title' => ['sk' => 'Street Workout'],
            'slug' => 'street-workout',
            'is_active' => true,
            ...$attributes,
        ]);
    }

    private function withCoach(Training $training): Training
    {
        $coach = User::factory()->create(['name' => 'Jozef Trener']);

        CoachProfile::factory()->create([
            'user_id' => $coach->id,
            'biography' => ['sk' => '<p>Bio</p>', 'en' => '<p>Bio</p>'],
        ]);

        $training->coaches()->attach($coach, ['role' => 'main']);

        return $training->refresh();
    }

    /** A training that renders every single section. */
    private function trainingWithEverySection(): Training
    {
        return $this->withCoach($this->training([
            'gallery_images' => ['trainings/gallery/photo-one.jpg', 'trainings/gallery/photo-two.jpg'],
        ]));
    }

    private function render(Training $training): string
    {
        return $this->get($training->getLinkUrl())->assertOk()->getContent();
    }

    /**
     * The order the sections appear in on the rendered page, derived from the
     * position of each section's anchor element.
     *
     * @return list<string>
     */
    private function renderedSectionOrder(string $html): array
    {
        $positions = [];

        foreach (['info', 'location', 'registration', 'coaches', 'gallery'] as $key) {
            $position = strpos($html, '<section id="'.$key.'"');

            if ($position !== false) {
                $positions[$key] = $position;
            }
        }

        asort($positions);

        return array_keys($positions);
    }

    /**
     * The client's ask: registration higher up, the coaches below it, the gallery last.
     */
    public function test_sections_render_in_the_hardcoded_order(): void
    {
        $this->assertSame(
            ['info', 'location', 'registration', 'coaches', 'gallery'],
            $this->renderedSectionOrder($this->render($this->trainingWithEverySection()))
        );
    }

    public function test_backgrounds_alternate_down_the_page(): void
    {
        $html = $this->render($this->trainingWithEverySection());

        foreach ([
            'info' => 'bg-[#0A0A0A]',
            'location' => 'bg-[#111111]',
            'registration' => 'bg-[#0A0A0A]',
            'coaches' => 'bg-[#111111]',
            'gallery' => 'bg-[#0A0A0A]',
        ] as $key => $background) {
            $this->assertStringContainsString(
                '<section id="'.$key.'" class="'.$background.' py-20',
                $html,
                'The '.$key.' section lost its place in the background banding.'
            );
        }
    }

    /**
     * Banding is computed from the sections that really render, so hiding one in
     * the middle must not leave two identical backgrounds stacked on each other.
     */
    public function test_backgrounds_still_alternate_when_a_section_is_hidden(): void
    {
        $html = $this->render($this->withCoach($this->training([
            'place_name' => ['sk' => '', 'en' => ''],
            'place_address' => null,
            'gathering_place' => ['sk' => '', 'en' => ''],
            'gallery_images' => [],
        ])));

        $this->assertStringNotContainsString('<section id="location"', $html);
        $this->assertStringContainsString('<section id="info" class="bg-[#0A0A0A] py-20', $html);
        $this->assertStringContainsString('<section id="registration" class="bg-[#111111] py-20', $html);
        $this->assertStringContainsString('<section id="coaches" class="bg-[#0A0A0A] py-20', $html);
    }

    public function test_section_navigation_links_every_rendered_section(): void
    {
        $html = $this->render($this->trainingWithEverySection());

        // The nav sits between the hero and the first section, so slicing the page
        // at the info anchor leaves exactly the navigation to assert against.
        $navStart = strpos($html, 'aria-label="'.__('training_detail.nav_sections_label').'"');
        $this->assertNotFalse($navStart, 'The section navigation did not render.');

        $nav = substr($html, $navStart, strpos($html, '<section id="info"') - $navStart);

        foreach ([
            'info' => __('training_detail.about_label'),
            'location' => __('training_detail.location_label'),
            'registration' => __('training_detail.form_label'),
            'coaches' => __('training_detail.coach_title'),
            'gallery' => __('training_detail.gallery_title'),
        ] as $key => $label) {
            $this->assertStringContainsString('href="#'.$key.'"', $nav, 'The nav is missing the '.$key.' link.');
            $this->assertStringContainsString($label, $nav, 'The '.$key.' link is missing its Slovak label.');
        }

        // Sticky under the site header, and a fixed sidebar on very wide screens.
        $this->assertStringContainsString('sticky top-16 lg:top-20', $nav);
        $this->assertStringContainsString('min-[1800px]:fixed', $nav);
    }

    /**
     * A training with no coaches and no gallery must not offer dead links to them.
     */
    public function test_section_navigation_omits_sections_that_do_not_render(): void
    {
        $html = $this->render($this->training(['gallery_images' => []]));

        $this->assertStringContainsString('href="#info"', $html);
        $this->assertStringContainsString('href="#location"', $html);
        $this->assertStringContainsString('href="#registration"', $html);

        $this->assertStringNotContainsString('href="#coaches"', $html);
        $this->assertStringNotContainsString('href="#gallery"', $html);
        $this->assertStringNotContainsString('<section id="coaches"', $html);
        $this->assertStringNotContainsString('<section id="gallery"', $html);
        $this->assertStringNotContainsString(__('training_detail.coach_title'), $html);
    }

    /**
     * With nothing but the info block and the form there is nothing to navigate,
     * so the bar degrades to no bar at all rather than to a two-link stub.
     */
    public function test_section_navigation_is_absent_when_there_is_nothing_to_navigate(): void
    {
        $html = $this->render($this->training([
            'place_name' => ['sk' => '', 'en' => ''],
            'place_address' => null,
            'gathering_place' => ['sk' => '', 'en' => ''],
            'gallery_images' => [],
        ]));

        $this->assertStringNotContainsString('aria-label="'.__('training_detail.nav_sections_label').'"', $html);
        $this->assertStringNotContainsString('href="#info"', $html);
        $this->assertStringContainsString('<section id="info"', $html);
        $this->assertStringContainsString('<section id="registration"', $html);
    }

    /**
     * Carried over from #39: the coach biography is rendered as HTML, and the
     * reorder must not resurrect the escaped version.
     */
    public function test_coach_biography_still_renders_as_html_after_the_reorder(): void
    {
        $html = $this->render($this->trainingWithEverySection());

        $this->assertStringContainsString(
            '<div class="text-[#888888] text-[15px] leading-[1.7] space-y-4"><p>Bio</p></div>',
            $html
        );
    }
}
