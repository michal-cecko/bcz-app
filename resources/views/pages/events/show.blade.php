@extends('layouts.public')

@section('title', $event->getTranslation('title', app()->getLocale()) . ' | BCZ Club')

@php
 $locale = app()->getLocale();
 $categoryColor = $event->eventCategory?->color ?? '#FF2D2D';
 $categoryName = $event->eventCategory?->getTranslation('title', $locale) ?? '';
 $title = $event->getTranslation('title', $locale);
 $description = $event->getTranslation('card_description', $locale);
 $heroImage = $event->getFirstMediaUrl('detail_image') ?: $event->getFirstMediaUrl('card_image');
 $isReport = $event->event_type === \App\Enums\EventTypeEnum::Report;
 $isOrganized = $event->event_type === \App\Enums\EventTypeEnum::Organized;
 $isCompetition = $event->event_type === \App\Enums\EventTypeEnum::Competition;
 $galleryImages = $event->getMedia('gallery');
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
 <a href="{{ route('home') }}"class="text-[#888888] text-sm font-normal font-sans hover:text-white transition-colors">{{ __('event_detail.home') }}</a>
 <span class="text-[#444444] text-sm">/</span>
 <a href="{{ route('eventy') }}"class="text-[#888888] text-sm font-normal font-sans hover:text-white transition-colors">{{ __('event_detail.events_archive') }}</a>
 <span class="text-[#444444] text-sm">/</span>
 <span class="text-sm font-normal font-sans"style="color: {{ $categoryColor }}">{{ $title }}</span>
 </div>

 {{-- Category Badge --}}
 @if($categoryName)
 <div class="w-fit px-3.5 py-1.5"style="background-color: {{ $categoryColor }}">
 <span class="text-white text-xs font-bold tracking-wide">{{ mb_strtoupper($categoryName) }}</span>
 </div>
 @endif

 {{-- Title --}}
 <h1 class="font-display font-bold text-[36px] md:text-[48px] lg:text-[56px] tracking-wide text-white leading-tight">
 {{ $title }}
 </h1>
 </div>
 </section>

 {{-- Content Wrapper (2-column) --}}
 <section class="bg-[#0A0A0A]">
 <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 py-[60px]">
 <div class="flex flex-col lg:flex-row gap-16">
 {{-- Main Content --}}
 <div class="flex-1 min-w-0 flex flex-col gap-4">
 {{-- Mason rendered content --}}
 @if($renderedContent)
 {!! $renderedContent !!}
 @elseif($description)
 <div class="flex flex-col gap-6">
 <h2 class="font-display font-bold text-[32px] tracking-wide text-white">
 @if($isReport)
 {{ __('event_detail.about_report') }}
 @elseif($isOrganized)
 {{ __('event_detail.about_organized') }}
 @else
 {{ __('event_detail.about_competition') }}
 @endif
 </h2>
 <p class="text-[#CCCCCC] text-base leading-relaxed font-sans">{{ $description }}</p>
 </div>
 @endif
 </div>

 {{-- Sidebar --}}
 <div class="w-full lg:w-[320px] shrink-0 flex flex-col gap-6 lg:sticky lg:top-24 lg:self-start">
 {{-- Event Info Card --}}
 <div class="bg-[#111111] border border-[#222222] p-6 flex flex-col gap-5">
 <h3 class="text-white text-lg font-bold font-sans">{{ __('event_detail.event_info') }}</h3>
 <div class="w-full h-px bg-[#222222]"></div>

 {{-- Type --}}
 <div class="flex items-center justify-between">
 <span class="text-[#888888] text-sm font-sans">{{ __('event_detail.type') }}</span>
 <span class="text-sm font-sans text-right"style="color: {{ $categoryColor }}">{{ $categoryName }}</span>
 </div>

 {{-- Client (Report only) --}}
 @if($isReport && $event->client)
 <div class="flex items-center justify-between">
 <span class="text-[#888888] text-sm font-sans">{{ __('event_detail.client') }}</span>
 <span class="text-white text-sm font-sans text-right">{{ $event->client }}</span>
 </div>
 @endif

 {{-- Location --}}
 @if($event->city || $event->country)
 <div class="flex items-center justify-between">
 <span class="text-[#888888] text-sm font-sans">{{ __('event_detail.location') }}</span>
 <span class="text-white text-sm font-sans text-right">{{ collect([$event->place_name, $event->city, $event->country])->filter()->join(', ') }}</span>
 </div>
 @endif

 {{-- Date --}}
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

 {{-- Attendees (Report) --}}
 @if($isReport && $event->attendee_count)
 <div class="flex items-center justify-between">
 <span class="text-[#888888] text-sm font-sans">{{ __('event_detail.attendees') }}</span>
 <span class="text-white text-sm font-sans text-right">{{ number_format($event->attendee_count, 0, ',', ' ') }}+</span>
 </div>
 @endif

 {{-- Price (Organized/Competition) --}}
 @if(!$isReport && $event->organization)
 <div class="flex items-center justify-between">
 <span class="text-[#888888] text-sm font-sans">{{ __('event_detail.price') }}</span>
 <span class="text-white text-sm font-semibold font-sans text-right">
 @if($event->organization->pricing_type->value === 'paid')
 {{ number_format($event->organization->price_amount, 2) }} {{ $event->organization->price_currency }}
 @else
 {{ __('event_detail.free') }}
 @endif
 </span>
 </div>

 @if($event->organization->max_capacity)
 <div class="flex items-center justify-between">
 <span class="text-[#888888] text-sm font-sans">{{ __('event_detail.capacity') }}</span>
 <span class="text-white text-sm font-sans text-right">{{ max(0, $event->organization->max_capacity - $event->registrations->count()) }}</span>
 </div>
 @endif

 <div class="flex items-center justify-between">
 <span class="text-[#888888] text-sm font-sans">{{ __('event_detail.registration') }}</span>
 @if($event->status === 'registering' && $event->organization?->registration_closes_at && $event->organization->registration_closes_at->isFuture())
 <div x-data="countdown('{{ $event->organization->registration_closes_at->toIso8601String() }}')" x-init="start()" class="flex items-center gap-1.5">
     <span class="text-green-500 text-sm font-semibold font-sans">{{ __('event_detail.open') }}</span>
     <span class="text-[#555555] text-xs">-</span>
     <span class="text-bcz-muted text-xs font-sans text-right" x-text="(days > 0 ? days + 'd ' : '') + hours.toString().padStart(2,'0') + 'h ' + minutes.toString().padStart(2,'0') + 'm ' + seconds.toString().padStart(2,'0') + 's'"></span>
 </div>
 @elseif($event->status === 'registering')
 <span class="text-green-500 text-sm font-semibold font-sans text-right">{{ __('event_detail.open') }}</span>
 @elseif($event->status === 'finished')
 <span class="text-[#666666] text-sm font-semibold font-sans text-right">{{ __('event_detail.finished') }}</span>
 @elseif(in_array($event->status, ['upcoming', 'countdown']))
 <span class="text-yellow-500 text-sm font-semibold font-sans text-right">{{ __('event_detail.soon') }}</span>
 @else
 <span class="text-[#666666] text-sm font-semibold font-sans text-right">{{ __('event_detail.closed') }}</span>
 @endif
 </div>
 @endif

 {{-- Disciplines (Competition) --}}
 @if($isCompetition && $event->competitionDetail?->disciplines->isNotEmpty())
 <div class="flex items-center justify-between">
 <span class="text-[#888888] text-sm font-sans">{{ __('event_detail.disciplines') }}</span>
 <span class="text-white text-sm font-sans text-right">{{ $event->competitionDetail->disciplines->map(fn ($d) => $d->getTranslation('name', $locale))->join(', ') }}</span>
 </div>
 @endif
 </div>

 {{-- CTA Card --}}
 <div class="p-6 flex flex-col gap-4"style="background-color: {{ $categoryColor }}10; border: 1px solid {{ $categoryColor }}40">
 <h3 class="text-white text-base font-bold font-sans">
 @if($isReport)
 {{ __('event_detail.cta_report') }}
 @elseif($isOrganized)
 {{ __('event_detail.cta_organized') }}
 @else
 {{ __('event_detail.cta_competition') }}
 @endif
 </h3>
 <p class="text-[#AAAAAA] text-sm font-sans leading-relaxed">
 @if($isReport)
 {{ __('event_detail.cta_report_description') }}
 @else
 {{ __('event_detail.cta_description') }}
 @endif
 </p>
 <a href="{{ route('kontakt') }}"class="flex items-center justify-center gap-2 text-white text-sm font-semibold font-sans py-3 w-full"style="background-color: {{ $categoryColor }}">
 {{ __('event_detail.contact') }}
 <span>&#8594;</span>
 </a>
 </div>

 @include('pages.events._share')
 </div>
 </div>
 </div>
 </section>

 {{-- Gallery Section --}}
 @if($galleryImages->isNotEmpty())
 <section class="bg-[#0D0D0D]">
 <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 py-[60px]">
 <div class="flex flex-col gap-8">
 {{-- Header --}}
 <div class="flex items-end justify-between">
 <div class="flex flex-col gap-3">
 <div class="flex items-center gap-2">
 <div class="w-6 h-0.5"style="background-color: {{ $categoryColor }}"></div>
 <span class="text-xs font-bold tracking-[2px]"style="color: {{ $categoryColor }}">{{ __('event_detail.gallery') }}</span>
 </div>
 <h2 class="font-display font-bold text-[36px] tracking-wide text-white">{{ __('event_detail.photos_from_event') }}</h2>
 </div>
 </div>

 {{-- Grid --}}
 <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
 @foreach($galleryImages as $image)
 <div class="overflow-hidden bg-[#1A1A1A] aspect-[4/3]">
 <img src="{{ $image->getUrl() }}"alt="{{ $image->name }}"class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
 </div>
 @endforeach
 </div>
 </div>
 </div>
 </section>
 @endif

 {{-- Registration Fees (Competition only) --}}
 @php
 $org2 = $event->organization;
 $overrideFees2 = $isCompetition ? ($event->competitionDetail?->registrationFees ?? collect()) : collect();
 $hasOverrides2 = $overrideFees2->isNotEmpty();
 $hasDefault2 = $isCompetition && $org2 && $org2->pricing_type === \App\Enums\EventPricingTypeEnum::Paid && $org2->price_amount > 0;
 $defaultLabelKey2 = $hasOverrides2 ? 'event_detail.fee_label_others' : 'event_detail.fee_label_all';
 @endphp
 @if($hasOverrides2 || $hasDefault2)
 <section class="bg-[#0A0A0A]">
 <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 py-[60px]">
 <div class="max-w-[900px]">
 <div class="bg-[#111111] border border-[#222222] p-6">
 <h2 class="text-white text-xl font-bold mb-4">{{ __('event_detail.registration_fees') }}</h2>
 <div class="flex flex-col gap-2">
 @foreach($overrideFees2 as $fee)
 <div class="flex items-center justify-between py-2 border-b border-[#1A1A1A]">
 <span class="text-white text-sm">
 {{ $fee->athleteCategory ? $fee->athleteCategory->getTranslation('name', $locale) : ($fee->description ?? __('event_detail.standard_fee')) }}
 </span>
 <span class="font-bold text-sm" style="color: {{ $categoryColor }}">
 @if((float) $fee->amount <= 0)
 {{ __('event_detail.free') }}
 @else
 {{ number_format($fee->amount, 2) }} {{ $fee->currency }}
 @endif
 </span>
 </div>
 @endforeach
 @if($hasDefault2)
 <div class="flex items-center justify-between py-2">
 <span class="text-white text-sm">{{ __($defaultLabelKey2) }}</span>
 <span class="font-bold text-sm" style="color: {{ $categoryColor }}">
 @if((float) $org2->price_amount <= 0)
 {{ __('event_detail.free') }}
 @else
 {{ number_format($org2->price_amount, 2) }} {{ $org2->price_currency ?? 'EUR' }}
 @endif
 </span>
 </div>
 @endif
 </div>
 </div>
 </div>
 </div>
 </section>
 @endif

 {{-- Timetable (Competition only) --}}
 @if($isCompetition && $event->competitionDetail?->timetableEntries->isNotEmpty())
 <section class="bg-[#0A0A0A]">
 <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 pb-[60px]">
 <div class="max-w-[900px]">
 <h2 class="font-display text-2xl font-bold tracking-wide mb-6">{{ __('event_detail.timetable') }}</h2>
 <div class="flex flex-col gap-3">
 @foreach($event->competitionDetail->timetableEntries as $entry)
 <div class="flex items-center gap-4 bg-[#111111] border border-[#1A1A1A] px-5 py-4">
 <span class="text-sm font-bold min-w-[60px]"style="color: {{ $categoryColor }}">{{ $entry->scheduled_time?->format('H:i') }}</span>
 <p class="text-white text-[15px] font-bold">{{ $entry->getTranslation('title', $locale) }}</p>
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
 {{-- Header --}}
 <div class="flex items-end justify-between">
 <div class="flex flex-col gap-3">
 <div class="flex items-center gap-2">
 <div class="w-6 h-0.5"style="background-color: {{ $categoryColor }}"></div>
 <span class="text-xs font-bold tracking-[2px]"style="color: {{ $categoryColor }}">{{ __('event_detail.more_events_label') }}</span>
 </div>
 <h2 class="font-display font-bold text-[36px] tracking-wide text-white">{{ __('event_detail.more_events_title') }} {{ mb_strtolower($categoryName) }}</h2>
 </div>
 <a href="{{ route('eventy') }}"class="flex items-center gap-2 border px-6 py-3 text-sm font-semibold transition-colors hover:border-current"style="color: {{ $categoryColor }}; border-color: {{ $categoryColor }}">
 {{ __('event_detail.all_events') }}
 <span>&#8594;</span>
 </a>
 </div>

 {{-- Grid --}}
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
 @if($related->eventCategory)
 <span class="text-[11px] font-bold tracking-wider px-2.5 py-1"style="color: {{ $relColor }}; background-color: {{ $relColor }}20">
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
