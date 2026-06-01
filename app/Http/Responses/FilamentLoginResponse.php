<?php

namespace App\Http\Responses;

use App\Models\User;
use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

/**
 * Everyone logs in through the customer panel (the only one every user can
 * access). After authenticating, route the user to the panel that is actually
 * their home: team members and global admins land on /admin, teamless
 * customers stay on /customer. Mirrors MagicLoginController's redirect.
 */
class FilamentLoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        $user = Filament::auth()->user();

        $panelId = $user instanceof User
            ? $user->homePanelId()
            : Filament::getCurrentPanel()?->getId() ?? 'customer';

        return redirect()->intended("/{$panelId}");
    }
}
