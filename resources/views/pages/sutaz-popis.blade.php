@extends('layouts.public')

@section('title', 'World Freerunning Championship 2026 | BCZ Club')

@section('content')
    {{-- Hero Section --}}
    <section class="relative w-full h-[400px] md:h-[450px] lg:h-[500px] overflow-hidden">
        <div class="absolute inset-0 bg-[#1A1A1A]"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-transparent to-[#0A0A0A]"></div>

        <div class="absolute bottom-0 left-0 right-0 pb-[60px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col gap-4">
            {{-- Breadcrumb --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="text-[#666666] text-[11px] font-medium tracking-[2px] hover:text-white transition-colors">DOMOV</a>
                <span class="text-[#444444] text-[11px]">/</span>
                <a href="#" class="text-[#666666] text-[11px] font-medium tracking-[2px] hover:text-white transition-colors">SUTAZE</a>
                <span class="text-[#444444] text-[11px]">/</span>
                <span class="text-bcz-red text-[11px] font-medium tracking-[2px]">WORLD FREERUNNING CHAMPIONSHIP</span>
            </div>

            {{-- Badge --}}
            <span class="bg-bcz-red text-white text-xs font-bold px-3.5 py-1.5 rounded-md w-fit">KOMBINOVANY FORMAT</span>

            {{-- Title --}}
            <h1 class="font-display font-bold text-[28px] md:text-[40px] lg:text-[56px] tracking-wide text-white">World Freerunning Championship 2026</h1>
        </div>
        </div>
    </section>

    {{-- Info Strip --}}
    <section class="bg-bcz-dark border-b border-[#222222]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 py-6">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            {{-- Datum --}}
            <div class="bg-[#111111] border border-[#222222] rounded-lg p-3 flex flex-col gap-1.5">
                <span class="text-[#666666] text-[10px] font-bold tracking-[2px]">DATUM</span>
                <span class="text-white text-sm font-medium">15. - 16. marca 2026</span>
            </div>
            {{-- Miesto --}}
            <div class="bg-[#111111] border border-[#222222] rounded-lg p-3 flex flex-col gap-1.5">
                <span class="text-[#666666] text-[10px] font-bold tracking-[2px]">MIESTO</span>
                <span class="text-white text-sm font-medium">Bratislava, Slovensko</span>
            </div>
            {{-- Format --}}
            <div class="bg-[#111111] border border-[#222222] rounded-lg p-3 flex flex-col gap-1.5">
                <span class="text-[#666666] text-[10px] font-bold tracking-[2px]">FORMAT</span>
                <span class="text-white text-sm font-medium">Kvalifikacia + Battle</span>
            </div>
            {{-- Kategorie --}}
            <div class="bg-[#111111] border border-[#222222] rounded-lg p-3 flex flex-col gap-1.5">
                <span class="text-[#666666] text-[10px] font-bold tracking-[2px]">KATEGORIE</span>
                <span class="text-white text-sm font-medium">6 kategorii</span>
            </div>
            {{-- Stav --}}
            <div class="bg-[#111111] border border-[#222222] rounded-lg p-3 flex flex-col gap-1.5">
                <span class="text-[#666666] text-[10px] font-bold tracking-[2px]">STAV</span>
                <span class="text-[#22C55E] text-sm font-medium">Registracia otvorena</span>
            </div>
        </div>
        </div>
    </section>

    {{-- Tab Bar --}}
    <section class="bg-[#111111] border-b border-[#222222]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
        <nav class="flex gap-8">
            <a href="{{ route('sutaz.popis') }}" class="relative py-4 text-white text-sm font-semibold border-b-[3px] border-bcz-red">
                Popis
            </a>
            <a href="{{ route('sutaz.harmonogram') }}" class="relative py-4 text-[#666666] text-sm font-semibold border-b-[3px] border-transparent hover:text-white transition-colors">
                Harmonogram
            </a>
            <a href="{{ route('sutaz.vysledky') }}" class="relative py-4 text-[#666666] text-sm font-semibold border-b-[3px] border-transparent hover:text-white transition-colors">
                Vysledky
            </a>
            <a href="{{ route('sutaz.registracia') }}" class="relative py-4 text-[#666666] text-sm font-semibold border-b-[3px] border-transparent hover:text-white transition-colors">
                Registracia
            </a>
        </nav>
        </div>
    </section>

    {{-- Content Section --}}
    <section class="py-[60px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col lg:flex-row gap-10 lg:gap-16">
        {{-- LEFT - Main Content --}}
        <div class="flex-1 flex flex-col gap-12">
            {{-- O sutazi --}}
            <div class="flex flex-col gap-6">
                <h2 class="font-display font-bold text-[32px] tracking-wide text-white">O sutazi</h2>
                <p class="text-[#CCCCCC] text-base leading-relaxed">
                    World Freerunning Championship 2026 je najvacsie medzinarodne podujatie v oblasti freeruningu a parkouru na Slovensku. Sutaz prinasa unikatny kombinovany format, ktory spaja kvalifikacne kola s priamymi battle suveniami. Ucastnici z celej Europy sa stretnu v Bratislave, aby predviedli svoje najlepsie triky, kreativitu a atletickost.
                </p>
                <p class="text-[#CCCCCC] text-base leading-relaxed">
                    Podujatie organizuje BCZ Club v spolupraci s medzinarodnou parkourovou federaciou. Sutaz sa kona v modernom sportovom komplexe s profesionalnym zabezpecenim, rozhodcami svetoveho formatu a zivym prenosom pre fanusikov po celom svete. Kazdy ucastnik ma moznost kvalifikovat sa do medzinarodneho rebricka.
                </p>
            </div>

            {{-- Program sutaze --}}
            <div class="flex flex-col gap-6">
                <h3 class="font-display font-bold text-[24px] tracking-wide text-white">Program sutaze</h3>
                <div class="rounded-xl bg-[#111111] border border-[#222222] overflow-hidden">
                    {{-- Table Header --}}
                    <div class="grid grid-cols-4 gap-4 px-5 py-3 border-b border-[#222222] bg-[#0D0D0D]">
                        <span class="text-[#666666] text-[11px] font-bold tracking-[2px]">DEN</span>
                        <span class="text-[#666666] text-[11px] font-bold tracking-[2px]">DISCIPLINA</span>
                        <span class="text-[#666666] text-[11px] font-bold tracking-[2px]">CAS</span>
                        <span class="text-[#666666] text-[11px] font-bold tracking-[2px]">MIESTO</span>
                    </div>
                    {{-- Row 1 --}}
                    <div class="grid grid-cols-4 gap-4 px-5 py-4 border-b border-[#222222]">
                        <span class="text-white text-sm">Sobota</span>
                        <span class="text-[#CCCCCC] text-sm">Freerun Kvalifikacia</span>
                        <span class="text-[#CCCCCC] text-sm">09:00 - 12:00</span>
                        <span class="text-[#CCCCCC] text-sm">Hala A</span>
                    </div>
                    {{-- Row 2 --}}
                    <div class="grid grid-cols-4 gap-4 px-5 py-4 border-b border-[#222222]">
                        <span class="text-white text-sm">Sobota</span>
                        <span class="text-[#CCCCCC] text-sm">Speed Run</span>
                        <span class="text-[#CCCCCC] text-sm">13:00 - 15:00</span>
                        <span class="text-[#CCCCCC] text-sm">Hala B</span>
                    </div>
                    {{-- Row 3 --}}
                    <div class="grid grid-cols-4 gap-4 px-5 py-4 border-b border-[#222222]">
                        <span class="text-white text-sm">Sobota</span>
                        <span class="text-[#CCCCCC] text-sm">Skill Showcase</span>
                        <span class="text-[#CCCCCC] text-sm">16:00 - 18:00</span>
                        <span class="text-[#CCCCCC] text-sm">Hlavne podium</span>
                    </div>
                    {{-- Row 4 --}}
                    <div class="grid grid-cols-4 gap-4 px-5 py-4 border-b border-[#222222]">
                        <span class="text-white text-sm">Nedela</span>
                        <span class="text-[#CCCCCC] text-sm">Battle Semifinale</span>
                        <span class="text-[#CCCCCC] text-sm">10:00 - 13:00</span>
                        <span class="text-[#CCCCCC] text-sm">Hlavne podium</span>
                    </div>
                    {{-- Row 5 --}}
                    <div class="grid grid-cols-4 gap-4 px-5 py-4">
                        <span class="text-white text-sm">Nedela</span>
                        <span class="text-[#CCCCCC] text-sm">Battle Finale</span>
                        <span class="text-[#CCCCCC] text-sm">15:00 - 18:00</span>
                        <span class="text-[#CCCCCC] text-sm">Hlavne podium</span>
                    </div>
                </div>
            </div>

            {{-- Sutazne kategorie --}}
            <div class="flex flex-col gap-6">
                <h3 class="font-display font-bold text-[24px] tracking-wide text-white">Sutazne kategorie</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Category 1 --}}
                    <div class="rounded-xl bg-[#111111] border border-[#222222] p-5 flex flex-col gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-bcz-red"></div>
                            <span class="text-white font-bold text-[15px]">Freerun Open</span>
                        </div>
                        <p class="text-[#888888] text-sm leading-relaxed">Otvorena kategoria pre vsetkych ucastnikov bez vekovych obmedzeni.</p>
                    </div>
                    {{-- Category 2 --}}
                    <div class="rounded-xl bg-[#111111] border border-[#222222] p-5 flex flex-col gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-bcz-red"></div>
                            <span class="text-white font-bold text-[15px]">Freerun Junior</span>
                        </div>
                        <p class="text-[#888888] text-sm leading-relaxed">Kategoria pre mladych atletov do 16 rokov.</p>
                    </div>
                    {{-- Category 3 --}}
                    <div class="rounded-xl bg-[#111111] border border-[#222222] p-5 flex flex-col gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-bcz-red"></div>
                            <span class="text-white font-bold text-[15px]">Speed Run</span>
                        </div>
                        <p class="text-[#888888] text-sm leading-relaxed">Rychlostna disciplina na case cez pripravenu drahu.</p>
                    </div>
                    {{-- Category 4 --}}
                    <div class="rounded-xl bg-[#111111] border border-[#222222] p-5 flex flex-col gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-bcz-red"></div>
                            <span class="text-white font-bold text-[15px]">Skill Battle</span>
                        </div>
                        <p class="text-[#888888] text-sm leading-relaxed">Priame suboje 1v1 v troch kolach s hodnotenim porotou.</p>
                    </div>
                    {{-- Category 5 --}}
                    <div class="rounded-xl bg-[#111111] border border-[#222222] p-5 flex flex-col gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-bcz-red"></div>
                            <span class="text-white font-bold text-[15px]">Creative Flow</span>
                        </div>
                        <p class="text-[#888888] text-sm leading-relaxed">Kategoria zamerana na kreativitu, plynulost a originalitu.</p>
                    </div>
                    {{-- Category 6 --}}
                    <div class="rounded-xl bg-[#111111] border border-[#222222] p-5 flex flex-col gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-bcz-red"></div>
                            <span class="text-white font-bold text-[15px]">Team Battle</span>
                        </div>
                        <p class="text-[#888888] text-sm leading-relaxed">Timova kategoria - 3 atleti spolupracuju na spolocnej zostave.</p>
                    </div>
                </div>

                {{-- Discipline & Scoring Info --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="rounded-xl bg-[#0D0D0D] border border-[#222222] p-5 flex flex-col gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-0.5 bg-bcz-red"></div>
                            <span class="text-bcz-red text-[11px] font-bold tracking-[2px]">DISCIPLINY</span>
                        </div>
                        <ul class="flex flex-col gap-2">
                            <li class="flex items-center gap-3">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#444444]"></span>
                                <span class="text-[#AAAAAA] text-sm">Freerunning</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#444444]"></span>
                                <span class="text-[#AAAAAA] text-sm">Speed Run</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#444444]"></span>
                                <span class="text-[#AAAAAA] text-sm">Skill Battle (1v1)</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#444444]"></span>
                                <span class="text-[#AAAAAA] text-sm">Creative Flow</span>
                            </li>
                        </ul>
                    </div>
                    <div class="rounded-xl bg-[#0D0D0D] border border-[#222222] p-5 flex flex-col gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-0.5 bg-bcz-red"></div>
                            <span class="text-bcz-red text-[11px] font-bold tracking-[2px]">HODNOTENIE</span>
                        </div>
                        <ul class="flex flex-col gap-2">
                            <li class="flex items-center gap-3">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#444444]"></span>
                                <span class="text-[#AAAAAA] text-sm">Narocnost trikov (0 - 10)</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#444444]"></span>
                                <span class="text-[#AAAAAA] text-sm">Kreativita a originalita (0 - 10)</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#444444]"></span>
                                <span class="text-[#AAAAAA] text-sm">Flow a plynulost (0 - 10)</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#444444]"></span>
                                <span class="text-[#AAAAAA] text-sm">Ciste prevedenie (0 - 10)</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT - Sidebar --}}
        <div class="w-full lg:w-[320px] flex flex-col gap-6 shrink-0">
            {{-- Event Info Card --}}
            <div class="rounded-xl bg-[#111111] p-6 border border-[#222222] flex flex-col gap-5">
                <h3 class="text-white font-bold text-lg">Informacie o sutazi</h3>
                <div class="h-px bg-[#222222]"></div>
                <div class="flex flex-col gap-4">
                    <div class="flex justify-between">
                        <span class="text-[#888888] text-sm">Datum</span>
                        <span class="text-white text-sm">15. - 16. marca 2026</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#888888] text-sm">Miesto</span>
                        <span class="text-white text-sm">Bratislava, Slovensko</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#888888] text-sm">Typ</span>
                        <span class="text-bcz-red text-sm">Kombinovany format</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#888888] text-sm">Kategorie</span>
                        <span class="text-white text-sm">6 kategorii</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#888888] text-sm">Organizator</span>
                        <span class="text-white text-sm">BCZ Club</span>
                    </div>
                </div>
            </div>

            {{-- CTA Card --}}
            <div class="rounded-xl bg-[#FF2D2D10] p-6 border border-[#FF2D2D40] flex flex-col gap-4">
                <h3 class="text-white font-bold">Zaregistrujte sa</h3>
                <p class="text-[#AAAAAA] text-sm leading-relaxed">Registracia je otvorena do 10. marca 2026. Pocet miest je obmedzeny.</p>
                <a href="{{ route('sutaz.registracia') }}" class="bg-bcz-red text-white rounded-lg h-11 w-full flex items-center justify-center gap-2 text-sm font-semibold hover:bg-bcz-red/90 transition-colors">
                    Registrovat sa
                </a>
            </div>

            {{-- Share Card --}}
            <div class="rounded-xl bg-[#111111] p-6 border border-[#222222] flex flex-col gap-4">
                <h3 class="text-white font-bold">Zdielat</h3>
                <div class="flex gap-3">
                    <button class="w-10 h-10 bg-[#1A1A1A] rounded-lg flex items-center justify-center text-[#888888] text-sm font-bold hover:bg-[#222222] transition-colors">f</button>
                    <button class="w-10 h-10 bg-[#1A1A1A] rounded-lg flex items-center justify-center text-[#888888] text-sm font-bold hover:bg-[#222222] transition-colors">X</button>
                    <button class="w-10 h-10 bg-[#1A1A1A] rounded-lg flex items-center justify-center text-[#888888] text-sm font-bold hover:bg-[#222222] transition-colors">in</button>
                    <button class="w-10 h-10 bg-[#1A1A1A] rounded-lg flex items-center justify-center text-[#888888] text-sm font-bold hover:bg-[#222222] transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                    </button>
                </div>
            </div>
        </div>
        </div>
    </section>

    {{-- Gallery Section --}}
    <section class="bg-[#0D0D0D] py-[60px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col gap-8">
        {{-- Header --}}
        <div class="flex justify-between items-end">
            <div class="flex flex-col gap-2">
                <h2 class="font-display font-bold text-[32px] tracking-wide text-white">Galeria</h2>
                <p class="text-[#888888] text-sm">Fotografie z minulych rocnikov</p>
            </div>
            <div class="flex gap-3">
                <button class="rounded-lg bg-[#111111] border border-[#222222] w-10 h-10 flex items-center justify-center text-white hover:bg-[#1A1A1A] transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                </button>
                <button class="rounded-lg bg-[#111111] border border-[#222222] w-10 h-10 flex items-center justify-center text-white hover:bg-[#1A1A1A] transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </button>
            </div>
        </div>

        {{-- Slideshow Row --}}
        <div class="flex gap-4 overflow-hidden">
            <div class="w-full lg:w-[600px] h-[400px] rounded-xl bg-[#1A1A1A] shrink-0"></div>
            <div class="hidden sm:block w-[300px] h-[400px] rounded-xl bg-[#1A1A1A] opacity-60 shrink-0"></div>
            <div class="hidden sm:block w-[300px] h-[400px] rounded-xl bg-[#1A1A1A] opacity-60 shrink-0"></div>
        </div>

        {{-- Dots --}}
        <div class="flex justify-center gap-2">
            <div class="w-2 h-2 rounded-full bg-bcz-red"></div>
            <div class="w-2 h-2 rounded-full bg-[#333333]"></div>
            <div class="w-2 h-2 rounded-full bg-[#333333]"></div>
            <div class="w-2 h-2 rounded-full bg-[#333333]"></div>
            <div class="w-2 h-2 rounded-full bg-[#333333]"></div>
        </div>

        {{-- Grid --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div class="h-[200px] rounded-xl bg-[#1A1A1A]"></div>
            <div class="h-[200px] rounded-xl bg-[#1A1A1A]"></div>
            <div class="h-[200px] rounded-xl bg-[#1A1A1A]"></div>
            <div class="h-[200px] rounded-xl bg-[#1A1A1A]"></div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div class="h-[200px] rounded-xl bg-[#1A1A1A]"></div>
            <div class="h-[200px] rounded-xl bg-[#1A1A1A]"></div>
            <div class="h-[200px] rounded-xl bg-[#1A1A1A]"></div>
            <div class="h-[200px] rounded-xl bg-[#1A1A1A]"></div>
        </div>
        </div>
    </section>

    {{-- More Competitions Section --}}
    <section class="py-[60px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col gap-8">
        {{-- Header --}}
        <div class="flex items-end justify-between">
            <h2 class="font-display font-bold text-[32px] tracking-wide text-white">Dalsie sutaze</h2>
            <a href="#" class="border border-bcz-red text-bcz-red rounded-lg px-6 py-3 text-sm font-semibold hover:bg-bcz-red hover:text-white transition-colors">
                Vsetky sutaze
            </a>
        </div>

        {{-- 3-column Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- Competition 1 --}}
            <div class="bg-[#111111] rounded-2xl overflow-hidden">
                <div class="w-full h-[200px] bg-[#1A1A1A]"></div>
                <div class="flex flex-col gap-3 p-5">
                    <span class="bg-bcz-red/20 text-bcz-red text-[10px] font-bold px-2.5 py-1 rounded w-fit">FREERUN</span>
                    <h3 class="text-white text-[18px] font-bold">Slovak Freerun Open 2026</h3>
                    <p class="text-[#888888] text-[13px]">Otvorena sutaz vo freeruningu pre vsetky vekove kategorie.</p>
                    <span class="text-[#666666] text-[12px]">April 2026 &middot; Kosice</span>
                </div>
            </div>

            {{-- Competition 2 --}}
            <div class="bg-[#111111] rounded-2xl overflow-hidden">
                <div class="w-full h-[200px] bg-[#1A1A1A]"></div>
                <div class="flex flex-col gap-3 p-5">
                    <span class="bg-[#3B82F6]/20 text-[#3B82F6] text-[10px] font-bold px-2.5 py-1 rounded w-fit">PARKOUR</span>
                    <h3 class="text-white text-[18px] font-bold">Parkour Speed Challenge</h3>
                    <p class="text-[#888888] text-[13px]">Rychlostna parkourova sutaz na profesionalnej drahe.</p>
                    <span class="text-[#666666] text-[12px]">Maj 2026 &middot; Bratislava</span>
                </div>
            </div>

            {{-- Competition 3 --}}
            <div class="bg-[#111111] rounded-2xl overflow-hidden">
                <div class="w-full h-[200px] bg-[#1A1A1A]"></div>
                <div class="flex flex-col gap-3 p-5">
                    <span class="bg-[#22C55E]/20 text-[#22C55E] text-[10px] font-bold px-2.5 py-1 rounded w-fit">BATTLE</span>
                    <h3 class="text-white text-[18px] font-bold">Street Battle Zilina</h3>
                    <p class="text-[#888888] text-[13px]">1v1 battle sutaz v exterierovom prostredi.</p>
                    <span class="text-[#666666] text-[12px]">Jun 2026 &middot; Zilina</span>
                </div>
            </div>
        </div>
        </div>
    </section>
@endsection