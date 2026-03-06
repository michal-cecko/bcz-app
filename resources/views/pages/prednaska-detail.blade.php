@extends('layouts.public')

@section('title', 'SOŠ Čadca - Prednáška - BCZ Club')

@section('content')
    {{-- Hero Section --}}
    <section class="relative h-[500px] overflow-hidden">
        {{-- Background Placeholder --}}
        <div class="absolute inset-0 bg-[#1A1A1A]"></div>

        {{-- Gradient Overlay --}}
        <div class="absolute inset-0 bg-gradient-to-b from-transparent to-[#0A0A0A]"></div>

        {{-- Content --}}
        <div class="absolute bottom-0 w-full pb-[60px]">
            <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col gap-4">
            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-sm text-[#888888]">
                <a href="/" class="hover:text-white transition-colors">Domov</a>
                <span>/</span>
                <a href="#" class="hover:text-white transition-colors">Vystúpenia</a>
                <span>/</span>
                <span class="text-white">Prednáška</span>
            </nav>

            {{-- Badge --}}
            <span class="bg-[#3B82F6] text-white text-xs font-bold px-3.5 py-1.5 rounded-md w-fit">PREDNÁŠKA</span>

            {{-- Title --}}
            <h1 class="font-display font-bold text-[26px] md:text-[38px] lg:text-[52px] tracking-wide text-white">Stredná odborná škola Čadca</h1>
            </div>
        </div>
    </section>

    {{-- Content Wrapper --}}
    <section class="py-[60px] bg-[#0A0A0A]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col lg:flex-row gap-10 lg:gap-16">
            {{-- LEFT - Main Content --}}
        <div class="flex-1 flex flex-col gap-12">
            {{-- Text Block --}}
            <div class="flex flex-col gap-4">
                <h2 class="font-display font-bold text-[32px] tracking-wide text-white">O prednáške</h2>
                <p class="text-[#CCCCCC] text-base leading-[1.8]">
                    Navštívili sme Strednú odbornú školu v Čadci, kde sme pre študentov pripravili inšpiratívnu prednášku o správnom nastavení mysle, prekonávaní prekážok a budovaní vytrvalosti. Cieľom bolo ukázať mladým ľuďom, že s disciplínou a tvrdou prácou môžu dosiahnuť čokoľvek.
                </p>
                <p class="text-[#CCCCCC] text-base leading-[1.8]">
                    Prednáška trvala 90 minút a zúčastnilo sa jej viac ako 150 študentov z rôznych ročníkov. Súčasťou bola aj praktická ukážka základných prvkov parkouru a street workoutu, ktorá študentov nadchla.
                </p>
            </div>

            {{-- Topics Block --}}
            <div class="flex flex-col gap-4">
                <h3 class="font-display font-bold text-[24px] tracking-wide text-white">Témy prednášky</h3>
                <ul class="flex flex-col gap-3">
                    <li class="flex items-start gap-3">
                        <div class="w-2 h-2 bg-[#3B82F6] rounded-sm mt-2 shrink-0"></div>
                        <span class="text-[#CCCCCC] text-base leading-[1.8]">Správne nastavenie mysle a pozitívne myslenie</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-2 h-2 bg-[#3B82F6] rounded-sm mt-2 shrink-0"></div>
                        <span class="text-[#CCCCCC] text-base leading-[1.8]">Hodnotové rebríčky - čo je v živote skutočne dôležité</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-2 h-2 bg-[#3B82F6] rounded-sm mt-2 shrink-0"></div>
                        <span class="text-[#CCCCCC] text-base leading-[1.8]">Trpezlivosť a vytrvalosť - kľúč k úspechu</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-2 h-2 bg-[#3B82F6] rounded-sm mt-2 shrink-0"></div>
                        <span class="text-[#CCCCCC] text-base leading-[1.8]">Výhody pravidelného cvičenia pre telo aj myseľ</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-2 h-2 bg-[#3B82F6] rounded-sm mt-2 shrink-0"></div>
                        <span class="text-[#CCCCCC] text-base leading-[1.8]">Prekonávanie strachu a komfortnej zóny</span>
                    </li>
                </ul>
            </div>

            {{-- Quote Block --}}
            <div class="rounded-xl bg-[#111111] p-8 border border-[#3B82F640] flex gap-5">
                <div class="w-1 bg-[#3B82F6] rounded-sm self-stretch shrink-0"></div>
                <div class="flex flex-col gap-3">
                    <p class="text-[#CCCCCC] text-base leading-[1.8] italic">
                        „Prednáška bola úžasná! Konečne niekto, kto hovorí k mladým ľuďom ich jazykom. Študenti boli nadšení a ešte týždne potom diskutovali o témach, ktoré ste otvorili."
                    </p>
                    <span class="text-[#888888] text-sm">— Mgr. Jana Kováčová, riaditeľka SOŠ Čadca</span>
                </div>
            </div>
        </div>

        {{-- RIGHT - Sidebar --}}
        <div class="w-full lg:w-[320px] flex flex-col gap-6 shrink-0">
            {{-- Info Card --}}
            <div class="rounded-xl bg-[#111111] p-6 border border-[#222222]">
                <h3 class="text-white font-bold text-[18px] mb-5">Informácie o prednáške</h3>
                <div class="flex flex-col gap-4">
                    <div class="flex justify-between items-center">
                        <span class="text-[#888888] text-sm">Typ</span>
                        <span class="text-[#3B82F6] text-sm font-semibold">Prednáška</span>
                    </div>
                    <div class="w-full h-px bg-[#222222]"></div>
                    <div class="flex justify-between items-center">
                        <span class="text-[#888888] text-sm">Klient</span>
                        <span class="text-white text-sm">SOŠ Čadca</span>
                    </div>
                    <div class="w-full h-px bg-[#222222]"></div>
                    <div class="flex justify-between items-center">
                        <span class="text-[#888888] text-sm">Miesto</span>
                        <span class="text-white text-sm">Čadca</span>
                    </div>
                    <div class="w-full h-px bg-[#222222]"></div>
                    <div class="flex justify-between items-center">
                        <span class="text-[#888888] text-sm">Dátum</span>
                        <span class="text-white text-sm">15. marca 2024</span>
                    </div>
                    <div class="w-full h-px bg-[#222222]"></div>
                    <div class="flex justify-between items-center">
                        <span class="text-[#888888] text-sm">Účastníci</span>
                        <span class="text-white text-sm">150+ študentov</span>
                    </div>
                </div>
            </div>

            {{-- CTA Card --}}
            <div class="rounded-xl bg-[#3B82F6] p-6 flex flex-col gap-4">
                <h3 class="text-white font-bold text-[18px]">Chcete prednášku na vašej škole?</h3>
                <p class="text-white/80 text-sm leading-[1.6]">Pripravíme prednášku na mieru pre vašich študentov.</p>
                <a href="#" class="bg-white text-[#3B82F6] rounded-lg py-3 text-center font-semibold text-sm hover:bg-white/90 transition-colors">
                    Kontaktujte nás
                </a>
            </div>

            {{-- Share Card --}}
            <div class="rounded-xl bg-[#111111] p-6 border border-[#222222]">
                <h3 class="text-white font-bold text-[18px] mb-4">Zdieľať</h3>
                <div class="flex gap-3">
                    {{-- Facebook --}}
                    <a href="#" class="w-10 h-10 bg-[#1A1A1A] rounded-lg flex items-center justify-center hover:bg-[#222222] transition-colors">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    {{-- Instagram --}}
                    <a href="#" class="w-10 h-10 bg-[#1A1A1A] rounded-lg flex items-center justify-center hover:bg-[#222222] transition-colors">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                    {{-- Copy Link --}}
                    <a href="#" class="w-10 h-10 bg-[#1A1A1A] rounded-lg flex items-center justify-center hover:bg-[#222222] transition-colors">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                    </a>
                </div>
            </div>
        </div>
        </div>
    </section>

    {{-- Gallery Section --}}
    <section class="bg-[#0A0A0A] py-[60px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
        {{-- Header --}}
        <div class="flex items-end justify-between mb-8">
            <h2 class="font-display font-bold text-[32px] tracking-wide text-white">Fotogaléria</h2>
            <span class="text-[#888888] text-sm">12 fotografií</span>
        </div>

        {{-- Row 1 --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
            <div class="h-[280px] rounded-xl bg-[#1A1A1A]"></div>
            <div class="h-[280px] rounded-xl bg-[#1A1A1A]"></div>
            <div class="h-[280px] rounded-xl bg-[#1A1A1A]"></div>
        </div>

        {{-- Row 2 --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div class="h-[200px] rounded-xl bg-[#1A1A1A]"></div>
            <div class="h-[200px] rounded-xl bg-[#1A1A1A]"></div>
            <div class="h-[200px] rounded-xl bg-[#1A1A1A]"></div>
            <div class="h-[200px] rounded-xl bg-[#1A1A1A]"></div>
        </div>
        </div>
    </section>

    {{-- More Lectures Section --}}
    <section class="bg-[#111111] py-[60px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
        {{-- Header --}}
        <div class="flex items-end justify-between mb-8">
            <h2 class="font-display font-bold text-[32px] tracking-wide text-white">Ďalšie prednášky</h2>
            <a href="#" class="text-[#3B82F6] text-sm font-semibold hover:text-[#3B82F6]/80 transition-colors">Zobraziť všetky →</a>
        </div>

        {{-- 3-column Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- Card 1 --}}
            <div class="rounded-xl bg-[#0A0A0A] border border-[#222222] overflow-hidden">
                <div class="h-[180px] bg-[#1A1A1A]"></div>
                <div class="p-5 flex flex-col gap-3">
                    <span class="bg-[#3B82F6]/20 text-[#3B82F6] text-[10px] font-bold px-2.5 py-1 rounded w-fit">PREDNÁŠKA</span>
                    <h3 class="text-white text-[18px] font-bold">Gymnázium Metodova</h3>
                    <p class="text-[#888888] text-[13px]">Motivačná prednáška pre študentov o nastavení mysle a prekonávaní prekážok.</p>
                    <span class="text-[#666666] text-[12px]">Október 2024 · Bratislava</span>
                </div>
            </div>

            {{-- Card 2 --}}
            <div class="rounded-xl bg-[#0A0A0A] border border-[#222222] overflow-hidden">
                <div class="h-[180px] bg-[#1A1A1A]"></div>
                <div class="p-5 flex flex-col gap-3">
                    <span class="bg-[#3B82F6]/20 text-[#3B82F6] text-[10px] font-bold px-2.5 py-1 rounded w-fit">PREDNÁŠKA</span>
                    <h3 class="text-white text-[18px] font-bold">SPŠ Žilina</h3>
                    <p class="text-[#888888] text-[13px]">Inšpiratívna prednáška o disciplíne, vytrvalosti a budovaní zdravých návykov.</p>
                    <span class="text-[#666666] text-[12px]">November 2024 · Žilina</span>
                </div>
            </div>

            {{-- Card 3 --}}
            <div class="rounded-xl bg-[#0A0A0A] border border-[#222222] overflow-hidden">
                <div class="h-[180px] bg-[#1A1A1A]"></div>
                <div class="p-5 flex flex-col gap-3">
                    <span class="bg-[#3B82F6]/20 text-[#3B82F6] text-[10px] font-bold px-2.5 py-1 rounded w-fit">PREDNÁŠKA</span>
                    <h3 class="text-white text-[18px] font-bold">Obchodná akadémia Banská Bystrica</h3>
                    <p class="text-[#888888] text-[13px]">Prednáška o správnom mindset-e a výhodách pravidelného pohybu pre študentov.</p>
                    <span class="text-[#666666] text-[12px]">December 2024 · Banská Bystrica</span>
                </div>
            </div>
        </div>
        </div>
    </section>
@endsection
