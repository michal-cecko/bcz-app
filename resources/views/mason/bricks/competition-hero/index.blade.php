@php
 $h1 = brick_trans($headline1 ?? ['sk' => 'BOJUJEME']);
 $h2 = brick_trans($headline2 ?? ['sk' => 'ZA VÍŤAZSTVO']);
 $sub = brick_trans($subtitle ?? ['sk' => 'Reprezentujeme Slovensko na medzinárodných súťažiach v parkour freestyle, speed a skill competition.']);
 $badgeText = brick_trans($badge ?? ['sk' => 'SÚŤAŽNÝ TÍM BCZ']);
 $img = $heroImage ?? '';

 $ctaUrl = brick_link(['link_type' => $cta_link_type ?? '', 'link_url' => brick_trans($cta_link_url ?? []), 'link_model_id' => $cta_link_model_id ?? '']);
 $ctaText = brick_trans($cta_text ?? ['sk' => 'NAJBLIŽŠIE SÚŤAŽE']);
 $secondaryCtaUrl = brick_link(['link_type' => $secondary_cta_link_type ?? '', 'link_url' => brick_trans($secondary_cta_link_url ?? []), 'link_model_id' => $secondary_cta_link_model_id ?? '']);
 $secondaryCtaText = brick_trans($secondary_cta_text ?? ['sk' => 'KONTAKTUJTE NÁS']);

 $statsItems = $stats ?? [];
@endphp

<section class="relative w-full min-h-[700px] overflow-hidden">
 <div class="absolute inset-0">
 @if($img)
 <img src="{{ $img }}"alt=""class="w-full h-full object-cover">
 @else
 <div class="w-full h-full bg-[#1A1A1A]"></div>
 @endif
 </div>
 <div class="absolute inset-0 bg-gradient-to-t from-[#0A0A0A] via-[#0A0A0AEE] to-[#0A0A0A99]"></div>

 <div class="relative w-full min-h-[700px] flex flex-col justify-end gap-6 max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 pt-[140px] pb-20">
 @if($badgeText)
 <div class="flex items-center gap-3 px-5 py-2.5 w-fit border border-bcz-red"style="background-color: #FF2D2D20">
 <div class="w-2 h-2 rounded-full bg-bcz-red"></div>
 <span class="text-bcz-red text-xs font-bold tracking-[3px]">{{ mb_strtoupper($badgeText) }}</span>
 </div>
 @endif

 <div class="flex flex-col">
 @if($h1)
 <span class="font-display font-bold text-[50px] md:text-[70px] lg:text-[80px] tracking-wide text-white leading-[0.95]">{{ $h1 }}</span>
 @endif
 @if($h2)
 <span class="font-display font-bold text-[50px] md:text-[70px] lg:text-[80px] tracking-wide text-bcz-red leading-[0.95]">{{ $h2 }}</span>
 @endif
 </div>

 @if($sub)
 <p class="text-[#AAAAAA] text-lg md:text-xl max-w-[650px]">{{ $sub }}</p>
 @endif

 @if($ctaText || $secondaryCtaText)
 <div class="flex items-center gap-5">
 @if($ctaText)
 <a href="{{ $ctaUrl ?: '#upcoming' }}"class="flex items-center gap-3 bg-bcz-red text-white text-sm font-bold tracking-wider px-9 py-[18px] hover:bg-red-700 transition-colors">
 {{ mb_strtoupper($ctaText) }}
 <svg class="w-4 h-4"fill="none"stroke="currentColor"viewBox="0 0 24 24"stroke-width="2"><path d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
 </a>
 @endif
 @if($secondaryCtaText)
 <a href="{{ $secondaryCtaUrl ?: route('kontakt') }}"class="flex items-center gap-3 border-2 border-white text-white text-sm font-bold tracking-wider px-9 py-[18px] hover:bg-white/10 transition-colors">
 {{ mb_strtoupper($secondaryCtaText) }}
 </a>
 @endif
 </div>
 @endif

 @if(!empty($statsItems))
 <div class="flex items-center gap-16 pt-4">
 @foreach($statsItems as $stat)
 @if(!$loop->first)
 <div class="w-px h-10 bg-[#333333]"></div>
 @endif
 <div class="flex flex-col gap-1">
 <span class="text-white font-display font-bold text-3xl">{{ $stat['number'] ?? '' }}</span>
 <span class="text-[#888888] text-xs font-sans tracking-wider">{{ mb_strtoupper(brick_trans($stat['label'] ?? [])) }}</span>
 </div>
 @endforeach
 </div>
 @endif
 </div>
</section>
