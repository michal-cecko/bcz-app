@extends('layouts.public')

@section('title', 'Dominik Klimek | BCZ Club')

@section('content')
    {{-- Hero Section --}}
    <section class="relative w-full h-[450px] md:h-[580px] lg:h-[700px] overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1583454110551-21f2fa2afe61?w=1440&q=80')] bg-cover bg-center"></div>
        <div class="absolute inset-0" style="background: linear-gradient(180deg, #0A0A0A 0%, #0A0A0A44 30%, #0A0A0A88 60%, #0A0A0A 100%)"></div>

        <div class="relative w-full h-full flex flex-col justify-end pb-20">
            <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 w-full flex flex-col gap-6">
                {{-- Breadcrumbs --}}
                <div class="flex items-center gap-2 text-xs">
                    <a href="{{ route('home') }}" class="text-[#888888] hover:text-white transition-colors">Domov</a>
                    <span class="text-[#444444]">/</span>
                    <a href="{{ route('about') }}" class="text-[#888888] hover:text-white transition-colors">O nás</a>
                    <span class="text-[#444444]">/</span>
                    <span class="text-bcz-red">Dominik Klimek</span>
                </div>

                {{-- Badge --}}
                <div class="bg-bcz-red rounded px-4 py-1.5 w-fit">
                    <span class="text-white text-[11px] font-bold tracking-[2px]">ZAKLADATEĽ &amp; CEO</span>
                </div>

                {{-- Title --}}
                <h1 class="font-display font-bold text-[40px] md:text-[64px] lg:text-[96px] leading-[0.95] tracking-wide">DOMINIK KLIMEK</h1>

                {{-- Subtitle --}}
                <span class="text-bcz-red text-lg font-medium tracking-wider">Majster sveta v street workoute &middot; Master tréner &middot; Zakladateľ BCZ Club</span>
            </div>
        </div>
    </section>

    {{-- Stats Bar --}}
    <section class="w-full bg-[#111111] border-y border-[#1A1A1A]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-wrap items-center justify-center lg:justify-between gap-6 py-8">
            <div class="flex flex-col items-center gap-1">
                <span class="font-display font-bold text-4xl text-bcz-red tracking-wide">1x</span>
                <span class="text-[#666666] text-xs font-medium">Majster sveta</span>
            </div>
            <div class="flex flex-col items-center gap-1">
                <span class="font-display font-bold text-4xl text-bcz-red tracking-wide">3x</span>
                <span class="text-[#666666] text-xs font-medium">Majster SR</span>
            </div>
            <div class="flex flex-col items-center gap-1">
                <span class="font-display font-bold text-4xl text-bcz-red tracking-wide">L4</span>
                <span class="text-[#666666] text-xs font-medium">S&amp;C Coach</span>
            </div>
            <div class="flex flex-col items-center gap-1">
                <span class="font-display font-bold text-4xl text-bcz-red tracking-wide">500+</span>
                <span class="text-[#666666] text-xs font-medium">Mentorovaných detí</span>
            </div>
            <div class="flex flex-col items-center gap-1">
                <span class="font-display font-bold text-4xl text-bcz-red tracking-wide">30+</span>
                <span class="text-[#666666] text-xs font-medium">Krajiny</span>
            </div>
        </div>
    </section>

    {{-- Bio Section --}}
    <section class="bg-bcz-dark py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col lg:flex-row gap-10 lg:gap-20">
            {{-- Left --}}
            <div class="flex-1 flex flex-col gap-8">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-[3px] bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[3px]">MÔJ PRÍBEH</span>
                </div>

                <h2 class="font-display font-bold text-[28px] md:text-[40px] lg:text-[56px] leading-none tracking-wide">OD DETÍNSKYCH<br>SNOV K TITULU<br>MAJSTRA SVETA</h2>

                <p class="text-[#AAAAAA] text-base leading-[1.7]">
                    Od detstva som hľadal svoju cestu cez futbal, volejbal, parkour, klavír aj šachovú ligu. Nič ma však nenaplnilo tak ako street workout, ktorý som objavil, keď som uvidel chlapa robiť variace na hrázdach na ihrisku. Behom niekoľkých mesiacov som zvládol zadnú páku a vedel som &mdash; toto je to.
                </p>

                <p class="text-[#AAAAAA] text-base leading-[1.7]">
                    V roku 2019 som súťažil prvýkrát na majstrovstvách v Žiline a nepostúpil som ani z kvalifikácie. O tri roky neskôr som však stál na najvyššom stupíenku na Majstrovstvách sveta v Rige ako majster sveta v strednej váhovej kategórii.
                </p>
            </div>

            {{-- Right --}}
            <div class="w-full lg:w-[500px] shrink-0">
                <img src="https://images.unsplash.com/photo-1583454110551-21f2fa2afe61?w=600&q=80" alt="Dominik Klimek" class="w-full lg:w-[500px] h-[500px] object-cover">
            </div>
        </div>
    </section>

    {{-- Achievements Section --}}
    <section class="bg-[#111111] py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col gap-12">
            {{-- Header --}}
            <div class="flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-[3px] bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[3px]">VÝSLEDKY</span>
                </div>
                <h2 class="font-display font-bold text-[24px] md:text-[36px] lg:text-[48px] tracking-wide">ÚSPECHY NA SÚŤAŽIACH</h2>
                <p class="text-[#888888] text-base leading-[1.6] max-w-[700px]">
                    Od prvého neúspechu na kvalifikácii až po titul majstra sveta &mdash; každá súťaž bola krokom vpred.
                </p>
            </div>

            {{-- Grid Row 1 --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                {{-- Ach 1 --}}
                <div class="bg-bcz-dark rounded-2xl border border-[#222222] p-6 flex flex-col gap-4">
                    <span class="text-bcz-red text-xs font-semibold">2022</span>
                    <h3 class="font-display font-bold text-[32px] tracking-wide">Majster sveta</h3>
                    <p class="text-[#888888] text-[13px] leading-[1.6]">WSWCF World Championship<br>Riga, Lotyšsko<br>Stredná váha (68-80 kg)</p>
                    <div class="bg-[#FFD70020] rounded px-3 py-1 w-fit">
                        <span class="text-[#FFD700] text-[11px] font-bold tracking-wider">1. MIESTO</span>
                    </div>
                </div>

                {{-- Ach 2 --}}
                <div class="bg-bcz-dark rounded-2xl border border-[#222222] p-6 flex flex-col gap-4">
                    <span class="text-bcz-red text-xs font-semibold">2020&ndash;2022</span>
                    <h3 class="font-display font-bold text-[32px] tracking-wide">3x Majster SR</h3>
                    <p class="text-[#888888] text-[13px] leading-[1.6]">Majstrovstvá Slovenska<br>v street workoute<br>Tri po sebe idúce tituly</p>
                    <div class="bg-[#FFD70020] rounded px-3 py-1 w-fit">
                        <span class="text-[#FFD700] text-[11px] font-bold tracking-wider">ZLATO</span>
                    </div>
                </div>

                {{-- Ach 3 --}}
                <div class="bg-bcz-dark rounded-2xl border border-[#222222] p-6 flex flex-col gap-4">
                    <span class="text-bcz-red text-xs font-semibold">2021</span>
                    <h3 class="font-display font-bold text-[32px] tracking-wide">MS Moskva</h3>
                    <p class="text-[#888888] text-[13px] leading-[1.6]">Majstrovstvá sveta<br>Moskva, Rusko<br>Kvalifikácia 8. / Finále 7.</p>
                    <div class="bg-[#C0C0C020] rounded px-3 py-1 w-fit">
                        <span class="text-[#C0C0C0] text-[11px] font-bold tracking-wider">TOP 10</span>
                    </div>
                </div>
            </div>

            {{-- Grid Row 2 --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                {{-- Ach 4 --}}
                <div class="bg-bcz-dark rounded-2xl border border-[#222222] p-6 flex flex-col gap-4">
                    <span class="text-bcz-red text-xs font-semibold">2019</span>
                    <h3 class="font-display font-bold text-[32px] tracking-wide">Vicemajster SR</h3>
                    <p class="text-[#888888] text-[13px] leading-[1.6]">Majstrovstvá Slovenska<br>Trenčín<br>2. miesto</p>
                    <div class="bg-[#C0C0C020] rounded px-3 py-1 w-fit">
                        <span class="text-[#C0C0C0] text-[11px] font-bold tracking-wider">STRIEBRO</span>
                    </div>
                </div>

                {{-- Ach 5 --}}
                <div class="bg-bcz-dark rounded-2xl border border-[#222222] p-6 flex flex-col gap-4">
                    <span class="text-bcz-red text-xs font-semibold">2019</span>
                    <h3 class="font-display font-bold text-[32px] tracking-wide">SW Games Brno</h3>
                    <p class="text-[#888888] text-[13px] leading-[1.6]">Street Workout Games<br>Brno, Česko<br>Víťaz</p>
                    <div class="bg-[#FFD70020] rounded px-3 py-1 w-fit">
                        <span class="text-[#FFD700] text-[11px] font-bold tracking-wider">1. MIESTO</span>
                    </div>
                </div>

                {{-- Ach 6 --}}
                <div class="bg-bcz-dark rounded-2xl border border-[#222222] p-6 flex flex-col gap-4">
                    <span class="text-bcz-red text-xs font-semibold">2022</span>
                    <h3 class="font-display font-bold text-[32px] tracking-wide">Svetový pohár</h3>
                    <p class="text-[#888888] text-[13px] leading-[1.6]">WSWCF World Cup<br>Jurmala, Lotyšsko<br>Striebro</p>
                    <div class="bg-[#C0C0C020] rounded px-3 py-1 w-fit">
                        <span class="text-[#C0C0C0] text-[11px] font-bold tracking-wider">STRIEBRO</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Timeline Section --}}
    <section class="bg-bcz-dark py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col gap-12">
            {{-- Header --}}
            <div class="flex items-end justify-between">
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-[3px] bg-bcz-red"></div>
                        <span class="text-bcz-red text-xs font-bold tracking-[3px]">CESTA</span>
                    </div>
                    <h2 class="font-display font-bold text-[24px] md:text-[36px] lg:text-[48px] tracking-wide">MOJA CESTA</h2>
                </div>
            </div>

            {{-- Items --}}
            <div class="flex flex-col">
                {{-- 2017 --}}
                <div class="flex gap-10 py-6 border-t border-[#1A1A1A]">
                    <span class="font-display font-bold text-4xl text-bcz-red tracking-wide w-[100px] shrink-0">2017</span>
                    <div class="flex flex-col gap-2">
                        <h3 class="text-white text-lg font-bold">Objav street workoute</h3>
                        <p class="text-[#888888] text-sm leading-[1.6]">Prvý kontakt s kalistenikou na ihrisku. Začiatok samoukého tréningu a nekončiace sa hodiny na hrázdach.</p>
                    </div>
                </div>

                {{-- 2019 --}}
                <div class="flex gap-10 py-6 border-t border-[#1A1A1A]">
                    <span class="font-display font-bold text-4xl text-bcz-red tracking-wide w-[100px] shrink-0">2019</span>
                    <div class="flex flex-col gap-2">
                        <h3 class="text-white text-lg font-bold">Prvé súťaže a vicemajster SR</h3>
                        <p class="text-[#888888] text-sm leading-[1.6]">Prvá účasť na majstrovstvách SR v Žiline, nepostúpil z kvalifikácie. O niekoľko mesiacov neskôr už 2. miesto na SR v Trenčíne. Víťazstvo na SW Games Brno.</p>
                    </div>
                </div>

                {{-- 2020 --}}
                <div class="flex gap-10 py-6 border-t border-[#1A1A1A]">
                    <span class="font-display font-bold text-4xl text-bcz-red tracking-wide w-[100px] shrink-0">2020</span>
                    <div class="flex flex-col gap-2">
                        <h3 class="text-white text-lg font-bold">Založenie Street Workout Kysuce</h3>
                        <p class="text-[#888888] text-sm leading-[1.6]">Prvý titul majstra Slovenska. Založenie občianskeho združenia Street Workout Kysuce, dnešného BCZ Club.</p>
                    </div>
                </div>

                {{-- 2022 --}}
                <div class="flex gap-10 py-6 border-t border-[#1A1A1A]">
                    <span class="font-display font-bold text-4xl text-bcz-red tracking-wide w-[100px] shrink-0">2022</span>
                    <div class="flex flex-col gap-2">
                        <h3 class="text-white text-lg font-bold">Majster sveta v Rige</h3>
                        <p class="text-[#888888] text-sm leading-[1.6]">Titul majstra sveta v strednej váhovej kategórii na MS v Rige. Na tej istej súťaži získali medaily aj bratia Matej (zlato ľahká váha) a Daniel (striebro).</p>
                    </div>
                </div>

                {{-- 2024 --}}
                <div class="flex gap-10 py-6 border-t border-[#1A1A1A]">
                    <span class="font-display font-bold text-4xl text-bcz-red tracking-wide w-[100px] shrink-0">2024</span>
                    <div class="flex flex-col gap-2">
                        <h3 class="text-white text-lg font-bold">Medzinárodný tréner a porotca</h3>
                        <p class="text-[#888888] text-sm leading-[1.6]">Prestávka od súťaženía. Zameranie na koučšing, medzinárodné workshopy (Hong Kong, Uzbekistan, Švajčiarsko) a porotcovanie na MS v Hong Kongu.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Mentorship Section --}}
    <section class="bg-[#111111] py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col lg:flex-row gap-10 lg:gap-20 items-center">
            {{-- Image --}}
            <div class="w-full lg:w-[550px] shrink-0">
                <img src="https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=600&q=80" alt="Mentoring" class="w-full lg:w-[550px] h-[420px] object-cover">
            </div>

            {{-- Text --}}
            <div class="flex-1 flex flex-col justify-center gap-6">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-[3px] bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[3px]">MENTOR &amp; TRÉNER</span>
                </div>

                <h2 class="font-display font-bold text-[24px] md:text-[36px] lg:text-[48px] leading-none tracking-wide">INŠPIRÁCIA<br>PRE MLADÚ<br>GENERÁCIU</h2>

                <p class="text-[#AAAAAA] text-base leading-[1.7]">
                    Viac než súťažením sa Dominik venuje práci s mládežou. Spolu s kolegami chodí po školách, kde učí deti o stanovení cieľov, vytrvalosti a dôležitosti pohybu. Jeho cieľom je ukázať mladým ľuďom, že disciplína a tvrdá práca dokážu zmeniť životy.
                </p>

                <p class="text-[#AAAAAA] text-base leading-[1.7]">
                    Ako jediný certifikovaný master tréner kalisteniky na Slovensku viedľe medzinárodné workshopy po celom svete &mdash; od Hong Kongu cez Uzbekistan až po Švajčiarsko.
                </p>
            </div>
        </div>
    </section>

    {{-- Quote Section --}}
    <section class="bg-bcz-dark py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-20 lg:px-40 flex flex-col items-center gap-6">
            <span class="font-display font-bold text-[80px] text-bcz-red leading-[0.5]">&ldquo;</span>
            <p class="text-[#CCCCCC] text-2xl font-light italic text-center leading-[1.6] max-w-[900px]">
                Chcem pomáhať rozvíjať street workout na Slovensku aj vo svete a zároveň inšpirovať ostatných, aby nasledovali svoje sny.
            </p>
            <span class="text-[#888888] text-base font-semibold">&mdash; Dominik Klimek</span>
        </div>
    </section>

    {{-- Contact & Socials Section --}}
    <section class="bg-[#111111] py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col items-center gap-12">
            {{-- Header --}}
            <div class="flex items-center gap-3">
                <div class="w-8 h-[3px] bg-bcz-red"></div>
                <span class="text-bcz-red text-sm font-semibold tracking-[3px]">KONTAKT &amp; SOCIÁLNE SIETE</span>
            </div>

            <h2 class="font-display font-bold text-[32px] md:text-[48px] lg:text-[64px] tracking-wide text-center">SPOJ SA S DOMINIKOM</h2>

            <p class="text-[#888888] text-lg leading-[1.6] text-center max-w-[700px]">
                Sleduj Dominika na sociálnych sieťach, navštív jeho osobnú stránku alebo ho kontaktuj priamo.
            </p>

            {{-- Social Cards --}}
            <div class="flex flex-wrap gap-4 lg:gap-6">
                {{-- Website --}}
                <a href="https://dodoworkout.com" target="_blank" class="flex flex-col items-center gap-3.5 bg-[#151515] rounded-2xl border border-bcz-red/25 px-10 py-7 w-full sm:w-[200px] hover:border-bcz-red/50 transition-colors">
                    <svg class="w-9 h-9 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    <span class="text-white text-base font-bold">Osobná stránka</span>
                    <span class="text-bcz-red text-sm font-bold">dodoworkout.com</span>
                </a>

                {{-- Instagram --}}
                <a href="https://instagram.com/dodoworkout" target="_blank" class="flex flex-col items-center gap-3.5 bg-[#151515] rounded-2xl border border-[#333333] px-10 py-7 w-full sm:w-[200px] hover:border-[#555555] transition-colors">
                    <svg class="w-9 h-9 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
                    <span class="text-white text-base font-bold">Instagram</span>
                    <span class="text-bcz-red text-sm font-semibold">@dodoworkout</span>
                </a>

                {{-- YouTube --}}
                <a href="https://youtube.com/@dodoworkout" target="_blank" class="flex flex-col items-center gap-3.5 bg-[#151515] rounded-2xl border border-[#333333] px-10 py-7 w-full sm:w-[200px] hover:border-[#555555] transition-colors">
                    <svg class="w-9 h-9 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.5 17a24.12 24.12 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49.56 49.56 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24.12 24.12 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49.55 49.55 0 0 1-16.2 0A2 2 0 0 1 2.5 17"/><path d="m10 15 5-3-5-3z"/></svg>
                    <span class="text-white text-base font-bold">YouTube</span>
                    <span class="text-bcz-red text-sm font-semibold">@dodoworkout</span>
                </a>

                {{-- TikTok --}}
                <a href="https://tiktok.com/@dodoworkout_sk" target="_blank" class="flex flex-col items-center gap-3.5 bg-[#151515] rounded-2xl border border-[#333333] px-10 py-7 w-full sm:w-[200px] hover:border-[#555555] transition-colors">
                    <svg class="w-9 h-9 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"/></svg>
                    <span class="text-white text-base font-bold">TikTok</span>
                    <span class="text-bcz-red text-sm font-semibold">@dodoworkout_sk</span>
                </a>

                {{-- Facebook --}}
                <a href="https://facebook.com/dominikklimek" target="_blank" class="flex flex-col items-center gap-3.5 bg-[#151515] rounded-2xl border border-[#333333] px-10 py-7 w-full sm:w-[200px] hover:border-[#555555] transition-colors">
                    <svg class="w-9 h-9 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                    <span class="text-white text-base font-bold">Facebook</span>
                    <span class="text-bcz-red text-sm font-semibold">Dominik Klimek</span>
                </a>
            </div>

            {{-- Divider --}}
            <div class="w-[120px] h-px bg-[#333333]"></div>

            {{-- Contact --}}
            <div class="flex flex-col md:flex-row items-center gap-8 md:gap-16">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                    <span class="text-[#AAAAAA] text-base font-medium">info@dodoworkout.com</span>
                </div>
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    <span class="text-[#AAAAAA] text-base font-medium">+421 950 451 310</span>
                </div>
            </div>
        </div>
    </section>
@endsection
