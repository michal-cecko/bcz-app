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

                {{-- Client (REPORT only) --}}
                @if($event->client)
                    <div class="flex items-center gap-3 rounded-xl bg-bcz-dark border border-[#1A1A1A] px-5 py-4">
                        <div>
                            <p class="text-bcz-dim text-xs">Klient</p>
                            <p class="text-white text-[15px] font-bold">{{ $event->client }}</p>
                        </div>
                    </div>
                @endif

                {{-- Organization info (ORGANIZED + COMPETITION) --}}
                @if($event->organization)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        {{-- Price --}}
                        <div class="flex items-center gap-3 rounded-xl bg-bcz-dark border border-[#1A1A1A] px-5 py-4">
                            <svg class="w-5 h-5 text-bcz-red shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/><path d="M12 18V6"/>
                            </svg>
                            <div>
                                <p class="text-white text-[15px] font-bold">Cena</p>
                                @if($event->organization->pricing_type->value === 'paid')
                                    <p class="text-bcz-dim text-xs">{{ number_format($event->organization->price_amount, 2) }} {{ $event->organization->price_currency }}</p>
                                @else
                                    <p class="text-green-500 text-xs font-semibold">Zadarmo</p>
                                @endif
                            </div>
                        </div>

                        {{-- Capacity --}}
                        @if($event->organization->max_capacity)
                            <div class="flex items-center gap-3 rounded-xl bg-bcz-dark border border-[#1A1A1A] px-5 py-4">
                                <svg class="w-5 h-5 text-bcz-red shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                                <div>
                                    <p class="text-white text-[15px] font-bold">Kapacita</p>
                                    <p class="text-bcz-dim text-xs">{{ $event->registrations->count() }} / {{ $event->organization->max_capacity }}</p>
                                </div>
                            </div>
                        @endif

                        {{-- Registration status --}}
                        <div class="flex items-center gap-3 rounded-xl bg-bcz-dark border border-[#1A1A1A] px-5 py-4">
                            <svg class="w-5 h-5 text-bcz-red shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                                <rect width="8" height="4" x="8" y="2" rx="1" ry="1"/>
                            </svg>
                            <div>
                                <p class="text-white text-[15px] font-bold">Registrácia</p>
                                @if($event->status === 'registering')
                                    <p class="text-green-500 text-xs font-semibold">Otvorená</p>
                                @elseif($event->status === 'finished')
                                    <p class="text-[#666666] text-xs">Ukončená</p>
                                @elseif($event->status === 'upcoming' || $event->status === 'countdown')
                                    <p class="text-yellow-500 text-xs">Čoskoro</p>
                                @else
                                    <p class="text-[#666666] text-xs">Zatvorená</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Registration link --}}
                    @if($event->organization->external_link && $event->status === 'registering')
                        <a href="{{ $event->organization->external_link }}" target="_blank" rel="noopener"
                           class="inline-flex items-center justify-center gap-2 bg-bcz-red hover:bg-red-700 text-white font-bold py-3 px-8 rounded-xl transition-colors">
                            Registrovať sa
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M7 17L17 7M17 7H7M17 7v10"/></svg>
                        </a>
                    @endif
                @endif

                {{-- Competition timetable --}}
                @if($event->competitionDetail && $event->competitionDetail->timetableEntries->isNotEmpty())
                    <div class="rounded-2xl bg-bcz-dark border border-[#1A1A1A] p-6">
                        <h2 class="text-white text-xl font-bold mb-4">Harmonogram</h2>
                        <div class="flex flex-col gap-3">
                            @foreach($event->competitionDetail->timetableEntries as $entry)
                                <div class="flex items-center gap-4 py-2 {{ !$loop->last ? 'border-b border-[#1A1A1A]' : '' }}">
                                    <span class="text-bcz-dim text-sm w-16 shrink-0">{{ $entry->scheduled_time?->format('H:i') }}</span>
                                    <span class="text-white text-sm flex-1">{{ $entry->getTranslation('title', app()->getLocale()) }}</span>
                                    @if($entry->status->value === 'finished')
                                        <span class="text-xs px-2 py-0.5 rounded bg-green-500/10 text-green-500">Dokoncene</span>
                                    @elseif($entry->status->value === 'in_progress')
                                        <span class="text-xs px-2 py-0.5 rounded bg-yellow-500/10 text-yellow-500">Prebieha</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Competition registration fees --}}
                @if($event->competitionDetail && $event->competitionDetail->registrationFees->isNotEmpty())
                    <div class="rounded-2xl bg-bcz-dark border border-[#1A1A1A] p-6">
                        <h2 class="text-white text-xl font-bold mb-4">Registracne poplatky</h2>
                        <div class="flex flex-col gap-2">
                            @foreach($event->competitionDetail->registrationFees as $fee)
                                <div class="flex items-center justify-between py-2 {{ !$loop->last ? 'border-b border-[#1A1A1A]' : '' }}">
                                    <span class="text-white text-sm">
                                        {{ $fee->athleteCategory ? $fee->athleteCategory->getTranslation('name', app()->getLocale()) : $fee->description ?? 'Standardny' }}
                                    </span>
                                    <span class="text-bcz-red font-bold text-sm">{{ number_format($fee->amount, 2) }} {{ $fee->currency }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
