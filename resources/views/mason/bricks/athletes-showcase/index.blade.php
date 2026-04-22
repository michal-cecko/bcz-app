@php $locale = app()->getLocale(); @endphp

@if(($athletes ?? collect())->isNotEmpty())
<section class="bg-[#0A0A0A] py-[100px] px-10 md:px-20">
 <div class="max-w-[1440px] mx-auto">
 <div class="flex flex-col gap-16 items-center">
 <div class="flex flex-col items-center gap-4">
 @php
 $labelText = brick_trans($label ?? ['sk' => 'NÁŠ TÍM']);
 $heading = brick_trans($title ?? ['sk' => 'SÚŤAŽIACI ATLÉTI']);
 $desc = brick_trans($description ?? ['sk' => 'Spoznajte našich reprezentantov, ktorí bojujú o medaily na domácich aj medzinárodných súťažiach.']);
 @endphp
 @if($labelText)
 <div class="flex items-center gap-3">
 <div class="w-10 h-0.5 bg-bcz-red"></div>
 <span class="text-bcz-red text-xs font-bold tracking-[3px]">{{ mb_strtoupper($labelText) }}</span>
 <div class="w-10 h-0.5 bg-bcz-red"></div>
 </div>
 @endif
 @if($heading)
 <h2 class="font-display font-bold text-[36px] md:text-[48px] tracking-wide text-white text-center">{{ $heading }}</h2>
 @endif
 @if($desc)
 <p class="text-[#666666] text-lg text-center">{{ $desc }}</p>
 @endif
 </div>

 <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 w-full">
 @foreach($athletes as $athlete)
 <a href="{{ route('athlete.show', $athlete->slug) }}"class="bg-[#111111] border border-[#222222] overflow-hidden group hover:border-[#333333] transition-colors flex flex-col">
 <div class="w-full h-[300px] bg-[#1A1A1A] overflow-hidden">
 @if($athlete->getFirstMediaUrl('profile_image'))
 <img src="{{ $athlete->getFirstMediaUrl('profile_image') }}"alt="{{ $athlete->name }}"class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
 @endif
 </div>
 <div class="flex flex-col gap-3 p-6">
 <h3 class="text-white text-base font-bold font-sans">{{ $athlete->name }}</h3>
 <span class="text-[#888888] text-xs font-sans">Atlét BCZ Club</span>
 </div>
 </a>
 @endforeach
 </div>

 <a href="{{ route('athletes.index') }}"class="flex items-center gap-2 border border-[#444444] text-white text-sm font-bold tracking-wider px-8 py-4 hover:border-bcz-red transition-colors">
 VŠETCI ATLÉTI
 <svg class="w-4 h-4"fill="none"stroke="currentColor"viewBox="0 0 24 24"stroke-width="2"><path stroke-linecap="round"stroke-linejoin="round"d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
 </a>
 </div>
 </div>
</section>
@endif
