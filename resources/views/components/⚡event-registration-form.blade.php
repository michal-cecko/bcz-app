<?php

use App\Enums\EventPricingTypeEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\RegistrationFieldTypeEnum;
use App\Enums\RegistrationStatusEnum;
use App\Enums\RoleEnum;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use App\Services\PaymentService;
use App\Services\RegistrationService;
use Livewire\Component;

new class extends Component
{
    public Event $event;

    public array $fields = [];

    public bool $gdprAgreed = false;

    public string $registrationState = 'form';

    public ?string $selectedPaymentMethod = null;

    public ?string $pendingPaymentId = null;

    public function mount(Event $event): void
    {
        $this->event = $event;

        // Check if returning from GoPay payment success
        if (request()->query('payment') === 'success') {
            $this->registrationState = 'payment_success';

            return;
        }

        $org = $this->event->organization;

        // Check registration window
        if ($this->event->status !== 'registering') {
            $this->registrationState = 'registration_closed';

            return;
        }

        // Check if already registered (logged-in users)
        $user = auth()->user();
        if ($user) {
            $registration = EventRegistration::where('event_id', $this->event->id)
                ->where('user_id', $user->id)
                ->whereNotIn('status', [RegistrationStatusEnum::Cancelled->value])
                ->first();

            if ($registration) {
                if ($registration->status === RegistrationStatusEnum::Pending->value) {
                    $this->registrationState = $this->determinePostState($user);
                    $this->autoSelectPaymentMethod();
                    $this->pendingPaymentId = $registration->payments()
                        ->where('status', \App\Enums\PaymentStatusEnum::PENDING)
                        ->latest('created_at')->value('id');
                } elseif ($registration->status === RegistrationStatusEnum::Approved->value) {
                    // Re-check: paid event with no completed payment
                    $needsPayment = $org
                        && $org->pricing_type === EventPricingTypeEnum::Paid
                        && $org->price_amount > 0
                        && $registration->payments()->where('status', \App\Enums\PaymentStatusEnum::COMPLETED)->doesntExist();

                    $this->registrationState = $needsPayment
                        ? $this->determinePostState($user)
                        : 'already_registered';

                    if ($needsPayment) {
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

        // Check if event is full
        if ($org && $org->max_capacity && $this->event->registrations()->count() >= $org->max_capacity) {
            $this->registrationState = 'full';

            return;
        }

        // Initialize form fields
        $schema = $org->registration_form_schema ?? [];
        foreach ($schema as $field) {
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

            $fieldKey = $field['name'] ?? $field['key'] ?? '';
            $this->fields[$fieldKey] = $prefillValue ?? '';
        }
    }

    public function submit(): void
    {
        $org = $this->event->organization;
        $schema = $org->registration_form_schema ?? [];
        $rules = [];
        $attributes = [];
        $locale = app()->getLocale();

        foreach ($schema as $field) {
            $fieldKey = $field['name'] ?? $field['key'] ?? '';
            $key = 'fields.' . $fieldKey;
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
                ? ($field['label'][$locale] ?? $field['label']['sk'] ?? $fieldKey)
                : ($field['label'] ?? $fieldKey);
            $attributes[$key] = $label;
        }

        $rules['gdprAgreed'] = 'accepted';
        $attributes['gdprAgreed'] = __('consent.privacy_policy');

        $this->validate($rules, [], $attributes);

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
                $this->addError('fields.' . $this->getEmailFieldName($schema), __('event_detail.error_email_exists'));

                return;
            }

            // Duplicate phone check for guests
            if ($phone && $email && User::where('phone', $phone)->where('email', '!=', $email)->exists()) {
                $this->addError('fields.' . $this->getPhoneFieldName($schema), __('event_detail.error_phone_exists'));

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
                        ->where('teams.id', $this->event->team_id)
                        ->wherePivot('role', RoleEnum::ATHLETE->value)
                        ->exists();

                    if (! $alreadyHasRole) {
                        $user->teams()->attach($this->event->team_id, [
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

        $isPaid = $org && $org->pricing_type === EventPricingTypeEnum::Paid && $org->price_amount > 0;
        $status = $isPaid ? RegistrationStatusEnum::Pending : RegistrationStatusEnum::Approved;

        $registration = EventRegistration::create([
            'event_id' => $this->event->id,
            'user_id' => $user?->id,
            'status' => $status->value,
            'registered_at' => now(),
            'payment_due_at' => $isPaid ? now()->addDays(14) : null,
        ]);

        // Store field values
        foreach ($this->fields as $key => $value) {
            if ($value !== '' && $value !== null) {
                $fieldMeta = collect($schema)->firstWhere('name', $key) ?? collect($schema)->firstWhere('key', $key);
                $registration->fieldValues()->create([
                    'field_key' => $key,
                    'field_type' => $fieldMeta['type'] ?? 'text_input',
                    'value' => $value,
                ]);
            }
        }

        $payment = null;
        if ($user && $isPaid) {
            $paymentService = app(PaymentService::class);
            $payment = $paymentService->createPendingPayment(
                user: $user,
                team: $this->event->team,
                payable: $registration,
                amount: (float) $org->price_amount,
                currency: $org->price_currency ?? 'EUR',
            );
            $this->pendingPaymentId = $payment->id;
        }

        if ($user) {
            RegistrationService::sendConfirmation(
                user: $user,
                registrationType: 'podujatie',
                registrationTitle: $this->event->getTranslation('title', app()->getLocale()),
                isNewUser: $isNewUser,
                team: $this->event->team,
                customEmailContent: $org->confirmation_email_content,
                locale: app()->getLocale(),
                attachments: $this->event->getMedia('email_attachments'),
                payment: $payment,
            );
        }

        $this->registrationState = $this->determinePostState($user);
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

        $enabledMethods = $this->event->effectivePaymentMethodKeys();
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

            $org = $this->event->organization;
            $registration = EventRegistration::query()
                ->where('event_id', $this->event->id)
                ->where('user_id', $user->id)
                ->latest()
                ->first();

            if (! $registration || ! $org?->price_amount) {
                return;
            }

            try {
                $paymentService = app(PaymentService::class);
                $result = $paymentService->createGoPayPayment(
                    user: $user,
                    team: $this->event->team,
                    payable: $registration,
                    amount: (float) $org->price_amount,
                    currency: $org->price_currency ?? 'EUR',
                );

                $this->redirect($result['url']);
            } catch (\Exception $e) {
                session()->flash('error', 'Platba sa nepodarila. Skúste to znova.');
            }
        }
    }

    /**
     * Determine the post-registration UI state for the event.
     *
     * @return 'free_approved'|'payment_needed'
     */
    protected function determinePostState(?User $user): string
    {
        $org = $this->event->organization;

        if (! $org || $org->pricing_type === EventPricingTypeEnum::Free || ! $org->price_amount) {
            return 'free_approved';
        }

        return 'payment_needed';
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

    protected function getPhoneFieldName(array $schema): string
    {
        foreach ($schema as $field) {
            if (($field['type'] ?? '') === 'phone') {
                return $field['name'] ?? $field['key'] ?? 'phone';
            }
        }

        return 'phone';
    }
};
?>

@php
    $locale = app()->getLocale();
    $org = $event->organization;
    $schema = $org->registration_form_schema ?? [];
    $isLoggedIn = auth()->check();
    $prefillableTypes = ['email', 'first_name', 'last_name', 'full_name', 'phone', 'birth_date', 'gender'];
    $accentColor = $event->eventCategory->color ?? '#FF2D2D';
@endphp

<div>
    @if($registrationState === 'registration_closed')
        <div class="bg-[#111111] rounded-2xl border border-[#222222] p-10 flex flex-col items-center gap-4 text-center">
            <svg class="w-12 h-12 text-[#555555]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            <h3 class="font-display font-bold text-2xl tracking-wide text-white">
                @if($org?->registration_opens_at && now()->lessThan($org->registration_opens_at))
                    {{ __('event_detail.registration_not_yet_open_title') }}
                @else
                    {{ __('event_detail.registration_closed_title') }}
                @endif
            </h3>
            @if($org?->registration_opens_at && now()->lessThan($org->registration_opens_at))
                <p class="text-[#888888] text-base">
                    {{ __('event_detail.registration_opens') }}: {{ $org->registration_opens_at->format('d.m.Y H:i') }}
                </p>
            @endif
        </div>

    @elseif($registrationState === 'full')
        <div class="bg-[#111111] rounded-2xl border border-[#222222] p-10 flex flex-col items-center gap-4 text-center">
            <svg class="w-12 h-12 text-[#555555]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
            <h3 class="font-display font-bold text-2xl tracking-wide text-white">{{ __('event_detail.capacity_full') }}</h3>
            <p class="text-[#888888] text-base">{{ __('event_detail.capacity_full_message') }}</p>
        </div>

    @elseif($registrationState === 'payment_success')
        <div class="bg-[#111111] rounded-2xl border border-[#222222] p-10 flex flex-col items-center gap-6 text-center">
            <span class="text-[#22C55E] text-[10px] font-bold tracking-[2px]">{{ __('event_detail.state_payment_success') }}</span>
            <div class="w-[72px] h-[72px] rounded-full bg-[#22C55E]/10 flex items-center justify-center">
                <svg class="w-9 h-9 text-[#22C55E]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <h3 class="font-display font-bold text-[28px] text-white">{{ __('event_detail.payment_success_title') }}</h3>
            <p class="text-[#888888] text-sm leading-relaxed">{{ __('event_detail.payment_success_message') }}</p>

            <div class="w-full h-px bg-[#222222]"></div>

            <div class="w-full flex flex-col gap-3">
                <div class="flex justify-between w-full">
                    <span class="text-[#888888] text-[13px]">{{ __('event_detail.dr_event') }}</span>
                    <span class="text-white text-[13px] font-medium">{{ $event->getTranslation('title', app()->getLocale()) }}</span>
                </div>
                @if($event->date)
                    <div class="flex justify-between w-full">
                        <span class="text-[#888888] text-[13px]">{{ __('event_detail.dr_date') }}</span>
                        <span class="text-white text-[13px] font-medium">{{ $event->date->translatedFormat('l, j. F Y') }}</span>
                    </div>
                @endif
                @if($event->city)
                    <div class="flex justify-between w-full">
                        <span class="text-[#888888] text-[13px]">{{ __('event_detail.dr_location') }}</span>
                        <span class="text-white text-[13px] font-medium">{{ $event->city }}</span>
                    </div>
                @endif
                @if($org?->price_amount)
                    <div class="flex justify-between w-full">
                        <span class="text-[#888888] text-[13px]">{{ __('event_detail.dr_amount') }}</span>
                        <span class="text-[#22C55E] text-[13px] font-bold">{{ number_format($org->price_amount, 2, ',', ' ') }} {{ $org->price_currency ?? 'EUR' }}</span>
                    </div>
                @endif
                <div class="flex justify-between w-full">
                    <span class="text-[#888888] text-[13px]">{{ __('event_detail.dr_payment_method') }}</span>
                    <span class="text-white text-[13px] font-medium">GoPay</span>
                </div>
            </div>

            <div class="w-full h-px bg-[#222222]"></div>

            <div class="w-full rounded-[10px] bg-[#22C55E]/[0.06] border border-[#22C55E]/20 p-4 flex items-center gap-2.5">
                <svg class="w-[18px] h-[18px] text-[#22C55E] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span class="text-[#22C55E]/80 text-xs font-medium">{{ __('event_detail.payment_confirmation_email') }}</span>
            </div>
        </div>

    @elseif($registrationState === 'already_registered')
        <div class="bg-[#111111] rounded-2xl border border-[#222222] p-10 flex flex-col items-center gap-6 text-center">
            <span class="text-[#22C55E] text-[10px] font-bold tracking-[2px]">{{ __('event_detail.state_registered') }}</span>
            <div class="w-[72px] h-[72px] rounded-full bg-[#22C55E]/10 flex items-center justify-center">
                <svg class="w-9 h-9 text-[#22C55E]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <h3 class="font-display font-bold text-[28px] text-white">{{ __('event_detail.already_registered_title') }}</h3>
            <p class="text-[#888888] text-sm leading-relaxed">{{ __('event_detail.already_registered_message') }}</p>

            <div class="w-full h-px bg-[#222222]"></div>

            <div class="w-full flex flex-col gap-3">
                <div class="flex justify-between w-full">
                    <span class="text-[#888888] text-[13px]">{{ __('event_detail.dr_event') }}</span>
                    <span class="text-white text-[13px] font-medium">{{ $event->getTranslation('title', app()->getLocale()) }}</span>
                </div>
                @if($event->date)
                    <div class="flex justify-between w-full">
                        <span class="text-[#888888] text-[13px]">{{ __('event_detail.dr_date') }}</span>
                        <span class="text-white text-[13px] font-medium">{{ $event->date->translatedFormat('l, j. F Y') }}</span>
                    </div>
                @endif
                @if($event->city)
                    <div class="flex justify-between w-full">
                        <span class="text-[#888888] text-[13px]">{{ __('event_detail.dr_location') }}</span>
                        <span class="text-white text-[13px] font-medium">{{ $event->city }}</span>
                    </div>
                @endif
            </div>
        </div>

    @elseif($registrationState === 'free_approved')
        <div class="bg-[#111111] rounded-2xl border border-emerald-500/30 p-10 flex flex-col items-center gap-6 text-center">
            <div class="w-[72px] h-[72px] rounded-full bg-[#22C55E]/10 flex items-center justify-center">
                <svg class="w-9 h-9 text-[#22C55E]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <h3 class="font-display font-bold text-[28px] text-white">{{ __('event_detail.registration_success_title') }}</h3>
            <p class="text-[#888888] text-sm leading-relaxed">{{ __('event_detail.registration_success_message') }}</p>

            <div class="w-full h-px bg-[#222222]"></div>

            <div class="w-full flex flex-col gap-3">
                <div class="flex justify-between w-full">
                    <span class="text-[#888888] text-[13px]">{{ __('event_detail.dr_event') }}</span>
                    <span class="text-white text-[13px] font-medium">{{ $event->getTranslation('title', app()->getLocale()) }}</span>
                </div>
                @if($event->date)
                    <div class="flex justify-between w-full">
                        <span class="text-[#888888] text-[13px]">{{ __('event_detail.dr_date') }}</span>
                        <span class="text-white text-[13px] font-medium">{{ $event->date->translatedFormat('l, j. F Y') }}</span>
                    </div>
                @endif
                @if($event->city)
                    <div class="flex justify-between w-full">
                        <span class="text-[#888888] text-[13px]">{{ __('event_detail.dr_location') }}</span>
                        <span class="text-white text-[13px] font-medium">{{ $event->city }}</span>
                    </div>
                @endif
            </div>

            <div class="w-full rounded-[10px] bg-[#22C55E]/[0.06] border border-[#22C55E]/20 p-4 flex items-center gap-2.5">
                <svg class="w-[18px] h-[18px] text-[#22C55E] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span class="text-[#22C55E]/80 text-xs font-medium">{{ __('event_detail.payment_confirmation_email') }}</span>
            </div>
        </div>

    @elseif($registrationState === 'payment_needed')
        @php
            $team = $event->team;
            $enabledMethods = $event->effectivePaymentMethodKeys();
            $priceLabel = number_format($org->price_amount, 2) . ' ' . ($org->price_currency ?? 'EUR');
            $pendingPayment = $pendingPaymentId ? \App\Models\Payment::find($pendingPaymentId) : null;
        @endphp
        <div class="bg-[#111111] rounded-2xl border border-[#222222] p-10 flex flex-col items-center gap-6 text-center">
            <span class="text-[#F59E0B] text-[10px] font-bold tracking-[2px]">{{ __('event_detail.state_payment_needed') }}</span>
            <div class="w-[72px] h-[72px] rounded-full bg-[#F59E0B]/10 flex items-center justify-center">
                <svg class="w-9 h-9 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            </div>
            <h3 class="font-display font-bold text-[28px] text-white">{{ __('event_detail.payment_needed_title') }}</h3>
            <p class="text-[#888888] text-sm leading-relaxed">{{ __('event_detail.payment_needed_message', ['price' => $priceLabel]) }}</p>

            <div class="w-full h-px bg-[#222222]"></div>

            <span class="text-white text-[15px] font-semibold">{{ __('event_detail.payment_method_label') }}</span>

            @include('components.training-payment-methods', [
                'enabledMethods' => $enabledMethods,
                'selectedPaymentMethod' => $selectedPaymentMethod,
                'feeLabel' => $priceLabel,
                'feeAmount' => $org->price_amount,
                'feeCurrency' => $org->price_currency ?? 'EUR',
                'team' => $team,
                'season' => null,
                'payable' => $event,
                'variableSymbol' => $pendingPayment?->formattedVariableSymbol(),
                'paymentNote' => $pendingPayment?->payable?->getQrPaymentNote(),
                'context' => 'registration',
            ])
        </div>

    @else
        <div class="bg-[#111111] rounded-2xl border border-[#222222] p-8">
            <div class="flex flex-col gap-2 mb-8">
                <h3 class="font-display font-bold text-[32px] text-white">{{ __('event_detail.registration_form') }}</h3>
                <p class="text-[#888888] text-[16px] font-sans">{{ __('event_detail.registration_form_event_desc') }}</p>
            </div>

            <form wire:submit="submit" class="flex flex-col gap-6">
                @if(count($schema) > 0)
                    @php $halfFields = []; @endphp
                    @foreach($schema as $field)
                        @php
                            $fieldName = $field['name'] ?? $field['key'] ?? '';
                            $fieldType = $field['type'] ?? 'text_input';
                            $isHalf = ($field['width'] ?? 'full') === 'half';
                            $isRequired = $field['required'] ?? false;
                            $label = is_array($field['label'] ?? null) ? ($field['label'][$locale] ?? $field['label']['sk'] ?? '') : ($field['label'] ?? '');
                            $placeholder = is_array($field['placeholder'] ?? null) ? ($field['placeholder'][$locale] ?? $field['placeholder']['sk'] ?? '') : ($field['placeholder'] ?? '');
                            $options = [];
                            if (!empty($field['options'])) {
                                $opts = is_array($field['options']) ? $field['options'] : preg_split('/\r\n|\r|\n/', $field['options']);
                                $options = array_map('trim', $opts);
                            }
                            $hasCondition = $field['has_condition'] ?? false;
                            $conditionField = $field['condition_field'] ?? null;
                            $conditionValue = $field['condition_value'] ?? null;
                            $inputClass = 'bg-[#0A0A0A] border border-[#333333] rounded-lg h-[44px] px-3.5 text-white text-[14px] focus:border-bcz-red focus:ring-0 outline-none w-full placeholder-[#555555]';
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
                                            $hfName = $hf['name'] ?? $hf['key'] ?? '';
                                            $hfType = $hf['type'] ?? 'text_input';
                                            $hfRequired = $hf['required'] ?? false;
                                            $hfLabel = is_array($hf['label'] ?? null) ? ($hf['label'][$locale] ?? $hf['label']['sk'] ?? '') : ($hf['label'] ?? '');
                                            $hfPlaceholder = is_array($hf['placeholder'] ?? null) ? ($hf['placeholder'][$locale] ?? $hf['placeholder']['sk'] ?? '') : ($hf['placeholder'] ?? '');
                                            $hfOptions = [];
                                            if (!empty($hf['options'])) {
                                                $hfOpts = is_array($hf['options']) ? $hf['options'] : preg_split('/\r\n|\r|\n/', $hf['options']);
                                                $hfOptions = array_map('trim', $hfOpts);
                                            }
                                        @endphp
                                        <div class="flex flex-col gap-2">
                                            <label class="text-[#AAAAAA] text-[13px] font-medium">{{ $hfLabel }} @if($hfRequired)<span class="text-bcz-red">*</span>@endif</label>
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
                                            $hfName = $hf['name'] ?? $hf['key'] ?? '';
                                            $hfType = $hf['type'] ?? 'text_input';
                                            $hfRequired = $hf['required'] ?? false;
                                            $hfLabel = is_array($hf['label'] ?? null) ? ($hf['label'][$locale] ?? $hf['label']['sk'] ?? '') : ($hf['label'] ?? '');
                                            $hfPlaceholder = is_array($hf['placeholder'] ?? null) ? ($hf['placeholder'][$locale] ?? $hf['placeholder']['sk'] ?? '') : ($hf['placeholder'] ?? '');
                                            $hfOptions = [];
                                            if (!empty($hf['options'])) {
                                                $hfOpts = is_array($hf['options']) ? $hf['options'] : preg_split('/\r\n|\r|\n/', $hf['options']);
                                                $hfOptions = array_map('trim', $hfOpts);
                                            }
                                        @endphp
                                        <div class="flex flex-col gap-2">
                                            <label class="text-[#AAAAAA] text-[13px] font-medium">{{ $hfLabel }} @if($hfRequired)<span class="text-bcz-red">*</span>@endif</label>
                                            @include('components.training-registration-field', ['fieldName' => $hfName, 'fieldType' => $hfType, 'placeholder' => $hfPlaceholder, 'options' => $hfOptions, 'isRequired' => $hfRequired, 'inputClass' => $inputClass, 'isDisabled' => $isLoggedIn && in_array($hfType, $prefillableTypes)])
                                            @error('fields.' . $hfName) <span class="text-red-500 text-xs">{!! $message !!}</span> @enderror
                                        </div>
                                    @endforeach
                                </div>
                                @php $halfFields = []; @endphp
                            @endif

                            <div class="flex flex-col gap-2">
                                <label class="text-[#AAAAAA] text-[13px] font-medium">{{ $label }} @if($isRequired)<span class="text-bcz-red">*</span>@endif</label>
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
                                    $hfName = $hf['name'] ?? $hf['key'] ?? '';
                                    $hfType = $hf['type'] ?? 'text_input';
                                    $hfRequired = $hf['required'] ?? false;
                                    $hfLabel = is_array($hf['label'] ?? null) ? ($hf['label'][$locale] ?? $hf['label']['sk'] ?? '') : ($hf['label'] ?? '');
                                    $hfPlaceholder = is_array($hf['placeholder'] ?? null) ? ($hf['placeholder'][$locale] ?? $hf['placeholder']['sk'] ?? '') : ($hf['placeholder'] ?? '');
                                    $hfOptions = [];
                                    if (!empty($hf['options'])) {
                                        $hfOpts = is_array($hf['options']) ? $hf['options'] : preg_split('/\r\n|\r|\n/', $hf['options']);
                                        $hfOptions = array_map('trim', $hfOpts);
                                    }
                                @endphp
                                <div class="flex flex-col gap-2">
                                    <label class="text-[#AAAAAA] text-[13px] font-medium">{{ $hfLabel }} @if($hfRequired)<span class="text-bcz-red">*</span>@endif</label>
                                    @include('components.training-registration-field', ['fieldName' => $hfName, 'fieldType' => $hfType, 'placeholder' => $hfPlaceholder, 'options' => $hfOptions, 'isRequired' => $hfRequired, 'inputClass' => $inputClass, 'isDisabled' => $isLoggedIn && in_array($hfType, $prefillableTypes)])
                                    @error('fields.' . $hfName) <span class="text-red-500 text-xs">{!! $message !!}</span> @enderror
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endif

                <div class="h-px bg-[#222222]"></div>

                <x-gdpr-checkbox />

                {{-- Submit --}}
                <button type="submit" wire:loading.attr="disabled" class="flex items-center justify-center gap-2 bg-bcz-red rounded-lg h-[52px] text-white text-sm font-bold tracking-wider px-6 hover:bg-red-700 transition w-full disabled:opacity-50">
                    <span wire:loading.remove class="flex items-center gap-2">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                        {{ __('event_detail.submit_registration') }}
                    </span>
                    <span wire:loading>{{ __('event_detail.form_submitting') }}</span>
                </button>
            </form>
        </div>
    @endif
</div>
