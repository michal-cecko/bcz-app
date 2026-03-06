@extends('layouts.public')

@section('title', 'Kurz Stojky - Fitness Factory Žilina - BCZ Club')

@section('content')
    {{-- Hero Section --}}
    <section class="relative w-full h-[500px] overflow-hidden bg-[#1A1A1A]">
        <div class="absolute inset-0 bg-gradient-to-b from-transparent to-[#0A0A0A]"></div>

        <div class="absolute bottom-0 left-0 right-0 pb-[60px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col gap-4">
            {{-- Breadcrumb --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="text-[#666666] text-[11px] font-medium tracking-[2px] hover:text-white transition-colors">DOMOV</a>
                <span class="text-[#444444] text-[11px]">/</span>
                <a href="#" class="text-[#666666] text-[11px] font-medium tracking-[2px] hover:text-white transition-colors">VYSTÚPENIA</a>
                <span class="text-[#444444] text-[11px]">/</span>
                <span class="text-[#22C55E] text-[11px] font-medium tracking-[2px]">WORKSHOP</span>
            </div>

            {{-- Badge --}}
            <span class="bg-[#22C55E] text-white text-xs font-bold px-3.5 py-1.5 rounded-md w-fit">WORKSHOP</span>

            {{-- Title --}}
            <h1 class="font-display font-bold text-[24px] md:text-[36px] lg:text-[48px] tracking-wide text-white">Kurz Stojky — Fitness Factory Žilina</h1>
        </div>
        </div>
    </section>

    {{-- Content Section --}}
    <section class="bg-bcz-dark py-[60px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
        <div class="flex flex-col lg:flex-row gap-10 lg:gap-16">
            {{-- Left: Main Content --}}
            <div class="flex-1 flex flex-col gap-12">
                {{-- Text Block --}}
                <div class="flex flex-col gap-6">
                    <h2 class="font-display font-bold text-[32px] tracking-wide text-white">O workshope</h2>
                    <p class="text-[#888888] text-[16px] leading-[1.7]">
                        Pripravili sme exkluzívny kurz stojky pre členov Fitness Factory Žilina. Workshop bol zameraný na správnu techniku, posilnenie potrebných svalových skupín a postupný progres od základov až po voľnú stojku.
                    </p>
                    <p class="text-[#888888] text-[16px] leading-[1.7]">
                        Účastníci sa naučili základné prípravné cvičenia, správne postavenie rúk a ramien, ako aj techniky na udržanie rovnováhy. Každý odchádzal s individuálnym plánom na ďalší tréning doma.
                    </p>
                </div>

                {{-- Program Block --}}
                <div class="flex flex-col gap-6">
                    <h3 class="font-display font-bold text-[24px] tracking-wide text-white">Program workshopu</h3>
                    <ul class="flex flex-col gap-3">
                        <li class="flex items-center gap-3">
                            <span class="w-2 h-2 rounded-full bg-[#22C55E] shrink-0"></span>
                            <span class="text-[#AAAAAA] text-[15px]">Úvod do stojky a jej benefity pre telo</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="w-2 h-2 rounded-full bg-[#22C55E] shrink-0"></span>
                            <span class="text-[#AAAAAA] text-[15px]">Rozcvička a mobilita zápästí a ramien</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="w-2 h-2 rounded-full bg-[#22C55E] shrink-0"></span>
                            <span class="text-[#AAAAAA] text-[15px]">Základné prípravné cvičenia a posilňovanie</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="w-2 h-2 rounded-full bg-[#22C55E] shrink-0"></span>
                            <span class="text-[#AAAAAA] text-[15px]">Stojka pri stene — správna technika</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="w-2 h-2 rounded-full bg-[#22C55E] shrink-0"></span>
                            <span class="text-[#AAAAAA] text-[15px]">Prechod k voľnej stojke — balans a korekcie</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="w-2 h-2 rounded-full bg-[#22C55E] shrink-0"></span>
                            <span class="text-[#AAAAAA] text-[15px]">Individuálny feedback a plán na tréning doma</span>
                        </li>
                    </ul>
                </div>

                {{-- Quote Block --}}
                <div class="rounded-xl bg-[#111111] p-8 border border-[#22C55E40] flex gap-5">
                    <div class="w-1 bg-[#22C55E] rounded-full shrink-0"></div>
                    <div class="flex flex-col gap-4">
                        <p class="text-[#CCCCCC] text-[16px] leading-[1.7] italic">
                            "Workshop prekonal moje očakávania. Konečne som pochopila, čo robím zle a ako správne zapojiť svaly. Po dvoch týždňoch tréningu podľa plánu som už stála 10 sekúnd!"
                        </p>
                        <span class="text-[#22C55E] text-sm font-semibold">— Martina K., účastníčka workshopu</span>
                    </div>
                </div>
            </div>

            {{-- Right: Sidebar --}}
            <div class="w-full lg:w-[320px] shrink-0 flex flex-col gap-6">
                {{-- Info Card --}}
                <div class="bg-[#111111] border border-[#222222] rounded-xl p-6 flex flex-col gap-5">
                    <span class="text-[#22C55E] text-xs font-bold tracking-[2px]">DETAILY</span>
                    <div class="flex flex-col gap-4">
                        <div class="flex justify-between">
                            <span class="text-[#666666] text-sm">Typ</span>
                            <span class="text-[#22C55E] text-sm font-semibold">Workshop</span>
                        </div>
                        <div class="w-full h-px bg-[#222222]"></div>
                        <div class="flex justify-between">
                            <span class="text-[#666666] text-sm">Klient</span>
                            <span class="text-white text-sm font-semibold">Fitness Factory</span>
                        </div>
                        <div class="w-full h-px bg-[#222222]"></div>
                        <div class="flex justify-between">
                            <span class="text-[#666666] text-sm">Miesto</span>
                            <span class="text-white text-sm font-semibold">Žilina</span>
                        </div>
                        <div class="w-full h-px bg-[#222222]"></div>
                        <div class="flex justify-between">
                            <span class="text-[#666666] text-sm">Dátum</span>
                            <span class="text-white text-sm font-semibold">8. februára 2024</span>
                        </div>
                        <div class="w-full h-px bg-[#222222]"></div>
                        <div class="flex justify-between">
                            <span class="text-[#666666] text-sm">Účastníci</span>
                            <span class="text-white text-sm font-semibold">12 osôb</span>
                        </div>
                        <div class="w-full h-px bg-[#222222]"></div>
                        <div class="flex justify-between">
                            <span class="text-[#666666] text-sm">Trvanie</span>
                            <span class="text-white text-sm font-semibold">3 hodiny</span>
                        </div>
                    </div>
                </div>

                {{-- CTA Card --}}
                <div class="rounded-xl bg-[#22C55E] p-6 flex flex-col gap-4">
                    <h4 class="text-white font-bold text-[18px] leading-[1.4]">Chcete workshop pre vašu firmu?</h4>
                    <p class="text-white/80 text-[14px] leading-[1.6]">
                        Pripravíme kurz stojky alebo iný workshop na mieru.
                    </p>
                    <a href="#" class="bg-white text-[#22C55E] rounded-lg px-6 py-3 text-sm font-bold text-center hover:bg-white/90 transition-colors">
                        Kontaktujte nás
                    </a>
                </div>

                {{-- Share Card --}}
                <div class="bg-[#111111] border border-[#222222] rounded-xl p-6 flex flex-col gap-4">
                    <span class="text-[#666666] text-xs font-bold tracking-[2px]">ZDIEĽAŤ</span>
                    <div class="flex gap-3">
                        <a href="#" class="w-10 h-10 bg-[#222222] rounded-lg flex items-center justify-center hover:bg-[#333333] transition-colors">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-[#222222] rounded-lg flex items-center justify-center hover:bg-[#333333] transition-colors">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-[#222222] rounded-lg flex items-center justify-center hover:bg-[#333333] transition-colors">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>

    {{-- Gallery Section --}}
    <section class="bg-bcz-dark py-[60px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
        <div class="flex flex-col gap-10">
            {{-- Header --}}
            <div class="flex items-end justify-between">
                <h2 class="font-display font-bold text-[32px] tracking-wide text-white">Fotogaléria</h2>
                <span class="text-[#666666] text-sm">8 fotografií</span>
            </div>

            {{-- Row 1: 3 images --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <div class="w-full h-[280px] rounded-xl bg-[#1A1A1A] border border-[#222222] flex items-center justify-center">
                    <svg class="w-10 h-10 text-[#333333]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                </div>
                <div class="w-full h-[280px] rounded-xl bg-[#1A1A1A] border border-[#222222] flex items-center justify-center">
                    <svg class="w-10 h-10 text-[#333333]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                </div>
                <div class="w-full h-[280px] rounded-xl bg-[#1A1A1A] border border-[#222222] flex items-center justify-center">
                    <svg class="w-10 h-10 text-[#333333]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                </div>
            </div>

            {{-- Row 2: 4 images --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                <div class="w-full h-[200px] rounded-xl bg-[#1A1A1A] border border-[#222222] flex items-center justify-center">
                    <svg class="w-10 h-10 text-[#333333]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                </div>
                <div class="w-full h-[200px] rounded-xl bg-[#1A1A1A] border border-[#222222] flex items-center justify-center">
                    <svg class="w-10 h-10 text-[#333333]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                </div>
                <div class="w-full h-[200px] rounded-xl bg-[#1A1A1A] border border-[#222222] flex items-center justify-center">
                    <svg class="w-10 h-10 text-[#333333]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                </div>
                <div class="w-full h-[200px] rounded-xl bg-[#1A1A1A] border border-[#222222] flex items-center justify-center">
                    <svg class="w-10 h-10 text-[#333333]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                </div>
            </div>
        </div>
        </div>
    </section>

    {{-- More Workshops Section --}}
    <section class="bg-[#111111] py-[60px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
        <div class="flex flex-col gap-10">
            {{-- Header --}}
            <div class="flex items-end justify-between">
                <h2 class="font-display font-bold text-[32px] tracking-wide text-white">Ďalšie workshopy</h2>
                <a href="#" class="text-[#22C55E] text-sm font-semibold hover:text-[#16A34A] transition-colors">Zobraziť všetky →</a>
            </div>

            {{-- 3-column Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Workshop Card 1 --}}
                <div class="bg-[#0A0A0A] rounded-2xl overflow-hidden border border-[#222222] hover:border-[#22C55E]/40 transition-colors">
                    <div class="w-full h-[200px] bg-[#1A1A1A] flex items-center justify-center">
                        <svg class="w-10 h-10 text-[#333333]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                    </div>
                    <div class="flex flex-col gap-3 p-5">
                        <span class="bg-[#22C55E]/20 text-[#22C55E] text-[10px] font-bold px-2.5 py-1 rounded w-fit">WORKSHOP</span>
                        <h3 class="text-white text-[18px] font-bold">Kurz bezpečného pádu</h3>
                        <p class="text-[#888888] text-[13px]">Základy techniky pádu pre začiatočníkov aj pokročilých.</p>
                        <span class="text-[#666666] text-[12px]">Január 2024 · Bratislava</span>
                    </div>
                </div>

                {{-- Workshop Card 2 --}}
                <div class="bg-[#0A0A0A] rounded-2xl overflow-hidden border border-[#222222] hover:border-[#22C55E]/40 transition-colors">
                    <div class="w-full h-[200px] bg-[#1A1A1A] flex items-center justify-center">
                        <svg class="w-10 h-10 text-[#333333]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                    </div>
                    <div class="flex flex-col gap-3 p-5">
                        <span class="bg-[#22C55E]/20 text-[#22C55E] text-[10px] font-bold px-2.5 py-1 rounded w-fit">WORKSHOP</span>
                        <h3 class="text-white text-[18px] font-bold">Akrobacia pre trénerov</h3>
                        <p class="text-[#888888] text-[13px]">Metodika výuky akrobatických prvkov pre fitness trénerov.</p>
                        <span class="text-[#666666] text-[12px]">Marec 2024 · Košice</span>
                    </div>
                </div>

                {{-- Workshop Card 3 --}}
                <div class="bg-[#0A0A0A] rounded-2xl overflow-hidden border border-[#222222] hover:border-[#22C55E]/40 transition-colors">
                    <div class="w-full h-[200px] bg-[#1A1A1A] flex items-center justify-center">
                        <svg class="w-10 h-10 text-[#333333]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                    </div>
                    <div class="flex flex-col gap-3 p-5">
                        <span class="bg-[#22C55E]/20 text-[#22C55E] text-[10px] font-bold px-2.5 py-1 rounded w-fit">WORKSHOP</span>
                        <h3 class="text-white text-[18px] font-bold">Mobility & Flexibility</h3>
                        <p class="text-[#888888] text-[13px]">Workshop zameraný na mobilitu a flexibilitu pre lepší pohyb.</p>
                        <span class="text-[#666666] text-[12px]">Máj 2024 · Žilina</span>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
@endsection
