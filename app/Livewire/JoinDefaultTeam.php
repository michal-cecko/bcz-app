<?php

namespace App\Livewire;

use App\Enums\InvitationStatusEnum;
use App\Enums\JoinRequestStatusEnum;
use App\Enums\RoleEnum;
use App\Models\Setting;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\TeamJoinRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class JoinDefaultTeam extends Component
{
    public ?Team $team = null;

    public string $inviteCode = '';

    #[Validate('required|string|max:255')]
    public string $requestName = '';

    #[Validate('required|string|max:255')]
    public string $requestSurname = '';

    #[Validate('required|email|max:255')]
    public string $requestEmail = '';

    public bool $showRequestForm = false;

    public bool $requestSent = false;

    public bool $joinedDirectly = false;

    public ?string $requestError = null;

    public ?string $codeError = null;

    public bool $codeSuccess = false;

    public function mount(): void
    {
        $defaultTeamId = Setting::get('default_team_id');

        if ($defaultTeamId) {
            $this->team = Team::query()
                ->where('is_active', true)
                ->withCount('members')
                ->find($defaultTeamId);
        }

        if (Auth::check()) {
            $this->requestName = Auth::user()->name;
            $this->requestEmail = Auth::user()->email;
        }
    }

    public function joinDirectly(): void
    {
        if (! Auth::check()) {
            session(['pending_join_team_id' => $this->team->id]);
            $this->redirect(route('login'));

            return;
        }

        $user = Auth::user();

        if ($user->teams()->where('teams.id', $this->team->id)->exists()) {
            $this->requestError = __('Už ste členom tohto tímu.');

            return;
        }

        $user->teams()->attach($this->team->id, [
            'role' => RoleEnum::ATHLETE->value,
            'is_active' => true,
            'joined_at' => now(),
            'continuous_membership' => true,
        ]);

        $this->joinedDirectly = true;
    }

    public function submitJoinRequest(): void
    {
        if (! Auth::check()) {
            $this->validate([
                'requestName' => 'required|string|max:255',
                'requestSurname' => 'required|string|max:255',
                'requestEmail' => 'required|email|max:255',
            ]);
        }

        $name = Auth::check() ? Auth::user()->name : trim($this->requestName.' '.$this->requestSurname);
        $email = Auth::check() ? Auth::user()->email : $this->requestEmail;

        $existing = TeamJoinRequest::where('team_id', $this->team->id)
            ->where('email', $email)
            ->where('status', JoinRequestStatusEnum::Pending)
            ->first();

        if ($existing) {
            $this->requestError = __('Žiadosť o pripojenie už bola odoslaná.');

            return;
        }

        if (Auth::check() && Auth::user()->teams()->where('teams.id', $this->team->id)->exists()) {
            $this->requestError = __('Už ste členom tohto tímu.');

            return;
        }

        TeamJoinRequest::create([
            'team_id' => $this->team->id,
            'user_id' => Auth::id(),
            'name' => $name,
            'email' => $email,
            'status' => JoinRequestStatusEnum::Pending,
        ]);

        $this->requestSent = true;
        $this->showRequestForm = false;
    }

    public function submitGuestJoinRequest(): void
    {
        $this->validate([
            'requestName' => 'required|string|max:255',
            'requestSurname' => 'required|string|max:255',
            'requestEmail' => 'required|email|max:255',
        ]);

        $existing = TeamJoinRequest::where('team_id', $this->team->id)
            ->where('email', $this->requestEmail)
            ->where('status', JoinRequestStatusEnum::Pending)
            ->first();

        if ($existing) {
            $this->requestError = __('Žiadosť o pripojenie už bola odoslaná.');

            return;
        }

        TeamJoinRequest::create([
            'team_id' => $this->team->id,
            'name' => trim($this->requestName.' '.$this->requestSurname),
            'email' => $this->requestEmail,
            'status' => JoinRequestStatusEnum::Pending,
        ]);

        $this->requestSent = true;
    }

    public function showGuestRequestForm(): void
    {
        $this->showRequestForm = true;
    }

    public function redeemCode(): void
    {
        $this->codeError = null;
        $this->codeSuccess = false;

        if (empty($this->inviteCode)) {
            $this->codeError = __('Zadajte pozývací kód.');

            return;
        }

        $invitation = TeamInvitation::where('code', $this->inviteCode)
            ->where('team_id', $this->team->id)
            ->first();

        if (! $invitation) {
            $this->codeError = __('Neplatný pozývací kód.');

            return;
        }

        if (! $invitation->isPending() || $invitation->isExpired()) {
            $this->codeError = __('Tento pozývací kód už nie je platný.');

            return;
        }

        if (Auth::check()) {
            $user = Auth::user();

            if ($user->teams()->where('teams.id', $invitation->team_id)->exists()) {
                $this->codeError = __('Už ste členom tohto tímu.');

                return;
            }

            $user->teams()->syncWithoutDetaching([
                $invitation->team_id => [
                    'is_active' => true,
                    'joined_at' => now(),
                ],
            ]);

            $invitation->update([
                'status' => InvitationStatusEnum::Accepted,
                'accepted_at' => now(),
            ]);

            $this->codeSuccess = true;
        } else {
            session(['pending_invite_code' => $this->inviteCode]);
            $this->redirect(route('login'));
        }
    }

    public function render(): View
    {
        return view('livewire.join-default-team');
    }
}
