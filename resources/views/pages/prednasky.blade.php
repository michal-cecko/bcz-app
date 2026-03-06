@extends('layouts.public')

@section('title', 'Inšpiratívne Prednášky - BCZ Club')

@section('content')
    {{-- Hero Section --}}
    <section class="relative w-full h-[700px] overflow-hidden bg-bcz-dark">
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1755548413928-4aaeba7c740e?w=1080&q=80')] bg-cover bg-center"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-[#0A0A0A] via-[#0A0A0ADD] to-transparent"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-[#0A0A0AEE] via-[#0A0A0ABB] to-transparent"></div>
        <div class="relative max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 h-full flex flex-col justify-end pb-20 gap-6">
            {{-- Breadcrumbs --}}
            <div class="flex items-center gap-2 text-[13px]">
                <a href="{{ route('home') }}" class="text-bcz-muted hover:text-white transition-colors">Domov</a>
                <span class="text-bcz-muted">›</span>
                <a href="{{ route('vystupenia-workshopy') }}" class="text-bcz-muted hover:text-white transition-colors">Vystúpenia & Workshopy</a>
                <span class="text-bcz-muted">›</span>
                <span class="text-white">Prednášky</span>
            </div>

            {{-- Badge --}}
            <div class="bg-[#3B82F6]/10 border border-[#3B82F6]/25 rounded-md px-5 py-2 w-fit">
                <span class="text-[#3B82F6] text-[12px] font-bold tracking-[2px]">MOTIVAČNÉ PREDNÁŠKY</span>
            </div>

            {{-- Title --}}
            <h1 class="font-display font-bold text-[48px] md:text-[72px] lg:text-[96px] leading-[0.95] tracking-wide text-white">
                INŠPIRATÍVNE<br>PREDNÁŠKY
            </h1>

            {{-- Description --}}
            <p class="text-bcz-light text-[18px] md:text-[20px] leading-[1.6] max-w-[700px]">
                Motivačné prednášky pre školy, firmy a organizácie. Inšpirujeme mladých ľudí príbehom o disciplíne, vytrvalosti a sile pohybu.
            </p>
        </div>
    </section>

    {{-- About Section --}}
    <section class="bg-[#111111] py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col lg:flex-row items-center gap-16">
                {{-- Left --}}
                <div class="flex flex-col gap-6 flex-1">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-0.5 bg-[#3B82F6]"></div>
                        <span class="text-[#3B82F6] text-[12px] font-bold tracking-[2px]">O PREDNÁŠKACH</span>
                    </div>
                    <h2 class="font-display font-bold text-[36px] md:text-[48px] leading-[1.1] tracking-wide text-white">
                        Inšpirujeme Príbehom<br>a Pohybom
                    </h2>
                    <p class="text-bcz-lighter text-[16px] leading-[1.7]">
                        Naše prednášky sú viac než len slová. Sú to skutočné príbehy členov BCZ Clubu, ktorí prostredníctvom street workoutu a kalisteniky objavili silu disciplíny, trpezlivosti a vytrvalosti.
                    </p>
                    <p class="text-bcz-muted text-[16px] leading-[1.7]">
                        Prednášame na školách, v firmách aj na konferenciách. Učíme mladých ľudí, že cesta k úspechu vedie cez tvrdú prácu, správne nastavenie mysle a zdravý životný štýl. Každá prednáška je kombináciou motivačného rozprávania a praktických ukážok.
                    </p>
                </div>

                {{-- Right --}}
                <div class="flex-1 w-full">
                    <div class="w-full h-[450px] rounded-2xl bg-[url('https://images.unsplash.com/photo-1633643333515-ef3727546bd4?w=1080&q=80')] bg-cover bg-center"></div>
                </div>
            </div>
        </div>
    </section>

    {{-- Topics Section --}}
    <section class="bg-bcz-dark py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col items-center gap-12">
                {{-- Header --}}
                <div class="flex flex-col items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-0.5 bg-[#3B82F6]"></div>
                        <span class="text-[#3B82F6] text-[12px] font-bold tracking-[2px]">TÉMY</span>
                        <div class="w-10 h-0.5 bg-[#3B82F6]"></div>
                    </div>
                    <h2 class="font-display font-bold text-[36px] md:text-[48px] tracking-wide text-white text-center">O Čom Prednášame</h2>
                    <p class="text-bcz-muted text-[16px] text-center">Každá prednáška je prispôsobená publiku a prináša praktické posolstvo</p>
                </div>

                {{-- Cards Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 w-full">
                    {{-- Card 1 --}}
                    <div class="bg-[#111111] border border-bcz-border rounded-2xl p-8 flex flex-col gap-5">
                        <div class="w-14 h-14 bg-[#3B82F6]/10 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-[#3B82F6]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5a3 3 0 1 0-5.997.125 4 4 0 0 0-2.526 5.77 4 4 0 0 0 .556 6.588A4 4 0 1 0 12 18Z"/><path d="M12 5a3 3 0 1 1 5.997.125 4 4 0 0 1 2.526 5.77 4 4 0 0 1-.556 6.588A4 4 0 1 1 12 18Z"/><path d="M15 13a4.5 4.5 0 0 1-3-4 4.5 4.5 0 0 1-3 4"/><path d="M17.599 6.5a3 3 0 0 0 .399-1.375"/><path d="M6.003 5.125A3 3 0 0 0 6.401 6.5"/><path d="M3.477 10.896a4 4 0 0 1 .585-.396"/><path d="M19.938 10.5a4 4 0 0 1 .585.396"/><path d="M6 18a4 4 0 0 1-1.967-.516"/><path d="M19.967 17.484A4 4 0 0 1 18 18"/></svg>
                        </div>
                        <h3 class="font-display font-bold text-[24px] tracking-wide text-white">Správne Nastavenie Mysle</h3>
                        <p class="text-bcz-muted text-[14px] leading-[1.6]">Growth mindset a pozitívne myslenie. Ako zmeniť pohľad na prekážky a premeniť ich na príležitosti.</p>
                    </div>

                    {{-- Card 2 --}}
                    <div class="bg-[#111111] border border-bcz-border rounded-2xl p-8 flex flex-col gap-5">
                        <div class="w-14 h-14 bg-[#3B82F6]/10 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-[#3B82F6]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/></svg>
                        </div>
                        <h3 class="font-display font-bold text-[24px] tracking-wide text-white">Hodnota Disciplíny</h3>
                        <p class="text-bcz-muted text-[14px] leading-[1.6]">Prečo je disciplína základom úspechu. Denné návyky a rutiny, ktoré formujú charakter a budujú odolnosť.</p>
                    </div>

                    {{-- Card 3 --}}
                    <div class="bg-[#111111] border border-bcz-border rounded-2xl p-8 flex flex-col gap-5">
                        <div class="w-14 h-14 bg-[#3B82F6]/10 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-[#3B82F6]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"/></svg>
                        </div>
                        <h3 class="font-display font-bold text-[24px] tracking-wide text-white">Sila Pohybu</h3>
                        <p class="text-bcz-muted text-[14px] leading-[1.6]">Fyzická aktivita ako nástroj osobného rastu. Benefity cvičenia pre telo aj myseľ a ich vplyv na každodenný život.</p>
                    </div>

                    {{-- Card 4 --}}
                    <div class="bg-[#111111] border border-bcz-border rounded-2xl p-8 flex flex-col gap-5">
                        <div class="w-14 h-14 bg-[#3B82F6]/10 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-[#3B82F6]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.26 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg>
                        </div>
                        <h3 class="font-display font-bold text-[24px] tracking-wide text-white">Od Sna k Realite</h3>
                        <p class="text-bcz-muted text-[14px] leading-[1.6]">Ako premeniť víziu na skutočnosť. Príbeh BCZ Clubu od garážových tréningov po celoslovenské vystúpenia a medzinárodné súťaže.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- For Whom Section --}}
    <section class="bg-[#0D0D0D] py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col items-center gap-12">
                {{-- Header --}}
                <div class="flex flex-col items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-0.5 bg-[#3B82F6]"></div>
                        <span class="text-[#3B82F6] text-[12px] font-bold tracking-[2px]">CIEĽOVÉ SKUPINY</span>
                        <div class="w-10 h-0.5 bg-[#3B82F6]"></div>
                    </div>
                    <h2 class="font-display font-bold text-[36px] md:text-[48px] tracking-wide text-white text-center">Komu Sú Prednášky Určené</h2>
                </div>

                {{-- Cards Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 w-full">
                    {{-- Card 1 --}}
                    <div class="bg-[#111111] border border-bcz-border rounded-2xl p-8 flex flex-col gap-4">
                        <div class="w-14 h-14 bg-[#3B82F6]/10 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-[#3B82F6]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                        </div>
                        <h3 class="font-display font-bold text-[24px] tracking-wide text-white">Základné Školy</h3>
                        <p class="text-bcz-muted text-[14px] leading-[1.6]">Interaktívne prednášky prispôsobené veku žiakov. Učíme deti hodnotu pohybu, trpezlivosti a tímovej práce hravou formou.</p>
                    </div>

                    {{-- Card 2 --}}
                    <div class="bg-[#111111] border border-bcz-border rounded-2xl p-8 flex flex-col gap-4">
                        <div class="w-14 h-14 bg-[#3B82F6]/10 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-[#3B82F6]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z"/><path d="M22 10v6"/><path d="M6 12.5V16a6 3 0 0 0 12 0v-3.5"/></svg>
                        </div>
                        <h3 class="font-display font-bold text-[24px] tracking-wide text-white">Stredné Školy</h3>
                        <p class="text-bcz-muted text-[14px] leading-[1.6]">Motivačné prednášky pre tínedžerov. Reálne príbehy o prekonávaní prekážok, budovaní sebadôvery a hľadaní vlastnej cesty.</p>
                    </div>

                    {{-- Card 3 --}}
                    <div class="bg-[#111111] border border-bcz-border rounded-2xl p-8 flex flex-col gap-4">
                        <div class="w-14 h-14 bg-[#3B82F6]/10 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-[#3B82F6]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="20" x="4" y="2" rx="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01"/><path d="M16 6h.01"/><path d="M12 6h.01"/><path d="M12 10h.01"/><path d="M12 14h.01"/><path d="M16 10h.01"/><path d="M16 14h.01"/><path d="M8 10h.01"/><path d="M8 14h.01"/></svg>
                        </div>
                        <h3 class="font-display font-bold text-[24px] tracking-wide text-white">Firmy a Organizácie</h3>
                        <p class="text-bcz-muted text-[14px] leading-[1.6]">Teambuildingy a firemné eventy. Inšpiratívny obsah o disciplíne a vytrvalosti aplikovaný do pracovného prostredia.</p>
                    </div>

                    {{-- Card 4 --}}
                    <div class="bg-[#111111] border border-bcz-border rounded-2xl p-8 flex flex-col gap-4">
                        <div class="w-14 h-14 bg-[#3B82F6]/10 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-[#3B82F6]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>
                        </div>
                        <h3 class="font-display font-bold text-[24px] tracking-wide text-white">Športové Kluby</h3>
                        <p class="text-bcz-muted text-[14px] leading-[1.6]">Mentálna príprava pre športovcov. Ako pracovať s motiváciou, zvládať tlak a budovať víťaznú mentalitu.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Events Section --}}
    <section class="bg-[#111111] py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col gap-12">
                {{-- Header --}}
                <div class="flex flex-col items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-0.5 bg-[#3B82F6]"></div>
                        <span class="text-[#3B82F6] text-[12px] font-bold tracking-[2px]">PORTFÓLIO</span>
                        <div class="w-10 h-0.5 bg-[#3B82F6]"></div>
                    </div>
                    <h2 class="font-display font-bold text-[36px] md:text-[48px] tracking-wide text-white text-center">Kde Sme Prednášali</h2>
                </div>

                {{-- Events Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {{-- Event 1 --}}
                    <div class="bg-[#1A1A1A] border border-bcz-border rounded-2xl overflow-hidden">
                        <div class="w-full h-[200px] bg-[url('https://images.unsplash.com/photo-1758270704587-43339a801396?w=1080&q=80')] bg-cover bg-center"></div>
                        <div class="flex flex-col gap-3 p-5">
                            <h3 class="font-display font-bold text-[22px] tracking-wide text-white">ZŠ Čadca - Motivačná Prednáška</h3>
                            <p class="text-bcz-muted text-[13px] leading-[1.5]">Motivačná prednáška pre žiakov základnej školy o sile pohybu a správnom nastavení mysle.</p>
                        </div>
                    </div>

                    {{-- Event 2 --}}
                    <div class="bg-[#1A1A1A] border border-bcz-border rounded-2xl overflow-hidden">
                        <div class="w-full h-[200px] bg-[url('https://images.unsplash.com/photo-1651315283944-852219dff97b?w=1080&q=80')] bg-cover bg-center"></div>
                        <div class="flex flex-col gap-3 p-5">
                            <h3 class="font-display font-bold text-[22px] tracking-wide text-white">Gymnázium BB - Sila Pohybu</h3>
                            <p class="text-bcz-muted text-[13px] leading-[1.5]">Prednáška o benefitoch pohybu a cvičenia pre študentov gymnázia v Banskej Bystrici.</p>
                        </div>
                    </div>

                    {{-- Event 3 --}}
                    <div class="bg-[#1A1A1A] border border-bcz-border rounded-2xl overflow-hidden">
                        <div class="w-full h-[200px] bg-[url('https://images.unsplash.com/photo-1560439514-07abbb294a86?w=1080&q=80')] bg-cover bg-center"></div>
                        <div class="flex flex-col gap-3 p-5">
                            <h3 class="font-display font-bold text-[22px] tracking-wide text-white">Konferencia Mladých Lídrov</h3>
                            <p class="text-bcz-muted text-[13px] leading-[1.5]">Inšpiratívna prednáška na konferencii pre mladých lídrov o ceste od sna k realite.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="bg-bcz-dark py-24">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col items-center gap-8">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-[#3B82F6]"></div>
                    <span class="text-[#3B82F6] text-[12px] font-bold tracking-[2px]">OBJEDNAJTE SI PREDNÁŠKU</span>
                    <div class="w-10 h-0.5 bg-[#3B82F6]"></div>
                </div>
                <h2 class="font-display font-bold text-[36px] md:text-[56px] leading-[1.05] tracking-wide text-white text-center">
                    Prineste Inšpiráciu<br>Do Vašej Školy
                </h2>
                <p class="text-bcz-muted text-[18px] leading-[1.6] text-center max-w-[600px]">
                    Kontaktujte nás a spoločne naplánujeme prednášku prispôsobenú presne vašim potrebám. Každá prednáška je jedinečná.
                </p>
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-[#3B82F6]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                    <span class="text-white text-[18px] font-semibold">info@bfreak.sk</span>
                </div>
                <a href="{{ route('kontakt') }}" class="bg-[#3B82F6] px-10 py-4 rounded-lg text-white font-bold text-[14px] tracking-wide hover:bg-[#3B82F6]/90 transition-colors">
                    NAPÍŠTE NÁM
                </a>
            </div>
        </div>
    </section>
@endsection
