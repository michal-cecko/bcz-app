@php
    $bgColor = $bg_color ?? '#111111';
    $iconName = $icon ?? 'bell';
    $titleText = brick_trans($title ?? []);
    $descText = brick_trans($description ?? []);
    $btnText = brick_trans($primary_button_text ?? []);
    $btnUrl = brick_link([
        'link_type' => $primary_button_link_type ?? '',
        'link_model_id' => $primary_button_link_model_id ?? '',
        'link_url' => $primary_button_link_url ?? '',
    ]);
    $noteText = brick_trans($note ?? []);
    $hasStats = !empty($stat1_value ?? null);
    $isLight = in_array(strtolower($bgColor), ['#ffffff', '#fff', '#f5f5f5']);
@endphp

@if($hasStats)
    {{-- Rich card layout (e.g. 2% z dane) --}}
    <div class="flex flex-col gap-4 rounded-2xl p-5" style="width: 280px; background-color: {{ $bgColor }}; box-shadow: 0 16px 48px rgba(0,0,0,0.25)">
        {{-- Header: icon --}}
        <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: #FF2D2D15">
            <svg class="w-6 h-6" style="color: #FF2D2D" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
        </div>

        {{-- Title --}}
        <h3 class="font-['Thunder'] text-xl font-bold tracking-wide {{ $isLight ? 'text-[#0A0A0A]' : 'text-white' }}">{{ $titleText }}</h3>

        {{-- Description --}}
        @if($descText)
            <p class="{{ $isLight ? 'text-[#555555]' : 'text-[#666666]' }} text-[12px] font-['DM_Sans'] leading-relaxed">{{ $descText }}</p>
        @endif

        {{-- Stats row --}}
        <div class="flex gap-3 w-full">
            @if(!empty($stat1_value))
                <div class="flex-1 flex flex-col items-center gap-0.5 {{ $isLight ? 'bg-[#F5F5F5]' : 'bg-white/[0.05]' }} rounded-lg p-3">
                    <span class="font-['Thunder'] text-xl font-bold tracking-wide text-[#FF2D2D]">{{ $stat1_value }}</span>
                    <span class="text-[#888888] text-[10px] font-medium font-['DM_Sans']">{{ brick_trans($stat1_label ?? []) }}</span>
                </div>
            @endif
            @if(!empty($stat2_value))
                <div class="flex-1 flex flex-col items-center gap-0.5 {{ $isLight ? 'bg-[#F5F5F5]' : 'bg-white/[0.05]' }} rounded-lg p-3">
                    <span class="font-['Thunder'] text-xl font-bold tracking-wide text-[#FF2D2D]">{{ $stat2_value }}</span>
                    <span class="text-[#888888] text-[10px] font-medium font-['DM_Sans']">{{ brick_trans($stat2_label ?? []) }}</span>
                </div>
            @endif
        </div>

        {{-- CTA Button --}}
        @if($btnText && $btnUrl)
            <a href="{{ $btnUrl }}" class="flex items-center justify-center gap-2 w-full py-3 bg-[#FF2D2D] text-white text-[11px] font-bold font-['DM_Sans'] tracking-widest rounded-lg hover:bg-[#E02626] transition-colors">
                {{ $btnText }}
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        @endif

        {{-- Note --}}
        @if($noteText)
            <p class="{{ $isLight ? 'text-[#888888]' : 'text-[#999999]' }} text-[9px] font-['DM_Sans'] text-center w-full">{{ $noteText }}</p>
        @endif
    </div>
@else
    {{-- Compact toast layout --}}
    <div class="flex items-center gap-4 rounded-2xl border border-[#222222] p-4 pr-5 w-[480px] max-w-[calc(100vw-2rem)]" style="background-color: {{ $bgColor }}; box-shadow: 0 8px 32px rgba(0,0,0,0.38)">
        {{-- Icon --}}
        <div class="shrink-0 w-10 h-10 rounded-[10px] bg-[#FF2D2D]/[0.08] flex items-center justify-center">
            <i data-lucide="{{ $iconName }}" class="w-[18px] h-[18px] text-[#FF2D2D]"></i>
        </div>

        {{-- Text --}}
        <div class="flex-1 min-w-0">
            <p class="text-white text-[13px] font-semibold font-['DM_Sans'] truncate">{{ $titleText }}</p>
            @if($descText)
                <p class="text-[#666666] text-[11px] font-['DM_Sans'] truncate">{{ $descText }}</p>
            @endif
        </div>

        {{-- Button --}}
        @if($btnText && $btnUrl)
            <a href="{{ $btnUrl }}" class="shrink-0 inline-flex items-center justify-center h-[34px] px-4 bg-[#FF2D2D] text-white text-[11px] font-bold font-['DM_Sans'] rounded-lg hover:bg-[#E02626] transition-colors">
                {{ $btnText }}
            </a>
        @endif
    </div>
@endif
