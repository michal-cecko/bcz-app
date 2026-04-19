@extends('layouts.public')

@section('title', $event->getTranslation('title', app()->getLocale()) . ' | BCZ Club')

@php
 $locale = app()->getLocale();
 $categoryColor = $event->eventCategory?->color ?? '#FF2D2D';
 $categoryName = $event->eventCategory?->getTranslation('title', $locale) ?? '';
 $title = $event->getTranslation('title', $locale);
 $description = $event->getTranslation('card_description', $locale);
 $heroImage = $event->getFirstMediaUrl('detail_image') ?: $event->getFirstMediaUrl('card_image');
 $galleryImages = $event->getMedia('gallery');
 $detail = $event->competitionDetail;
 $org = $event->organization;
@endphp

@section('content')
 @unless($event->is_published)
     <x-preview-banner />
 @endunless
 {{-- Hero Section --}}
 <section class="relative w-full h-[500px] overflow-hidden">
 @if($heroImage)
 <div class="absolute inset-0">
 <img src="{{ $heroImage }}"alt="{{ $title }}"class="w-full h-full object-cover">
 </div>
 @else
 <div class="absolute inset-0 bg-[#1A1A1A]"></div>
 @endif
 <div class="absolute inset-0 bg-gradient-to-b from-[#0A0A0A]/0 to-[#0A0A0A]"></div>

 <div class="relative w-full h-full flex flex-col justify-end gap-4 max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 pb-[60px]">
 {{-- Breadcrumb --}}
 <div class="flex items-center gap-2">
 <a href="{{ route('home') }}"class="text-[#AAAAAA] text-sm font-sans hover:text-white transition-colors">{{ __('event_detail.home') }}</a>
 <span class="text-[#555555] text-sm">/</span>
 <a href="{{ route('sutaze') }}"class="text-[#AAAAAA] text-sm font-sans hover:text-white transition-colors">{{ __('event_detail.competitions') }}</a>
 <span class="text-[#555555] text-sm">/</span>
 <span class="text-sm font-semibold font-sans"style="color: {{ $categoryColor }}">{{ $title }}</span>
 </div>

 {{-- Status + Date --}}
 <div class="flex items-center gap-4">
 <div class="flex items-center gap-2 px-3.5 py-1.5"style="background-color: {{ $categoryColor }}">
 <span class="text-white text-xs font-bold tracking-wider">{{ __('event_detail.status_finished') }}</span>
 </div>
 @if($event->date)
 <span class="text-white text-sm font-semibold font-sans">
 {{ $event->date->translatedFormat('d. F Y') }}
 @if($event->date_end && !$event->date->isSameDay($event->date_end))
 — {{ $event->date_end->translatedFormat('d. F Y') }}
 @endif
 </span>
 @endif
 </div>

 {{-- Title --}}
 <h1 class="font-display font-bold text-[36px] md:text-[48px] lg:text-[56px] tracking-wide text-white leading-tight">
 {{ $title }}
 </h1>

 {{-- Location + Category --}}
 <div class="flex items-center gap-4">
 @if($event->city || $event->country)
 <div class="flex items-center gap-2 text-[#CCCCCC] text-sm">
 <svg class="w-4 h-4"style="color: {{ $categoryColor }}"fill="none"stroke="currentColor"viewBox="0 0 24 24"stroke-width="2"stroke-linecap="round"stroke-linejoin="round"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12"cy="10"r="3"/></svg>
 <span>{{ collect([$event->place_name, $event->city, $event->country])->filter()->join(', ') }}</span>
 </div>
 @endif
 @if($categoryName)
 <span class="text-sm font-sans px-2.5 py-0.5"style="color: {{ $categoryColor }}; background-color: {{ $categoryColor }}15">{{ $categoryName }}</span>
 @endif
 </div>
 </div>
 </section>

 {{-- Section Nav (sticky below header, scroll-aware) --}}
 <nav x-data="{
     active: 'report',
     sections: ['report', 'o-sutazi', @if($detail?->rounds->isNotEmpty()) 'vysledky', @endif @if($detail?->timetableEntries->isNotEmpty()) 'harmonogram', @endif @if($galleryImages->isNotEmpty()) 'galeria', @endif],
     scrollTo(id) {
         this.active = id;
         const el = document.getElementById(id);
         if (el) {
             const navHeight = this.$el.offsetHeight + (window.innerWidth >= 1024 ? 80 : 64);
             const top = el.getBoundingClientRect().top + window.scrollY - navHeight;
             window.scrollTo({ top, behavior: 'smooth' });
         }
     },
     init() {
         const observer = new IntersectionObserver((entries) => {
             entries.forEach(entry => {
                 if (entry.isIntersecting) {
                     this.active = entry.target.id;
                 }
             });
         }, { rootMargin: '-30% 0px -60% 0px' });
         this.sections.forEach(id => {
             const el = document.getElementById(id);
             if (el) observer.observe(el);
         });
     }
 }" class="bg-[#111111]/95 backdrop-blur-sm border-b border-[#222222] sticky top-16 lg:top-20 z-30">
 <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
 <div class="flex items-center overflow-x-auto">
 <a @click.prevent="scrollTo('report')" href="#report"
    class="px-6 py-4 text-sm font-semibold font-sans whitespace-nowrap transition-colors border-b-2 cursor-pointer"
    :class="active === 'report' ? 'text-white' : 'text-[#888888] border-transparent hover:text-white'"
    :style="active === 'report' ? 'border-color: {{ $categoryColor }}' : ''">{{ __('event_detail.post_competition_report') }}</a>
 <a @click.prevent="scrollTo('o-sutazi')" href="#o-sutazi"
    class="px-6 py-4 text-sm font-semibold font-sans whitespace-nowrap transition-colors border-b-2 cursor-pointer"
    :class="active === 'o-sutazi' ? 'text-white' : 'text-[#888888] border-transparent hover:text-white'"
    :style="active === 'o-sutazi' ? 'border-color: {{ $categoryColor }}' : ''">{{ __('event_detail.about_competition') }}</a>
 @if($detail?->rounds->isNotEmpty())
 <a @click.prevent="scrollTo('vysledky')" href="#vysledky"
    class="px-6 py-4 text-sm font-semibold font-sans whitespace-nowrap transition-colors border-b-2 cursor-pointer"
    :class="active === 'vysledky' ? 'text-white' : 'text-[#888888] border-transparent hover:text-white'"
    :style="active === 'vysledky' ? 'border-color: {{ $categoryColor }}' : ''">{{ __('event_detail.results') }}</a>
 @endif
 @if($detail?->timetableEntries->isNotEmpty())
 <a @click.prevent="scrollTo('harmonogram')" href="#harmonogram"
    class="px-6 py-4 text-sm font-semibold font-sans whitespace-nowrap transition-colors border-b-2 cursor-pointer"
    :class="active === 'harmonogram' ? 'text-white' : 'text-[#888888] border-transparent hover:text-white'"
    :style="active === 'harmonogram' ? 'border-color: {{ $categoryColor }}' : ''">{{ __('event_detail.timetable') }}</a>
 @endif
 @if($galleryImages->isNotEmpty())
 <a @click.prevent="scrollTo('galeria')" href="#galeria"
    class="px-6 py-4 text-sm font-semibold font-sans whitespace-nowrap transition-colors border-b-2 cursor-pointer"
    :class="active === 'galeria' ? 'text-white' : 'text-[#888888] border-transparent hover:text-white'"
    :style="active === 'galeria' ? 'border-color: {{ $categoryColor }}' : ''">{{ __('event_detail.gallery') }}</a>
 @endif
 </div>
 </div>
 </nav>

 {{-- Posúťažný report --}}
 <section id="report" class="bg-[#0A0A0A]">
 <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 py-[60px]">
 <div class="flex flex-col lg:flex-row gap-16">
 <div class="flex-1 min-w-0 flex flex-col gap-4">
 {{-- Posúťažný report --}}
 <div class="flex items-center gap-3 mb-4">
 <div class="w-6 h-0.5" style="background-color: {{ $categoryColor }}"></div>
 <span class="text-xs font-bold tracking-[2px]" style="color: {{ $categoryColor }}">{{ mb_strtoupper(__('event_detail.post_competition_report')) }}</span>
 </div>

 @if($renderedReportContent ?? '')
 {!! $renderedReportContent !!}
 @else
 <p class="text-[#666666] text-base font-sans">{{ __('event_detail.report_not_published') }}</p>
 @endif

 {{-- O súťaži --}}
 <div id="o-sutazi" class="pt-10 mt-4 border-t border-[#222222] flex flex-col gap-12">
 <div class="flex items-center gap-3">
 <div class="w-6 h-0.5" style="background-color: {{ $categoryColor }}"></div>
 <span class="text-xs font-bold tracking-[2px]" style="color: {{ $categoryColor }}">{{ mb_strtoupper(__('event_detail.about_competition')) }}</span>
 </div>

 @if($renderedContent)
 {!! $renderedContent !!}
 @elseif($description)
 <p class="text-[#CCCCCC] text-base leading-relaxed font-sans">{{ $description }}</p>
 @endif
 </div>
 </div>

 {{-- Sidebar --}}
 <div class="w-full lg:w-[320px] shrink-0 flex flex-col gap-6 lg:sticky lg:top-24 lg:self-start">
 <div class="bg-[#111111] p-6 flex flex-col gap-5"style="border: 1px solid {{ $categoryColor }}30">
 <h3 class="text-white text-lg font-bold font-sans">{{ __('event_detail.competition_info') }}</h3>
 <div class="w-full h-px"style="background-color: {{ $categoryColor }}20"></div>

 @if($categoryName)
 <div class="flex items-center justify-between gap-4">
 <span class="text-[#888888] text-sm font-sans">{{ __('event_detail.type') }}</span>
 <span class="text-sm font-sans text-right"style="color: {{ $categoryColor }}">{{ $categoryName }}</span>
 </div>
 @endif

 @if($event->city || $event->country)
 <div class="flex items-center justify-between gap-4">
 <span class="text-[#888888] text-sm font-sans">{{ __('event_detail.location') }}</span>
 <span class="text-white text-sm font-sans text-right">{{ collect([$event->place_name, $event->city, $event->country])->filter()->join(', ') }}</span>
 </div>
 @endif

 @if($event->date)
 <div class="flex items-center justify-between gap-4">
 <span class="text-[#888888] text-sm font-sans">{{ __('event_detail.date') }}</span>
 <span class="text-white text-sm font-sans text-right">
 {{ $event->date->translatedFormat('d. F Y') }}
 @if($event->date_end && !$event->date->isSameDay($event->date_end))
 — {{ $event->date_end->translatedFormat('d. F Y') }}
 @endif
 </span>
 </div>
 @endif

 @if($event->registrations->count() > 0)
 <div class="flex items-center justify-between gap-4">
 <span class="text-[#888888] text-sm font-sans">{{ __('event_detail.participants') }}</span>
 <span class="text-white text-sm font-bold font-sans text-right">{{ $event->registrations->count() }}</span>
 </div>
 @endif

 @if($detail?->disciplines->isNotEmpty())
 <div class="flex items-center justify-between gap-4">
 <span class="text-[#888888] text-sm font-sans">{{ __('event_detail.disciplines') }}</span>
 <span class="text-white text-sm font-sans text-right">{{ $detail->disciplines->map(fn ($d) => $d->getTranslation('name', $locale))->join(', ') }}</span>
 </div>
 @endif

 @if($detail?->athleteCategories->isNotEmpty())
 <div class="flex items-center justify-between gap-4">
 <span class="text-[#888888] text-sm font-sans">{{ __('event_detail.categories') }}</span>
 <span class="text-white text-sm font-sans text-right">{{ $detail->athleteCategories->map(fn ($c) => $c->getTranslation('name', $locale))->join(', ') }}</span>
 </div>
 @endif
 </div>

 @include('pages.events._share')
 </div>
 </div>
 </div>
 </div>
 </section>

 {{-- Výsledky --}}
 @if($detail?->rounds->isNotEmpty())
 <section id="vysledky" class="bg-[#0D0D0D]">
 <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 py-[60px]">
 <div class="flex flex-col gap-4 mb-8">
 <div class="flex items-center gap-2">
 <div class="w-6 h-0.5" style="background-color: {{ $categoryColor }}"></div>
 <span class="text-xs font-bold tracking-[2px]" style="color: {{ $categoryColor }}">{{ mb_strtoupper(__('event_detail.results')) }}</span>
 </div>
 </div>
 <div x-data="{ resultsTab: 'qualification' }">
 @include('pages.events._results-tab')
 </div>
 </div>
 </section>
 @endif

 {{-- Harmonogram --}}
 @if($detail?->timetableEntries->isNotEmpty())
 <section id="harmonogram"class="bg-[#0A0A0A]">
 <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 py-[60px]">
 <div class="flex flex-col gap-4 mb-8">
 <div class="flex items-center gap-2">
 <div class="w-6 h-0.5"style="background-color: {{ $categoryColor }}"></div>
 <span class="text-xs font-bold tracking-[2px]"style="color: {{ $categoryColor }}">{{ mb_strtoupper(__('event_detail.timetable')) }}</span>
 </div>
 <h2 class="font-display font-bold text-[36px] tracking-wide text-white">{{ __('event_detail.competition_flow') }}</h2>
 </div>

 <div class="flex flex-col">
 @foreach($detail->timetableEntries as $entry)
 <div class="flex items-center gap-6 py-4 {{ !$loop->last ? 'border-b border-[#1A1A1A]' : '' }}">
 <span class="text-sm font-bold min-w-[60px] font-sans text-[#666666]">{{ $entry->scheduled_time?->format('H:i') }}</span>
 <span class="text-[#AAAAAA] text-sm font-sans">{{ $entry->getTranslation('title', $locale) }}</span>
 <span class="text-xs text-[#444444] ml-auto">{{ __('event_detail.completed') }}</span>
 </div>
 @endforeach
 </div>
 </div>
 </section>
 @endif

 {{-- Gallery --}}
 @if($galleryImages->isNotEmpty())
 <section id="galeria" class="bg-[#0D0D0D]">
 <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 py-[60px]">
 <div class="flex flex-col gap-8">
 <div class="flex items-end justify-between">
 <div class="flex flex-col gap-3">
 <div class="flex items-center gap-2">
 <div class="w-6 h-0.5" style="background-color: {{ $categoryColor }}"></div>
 <span class="text-xs font-bold tracking-[2px]" style="color: {{ $categoryColor }}">{{ __('event_detail.gallery') }}</span>
 </div>
 <h2 class="font-display font-bold text-[36px] tracking-wide text-white">{{ __('event_detail.photos_from_competition') }}</h2>
 </div>
 </div>
 <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
 @foreach($galleryImages as $image)
 <div class="overflow-hidden bg-[#1A1A1A] aspect-[4/3]">
 <img src="{{ $image->getUrl() }}" alt="{{ $image->name }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
 </div>
 @endforeach
 </div>
 </div>
 </div>
 </section>
 @endif

 {{-- More Competitions --}}
 @if($moreEvents->isNotEmpty())
 <section class="bg-[#0A0A0A]">
 <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 py-[60px]">
 <div class="flex flex-col gap-8">
 <div class="flex items-end justify-between">
 <div class="flex flex-col gap-3">
 <div class="flex items-center gap-2">
 <div class="w-6 h-0.5"style="background-color: {{ $categoryColor }}"></div>
 <span class="text-xs font-bold tracking-[2px]"style="color: {{ $categoryColor }}">{{ __('event_detail.more_competitions_label') }}</span>
 </div>
 <h2 class="font-display font-bold text-[36px] tracking-wide text-white">{{ __('event_detail.more_competitions_title') }}</h2>
 </div>
 <a href="{{ route('sutaze') }}"class="flex items-center gap-2 border px-6 py-3 text-sm font-semibold transition-colors hover:border-current"style="color: {{ $categoryColor }}; border-color: {{ $categoryColor }}">
 {{ __('event_detail.all_competitions') }}
 <span>&#8594;</span>
 </a>
 </div>

 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
 @foreach($moreEvents as $related)
 @php $relColor = $related->eventCategory->color ?? $categoryColor; @endphp
 <a href="{{ route('event.show', $related) }}"class="bg-[#111111] overflow-hidden group hover:border-[#333333] transition-colors flex flex-col">
 <div class="relative w-full h-[180px] bg-[#1A1A1A] overflow-hidden">
 @if($related->getFirstMediaUrl('card_image'))
 <img src="{{ $related->getFirstMediaUrl('card_image') }}"alt="{{ $related->getTranslation('title', $locale) }}"class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
 @endif
 </div>
 <div class="flex flex-col gap-3 p-5">
 <div class="flex items-center justify-between">
 <span class="text-[11px] font-bold tracking-wider px-2.5 py-1"style="color: {{ $relColor }}; background-color: {{ $relColor }}20">
 {{ $related->status === 'finished' ? __('event_detail.status_finished') : __('event_detail.status_upcoming') }}
 </span>
 @if($related->date)
 <span class="text-[#666666] text-xs font-sans">{{ $related->date->format('Y') }}</span>
 @endif
 </div>
 <h3 class="text-white text-lg font-bold font-sans">{{ $related->getTranslation('title', $locale) }}</h3>
 @if($related->city)
 <div class="flex items-center gap-1.5 text-[#666666] text-xs font-sans">
 <svg class="w-3.5 h-3.5 shrink-0"fill="none"stroke="currentColor"viewBox="0 0 24 24"stroke-width="2"stroke-linecap="round"stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12"cy="10"r="3"/></svg>
 {{ collect([$related->city, $related->country])->filter()->join(', ') }}
 </div>
 @endif
 </div>
 </a>
 @endforeach
 </div>
 </div>
 </div>
 </section>
 @endif
@endsection
