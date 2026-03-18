@php
    $wireModel = "fields.{$fieldName}";
    $disabled = $isDisabled ?? false;
    $disabledClasses = $disabled ? ' opacity-50 cursor-not-allowed' : '';
@endphp

@switch($fieldType)
    @case('textarea')
        <textarea wire:model="{{ $wireModel }}" placeholder="{{ $placeholder }}" rows="3" @if($isRequired) required @endif class="{{ $inputClass }}"></textarea>
        @break
    @case('select')
        <select wire:model="{{ $wireModel }}" @if($isRequired) required @endif class="{{ $inputClass }}">
            <option value="">{{ $placeholder ?: '---' }}</option>
            @foreach($options as $opt)
                <option value="{{ $opt }}">{{ $opt }}</option>
            @endforeach
        </select>
        @break
    @case('multi_select')
        <select wire:model="{{ $wireModel }}" multiple @if($isRequired) required @endif class="{{ $inputClass }}">
            @foreach($options as $opt)
                <option value="{{ $opt }}">{{ $opt }}</option>
            @endforeach
        </select>
        @break
    @case('date_picker')
        <input type="date" wire:model="{{ $wireModel }}" @if($isRequired) required @endif class="{{ $inputClass }}">
        @break
    @case('year_picker')
        <input type="number" wire:model="{{ $wireModel }}" min="1900" max="2100" placeholder="{{ $placeholder ?: 'YYYY' }}" @if($isRequired) required @endif class="{{ $inputClass }}">
        @break
    @case('number_input')
        <input type="number" wire:model="{{ $wireModel }}" placeholder="{{ $placeholder }}" @if($isRequired) required @endif class="{{ $inputClass }}">
        @break
    @case('time_picker')
        <input type="time" wire:model="{{ $wireModel }}" @if($isRequired) required @endif class="{{ $inputClass }}">
        @break
    @case('phone')
        <input type="tel" wire:model="{{ $wireModel }}" placeholder="{{ $placeholder ?: '+421 XXX XXX XXX' }}" @if($isRequired) required @endif @if($disabled) disabled @endif class="{{ $inputClass }}{{ $disabledClasses }}">
        @break
    @case('email')
        <input type="email" wire:model="{{ $wireModel }}" placeholder="{{ $placeholder }}" @if($isRequired) required @endif @if($disabled) disabled @endif class="{{ $inputClass }}{{ $disabledClasses }}">
        @break
    @case('first_name')
    @case('last_name')
    @case('full_name')
        <input type="text" wire:model="{{ $wireModel }}" placeholder="{{ $placeholder }}" @if($isRequired) required @endif @if($disabled) disabled @endif class="{{ $inputClass }}{{ $disabledClasses }}">
        @break
    @case('birth_date')
        <input type="date" wire:model="{{ $wireModel }}" @if($isRequired) required @endif @if($disabled) disabled @endif class="{{ $inputClass }}{{ $disabledClasses }}">
        @break
    @case('gender')
        <select wire:model="{{ $wireModel }}" @if($isRequired) required @endif @if($disabled) disabled @endif class="{{ $inputClass }}{{ $disabledClasses }}">
            <option value="">{{ $placeholder ?: '---' }}</option>
            <option value="male">{{ __('enums.' . \App\Enums\GenderEnum::class . '.male') }}</option>
            <option value="female">{{ __('enums.' . \App\Enums\GenderEnum::class . '.female') }}</option>
        </select>
        @break
    @default
        <input type="text" wire:model="{{ $wireModel }}" placeholder="{{ $placeholder }}" @if($isRequired) required @endif class="{{ $inputClass }}">
@endswitch
