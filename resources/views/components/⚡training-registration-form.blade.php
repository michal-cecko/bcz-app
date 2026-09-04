<?php

use App\Enums\PaymentMethodEnum;
use App\Enums\RegistrationFieldTypeEnum;
use App\Enums\RegistrationStatusEnum;
use App\Enums\RoleEnum;
use App\Enums\TrainingPricingTypeEnum;
use App\Models\Training;
use App\Models\TrainingRegistration;
use App\Models\User;
use App\Notifications\WelcomeToApp;
use App\Services\PaymentService;
use App\Services\RegistrationService;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public Training $training;

    public array $fields = [];

    public bool $gdprAgreed = false;

    public string $registrationState = 'form';

    public ?string $selectedPaymentMethod = null;

    public ?string $pendingPaymentId = null;

    public function mount(Training $training): void
    {
        $this->training = $training;

        // Check if returning from GoPay payment success
        if (request()->query('payment') === 'success') {
            $this->registrationState = 'payment_success';

            return;
        }

        // Check registration window
        if (! $this->training->isRegistrationOpen()) {
            $this->registrationState = 'registration_closed';

            return;
        }

        $user = auth()->user();

        // Check if already registered (logged-in users)
        if ($user) {
            $registration = TrainingRegistration::where('training_id', $this->training->id)
                ->where('user_id', $user->id)
                ->whereNotIn('status', [RegistrationStatusEnum::Cancelled->value])
                ->first();

            if ($registration) {
                if ($registration->status === RegistrationStatusEnum::Pending) {
                    // Show payment info for pending registrations
                    $this->registrationState = RegistrationService::determinePostRegistrationState($this->training, $user);
                    $this->autoSelectPaymentMethod();
                    $this->pendingPaymentId = $registration->payments()
                        ->where('status', \App\Enums\PaymentStatusEnum::PENDING)
                        ->latest('created_at')->value('id');
                } elseif ($registration->status === RegistrationStatusEnum::Approved) {
                    // Re-check: membership-required training where membership expired/unpaid
                    $needsMembership = $this->training->pricing_type === \App\Enums\TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED
                        && ! $user->hasActiveMembershipForTeam($this->training->team_id);

                    // Re-check: paid training with no completed payment
                    $needsPayment = $this->training->pricing_type === \App\Enums\TrainingPricingTypeEnum::PAID
                        && $this->training->price_amount > 0
                        && $registration->payments()->where('status', \App\Enums\PaymentStatusEnum::COMPLETED)->doesntExist();

                    $this->registrationState = ($needsMembership || $needsPayment)
                        ? RegistrationService::determinePostRegistrationState($this->training, $user)
                        : 'already_registered';

                    if ($needsMembership || $needsPayment) {
                        $this->autoSelectPaymentMethod();
                        $this->pendingPaymentId = $registration->payments()
                            ->where('status', \App\Enums\PaymentStatusEnum::PENDING)
                            ->latest('created_at')->value('id');
                    }
                } else {
                    $this->registrationState = 'already_registered';
                }

                return;
            }
        }

        // Check if training is full (only blocks new registrations, not existing states)
        if ($this->training->isFull()) {
            $this->registrationState = 'full';

            return;
        }

        // Initialize form fields
        foreach ($this->training->registration_form_schema ?? [] as $field) {
            $type = $field['type'] ?? 'text_input';
            $prefillValue = $user ? match ($type) {
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'full_name' => $user->name,
                'phone' => $user->phone,
                'birth_date' => $user->birth_date?->format('Y-m-d'),
                'gender' => $user->gender?->value,
                default => null,
            } : null;

            $this->fields[$field['name']] = $prefillValue ?? '';
        }
    }

    public function submit(): void
    {
        $rules = [];
        $attributes = [];
        $locale = app()->getLocale();

        foreach ($this->training->registration_form_schema ?? [] as $field) {
            $key = 'fields.' . $field['name'];
            $fieldRules = [];

            $type = RegistrationFieldTypeEnum::tryFrom($field['type'] ?? '');

            if ($type === RegistrationFieldTypeEnum::CHECKBOX) {
                $fieldRules[] = ($field['required'] ?? false) ? 'accepted' : 'nullable';
            } else {
                $fieldRules[] = ($field['required'] ?? false) ? 'required' : 'nullable';

                match ($type) {
                    RegistrationFieldTypeEnum::EMAIL => $fieldRules[] = 'email',
                    RegistrationFieldTypeEnum::NUMBER_INPUT => $fieldRules[] = 'numeric',
                    default => null,
                };
            }

            $rules[$key] = $fieldRules;

            $label = is_array($field['label'] ?? null)
                ? ($field['label'][$locale] ?? $field['label']['sk'] ?? $field['name'])
                : ($field['label'] ?? $field['name']);
            $attributes[$key] = $label;
        }

        $rules['gdprAgreed'] = 'accepted';
        $attributes['gdprAgreed'] = __('consent.privacy_policy');

        $this->validate($rules, [], $attributes);

        $schema = $this->training->registration_form_schema ?? [];

        // Normalize checkbox values to a stable string for storage ('1' when checked, '' otherwise).
        foreach ($schema as $field) {
            if (($field['type'] ?? null) !== RegistrationFieldTypeEnum::CHECKBOX->value) {
                continue;
            }
            $checkboxKey = $field['name'] ?? $field['key'] ?? '';
            $this->fields[$checkboxKey] = filter_var($this->fields[$checkboxKey] ?? null, FILTER_VALIDATE_BOOLEAN) ? '1' : '';
        }

        foreach ($schema as $field) {
            if (($field['type'] ?? null) !== RegistrationFieldTypeEnum::FILE_INPUT->value) {
                continue;
            }
            $value = $this->fields[$field['name']] ?? null;
            if ($value instanceof TemporaryUploadedFile) {
                $this->fields[$field['name']] = $value->store('registrations', 'public');
            }
        }

        $authUser = auth()->user();

        if ($authUser) {
            // One registration per email per training — guard the submit even for
            // logged-in users (mount() already switches the UI to "already registered").
            if (TrainingRegistration::where('training_id', $this->training->id)
                ->where('user_id', $authUser->id)
                ->whereNotIn('status', [RegistrationStatusEnum::Cancelled->value])
                ->exists()) {
                $this->registrationState = 'already_registered';

                return;
            }

            $user = $authUser;
            $isNewUser = false;
        } else {
            $email = RegistrationService::extractEmailFromFormData($this->fields, $schema);
            $name = RegistrationService::extractNameFromFormData($this->fields, $schema);
            $phone = RegistrationService::extractPhoneFromFormData($this->fields, $schema);
            $birthDate = RegistrationService::extractBirthDateFromFormData($this->fields, $schema);
            $gender = RegistrationService::extractGenderFromFormData($this->fields, $schema);

            // Duplicate phone check for guests (block when phone is registered to a *different* email — fraud prevention).
            if ($phone && $email && User::where('phone', $phone)->where('email', '!=', $email)->exists()) {
                $this->addError('fields.' . $this->getPhoneFieldName($schema), __('training_detail.error_phone_exists'));

                return;
            }

            // One registration per email per training.
            if ($email && RegistrationService::emailAlreadyRegisteredForTraining($email, $this->training->id)) {
                $this->addError('fields.' . $this->getEmailFieldName($schema), __('training_detail.error_email_already_registered'));

                return;
            }

            if ($email) {
                $result = RegistrationService::resolveOrCreateUser($email, $name);
                $user = $result['user'];
                $isNewUser = $result['created'];

                // Set profile fields on new user if provided
                if ($isNewUser) {
                    $user->update(array_filter([
                        'phone' => $phone,
                        'birth_date' => $birthDate,
                        'gender' => $gender,
                    ], fn ($v) => $v !== null));
                }

                // New users get the global CUSTOMER role. Team enrollment is
                // handled below and only happens for membership-required
                // trainings.
                if ($isNewUser) {
                    $user->assignRole(RoleEnum::CUSTOMER);
                }
            } else {
                $user = null;
                $isNewUser = false;
            }
        }

        // Only membership-required trainings enroll the registrant into the
        // team (as a continuous member). Free and paid trainings never assign
        // a team.
        if ($user && $this->training->pricing_type === TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED) {
            $this->enrollAsContinuousMember($user);
        }

        $status = RegistrationService::determineRegistrationStatus($this->training, $user);

        $paymentDueAt = $status === RegistrationStatusEnum::Pending ? now()->addDays(7) : null;

        $registration = TrainingRegistration::create([
            'training_id' => $this->training->id,
            'user_id' => $user?->id,
            'form_data' => $this->fields,
            'status' => $status->value,
            'locale' => app()->getLocale(),
            'registered_at' => now(),
            'payment_due_at' => $paymentDueAt,
        ]);

        $payment = null;
        if ($user && $status === RegistrationStatusEnum::Pending && $this->training->price_amount) {
            $paymentService = app(PaymentService::class);
            $payment = $paymentService->createPendingPayment(
                user: $user,
                team: $this->training->team,
                payable: $registration,
                amount: (float) $this->training->price_amount,
                currency: 'EUR',
            );
            $this->pendingPaymentId = $payment->id;
        }

        // Membership-required trainings owe a club membership fee rather than a
        // per-training price. Issue it here so the fee — and its variable symbol —
        // exists in time for the welcome email below, instead of only once the
        // registrant comes back to the payment box on this page.
        if ($user && $this->training->pricing_type === TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED) {
            $this->ensureMembershipFeeIssued($user);
        }

        $confirmationRecipient = $user
            ?? RegistrationService::extractEmailFromFormData($this->fields, $schema);

        if ($confirmationRecipient) {
            RegistrationService::sendConfirmation(
                userOrEmail: $confirmationRecipient,
                registrationKind: 'training',
                registrationTitle: $this->training->getTranslation('title', $registration->locale),
                isNewUser: $isNewUser,
                team: $this->training->team,
                customEmailContent: $this->training->confirmation_email_content,
                locale: $registration->locale,
                attachments: $this->training->getMedia('email_attachments'),
                payment: $payment,
            );
        }

        // Registering for a membership-required training is the moment a person
        // joins the club, so welcome them to the platform. The welcome email
        // resolves the membership fee QR code for itself.
        if ($isNewUser && $user && $this->training->pricing_type === TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED) {
            $user->notify(new WelcomeToApp);
        }

        $this->registrationState = RegistrationService::determinePostRegistrationState($this->training, $user);
        $this->autoSelectPaymentMethod();
    }

    public function selectPaymentMethod(string $method): void
    {
        $this->selectedPaymentMethod = $method;
    }

    /**
     * Enroll the user into the training's team as a continuous member.
     * Called only for membership-required trainings; attaches the ATHLETE
     * pivot when missing, otherwise flips continuous_membership to true.
     */
    protected function enrollAsContinuousMember(User $user): void
    {
        $teamId = $this->training->team_id;

        $pivotExists = $user->teams()
            ->where('teams.id', $teamId)
            ->wherePivot('role', RoleEnum::ATHLETE->value)
            ->exists();

        if ($pivotExists) {
            $user->teams()->updateExistingPivot($teamId, [
                'continuous_membership' => true,
            ]);

            return;
        }

        $user->teams()->attach($teamId, [
            'role' => RoleEnum::ATHLETE->value,
            'is_active' => true,
            'joined_at' => now(),
            'continuous_membership' => true,
        ]);
    }

    /**
     * Issue the pending membership fee for the team's current season, unless the
     * user is already a paid-up member. Idempotent — the payment box on this page
     * resolves the very same Membership and Payment.
     */
    protected function ensureMembershipFeeIssued(User $user): void
    {
        if ($user->hasActiveMembershipForTeam($this->training->team_id)) {
            return;
        }

        $season = $this->training->team?->currentSeason;

        if (! $season) {
            return;
        }

        app(PaymentService::class)->ensurePendingMembershipPayment(
            user: $user,
            team: $this->training->team,
            season: $season,
        );
    }

    protected function autoSelectPaymentMethod(): void
    {
        if ($this->selectedPaymentMethod !== null) {
            return;
        }

        $enabledMethods = $this->training->effectivePaymentMethodKeys();
        $this->selectedPaymentMethod = $enabledMethods[0] ?? null;
    }

    public function handlePayment(): void
    {
        if (! $this->selectedPaymentMethod) {
            return;
        }

        if ($this->selectedPaymentMethod === PaymentMethodEnum::CASH->value) {
            $this->registrationState = 'cash_instructions';

            return;
        }

        if ($this->selectedPaymentMethod === PaymentMethodEnum::BANK_TRANSFER->value) {
            $this->registrationState = 'bank_transfer_details';

            return;
        }

        if ($this->selectedPaymentMethod === PaymentMethodEnum::GOPAY->value) {
            $user = auth()->user();
            if (! $user) {
                return;
            }

            $registration = TrainingRegistration::query()
                ->where('training_id', $this->training->id)
                ->where('user_id', $user->id)
                ->latest()
                ->first();

            if (! $registration || ! $this->training->price) {
                return;
            }

            try {
                $paymentService = app(PaymentService::class);
                $result = $paymentService->createGoPayPayment(
                    user: $user,
                    team: $this->training->team,
                    payable: $registration,
                    amount: (float) $this->training->price,
                    currency: $this->training->currency ?? 'EUR',
                );

                $this->redirect($result['url']);
            } catch (\Exception $e) {
                session()->flash('error', 'Platba sa nepodarila. Skúste to znova.');
            }
        }
    }

    protected function getPhoneFieldName(array $schema): string
    {
        foreach ($schema as $field) {
            if (($field['type'] ?? '') === 'phone') {
                return $field['name'];
            }
        }

        return 'phone';
    }

    protected function getEmailFieldName(array $schema): string
    {
        foreach ($schema as $field) {
            if (($field['type'] ?? '') === 'email') {
                return $field['name'] ?? $field['key'] ?? 'email';
            }
        }

        return 'email';
    }
};
?>

@php
    $locale = app()->getLocale();
    $schema = $training->registration_form_schema ?? [];
    $isLoggedIn = auth()->check();
    $prefillableTypes = ['email', 'first_name', 'last_name', 'full_name', 'phone', 'birth_date', 'gender'];
@endphp

<div>
    @if($registrationState === 'not_eligible')
        <div class="bg-[#111111] rounded-2xl border border-[#222222] p-10 flex flex-col items-center gap-4 text-center">
            <svg class="w-12 h-12 text-[#555555]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <h3 class="font-display font-bold text-2xl tracking-wide text-white">{{ __('training_detail.registration_not_eligible_title') }}</h3>
            <p class="text-[#888888] text-base max-w-md">{{ __('training_detail.registration_not_eligible_message') }}</p>
        </div>

    @elseif($registrationState === 'registration_closed')
        <div class="bg-[#111111] border border-[#333333] p-10 flex flex-col items-center gap-4 text-center">
            <svg class="w-12 h-12 text-[#555555]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            <h3 class="font-display font-bold text-2xl tracking-wide text-white">
                @if($training->registrationStatus() === 'not_yet_open')
                    {{ __('training_detail.registration_not_yet_open') }}
                @else
                    {{ __('training_detail.registration_closed') }}
                @endif
            </h3>
            @if($training->registrationStatus() === 'not_yet_open' && $training->registration_opens_at)
                <p class="text-[#888888] text-base">
                    {{ __('training_detail.registration_opens_at', ['date' => $training->registration_opens_at->format('d.m.Y H:i')]) }}
                </p>
            @endif
        </div>

    @elseif($registrationState === 'full')
        <div class="bg-[#111111] border border-[#333333] p-10 flex flex-col items-center gap-4 text-center">
            <svg class="w-12 h-12 text-[#555555]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
            <h3 class="font-display font-bold text-2xl tracking-wide text-white">{{ __('training_detail.capacity_full') }}</h3>
            <p class="text-[#888888] text-base">{{ __('training_detail.training_full_message') }}</p>
        </div>

    @elseif($registrationState === 'payment_success')
        <div class="bg-[#111111] rounded-2xl border border-[#222222] p-10 flex flex-col items-center gap-6 text-center">
            <span class="text-[#22C55E] text-[10px] font-bold tracking-[2px]">{{ __('training_detail.state_payment_success') }}</span>
            <div class="w-[72px] h-[72px] rounded-full bg-[#22C55E]/10 flex items-center justify-center">
                <svg class="w-9 h-9 text-[#22C55E]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <h3 class="font-display font-bold text-[28px] text-white">{{ __('training_detail.payment_success_title') }}</h3>
            <p class="text-[#888888] text-sm leading-relaxed">{{ __('training_detail.payment_success_message') }}</p>

            <div class="w-full h-px bg-[#222222]"></div>

            <div class="w-full flex flex-col gap-3">
                <div class="flex justify-between w-full">
                    <span class="text-[#888888] text-[13px]">{{ __('training_detail.dr_training') }}</span>
                    <span class="text-white text-[13px] font-medium">{{ $training->getTranslation('title', app()->getLocale()) }}</span>
                </div>
                @if($training->event_date)
                    <div class="flex justify-between w-full">
                        <span class="text-[#888888] text-[13px]">{{ __('training_detail.dr_date') }}</span>
                        <span class="text-white text-[13px] font-medium">{{ $training->event_date->translatedFormat('l, j. F Y') }}@if($training->start_time) — {{ $training->start_time }}@endif</span>
                    </div>
                @endif
                @if($training->city)
                    <div class="flex justify-between w-full">
                        <span class="text-[#888888] text-[13px]">{{ __('training_detail.dr_location') }}</span>
                        <span class="text-white text-[13px] font-medium">{{ $training->city->name }}</span>
                    </div>
                @endif
                @if($training->price_amount)
                    <div class="flex justify-between w-full">
                        <span class="text-[#888888] text-[13px]">{{ __('training_detail.dr_amount') }}</span>
                        <span class="text-[#22C55E] text-[13px] font-bold">{{ number_format($training->price_amount, 2, ',', ' ') }} {{ $training->currency ?? 'EUR' }}</span>
                    </div>
                @endif
                <div class="flex justify-between w-full">
                    <span class="text-[#888888] text-[13px]">{{ __('training_detail.dr_payment_method') }}</span>
                    <span class="text-white text-[13px] font-medium">GoPay</span>
                </div>
            </div>

            <div class="w-full h-px bg-[#222222]"></div>

            <div class="w-full rounded-[10px] bg-[#22C55E]/[0.06] border border-[#22C55E]/20 p-4 flex items-center gap-2.5">
                <svg class="w-[18px] h-[18px] text-[#22C55E] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span class="text-[#22C55E]/80 text-xs font-medium">{{ __('training_detail.payment_confirmation_email') }}</span>
            </div>
        </div>

    @elseif($registrationState === 'already_registered')
        <div class="bg-[#111111] rounded-2xl border border-[#222222] p-10 flex flex-col items-center gap-6 text-center">
            <span class="text-[#22C55E] text-[10px] font-bold tracking-[2px]">{{ __('training_detail.state_registered') }}</span>
            <div class="w-[72px] h-[72px] rounded-full bg-[#22C55E]/10 flex items-center justify-center">
                <svg class="w-9 h-9 text-[#22C55E]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <h3 class="font-display font-bold text-[28px] text-white">{{ __('training_detail.already_registered_title') }}</h3>
            <p class="text-[#888888] text-sm leading-relaxed">{{ __('training_detail.already_registered_message') }}</p>

            <div class="w-full h-px bg-[#222222]"></div>

            <div class="w-full flex flex-col gap-3">
                <div class="flex justify-between w-full">
                    <span class="text-[#888888] text-[13px]">{{ __('training_detail.dr_training') }}</span>
                    <span class="text-white text-[13px] font-medium">{{ $training->getTranslation('title', app()->getLocale()) }}</span>
                </div>
                @if($training->event_date)
                    <div class="flex justify-between w-full">
                        <span class="text-[#888888] text-[13px]">{{ __('training_detail.dr_date') }}</span>
                        <span class="text-white text-[13px] font-medium">{{ $training->event_date->translatedFormat('l, j. F Y') }}@if($training->start_time) — {{ $training->start_time }}@endif</span>
                    </div>
                @endif
                @if($training->city)
                    <div class="flex justify-between w-full">
                        <span class="text-[#888888] text-[13px]">{{ __('training_detail.dr_location') }}</span>
                        <span class="text-white text-[13px] font-medium">{{ $training->city->name }}</span>
                    </div>
                @endif
                @php
                    $user = auth()->user();
                    $reg = $user ? \App\Models\TrainingRegistration::where('training_id', $training->id)->where('user_id', $user->id)->first() : null;
                    $hasMembership = $user && $user->hasActiveMembershipForTeam($training->team_id);
                @endphp
                <div class="flex justify-between w-full">
                    <span class="text-[#888888] text-[13px]">{{ __('training_detail.dr_membership') }}</span>
                    <div class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-[#22C55E]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <span class="text-[#22C55E] text-[13px] font-medium">{{ $hasMembership ? __('training_detail.membership_active') : __('training_detail.membership_not_required') }}</span>
                    </div>
                </div>
            </div>
        </div>

    @elseif($registrationState === 'free_approved')
        <div class="bg-[#111111] border border-emerald-500/30 p-10 flex flex-col items-center gap-4 text-center">
            <svg class="w-12 h-12 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            <h3 class="font-display font-bold text-2xl tracking-wide text-white">{{ __('training_detail.form_success_title') }}</h3>
            <p class="text-[#888888] text-base">{{ __('training_detail.free_approved_message') }}</p>
        </div>

    @elseif($registrationState === 'membership_valid')
        <div class="bg-[#111111] border border-emerald-500/30 p-10 flex flex-col items-center gap-4 text-center">
            <svg class="w-12 h-12 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            <h3 class="font-display font-bold text-2xl tracking-wide text-white">{{ __('training_detail.form_success_title') }}</h3>
            <p class="text-[#888888] text-base">{{ __('training_detail.membership_valid_message') }}</p>
        </div>

    @elseif($registrationState === 'membership_needed')
        @php
            $team = $training->team;
            $season = $team->currentSeason;
            $enabledMethods = $team->getEnabledPaymentMethodKeys();
            $feeLabel = $season ? number_format($season->proratedFee(), 2) . ' ' . ($season->fee_currency ?? 'EUR') : '';
            $authUser = auth()->user();
            $membershipPayment = null;
            if ($authUser && $season) {
                $membershipPayment = app(\App\Services\PaymentService::class)->ensurePendingMembershipPayment(
                    user: $authUser,
                    team: $team,
                    season: $season,
                );
            }
        @endphp
        <div class="bg-[#111111] rounded-2xl border border-[#222222] p-10 flex flex-col items-center gap-6 text-center">
            <span class="text-[#DC2626] text-[10px] font-bold tracking-[2px]">{{ __('training_detail.state_membership_needed') }}</span>
            <div class="w-[72px] h-[72px] rounded-full bg-[#DC2626]/10 flex items-center justify-center">
                <svg class="w-9 h-9 text-[#DC2626]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <h3 class="font-display font-bold text-[28px] text-white">{{ __('training_detail.membership_needed_title') }}</h3>
            <p class="text-[#888888] text-sm leading-relaxed">{{ __('training_detail.membership_needed_message') }}</p>

            <div class="w-full h-px bg-[#222222]"></div>

            <span class="text-white text-[15px] font-semibold">{{ __('training_detail.payment_method_label') }}</span>

            @if($season)
                <div class="w-full rounded-xl bg-[#0A0A0A] border border-[#333333] p-4 text-left flex flex-col gap-1">
                    <span class="text-white text-sm font-semibold">{{ $season->name }}</span>
                    <span class="text-[#FF2D2D] text-[13px] font-medium">{{ $feeLabel }} {{ __('training_detail.season_remaining') }}</span>
                    <span class="text-[#666666] text-xs">{{ __('training_detail.season_prorated_note') }}</span>
                </div>
            @endif

            @include('components.training-payment-methods', [
                'enabledMethods' => $enabledMethods,
                'selectedPaymentMethod' => $selectedPaymentMethod,
                'feeLabel' => $feeLabel,
                'feeAmount' => $season ? $season->proratedFee() : 0,
                'feeCurrency' => $season->fee_currency ?? 'EUR',
                'team' => $team,
                'season' => $season,
                'variableSymbol' => $membershipPayment?->formattedVariableSymbol(),
                'paymentNote' => $training->renderQrPaymentNote($authUser) ?: $membershipPayment?->payable?->getQrPaymentNote(),
            ])
        </div>

    @elseif($registrationState === 'payment_needed')
        @php
            $team = $training->team;
            $enabledMethods = $training->effectivePaymentMethodKeys();
            $priceLabel = number_format($training->price_amount, 2) . ' EUR';
            $pendingPayment = $pendingPaymentId ? \App\Models\Payment::find($pendingPaymentId) : null;
        @endphp
        <div class="bg-[#111111] rounded-2xl border border-[#222222] p-10 flex flex-col items-center gap-6 text-center">
            <span class="text-[#F59E0B] text-[10px] font-bold tracking-[2px]">{{ __('training_detail.state_payment_needed') }}</span>
            <div class="w-[72px] h-[72px] rounded-full bg-[#F59E0B]/10 flex items-center justify-center">
                <svg class="w-9 h-9 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            </div>
            <h3 class="font-display font-bold text-[28px] text-white">{{ __('training_detail.payment_needed_title') }}</h3>
            <p class="text-[#888888] text-sm leading-relaxed">{{ __('training_detail.payment_needed_message', ['price' => $priceLabel]) }}</p>

            <div class="w-full h-px bg-[#222222]"></div>

            <span class="text-white text-[15px] font-semibold">{{ __('training_detail.payment_method_label') }}</span>

            @include('components.training-payment-methods', [
                'enabledMethods' => $enabledMethods,
                'selectedPaymentMethod' => $selectedPaymentMethod,
                'feeLabel' => $priceLabel,
                'feeAmount' => $training->price_amount,
                'feeCurrency' => 'EUR',
                'team' => $team,
                'season' => null,
                'payable' => $training,
                'variableSymbol' => $pendingPayment?->formattedVariableSymbol(),
                'paymentNote' => $pendingPayment?->payable?->getQrPaymentNote(),
                'context' => 'registration',
            ])
        </div>

    @else
        <form wire:submit="submit" class="flex flex-col gap-6">
            @if(count($schema) > 0)
                @php $halfFields = []; @endphp
                @foreach($schema as $field)
                    @php
                        $fieldName = $field['name'];
                        $fieldType = $field['type'] ?? 'text_input';
                        $isHalf = ($field['width'] ?? 'full') === 'half';
                        $isRequired = $field['required'] ?? false;
                        $label = is_array($field['label'] ?? null) ? ($field['label'][$locale] ?? $field['label']['sk'] ?? '') : ($field['label'] ?? '');
                        $placeholder = is_array($field['placeholder'] ?? null) ? ($field['placeholder'][$locale] ?? $field['placeholder']['sk'] ?? '') : ($field['placeholder'] ?? '');
                        $helperText = is_array($field['helper_text'] ?? null) ? ($field['helper_text'][$locale] ?? $field['helper_text']['sk'] ?? '') : ($field['helper_text'] ?? '');
                        $options = \App\Support\RegistrationFieldOptions::resolve($field, $locale);
                        $hasCondition = $field['has_condition'] ?? false;
                        $conditionField = $field['condition_field'] ?? null;
                        $conditionValues = $field['condition_values'] ?? null;
                        if (! is_array($conditionValues) || empty($conditionValues)) {
                            $legacy = $field['condition_value'] ?? null;
                            $conditionValues = ($legacy !== null && $legacy !== '') ? [$legacy] : [];
                        }
                        $conditionValues = array_map('strval', $conditionValues);
                        $inputClass = 'bg-[#0A0A0A] border border-[#333333] text-white text-sm px-4 py-3.5 focus:border-bcz-red focus:ring-0 outline-none w-full placeholder-[#555555]';
                    @endphp

                    @if($hasCondition && $conditionField && ! empty($conditionValues))
                        @php
                            $current = $this->fields[$conditionField] ?? null;
                            $show = is_array($current)
                                ? (bool) array_intersect(array_map('strval', $current), $conditionValues)
                                : in_array((string) ($current ?? ''), $conditionValues, true);
                        @endphp
                        @if(!$show) @continue @endif
                    @endif

                    @if($isHalf)
                        @php $halfFields[] = $field; @endphp
                        @if(count($halfFields) === 2)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                @foreach($halfFields as $hf)
                                    @php
                                        $hfName = $hf['name'];
                                        $hfType = $hf['type'] ?? 'text_input';
                                        $hfRequired = $hf['required'] ?? false;
                                        $hfLabel = is_array($hf['label'] ?? null) ? ($hf['label'][$locale] ?? $hf['label']['sk'] ?? '') : ($hf['label'] ?? '');
                                        $hfPlaceholder = is_array($hf['placeholder'] ?? null) ? ($hf['placeholder'][$locale] ?? $hf['placeholder']['sk'] ?? '') : ($hf['placeholder'] ?? '');
                                        $hfHelper = is_array($hf['helper_text'] ?? null) ? ($hf['helper_text'][$locale] ?? $hf['helper_text']['sk'] ?? '') : ($hf['helper_text'] ?? '');
                                        $hfOptions = \App\Support\RegistrationFieldOptions::resolve($hf, $locale);
                                    @endphp
                                    <div class="flex flex-col gap-2">
                                        <label class="text-[#888888] text-[13px] font-medium">{{ $hfLabel }} @if($hfRequired)<span class="text-bcz-red">*</span>@endif</label>
                                        @include('components.training-registration-field', ['fieldName' => $hfName, 'fieldType' => $hfType, 'placeholder' => $hfPlaceholder, 'options' => $hfOptions, 'isRequired' => $hfRequired, 'inputClass' => $inputClass, 'isDisabled' => $isLoggedIn && in_array($hfType, $prefillableTypes)])
                                        @error('fields.' . $hfName) <span class="text-red-500 text-xs">{!! $message !!}</span> @enderror
                                        @if(trim(strip_tags((string) $hfHelper)) !== '')
                                            <div class="text-[#888888] text-[12px] leading-[1.6] [&>p]:m-0 [&_a]:text-bcz-red [&_a]:underline [&_ul]:list-disc [&_ul]:pl-4 [&_ol]:list-decimal [&_ol]:pl-4">{!! $hfHelper !!}</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            @php $halfFields = []; @endphp
                        @endif
                    @else
                        {{-- Flush any pending half field --}}
                        @if(count($halfFields) > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                @foreach($halfFields as $hf)
                                    @php
                                        $hfName = $hf['name'];
                                        $hfType = $hf['type'] ?? 'text_input';
                                        $hfRequired = $hf['required'] ?? false;
                                        $hfLabel = is_array($hf['label'] ?? null) ? ($hf['label'][$locale] ?? $hf['label']['sk'] ?? '') : ($hf['label'] ?? '');
                                        $hfPlaceholder = is_array($hf['placeholder'] ?? null) ? ($hf['placeholder'][$locale] ?? $hf['placeholder']['sk'] ?? '') : ($hf['placeholder'] ?? '');
                                        $hfHelper = is_array($hf['helper_text'] ?? null) ? ($hf['helper_text'][$locale] ?? $hf['helper_text']['sk'] ?? '') : ($hf['helper_text'] ?? '');
                                        $hfOptions = \App\Support\RegistrationFieldOptions::resolve($hf, $locale);
                                    @endphp
                                    <div class="flex flex-col gap-2">
                                        <label class="text-[#888888] text-[13px] font-medium">{{ $hfLabel }} @if($hfRequired)<span class="text-bcz-red">*</span>@endif</label>
                                        @include('components.training-registration-field', ['fieldName' => $hfName, 'fieldType' => $hfType, 'placeholder' => $hfPlaceholder, 'options' => $hfOptions, 'isRequired' => $hfRequired, 'inputClass' => $inputClass, 'isDisabled' => $isLoggedIn && in_array($hfType, $prefillableTypes)])
                                        @error('fields.' . $hfName) <span class="text-red-500 text-xs">{!! $message !!}</span> @enderror
                                        @if(trim(strip_tags((string) $hfHelper)) !== '')
                                            <div class="text-[#888888] text-[12px] leading-[1.6] [&>p]:m-0 [&_a]:text-bcz-red [&_a]:underline [&_ul]:list-disc [&_ul]:pl-4 [&_ol]:list-decimal [&_ol]:pl-4">{!! $hfHelper !!}</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            @php $halfFields = []; @endphp
                        @endif

                        <div class="flex flex-col gap-2">
                            <label class="text-[#888888] text-[13px] font-medium">{{ $label }} @if($isRequired)<span class="text-bcz-red">*</span>@endif</label>
                            @include('components.training-registration-field', ['fieldName' => $fieldName, 'fieldType' => $fieldType, 'placeholder' => $placeholder, 'options' => $options, 'isRequired' => $isRequired, 'inputClass' => $inputClass, 'isDisabled' => $isLoggedIn && in_array($fieldType, $prefillableTypes)])
                            @error('fields.' . $fieldName) <span class="text-red-500 text-xs">{!! $message !!}</span> @enderror
                            @if(trim(strip_tags((string) $helperText)) !== '')
                                <div class="text-[#888888] text-[12px] leading-[1.6] [&>p]:m-0 [&_a]:text-bcz-red [&_a]:underline [&_ul]:list-disc [&_ul]:pl-4 [&_ol]:list-decimal [&_ol]:pl-4">{!! $helperText !!}</div>
                            @endif
                        </div>
                    @endif
                @endforeach

                {{-- Flush remaining half field --}}
                @if(count($halfFields) > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        @foreach($halfFields as $hf)
                            @php
                                $hfName = $hf['name'];
                                $hfType = $hf['type'] ?? 'text_input';
                                $hfRequired = $hf['required'] ?? false;
                                $hfLabel = is_array($hf['label'] ?? null) ? ($hf['label'][$locale] ?? $hf['label']['sk'] ?? '') : ($hf['label'] ?? '');
                                $hfPlaceholder = is_array($hf['placeholder'] ?? null) ? ($hf['placeholder'][$locale] ?? $hf['placeholder']['sk'] ?? '') : ($hf['placeholder'] ?? '');
                                $hfHelper = is_array($hf['helper_text'] ?? null) ? ($hf['helper_text'][$locale] ?? $hf['helper_text']['sk'] ?? '') : ($hf['helper_text'] ?? '');
                                $hfOptions = \App\Support\RegistrationFieldOptions::resolve($hf, $locale);
                            @endphp
                            <div class="flex flex-col gap-2">
                                <label class="text-[#888888] text-[13px] font-medium">{{ $hfLabel }} @if($hfRequired)<span class="text-bcz-red">*</span>@endif</label>
                                @include('components.training-registration-field', ['fieldName' => $hfName, 'fieldType' => $hfType, 'placeholder' => $hfPlaceholder, 'options' => $hfOptions, 'isRequired' => $hfRequired, 'inputClass' => $inputClass, 'isDisabled' => $isLoggedIn && in_array($hfType, $prefillableTypes)])
                                @error('fields.' . $hfName) <span class="text-red-500 text-xs">{!! $message !!}</span> @enderror
                                @if(trim(strip_tags((string) $hfHelper)) !== '')
                                    <div class="text-[#888888] text-[12px] leading-[1.6] [&>p]:m-0 [&_a]:text-bcz-red [&_a]:underline [&_ul]:list-disc [&_ul]:pl-4 [&_ol]:list-decimal [&_ol]:pl-4">{!! $hfHelper !!}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif

            <div class="h-px bg-[#222222]"></div>

            <x-gdpr-checkbox />

            {{-- Submit --}}
            <button type="submit" wire:loading.attr="disabled" class="flex items-center justify-center bg-bcz-red text-white text-sm font-bold tracking-wider px-6 py-[18px] hover:bg-red-700 transition w-full disabled:opacity-50">
                <span wire:loading.remove>{{ __('training_detail.form_submit') }}</span>
                <span wire:loading>{{ __('training_detail.form_submitting') }}</span>
            </button>
        </form>
    @endif
</div>
