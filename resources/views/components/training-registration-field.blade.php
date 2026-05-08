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
        <div class="flex flex-col gap-2">
            <input
                type="file"
                wire:model="{{ $wireModel }}"
                @if($isRequired) required @endif
                class="{{ $inputClass }} file:bg-bcz-red file:text-white file:border-0 file:px-4 file:py-2 file:mr-4 file:cursor-pointer file:font-bold file:tracking-wider file:text-xs hover:file:bg-red-700"
            >
            <div wire:loading wire:target="{{ $wireModel }}" class="text-xs text-[#999999]">{{ __('training_detail.file_uploading') }}</div>
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
