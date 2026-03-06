@extends('layouts.public')

@section('title', '2% z dane - BCZ Club')

@section('content')
    {{-- Hero Section --}}
    <section class="pt-[100px] pb-[60px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col items-center gap-6">
        {{-- Badge --}}
        <div class="rounded-full bg-bcz-red px-4 py-2 flex items-center gap-2">
            <span class="text-white text-sm font-bold">2%</span>
            <span class="text-white text-xs font-bold">Z DANE</span>
        </div>

        {{-- Title --}}
        <h1 class="font-display font-bold text-[28px] md:text-[40px] lg:text-[56px] tracking-wide text-center">Darujte nám 2% z dane</h1>

        {{-- Description --}}
        <p class="text-[#888888] text-lg text-center max-w-[750px] leading-relaxed">
            Ak sa vám páči naša činnosť a ciele, môžete nás podporiť darovaním 2% z vašej dane. Nestojí vás to nič navyše - tieto peniaze by inak išli štátu.
        </p>

        {{-- Highlight box --}}
        <div class="rounded-xl bg-[#22C55E20] border border-[#22C55E40] px-6 py-4 flex items-center gap-3">
            <span class="text-[#22C55E] text-2xl">🤝</span>
            <span class="text-[#22C55E] text-[15px] font-medium">Vaše 2% pomáhajú rozvíjať parkour, street workout a calisthenics komunitu na Slovensku</span>
        </div>
        </div>
    </section>

    {{-- Video Section --}}
    <section class="bg-[#0A0A0A] py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col items-center gap-6 lg:gap-10">
        {{-- Header --}}
        <div class="flex flex-col items-center gap-4">
            <h2 class="font-display font-bold text-[32px] tracking-wide">🎬 Spoznajte našu komunitu</h2>
            <p class="text-[#888888] text-center max-w-[600px] leading-relaxed">
                Pozrite si krátke video o tom, čo robíme a ako pomáhame mladým ľuďom rozvíjať sa cez pohyb
            </p>
        </div>

        {{-- Video placeholder --}}
        <div class="w-full max-w-[900px] aspect-video rounded-2xl bg-[#0D0D0D] border border-[#222222] flex flex-col items-center justify-center">
            <div class="w-20 h-20 bg-bcz-red rounded-full flex items-center justify-center text-white text-3xl">▶</div>
            <span class="text-[#333333] text-xs tracking-[2px] font-semibold mt-4">VIDEO PLACEHOLDER</span>
        </div>

        {{-- Checkpoints --}}
        <div class="flex flex-col lg:flex-row gap-8 lg:gap-10 pt-5">
            <div class="flex items-center gap-3">
                <span class="text-[#22C55E] text-xl font-bold">✓</span>
                <span class="text-[#CCCCCC] text-sm">Trénujeme deti, mládež aj dospelých</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-[#22C55E] text-xl font-bold">✓</span>
                <span class="text-[#CCCCCC] text-sm">Organizujeme súťaže a workshopy</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-[#22C55E] text-xl font-bold">✓</span>
                <span class="text-[#CCCCCC] text-sm">Budujeme silnú komunitu pohybu</span>
            </div>
        </div>
        </div>
    </section>

    {{-- Organization Details Section --}}
    <section class="bg-[#0D0D0D] py-[60px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col items-center gap-8">
        {{-- Title --}}
        <h2 class="font-display font-bold text-[32px] tracking-wide">Údaje organizácie</h2>
        <p class="text-[#888888] text-center">Tieto údaje potrebujete vyplniť do formulára alebo daňového priznania</p>

        {{-- Card --}}
        <div class="rounded-2xl bg-[#111111] border border-[#222222] p-8 max-w-[700px] w-full flex flex-col gap-4">
            {{-- Rows --}}
            <div class="flex justify-between py-4 border-b border-[#222222]">
                <span class="text-[#888888]">Obchodné meno (názov)</span>
                <span class="text-white">BCZ Club, občianske združenie</span>
            </div>
            <div class="flex justify-between py-4 border-b border-[#222222]">
                <span class="text-[#888888]">Sídlo</span>
                <span class="text-white">Palárikova 123, 022 01 Čadca</span>
            </div>
            <div class="flex justify-between py-4 border-b border-[#222222]">
                <span class="text-[#888888]">IČO</span>
                <span class="text-bcz-red">52 841 235</span>
            </div>
            <div class="flex justify-between py-4 border-b border-[#222222]">
                <span class="text-[#888888]">Právna forma</span>
                <span class="text-white">Občianske združenie</span>
            </div>
            <div class="flex justify-between py-4">
                <span class="text-[#888888]">Rok</span>
                <span class="text-white">2025</span>
            </div>

            {{-- Copy button --}}
            <button class="w-full bg-[#222222] rounded-lg py-3.5 text-center text-white text-sm font-semibold flex items-center justify-center gap-2">
                📋 Kopírovať všetky údaje
            </button>
        </div>
        </div>
    </section>

    {{-- How-To Section --}}
    <section class="py-[60px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col items-center gap-6 lg:gap-10">
        {{-- Title --}}
        <div class="flex flex-col items-center gap-4">
            <h2 class="font-display font-bold text-[32px] tracking-wide">Ako darovať 2% z dane?</h2>
            <p class="text-[#888888] text-center">Vyberte si postup podľa toho, či ste zamestnanec, SZČO alebo právnická osoba</p>
        </div>

        {{-- Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- Card 1 - Zamestnanci (Blue) --}}
            <div class="rounded-2xl bg-[#111111] p-8 border border-[#222222] flex flex-col gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-[#3B82F6] flex items-center justify-center text-white text-xl">💼</div>
                    <h3 class="text-white text-xl font-semibold">Zamestnanci</h3>
                </div>
                <p class="text-[#888888] text-sm">Ak vám zamestnávateľ robí ročné zúčtovanie dane</p>

                <div class="flex flex-col gap-4">
                    <div class="flex gap-3">
                        <div class="w-6 h-6 bg-[#3B82F6] rounded-xl flex items-center justify-center text-white text-xs font-bold shrink-0">1</div>
                        <span class="text-[#CCCCCC] text-sm leading-relaxed">Požiadajte zamestnávateľa o Potvrdenie o zaplatení dane</span>
                    </div>
                    <div class="flex gap-3">
                        <div class="w-6 h-6 bg-[#3B82F6] rounded-xl flex items-center justify-center text-white text-xs font-bold shrink-0">2</div>
                        <span class="text-[#CCCCCC] text-sm leading-relaxed">Vyplňte Vyhlásenie o poukázaní 2% dane</span>
                    </div>
                    <div class="flex gap-3">
                        <div class="w-6 h-6 bg-[#3B82F6] rounded-xl flex items-center justify-center text-white text-xs font-bold shrink-0">3</div>
                        <span class="text-[#CCCCCC] text-sm leading-relaxed">Obe tlačivá doručte na daňový úrad do 30. apríla</span>
                    </div>
                </div>

                <button class="bg-[#3B82F6] text-white rounded-lg py-3.5 w-full text-center font-semibold">Stiahnuť Vyhlásenie</button>
            </div>

            {{-- Card 2 - SZČO (Green) --}}
            <div class="rounded-2xl bg-[#111111] p-8 border border-[#222222] flex flex-col gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-[#22C55E] flex items-center justify-center text-white text-xl">📋</div>
                    <h3 class="text-white text-xl font-semibold">Fyzické osoby (SZČO)</h3>
                </div>
                <p class="text-[#888888] text-sm">Ak si podávate daňové priznanie sami</p>

                <div class="flex flex-col gap-4">
                    <div class="flex gap-3">
                        <div class="w-6 h-6 bg-[#22C55E] rounded-xl flex items-center justify-center text-white text-xs font-bold shrink-0">1</div>
                        <span class="text-[#CCCCCC] text-sm leading-relaxed">V daňovom priznaní (typ A alebo B) vyplňte oddiel na poukázanie 2%</span>
                    </div>
                    <div class="flex gap-3">
                        <div class="w-6 h-6 bg-[#22C55E] rounded-xl flex items-center justify-center text-white text-xs font-bold shrink-0">2</div>
                        <span class="text-[#CCCCCC] text-sm leading-relaxed">Uveďte naše IČO a názov organizácie</span>
                    </div>
                    <div class="flex gap-3">
                        <div class="w-6 h-6 bg-[#22C55E] rounded-xl flex items-center justify-center text-white text-xs font-bold shrink-0">3</div>
                        <span class="text-[#CCCCCC] text-sm leading-relaxed">Podajte daňové priznanie do 31. marca</span>
                    </div>
                </div>

                <button class="bg-[#22C55E] text-white rounded-lg py-3.5 w-full text-center font-semibold">Daňové priznanie typ A / B</button>
            </div>

            {{-- Card 3 - Právnické osoby (Purple) --}}
            <div class="rounded-2xl bg-[#111111] p-8 border border-[#222222] flex flex-col gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-[#8B5CF6] flex items-center justify-center text-white text-xl">🏢</div>
                    <h3 class="text-white text-xl font-semibold">Právnické osoby</h3>
                </div>
                <p class="text-[#888888] text-sm">Firmy a spoločnosti môžu darovať 1-2%</p>

                <div class="flex flex-col gap-4">
                    <div class="flex gap-3">
                        <div class="w-6 h-6 bg-[#8B5CF6] rounded-xl flex items-center justify-center text-white text-xs font-bold shrink-0">1</div>
                        <span class="text-[#CCCCCC] text-sm leading-relaxed">V daňovom priznaní právnickej osoby vyplňte príslušnú časť</span>
                    </div>
                    <div class="flex gap-3">
                        <div class="w-6 h-6 bg-[#8B5CF6] rounded-xl flex items-center justify-center text-white text-xs font-bold shrink-0">2</div>
                        <span class="text-[#CCCCCC] text-sm leading-relaxed">Môžete uviesť aj viacerých prijímateľov</span>
                    </div>
                    <div class="flex gap-3">
                        <div class="w-6 h-6 bg-[#8B5CF6] rounded-xl flex items-center justify-center text-white text-xs font-bold shrink-0">3</div>
                        <span class="text-[#CCCCCC] text-sm leading-relaxed">Termín podania: 31. marca (resp. v predĺženej lehote)</span>
                    </div>
                </div>

                <button class="bg-[#8B5CF6] text-white rounded-lg py-3.5 w-full text-center font-semibold">Daňové priznanie PO</button>
            </div>
        </div>
        </div>
    </section>

    {{-- FAQ Section --}}
    <section class="bg-[#0D0D0D] py-[60px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col gap-6 lg:gap-10">
        <h2 class="font-display font-bold text-[32px] tracking-wide">Často kladené otázky</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Column 1 --}}
            <div class="flex flex-col gap-6">
                <div class="rounded-xl bg-[#111111] border border-[#222222] p-6 flex flex-col gap-3">
                    <h3 class="text-white font-semibold">Koľko ma to bude stáť?</h3>
                    <p class="text-[#888888] text-sm leading-relaxed">Nič. Tieto 2% by ste aj tak zaplatili štátu ako daň. Rozhodujete len o tom, kam pôjdu.</p>
                </div>
                <div class="rounded-xl bg-[#111111] border border-[#222222] p-6 flex flex-col gap-3">
                    <h3 class="text-white font-semibold">Do kedy musím podať vyhlásenie?</h3>
                    <p class="text-[#888888] text-sm leading-relaxed">Zamestnanci do 30. apríla, SZČO a firmy do 31. marca (alebo v predĺženej lehote).</p>
                </div>
            </div>

            {{-- Column 2 --}}
            <div class="flex flex-col gap-6">
                <div class="rounded-xl bg-[#111111] border border-[#222222] p-6 flex flex-col gap-3">
                    <h3 class="text-white font-semibold">Ako zistím, či boli moje 2% poukázané?</h3>
                    <p class="text-[#888888] text-sm leading-relaxed">Daňový úrad vás informuje, ak o to požiadate v tlačive. My informácie o darcoch nedostávame.</p>
                </div>
                <div class="rounded-xl bg-[#111111] border border-[#222222] p-6 flex flex-col gap-3">
                    <h3 class="text-white font-semibold">Môžem darovať aj viac ako 2%?</h3>
                    <p class="text-[#888888] text-sm leading-relaxed">Áno, môžete nás podporiť aj priamym finančným darom na náš účet. Navštívte stránku Podporte nás.</p>
                </div>
            </div>
        </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col items-center gap-6">
        <div class="rounded-3xl bg-[#FF2D2D10] border border-[#FF2D2D30] px-5 md:px-10 lg:px-20 py-[60px] w-full flex flex-col items-center gap-6">
            {{-- Icon --}}
            <div class="w-20 h-20 bg-bcz-red rounded-[20px] flex items-center justify-center font-display font-bold text-[28px] text-white">2%</div>

            {{-- Title --}}
            <h2 class="font-display font-bold text-[24px] md:text-[36px] tracking-wide text-center">Ďakujeme za vašu podporu!</h2>

            {{-- Description --}}
            <p class="text-[#CCCCCC] text-center max-w-[600px] leading-relaxed">
                Každé 2% pomáhajú. Vďaka vám môžeme ďalej rozvíjať parkour, street workout a calisthenics komunitu na Slovensku.
            </p>

            {{-- Buttons --}}
            <div class="flex items-center gap-4">
                <a href="#" class="bg-bcz-red rounded-lg px-8 py-4 font-semibold text-white">Stiahnuť tlačivo</a>
                <a href="#" class="bg-[#111111] border border-[#333333] rounded-lg px-8 py-4 font-semibold text-white">Podporte nás</a>
            </div>
        </div>
        </div>
    </section>
@endsection
