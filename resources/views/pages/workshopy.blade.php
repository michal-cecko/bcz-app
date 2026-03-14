@extends('layouts.public')

@section('title', 'Praktické Workshopy | BCZ Club')

@section('content')
    {{-- Hero Section --}}
    <section class="relative w-full h-[700px] overflow-hidden bg-bcz-dark">
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1638536534846-16e51e69a9b3?w=1080&q=80')] bg-cover bg-center"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-[#0A0A0A] via-[#0A0A0ACC] to-transparent"></div>
        <div class="relative max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 h-full flex flex-col justify-end pb-20 gap-6">
            {{-- Breadcrumbs --}}
            <span class="text-bcz-dim text-[13px]">Domov › Vystúpenia & Workshopy › Workshopy</span>

            {{-- Badge --}}
            <div class="bg-[#22C55E]/10 border border-[#22C55E]/25 rounded-md px-4 py-2 w-fit">
                <span class="text-[#22C55E] text-[12px] font-bold tracking-[2px]">KALISTENICKÉ KURZY</span>
            </div>

            {{-- Title --}}
            <h1 class="font-display font-bold text-[48px] md:text-[72px] lg:text-[96px] leading-[0.95] tracking-wide text-white">
                PRAKTICKÉ<br>WORKSHOPY
            </h1>

            {{-- Description --}}
            <p class="text-bcz-light text-[18px] md:text-[20px] leading-[1.6] max-w-[700px]">
                Učíme základné aj pokročilé prvky kalisteniky — od bezpečného pádu až po kurz stojky. Prispôsobíme sa vašej úrovni.
            </p>
        </div>
    </section>

    {{-- About Section --}}
    <section class="bg-[#111111] py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col lg:flex-row items-center gap-16">
                {{-- Left --}}
                <div class="flex flex-col gap-6 flex-1">
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-0.5 bg-[#22C55E]"></div>
                        <span class="text-[#22C55E] text-[12px] font-bold tracking-[2px]">O WORKSHOPOCH</span>
                    </div>
                    <h2 class="font-display font-bold text-[40px] md:text-[56px] tracking-wide leading-tight text-white">
                        ČO VÁS NAUČÍME
                    </h2>
                    <p class="text-bcz-lighter text-[16px] leading-[1.7]">
                        Naše workshopy sú určené pre fitness centrá, trénerov, školy a podujatia. Učíme účastníkov základné aj pokročilé prvky kalisteniky — od správnej techniky po efektívne zostavenie tréningového plánu.
                    </p>
                    <p class="text-bcz-lighter text-[16px] leading-[1.7]">
                        Každý workshop je vedený certifikovaným trénerom s medzinárodnými skúsenosťami. Prispôsobíme obsah vašej úrovni — od úplných začiatočníkov až po pokročilých športovcov.
                    </p>
                </div>

                {{-- Right --}}
                <div class="w-[500px] shrink-0 hidden lg:block">
                    <div class="w-full h-[400px] rounded-2xl bg-[url('https://images.unsplash.com/photo-1579156411931-4ed01974dfb4?w=1080&q=80')] bg-cover bg-center"></div>
                </div>
            </div>
        </div>
    </section>

    {{-- Workshop Types Section --}}
    <section class="bg-bcz-dark py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col items-center gap-12">
                {{-- Header --}}
                <div class="flex flex-col items-center gap-4">
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-0.5 bg-[#22C55E]"></div>
                        <span class="text-[#22C55E] text-[12px] font-bold tracking-[2px]">TYPY</span>
                        <div class="w-10 h-0.5 bg-[#22C55E]"></div>
                    </div>
                    <h2 class="font-display font-bold text-[36px] md:text-[48px] tracking-wide text-white">Čo ponúkame</h2>
                    <p class="text-bcz-muted text-[18px] text-center max-w-[600px]">Vyberajte z našich špecializovaných workshopov podľa vašich potrieb a úrovne.</p>
                </div>

                {{-- Cards Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 w-full">
                    {{-- Card 1 --}}
                    <div class="bg-[#111111] border border-[#22C55E]/25 rounded-2xl p-6 flex flex-col gap-4">
                        <svg class="w-8 h-8 text-[#22C55E]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m18 7 4 2v11a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9l4-2"/><path d="M14 22v-4a2 2 0 0 0-2-2a2 2 0 0 0-2 2v4"/><path d="M18 22V5l-6-3-6 3v17"/><path d="M12 7v5"/><path d="M10 9h4"/></svg>
                        <h3 class="font-display font-bold text-[28px] tracking-wide text-white">Kurz Stojky</h3>
                        <p class="text-bcz-muted text-[14px] leading-[1.6]">Od základnej prípravy až po voľnú stojku. Naučíme vás správnu techniku, posilnenie jadra a progresiu krok za krokom.</p>
                    </div>

                    {{-- Card 2 --}}
                    <div class="bg-[#111111] border border-[#22C55E]/25 rounded-2xl p-6 flex flex-col gap-4">
                        <svg class="w-8 h-8 text-[#22C55E]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.5 6.5 12 2 6.5 6.5"/><path d="M2 12h4"/><path d="M18 12h4"/><path d="M12 18v4"/><path d="m2 22 4-4"/><path d="m18 22 4-4"/></svg>
                        <h3 class="font-display font-bold text-[28px] tracking-wide text-white">Základy Kalisteniky</h3>
                        <p class="text-bcz-muted text-[14px] leading-[1.6]">Zhyby, kliky, dipy a ich variácie. Správna forma, progresie a zostava tréningového plánu pre začiatočníkov aj mierne pokročilých.</p>
                    </div>

                    {{-- Card 3 --}}
                    <div class="bg-[#111111] border border-[#22C55E]/25 rounded-2xl p-6 flex flex-col gap-4">
                        <svg class="w-8 h-8 text-[#22C55E]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>
                        <h3 class="font-display font-bold text-[28px] tracking-wide text-white">Bezpečný Pád</h3>
                        <p class="text-bcz-muted text-[14px] leading-[1.6]">Techniky bezpečného pádu a základov parkour rolľov. Nevyhnutné pre každého, kto chce začať s kalistenikou, parkourom alebo freerunningom.</p>
                    </div>

                    {{-- Card 4 --}}
                    <div class="bg-[#111111] border border-[#22C55E]/25 rounded-2xl p-6 flex flex-col gap-4">
                        <svg class="w-8 h-8 text-[#22C55E]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>
                        <h3 class="font-display font-bold text-[28px] tracking-wide text-white">Pokročilé Prvky</h3>
                        <p class="text-bcz-muted text-[14px] leading-[1.6]">Muscle-up, front lever, planche a ďalšie. Pre tých, čo už ovládajú základy a chcú posunúť svoje zručnosti na vyššiu úroveň.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Process Section --}}
    <section class="bg-[#0D0D0D] py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col gap-12">
                {{-- Header --}}
                <div class="flex flex-col items-center gap-4">
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-0.5 bg-[#22C55E]"></div>
                        <span class="text-[#22C55E] text-[12px] font-bold tracking-[2px]">PROCES</span>
                        <div class="w-10 h-0.5 bg-[#22C55E]"></div>
                    </div>
                    <h2 class="font-display font-bold text-[36px] md:text-[48px] tracking-wide text-white">Priebeh workshopu</h2>
                </div>

                {{-- Steps Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-[#111111] rounded-2xl p-6 flex flex-col gap-4">
                        <span class="font-display font-bold text-[36px] text-[#22C55E]">01</span>
                        <h3 class="text-white font-bold text-[18px]">Úvod & Rozohriatie</h3>
                        <p class="text-bcz-muted text-[14px] leading-[1.6]">Zoznámenie s účastníkmi, stanovenie cieľov a dôkladné rozohriatie tela.</p>
                    </div>
                    <div class="bg-[#111111] rounded-2xl p-6 flex flex-col gap-4">
                        <span class="font-display font-bold text-[36px] text-[#22C55E]">02</span>
                        <h3 class="text-white font-bold text-[18px]">Technika & Progresie</h3>
                        <p class="text-bcz-muted text-[14px] leading-[1.6]">Detailný rozklad cvikov, správna forma a individuálne progresie pre každú úroveň.</p>
                    </div>
                    <div class="bg-[#111111] rounded-2xl p-6 flex flex-col gap-4">
                        <span class="font-display font-bold text-[36px] text-[#22C55E]">03</span>
                        <h3 class="text-white font-bold text-[18px]">Prax & Feedback</h3>
                        <p class="text-bcz-muted text-[14px] leading-[1.6]">Praktické precvičovanie s osobným feedbackom trénera a korekciami v reálnom čase.</p>
                    </div>
                    <div class="bg-[#111111] rounded-2xl p-6 flex flex-col gap-4">
                        <span class="font-display font-bold text-[36px] text-[#22C55E]">04</span>
                        <h3 class="text-white font-bold text-[18px]">Plán & Materiály</h3>
                        <p class="text-bcz-muted text-[14px] leading-[1.6]">Na záver dostanete tréningový plán a materiály na ďalšie samostatné cvičenie.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Stats Section --}}
    <section class="bg-[#111111] border-y border-[#1A1A1A] py-10">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-wrap justify-between items-center gap-8">
                <div class="flex flex-col items-center gap-1">
                    <span class="font-display font-bold text-[48px] text-[#22C55E]">80+</span>
                    <span class="text-bcz-muted text-[14px]">Workshopov</span>
                </div>
                <div class="flex flex-col items-center gap-1">
                    <span class="font-display font-bold text-[48px] text-[#22C55E]">2000+</span>
                    <span class="text-bcz-muted text-[14px]">Účastníkov</span>
                </div>
                <div class="flex flex-col items-center gap-1">
                    <span class="font-display font-bold text-[48px] text-[#22C55E]">15+</span>
                    <span class="text-bcz-muted text-[14px]">Krajín</span>
                </div>
                <div class="flex flex-col items-center gap-1">
                    <span class="font-display font-bold text-[48px] text-[#22C55E]">5</span>
                    <span class="text-bcz-muted text-[14px]">Typov workshopov</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Recent Events Section --}}
    <section class="bg-bcz-dark py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col gap-12">
                {{-- Header --}}
                <div class="flex items-end justify-between">
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center gap-2">
                            <div class="w-10 h-0.5 bg-[#22C55E]"></div>
                            <span class="text-[#22C55E] text-[12px] font-bold tracking-[2px]">PORTFÓLIO</span>
                        </div>
                        <h2 class="font-display font-bold text-[36px] md:text-[48px] tracking-wide text-white">Naše Workshopy</h2>
                    </div>
                    <a href="{{ route('archiv-podujati') }}" class="hidden md:flex border border-[#22C55E] rounded-lg px-6 py-3 text-[#22C55E] text-sm font-semibold hover:bg-[#22C55E] hover:text-white transition-colors items-center gap-2">
                        Zobraziť všetky <span>→</span>
                    </a>
                </div>

                {{-- Events Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {{-- Event 1 --}}
                    <div class="bg-[#111111] rounded-2xl overflow-hidden">
                        <div class="w-full h-[200px] bg-[url('https://images.unsplash.com/photo-1579156411931-4ed01974dfb4?w=1080&q=80')] bg-cover bg-center"></div>
                        <div class="flex flex-col gap-3 p-5">
                            <span class="bg-[#22C55E]/10 text-[#22C55E] text-[10px] font-bold px-2.5 py-1 rounded w-fit">STOJKA</span>
                            <h3 class="text-white text-[18px] font-bold">Handstand Camp Žilina</h3>
                            <p class="text-bcz-muted text-[13px] leading-[1.5]">Víkendový intenzívny kurz stojky pre všetky úrovne. 20 účastníkov, 2 dni, maximálny progres.</p>
                            <span class="text-bcz-dim text-[12px]">Február 2024 · Žilina</span>
                        </div>
                    </div>

                    {{-- Event 2 --}}
                    <div class="bg-[#111111] rounded-2xl overflow-hidden">
                        <div class="w-full h-[200px] bg-[url('https://images.unsplash.com/photo-1600033402709-fa12673e98dc?w=1080&q=80')] bg-cover bg-center"></div>
                        <div class="flex flex-col gap-3 p-5">
                            <span class="bg-[#22C55E]/10 text-[#22C55E] text-[10px] font-bold px-2.5 py-1 rounded w-fit">ZÁKLADY</span>
                            <h3 class="text-white text-[18px] font-bold">Calisthenics Basics — Bratislava</h3>
                            <p class="text-bcz-muted text-[13px] leading-[1.5]">Otvorený workshop základov kalisteniky v spolupráci s Fit Park Bratislava. 35 účastníkov.</p>
                            <span class="text-bcz-dim text-[12px]">Október 2023 · Bratislava</span>
                        </div>
                    </div>

                    {{-- Event 3 --}}
                    <div class="bg-[#111111] rounded-2xl overflow-hidden">
                        <div class="w-full h-[200px] bg-[url('https://images.unsplash.com/photo-1734668486909-4637ecd66408?w=1080&q=80')] bg-cover bg-center"></div>
                        <div class="flex flex-col gap-3 p-5">
                            <span class="bg-[#22C55E]/10 text-[#22C55E] text-[10px] font-bold px-2.5 py-1 rounded w-fit">POKROČILÉ</span>
                            <h3 class="text-white text-[18px] font-bold">Muscle-up Masterclass</h3>
                            <p class="text-bcz-muted text-[13px] leading-[1.5]">Pokročilý workshop zameraný na techniku muscle-upu. Od prípravných cvikov po čisté opakovanie.</p>
                            <span class="text-bcz-dim text-[12px]">Jún 2023 · Košice</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="bg-[#22C55E] py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col items-center gap-8">
                <h2 class="font-display font-bold text-[36px] md:text-[56px] tracking-wide text-white text-center">
                    OBJEDNAJTE SI WORKSHOP
                </h2>
                <p class="text-white/80 text-[18px] leading-[1.6] text-center max-w-[700px]">
                    Prispôsobíme workshop vašim potrebám — či už ide o školské podujatie, firemný teambulding alebo tréningový seminár. Kontaktujte nás a pripravíme vám ponuku na mieru.
                </p>
                <div class="flex flex-col sm:flex-row items-center gap-4">
                    <a href="{{ route('kontakt') }}" class="bg-white px-8 py-4 rounded-lg text-[#22C55E] font-bold text-[14px] hover:bg-white/90 transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                        KONTAKTUJTE NÁS
                    </a>
                    <a href="tel:+421900123456" class="border border-white px-8 py-4 rounded-lg text-white font-semibold text-[14px] hover:bg-white/10 transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        ZAVOLAJTE NÁM
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
