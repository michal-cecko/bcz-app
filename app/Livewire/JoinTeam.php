<?php

namespace App\Livewire;

use App\Enums\JoinRequestStatusEnum;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\TeamJoinRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class JoinTeam extends Component
{
    public string $search = '';

    public string $inviteCode = '';

    public ?string $selectedTeamId = null;

    #[Validate('required|string|max:255')]
    public string $requestName = '';

    #[Validate('required|email|max:255')]
    public string $requestEmail = '';

    public bool $showRequestForm = false;

    public bool $requestSent = false;

    public ?string $requestError = null;

    public ?string $codeError = null;

    public bool $codeSuccess = false;

    public function updatedSearch(): void
    {
        $this->resetRequestState();
    }

    public function getTeamResultsProperty(): Collection
    {
        if (strlen($this->search) < 2) {
            return collect();
        }

        return Team::query()
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereRaw("name->>'sk' ILIKE ?", ['%'.$this->search.'%'])
                    ->orWhereRaw("name->>'en' ILIKE ?", ['%'.$this->search.'%'])
                    ->orWhereRaw("name->>'cs' ILIKE ?", ['%'.$this->search.'%']);
            })
            ->withCount('members')
            ->limit(5)
            ->get();
    }

    public function selectTeam(string $teamId): void
    {
        $this->selectedTeamId = $teamId;
        $this->showRequestForm = ! Auth::check();
        $this->requestError = null;

        if (Auth::check()) {
            $this->submitJoinRequest($teamId);
        }
    }

    public function submitGuestRequest(): void
    {
        $this->validate([
            'requestName' => 'required|string|max:255',
            'requestEmail' => 'required|email|max:255',
        ]);

        $this->submitJoinRequest($this->selectedTeamId);
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
            ->whereHas('team', fn ($q) => $q->where('is_active', true))
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
                'status' => \App\Enums\InvitationStatusEnum::Accepted,
                'accepted_at' => now(),
            ]);

            $this->codeSuccess = true;
        } else {
            session(['pending_invite_code' => $this->inviteCode]);
            $this->redirect(route('register'));
        }
    }

    protected function submitJoinRequest(string $teamId): void
    {
        $team = Team::findOrFail($teamId);

        $name = Auth::check() ? Auth::user()->name : $this->requestName;
        $email = Auth::check() ? Auth::user()->email : $this->requestEmail;

        $existing = TeamJoinRequest::where('team_id', $teamId)
            ->where('email', $email)
            ->where('status', JoinRequestStatusEnum::Pending)
            ->first();

        if ($existing) {
            $this->requestError = __('Žiadosť o pripojenie už bola odoslaná.');
            $this->showRequestForm = false;

            return;
        }

        if (Auth::check() && Auth::user()->teams()->where('teams.id', $teamId)->exists()) {
            $this->requestError = __('Už ste členom tohto tímu.');
            $this->showRequestForm = false;

            return;
        }

        TeamJoinRequest::create([
            'team_id' => $teamId,
            'user_id' => Auth::id(),
            'name' => $name,
            'email' => $email,
            'status' => JoinRequestStatusEnum::Pending,
        ]);

        $this->requestSent = true;
        $this->showRequestForm = false;
        $this->selectedTeamId = null;
    }

    protected function resetRequestState(): void
    {
        $this->showRequestForm = false;
        $this->requestSent = false;
        $this->requestError = null;
        $this->selectedTeamId = null;
    }

    public function render(): View
    {
        return view('livewire.join-team');
    }
}
