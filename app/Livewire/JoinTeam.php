<?php

namespace App\Livewire;

use App\Enums\InvitationStatusEnum;
use App\Enums\JoinRequestStatusEnum;
use App\Enums\RoleEnum;
use App\Enums\TeamJoinModeEnum;
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

    #[Validate('accepted')]
    public bool $gdprAgreed = false;

    public bool $showRequestForm = false;

    public bool $requestSent = false;

    public bool $joinedDirectly = false;

    public ?string $requestError = null;

    public ?string $codeError = null;

    public bool $codeSuccess = false;

    public function mount(): void
    {
        if (Auth::check()) {
            $this->requestName = Auth::user()->name;
            $this->requestEmail = Auth::user()->email;
        }
    }

    public function updatedSearch(): void
    {
        $this->resetRequestState();
    }

    public function getTeamResultsProperty(): Collection
    {
        if (strlen($this->search) < 2) {
            return collect();
        }

        $needle = '%'.mb_strtolower($this->search).'%';

        return Team::query()
            ->where('is_active', true)
            ->where(function ($query) use ($needle) {
                $query->whereRaw("LOWER(name->>'sk') LIKE ?", [$needle])
                    ->orWhereRaw("LOWER(name->>'en') LIKE ?", [$needle])
                    ->orWhereRaw("LOWER(name->>'cs') LIKE ?", [$needle]);
            })
            ->withCount('members')
            ->limit(5)
            ->get();
    }

    public function selectTeam(string $teamId): void
    {
        $this->selectedTeamId = $teamId;
        $this->requestError = null;
        $this->joinedDirectly = false;

        $team = Team::findOrFail($teamId);

        if (Auth::check()) {
            if ($team->join_mode === TeamJoinModeEnum::OPEN) {
                $this->joinTeamDirectly($team);
            } else {
                $this->submitJoinRequest($teamId);
            }
        } else {
            $this->showRequestForm = true;
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
                'status' => InvitationStatusEnum::Accepted,
                'accepted_at' => now(),
            ]);

            $this->codeSuccess = true;
        } else {
            session(['pending_invite_code' => $this->inviteCode]);
            $this->redirect(route('register'));
        }
    }

    protected function joinTeamDirectly(Team $team): void
    {
        $user = Auth::user();

        if ($user->teams()->where('teams.id', $team->id)->exists()) {
            $this->requestError = __('Už ste členom tohto tímu.');
            $this->showRequestForm = false;

            return;
        }

        $user->teams()->attach($team->id, [
            'role' => RoleEnum::ATHLETE->value,
            'is_active' => true,
            'joined_at' => now(),
        ]);

        $this->joinedDirectly = true;
        $this->showRequestForm = false;
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
        $this->joinedDirectly = false;
        $this->requestError = null;
        $this->selectedTeamId = null;
    }

    public function render(): View
    {
        return view('livewire.join-team');
    }
}
