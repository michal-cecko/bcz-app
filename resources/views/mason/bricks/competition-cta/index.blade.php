@php
 $heading = brick_trans($title ?? []);
 $desc = brick_trans($description ?? []);
 $bgColor = $background_color ?? '#FF2D2D';
 $ctaHref = brick_link(['link_type' => $button_link_type ?? '', 'link_model_id' => $button_link_model_id ?? '', 'link_url' => $button_link_url ?? '']);
 $ctaText = brick_trans($button_text ?? []);
 $secondaryHref = brick_link(['link_type' => $secondary_link_type ?? '', 'link_model_id' => $secondary_link_model_id ?? '', 'link_url' => $secondary_link_url ?? '']);
 $secondaryLabel = brick_trans($secondary_text ?? []);
@endphp

<section class="relative w-full py-24 overflow-hidden">
 <div class="absolute inset-0"style="background: linear-gradient(to bottom, {{ $bgColor }}, {{ $bgColor }}CC)"></div>
 <div class="relative w-full flex flex-col items-center justify-center gap-8 max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
 @if($heading)
 <h2 class="font-display font-bold text-[36px] md:text-[48px] tracking-wide text-white text-center">{{ $heading }}</h2>
 @endif
 @if($desc)
 <div class="text-white/90 text-lg text-center max-w-[600px]">{!! $desc !!}</div>
 @endif
 @if($ctaText || $secondaryLabel)
 <div class="flex flex-wrap items-center justify-center gap-5">
 @if($ctaText)
 <a href="{{ $ctaHref ?: '#' }}"class="inline-flex items-center gap-3 bg-white font-bold tracking-wider px-9 py-[18px] hover:bg-gray-100 transition-colors text-sm"style="color: {{ $bgColor }}">
 @if(! empty($button_icon))
 <x-filament::icon :icon="$button_icon"class="w-[18px] h-[18px]"/>
 @endif
 {{ mb_strtoupper($ctaText) }}
 <svg class="w-4 h-4"fill="none"stroke="currentColor"viewBox="0 0 24 24"stroke-width="2"><path stroke-linecap="round"stroke-linejoin="round"d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
 </a>
 @endif
 @if($secondaryLabel)
 <a href="{{ $secondaryHref ?: '#' }}"class="inline-flex items-center gap-3 border-2 border-white text-white text-sm font-bold tracking-wider px-9 py-[18px] hover:bg-white/10 transition-colors">
 {{ mb_strtoupper($secondaryLabel) }}
 </a>
 @endif
 </div>
 @endif
 </div>
</section>
