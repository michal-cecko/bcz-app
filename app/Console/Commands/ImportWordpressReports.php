<?php

namespace App\Console\Commands;

use App\Enums\EventTypeEnum;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Setting;
use Carbon\Carbon;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportWordpressReports extends Command
{
    protected $signature = 'app:import-wordpress-reports
        {--source=https://streetworkoutkysuce.sk : WordPress site base URL}
        {--limit= : Import only the first N posts (newest first)}
        {--slug= : Import only a single post by WP slug}
        {--dry-run : Parse and report without persisting}
        {--force : Re-import even if an event with the same slug exists (hard-deletes the existing one first)}';

    protected $description = 'Import WordPress blog posts as REPORT-type Events with Mason brick content';

    /** @var array<string, string> source URL → stored path */
    private array $downloadCache = [];

    public function handle(): int
    {
        $source = rtrim((string) $this->option('source'), '/');
        $teamId = Setting::get('default_team_id');

        if (! $teamId) {
            $this->error('No default_team_id set in Settings.');

            return self::FAILURE;
        }

        $posts = $this->fetchAllPosts($source);

        // Import oldest→newest so Event.created_at order reflects original publish order.
        $posts = $posts->sortBy(fn (array $p) => $p['date'] ?? '')->values();

        if ($slug = $this->option('slug')) {
            $posts = $posts->filter(fn (array $p): bool => ($p['slug'] ?? null) === $slug)->values();
        }
        if ($limit = $this->option('limit')) {
            $posts = $posts->take((int) $limit);
        }

        $this->info("Importing {$posts->count()} post(s) from {$source}");

        foreach ($posts as $post) {
            $this->line("\n→ {$post['slug']}");
            try {
                $this->importPost($post, $teamId);
            } catch (\Throwable $e) {
                $this->error("   FAILED: {$e->getMessage()}");
                report($e);
            }
        }

        return self::SUCCESS;
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function fetchAllPosts(string $source): Collection
    {
        $posts = collect();
        $page = 1;

        while (true) {
            $res = Http::timeout(60)->get("$source/wp-json/wp/v2/posts", [
                'per_page' => 100,
                'page' => $page,
                '_embed' => 1,
            ]);

            if ($res->status() === 400 && $page > 1) {
                break;
            }

            if (! $res->successful()) {
                $this->error("Failed to fetch page {$page}: HTTP {$res->status()}");
                break;
            }

            $batch = $res->json();
            if (empty($batch)) {
                break;
            }

            $posts = $posts->concat($batch);

            if (count($batch) < 100) {
                break;
            }
            $page++;
        }

        return $posts;
    }

    /**
     * @param  array<string, mixed>  $post
     */
    private function importPost(array $post, string $teamId): void
    {
        $slug = $post['slug'];
        $dryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');

        $existing = Event::withTrashed()->where('slug', $slug)->first();
        if ($existing) {
            if (! $force) {
                $this->warn('   exists — skipping (use --force to overwrite)');

                return;
            }
            if (! $dryRun) {
                $existing->forceDelete();
                $this->line('   existing event deleted');
            }
        }

        $title = $this->decodeText($post['title']['rendered'] ?? '');
        $excerpt = Str::of($this->decodeText($post['excerpt']['rendered'] ?? ''))
            ->trim()
            ->limit(240, '…')
            ->toString();
        $html = (string) ($post['content']['rendered'] ?? '');
        $date = Carbon::parse($post['date'] ?? now());

        $featuredUrl = $post['_embedded']['wp:featuredmedia'][0]['source_url'] ?? null;

        $this->downloadCache = [];
        $bricks = $this->htmlToBricks($html, $slug, $dryRun);

        $imageCount = count(array_filter($bricks, fn (array $b) => $b['type'] === 'image'));
        $galleryCount = count(array_filter($bricks, fn (array $b) => $b['type'] === 'gallery'));
        $videoCount = count(array_filter($bricks, fn (array $b) => $b['type'] === 'video-section'));
        $this->line(sprintf(
            '   "%s" — %d bricks (%d img, %d gallery, %d video), featured=%s',
            $title,
            count($bricks),
            $imageCount,
            $galleryCount,
            $videoCount,
            $featuredUrl ? 'yes' : 'no'
        ));

        if ($dryRun) {
            return;
        }

        $categoryId = EventCategory::query()->inRandomOrder()->value('id');
        if (! $categoryId) {
            $this->error('   no EventCategory rows exist — cannot import');

            return;
        }

        $event = Event::create([
            'team_id' => $teamId,
            'event_category_id' => $categoryId,
            'event_type' => EventTypeEnum::Report,
            'title' => ['sk' => $title],
            'slug' => $slug,
            'card_description' => ['sk' => $excerpt],
            'date' => $date->toDateString(),
            'timezone' => 'Europe/Bratislava',
            'content' => [
                'sk' => $this->wrapMasonBricks($bricks),
            ],
            'is_published' => false,
            'published_at' => $date,
        ]);

        if ($featuredUrl) {
            try {
                $event->addMediaFromUrl($featuredUrl)->toMediaCollection('card_image');
                $event->addMediaFromUrl($featuredUrl)->toMediaCollection('detail_image');
            } catch (\Throwable $e) {
                $this->warn("   featured media failed: {$e->getMessage()}");
            }
        }

        $this->info("   ✓ event {$event->id} (unpublished, sk-only)");
    }

    /**
     * @param  array<int, array<string, mixed>>  $flat
     * @return array<int, array<string, mixed>>
     */
    private function wrapMasonBricks(array $flat): array
    {
        return array_map(fn (array $b): array => [
            'type' => 'masonBrick',
            'attrs' => [
                'id' => $b['type'],
                'config' => array_diff_key($b, ['type' => true]),
            ],
        ], $flat);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function htmlToBricks(string $html, string $slug, bool $dryRun): array
    {
        $bricks = [];
        $richBuf = '';

        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $wrapped = '<?xml encoding="utf-8" ?><html><body>'.$html.'</body></html>';
        $dom->loadHTML($wrapped, LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $body = $dom->getElementsByTagName('body')->item(0);
        if (! $body) {
            return [];
        }

        foreach (iterator_to_array($body->childNodes) as $node) {
            $this->processNode($node, $bricks, $richBuf, $slug, $dryRun);
        }
        $this->flushRich($bricks, $richBuf);

        return $bricks;
    }

    /**
     * @param  array<int, array<string, mixed>>  $bricks
     */
    private function processNode(DOMNode $node, array &$bricks, string &$richBuf, string $slug, bool $dryRun): void
    {
        // Plain text nodes — only append if non-whitespace
        if (! $node instanceof DOMElement) {
            if ($node->nodeValue !== null && trim($node->nodeValue) !== '') {
                $richBuf .= $node->ownerDocument->saveHTML($node);
            }

            return;
        }

        $tag = strtolower($node->nodeName);

        if (in_array($tag, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'], true)) {
            $this->flushRich($bricks, $richBuf);
            $text = trim($this->decodeText($this->innerHtml($node)));
            if ($text !== '') {
                $level = match ($tag) {
                    'h1', 'h2' => 'h2',
                    'h3' => 'h3',
                    default => 'h4',
                };
                $bricks[] = ['type' => 'heading', 'level' => $level, 'text' => ['sk' => $text]];
            }

            return;
        }

        if ($tag === 'figure') {
            $iframes = $this->findIframes($node);
            if ($iframes !== []) {
                $this->flushRich($bricks, $richBuf);
                $this->emitVideo($bricks, $iframes[0], $node);

                return;
            }
            $imgs = $this->findImages($node);
            if (count($imgs) > 1) {
                $this->flushRich($bricks, $richBuf);
                $this->emitGallery($bricks, $imgs, $slug, $dryRun);

                return;
            }
            if (count($imgs) === 1) {
                $this->flushRich($bricks, $richBuf);
                $this->emitImage($bricks, $imgs[0], $node, $slug, $dryRun);

                return;
            }
            // No image/iframe — fall through to rich-text
        }

        if ($tag === 'p') {
            $imgs = $this->findImages($node);
            $textOnly = trim($this->decodeText($this->innerHtml($node)));
            if (count($imgs) > 0 && $textOnly === '') {
                $this->flushRich($bricks, $richBuf);
                if (count($imgs) === 1) {
                    $this->emitImage($bricks, $imgs[0], $node, $slug, $dryRun);
                } else {
                    $this->emitGallery($bricks, $imgs, $slug, $dryRun);
                }

                return;
            }
        }

        if ($tag === 'blockquote') {
            $this->flushRich($bricks, $richBuf);
            $inner = trim($this->innerHtml($node));
            if ($inner !== '') {
                $bricks[] = ['type' => 'quote', 'quote' => ['sk' => $inner]];
            }

            return;
        }

        if ($tag === 'iframe') {
            $this->flushRich($bricks, $richBuf);
            $this->emitVideo($bricks, $node, null);

            return;
        }

        if (in_array($tag, ['div', 'section', 'article'], true)) {
            $class = (string) $node->getAttribute('class');
            $iframes = $this->findIframes($node);
            if ($iframes !== [] && $this->findImages($node) === []) {
                $this->flushRich($bricks, $richBuf);
                $this->emitVideo($bricks, $iframes[0], $node);

                return;
            }

            $imgs = $this->findImages($node);

            // Column/gallery-like wrappers with multiple images → gallery brick
            $isGalleryWrapper = $imgs !== []
                && (str_contains($class, 'gallery')
                    || str_contains($class, 'wp-block-columns')
                    || count($imgs) >= 2);

            if ($isGalleryWrapper && count($imgs) > 1) {
                $this->flushRich($bricks, $richBuf);
                $this->emitGallery($bricks, $imgs, $slug, $dryRun);

                return;
            }

            foreach (iterator_to_array($node->childNodes) as $child) {
                $this->processNode($child, $bricks, $richBuf, $slug, $dryRun);
            }

            return;
        }

        // Default: preserve inline HTML (p, ul, ol, strong, em, a, br, etc.)
        // Strip any stray iframes — Tiptap drops them on re-save anyway, and we've already
        // extracted YouTube/Vimeo embeds as video-section bricks above.
        $html = $node->ownerDocument->saveHTML($node);
        $html = (string) preg_replace('#<iframe\b[^>]*>.*?</iframe>#is', '', $html);
        $richBuf .= $html;
    }

    /**
     * @param  array<int, array<string, mixed>>  $bricks
     */
    private function emitImage(array &$bricks, DOMElement $img, DOMElement $figureOrP, string $slug, bool $dryRun): void
    {
        $path = $this->downloadImage($img, $slug, $dryRun);
        if (! $path) {
            return;
        }

        $cfg = ['type' => 'image', 'image' => $path];

        $alt = trim((string) $img->getAttribute('alt'));
        if ($alt !== '') {
            $cfg['alt'] = ['sk' => $alt];
        }

        $figcap = $figureOrP->getElementsByTagName('figcaption')->item(0);
        if ($figcap instanceof DOMElement) {
            $caption = trim($this->decodeText($this->innerHtml($figcap)));
            if ($caption !== '') {
                $cfg['caption'] = ['sk' => $caption];
            }
        }

        $bricks[] = $cfg;
    }

    /**
     * @param  array<int, array<string, mixed>>  $bricks
     * @param  array<int, DOMElement>  $imgs
     */
    private function emitGallery(array &$bricks, array $imgs, string $slug, bool $dryRun): void
    {
        $paths = [];
        foreach ($imgs as $img) {
            $p = $this->downloadImage($img, $slug, $dryRun);
            if ($p) {
                $paths[] = $p;
            }
        }
        if ($paths !== []) {
            $bricks[] = ['type' => 'gallery', 'images' => $paths];
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $bricks
     */
    private function flushRich(array &$bricks, string &$richBuf): void
    {
        $richBuf = trim($richBuf);
        if ($richBuf !== '') {
            $bricks[] = ['type' => 'rich-text', 'content' => ['sk' => $richBuf]];
        }
        $richBuf = '';
    }

    /**
     * @return array<int, DOMElement>
     */
    private function findImages(DOMNode $node): array
    {
        $xpath = new DOMXPath($node->ownerDocument);
        $out = [];
        /** @var DOMElement $img */
        foreach ($xpath->query('.//img', $node) as $img) {
            $out[] = $img;
        }

        return $out;
    }

    /**
     * @return array<int, DOMElement>
     */
    private function findIframes(DOMNode $node): array
    {
        $xpath = new DOMXPath($node->ownerDocument);
        $out = [];
        /** @var DOMElement $iframe */
        foreach ($xpath->query('.//iframe', $node) as $iframe) {
            $out[] = $iframe;
        }

        return $out;
    }

    /**
     * @param  array<int, array<string, mixed>>  $bricks
     */
    private function emitVideo(array &$bricks, DOMElement $iframe, ?DOMElement $wrapper): void
    {
        $src = trim((string) $iframe->getAttribute('src'));
        if ($src === '' || ! $this->isEmbeddableVideoUrl($src)) {
            return;
        }
        $src = $this->normalizeVideoEmbedUrl($src);

        $title = trim((string) $iframe->getAttribute('title'));
        if ($title === '' && $wrapper) {
            $figcap = $wrapper->getElementsByTagName('figcaption')->item(0);
            if ($figcap instanceof DOMElement) {
                $title = trim($this->decodeText($this->innerHtml($figcap)));
            }
        }
        if ($title === '') {
            $title = 'Video';
        }

        $subtitle = null;
        if ($wrapper) {
            $figcap = $wrapper->getElementsByTagName('figcaption')->item(0);
            if ($figcap instanceof DOMElement) {
                $cap = trim($this->decodeText($this->innerHtml($figcap)));
                if ($cap !== '' && $cap !== $title) {
                    $subtitle = $cap;
                }
            }
        }

        $cfg = [
            'type' => 'video-section',
            'title' => ['sk' => $title, 'en' => $title, 'cs' => $title],
            'video_source' => 'url',
            'video_url' => $src,
        ];
        if ($subtitle !== null) {
            $cfg['subtitle'] = ['sk' => $subtitle, 'en' => $subtitle, 'cs' => $subtitle];
        }

        $bricks[] = $cfg;
    }

    private function normalizeVideoEmbedUrl(string $url): string
    {
        // YouTube: any form → https://www.youtube.com/embed/<VIDEO_ID>
        if (preg_match('#(?:youtube\.com/(?:embed/|watch\?v=|shorts/)|youtu\.be/)([A-Za-z0-9_-]{11})#', $url, $m)) {
            return 'https://www.youtube.com/embed/'.$m[1];
        }

        // Vimeo: any form → https://player.vimeo.com/video/<VIDEO_ID>
        if (preg_match('#vimeo\.com/(?:video/)?(\d+)#', $url, $m)) {
            return 'https://player.vimeo.com/video/'.$m[1];
        }

        return $url;
    }

    private function isEmbeddableVideoUrl(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);
        if (! $host) {
            return false;
        }
        $host = strtolower($host);

        foreach (['youtube.com', 'youtu.be', 'vimeo.com', 'player.vimeo.com'] as $needle) {
            if (str_contains($host, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function innerHtml(DOMElement $el): string
    {
        $html = '';
        foreach ($el->childNodes as $child) {
            $html .= $el->ownerDocument->saveHTML($child);
        }

        return $html;
    }

    private function decodeText(string $html): string
    {
        return html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    private function downloadImage(DOMElement $img, string $slug, bool $dryRun): ?string
    {
        $src = $this->pickBestImageSrc($img);
        if (! $src) {
            return null;
        }
        if (isset($this->downloadCache[$src])) {
            return $this->downloadCache[$src];
        }
        if ($dryRun) {
            return 'DRY_RUN_'.md5($src);
        }

        try {
            $res = Http::timeout(60)->get($src);
            if (! $res->successful()) {
                $this->warn("   image HTTP {$res->status()}: {$src}");

                return null;
            }

            $urlPath = (string) parse_url($src, PHP_URL_PATH);
            $ext = strtolower(pathinfo($urlPath, PATHINFO_EXTENSION) ?: 'jpg');
            $name = Str::slug(pathinfo($urlPath, PATHINFO_FILENAME)) ?: Str::random(8);
            $storedPath = "bricks/wp-imports/{$slug}/{$name}.{$ext}";

            Storage::disk('public')->put($storedPath, $res->body(), ['visibility' => 'public']);
            $this->downloadCache[$src] = $storedPath;

            return $storedPath;
        } catch (\Throwable $e) {
            $this->warn("   image error: {$e->getMessage()}");

            return null;
        }
    }

    private function pickBestImageSrc(DOMElement $img): ?string
    {
        $srcset = (string) $img->getAttribute('srcset');
        if ($srcset !== '') {
            $bestUrl = null;
            $bestW = 0;
            foreach (explode(',', $srcset) as $part) {
                $p = trim($part);
                if (preg_match('/^(\S+)\s+(\d+)w$/', $p, $m)) {
                    $w = (int) $m[2];
                    if ($w > $bestW) {
                        $bestW = $w;
                        $bestUrl = $m[1];
                    }
                }
            }
            if ($bestUrl) {
                return $bestUrl;
            }
        }

        $src = (string) $img->getAttribute('src');
        if ($src === '') {
            $src = (string) $img->getAttribute('data-src');
        }

        return $src !== '' ? $src : null;
    }
}
