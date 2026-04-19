@props(['targetDate', 'label' => null, 'accentColor' => '#FF2D2D'])

<div
    x-data="countdown('{{ $targetDate->toIso8601String() }}')"
    x-init="start()"
    class="bg-[#111111] flex flex-col items-center gap-10 py-20 px-5 w-full"
>
    {{-- Label --}}
    <span class="text-[#888888] text-[12px] font-bold tracking-[2px]">
        {{ $label ?? __('event_detail.countdown_to_registration') }}
    </span>

    {{-- Timer Row --}}
    <div class="flex items-center gap-3 sm:gap-6">
        {{-- Days --}}
        <div class="flex flex-col items-center gap-2 bg-[#1A1A1A] border border-[#222222] rounded-2xl p-4 sm:p-8 w-[80px] sm:w-[140px] lg:w-[160px]">
            <span
                x-text="days.toString().padStart(2, '0')"
                class="font-display font-bold text-[36px] sm:text-[56px] lg:text-[64px] text-white tracking-wide leading-none tabular-nums"
                x-transition
            >00</span>
            <span class="text-[#888888] text-[10px] sm:text-[12px] tracking-[2px]">{{ __('event_detail.countdown_days') }}</span>
        </div>

        <span class="font-display font-bold text-[24px] sm:text-[48px] text-[#333333] tracking-wide leading-none">:</span>

        {{-- Hours --}}
        <div class="flex flex-col items-center gap-2 bg-[#1A1A1A] border border-[#222222] rounded-2xl p-4 sm:p-8 w-[80px] sm:w-[140px] lg:w-[160px]">
            <span
                x-text="hours.toString().padStart(2, '0')"
                class="font-display font-bold text-[36px] sm:text-[56px] lg:text-[64px] text-white tracking-wide leading-none tabular-nums"
            >00</span>
            <span class="text-[#888888] text-[10px] sm:text-[12px] tracking-[2px]">{{ __('event_detail.countdown_hours') }}</span>
        </div>

        <span class="font-display font-bold text-[24px] sm:text-[48px] text-[#333333] tracking-wide leading-none">:</span>

        {{-- Minutes --}}
        <div class="flex flex-col items-center gap-2 bg-[#1A1A1A] border border-[#222222] rounded-2xl p-4 sm:p-8 w-[80px] sm:w-[140px] lg:w-[160px]">
            <span
                x-text="minutes.toString().padStart(2, '0')"
                class="font-display font-bold text-[36px] sm:text-[56px] lg:text-[64px] text-white tracking-wide leading-none tabular-nums"
            >00</span>
            <span class="text-[#888888] text-[10px] sm:text-[12px] tracking-[2px]">{{ __('event_detail.countdown_minutes') }}</span>
        </div>

        <span class="font-display font-bold text-[24px] sm:text-[48px] text-[#333333] tracking-wide leading-none">:</span>

        {{-- Seconds --}}
        <div class="flex flex-col items-center gap-2 bg-[#1A1A1A] border border-[#222222] rounded-2xl p-4 sm:p-8 w-[80px] sm:w-[140px] lg:w-[160px]">
            <span
                x-text="seconds.toString().padStart(2, '0')"
                class="font-display font-bold text-[36px] sm:text-[56px] lg:text-[64px] text-white tracking-wide leading-none tabular-nums"
            >00</span>
            <span class="text-[#888888] text-[10px] sm:text-[12px] tracking-[2px]">{{ __('event_detail.countdown_seconds') }}</span>
        </div>
    </div>

    {{-- Registration date info --}}
    <div class="flex flex-col items-center gap-2">
        <span class="text-[#888888] text-base">{{ __('event_detail.registration_opens_on') }}</span>
        <span class="font-display font-bold text-[24px] tracking-wide" style="color: {{ $accentColor }}">
            {{ $targetDate->translatedFormat('d. F Y, H:i') }}
        </span>
    </div>
</div>
