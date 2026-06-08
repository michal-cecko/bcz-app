<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Http\Middleware\SetLocale;
use App\Models\Event;
use App\Models\Judge;
use App\Models\Page;
use App\Models\Team;
use App\Models\Training;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class SitemapController extends Controller
{
    /**
     * Render an XML sitemap of all public, indexable URLs with hreflang alternates.
     */
    public function index(): Response
    {
        /** @var array<int, array{path: string, lastmod: ?CarbonInterface}> $entries */
        $entries = [];

        $entries[] = ['path' => '/', 'lastmod' => null];

        foreach ($this->publicPages() as $page) {
            $entries[] = [
                'path' => '/'.ltrim($page->slug, '/'),
                'lastmod' => $page->updated_at,
            ];
        }

        foreach ($this->publicCoaches() as $coach) {
            $entries[] = ['path' => '/treneri/'.$coach->slug, 'lastmod' => $coach->updated_at];
        }

        foreach ($this->publicAthletes() as $athlete) {
            $entries[] = ['path' => '/atleti/'.$athlete->slug, 'lastmod' => $athlete->updated_at];
        }

        foreach (Judge::query()->get(['id', 'slug', 'updated_at']) as $judge) {
            $entries[] = ['path' => '/rozhodcovia/'.$judge->slug, 'lastmod' => $judge->updated_at];
        }

        foreach (Team::query()->where('is_active', true)->get(['id', 'slug', 'updated_at']) as $team) {
            $entries[] = ['path' => '/timy/'.$team->slug, 'lastmod' => $team->updated_at];
        }

        foreach (Training::query()->where('is_active', true)->with('team:id,slug')->get(['id', 'slug', 'team_id', 'updated_at']) as $training) {
            if ($training->team) {
                $entries[] = ['path' => '/timy/'.$training->team->slug.'/treningy/'.$training->slug, 'lastmod' => $training->updated_at];
            }
        }

        foreach (Event::query()->where('is_published', true)->get(['id', 'slug', 'updated_at']) as $event) {
            $entries[] = ['path' => '/eventy/'.$event->slug, 'lastmod' => $event->updated_at];
        }

        return response($this->render($entries), 200)
            ->header('Content-Type', 'application/xml');
    }

    /**
     * @return Collection<int, Page>
     */
    private function publicPages()
    {
        return Page::query()
            ->published()
            ->where('slug', '!=', '/')
            ->get(['id', 'slug', 'updated_at']);
    }

    /**
     * @return Collection<int, User>
     */
    private function publicCoaches()
    {
        return User::query()
            ->whereNotNull('coach_profile_approved_at')
            ->whereHas('teams', fn ($q) => $q->where('team_user.role', RoleEnum::COACH->value))
            ->get(['id', 'slug', 'updated_at']);
    }

    /**
     * @return Collection<int, User>
     */
    private function publicAthletes()
    {
        return User::query()
            ->whereNotNull('athlete_profile_approved_at')
            ->whereHas('teams', fn ($q) => $q->where('team_user.role', RoleEnum::ATHLETE->value))
            ->get(['id', 'slug', 'updated_at']);
    }

    /**
     * @param  array<int, array{path: string, lastmod: ?CarbonInterface}>  $entries
     */
    private function render(array $entries): string
    {
        $locales = ['sk', ...array_values(SetLocale::SUPPORTED_PREFIXES)];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">'."\n";

        foreach ($entries as $entry) {
            $xml .= '  <url>'."\n";
            $xml .= '    <loc>'.e(url($this->localizedPath($entry['path'], 'sk'))).'</loc>'."\n";

            foreach ($locales as $locale) {
                $xml .= '    <xhtml:link rel="alternate" hreflang="'.$locale.'" href="'.e(url($this->localizedPath($entry['path'], $locale))).'"/>'."\n";
            }

            $xml .= '    <xhtml:link rel="alternate" hreflang="x-default" href="'.e(url($this->localizedPath($entry['path'], 'sk'))).'"/>'."\n";

            if ($entry['lastmod'] instanceof CarbonInterface) {
                $xml .= '    <lastmod>'.$entry['lastmod']->toAtomString().'</lastmod>'."\n";
            }

            $xml .= '  </url>'."\n";
        }

        $xml .= '</urlset>'."\n";

        return $xml;
    }

    /**
     * Prefix a path with the locale segment (Slovak is the unprefixed default).
     */
    private function localizedPath(string $path, string $locale): string
    {
        $prefix = SetLocale::PREFIX_MAP[$locale] ?? null;

        if (! $prefix) {
            return $path;
        }

        return '/'.$prefix.($path === '/' ? '' : $path);
    }
}
