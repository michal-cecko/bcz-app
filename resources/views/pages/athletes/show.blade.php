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
    $certifications = $user->certifications->sortBy('sort_order');
    $results = $user->competitionResults ?? collect();
    $gallery = $user->profileGalleryItems ?? collect();
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
                    <a href="{{ route('atleti') }}" class="text-[#888888] text-[11px] font-medium tracking-[1px] hover:text-white transition-colors">{{ __('ATLETI') }}</a>
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
                                <span class="text-[#888888] text-[11px] font-medium tracking-[1px]">{{ __('rokov skusenosti') }}</span>
                            </div>
                        @endif
                        @if($results->count() > 0)
                            <div class="flex flex-col gap-1">
                                <span class="font-display font-bold text-[42px] tracking-[0.5px] text-white leading-none">{{ $results->count() }}</span>
                                <span class="text-[#888888] text-[11px] font-medium tracking-[1px]">{{ __('sutazi') }}</span>
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
                            <span class="text-bcz-red text-xs font-bold tracking-[2px]">{{ __('MOJ PRIBEH') }}</span>
                        </div>
                        <h2 class="font-display font-bold text-4xl tracking-[0.5px]">{{ __('AKO TO VSETKO ZACALO') }}</h2>
                    </div>

                    <div class="text-[#AAAAAA] text-base leading-[1.8] space-y-4">
                        @foreach(explode("\n", $journeyText) as $paragraph)
                            @if(trim($paragraph))
                                <p>{{ $paragraph }}</p>
                            @endif
                        @endforeach
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
                    <p class="text-[#888888] text-base">{{ __('Kolko casu mi trvalo naucit sa jednotlive prvky') }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($exercises as $exercise)
                        <div class="bg-[#111111] border border-bcz-border rounded-lg p-6 flex flex-col gap-4">
                            @if($exercise->exercise)
                                <h3 class="text-white font-semibold text-lg">{{ $exercise->exercise->getTranslation('name', $locale) }}</h3>
                            @endif
                            @if($exercise->duration)
                                <span class="text-bcz-red text-xs font-bold">{{ $exercise->duration }}</span>
                            @endif
                            @if($exercise->getTranslation('description', $locale))
                                <p class="text-[#666666] text-sm">{{ $exercise->getTranslation('description', $locale) }}</p>
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
                        <span class="text-bcz-red text-xs font-bold tracking-[3px]">{{ __('USPECHY') }}</span>
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                    </div>
                    <h2 class="font-display font-bold text-4xl tracking-[0.5px]">{{ __('USPECHY & UMIESTNENIA') }}</h2>
                </div>

                <div class="flex flex-col gap-6">
                    @foreach($results->sortByDesc(fn ($r) => $r->competitionDetail?->event?->date) as $result)
                        @php
                            $event = $result->competitionDetail?->event;
                            $placement = $result->placement;
                            $medalColor = match($placement) {
                                1 => '#FFD700',
                                2 => '#C0C0C0',
                                3 => '#CD7F32',
                                default => '#666666',
                            };
                        @endphp
                        <div class="flex items-center gap-6 bg-[#0A0A0A] border {{ $placement && $placement <= 3 ? 'border-bcz-red' : 'border-bcz-border' }} rounded-lg p-6">
                            <div class="w-16 h-16 rounded-full flex items-center justify-center shrink-0" style="background: {{ $medalColor }}22">
                                <svg class="w-7 h-7" style="color: {{ $medalColor }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .982-3.172M12 3.75a2.999 2.999 0 0 0-2.573 4.534M12 3.75a2.999 2.999 0 0 1 2.572 4.534" />
                                </svg>
                            </div>
                            <div class="flex flex-col gap-2 flex-1">
                                <span class="text-white font-semibold text-lg">{{ $event?->getTranslation('title', $locale) }}</span>
                                <span class="text-[#888888] text-sm">{{ $event?->city }} &middot; {{ $event?->date?->format('Y') }}</span>
                            </div>
                            @if($placement)
                                <div class="flex flex-col items-end gap-1">
                                    <span class="font-bold text-xl" style="color: {{ $medalColor }}">{{ $placement }}. {{ __('MIESTO') }}</span>
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
                    <p class="text-[#888888] text-base">{{ __('Kam smerujem a co chcem dosiahnut') }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($goals as $goal)
                        @php
                            $statusColor = match($goal->status?->value) {
                                'in_progress' => '#FFD700',
                                'active' => '#22C55E',
                                'completed' => '#22C55E',
                                default => '#888888',
                            };
                        @endphp
                        <div class="bg-[#111111] border border-bcz-border rounded-lg p-8 flex flex-col gap-5">
                            <div class="w-14 h-14 bg-bcz-red rounded-lg flex items-center justify-center">
                                <svg class="w-7 h-7 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" />
                                </svg>
                            </div>
                            <h3 class="text-white font-semibold text-xl">{{ $goal->getTranslation('heading', $locale) }}</h3>
                            <p class="text-[#888888] text-sm leading-[1.6]">{{ $goal->getTranslation('description', $locale) }}</p>
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full" style="background: {{ $statusColor }}"></div>
                                <span class="text-[11px] font-medium" style="color: {{ $statusColor }}">{{ $goal->status?->getLabel() }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Gallery Section --}}
    @if($gallery->isNotEmpty())
        <x-profile-gallery :items="$gallery" :locale="$locale" />
    @endif

    {{-- Certifications Section --}}
    @if($certifications->isNotEmpty())
        <section class="bg-[#0A0A0A] py-20">
            <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col gap-12">
                <div class="flex flex-col items-center gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                        <span class="text-bcz-red text-xs font-bold tracking-[3px]">{{ __('VZDELANIE') }}</span>
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                    </div>
                    <h2 class="font-display font-bold text-4xl tracking-[0.5px]">{{ __('VZDELANIE & CERTIFIKATY') }}</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($certifications as $cert)
                        <div class="bg-[#111111] border border-bcz-border rounded-lg p-8 flex flex-col gap-4">
                            <div class="w-8 h-8 bg-bcz-red/20 rounded flex items-center justify-center">
                                <svg class="w-4 h-4 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 10 3 12 0v-5"/>
                                </svg>
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

    {{-- Other Athletes Section --}}
    <x-other-profiles :user="$user" role="athlete" :locale="$locale" />
@endsection
