@extends('layouts.public')

@section('title', ($team ? $team->getTranslation('name', app()->getLocale()) . ' — ' : '') . 'Tréningy | BCZ Club')

@section('content')
    {{-- Hero Section --}}
    <section class="bg-bcz-dark py-[60px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col items-center gap-6">
            {{-- Breadcrumb --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="text-[#666666] text-[11px] font-medium tracking-[2px] hover:text-white transition-colors">DOMOV</a>
                <span class="text-[#444444] text-[11px]">/</span>
                @if($team)
                    <a href="{{ route('team.show', $team) }}" class="text-[#666666] text-[11px] font-medium tracking-[2px] hover:text-white transition-colors">{{ mb_strtoupper($team->getTranslation('name', app()->getLocale())) }}</a>
                    <span class="text-[#444444] text-[11px]">/</span>
                @endif
                <span class="text-bcz-red text-[11px] font-medium tracking-[2px]">TRÉNINGY</span>
            </div>

            {{-- Title --}}
            <h1 class="font-display font-bold text-[28px] md:text-[40px] lg:text-[56px] tracking-wide text-center">
                @if($team)
                    {{ mb_strtoupper($team->getTranslation('name', app()->getLocale())) }} — TRÉNINGY
                @else
                    TRÉNINGY
                @endif
            </h1>

            {{-- Subtitle --}}
            <p class="text-[#888888] text-[18px] text-center max-w-[600px]">
                @if($team)
                    Tréningy tímu {{ $team->getTranslation('name', app()->getLocale()) }}
                @else
                    Nájdi si tréning, ktorý ti vyhovuje - podľa kategórie, dňa alebo miesta konania
                @endif
            </p>
        </div>
    </section>

    {{-- Results Section --}}
    <section class="bg-bcz-dark py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col gap-8">
                {{-- Results Header --}}
                <div class="flex items-center justify-between">
                    <span class="text-[#888888] text-sm">{{ $trainings->count() }} {{ trans_choice('tréning|tréningy|tréningov', $trainings->count()) }}</span>
                </div>

                @if($trainings->isEmpty())
                    <div class="text-center py-20">
                        <p class="text-[#666666] text-lg">Momentálne nie sú k dispozícii žiadne tréningy.</p>
                    </div>
                @else
                    {{-- Training Cards Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($trainings as $training)
                            <a href="{{ route('team.training.show', [$training->team, $training]) }}" class="bg-[#111111] border border-[#222222] rounded-xl overflow-hidden group hover:border-[#333333] transition-colors">
                                <div class="h-[180px] bg-[#1A1A1A] overflow-hidden">
                                    @if($training->sportCategory?->hero_image)
                                        <img src="{{ $training->sportCategory->hero_image }}" alt="{{ $training->getTranslation('title', app()->getLocale()) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg class="w-12 h-12 text-[#333333]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-6 flex flex-col gap-4">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        @if($training->sportCategory)
                                            <span class="bg-bcz-red/20 text-bcz-red text-[10px] font-bold px-3 py-1.5 rounded">{{ $training->sportCategory->getTranslation('name', app()->getLocale()) }}</span>
                                        @endif
                                        @if($training->age_group)
                                            <span class="bg-[#222222] text-[#888888] text-[10px] font-bold px-3 py-1.5 rounded">{{ $training->age_group }}</span>
                                        @endif
                                    </div>

                                    <h3 class="text-white text-[20px] font-semibold">{{ $training->getTranslation('title', app()->getLocale()) }}</h3>

                                    <div class="flex flex-col gap-2">
                                        @if($training->schedule_days)
                                            <div class="flex items-center gap-2 text-[#888888] text-[14px]">
                                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/>
                                                </svg>
                                                <span>
                                                    {{ collect($training->schedule_days)->pluck('day')->join(', ') }}
                                                    @if($training->start_time)
                                                        &middot; {{ \Illuminate\Support\Str::substr($training->start_time, 0, 5) }}
                                                        @if($training->duration_minutes)
                                                            - {{ \Carbon\Carbon::createFromFormat('H:i:s', $training->start_time)->addMinutes($training->duration_minutes)->format('H:i') }}
                                                        @endif
                                                    @endif
                                                </span>
                                            </div>
                                        @endif
                                        @if($training->getTranslation('place_name', app()->getLocale()))
                                            <div class="flex items-center gap-2 text-[#888888] text-[14px]">
                                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>
                                                </svg>
                                                <span>{{ $training->getTranslation('place_name', app()->getLocale()) }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex items-center justify-between pt-2">
                                        <div class="flex items-center gap-3">
                                            @if($training->max_capacity)
                                                <div class="flex items-center gap-2 text-[#888888] text-sm">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                                    </svg>
                                                    <span>{{ $training->max_capacity }} miest</span>
                                                </div>
                                            @endif
                                            @if(!$team && $training->team)
                                                <x-team-badge :team="$training->team" />
                                            @endif
                                        </div>
                                        <span class="bg-bcz-red rounded-md px-5 py-2.5 text-sm font-semibold text-white group-hover:bg-bcz-red/90 transition-colors">Detail</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
