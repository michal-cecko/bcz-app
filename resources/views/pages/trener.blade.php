@extends('layouts.public')

@section('title', 'Michal Čečko - Tréner - BCZ Club')

@section('content')
    {{-- Hero Section --}}
    <section class="relative w-full h-[500px] overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=1440&q=80')] bg-cover bg-center"></div>
        <div class="absolute inset-0" style="background: linear-gradient(180deg, #0A0A0A 0%, #0A0A0A00 30%, #0A0A0A00 70%, #0A0A0A 100%)"></div>

        <div class="relative w-full h-full flex flex-col justify-center">
            <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col justify-center gap-6">
                {{-- Breadcrumb --}}
                <div class="flex items-center gap-2 text-[11px]">
                    <a href="{{ route('home') }}" class="text-[#888888] tracking-wider hover:text-white transition-colors">DOMOV</a>
                    <span class="text-[#666666]">&gt;</span>
                    <a href="{{ route('treningy') }}" class="text-[#888888] tracking-wider hover:text-white transition-colors">TRÉNINGY</a>
                    <span class="text-[#666666]">&gt;</span>
                    <span class="text-bcz-red tracking-wider">MICHAL ČEČKO</span>
                </div>

                <h1 class="font-display font-bold text-[32px] md:text-[48px] lg:text-[64px] tracking-wide">MICHAL ČEČKO</h1>
                <span class="text-bcz-red text-base font-medium tracking-[2px]">Tréner Parkour &amp; Street Workout</span>
            </div>
        </div>
    </section>

    {{-- About Section --}}
    <section class="bg-[#111111] py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col lg:flex-row gap-8 lg:gap-16">
                {{-- Left --}}
            <div class="flex-1 flex flex-col gap-8">
                <div class="flex flex-col gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                        <span class="text-bcz-red text-xs font-bold tracking-[2px]">O MNE</span>
                    </div>
                    <h2 class="font-display font-bold text-4xl tracking-wide">MÔJ PRÍBEH</h2>
                </div>

                <div class="text-[#AAAAAA] text-base leading-[1.8] flex flex-col gap-6">
                    <p>S parkourom a street workoutom som začal v roku 2016. Od tej doby som prešiel dlhú cestu od začiatočníka až po certifikovaného trénera a aktívneho súťažiaceho športovca.</p>
                    <p>Moja filozofia je jednoduchá: bezpečnosť na prvom mieste, ale bez strachu skúšať nové veci. Verím, že každý môže prekonať svoje limity, ak má správneho sprievodcu na tejto ceste.</p>
                    <p>Trénujem všetky vekové kategórie - od detí cez teenagerov až po dospelých. Každá skupina má svoje špecifiká a ja sa snažím prispôsobiť tréning individuálnym potrebám.</p>
                </div>
            </div>

            {{-- Right --}}
            <div class="w-full lg:w-[400px] shrink-0">
                <img src="https://images.unsplash.com/photo-1675910518330-1843b4d03de1?w=600&q=80" alt="Michal Čečko" class="w-full lg:w-[400px] h-[400px] object-cover">
            </div>
            </div>
        </div>
    </section>

    {{-- Education Section --}}
    <section class="bg-bcz-dark py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col gap-6 lg:gap-12">
            {{-- Header --}}
            <div class="flex flex-col items-center gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[2px]">KVALIFIKÁCIA</span>
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                </div>
                <h2 class="font-display font-bold text-4xl tracking-wide">VZDELANIE &amp; CERTIFIKÁTY</h2>
            </div>

            {{-- Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Cert 1 --}}
                <div class="bg-[#111111] rounded-lg border border-[#222222] p-8 flex flex-col gap-4">
                    <div class="size-14 bg-bcz-red/[0.13] rounded-full flex items-center justify-center">
                        <svg class="w-7 h-7 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
                    </div>
                    <h3 class="text-white text-lg font-semibold">Parkour Instructor Level 2</h3>
                    <p class="text-[#888888] text-sm leading-[1.6]">Medzinárodná certifikácia pre výuku parkour techniky a bezpečnostných postupov</p>
                    <span class="text-[#666666] text-xs font-medium">2021</span>
                </div>

                {{-- Cert 2 --}}
                <div class="bg-[#111111] rounded-lg border border-[#222222] p-8 flex flex-col gap-4">
                    <div class="size-14 bg-bcz-red/[0.13] rounded-full flex items-center justify-center">
                        <svg class="w-7 h-7 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.4 14.4 9.6 9.6"/><path d="M18.657 21.485a2 2 0 1 1-2.829-2.828l-1.767 1.768a2 2 0 1 1-2.829-2.829l6.364-6.364a2 2 0 1 1 2.829 2.829l-1.768 1.767a2 2 0 1 1 2.828 2.829z"/><path d="m21.5 21.5-1.4-1.4"/><path d="M3.9 3.9 2.5 2.5"/><path d="M6.404 12.768a2 2 0 1 1-2.829-2.829l1.768-1.767a2 2 0 1 1-2.828-2.829l6.364-6.364a2 2 0 1 1 2.829 2.829L9.939 3.546a2 2 0 1 1 2.829 2.828z"/></svg>
                    </div>
                    <h3 class="text-white text-lg font-semibold">Street Workout Trainer</h3>
                    <p class="text-[#888888] text-sm leading-[1.6]">Certifikovaný tréner kalisteniky a street workout disciplín</p>
                    <span class="text-[#666666] text-xs font-medium">2020</span>
                </div>

                {{-- Cert 3 --}}
                <div class="bg-[#111111] rounded-lg border border-[#222222] p-8 flex flex-col gap-4">
                    <div class="size-14 bg-bcz-red/[0.13] rounded-full flex items-center justify-center">
                        <svg class="w-7 h-7 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/><path d="M3.22 12H9.5l.5-1 2 4.5 2-7 1.5 3.5h5.27"/></svg>
                    </div>
                    <h3 class="text-white text-lg font-semibold">Prvá pomoc</h3>
                    <p class="text-[#888888] text-sm leading-[1.6]">Certifikát prvej pomoci a základnej zdravotnej starostlivosti pri športových aktivitách</p>
                    <span class="text-[#666666] text-xs font-medium">2022</span>
                </div>

                {{-- Cert 4 --}}
                <div class="bg-[#111111] rounded-lg border border-[#222222] p-8 flex flex-col gap-4">
                    <div class="size-14 bg-bcz-red/[0.13] rounded-full flex items-center justify-center">
                        <svg class="w-7 h-7 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                    </div>
                    <h3 class="text-white text-lg font-semibold">Akrobacia &amp; Tricking</h3>
                    <p class="text-[#888888] text-sm leading-[1.6]">Špecializované školenie pre výuku akrobatických prvkov a tricking techník</p>
                    <span class="text-[#666666] text-xs font-medium">2023</span>
                </div>
            </div>
            </div>
        </div>
    </section>

    {{-- My Groups Section --}}
    <section class="bg-[#111111] py-[100px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col gap-8 lg:gap-16">
            {{-- Header --}}
            <div class="flex flex-col items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[3px]">MOJE SKUPINKY</span>
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                </div>
                <h2 class="font-display font-bold text-[24px] md:text-[36px] lg:text-[48px] tracking-wide">TRÉNINGOVÉ SKUPINY</h2>
                <p class="text-[#666666] text-lg text-center">Pripoj sa k mojim skupinovým tréningom a trénuj s motivovanými ľuďmi</p>
            </div>

            {{-- Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Group 1: Začiatočníci --}}
                <div class="bg-bcz-dark border border-[#222222] p-7 flex flex-col gap-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-white text-lg font-bold">ZAČIATOČNÍCI</h3>
                        <span class="bg-bcz-red/[0.12] text-bcz-red text-[10px] font-bold tracking-wider px-3 py-1.5">PON &amp; STR</span>
                    </div>

                    <div class="flex flex-col gap-3">
                        <div class="flex justify-between">
                            <span class="text-[#666666] text-sm">Deň</span>
                            <span class="text-white text-sm font-semibold">Pondelok, Streda</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#666666] text-sm">Čas</span>
                            <span class="text-white text-sm font-semibold">17:00 - 18:30</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#666666] text-sm">Miesto</span>
                            <span class="text-white text-sm font-semibold">Športpark Kysuce</span>
                        </div>
                    </div>

                    <div class="w-full h-px bg-[#222222]"></div>

                    <div class="flex flex-col gap-2">
                        <div class="flex justify-between">
                            <span class="text-[#666666] text-[13px]">Kapacita</span>
                            <span class="text-bcz-red text-[13px] font-semibold">8/12 miest</span>
                        </div>
                        <div class="w-full h-1.5 bg-[#222222] rounded-full">
                            <div class="h-full bg-bcz-red rounded-full" style="width: 66%"></div>
                        </div>
                    </div>

                    <a href="#" class="w-full bg-bcz-red text-white text-xs font-bold tracking-wider text-center py-3.5 hover:bg-red-700 transition-colors">
                        PRIHLÁSIŤ SA
                    </a>
                </div>

                {{-- Group 2: Pokročilí --}}
                <div class="bg-bcz-dark border border-[#222222] p-7 flex flex-col gap-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-white text-lg font-bold">POKROČILÍ</h3>
                        <span class="bg-bcz-red/[0.12] text-bcz-red text-[10px] font-bold tracking-wider px-3 py-1.5">UTO &amp; ŠTV</span>
                    </div>

                    <div class="flex flex-col gap-3">
                        <div class="flex justify-between">
                            <span class="text-[#666666] text-sm">Deň</span>
                            <span class="text-white text-sm font-semibold">Utorok, Štvrtok</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#666666] text-sm">Čas</span>
                            <span class="text-white text-sm font-semibold">18:00 - 19:30</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#666666] text-sm">Miesto</span>
                            <span class="text-white text-sm font-semibold">Športpark Kysuce</span>
                        </div>
                    </div>

                    <div class="w-full h-px bg-[#222222]"></div>

                    <div class="flex flex-col gap-2">
                        <div class="flex justify-between">
                            <span class="text-[#666666] text-[13px]">Kapacita</span>
                            <span class="text-bcz-red text-[13px] font-semibold">10/12 miest</span>
                        </div>
                        <div class="w-full h-1.5 bg-[#222222] rounded-full">
                            <div class="h-full bg-bcz-red rounded-full" style="width: 83%"></div>
                        </div>
                    </div>

                    <a href="#" class="w-full bg-bcz-red text-white text-xs font-bold tracking-wider text-center py-3.5 hover:bg-red-700 transition-colors">
                        PRIHLÁSIŤ SA
                    </a>
                </div>

                {{-- Group 3: Parkour --}}
                <div class="bg-bcz-dark border border-[#222222] p-7 flex flex-col gap-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-white text-lg font-bold">PARKOUR</h3>
                        <span class="bg-bcz-red/[0.12] text-bcz-red text-[10px] font-bold tracking-wider px-3 py-1.5">SOBOTA</span>
                    </div>

                    <div class="flex flex-col gap-3">
                        <div class="flex justify-between">
                            <span class="text-[#666666] text-sm">Deň</span>
                            <span class="text-white text-sm font-semibold">Sobota</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#666666] text-sm">Čas</span>
                            <span class="text-white text-sm font-semibold">10:00 - 12:00</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#666666] text-sm">Miesto</span>
                            <span class="text-white text-sm font-semibold">Mesto Čadca</span>
                        </div>
                    </div>

                    <div class="w-full h-px bg-[#222222]"></div>

                    <div class="flex flex-col gap-2">
                        <div class="flex justify-between">
                            <span class="text-[#666666] text-[13px]">Kapacita</span>
                            <span class="text-bcz-red text-[13px] font-semibold">6/10 miest</span>
                        </div>
                        <div class="w-full h-1.5 bg-[#222222] rounded-full">
                            <div class="h-full bg-bcz-red rounded-full" style="width: 60%"></div>
                        </div>
                    </div>

                    <a href="#" class="w-full bg-bcz-red text-white text-xs font-bold tracking-wider text-center py-3.5 hover:bg-red-700 transition-colors">
                        PRIHLÁSIŤ SA
                    </a>
                </div>
            </div>
            </div>
        </div>
    </section>

    {{-- Athlete Section --}}
    <section class="bg-bcz-dark py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col lg:flex-row gap-6 lg:gap-12">
            {{-- Left --}}
            <div class="flex-1 flex flex-col gap-8">
                <div class="flex flex-col gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                        <span class="text-bcz-red text-xs font-bold tracking-[2px]">SÚŤAŽNÝ PROFIL</span>
                    </div>
                    <h2 class="font-display font-bold text-4xl tracking-wide">AKTÍVNY ATLÉT</h2>
                </div>

                <div class="text-[#AAAAAA] text-base leading-[1.8] flex flex-col gap-6">
                    <p>Okrem trénerskej činnosti som aj aktívnym súťažiacim športovcom. Pravidelne sa zúčastňujem domácich aj medzinárodných súťaží v parkour a street workout disciplínach.</p>
                    <p>Moje skúsenosti zo súťaží prenášam priamo do tréningov - viem, čo funguje pod tlakom a ako sa pripraviť na podanie maximálneho výkonu.</p>
                </div>

                {{-- Stats --}}
                <div class="flex gap-10">
                    <div class="flex flex-col gap-1">
                        <span class="font-display font-bold text-[24px] md:text-[36px] lg:text-[48px] text-bcz-red tracking-wide">15+</span>
                        <span class="text-[#888888] text-xs font-medium tracking-wider">súťaží</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="font-display font-bold text-[24px] md:text-[36px] lg:text-[48px] text-bcz-red tracking-wide">5x</span>
                        <span class="text-[#888888] text-xs font-medium tracking-wider">pódiové umiestnenie</span>
                    </div>
                </div>

                <a href="#" class="bg-bcz-red rounded text-white text-[13px] font-bold tracking-wider px-8 py-4 w-fit flex items-center gap-3 hover:bg-red-700 transition-colors">
                    ZOBRAZIŤ PROFIL ATLÉTA
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </a>
            </div>

            {{-- Right --}}
            <div class="relative w-full lg:w-[500px] h-[400px] shrink-0 rounded-lg overflow-hidden">
                <img src="https://images.unsplash.com/photo-1536407078615-9fd99f2915c8?w=600&q=80" alt="Atlét" class="w-full h-full object-cover">
                <div class="absolute inset-0" style="background: linear-gradient(45deg, #FF2D2D33, #0A0A0A00 50%)"></div>
                <div class="absolute top-5 left-5 bg-bcz-red rounded flex items-center gap-2 px-4 py-3">
                    <svg class="w-[18px] h-[18px] text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>
                    <span class="text-white text-[11px] font-bold tracking-wider">TOP 3 SK 2023</span>
                </div>
            </div>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="bg-[#111111] py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col items-center gap-8">
                <h2 class="font-display font-bold text-[42px] tracking-wide">PRIPRAVENÝ ZAČAŤ?</h2>
                <p class="text-[#888888] text-lg text-center">Napíš mi a dohodneme si prvý tréning alebo nezáväznú konzultáciu.</p>

                <div class="flex items-center gap-4">
                    <a href="#" class="bg-bcz-red rounded text-white text-[13px] font-bold tracking-wider px-9 py-[18px] flex items-center gap-2.5 hover:bg-red-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                        NAPÍŠ MI
                    </a>
                    <a href="#" class="rounded border border-[#444444] text-white text-[13px] font-bold tracking-wider px-9 py-[18px] flex items-center gap-2.5 hover:bg-white/10 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
                        INSTAGRAM
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
