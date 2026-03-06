@extends('layouts.public')

@section('title', 'BCZ Street Workout Cup 2026 - Vysledky - BCZ Club')

@section('content')
    {{-- Hero Section --}}
    <section class="relative w-full h-[400px] md:h-[450px] lg:h-[500px] overflow-hidden bg-[#1A1A1A]">
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-[#0A0A0A]"></div>

        <div class="absolute bottom-0 left-0 right-0 pb-[60px]">
            <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col gap-4">
                {{-- Breadcrumb --}}
                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" class="text-[#666666] text-[11px] font-medium tracking-[2px] hover:text-white transition-colors">DOMOV</a>
                    <span class="text-[#444444] text-[11px]">/</span>
                    <a href="#" class="text-[#666666] text-[11px] font-medium tracking-[2px] hover:text-white transition-colors">SUTAZE</a>
                    <span class="text-[#444444] text-[11px]">/</span>
                    <span class="text-bcz-red text-[11px] font-medium tracking-[2px]">VYSLEDKY</span>
                </div>

                {{-- Badge --}}
                <div class="flex items-center gap-3">
                    <span class="bg-bcz-red text-white text-xs font-bold px-3.5 py-1.5 rounded-md w-fit">PREBIEHA</span>
                    <span class="text-[#888888] text-sm">15. - 16. marca 2026</span>
                </div>

                {{-- Title --}}
                <h1 class="font-display font-bold text-[28px] md:text-[42px] lg:text-[56px] tracking-wide text-white">BCZ STREET WORKOUT CUP 2026</h1>

                {{-- Subtitle --}}
                <p class="text-[#888888] text-base max-w-[600px]">Najvacsia street workout sutaz na Slovensku. Kvalifikacia a 1v1 battle format.</p>
            </div>
        </div>
    </section>

    {{-- Tab Bar --}}
    <section class="bg-[#111111] border-b border-[#222222] sticky top-0 z-30">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex gap-0 overflow-x-auto">
                <a href="{{ route('sutaz.popis') }}" class="px-6 py-4 text-[#888888] text-sm font-semibold tracking-wider border-b-2 border-transparent hover:text-white transition-colors whitespace-nowrap">POPIS</a>
                <a href="{{ route('sutaz.harmonogram') }}" class="px-6 py-4 text-[#888888] text-sm font-semibold tracking-wider border-b-2 border-transparent hover:text-white transition-colors whitespace-nowrap">HARMONOGRAM</a>
                <a href="{{ route('sutaz.vysledky') }}" class="px-6 py-4 text-white text-sm font-semibold tracking-wider border-b-2 border-bcz-red whitespace-nowrap">VYSLEDKY</a>
                <a href="{{ route('sutaz.registracia') }}" class="px-6 py-4 text-[#888888] text-sm font-semibold tracking-wider border-b-2 border-transparent hover:text-white transition-colors whitespace-nowrap">REGISTRACIA</a>
            </div>
        </div>
    </section>

    {{-- Info Strip --}}
    <section class="bg-bcz-dark py-8">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                {{-- Card 1 --}}
                <div class="bg-[#111111] border border-[#222222] rounded-xl p-5 flex flex-col gap-2">
                    <span class="text-[#666666] text-[11px] font-medium tracking-[2px]">DATUM</span>
                    <span class="text-white text-sm font-semibold">15. - 16. mar 2026</span>
                </div>
                {{-- Card 2 --}}
                <div class="bg-[#111111] border border-[#222222] rounded-xl p-5 flex flex-col gap-2">
                    <span class="text-[#666666] text-[11px] font-medium tracking-[2px]">MIESTO</span>
                    <span class="text-white text-sm font-semibold">Bratislava</span>
                </div>
                {{-- Card 3 --}}
                <div class="bg-[#111111] border border-[#222222] rounded-xl p-5 flex flex-col gap-2">
                    <span class="text-[#666666] text-[11px] font-medium tracking-[2px]">FORMAT</span>
                    <span class="text-white text-sm font-semibold">Kvalifikacia + 1v1</span>
                </div>
                {{-- Card 4 --}}
                <div class="bg-[#111111] border border-[#222222] rounded-xl p-5 flex flex-col gap-2">
                    <span class="text-[#666666] text-[11px] font-medium tracking-[2px]">SUTAZIACI</span>
                    <span class="text-white text-sm font-semibold">48 atletov</span>
                </div>
                {{-- Card 5 --}}
                <div class="bg-[#111111] border border-[#222222] rounded-xl p-5 flex flex-col gap-2">
                    <span class="text-[#666666] text-[11px] font-medium tracking-[2px]">STATUS</span>
                    <span class="text-bcz-red text-sm font-semibold">Prebieha</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Results Sub Nav --}}
    <section class="bg-bcz-dark py-10">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col gap-6">
                <h2 class="font-display font-bold text-[32px] tracking-wide text-white">VYSLEDKY SUTAZE</h2>
                <p class="text-[#888888] text-base max-w-[700px]">Sutaz prebieha vo formate kvalifikacnych kol nasledovanych 1v1 battle vyraďovacim systémom. Top 8 z kvalifikacie postupuje do battle fazy.</p>

                {{-- Pill Tabs --}}
                <div class="flex gap-3 flex-wrap">
                    <button class="bg-bcz-red text-white text-sm font-semibold px-6 py-2.5 rounded-full">Kvalifikacia</button>
                    <button class="bg-[#111111] border border-[#333333] text-[#888888] text-sm font-semibold px-6 py-2.5 rounded-full hover:text-white hover:border-[#555555] transition-colors">1v1 Battle</button>
                    <button class="bg-[#111111] border border-[#333333] text-[#888888] text-sm font-semibold px-6 py-2.5 rounded-full hover:text-white hover:border-[#555555] transition-colors">Celkove poradie</button>
                </div>
            </div>
        </div>
    </section>

    {{-- Qualification Results --}}
    <section class="bg-[#0D0D0D] py-12">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col gap-8">
                {{-- Header --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <h3 class="font-display font-bold text-[24px] tracking-wide text-white">KVALIFIKACNE VYSLEDKY</h3>
                    <select class="bg-[#111111] border border-[#333333] text-white text-sm rounded-lg px-4 py-2.5 focus:outline-none focus:border-bcz-red">
                        <option>Muzi do 80kg</option>
                        <option>Muzi nad 80kg</option>
                        <option>Zeny</option>
                        <option>Juniori</option>
                    </select>
                </div>

                {{-- Results Table --}}
                <div class="overflow-x-auto">
                    <div class="bg-[#111111] rounded-xl overflow-hidden min-w-[700px]">
                        {{-- Table Header --}}
                        <div class="bg-[#1A1A1A] grid grid-cols-[60px_1fr_100px_100px_100px_100px_120px] gap-0 px-6 py-4">
                            <span class="text-[#666666] text-[11px] font-bold tracking-[2px]">#</span>
                            <span class="text-[#666666] text-[11px] font-bold tracking-[2px]">MENO</span>
                            <span class="text-[#666666] text-[11px] font-bold tracking-[2px] text-center">KOLO 1</span>
                            <span class="text-[#666666] text-[11px] font-bold tracking-[2px] text-center">KOLO 2</span>
                            <span class="text-[#666666] text-[11px] font-bold tracking-[2px] text-center">KOLO 3</span>
                            <span class="text-[#666666] text-[11px] font-bold tracking-[2px] text-center">PRIEMER</span>
                            <span class="text-[#666666] text-[11px] font-bold tracking-[2px] text-right">STATUS</span>
                        </div>

                        {{-- Row 1 - 1st place --}}
                        <div class="grid grid-cols-[60px_1fr_100px_100px_100px_100px_120px] gap-0 px-6 py-4 border-t border-[#1A1A1A] bg-[#111111]">
                            <span class="text-[#FFD700] text-sm font-bold">1.</span>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-[#1A1A1A] shrink-0"></div>
                                <span class="text-white text-sm font-semibold">Michal Cecko</span>
                            </div>
                            <span class="text-white text-sm text-center">9.2</span>
                            <span class="text-white text-sm text-center">9.5</span>
                            <span class="text-white text-sm text-center">9.4</span>
                            <span class="text-bcz-red text-sm font-bold text-center">9.37</span>
                            <div class="flex justify-end">
                                <span class="bg-[#22C55E]/20 text-[#22C55E] text-[11px] font-bold px-3 py-1 rounded">Postup</span>
                            </div>
                        </div>

                        {{-- Row 2 - 2nd place --}}
                        <div class="grid grid-cols-[60px_1fr_100px_100px_100px_100px_120px] gap-0 px-6 py-4 border-t border-[#1A1A1A] bg-[#0F0F0F]">
                            <span class="text-[#C0C0C0] text-sm font-bold">2.</span>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-[#1A1A1A] shrink-0"></div>
                                <span class="text-white text-sm font-semibold">Dominik Klimek</span>
                            </div>
                            <span class="text-white text-sm text-center">9.0</span>
                            <span class="text-white text-sm text-center">9.3</span>
                            <span class="text-white text-sm text-center">9.1</span>
                            <span class="text-bcz-red text-sm font-bold text-center">9.13</span>
                            <div class="flex justify-end">
                                <span class="bg-[#22C55E]/20 text-[#22C55E] text-[11px] font-bold px-3 py-1 rounded">Postup</span>
                            </div>
                        </div>

                        {{-- Row 3 - 3rd place --}}
                        <div class="grid grid-cols-[60px_1fr_100px_100px_100px_100px_120px] gap-0 px-6 py-4 border-t border-[#1A1A1A] bg-[#111111]">
                            <span class="text-[#CD7F32] text-sm font-bold">3.</span>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-[#1A1A1A] shrink-0"></div>
                                <span class="text-white text-sm font-semibold">Peter Novak</span>
                            </div>
                            <span class="text-white text-sm text-center">8.8</span>
                            <span class="text-white text-sm text-center">9.1</span>
                            <span class="text-white text-sm text-center">8.9</span>
                            <span class="text-bcz-red text-sm font-bold text-center">8.93</span>
                            <div class="flex justify-end">
                                <span class="bg-[#22C55E]/20 text-[#22C55E] text-[11px] font-bold px-3 py-1 rounded">Postup</span>
                            </div>
                        </div>

                        {{-- Row 4 --}}
                        <div class="grid grid-cols-[60px_1fr_100px_100px_100px_100px_120px] gap-0 px-6 py-4 border-t border-[#1A1A1A] bg-[#0F0F0F]">
                            <span class="text-[#888888] text-sm font-bold">4.</span>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-[#1A1A1A] shrink-0"></div>
                                <span class="text-white text-sm font-semibold">Tomas Horvath</span>
                            </div>
                            <span class="text-white text-sm text-center">8.5</span>
                            <span class="text-white text-sm text-center">8.7</span>
                            <span class="text-white text-sm text-center">8.9</span>
                            <span class="text-bcz-red text-sm font-bold text-center">8.70</span>
                            <div class="flex justify-end">
                                <span class="bg-[#22C55E]/20 text-[#22C55E] text-[11px] font-bold px-3 py-1 rounded">Postup</span>
                            </div>
                        </div>

                        {{-- Row 5 --}}
                        <div class="grid grid-cols-[60px_1fr_100px_100px_100px_100px_120px] gap-0 px-6 py-4 border-t border-[#1A1A1A] bg-[#111111]">
                            <span class="text-[#888888] text-sm font-bold">5.</span>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-[#1A1A1A] shrink-0"></div>
                                <span class="text-white text-sm font-semibold">Jakub Kovac</span>
                            </div>
                            <span class="text-white text-sm text-center">8.3</span>
                            <span class="text-white text-sm text-center">8.0</span>
                            <span class="text-white text-sm text-center">8.5</span>
                            <span class="text-white text-sm font-bold text-center">8.27</span>
                            <div class="flex justify-end">
                                <span class="bg-[#666666]/20 text-[#666666] text-[11px] font-bold px-3 py-1 rounded">Nepostupil</span>
                            </div>
                        </div>

                        {{-- Row 6 --}}
                        <div class="grid grid-cols-[60px_1fr_100px_100px_100px_100px_120px] gap-0 px-6 py-4 border-t border-[#1A1A1A] bg-[#0F0F0F]">
                            <span class="text-[#888888] text-sm font-bold">6.</span>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-[#1A1A1A] shrink-0"></div>
                                <span class="text-white text-sm font-semibold">Martin Stastny</span>
                            </div>
                            <span class="text-white text-sm text-center">7.9</span>
                            <span class="text-white text-sm text-center">8.2</span>
                            <span class="text-white text-sm text-center">7.8</span>
                            <span class="text-white text-sm font-bold text-center">7.97</span>
                            <div class="flex justify-end">
                                <span class="bg-[#666666]/20 text-[#666666] text-[11px] font-bold px-3 py-1 rounded">Nepostupil</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Battle Bracket --}}
    <section class="bg-bcz-dark py-12">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col gap-8">
                <h3 class="font-display font-bold text-[24px] tracking-wide text-white">BATTLE PAVUK</h3>

                {{-- Bracket Visualization --}}
                <div class="overflow-x-auto">
                    <div class="flex gap-8 lg:gap-12 items-center min-w-[800px] py-4">

                        {{-- Quarterfinals --}}
                        <div class="flex flex-col gap-6 shrink-0">
                            <span class="text-[#666666] text-[11px] font-bold tracking-[2px] mb-2">STVRTFINALE</span>

                            {{-- Match 1 --}}
                            <div class="flex flex-col gap-1">
                                <div class="bg-[#111111] border border-bcz-red rounded-lg px-4 py-3 flex justify-between items-center w-[220px]">
                                    <span class="text-white text-sm font-semibold">Michal Cecko</span>
                                    <span class="text-bcz-red text-sm font-bold">9.5</span>
                                </div>
                                <div class="bg-[#111111] border border-[#222222] rounded-lg px-4 py-3 flex justify-between items-center w-[220px]">
                                    <span class="text-[#666666] text-sm">Martin Stastny</span>
                                    <span class="text-[#666666] text-sm">7.2</span>
                                </div>
                            </div>

                            {{-- Match 2 --}}
                            <div class="flex flex-col gap-1">
                                <div class="bg-[#111111] border border-bcz-red rounded-lg px-4 py-3 flex justify-between items-center w-[220px]">
                                    <span class="text-white text-sm font-semibold">Dominik Klimek</span>
                                    <span class="text-bcz-red text-sm font-bold">8.9</span>
                                </div>
                                <div class="bg-[#111111] border border-[#222222] rounded-lg px-4 py-3 flex justify-between items-center w-[220px]">
                                    <span class="text-[#666666] text-sm">Jakub Kovac</span>
                                    <span class="text-[#666666] text-sm">8.1</span>
                                </div>
                            </div>

                            {{-- Match 3 --}}
                            <div class="flex flex-col gap-1">
                                <div class="bg-[#111111] border border-bcz-red rounded-lg px-4 py-3 flex justify-between items-center w-[220px]">
                                    <span class="text-white text-sm font-semibold">Peter Novak</span>
                                    <span class="text-bcz-red text-sm font-bold">8.7</span>
                                </div>
                                <div class="bg-[#111111] border border-[#222222] rounded-lg px-4 py-3 flex justify-between items-center w-[220px]">
                                    <span class="text-[#666666] text-sm">Filip Baran</span>
                                    <span class="text-[#666666] text-sm">8.0</span>
                                </div>
                            </div>

                            {{-- Match 4 --}}
                            <div class="flex flex-col gap-1">
                                <div class="bg-[#111111] border border-bcz-red rounded-lg px-4 py-3 flex justify-between items-center w-[220px]">
                                    <span class="text-white text-sm font-semibold">Tomas Horvath</span>
                                    <span class="text-bcz-red text-sm font-bold">8.8</span>
                                </div>
                                <div class="bg-[#111111] border border-[#222222] rounded-lg px-4 py-3 flex justify-between items-center w-[220px]">
                                    <span class="text-[#666666] text-sm">Samuel Kadlec</span>
                                    <span class="text-[#666666] text-sm">7.9</span>
                                </div>
                            </div>
                        </div>

                        {{-- Connector --}}
                        <div class="flex flex-col items-center justify-center gap-2 shrink-0">
                            <div class="w-8 h-px bg-[#333333]"></div>
                            <div class="w-8 h-px bg-[#333333]"></div>
                            <div class="w-8 h-px bg-[#333333]"></div>
                            <div class="w-8 h-px bg-[#333333]"></div>
                        </div>

                        {{-- Semifinals --}}
                        <div class="flex flex-col gap-6 shrink-0">
                            <span class="text-[#666666] text-[11px] font-bold tracking-[2px] mb-2">SEMIFINALE</span>

                            {{-- Semi 1 --}}
                            <div class="flex flex-col gap-1">
                                <div class="bg-[#111111] border border-bcz-red rounded-lg px-4 py-3 flex justify-between items-center w-[220px]">
                                    <span class="text-white text-sm font-semibold">Michal Cecko</span>
                                    <span class="text-bcz-red text-sm font-bold">9.3</span>
                                </div>
                                <div class="bg-[#111111] border border-[#222222] rounded-lg px-4 py-3 flex justify-between items-center w-[220px]">
                                    <span class="text-[#666666] text-sm">Dominik Klimek</span>
                                    <span class="text-[#666666] text-sm">8.8</span>
                                </div>
                            </div>

                            {{-- Semi 2 --}}
                            <div class="flex flex-col gap-1 mt-[72px]">
                                <div class="bg-[#111111] border border-[#222222] rounded-lg px-4 py-3 flex justify-between items-center w-[220px]">
                                    <span class="text-[#666666] text-sm">Peter Novak</span>
                                    <span class="text-[#666666] text-sm">8.5</span>
                                </div>
                                <div class="bg-[#111111] border border-bcz-red rounded-lg px-4 py-3 flex justify-between items-center w-[220px]">
                                    <span class="text-white text-sm font-semibold">Tomas Horvath</span>
                                    <span class="text-bcz-red text-sm font-bold">8.9</span>
                                </div>
                            </div>
                        </div>

                        {{-- Connector --}}
                        <div class="flex flex-col items-center justify-center gap-2 shrink-0">
                            <div class="w-8 h-px bg-[#333333]"></div>
                            <div class="w-8 h-px bg-[#333333]"></div>
                        </div>

                        {{-- Final --}}
                        <div class="flex flex-col gap-6 shrink-0">
                            <span class="text-bcz-red text-[11px] font-bold tracking-[2px] mb-2">FINALE</span>

                            {{-- Final Match --}}
                            <div class="flex flex-col gap-1 mt-[72px]">
                                <div class="bg-[#111111] border-2 border-bcz-red rounded-lg px-4 py-3 flex justify-between items-center w-[220px]">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-[#FFD700]" fill="currentColor" viewBox="0 0 24 24"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6m12 5h1.5a2.5 2.5 0 0 0 0-5H18M4 22h16M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22m7-7.34V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>
                                        <span class="text-white text-sm font-semibold">Michal Cecko</span>
                                    </div>
                                    <span class="text-bcz-red text-sm font-bold">9.6</span>
                                </div>
                                <div class="bg-[#111111] border border-[#222222] rounded-lg px-4 py-3 flex justify-between items-center w-[220px]">
                                    <span class="text-[#666666] text-sm">Tomas Horvath</span>
                                    <span class="text-[#666666] text-sm">8.7</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Legend --}}
                <div class="flex items-center gap-6 mt-2">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded border-2 border-bcz-red"></div>
                        <span class="text-[#888888] text-xs">Vitaz zapasu</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded border border-[#222222]"></div>
                        <span class="text-[#888888] text-xs">Porazeny</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
