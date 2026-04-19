@extends('layouts.public')

@section('title', 'Súťaže | BCZ Club')

@php $locale = app()->getLocale(); @endphp

@section('content')
 {{-- Hero Section --}}
 <section class="relative w-full h-[600px] overflow-hidden">
 <div class="absolute inset-0">
 @if($upcoming->first()?->getFirstMediaUrl('detail_image') ?: $upcoming->first()?->getFirstMediaUrl('card_image'))
 <img src="{{ $upcoming->first()->getFirstMediaUrl('detail_image') ?: $upcoming->first()->getFirstMediaUrl('card_image') }}"alt=""class="w-full h-full object-cover">
 @elseif($finished->first()?->getFirstMediaUrl('detail_image') ?: $finished->first()?->getFirstMediaUrl('card_image'))
 <img src="{{ $finished->first()->getFirstMediaUrl('detail_image') ?: $finished->first()->getFirstMediaUrl('card_image') }}"alt=""class="w-full h-full object-cover">
 @else
 <div class="w-full h-full bg-[#1A1A1A]"></div>
 @endif
 </div>
 <div class="absolute inset-0 bg-gradient-to-t from-[#0A0A0A] via-[#0A0A0AEE] to-[#0A0A0A99]"></div>

 <div class="relative w-full h-full flex flex-col justify-end gap-6 max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 pb-20">
 {{-- Badge --}}
 <div class="flex items-center gap-3 px-5 py-2.5 w-fit border border-bcz-red"style="background-color: #FF2D2D20">
 <div class="w-2 h-2 rounded-full bg-bcz-red"></div>
 <span class="text-bcz-red text-xs font-bold tracking-[3px]">SÚŤAŽNÝ TÍM BCZ</span>
 </div>

 {{-- Headline --}}
 <div class="flex flex-col">
 <h1 class="font-display font-bold text-[50px] md:text-[70px] lg:text-[80px] tracking-wide text-white leading-[0.95]">BOJUJEME</h1>
 <span class="font-display font-bold text-[50px] md:text-[70px] lg:text-[80px] tracking-wide text-bcz-red leading-[0.95]">ZA VÍŤAZSTVO</span>
 </div>

 {{-- Subtitle --}}
 <p class="text-[#AAAAAA] text-lg md:text-xl max-w-[650px]">
 Reprezentujeme Slovensko na medzinárodných súťažiach v parkour freestyle, speed a skill competition.
 </p>

 {{-- CTAs --}}
 <div class="flex items-center gap-5">
 <a href="#upcoming"class="flex items-center gap-3 bg-bcz-red text-white text-sm font-bold tracking-wider px-9 py-[18px] hover:bg-red-700 transition-colors">
 NAJBLIŽŠIE SÚŤAŽE
 <svg class="w-4 h-4"fill="none"stroke="currentColor"viewBox="0 0 24 24"stroke-width="2"><path d="M19 9l-7 7-7-7"/></svg>
 </a>
 <a href="{{ route('kontakt') }}"class="flex items-center gap-3 border-2 border-white text-white text-sm font-bold tracking-wider px-9 py-[18px] hover:bg-white/10 transition-colors">
 KONTAKTUJTE NÁS
 </a>
 </div>

 {{-- Stats Bar --}}
 <div class="flex items-center gap-16 pt-4">
 <div class="flex flex-col gap-1">
 <span class="text-white font-display font-bold text-3xl">{{ $upcoming->count() + $finished->count() }}</span>
 <span class="text-[#888888] text-xs font-sans tracking-wider">SÚŤAŽÍ</span>
 </div>
 <div class="w-px h-10 bg-[#333333]"></div>
 <div class="flex flex-col gap-1">
 <span class="text-white font-display font-bold text-3xl">{{ $athletes->count() }}+</span>
 <span class="text-[#888888] text-xs font-sans tracking-wider">ATLÉTOV</span>
 </div>
 <div class="w-px h-10 bg-[#333333]"></div>
 <div class="flex flex-col gap-1">
 <span class="text-white font-display font-bold text-3xl">5+</span>
 <span class="text-[#888888] text-xs font-sans tracking-wider">KRAJÍN</span>
 </div>
 </div>
 </div>
 </section>

 {{-- Upcoming Competitions Section --}}
 <section id="upcoming"class="bg-[#0A0A0A]">
 <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 py-[100px]">
 <div class="flex flex-col gap-16 items-center">
 {{-- Section Header --}}
 <div class="flex flex-col items-center gap-4">
 <div class="flex items-center gap-3">
 <div class="w-10 h-0.5 bg-bcz-red"></div>
 <span class="text-bcz-red text-xs font-bold tracking-[3px]">NADCHÁDZAJÚCE</span>
 <div class="w-10 h-0.5 bg-bcz-red"></div>
 </div>
 <h2 class="font-display font-bold text-[36px] md:text-[48px] tracking-wide text-white text-center">NAJBLIŽŠIE SÚŤAŽE</h2>
 <p class="text-[#666666] text-lg text-center">Sledujte náš kalendár súťaží a príďte nás povzbudiť.</p>
 </div>

 {{-- Competition Cards --}}
 @if($upcoming->isNotEmpty())
 <div class="flex flex-col gap-6 w-full">
 @foreach($upcoming as $competition)
 <a href="{{ route('event.show', $competition) }}"class="bg-[#111111] border border-[#222222] overflow-hidden flex flex-col md:flex-row hover:border-[#333333] transition-colors group">
 {{-- Date Column --}}
 <div class="w-full md:w-[140px] {{ $loop->first ? 'bg-bcz-red' : 'bg-[#1A1A1A]' }} flex flex-col items-center justify-center py-6 md:py-0 shrink-0">
 @if($competition->date)
 <span class="font-display font-bold text-[36px] leading-none text-white">{{ $competition->date->format('d') }}</span>
 <span class="{{ $loop->first ? 'text-white/80' : 'text-[#888888]' }} text-[13px] font-semibold tracking-wider">{{ mb_strtoupper($competition->date->translatedFormat('M Y')) }}</span>
 @endif
 </div>
 {{-- Content --}}
 <div class="flex-1 flex flex-col gap-3 p-6 md:p-8 justify-center">
 <h3 class="font-display font-bold text-[24px] md:text-[28px] tracking-wide text-white">{{ $competition->getTranslation('title', $locale) }}</h3>
 @if($competition->competitionDetail?->disciplines->isNotEmpty())
 <div class="flex flex-wrap gap-2">
 @foreach($competition->competitionDetail->disciplines as $discipline)
 <span class="bg-bcz-red/[0.12] text-bcz-red text-[10px] font-bold tracking-wider px-3 py-1.5">{{ mb_strtoupper($discipline->getTranslation('name', $locale)) }}</span>
 @endforeach
 </div>
 @endif
 @if($competition->city || $competition->country)
 <div class="flex items-center gap-2 text-[#888888] text-sm">
 <svg class="w-4 h-4"fill="none"stroke="currentColor"viewBox="0 0 24 24"stroke-width="2"stroke-linecap="round"stroke-linejoin="round"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12"cy="10"r="3"/></svg>
 <span>{{ collect([$competition->city, $competition->country])->filter()->join(', ') }}</span>
 </div>
 @endif
 </div>
 {{-- CTA --}}
 <div class="flex items-center px-6 pb-6 md:pb-0 md:pr-8 justify-center">
 <span class="{{ $loop->first ? 'bg-bcz-red text-white' : 'border border-[#444444] text-white hover:border-bcz-red' }} text-[12px] font-bold tracking-wider px-6 py-3 transition-colors whitespace-nowrap">DETAIL</span>
 </div>
 </a>
 @endforeach
 </div>
 @else
 <p class="text-[#666666] text-lg">Momentálne nie sú naplánované žiadne súťaže.</p>
 @endif
 </div>
 </div>
 </section>

 {{-- Results Section --}}
 @if($finished->isNotEmpty())
 <section class="bg-[#111111]">
 <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 py-[100px]">
 <div class="flex flex-col gap-16 items-center">
 {{-- Section Header --}}
 <div class="flex flex-col items-center gap-4">
 <div class="flex items-center gap-3">
 <div class="w-10 h-0.5 bg-bcz-red"></div>
 <span class="text-bcz-red text-xs font-bold tracking-[3px]">NAŠE ÚSPECHY</span>
 <div class="w-10 h-0.5 bg-bcz-red"></div>
 </div>
 <h2 class="font-display font-bold text-[36px] md:text-[48px] tracking-wide text-white text-center">VÝSLEDKY ZO SÚŤAŽÍ</h2>
 <p class="text-[#666666] text-lg text-center">Najnovšie umiestnenia a medaily našich atlétov.</p>
 </div>

 {{-- Results Grid --}}
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 w-full">
 @foreach($finished as $competition)
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

 {{-- Athletes Section --}}
 @if($athletes->isNotEmpty())
 <section class="bg-[#0A0A0A]">
 <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 py-[100px]">
 <div class="flex flex-col gap-16 items-center">
 {{-- Section Header --}}
 <div class="flex flex-col items-center gap-4">
 <div class="flex items-center gap-3">
 <div class="w-10 h-0.5 bg-bcz-red"></div>
 <span class="text-bcz-red text-xs font-bold tracking-[3px]">NÁŠ TÍM</span>
 <div class="w-10 h-0.5 bg-bcz-red"></div>
 </div>
 <h2 class="font-display font-bold text-[36px] md:text-[48px] tracking-wide text-white text-center">SÚŤAŽIACI ATLÉTI</h2>
 <p class="text-[#666666] text-lg text-center">Spoznajte našich reprezentantov, ktorí bojujú o medaily na domácich aj medzinárodných súťažiach.</p>
 </div>

 {{-- Athletes Grid --}}
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

 {{-- CTA Section --}}
 <section class="relative w-full h-[400px] overflow-hidden">
 <div class="absolute inset-0 bg-gradient-to-b from-bcz-red to-[#CC0000]"></div>
 <div class="relative w-full h-full flex flex-col items-center justify-center gap-8 max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
 <h2 class="font-display font-bold text-[36px] md:text-[48px] tracking-wide text-white text-center">CHCEŠ SÚŤAŽIŤ S NAMI?</h2>
 <p class="text-white/90 text-lg text-center max-w-[600px]">Pridaj sa k nášmu tímu a reprezentuj Slovensko na medzinárodných súťažiach v parkour a freerunning.</p>
 <div class="flex items-center gap-5">
 <a href="{{ route('kontakt') }}"class="flex items-center gap-3 bg-white text-bcz-red text-sm font-bold tracking-wider px-9 py-[18px] hover:bg-gray-100 transition-colors">
 PRIDAŤ SA DO TÍMU
 <svg class="w-4 h-4"fill="none"stroke="currentColor"viewBox="0 0 24 24"stroke-width="2"><path stroke-linecap="round"stroke-linejoin="round"d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
 </a>
 <a href="{{ route('kontakt') }}"class="flex items-center gap-3 border-2 border-white text-white text-sm font-bold tracking-wider px-9 py-[18px] hover:bg-white/10 transition-colors">
 KONTAKTOVAŤ
 </a>
 </div>
 </div>
 </section>
@endsection
