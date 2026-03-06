@extends('layouts.public')

@section('title', 'O nás - BCZ Club')

@section('content')
    {{-- Hero Section --}}
    <section class="relative w-full h-[400px] md:h-[500px] lg:h-[600px] overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1763664490292-c1f456a25912?w=1440&q=80')] bg-cover bg-center"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-bcz-dark via-transparent to-bcz-dark"></div>

        <div class="relative w-full h-full flex flex-col items-center justify-center gap-6 max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 pt-[120px]">
            {{-- Breadcrumb --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="text-bcz-muted text-[11px] font-medium tracking-widest hover:text-white transition-colors">DOMOV</a>
                <span class="text-[#444444] text-[11px]">/</span>
                <span class="text-bcz-red text-[11px] font-medium tracking-widest">O NÁS</span>
            </div>

            {{-- Title --}}
            <div class="flex flex-col items-center">
                <h1 class="font-display font-bold text-[36px] md:text-[56px] lg:text-[80px] leading-[0.95] tracking-wide">NÁŠ</h1>
                <h1 class="font-display font-bold text-[36px] md:text-[56px] lg:text-[80px] leading-[0.95] tracking-wide text-bcz-red">PRÍBEH</h1>
            </div>

            {{-- Subtitle --}}
            <p class="text-[#AAAAAA] text-xl text-center max-w-[700px]">
                Od skupiny priateľov posúvajúcich hranice po profesionálnu asociáciu inšpirujúcu ďalšiu generáciu športovcov.
            </p>

            {{-- Scroll indicator --}}
            <div class="flex flex-col items-center gap-2 mt-4">
                <span class="text-[#555555] text-[10px] font-medium tracking-widest">SCROLLUJ PRE VIAC</span>
                <svg class="w-5 h-5 text-[#555555]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
        </div>
    </section>

    {{-- Story Section --}}
    <section class="bg-bcz-dark py-[100px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
        <div class="flex flex-col gap-8 lg:gap-20">
            {{-- Story Intro --}}
            <div class="flex flex-col lg:flex-row lg:items-center gap-10 lg:gap-20">
                {{-- Left --}}
                <div class="flex-1 flex flex-col gap-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                        <span class="text-bcz-red text-xs font-bold tracking-[3px]">AKO TO VŠETKO ZAČALO</span>
                    </div>

                    <h2 class="font-display font-bold text-[28px] md:text-[40px] lg:text-[56px] leading-none tracking-wide">Z ULÍC<br>NA PÓDIA</h2>

                    <p class="text-[#888888] text-lg leading-[1.7]">
                        Všetko to začalo v roku 2015, keď malá skupina priateľov objavila parkour cez online videá. To, čo začalo ako neformálne stretnutia v miestnych parkoch, sa rýchlo vyvinulo v niečo omnoho väčšie.
                    </p>

                    <p class="text-[#888888] text-lg leading-[1.7]">
                        Čelili sme nespočetným prekážkam - nedostatok správnych tréningových priestorov, skepticizmus od ostatných a fyzické výzvy pri zvládaní nových pohybov. Ale každý pád nás niečo naučil a každý úspech nás posunul ďalej.
                    </p>
                </div>

                {{-- Right --}}
                <div class="w-full lg:w-[500px] shrink-0 flex flex-col gap-4">
                    <div class="w-full h-[350px] bg-[url('https://images.unsplash.com/photo-1651995859145-1ed661499fc9?w=600&q=80')] bg-cover bg-center"></div>
                    <span class="text-[#555555] text-[11px] font-medium tracking-wider">Prvé dni tréningu na uliciach, 2016</span>
                </div>
            </div>

            {{-- Timeline --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-0">
                {{-- 2015 --}}
                <div class="flex flex-col gap-5 pr-8">
                    <span class="font-display font-bold text-[32px] md:text-[48px] lg:text-[64px] text-bcz-red/20 tracking-wide">2015</span>
                    <div class="flex items-center gap-4">
                        <div class="w-4 h-4 rounded-full bg-bcz-red shrink-0"></div>
                        <div class="h-0.5 bg-[#222222] flex-1"></div>
                    </div>
                    <h3 class="text-white text-xl font-bold">Začiatok</h3>
                    <p class="text-[#666666] text-sm leading-relaxed">
                        Prvé neoficiálne stretnutia v miestnych parkoch. Len priatelia, ktorí sa zabávajú a učia sa spolu.
                    </p>
                </div>

                {{-- 2017 --}}
                <div class="flex flex-col gap-5 pr-8">
                    <span class="font-display font-bold text-[32px] md:text-[48px] lg:text-[64px] text-bcz-red/20 tracking-wide">2017</span>
                    <div class="flex items-center gap-4">
                        <div class="w-4 h-4 rounded-full bg-bcz-red shrink-0"></div>
                        <div class="h-0.5 bg-[#222222] flex-1"></div>
                    </div>
                    <h3 class="text-white text-xl font-bold">Prvá súťaž</h3>
                    <p class="text-[#666666] text-sm leading-relaxed">
                        Náš tím sa zúčastnil prvej národnej parkorovej súťaže. Nevyhrali sme, ale naučili sme sa.
                    </p>
                </div>

                {{-- 2019 --}}
                <div class="flex flex-col gap-5 pr-8">
                    <span class="font-display font-bold text-[32px] md:text-[48px] lg:text-[64px] text-bcz-red/20 tracking-wide">2019</span>
                    <div class="flex items-center gap-4">
                        <div class="w-4 h-4 rounded-full bg-bcz-red shrink-0"></div>
                        <div class="h-0.5 bg-[#222222] flex-1"></div>
                    </div>
                    <h3 class="text-white text-xl font-bold">Oficiálna asociácia</h3>
                    <p class="text-[#666666] text-sm leading-relaxed">
                        BCZ Club sa stal oficiálnou neziskovou organizáciou. Začali sme naše prvé tréningové programy.
                    </p>
                </div>

                {{-- 2024 --}}
                <div class="flex flex-col gap-5">
                    <span class="font-display font-bold text-[32px] md:text-[48px] lg:text-[64px] text-bcz-red/20 tracking-wide">2024</span>
                    <div class="flex items-center gap-4">
                        <div class="w-4 h-4 rounded-full bg-bcz-red shrink-0"></div>
                    </div>
                    <h3 class="text-white text-xl font-bold">Dnes a ďalej</h3>
                    <p class="text-[#666666] text-sm leading-relaxed">
                        Medzinárodné súťaže, profesionálne tréningy, vystúpenia po celej krajine. Cesta pokračuje.
                    </p>
                </div>
            </div>
        </div>
        </div>
    </section>

    {{-- Team Section --}}
    <section class="bg-[#111111] py-[100px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
        <div class="flex flex-col gap-[60px]">
            {{-- Header --}}
            <div class="flex flex-col items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-widest">ĽUDIA</span>
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                </div>
                <h2 class="font-display font-bold text-5xl tracking-wide">SPOZNAJTE NAŠICH ŠPORTOVCOV</h2>
                <p class="text-[#666666] text-lg text-center">
                    Talentovaní jednotlivci, ktorí reprezentujú BCZ Club na súťažiach po celom svete.
                </p>
            </div>

            {{-- Athletes Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Athlete 1 --}}
                <div class="bg-bcz-dark flex flex-col overflow-hidden">
                    <div class="w-full h-[320px] bg-[url('https://images.unsplash.com/photo-1583454110551-21f2fa2afe61?w=400&q=80')] bg-cover bg-center"></div>
                    <div class="p-6 flex flex-col gap-2">
                        <h3 class="text-white text-lg font-bold">DOMINIK KLIMEK</h3>
                        <span class="text-bcz-red text-[11px] font-medium tracking-wider">Zakladateľ &amp; Športovec</span>
                        <p class="text-[#666666] text-[13px] leading-relaxed mt-1">
                            10+ rokov v parkour. Viaceré medaily z národných majstrovstiev. Špecializuje sa na freestyle a flow.
                        </p>
                    </div>
                </div>

                {{-- Athlete 2 --}}
                <div class="bg-bcz-dark flex flex-col overflow-hidden">
                    <div class="w-full h-[320px] bg-[url('https://images.unsplash.com/photo-1762770645006-9dea6b23de1e?w=400&q=80')] bg-cover bg-center"></div>
                    <div class="p-6 flex flex-col gap-2">
                        <h3 class="text-white text-lg font-bold">MICHAL ČEČKO</h3>
                        <span class="text-bcz-red text-[11px] font-medium tracking-wider">Spoluzakladateľ &amp; Športovec</span>
                        <p class="text-[#666666] text-[13px] leading-relaxed mt-1">
                            Freerunning špecialista s medzinárodnými skúsenosťami zo súťaží. Známy kreatívnymi a technickými pohybmi.
                        </p>
                    </div>
                </div>

                {{-- Athlete 3 --}}
                <div class="bg-bcz-dark flex flex-col overflow-hidden">
                    <div class="w-full h-[320px] bg-[url('https://images.unsplash.com/photo-1763639700467-dfa82a4506ad?w=400&q=80')] bg-cover bg-center"></div>
                    <div class="p-6 flex flex-col gap-2">
                        <h3 class="text-white text-lg font-bold">ČLEN TÍMU</h3>
                        <span class="text-bcz-red text-[11px] font-medium tracking-wider">Súťažný športovec</span>
                        <p class="text-[#666666] text-[13px] leading-relaxed mt-1">
                            Stúpajúci talent na kalistenickej scéne. Súťaží na národných aj medzinárodných podujatiach.
                        </p>
                    </div>
                </div>

                {{-- Athlete 4 --}}
                <div class="bg-bcz-dark flex flex-col overflow-hidden">
                    <div class="w-full h-[320px] bg-[url('https://images.unsplash.com/photo-1759476597683-f51b04cf58d3?w=400&q=80')] bg-cover bg-center"></div>
                    <div class="p-6 flex flex-col gap-2">
                        <h3 class="text-white text-lg font-bold">ČLEN TÍMU</h3>
                        <span class="text-bcz-red text-[11px] font-medium tracking-wider">Súťažný športovec</span>
                        <p class="text-[#666666] text-[13px] leading-relaxed mt-1">
                            Prináša silu a eleganciu do nášho tímu. Zameriava sa na freestyle a akrobatické pohyby.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>

    {{-- Coaches Section --}}
    <section class="bg-bcz-dark py-[100px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
        <div class="flex flex-col gap-[60px]">
            {{-- Header --}}
            <div class="flex flex-col items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-widest">UČ SA OD NAJLEPŠÍCH</span>
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                </div>
                <h2 class="font-display font-bold text-5xl tracking-wide">NAŠI TRÉNERI</h2>
                <p class="text-[#666666] text-lg text-center">
                    Certifikovaní profesionáli oddaní pomáhať ti dosiahnuť tvoj plný potenciál.
                </p>
            </div>

            {{-- Coaches Grid --}}
            <div class="flex flex-col sm:flex-row justify-center gap-8">
                {{-- Coach 1 --}}
                <div class="w-full sm:w-[380px] bg-[#141414] border border-[#222222] flex flex-col overflow-hidden">
                    <div class="w-full h-auto min-h-[200px] lg:h-[280px] bg-[url('https://images.unsplash.com/photo-1734668488418-ad1024e97e6d?w=600&q=80')] bg-cover bg-center"></div>
                    <div class="p-7 flex flex-col gap-4">
                        <h3 class="text-white text-[22px] font-bold">MENO TRÉNERA</h3>
                        <span class="text-bcz-red text-[11px] font-medium tracking-wider">Hlavný tréner - Parkour &amp; Freerunning</span>
                        <p class="text-[#888888] text-sm leading-relaxed">
                            Certifikovaný inštruktor parkouru s 8+ rokmi učiteľských skúseností. Špecializuje sa na progresiu od začiatočníkov po pokročilých a bezpečnú tréningovú metodológiu.
                        </p>
                        <div class="flex flex-col gap-2">
                            <span class="text-[#555555] text-[10px] font-bold tracking-widest">CERTIFIKÁTY</span>
                            <div class="flex gap-2">
                                <span class="bg-[#1A1A1A] text-[#666666] text-[10px] font-medium px-3 py-1.5">ADAPT Level 2</span>
                                <span class="bg-[#1A1A1A] text-[#666666] text-[10px] font-medium px-3 py-1.5">First Aid</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Coach 2 --}}
                <div class="w-full sm:w-[380px] bg-[#141414] border border-[#222222] flex flex-col overflow-hidden">
                    <div class="w-full h-auto min-h-[200px] lg:h-[280px] bg-[url('https://images.unsplash.com/photo-1758875569284-c57e79ef75e0?w=600&q=80')] bg-cover bg-center"></div>
                    <div class="p-7 flex flex-col gap-4">
                        <h3 class="text-white text-[22px] font-bold">MENO TRÉNERA</h3>
                        <span class="text-bcz-red text-[11px] font-medium tracking-wider">Tréner - Kalistenika &amp; Sila</span>
                        <p class="text-[#888888] text-sm leading-relaxed">
                            Expert na tréning s vlastnou váhou a rozvoj sily. Pomáha športovcom všetkých úrovní budovať funkčnú silu a dosahovať ich fitness ciele.
                        </p>
                        <div class="flex flex-col gap-2">
                            <span class="text-[#555555] text-[10px] font-bold tracking-widest">CERTIFIKÁTY</span>
                            <div class="flex gap-2">
                                <span class="bg-[#1A1A1A] text-[#666666] text-[10px] font-medium px-3 py-1.5">Personal Trainer</span>
                                <span class="bg-[#1A1A1A] text-[#666666] text-[10px] font-medium px-3 py-1.5">Nutrition</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>

    {{-- Values Section --}}
    <section class="bg-[#111111] py-[100px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
        <div class="flex flex-col gap-[60px]">
            {{-- Header --}}
            <div class="flex flex-col items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-widest">ZA ČÍM SI STOJÍME</span>
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                </div>
                <h2 class="font-display font-bold text-5xl tracking-wide">NAŠE HODNOTY</h2>
            </div>

            {{-- Values Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Value 1: Vášeň - Lucide flame --}}
                <div class="bg-bcz-dark border border-[#222222] p-8 flex flex-col gap-5 h-auto min-h-[200px] lg:h-[280px]">
                    <div class="size-14 bg-[#FF2D2D12] flex items-center justify-center self-start">
                        <svg class="w-7 h-7 text-bcz-red" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/></svg>
                    </div>
                    <h3 class="text-white text-xl font-bold">VÁŠEŇ</h3>
                    <p class="text-[#888888] text-sm leading-relaxed">
                        Všetko čo robíme vychádza z hlbokej lásky k pohybu. Táto vášeň nás poháňa posúvať hranice a inšpirovať ostatných.
                    </p>
                </div>

                {{-- Value 2: Komunita - Lucide users --}}
                <div class="bg-bcz-dark border border-[#222222] p-8 flex flex-col gap-5 h-auto min-h-[200px] lg:h-[280px]">
                    <div class="size-14 bg-[#FF2D2D12] flex items-center justify-center self-start">
                        <svg class="w-7 h-7 text-bcz-red" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <h3 class="text-white text-xl font-bold">KOMUNITA</h3>
                    <p class="text-[#888888] text-sm leading-relaxed">
                        Sme silnejší spolu. Naša komunita sa navzájom podporuje, motivúje a oslavuje úspechy každého člena.
                    </p>
                </div>

                {{-- Value 3: Bezpečnosť - Lucide shield --}}
                <div class="bg-bcz-dark border border-[#222222] p-8 flex flex-col gap-5 h-auto min-h-[200px] lg:h-[280px]">
                    <div class="size-14 bg-[#FF2D2D12] flex items-center justify-center self-start">
                        <svg class="w-7 h-7 text-bcz-red" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/></svg>
                    </div>
                    <h3 class="text-white text-xl font-bold">BEZPEČNOSŤ</h3>
                    <p class="text-[#888888] text-sm leading-relaxed">
                        Progres cez správnu techniku a kalkulované riziko. Veríme v inteligentný tréning, ktorý minimalizuje zranenia a maximalizuje rast.
                    </p>
                </div>

                {{-- Value 4: Rast - Lucide trending-up --}}
                <div class="bg-bcz-dark border border-[#222222] p-8 flex flex-col gap-5 h-auto min-h-[200px] lg:h-[280px]">
                    <div class="size-14 bg-[#FF2D2D12] flex items-center justify-center self-start">
                        <svg class="w-7 h-7 text-bcz-red" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
                    </div>
                    <h3 class="text-white text-xl font-bold">RAST</h3>
                    <p class="text-[#888888] text-sm leading-relaxed">
                        Každý deň je príležitosťou na zlepšenie. Prijímame výzvy a vnímame zlyhania ako odrazové mostíky k úspechu.
                    </p>
                </div>
            </div>
        </div>
        </div>
    </section>

    {{-- Gallery Section --}}
    <section class="bg-bcz-dark py-[100px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
        <div class="flex flex-col gap-12">
            {{-- Header --}}
            <div class="flex items-end justify-between">
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                        <span class="text-bcz-red text-xs font-bold tracking-[3px]">MOMENTY</span>
                    </div>
                    <h2 class="font-display font-bold text-5xl tracking-wide">FOTOGALÉRIA</h2>
                </div>
                <a href="#" class="flex items-center gap-2 text-[#888888] text-xs font-bold tracking-wider hover:text-white transition-colors">
                    ZOBRAZIŤ VŠETKY FOTKY
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>

            {{-- Gallery Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 h-[250px] md:h-[320px] lg:h-[400px]">
                <div class="bg-[url('https://images.unsplash.com/photo-1591443749698-ba4db867b594?w=600&q=80')] bg-cover bg-center"></div>
                <div class="grid grid-rows-2 gap-4">
                    <div class="bg-[url('https://images.unsplash.com/photo-1758521959549-27f581bc400f?w=600&q=80')] bg-cover bg-center"></div>
                    <div class="bg-[url('https://images.unsplash.com/photo-1748698534492-746f3950d9ca?w=600&q=80')] bg-cover bg-center"></div>
                </div>
                <div class="grid grid-rows-2 gap-4">
                    <div class="bg-[url('https://images.unsplash.com/photo-1737309534666-1d0d0dd704ab?w=600&q=80')] bg-cover bg-center"></div>
                    <div class="bg-[url('https://images.unsplash.com/photo-1759760300494-7378d88180f9?w=600&q=80')] bg-cover bg-center"></div>
                </div>
            </div>
        </div>
        </div>
    </section>
@endsection
