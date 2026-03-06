@extends('layouts.public')

@section('title', 'Tréningy - BCZ Club')

@section('content')
    {{-- Hero Section --}}
    <section class="relative w-full h-[400px] md:h-[500px] lg:h-[600px] overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1650160500313-9d0b1b7659aa?w=1440&q=80')] bg-cover bg-center"></div>
        <div class="absolute inset-0" style="background: linear-gradient(180deg, #0A0A0A 0%, #0A0A0A00 40%, #0A0A0A00 60%, #0A0A0A 100%)"></div>

        <div class="relative w-full h-full flex flex-col items-center justify-center gap-6 max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 pt-[120px]">
            {{-- Breadcrumb --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="text-[#666666] text-[11px] font-medium tracking-[2px] hover:text-white transition-colors">DOMOV</a>
                <span class="text-[#444444] text-[11px]">/</span>
                <span class="text-bcz-red text-[11px] font-medium tracking-[2px]">TRÉNINGY</span>
            </div>

            {{-- Title --}}
            <div class="flex flex-col items-center">
                <h1 class="font-display font-bold text-[36px] md:text-[56px] lg:text-[80px] leading-[0.95] tracking-wide">TRÉNUJ</h1>
                <h1 class="font-display font-bold text-[36px] md:text-[56px] lg:text-[80px] leading-[0.95] tracking-wide text-bcz-red">S NAMI</h1>
            </div>

            {{-- Subtitle --}}
            <p class="text-[#AAAAAA] text-xl text-center max-w-[700px]">
                Profesionálne tréningy parkouru, kalisteniky a street workoutu pre všetky vekové kategórie.
            </p>

            {{-- Scroll Indicator --}}
            <div class="flex flex-col items-center gap-2 mt-4">
                <span class="text-[#555555] text-[10px] font-medium tracking-[2px]">SCROLLUJ PRE VIAC</span>
                <svg class="w-5 h-5 text-[#555555]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
            </div>
        </div>
    </section>

    {{-- Categories Section --}}
    <section class="bg-bcz-dark py-[100px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
        <div class="flex flex-col gap-16">
            {{-- Header --}}
            <div class="flex flex-col items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[3px]">ČO PONÚKAME</span>
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                </div>
                <h2 class="font-display font-bold text-[24px] md:text-[36px] lg:text-[48px] tracking-wide">TRÉNINGOVÉ KATEGÓRIE</h2>
                <p class="text-[#666666] text-lg text-center">Vyber si disciplínu, ktorá ťa baví najviac</p>
            </div>

            {{-- Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Parkour & Freerunning --}}
                <div class="bg-[#141414] border border-[#222222] overflow-hidden">
                    <div class="w-full h-[280px] bg-[url('https://images.unsplash.com/photo-1635356178539-54f76c6c6159?w=800&q=80')] bg-cover bg-center"></div>
                    <div class="flex flex-col gap-4 p-8">
                        <h3 class="text-white text-[22px] font-bold">PARKOUR &amp; FREERUNNING</h3>
                        <p class="text-[#888888] text-[15px] leading-[1.6]">
                            Nauč sa efektívne prekonávať prekážky a ovládni svoj pohyb v mestskom prostredí. Tréningy pre začiatočníkov aj pokročilých.
                        </p>
                        <a href="#" class="flex items-center gap-2 text-bcz-red text-xs font-bold tracking-wider hover:gap-3 transition-all">
                            ZISTI VIAC
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>

                {{-- Street Workout / Kalistenika --}}
                <div class="bg-[#141414] border border-[#222222] overflow-hidden">
                    <div class="w-full h-[280px] bg-[url('https://images.unsplash.com/photo-1517637382994-f02da38c6728?w=800&q=80')] bg-cover bg-center"></div>
                    <div class="flex flex-col gap-4 p-8">
                        <h3 class="text-white text-[22px] font-bold">STREET WORKOUT / KALISTENIKA</h3>
                        <p class="text-[#888888] text-[15px] leading-[1.6]">
                            Buduj silu vlastným telom. Od základov až po pokročilé prvky ako muscle-up, front lever a planche.
                        </p>
                        <a href="#" class="flex items-center gap-2 text-bcz-red text-xs font-bold tracking-wider hover:gap-3 transition-all">
                            ZISTI VIAC
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>

    {{-- Groups Section --}}
    <section class="bg-[#111111] py-[100px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
        <div class="flex flex-col gap-16">
            {{-- Header --}}
            <div class="flex flex-col items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[3px]">AKTUÁLNE SKUPINY</span>
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                </div>
                <h2 class="font-display font-bold text-[24px] md:text-[36px] lg:text-[48px] tracking-wide">VYBER SI SVOJU SKUPINU</h2>
                <p class="text-[#666666] text-lg text-center">Skupinové tréningy pre deti aj dospelých s obmedzenou kapacitou</p>
            </div>

            {{-- Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Group 1: Parkour Kids --}}
                <div class="bg-bcz-dark border border-[#222222] p-7 flex flex-col gap-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-white text-lg font-bold">PARKOUR KIDS</h3>
                        <span class="bg-bcz-red/[0.12] text-bcz-red text-[10px] font-bold tracking-wider px-3 py-1.5">6-12 ROKOV</span>
                    </div>

                    <div class="flex flex-col gap-3">
                        <div class="flex justify-between">
                            <span class="text-[#666666] text-sm">Deň</span>
                            <span class="text-white text-sm font-semibold">Utorok, Štvrtok</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#666666] text-sm">Čas</span>
                            <span class="text-white text-sm font-semibold">16:00 - 17:30</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#666666] text-sm">Tréner</span>
                            <span class="text-white text-sm font-semibold">Dominik Klimek</span>
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

                {{-- Group 2: Parkour Teens --}}
                <div class="bg-bcz-dark border border-[#222222] p-7 flex flex-col gap-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-white text-lg font-bold">PARKOUR TEENS</h3>
                        <span class="bg-bcz-red/[0.12] text-bcz-red text-[10px] font-bold tracking-wider px-3 py-1.5">13-17 ROKOV</span>
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
                            <span class="text-[#666666] text-sm">Tréner</span>
                            <span class="text-white text-sm font-semibold">Michal Čečko</span>
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

                {{-- Group 3: Kalistenika --}}
                <div class="bg-bcz-dark border border-[#222222] p-7 flex flex-col gap-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-white text-lg font-bold">KALISTENIKA</h3>
                        <span class="bg-bcz-red/[0.12] text-bcz-red text-[10px] font-bold tracking-wider px-3 py-1.5">18+ ROKOV</span>
                    </div>

                    <div class="flex flex-col gap-3">
                        <div class="flex justify-between">
                            <span class="text-[#666666] text-sm">Deň</span>
                            <span class="text-white text-sm font-semibold">Streda, Piatok</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#666666] text-sm">Čas</span>
                            <span class="text-white text-sm font-semibold">18:00 - 19:30</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#666666] text-sm">Tréner</span>
                            <span class="text-white text-sm font-semibold">Dominik Klimek</span>
                        </div>
                    </div>

                    <div class="w-full h-px bg-[#222222]"></div>

                    <div class="flex flex-col gap-2">
                        <div class="flex justify-between">
                            <span class="text-[#666666] text-[13px]">Kapacita</span>
                            <span class="text-[#22C55E] text-[13px] font-semibold">5/10 miest</span>
                        </div>
                        <div class="w-full h-1.5 bg-[#222222] rounded-full">
                            <div class="h-full bg-[#22C55E] rounded-full" style="width: 50%"></div>
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

    {{-- Coaches Section --}}
    <section class="bg-bcz-dark py-[100px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
        <div class="flex flex-col gap-16">
            {{-- Header --}}
            <div class="flex flex-col items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[3px]">UČ SA OD NAJLEPŠÍCH</span>
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                </div>
                <h2 class="font-display font-bold text-[24px] md:text-[36px] lg:text-[48px] tracking-wide">NAŠI TRÉNERI</h2>
                <p class="text-[#666666] text-lg text-center">Certifikovaní profesionáli s rokmi skúseností</p>
            </div>

            {{-- Grid --}}
            <div class="flex flex-col md:flex-row gap-8 justify-center">
                {{-- Coach 1 --}}
                <div class="w-full lg:w-[400px] bg-[#141414] border border-[#222222] overflow-hidden">
                    <div class="w-full h-[320px] bg-[url('https://images.unsplash.com/photo-1583454110551-21f2fa2afe61?w=600&q=80')] bg-cover bg-center"></div>
                    <div class="flex flex-col gap-4 p-7">
                        <h3 class="text-white text-[22px] font-bold">DOMINIK KLIMEK</h3>
                        <span class="text-bcz-red text-[11px] font-medium tracking-wider">Hlavný tréner Parkour &amp; Kalistenika</span>
                        <p class="text-[#888888] text-sm leading-[1.6]">
                            10+ rokov skúseností v parkour a kalistenike. Certifikovaný tréner s medzinárodnými úspechmi na súťažiach.
                        </p>
                        <div class="flex gap-2">
                            <span class="bg-[#222222] text-[#AAAAAA] text-[10px] font-medium px-2.5 py-1.5">Parkour Pro</span>
                            <span class="bg-[#222222] text-[#AAAAAA] text-[10px] font-medium px-2.5 py-1.5">Kalistenika L3</span>
                        </div>
                    </div>
                </div>

                {{-- Coach 2 --}}
                <div class="w-full lg:w-[400px] bg-[#141414] border border-[#222222] overflow-hidden">
                    <div class="w-full h-[320px] bg-[url('https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=600&q=80')] bg-cover bg-center"></div>
                    <div class="flex flex-col gap-4 p-7">
                        <h3 class="text-white text-[22px] font-bold">MICHAL ČEČKO</h3>
                        <span class="text-bcz-red text-[11px] font-medium tracking-wider">Tréner Parkour &amp; Street Workout</span>
                        <p class="text-[#888888] text-sm leading-[1.6]">
                            8 rokov aktívneho tréningu a 5 rokov skúseností s vedením skupín. Špecializácia na techniku a bezpečný progres.
                        </p>
                        <div class="flex gap-2">
                            <span class="bg-[#222222] text-[#AAAAAA] text-[10px] font-medium px-2.5 py-1.5">Freerunning</span>
                            <span class="bg-[#222222] text-[#AAAAAA] text-[10px] font-medium px-2.5 py-1.5">Street Workout</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>

    {{-- Private Training Section --}}
    <section class="bg-[#111111] py-[100px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
        <div class="flex flex-col lg:flex-row gap-8 lg:gap-20">
            {{-- Left --}}
            <div class="flex-1 flex flex-col gap-8">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[3px]">INDIVIDUÁLNY PRÍSTUP</span>
                </div>

                <h2 class="font-display font-bold text-[28px] md:text-[40px] lg:text-[56px] leading-none tracking-wide">SÚKROMNÉ<br>TRÉNINGY</h2>

                <p class="text-[#888888] text-lg leading-[1.7]">
                    Hľadáš individuálny prístup? Naši tréneri ti pripravia tréningový plán na mieru podľa tvojich cieľov a aktuálnej úrovne. Ideálne pre tých, ktorí chcú rýchlejší progres alebo sa pripravujú na súťaže.
                </p>

                {{-- Features --}}
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-4">
                        <div class="size-10 bg-bcz-red/[0.12] flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <span class="text-white text-base font-medium">Tréningový plán na mieru</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="size-10 bg-bcz-red/[0.12] flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <span class="text-white text-base font-medium">Flexibilný čas a miesto tréningu</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="size-10 bg-bcz-red/[0.12] flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <span class="text-white text-base font-medium">Video analýza techniky</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="size-10 bg-bcz-red/[0.12] flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <span class="text-white text-base font-medium">Príprava na súťaže</span>
                    </div>
                </div>

                <a href="#" class="flex items-center gap-2 text-white text-sm font-bold tracking-wider hover:gap-3 transition-all">
                    DOHODNÚŤ SI TRÉNING
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </a>
            </div>

            {{-- Right --}}
            <div class="w-full lg:w-[500px] shrink-0 flex flex-col gap-4">
                <img src="https://images.unsplash.com/photo-1700784795176-7ff886439d79?w=600&q=80" alt="Súkromný tréning" class="w-full h-[280px] rounded-none object-cover">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <img src="https://images.unsplash.com/photo-1764426445448-95103b0024a6?w=400&q=80" alt="Tréning" class="w-full h-[180px] rounded-none object-cover">
                    <img src="https://images.unsplash.com/photo-1648115063029-bed316d200bc?w=400&q=80" alt="Tréning" class="w-full h-[180px] rounded-none object-cover">
                </div>
            </div>
        </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="relative w-full h-[350px] md:h-[420px] lg:h-[500px] overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1607962252615-230f134370bd?w=1440&q=80')] bg-cover bg-center"></div>
        <div class="absolute inset-0 bg-[#0A0A0ACC]"></div>

        <div class="relative w-full h-full flex flex-col items-center justify-center gap-6 lg:gap-10 max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex items-center gap-3">
                <div class="w-10 h-0.5 bg-bcz-red"></div>
                <span class="text-bcz-red text-xs font-bold tracking-[3px]">ZAČNI EŠTE DNES</span>
                <div class="w-10 h-0.5 bg-bcz-red"></div>
            </div>

            <h2 class="font-display font-bold text-[28px] md:text-[40px] lg:text-[56px] tracking-wide text-center">PRIDAJ SA K NÁM</h2>

            <p class="text-[#888888] text-lg text-center">
                Prvá tréningová hodina je zadarmo. Príď si vyskúšať, či je to niečo pre teba.
            </p>

            <div class="flex flex-col sm:flex-row items-center gap-5">
                <a href="#" class="bg-bcz-red text-white text-sm font-bold tracking-wider px-9 py-[18px] hover:bg-red-700 transition-colors">
                    REZERVOVAŤ TRÉNING
                </a>
                <a href="#" class="border-2 border-white text-white text-sm font-bold tracking-wider px-9 py-[18px] hover:bg-white/10 transition-colors">
                    KONTAKTUJ NÁS
                </a>
            </div>
        </div>
    </section>
@endsection
