@extends('layouts.public')

@section('title', 'Archív podujatí - BCZ Club')

@section('content')
    {{-- Hero Section --}}
    <section class="bg-bcz-dark pt-[120px] pb-[60px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col items-center gap-6">
        {{-- Red Label with Lines --}}
        <div class="flex items-center gap-3">
            <div class="w-10 h-0.5 bg-bcz-red"></div>
            <span class="text-bcz-red text-[12px] font-bold tracking-[2px]">PORTFÓLIO</span>
            <div class="w-10 h-0.5 bg-bcz-red"></div>
        </div>

        {{-- Title --}}
        <h1 class="font-display font-bold text-[28px] md:text-[40px] lg:text-[56px] tracking-wide text-center text-white">Archív podujatí</h1>

        {{-- Description --}}
        <p class="text-[#888888] text-[18px] text-center leading-relaxed max-w-[700px]">
            Prehľad všetkých našich vystúpení, prednášok a workshopov. Filtrujte podľa kategórie, roku alebo mesta.
        </p>

        {{-- Stats Row --}}
        <div class="flex flex-wrap gap-6 lg:gap-12 pt-6">
            <div class="flex flex-col items-center">
                <span class="font-display font-bold text-[36px] text-bcz-red tracking-wide">50+</span>
                <span class="text-[#888888] text-sm">Podujatí</span>
            </div>
            <div class="flex flex-col items-center">
                <span class="font-display font-bold text-[36px] text-bcz-red tracking-wide">20+</span>
                <span class="text-[#888888] text-sm">Miest</span>
            </div>
            <div class="flex flex-col items-center">
                <span class="font-display font-bold text-[36px] text-bcz-red tracking-wide">5</span>
                <span class="text-[#888888] text-sm">Rokov</span>
            </div>
        </div>
        </div>
    </section>

    {{-- Filter Section --}}
    <section class="bg-[#0D0D0D] py-10">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
        <div class="flex flex-wrap items-center gap-3 md:gap-4">
            <span class="text-white text-sm font-semibold">Filtrovať:</span>

            {{-- Category Dropdown --}}
            <button class="bg-[#111111] border border-[#333333] rounded-lg px-4 py-2.5 flex items-center gap-2 text-white text-sm">
                <span>Všetky kategórie</span>
                <svg class="w-4 h-4 text-[#888888]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m6 9 6 6 6-6"/>
                </svg>
            </button>

            {{-- Year Dropdown --}}
            <button class="bg-[#111111] border border-[#333333] rounded-lg px-4 py-2.5 flex items-center gap-2 text-white text-sm">
                <span>Všetky roky</span>
                <svg class="w-4 h-4 text-[#888888]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m6 9 6 6 6-6"/>
                </svg>
            </button>

            {{-- City Dropdown --}}
            <button class="bg-[#111111] border border-[#333333] rounded-lg px-4 py-2.5 flex items-center gap-2 text-white text-sm">
                <span>Všetky mestá</span>
                <svg class="w-4 h-4 text-[#888888]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m6 9 6 6 6-6"/>
                </svg>
            </button>

            {{-- Search Input --}}
            <input type="text" placeholder="Hľadať podujatie..." class="flex-1 bg-[#111111] border border-[#333333] rounded-lg px-4 py-2.5 text-white text-sm placeholder-[#666666] focus:border-bcz-red focus:outline-none transition-colors">
        </div>
        </div>
    </section>

    {{-- Events Grid Section --}}
    <section class="bg-bcz-dark pt-10 pb-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- Row 1 --}}

            {{-- Card 1: Grape Festival --}}
            <div class="rounded-2xl bg-[#111111] overflow-hidden flex flex-col">
                <div class="h-[180px] bg-[#1A1A1A]"></div>
                <div class="p-5 flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <span class="bg-bcz-red/20 text-bcz-red text-xs px-2.5 py-1 rounded">Vystúpenie</span>
                        <span class="text-[#666666] text-xs">2024</span>
                    </div>
                    <h3 class="text-white text-lg font-bold">Grape Festival</h3>
                    <p class="text-[#888888] text-[13px] leading-relaxed">Hlavné pódium na najväčšom slovenskom festivale s 30 000+ návštevníkmi.</p>
                    <div class="flex items-center gap-1.5 text-[#666666] text-xs">
                        <span>&#x1F4CD;</span>
                        <span>Piešťany</span>
                    </div>
                </div>
            </div>

            {{-- Card 2: Gymnázium Metodova --}}
            <div class="rounded-2xl bg-[#111111] overflow-hidden flex flex-col">
                <div class="h-[180px] bg-[#1A1A1A]"></div>
                <div class="p-5 flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <span class="bg-[#3B82F6]/20 text-[#3B82F6] text-xs px-2.5 py-1 rounded">Prednáška</span>
                        <span class="text-[#666666] text-xs">2024</span>
                    </div>
                    <h3 class="text-white text-lg font-bold">Gymnázium Metodova</h3>
                    <p class="text-[#888888] text-[13px] leading-relaxed">Motivačná prednáška pre 200+ študentov o nastavení mysle a vytrvalosti.</p>
                    <div class="flex items-center gap-1.5 text-[#666666] text-xs">
                        <span>&#x1F4CD;</span>
                        <span>Bratislava</span>
                    </div>
                </div>
            </div>

            {{-- Card 3: Fitness Factory --}}
            <div class="rounded-2xl bg-[#111111] overflow-hidden flex flex-col">
                <div class="h-[180px] bg-[#1A1A1A]"></div>
                <div class="p-5 flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <span class="bg-[#22C55E]/20 text-[#22C55E] text-xs px-2.5 py-1 rounded">Workshop</span>
                        <span class="text-[#666666] text-xs">2024</span>
                    </div>
                    <h3 class="text-white text-lg font-bold">Fitness Factory</h3>
                    <p class="text-[#888888] text-[13px] leading-relaxed">Kurz stojky a základov akrobacie pre členov fitness centra.</p>
                    <div class="flex items-center gap-1.5 text-[#666666] text-xs">
                        <span>&#x1F4CD;</span>
                        <span>Bratislava</span>
                    </div>
                </div>
            </div>

            {{-- Row 2 --}}

            {{-- Card 4: Pohoda Festival --}}
            <div class="rounded-2xl bg-[#111111] overflow-hidden flex flex-col">
                <div class="h-[180px] bg-[#1A1A1A]"></div>
                <div class="p-5 flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <span class="bg-bcz-red/20 text-bcz-red text-xs px-2.5 py-1 rounded">Vystúpenie</span>
                        <span class="text-[#666666] text-xs">2024</span>
                    </div>
                    <h3 class="text-white text-lg font-bold">Pohoda Festival</h3>
                    <p class="text-[#888888] text-[13px] leading-relaxed">Energická show na druhom najväčšom festivale na Slovensku.</p>
                    <div class="flex items-center gap-1.5 text-[#666666] text-xs">
                        <span>&#x1F4CD;</span>
                        <span>Trenčín</span>
                    </div>
                </div>
            </div>

            {{-- Card 5: SOŠ Čadca --}}
            <div class="rounded-2xl bg-[#111111] overflow-hidden flex flex-col">
                <div class="h-[180px] bg-[#1A1A1A]"></div>
                <div class="p-5 flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <span class="bg-[#3B82F6]/20 text-[#3B82F6] text-xs px-2.5 py-1 rounded">Prednáška</span>
                        <span class="text-[#666666] text-xs">2024</span>
                    </div>
                    <h3 class="text-white text-lg font-bold">SOŠ Čadca</h3>
                    <p class="text-[#888888] text-[13px] leading-relaxed">Inšpiratívna prednáška pre 150+ študentov o prekonávaní prekážok.</p>
                    <div class="flex items-center gap-1.5 text-[#666666] text-xs">
                        <span>&#x1F4CD;</span>
                        <span>Čadca</span>
                    </div>
                </div>
            </div>

            {{-- Card 6: Kurz Parkouru --}}
            <div class="rounded-2xl bg-[#111111] overflow-hidden flex flex-col">
                <div class="h-[180px] bg-[#1A1A1A]"></div>
                <div class="p-5 flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <span class="bg-[#22C55E]/20 text-[#22C55E] text-xs px-2.5 py-1 rounded">Workshop</span>
                        <span class="text-[#666666] text-xs">2024</span>
                    </div>
                    <h3 class="text-white text-lg font-bold">Kurz Parkouru</h3>
                    <p class="text-[#888888] text-[13px] leading-relaxed">Základy parkouru pre začiatočníkov v mestskom prostredí.</p>
                    <div class="flex items-center gap-1.5 text-[#666666] text-xs">
                        <span>&#x1F4CD;</span>
                        <span>Žilina</span>
                    </div>
                </div>
            </div>

            {{-- Row 3 --}}

            {{-- Card 7: Vianočné trhy Žilina --}}
            <div class="rounded-2xl bg-[#111111] overflow-hidden flex flex-col">
                <div class="h-[180px] bg-[#1A1A1A]"></div>
                <div class="p-5 flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <span class="bg-bcz-red/20 text-bcz-red text-xs px-2.5 py-1 rounded">Vystúpenie</span>
                        <span class="text-[#666666] text-xs">2024</span>
                    </div>
                    <h3 class="text-white text-lg font-bold">Vianočné trhy Žilina</h3>
                    <p class="text-[#888888] text-[13px] leading-relaxed">Vianočná akrobatická show v centre mesta.</p>
                    <div class="flex items-center gap-1.5 text-[#666666] text-xs">
                        <span>&#x1F4CD;</span>
                        <span>Žilina</span>
                    </div>
                </div>
            </div>

            {{-- Card 8: Stredná škola CA --}}
            <div class="rounded-2xl bg-[#111111] overflow-hidden flex flex-col">
                <div class="h-[180px] bg-[#1A1A1A]"></div>
                <div class="p-5 flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <span class="bg-[#3B82F6]/20 text-[#3B82F6] text-xs px-2.5 py-1 rounded">Prednáška</span>
                        <span class="text-[#666666] text-xs">2024</span>
                    </div>
                    <h3 class="text-white text-lg font-bold">Stredná škola CA</h3>
                    <p class="text-[#888888] text-[13px] leading-relaxed">Prednáška o disciplíne a zdravom životnom štýle.</p>
                    <div class="flex items-center gap-1.5 text-[#666666] text-xs">
                        <span>&#x1F4CD;</span>
                        <span>Čadca</span>
                    </div>
                </div>
            </div>

            {{-- Card 9: Teambuilding - IBM --}}
            <div class="rounded-2xl bg-[#111111] overflow-hidden flex flex-col">
                <div class="h-[180px] bg-[#1A1A1A]"></div>
                <div class="p-5 flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <span class="bg-[#22C55E]/20 text-[#22C55E] text-xs px-2.5 py-1 rounded">Workshop</span>
                        <span class="text-[#666666] text-xs">2024</span>
                    </div>
                    <h3 class="text-white text-lg font-bold">Teambuilding - IBM</h3>
                    <p class="text-[#888888] text-[13px] leading-relaxed">Firemný teambuilding s prvkami street workoutu.</p>
                    <div class="flex items-center gap-1.5 text-[#666666] text-xs">
                        <span>&#x1F4CD;</span>
                        <span>Bratislava</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="flex justify-center gap-2 pt-6">
            <span class="w-10 h-10 bg-bcz-red rounded-lg flex items-center justify-center text-white text-sm font-bold">1</span>
            <span class="w-10 h-10 bg-[#111111] border border-[#333333] rounded-lg flex items-center justify-center text-[#888888] text-sm cursor-pointer hover:border-[#555555] transition-colors">2</span>
            <span class="w-10 h-10 bg-[#111111] border border-[#333333] rounded-lg flex items-center justify-center text-[#888888] text-sm cursor-pointer hover:border-[#555555] transition-colors">3</span>
            <span class="w-10 h-10 bg-[#111111] border border-[#333333] rounded-lg flex items-center justify-center text-[#888888] cursor-pointer hover:border-[#555555] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m9 18 6-6-6-6"/>
                </svg>
            </span>
        </div>
        </div>
    </section>
@endsection
