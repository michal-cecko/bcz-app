@extends('layouts.public')

@section('title', 'Michal Čečko - Atlét | BCZ Club')

@section('content')
    {{-- Hero Section --}}
    <section class="relative w-full h-[600px] overflow-hidden">
        <div class="absolute inset-0 bg-bcz-dark"></div>
        <div class="absolute inset-0" style="background: linear-gradient(180deg, #0A0A0A 0%, #0A0A0A88 40%, #0A0A0A88 60%, #0A0A0A 100%)"></div>

        <div class="relative w-full h-full flex flex-col lg:flex-row lg:items-center gap-8 lg:gap-20 max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            {{-- Left --}}
            <div class="flex-1 flex flex-col gap-6">
                <div class="flex items-center gap-2 text-[11px]">
                    <a href="{{ route('home') }}" class="text-[#888888] tracking-wider hover:text-white transition-colors">DOMOV</a>
                    <span class="text-[#666666]">&gt;</span>
                    <span class="text-[#888888] tracking-wider">SÚŤAŽE</span>
                    <span class="text-[#666666]">&gt;</span>
                    <span class="text-bcz-red tracking-wider">MICHAL ČEČKO</span>
                </div>

                <h1 class="font-display font-bold text-[72px] tracking-wide">MICHAL ČEČKO</h1>
                <span class="text-bcz-red text-lg font-medium tracking-[2px]">SK &middot; Parkour &amp; Street Workout Atlét</span>

                {{-- Stats --}}
                <div class="flex gap-6 lg:gap-12 mt-2">
                    <div class="flex flex-col gap-1">
                        <span class="font-display font-bold text-[42px] tracking-wide">8</span>
                        <span class="text-[#888888] text-[11px] font-medium tracking-wider">rokov skúseností</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="font-display font-bold text-[42px] tracking-wide">20+</span>
                        <span class="text-[#888888] text-[11px] font-medium tracking-wider">súťaží</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="font-display font-bold text-[42px] tracking-wide">7x</span>
                        <span class="text-[#888888] text-[11px] font-medium tracking-wider">na pódiu</span>
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="flex gap-4 mt-2">
                    <a href="#" class="bg-bcz-red rounded text-white text-xs font-bold tracking-wider px-7 py-4 flex items-center gap-2.5 hover:bg-red-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
                        SLEDUJ MA
                    </a>
                    <a href="{{ route('trener.michal-cecko') }}" class="rounded border border-[#444444] text-white text-xs font-bold tracking-wider px-7 py-4 flex items-center gap-2.5 hover:bg-white/10 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.4 14.4 9.6 9.6"/><path d="M18.657 21.485a2 2 0 1 1-2.829-2.828l-1.767 1.768a2 2 0 1 1-2.829-2.829l6.364-6.364a2 2 0 1 1 2.829 2.829l-1.768 1.767a2 2 0 1 1 2.828 2.829z"/><path d="m21.5 21.5-1.4-1.4"/><path d="M3.9 3.9 2.5 2.5"/><path d="M6.404 12.768a2 2 0 1 1-2.829-2.829l1.768-1.767a2 2 0 1 1-2.828-2.829l6.364-6.364a2 2 0 1 1 2.829 2.829L9.939 3.546a2 2 0 1 1 2.829 2.828z"/></svg>
                        TRÉNERSKÝ PROFIL
                    </a>
                </div>
            </div>

            {{-- Right --}}
            <div class="relative w-full lg:w-[400px] h-[440px] shrink-0 rounded-xl overflow-hidden">
                <img src="https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=500&q=80" alt="Michal Čečko" class="w-full h-full object-cover">
                <div class="absolute bottom-5 left-5 flex gap-2">
                    <span class="bg-bcz-red rounded text-white text-[10px] font-bold tracking-wider px-3 py-2">PARKOUR</span>
                    <span class="bg-[#222222] rounded text-white text-[10px] font-bold tracking-wider px-3 py-2">STREET WORKOUT</span>
                    <span class="bg-[#222222] rounded text-white text-[10px] font-bold tracking-wider px-3 py-2">TRICKING</span>
                </div>
            </div>
        </div>
    </section>

    {{-- About Section --}}
    <section class="bg-[#111111] py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
        <div class="flex flex-col lg:flex-row gap-8 lg:gap-16">
            <div class="flex-1 flex flex-col gap-8">
                <div class="flex flex-col gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                        <span class="text-bcz-red text-xs font-bold tracking-[2px]">MÔJ PRÍBEH</span>
                    </div>
                    <h2 class="font-display font-bold text-4xl tracking-wide">AKO TO VŠETKO ZAČALO</h2>
                </div>

                <div class="text-[#AAAAAA] text-base leading-relaxed flex flex-col gap-6">
                    <p>Všetko sa začalo v roku 2016, keď som prvýkrát videl parkour video na YouTube. Okamžite som vedel, že toto je to, čo chcem robiť. Začínal som úplne od nuly - prvé saltá, prvé preskoky, prvé pády.</p>
                    <p>Prvé 2 roky som trénoval sám, učil sa z videí a pomaly budoval základy. V roku 2018 som sa pripojil k BCZ Club a začala sa nová kapitola - systematický tréning, prvé súťaže a neustály posun vpred.</p>
                    <p>Dnes, po 8 rokoch, môžem povedať, že parkour mi dal viac ako len fyzickú kondíciu. Dal mi komunitu, disciplínu a neustálu motiváciu prekonávať vlastné limity.</p>
                </div>
            </div>

            <div class="w-full lg:w-[450px] shrink-0">
                <img src="https://images.unsplash.com/photo-1758521959295-38ef00565e7c?w=600&q=80" alt="Michal Čečko" class="w-full lg:w-[450px] h-[350px] object-cover">
            </div>
        </div>
        </div>
    </section>

    {{-- Progress Section --}}
    <section class="bg-bcz-dark py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
        <div class="flex flex-col gap-6 lg:gap-12">
            <div class="flex flex-col items-center gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[2px]">PROGRES</span>
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                </div>
                <h2 class="font-display font-bold text-4xl tracking-wide">CESTA K PRVKOM</h2>
                <p class="text-[#888888] text-base">Koľko času mi trvalo naučiť sa jednotlivé prvky</p>
            </div>

            {{-- Grid Row 1 --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach([
                    ['name' => 'Front Lever', 'time' => '2 roky', 'desc' => 'Od prvých pokusov po čistý 5s hold'],
                    ['name' => 'Planche', 'time' => '3 roky', 'desc' => 'Straddle planche - stále pracujem na full'],
                    ['name' => 'Swing 360', 'time' => '6 mesiacov', 'desc' => 'Jeden z mojich obľúbených parkour trikov'],
                    ['name' => 'Muscle Up', 'time' => '4 mesiace', 'desc' => 'Základný prvok, dnes robím 15+ za sebou'],
                ] as $skill)
                <div class="bg-[#111111] rounded-lg border border-[#222222] p-6 flex gap-5">
                    <div class="size-[100px] shrink-0 rounded-lg bg-[#1A1A1A]"></div>
                    <div class="flex flex-col gap-3">
                        <div class="flex flex-col gap-1">
                            <span class="text-white text-lg font-semibold">{{ $skill['name'] }}</span>
                            <span class="text-bcz-red text-xs font-bold">{{ $skill['time'] }}</span>
                        </div>
                        <p class="text-[#666666] text-[13px]">{{ $skill['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Grid Row 2 --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach([
                    ['name' => 'Backflip', 'time' => '3 mesiace', 'desc' => 'Prvé salto - otvorilo dvere k akrobacii'],
                    ['name' => 'Webster', 'time' => '8 mesiacov', 'desc' => 'Jeden z najkrajších tricking prvkov'],
                ] as $skill)
                <div class="bg-[#111111] rounded-lg border border-[#222222] p-6 flex gap-5">
                    <div class="size-[100px] shrink-0 rounded-lg bg-[#1A1A1A]"></div>
                    <div class="flex flex-col gap-3">
                        <div class="flex flex-col gap-1">
                            <span class="text-white text-lg font-semibold">{{ $skill['name'] }}</span>
                            <span class="text-bcz-red text-xs font-bold">{{ $skill['time'] }}</span>
                        </div>
                        <p class="text-[#666666] text-[13px]">{{ $skill['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        </div>
    </section>

    {{-- Achievements Section --}}
    <section class="bg-[#111111] py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
        <div class="flex flex-col gap-6 lg:gap-12">
            <div class="flex flex-col items-center gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[2px]">SÚŤAŽE</span>
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                </div>
                <h2 class="font-display font-bold text-4xl tracking-wide">ÚSPECHY &amp; UMIESTNENIA</h2>
                <p class="text-[#888888] text-base">Prvá súťaž: SK Parkour Open 2019</p>
            </div>

            <div class="flex flex-col gap-6">
                {{-- Comp 1: SK Parkour Championship 2023 --}}
                <div class="bg-bcz-dark rounded-lg border-2 border-bcz-red p-6 flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-6">
                    <div class="size-16 bg-bcz-red/[0.13] rounded-full flex items-center justify-center shrink-0">
                        <svg class="w-7 h-7 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>
                    </div>
                    <div class="flex-1 flex flex-col gap-2">
                        <span class="text-white text-lg font-semibold">SK Parkour Championship 2023</span>
                        <span class="text-[#888888] text-sm">Freestyle kategória &bull; Bratislava</span>
                    </div>
                    <div class="flex flex-col items-end gap-1">
                        <span class="text-bcz-red text-xl font-bold">3. MIESTO</span>
                        <span class="bg-[#CD7F3222] text-[#CD7F32] text-[11px] font-bold px-2.5 py-1 rounded">BRONZ</span>
                    </div>
                </div>

                {{-- Comp 2: Street Workout Nationals 2022 --}}
                <div class="bg-bcz-dark rounded-lg border border-[#222222] p-6 flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-6">
                    <div class="size-16 bg-[#C0C0C022] rounded-full flex items-center justify-center shrink-0">
                        <svg class="w-7 h-7 text-[#C0C0C0]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>
                    </div>
                    <div class="flex-1 flex flex-col gap-2">
                        <span class="text-white text-lg font-semibold">Street Workout Nationals 2022</span>
                        <span class="text-[#888888] text-sm">Freestyle kategória &bull; Košice</span>
                    </div>
                    <div class="flex flex-col items-end gap-1">
                        <span class="text-[#C0C0C0] text-xl font-bold">2. MIESTO</span>
                        <span class="bg-[#C0C0C022] text-[#C0C0C0] text-[11px] font-bold px-2.5 py-1 rounded">STRIEBRO</span>
                    </div>
                </div>

                {{-- Comp 3: CZ-SK Freerun Battle 2022 --}}
                <div class="bg-bcz-dark rounded-lg border border-[#222222] p-6 flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-6">
                    <div class="size-16 bg-[#FFD70022] rounded-full flex items-center justify-center shrink-0">
                        <svg class="w-7 h-7 text-[#FFD700]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>
                    </div>
                    <div class="flex-1 flex flex-col gap-2">
                        <span class="text-white text-lg font-semibold">CZ-SK Freerun Battle 2022</span>
                        <span class="text-[#888888] text-sm">Speed kategória &bull; Praha</span>
                    </div>
                    <div class="flex flex-col items-end gap-1">
                        <span class="text-[#FFD700] text-xl font-bold">1. MIESTO</span>
                        <span class="bg-[#FFD70022] text-[#FFD700] text-[11px] font-bold px-2.5 py-1 rounded">ZLATO</span>
                    </div>
                </div>

                {{-- Comp 4: SK Parkour Open 2019 --}}
                <div class="bg-bcz-dark rounded-lg border border-[#222222] p-6 flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-6">
                    <div class="size-16 bg-bcz-red/[0.13] rounded-full flex items-center justify-center shrink-0">
                        <svg class="w-7 h-7 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
                    </div>
                    <div class="flex-1 flex flex-col gap-2">
                        <span class="text-white text-lg font-semibold">SK Parkour Open 2019</span>
                        <span class="text-[#888888] text-sm">Prvá súťaž &bull; Bratislava</span>
                    </div>
                    <div class="flex flex-col items-end gap-1">
                        <span class="text-bcz-red text-xl font-bold">TOP 10</span>
                        <span class="bg-bcz-red/[0.13] text-bcz-red text-[11px] font-bold px-2.5 py-1 rounded">DEBUT</span>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>

    {{-- Goals Section --}}
    <section class="bg-bcz-dark py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
        <div class="flex flex-col gap-6 lg:gap-12">
            <div class="flex flex-col items-center gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[2px]">BUDÚCNOSŤ</span>
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                </div>
                <h2 class="font-display font-bold text-4xl tracking-wide">MOJE CIELE</h2>
                <p class="text-[#888888] text-base">Kam smerujem a čo chcem dosiahnuť</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Goal 1 --}}
                <div class="bg-[#111111] rounded-lg border border-[#222222] p-8 flex flex-col gap-5">
                    <div class="size-14 bg-bcz-red rounded-lg flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>
                    </div>
                    <h3 class="text-white text-xl font-semibold">Full Planche</h3>
                    <p class="text-[#888888] text-sm leading-[1.6]">Dokončiť cestu k full planche - momentálne som na straddle verzii. Cieľ: 3 sekundy hold do konca 2024.</p>
                    <div class="flex items-center gap-2">
                        <div class="size-2 bg-[#FFD700] rounded"></div>
                        <span class="text-[#FFD700] text-[11px] font-medium">V progrese</span>
                    </div>
                </div>

                {{-- Goal 2 --}}
                <div class="bg-[#111111] rounded-lg border border-[#222222] p-8 flex flex-col gap-5">
                    <div class="size-14 bg-bcz-red rounded-lg flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>
                    </div>
                    <h3 class="text-white text-xl font-semibold">Medzinárodná súťaž</h3>
                    <p class="text-[#888888] text-sm leading-[1.6]">Zúčastniť sa a umiestniť sa na medzinárodnej parkour súťaži. Cieľ: Art of Motion qualifiers 2025.</p>
                    <div class="flex items-center gap-2">
                        <div class="size-2 bg-[#888888] rounded"></div>
                        <span class="text-[#888888] text-[11px] font-medium">Plánované</span>
                    </div>
                </div>

                {{-- Goal 3 --}}
                <div class="bg-[#111111] rounded-lg border border-[#222222] p-8 flex flex-col gap-5">
                    <div class="size-14 bg-bcz-red rounded-lg flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <h3 class="text-white text-xl font-semibold">Inšpirovať ostatných</h3>
                    <p class="text-[#888888] text-sm leading-[1.6]">Rozširovať parkour komunitu na Slovensku a pomáhať ďalším začínajúcim športovcom na ich ceste.</p>
                    <div class="flex items-center gap-2">
                        <div class="size-2 bg-[#22C55E] rounded"></div>
                        <span class="text-[#22C55E] text-[11px] font-medium">Aktívne</span>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>

    {{-- Gallery Section --}}
    <section class="bg-[#111111] py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
        <div class="flex flex-col gap-6 lg:gap-12">
            <div class="flex flex-col items-center gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[2px]">V AKCII</span>
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                </div>
                <h2 class="font-display font-bold text-4xl tracking-wide">GALÉRIA</h2>
                <p class="text-[#888888] text-base">Momenty z tréningov a súťaží</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <div class="flex flex-col gap-5">
                    <div class="w-full h-[280px] rounded-lg overflow-hidden bg-[url('https://images.unsplash.com/photo-1763849049243-035d9fd74cec?w=600&q=80')] bg-cover bg-center"></div>
                    <div class="w-full h-[200px] rounded-lg overflow-hidden bg-[url('https://images.unsplash.com/photo-1758521959654-17618e77e2e8?w=600&q=80')] bg-cover bg-center"></div>
                </div>
                <div class="flex flex-col gap-5">
                    <div class="relative w-full h-[200px] rounded-lg overflow-hidden bg-[url('https://images.unsplash.com/photo-1625456825001-ce55c89e7f7c?w=600&q=80')] bg-cover bg-center">
                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                            <div class="size-14 bg-bcz-red rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-white ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            </div>
                        </div>
                    </div>
                    <div class="w-full h-[280px] rounded-lg overflow-hidden bg-[url('https://images.unsplash.com/photo-1759476531106-d4c3a57ded78?w=600&q=80')] bg-cover bg-center"></div>
                </div>
                <div class="flex flex-col gap-5">
                    <div class="w-full h-[240px] rounded-lg overflow-hidden bg-[url('https://images.unsplash.com/photo-1660171472311-06ac32fc3434?w=600&q=80')] bg-cover bg-center"></div>
                    <div class="w-full h-[240px] rounded-lg overflow-hidden bg-[url('https://images.unsplash.com/photo-1706009732638-eadf373b69bd?w=600&q=80')] bg-cover bg-center"></div>
                </div>
            </div>
        </div>
        </div>
    </section>

    {{-- Other Athletes Section --}}
    <section class="bg-bcz-dark py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
        <div class="flex flex-col gap-6 lg:gap-12">
            <div class="flex flex-col items-center gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[2px]">TÍM</span>
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                </div>
                <h2 class="font-display font-bold text-4xl tracking-wide">ĎALŠÍ ATLÉTI</h2>
                <p class="text-[#888888] text-base">Spoznaj ostatných členov BCZ Club tímu</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Athlete 1 --}}
                <div class="bg-[#111111] rounded-lg border border-[#222222] overflow-hidden">
                    <div class="w-full h-[220px] bg-[url('https://images.unsplash.com/photo-1583454110551-21f2fa2afe61?w=500&q=80')] bg-cover bg-center"></div>
                    <div class="p-6 flex flex-col gap-2">
                        <h3 class="text-white text-xl font-semibold">Dominik Klimek</h3>
                        <span class="text-[#666666] text-[11px] font-semibold tracking-wider">SK</span>
                        <span class="text-bcz-red text-xs font-medium tracking-wider">Parkour &amp; Freerunning</span>
                        <span class="text-[#888888] text-[13px]">10+ rokov skúseností &bull; 3x SK Champion</span>
                        <a href="#" class="mt-2 rounded border border-[#333333] text-white text-[11px] font-bold tracking-wider px-5 py-3 w-fit flex items-center gap-2 hover:bg-white/10 transition-colors">
                            ZOBRAZIŤ PROFIL
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>

                {{-- Athlete 2 --}}
                <div class="bg-[#111111] rounded-lg border border-[#222222] overflow-hidden">
                    <div class="w-full h-[220px] bg-[url('https://images.unsplash.com/photo-1536407078615-9fd99f2915c8?w=500&q=80')] bg-cover bg-center"></div>
                    <div class="p-6 flex flex-col gap-2">
                        <h3 class="text-white text-xl font-semibold">Peter Novák</h3>
                        <span class="text-[#666666] text-[11px] font-semibold tracking-wider">CZ</span>
                        <span class="text-bcz-red text-xs font-medium tracking-wider">Street Workout</span>
                        <span class="text-[#888888] text-[13px]">5 rokov skúseností &bull; Freestyle specialist</span>
                        <a href="#" class="mt-2 rounded border border-[#333333] text-white text-[11px] font-bold tracking-wider px-5 py-3 w-fit flex items-center gap-2 hover:bg-white/10 transition-colors">
                            ZOBRAZIŤ PROFIL
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>

                {{-- Athlete 3 --}}
                <div class="bg-[#111111] rounded-lg border border-[#222222] overflow-hidden">
                    <div class="w-full h-[220px] bg-[url('https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=500&q=80')] bg-cover bg-center"></div>
                    <div class="p-6 flex flex-col gap-2">
                        <h3 class="text-white text-xl font-semibold">Tomáš Horváth</h3>
                        <span class="text-[#666666] text-[11px] font-semibold tracking-wider">SK</span>
                        <span class="text-bcz-red text-xs font-medium tracking-wider">Tricking &amp; Akrobacia</span>
                        <span class="text-[#888888] text-[13px]">6 rokov skúseností &bull; Performer</span>
                        <a href="#" class="mt-2 rounded border border-[#333333] text-white text-[11px] font-bold tracking-wider px-5 py-3 w-fit flex items-center gap-2 hover:bg-white/10 transition-colors">
                            ZOBRAZIŤ PROFIL
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="bg-bcz-red py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
        <div class="flex flex-col items-center gap-8">
            <h2 class="font-display font-bold text-[42px] tracking-wide text-center">SLEDUJ MA NA INSTAGRAME</h2>
            <p class="text-white/60 text-lg text-center">Denne zdieľam tréningy, tipy a behind-the-scenes z mojej cesty</p>
            <span class="text-white text-2xl font-bold tracking-[2px]">@michal.cecko</span>

            <div class="flex items-center gap-4">
                <a href="#" class="bg-white rounded text-bcz-red text-[13px] font-bold tracking-wider px-9 py-[18px] flex items-center gap-2.5 hover:bg-gray-100 transition-colors">
                    <svg class="w-[22px] h-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
                    SLEDOVAŤ
                </a>
                <a href="{{ route('trener.michal-cecko') }}" class="rounded border-2 border-white text-white text-[13px] font-bold tracking-wider px-9 py-[18px] flex items-center gap-2.5 hover:bg-white/10 transition-colors">
                    <svg class="w-[22px] h-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.4 14.4 9.6 9.6"/><path d="M18.657 21.485a2 2 0 1 1-2.829-2.828l-1.767 1.768a2 2 0 1 1-2.829-2.829l6.364-6.364a2 2 0 1 1 2.829 2.829l-1.768 1.767a2 2 0 1 1 2.828 2.829z"/><path d="m21.5 21.5-1.4-1.4"/><path d="M3.9 3.9 2.5 2.5"/><path d="M6.404 12.768a2 2 0 1 1-2.829-2.829l1.768-1.767a2 2 0 1 1-2.828-2.829l6.364-6.364a2 2 0 1 1 2.829 2.829L9.939 3.546a2 2 0 1 1 2.829 2.828z"/></svg>
                    TRÉNERSKÝ PROFIL
                </a>
            </div>
        </div>
        </div>
    </section>
@endsection
