<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keeps users on the panel that matches their tenant situation:
 *  - teamless users belong on the tenant-free customer panel,
 *  - team members and global admins on the tenant admin panel.
 *
 * Registered in each panel's middleware stack, so a teamless user hitting the
 * tenant-scoped admin panel is redirected to the customer panel rather than
 * 403'd for lacking (or not belonging to) a tenant, and a team member landing
 * on the customer panel is bounced to their admin panel.
 *
 * The redirect maps the request to the SAME page on the user's home panel
 * (swapping the `filament.{panel}.` route-name prefix and dropping/adding the
 * `{tenant}` segment), so deep links such as a profile-edit URL aimed at the
 * wrong panel land on the correct resource instead of just the dashboard. This
 * is the single place that fixes mis-targeted panel links, so call sites do not
 * each need panel-aware URL generation.
 */
class RedirectToHomePanel
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        // Fall back to the first path segment (this middleware only runs on the
        // 'admin' and 'customer' panels) if the current panel isn't resolved yet.
        $currentPanelId = Filament::getCurrentPanel()?->getId() ?? $request->segment(1);

        if (
            $user !== null
            && in_array($currentPanelId, ['admin', 'customer'], true)
            && $user->homePanelId() !== $currentPanelId
        ) {
            return redirect($this->equivalentUrlOnHomePanel($request, $user, $currentPanelId));
        }

        return $next($request);
    }

    /**
     * Resolve the same Filament page on the user's home panel, falling back to
     * the panel root when the current route has no counterpart there.
     */
    private function equivalentUrlOnHomePanel(Request $request, Authenticatable $user, string $currentPanelId): string
    {
        $targetPanelId = $user->homePanelId();
        $fallback = '/'.$targetPanelId;

        $routeName = $request->route()?->getName();

        if ($routeName === null || ! str_starts_with($routeName, "filament.{$currentPanelId}.")) {
            return $fallback;
        }

        $targetRouteName = Str::replaceFirst("filament.{$currentPanelId}.", "filament.{$targetPanelId}.", $routeName);

        if (! Route::has($targetRouteName)) {
            return $fallback;
        }

        $parameters = $request->route()->parameters();
        unset($parameters['tenant']);

        // The admin panel is tenant-scoped, so its routes need a {tenant}. Use a
        // tenant the user may actually access; bail to the root if there is none.
        if ($targetPanelId === 'admin') {
            $tenant = $user->getTenants(Filament::getPanel('admin'))->first();

            if ($tenant === null) {
                return $fallback;
            }

            $parameters['tenant'] = $tenant;
        }

        try {
            return route($targetRouteName, $parameters);
        } catch (UrlGenerationException) {
            return $fallback;
        }
    }
}
