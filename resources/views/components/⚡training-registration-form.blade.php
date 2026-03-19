<?php

use App\Enums\RegistrationFieldTypeEnum;
use App\Enums\RegistrationStatusEnum;
use App\Enums\RoleEnum;
use App\Models\Training;
use App\Models\TrainingRegistration;
use App\Models\User;
use App\Services\RegistrationService;
use Livewire\Component;

new class extends Component
{
    public Training $training;

    public array $fields = [];

    public string $registrationState = 'form';

    public ?string $selectedPaymentMethod = null;

    public function mount(Training $training): void
    {
        $this->training = $training;

        // Check registration window
        if (! $this->training->isRegistrationOpen()) {
            $this->registrationState = 'registration_closed';

            return;
        }

        // Check if already registered (logged-in users)
        $user = auth()->user();
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

            if ($field['required'] ?? false) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            $type = RegistrationFieldTypeEnum::tryFrom($field['type'] ?? '');
            match ($type) {
                RegistrationFieldTypeEnum::EMAIL => $fieldRules[] = 'email',
                RegistrationFieldTypeEnum::NUMBER_INPUT => $fieldRules[] = 'numeric',
                default => null,
            };

            $rules[$key] = $fieldRules;

            $label = is_array($field['label'] ?? null)
                ? ($field['label'][$locale] ?? $field['label']['sk'] ?? $field['name'])
                : ($field['label'] ?? $field['name']);
            $attributes[$key] = $label;
        }

        $this->validate($rules, [], $attributes);

        $schema = $this->training->registration_form_schema ?? [];
        $authUser = auth()->user();

        if ($authUser) {
            $user = $authUser;
            $isNewUser = false;
        } else {
            $email = RegistrationService::extractEmailFromFormData($this->fields, $schema);
            $name = RegistrationService::extractNameFromFormData($this->fields, $schema);
            $phone = RegistrationService::extractPhoneFromFormData($this->fields, $schema);
            $birthDate = RegistrationService::extractBirthDateFromFormData($this->fields, $schema);
            $gender = RegistrationService::extractGenderFromFormData($this->fields, $schema);

            // Duplicate email check for guests
            if ($email && User::where('email', $email)->exists()) {
                $this->addError('fields.' . $this->getEmailFieldName($schema), __('training_detail.error_email_exists'));

                return;
            }

            // Duplicate phone check for guests
            if ($phone && $email && User::where('phone', $phone)->where('email', '!=', $email)->exists()) {
                $this->addError('fields.' . $this->getPhoneFieldName($schema), __('training_detail.error_phone_exists'));

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

                // Attach new user to team
                if ($isNewUser) {
                    $user->assignRole(RoleEnum::CUSTOMER);

                    $alreadyHasRole = $user->teams()
                        ->where('teams.id', $this->training->team_id)
                        ->wherePivot('role', RoleEnum::ATHLETE->value)
                        ->exists();

                    if (! $alreadyHasRole) {
                        $user->teams()->attach($this->training->team_id, [
                            'role' => RoleEnum::ATHLETE->value,
                            'is_active' => true,
                            'joined_at' => now(),
                        ]);
                    }
                }
            } else {
                $user = null;
                $isNewUser = false;
            }
        }

        $status = RegistrationService::determineRegistrationStatus($this->training, $user);

        $paymentDueAt = $status === RegistrationStatusEnum::Pending ? now()->addDays(7) : null;

        TrainingRegistration::create([
            'training_id' => $this->training->id,
            'user_id' => $user?->id,
            'form_data' => $this->fields,
            'status' => $status->value,
            'registered_at' => now(),
            'payment_due_at' => $paymentDueAt,
        ]);

        if ($user) {
            RegistrationService::sendConfirmation(
                user: $user,
                registrationType: 'tréning',
                registrationTitle: $this->training->getTranslation('title', app()->getLocale()),
                isNewUser: $isNewUser,
                team: $this->training->team,
                customEmailContent: $this->training->confirmation_email_content,
                locale: app()->getLocale(),
            );
        }

        $this->registrationState = RegistrationService::determinePostRegistrationState($this->training, $user);
        $this->autoSelectPaymentMethod();
    }

    public function selectPaymentMethod(string $method): void
    {
        $this->selectedPaymentMethod = $method;
    }

    protected function autoSelectPaymentMethod(): void
    {
        if ($this->selectedPaymentMethod !== null) {
            return;
        }

        $enabledMethods = $this->training->team?->payment_methods_enabled ?? ['stripe', 'bank_transfer', 'cash'];
        $this->selectedPaymentMethod = $enabledMethods[0] ?? null;
    }

    public function handlePayment(): void
    {
        if (! $this->selectedPaymentMethod) {
            return;
        }

        if ($this->selectedPaymentMethod === 'cash') {
            $this->registrationState = 'cash_instructions';

            return;
        }

        if ($this->selectedPaymentMethod === 'bank_transfer') {
            $this->registrationState = 'bank_transfer_details';

            return;
        }

        // Stripe — redirect to checkout (placeholder for now)
        // TODO: implement PaymentService::createCheckoutSession() redirect
    }

    protected function getEmailFieldName(array $schema): string
    {
        foreach ($schema as $field) {
            if (($field['type'] ?? '') === 'email') {
                return $field['name'];
            }
        }

        return 'email';
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
};
?>

@php
    $locale = app()->getLocale();
    $schema = $training->registration_form_schema ?? [];
    $isLoggedIn = auth()->check();
    $prefillableTypes = ['email', 'first_name', 'last_name', 'full_name', 'phone', 'birth_date', 'gender'];
@endphp

<div>
    @if($registrationState === 'registration_closed')
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

    @elseif($registrationState === 'already_registered')
        <div class="bg-[#111111] border border-blue-500/30 p-10 flex flex-col items-center gap-4 text-center">
            <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            <h3 class="font-display font-bold text-2xl tracking-wide text-white">{{ __('training_detail.already_registered_title') }}</h3>
            <p class="text-[#888888] text-base">{{ __('training_detail.already_registered_message') }}</p>
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
            $enabledMethods = $team->payment_methods_enabled ?? ['stripe', 'bank_transfer', 'cash'];
            $feeLabel = $season ? number_format($season->proratedFee(), 2) . ' ' . ($season->fee_currency ?? 'EUR') : '';
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
                'variableSymbol' => $season->variable_symbol ?? null,
                'paymentNote' => $season->payment_note ?? null,
            ])
        </div>

    @elseif($registrationState === 'payment_needed')
        @php
            $team = $training->team;
            $enabledMethods = $team->payment_methods_enabled ?? ['stripe', 'bank_transfer', 'cash'];
            $priceLabel = number_format($training->price_amount, 2) . ' EUR';
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
                'variableSymbol' => $training->variable_symbol ?? null,
                'paymentNote' => $training->payment_note ?? null,
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
                        $options = [];
                        if (!empty($field['options'])) {
                            $opts = is_array($field['options']) ? $field['options'] : explode(',', $field['options']);
                            $options = array_map('trim', $opts);
                        }
                        $hasCondition = $field['has_condition'] ?? false;
                        $conditionField = $field['condition_field'] ?? null;
                        $conditionValue = $field['condition_value'] ?? null;
                        $inputClass = 'bg-[#0A0A0A] border border-[#333333] text-white text-sm px-4 py-3.5 focus:border-bcz-red focus:ring-0 outline-none w-full placeholder-[#555555]';
                    @endphp

                    @if($hasCondition && $conditionField)
                        @php $show = ($this->fields[$conditionField] ?? '') == $conditionValue; @endphp
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
                                        $hfOptions = [];
                                        if (!empty($hf['options'])) {
                                            $hfOpts = is_array($hf['options']) ? $hf['options'] : explode(',', $hf['options']);
                                            $hfOptions = array_map('trim', $hfOpts);
                                        }
                                    @endphp
                                    <div class="flex flex-col gap-2">
                                        <label class="text-[#888888] text-[13px] font-medium">{{ $hfLabel }} @if($hfRequired)<span class="text-bcz-red">*</span>@endif</label>
                                        @include('components.training-registration-field', ['fieldName' => $hfName, 'fieldType' => $hfType, 'placeholder' => $hfPlaceholder, 'options' => $hfOptions, 'isRequired' => $hfRequired, 'inputClass' => $inputClass, 'isDisabled' => $isLoggedIn && in_array($hfType, $prefillableTypes)])
                                        @error('fields.' . $hfName) <span class="text-red-500 text-xs">{!! $message !!}</span> @enderror
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
                                        $hfOptions = [];
                                        if (!empty($hf['options'])) {
                                            $hfOpts = is_array($hf['options']) ? $hf['options'] : explode(',', $hf['options']);
                                            $hfOptions = array_map('trim', $hfOpts);
                                        }
                                    @endphp
                                    <div class="flex flex-col gap-2">
                                        <label class="text-[#888888] text-[13px] font-medium">{{ $hfLabel }} @if($hfRequired)<span class="text-bcz-red">*</span>@endif</label>
                                        @include('components.training-registration-field', ['fieldName' => $hfName, 'fieldType' => $hfType, 'placeholder' => $hfPlaceholder, 'options' => $hfOptions, 'isRequired' => $hfRequired, 'inputClass' => $inputClass, 'isDisabled' => $isLoggedIn && in_array($hfType, $prefillableTypes)])
                                        @error('fields.' . $hfName) <span class="text-red-500 text-xs">{!! $message !!}</span> @enderror
                                    </div>
                                @endforeach
                            </div>
                            @php $halfFields = []; @endphp
                        @endif

                        <div class="flex flex-col gap-2">
                            <label class="text-[#888888] text-[13px] font-medium">{{ $label }} @if($isRequired)<span class="text-bcz-red">*</span>@endif</label>
                            @include('components.training-registration-field', ['fieldName' => $fieldName, 'fieldType' => $fieldType, 'placeholder' => $placeholder, 'options' => $options, 'isRequired' => $isRequired, 'inputClass' => $inputClass, 'isDisabled' => $isLoggedIn && in_array($fieldType, $prefillableTypes)])
                            @error('fields.' . $fieldName) <span class="text-red-500 text-xs">{!! $message !!}</span> @enderror
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
                                $hfOptions = [];
                                if (!empty($hf['options'])) {
                                    $hfOpts = is_array($hf['options']) ? $hf['options'] : explode(',', $hf['options']);
                                    $hfOptions = array_map('trim', $hfOpts);
                                }
                            @endphp
                            <div class="flex flex-col gap-2">
                                <label class="text-[#888888] text-[13px] font-medium">{{ $hfLabel }} @if($hfRequired)<span class="text-bcz-red">*</span>@endif</label>
                                @include('components.training-registration-field', ['fieldName' => $hfName, 'fieldType' => $hfType, 'placeholder' => $hfPlaceholder, 'options' => $hfOptions, 'isRequired' => $hfRequired, 'inputClass' => $inputClass, 'isDisabled' => $isLoggedIn && in_array($hfType, $prefillableTypes)])
                                @error('fields.' . $hfName) <span class="text-red-500 text-xs">{!! $message !!}</span> @enderror
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif

            <div class="h-px bg-[#222222]"></div>

            {{-- Submit --}}
            <button type="submit" wire:loading.attr="disabled" class="flex items-center justify-center bg-bcz-red text-white text-sm font-bold tracking-wider px-6 py-[18px] hover:bg-red-700 transition w-full disabled:opacity-50">
                <span wire:loading.remove>{{ __('training_detail.form_submit') }}</span>
                <span wire:loading>{{ __('training_detail.form_submitting') }}</span>
            </button>

            <p class="text-[#555555] text-xs text-center">{{ __('training_detail.form_consent') }}</p>
        </form>
    @endif
</div>
