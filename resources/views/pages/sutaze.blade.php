@extends('layouts.public')

@section('title', 'Sutaze - BCZ Club')

@section('content')
    {{-- Hero Section --}}
    <section class="relative w-full h-[500px] md:h-[550px] lg:h-[600px] overflow-hidden">
        <div class="absolute inset-0 bg-[#1A1A1A]"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-[#0A0A0A] via-[#0A0A0ACC] to-[#0A0A0A]"></div>

        <div class="relative w-full h-full flex flex-col items-center justify-center gap-6 max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 pt-[80px]">
            {{-- Badge --}}
            <div class="flex items-center gap-2 bg-transparent border border-bcz-red/30 rounded-md px-4 py-2 w-fit">
                <div class="w-2 h-2 rounded-full bg-bcz-red"></div>
                <span class="text-bcz-red text-[11px] font-bold tracking-[2px]">SUTAZNY TIM BCZ</span>
            </div>

            {{-- Title --}}
            <div class="flex flex-col items-center">
                <h1 class="font-display font-bold text-[36px] md:text-[56px] lg:text-[80px] leading-[0.95] tracking-wide text-white">BOJUJEME</h1>
                <h1 class="font-display font-bold text-[36px] md:text-[56px] lg:text-[80px] leading-[0.95] tracking-wide text-bcz-red">ZA VITAZSTVO</h1>
            </div>

            {{-- Subtitle --}}
            <p class="text-[#AAAAAA] text-lg md:text-xl text-center max-w-[700px]">
                Reprezentujeme Slovensko na medzinarodnych sutaziach v parkour freestyle, speed a skill competition.
            </p>

            {{-- CTA Buttons --}}
            <div class="flex flex-col sm:flex-row items-center gap-4 mt-2">
                <a href="#sutaze" class="bg-bcz-red text-white text-sm font-bold tracking-wider px-9 py-[16px] rounded-lg hover:bg-red-700 transition-colors">
                    NAJBLIZISIE SUTAZE
                </a>
                <a href="#vysledky" class="border border-white text-white text-sm font-bold tracking-wider px-9 py-[16px] rounded-lg hover:bg-white/10 transition-colors">
                    VYSLEDKY
                </a>
            </div>
        </div>

        {{-- Stats Bar --}}
        <div class="absolute bottom-0 left-0 right-0 bg-[#0D0D0D]/80 border-t border-[#222222]">
            <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 py-6">
                <div class="flex flex-wrap justify-around items-center gap-6">
                    <div class="flex items-center gap-3">
                        <span class="font-display font-bold text-[32px] md:text-[40px] text-bcz-red">50+</span>
                        <span class="text-bcz-muted text-[13px] font-medium">sutazi</span>
                    </div>
                    <div class="w-px h-10 bg-[#222222] hidden md:block"></div>
                    <div class="flex items-center gap-3">
                        <span class="font-display font-bold text-[32px] md:text-[40px] text-bcz-red">25+</span>
                        <span class="text-bcz-muted text-[13px] font-medium">medaili</span>
                    </div>
                    <div class="w-px h-10 bg-[#222222] hidden md:block"></div>
                    <div class="flex items-center gap-3">
                        <span class="font-display font-bold text-[32px] md:text-[40px] text-bcz-red">15</span>
                        <span class="text-bcz-muted text-[13px] font-medium">atletov</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Upcoming Events Section --}}
    <section id="sutaze" class="bg-bcz-dark py-[100px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col gap-12">
                {{-- Header --}}
                <div class="flex flex-col items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                        <span class="text-bcz-red text-xs font-bold tracking-[3px]">NADCHADZAJUCE</span>
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                    </div>
                    <h2 class="font-display font-bold text-[24px] md:text-[36px] lg:text-[48px] tracking-wide text-white text-center">NAJBLIZISIE SUTAZE</h2>
                    <p class="text-[#666666] text-lg text-center max-w-[600px]">Prehladajte nadchadzajuce sutaze, na ktorych nas uvidite</p>
                </div>

                {{-- Filter Area --}}
                <div class="bg-[#0D0D0D] border border-[#222222] rounded-2xl p-6 flex flex-col gap-5">
                    {{-- Filter Dropdowns Row --}}
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1 bg-[#141414] border border-[#222222] rounded-lg px-4 py-3 flex items-center justify-between cursor-pointer">
                            <span class="text-[#666666] text-sm">Vsetky discipliny</span>
                            <svg class="w-4 h-4 text-[#666666]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                        </div>
                        <div class="flex-1 bg-[#141414] border border-[#222222] rounded-lg px-4 py-3 flex items-center justify-between cursor-pointer">
                            <span class="text-[#666666] text-sm">Vsetky krajiny</span>
                            <svg class="w-4 h-4 text-[#666666]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                        </div>
                        <div class="flex-1 bg-[#141414] border border-[#222222] rounded-lg px-4 py-3 flex items-center justify-between cursor-pointer">
                            <span class="text-[#666666] text-sm">Rok 2026</span>
                            <svg class="w-4 h-4 text-[#666666]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                        </div>
                    </div>

                    {{-- Category Pills Row --}}
                    <div class="flex flex-wrap gap-3">
                        <button class="bg-bcz-red text-white text-[12px] font-bold tracking-wider px-5 py-2.5 rounded-full">VSETKY</button>
                        <button class="bg-[#141414] border border-[#222222] text-[#888888] text-[12px] font-bold tracking-wider px-5 py-2.5 rounded-full hover:border-bcz-red hover:text-white transition-colors">FREESTYLE</button>
                        <button class="bg-[#141414] border border-[#222222] text-[#888888] text-[12px] font-bold tracking-wider px-5 py-2.5 rounded-full hover:border-bcz-red hover:text-white transition-colors">SPEED</button>
                        <button class="bg-[#141414] border border-[#222222] text-[#888888] text-[12px] font-bold tracking-wider px-5 py-2.5 rounded-full hover:border-bcz-red hover:text-white transition-colors">SKILL</button>
                    </div>
                </div>

                {{-- Events List --}}
                <div class="flex flex-col gap-4">
                    {{-- Event 1 --}}
                    <div class="bg-[#111111] border border-[#222222] rounded-2xl overflow-hidden flex flex-col md:flex-row">
                        {{-- Date Column --}}
                        <div class="w-full md:w-[140px] bg-bcz-red flex flex-col items-center justify-center py-6 md:py-0 shrink-0">
                            <span class="font-display font-bold text-[36px] leading-none text-white">15</span>
                            <span class="text-white/80 text-[13px] font-semibold tracking-wider">MAR 2026</span>
                        </div>
                        {{-- Content Column --}}
                        <div class="flex-1 flex flex-col gap-3 p-6 md:p-8">
                            <h3 class="font-display font-bold text-[24px] md:text-[28px] tracking-wide text-white">World Parkour Championship</h3>
                            <div class="flex flex-wrap gap-2">
                                <span class="bg-bcz-red/[0.12] text-bcz-red text-[10px] font-bold tracking-wider px-3 py-1.5 rounded">FREESTYLE</span>
                                <span class="bg-[#222222] text-[#AAAAAA] text-[10px] font-bold tracking-wider px-3 py-1.5 rounded">SPEED</span>
                            </div>
                            <div class="flex items-center gap-2 text-[#888888] text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>
                                <span>Montpellier, Francuzsko</span>
                            </div>
                        </div>
                        {{-- CTA Column --}}
                        <div class="flex items-center px-6 pb-6 md:pb-0 md:pr-8">
                            <a href="#" class="bg-bcz-red text-white text-[12px] font-bold tracking-wider px-6 py-3 rounded-lg hover:bg-red-700 transition-colors whitespace-nowrap">
                                DETAIL
                            </a>
                        </div>
                    </div>

                    {{-- Event 2 --}}
                    <div class="bg-[#111111] border border-[#222222] rounded-2xl overflow-hidden flex flex-col md:flex-row">
                        <div class="w-full md:w-[140px] bg-[#1A1A1A] flex flex-col items-center justify-center py-6 md:py-0 shrink-0">
                            <span class="font-display font-bold text-[36px] leading-none text-white">28</span>
                            <span class="text-[#888888] text-[13px] font-semibold tracking-wider">APR 2026</span>
                        </div>
                        <div class="flex-1 flex flex-col gap-3 p-6 md:p-8">
                            <h3 class="font-display font-bold text-[24px] md:text-[28px] tracking-wide text-white">Storm Freerun Open</h3>
                            <div class="flex flex-wrap gap-2">
                                <span class="bg-bcz-red/[0.12] text-bcz-red text-[10px] font-bold tracking-wider px-3 py-1.5 rounded">FREESTYLE</span>
                            </div>
                            <div class="flex items-center gap-2 text-[#888888] text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>
                                <span>London, Velka Britania</span>
                            </div>
                        </div>
                        <div class="flex items-center px-6 pb-6 md:pb-0 md:pr-8">
                            <a href="#" class="border border-[#444444] text-white text-[12px] font-bold tracking-wider px-6 py-3 rounded-lg hover:border-bcz-red transition-colors whitespace-nowrap">
                                DETAIL
                            </a>
                        </div>
                    </div>

                    {{-- Event 3 --}}
                    <div class="bg-[#111111] border border-[#222222] rounded-2xl overflow-hidden flex flex-col md:flex-row">
                        <div class="w-full md:w-[140px] bg-[#1A1A1A] flex flex-col items-center justify-center py-6 md:py-0 shrink-0">
                            <span class="font-display font-bold text-[36px] leading-none text-white">12</span>
                            <span class="text-[#888888] text-[13px] font-semibold tracking-wider">JUN 2026</span>
                        </div>
                        <div class="flex-1 flex flex-col gap-3 p-6 md:p-8">
                            <h3 class="font-display font-bold text-[24px] md:text-[28px] tracking-wide text-white">SK Nationals Parkour</h3>
                            <div class="flex flex-wrap gap-2">
                                <span class="bg-bcz-red/[0.12] text-bcz-red text-[10px] font-bold tracking-wider px-3 py-1.5 rounded">SKILL</span>
                                <span class="bg-[#222222] text-[#AAAAAA] text-[10px] font-bold tracking-wider px-3 py-1.5 rounded">SPEED</span>
                            </div>
                            <div class="flex items-center gap-2 text-[#888888] text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>
                                <span>Bratislava, Slovensko</span>
                            </div>
                        </div>
                        <div class="flex items-center px-6 pb-6 md:pb-0 md:pr-8">
                            <a href="#" class="border border-[#444444] text-white text-[12px] font-bold tracking-wider px-6 py-3 rounded-lg hover:border-bcz-red transition-colors whitespace-nowrap">
                                DETAIL
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Pagination --}}
                <div class="flex items-center justify-center gap-3">
                    <button class="w-10 h-10 rounded-full bg-bcz-red text-white text-sm font-bold flex items-center justify-center">1</button>
                    <button class="w-10 h-10 rounded-full bg-[#141414] border border-[#222222] text-[#888888] text-sm font-bold flex items-center justify-center hover:border-bcz-red hover:text-white transition-colors">2</button>
                    <button class="w-10 h-10 rounded-full bg-[#141414] border border-[#222222] text-[#888888] text-sm font-bold flex items-center justify-center hover:border-bcz-red hover:text-white transition-colors">3</button>
                </div>
            </div>
        </div>
    </section>

    {{-- Results Section --}}
    <section id="vysledky" class="bg-[#111111] py-[100px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col gap-12">
                {{-- Header --}}
                <div class="flex flex-col items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                        <span class="text-bcz-red text-xs font-bold tracking-[3px]">NASE USPECHY</span>
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                    </div>
                    <h2 class="font-display font-bold text-[24px] md:text-[36px] lg:text-[48px] tracking-wide text-white text-center">VYSLEDKY ZO SUTAZI</h2>
                    <p class="text-[#666666] text-lg text-center max-w-[600px]">Pozrite si nase najlepsie umiestnenia a uspechy na medzinarodnych sutaziach</p>
                </div>

                {{-- Filter Area --}}
                <div class="bg-[#0D0D0D] border border-[#222222] rounded-2xl p-6 flex flex-col gap-5">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1 bg-[#141414] border border-[#222222] rounded-lg px-4 py-3 flex items-center justify-between cursor-pointer">
                            <span class="text-[#666666] text-sm">Vsetky discipliny</span>
                            <svg class="w-4 h-4 text-[#666666]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                        </div>
                        <div class="flex-1 bg-[#141414] border border-[#222222] rounded-lg px-4 py-3 flex items-center justify-between cursor-pointer">
                            <span class="text-[#666666] text-sm">Vsetky roky</span>
                            <svg class="w-4 h-4 text-[#666666]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                        </div>
                        <div class="flex-1 bg-[#141414] border border-[#222222] rounded-lg px-4 py-3 flex items-center justify-between cursor-pointer">
                            <span class="text-[#666666] text-sm">Umiestnenie</span>
                            <svg class="w-4 h-4 text-[#666666]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                        </div>
                    </div>
                </div>

                {{-- Results Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {{-- Result 1 --}}
                    <div class="bg-[#1A1A1A] border border-[#222222] rounded-2xl overflow-hidden">
                        <div class="relative w-full h-[200px] bg-[#1A1A1A]">
                            <div class="absolute top-4 left-4 bg-[#FFD700] text-black text-[11px] font-bold tracking-wider px-3 py-1.5 rounded">1. MIESTO</div>
                        </div>
                        <div class="flex flex-col gap-3 p-6">
                            <span class="text-[#888888] text-[12px] font-medium tracking-wider">FEBRUAR 2026</span>
                            <h3 class="font-display font-bold text-[24px] tracking-wide text-white">Red Bull Art of Motion</h3>
                            <p class="text-[#666666] text-[14px] leading-[1.6]">Dominik Klimek obsadil 1. miesto v kategori freestyle na prestiznej medzinarodnej sutazi v Grecku.</p>
                            <div class="flex items-center gap-2 text-bcz-red text-[12px] font-bold">
                                <span>Santorini, Grecko</span>
                            </div>
                        </div>
                    </div>

                    {{-- Result 2 --}}
                    <div class="bg-[#1A1A1A] border border-[#222222] rounded-2xl overflow-hidden">
                        <div class="relative w-full h-[200px] bg-[#1A1A1A]">
                            <div class="absolute top-4 left-4 bg-[#C0C0C0] text-black text-[11px] font-bold tracking-wider px-3 py-1.5 rounded">2. MIESTO</div>
                        </div>
                        <div class="flex flex-col gap-3 p-6">
                            <span class="text-[#888888] text-[12px] font-medium tracking-wider">JANUAR 2026</span>
                            <h3 class="font-display font-bold text-[24px] tracking-wide text-white">WFPF World Championship</h3>
                            <p class="text-[#666666] text-[14px] leading-[1.6]">Strieborna medaila v speed kategori na svetovom sampionate World Freerunning & Parkour Federation.</p>
                            <div class="flex items-center gap-2 text-bcz-red text-[12px] font-bold">
                                <span>Los Angeles, USA</span>
                            </div>
                        </div>
                    </div>

                    {{-- Result 3 --}}
                    <div class="bg-[#1A1A1A] border border-[#222222] rounded-2xl overflow-hidden">
                        <div class="relative w-full h-[200px] bg-[#1A1A1A]">
                            <div class="absolute top-4 left-4 bg-[#CD7F32] text-black text-[11px] font-bold tracking-wider px-3 py-1.5 rounded">3. MIESTO</div>
                        </div>
                        <div class="flex flex-col gap-3 p-6">
                            <span class="text-[#888888] text-[12px] font-medium tracking-wider">NOVEMBER 2025</span>
                            <h3 class="font-display font-bold text-[24px] tracking-wide text-white">European Parkour Open</h3>
                            <p class="text-[#666666] text-[14px] leading-[1.6]">Bronzova medaila v skill competition na Europskom parkour sampionate v Nemecku.</p>
                            <div class="flex items-center gap-2 text-bcz-red text-[12px] font-bold">
                                <span>Berlin, Nemecko</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Pagination --}}
                <div class="flex items-center justify-center gap-3">
                    <button class="w-10 h-10 rounded-full bg-bcz-red text-white text-sm font-bold flex items-center justify-center">1</button>
                    <button class="w-10 h-10 rounded-full bg-[#141414] border border-[#222222] text-[#888888] text-sm font-bold flex items-center justify-center hover:border-bcz-red hover:text-white transition-colors">2</button>
                    <button class="w-10 h-10 rounded-full bg-[#141414] border border-[#222222] text-[#888888] text-sm font-bold flex items-center justify-center hover:border-bcz-red hover:text-white transition-colors">3</button>
                </div>
            </div>
        </div>
    </section>

    {{-- Athletes Section --}}
    <section class="bg-bcz-dark py-[100px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col gap-16">
                {{-- Header --}}
                <div class="flex flex-col items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                        <span class="text-bcz-red text-xs font-bold tracking-[3px]">NAS TIM</span>
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                    </div>
                    <h2 class="font-display font-bold text-[24px] md:text-[36px] lg:text-[48px] tracking-wide text-white text-center">SUTAZIACI ATLETI</h2>
                    <p class="text-[#666666] text-lg text-center max-w-[600px]">Spoznajte nasich atletov, ktori reprezentuju BCZ Club na domacich aj medzinarodnych sutaziach</p>
                </div>

                {{-- Athletes Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    {{-- Athlete 1 --}}
                    <div class="bg-[#111111] border border-[#222222] rounded-2xl overflow-hidden">
                        <div class="w-full h-[300px] bg-[#1A1A1A]"></div>
                        <div class="flex flex-col gap-2 p-6">
                            <h3 class="text-white text-[20px] font-bold">Dominik Klimek</h3>
                            <span class="text-bcz-red text-[12px] font-medium tracking-wider">Parkour Freestyle</span>
                        </div>
                    </div>

                    {{-- Athlete 2 --}}
                    <div class="bg-[#111111] border border-[#222222] rounded-2xl overflow-hidden">
                        <div class="w-full h-[300px] bg-[#1A1A1A]"></div>
                        <div class="flex flex-col gap-2 p-6">
                            <h3 class="text-white text-[20px] font-bold">Michal Cecko</h3>
                            <span class="text-bcz-red text-[12px] font-medium tracking-wider">Speed & Skill</span>
                        </div>
                    </div>

                    {{-- Athlete 3 --}}
                    <div class="bg-[#111111] border border-[#222222] rounded-2xl overflow-hidden">
                        <div class="w-full h-[300px] bg-[#1A1A1A]"></div>
                        <div class="flex flex-col gap-2 p-6">
                            <h3 class="text-white text-[20px] font-bold">Tomas Novak</h3>
                            <span class="text-bcz-red text-[12px] font-medium tracking-wider">Parkour Speed</span>
                        </div>
                    </div>

                    {{-- Athlete 4 --}}
                    <div class="bg-[#111111] border border-[#222222] rounded-2xl overflow-hidden">
                        <div class="w-full h-[300px] bg-[#1A1A1A]"></div>
                        <div class="flex flex-col gap-2 p-6">
                            <h3 class="text-white text-[20px] font-bold">Adam Horvath</h3>
                            <span class="text-bcz-red text-[12px] font-medium tracking-wider">Freestyle & Skill</span>
                        </div>
                    </div>

                    {{-- Athlete 5 --}}
                    <div class="bg-[#111111] border border-[#222222] rounded-2xl overflow-hidden">
                        <div class="w-full h-[300px] bg-[#1A1A1A]"></div>
                        <div class="flex flex-col gap-2 p-6">
                            <h3 class="text-white text-[20px] font-bold">Peter Kovac</h3>
                            <span class="text-bcz-red text-[12px] font-medium tracking-wider">Parkour Freestyle</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="relative w-full h-[300px] md:h-[350px] lg:h-[400px] overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-bcz-red to-[#CC0000]"></div>

        <div class="relative w-full h-full flex flex-col items-center justify-center gap-6 max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <h2 class="font-display font-bold text-[28px] md:text-[40px] lg:text-[56px] tracking-wide text-white text-center">CHCES SUTAZIT S NAMI?</h2>

            <p class="text-white/80 text-lg text-center max-w-[600px]">
                Ak mas za sebou treningy a chces sa posunut na sutaznu uroven, pridaj sa k nasmu timu. Hladame motivovanych atletov.
            </p>

            <div class="flex flex-col sm:flex-row items-center gap-4 mt-2">
                <a href="#" class="bg-white text-bcz-dark text-sm font-bold tracking-wider px-9 py-[16px] rounded-lg hover:bg-white/90 transition-colors">
                    PRIDAJ SA
                </a>
                <a href="#" class="border border-white text-white text-sm font-bold tracking-wider px-9 py-[16px] rounded-lg hover:bg-white/10 transition-colors">
                    KONTAKTUJ NAS
                </a>
            </div>
        </div>
    </section>
@endsection
