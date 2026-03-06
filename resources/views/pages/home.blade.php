@extends('layouts.public')

@section('title', 'BCZ Club - Beyond Comfort Zone')

@section('content')
    {{-- Hero Section --}}
    <section class="relative w-full h-[500px] md:h-[650px] lg:h-[800px] overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=1440&q=80')] bg-cover bg-center"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-bcz-dark/95 to-transparent"></div>

        <div class="relative w-full h-full flex flex-col justify-end pb-20 pt-44">
            <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 w-full">
            {{-- Badge --}}
            <div class="flex items-center gap-3 px-5 py-2.5 border border-bcz-red bg-bcz-red/10 w-fit mb-8">
                <span class="w-2 h-2 rounded-full bg-bcz-red"></span>
                <span class="text-bcz-red text-xs font-bold tracking-widest">BEYOND COMFORT ZONE</span>
            </div>

            {{-- Headline --}}
            <div class="mb-8">
                <h1 class="font-display font-bold text-[40px] md:text-[64px] lg:text-[96px] leading-[0.95] tracking-wide">PREKONAJ</h1>
                <h1 class="font-display font-bold text-[40px] md:text-[64px] lg:text-[96px] leading-[0.95] tracking-wide text-bcz-red">SVOJE LIMITY</h1>
            </div>

            {{-- Subtitle --}}
            <p class="text-bcz-lighter text-xl max-w-[600px] mb-8">
                Profesionálne tréningy kalisteniky a parkouru, súťaže a vystúpenia.
            </p>

            {{-- CTAs --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
                <a href="/admin" class="bg-bcz-red text-white text-sm font-bold tracking-widest px-9 py-4.5 flex items-center gap-3 hover:bg-red-700 transition-colors">
                    ZAČAŤ TRÉNOVAŤ
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
                <a href="#" class="border-2 border-white text-white text-sm font-bold tracking-widest px-9 py-4.5 flex items-center gap-3 hover:bg-white/10 transition-colors">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/></svg>
                    POZRIEŤ VIDEO
                </a>
            </div>
            </div>
        </div>
    </section>

    {{-- Three Pillars Section --}}
    <section id="pillars" class="bg-bcz-dark py-24">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col gap-16">
            {{-- Header --}}
            <div class="flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-widest">ČO ROBÍME</span>
                </div>
                <h2 class="font-display font-bold text-5xl tracking-wide">TRI PILIERE BCZ</h2>
                <p class="text-bcz-dim text-lg">Súťaže. Tréningy. Vystúpenia.</p>
            </div>

            {{-- Pillars Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Pillar 1: Sutaze --}}
                <div class="bg-bcz-card border border-bcz-border flex flex-col overflow-hidden">
                    <div class="w-full h-[280px] bg-[url('https://images.unsplash.com/photo-1526506118085-60ce8714f8c5?w=600&q=80')] bg-cover bg-center"></div>
                    <div class="p-8 flex flex-col gap-4">
                        <span class="font-display font-bold text-5xl text-bcz-red/20">01</span>
                        <h3 class="font-display font-bold text-[28px] tracking-wide">SÚŤAŽE</h3>
                        <p class="text-bcz-muted text-[15px] leading-relaxed">
                            Profesionálna účasť na medzinárodných a domácich súťažiach. Organizujeme a propagujeme podujatia, pričom naši aktívni členovia dosahujú výnimočné úspechy.
                        </p>
                        <a href="#" class="flex items-center gap-2 text-bcz-red text-xs font-bold tracking-widest hover:gap-3 transition-all">
                            ZOBRAZIŤ SÚŤAŽE
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>

                {{-- Pillar 2: Treningy --}}
                <div class="bg-bcz-card border border-bcz-border flex flex-col overflow-hidden">
                    <div class="w-full h-[280px] bg-[url('https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=600&q=80')] bg-cover bg-center"></div>
                    <div class="p-8 flex flex-col gap-4">
                        <span class="font-display font-bold text-5xl text-bcz-red/20">02</span>
                        <h3 class="font-display font-bold text-[28px] tracking-wide">TRÉNINGY</h3>
                        <p class="text-bcz-muted text-[15px] leading-relaxed">
                            Súkromné a skupinové tréningy pre všetky úrovne. Parkour &amp; Freerunning, Freestyle a Kalistenika pre dospelých aj deti s certifikovanými trénermi.
                        </p>
                        <a href="#" class="flex items-center gap-2 text-bcz-red text-xs font-bold tracking-widest hover:gap-3 transition-all">
                            PRESKÚMAŤ TRÉNINGY
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>

                {{-- Pillar 3: Vystupenia --}}
                <div class="bg-bcz-card border border-bcz-border flex flex-col overflow-hidden">
                    <div class="w-full h-[280px] bg-[url('https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=600&q=80')] bg-cover bg-center"></div>
                    <div class="p-8 flex flex-col gap-4">
                        <span class="font-display font-bold text-5xl text-bcz-red/20">03</span>
                        <h3 class="font-display font-bold text-[28px] tracking-wide">VYSTÚPENIA</h3>
                        <p class="text-bcz-muted text-[15px] leading-relaxed">
                            Spektakulárne vystúpenia pre školy, škôlky, firmy a verejné podujatia. Dynamické show s profesionálnym vybavením, ktoré inšpirujú a bavia každé publíkum.
                        </p>
                        <a href="#" class="flex items-center gap-2 text-bcz-red text-xs font-bold tracking-widest hover:gap-3 transition-all">
                            OBJEDNAŤ VYSTÚPENIE
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- About Section --}}
    <section id="about" class="bg-[#111111] py-24">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col lg:flex-row gap-10 lg:gap-20">
            {{-- Left --}}
            <div class="flex-1 flex flex-col gap-8">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-widest">NÁŠ PRÍBEH</span>
                </div>

                <h2 class="font-display font-bold text-[32px] md:text-[48px] lg:text-[64px] leading-none tracking-wide">ZRODENÍ<br>Z VÁŠNE</h2>

                <p class="text-bcz-muted text-lg leading-relaxed">
                    BCZ Club začal ako skupina priateľov, ktorí posúvali hranice a objavovali, čoho je ľudské telo skutočne schopné. Dnes sme profesionálna asociácia venovaná šíreniu pohybovej kultúry prostredníctvom súťaží, svetových tréningov a nezabudnuteľných vystúpení.
                </p>

                <a href="#" class="flex items-center gap-2 text-white text-sm font-bold tracking-widest hover:gap-3 transition-all">
                    PREČÍTAŤ CELÝ PRÍBEH
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>

            {{-- Right --}}
            <div class="w-full lg:w-[500px] flex flex-col gap-4 lg:shrink-0">
                <div class="w-full h-[320px] bg-[url('https://images.unsplash.com/photo-1765741836851-8071475d4911?w=600&q=80')] bg-cover bg-center"></div>
                <div class="flex gap-4">
                    <div class="flex-1 h-[200px] bg-[url('https://images.unsplash.com/photo-1758521959503-46334a6dc64a?w=400&q=80')] bg-cover bg-center"></div>
                    <div class="flex-1 h-[200px] bg-[url('https://images.unsplash.com/photo-1663419122687-5004be891ce1?w=400&q=80')] bg-cover bg-center"></div>
                </div>
            </div>
        </div>
    </section>

    {{-- CEO Section --}}
    <section class="bg-bcz-dark py-[100px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col lg:flex-row gap-10 lg:gap-20">
            {{-- Photo --}}
            <div class="w-full lg:w-[480px] lg:shrink-0">
                <img src="https://images.unsplash.com/photo-1583454110551-21f2fa2afe61?w=600&q=80" alt="Dominik Klimek" class="w-full h-[580px] object-cover">
            </div>

            {{-- Text --}}
            <div class="flex-1 flex flex-col justify-center gap-8">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-[3px] bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[3px]">ZAKLADATEĽ &amp; CEO</span>
                </div>

                <h2 class="font-display font-bold text-[32px] md:text-[48px] lg:text-[64px] leading-none tracking-wide">DOMINIK<br>KLIMEK</h2>

                <span class="text-bcz-red text-base font-medium tracking-wider">Majster sveta v street workoute &middot; Tréner &middot; Mentor</span>

                <p class="text-[#888888] text-base leading-[1.7]">
                    Dominik <a href="https://dodoworkout.com" target="_blank" class="text-bcz-red font-semibold hover:underline">DODOWORKOUT</a> Klimek je zakladateľ BCZ Club a jediný certifikovaný master tréner kalisteniky a street workoute na Slovensku. V roku 2022 sa stal majstrom sveta v street workoute v Rige a trikrát po sebe vyhral majstrovstvá Slovenska.
                </p>

                <p class="text-[#888888] text-base leading-[1.7]">
                    Dnes vedie komunitu mladých ľudí, organizuje workshopy po školách a inšpiruje novú generáciu k pohybu a zdravému životnému štýlu. Jeho víziou je ukázať, že disciplína a tvrdá práca dokážu zmeniť životy.
                </p>

                {{-- Stats --}}
                <div class="flex flex-wrap gap-8">
                    <div class="flex flex-col gap-1">
                        <span class="font-display font-bold text-[28px] md:text-[40px] lg:text-[56px] text-bcz-red tracking-wide">1x</span>
                        <span class="text-[#666666] text-sm font-medium">Majster sveta</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="font-display font-bold text-[28px] md:text-[40px] lg:text-[56px] text-bcz-red tracking-wide">3x</span>
                        <span class="text-[#666666] text-sm font-medium">Majster SR</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="font-display font-bold text-[28px] md:text-[40px] lg:text-[56px] text-bcz-red tracking-wide">L4</span>
                        <span class="text-[#666666] text-sm font-medium">Conditioning Coach</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="font-display font-bold text-[28px] md:text-[40px] lg:text-[56px] text-bcz-red tracking-wide">500+</span>
                        <span class="text-[#666666] text-sm font-medium">Mentorovaných detí</span>
                    </div>
                </div>

                <a href="#" class="flex items-center gap-2 text-bcz-red text-[13px] font-bold tracking-widest hover:gap-3 transition-all">
                    SPOZNAJ DOMINIKA
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>
    </section>

    {{-- Social / CTA Section --}}
    <section class="relative w-full h-[350px] md:h-[420px] lg:h-[500px] overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=1440&q=80')] bg-cover bg-center"></div>
        <div class="absolute inset-0 bg-bcz-dark/80"></div>

        <div class="relative w-full h-full flex flex-col items-center justify-center">
            <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 w-full flex flex-col items-center justify-center gap-10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-0.5 bg-bcz-red"></div>
                <span class="text-bcz-red text-xs font-bold tracking-widest">SLEDUJTE NAŠU CESTU</span>
                <div class="w-10 h-0.5 bg-bcz-red"></div>
            </div>

            <h2 class="font-display font-bold text-[28px] md:text-[40px] lg:text-[56px] tracking-wide text-center">PRIDAJ SA K POHYBU</h2>

            <p class="text-bcz-muted text-lg text-center">
                Sledujte nás na sociálnych sieťach pre tréningové tipy, novinky zo súťaží a obsah zo zákulisia.
            </p>

            <div class="flex items-center gap-6">
                <a href="#" class="w-10 h-10 md:w-[60px] md:h-[60px] bg-bcz-red flex items-center justify-center hover:bg-red-700 transition-colors">
                    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                </a>
                <a href="#" class="w-10 h-10 md:w-[60px] md:h-[60px] border-2 border-white flex items-center justify-center hover:bg-white/10 transition-colors">
                    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </a>
                <a href="#" class="w-10 h-10 md:w-[60px] md:h-[60px] border-2 border-white flex items-center justify-center hover:bg-white/10 transition-colors">
                    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                </a>
            </div>
            </div>
        </div>
    </section>

    @include('partials.floating-donate')
@endsection
