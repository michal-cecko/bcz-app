@extends('layouts.public')

@section('title', $competition->getTranslation('name', app()->getLocale()) . ' | BCZ Club')

@section('content')
    {{-- Hero Section --}}
    <section class="relative w-full py-[80px] md:py-[100px] overflow-hidden">
        <div class="absolute inset-0 bg-[#1A1A1A]"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-[#0A0A0A] via-[#0A0A0ACC] to-[#0A0A0A]"></div>

        <div class="relative w-full flex flex-col items-center justify-center gap-6 max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            {{-- Breadcrumb --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="text-[#666666] text-[11px] font-medium tracking-[2px] hover:text-white transition-colors">DOMOV</a>
                <span class="text-[#444444] text-[11px]">/</span>
                <a href="{{ route('sutaze') }}" class="text-[#666666] text-[11px] font-medium tracking-[2px] hover:text-white transition-colors">SÚŤAŽE</a>
                <span class="text-[#444444] text-[11px]">/</span>
                <span class="text-bcz-red text-[11px] font-medium tracking-[2px]">{{ mb_strtoupper($competition->getTranslation('name', app()->getLocale())) }}</span>
            </div>

            {{-- Status Badge --}}
            @php
                $statusColors = [
                    'upcoming' => 'border-bcz-red/30 text-bcz-red',
                    'countdown' => 'border-yellow-500/30 text-yellow-500',
                    'registering' => 'border-green-500/30 text-green-500',
                    'in_progress' => 'border-blue-500/30 text-blue-500',
                    'finished' => 'border-[#666]/30 text-[#888]',
                ];
                $statusLabels = [
                    'upcoming' => 'NADCHÁDZAJÚCA',
                    'countdown' => 'ODPOČET',
                    'registering' => 'REGISTRÁCIA OTVORENÁ',
                    'in_progress' => 'PREBIEHA',
                    'finished' => 'UKONČENÁ',
                ];
            @endphp
            <div class="flex items-center gap-2 bg-transparent border {{ $statusColors[$competition->status] ?? 'border-[#666]/30 text-[#888]' }} rounded-md px-4 py-2 w-fit">
                <div class="w-2 h-2 rounded-full bg-current"></div>
                <span class="text-[11px] font-bold tracking-[2px]">{{ $statusLabels[$competition->status] ?? mb_strtoupper($competition->status) }}</span>
            </div>

            {{-- Title --}}
            <h1 class="font-display font-bold text-[36px] md:text-[56px] lg:text-[72px] leading-[0.95] tracking-wide text-white text-center">
                {{ $competition->getTranslation('name', app()->getLocale()) }}
            </h1>

            @if($competition->organizerTeam)
                <x-team-badge :team="$competition->organizerTeam" />
            @endif
        </div>
    </section>

    {{-- Info Section --}}
    <section class="bg-[#111111] py-16">
        <div class="max-w-[900px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col gap-10">
                {{-- Info Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @if($competition->date_start)
                        <div class="flex items-center gap-3 rounded-xl bg-bcz-dark border border-[#1A1A1A] px-5 py-4">
                            <svg class="w-5 h-5 text-bcz-red shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/>
                            </svg>
                            <div>
                                <p class="text-white text-[15px] font-bold">Dátum</p>
                                <p class="text-bcz-dim text-xs">
                                    {{ $competition->date_start->translatedFormat('d. F Y') }}
                                    @if($competition->date_end && !$competition->date_start->isSameDay($competition->date_end))
                                        — {{ $competition->date_end->translatedFormat('d. F Y') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endif

                    @if($competition->city || $competition->country)
                        <div class="flex items-center gap-3 rounded-xl bg-bcz-dark border border-[#1A1A1A] px-5 py-4">
                            <svg class="w-5 h-5 text-bcz-red shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>
                            </svg>
                            <div>
                                <p class="text-white text-[15px] font-bold">Miesto</p>
                                <p class="text-bcz-dim text-xs">{{ collect([$competition->place_name, $competition->city, $competition->country])->filter()->join(', ') }}</p>
                            </div>
                        </div>
                    @endif

                    @if($competition->disciplines->isNotEmpty())
                        <div class="flex items-center gap-3 rounded-xl bg-bcz-dark border border-[#1A1A1A] px-5 py-4">
                            <svg class="w-5 h-5 text-bcz-red shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172"/>
                            </svg>
                            <div>
                                <p class="text-white text-[15px] font-bold">Disciplíny</p>
                                <p class="text-bcz-dim text-xs">{{ $competition->disciplines->map(fn ($d) => $d->getTranslation('name', app()->getLocale()))->join(', ') }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Description --}}
                @if($competition->getTranslation('description', app()->getLocale()))
                    <div class="text-bcz-lighter text-base leading-relaxed">
                        {{ $competition->getTranslation('description', app()->getLocale()) }}
                    </div>
                @endif

                {{-- Timetable --}}
                @if($competition->timetableEntries->isNotEmpty())
                    <div class="flex flex-col gap-6">
                        <h2 class="font-display text-2xl font-bold tracking-wide">Harmonogram</h2>
                        <div class="flex flex-col gap-3">
                            @foreach($competition->timetableEntries as $entry)
                                <div class="flex items-center gap-4 rounded-xl bg-bcz-dark border border-[#1A1A1A] px-5 py-4">
                                    <span class="text-bcz-red text-sm font-bold min-w-[60px]">{{ $entry->scheduled_time?->format('H:i') }}</span>
                                    <div>
                                        <p class="text-white text-[15px] font-bold">{{ $entry->getTranslation('title', app()->getLocale()) }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
