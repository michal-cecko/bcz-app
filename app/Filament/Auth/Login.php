<?php

namespace App\Filament\Auth;

use App\Models\User;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Illuminate\Validation\ValidationException;

/**
 * Filament reports a failed canAccessPanel() check with the same
 * "credentials do not match" error as a wrong password. So a teamless customer
 * who lands on /admin/login (an old link, a bookmark) sees that error even
 * though their password is correct — they simply can't use the admin panel.
 *
 * This login page catches that one case: when the credentials are valid but the
 * user cannot access the panel they are logging into, log them in and forward
 * them to a panel they can actually use (their home panel) instead of erroring.
 */
class Login extends BaseLogin
{
    public function authenticate(): ?LoginResponse
    {
        try {
            return parent::authenticate();
        } catch (ValidationException $exception) {
            return $this->attemptHomePanelFallback() ?? throw $exception;
        }
    }

    protected function attemptHomePanelFallback(): ?LoginResponse
    {
        $guard = Filament::auth();
        $provider = $guard->getProvider();

        $data = $this->form->getState();
        $credentials = $this->getCredentialsFromFormData($data);

        $user = $provider->retrieveByCredentials($credentials);

        // Bail out on anything that isn't specifically "valid password, wrong
        // panel" — a genuinely wrong password must still surface the error.
        if (! $user instanceof User || ! $provider->validateCredentials($user, $credentials)) {
            return null;
        }

        if ($user->canAccessPanel(Filament::getCurrentOrDefaultPanel())) {
            return null;
        }

        $homePanel = Filament::getPanel($user->homePanelId(), isStrict: false);

        if ($homePanel === null || ! $user->canAccessPanel($homePanel)) {
            return null;
        }

        $guard->login($user, (bool) ($data['remember'] ?? false));
        session()->regenerate();
        // Drop any intended URL captured for the unreachable panel so the
        // LoginResponse redirects cleanly to the user's home panel.
        session()->forget('url.intended');

        return app(LoginResponse::class);
    }
}
