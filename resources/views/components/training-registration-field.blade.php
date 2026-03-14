@php
    $wireModel = "fields.{$fieldName}";
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
        <input type="tel" wire:model="{{ $wireModel }}" placeholder="{{ $placeholder ?: '+421 XXX XXX XXX' }}" @if($isRequired) required @endif class="{{ $inputClass }}">
        @break
    @case('email')
        <input type="email" wire:model="{{ $wireModel }}" placeholder="{{ $placeholder }}" @if($isRequired) required @endif class="{{ $inputClass }}">
        @break
    @default
        <input type="text" wire:model="{{ $wireModel }}" placeholder="{{ $placeholder }}" @if($isRequired) required @endif class="{{ $inputClass }}">
@endswitch
