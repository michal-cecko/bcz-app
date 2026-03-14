@extends('layouts.public')

@section('title', 'Vystúpenia &amp; Workshopy | BCZ Club')

@section('content')
    {{-- Hero Section --}}
    <section class="bg-bcz-dark w-full h-[600px] pt-[120px] pb-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 h-full flex flex-col items-center justify-center gap-8">
        {{-- Label --}}
        <div class="flex items-center gap-3">
            <div class="w-10 h-0.5 bg-bcz-red"></div>
            <span class="text-bcz-red text-[12px] font-bold tracking-[2px]">SLUŽBY PRE FIRMY &amp; EVENTY</span>
            <div class="w-10 h-0.5 bg-bcz-red"></div>
        </div>

        {{-- Title --}}
        <h1 class="font-display font-bold text-[32px] md:text-[48px] lg:text-[64px] leading-[1.1] tracking-wide text-center text-white">
            Vystúpenia, Workshopy<br>&amp; Prednášky
        </h1>

        {{-- Description --}}
        <p class="text-[#AAAAAA] text-[20px] text-center max-w-[800px]">
            Prinášame akrobatické umenie, inšpiratívne prednášky a praktické workshopy pre vaše podujatia, školy a fitness centrá.
        </p>

        {{-- Pricing Badge --}}
        <div class="bg-bcz-red/20 border border-bcz-red/40 text-bcz-red rounded-lg px-6 py-3 text-sm">
            &#x1F4B0; Cena je individuálna podža rozsahu a typu akcie
        </div>
        </div>
    </section>

    {{-- Categories Section --}}
    <section class="bg-bcz-dark py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
        <div class="flex flex-col items-center gap-16">
            {{-- Header --}}
            <div class="flex flex-col items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[3px]">ČO PONÚKAME</span>
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                </div>
                <h2 class="font-display font-bold text-[24px] md:text-[36px] lg:text-[48px] tracking-wide text-white">Naše služby</h2>
                <p class="text-[#888888] text-center max-w-[600px]">
                    Vyberajte z troch hlavných kategórií služieb, ktoré prispôsobíme vašim potrebám.
                </p>
            </div>

            {{-- 3-column Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-full">
                {{-- Card 1 - Vystupenia --}}
                <div class="bg-[#111111] rounded-2xl overflow-hidden border border-[#FF2D2D]/40 flex flex-col">
                    <div class="w-full h-[220px] bg-[url('https://images.unsplash.com/photo-1742980823479-af4a1d77ca82?w=800&q=80')] bg-cover bg-center"></div>
                    <div class="flex flex-col gap-4 p-6">
                        <div class="w-12 h-12 bg-bcz-red/20 rounded-xl flex items-center justify-center text-2xl">&#x1F3AD;</div>
                        <h3 class="font-display font-bold text-[28px] text-white">Vystúpenia</h3>
                        <span class="text-bcz-red text-[14px] font-semibold">Akrobatické umenie pre divákov</span>
                        <p class="text-[#888888] text-[15px] leading-[1.6]">
                            Dynamické akrobatické show pre firemné eventy, festivaly, otvorenia a špeciálne príležitosti. Kombinujeme parkour, freerunning a akrobaciu do nezabudnutežného vizuálneho zážitku.
                        </p>
                        <div class="flex flex-col gap-2.5">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-5 h-5 text-[#22C55E] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                                <span class="text-[#888888] text-[14px]">Profesionálna choreografia</span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <svg class="w-5 h-5 text-[#22C55E] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                                <span class="text-[#888888] text-[14px]">Prispôsobenie vášmu eventu</span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <svg class="w-5 h-5 text-[#22C55E] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                                <span class="text-[#888888] text-[14px]">Indoor aj outdoor</span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <svg class="w-5 h-5 text-[#22C55E] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                                <span class="text-[#888888] text-[14px]">Až 10 performerov</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 2 - Prednasky --}}
                <div class="bg-[#111111] rounded-2xl overflow-hidden border border-[#3B82F6]/40 flex flex-col">
                    <div class="w-full h-[220px] bg-[url('https://images.unsplash.com/photo-1575975008013-60992f23c192?w=800&q=80')] bg-cover bg-center"></div>
                    <div class="flex flex-col gap-4 p-6">
                        <div class="w-12 h-12 bg-[#3B82F6]/20 rounded-xl flex items-center justify-center text-2xl">&#x1F393;</div>
                        <h3 class="font-display font-bold text-[28px] text-white">Prednášky</h3>
                        <span class="text-[#3B82F6] text-[14px] font-semibold">Inšpirácia pre školy a organizácie</span>
                        <p class="text-[#888888] text-[15px] leading-[1.6]">
                            Motivačné prednášky o správnom nastavení mysle, hodnotových rebríčkoch a výhodách cvičenia. Učíme mladých žudí trpezlivosti, tvrdej drine a vytrvalosti cez náš príbeh.
                        </p>
                        <div class="flex flex-col gap-2.5">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-5 h-5 text-[#22C55E] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                                <span class="text-[#888888] text-[14px]">Pre školy a organizácie</span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <svg class="w-5 h-5 text-[#22C55E] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                                <span class="text-[#888888] text-[14px]">Motivačný obsah</span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <svg class="w-5 h-5 text-[#22C55E] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                                <span class="text-[#888888] text-[14px]">Interaktívny formát</span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <svg class="w-5 h-5 text-[#22C55E] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                                <span class="text-[#888888] text-[14px]">Q&amp;A sekcia</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 3 - Workshopy --}}
                <div class="bg-[#111111] rounded-2xl overflow-hidden border border-[#22C55E]/40 flex flex-col">
                    <div class="w-full h-[220px] bg-[url('https://images.unsplash.com/photo-1716367840407-f9414a84b325?w=800&q=80')] bg-cover bg-center"></div>
                    <div class="flex flex-col gap-4 p-6">
                        <div class="w-12 h-12 bg-[#22C55E]/20 rounded-xl flex items-center justify-center text-2xl">&#x1F4AA;</div>
                        <h3 class="font-display font-bold text-[28px] text-white">Workshopy</h3>
                        <span class="text-[#22C55E] text-[14px] font-semibold">Praktické kurzy pre všetkých</span>
                        <p class="text-[#888888] text-[15px] leading-[1.6]">
                            Workshopy pre fitness centrá, trénerov a podujatia. Učíme základné aj pokročilé prvky - od bezpečného pádu až po kurz stojky. Prispôsobíme sa vašej úrovni.
                        </p>
                        <div class="flex flex-col gap-2.5">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-5 h-5 text-[#22C55E] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                                <span class="text-[#888888] text-[14px]">Pre fitness centrá</span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <svg class="w-5 h-5 text-[#22C55E] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                                <span class="text-[#888888] text-[14px]">Všetky úrovne</span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <svg class="w-5 h-5 text-[#22C55E] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                                <span class="text-[#888888] text-[14px]">Praktické cvičenia</span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <svg class="w-5 h-5 text-[#22C55E] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                                <span class="text-[#888888] text-[14px]">Skupiny do 20 žudí</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>

    {{-- Process Section --}}
    <section class="bg-[#0D0D0D] py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
        <div class="flex flex-col items-center gap-16">
            {{-- Header --}}
            <div class="flex flex-col items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[3px]">PROCES</span>
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                </div>
                <h2 class="font-display font-bold text-[24px] md:text-[36px] lg:text-[48px] tracking-wide text-white">Ako spolupracujeme</h2>
                <p class="text-[#888888] text-center max-w-[600px]">
                    Od prvého kontaktu po úspešnú realizáciu - jednoduchý a transparentný proces.
                </p>
            </div>

            {{-- 4-column Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 w-full">
                {{-- Step 1 --}}
                <div class="bg-[#111111] border border-[#222222] rounded-xl p-6 flex flex-col gap-4">
                    <div class="w-12 h-12 bg-bcz-red rounded-full flex items-center justify-center">
                        <span class="text-white font-bold text-[20px]">1</span>
                    </div>
                    <h3 class="text-white font-bold text-[20px]">Kontakt</h3>
                    <p class="text-[#888888] text-[15px] leading-[1.6]">
                        Napíšte nám cez kontaktný formulár alebo email. Popíšte typ podujatia, dátum a vaše predstavy.
                    </p>
                </div>

                {{-- Step 2 --}}
                <div class="bg-[#111111] border border-[#222222] rounded-xl p-6 flex flex-col gap-4">
                    <div class="w-12 h-12 bg-bcz-red rounded-full flex items-center justify-center">
                        <span class="text-white font-bold text-[20px]">2</span>
                    </div>
                    <h3 class="text-white font-bold text-[20px]">Konzultácia</h3>
                    <p class="text-[#888888] text-[15px] leading-[1.6]">
                        Preberieme detaily, vaše požiadavky a navrhneme riešenie šité na mieru vášmu eventu.
                    </p>
                </div>

                {{-- Step 3 --}}
                <div class="bg-[#111111] border border-[#222222] rounded-xl p-6 flex flex-col gap-4">
                    <div class="w-12 h-12 bg-bcz-red rounded-full flex items-center justify-center">
                        <span class="text-white font-bold text-[20px]">3</span>
                    </div>
                    <h3 class="text-white font-bold text-[20px]">Príprava</h3>
                    <p class="text-[#888888] text-[15px] leading-[1.6]">
                        Pripravíme program, nacvičíme choreografiu a doladíme všetky detaily pred vaším podujatím.
                    </p>
                </div>

                {{-- Step 4 --}}
                <div class="bg-[#111111] border border-[#222222] rounded-xl p-6 flex flex-col gap-4">
                    <div class="w-12 h-12 bg-bcz-red rounded-full flex items-center justify-center">
                        <span class="text-white font-bold text-[20px]">4</span>
                    </div>
                    <h3 class="text-white font-bold text-[20px]">Realizácia</h3>
                    <p class="text-[#888888] text-[15px] leading-[1.6]">
                        Dodáme nezabudnutežný zážitok pre vás a vašich hostí. Profesionálne, spožahlivo a s energiou.
                    </p>
                </div>
            </div>
        </div>
        </div>
    </section>

    {{-- Events Section --}}
    <section class="bg-bcz-dark py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
        <div class="flex flex-col gap-12">
            {{-- Header Row --}}
            <div class="flex items-end justify-between">
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                        <span class="text-bcz-red text-xs font-bold tracking-[3px]">PORTFÓLIO</span>
                    </div>
                    <h2 class="font-display font-bold text-[24px] md:text-[36px] lg:text-[48px] tracking-wide text-white">Kde sme vystupovali</h2>
                </div>
                <a href="#" class="border border-bcz-red text-bcz-red rounded-lg px-6 py-3 text-sm font-semibold hover:bg-bcz-red hover:text-white transition-colors">
                    Všetky podujatia →
                </a>
            </div>

            {{-- 3-column Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Event 1 --}}
                <div class="bg-[#111111] rounded-2xl overflow-hidden">
                    <div class="w-full h-[200px] bg-[url('https://images.unsplash.com/photo-1761145090303-670cf0c19773?w=800&q=80')] bg-cover bg-center"></div>
                    <div class="flex flex-col gap-3 p-5">
                        <span class="bg-bcz-red/20 text-bcz-red text-[10px] font-bold px-2.5 py-1 rounded w-fit">VYSTÚPENIE</span>
                        <h3 class="text-white text-[18px] font-bold">Grape Festival 2024</h3>
                        <p class="text-[#888888] text-[13px]">Hlavné pódium na najväčšom slovenskom festivale.</p>
                        <span class="text-[#666666] text-[12px]">August 2024 &middot; Piešťany</span>
                    </div>
                </div>

                {{-- Event 2 --}}
                <div class="bg-[#111111] rounded-2xl overflow-hidden">
                    <div class="w-full h-[200px] bg-[url('https://images.unsplash.com/photo-1690079374922-7f50d5c1a102?w=800&q=80')] bg-cover bg-center"></div>
                    <div class="flex flex-col gap-3 p-5">
                        <span class="bg-[#3B82F6]/20 text-[#3B82F6] text-[10px] font-bold px-2.5 py-1 rounded w-fit">PREDNÁŠKA</span>
                        <h3 class="text-white text-[18px] font-bold">Gymnázium Metodova</h3>
                        <p class="text-[#888888] text-[13px]">Motivačná prednáška pre študentov o nastavení mysle.</p>
                        <span class="text-[#666666] text-[12px]">Október 2024 &middot; Bratislava</span>
                    </div>
                </div>

                {{-- Event 3 --}}
                <div class="bg-[#111111] rounded-2xl overflow-hidden">
                    <div class="w-full h-[200px] bg-[url('https://images.unsplash.com/photo-1760331840426-027b269d0af2?w=800&q=80')] bg-cover bg-center"></div>
                    <div class="flex flex-col gap-3 p-5">
                        <span class="bg-[#22C55E]/20 text-[#22C55E] text-[10px] font-bold px-2.5 py-1 rounded w-fit">WORKSHOP</span>
                        <h3 class="text-white text-[18px] font-bold">Fitness Factory BA</h3>
                        <p class="text-[#888888] text-[13px]">Kurz stojky pre členov fitness centra.</p>
                        <span class="text-[#666666] text-[12px]">November 2024 &middot; Bratislava</span>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>

    {{-- Contact Section --}}
    <section class="bg-[#0D0D0D] py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-16">
            {{-- Left Column --}}
            <div class="flex flex-col gap-8">
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                        <span class="text-bcz-red text-xs font-bold tracking-[3px]">KONTAKT</span>
                    </div>
                    <h2 class="font-display font-bold text-[42px] leading-[1.2] tracking-wide text-white">Máte záujem o spoluprácu?</h2>
                    <p class="text-[#888888] text-[16px] leading-[1.6]">
                        Vyplňte formulár a my sa vám ozveme do 24 hodín. Radi vám pripravíme ponuku na mieru.
                    </p>
                </div>

                {{-- Contact Info --}}
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-bcz-red/20 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                        </div>
                        <span class="text-white text-[16px]">info@bczclub.sk</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-bcz-red/20 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        </div>
                        <span class="text-white text-[16px]">+421 900 123 456</span>
                    </div>
                </div>
            </div>

            {{-- Right Column - Form --}}
            <div class="bg-[#111111] border border-[#222222] rounded-2xl p-8">
                <form class="flex flex-col gap-5">
                    {{-- Row 1: Name + Email --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-2">
                            <label class="text-white text-[14px] font-medium">Meno</label>
                            <input type="text" placeholder="Vaše meno" class="bg-bcz-dark border border-[#333333] rounded-lg h-12 px-4 text-white placeholder-[#666666] text-sm focus:border-bcz-red focus:outline-none transition-colors">
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-white text-[14px] font-medium">Email</label>
                            <input type="email" placeholder="vas@email.sk" class="bg-bcz-dark border border-[#333333] rounded-lg h-12 px-4 text-white placeholder-[#666666] text-sm focus:border-bcz-red focus:outline-none transition-colors">
                        </div>
                    </div>

                    {{-- Row 2: Phone + Service Type --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-2">
                            <label class="text-white text-[14px] font-medium">Telefón</label>
                            <input type="tel" placeholder="+421 ..." class="bg-bcz-dark border border-[#333333] rounded-lg h-12 px-4 text-white placeholder-[#666666] text-sm focus:border-bcz-red focus:outline-none transition-colors">
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-white text-[14px] font-medium">Typ služby</label>
                            <select class="bg-bcz-dark border border-[#333333] rounded-lg h-12 px-4 text-white text-sm focus:border-bcz-red focus:outline-none transition-colors appearance-none">
                                <option value="" class="text-[#666666]">Vyberte typ služby</option>
                                <option value="vystupenie">Vystúpenie</option>
                                <option value="prednaska">Prednáška</option>
                                <option value="workshop">Workshop</option>
                            </select>
                        </div>
                    </div>

                    {{-- Row 3: Message --}}
                    <div class="flex flex-col gap-2">
                        <label class="text-white text-[14px] font-medium">Správa</label>
                        <textarea placeholder="Popíšte vaše podujatie, dátum a predstavy..." class="bg-bcz-dark border border-[#333333] rounded-lg h-[120px] px-4 py-3 text-white placeholder-[#666666] text-sm focus:border-bcz-red focus:outline-none transition-colors resize-none"></textarea>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="bg-bcz-red w-full h-[52px] rounded-lg text-white font-semibold hover:bg-bcz-red/90 transition-colors">
                        Odoslať dopyt →
                    </button>
                </form>
            </div>
        </div>
        </div>
    </section>
@endsection
