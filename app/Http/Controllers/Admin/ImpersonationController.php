<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ImpersonationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ImpersonationController extends Controller
{
    public function start(Request $request, User $user): RedirectResponse
    {
        /** @var User $currentUser */
        $currentUser = $request->user();

        if (! $currentUser->hasRole([RoleEnum::SUPER_ADMIN, RoleEnum::ADMIN])) {
            abort(403);
        }

        if ($currentUser->id === $user->id) {
            return back();
        }

        ImpersonationService::start($user);

        return redirect()->to(filament()->getUrl());
    }

    public function stop(): RedirectResponse
    {
        ImpersonationService::stop();

        return redirect()->to(filament()->getUrl());
    }
}
