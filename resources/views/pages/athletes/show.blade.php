@extends('layouts.public')

@section('title', $user->name . ' | BCZ Club')

@php
    $locale = app()->getLocale();
    $profile = $user->athleteProfile;
    $journeyText = $profile?->getTranslation('journey_text', $locale);
    $heroImage = $profile?->getFirstMediaUrl('main_image');
    $journeyImage = $profile?->getFirstMediaUrl('journey_image');
    $exercises = $user->athleteExercises;
    $goals = $user->athleteGoals;
    $results = $user->competitionResults ?? collect();
    $gallery = $profile?->getMedia('gallery') ?? collect();
    $yearsExperience = $profile?->date_started_working_out ? (int) $profile->date_started_working_out->diffInYears(now()) : null;
    $country = $user->country_code ?? 'SK';
@endphp

@section('content')
    {{-- Hero Section --}}
    <section class="relative h-[600px] overflow-hidden">
        <div class="absolute inset-0 bg-[#1A1A1A]"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-[#0A0A0A] via-[#0A0A0A88] to-[#0A0A0A]"></div>

        <div class="relative z-10 flex items-center h-full max-w-[1440px] mx-auto px-5 md:px-20 gap-20">
            {{-- Left Content --}}
            <div class="flex flex-col gap-6 flex-1">
                {{-- Breadcrumb --}}
                <div class="flex items-center gap-2">
                    <a href="{{ route('home') }}" class="text-[#888888] text-[11px] font-medium tracking-[1px] hover:text-white transition-colors">{{ __('DOMOV') }}</a>
                    <span class="text-[#666666] text-[11px]">></span>
                    <a href="{{ route('athletes.index') }}" class="text-[#888888] text-[11px] font-medium tracking-[1px] hover:text-white transition-colors">{{ __('ATLETI') }}</a>
                    <span class="text-[#666666] text-[11px]">></span>
                    <span class="text-bcz-red text-[11px] font-medium tracking-[1px]">{{ mb_strtoupper($user->name) }}</span>
                </div>

                <h1 class="font-display font-bold text-[72px] tracking-[1px] text-white leading-none">
                    {{ mb_strtoupper($user->name) }}
                </h1>

                <span class="text-bcz-red text-lg font-medium tracking-[2px]">
                    {{ $country }} &middot; {{ __('Parkour & Street Workout Atlet') }}
                </span>

                {{-- Stats --}}
                @if($yearsExperience || $results->count() > 0)
                    <div class="flex items-center gap-12 mt-2">
                        @if($yearsExperience)
                            <div class="flex flex-col gap-1">
                                <span class="font-display font-bold text-[42px] tracking-[0.5px] text-white leading-none">{{ $yearsExperience }}</span>
                                <span class="text-[#888888] text-[11px] font-medium tracking-[1px]">{{ __('rokov skúseností') }}</span>
                            </div>
                        @endif
                        @if($results->count() > 0)
                            <div class="flex flex-col gap-1">
                                <span class="font-display font-bold text-[42px] tracking-[0.5px] text-white leading-none">{{ $results->count() }}</span>
                                <span class="text-[#888888] text-[11px] font-medium tracking-[1px]">{{ __('súťaží') }}</span>
                            </div>
                        @endif
                        @php $podiums = $results->filter(fn ($r) => $r->placement && $r->placement <= 3)->count(); @endphp
                        @if($podiums > 0)
                            <div class="flex flex-col gap-1">
                                <span class="font-display font-bold text-[42px] tracking-[0.5px] text-white leading-none">{{ $podiums }}x</span>
                                <span class="text-[#888888] text-[11px] font-medium tracking-[1px]">{{ __('na podiu') }}</span>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- CTA Buttons --}}
                <div class="flex items-center gap-4 mt-2">
                    @if($user->socials && isset($user->socials['instagram']))
                        <a href="{{ $user->socials['instagram'] }}" target="_blank" class="flex items-center gap-2.5 bg-bcz-red px-7 py-4 rounded text-white text-xs font-bold tracking-[1px]">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
                            {{ __('SLEDUJ MA') }}
                        </a>
                    @endif
                    @if($hasCoachProfile)
                        <a href="{{ route('coach.show', $user) }}" class="flex items-center gap-2.5 border border-[#444444] px-7 py-4 rounded text-white text-xs font-bold tracking-[1px] hover:border-white transition-colors">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path d="m6.5 6.5 11 11"/><path d="m21 21-4.35-4.35"/><path d="M14.5 13.5 18 17"/><path d="m10 10-6.35 6.35a2 2 0 0 0 0 2.83L5 20.5"/><path d="M8.93 6.59 6.71 4.36a2 2 0 0 0-2.83 0l-1.24 1.24"/><path d="m15 5 4 4"/><path d="m18 2 4 4"/></svg>
                            {{ __('TRENERSKY PROFIL') }}
                        </a>
                    @endif
                </div>
            </div>

            {{-- Right: Profile Image --}}
            @if($heroImage)
                <div class="hidden lg:block relative w-[400px] h-[440px] rounded-xl overflow-hidden shrink-0">
                    <img src="{{ $heroImage }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                </div>
            @endif
        </div>
    </section>

    {{-- About / Journey Section --}}
    @if($journeyText)
        <section class="bg-[#111111] py-20">
            <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col lg:flex-row gap-16">
                <div class="flex flex-col gap-8 flex-1">
                    <div class="flex flex-col gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-0.5 bg-bcz-red"></div>
                            <span class="text-bcz-red text-xs font-bold tracking-[2px]">{{ __('MÔJ PRÍBEH') }}</span>
                        </div>
                        <h2 class="font-display font-bold text-4xl tracking-[0.5px]">{{ __('AKO TO VŠETKO ZAČALO') }}</h2>
                    </div>

                    <div class="text-[#AAAAAA] text-base leading-relaxed space-y-4 prose prose-invert prose-p:text-[#AAAAAA] prose-a:text-bcz-red max-w-none">
                        {!! $journeyText !!}
                    </div>
                </div>

                @if($journeyImage)
                    <div class="w-full lg:w-[450px] h-[350px] rounded-lg overflow-hidden shrink-0">
                        <img src="{{ $journeyImage }}" alt="" class="w-full h-full object-cover">
                    </div>
                @endif
            </div>
        </section>
    @endif

    {{-- Progress / Skills Section --}}
    @if($exercises->isNotEmpty())
        <section class="bg-[#0A0A0A] py-20">
            <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col gap-12">
                <div class="flex flex-col items-center gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                        <span class="text-bcz-red text-xs font-bold tracking-[3px]">{{ __('POKROK') }}</span>
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                    </div>
                    <h2 class="font-display font-bold text-4xl tracking-[0.5px]">{{ __('CESTA K PRVKOM') }}</h2>
                    <p class="text-[#888888] text-base">{{ __('Koľko času mi trvalo naučiť sa jednotlivé prvky') }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($exercises as $exIndex => $exercise)
                        @php
                            $exMedia = $exercise->getMedia('exercise_media');
                            $exThumbnail = $exMedia->first();
                            $exName = $exercise->custom_name ?: $exercise->exercise?->getTranslation('name', $locale);
                            $exDescription = $exercise->getTranslation('description', $locale);
                            $hasModal = $exDescription || $exMedia->count() > 0;
                        @endphp
                        <div
                            @if($hasModal)
                                x-data="{ open: false }"
                                @click="open = true"
                            @endif
                            class="bg-[#111111] border border-bcz-border rounded-lg overflow-hidden flex flex-col {{ $hasModal ? 'cursor-pointer hover:border-bcz-red/50 transition-colors' : '' }}"
                        >
                            @if($exThumbnail)
                                <div class="w-full h-40 overflow-hidden">
                                    <img src="{{ $exThumbnail->getUrl() }}" alt="{{ $exName }}" class="w-full h-full object-cover">
                                </div>
                            @endif
                            <div class="p-6 flex flex-col gap-3 flex-1">
                                @if($exName)
                                    <h3 class="text-white font-semibold text-lg">{{ $exName }}</h3>
                                @endif
                                @if($exercise->duration)
                                    <span class="text-bcz-red text-xs font-bold">{{ $exercise->duration }}</span>
                                @endif
                                @if($exDescription)
                                    <p class="text-[#666666] text-sm line-clamp-3">{{ $exDescription }}</p>
                                @endif
                            </div>

                            {{-- Modal --}}
                            @if($hasModal)
                                <template x-teleport="body">
                                    <div x-show="open" x-cloak @click.self="open = false" @keydown.escape.window="open = false"
                                         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
                                        <div @click.stop class="bg-[#111111] border border-bcz-border rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                                            @if($exMedia->count() > 0)
                                                <div x-data="{ current: 0, fullscreen: false, images: {{ $exMedia->map(fn ($m) => $m->getUrl())->values()->toJson() }} }" class="relative"
                                                     @keydown.left.window="if (fullscreen) current = (current - 1 + images.length) % images.length"
                                                     @keydown.right.window="if (fullscreen) current = (current + 1) % images.length"
                                                     @keydown.escape.window="fullscreen = false"
                                                >
                                                    <img :src="images[current]" alt="{{ $exName }}" class="w-full h-72 object-cover rounded-t-xl cursor-pointer" @click="fullscreen = true">
                                                    @if($exMedia->count() > 1)
                                                        <button @click="current = (current - 1 + images.length) % images.length" class="absolute left-3 top-1/2 -translate-y-1/2 bg-black/60 hover:bg-black/80 text-white w-8 h-8 rounded-full flex items-center justify-center transition">
                                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
                                                        </button>
                                                        <button @click="current = (current + 1) % images.length" class="absolute right-3 top-1/2 -translate-y-1/2 bg-black/60 hover:bg-black/80 text-white w-8 h-8 rounded-full flex items-center justify-center transition">
                                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                                                        </button>
                                                        <div class="absolute bottom-3 right-3 bg-black/60 text-white text-xs px-2 py-1 rounded">
                                                            <span x-text="(current + 1) + '/' + images.length"></span>
                                                        </div>
                                                    @endif

                                                    {{-- Fullscreen Lightbox --}}
                                                    <template x-teleport="body">
                                                        <div x-show="fullscreen" x-cloak x-transition.opacity @click.self="fullscreen = false"
                                                             class="fixed inset-0 z-[9999] bg-black/95 flex items-center justify-center">
                                                            <button @click="fullscreen = false" class="absolute top-4 right-4 text-white/60 hover:text-white z-10 transition-colors">
                                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                                            </button>
                                                            @if($exMedia->count() > 1)
                                                                <button @click="current = (current - 1 + images.length) % images.length" class="absolute left-4 text-white/60 hover:text-white z-10 transition-colors">
                                                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                                                                </button>
                                                                <button @click="current = (current + 1) % images.length" class="absolute right-4 text-white/60 hover:text-white z-10 transition-colors">
                                                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                                                                </button>
                                                            @endif
                                                            <img :src="images[current]" alt="{{ $exName }}" class="max-h-[85vh] max-w-[90vw] object-contain rounded-lg">
                                                            <div class="absolute bottom-4 text-white/40 text-sm" x-show="images.length > 1" x-text="(current + 1) + ' / ' + images.length"></div>
                                                        </div>
                                                    </template>
                                                </div>
                                            @endif
                                            <div class="p-8 flex flex-col gap-4">
                                                <div class="flex items-center justify-between">
                                                    @if($exName)
                                                        <h3 class="text-white font-bold text-2xl">{{ $exName }}</h3>
                                                    @endif
                                                    <button @click="open = false" class="text-[#666666] hover:text-white transition shrink-0">
                                                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                                                    </button>
                                                </div>
                                                @if($exercise->duration)
                                                    <span class="text-bcz-red text-sm font-bold">{{ $exercise->duration }}</span>
                                                @endif
                                                @if($exDescription)
                                                    <p class="text-[#AAAAAA] text-base leading-relaxed">{{ $exDescription }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Achievements Section --}}
    @if($results->isNotEmpty())
        <section class="bg-[#111111] py-20">
            <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col gap-12">
                <div class="flex flex-col items-center gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                        <span class="text-bcz-red text-xs font-bold tracking-[3px]">{{ __('ÚSPECHY') }}</span>
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                    </div>
                    <h2 class="font-display font-bold text-4xl tracking-[0.5px]">{{ __('ÚSPECHY & UMIESTNENIA') }}</h2>
                </div>

                <div class="flex flex-col gap-6">
                    @foreach($results->sortByDesc(fn ($r) => $r->roundPart?->competitionRound?->competitionDetail?->event?->date) as $result)
                        @php
                            $event = $result->roundPart?->competitionRound?->competitionDetail?->event;
                            $roundPart = $result->roundPart;
                            $placement = $result->place;
                            $medalColor = match($placement) {
                                1 => '#FFD700',
                                2 => '#C0C0C0',
                                3 => '#CD7F32',
                                default => '#888888',
                            };
                            $isPodium = $placement && $placement <= 3;
                            $badgeLabel = match($placement) {
                                1 => '&#x1F947; ' . __('ZLATO'),
                                2 => '&#x1F948; ' . __('STRIEBRO'),
                                3 => '&#x1F949; ' . __('BRONZ'),
                                default => null,
                            };
                            $placementText = $isPodium
                                ? $placement . '. ' . __('MIESTO')
                                : ($placement ? 'TOP ' . $placement : null);
                            $borderColor = $isPodium ? $medalColor : '#222222';
                            $category = $roundPart?->competitionRound?->name;
                        @endphp
                        <div class="flex items-center gap-6 bg-[#0A0A0A] rounded-lg p-6" style="border: 1px solid {{ $borderColor }}">
                            <div class="w-16 h-16 rounded-full flex items-center justify-center shrink-0" style="background: {{ $medalColor }}22">
                                <svg class="w-7 h-7" style="color: {{ $medalColor }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/>
                                </svg>
                            </div>
                            <div class="flex flex-col gap-2 flex-1 min-w-0">
                                <span class="text-white font-semibold text-lg">{{ $event?->getTranslation('title', $locale) }}</span>
                                <span class="text-[#888888] text-sm">
                                    @if($category){{ $category }} &middot; @endif{{ $event?->city }}
                                </span>
                            </div>
                            @if($placementText)
                                <div class="flex flex-col items-end gap-1 shrink-0">
                                    <span class="font-bold text-xl font-display" style="color: {{ $medalColor }}">{{ $placementText }}</span>
                                    @if($badgeLabel)
                                        <span class="text-[11px] font-bold rounded px-2.5 py-1" style="color: {{ $medalColor }}; background: {{ $medalColor }}22">{!! $badgeLabel !!}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Goals Section --}}
    @if($goals->isNotEmpty())
        <section class="bg-[#0A0A0A] py-20">
            <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col gap-12">
                <div class="flex flex-col items-center gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                        <span class="text-bcz-red text-xs font-bold tracking-[3px]">{{ __('CIELE') }}</span>
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                    </div>
                    <h2 class="font-display font-bold text-4xl tracking-[0.5px]">{{ __('MOJE CIELE') }}</h2>
                    <p class="text-[#888888] text-base">{{ __('Kam smerujem a čo chcem dosiahnuť') }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($goals as $goalIndex => $goal)
                        @php
                            $statusColor = match($goal->status?->value) {
                                'in_progress' => '#FFD700',
                                'active' => '#22C55E',
                                'completed' => '#22C55E',
                                default => '#888888',
                            };
                            $goalMedia = $goal->getMedia('goal_media');
                            $thumbnail = $goalMedia->first();
                        @endphp
                        <div class="bg-[#111111] border border-bcz-border rounded-lg overflow-hidden flex flex-col">
                            {{-- Thumbnail --}}
                            @if($thumbnail)
                                <div
                                    x-data="{ lightbox: false, current: 0, images: {{ $goalMedia->map(fn ($m) => $m->getUrl())->values()->toJson() }} }"
                                    class="relative"
                                >
                                    <button @click="lightbox = true; current = 0" class="w-full h-[200px] overflow-hidden cursor-pointer group">
                                        <img src="{{ $thumbnail->getUrl() }}" alt="" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        @if($goalMedia->count() > 1)
                                            <div class="absolute bottom-2 right-2 bg-black/60 rounded-full px-2.5 py-1 flex items-center gap-1">
                                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                                                <span class="text-white text-[10px] font-medium">{{ $goalMedia->count() }}</span>
                                            </div>
                                        @endif
                                    </button>

                                    {{-- Lightbox --}}
                                    <template x-teleport="body">
                                        <div x-show="lightbox" x-cloak x-transition.opacity class="fixed inset-0 z-[9999] bg-black/95 flex items-center justify-center" @keydown.escape.window="lightbox = false" @keydown.left.window="current = (current - 1 + images.length) % images.length" @keydown.right.window="current = (current + 1) % images.length">
                                            <button @click="lightbox = false" class="absolute top-4 right-4 text-white/60 hover:text-white z-10">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                            </button>
                                            <button @click="current = (current - 1 + images.length) % images.length" class="absolute left-4 text-white/60 hover:text-white z-10" x-show="images.length > 1">
                                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                                            </button>
                                            <button @click="current = (current + 1) % images.length" class="absolute right-4 text-white/60 hover:text-white z-10" x-show="images.length > 1">
                                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                                            </button>
                                            <img :src="images[current]" alt="" class="max-h-[85vh] max-w-[90vw] object-contain rounded-lg">
                                            <div class="absolute bottom-4 text-white/40 text-sm" x-show="images.length > 1" x-text="(current + 1) + ' / ' + images.length"></div>
                                        </div>
                                    </template>
                                </div>
                            @endif

                            <div class="p-8 flex flex-col gap-5 flex-1">
                                <div class="w-14 h-14 bg-bcz-red rounded-lg flex items-center justify-center">
                                    @if($goal->icon)
                                        <x-icon name="{{ $goal->icon }}" class="w-7 h-7 text-white" />
                                    @else
                                        <svg class="w-7 h-7 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" />
                                        </svg>
                                    @endif
                                </div>
                                <h3 class="text-white font-semibold text-xl">{{ $goal->getTranslation('heading', $locale) }}</h3>
                                @if($goal->getTranslation('description', $locale))
                                    <p class="text-[#888888] text-sm leading-[1.6]">{{ $goal->getTranslation('description', $locale) }}</p>
                                @endif
                                <div class="flex items-center gap-2 mt-auto">
                                    <div class="w-2 h-2 rounded-full" style="background: {{ $statusColor }}"></div>
                                    <span class="text-[11px] font-medium" style="color: {{ $statusColor }}">{{ $goal->status?->getLabel() }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Gallery Section --}}
    @if($gallery->isNotEmpty())
        <x-profile-gallery :media="$gallery" />
    @endif

    {{-- Other Athletes Section --}}
    <x-other-profiles :user="$user" role="athlete" :locale="$locale" />
@endsection
