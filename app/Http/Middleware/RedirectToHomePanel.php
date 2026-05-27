<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keeps users on the panel that matches their tenant situation:
 *  - teamless users belong on the tenant-free customer panel,
 *  - team members and global admins on the tenant admin panel.
 *
 * Registered first in each panel's authMiddleware (before Filament's
 * Authenticate), so a teamless user hitting the admin panel is redirected to
 * the customer panel rather than 403'd for lacking a tenant, and a team member
 * landing on the customer panel is bounced to their admin panel.
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
            return redirect('/'.$user->homePanelId());
        }

        return $next($request);
    }
}
