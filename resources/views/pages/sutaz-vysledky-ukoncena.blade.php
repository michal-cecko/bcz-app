@extends('layouts.public')

@section('title', 'Výsledky súťaže | BCZ Club')

@section('content')
    {{-- Hero Section --}}
    <section class="relative w-full h-[400px] md:h-[450px] lg:h-[500px] overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=1440&q=80')] bg-cover bg-center"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-bcz-dark/60 via-bcz-dark/40 to-bcz-dark"></div>

        <div class="absolute bottom-0 left-0 right-0 pb-[60px]">
            <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col gap-4">
                {{-- Breadcrumb --}}
                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" class="text-bcz-muted text-[11px] font-medium tracking-widest hover:text-white transition-colors">DOMOV</a>
                    <span class="text-[#444444] text-[11px]">/</span>
                    <span class="text-[#888888] text-[11px] font-medium tracking-widest">SÚŤAŽE</span>
                    <span class="text-[#444444] text-[11px]">/</span>
                    <span class="text-bcz-red text-[11px] font-medium tracking-widest">VÝSLEDKY</span>
                </div>

                {{-- Badge --}}
                <span class="bg-[#333333] text-[#AAAAAA] text-xs font-bold px-3.5 py-1.5 rounded-md w-fit">UKONČENÁ</span>

                {{-- Title --}}
                <h1 class="font-display font-bold text-[28px] md:text-[40px] lg:text-[56px] tracking-wide text-white">BCZ Street Workout Cup 2025</h1>

                {{-- Date & Location --}}
                <div class="flex flex-wrap items-center gap-6 text-[#AAAAAA] text-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>15. marca 2025</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Bratislava, Slovensko</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>64 súťažiacich</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Tab Bar --}}
    <section class="bg-[#111111] border-b border-[#222222]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex items-center gap-0 overflow-x-auto">
                <a href="{{ route('sutaz.popis') }}" class="px-6 py-4 text-[#888888] text-sm font-bold tracking-widest hover:text-white transition-colors whitespace-nowrap border-b-2 border-transparent">POPIS</a>
                <a href="{{ route('sutaz.harmonogram') }}" class="px-6 py-4 text-[#888888] text-sm font-bold tracking-widest hover:text-white transition-colors whitespace-nowrap border-b-2 border-transparent">HARMONOGRAM</a>
                <a href="{{ route('sutaz.vysledky-ukoncena') }}" class="px-6 py-4 text-white text-sm font-bold tracking-widest whitespace-nowrap border-b-2 border-bcz-red">VÝSLEDKY</a>
                <a href="{{ route('sutaz.registracia-coskoro') }}" class="px-6 py-4 text-[#888888] text-sm font-bold tracking-widest hover:text-white transition-colors whitespace-nowrap border-b-2 border-transparent">REGISTRÁCIA</a>
            </div>
        </div>
    </section>

    {{-- Info Strip --}}
    <section class="bg-[#0D0D0D] py-6 border-b border-[#1A1A1A]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                {{-- Card 1 --}}
                <div class="bg-[#111111] rounded-xl p-5 flex flex-col gap-2">
                    <span class="text-[#666666] text-[11px] font-bold tracking-widest">FORMÁT</span>
                    <span class="text-white text-sm font-semibold">Kvalifikácia + Battle</span>
                </div>
                {{-- Card 2 --}}
                <div class="bg-[#111111] rounded-xl p-5 flex flex-col gap-2">
                    <span class="text-[#666666] text-[11px] font-bold tracking-widest">KATEGÓRIE</span>
                    <span class="text-white text-sm font-semibold">4 váhové kategórie</span>
                </div>
                {{-- Card 3 --}}
                <div class="bg-[#111111] rounded-xl p-5 flex flex-col gap-2">
                    <span class="text-[#666666] text-[11px] font-bold tracking-widest">PRIZE POOL</span>
                    <span class="text-white text-sm font-semibold">2 000 EUR</span>
                </div>
                {{-- Card 4 --}}
                <div class="bg-[#111111] rounded-xl p-5 flex flex-col gap-2">
                    <span class="text-[#666666] text-[11px] font-bold tracking-widest">ROZHODCOVIA</span>
                    <span class="text-white text-sm font-semibold">5 certifikovaných</span>
                </div>
                {{-- Card 5 --}}
                <div class="bg-[#111111] rounded-xl p-5 flex flex-col gap-2">
                    <span class="text-[#666666] text-[11px] font-bold tracking-widest">ORGANIZÁTOR</span>
                    <span class="text-white text-sm font-semibold">BCZ Club</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Results Sub Nav --}}
    <section class="bg-bcz-dark border-b border-[#1A1A1A]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex items-center gap-0 overflow-x-auto py-1">
                <a href="#celkove-vysledky" class="px-5 py-3 text-white text-xs font-bold tracking-widest whitespace-nowrap border-b-2 border-bcz-red">CELKOVÉ VÝSLEDKY</a>
                <a href="#kvalifikacia" class="px-5 py-3 text-[#888888] text-xs font-bold tracking-widest hover:text-white transition-colors whitespace-nowrap border-b-2 border-transparent">KVALIFIKÁCIA</a>
                <a href="#battle-bracket" class="px-5 py-3 text-[#888888] text-xs font-bold tracking-widest hover:text-white transition-colors whitespace-nowrap border-b-2 border-transparent">BATTLE BRACKET</a>
            </div>
        </div>
    </section>

    {{-- Top 3 Results / Podium --}}
    <section id="celkove-vysledky" class="bg-bcz-dark py-16">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            {{-- Section Header --}}
            <div class="flex flex-col gap-4 mb-10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[3px]">VÍŤAZI</span>
                </div>
                <h2 class="font-display font-bold text-[28px] md:text-[40px] tracking-wide">CELKOVÉ VÝSLEDKY</h2>
            </div>

            {{-- Category Tabs --}}
            <div class="flex flex-wrap items-center gap-3 mb-12" x-data="{ activeCategory: 'muzi-do-80' }">
                <button @click="activeCategory = 'muzi-do-80'" :class="activeCategory === 'muzi-do-80' ? 'bg-bcz-red text-white' : 'bg-[#1A1A1A] text-[#888888] hover:text-white'" class="px-5 py-2.5 rounded-lg text-xs font-bold tracking-widest transition-colors">MUŽI DO 80KG</button>
                <button @click="activeCategory = 'muzi-nad-80'" :class="activeCategory === 'muzi-nad-80' ? 'bg-bcz-red text-white' : 'bg-[#1A1A1A] text-[#888888] hover:text-white'" class="px-5 py-2.5 rounded-lg text-xs font-bold tracking-widest transition-colors">MUŽI NAD 80KG</button>
                <button @click="activeCategory = 'zeny-do-60'" :class="activeCategory === 'zeny-do-60' ? 'bg-bcz-red text-white' : 'bg-[#1A1A1A] text-[#888888] hover:text-white'" class="px-5 py-2.5 rounded-lg text-xs font-bold tracking-widest transition-colors">ŽENY DO 60KG</button>
                <button @click="activeCategory = 'zeny-nad-60'" :class="activeCategory === 'zeny-nad-60' ? 'bg-bcz-red text-white' : 'bg-[#1A1A1A] text-[#888888] hover:text-white'" class="px-5 py-2.5 rounded-lg text-xs font-bold tracking-widest transition-colors">ŽENY NAD 60KG</button>
            </div>

            {{-- Men's Category Header --}}
            <div class="flex items-center gap-4 mb-8">
                <div class="w-1 h-8 bg-bcz-red rounded-full"></div>
                <h3 class="font-display font-bold text-[24px] md:text-[32px] tracking-wide">MUŽI DO 80KG</h3>
            </div>

            {{-- Podium - Muži do 80kg --}}
            <div class="flex flex-col md:flex-row items-end justify-center gap-6 mb-16">
                {{-- 2nd Place - Silver --}}
                <div class="w-full md:w-1/3 bg-[#111111] rounded-2xl border-2 border-[#C0C0C0] overflow-hidden order-2 md:order-1">
                    <div class="relative h-[200px] bg-[#1A1A1A] flex items-center justify-center">
                        <div class="w-24 h-24 rounded-full bg-[#222222] flex items-center justify-center">
                            <svg class="w-12 h-12 text-[#444444]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div class="absolute top-4 left-4 w-10 h-10 rounded-full bg-[#C0C0C0] flex items-center justify-center">
                            <span class="text-black font-bold text-lg">2</span>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col items-center gap-2 text-center">
                        <span class="text-[#C0C0C0] text-2xl">&#127944;</span>
                        <h4 class="text-white text-lg font-bold">Peter Novák</h4>
                        <span class="text-[#888888] text-sm">BCZ Bratislava</span>
                        <div class="mt-2 bg-[#C0C0C0]/10 px-4 py-2 rounded-lg">
                            <span class="text-[#C0C0C0] text-xl font-bold">87.5</span>
                            <span class="text-[#666666] text-xs ml-1">bodov</span>
                        </div>
                    </div>
                </div>

                {{-- 1st Place - Gold --}}
                <div class="w-full md:w-1/3 bg-[#111111] rounded-2xl border-2 border-[#FFD700] overflow-hidden md:-mt-6 order-1 md:order-2">
                    <div class="relative h-[220px] bg-[#1A1A1A] flex items-center justify-center">
                        <div class="w-28 h-28 rounded-full bg-[#222222] flex items-center justify-center ring-4 ring-[#FFD700]/30">
                            <svg class="w-14 h-14 text-[#444444]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div class="absolute top-4 left-4 w-12 h-12 rounded-full bg-[#FFD700] flex items-center justify-center shadow-lg shadow-[#FFD700]/30">
                            <span class="text-black font-bold text-xl">1</span>
                        </div>
                        {{-- Crown icon --}}
                        <div class="absolute top-4 right-4">
                            <svg class="w-8 h-8 text-[#FFD700]" fill="currentColor" viewBox="0 0 24 24"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5z"/></svg>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col items-center gap-2 text-center">
                        <span class="text-[#FFD700] text-3xl">&#127942;</span>
                        <h4 class="text-white text-xl font-bold">Tomáš Kováč</h4>
                        <span class="text-[#888888] text-sm">BCZ Košice</span>
                        <div class="mt-2 bg-[#FFD700]/10 px-5 py-2.5 rounded-lg">
                            <span class="text-[#FFD700] text-2xl font-bold">94.2</span>
                            <span class="text-[#666666] text-xs ml-1">bodov</span>
                        </div>
                    </div>
                </div>

                {{-- 3rd Place - Bronze --}}
                <div class="w-full md:w-1/3 bg-[#111111] rounded-2xl border-2 border-[#CD7F32] overflow-hidden order-3">
                    <div class="relative h-[200px] bg-[#1A1A1A] flex items-center justify-center">
                        <div class="w-24 h-24 rounded-full bg-[#222222] flex items-center justify-center">
                            <svg class="w-12 h-12 text-[#444444]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div class="absolute top-4 left-4 w-10 h-10 rounded-full bg-[#CD7F32] flex items-center justify-center">
                            <span class="text-black font-bold text-lg">3</span>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col items-center gap-2 text-center">
                        <span class="text-[#CD7F32] text-2xl">&#129353;</span>
                        <h4 class="text-white text-lg font-bold">Martin Horváth</h4>
                        <span class="text-[#888888] text-sm">BCZ Žilina</span>
                        <div class="mt-2 bg-[#CD7F32]/10 px-4 py-2 rounded-lg">
                            <span class="text-[#CD7F32] text-xl font-bold">82.8</span>
                            <span class="text-[#666666] text-xs ml-1">bodov</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Men's nad 80kg Header --}}
            <div class="flex items-center gap-4 mb-8">
                <div class="w-1 h-8 bg-bcz-red rounded-full"></div>
                <h3 class="font-display font-bold text-[24px] md:text-[32px] tracking-wide">MUŽI NAD 80KG</h3>
            </div>

            {{-- Podium - Muži nad 80kg --}}
            <div class="flex flex-col md:flex-row items-end justify-center gap-6 mb-16">
                {{-- 2nd Place - Silver --}}
                <div class="w-full md:w-1/3 bg-[#111111] rounded-2xl border-2 border-[#C0C0C0] overflow-hidden order-2 md:order-1">
                    <div class="relative h-[200px] bg-[#1A1A1A] flex items-center justify-center">
                        <div class="w-24 h-24 rounded-full bg-[#222222] flex items-center justify-center">
                            <svg class="w-12 h-12 text-[#444444]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div class="absolute top-4 left-4 w-10 h-10 rounded-full bg-[#C0C0C0] flex items-center justify-center">
                            <span class="text-black font-bold text-lg">2</span>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col items-center gap-2 text-center">
                        <span class="text-[#C0C0C0] text-2xl">&#127944;</span>
                        <h4 class="text-white text-lg font-bold">Ján Majcher</h4>
                        <span class="text-[#888888] text-sm">BCZ Nitra</span>
                        <div class="mt-2 bg-[#C0C0C0]/10 px-4 py-2 rounded-lg">
                            <span class="text-[#C0C0C0] text-xl font-bold">89.1</span>
                            <span class="text-[#666666] text-xs ml-1">bodov</span>
                        </div>
                    </div>
                </div>

                {{-- 1st Place - Gold --}}
                <div class="w-full md:w-1/3 bg-[#111111] rounded-2xl border-2 border-[#FFD700] overflow-hidden md:-mt-6 order-1 md:order-2">
                    <div class="relative h-[220px] bg-[#1A1A1A] flex items-center justify-center">
                        <div class="w-28 h-28 rounded-full bg-[#222222] flex items-center justify-center ring-4 ring-[#FFD700]/30">
                            <svg class="w-14 h-14 text-[#444444]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div class="absolute top-4 left-4 w-12 h-12 rounded-full bg-[#FFD700] flex items-center justify-center shadow-lg shadow-[#FFD700]/30">
                            <span class="text-black font-bold text-xl">1</span>
                        </div>
                        <div class="absolute top-4 right-4">
                            <svg class="w-8 h-8 text-[#FFD700]" fill="currentColor" viewBox="0 0 24 24"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5z"/></svg>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col items-center gap-2 text-center">
                        <span class="text-[#FFD700] text-3xl">&#127942;</span>
                        <h4 class="text-white text-xl font-bold">Lukáš Baran</h4>
                        <span class="text-[#888888] text-sm">BCZ Bratislava</span>
                        <div class="mt-2 bg-[#FFD700]/10 px-5 py-2.5 rounded-lg">
                            <span class="text-[#FFD700] text-2xl font-bold">96.0</span>
                            <span class="text-[#666666] text-xs ml-1">bodov</span>
                        </div>
                    </div>
                </div>

                {{-- 3rd Place - Bronze --}}
                <div class="w-full md:w-1/3 bg-[#111111] rounded-2xl border-2 border-[#CD7F32] overflow-hidden order-3">
                    <div class="relative h-[200px] bg-[#1A1A1A] flex items-center justify-center">
                        <div class="w-24 h-24 rounded-full bg-[#222222] flex items-center justify-center">
                            <svg class="w-12 h-12 text-[#444444]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div class="absolute top-4 left-4 w-10 h-10 rounded-full bg-[#CD7F32] flex items-center justify-center">
                            <span class="text-black font-bold text-lg">3</span>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col items-center gap-2 text-center">
                        <span class="text-[#CD7F32] text-2xl">&#129353;</span>
                        <h4 class="text-white text-lg font-bold">Matej Šimko</h4>
                        <span class="text-[#888888] text-sm">BCZ Trenčín</span>
                        <div class="mt-2 bg-[#CD7F32]/10 px-4 py-2 rounded-lg">
                            <span class="text-[#CD7F32] text-xl font-bold">84.3</span>
                            <span class="text-[#666666] text-xs ml-1">bodov</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Women's Category Header --}}
            <div class="flex items-center gap-4 mb-8">
                <div class="w-1 h-8 bg-bcz-red rounded-full"></div>
                <h3 class="font-display font-bold text-[24px] md:text-[32px] tracking-wide">ŽENY DO 60KG</h3>
            </div>

            {{-- Podium - Ženy do 60kg --}}
            <div class="flex flex-col md:flex-row items-end justify-center gap-6 mb-16">
                {{-- 2nd Place - Silver --}}
                <div class="w-full md:w-1/3 bg-[#111111] rounded-2xl border-2 border-[#C0C0C0] overflow-hidden order-2 md:order-1">
                    <div class="relative h-[200px] bg-[#1A1A1A] flex items-center justify-center">
                        <div class="w-24 h-24 rounded-full bg-[#222222] flex items-center justify-center">
                            <svg class="w-12 h-12 text-[#444444]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div class="absolute top-4 left-4 w-10 h-10 rounded-full bg-[#C0C0C0] flex items-center justify-center">
                            <span class="text-black font-bold text-lg">2</span>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col items-center gap-2 text-center">
                        <span class="text-[#C0C0C0] text-2xl">&#127944;</span>
                        <h4 class="text-white text-lg font-bold">Lucia Tóthová</h4>
                        <span class="text-[#888888] text-sm">BCZ Košice</span>
                        <div class="mt-2 bg-[#C0C0C0]/10 px-4 py-2 rounded-lg">
                            <span class="text-[#C0C0C0] text-xl font-bold">85.4</span>
                            <span class="text-[#666666] text-xs ml-1">bodov</span>
                        </div>
                    </div>
                </div>

                {{-- 1st Place - Gold --}}
                <div class="w-full md:w-1/3 bg-[#111111] rounded-2xl border-2 border-[#FFD700] overflow-hidden md:-mt-6 order-1 md:order-2">
                    <div class="relative h-[220px] bg-[#1A1A1A] flex items-center justify-center">
                        <div class="w-28 h-28 rounded-full bg-[#222222] flex items-center justify-center ring-4 ring-[#FFD700]/30">
                            <svg class="w-14 h-14 text-[#444444]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div class="absolute top-4 left-4 w-12 h-12 rounded-full bg-[#FFD700] flex items-center justify-center shadow-lg shadow-[#FFD700]/30">
                            <span class="text-black font-bold text-xl">1</span>
                        </div>
                        <div class="absolute top-4 right-4">
                            <svg class="w-8 h-8 text-[#FFD700]" fill="currentColor" viewBox="0 0 24 24"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5z"/></svg>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col items-center gap-2 text-center">
                        <span class="text-[#FFD700] text-3xl">&#127942;</span>
                        <h4 class="text-white text-xl font-bold">Nina Kočišová</h4>
                        <span class="text-[#888888] text-sm">BCZ Bratislava</span>
                        <div class="mt-2 bg-[#FFD700]/10 px-5 py-2.5 rounded-lg">
                            <span class="text-[#FFD700] text-2xl font-bold">91.7</span>
                            <span class="text-[#666666] text-xs ml-1">bodov</span>
                        </div>
                    </div>
                </div>

                {{-- 3rd Place - Bronze --}}
                <div class="w-full md:w-1/3 bg-[#111111] rounded-2xl border-2 border-[#CD7F32] overflow-hidden order-3">
                    <div class="relative h-[200px] bg-[#1A1A1A] flex items-center justify-center">
                        <div class="w-24 h-24 rounded-full bg-[#222222] flex items-center justify-center">
                            <svg class="w-12 h-12 text-[#444444]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div class="absolute top-4 left-4 w-10 h-10 rounded-full bg-[#CD7F32] flex items-center justify-center">
                            <span class="text-black font-bold text-lg">3</span>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col items-center gap-2 text-center">
                        <span class="text-[#CD7F32] text-2xl">&#129353;</span>
                        <h4 class="text-white text-lg font-bold">Eva Miklošová</h4>
                        <span class="text-[#888888] text-sm">BCZ Prešov</span>
                        <div class="mt-2 bg-[#CD7F32]/10 px-4 py-2 rounded-lg">
                            <span class="text-[#CD7F32] text-xl font-bold">79.6</span>
                            <span class="text-[#666666] text-xs ml-1">bodov</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Women's nad 60kg Header --}}
            <div class="flex items-center gap-4 mb-8">
                <div class="w-1 h-8 bg-bcz-red rounded-full"></div>
                <h3 class="font-display font-bold text-[24px] md:text-[32px] tracking-wide">ŽENY NAD 60KG</h3>
            </div>

            {{-- Podium - Ženy nad 60kg --}}
            <div class="flex flex-col md:flex-row items-end justify-center gap-6 mb-8">
                {{-- 2nd Place - Silver --}}
                <div class="w-full md:w-1/3 bg-[#111111] rounded-2xl border-2 border-[#C0C0C0] overflow-hidden order-2 md:order-1">
                    <div class="relative h-[200px] bg-[#1A1A1A] flex items-center justify-center">
                        <div class="w-24 h-24 rounded-full bg-[#222222] flex items-center justify-center">
                            <svg class="w-12 h-12 text-[#444444]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div class="absolute top-4 left-4 w-10 h-10 rounded-full bg-[#C0C0C0] flex items-center justify-center">
                            <span class="text-black font-bold text-lg">2</span>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col items-center gap-2 text-center">
                        <span class="text-[#C0C0C0] text-2xl">&#127944;</span>
                        <h4 class="text-white text-lg font-bold">Michaela Vargová</h4>
                        <span class="text-[#888888] text-sm">BCZ Banská Bystrica</span>
                        <div class="mt-2 bg-[#C0C0C0]/10 px-4 py-2 rounded-lg">
                            <span class="text-[#C0C0C0] text-xl font-bold">83.9</span>
                            <span class="text-[#666666] text-xs ml-1">bodov</span>
                        </div>
                    </div>
                </div>

                {{-- 1st Place - Gold --}}
                <div class="w-full md:w-1/3 bg-[#111111] rounded-2xl border-2 border-[#FFD700] overflow-hidden md:-mt-6 order-1 md:order-2">
                    <div class="relative h-[220px] bg-[#1A1A1A] flex items-center justify-center">
                        <div class="w-28 h-28 rounded-full bg-[#222222] flex items-center justify-center ring-4 ring-[#FFD700]/30">
                            <svg class="w-14 h-14 text-[#444444]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div class="absolute top-4 left-4 w-12 h-12 rounded-full bg-[#FFD700] flex items-center justify-center shadow-lg shadow-[#FFD700]/30">
                            <span class="text-black font-bold text-xl">1</span>
                        </div>
                        <div class="absolute top-4 right-4">
                            <svg class="w-8 h-8 text-[#FFD700]" fill="currentColor" viewBox="0 0 24 24"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5z"/></svg>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col items-center gap-2 text-center">
                        <span class="text-[#FFD700] text-3xl">&#127942;</span>
                        <h4 class="text-white text-xl font-bold">Simona Baková</h4>
                        <span class="text-[#888888] text-sm">BCZ Bratislava</span>
                        <div class="mt-2 bg-[#FFD700]/10 px-5 py-2.5 rounded-lg">
                            <span class="text-[#FFD700] text-2xl font-bold">90.3</span>
                            <span class="text-[#666666] text-xs ml-1">bodov</span>
                        </div>
                    </div>
                </div>

                {{-- 3rd Place - Bronze --}}
                <div class="w-full md:w-1/3 bg-[#111111] rounded-2xl border-2 border-[#CD7F32] overflow-hidden order-3">
                    <div class="relative h-[200px] bg-[#1A1A1A] flex items-center justify-center">
                        <div class="w-24 h-24 rounded-full bg-[#222222] flex items-center justify-center">
                            <svg class="w-12 h-12 text-[#444444]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div class="absolute top-4 left-4 w-10 h-10 rounded-full bg-[#CD7F32] flex items-center justify-center">
                            <span class="text-black font-bold text-lg">3</span>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col items-center gap-2 text-center">
                        <span class="text-[#CD7F32] text-2xl">&#129353;</span>
                        <h4 class="text-white text-lg font-bold">Zuzana Kráľová</h4>
                        <span class="text-[#888888] text-sm">BCZ Žilina</span>
                        <div class="mt-2 bg-[#CD7F32]/10 px-4 py-2 rounded-lg">
                            <span class="text-[#CD7F32] text-xl font-bold">78.2</span>
                            <span class="text-[#666666] text-xs ml-1">bodov</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Qualification Table --}}
    <section id="kvalifikacia" class="bg-[#0D0D0D] py-16">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            {{-- Section Header --}}
            <div class="flex flex-col gap-4 mb-10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[3px]">KVALIFIKÁCIA</span>
                </div>
                <h2 class="font-display font-bold text-[28px] md:text-[40px] tracking-wide">KOMPLETNÉ PORADIE</h2>
            </div>

            {{-- Category Filter --}}
            <div class="flex flex-wrap items-center gap-3 mb-8">
                <button class="bg-bcz-red text-white px-5 py-2.5 rounded-lg text-xs font-bold tracking-widest">MUŽI DO 80KG</button>
                <button class="bg-[#1A1A1A] text-[#888888] hover:text-white px-5 py-2.5 rounded-lg text-xs font-bold tracking-widest transition-colors">MUŽI NAD 80KG</button>
                <button class="bg-[#1A1A1A] text-[#888888] hover:text-white px-5 py-2.5 rounded-lg text-xs font-bold tracking-widest transition-colors">ŽENY DO 60KG</button>
                <button class="bg-[#1A1A1A] text-[#888888] hover:text-white px-5 py-2.5 rounded-lg text-xs font-bold tracking-widest transition-colors">ŽENY NAD 60KG</button>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-[#222222]">
                            <th class="text-left text-[#666666] text-[11px] font-bold tracking-widest py-4 px-4">#</th>
                            <th class="text-left text-[#666666] text-[11px] font-bold tracking-widest py-4 px-4">MENO</th>
                            <th class="text-left text-[#666666] text-[11px] font-bold tracking-widest py-4 px-4">KLUB</th>
                            <th class="text-center text-[#666666] text-[11px] font-bold tracking-widest py-4 px-4">CVIK 1</th>
                            <th class="text-center text-[#666666] text-[11px] font-bold tracking-widest py-4 px-4">CVIK 2</th>
                            <th class="text-center text-[#666666] text-[11px] font-bold tracking-widest py-4 px-4">CVIK 3</th>
                            <th class="text-center text-[#666666] text-[11px] font-bold tracking-widest py-4 px-4">CVIK 4</th>
                            <th class="text-center text-[#666666] text-[11px] font-bold tracking-widest py-4 px-4">SPOLU</th>
                            <th class="text-center text-[#666666] text-[11px] font-bold tracking-widest py-4 px-4">STAV</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Row 1 - Gold --}}
                        <tr class="border-b border-[#1A1A1A] bg-[#FFD700]/5">
                            <td class="py-4 px-4 text-[#FFD700] font-bold">1</td>
                            <td class="py-4 px-4 text-white font-semibold">Tomáš Kováč</td>
                            <td class="py-4 px-4 text-[#888888] text-sm">BCZ Košice</td>
                            <td class="py-4 px-4 text-center text-[#CCCCCC]">24.5</td>
                            <td class="py-4 px-4 text-center text-[#CCCCCC]">23.2</td>
                            <td class="py-4 px-4 text-center text-[#CCCCCC]">22.8</td>
                            <td class="py-4 px-4 text-center text-[#CCCCCC]">23.7</td>
                            <td class="py-4 px-4 text-center text-[#FFD700] font-bold">94.2</td>
                            <td class="py-4 px-4 text-center"><span class="bg-[#FFD700]/20 text-[#FFD700] text-[10px] font-bold px-3 py-1 rounded-full">VÍŤAZ</span></td>
                        </tr>
                        {{-- Row 2 - Silver --}}
                        <tr class="border-b border-[#1A1A1A] bg-[#C0C0C0]/5">
                            <td class="py-4 px-4 text-[#C0C0C0] font-bold">2</td>
                            <td class="py-4 px-4 text-white font-semibold">Peter Novák</td>
                            <td class="py-4 px-4 text-[#888888] text-sm">BCZ Bratislava</td>
                            <td class="py-4 px-4 text-center text-[#CCCCCC]">22.1</td>
                            <td class="py-4 px-4 text-center text-[#CCCCCC]">21.8</td>
                            <td class="py-4 px-4 text-center text-[#CCCCCC]">22.0</td>
                            <td class="py-4 px-4 text-center text-[#CCCCCC]">21.6</td>
                            <td class="py-4 px-4 text-center text-[#C0C0C0] font-bold">87.5</td>
                            <td class="py-4 px-4 text-center"><span class="bg-[#C0C0C0]/20 text-[#C0C0C0] text-[10px] font-bold px-3 py-1 rounded-full">2. MIESTO</span></td>
                        </tr>
                        {{-- Row 3 - Bronze --}}
                        <tr class="border-b border-[#1A1A1A] bg-[#CD7F32]/5">
                            <td class="py-4 px-4 text-[#CD7F32] font-bold">3</td>
                            <td class="py-4 px-4 text-white font-semibold">Martin Horváth</td>
                            <td class="py-4 px-4 text-[#888888] text-sm">BCZ Žilina</td>
                            <td class="py-4 px-4 text-center text-[#CCCCCC]">21.0</td>
                            <td class="py-4 px-4 text-center text-[#CCCCCC]">20.5</td>
                            <td class="py-4 px-4 text-center text-[#CCCCCC]">20.8</td>
                            <td class="py-4 px-4 text-center text-[#CCCCCC]">20.5</td>
                            <td class="py-4 px-4 text-center text-[#CD7F32] font-bold">82.8</td>
                            <td class="py-4 px-4 text-center"><span class="bg-[#CD7F32]/20 text-[#CD7F32] text-[10px] font-bold px-3 py-1 rounded-full">3. MIESTO</span></td>
                        </tr>
                        {{-- Row 4 --}}
                        <tr class="border-b border-[#1A1A1A]">
                            <td class="py-4 px-4 text-[#888888] font-bold">4</td>
                            <td class="py-4 px-4 text-white font-semibold">Andrej Polák</td>
                            <td class="py-4 px-4 text-[#888888] text-sm">BCZ Prešov</td>
                            <td class="py-4 px-4 text-center text-[#CCCCCC]">20.2</td>
                            <td class="py-4 px-4 text-center text-[#CCCCCC]">19.8</td>
                            <td class="py-4 px-4 text-center text-[#CCCCCC]">20.0</td>
                            <td class="py-4 px-4 text-center text-[#CCCCCC]">19.5</td>
                            <td class="py-4 px-4 text-center text-white font-bold">79.5</td>
                            <td class="py-4 px-4 text-center"><span class="bg-[#333333] text-[#888888] text-[10px] font-bold px-3 py-1 rounded-full">SEMIFINÁLE</span></td>
                        </tr>
                        {{-- Row 5 --}}
                        <tr class="border-b border-[#1A1A1A]">
                            <td class="py-4 px-4 text-[#888888] font-bold">5</td>
                            <td class="py-4 px-4 text-white font-semibold">Filip Dvořák</td>
                            <td class="py-4 px-4 text-[#888888] text-sm">BCZ Trnava</td>
                            <td class="py-4 px-4 text-center text-[#CCCCCC]">19.5</td>
                            <td class="py-4 px-4 text-center text-[#CCCCCC]">19.0</td>
                            <td class="py-4 px-4 text-center text-[#CCCCCC]">19.8</td>
                            <td class="py-4 px-4 text-center text-[#CCCCCC]">19.2</td>
                            <td class="py-4 px-4 text-center text-white font-bold">77.5</td>
                            <td class="py-4 px-4 text-center"><span class="bg-[#333333] text-[#888888] text-[10px] font-bold px-3 py-1 rounded-full">SEMIFINÁLE</span></td>
                        </tr>
                        {{-- Row 6 --}}
                        <tr class="border-b border-[#1A1A1A]">
                            <td class="py-4 px-4 text-[#888888] font-bold">6</td>
                            <td class="py-4 px-4 text-white font-semibold">Dávid Kučera</td>
                            <td class="py-4 px-4 text-[#888888] text-sm">BCZ Martin</td>
                            <td class="py-4 px-4 text-center text-[#CCCCCC]">18.8</td>
                            <td class="py-4 px-4 text-center text-[#CCCCCC]">18.5</td>
                            <td class="py-4 px-4 text-center text-[#CCCCCC]">19.0</td>
                            <td class="py-4 px-4 text-center text-[#CCCCCC]">18.2</td>
                            <td class="py-4 px-4 text-center text-white font-bold">74.5</td>
                            <td class="py-4 px-4 text-center"><span class="bg-[#333333] text-[#888888] text-[10px] font-bold px-3 py-1 rounded-full">ŠTVRŤFINÁLE</span></td>
                        </tr>
                        {{-- Row 7 --}}
                        <tr class="border-b border-[#1A1A1A]">
                            <td class="py-4 px-4 text-[#888888] font-bold">7</td>
                            <td class="py-4 px-4 text-white font-semibold">Samuel Vrábel</td>
                            <td class="py-4 px-4 text-[#888888] text-sm">BCZ Poprad</td>
                            <td class="py-4 px-4 text-center text-[#CCCCCC]">18.0</td>
                            <td class="py-4 px-4 text-center text-[#CCCCCC]">17.5</td>
                            <td class="py-4 px-4 text-center text-[#CCCCCC]">18.2</td>
                            <td class="py-4 px-4 text-center text-[#CCCCCC]">17.8</td>
                            <td class="py-4 px-4 text-center text-white font-bold">71.5</td>
                            <td class="py-4 px-4 text-center"><span class="bg-[#333333] text-[#888888] text-[10px] font-bold px-3 py-1 rounded-full">ŠTVRŤFINÁLE</span></td>
                        </tr>
                        {{-- Row 8 --}}
                        <tr class="border-b border-[#1A1A1A]">
                            <td class="py-4 px-4 text-[#888888] font-bold">8</td>
                            <td class="py-4 px-4 text-white font-semibold">Róbert Szabó</td>
                            <td class="py-4 px-4 text-[#888888] text-sm">BCZ Levice</td>
                            <td class="py-4 px-4 text-center text-[#CCCCCC]">17.5</td>
                            <td class="py-4 px-4 text-center text-[#CCCCCC]">17.0</td>
                            <td class="py-4 px-4 text-center text-[#CCCCCC]">17.8</td>
                            <td class="py-4 px-4 text-center text-[#CCCCCC]">17.2</td>
                            <td class="py-4 px-4 text-center text-white font-bold">69.5</td>
                            <td class="py-4 px-4 text-center"><span class="bg-[#333333] text-[#888888] text-[10px] font-bold px-3 py-1 rounded-full">ŠTVRŤFINÁLE</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- Battle Bracket --}}
    <section id="battle-bracket" class="bg-bcz-dark py-16">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            {{-- Section Header --}}
            <div class="flex flex-col gap-4 mb-10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[3px]">BATTLE</span>
                </div>
                <h2 class="font-display font-bold text-[28px] md:text-[40px] tracking-wide">BATTLE BRACKET</h2>
                <p class="text-[#888888] text-sm">Muži do 80kg - kompletné výsledky</p>
            </div>

            {{-- Bracket --}}
            <div class="overflow-x-auto pb-4">
                <div class="flex items-start gap-8 min-w-[900px]">
                    {{-- Quarter Finals --}}
                    <div class="flex flex-col gap-6 w-[200px] shrink-0">
                        <span class="text-[#666666] text-[11px] font-bold tracking-widest mb-2">ŠTVRŤFINÁLE</span>

                        {{-- Match 1 --}}
                        <div class="flex flex-col rounded-lg overflow-hidden">
                            <div class="bg-[#FFD700]/10 border border-[#FFD700]/30 px-4 py-3 flex justify-between items-center">
                                <span class="text-white text-sm font-semibold">Tomáš Kováč</span>
                                <span class="text-[#FFD700] text-sm font-bold">W</span>
                            </div>
                            <div class="bg-[#111111] border border-[#222222] border-t-0 px-4 py-3 flex justify-between items-center">
                                <span class="text-[#666666] text-sm">Róbert Szabó</span>
                                <span class="text-[#666666] text-sm font-bold">L</span>
                            </div>
                        </div>

                        {{-- Match 2 --}}
                        <div class="flex flex-col rounded-lg overflow-hidden">
                            <div class="bg-[#FFD700]/10 border border-[#FFD700]/30 px-4 py-3 flex justify-between items-center">
                                <span class="text-white text-sm font-semibold">Peter Novák</span>
                                <span class="text-[#FFD700] text-sm font-bold">W</span>
                            </div>
                            <div class="bg-[#111111] border border-[#222222] border-t-0 px-4 py-3 flex justify-between items-center">
                                <span class="text-[#666666] text-sm">Samuel Vrábel</span>
                                <span class="text-[#666666] text-sm font-bold">L</span>
                            </div>
                        </div>

                        {{-- Match 3 --}}
                        <div class="flex flex-col rounded-lg overflow-hidden">
                            <div class="bg-[#FFD700]/10 border border-[#FFD700]/30 px-4 py-3 flex justify-between items-center">
                                <span class="text-white text-sm font-semibold">Martin Horváth</span>
                                <span class="text-[#FFD700] text-sm font-bold">W</span>
                            </div>
                            <div class="bg-[#111111] border border-[#222222] border-t-0 px-4 py-3 flex justify-between items-center">
                                <span class="text-[#666666] text-sm">Dávid Kučera</span>
                                <span class="text-[#666666] text-sm font-bold">L</span>
                            </div>
                        </div>

                        {{-- Match 4 --}}
                        <div class="flex flex-col rounded-lg overflow-hidden">
                            <div class="bg-[#FFD700]/10 border border-[#FFD700]/30 px-4 py-3 flex justify-between items-center">
                                <span class="text-white text-sm font-semibold">Andrej Polák</span>
                                <span class="text-[#FFD700] text-sm font-bold">W</span>
                            </div>
                            <div class="bg-[#111111] border border-[#222222] border-t-0 px-4 py-3 flex justify-between items-center">
                                <span class="text-[#666666] text-sm">Filip Dvořák</span>
                                <span class="text-[#666666] text-sm font-bold">L</span>
                            </div>
                        </div>
                    </div>

                    {{-- Connector Lines QF -> SF --}}
                    <div class="flex flex-col justify-around h-[480px] w-[40px] shrink-0">
                        <div class="border-r-2 border-t-2 border-b-2 border-[#333333] h-[100px] rounded-r-lg"></div>
                        <div class="border-r-2 border-t-2 border-b-2 border-[#333333] h-[100px] rounded-r-lg"></div>
                    </div>

                    {{-- Semi Finals --}}
                    <div class="flex flex-col gap-20 w-[200px] shrink-0 mt-[60px]">
                        <span class="text-[#666666] text-[11px] font-bold tracking-widest mb-2">SEMIFINÁLE</span>

                        {{-- SF Match 1 --}}
                        <div class="flex flex-col rounded-lg overflow-hidden">
                            <div class="bg-[#FFD700]/10 border border-[#FFD700]/30 px-4 py-3 flex justify-between items-center">
                                <span class="text-white text-sm font-semibold">Tomáš Kováč</span>
                                <span class="text-[#FFD700] text-sm font-bold">W</span>
                            </div>
                            <div class="bg-[#111111] border border-[#222222] border-t-0 px-4 py-3 flex justify-between items-center">
                                <span class="text-[#666666] text-sm">Peter Novák</span>
                                <span class="text-[#666666] text-sm font-bold">L</span>
                            </div>
                        </div>

                        {{-- SF Match 2 --}}
                        <div class="flex flex-col rounded-lg overflow-hidden">
                            <div class="bg-[#FFD700]/10 border border-[#FFD700]/30 px-4 py-3 flex justify-between items-center">
                                <span class="text-white text-sm font-semibold">Martin Horváth</span>
                                <span class="text-[#FFD700] text-sm font-bold">W</span>
                            </div>
                            <div class="bg-[#111111] border border-[#222222] border-t-0 px-4 py-3 flex justify-between items-center">
                                <span class="text-[#666666] text-sm">Andrej Polák</span>
                                <span class="text-[#666666] text-sm font-bold">L</span>
                            </div>
                        </div>
                    </div>

                    {{-- Connector Lines SF -> F --}}
                    <div class="flex flex-col justify-center h-[480px] w-[40px] shrink-0">
                        <div class="border-r-2 border-t-2 border-b-2 border-[#333333] h-[160px] rounded-r-lg"></div>
                    </div>

                    {{-- Finals --}}
                    <div class="flex flex-col w-[220px] shrink-0 mt-[180px]">
                        <span class="text-[#666666] text-[11px] font-bold tracking-widest mb-4">FINÁLE</span>

                        {{-- Final Match --}}
                        <div class="flex flex-col rounded-lg overflow-hidden border-2 border-[#FFD700]">
                            <div class="bg-[#FFD700]/15 px-4 py-4 flex justify-between items-center">
                                <div class="flex items-center gap-3">
                                    <span class="text-[#FFD700] text-lg">&#127942;</span>
                                    <span class="text-white text-sm font-bold">Tomáš Kováč</span>
                                </div>
                                <span class="text-[#FFD700] text-sm font-bold">W</span>
                            </div>
                            <div class="bg-[#111111] px-4 py-4 flex justify-between items-center">
                                <div class="flex items-center gap-3">
                                    <span class="text-[#CD7F32] text-lg">&#129353;</span>
                                    <span class="text-[#888888] text-sm">Martin Horváth</span>
                                </div>
                                <span class="text-[#666666] text-sm font-bold">L</span>
                            </div>
                        </div>

                        {{-- 3rd Place Match --}}
                        <div class="mt-8">
                            <span class="text-[#666666] text-[11px] font-bold tracking-widest mb-4 block">O 3. MIESTO</span>
                            <div class="flex flex-col rounded-lg overflow-hidden border border-[#CD7F32]/50">
                                <div class="bg-[#CD7F32]/10 px-4 py-3 flex justify-between items-center">
                                    <div class="flex items-center gap-3">
                                        <span class="text-[#C0C0C0] text-base">&#127944;</span>
                                        <span class="text-white text-sm font-semibold">Peter Novák</span>
                                    </div>
                                    <span class="text-[#FFD700] text-sm font-bold">W</span>
                                </div>
                                <div class="bg-[#111111] border-t border-[#222222] px-4 py-3 flex justify-between items-center">
                                    <span class="text-[#666666] text-sm">Andrej Polák</span>
                                    <span class="text-[#666666] text-sm font-bold">L</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
