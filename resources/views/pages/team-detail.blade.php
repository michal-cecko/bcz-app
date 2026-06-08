@extends('layouts.public')

@php
    $seoLocale = app()->getLocale();
    $teamOgImage = $team->getFilamentAvatarUrl() ?: $team->getFirstMediaUrl('logo');
    $teamSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'SportsTeam',
        'name' => $team->getTranslation('name', $seoLocale),
        'description' => seo_description($team->getTranslation('story', $seoLocale)),
        'url' => url()->current(),
        'logo' => $teamOgImage ?: asset('images/og-default.png'),
    ];
@endphp

@section('title', $team->getTranslation('name', $seoLocale) . ' | BCZ Club')
@section('meta_description', seo_description($team->getTranslation('story', $seoLocale)))
@if ($teamOgImage)
    @section('og_image', $teamOgImage)
@endif
@section('og_type', 'profile')

@push('schema')
    <script type="application/ld+json">
        @json($teamSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
    </script>
@endpush

@section('content')
    {{-- Hero Section --}}
    <section class="relative h-[420px] overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-bcz-dark via-bcz-dark/85 to-bcz-dark/60"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-bcz-dark/80 via-bcz-dark/40 to-transparent"></div>

        <div class="relative z-10 max-w-[1440px] mx-auto h-full flex flex-col justify-end px-5 md:px-10 lg:px-20 pb-10">
            {{-- Team Identity --}}
            <div class="flex items-end gap-6 mb-5">
                @if($team->getFilamentAvatarUrl())
                    <img src="{{ $team->getFilamentAvatarUrl() }}" alt="{{ $team->getTranslation('name', 'sk') }}"
                         class="w-20 h-20 lg:w-[100px] lg:h-[100px] rounded-2xl border-2 border-bcz-faint object-cover">
                @else
                    <div class="w-20 h-20 lg:w-[100px] lg:h-[100px] rounded-2xl bg-bcz-red flex items-center justify-center border-2 border-bcz-faint">
                        <span class="font-display text-2xl lg:text-3xl font-bold text-white">{{ mb_substr($team->getTranslation('name', 'sk'), 0, 3) }}</span>
                    </div>
                @endif
                <div class="flex flex-col justify-end gap-2">
                    <h1 class="font-display text-4xl sm:text-5xl lg:text-[56px] font-bold tracking-wide text-white uppercase">
                        {{ $team->getTranslation('name', app()->getLocale()) }}
                    </h1>
                    <p class="text-bcz-lighter text-base">Street Workout & Calisthenics Team</p>
                </div>
            </div>

            {{-- Meta Row --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-6">
                    <span class="flex items-center gap-1.5 text-bcz-dim text-[13px]">
                        <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                        Slovensko
                    </span>
                    <span class="flex items-center gap-1.5 text-bcz-dim text-[13px]">
                        <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                        Založený {{ $team->created_at->format('Y') }}
                    </span>
                    <span class="flex items-center gap-1.5 text-bcz-dim text-[13px]">
                        <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                        {{ $team->members->count() }} {{ trans_choice('člen|členovia|členov', $team->members->count()) }}
                    </span>
                </div>
                <div class="flex items-center gap-3">
                    <button class="flex items-center gap-2 bg-bcz-red text-white text-[13px] font-bold rounded-lg px-6 py-2.5 hover:bg-red-700 transition-colors">
                        <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        Sledovať
                    </button>
                    <button class="flex items-center gap-2 text-bcz-lighter text-[13px] font-semibold rounded-lg px-6 py-2.5 border border-bcz-faint hover:border-bcz-dim hover:text-white transition-colors">
                        <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z"/></svg>
                        Zdieľať
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- Sticky Navigation --}}
    <nav x-data="{ activeSection: 'prehled' }"
         x-init="
            const sections = ['prehled', 'clenovia', 'sutaze', 'uspechy', 'galeria', 'kontakt'];
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) activeSection = entry.target.id;
                });
            }, { rootMargin: '-80px 0px -70% 0px' });
            sections.forEach(id => {
                const el = document.getElementById(id);
                if (el) observer.observe(el);
            });
         "
         class="sticky top-0 z-40 bg-bcz-dark border-y border-[#1A1A1A]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex items-center overflow-x-auto scrollbar-hide">
            @foreach(['prehled' => 'Prehľad', 'clenovia' => 'Členovia', 'sutaze' => 'Súťaže', 'uspechy' => 'Úspechy', 'galeria' => 'Galéria', 'kontakt' => 'Kontakt'] as $id => $label)
                <a href="#{{ $id }}"
                   :class="activeSection === '{{ $id }}' ? 'text-white font-bold border-b-2 border-bcz-red' : 'text-bcz-lighter font-medium border-b-2 border-transparent'"
                   class="whitespace-nowrap text-[13px] px-6 py-4 hover:text-white transition-colors">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </nav>

    {{-- About / Prehľad Section --}}
    <section id="prehled" class="bg-bcz-dark">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 py-16">
            <div class="flex flex-col lg:flex-row gap-12">
                {{-- Left: Story --}}
                <div class="flex-1 flex flex-col gap-6">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-0.5 bg-bcz-red"></span>
                        <span class="text-bcz-red text-xs font-bold tracking-[2px]">O TÍME</span>
                    </div>
                    <h2 class="font-display text-5xl font-bold tracking-wide">NÁŠ PRÍBEH</h2>
                    @if($team->getTranslation('story', app()->getLocale()))
                        <div class="text-bcz-lighter text-base leading-relaxed space-y-4">
                            @foreach(explode("\n", $team->getTranslation('story', app()->getLocale())) as $paragraph)
                                @if(trim($paragraph))
                                    <p>{{ $paragraph }}</p>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Right: Stats Cards --}}
                <div class="w-full lg:w-[400px] flex flex-col gap-4">
                    <div class="flex items-center gap-3 rounded-xl bg-[#111111] border border-[#1A1A1A] px-5 py-4">
                        <svg class="w-5 h-5 text-bcz-red shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M18.75 4.236c.982.143 1.954.317 2.916.52A6.003 6.003 0 0016.27 9.728M18.75 4.236V4.5c0 2.108-.966 3.99-2.48 5.228m0 0a6.003 6.003 0 01-4.52-1.978 6.003 6.003 0 01-4.52 1.978"/></svg>
                        <div>
                            <p class="text-white text-[15px] font-bold">{{ $team->organizedCompetitions->count() }} súťaží</p>
                            <p class="text-bcz-dim text-xs">organizovaných podujatí</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 rounded-xl bg-[#111111] border border-[#1A1A1A] px-5 py-4">
                        <svg class="w-5 h-5 text-bcz-red shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                        <div>
                            <p class="text-white text-[15px] font-bold">{{ $team->members->count() }} aktívnych členov</p>
                            <p class="text-bcz-dim text-xs">športovci, tréneri a organizátori</p>
                        </div>
                    </div>
                    <a href="{{ route('team.trainings', $team) }}" class="flex items-center gap-3 rounded-xl bg-[#111111] border border-[#1A1A1A] px-5 py-4 hover:border-bcz-dim transition-colors">
                        <svg class="w-5 h-5 text-bcz-red shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                        <div>
                            <p class="text-white text-[15px] font-bold">{{ $team->trainings->count() }} tréningov</p>
                            <p class="text-bcz-dim text-xs">aktívnych tréningových skupín</p>
                        </div>
                    </a>
                    <div class="flex items-center gap-3 rounded-xl bg-[#111111] border border-[#1A1A1A] px-5 py-4">
                        <svg class="w-5 h-5 text-bcz-red shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418"/></svg>
                        <div>
                            <p class="text-white text-[15px] font-bold">{{ $team->events->count() }} podujatí</p>
                            <p class="text-bcz-dim text-xs">workshopy, vystúpenia a prednášky</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Members Section --}}
    <section id="clenovia" class="bg-[#111111]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 py-16">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-6 mb-12">
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-0.5 bg-bcz-red"></span>
                        <span class="text-bcz-red text-xs font-bold tracking-[2px]">ČLENOVIA TÍMU</span>
                    </div>
                    <h2 class="font-display text-5xl font-bold tracking-wide">Naši športovci & tréneri</h2>
                </div>
                @if($team->members->count() > 4)
                    <a href="{{ route('team.members', $team) }}" class="flex items-center gap-2 text-bcz-red text-sm font-semibold rounded-lg px-6 py-3 border border-bcz-red hover:bg-bcz-red/10 transition-colors">
                        Všetci členovia
                        <span>&rarr;</span>
                    </a>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($team->members as $member)
                    <div class="rounded-2xl bg-bcz-dark overflow-hidden group">
                        <div class="h-[280px] bg-[#1A1A1A] overflow-hidden">
                            @if($member->avatar_url)
                                <img src="{{ $member->avatar_url }}" alt="{{ $member->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-[#1A1A1A]">
                                    <span class="font-display text-4xl font-bold text-bcz-faint">{{ mb_substr($member->name, 0, 2) }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="px-5 py-4 flex flex-col gap-2">
                            <h3 class="text-white text-[17px] font-bold">{{ $member->name }}</h3>
                            <span class="text-bcz-red text-[11px] font-semibold tracking-wide uppercase">
                                {{ $member->pivot->is_active ? 'Aktívny člen' : 'Člen' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Competitions Section --}}
    <section id="sutaze" class="bg-bcz-dark">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 py-16">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-6 mb-12">
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-0.5 bg-bcz-red"></span>
                        <span class="text-bcz-red text-xs font-bold tracking-[2px]">SÚŤAŽE</span>
                    </div>
                    <h2 class="font-display text-5xl font-bold tracking-wide">Organizujeme & súťažíme</h2>
                </div>
                @if($team->organizedCompetitions->count() > 3)
                    <a href="{{ route('team.competitions', $team) }}" class="flex items-center gap-2 text-bcz-red text-sm font-semibold rounded-lg px-6 py-3 border border-bcz-red hover:bg-bcz-red/10 transition-colors">
                        Všetky súťaže
                        <span>&rarr;</span>
                    </a>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($team->organizedCompetitions->take(3) as $competition)
                    <div class="rounded-2xl bg-[#111111] border border-[#1A1A1A] overflow-hidden group">
                        <div class="h-[180px] bg-[#1A1A1A] overflow-hidden">
                            @if($competition->featured_image)
                                <img src="{{ $competition->featured_image }}" alt="{{ $competition->getTranslation('name', app()->getLocale()) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-12 h-12 text-bcz-faint" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M18.75 4.236c.982.143 1.954.317 2.916.52A6.003 6.003 0 0016.27 9.728M18.75 4.236V4.5c0 2.108-.966 3.99-2.48 5.228m0 0a6.003 6.003 0 01-4.52-1.978 6.003 6.003 0 01-4.52 1.978"/></svg>
                                </div>
                            @endif
                        </div>
                        <div class="px-5 py-4 flex flex-col gap-2.5">
                            <h3 class="text-white text-[17px] font-bold">{{ $competition->getTranslation('name', app()->getLocale()) }}</h3>
                            @if($competition->getTranslation('description', app()->getLocale()))
                                <p class="text-bcz-muted text-[13px] leading-relaxed line-clamp-2">{{ $competition->getTranslation('description', app()->getLocale()) }}</p>
                            @endif
                            @if($competition->start_date)
                                <p class="text-bcz-dim text-xs">{{ $competition->start_date->translatedFormat('F Y') }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Achievements Section --}}
    @if($team->getTranslation('achievements', app()->getLocale()))
        <section id="uspechy" class="bg-[#111111]">
            <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 py-16">
                <div class="flex flex-col gap-4 mb-12">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-0.5 bg-bcz-red"></span>
                        <span class="text-bcz-red text-xs font-bold tracking-[2px]">ÚSPECHY</span>
                    </div>
                    <h2 class="font-display text-5xl font-bold tracking-wide">Najväčšie úspechy</h2>
                </div>

                <div class="flex flex-col gap-4">
                    <div class="text-bcz-lighter text-base leading-relaxed">
                        {{ $team->getTranslation('achievements', app()->getLocale()) }}
                    </div>
                </div>
            </div>
        </section>
    @else
        <section id="uspechy"></section>
    @endif

    {{-- Gallery Section --}}
    <section id="galeria" class="bg-bcz-dark">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 py-16">
            <div class="flex flex-col gap-4 mb-12">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-0.5 bg-bcz-red"></span>
                    <span class="text-bcz-red text-xs font-bold tracking-[2px]">GALÉRIA</span>
                </div>
                <h2 class="font-display text-5xl font-bold tracking-wide">Momenty z akcií</h2>
            </div>

            <div class="text-bcz-dim text-center py-20">
                Galéria bude čoskoro dostupná.
            </div>
        </div>
    </section>

    {{-- Contact Section --}}
    <section id="kontakt" class="bg-[#111111]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 py-16">
            <div class="flex flex-col gap-4 mb-12">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-0.5 bg-bcz-red"></span>
                    <span class="text-bcz-red text-xs font-bold tracking-[2px]">KONTAKT</span>
                </div>
                <h2 class="font-display text-5xl font-bold tracking-wide">Spojte sa s nami</h2>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                {{-- Contact Info --}}
                <div class="flex flex-col gap-8">
                    <p class="text-bcz-muted text-base leading-relaxed">
                        Máte záujem o spoluprácu, chcete sa pripojiť k tímu alebo máte otázky? Neváhajte nás kontaktovať.
                    </p>

                    <div class="flex flex-col gap-5">
                        <div class="flex items-center gap-4">
                            <svg class="w-5 h-5 text-bcz-red shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                            <span class="text-white text-[15px] font-medium">info@bczclub.sk</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <svg class="w-5 h-5 text-bcz-red shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                            <span class="text-white text-[15px] font-medium">+421 950 451 310</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <svg class="w-5 h-5 text-bcz-red shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                            <span class="text-white text-[15px] font-medium">Čadca, Slovensko</span>
                        </div>
                    </div>

                    {{-- Social Icons --}}
                    @if($team->socials)
                        <div class="flex items-center gap-3">
                            @if($team->socials['instagram'] ?? null)
                                <a href="{{ $team->socials['instagram'] }}" target="_blank" class="w-11 h-11 rounded-full bg-[#1A1A1A] border border-bcz-faint flex items-center justify-center hover:border-bcz-dim transition-colors">
                                    <svg class="w-5 h-5 text-bcz-lighter" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                                </a>
                            @endif
                            @if($team->socials['facebook'] ?? null)
                                <a href="{{ $team->socials['facebook'] }}" target="_blank" class="w-11 h-11 rounded-full bg-[#1A1A1A] border border-bcz-faint flex items-center justify-center hover:border-bcz-dim transition-colors">
                                    <svg class="w-5 h-5 text-bcz-lighter" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                </a>
                            @endif
                            @if($team->socials['youtube'] ?? null)
                                <a href="{{ $team->socials['youtube'] }}" target="_blank" class="w-11 h-11 rounded-full bg-[#1A1A1A] border border-bcz-faint flex items-center justify-center hover:border-bcz-dim transition-colors">
                                    <svg class="w-5 h-5 text-bcz-lighter" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Contact Form --}}
                <div class="rounded-2xl bg-bcz-dark border border-[#1A1A1A] p-8">
                    <h3 class="text-white text-lg font-bold mb-5">Napíšte nám</h3>
                    <form class="flex flex-col gap-5">
                        <div class="flex flex-col gap-2">
                            <label class="text-bcz-muted text-[13px] font-medium">Meno</label>
                            <input type="text" placeholder="Vaše meno" class="w-full h-11 rounded-lg bg-[#111111] border border-[#222222] px-4 text-sm text-white placeholder-bcz-dim focus:border-bcz-red focus:outline-none transition-colors">
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-bcz-muted text-[13px] font-medium">Email</label>
                            <input type="email" placeholder="vas@email.com" class="w-full h-11 rounded-lg bg-[#111111] border border-[#222222] px-4 text-sm text-white placeholder-bcz-dim focus:border-bcz-red focus:outline-none transition-colors">
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-bcz-muted text-[13px] font-medium">Správa</label>
                            <textarea placeholder="Vaša správa..." rows="4" class="w-full rounded-lg bg-[#111111] border border-[#222222] px-4 py-3 text-sm text-white placeholder-bcz-dim focus:border-bcz-red focus:outline-none transition-colors resize-none"></textarea>
                        </div>
                        <button type="submit" class="w-full h-12 rounded-lg bg-bcz-red text-white text-sm font-bold hover:bg-red-700 transition-colors">
                            Odoslať správu
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
