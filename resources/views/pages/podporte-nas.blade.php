@extends('layouts.public')

@section('title', 'Podporte nás - BCZ Club')

@section('content')
    {{-- Hero Section --}}
    <section class="pt-[100px] pb-[60px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col items-center gap-6">
            {{-- Badge --}}
            <div class="rounded-full bg-[#FF2D2D20] px-4 py-2 flex items-center gap-2">
                <span>❤️</span>
                <span class="text-bcz-red text-xs font-bold">PODPORTE NÁS</span>
            </div>

            {{-- Title --}}
            <h1 class="font-display font-bold text-[28px] md:text-[40px] lg:text-[56px] tracking-wide text-center">Pomôžte nám rásť</h1>

            {{-- Description --}}
            <p class="text-[#888888] text-lg text-center max-w-[700px] leading-relaxed">
                Vaša podpora nám pomáha rozvíjať komunitu a poskytovať kvalitné tréningy pre všetkých. Každý dar má zmysel.
            </p>
        </div>
    </section>

    {{-- Main Content --}}
    <section class="py-[60px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col lg:flex-row gap-8 lg:gap-10">
            {{-- LEFT Column --}}
        <div class="flex-1 flex flex-col gap-6 lg:gap-10">
            {{-- Bank Transfer Card --}}
            <div class="rounded-2xl bg-[#111111] p-8 border border-[#222222] flex flex-col gap-6">
                {{-- Header --}}
                <div class="flex items-center gap-4">
                    <div class="rounded-xl bg-[#FF2D2D20] w-12 h-12 flex items-center justify-center">
                        <svg class="w-6 h-6 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                    <h2 class="font-display font-semibold text-[24px]">Bankový prevod</h2>
                </div>

                {{-- Bank Details --}}
                <div class="flex flex-col gap-4">
                    <div class="flex justify-between py-4 border-b border-[#222222]">
                        <span class="text-[#888888] text-sm">Názov organizácie</span>
                        <span class="text-white text-sm font-semibold">BCZ Club, občianske združenie</span>
                    </div>
                    <div class="flex justify-between py-4 border-b border-[#222222]">
                        <span class="text-[#888888] text-sm">IČO</span>
                        <span class="text-white text-sm font-semibold">52 841 235</span>
                    </div>
                    <div class="flex justify-between py-4 border-b border-[#222222]">
                        <span class="text-[#888888] text-sm">IBAN</span>
                        <span class="text-white text-sm font-semibold">SK89 0900 0000 0051 8742 6513</span>
                    </div>
                    <div class="flex justify-between py-4 border-b border-[#222222]">
                        <span class="text-[#888888] text-sm">SWIFT/BIC</span>
                        <span class="text-white text-sm font-semibold">GIBASKBX</span>
                    </div>
                    <div class="flex justify-between py-4 border-b border-[#222222]">
                        <span class="text-[#888888] text-sm">Banka</span>
                        <span class="text-white text-sm font-semibold">Slovenská sporiteľňa, a.s.</span>
                    </div>
                    <div class="flex justify-between py-4">
                        <span class="text-[#888888] text-sm">Variabilný symbol</span>
                        <span class="text-white text-sm font-semibold">{{ date('Y') }} (aktuálny rok)</span>
                    </div>
                </div>

                {{-- QR Section --}}
                <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-6 pt-6">
                    <div class="w-[140px] h-[140px] bg-white rounded-xl flex items-center justify-center shrink-0">
                        <span class="text-[#0A0A0A] text-2xl font-bold">QR</span>
                    </div>
                    <div class="flex flex-col gap-3">
                        <h3 class="text-white font-semibold">Naskenujte QR kód</h3>
                        <p class="text-[#888888] text-sm leading-relaxed">
                            Použite mobilnú aplikáciu vašej banky na rýchlu platbu. QR kód obsahuje všetky potrebné údaje.
                        </p>
                        <button class="bg-[#222222] rounded-lg px-4 py-2.5 text-white text-sm w-fit">
                            Kopírovať IBAN
                        </button>
                    </div>
                </div>
            </div>

            {{-- Usage Card --}}
            <div class="rounded-2xl bg-[#111111] p-8 border border-[#222222] flex flex-col gap-6">
                {{-- Header --}}
                <div class="flex items-center gap-4">
                    <div class="rounded-xl bg-[#22C55E20] w-12 h-12 flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#22C55E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h2 class="font-display font-semibold text-[24px]">Na čo využívame dary</h2>
                </div>

                {{-- Description --}}
                <p class="text-[#888888] text-[15px] leading-relaxed">
                    Všetky získané prostriedky využívame transparentne na rozvoj našej komunity a zlepšovanie tréningových podmienok.
                </p>

                {{-- Items --}}
                <div class="flex flex-col gap-4">
                    {{-- Item 1 --}}
                    <div class="flex gap-4">
                        <div class="bg-[#FF2D2D20] w-8 h-8 rounded-lg flex items-center justify-center shrink-0">
                            <span>🏋️</span>
                        </div>
                        <div>
                            <h4 class="text-white font-semibold">Cvičebné pomôcky</h4>
                            <p class="text-[#888888] text-sm leading-relaxed">Nákup nových podložiek, odporových gúm, švihadiel a ďalšieho vybavenia pre tréningy.</p>
                        </div>
                    </div>

                    {{-- Item 2 --}}
                    <div class="flex gap-4">
                        <div class="bg-[#3B82F620] w-8 h-8 rounded-lg flex items-center justify-center shrink-0">
                            <span>💪</span>
                        </div>
                        <div>
                            <h4 class="text-white font-semibold">Hrazdy a bradlá</h4>
                            <p class="text-[#888888] text-sm leading-relaxed">Inštalácia a údržba street workout prvkov v Čadci a okolí.</p>
                        </div>
                    </div>

                    {{-- Item 3 --}}
                    <div class="flex gap-4">
                        <div class="bg-[#8B5CF620] w-8 h-8 rounded-lg flex items-center justify-center shrink-0">
                            <span>🛡️</span>
                        </div>
                        <div>
                            <h4 class="text-white font-semibold">Bezpečnostné vybavenie</h4>
                            <p class="text-[#888888] text-sm leading-relaxed">Crash pady, žinenky a ochranné pomôcky pre bezpečný tréning akrobacie.</p>
                        </div>
                    </div>

                    {{-- Item 4 --}}
                    <div class="flex gap-4">
                        <div class="bg-[#F59E0B20] w-8 h-8 rounded-lg flex items-center justify-center shrink-0">
                            <span>📅</span>
                        </div>
                        <div>
                            <h4 class="text-white font-semibold">Workshopy a podujatia</h4>
                            <p class="text-[#888888] text-sm leading-relaxed">Organizácia bezplatných workshopov a podujatí pre verejnosť.</p>
                        </div>
                    </div>

                    {{-- Item 5 --}}
                    <div class="flex gap-4">
                        <div class="bg-[#22C55E20] w-8 h-8 rounded-lg flex items-center justify-center shrink-0">
                            <span>👥</span>
                        </div>
                        <div>
                            <h4 class="text-white font-semibold">Rozvoj komunity</h4>
                            <p class="text-[#888888] text-sm leading-relaxed">Prenájom priestorov, cestovné náklady trénerov a propagácia aktivít.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT Column --}}
        <div class="w-full lg:w-[400px] flex flex-col gap-6">
            {{-- Tax Card --}}
            <div class="rounded-2xl bg-[#FF2D2D10] p-8 border border-[#FF2D2D40] flex flex-col gap-5">
                {{-- Header --}}
                <div class="flex items-center gap-4">
                    <div class="rounded-xl bg-[#FF2D2D20] w-12 h-12 flex items-center justify-center">
                        <span class="text-bcz-red text-sm font-bold">2%</span>
                    </div>
                    <h2 class="text-white text-xl font-semibold">Darujte nám 2% z dane</h2>
                </div>

                {{-- Description --}}
                <p class="text-[#CCCCCC] text-sm leading-relaxed">
                    Darovaním 2% z dane nám pomôžete bez toho, aby vás to stálo čokoľvek navyše. Tieto prostriedky idú priamo na rozvoj našich aktivít.
                </p>

                {{-- Button --}}
                <a href="#" class="bg-bcz-red text-white rounded-lg py-3.5 w-full text-center font-semibold flex items-center justify-center gap-2">
                    <span>→</span>
                    <span>Zistiť viac o 2% z dane</span>
                </a>
            </div>

            {{-- Contact Card --}}
            <div class="rounded-2xl bg-[#111111] p-8 border border-[#222222] flex flex-col gap-5">
                {{-- Header --}}
                <div class="flex items-center gap-4">
                    <div class="rounded-xl bg-[#3B82F620] w-12 h-12 flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#3B82F6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h2 class="text-white text-xl font-semibold">Kontaktujte nás</h2>
                </div>

                {{-- Description --}}
                <p class="text-[#888888] text-sm leading-relaxed">
                    Máte otázky ohľadom darovania alebo spolupráce? Neváhajte nás kontaktovať.
                </p>

                {{-- Contact Items --}}
                <div class="flex flex-col gap-3">
                    <div class="flex items-center gap-3">
                        <span class="text-[#888888]">✉</span>
                        <span class="text-white text-sm">podpora@bczclub.sk</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-[#888888]">📞</span>
                        <span class="text-white text-sm">+421 907 123 456</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-[#888888]">📍</span>
                        <span class="text-white text-sm">Palárikova 123, 022 01 Čadca</span>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>

    {{-- Transparency Section --}}
    <section class="bg-[#0D0D0D] py-[60px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col items-center gap-8">
            {{-- Badge --}}
        <div class="rounded-full bg-[#22C55E20] px-4 py-2 flex items-center gap-2">
            <span class="text-[#22C55E]">✓</span>
            <span class="text-[#22C55E] text-xs font-bold">TRANSPARENTNOSŤ</span>
        </div>

        {{-- Title --}}
        <h2 class="font-display font-bold text-[32px] tracking-wide text-center">Zaväzujeme sa k transparentnosti</h2>

        {{-- Description --}}
        <p class="text-[#888888] text-center max-w-[700px] leading-relaxed">
            Každý rok zverejňujeme výročnú správu o hospodárení, kde nájdete podrobný prehľad o využití všetkých finančných prostriedkov.
        </p>

        {{-- Stats --}}
        <div class="flex flex-wrap justify-center gap-4 sm:gap-6">
            <div class="rounded-xl bg-[#111111] border border-[#222222] p-6 w-full sm:w-[200px] flex flex-col items-center gap-2">
                <span class="font-display font-bold text-[36px] text-[#22C55E]">100%</span>
                <span class="text-[#888888] text-sm text-center">Využité na rozvoj</span>
            </div>
            <div class="rounded-xl bg-[#111111] border border-[#222222] p-6 w-full sm:w-[200px] flex flex-col items-center gap-2">
                <span class="font-display font-bold text-[36px] text-bcz-red">0%</span>
                <span class="text-[#888888] text-sm text-center">Administratívne náklady</span>
            </div>
            <div class="rounded-xl bg-[#111111] border border-[#222222] p-6 w-full sm:w-[200px] flex flex-col items-center gap-2">
                <span class="font-display font-bold text-[36px] text-[#3B82F6]">3+</span>
                <span class="text-[#888888] text-sm text-center">Roky transparentnosti</span>
            </div>
        </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col items-center gap-8">
            {{-- Title --}}
            <h2 class="font-display font-bold text-[24px] md:text-[36px] lg:text-[48px] tracking-wide text-center">Každý dar má zmysel</h2>

            {{-- Description --}}
            <p class="text-[#888888] text-lg text-center">
                Aj malá suma pomáha. Ďakujeme, že ste súčasťou našej komunity.
            </p>

            {{-- Buttons --}}
            <div class="flex items-center gap-4">
                <a href="#" class="bg-bcz-red text-white rounded-lg px-8 py-4 font-semibold">Darovať teraz</a>
                <a href="#" class="bg-[#111111] border border-[#333333] text-white rounded-lg px-8 py-4 font-semibold">Kontaktovať nás</a>
            </div>
        </div>
    </section>
@endsection
