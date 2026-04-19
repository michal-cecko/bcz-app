<section class="bg-[#111111] py-24">
 <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col lg:flex-row gap-10 lg:gap-20">
 <div class="flex-1 flex flex-col gap-8">
 @if(! empty($label))
 <div class="flex items-center gap-3">
 <div class="w-10 h-0.5 bg-bcz-red"></div>
 <span class="text-bcz-red text-xs font-bold tracking-widest">{{ brick_trans($label) }}</span>
 </div>
 @endif

 @if(! empty($title))
 <h2 class="font-display font-bold text-[32px] md:text-[48px] lg:text-[64px] leading-none tracking-wide">{!! nl2br(e(brick_trans($title))) !!}</h2>
 @endif

 @if(! empty($description))
 <p class="text-bcz-muted text-lg leading-relaxed">{!! brick_trans($description) !!}</p>
 @endif

 @php
 $ctaHref = brick_link(['link_type' => $cta_link_type ?? '', 'link_model_id' => $cta_link_model_id ?? '', 'link_url' => $cta_link_url ?? '']) ?? brick_trans($cta_url ?? []);
 @endphp
 @if(! empty($cta_text) && $ctaHref)
 <a href="{{ $ctaHref }}"class="flex items-center gap-2 text-white text-sm font-bold tracking-widest hover:gap-3 transition-all">
 {{ brick_trans($cta_text) }}
 <svg class="w-4.5 h-4.5"fill="none"stroke="currentColor"viewBox="0 0 24 24"><path stroke-linecap="round"stroke-linejoin="round"stroke-width="2"d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
 </a>
 @endif
 </div>

 <div class="w-full lg:w-[500px] flex flex-col gap-4 lg:shrink-0">
 @php $mainUrl = brick_media_url($image_main ?? null); @endphp
 @if($mainUrl)
 <div class="w-full h-[350px] bg-cover bg-center"style="background-image: url('{{ $mainUrl }}')"></div>
 @endif
 @if(! empty($image_caption))
 <span class="text-[#555555] text-[11px] font-medium tracking-wider">{{ brick_trans($image_caption) }}</span>
 @endif
 @php $leftUrl = brick_media_url($image_left ?? null); $rightUrl = brick_media_url($image_right ?? null); @endphp
 @if($leftUrl || $rightUrl)
 <div class="flex gap-4">
 @if($leftUrl)
 <div class="flex-1 h-[200px] bg-cover bg-center"style="background-image: url('{{ $leftUrl }}')"></div>
 @endif
 @if($rightUrl)
 <div class="flex-1 h-[200px] bg-cover bg-center"style="background-image: url('{{ $rightUrl }}')"></div>
 @endif
 </div>
 @endif
 </div>
 </div>
</section>
