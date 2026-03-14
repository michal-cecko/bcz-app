@extends('layouts.public')

@section('title', ($team ? $team->getTranslation('name', app()->getLocale()) . ' — ' : '') . 'Súťaže | BCZ Club')

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
                @if($team)
                    <a href="{{ route('team.show', $team) }}" class="text-[#666666] text-[11px] font-medium tracking-[2px] hover:text-white transition-colors">{{ mb_strtoupper($team->getTranslation('name', app()->getLocale())) }}</a>
                    <span class="text-[#444444] text-[11px]">/</span>
                @endif
                <span class="text-bcz-red text-[11px] font-medium tracking-[2px]">SÚŤAŽE</span>
            </div>

            {{-- Badge --}}
            <div class="flex items-center gap-2 bg-transparent border border-bcz-red/30 rounded-md px-4 py-2 w-fit">
                <div class="w-2 h-2 rounded-full bg-bcz-red"></div>
                <span class="text-bcz-red text-[11px] font-bold tracking-[2px]">
                    @if($team)
                        {{ mb_strtoupper($team->getTranslation('name', app()->getLocale())) }}
                    @else
                        SÚŤAŽE
                    @endif
                </span>
            </div>

            {{-- Title --}}
            <h1 class="font-display font-bold text-[36px] md:text-[56px] lg:text-[72px] leading-[0.95] tracking-wide text-white text-center">
                @if($team)
                    {{ mb_strtoupper($team->getTranslation('name', app()->getLocale())) }} — SÚŤAŽE
                @else
                    SÚŤAŽE
                @endif
            </h1>

            <p class="text-[#AAAAAA] text-lg md:text-xl text-center max-w-[700px]">
                @if($team)
                    Súťaže organizované tímom {{ $team->getTranslation('name', app()->getLocale()) }}
                @else
                    Prehľad všetkých súťaží na platforme BCZ
                @endif
            </p>
        </div>
    </section>

    {{-- Upcoming Competitions --}}
    @php
        $upcoming = $competitions->filter(fn ($c) => $c->status !== 'finished');
        $finished = $competitions->filter(fn ($c) => $c->status === 'finished');
    @endphp

    @if($upcoming->isNotEmpty())
        <section class="bg-bcz-dark py-[80px]">
            <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
                <div class="flex flex-col gap-12">
                    <div class="flex flex-col items-center gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-0.5 bg-bcz-red"></div>
                            <span class="text-bcz-red text-xs font-bold tracking-[3px]">NADCHÁDZAJÚCE</span>
                            <div class="w-10 h-0.5 bg-bcz-red"></div>
                        </div>
                        <h2 class="font-display font-bold text-[24px] md:text-[36px] lg:text-[48px] tracking-wide text-white text-center">NAJBLIŽŠIE SÚŤAŽE</h2>
                    </div>

                    <div class="flex flex-col gap-4">
                        @foreach($upcoming as $competition)
                            <a href="{{ route('team.competition.show', [$competition->organizerTeam, $competition]) }}" class="bg-[#111111] border border-[#222222] rounded-2xl overflow-hidden flex flex-col md:flex-row hover:border-[#333333] transition-colors group">
                                {{-- Date Column --}}
                                <div class="w-full md:w-[140px] {{ $loop->first ? 'bg-bcz-red' : 'bg-[#1A1A1A]' }} flex flex-col items-center justify-center py-6 md:py-0 shrink-0">
                                    @if($competition->date_start)
                                        <span class="font-display font-bold text-[36px] leading-none text-white">{{ $competition->date_start->format('d') }}</span>
                                        <span class="{{ $loop->first ? 'text-white/80' : 'text-[#888888]' }} text-[13px] font-semibold tracking-wider">{{ mb_strtoupper($competition->date_start->translatedFormat('M Y')) }}</span>
                                    @endif
                                </div>
                                {{-- Content Column --}}
                                <div class="flex-1 flex flex-col gap-3 p-6 md:p-8">
                                    <h3 class="font-display font-bold text-[24px] md:text-[28px] tracking-wide text-white">{{ $competition->getTranslation('name', app()->getLocale()) }}</h3>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($competition->disciplines as $discipline)
                                            <span class="bg-bcz-red/[0.12] text-bcz-red text-[10px] font-bold tracking-wider px-3 py-1.5 rounded">{{ mb_strtoupper($discipline->getTranslation('name', app()->getLocale())) }}</span>
                                        @endforeach
                                    </div>
                                    <div class="flex items-center gap-4 flex-wrap">
                                        @if($competition->city || $competition->country)
                                            <div class="flex items-center gap-2 text-[#888888] text-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>
                                                <span>{{ collect([$competition->city, $competition->country])->filter()->join(', ') }}</span>
                                            </div>
                                        @endif
                                        @if(!$team && $competition->organizerTeam)
                                            <x-team-badge :team="$competition->organizerTeam" />
                                        @endif
                                    </div>
                                </div>
                                {{-- CTA Column --}}
                                <div class="flex items-center px-6 pb-6 md:pb-0 md:pr-8">
                                    <span class="{{ $loop->first ? 'bg-bcz-red text-white hover:bg-red-700' : 'border border-[#444444] text-white hover:border-bcz-red' }} text-[12px] font-bold tracking-wider px-6 py-3 rounded-lg transition-colors whitespace-nowrap">
                                        DETAIL
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- Finished Competitions --}}
    @if($finished->isNotEmpty())
        <section class="bg-[#111111] py-[80px]">
            <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
                <div class="flex flex-col gap-12">
                    <div class="flex flex-col items-center gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-0.5 bg-bcz-red"></div>
                            <span class="text-bcz-red text-xs font-bold tracking-[3px]">UKONČENÉ</span>
                            <div class="w-10 h-0.5 bg-bcz-red"></div>
                        </div>
                        <h2 class="font-display font-bold text-[24px] md:text-[36px] lg:text-[48px] tracking-wide text-white text-center">VÝSLEDKY ZO SÚŤAŽÍ</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($finished as $competition)
                            <a href="{{ route('team.competition.show', [$competition->organizerTeam, $competition]) }}" class="bg-[#1A1A1A] border border-[#222222] rounded-2xl overflow-hidden group hover:border-[#333333] transition-colors">
                                <div class="relative w-full h-[200px] bg-[#1A1A1A] overflow-hidden">
                                    @if($competition->featured_image)
                                        <img src="{{ $competition->featured_image }}" alt="{{ $competition->getTranslation('name', app()->getLocale()) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    @endif
                                    <div class="absolute top-4 left-4 bg-[#222222] text-[#AAAAAA] text-[11px] font-bold tracking-wider px-3 py-1.5 rounded">UKONČENÁ</div>
                                </div>
                                <div class="flex flex-col gap-3 p-6">
                                    @if($competition->date_start)
                                        <span class="text-[#888888] text-[12px] font-medium tracking-wider">{{ mb_strtoupper($competition->date_start->translatedFormat('F Y')) }}</span>
                                    @endif
                                    <h3 class="font-display font-bold text-[24px] tracking-wide text-white">{{ $competition->getTranslation('name', app()->getLocale()) }}</h3>
                                    @if($competition->city || $competition->country)
                                        <div class="flex items-center gap-2 text-bcz-red text-[12px] font-bold">
                                            <span>{{ collect([$competition->city, $competition->country])->filter()->join(', ') }}</span>
                                        </div>
                                    @endif
                                    @if(!$team && $competition->organizerTeam)
                                        <x-team-badge :team="$competition->organizerTeam" />
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if($competitions->isEmpty())
        <section class="bg-bcz-dark py-[80px]">
            <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
                <div class="text-center py-20">
                    <p class="text-[#666666] text-lg">Momentálne nie sú k dispozícii žiadne súťaže.</p>
                </div>
            </div>
        </section>
    @endif

    {{-- Pagination --}}
    @if($competitions->hasPages())
        <section class="bg-bcz-dark pb-20">
            <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
                <div class="flex items-center justify-center">
                    {{ $competitions->links() }}
                </div>
            </div>
        </section>
    @endif
@endsection
