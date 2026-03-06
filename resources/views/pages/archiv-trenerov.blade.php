@extends('layouts.public')

@section('title', 'Archív trénerov - BCZ Club')

@section('content')
    {{-- Hero Section --}}
    <section class="bg-bcz-dark pt-[120px] pb-[60px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col items-center gap-6">
        {{-- Badge --}}
        <div class="rounded-full bg-[#FF2D2D20] px-4 py-2 flex items-center gap-2">
            <span>👥</span>
            <span class="text-bcz-red text-xs font-bold">NÁŠ TÍM</span>
        </div>

        {{-- Title --}}
        <h1 class="font-display font-bold text-[28px] md:text-[40px] lg:text-[56px] tracking-wide text-center text-white">Naši tréneri</h1>

        {{-- Description --}}
        <p class="text-[#888888] text-lg text-center max-w-[650px] leading-relaxed">
            Zoznámte sa s našimi skúsenými trénermi. Každý z nich má jedinečný prístup a špecializáciu.
        </p>

        {{-- Stats Row --}}
        <div class="flex flex-wrap gap-6 lg:gap-12 pt-6">
            <div class="flex flex-col items-center">
                <span class="font-display font-bold text-[36px] text-bcz-red tracking-wide">8+</span>
                <span class="text-[#888888] text-sm">Trénerov</span>
            </div>
            <div class="flex flex-col items-center">
                <span class="font-display font-bold text-[36px] text-bcz-red tracking-wide">15+</span>
                <span class="text-[#888888] text-sm">Rokov skúseností</span>
            </div>
            <div class="flex flex-col items-center">
                <span class="font-display font-bold text-[36px] text-bcz-red tracking-wide">500+</span>
                <span class="text-[#888888] text-sm">Odtrénovaných hodín</span>
            </div>
        </div>
        </div>
    </section>

    {{-- Filter Section --}}
    <section class="bg-[#0D0D0D] py-10">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
        <div class="flex flex-col gap-6">
            {{-- Search Row --}}
            <div class="bg-[#111111] border border-[#222222] rounded-xl px-5 py-4 flex items-center gap-3">
                <span>🔍</span>
                <input type="text" placeholder="Hľadať trénera..." class="bg-transparent text-white text-sm w-full outline-none placeholder-[#666666]">
            </div>

            {{-- Filter Row --}}
            <div class="flex flex-wrap items-center gap-3 md:gap-4">
                <span class="text-[#888888] text-sm font-semibold">Filtrovať:</span>

                {{-- Špecializácie Dropdown --}}
                <button class="bg-[#111111] border border-[#333333] rounded-lg px-4 py-2.5 flex items-center gap-2 text-white text-sm">
                    <span>Všetky špecializácie</span>
                    <svg class="w-4 h-4 text-[#888888]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m6 9 6 6 6-6"/>
                    </svg>
                </button>

                {{-- Mestá Dropdown --}}
                <button class="bg-[#111111] border border-[#333333] rounded-lg px-4 py-2.5 flex items-center gap-2 text-white text-sm">
                    <span>Všetky mestá</span>
                    <svg class="w-4 h-4 text-[#888888]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m6 9 6 6 6-6"/>
                    </svg>
                </button>

                {{-- Úrovne Dropdown --}}
                <button class="bg-[#111111] border border-[#333333] rounded-lg px-4 py-2.5 flex items-center gap-2 text-white text-sm">
                    <span>Všetky úrovne</span>
                    <svg class="w-4 h-4 text-[#888888]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m6 9 6 6 6-6"/>
                    </svg>
                </button>
            </div>

            {{-- Category Tabs --}}
            <div class="flex flex-wrap gap-3">
                <button class="bg-bcz-red text-white rounded-lg px-5 py-2.5 text-sm font-semibold">Všetci</button>
                <button class="bg-[#111111] border border-[#333333] text-[#CCCCCC] rounded-lg px-5 py-2.5 text-sm">Parkour</button>
                <button class="bg-[#111111] border border-[#333333] text-[#CCCCCC] rounded-lg px-5 py-2.5 text-sm">Street Workout</button>
                <button class="bg-[#111111] border border-[#333333] text-[#CCCCCC] rounded-lg px-5 py-2.5 text-sm">Calisthenics</button>
                <button class="bg-[#111111] border border-[#333333] text-[#CCCCCC] rounded-lg px-5 py-2.5 text-sm">Akrobacia</button>
            </div>
        </div>
        </div>
    </section>

    {{-- Results Section --}}
    <section class="bg-bcz-dark py-10 pb-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
        {{-- Results Header --}}
        <div class="flex justify-between items-center mb-8">
            <span class="text-[#888888] text-sm">Zobrazujem 8 trénerov</span>
            <button class="bg-[#111111] border border-[#333333] rounded-lg px-4 py-2.5 flex items-center gap-2 text-white text-sm">
                <span>Zoradiť podľa</span>
                <svg class="w-4 h-4 text-[#888888]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m6 9 6 6 6-6"/>
                </svg>
            </button>
        </div>

        {{-- Trainers Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- Trainer 1: Dominik Klimek --}}
            <div class="rounded-2xl bg-[#111111] border border-[#222222] overflow-hidden">
                <div class="h-[280px] bg-[#1A1A1A]"></div>
                <div class="p-6 flex flex-col gap-4">
                    <div class="flex justify-between items-center">
                        <span class="text-white text-lg font-bold">Dominik Klimek</span>
                        <span class="text-bcz-red text-xs font-bold">ZAKLADATEĽ</span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-[#1A1A1A] text-[#888888] text-xs rounded-md px-2.5 py-1">Parkour</span>
                        <span class="bg-[#1A1A1A] text-[#888888] text-xs rounded-md px-2.5 py-1">Freerunning</span>
                        <span class="bg-[#1A1A1A] text-[#888888] text-xs rounded-md px-2.5 py-1">Akrobacia</span>
                    </div>
                    <button class="w-full bg-[#0A0A0A] border border-[#333333] rounded-lg py-3.5 text-center text-white text-sm font-semibold flex items-center justify-center gap-2">
                        Zobraziť profil <span>→</span>
                    </button>
                </div>
            </div>

            {{-- Trainer 2: Michal Čečko --}}
            <div class="rounded-2xl bg-[#111111] border border-[#222222] overflow-hidden">
                <div class="h-[280px] bg-[#1A1A1A]"></div>
                <div class="p-6 flex flex-col gap-4">
                    <div class="flex justify-between items-center">
                        <span class="text-white text-lg font-bold">Michal Čečko</span>
                        <span class="text-bcz-red text-xs font-bold">TRÉNER</span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-[#1A1A1A] text-[#888888] text-xs rounded-md px-2.5 py-1">Street Workout</span>
                        <span class="bg-[#1A1A1A] text-[#888888] text-xs rounded-md px-2.5 py-1">Calisthenics</span>
                    </div>
                    <button class="w-full bg-[#0A0A0A] border border-[#333333] rounded-lg py-3.5 text-center text-white text-sm font-semibold flex items-center justify-center gap-2">
                        Zobraziť profil <span>→</span>
                    </button>
                </div>
            </div>

            {{-- Trainer 3: Peter Novák --}}
            <div class="rounded-2xl bg-[#111111] border border-[#222222] overflow-hidden">
                <div class="h-[280px] bg-[#1A1A1A]"></div>
                <div class="p-6 flex flex-col gap-4">
                    <div class="flex justify-between items-center">
                        <span class="text-white text-lg font-bold">Peter Novák</span>
                        <span class="text-bcz-red text-xs font-bold">TRÉNER</span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-[#1A1A1A] text-[#888888] text-xs rounded-md px-2.5 py-1">Parkour</span>
                        <span class="bg-[#1A1A1A] text-[#888888] text-xs rounded-md px-2.5 py-1">Freerunning</span>
                    </div>
                    <button class="w-full bg-[#0A0A0A] border border-[#333333] rounded-lg py-3.5 text-center text-white text-sm font-semibold flex items-center justify-center gap-2">
                        Zobraziť profil <span>→</span>
                    </button>
                </div>
            </div>

            {{-- Trainer 4: Tomáš Horváth --}}
            <div class="rounded-2xl bg-[#111111] border border-[#222222] overflow-hidden">
                <div class="h-[280px] bg-[#1A1A1A]"></div>
                <div class="p-6 flex flex-col gap-4">
                    <div class="flex justify-between items-center">
                        <span class="text-white text-lg font-bold">Tomáš Horváth</span>
                        <span class="text-bcz-red text-xs font-bold">TRÉNER</span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-[#1A1A1A] text-[#888888] text-xs rounded-md px-2.5 py-1">Street Workout</span>
                        <span class="bg-[#1A1A1A] text-[#888888] text-xs rounded-md px-2.5 py-1">Akrobacia</span>
                    </div>
                    <button class="w-full bg-[#0A0A0A] border border-[#333333] rounded-lg py-3.5 text-center text-white text-sm font-semibold flex items-center justify-center gap-2">
                        Zobraziť profil <span>→</span>
                    </button>
                </div>
            </div>

            {{-- Trainer 5: Jakub Kráľ --}}
            <div class="rounded-2xl bg-[#111111] border border-[#222222] overflow-hidden">
                <div class="h-[280px] bg-[#1A1A1A]"></div>
                <div class="p-6 flex flex-col gap-4">
                    <div class="flex justify-between items-center">
                        <span class="text-white text-lg font-bold">Jakub Kráľ</span>
                        <span class="text-bcz-red text-xs font-bold">TRÉNER</span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-[#1A1A1A] text-[#888888] text-xs rounded-md px-2.5 py-1">Calisthenics</span>
                        <span class="bg-[#1A1A1A] text-[#888888] text-xs rounded-md px-2.5 py-1">Mobilita</span>
                    </div>
                    <button class="w-full bg-[#0A0A0A] border border-[#333333] rounded-lg py-3.5 text-center text-white text-sm font-semibold flex items-center justify-center gap-2">
                        Zobraziť profil <span>→</span>
                    </button>
                </div>
            </div>

            {{-- Trainer 6: Matej Varga --}}
            <div class="rounded-2xl bg-[#111111] border border-[#222222] overflow-hidden">
                <div class="h-[280px] bg-[#1A1A1A]"></div>
                <div class="p-6 flex flex-col gap-4">
                    <div class="flex justify-between items-center">
                        <span class="text-white text-lg font-bold">Matej Varga</span>
                        <span class="text-bcz-red text-xs font-bold">TRÉNER</span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-[#1A1A1A] text-[#888888] text-xs rounded-md px-2.5 py-1">Parkour</span>
                        <span class="bg-[#1A1A1A] text-[#888888] text-xs rounded-md px-2.5 py-1">Street Workout</span>
                    </div>
                    <button class="w-full bg-[#0A0A0A] border border-[#333333] rounded-lg py-3.5 text-center text-white text-sm font-semibold flex items-center justify-center gap-2">
                        Zobraziť profil <span>→</span>
                    </button>
                </div>
            </div>
        </div>
        </div>
    </section>
@endsection
