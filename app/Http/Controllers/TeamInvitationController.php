<?php

namespace App\Http\Controllers;

use App\Enums\InvitationStatusEnum;
use App\Enums\RoleEnum;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class TeamInvitationController extends Controller
{
    public function accept(TeamInvitation $invitation): RedirectResponse
    {
        if (! $invitation->isPending() || $invitation->isExpired()) {
            abort(403, 'Táto pozvánka už nie je platná.');
        }

        $user = User::where('email', $invitation->email)->firstOrFail();

        // Attach user to team with ATHLETE role (ignore if already exists)
        $alreadyHasRole = $user->teams()
            ->where('teams.id', $invitation->team_id)
            ->wherePivot('role', RoleEnum::ATHLETE->value)
            ->exists();

        if (! $alreadyHasRole) {
            $user->teams()->attach($invitation->team_id, [
                'role' => RoleEnum::ATHLETE->value,
                'is_active' => true,
                'joined_at' => now(),
            ]);
        }

        $invitation->update([
            'status' => InvitationStatusEnum::Accepted,
            'accepted_at' => now(),
        ]);

        Auth::login($user);

        return redirect()->to('/admin');
    }

    public function showRegisterForm(TeamInvitation $invitation): View|RedirectResponse
    {
        if (! $invitation->isPending() || $invitation->isExpired()) {
            abort(403, 'Táto pozvánka už nie je platná.');
        }

        return view('auth.register-via-invitation', [
            'invitation' => $invitation,
        ]);
    }

    public function register(Request $request, TeamInvitation $invitation): RedirectResponse
    {
        if (! $invitation->isPending() || $invitation->isExpired()) {
            abort(403, 'Táto pozvánka už nie je platná.');
        }

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $invitation->email,
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);

        $user->assignRole(RoleEnum::CUSTOMER);

        $user->teams()->attach($invitation->team_id, [
            'role' => RoleEnum::ATHLETE->value,
            'is_active' => true,
            'joined_at' => now(),
        ]);

        $invitation->update([
            'status' => InvitationStatusEnum::Accepted,
            'accepted_at' => now(),
        ]);

        Auth::login($user);

        return redirect()->to('/admin');
    }
}
