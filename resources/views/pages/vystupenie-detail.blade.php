@extends('layouts.public')

@section('title', 'Grape Festival 2024 | BCZ Club')

@section('content')
    {{-- Hero Section --}}
    <section class="relative w-full h-[500px] overflow-hidden">
        <div class="absolute inset-0 bg-[#1A1A1A]"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-transparent to-[#0A0A0A]"></div>

        <div class="absolute bottom-0 left-0 right-0 pb-[60px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col gap-4">
            {{-- Breadcrumb --}}
            <span class="text-[#666666] text-[11px] tracking-[2px]">Domov / Vystúpenia / Detail</span>

            {{-- Badge --}}
            <span class="bg-bcz-red text-white text-xs font-bold px-3.5 py-1.5 rounded-md w-fit">VYSTÚPENIE</span>

            {{-- Title --}}
            <h1 class="font-display font-bold text-[28px] md:text-[40px] lg:text-[56px] tracking-wide text-white">Grape Festival 2024</h1>
        </div>
        </div>
    </section>

    {{-- Content Wrapper --}}
    <section class="py-[60px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col lg:flex-row gap-10 lg:gap-16">
        {{-- LEFT - Main Content --}}
        <div class="flex-1 flex flex-col gap-12">
            {{-- Video Section --}}
            <div class="flex flex-col gap-4">
                {{-- Label --}}
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-[12px] font-bold tracking-[2px]">VIDEO</span>
                </div>

                {{-- Video Placeholder --}}
                <div class="rounded-2xl bg-[#111111] h-[400px] relative flex items-center justify-center">
                    <button class="w-20 h-20 bg-bcz-red rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                    </button>
                </div>

                {{-- Caption --}}
                <p class="text-[#666666] text-sm italic">Zostrih z nášho vystúpenia na hlavnom pódiu Grape Festivalu 2024</p>
            </div>

            {{-- Text Block --}}
            <div class="flex flex-col gap-6">
                <h2 class="font-display font-bold text-[32px] tracking-wide text-white">O vystúpení</h2>
                <p class="text-[#CCCCCC] text-base leading-relaxed">
                    Grape Festival 2024 bol pre nás výnimočný zážitok. Ako headlineri akrobatickej show sme vystúpili na hlavnom pódiu pred viac ako 30 000 divákmi. Naša 20-minútová performance kombinovala prvky parkouru, freeruningu a akrobacie s hudobným sprievodom naživo.
                </p>
                <p class="text-[#CCCCCC] text-base leading-relaxed">
                    Spolupráca s organizátormi festivalu bola bezproblémová a profesionálna. Tím BCZ Club pripravil jedinečnú choreografiu špeciálne pre tento event, ktorá zahŕňala synchronizované salto, wall-flipy a ďalšie náročné prvky.
                </p>
            </div>

            {{-- Quote Block --}}
            <div class="rounded-xl bg-[#111111] p-8 border border-[#FF2D2D40] flex gap-5">
                <div class="w-1 bg-bcz-red rounded-sm self-stretch"></div>
                <div class="flex flex-col gap-4">
                    <p class="text-white text-lg leading-relaxed italic">
                        "BCZ Club dodali absolútne profesionálnu show, ktorá nadchla celé publikum. Ich energia a precíznosť bola neuveriteľná. Určite budeme spolupracovať aj v budúcnosti."
                    </p>
                    <span class="text-[#888888] text-sm">— Martin Kováč, produkčný manažér Grape Festival</span>
                </div>
            </div>

            {{-- List Block --}}
            <div class="flex flex-col gap-6">
                <h3 class="font-display font-bold text-[24px] tracking-wide text-white">Čo bolo súčasťou vystúpenia</h3>
                <div class="flex flex-col gap-3">
                    <div class="flex gap-3">
                        <div class="w-2 h-2 bg-bcz-red rounded-sm mt-2 shrink-0"></div>
                        <span class="text-[#CCCCCC] text-[15px] leading-relaxed">Synchronizované akrobatické prvky pre 5-členný tím</span>
                    </div>
                    <div class="flex gap-3">
                        <div class="w-2 h-2 bg-bcz-red rounded-sm mt-2 shrink-0"></div>
                        <span class="text-[#CCCCCC] text-[15px] leading-relaxed">Špeciálne navrhnuté rekvizity a konštrukcie na wall-flipy</span>
                    </div>
                    <div class="flex gap-3">
                        <div class="w-2 h-2 bg-bcz-red rounded-sm mt-2 shrink-0"></div>
                        <span class="text-[#CCCCCC] text-[15px] leading-relaxed">Interakcia s publikom počas celej show</span>
                    </div>
                    <div class="flex gap-3">
                        <div class="w-2 h-2 bg-bcz-red rounded-sm mt-2 shrink-0"></div>
                        <span class="text-[#CCCCCC] text-[15px] leading-relaxed">Profesionálne osvetlenie a pyrotechnika</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT - Sidebar --}}
        <div class="w-full lg:w-[320px] flex flex-col gap-6 shrink-0">
            {{-- Event Info Card --}}
            <div class="rounded-xl bg-[#111111] p-6 border border-[#222222] flex flex-col gap-5">
                <h3 class="text-white font-bold text-lg">Informácie o akcii</h3>
                <div class="h-px bg-[#222222]"></div>
                <div class="flex flex-col gap-4">
                    <div class="flex justify-between">
                        <span class="text-[#888888] text-sm">Typ</span>
                        <span class="text-bcz-red text-sm">Vystúpenie</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#888888] text-sm">Klient</span>
                        <span class="text-white text-sm">Grape Festival</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#888888] text-sm">Lokácia</span>
                        <span class="text-white text-sm">Piešťany, Slovensko</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#888888] text-sm">Dátum</span>
                        <span class="text-white text-sm">16. - 18. August 2024</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#888888] text-sm">Divákov</span>
                        <span class="text-white text-sm">30 000+</span>
                    </div>
                </div>
            </div>

            {{-- CTA Card --}}
            <div class="rounded-xl bg-[#FF2D2D10] p-6 border border-[#FF2D2D40] flex flex-col gap-4">
                <h3 class="text-white font-bold">Máte záujem o podobné vystúpenie?</h3>
                <p class="text-[#AAAAAA] text-sm leading-relaxed">Pripravíme vám ponuku na mieru pre váš event.</p>
                <a href="#" class="bg-bcz-red text-white rounded-lg h-11 w-full flex items-center justify-center gap-2 text-sm font-semibold hover:bg-bcz-red/90 transition-colors">
                    Kontaktovať →
                </a>
            </div>

            {{-- Share Card --}}
            <div class="rounded-xl bg-[#111111] p-6 border border-[#222222] flex flex-col gap-4">
                <h3 class="text-white font-bold">Zdieľať</h3>
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
                <h2 class="font-display font-bold text-[32px] tracking-wide text-white">Galéria</h2>
                <p class="text-[#888888] text-sm">12 fotografií</p>
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
            <div class="w-full sm:w-[300px] h-[400px] rounded-xl bg-[#1A1A1A] opacity-60 shrink-0"></div>
            <div class="w-full sm:w-[300px] h-[400px] rounded-xl bg-[#1A1A1A] opacity-60 shrink-0"></div>
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

    {{-- More Events Section --}}
    <section class="py-[60px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col gap-8">
        {{-- Header --}}
        <div class="flex items-end justify-between">
            <h2 class="font-display font-bold text-[32px] tracking-wide text-white">Ďalšie vystúpenia</h2>
            <a href="#" class="border border-bcz-red text-bcz-red rounded-lg px-6 py-3 text-sm font-semibold hover:bg-bcz-red hover:text-white transition-colors">
                Zobraziť všetky →
            </a>
        </div>

        {{-- 3-column Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- Event 1 --}}
            <div class="bg-[#111111] rounded-2xl overflow-hidden">
                <div class="w-full h-[200px] bg-[#1A1A1A]"></div>
                <div class="flex flex-col gap-3 p-5">
                    <span class="bg-bcz-red/20 text-bcz-red text-[10px] font-bold px-2.5 py-1 rounded w-fit">VYSTÚPENIE</span>
                    <h3 class="text-white text-[18px] font-bold">Gymnázium Metodova</h3>
                    <p class="text-[#888888] text-[13px]">Motivačná prednáška pre študentov o nastavení mysle.</p>
                    <span class="text-[#666666] text-[12px]">Október 2024 · Bratislava</span>
                </div>
            </div>

            {{-- Event 2 --}}
            <div class="bg-[#111111] rounded-2xl overflow-hidden">
                <div class="w-full h-[200px] bg-[#1A1A1A]"></div>
                <div class="flex flex-col gap-3 p-5">
                    <span class="bg-[#22C55E]/20 text-[#22C55E] text-[10px] font-bold px-2.5 py-1 rounded w-fit">WORKSHOP</span>
                    <h3 class="text-white text-[18px] font-bold">Fitness Factory BA</h3>
                    <p class="text-[#888888] text-[13px]">Kurz stojky pre členov fitness centra.</p>
                    <span class="text-[#666666] text-[12px]">November 2024 · Bratislava</span>
                </div>
            </div>

            {{-- Event 3 --}}
            <div class="bg-[#111111] rounded-2xl overflow-hidden">
                <div class="w-full h-[200px] bg-[#1A1A1A]"></div>
                <div class="flex flex-col gap-3 p-5">
                    <span class="bg-[#3B82F6]/20 text-[#3B82F6] text-[10px] font-bold px-2.5 py-1 rounded w-fit">PREDNÁŠKA</span>
                    <h3 class="text-white text-[18px] font-bold">TEDx Bratislava</h3>
                    <p class="text-[#888888] text-[13px]">Inšpiratívna prednáška o prekonávaní limitov.</p>
                    <span class="text-[#666666] text-[12px]">September 2024 · Bratislava</span>
                </div>
            </div>
        </div>
        </div>
    </section>
@endsection
