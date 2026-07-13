<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LegacyRedirectTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('legacyPathsProvider')]
    public function test_legacy_path_permanently_redirects(string $legacyPath, string $target): void
    {
        $response = $this->get($legacyPath);

        $response->assertStatus(301);
        $response->assertRedirect($target);
    }

    /** @return array<string, array{string, string}> */
    public static function legacyPathsProvider(): array
    {
        return [
            'domov' => ['/domov', '/'],
            'two percent (old slug)' => ['/2-percenta-z-dane', '/dva-percenta-z-dane'],
            'membership page' => ['/stan-sa-clenom-timu-street-workout-kysuce', '/trenuj-s-nami'],
            'podujatia a sutaze' => ['/podujatia-a-sutaze', '/eventy'],
            'blog' => ['/blog', '/eventy'],
            'blog with trailing slash' => ['/blog/', '/eventy'],
            'category sutaze' => ['/category/sutaze', '/sutaze'],
            'category exhibicie' => ['/category/exhibicie', '/akrobaticke-vystupenia'],
            'category wildcard vylety' => ['/category/vylety', '/eventy'],
            'category wildcard beneficne' => ['/category/beneficne-vystupenia', '/eventy'],
            'category wildcard pagination' => ['/category/sutaze/page/2', '/eventy'],
            'tim wildcard' => ['/tim/nejaky-tim', '/timy'],
        ];
    }

    #[DataProvider('renamedEventSlugsProvider')]
    public function test_renamed_blog_post_slug_redirects_to_mapped_event(string $oldSlug, string $eventSlug): void
    {
        $response = $this->get('/'.$oldSlug);

        $response->assertStatus(301);
        $response->assertRedirect('/eventy/'.$eventSlug);
    }

    /** @return array<string, array{string, string}> */
    public static function renamedEventSlugsProvider(): array
    {
        return [
            'msr freestyle 2023' => [
                'majstrovstva-slovenska-freestyle-2023-v-presove',
                'majstrovstva-v-street-workout-e-2023-boli-velkolepe-freestyle-atleti-hviezdili',
            ],
            'hotel dixon' => [
                'exhibicia-hotel-dixon-banska-bystrica',
                'street-workout-kysuce-na-plese-sportovcov-v-banskej-bystrici',
            ],
            'zilinska univerzita' => [
                'exhibicia-na-zilinskej-univerzite',
                'exhibicia-pre-fakultu-bezpecnostneho-inzinierstva-uniza',
            ],
            'cvc cadca 60th anniversary' => [
                'street-workout-kysuce-nadchol-v-cadci-silova-exhibicia-na-60-vyrocie-cvc-ukazala-co-dokaze-ludske-telo',
                'street-workout-kysuce-ohuril-exhibiciou-pri-60-vyroci-cvc-cadca',
            ],
            'gym hall trip with emoji' => [
                'vylet-z-kruzku-do-gymnastickej-haly-🏆🔥',
                'vylet-z-kruzku-do-gymnastickej-haly',
            ],
        ];
    }

    public function test_unchanged_blog_post_slug_redirects_to_published_event_with_same_slug(): void
    {
        $event = Event::factory()->create([
            'title' => ['sk' => 'Posúťažný report z MSR'],
        ]);

        $response = $this->get('/'.$event->slug);

        $response->assertStatus(301);
        $response->assertRedirect('/eventy/'.$event->slug);
    }

    public function test_unpublished_event_slug_returns_404(): void
    {
        $event = Event::factory()->draft()->create([
            'title' => ['sk' => 'Neuverejnený event'],
        ]);

        $this->get('/'.$event->slug)->assertStatus(404);
    }

    public function test_unknown_slug_still_returns_404(): void
    {
        $this->get('/toto-neexistuje')->assertStatus(404);
    }

    public function test_cms_page_slug_wins_over_event_with_same_slug(): void
    {
        Page::factory()->published()->create([
            'title' => ['sk' => 'Zdieľaný slug'],
            'slug' => 'zdielany-slug',
            'content' => [],
        ]);
        Event::factory()->create([
            'title' => ['sk' => 'Zdieľaný slug'],
        ]);

        $this->get('/zdielany-slug')->assertStatus(200);
    }
}
