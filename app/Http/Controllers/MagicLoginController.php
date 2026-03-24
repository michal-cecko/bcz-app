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
        Auth::login($user, remember: true);

        $request->session()->regenerate();

        // If user has no real password (created via guest registration), prompt them to set one
        if ($user->password_set_at === null) {
            return redirect()->route('filament.admin.auth.setup-wizard');
        }

        return redirect('/admin');
    }
}
