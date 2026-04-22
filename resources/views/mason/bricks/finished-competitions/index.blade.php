@php
 $locale = app()->getLocale();
 $label = brick_trans($label ?? ['sk' => 'NAŠE ÚSPECHY']);
 $heading = brick_trans($title ?? ['sk' => 'VÝSLEDKY ZO SÚŤAŽÍ']);
 $desc = brick_trans($description ?? ['sk' => 'Najnovšie umiestnenia a medaily našich atlétov.']);
@endphp

@if(($competitions ?? collect())->isNotEmpty())
<section class="bg-[#111111] pb-[100px] px-10 md:px-20">
 <div class="max-w-[1440px] mx-auto">
 <div class="flex flex-col gap-16 items-center">
 <div class="flex flex-col items-center gap-4">
 <div class="flex items-center gap-3">
 <div class="w-10 h-0.5 bg-bcz-red"></div>
 <span class="text-bcz-red text-xs font-bold tracking-[3px]">{{ mb_strtoupper($label) }}</span>
 <div class="w-10 h-0.5 bg-bcz-red"></div>
 </div>
 <h2 class="font-display font-bold text-[36px] md:text-[48px] tracking-wide text-white text-center">{{ $heading }}</h2>
 <p class="text-[#666666] text-lg text-center">{{ $desc }}</p>
 </div>

 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 w-full">
 @foreach($competitions as $competition)
 <a href="{{ route('event.show', $competition) }}"class="bg-[#1A1A1A] border border-[#2A2A2A] overflow-hidden group hover:border-[#333333] transition-colors flex flex-col">
 <div class="relative w-full h-[200px] bg-[#222222] overflow-hidden">
 @if($competition->getFirstMediaUrl('card_image'))
 <img src="{{ $competition->getFirstMediaUrl('card_image') }}"alt="{{ $competition->getTranslation('title', $locale) }}"class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
 @endif
 </div>
 <div class="flex flex-col gap-4 p-6">
 @if($competition->date)
 <span class="text-[#888888] text-[12px] font-medium tracking-wider">{{ mb_strtoupper($competition->date->translatedFormat('F Y')) }}</span>
 @endif
 <h3 class="font-display font-bold text-xl tracking-wide text-white">{{ $competition->getTranslation('title', $locale) }}</h3>
 @if($competition->city || $competition->country)
 <span class="text-bcz-red text-[12px] font-bold">{{ collect([$competition->city, $competition->country])->filter()->join(', ') }}</span>
 @endif
 </div>
 </a>
 @endforeach
 </div>
 </div>
 </div>
</section>
@endif
