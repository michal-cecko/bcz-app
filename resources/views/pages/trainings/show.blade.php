{{-- Tailwind safelist: text-red-500 text-orange-400 text-emerald-500 bg-red-500 bg-orange-400 bg-emerald-500 bg-[#0A0A0A] bg-[#111111] --}}
@extends('layouts.public')

@section('title', $training->getTranslation('title', app()->getLocale()) . ' | BCZ Club')

@php
    $locale = app()->getLocale();
    $title = $training->getTranslation('title', $locale);
    $description = $training->getTranslation('description', $locale);
    $placeName = $training->getTranslation('place_name', $locale);
    $gatheringPlace = $training->getTranslation('gathering_place', $locale);
    $heroImage = $training->cardImageUrl();
    $schedules = $training->schedules;
    $timeRange = '';
    if (!$training->is_recurring && $training->start_time) {
        $timeRange = \Illuminate\Support\Str::substr($training->start_time, 0, 5);
        if ($training->duration_minutes) {
            $timeRange .= ' - ' . \Carbon\Carbon::createFromFormat('H:i:s', $training->start_time)->addMinutes($training->duration_minutes)->format('H:i');
        }
    }
    $registeredCount = $training->registrations_count;
    $remaining = $training->max_capacity ? max(0, $training->max_capacity - $registeredCount) : null;
    $capacityPercent = $training->max_capacity ? min(100, round(($registeredCount / $training->max_capacity) * 100)) : 0;
    $capacityColor = match(true) {
        $capacityPercent >= 90 => 'text-red-500',
        $capacityPercent >= 65 => 'text-orange-400',
        default => 'text-emerald-500',
    };
    $barColor = match(true) {
        $capacityPercent >= 90 => 'bg-red-500',
        $capacityPercent >= 65 => 'bg-orange-400',
        default => 'bg-emerald-500',
    };
    $ogImage = $heroImage ?: $training->team?->getFirstMediaUrl('logo');
    $galleryImages = $training->gallery_images ?? [];

    // The page ships one fixed section order: the hero, then the list below.
    // Only sections that actually have something to show are kept, so the
    // in-page navigation never links to a section that does not render, and the
    // background banding stays alternating whichever sections are skipped.
    // Each flag must mirror the Blade guard on the matching section below.
    $sections = array_keys(array_filter([
        'info' => true,
        'location' => (bool) ($placeName || $training->place_address || $gatheringPlace),
        'registration' => true,
        'coaches' => $training->coaches->isNotEmpty(),
        'gallery' => count($galleryImages) > 0,
    ]));

    $sectionLabels = [
        'info' => __('training_detail.about_label'),
        'location' => __('training_detail.location_label'),
        'registration' => __('training_detail.form_label'),
        'coaches' => __('training_detail.coach_title'),
        'gallery' => __('training_detail.gallery_title'),
    ];

    $sectionBackground = [];
    foreach ($sections as $index => $sectionKey) {
        $sectionBackground[$sectionKey] = $index % 2 === 0 ? 'bg-[#0A0A0A]' : 'bg-[#111111]';
    }
@endphp

@section('meta_description', seo_description($description))
@if ($ogImage)
    @section('og_image', $ogImage)
@endif
@section('og_type', 'article')

@section('content')
    {{-- Hero Section --}}
    <section class="relative h-[450px] overflow-hidden">
        @if($heroImage)
            <img src="{{ $heroImage }}" alt="{{ $title }}" class="absolute inset-0 w-full h-full object-cover">
        @else
            <div class="absolute inset-0 bg-[#1A1A1A]"></div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-b from-[#0A0A0A] via-transparent to-[#0A0A0A]"></div>

        <div class="relative z-10 flex flex-col items-center justify-center h-full px-5 md:px-20 gap-5">
            {{-- Breadcrumb --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="text-[#666666] text-[11px] font-medium tracking-[2px] hover:text-white transition-colors">{{ __('training_detail.breadcrumb_home') }}</a>
                <span class="text-[#444444] text-[11px]">/</span>
                <a href="{{ route('treningy') }}" class="text-[#666666] text-[11px] font-medium tracking-[2px] hover:text-white transition-colors">{{ __('training_detail.breadcrumb_trainings') }}</a>
                <span class="text-[#444444] text-[11px]">/</span>
                <span class="text-bcz-red text-[11px] font-medium tracking-[2px]">{{ mb_strtoupper($title) }}</span>
            </div>

            {{-- Title --}}
            <h1 class="font-display font-bold text-[40px] md:text-[64px] tracking-wide text-center text-white leading-none">
                {{ $title }}
            </h1>

            {{-- Badge --}}
            @if($training->age_range || $training->sportCategory)
                <div class="flex items-center gap-3 border border-bcz-red bg-bcz-red/10 px-5 py-2.5">
                    <span class="text-bcz-red text-[11px] font-bold tracking-wider">
                        @if($training->age_range){{ $training->age_range }}@endif
                        @if($training->age_range && $training->sportCategory)&nbsp;&middot;&nbsp;@endif
                        @if($training->sportCategory){{ $training->sportCategory->getTranslation('name', $locale) }}@endif
                    </span>
                </div>
            @endif
        </div>
    </section>

    {{-- Section Navigation --}}
    {{-- Sticks under the site header as a horizontal bar, and becomes a fixed
         vertical sidebar from 1800px up, where there is finally room beside the
         1440px content column for one that does not cover the page. Built from
         $sections, so it only ever links to sections that really rendered. --}}
    @if(count($sections) > 2)
        <nav
            aria-label="{{ __('training_detail.nav_sections_label') }}"
            x-data="{
                activeSection: @js($sections[0]),
                scrollToSection(id) {
                    const el = document.getElementById(id);
                    if (! el) return;
                    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                    el.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'start' });
                    this.activeSection = id;
                    history.replaceState(null, '', '#' + id);
                },
            }"
            x-init="
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) activeSection = entry.target.id;
                    });
                }, { rootMargin: '-140px 0px -65% 0px' });
                @js($sections).forEach(id => {
                    const el = document.getElementById(id);
                    if (el) observer.observe(el);
                });
            "
            class="sticky top-16 lg:top-20 z-40 bg-[#0A0A0A] border-y border-[#1A1A1A] min-[1800px]:fixed min-[1800px]:top-1/2 min-[1800px]:right-10 min-[1800px]:-translate-y-1/2 min-[1800px]:w-[200px] min-[1800px]:border-0 min-[1800px]:bg-transparent"
        >
            <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex items-center overflow-x-auto [scrollbar-width:none] [&::-webkit-scrollbar]:hidden min-[1800px]:mx-0 min-[1800px]:px-0 min-[1800px]:flex-col min-[1800px]:items-stretch min-[1800px]:overflow-visible">
                <span class="hidden min-[1800px]:block text-[#555555] text-[10px] font-bold tracking-[3px] pb-3 pl-4">{{ mb_strtoupper(__('training_detail.nav_sections_label')) }}</span>
                @foreach($sections as $sectionKey)
                    <a
                        href="#{{ $sectionKey }}"
                        @click.prevent="scrollToSection(@js($sectionKey))"
                        :class="activeSection === @js($sectionKey) ? 'text-white border-bcz-red' : 'text-[#888888] border-transparent hover:text-white'"
                        class="whitespace-nowrap font-display text-[11px] font-bold tracking-[2px] px-5 py-4 border-b-2 transition-colors min-[1800px]:border-b-0 min-[1800px]:border-l-2 min-[1800px]:px-4 min-[1800px]:py-2.5"
                    >
                        {{ $sectionLabels[$sectionKey] }}
                    </a>
                @endforeach
            </div>
        </nav>
    @endif

    {{-- Info Section --}}
    <section id="info" class="{{ $sectionBackground['info'] }} py-20 scroll-mt-[124px] lg:scroll-mt-[140px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col lg:flex-row gap-20">
            {{-- Left: About --}}
            <div class="flex-1 flex flex-col gap-8">
                {{-- Label --}}
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[3px]">{{ __('training_detail.about_label') }}</span>
                </div>

                {{-- Title --}}
                <h2 class="font-display font-bold text-[40px] tracking-wide leading-none whitespace-pre-line">{{ __('training_detail.about_title') }}</h2>

                {{-- Description --}}
                @if($description)
                    <div class="text-[#888888] text-[17px] leading-[1.7] space-y-4">
                        @foreach(explode("\n", $description) as $paragraph)
                            @if(trim($paragraph))
                                <p>{{ $paragraph }}</p>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Right: Details Card --}}
            <div class="w-full lg:w-[420px] shrink-0 bg-[#111111] border border-[#222222] p-8 flex flex-col gap-6">
                <span class="text-bcz-red text-xs font-bold tracking-[2px]">{{ __('training_detail.details_title') }}</span>

                {{-- Detail Rows --}}
                <div class="flex flex-col gap-5">
                    @if($training->sportCategory)
                        <div class="flex items-center justify-between">
                            <span class="text-[#666666] text-sm">{{ __('training_detail.detail_category') }}</span>
                            <span class="text-white text-sm font-semibold">{{ $training->sportCategory->getTranslation('name', $locale) }}</span>
                        </div>
                    @endif
                    @if($training->age_range)
                        <div class="flex items-center justify-between">
                            <span class="text-[#666666] text-sm">{{ __('training_detail.detail_age_group') }}</span>
                            <span class="text-white text-sm font-semibold">{{ $training->age_range }}</span>
                        </div>
                    @endif
                    <div class="flex items-center justify-between">
                        <span class="text-[#666666] text-sm">{{ __('training_detail.detail_gender') }}</span>
                        <span class="text-white text-sm font-semibold">
                            @if($training->gender)
                                {{ $training->gender->translation() }}
                            @else
                                {{ __('training_detail.all_genders') }}
                            @endif
                        </span>
                    </div>
                    @if($schedules->isNotEmpty())
                        @foreach($schedules as $schedule)
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] text-sm">{{ __('training_detail.days.' . $schedule->day) }}</span>
                                <span class="text-white text-sm font-semibold">
                                    @if($schedule->start_time)
                                        {{ \Illuminate\Support\Str::substr($schedule->start_time, 0, 5) }}
                                        @if($training->duration_minutes)
                                            - {{ \Carbon\Carbon::createFromFormat('H:i:s', $schedule->start_time)->addMinutes($training->duration_minutes)->format('H:i') }}
                                        @endif
                                    @endif
                                </span>
                            </div>
                        @endforeach
                    @elseif($timeRange)
                        <div class="flex items-center justify-between">
                            <span class="text-[#666666] text-sm">{{ __('training_detail.detail_time') }}</span>
                            <span class="text-white text-sm font-semibold">{{ $timeRange }}</span>
                        </div>
                    @endif
                    @if($placeName)
                        <div class="flex items-center justify-between">
                            <span class="text-[#666666] text-sm">{{ __('training_detail.detail_place') }}</span>
                            <span class="text-white text-sm font-semibold">{{ $placeName }}</span>
                        </div>
                    @endif
                    @if($training->city)
                        <div class="flex items-center justify-between">
                            <span class="text-[#666666] text-sm">{{ __('training_detail.detail_city') }}</span>
                            <span class="text-white text-sm font-semibold">{{ $training->city->getTranslation('name', $locale) ?: $training->city->getTranslation('name', 'sk') }}</span>
                        </div>
                    @endif
                    @if($training->pricing_type)
                        <div class="flex items-center justify-between">
                            <span class="text-[#666666] text-sm">{{ __('training_detail.detail_price') }}</span>
                            @if($training->pricing_type === \App\Enums\TrainingPricingTypeEnum::FREE)
                                <span class="text-emerald-500 text-sm font-semibold">{{ __('training_detail.pricing_free') }}</span>
                            @elseif($training->pricing_type === \App\Enums\TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED)
                                <span class="text-blue-400 text-sm font-semibold">{{ __('training_detail.pricing_membership') }}</span>
                            @elseif($training->pricing_type === \App\Enums\TrainingPricingTypeEnum::PAID && $training->price_amount)
                                <span class="text-white text-sm font-semibold">{{ number_format($training->price_amount, 2, ',', ' ') }} {{ $training->currency ?? '€' }}</span>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Capacity Section --}}
                @if($training->max_capacity)
                    <div class="h-px bg-[#222222]"></div>

                    <div class="flex flex-col gap-3">
                        <div class="flex items-center justify-between">
                            <span class="text-[#666666] text-sm">{{ __('training_detail.capacity_label') }}</span>
                            <span class="{{ $capacityColor }} text-sm font-semibold">
                                @if($remaining > 0)
                                    {{ $remaining }}/{{ $training->max_capacity }} {{ __('training_detail.capacity_spots') }}
                                @else
                                    {{ __('training_detail.capacity_full') }}
                                @endif
                            </span>
                        </div>
                        <div class="w-full h-2 bg-[#222222] rounded-full">
                            <div class="h-full {{ $barColor }} rounded-full transition-all" style="width: {{ $capacityPercent }}%"></div>
                        </div>
                        @if($remaining !== null && $remaining > 0 && $remaining <= 5)
                            <p class="{{ $capacityColor }} text-xs font-medium">
                                {{ trans_choice('training_detail.capacity_remaining', $remaining, ['count' => $remaining]) }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- Location Section --}}
    @if($placeName || $training->place_address || $gatheringPlace)
        <section id="location" class="{{ $sectionBackground['location'] }} py-20 scroll-mt-[124px] lg:scroll-mt-[140px]">
            <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col gap-12">
                {{-- Header --}}
                <div class="flex flex-col items-center gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                        <span class="text-bcz-red text-xs font-normal tracking-wider">{{ __('training_detail.location_label') }}</span>
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                    </div>
                    <h2 class="font-display font-bold text-4xl tracking-wide text-center">{{ __('training_detail.location_title') }}</h2>
                    @if($placeName)
                        <p class="text-[#888888] text-base text-center">{{ $placeName }}</p>
                    @endif
                </div>

                {{-- Content --}}
                <div class="flex flex-col lg:flex-row gap-10">
                    {{-- Map --}}
                    @if($training->latitude && $training->longitude)
                        <div class="flex-1 h-[350px] rounded-xl overflow-hidden bg-[#1A1A1A]">
                            <iframe
                                src="https://maps.google.com/maps?q={{ $training->latitude }},{{ $training->longitude }}&z=15&output=embed"
                                class="w-full h-full border-0"
                                allowfullscreen
                                loading="lazy"
                            ></iframe>
                        </div>
                    @endif

                    {{-- Location Details --}}
                    <div class="w-full lg:w-[400px] shrink-0 flex flex-col gap-6">
                        @if($training->place_address)
                            <div class="bg-[#0A0A0A] border border-[#222222] rounded-xl p-6 flex flex-col gap-4">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-bcz-red shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>
                                    </svg>
                                    <span class="text-white text-sm font-bold">{{ __('training_detail.location_address') }}</span>
                                </div>
                                <div class="flex flex-col gap-1">
                                    @if($placeName)
                                        <p class="text-white text-sm">{{ $placeName }}</p>
                                    @endif
                                    <p class="text-[#888888] text-sm">{{ $training->place_address }}</p>
                                </div>
                            </div>
                        @endif

                        @if($gatheringPlace)
                            <div class="bg-[#0A0A0A] border border-[#222222] rounded-xl p-6 flex flex-col gap-4">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-bcz-red shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                    <span class="text-white text-sm font-bold">{{ __('training_detail.location_meeting_title') }}</span>
                                </div>
                                <p class="text-[#888888] text-sm leading-relaxed">{{ $gatheringPlace }}</p>
                            </div>
                        @endif

                        @if($training->latitude && $training->longitude)
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $training->latitude }},{{ $training->longitude }}" target="_blank" rel="noopener" class="flex items-center justify-center gap-3 bg-bcz-red text-white text-base font-semibold rounded-lg px-6 py-4 hover:bg-red-700 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="3 11 22 2 13 21 11 13 3 11"/>
                                </svg>
                                {{ __('training_detail.location_open_maps') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- Registration Form Section --}}
    <section id="registration" class="{{ $sectionBackground['registration'] }} py-20 scroll-mt-[124px] lg:scroll-mt-[140px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col items-center gap-12">
            {{-- Header --}}
            <div class="flex flex-col items-center gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[3px]">{{ __('training_detail.form_label') }}</span>
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                </div>
                <h2 class="font-display font-bold text-4xl tracking-wide">{{ __('training_detail.form_title') }}</h2>
                <p class="text-[#666666] text-base">{{ __('training_detail.form_subtitle') }}</p>
            </div>

            {{-- Form Card --}}
            <div class="w-full max-w-[600px] bg-[#111111] border border-[#222222] p-10">
                <livewire:training-registration-form :training="$training" />
            </div>
        </div>
    </section>

    {{-- Coach Section --}}
    @if($training->coaches->isNotEmpty())
        <section id="coaches" class="{{ $sectionBackground['coaches'] }} py-20 scroll-mt-[124px] lg:scroll-mt-[140px]">
            <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col gap-12">
                {{-- Header --}}
                <div class="flex flex-col items-center gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                        <span class="text-bcz-red text-xs font-bold tracking-[3px]">{{ __('training_detail.coach_label') }}</span>
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                    </div>
                    <h2 class="font-display font-bold text-4xl tracking-wide">{{ __('training_detail.coach_title') }}</h2>
                </div>

                {{-- Coach Cards --}}
                @php $coachCount = $training->coaches->count(); @endphp
                <div class="grid grid-cols-1 {{ $coachCount >= 2 ? 'lg:grid-cols-2' : '' }} gap-6 {{ $coachCount === 1 ? 'mx-auto' : '' }}" @if($coachCount === 1) style="max-width: 680px" @endif>
                    @foreach($training->coaches as $coach)
                        @php
                            $roleEnum = \App\Enums\CoachRoleEnum::tryFrom($coach->pivot->role);
                            $isMain = $coach->pivot->role === 'main';
                        @endphp
                        <div class="bg-[#0A0A0A] border border-[#222222] rounded-2xl flex flex-col">
                            {{-- Coach Image with Badge --}}
                            <div class="relative w-full h-[250px] shrink-0 overflow-hidden rounded-t-2xl">
                                @if($coach->coachProfile?->getFirstMediaUrl('biography_image'))
                                    <img src="{{ $coach->coachProfile->getFirstMediaUrl('biography_image') }}" alt="{{ $coach->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-[#1A1A1A] flex items-center justify-center">
                                        <span class="text-bcz-red font-display font-bold text-6xl">{{ mb_substr($coach->name, 0, 2) }}</span>
                                    </div>
                                @endif
                                {{-- Role Badge --}}
                                @if($roleEnum)
                                    <div class="absolute top-4 left-4 {{ $isMain ? 'bg-bcz-red' : 'bg-[#333333]' }} rounded-md px-4 py-2">
                                        <span class="text-white text-[10px] font-bold tracking-[2px]">{{ mb_strtoupper($roleEnum->getLabel()) }}</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Coach Info --}}
                            <div class="flex flex-col gap-5 px-7 pb-[36px] pt-6 flex-1">
                                <div class="flex flex-col gap-1">
                                    <h3 class="font-display font-bold text-[28px] tracking-[0.5px]">{{ mb_strtoupper($coach->name) }}</h3>
                                    @if($roleEnum)
                                        <span class="text-bcz-red text-xs font-medium tracking-[1px]">{{ $roleEnum->getLabel() }}</span>
                                    @endif
                                </div>

                                @if($coach->coachProfile?->getTranslation('biography', $locale))
                                    <div class="text-[#888888] text-[15px] leading-[1.7] space-y-4">{!! $coach->coachProfile->getTranslation('biography', $locale) !!}</div>
                                @endif

                                {{-- Certifications --}}
                                @if($coach->certifications->isNotEmpty())
                                    <div class="flex flex-wrap gap-3">
                                        @foreach($coach->certifications as $cert)
                                            <div class="bg-[#222222] px-3.5 py-2 rounded">
                                                <span class="text-[#AAAAAA] text-[11px] font-medium">{{ $cert->getTranslation('name', $locale) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- CTA --}}
                                <a href="{{ route('coach.show', $coach) }}" class="flex items-center gap-2 text-bcz-red text-xs font-bold tracking-wider hover:text-red-400 transition-colors group/cta">
                                    {{ __('coach_detail.view_profile') }}
                                    <svg class="w-4 h-4 group-hover/cta:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Gallery Section --}}
    @if(count($galleryImages) > 0)
        <section id="gallery" class="{{ $sectionBackground['gallery'] }} py-20 scroll-mt-[124px] lg:scroll-mt-[140px]">
            <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col gap-12">
                {{-- Header --}}
                <div class="flex flex-col items-center gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                        <span class="text-bcz-red text-xs font-bold tracking-[3px]">{{ __('training_detail.gallery_label') }}</span>
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                    </div>
                    <h2 class="font-display font-bold text-4xl tracking-wide">{{ __('training_detail.gallery_title') }}</h2>
                    <p class="text-[#888888] text-base">{{ __('training_detail.gallery_subtitle') }}</p>
                </div>

                {{-- Masonry Grid --}}
                @php
                    $mediaItems = collect($galleryImages)->map(fn ($path) => (object) [
                        'url' => \Illuminate\Support\Facades\Storage::disk('public')->url($path),
                        'alt' => '',
                        'caption' => '',
                        'type' => 'image',
                    ])->filter(fn ($m) => $m->url)->values();
                    // Repeating tile aspect ratios keep the masonry rhythm at any column count.
                    $tileAspects = ['aspect-[4/3]', 'aspect-square', 'aspect-[3/2]'];
                    $jsData = $mediaItems->map(fn ($m) => ['url' => $m->url, 'alt' => $m->alt, 'caption' => $m->caption]);
                @endphp
                <div
                    x-data="{ lightbox: false, current: 0, items: {{ Js::from($jsData) }} }"
                    @keydown.escape.window="lightbox = false"
                    @keydown.left.window="if(lightbox) current = (current - 1 + items.length) % items.length"
                    @keydown.right.window="if(lightbox) current = (current + 1) % items.length"
                >
                    <div class="columns-1 sm:columns-2 lg:columns-3 gap-5">
                        @foreach($mediaItems as $index => $media)
                            @php
                                $isVideo = ($media->type ?? 'image') === 'video';
                            @endphp
                            <div
                                class="mb-5 break-inside-avoid {{ $tileAspects[$index % count($tileAspects)] }} rounded-lg overflow-hidden bg-[#1A1A1A] cursor-pointer relative group"
                                @if($media->url) @click="current = {{ $index }}; lightbox = true" @endif
                            >
                                @if($media->url)
                                    <img src="{{ $media->url }}" alt="{{ $media->alt ?? '' }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @endif
                                @if($isVideo)
                                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                                        <div class="w-16 h-16 rounded-full bg-bcz-red flex items-center justify-center">
                                            <svg class="w-7 h-7 text-white ml-1" fill="currentColor" viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21"/></svg>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Lightbox --}}
                    <template x-teleport="body">
                        <div x-show="lightbox" x-transition.opacity class="fixed inset-0 z-50 bg-black/95 flex items-center justify-center" @click.self="lightbox = false">
                            <button @click="lightbox = false" class="absolute top-6 right-6 text-white/70 hover:text-white z-10">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                            </button>
                            <a :href="items[current]?.url" download class="absolute top-6 right-20 text-white/70 hover:text-white z-10">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            </a>
                            <button @click.stop="current = (current - 1 + items.length) % items.length" class="absolute left-4 md:left-8 text-white/70 hover:text-white z-10">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                            </button>
                            <button @click.stop="current = (current + 1) % items.length" class="absolute right-4 md:right-8 text-white/70 hover:text-white z-10">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                            </button>
                            <div class="flex flex-col items-center gap-4 max-w-[90vw]">
                                <img :src="items[current]?.url" :alt="items[current]?.alt" class="max-h-[75vh] max-w-full object-contain">
                                <div x-show="items[current]?.caption" class="text-white/80 text-sm text-center max-w-2xl" x-text="items[current]?.caption"></div>
                            </div>
                            <div class="absolute bottom-6 left-1/2 -translate-x-1/2 text-white/50 text-sm" x-text="(current + 1) + ' / ' + items.length"></div>
                        </div>
                    </template>
                </div>
            </div>
        </section>
    @endif
@endsection
