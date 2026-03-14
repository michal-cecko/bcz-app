@extends('layouts.public')

@section('title', $user->name . ' - BCZ Club')

@php
    $locale = app()->getLocale();
    $certifications = $user->certifications->sortBy('sort_order');
    $competitions = $user->judgedCompetitions;
@endphp

@section('content')
    {{-- Hero Section --}}
    <section class="relative h-[500px] overflow-hidden">
        @if($user->profile_image)
            <img src="{{ Storage::url($user->profile_image) }}" alt="{{ $user->name }}" class="absolute inset-0 w-full h-full object-cover">
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

            {{-- Subtitle: country + certifications --}}
            @if($certifications->isNotEmpty() || $user->country_code)
                <p class="text-bcz-red text-base font-medium tracking-[2px]">
                    @php
                        $parts = [];
                        if ($user->country_code) {
                            $parts[] = $user->country_code;
                        }
                        foreach ($certifications as $cert) {
                            $name = $cert->getTranslation('name', $locale);
                            if (is_array($name)) {
                                $name = $name[$locale] ?? reset($name);
                            }
                            $parts[] = $name;
                        }
                    @endphp
                    {{ implode(' · ', $parts) }}
                </p>
            @endif
        </div>
    </section>

    {{-- About Section --}}
    <section class="bg-[#111111] px-5 md:px-20 py-20">
        <div class="max-w-[1440px] mx-auto flex flex-col lg:flex-row gap-16">
            {{-- Left: Bio --}}
            <div class="flex-1 flex flex-col gap-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-[2px] bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[3px]">O ROZHODCOVI</span>
                    <div class="w-10 h-[2px] bg-bcz-red"></div>
                </div>
                <h2 class="font-display font-bold text-4xl tracking-[0.5px]">MÔJ PRÍBEH</h2>
                <p class="text-[#AAAAAA] text-base leading-[1.7]">{{ $user->name }} je skúsený rozhodca pôsobiaci v BCZ Club.</p>
            </div>

            {{-- Right: Info Cards --}}
            <div class="w-full lg:w-[320px] flex flex-col">
                @if($certifications->isNotEmpty())
                    <div class="flex justify-between items-center py-4 px-5 border-b border-[#222222]">
                        <span class="text-[#888888] text-sm">Certifikácie</span>
                        <span class="text-bcz-red text-sm font-semibold">{{ $certifications->pluck('name')->map(fn($n) => is_array($n) ? ($n[$locale] ?? reset($n)) : $n)->join(', ') }}</span>
                    </div>
                @endif
                <div class="flex justify-between items-center py-4 px-5 border-b border-[#222222]">
                    <span class="text-[#888888] text-sm">Súťaže</span>
                    <span class="text-white text-sm">{{ $competitions->count() }} hodnotených</span>
                </div>
                @if($user->country_code)
                    <div class="flex justify-between items-center py-4 px-5">
                        <span class="text-[#888888] text-sm">Krajina</span>
                        <span class="text-white text-sm">{{ $user->country_code }}</span>
                    </div>
                @endif
            </div>
        </div>
    </section>

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
                    @foreach($competitions as $competition)
                        <div class="flex items-center justify-between py-5 {{ !$loop->last ? 'border-b border-[#222222]' : '' }}">
                            <div class="flex items-center gap-4">
                                {{-- Year --}}
                                @if($competition->date_start)
                                    <span class="text-bcz-red text-sm font-bold">{{ $competition->date_start->format('Y') }}</span>
                                @endif

                                {{-- Name + Location --}}
                                <div class="flex flex-col gap-1">
                                    <span class="text-white text-[15px] font-semibold">{{ $competition->getTranslation('name', $locale) }}</span>
                                    @if($competition->place_name)
                                        <span class="text-[#888888] text-[13px]">{{ $competition->place_name }}</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Arrow --}}
                            <svg class="w-4 h-4 text-[#666666] shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
