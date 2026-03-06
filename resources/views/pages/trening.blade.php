@extends('layouts.public')

@section('title', 'Parkour Teens - BCZ Club')

@section('content')
    {{-- Hero Section --}}
    <section class="relative w-full h-[450px] overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1758521958524-4087a1e6f55d?w=1440&q=80')] bg-cover bg-center"></div>
        <div class="absolute inset-0" style="background: linear-gradient(180deg, #0A0A0A 0%, #0A0A0A00 40%, #0A0A0A00 60%, #0A0A0A 100%)"></div>

        <div class="relative w-full h-full flex flex-col items-center justify-center gap-5 max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            {{-- Breadcrumb --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="text-[#666666] text-[11px] font-medium tracking-[2px] hover:text-white transition-colors">DOMOV</a>
                <span class="text-[#444444] text-[11px]">/</span>
                <a href="{{ route('treningy') }}" class="text-[#666666] text-[11px] font-medium tracking-[2px] hover:text-white transition-colors">TRÉNINGY</a>
                <span class="text-[#444444] text-[11px]">/</span>
                <span class="text-bcz-red text-[11px] font-medium tracking-[2px]">PARKOUR TEENS</span>
            </div>

            <h1 class="font-display font-bold text-[32px] md:text-[48px] lg:text-[64px] tracking-wide text-center">PARKOUR TEENS</h1>

            <div class="bg-bcz-red/[0.12] border border-bcz-red px-5 py-2.5">
                <span class="text-bcz-red text-[11px] font-bold tracking-wider">13-17 ROKOV &nbsp;&middot;&nbsp; SKUPINOVÝ TRÉNING &nbsp;&middot;&nbsp; PARKOUR &amp; FREERUNNING</span>
            </div>
        </div>
    </section>

    {{-- Info Section --}}
    <section class="bg-bcz-dark py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col lg:flex-row gap-8 lg:gap-20">
            {{-- Left --}}
            <div class="flex-1 flex flex-col gap-8">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[3px]">O TRÉNINGU</span>
                </div>

                <h2 class="font-display font-bold text-[40px] leading-none tracking-wide">ČO ŤA ČAKÁ<br>NA TRÉNINGU?</h2>

                <p class="text-[#888888] text-[17px] leading-[1.7]">
                    Parkour Teens je skupinový tréning určený pre mladých vo veku 13-17 rokov. Naučíš sa základy parkouru a freerunningU - od bezpečných pádov, cez preskoky a výstupy, až po dynamické pohyby a salto. Tréningy sú zamerané na postupný progres, správnu techniku a hlavne zábavu v skvelej komunite.
                </p>

                {{-- Features --}}
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-4">
                        <div class="size-9 bg-bcz-red/[0.12] flex items-center justify-center shrink-0">
                            <svg class="w-[18px] h-[18px] text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </div>
                        <span class="text-white text-[15px] font-medium">Max. 12 účastníkov na tréning</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="size-9 bg-bcz-red/[0.12] flex items-center justify-center shrink-0">
                            <svg class="w-[18px] h-[18px] text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>
                        </div>
                        <span class="text-white text-[15px] font-medium">Bezpečnostné matrace a vybavenie</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="size-9 bg-bcz-red/[0.12] flex items-center justify-center shrink-0">
                            <svg class="w-[18px] h-[18px] text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
                        </div>
                        <span class="text-white text-[15px] font-medium">Progres pre všetky úrovne</span>
                    </div>
                </div>
            </div>

            {{-- Right: Details Card --}}
            <div class="w-full lg:w-[420px] shrink-0 bg-[#111111] border border-[#222222] p-8 flex flex-col gap-6">
                <span class="text-bcz-red text-xs font-bold tracking-[2px]">DETAILY TRÉNINGU</span>

                <div class="flex flex-col gap-5">
                    <div class="flex justify-between">
                        <span class="text-[#666666] text-sm">Kategória</span>
                        <span class="text-white text-sm font-semibold">Parkour &amp; Freerunning</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#666666] text-sm">Veková skupina</span>
                        <span class="text-white text-sm font-semibold">13-17 rokov</span>
                    </div>
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
                        <span class="text-white text-sm font-semibold">Športová hala Čadca</span>
                    </div>
                </div>

                <div class="w-full h-px bg-[#222222]"></div>

                {{-- Capacity --}}
                <div class="flex flex-col gap-3">
                    <div class="flex justify-between">
                        <span class="text-[#666666] text-sm">Aktuálna kapacita</span>
                        <span class="text-bcz-red text-sm font-semibold">10/12 miest</span>
                    </div>
                    <div class="w-full h-2 bg-[#222222] rounded">
                        <div class="h-full bg-bcz-red rounded" style="width: 83%"></div>
                    </div>
                    <span class="text-bcz-red text-xs font-medium">Zostávajú už len 2 voľné miesta!</span>
                </div>
            </div>
        </div>
        </div>
    </section>

    {{-- Location Section --}}
    <section class="bg-[#111111] py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
        <div class="flex flex-col gap-12">
            {{-- Header --}}
            <div class="flex flex-col items-center gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-normal tracking-wider">LOKÁCIA</span>
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                </div>
                <h2 class="font-display font-bold text-4xl tracking-wide text-center">KDE NÁS NÁJDEŠ</h2>
                <p class="text-[#888888] text-base text-center">Tréning sa koná v Športovej hale Čadca</p>
            </div>

            {{-- Content --}}
            <div class="flex flex-col lg:flex-row gap-6 lg:gap-10">
                {{-- Map --}}
                <div class="flex-1 h-[350px] rounded-xl overflow-hidden bg-[url('https://images.unsplash.com/photo-1757847112041-00b415d07319?w=1080&q=80')] bg-cover bg-center"></div>

                {{-- Details --}}
                <div class="w-full lg:w-[400px] shrink-0 flex flex-col gap-6">
                    {{-- Address Card --}}
                    <div class="bg-bcz-dark rounded-xl border border-[#222222] p-6 flex flex-col gap-4">
                        <div class="flex items-center gap-3">
                            <div class="size-12 bg-bcz-red rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-white text-sm font-bold">Adresa</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2">
                            <span class="text-[#888888] text-sm">Ul. 17. novembra 1296</span>
                            <span class="text-[#888888] text-sm">022 01 Čadca</span>
                            <span class="text-[#888888] text-sm">Slovenská republika</span>
                        </div>
                    </div>

                    {{-- Meeting Card --}}
                    <div class="bg-bcz-dark rounded-xl border border-[#222222] p-6 flex flex-col gap-4">
                        <div class="flex items-center gap-3">
                            <div class="size-12 bg-[#22C55E20] rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-[#22C55E]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-white text-sm font-bold">Stretnutie</span>
                            </div>
                        </div>
                        <p class="text-[#888888] text-sm leading-[1.6]">
                            Stretávame sa 10 minút pred začiatkom tréningu pri hlavnom vchode do športovej haly.
                        </p>
                    </div>

                    {{-- Maps Button --}}
                    <a href="#" class="w-full bg-bcz-red rounded-lg flex items-center justify-center gap-3 py-4 hover:bg-red-700 transition-colors">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
                        <span class="text-white text-base font-semibold">Otvoriť v Google Maps</span>
                    </a>
                </div>
            </div>
        </div>
        </div>
    </section>

    {{-- Coach Section --}}
    <section class="bg-[#111111] py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
        <div class="flex flex-col gap-12">
            {{-- Header --}}
            <div class="flex flex-col items-center gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[3px]">VEDENIE TRÉNINGU</span>
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                </div>
                <h2 class="font-display font-bold text-4xl tracking-wide">TVOJ TRÉNER</h2>
            </div>

            {{-- Coach Card --}}
            <div class="bg-bcz-dark border border-[#222222] p-8 flex flex-col sm:flex-row gap-6 lg:gap-10">
                <img src="https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=400&q=80" alt="Michal Čečko" class="w-full sm:w-[200px] h-[240px] object-cover shrink-0">
                <div class="flex-1 flex flex-col justify-center gap-5">
                    <h3 class="font-display font-bold text-[28px] tracking-wide">MICHAL ČEČKO</h3>
                    <span class="text-bcz-red text-xs font-medium tracking-wider">Tréner Parkour &amp; Street Workout</span>
                    <p class="text-[#888888] text-[15px] leading-[1.7]">
                        8 rokov aktívneho tréningu a 5 rokov skúseností s vedením skupín. Michal sa špecializuje na výuku techniky a bezpečný progres. Jeho tréningy sú známe skvelou atmosférou a individuálnym prístupom ku každému účastníkovi.
                    </p>
                    <div class="flex gap-3">
                        <span class="bg-[#222222] text-[#AAAAAA] text-[11px] font-medium px-3.5 py-2">Freerunning</span>
                        <span class="bg-[#222222] text-[#AAAAAA] text-[11px] font-medium px-3.5 py-2">Street Workout</span>
                        <span class="bg-[#222222] text-[#AAAAAA] text-[11px] font-medium px-3.5 py-2">Prvá pomoc</span>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>

    {{-- Gallery Section --}}
    <section class="bg-bcz-dark py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
        <div class="flex flex-col gap-12">
            {{-- Header --}}
            <div class="flex flex-col items-center gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[2px]">Z TRÉNINGU</span>
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                </div>
                <h2 class="font-display font-bold text-4xl tracking-wide">GALÉRIA</h2>
                <p class="text-[#888888] text-base text-center">Pozri sa, ako vyzerá tento tréning v akcii</p>
            </div>

            {{-- Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                {{-- Col 1 --}}
                <div class="flex flex-col gap-5">
                    <div class="w-full h-[280px] rounded-lg overflow-hidden bg-[url('https://images.unsplash.com/photo-1754258166661-b827f0d558aa?w=600&q=80')] bg-cover bg-center"></div>
                    <div class="w-full h-[200px] rounded-lg overflow-hidden bg-[url('https://images.unsplash.com/photo-1758521959675-5874879f3977?w=600&q=80')] bg-cover bg-center"></div>
                </div>

                {{-- Col 2 --}}
                <div class="flex flex-col gap-5">
                    <div class="w-full h-[200px] rounded-lg overflow-hidden bg-[url('https://images.unsplash.com/photo-1758274526293-983d157dabf0?w=600&q=80')] bg-cover bg-center"></div>
                    <div class="relative w-full h-[280px] rounded-lg overflow-hidden bg-[url('https://images.unsplash.com/photo-1589413566549-5cdc36605c7a?w=600&q=80')] bg-cover bg-center">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="size-16 bg-bcz-red/80 rounded-full flex items-center justify-center">
                                <svg class="w-7 h-7 text-white ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Col 3 --}}
                <div class="flex flex-col gap-5">
                    <div class="w-full h-[240px] rounded-lg overflow-hidden bg-[url('https://images.unsplash.com/photo-1758798474157-99bd084de57f?w=600&q=80')] bg-cover bg-center"></div>
                    <div class="w-full h-[240px] rounded-lg overflow-hidden bg-[url('https://images.unsplash.com/photo-1747336406564-717968046260?w=600&q=80')] bg-cover bg-center"></div>
                </div>
            </div>
        </div>
        </div>
    </section>

    {{-- Form Section --}}
    <section class="bg-bcz-dark py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
        <div class="flex flex-col items-center gap-12">
            {{-- Header --}}
            <div class="flex flex-col items-center gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[3px]">REGISTRÁCIA</span>
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                </div>
                <h2 class="font-display font-bold text-4xl tracking-wide">PRIHLÁS SA NA TRÉNING</h2>
                <p class="text-[#666666] text-base text-center">Vypľň formulár a my sa ti ozveme s potvrdením</p>
            </div>

            {{-- Form Card --}}
            <div class="w-full lg:w-[600px] bg-[#111111] border border-[#222222] p-10 flex flex-col items-center gap-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 w-full">
                    <div class="flex flex-col gap-2">
                        <label class="text-[#888888] text-[13px] font-medium">Meno</label>
                        <input type="text" placeholder="Tvoje meno" class="bg-bcz-dark border border-[#333333] text-white text-sm px-4 py-3.5 w-full placeholder-[#555555] focus:border-bcz-red focus:outline-none transition-colors">
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-[#888888] text-[13px] font-medium">Priezvisko</label>
                        <input type="text" placeholder="Tvoje priezvisko" class="bg-bcz-dark border border-[#333333] text-white text-sm px-4 py-3.5 w-full placeholder-[#555555] focus:border-bcz-red focus:outline-none transition-colors">
                    </div>
                </div>

                <div class="flex flex-col gap-2 w-full">
                    <label class="text-[#888888] text-[13px] font-medium">Email</label>
                    <input type="email" placeholder="tvoj@email.sk" class="bg-bcz-dark border border-[#333333] text-white text-sm px-4 py-3.5 w-full placeholder-[#555555] focus:border-bcz-red focus:outline-none transition-colors">
                </div>

                <div class="flex flex-col gap-2 w-full">
                    <label class="text-[#888888] text-[13px] font-medium">Telefón</label>
                    <input type="tel" placeholder="+421 ..." class="bg-bcz-dark border border-[#333333] text-white text-sm px-4 py-3.5 w-full placeholder-[#555555] focus:border-bcz-red focus:outline-none transition-colors">
                </div>

                <div class="flex flex-col gap-2 w-full">
                    <label class="text-[#888888] text-[13px] font-medium">Vek účastníka</label>
                    <input type="number" placeholder="napr. 15" class="bg-bcz-dark border border-[#333333] text-white text-sm px-4 py-3.5 w-full placeholder-[#555555] focus:border-bcz-red focus:outline-none transition-colors">
                </div>

                <div class="w-full h-px bg-[#222222]"></div>

                <button type="button" class="w-full bg-bcz-red text-white text-sm font-bold tracking-wider py-[18px] hover:bg-red-700 transition-colors">
                    ODOSLAŤ PRIHLÁŠKU
                </button>

                <span class="text-[#555555] text-xs text-center">Odoslaním súhlasíš so spracovaním osobných údajov.</span>
            </div>
        </div>
        </div>
    </section>
@endsection
