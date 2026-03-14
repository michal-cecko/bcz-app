@extends('layouts.public')

@section('title', $event->getTranslation('title', app()->getLocale()) . ' | BCZ Club')

@section('content')
    {{-- Hero Section --}}
    <section class="bg-bcz-dark py-[60px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col items-center gap-6">
            {{-- Breadcrumb --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="text-[#666666] text-[11px] font-medium tracking-[2px] hover:text-white transition-colors">DOMOV</a>
                <span class="text-[#444444] text-[11px]">/</span>
                <a href="{{ route('eventy') }}" class="text-[#666666] text-[11px] font-medium tracking-[2px] hover:text-white transition-colors">PODUJATIA</a>
                <span class="text-[#444444] text-[11px]">/</span>
                <span class="text-bcz-red text-[11px] font-medium tracking-[2px]">{{ mb_strtoupper($event->getTranslation('title', app()->getLocale())) }}</span>
            </div>

            @if($event->eventCategory)
                <span class="text-xs px-4 py-1.5 rounded"
                      style="background-color: {{ $event->eventCategory->color ?? '#E53E3E' }}20; color: {{ $event->eventCategory->color ?? '#E53E3E' }}">
                    {{ $event->eventCategory->getTranslation('title', app()->getLocale()) }}
                </span>
            @endif

            <h1 class="font-display font-bold text-[28px] md:text-[40px] lg:text-[56px] tracking-wide text-center">
                {{ $event->getTranslation('title', app()->getLocale()) }}
            </h1>

            @if($event->date)
                <p class="text-[#888888] text-lg">{{ $event->date->translatedFormat('d. F Y') }}</p>
            @endif
        </div>
    </section>

    {{-- Content Section --}}
    <section class="bg-[#111111] py-16">
        <div class="max-w-[900px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col gap-10">
                {{-- Detail Image --}}
                @if($event->detail_image)
                    <div class="rounded-2xl overflow-hidden">
                        <img src="{{ $event->detail_image }}" alt="{{ $event->getTranslation('title', app()->getLocale()) }}" class="w-full object-cover">
                    </div>
                @endif

                {{-- Info Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @if($event->date)
                        <div class="flex items-center gap-3 rounded-xl bg-bcz-dark border border-[#1A1A1A] px-5 py-4">
                            <svg class="w-5 h-5 text-bcz-red shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/>
                            </svg>
                            <div>
                                <p class="text-white text-[15px] font-bold">Dátum</p>
                                <p class="text-bcz-dim text-xs">{{ $event->date->translatedFormat('d. F Y') }}</p>
                            </div>
                        </div>
                    @endif

                    @if($event->city)
                        <div class="flex items-center gap-3 rounded-xl bg-bcz-dark border border-[#1A1A1A] px-5 py-4">
                            <svg class="w-5 h-5 text-bcz-red shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>
                            </svg>
                            <div>
                                <p class="text-white text-[15px] font-bold">Miesto</p>
                                <p class="text-bcz-dim text-xs">{{ collect([$event->city, $event->country])->filter()->join(', ') }}</p>
                            </div>
                        </div>
                    @endif

                    @if($event->attendee_count)
                        <div class="flex items-center gap-3 rounded-xl bg-bcz-dark border border-[#1A1A1A] px-5 py-4">
                            <svg class="w-5 h-5 text-bcz-red shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            <div>
                                <p class="text-white text-[15px] font-bold">Účastníci</p>
                                <p class="text-bcz-dim text-xs">{{ $event->attendee_count }}+ účastníkov</p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Description --}}
                @if($event->getTranslation('card_description', app()->getLocale()))
                    <div class="text-bcz-lighter text-base leading-relaxed">
                        <p>{{ $event->getTranslation('card_description', app()->getLocale()) }}</p>
                    </div>
                @endif

                {{-- Client --}}
                @if($event->client)
                    <div class="flex items-center gap-3 rounded-xl bg-bcz-dark border border-[#1A1A1A] px-5 py-4">
                        <div>
                            <p class="text-bcz-dim text-xs">Klient</p>
                            <p class="text-white text-[15px] font-bold">{{ $event->client }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
