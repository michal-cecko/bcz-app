@extends('layouts.public')

@section('title', $event->getTranslation('title', app()->getLocale()) . ' | BCZ Club')

@php
 $locale = app()->getLocale();
 $categoryColor = $event->eventCategory?->color ?? '#9B5DE5';
 $categoryName = $event->eventCategory?->getTranslation('title', $locale) ?? '';
 $title = $event->getTranslation('title', $locale);
 $description = $event->getTranslation('card_description', $locale);
 $heroImage = $event->getFirstMediaUrl('detail_image') ?: $event->getFirstMediaUrl('card_image');
 $galleryImages = $event->getMedia('gallery');
 $org = $event->organization;
 $registrationCount = $event->registrations->count();
 $hasRegistration = $org && in_array($event->status, ['registering', 'countdown', 'upcoming']);
@endphp

@section('meta_description', seo_description($description))
@if ($heroImage)
    @section('og_image', $heroImage)
@endif
@section('og_type', 'article')

@include('partials.event-schema')

@section('content')
 @unless($event->is_published)
     <x-preview-banner />
 @endunless

 {{-- Hero Section --}}
 <section class="relative w-full h-[500px] overflow-hidden">
 @if($heroImage)
 <div class="absolute inset-0">
 <img src="{{ $heroImage }}" alt="{{ $title }}" class="w-full h-full object-cover">
 </div>
 @else
 <div class="absolute inset-0 bg-[#1A1A1A]"></div>
 @endif
 <div class="absolute inset-0 bg-gradient-to-b from-[#0A0A0A]/0 to-[#0A0A0A]"></div>

 <div class="relative w-full h-full flex flex-col justify-end gap-4 max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 pb-[60px]">
 {{-- Breadcrumb --}}
 <div class="flex items-center gap-2">
 <a href="{{ route('home') }}" class="text-[#888888] text-sm font-normal font-sans hover:text-white transition-colors">{{ __('event_detail.home') }}</a>
 <span class="text-[#444444] text-sm">/</span>
 <a href="{{ route('eventy') }}" class="text-[#888888] text-sm font-normal font-sans hover:text-white transition-colors">{{ __('event_detail.events') }}</a>
 <span class="text-[#444444] text-sm">/</span>
 <span class="text-sm font-normal font-sans" style="color: {{ $categoryColor }}">{{ $title }}</span>
 </div>

 {{-- Category Badge --}}
 @if($categoryName)
 <div class="w-fit px-3.5 py-1.5" style="background-color: {{ $categoryColor }}">
 <span class="text-white text-xs font-bold tracking-wide">{{ mb_strtoupper($categoryName) }}</span>
 </div>
 @endif

 {{-- Title --}}
 <h1 class="font-display font-bold text-[36px] md:text-[48px] lg:text-[56px] tracking-wide text-white leading-tight">
 {{ $title }}
 </h1>
 </div>
 </section>

 {{-- Info Strip --}}
 <section class="bg-[#0A0A0A]">
 <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 pt-[40px]">
 <div class="flex flex-wrap gap-4">
 @if($event->date)
 <div class="flex-1 min-w-[160px] bg-[#111111] border border-[#222222] rounded-lg px-4 py-3 flex flex-col gap-1">
 <span class="text-[#666666] text-[11px] tracking-wider font-sans">{{ mb_strtoupper(__('event_detail.date')) }}</span>
 <span class="text-white text-[15px] font-semibold font-sans">
 {{ $event->date->translatedFormat('d. F Y') }}
 @if($event->date_end && !$event->date->isSameDay($event->date_end))
 — {{ $event->date_end->translatedFormat('d. F Y') }}
 @endif
 </span>
 </div>
 @endif

 @if($event->city || $event->place_name)
 <div class="flex-1 min-w-[160px] bg-[#111111] border border-[#222222] rounded-lg px-4 py-3 flex flex-col gap-1">
 <span class="text-[#666666] text-[11px] tracking-wider font-sans">{{ mb_strtoupper(__('event_detail.place')) }}</span>
 <span class="text-white text-[15px] font-semibold font-sans">{{ collect([$event->place_name, $event->city])->filter()->join(', ') }}</span>
 </div>
 @endif

 @if($org)
 <div class="flex-1 min-w-[160px] bg-[#111111] border border-[#222222] rounded-lg px-4 py-3 flex flex-col gap-1">
 <span class="text-[#666666] text-[11px] tracking-wider font-sans">{{ mb_strtoupper(__('event_detail.registration')) }}</span>
 @if($event->status === 'registering' && $org->registration_closes_at && $org->registration_closes_at->isFuture())
 <div x-data="countdown('{{ $org->registration_closes_at->toIso8601String() }}')" x-init="start()" class="flex items-center gap-1.5">
     <span class="text-green-500 text-[15px] font-semibold font-sans">{{ __('event_detail.open') }}</span>
     <span class="text-[#888888] text-[13px]" x-text="'· ' + (days > 0 ? days + 'd ' : '') + hours.toString().padStart(2,'0') + 'h ' + minutes.toString().padStart(2,'0') + 'm'"></span>
 </div>
 @elseif($event->status === 'registering')
 <span class="text-green-500 text-[15px] font-semibold font-sans">{{ __('event_detail.open') }}</span>
 @elseif(in_array($event->status, ['upcoming', 'countdown']))
 <span style="color: {{ $categoryColor }}" class="text-[15px] font-semibold font-sans">{{ __('event_detail.soon') }}</span>
 @else
 <span class="text-[#666666] text-[15px] font-semibold font-sans">{{ __('event_detail.closed') }}</span>
 @endif
 </div>
 @endif

 @if($org && $org->max_capacity)
 <div class="flex-1 min-w-[160px] bg-[#111111] border border-[#222222] rounded-lg px-4 py-3 flex flex-col gap-1">
 <span class="text-[#666666] text-[11px] tracking-wider font-sans">{{ mb_strtoupper(__('event_detail.available_spots')) }}</span>
 <div class="flex items-center gap-2">
 <span style="color: {{ $categoryColor }}" class="text-[15px] font-bold font-sans">{{ max(0, $org->max_capacity - $registrationCount) }}</span>
 </div>
 </div>
 @endif
 </div>
 </div>
 </section>

 {{-- Tab Bar + Content --}}
 <section class="bg-[#0A0A0A]" x-data="{ tab: 'popis' }">
 <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 pt-[40px] pb-[60px]">

 {{-- Tab Bar --}}
 <div class="flex items-center gap-0 border-b border-[#222222] mb-10 overflow-x-auto">
 @php
     $tabs = ['popis' => __('event_detail.tab_description')];
     if ($hasRegistration) {
         $tabs['registracia'] = __('event_detail.tab_registration');
     }
 @endphp
 @foreach($tabs as $key => $label)
 <button
 x-on:click="tab = '{{ $key }}'"
 :class="tab === '{{ $key }}' ? 'border-b-[3px] text-white' : 'border-b-[3px] border-transparent text-[#888888] hover:text-white'"
 class="px-6 py-4 text-sm font-bold font-sans whitespace-nowrap transition-colors cursor-pointer"
 :style="tab === '{{ $key }}' ? 'border-color: {{ $categoryColor }}' : ''"
 >
 {!! $label !!}
 </button>
 @endforeach
 </div>

 {{-- Tab: Popis --}}
 <div x-show="tab === 'popis'">
 <div class="flex flex-col lg:flex-row gap-16">
 {{-- Main Content --}}
 <div class="flex-1 min-w-0 flex flex-col gap-4">
 @if($renderedContent)
 {!! $renderedContent !!}
 @elseif($description)
 <div class="flex flex-col gap-6">
 <h2 class="font-display font-bold text-[32px] tracking-wide text-white">{{ __('event_detail.about_organized') }}</h2>
 <p class="text-[#CCCCCC] text-base leading-relaxed font-sans">{{ $description }}</p>
 </div>
 @endif
 </div>

 {{-- Sidebar --}}
 <div class="w-full lg:w-[320px] shrink-0 flex flex-col gap-6 lg:sticky lg:top-24 lg:self-start">
 {{-- Event Info Card --}}
 <div class="bg-[#111111] border border-[#222222] rounded-xl p-6 flex flex-col gap-5">
 <h3 class="text-white text-lg font-bold font-sans">{{ __('event_detail.organized_event_info') }}</h3>
 <div class="w-full h-px bg-[#222222]"></div>

 @if($categoryName)
 <div class="flex items-center justify-between">
 <span class="text-[#888888] text-sm font-sans">{{ __('event_detail.type') }}</span>
 <span class="text-sm font-sans text-right" style="color: {{ $categoryColor }}">{{ $categoryName }}</span>
 </div>
 @endif

 @if($event->team)
 <div class="flex items-center justify-between">
 <span class="text-[#888888] text-sm font-sans">{{ __('event_detail.organizer') }}</span>
 <span class="text-white text-sm font-sans text-right">{{ $event->team->name }}</span>
 </div>
 @endif

 @if($event->city || $event->country)
 <div class="flex items-center justify-between">
 <span class="text-[#888888] text-sm font-sans">{{ __('event_detail.location') }}</span>
 <span class="text-white text-sm font-sans text-right">{{ collect([$event->place_name, $event->city, $event->country])->filter()->join(', ') }}</span>
 </div>
 @endif

 @if($event->date)
 <div class="flex items-center justify-between">
 <span class="text-[#888888] text-sm font-sans">{{ __('event_detail.date') }}</span>
 <span class="text-white text-sm font-sans text-right">
 {{ $event->date->translatedFormat('d. F Y') }}
 @if($event->date_end && !$event->date->isSameDay($event->date_end))
 — {{ $event->date_end->translatedFormat('d. F Y') }}
 @endif
 </span>
 </div>
 @endif

 @if($org)
 <div class="flex items-center justify-between">
 <span class="text-[#888888] text-sm font-sans">{{ __('event_detail.price') }}</span>
 <span class="text-white text-sm font-semibold font-sans text-right">
 @if($org->pricing_type->value === 'paid')
 {{ number_format($org->price_amount, 2) }} {{ $org->price_currency }}
 @else
 {{ __('event_detail.free') }}
 @endif
 </span>
 </div>
 @endif
 </div>

 {{-- CTA Card --}}
 <div class="p-6 flex flex-col gap-4 rounded-xl" style="background-color: {{ $categoryColor }}10; border: 1px solid {{ $categoryColor }}40">
 <h3 class="text-white text-base font-bold font-sans">{{ __('event_detail.cta_organized') }}</h3>
 <p class="text-[#AAAAAA] text-sm font-sans leading-relaxed">{{ __('event_detail.cta_description') }}</p>
 <a href="{{ route('kontakt') }}" class="flex items-center justify-center gap-2 text-white text-sm font-semibold font-sans py-3 w-full" style="background-color: {{ $categoryColor }}">
 {{ __('event_detail.contact') }}
 <span>&#8594;</span>
 </a>
 </div>

 @include('pages.events._share')
 </div>
 </div>
 </div>

 {{-- Tab: Registrácia --}}
 @if($hasRegistration)
 <div x-show="tab === 'registracia'" x-cloak>

 {{-- Countdown: registration not yet open --}}
 @if(in_array($event->status, ['countdown', 'upcoming']) && $org->registration_opens_at && $org->registration_opens_at->isFuture())
 <x-registration-countdown :target-date="$org->registration_opens_at" :accent-color="$categoryColor" />

 {{-- Registration is open OR has external link --}}
 @elseif($event->status === 'registering')

 {{-- Closing countdown --}}
 @if($org->registration_closes_at && $org->registration_closes_at->isFuture())
 <div
     x-data="countdown('{{ $org->registration_closes_at->toIso8601String() }}')"
     x-init="start()"
     class="flex items-center gap-3 bg-[#1A1A1A] border border-[#222222] rounded-lg px-5 py-3 mb-8"
 >
     <span class="text-[#888888] text-xs font-bold tracking-wider shrink-0">{{ __('event_detail.countdown_to_close') }}</span>
     <span class="font-display font-bold text-lg tracking-wide" style="color: {{ $categoryColor }}">
         <span x-text="days > 0 ? days + 'd ' : ''"></span><span x-text="hours.toString().padStart(2,'0')">00</span>h
         <span x-text="minutes.toString().padStart(2,'0')">00</span>m
         <span x-text="seconds.toString().padStart(2,'0')">00</span>s
     </span>
 </div>
 @endif

 @if($org->external_link)
 {{-- External Link Registration --}}
 <div class="bg-[#111111] border border-[#222222] rounded-xl p-8 flex flex-col gap-5 max-w-[700px]">
 <h2 class="font-display font-bold text-[32px] tracking-wide text-white">{{ __('event_detail.register') }}</h2>
 <p class="text-[#888888] text-base leading-relaxed">{{ __('event_detail.cta_description') }}</p>
 <a href="{{ $org->external_link }}" target="_blank" rel="noopener" class="flex items-center justify-center gap-2 text-white text-base font-bold font-sans rounded-lg h-[52px] w-full transition-opacity hover:opacity-90" style="background-color: {{ $categoryColor }}">
 {{ __('event_detail.register') }}
 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
 </a>
 <p class="text-[#555555] text-xs text-center">{{ __('event_detail.external_link_note') }}</p>
 </div>
 @else
 {{-- Inline Livewire Registration Form --}}
 <livewire:event-registration-form :event="$event" />
 @endif
 @endif

 </div>
 @endif

 </div>
 </section>

 {{-- Gallery Section --}}
 @if($galleryImages->isNotEmpty())
 <section class="bg-[#0D0D0D]">
 <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 py-[60px]">
 <div class="flex flex-col gap-8">
 <div class="flex items-end justify-between">
 <div class="flex flex-col gap-3">
 <div class="flex items-center gap-2">
 <div class="w-6 h-0.5" style="background-color: {{ $categoryColor }}"></div>
 <span class="text-xs font-bold tracking-[2px]" style="color: {{ $categoryColor }}">{{ __('event_detail.gallery') }}</span>
 </div>
 <h2 class="font-display font-bold text-[36px] tracking-wide text-white">{{ __('event_detail.photos_from_event') }}</h2>
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

 {{-- More Events Section --}}
 @if($moreEvents->isNotEmpty())
 <section class="bg-[#0A0A0A]">
 <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 py-[60px]">
 <div class="flex flex-col gap-8">
 <div class="flex items-end justify-between">
 <div class="flex flex-col gap-3">
 <div class="flex items-center gap-2">
 <div class="w-6 h-0.5" style="background-color: {{ $categoryColor }}"></div>
 <span class="text-xs font-bold tracking-[2px]" style="color: {{ $categoryColor }}">{{ __('event_detail.more_events_label') }}</span>
 </div>
 <h2 class="font-display font-bold text-[36px] tracking-wide text-white">{{ __('event_detail.more_events_title') }} {{ mb_strtolower($categoryName) }}</h2>
 </div>
 <a href="{{ route('eventy') }}" class="flex items-center gap-2 border px-6 py-3 text-sm font-semibold transition-colors hover:border-current" style="color: {{ $categoryColor }}; border-color: {{ $categoryColor }}">
 {{ __('event_detail.all_events') }}
 <span>&#8594;</span>
 </a>
 </div>
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
 @foreach($moreEvents as $related)
 @php $relColor = $related->eventCategory->color ?? $categoryColor; @endphp
 <a href="{{ route('event.show', $related) }}" class="bg-[#111111] overflow-hidden group hover:border-[#333333] transition-colors flex flex-col">
 <div class="relative w-full h-[180px] bg-[#1A1A1A] overflow-hidden">
 @if($related->getFirstMediaUrl('card_image'))
 <img src="{{ $related->getFirstMediaUrl('card_image') }}" alt="{{ $related->getTranslation('title', $locale) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
 @endif
 </div>
 <div class="flex flex-col gap-3 p-5">
 <div class="flex items-center justify-between">
 @if($related->eventCategory)
 <span class="text-[11px] font-bold tracking-wider px-2.5 py-1" style="color: {{ $relColor }}; background-color: {{ $relColor }}20">
 {{ mb_strtoupper($related->eventCategory->getTranslation('title', $locale)) }}
 </span>
 @endif
 @if($related->date)
 <span class="text-[#666666] text-xs font-sans">{{ $related->date->format('Y') }}</span>
 @endif
 </div>
 <h3 class="text-white text-lg font-bold font-sans">{{ $related->getTranslation('title', $locale) }}</h3>
 @if($related->city)
 <div class="flex items-center gap-1.5 text-[#666666] text-xs font-sans">
 <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
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
