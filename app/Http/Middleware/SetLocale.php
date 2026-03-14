<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /** URL prefix → internal Laravel locale (prefix = locale for simplicity) */
    public const LOCALE_MAP = [
        'cs' => 'cs',
        'en' => 'en',
    ];

    /** Internal locale → URL prefix */
    public const PREFIX_MAP = [
        'cs' => 'cs',
        'en' => 'en',
    ];

    public const DEFAULT_LOCALE = 'sk';

    public const SUPPORTED_PREFIXES = ['cs', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        // Skip locale switching for admin panel — always use default
        if ($request->is('admin', 'admin/*', 'livewire/*')) {
            app()->setLocale(self::DEFAULT_LOCALE);

            return $next($request);
        }

        $prefix = $request->route('locale');

        if ($prefix && isset(self::LOCALE_MAP[$prefix])) {
            $locale = self::LOCALE_MAP[$prefix];
        } else {
            $locale = self::DEFAULT_LOCALE;
        }

        app()->setLocale($locale);

        // Remove {locale} so controllers never receive it
        if ($prefix) {
            $request->route()->forgetParameter('locale');
        }

        // Auto-inject locale into route() URL generation
        $urlPrefix = self::PREFIX_MAP[$locale] ?? null;
        URL::defaults(['locale' => $urlPrefix]);

        return $next($request);
    }
}
