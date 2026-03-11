@extends('layouts.public')

@section('title', $training->getTranslation('title', app()->getLocale()) . ' - BCZ Club')

@section('content')
    {{-- Hero Section --}}
    <section class="bg-bcz-dark py-[60px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col items-center gap-6">
            {{-- Breadcrumb --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="text-[#666666] text-[11px] font-medium tracking-[2px] hover:text-white transition-colors">DOMOV</a>
                <span class="text-[#444444] text-[11px]">/</span>
                <a href="{{ route('treningy') }}" class="text-[#666666] text-[11px] font-medium tracking-[2px] hover:text-white transition-colors">TRÉNINGY</a>
                <span class="text-[#444444] text-[11px]">/</span>
                <span class="text-bcz-red text-[11px] font-medium tracking-[2px]">{{ mb_strtoupper($training->getTranslation('title', app()->getLocale())) }}</span>
            </div>

            @if($training->sportCategory)
                <span class="bg-bcz-red/20 text-bcz-red text-[11px] font-bold px-4 py-1.5 rounded">{{ $training->sportCategory->getTranslation('name', app()->getLocale()) }}</span>
            @endif

            <h1 class="font-display font-bold text-[28px] md:text-[40px] lg:text-[56px] tracking-wide text-center">
                {{ $training->getTranslation('title', app()->getLocale()) }}
            </h1>

            @if($training->team)
                <x-team-badge :team="$training->team" />
            @endif
        </div>
    </section>

    {{-- Content Section --}}
    <section class="bg-[#111111] py-16">
        <div class="max-w-[900px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col gap-10">
                {{-- Info Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @if($training->schedule_days)
                        <div class="flex items-center gap-3 rounded-xl bg-bcz-dark border border-[#1A1A1A] px-5 py-4">
                            <svg class="w-5 h-5 text-bcz-red shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/>
                            </svg>
                            <div>
                                <p class="text-white text-[15px] font-bold">Rozvrh</p>
                                <p class="text-bcz-dim text-xs">
                                    {{ collect($training->schedule_days)->pluck('day')->join(', ') }}
                                    @if($training->start_time)
                                        &middot; {{ \Illuminate\Support\Str::substr($training->start_time, 0, 5) }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endif

                    @if($training->getTranslation('place_name', app()->getLocale()))
                        <div class="flex items-center gap-3 rounded-xl bg-bcz-dark border border-[#1A1A1A] px-5 py-4">
                            <svg class="w-5 h-5 text-bcz-red shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>
                            </svg>
                            <div>
                                <p class="text-white text-[15px] font-bold">Miesto</p>
                                <p class="text-bcz-dim text-xs">{{ $training->getTranslation('place_name', app()->getLocale()) }}</p>
                            </div>
                        </div>
                    @endif

                    @if($training->max_capacity)
                        <div class="flex items-center gap-3 rounded-xl bg-bcz-dark border border-[#1A1A1A] px-5 py-4">
                            <svg class="w-5 h-5 text-bcz-red shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            <div>
                                <p class="text-white text-[15px] font-bold">Kapacita</p>
                                <p class="text-bcz-dim text-xs">{{ $training->max_capacity }} miest</p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Description --}}
                @if($training->getTranslation('description', app()->getLocale()))
                    <div class="text-bcz-lighter text-base leading-relaxed space-y-4">
                        @foreach(explode("\n", $training->getTranslation('description', app()->getLocale())) as $paragraph)
                            @if(trim($paragraph))
                                <p>{{ $paragraph }}</p>
                            @endif
                        @endforeach
                    </div>
                @endif

                {{-- Coaches --}}
                @if($training->coaches->isNotEmpty())
                    <div class="flex flex-col gap-6">
                        <h2 class="font-display text-2xl font-bold tracking-wide">Tréneri</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($training->coaches as $coach)
                                <div class="flex items-center gap-4 rounded-xl bg-bcz-dark border border-[#1A1A1A] px-5 py-4">
                                    @if($coach->avatar_url)
                                        <img src="{{ $coach->avatar_url }}" alt="{{ $coach->name }}" class="w-12 h-12 rounded-full object-cover">
                                    @else
                                        <div class="w-12 h-12 rounded-full bg-bcz-red flex items-center justify-center">
                                            <span class="text-white font-bold">{{ mb_substr($coach->name, 0, 2) }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="text-white text-[15px] font-bold">{{ $coach->name }}</p>
                                        @if($coach->pivot->role)
                                            <p class="text-bcz-dim text-xs">{{ $coach->pivot->role }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Price --}}
                @if($training->price_amount)
                    <div class="flex items-center gap-3 rounded-xl bg-bcz-red/10 border border-bcz-red/20 px-6 py-5">
                        <div>
                            <p class="text-white text-lg font-bold">{{ number_format($training->price_amount, 2) }} €</p>
                            @if($training->pricing_type)
                                <p class="text-bcz-dim text-xs">{{ $training->pricing_type->value }}</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
