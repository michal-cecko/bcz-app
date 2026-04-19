@php
    $bgColor = $bg_color ?? '#FF2D2D';
    $iconName = $icon ?? null;
    $titleText = brick_trans($title ?? []);
    $btnText = brick_trans($primary_button_text ?? []);
    $btnUrl = brick_link([
        'link_type' => $primary_button_link_type ?? '',
        'link_model_id' => $primary_button_link_model_id ?? '',
        'link_url' => $primary_button_link_url ?? '',
    ]);
@endphp

<div class="w-full flex items-center justify-center gap-2 px-5 py-2" style="background-color: {{ $bgColor }}">
    @if($iconName)
        <i data-lucide="{{ $iconName }}" class="w-4 h-4 text-white shrink-0"></i>
    @endif
    <span class="text-white text-[11px] font-['DM_Sans']">{{ $titleText }}</span>
    @if($btnText && $btnUrl)
        <a href="{{ $btnUrl }}" class="inline-flex items-center gap-1 bg-white/[0.12] text-white text-[11px] font-bold font-['DM_Sans'] px-2.5 py-1 hover:bg-white/20 transition-colors">
            {{ $btnText }}
            <i data-lucide="arrow-right" class="w-3 h-3"></i>
        </a>
    @endif
</div>
