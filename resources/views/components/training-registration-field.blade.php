@php
    $wireModel = "fields.{$fieldName}";
@endphp

@switch($fieldType)
    @case('textarea')
        <textarea wire:model="{{ $wireModel }}" placeholder="{{ $placeholder }}" rows="3" @if($isRequired) required @endif class="{{ $inputClass }}"></textarea>
        @break
    @case('checkbox')
        <label class="flex items-start gap-3 cursor-pointer select-none">
            <input
                type="checkbox"
                wire:model="{{ $wireModel }}"
                @if($isRequired) required @endif
                class="mt-0.5 w-4 h-4 rounded-none border-[#333333] bg-bcz-dark text-bcz-red focus:ring-bcz-red focus:ring-offset-0 shrink-0 cursor-pointer"
            >
            @if(trim((string) $placeholder) !== '')
                <span class="text-[#888888] text-[13px] leading-[1.6]">{{ $placeholder }}</span>
            @endif
        </label>
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
            $yearOptions = [];
            for ($y = $maxYear; $y >= $minYear; $y--) {
                $yearOptions[(string) $y] = (string) $y;
            }
        @endphp
        <x-pretty-picker
            :wire-model="$wireModel"
            :options="$yearOptions"
            :placeholder="$placeholder ?: 'YYYY'"
            :is-required="$isRequired"
            :multiple="false"
        />
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
    @case('file_input')
        <div
            x-data="bczFilepond({
                statePath: @js($wireModel),
                accept: null,
                maxSizeMb: 10,
                labelIdle: @js($placeholder ?: __('training_detail.file_upload_idle')),
            })"
            x-init="init()"
            x-on:livewire:navigating.document.window="destroy()"
            wire:ignore
        >
            <input type="file" @if($isRequired) required @endif>
        </div>
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
        @php
            $genderOptions = [
                'male' => __('enums.'.\App\Enums\GenderEnum::class.'.male'),
                'female' => __('enums.'.\App\Enums\GenderEnum::class.'.female'),
            ];
        @endphp
        <x-pretty-picker
            :wire-model="$wireModel"
            :options="$genderOptions"
            :placeholder="$placeholder ?: '---'"
            :is-required="$isRequired"
            :multiple="false"
        />
        @break
    @default
        <input type="text" wire:model="{{ $wireModel }}" placeholder="{{ $placeholder }}" @if($isRequired) required @endif class="{{ $inputClass }}">
@endswitch
