<?php

use App\Enums\RegistrationFieldTypeEnum;
use App\Models\Training;
use App\Models\TrainingRegistration;
use App\Services\RegistrationService;
use Livewire\Component;

new class extends Component
{
    public Training $training;

    public array $fields = [];

    public bool $submitted = false;

    public function mount(Training $training): void
    {
        $this->training = $training;

        foreach ($this->training->registration_form_schema ?? [] as $field) {
            $this->fields[$field['name']] = '';
        }
    }

    public function submit(): void
    {
        $rules = [];

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
        }

        $this->validate($rules);

        $schema = $this->training->registration_form_schema ?? [];
        $email = RegistrationService::extractEmailFromFormData($this->fields, $schema);
        $name = RegistrationService::extractNameFromFormData($this->fields, $schema);

        $userId = null;
        $isNewUser = false;

        if ($email) {
            $result = RegistrationService::resolveOrCreateUser($email, $name);
            $userId = $result['user']->id;
            $isNewUser = $result['created'];
        }

        TrainingRegistration::create([
            'training_id' => $this->training->id,
            'user_id' => $userId,
            'form_data' => $this->fields,
            'status' => 'pending',
            'registered_at' => now(),
        ]);

        if ($email && $userId) {
            RegistrationService::sendConfirmation(
                user: \App\Models\User::find($userId),
                registrationType: 'tréning',
                registrationTitle: $this->training->getTranslation('title', app()->getLocale()),
                isNewUser: $isNewUser,
            );
        }

        $this->submitted = true;
    }
};
?>

@php
    $locale = app()->getLocale();
    $schema = $training->registration_form_schema ?? [];
@endphp

<div>
    @if($submitted)
        <div class="bg-[#111111] border border-emerald-500/30 p-10 flex flex-col items-center gap-4 text-center">
            <svg class="w-12 h-12 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            <h3 class="font-display font-bold text-2xl tracking-wide text-white">{{ __('training_detail.form_success_title') }}</h3>
            <p class="text-[#888888] text-base">{{ __('training_detail.form_success_message') }}</p>
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
                                        @include('components.training-registration-field', ['fieldName' => $hfName, 'fieldType' => $hfType, 'placeholder' => $hfPlaceholder, 'options' => $hfOptions, 'isRequired' => $hfRequired, 'inputClass' => $inputClass])
                                        @error('fields.' . $hfName) <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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
                                        @include('components.training-registration-field', ['fieldName' => $hfName, 'fieldType' => $hfType, 'placeholder' => $hfPlaceholder, 'options' => $hfOptions, 'isRequired' => $hfRequired, 'inputClass' => $inputClass])
                                        @error('fields.' . $hfName) <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                @endforeach
                            </div>
                            @php $halfFields = []; @endphp
                        @endif

                        <div class="flex flex-col gap-2">
                            <label class="text-[#888888] text-[13px] font-medium">{{ $label }} @if($isRequired)<span class="text-bcz-red">*</span>@endif</label>
                            @include('components.training-registration-field', ['fieldName' => $fieldName, 'fieldType' => $fieldType, 'placeholder' => $placeholder, 'options' => $options, 'isRequired' => $isRequired, 'inputClass' => $inputClass])
                            @error('fields.' . $fieldName) <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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
                                @include('components.training-registration-field', ['fieldName' => $hfName, 'fieldType' => $hfType, 'placeholder' => $hfPlaceholder, 'options' => $hfOptions, 'isRequired' => $hfRequired, 'inputClass' => $inputClass])
                                @error('fields.' . $hfName) <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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
