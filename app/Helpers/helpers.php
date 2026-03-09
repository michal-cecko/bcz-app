<?php

use App\Models\MediaLibraryItem;
use App\Services\LinkResolver;

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
     * Resolve a MediaPicker UUID value to a public URL.
     * Falls back to returning the raw value if it looks like a URL (legacy data).
     */
    function brick_media_url(mixed $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        // Legacy: raw URL string
        if (is_string($value) && str_starts_with($value, 'http')) {
            return $value;
        }

        $item = MediaLibraryItem::find($value);

        return $item?->getFirstMediaUrl('library');
    }
}

if (! function_exists('brick_media')) {
    /**
     * Resolve a MediaPicker UUID value to an object with url, alt, and caption.
     *
     * @return object{url: ?string, alt: ?string, caption: ?string}
     */
    function brick_media(mixed $value): object
    {
        $empty = (object) ['url' => null, 'alt' => null, 'caption' => null];

        if (empty($value)) {
            return $empty;
        }

        if (is_string($value) && str_starts_with($value, 'http')) {
            return (object) ['url' => $value, 'alt' => null, 'caption' => null];
        }

        $item = MediaLibraryItem::find($value);

        if (! $item) {
            return $empty;
        }

        return (object) [
            'url' => $item->getFirstMediaUrl('library'),
            'alt' => $item->alt_text,
            'caption' => $item->caption,
        ];
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
