<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageControllerTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\DataProvider('systemPagesProvider')]
    public function test_system_page_returns_200_and_shows_content(string $systemKey, string $slug, string $titleSk, array $content, string $expectedText): void
    {
        Page::factory()->published()->create([
            'title' => ['sk' => $titleSk],
            'slug' => $slug,
            'is_system' => true,
            'system_key' => $systemKey,
            'content' => $content,
        ]);

        $url = $slug === '/' ? '/' : '/'.$slug;
        $response = $this->get($url);

        $response->assertStatus(200);
        $response->assertSee($expectedText);
    }

    public static function systemPagesProvider(): array
    {
        return [
            'homepage' => [
                'homepage',
                '/',
                'Domov',
                [
                    ['type' => 'masonBrick', 'attrs' => ['id' => 'hero', 'config' => ['badge' => ['sk' => 'BEYOND COMFORT ZONE'], 'title' => ['sk' => 'PREKONAJ'], 'title_accent' => ['sk' => 'SVOJE LIMITY']]]],
                ],
                'PREKONAJ',
            ],
            'about' => [
                'about',
                'o-nas',
                'O nás',
                [
                    ['type' => 'masonBrick', 'attrs' => ['id' => 'hero', 'config' => ['title' => ['sk' => 'NÁŠ PRÍBEH'], 'subtitle' => ['sk' => 'Od skupiny priateľov']]]],
                ],
                'NÁŠ PRÍBEH',
            ],
            'contact' => [
                'contact',
                'kontakt',
                'Kontakt',
                [
                    ['type' => 'masonBrick', 'attrs' => ['id' => 'hero', 'config' => ['title' => ['sk' => 'Napíšte nám'], 'badge' => ['sk' => 'KONTAKT']]]],
                ],
                'Napíšte nám',
            ],
            'faq' => [
                'faq',
                'faq',
                'FAQ',
                [
                    ['type' => 'masonBrick', 'attrs' => ['id' => 'hero', 'config' => ['title' => ['sk' => 'Často kladené otázky']]]],
                ],
                'Často kladené otázky',
            ],
            'support' => [
                'support',
                'podporte-nas',
                'Podporte nás',
                [
                    ['type' => 'masonBrick', 'attrs' => ['id' => 'hero', 'config' => ['title' => ['sk' => 'Pomôžte nám rásť']]]],
                ],
                'Pomôžte nám rásť',
            ],
            'founder' => [
                'founder',
                'zakladatel-ceo-dominik-klimek',
                'Zakladateľ & CEO — Dominik Klimek',
                [
                    ['type' => 'masonBrick', 'attrs' => ['id' => 'hero', 'config' => ['title' => ['sk' => 'Dominik Klimek'], 'subtitle' => ['sk' => 'Majster sveta']]]],
                ],
                'Dominik Klimek',
            ],
            'tax_donation' => [
                'tax_donation',
                'dva-percenta-z-dane',
                '2% z dane',
                [
                    ['type' => 'masonBrick', 'attrs' => ['id' => 'hero', 'config' => ['title' => ['sk' => 'Darujte nám 2% z dane']]]],
                ],
                'Darujte nám 2% z dane',
            ],
            'services' => [
                'services',
                'vystupenia-workshopy',
                'Vystúpenia & Workshopy',
                [
                    ['type' => 'masonBrick', 'attrs' => ['id' => 'hero', 'config' => ['title' => ['sk' => 'Vystúpenia, Workshopy & Prednášky']]]],
                ],
                'Vystúpenia, Workshopy',
            ],
            'lectures' => [
                'lectures',
                'prednasky',
                'Inšpiratívne Prednášky',
                [
                    ['type' => 'masonBrick', 'attrs' => ['id' => 'hero', 'config' => ['title' => ['sk' => 'INŠPIRATÍVNE PREDNÁŠKY']]]],
                ],
                'INŠPIRATÍVNE PREDNÁŠKY',
            ],
            'workshops' => [
                'workshops',
                'workshopy',
                'Praktické Workshopy',
                [
                    ['type' => 'masonBrick', 'attrs' => ['id' => 'hero', 'config' => ['title' => ['sk' => 'PRAKTICKÉ WORKSHOPY']]]],
                ],
                'PRAKTICKÉ WORKSHOPY',
            ],
            'parkour' => [
                'parkour',
                'kategoria/parkour-freerunning',
                'Parkour & Freerunning',
                [
                    ['type' => 'masonBrick', 'attrs' => ['id' => 'hero', 'config' => ['title' => ['sk' => 'Parkour & Freerunning'], 'subtitle' => ['sk' => 'Umenie pohybu']]]],
                ],
                'Umenie pohybu',
            ],
            'street_workout' => [
                'street_workout',
                'kategoria/street-workout',
                'Street Workout & Kalistenika',
                [
                    ['type' => 'masonBrick', 'attrs' => ['id' => 'hero', 'config' => ['title' => ['sk' => 'Street Workout & Kalistenika'], 'subtitle' => ['sk' => 'Ovládni svoje telo']]]],
                ],
                'Ovládni svoje telo',
            ],
        ];
    }

    public function test_nonexistent_page_returns_404(): void
    {
        $response = $this->get('/nonexistent-page-slug');

        $response->assertStatus(404);
    }

    public function test_draft_page_returns_404(): void
    {
        Page::factory()->create([
            'title' => ['sk' => 'Draft Page'],
            'slug' => 'draft-page',
        ]);

        $response = $this->get('/draft-page');

        $response->assertStatus(404);
    }

    public function test_archiv_trenerov_redirects_to_treningy(): void
    {
        $response = $this->get('/archiv-trenerov');

        $response->assertRedirect('/treningy');
        $response->assertStatus(301);
    }

    public function test_page_with_faq_brick_renders_accordion(): void
    {
        \App\Models\Faq::factory()->create([
            'question' => ['sk' => 'Test question?'],
            'answer' => ['sk' => '<p>Test answer.</p>'],
            'is_published' => true,
        ]);

        Page::factory()->published()->create([
            'slug' => 'test-faq',
            'content' => [
                ['type' => 'masonBrick', 'attrs' => ['id' => 'faq', 'config' => [
                    'heading' => ['sk' => 'FAQ'],
                ]]],
            ],
        ]);

        $response = $this->get('/test-faq');

        $response->assertStatus(200);
        $response->assertSee('Test question?');
        $response->assertSee('Test answer.');
    }

    public function test_page_with_timeline_brick_renders(): void
    {
        Page::factory()->published()->create([
            'slug' => 'test-timeline',
            'content' => [
                ['type' => 'masonBrick', 'attrs' => ['id' => 'timeline', 'config' => [
                    'items' => [
                        ['year' => '2020', 'title' => ['sk' => 'Timeline Event'], 'description' => ['sk' => 'Something happened.']],
                    ],
                ]]],
            ],
        ]);

        $response = $this->get('/test-timeline');

        $response->assertStatus(200);
        $response->assertSee('Timeline Event');
        $response->assertSee('2020');
    }

    public function test_page_with_skill_cards_brick_renders(): void
    {
        Page::factory()->published()->create([
            'slug' => 'test-skills',
            'content' => [
                ['type' => 'masonBrick', 'attrs' => ['id' => 'skill-cards', 'config' => [
                    'levels' => [
                        [
                            'name' => ['sk' => 'ZÁKLADY'],
                            'color' => '#22c55e',
                            'cards' => [
                                ['title' => ['sk' => 'Pull-up'], 'description' => ['sk' => 'Basic pull-up.']],
                            ],
                        ],
                    ],
                ]]],
            ],
        ]);

        $response = $this->get('/test-skills');

        $response->assertStatus(200);
        $response->assertSee('ZÁKLADY');
        $response->assertSee('Pull-up');
    }

    public function test_page_with_numbered_steps_brick_renders(): void
    {
        Page::factory()->published()->create([
            'slug' => 'test-steps',
            'content' => [
                ['type' => 'masonBrick', 'attrs' => ['id' => 'numbered-steps', 'config' => [
                    'steps' => [
                        ['title' => ['sk' => 'Step One'], 'description' => ['sk' => 'First step.']],
                        ['title' => ['sk' => 'Step Two'], 'description' => ['sk' => 'Second step.']],
                    ],
                ]]],
            ],
        ]);

        $response = $this->get('/test-steps');

        $response->assertStatus(200);
        $response->assertSee('Step One');
        $response->assertSee('Step Two');
    }

    public function test_page_with_person_cards_brick_renders(): void
    {
        Page::factory()->published()->create([
            'slug' => 'test-people',
            'content' => [
                ['type' => 'masonBrick', 'attrs' => ['id' => 'person-cards', 'config' => [
                    'people' => [
                        ['name' => ['sk' => 'John Doe'], 'role' => ['sk' => 'Coach'], 'tags' => ['Parkour']],
                    ],
                ]]],
            ],
        ]);

        $response = $this->get('/test-people');

        $response->assertStatus(200);
        $response->assertSee('John Doe');
        $response->assertSee('Coach');
    }

    public function test_page_with_contact_form_brick_renders(): void
    {
        Page::factory()->published()->create([
            'slug' => 'test-contact',
            'content' => [
                ['type' => 'masonBrick', 'attrs' => ['id' => 'contact-form', 'config' => [
                    'show_reason' => true,
                    'show_phone' => true,
                ]]],
            ],
        ]);

        $response = $this->get('/test-contact');

        $response->assertStatus(200);
    }
}
