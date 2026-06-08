<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    private function publishHomepage(array $overrides = []): Page
    {
        return Page::factory()->published()->create(array_merge([
            'title' => ['sk' => 'Domov'],
            'slug' => '/',
            'is_system' => true,
            'system_key' => 'homepage',
            'content' => [
                ['type' => 'masonBrick', 'attrs' => ['id' => 'hero', 'config' => ['title' => ['sk' => 'PREKONAJ']]]],
            ],
        ], $overrides));
    }

    public function test_public_layout_emits_core_seo_meta_tags(): void
    {
        $this->publishHomepage();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('<meta name="description"', false);
        $response->assertSee('<meta property="og:title"', false);
        $response->assertSee('<meta property="og:image"', false);
        $response->assertSee('<meta property="og:url"', false);
        $response->assertSee('<meta name="twitter:card" content="summary_large_image">', false);
        $response->assertSee('og-default.png', false);
        $response->assertSee('"@type":"Organization"', false);
        $response->assertSee('<meta name="robots" content="index, follow">', false);
    }

    public function test_page_meta_description_is_rendered_in_meta_tag(): void
    {
        $this->publishHomepage([
            'meta_description' => ['sk' => 'Vlastný SEO popis stránky.'],
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('<meta name="description" content="Vlastný SEO popis stránky.">', false);
        $response->assertSee('<meta property="og:description" content="Vlastný SEO popis stránky.">', false);
    }

    public function test_page_falls_back_to_default_description(): void
    {
        $this->publishHomepage();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee(__('seo.default_description'), false);
    }

    public function test_sitemap_returns_xml_with_published_pages(): void
    {
        $this->publishHomepage();
        Page::factory()->published()->create([
            'title' => ['sk' => 'O nás'],
            'slug' => 'o-nas',
        ]);
        Page::factory()->create([
            'title' => ['sk' => 'Draft'],
            'slug' => 'draft-page',
        ]);

        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml');
        $response->assertSee('<urlset', false);
        $response->assertSee(url('/o-nas'), false);
        $response->assertSee('hreflang="en"', false);
        $response->assertSee('hreflang="x-default"', false);
        $response->assertDontSee(url('/draft-page'), false);
    }

    public function test_robots_txt_references_sitemap_and_blocks_admin(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertStatus(200);
        $response->assertSee('Disallow: /admin', false);
        $response->assertSee('Disallow: /customer', false);
        $response->assertSee('Disallow: /team-invitations', false);
        $response->assertSee('Disallow: /payment', false);
        $response->assertSee('Disallow: /gopay', false);
        $response->assertSee('Sitemap: '.url('/sitemap.xml'), false);
    }
}
