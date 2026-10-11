<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MagicLoginController extends Controller
{
    public function __invoke(Request $request, User $user): RedirectResponse
    {
        if (Auth::check() && Auth::id() !== $user->id) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        Auth::login($user, remember: true);

        $request->session()->regenerate();

        $returnPath = $request->query('return');
        if (config('family.enabled') && is_string($returnPath)
            && str_starts_with($returnPath, '/') && ! str_starts_with($returnPath, '//')
            && ! str_contains($returnPath, '\\') && ! preg_match('/[\x00-\x20]/', $returnPath)) {
            if (! $user->email_verified_at) {
                $user->forceFill(['email_verified_at' => now()])->save();
            }

            return redirect($returnPath);
        }

        // Teamless users (e.g. guest registrants) land in the tenant-free
        // customer panel; team members and admins in the tenant admin panel.
        $panelId = $user->homePanelId();

        // If user has no real password (created via guest registration), prompt them to set one
        if ($user->password_set_at === null) {
            return redirect()->route("filament.{$panelId}.auth.setup-wizard");
        }

        return redirect("/{$panelId}");
    }
}
