@php
    $iconName = $icon ?? 'megaphone';
    $accentColor = $bg_color ?? '#FF2D2D';
    $titleText = brick_trans($title ?? []);
    $descText = brick_trans($description ?? []);
    $btnText = brick_trans($primary_button_text ?? []);
    $btnUrl = brick_link([
        'link_type' => $primary_button_link_type ?? '',
        'link_model_id' => $primary_button_link_model_id ?? '',
        'link_url' => $primary_button_link_url ?? '',
    ]);
    $secBtnText = brick_trans($secondary_button_text ?? []);
    $secBtnUrl = brick_link([
        'link_type' => $secondary_button_link_type ?? '',
        'link_model_id' => $secondary_button_link_model_id ?? '',
        'link_url' => $secondary_button_link_url ?? '',
    ]);
    $badgeLabel = brick_trans($badge_text ?? []);
    $noteText = brick_trans($note ?? []);
    $imageUrl = isset($image) && $image ? brick_media_url($image) : null;
@endphp

{{-- Image header --}}
@if($imageUrl)
    <div class="w-full h-[200px] overflow-hidden">
        <img src="{{ $imageUrl }}" alt="" class="w-full h-full object-cover">
    </div>
@endif

<div class="flex flex-col items-center gap-5 {{ $imageUrl ? 'px-8 pt-6 pb-8' : '' }}">
    {{-- Badge --}}
    @if($badgeLabel)
        <div class="inline-flex items-center gap-1.5 px-2.5 py-1" style="background-color: {{ $accentColor }}1F; color: {{ $accentColor }}">
            <i data-lucide="trophy" class="w-3 h-3"></i>
            <span class="text-[10px] font-semibold font-['DM_Sans']">{{ $badgeLabel }}</span>
        </div>
    @endif

    {{-- Icon (only if no image) --}}
    @if(!$imageUrl && !$badgeLabel)
        <div class="w-16 h-16 flex items-center justify-center" style="background-color: {{ $accentColor }}14">
            <i data-lucide="{{ $iconName }}" class="w-7 h-7" style="color: {{ $accentColor }}"></i>
        </div>
    @endif

    {{-- Title --}}
    @if($titleText)
        <h3 class="text-white font-['Thunder'] text-[26px] font-bold text-center">{{ $titleText }}</h3>
    @endif

    {{-- Description --}}
    @if($descText)
        <p class="text-[#888888] text-[13px] font-['DM_Sans'] leading-relaxed text-center w-full">{{ $descText }}</p>
    @endif

    {{-- Divider (simple popup without image) --}}
    @if(!$imageUrl && $btnText)
        <div class="w-full h-px bg-[#222222]"></div>
    @endif

    {{-- Buttons --}}
    @if($btnText && $btnUrl)
        <div class="w-full flex flex-col gap-3">
            <a href="{{ $btnUrl }}" class="flex items-center justify-center gap-2 w-full h-[46px] text-white text-[13px] font-bold font-['DM_Sans'] hover:brightness-90 transition-all cursor-pointer" style="background-color: {{ $accentColor }}">
                @if(!$imageUrl)
                    <i data-lucide="{{ $iconName === 'heart' ? 'heart' : 'arrow-right' }}" class="w-4 h-4"></i>
                @endif
                {{ $btnText }}
            </a>
            @if($secBtnText)
                <a href="{{ $secBtnUrl ?: '#' }}" class="banner-dismiss flex items-center justify-center w-full h-[44px] bg-[#0A0A0A] border border-[#333333] text-[#888888] text-[13px] font-semibold font-['DM_Sans'] hover:text-white transition-colors cursor-pointer">
                    {{ $secBtnText }}
                </a>
            @elseif(!$imageUrl)
                <button class="banner-dismiss text-[#555555] text-xs font-medium font-['DM_Sans'] text-center hover:text-white transition-colors cursor-pointer">
                    {{ __('Neskôr') }}
                </button>
            @endif
        </div>
    @endif

    {{-- Note --}}
    @if($noteText)
        <p class="text-[#555555] text-[10px] font-['DM_Sans'] text-center">{{ $noteText }}</p>
    @endif
</div>
