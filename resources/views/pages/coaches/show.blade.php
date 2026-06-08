@extends('layouts.public')

@section('title', $user->name . ' | BCZ Club')

@php
    $locale = app()->getLocale();
    $profile = $user->coachProfile;
    $biography = $profile?->getTranslation('biography', $locale);
    $heroImage = $profile?->getFirstMediaUrl('main_background_image');
    $biographyImage = $profile?->getFirstMediaUrl('biography_image');
    $trainings = $user->coachedTrainings;
    $certifications = $user->certifications->sortBy('sort_order');
    $gallery = $profile?->getMedia('gallery') ?? collect();
    $hasAthleteProfile = $hasAthleteProfile ?? false;
    $specialization = ($profile?->getTranslation('specialization', $locale) ?: null) ?? 'Parkour & Street Workout';
    $ogImage = $user->getFirstMediaUrl('profile_image') ?: $heroImage ?: $biographyImage;
    $personSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Person',
        'name' => $user->name,
        'jobTitle' => $specialization,
        'description' => seo_description($biography ?: $specialization),
        'url' => url()->current(),
        'image' => $ogImage ?: seo_default_og_image(),
    ];
@endphp

@section('meta_description', seo_description($biography ?: $specialization))
@if ($ogImage)
    @section('og_image', $ogImage)
@endif
@section('og_type', 'profile')

@push('schema')
    <script type="application/ld+json">
        @json($personSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
    </script>
@endpush

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
            <div class="flex items-center gap-2">
                <a href="{{ route('home') }}" class="text-[#888888] text-[11px] font-medium tracking-[1px] hover:text-white transition-colors">{{ __('coach_detail.breadcrumb_home') }}</a>
                <span class="text-[#666666] text-[11px]">></span>
                <a href="{{ route('treningy') }}" class="text-[#888888] text-[11px] font-medium tracking-[1px] hover:text-white transition-colors">{{ __('coach_detail.breadcrumb_trainings') }}</a>
                <span class="text-[#666666] text-[11px]">></span>
                <span class="text-bcz-red text-[11px] font-medium tracking-[1px]">{{ mb_strtoupper($user->name) }}</span>
            </div>

            {{-- Name --}}
            <h1 class="font-display font-bold text-[64px] tracking-[1px] text-white leading-none">
                {{ mb_strtoupper($user->name) }}
            </h1>

            {{-- Subtitle --}}
            <span class="text-bcz-red text-base font-medium tracking-[2px]">
                {{ __('coach_detail.role_subtitle', ['name' => $specialization]) }}
            </span>
        </div>
    </section>

    {{-- About / Biography Section --}}
    @if($biography)
        <section class="bg-[#111111] py-20">
            <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col lg:flex-row gap-16">
                {{-- Left: Text --}}
                <div class="flex flex-col gap-8 flex-1">
                    <div class="flex flex-col gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-0.5 bg-bcz-red"></div>
                            <span class="text-bcz-red text-xs font-bold tracking-[2px]">{{ __('coach_detail.about_label') }}</span>
                        </div>
                        <h2 class="font-display font-bold text-4xl tracking-[0.5px]">{{ __('coach_detail.about_title') }}</h2>
                    </div>

                    <div class="text-[#AAAAAA] text-base leading-relaxed space-y-4">
                        {!! $biography !!}
                    </div>
                </div>

                {{-- Right: Image --}}
                @if($biographyImage)
                    <div class="w-full lg:w-[400px] h-[400px] rounded-lg overflow-hidden shrink-0">
                        <img src="{{ $biographyImage }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    </div>
                @endif
            </div>
        </section>
    @endif

    {{-- Certifications / Education Section --}}
    @if($certifications->isNotEmpty())
        <section class="bg-[#0A0A0A] py-20">
            <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col gap-12">
                {{-- Header --}}
                <div class="flex flex-col items-center gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                        <span class="text-bcz-red text-xs font-bold tracking-[3px]">{{ __('coach_detail.certifications_label') }}</span>
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                    </div>
                    <h2 class="font-display font-bold text-4xl tracking-wide">{{ __('coach_detail.certifications_title') }}</h2>
                </div>

                {{-- Certification Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($certifications as $cert)
                        <div class="bg-[#111111] border border-bcz-border rounded-lg p-8 flex flex-col gap-4">
                            <div class="w-8 h-8 bg-bcz-red/20 rounded flex items-center justify-center">
                                @if($cert->getFirstMediaUrl('icon'))
                                    <img src="{{ $cert->getFirstMediaUrl('icon') }}" alt="" class="w-5 h-5 object-contain">
                                @else
                                    <svg class="w-4 h-4 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 10 3 12 0v-5"/>
                                    </svg>
                                @endif
                            </div>
                            <h3 class="text-white font-semibold text-lg">{{ $cert->getTranslation('name', $locale) }}</h3>
                            @if($cert->year_of_issue)
                                <span class="text-bcz-red text-xs font-medium">{{ $cert->year_of_issue }}</span>
                            @endif
                            @if($cert->getTranslation('description', $locale))
                                <p class="text-[#888888] text-sm leading-[1.6]">{{ $cert->getTranslation('description', $locale) }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Training Groups Section --}}
    @if($trainings->isNotEmpty())
        <section class="bg-[#111111] py-20">
            <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col gap-16">
                {{-- Header --}}
                <div class="flex flex-col items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                        <span class="text-bcz-red text-xs font-bold tracking-[3px]">{{ __('coach_detail.trainings_label') }}</span>
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                    </div>
                    <h2 class="font-display font-bold text-[48px] tracking-[1px]">{{ __('coach_detail.trainings_title') }}</h2>
                    <p class="text-[#666666] text-lg text-center">{{ __('coach_detail.trainings_subtitle') }}</p>
                </div>

                {{-- Training Cards (archive style) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($trainings as $training)
                        <a href="{{ route('team.training.show', [$training->team, $training]) }}" class="bg-[#111111] border border-bcz-border rounded-xl overflow-hidden group hover:border-[#333333] transition-colors flex flex-col">
                            <div class="h-[180px] bg-[#1A1A1A] overflow-hidden">
                                @if($training->sportCategory?->getFirstMediaUrl('hero_image'))
                                    <img src="{{ $training->sportCategory->getFirstMediaUrl('hero_image') }}" alt="{{ $training->getTranslation('title', $locale) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-12 h-12 text-[#333333]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                                    </div>
                                @endif
                            </div>

                            <div class="p-6 flex flex-col gap-4 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    @if($training->sportCategory)
                                        <span class="bg-bcz-red/20 text-bcz-red text-[10px] font-normal uppercase px-3 py-1.5 rounded">{{ $training->sportCategory->getTranslation('name', $locale) }}</span>
                                    @endif
                                    @if($training->age_range)
                                        <span class="bg-[#222222] text-[#888888] text-[10px] font-normal uppercase px-3 py-1.5 rounded">{{ $training->age_range }}</span>
                                    @endif
                                </div>

                                <h3 class="text-white text-xl font-semibold">{{ $training->getTranslation('title', $locale) }}</h3>

                                <div class="flex flex-col gap-2 flex-1">
                                    @if($training->schedules->isNotEmpty())
                                        <div class="flex items-center gap-2 text-[#888888] text-sm">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/>
                                            </svg>
                                            <span>
                                                @foreach($training->schedules as $schedule)
                                                    {{ __('coach_detail.days.' . $schedule->day) }} {{ $schedule->start_time ? \Illuminate\Support\Str::substr($schedule->start_time, 0, 5) : '' }}@if($schedule->start_time && $training->duration_minutes) - {{ \Carbon\Carbon::createFromFormat('H:i:s', $schedule->start_time)->addMinutes($training->duration_minutes)->format('H:i') }}@endif@if(!$loop->last), @endif
                                                @endforeach
                                            </span>
                                        </div>
                                    @endif
                                    @if($training->getTranslation('place_name', $locale))
                                        <div class="flex items-center gap-2 text-[#888888] text-sm">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>
                                            </svg>
                                            <span>{{ $training->getTranslation('place_name', $locale) }}</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex items-center justify-between pt-2">
                                    <div></div>
                                    <span class="bg-bcz-red rounded-md px-5 py-2.5 text-sm font-semibold text-white group-hover:bg-red-700 transition-colors">Detail</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Active Athlete Section (cross-role link) --}}
    @if($hasAthleteProfile)
        <section class="bg-[#0A0A0A] py-20">
            <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col lg:flex-row gap-12">
                <div class="flex flex-col gap-8 flex-1">
                    <div class="flex flex-col gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-0.5 bg-bcz-red"></div>
                            <span class="text-bcz-red text-xs font-bold tracking-[2px]">{{ __('SÚŤAŽNÝ PROFIL') }}</span>
                        </div>
                        <h2 class="font-display font-bold text-4xl tracking-[0.5px]">{{ __('AKTÍVNY ATLÉT') }}</h2>
                    </div>

                    <p class="text-[#AAAAAA] text-base leading-relaxed">
                        {{ __('Okrem trénerskej činnosti som aj aktívnym súťažiacim športovcom.') }}
                    </p>

                    <a href="{{ route('athlete.show', $user) }}" class="inline-flex items-center gap-3 bg-bcz-red px-8 py-4 rounded text-white text-sm font-bold tracking-wider hover:bg-red-700 transition-colors w-fit">
                        {{ __('ZOBRAZIŤ ATLETICKÝ PROFIL') }}
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- Gallery Section --}}
    @if($gallery->isNotEmpty())
        <x-profile-gallery :media="$gallery" />
    @endif

    {{-- Other Coaches --}}
    <x-other-profiles :user="$user" role="coach" :locale="$locale" />
@endsection
