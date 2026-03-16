<?php

namespace App\Livewire;

use App\Enums\InvitationStatusEnum;
use App\Enums\PlanTierEnum;
use App\Enums\RoleEnum;
use App\Models\SubscriptionPlan;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\TeamSubscription;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\Permission\Models\Role;

class RegisterTeam extends Component
{
    use WithFileUploads;

    public int $step = 1;

    // Step 1: Account
    public string $firstName = '';

    public string $lastName = '';

    public string $email = '';

    public string $password = '';

    public string $passwordConfirmation = '';

    // Step 2: Team details
    public string $teamName = '';

    public string $ownerName = '';

    public string $ownerEmail = '';

    public string $sportType = '';

    public string $country = 'SK';

    public string $city = '';

    public $logo = null;

    public string $description = '';

    // Step 3: Plan
    public string $billingPeriod = 'monthly';

    public ?string $selectedPlanId = null;

    // Step 4: Success data
    public ?string $createdTeamName = null;

    public ?string $createdPlanName = null;

    public ?string $createdSportType = null;

    public ?string $createdOwnerName = null;

    public ?string $createdTeamSlug = null;

    /** @return array<string, string[]> */
    protected function step1Rules(): array
    {
        return [
            'firstName' => ['required', 'string', 'max:255'],
            'lastName' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'passwordConfirmation' => ['required', 'same:password'],
        ];
    }

    /** @return array<string, string> */
    protected function step1Messages(): array
    {
        return [
            'firstName.required' => 'Meno je povinné.',
            'lastName.required' => 'Priezvisko je povinné.',
            'email.required' => 'E-mail je povinný.',
            'email.email' => 'Zadajte platnú e-mailovú adresu.',
            'email.unique' => 'Tento e-mail je už zaregistrovaný.',
            'password.required' => 'Heslo je povinné.',
            'password.min' => 'Heslo musí mať aspoň 8 znakov.',
            'passwordConfirmation.required' => 'Potvrdenie hesla je povinné.',
            'passwordConfirmation.same' => 'Heslá sa nezhodujú.',
        ];
    }

    /** @return array<string, string[]> */
    protected function step2Rules(): array
    {
        return [
            'teamName' => ['required', 'string', 'max:255'],
            'ownerName' => ['required', 'string', 'max:255'],
            'ownerEmail' => ['required', 'email', 'max:255'],
            'sportType' => ['nullable', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:2'],
            'city' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /** @return array<string, string> */
    protected function step2Messages(): array
    {
        return [
            'teamName.required' => 'Názov tímu je povinný.',
            'ownerName.required' => 'Meno vlastníka je povinné.',
            'ownerEmail.required' => 'E-mail vlastníka je povinný.',
            'ownerEmail.email' => 'Zadajte platnú e-mailovú adresu.',
            'country.required' => 'Krajina je povinná.',
            'logo.image' => 'Logo musí byť obrázok.',
            'logo.max' => 'Logo môže mať maximálne 2 MB.',
        ];
    }

    public function nextStep(): void
    {
        if ($this->step === 1) {
            $this->validate($this->step1Rules(), $this->step1Messages());
            $this->ownerName = $this->firstName.' '.$this->lastName;
            $this->ownerEmail = $this->email;
        }

        if ($this->step === 2) {
            $this->validate($this->step2Rules(), $this->step2Messages());
        }

        if ($this->step === 3) {
            $this->createTeamAndUser();

            return;
        }

        $this->step++;
    }

    public function previousStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function selectPlan(string $planId): void
    {
        $this->selectedPlanId = $planId;
    }

    public function toggleBilling(): void
    {
        $this->billingPeriod = $this->billingPeriod === 'monthly' ? 'yearly' : 'monthly';
    }

    protected function createTeamAndUser(): void
    {
        DB::transaction(function () {
            $user = User::create([
                'first_name' => $this->firstName,
                'last_name' => $this->lastName,
                'name' => $this->firstName.' '.$this->lastName,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'locale' => app()->getLocale(),
                'country_code' => $this->country,
            ]);

            $user->forceFill(['email_verified_at' => now()])->save();

            $role = Role::firstOrCreate(['name' => RoleEnum::ADMIN->value, 'guard_name' => 'web']);
            $user->assignRole($role);

            $logoPath = null;
            if ($this->logo) {
                $logoPath = $this->logo->store('team-logos', 'public');
            }

            $team = Team::create([
                'name' => ['sk' => $this->teamName],
                'story' => ['sk' => $this->description ?: null],
                'is_active' => true,
                'default_locale' => app()->getLocale(),
            ]);

            if ($logoPath) {
                $team->update(['logo' => $logoPath]);
            }

            $user->teams()->attach($team->id, [
                'role' => RoleEnum::TEAM_ADMIN->value,
                'is_active' => true,
                'joined_at' => now(),
            ]);

            $plan = null;
            if ($this->selectedPlanId) {
                $plan = SubscriptionPlan::find($this->selectedPlanId);
            }

            if (! $plan) {
                $plan = SubscriptionPlan::where('tier', PlanTierEnum::PRO)->first();
            }

            if ($plan) {
                TeamSubscription::create([
                    'team_id' => $team->id,
                    'subscription_plan_id' => $plan->id,
                    'status' => 'active',
                    'billing_period' => $this->billingPeriod,
                    'amount' => 0,
                    'currency' => 'EUR',
                    'starts_at' => now(),
                    'trial_ends_at' => now()->addMonths(2),
                ]);
            }

            Auth::login($user);

            $this->redeemPendingInviteCode($user);

            $this->createdTeamName = $this->teamName;
            $this->createdPlanName = $plan?->getTranslation('name', 'sk') ?? 'PRO Trial';
            $this->createdSportType = $this->sportType ?: '-';
            $this->createdOwnerName = $this->firstName.' '.$this->lastName;
            $this->createdTeamSlug = $team->slug;
        });

        $this->step = 4;
    }

    protected function redeemPendingInviteCode(User $user): void
    {
        $code = session()->pull('pending_invite_code');

        if (! $code) {
            return;
        }

        $invitation = TeamInvitation::where('code', $code)
            ->whereHas('team', fn ($q) => $q->where('is_active', true))
            ->first();

        if (! $invitation || ! $invitation->isPending() || $invitation->isExpired()) {
            return;
        }

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
    }

    /** @return Collection<int, SubscriptionPlan> */
    public function getPlansProperty()
    {
        return SubscriptionPlan::query()
            ->where('is_active', true)
            ->with('prices')
            ->orderBy('sort_order')
            ->get();
    }

    public function render(): View
    {
        return view('livewire.register-team');
    }
}
