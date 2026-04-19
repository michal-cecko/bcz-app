@extends('layouts.public')

@section('title', $user->name . ' | BCZ Club')

@php
    $locale = app()->getLocale();
    $judgeProfile = $user->judgeProfile;
    $biography = $judgeProfile?->getTranslation('biography', $locale);
    $heroImage = $judgeProfile?->getFirstMediaUrl('hero_image');
    $disciplines = $judgeProfile?->disciplines ?? [];
    $certifications = $user->certifications->sortBy('sort_order');
    $competitions = $user->judgedCompetitionDetails ?? collect();
    $gallery = $judgeProfile?->getMedia('gallery') ?? collect();
    $yearsJudging = $judgeProfile?->date_started_judging ? (int) $judgeProfile->date_started_judging->diffInYears(now()) : null;
@endphp

@section('content')
    {{-- Hero Section --}}
    <section class="relative h-[500px] overflow-hidden">
        @if($heroImage)
            <img src="{{ $heroImage }}" alt="{{ $user->name }}" class="absolute inset-0 w-full h-full object-cover">
        @else
            <div class="absolute inset-0 bg-[#1A1A1A]"></div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-b from-[#0A0A0A] via-transparent to-[#0A0A0A]"></div>

        <div class="relative z-10 flex flex-col justify-center h-full max-w-[1440px] mx-auto px-5 md:px-20 gap-6">
            {{-- Breadcrumb --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="text-[#888888] text-[11px] font-medium tracking-[2px] hover:text-white transition-colors">DOMOV</a>
                <span class="text-[#666666] text-[11px]">></span>
                <a href="{{ route('judges.index') }}" class="text-[#888888] text-[11px] font-medium tracking-[2px] hover:text-white transition-colors">ROZHODCOVIA</a>
                <span class="text-[#666666] text-[11px]">></span>
                <span class="text-bcz-red text-[11px] font-medium tracking-[2px]">{{ mb_strtoupper($user->name) }}</span>
            </div>

            {{-- Name --}}
            <h1 class="font-display font-bold text-[64px] tracking-[1px] leading-none">{{ mb_strtoupper($user->name) }}</h1>

            {{-- Subtitle: country + role + disciplines --}}
            <p class="text-bcz-red text-base font-medium tracking-[2px]">
                @php
                    $parts = [];
                    if ($user->country_code) {
                        $parts[] = $user->country_code;
                    }
                    $parts[] = __('Porotca');
                    if (! empty($disciplines)) {
                        $parts[] = implode(' & ', array_map('ucfirst', $disciplines));
                    }
                @endphp
                {{ implode(' · ', $parts) }}
            </p>
        </div>
    </section>

    {{-- About Section --}}
    <section class="bg-[#111111] px-5 md:px-20 py-20">
        <div class="max-w-[1440px] mx-auto flex flex-col lg:flex-row gap-16">
            {{-- Left: Bio --}}
            <div class="flex-1 flex flex-col gap-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-[2px] bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[3px]">{{ __('O ROZHODCOVI') }}</span>
                    <div class="w-10 h-[2px] bg-bcz-red"></div>
                </div>
                <h2 class="font-display font-bold text-4xl tracking-[0.5px]">{{ __('MÔJ PRÍBEH') }}</h2>
                @if($biography)
                    <div class="text-[#AAAAAA] text-base leading-[1.7] space-y-4">
                        @foreach(explode("\n", $biography) as $paragraph)
                            @if(trim($paragraph))
                                <p>{{ $paragraph }}</p>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Right: Info Cards --}}
            <div class="w-full lg:w-[320px] flex flex-col">
                @if($yearsJudging)
                    <div class="flex justify-between items-center py-4 px-5 border-b border-bcz-border">
                        <span class="text-[#888888] text-sm">{{ __('Skúsenosti') }}</span>
                        <span class="text-white text-sm">{{ $yearsJudging }} {{ __('rokov') }}</span>
                    </div>
                @endif
                @if(! empty($disciplines))
                    <div class="flex justify-between items-center py-4 px-5 border-b border-bcz-border">
                        <span class="text-[#888888] text-sm">{{ __('Disciplíny') }}</span>
                        <span class="text-white text-sm">{{ implode(', ', array_map('ucfirst', $disciplines)) }}</span>
                    </div>
                @endif
                @if($certifications->isNotEmpty())
                    <div class="flex justify-between items-center py-4 px-5 border-b border-bcz-border">
                        <span class="text-[#888888] text-sm">{{ __('Certifikácie') }}</span>
                        <span class="text-bcz-red text-sm font-semibold">{{ $certifications->first()?->getTranslation('name', $locale) }}</span>
                    </div>
                @endif
                <div class="flex justify-between items-center py-4 px-5 border-b border-bcz-border">
                    <span class="text-[#888888] text-sm">{{ __('Súťaže') }}</span>
                    <span class="text-white text-sm">{{ $competitions->count() }} {{ __('hodnotených') }}</span>
                </div>
                @if($user->country_code)
                    <div class="flex justify-between items-center py-4 px-5">
                        <span class="text-[#888888] text-sm">{{ __('Krajina') }}</span>
                        <span class="text-white text-sm">{{ $user->country_code }}</span>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- Certifications Section --}}
    @if($certifications->isNotEmpty())
        <section class="bg-[#0D0D0D] px-5 md:px-20 py-20">
            <div class="max-w-[1440px] mx-auto flex flex-col gap-8">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-[2px] bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[3px]">CERTIFIKÁCIE A LICENCIE</span>
                    <div class="w-10 h-[2px] bg-bcz-red"></div>
                </div>
                <h2 class="font-display font-bold text-4xl tracking-[0.5px]">ODBORNÉ KVALIFIKÁCIE</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 {{ $certifications->count() === 1 ? 'max-w-[50%]' : '' }}">
                    @foreach($certifications as $cert)
                        <div class="bg-[#111111] border border-[#222222] rounded-xl p-6 flex flex-col gap-4">
                            @if($cert->year_of_issue)
                                <div class="flex items-center gap-2 bg-bcz-red/10 text-bcz-red text-[11px] font-bold rounded-md px-3 py-1.5 w-fit">
                                    <x-heroicon-o-academic-cap class="w-3.5 h-3.5" />
                                    {{ $cert->year_of_issue }}
                                </div>
                            @endif
                            <h3 class="text-white text-lg font-bold">{{ $cert->getTranslation('name', $locale) }}</h3>
                            @if($cert->getTranslation('description', $locale))
                                <div class="h-px bg-[#222222]"></div>
                                <p class="text-[#AAAAAA] text-[13px] leading-relaxed">{{ $cert->getTranslation('description', $locale) }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Competitions Section --}}
    @if($competitions->isNotEmpty())
        <section class="bg-[#0A0A0A] px-5 md:px-20 py-20">
            <div class="max-w-[1440px] mx-auto flex flex-col gap-8">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-[2px] bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[3px]">HISTÓRIA</span>
                    <div class="w-10 h-[2px] bg-bcz-red"></div>
                </div>
                <h2 class="font-display font-bold text-4xl tracking-[0.5px]">HODNOTIL NA SÚŤAŽIACH</h2>

                <div class="flex flex-col">
                    @foreach($competitions as $compDetail)
                        @php $event = $compDetail->event; @endphp
                        <div class="flex items-center justify-between py-5 {{ ! $loop->last ? 'border-b border-bcz-border' : '' }}">
                            <div class="flex items-center gap-4">
                                @if($event?->date)
                                    <span class="text-bcz-red text-sm font-bold">{{ $event->date->format('Y') }}</span>
                                @endif

                                <div class="flex flex-col gap-1">
                                    <span class="text-white text-[15px] font-semibold">{{ $event?->getTranslation('title', $locale) }}</span>
                                    @if($event?->city)
                                        <span class="text-[#888888] text-[13px]">{{ $event->city }}</span>
                                    @endif
                                </div>
                            </div>

                            <svg class="w-4 h-4 text-[#666666] shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
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

    {{-- Other Judges --}}
    <x-other-profiles :user="$user" role="judge" :locale="$locale" />
@endsection
