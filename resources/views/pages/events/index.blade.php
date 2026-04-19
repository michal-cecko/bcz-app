@extends('layouts.public')

@section('title', __('event_detail.events_page_title') . ' | BCZ Club')

@section('content')
 {{-- Hero Section --}}
 <section class="bg-bcz-dark pt-[120px] pb-[60px]">
 <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col items-center gap-6">
 {{-- Red Label with Lines --}}
 <div class="flex items-center gap-3">
 <div class="w-10 h-0.5 bg-bcz-red"></div>
 <span class="text-bcz-red text-[12px] font-bold tracking-[2px]">{{ __('event_detail.portfolio') }}</span>
 <div class="w-10 h-0.5 bg-bcz-red"></div>
 </div>

 {{-- Title --}}
 <h1 class="font-display font-bold text-[28px] md:text-[40px] lg:text-[56px] tracking-wide text-center text-white">{{ __('event_detail.events_page_title') }}</h1>

 {{-- Description --}}
 <p class="text-[#888888] text-[18px] text-center leading-relaxed max-w-[700px]">
 {{ __('event_detail.events_page_description') }}
 </p>

 {{-- Stats Row --}}
 <div class="flex flex-wrap gap-6 lg:gap-12 pt-6">
 <div class="flex flex-col items-center">
 <span class="font-display font-bold text-[36px] text-bcz-red tracking-wide">{{ $events->total() }}+</span>
 <span class="text-[#888888] text-sm">{{ __('event_detail.events_count') }}</span>
 </div>
 </div>
 </div>
 </section>

 {{-- Events Grid Section --}}
 <section class="bg-bcz-dark pt-10 pb-20">
 <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
 @if($events->isEmpty())
 <div class="text-center py-20">
 <p class="text-[#666666] text-lg">{{ __('event_detail.no_events') }}</p>
 </div>
 @else
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
 @foreach($events as $event)
 <a href="{{ route('event.show', $event) }}"class="bg-[#111111] overflow-hidden flex flex-col group hover:ring-1 hover:ring-[#333333] transition-all">
 <div class="h-[180px] bg-[#1A1A1A] overflow-hidden">
 @if($event->getFirstMediaUrl('card_image') || $event->card_image)
 <img src="{{ $event->getFirstMediaUrl('card_image') ?: $event->card_image }}"alt="{{ $event->getTranslation('title', app()->getLocale()) }}"class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
 @endif
 </div>
 <div class="p-5 flex flex-col gap-3">
 <div class="flex items-center justify-between">
 @if($event->eventCategory)
 <span class="text-xs px-2.5 py-1"
 style="background-color: {{ $event->eventCategory->color ?? '#E53E3E' }}20; color: {{ $event->eventCategory->color ?? '#E53E3E' }}">
 {{ $event->eventCategory->getTranslation('title', app()->getLocale()) }}
 </span>
 @endif
 @if($event->date)
 <span class="text-[#666666] text-xs">{{ $event->date->format('Y') }}</span>
 @endif
 </div>
 <h3 class="text-white text-lg font-bold">{{ $event->getTranslation('title', app()->getLocale()) }}</h3>
 @if($event->getTranslation('card_description', app()->getLocale()))
 <p class="text-[#888888] text-[13px] leading-relaxed line-clamp-2">{{ $event->getTranslation('card_description', app()->getLocale()) }}</p>
 @endif
 @if($event->city)
 <div class="flex items-center gap-1.5 text-[#666666] text-xs">
 <svg class="w-3.5 h-3.5"fill="none"stroke="currentColor"viewBox="0 0 24 24"stroke-width="2"stroke-linecap="round"stroke-linejoin="round">
 <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12"cy="10"r="3"/>
 </svg>
 <span>{{ $event->city }}</span>
 </div>
 @endif
 </div>
 </a>
 @endforeach
 </div>

 {{-- Pagination --}}
 @if($events->hasPages())
 {{ $events->links() }}
 @endif
 @endif
 </div>
 </section>
@endsection
