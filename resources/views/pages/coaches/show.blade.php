@extends('layouts.public')

@section('title', $user->name . ' - BCZ Club')

@php
    $locale = app()->getLocale();
    $profile = $user->coachProfile;
    $biography = $profile?->getTranslation('biography', $locale);
    $heroImage = $profile?->main_background_image;
    $profileImage = $profile?->biography_image;
    $trainings = $user->coachedTrainings;
    $certifications = $user->certifications->sortBy('sort_order');
@endphp

@section('content')
    {{-- Hero Section --}}
    <section class="relative h-[450px] overflow-hidden">
        @if($heroImage)
            <img src="{{ brick_media_url($heroImage) }}" alt="{{ $user->name }}" class="absolute inset-0 w-full h-full object-cover">
        @else
            <div class="absolute inset-0 bg-[#1A1A1A]"></div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-b from-[#0A0A0A] via-transparent to-[#0A0A0A]"></div>

        <div class="relative z-10 flex flex-col items-center justify-center h-full px-5 md:px-20 gap-5">
            {{-- Breadcrumb --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="text-[#666666] text-[11px] font-medium tracking-[2px] hover:text-white transition-colors">{{ __('coach_detail.breadcrumb_home') }}</a>
                <span class="text-[#444444] text-[11px]">/</span>
                <a href="{{ route('treningy') }}" class="text-[#666666] text-[11px] font-medium tracking-[2px] hover:text-white transition-colors">{{ __('coach_detail.breadcrumb_trainings') }}</a>
                <span class="text-[#444444] text-[11px]">/</span>
                <span class="text-bcz-red text-[11px] font-medium tracking-[2px]">{{ mb_strtoupper($user->name) }}</span>
            </div>

            {{-- Profile Image --}}
            @if($profileImage)
                <div class="w-[120px] h-[120px] rounded-full overflow-hidden border-2 border-bcz-red">
                    <img src="{{ brick_media_url($profileImage) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                </div>
            @else
                <div class="w-[120px] h-[120px] rounded-full bg-bcz-red/20 flex items-center justify-center border-2 border-bcz-red">
                    <span class="text-bcz-red font-display font-bold text-4xl">{{ mb_substr($user->name, 0, 2) }}</span>
                </div>
            @endif

            {{-- Name --}}
            <h1 class="font-display font-bold text-[40px] md:text-[56px] tracking-wide text-center text-white leading-none">
                {{ $user->name }}
            </h1>

            {{-- Coaching since --}}
            @if($profile?->date_started_coaching)
                <span class="text-[#888888] text-sm">{{ __('coach_detail.coaching_since') }} {{ $profile->date_started_coaching->format('Y') }}</span>
            @endif
        </div>
    </section>

    {{-- Biography Section --}}
    @if($biography)
        <section class="bg-[#0A0A0A] py-20">
            <div class="max-w-[800px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col gap-8">
                {{-- Label --}}
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[3px]">{{ __('coach_detail.about_label') }}</span>
                </div>

                {{-- Title --}}
                <h2 class="font-display font-bold text-[40px] tracking-wide leading-none">{{ __('coach_detail.about_title') }}</h2>

                {{-- Biography Text --}}
                <div class="text-[#888888] text-[17px] leading-[1.7] space-y-4">
                    @foreach(explode("\n", $biography) as $paragraph)
                        @if(trim($paragraph))
                            <p>{{ $paragraph }}</p>
                        @endif
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Trainings Section --}}
    @if($trainings->isNotEmpty())
        <section class="bg-[#111111] py-20">
            <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col gap-12">
                {{-- Header --}}
                <div class="flex flex-col items-center gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                        <span class="text-bcz-red text-xs font-bold tracking-[3px]">{{ __('coach_detail.trainings_label') }}</span>
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                    </div>
                    <h2 class="font-display font-bold text-4xl tracking-wide">{{ __('coach_detail.trainings_title') }}</h2>
                </div>

                {{-- Training Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($trainings as $training)
                        <a href="{{ route('team.training.show', [$training->team, $training]) }}" class="bg-[#0A0A0A] border border-[#222222] p-6 flex flex-col gap-4 hover:border-bcz-red/50 transition-colors group">
                            <div class="flex items-center gap-2">
                                @if($training->sportCategory)
                                    <span class="text-bcz-red text-[11px] font-bold tracking-wider">{{ $training->sportCategory->getTranslation('name', $locale) }}</span>
                                @endif
                                @if($training->age_group)
                                    <span class="text-[#444444] text-[11px]">&middot;</span>
                                    <span class="text-[#666666] text-[11px] font-medium">{{ $training->age_group }}</span>
                                @endif
                            </div>

                            <h3 class="font-display font-bold text-xl tracking-wide group-hover:text-bcz-red transition-colors">{{ $training->getTranslation('title', $locale) }}</h3>

                            <div class="flex flex-col gap-2 text-[#888888] text-sm">
                                @if($training->schedule_days)
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-[#666666] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/>
                                        </svg>
                                        {{ collect($training->schedule_days)->map(fn ($d) => __('coach_detail.days.' . $d))->join(', ') }}
                                    </div>
                                @endif
                                @if($training->start_time)
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-[#666666] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                        </svg>
                                        {{ \Illuminate\Support\Str::substr($training->start_time, 0, 5) }}
                                    </div>
                                @endif
                                @if($training->getTranslation('place_name', $locale))
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-[#666666] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>
                                        </svg>
                                        {{ $training->getTranslation('place_name', $locale) }}
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center gap-2 text-bcz-red text-xs font-bold tracking-wider mt-auto pt-2">
                                {{ __('coach_detail.training_detail') }}
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                                </svg>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Certifications Section --}}
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
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($certifications as $cert)
                        <div class="bg-[#111111] border border-[#222222] p-6 flex flex-col gap-3">
                            <div class="flex items-center gap-3">
                                @if($cert->icon)
                                    <img src="{{ brick_media_url($cert->icon) }}" alt="" class="w-8 h-8 object-contain">
                                @else
                                    <div class="w-8 h-8 bg-bcz-red/20 rounded flex items-center justify-center">
                                        <svg class="w-4 h-4 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 10 3 12 0v-5"/>
                                        </svg>
                                    </div>
                                @endif
                                <h3 class="font-display font-bold text-lg tracking-wide">{{ $cert->getTranslation('name', $locale) }}</h3>
                            </div>

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
@endsection
