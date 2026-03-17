@php
    $locale = app()->getLocale();
    $approvedCount = $training->registrations->where('status', \App\Enums\RegistrationStatusEnum::Approved)->count();
    $isFull = $training->max_capacity !== null && $approvedCount >= $training->max_capacity;
    $title = $training->getTranslation('title', $locale) ?: $training->getTranslation('title', 'sk');
    $remaining = $training->max_capacity ? max(0, $training->max_capacity - $approvedCount) : null;
    $capacityPercent = $training->max_capacity ? min(100, round(($approvedCount / $training->max_capacity) * 100)) : 0;

    $capacityColorHex = match(true) {
        $capacityPercent >= 90 => '#ef4444',
        $capacityPercent >= 65 => '#fb923c',
        default => '#10b981',
    };

    $scheduleText = $training->schedule_days
        ? collect($training->schedule_days)->map(fn ($day) => __('archive.days.'.$day))->implode(', ')
        : null;

    $timeText = null;
    if ($training->start_time) {
        $timeText = \Illuminate\Support\Str::substr($training->start_time, 0, 5);
        if ($training->duration_minutes) {
            $end = \Carbon\Carbon::parse($training->start_time)->addMinutes($training->duration_minutes)->format('H:i');
            $timeText .= ' - ' . $end;
        }
    }
@endphp

<div class="flex h-full flex-col gap-5">

    <div>
        <div class="flex gap-2 items-center mb-2">
            {{-- Age badge --}}
            @if($training->age_range)
                <x-filament::badge color="danger">{{ $training->age_range }} rokov</x-filament::badge>
            @endif

            {{-- Gender --}}
            <x-filament::badge color="info">
                @if($training->gender)
                    {{$training->gender->translation()}}
                @else
                    Obe pohlavia
                @endif
            </x-filament::badge>
        </div>

        {{-- Title --}}
        <h3 class="text-base font-bold uppercase text-gray-900 dark:text-white mb-2">{{ $title }}</h3>
    </div>

    {{-- Detail rows --}}
    <div class="flex flex-col" style="gap: 10px">
        @if($scheduleText)
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500 dark:text-gray-400">Deň</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $scheduleText }}</span>
            </div>
        @endif

        @if($timeText)
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500 dark:text-gray-400">Čas</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $timeText }}</span>
            </div>
        @endif

        @if($training->coaches->isNotEmpty())
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500 dark:text-gray-400">Tréneri</span>
                <span
                    class="text-sm font-semibold text-gray-900 dark:text-white">{{ $training->coaches->pluck('name')->implode(', ') }}</span>
            </div>
        @endif

        @if($training->place_name)
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500 dark:text-gray-400">Miesto</span>
                <span
                    class="text-sm font-semibold text-gray-900 dark:text-white">{{ $training->getTranslation('place_name', $locale) ?: $training->getTranslation('place_name', 'sk') }}</span>
            </div>
        @endif
    </div>

    {{-- Separator --}}
    <div style="height: 1px; background: #d1d5db; margin: 14px 0;"></div>

    {{-- Capacity progress bar --}}
    @if($training->max_capacity)
        <div class="flex flex-col gap-2">
            <div class="flex items-center justify-between">
                <span class="text-[13px] text-gray-500 dark:text-gray-400">Kapacita</span>
                <span class="text-[13px] font-semibold" style="color: {{ $capacityColorHex }}">{{ $remaining }}/{{ $training->max_capacity }} miest</span>
            </div>
            <div style="width: 100%; height: 6px; border-radius: 9999px; background: #d1d5db;">
                <div style="height: 100%; border-radius: 9999px; background: {{ $capacityColorHex }}; width: {{ max($capacityPercent, 2) }}%;"></div>
            </div>
        </div>
    @endif

    {{-- Footer: Price left + Button right --}}
    <div class="flex items-center justify-between gap-3" style="margin-top: 14px;">
        <div>
            @if($training->pricing_type === \App\Enums\TrainingPricingTypeEnum::FREE)
                <span class="text-sm font-bold">Zadarmo</span>
            @elseif($training->pricing_type === \App\Enums\TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED)
                <span class="text-sm font-bold">Vyžaduje členstvo</span>
            @elseif($training->price_amount)
                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ number_format((float) $training->price_amount, 2) }} €</span>
            @endif
        </div>

        @if($isFull)
            <x-filament::button color="gray" size="sm" disabled>
                Plný
            </x-filament::button>
        @else
            <x-filament::button color="danger" size="sm" tag="a" href="{{ $training->getLinkUrl() }}#registracia">
                Registrovať sa
            </x-filament::button>
        @endif
    </div>
</div>
