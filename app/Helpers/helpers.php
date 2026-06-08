<?php

use App\Http\Middleware\SetLocale;
use App\Services\LinkResolver;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

if (! function_exists('brick_trans')) {
    /**
     * Resolve a Mason brick value that may be a translatable array {sk: '...', en: '...'}
     * or a plain string. Returns the value for the current locale, falling back to 'sk'.
     */
    function brick_trans(mixed $value, string $fallback = ''): string
    {
        if (is_array($value)) {
            $locale = app()->getLocale();

            return $value[$locale] ?? $value['sk'] ?? ((string) (reset($value) ?: $fallback));
        }

        return (string) ($value ?? $fallback);
    }
}

if (! function_exists('brick_media_url')) {
    /**
     * Resolve a brick image path or URL to a public URL.
     * Falls back to returning the raw value if it looks like a URL (legacy data).
     */
    function brick_media_url(mixed $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if (is_string($value) && str_starts_with($value, 'http')) {
            return $value;
        }

        return Storage::disk('public')->url($value);
    }
}

if (! function_exists('brick_media')) {
    /**
     * Resolve a brick image path or URL to an object with url, alt, and caption.
     *
     * @return object{url: ?string, alt: ?string, caption: ?string}
     */
    function brick_media(mixed $value): object
    {
        $empty = (object) ['url' => null, 'alt' => null, 'caption' => null];

        if (empty($value)) {
            return $empty;
        }

        if (is_array($value)) {
            $url = $value['url'] ?? $value['path'] ?? null;
            if (empty($url)) {
                return $empty;
            }

            $resolvedUrl = str_starts_with($url, 'http')
                ? $url
                : Storage::disk('public')->url($url);

            return (object) [
                'url' => $resolvedUrl,
                'alt' => $value['alt'] ?? null,
                'caption' => $value['caption'] ?? null,
            ];
        }

        if (is_string($value) && str_starts_with($value, 'http')) {
            return (object) ['url' => $value, 'alt' => null, 'caption' => null];
        }

        if (! is_string($value)) {
            return $empty;
        }

        return (object) [
            'url' => Storage::disk('public')->url($value),
            'alt' => null,
            'caption' => null,
        ];
    }
}

if (! function_exists('isTwoPercentVisible')) {
    /**
     * Whether the "2% z dane" campaign is currently active.
     * Visible from January 1st through April 30th each year.
     */
    function isTwoPercentVisible(): bool
    {
        $now = now();

        return $now->month >= 1 && $now->month <= 4;
    }
}

if (! function_exists('locale_url')) {
    /**
     * Prefix a path with the current locale segment (e.g. /en/treningy).
     * Slovak (default) has no prefix.
     */
    function locale_url(string $path): string
    {
        $locale = app()->getLocale();
        $prefix = SetLocale::PREFIX_MAP[$locale] ?? null;

        if (! $prefix) {
            return $path;
        }

        $path = ltrim($path, '/');

        return '/'.$prefix.($path ? '/'.$path : '');
    }
}

if (! function_exists('locale_switch_url')) {
    /**
     * Generate a URL for switching to a different locale on the current page.
     */
    function locale_switch_url(string $targetLocale): string
    {
        $path = request()->getPathInfo();

        // Strip existing locale prefix
        foreach (SetLocale::SUPPORTED_PREFIXES as $prefix) {
            if (str_starts_with($path, '/'.$prefix.'/') || $path === '/'.$prefix) {
                $path = substr($path, strlen('/'.$prefix)) ?: '/';
                break;
            }
        }

        $urlPrefix = SetLocale::PREFIX_MAP[$targetLocale] ?? null;

        if ($urlPrefix) {
            $path = '/'.$urlPrefix.($path === '/' ? '' : $path);
        }

        $query = request()->getQueryString();

        return $path.($query ? '?'.$query : '');
    }
}

if (! function_exists('brick_link')) {
    /**
     * Resolve structured link data from a Mason brick config to a URL string.
     */
    function brick_link(array $data): ?string
    {
        if (empty($data['link_type'])) {
            return null;
        }

        return LinkResolver::resolve($data);
    }
}

if (! function_exists('embed_video_url')) {
    /**
     * Normalize a YouTube / Vimeo share URL into the iframe-embeddable form.
     * Returns the original URL unchanged if it is already an embed URL or
     * doesn't match a known provider — callers should still validate it.
     *
     * Why: pasting `youtube.com/watch?v=...` or `youtu.be/...` directly into
     * an <iframe src> triggers a "youtube refused to connect" error because
     * YouTube serves those URLs with `X-Frame-Options: SAMEORIGIN`.
     */
    function embed_video_url(?string $url): ?string
    {
        if ($url === null || $url === '') {
            return $url;
        }

        // YouTube: extract the 11-char video ID from any URL shape
        // (watch, share, shorts, live, embed, mobile, no-cookie, with extra params).
        $youtubeHost = '(?:youtu\.be|(?:www\.|m\.|music\.)?youtube(?:-nocookie)?\.com)';
        $youtubePathPrefix = '(?:/(?:embed|shorts|live|v)/|/watch\?(?:[^"\s]*&)?v=|/)';
        $youtubeRegex = '#(?:https?://)?'.$youtubeHost.$youtubePathPrefix.'([A-Za-z0-9_-]{11})#i';

        if (preg_match($youtubeRegex, $url, $m)) {
            return 'https://www.youtube-nocookie.com/embed/'.$m[1];
        }

        // Vimeo
        if (preg_match('#(?:https?://)?(?:www\.)?vimeo\.com/(\d+)#i', $url, $m)) {
            return 'https://player.vimeo.com/video/'.$m[1];
        }

        return $url;
    }
}

if (! function_exists('sk_plural')) {
    /**
     * Slovak pluralization: 1 = singular, 2-4 = few, 5+ = many.
     * Usage: sk_plural($count, 'člen', 'členovia', 'členov')
     */
    function sk_plural(int $count, string $one, string $few, string $many): string
    {
        if ($count === 1) {
            return "{$count} {$one}";
        }

        if ($count >= 2 && $count <= 4) {
            return "{$count} {$few}";
        }

        return "{$count} {$many}";
    }
}

if (! function_exists('seo_description')) {
    /**
     * Normalize arbitrary content into a clean meta description: strip HTML/markup,
     * collapse whitespace, and truncate to a search-engine-friendly length.
     */
    function seo_description(?string $text, int $length = 160): string
    {
        $clean = trim(preg_replace('/\s+/', ' ', strip_tags((string) $text)) ?? '');

        if ($clean === '') {
            return (string) __('seo.default_description');
        }

        return Str::limit($clean, $length);
    }
}

if (! function_exists('seo_og_locale')) {
    /**
     * Map an internal locale key (sk/en/cs) to an Open Graph locale string.
     */
    function seo_og_locale(string $locale): string
    {
        return match ($locale) {
            'en' => 'en_US',
            'cs' => 'cs_CZ',
            default => 'sk_SK',
        };
    }
}
