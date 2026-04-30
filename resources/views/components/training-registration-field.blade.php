@php
    $wireModel = "fields.{$fieldName}";
@endphp

@switch($fieldType)
    @case('textarea')
        <textarea wire:model="{{ $wireModel }}" placeholder="{{ $placeholder }}" rows="3" @if($isRequired) required @endif class="{{ $inputClass }}"></textarea>
        @break
    @case('select')
    @case('category')
        <x-pretty-picker
            :wire-model="$wireModel"
            :options="$options"
            :placeholder="$placeholder ?: '---'"
            :is-required="$isRequired"
            :multiple="false"
        />
        @break
    @case('multi_select')
        <x-pretty-picker
            :wire-model="$wireModel"
            :options="$options"
            :placeholder="$placeholder ?: '---'"
            :is-required="$isRequired"
            :multiple="true"
        />
        @break
    @case('date_picker')
        <input
            type="date"
            wire:model="{{ $wireModel }}"
            @if($isRequired) required @endif
            class="{{ $inputClass }}"
            x-data
            x-init="window.flatpickr && window.flatpickr($el, { dateFormat: 'Y-m-d', altInput: true, altFormat: 'd.m.Y', altInputClass: '{{ $inputClass }}', allowInput: true, disableMobile: false })"
        >
        @break
    @case('year_picker')
        @php
            $minYear = 1900;
            $maxYear = (int) date('Y');
        @endphp
        <select wire:model="{{ $wireModel }}" @if($isRequired) required @endif class="{{ $inputClass }}">
            <option value="">{{ $placeholder ?: 'YYYY' }}</option>
            @for($y = $maxYear; $y >= $minYear; $y--)
                <option value="{{ $y }}">{{ $y }}</option>
            @endfor
        </select>
        @break
    @case('number_input')
        <input type="number" wire:model="{{ $wireModel }}" placeholder="{{ $placeholder }}" @if($isRequired) required @endif class="{{ $inputClass }}">
        @break
    @case('time_picker')
        <input
            type="time"
            wire:model="{{ $wireModel }}"
            @if($isRequired) required @endif
            class="{{ $inputClass }}"
            x-data
            x-init="window.flatpickr && window.flatpickr($el, { enableTime: true, noCalendar: true, dateFormat: 'H:i', time_24hr: true, allowInput: true, disableMobile: false })"
        >
        @break
    @case('phone')
        <input type="tel" wire:model="{{ $wireModel }}" placeholder="{{ $placeholder ?: '+421 XXX XXX XXX' }}" @if($isRequired) required @endif class="{{ $inputClass }}">
        @break
    @case('email')
        <input type="email" wire:model="{{ $wireModel }}" placeholder="{{ $placeholder }}" @if($isRequired) required @endif class="{{ $inputClass }}">
        @break
    @case('first_name')
    @case('last_name')
    @case('full_name')
        <input type="text" wire:model="{{ $wireModel }}" placeholder="{{ $placeholder }}" @if($isRequired) required @endif class="{{ $inputClass }}">
        @break
    @case('birth_date')
        <input
            type="date"
            wire:model="{{ $wireModel }}"
            @if($isRequired) required @endif
            class="{{ $inputClass }}"
            x-data
            x-init="window.flatpickr && window.flatpickr($el, {
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd.m.Y',
                altInputClass: '{{ $inputClass }}',
                allowInput: true,
                disableMobile: false,
                maxDate: 'today',
                defaultDate: $el.value || null,
            })"
        >
        @break
    @case('gender')
        <select wire:model="{{ $wireModel }}" @if($isRequired) required @endif class="{{ $inputClass }}">
            <option value="">{{ $placeholder ?: '---' }}</option>
            <option value="male">{{ __('enums.' . \App\Enums\GenderEnum::class . '.male') }}</option>
            <option value="female">{{ __('enums.' . \App\Enums\GenderEnum::class . '.female') }}</option>
        </select>
        @break
    @default
        <input type="text" wire:model="{{ $wireModel }}" placeholder="{{ $placeholder }}" @if($isRequired) required @endif class="{{ $inputClass }}">
@endswitch
