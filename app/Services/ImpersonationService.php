<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ImpersonationService
{
    private const SESSION_KEY = 'impersonator_id';

    public static function start(User $target): void
    {
        Session::put(self::SESSION_KEY, Auth::id());
        Auth::login($target);
    }

    public static function stop(): void
    {
        $impersonatorId = Session::pull(self::SESSION_KEY);

        if ($impersonatorId) {
            $impersonator = User::find($impersonatorId);

            if ($impersonator) {
                Auth::login($impersonator);
            }
        }
    }

    public static function isImpersonating(): bool
    {
        return Session::has(self::SESSION_KEY);
    }

    public static function getImpersonatorId(): ?string
    {
        return Session::get(self::SESSION_KEY);
    }
}
