@php
    $ctaHref = brick_link(['link_type' => $button_link_type ?? '', 'link_model_id' => $button_link_model_id ?? '', 'link_url' => $button_link_url ?? '']) ?? brick_trans($button_url ?? []);
    $secondaryHref = brick_link(['link_type' => $secondary_link_type ?? '', 'link_model_id' => $secondary_link_model_id ?? '', 'link_url' => $secondary_link_url ?? '']);
    $secondaryText = brick_trans($secondary_text ?? []);
@endphp
<section class="px-8 py-20 text-center" style="background-color: {{ $background_color ?? '#0A0A0A' }};">
    @if(! empty($title))
        <h2 class="font-display font-bold text-[40px] tracking-wide mb-4 text-white">{{ brick_trans($title) }}</h2>
    @endif
    @if(! empty($description))
        <div class="text-[18px] text-[#888888] mb-8 max-w-2xl mx-auto">{!! brick_trans($description) !!}</div>
    @endif
    <div class="flex flex-wrap items-center justify-center gap-4">
        @if(! empty($button_text) && $ctaHref)
            <a href="{{ $ctaHref }}" class="inline-flex items-center gap-2 rounded-lg bg-bcz-red text-white font-semibold text-[15px] px-8 py-4 hover:bg-red-700 transition">
                @if(! empty($button_icon))
                    <x-filament::icon :icon="$button_icon" class="w-[18px] h-[18px]" />
                @endif
                {{ brick_trans($button_text) }}
            </a>
        @endif
        @if($secondaryText && $secondaryHref)
            <a href="{{ $secondaryHref }}" class="inline-flex items-center gap-2 rounded-lg bg-[#111111] border border-[#333333] text-white font-semibold text-[15px] px-8 py-4 hover:bg-[#1a1a1a] transition">
                {{ $secondaryText }}
            </a>
        @endif
    </div>
</section>
