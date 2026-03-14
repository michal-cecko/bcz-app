@extends('layouts.public')

@section('title', 'FAQ | BCZ Club')

@section('content')
    {{-- Hero Section --}}
    <section class="bg-bcz-dark pt-[120px] pb-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col items-center gap-8">
        {{-- Badge --}}
        <span class="bg-bcz-red text-white text-xs font-bold px-3.5 py-1.5 rounded-md">POMOC & PODPORA</span>

        {{-- Title --}}
        <h1 class="font-display font-bold text-[28px] md:text-[40px] lg:text-[56px] tracking-wide text-center text-white">Často kladené otázky</h1>

        {{-- Description --}}
        <p class="text-[#888888] text-lg text-center max-w-[600px] leading-relaxed">
            Nájdite odpovede na najčastejšie otázky o našich tréningoch, vystúpeniach a workshopoch
        </p>

        {{-- Search Bar --}}
        <div class="max-w-[700px] w-full bg-[#111111] border border-[#333333] rounded-xl px-6 py-[18px] flex items-center gap-4">
            <span class="text-[#666666] text-lg">&#x1F50D;</span>
            <input type="text" placeholder="Hľadať v otázkach..." class="bg-transparent text-white placeholder-[#666666] w-full outline-none border-none">
        </div>

        {{-- Hint --}}
        <p class="text-[#555555] text-sm">Populárne: tréningy, ceny, vybavenie, lokácie</p>
        </div>
    </section>

    {{-- Category Navigation --}}
    <section class="bg-bcz-dark">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-wrap justify-center gap-3">
        <button class="bg-bcz-red text-white rounded-lg px-6 py-3 text-sm font-semibold">Všetky</button>
        <button class="bg-[#111111] border border-[#333333] text-[#CCCCCC] rounded-lg px-6 py-3 text-sm font-medium hover:border-[#555555] transition">Tréningy</button>
        <button class="bg-[#111111] border border-[#333333] text-[#CCCCCC] rounded-lg px-6 py-3 text-sm font-medium hover:border-[#555555] transition">Vystúpenia</button>
        <button class="bg-[#111111] border border-[#333333] text-[#CCCCCC] rounded-lg px-6 py-3 text-sm font-medium hover:border-[#555555] transition">Workshopy</button>
        <button class="bg-[#111111] border border-[#333333] text-[#CCCCCC] rounded-lg px-6 py-3 text-sm font-medium hover:border-[#555555] transition">Prednášky</button>
        <button class="bg-[#111111] border border-[#333333] text-[#CCCCCC] rounded-lg px-6 py-3 text-sm font-medium hover:border-[#555555] transition">Platby & Ceny</button>
        </div>
    </section>

    {{-- Main Content --}}
    <section class="bg-bcz-dark py-[60px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col gap-10 lg:gap-[60px]">

        {{-- Section 1: Tréningy --}}
        <div class="flex flex-col gap-6">
            {{-- Section Header --}}
            <div class="flex items-center gap-4">
                <div class="bg-[#FF2D2D20] rounded-xl w-12 h-12 flex items-center justify-center text-2xl">&#x1F3C3;</div>
                <h2 class="font-display font-bold text-[28px] tracking-wide text-white">Tréningy</h2>
                <span class="bg-[#222222] rounded-full px-3 py-1 text-[#888888] text-xs">6 otázok</span>
            </div>

            {{-- FAQ Items --}}
            <div class="flex flex-col gap-3">
                {{-- Expanded FAQ Item --}}
                <div class="rounded-xl bg-[#111111] border border-bcz-red p-6 cursor-pointer">
                    <div class="flex justify-between items-center">
                        <span class="text-white font-semibold">Ako prebiehajú vaše tréningy?</span>
                        <span class="text-bcz-red text-2xl">&minus;</span>
                    </div>
                    <p class="text-[#888888] text-[15px] leading-relaxed mt-4">
                        Naše tréningy sú rozdelené do niekoľkých úrovní podľa skúseností. Každý tréning začína rozcvičkou, pokračuje nácvikom základných techník a končí voľným tréningom. Tréningy vedú certifikovaní tréneri s dlhoročnými skúsenosťami v parkour a freerunning disciplínach.
                    </p>
                </div>

                {{-- Collapsed FAQ Items --}}
                <div class="rounded-xl bg-[#111111] border border-[#222222] p-6 flex justify-between items-center cursor-pointer hover:border-[#333333] transition">
                    <span class="text-white font-semibold">Potrebujem predchádzajúce skúsenosti?</span>
                    <span class="text-[#888888] text-2xl">+</span>
                </div>

                <div class="rounded-xl bg-[#111111] border border-[#222222] p-6 flex justify-between items-center cursor-pointer hover:border-[#333333] transition">
                    <span class="text-white font-semibold">Aké vybavenie potrebujem na tréning?</span>
                    <span class="text-[#888888] text-2xl">+</span>
                </div>
            </div>

            {{-- Show All Link --}}
            <div class="flex justify-center gap-2 pt-4">
                <a href="#" class="text-bcz-red text-sm font-semibold">Zobraziť všetkých 6 otázok &#x2193;</a>
            </div>
        </div>

        {{-- Section 2: Vystúpenia --}}
        <div class="flex flex-col gap-6">
            {{-- Section Header --}}
            <div class="flex items-center gap-4">
                <div class="bg-[#FF2D2D20] rounded-xl w-12 h-12 flex items-center justify-center text-2xl">&#x1F3AD;</div>
                <h2 class="font-display font-bold text-[28px] tracking-wide text-white">Vystúpenia</h2>
                <span class="bg-[#222222] rounded-full px-3 py-1 text-[#888888] text-xs">4 otázky</span>
            </div>

            {{-- FAQ Items --}}
            <div class="flex flex-col gap-3">
                <div class="rounded-xl bg-[#111111] border border-[#222222] p-6 flex justify-between items-center cursor-pointer hover:border-[#333333] transition">
                    <span class="text-white font-semibold">Aké typy vystúpení ponúkate?</span>
                    <span class="text-[#888888] text-2xl">+</span>
                </div>

                <div class="rounded-xl bg-[#111111] border border-[#222222] p-6 flex justify-between items-center cursor-pointer hover:border-[#333333] transition">
                    <span class="text-white font-semibold">Koľko stojí vystúpenie na našej akcii?</span>
                    <span class="text-[#888888] text-2xl">+</span>
                </div>
            </div>

            {{-- Show All Link --}}
            <div class="flex justify-center gap-2 pt-4">
                <a href="#" class="text-bcz-red text-sm font-semibold">Zobraziť všetky 4 otázky &#x2193;</a>
            </div>
        </div>

        {{-- Section 3: Workshopy --}}
        <div class="flex flex-col gap-6">
            {{-- Section Header --}}
            <div class="flex items-center gap-4">
                <div class="bg-[#22C55E20] rounded-xl w-12 h-12 flex items-center justify-center text-2xl">&#x1F3AF;</div>
                <h2 class="font-display font-bold text-[28px] tracking-wide text-white">Workshopy</h2>
                <span class="bg-[#222222] rounded-full px-3 py-1 text-[#888888] text-xs">4 otázky</span>
            </div>

            {{-- FAQ Items --}}
            <div class="flex flex-col gap-3">
                <div class="rounded-xl bg-[#111111] border border-[#222222] p-6 flex justify-between items-center cursor-pointer hover:border-[#333333] transition">
                    <span class="text-white font-semibold">Aké workshopy organizujete?</span>
                    <span class="text-[#888888] text-2xl">+</span>
                </div>

                <div class="rounded-xl bg-[#111111] border border-[#222222] p-6 flex justify-between items-center cursor-pointer hover:border-[#333333] transition">
                    <span class="text-white font-semibold">Môžete prísť s workshopom k nám do firmy?</span>
                    <span class="text-[#888888] text-2xl">+</span>
                </div>
            </div>

            {{-- Show All Link --}}
            <div class="flex justify-center gap-2 pt-4">
                <a href="#" class="text-[#22C55E] text-sm font-semibold">Zobraziť všetky 4 otázky &#x2193;</a>
            </div>
        </div>

        {{-- Section 4: Prednášky --}}
        <div class="flex flex-col gap-6">
            {{-- Section Header --}}
            <div class="flex items-center gap-4">
                <div class="bg-[#3B82F620] rounded-xl w-12 h-12 flex items-center justify-center text-2xl">&#x1F393;</div>
                <h2 class="font-display font-bold text-[28px] tracking-wide text-white">Prednášky</h2>
                <span class="bg-[#222222] rounded-full px-3 py-1 text-[#888888] text-xs">3 otázky</span>
            </div>

            {{-- FAQ Items --}}
            <div class="flex flex-col gap-3">
                <div class="rounded-xl bg-[#111111] border border-[#222222] p-6 flex justify-between items-center cursor-pointer hover:border-[#333333] transition">
                    <span class="text-white font-semibold">O čom sú vaše prednášky pre školy?</span>
                    <span class="text-[#888888] text-2xl">+</span>
                </div>

                <div class="rounded-xl bg-[#111111] border border-[#222222] p-6 flex justify-between items-center cursor-pointer hover:border-[#333333] transition">
                    <span class="text-white font-semibold">Pre aké vekové skupiny sú prednášky určené?</span>
                    <span class="text-[#888888] text-2xl">+</span>
                </div>
            </div>

            {{-- Show All Link --}}
            <div class="flex justify-center gap-2 pt-4">
                <a href="#" class="text-[#3B82F6] text-sm font-semibold">Zobraziť všetky 3 otázky &#x2193;</a>
            </div>
        </div>

        {{-- Section 5: Platby & Ceny --}}
        <div class="flex flex-col gap-6">
            {{-- Section Header --}}
            <div class="flex items-center gap-4">
                <div class="bg-[#F59E0B20] rounded-xl w-12 h-12 flex items-center justify-center text-2xl">&#x1F4B0;</div>
                <h2 class="font-display font-bold text-[28px] tracking-wide text-white">Platby & Ceny</h2>
                <span class="bg-[#222222] rounded-full px-3 py-1 text-[#888888] text-xs">5 otázok</span>
            </div>

            {{-- FAQ Items --}}
            <div class="flex flex-col gap-3">
                <div class="rounded-xl bg-[#111111] border border-[#222222] p-6 flex justify-between items-center cursor-pointer hover:border-[#333333] transition">
                    <span class="text-white font-semibold">Aké sú ceny za tréningy?</span>
                    <span class="text-[#888888] text-2xl">+</span>
                </div>

                <div class="rounded-xl bg-[#111111] border border-[#222222] p-6 flex justify-between items-center cursor-pointer hover:border-[#333333] transition">
                    <span class="text-white font-semibold">Ponúkate nejaké permanentky alebo balíčky?</span>
                    <span class="text-[#888888] text-2xl">+</span>
                </div>

                <div class="rounded-xl bg-[#111111] border border-[#222222] p-6 flex justify-between items-center cursor-pointer hover:border-[#333333] transition">
                    <span class="text-white font-semibold">Ako môžem platiť za tréningy?</span>
                    <span class="text-[#888888] text-2xl">+</span>
                </div>
            </div>
        </div>

        {{-- Section 6: Platby & Ceny (2) --}}
        <div class="flex flex-col gap-6">
            {{-- Section Header --}}
            <div class="flex items-center gap-4">
                <div class="bg-[#F59E0B20] rounded-xl w-12 h-12 flex items-center justify-center text-2xl">&#x1F4B3;</div>
                <h2 class="font-display font-bold text-[28px] tracking-wide text-white">Platby & Ceny</h2>
                <span class="bg-[#222222] rounded-full px-3 py-1 text-[#888888] text-xs">4 otázky</span>
            </div>

            {{-- FAQ Items --}}
            <div class="flex flex-col gap-3">
                <div class="rounded-xl bg-[#111111] border border-[#222222] p-6 flex justify-between items-center cursor-pointer hover:border-[#333333] transition">
                    <span class="text-white font-semibold">Aké sú ceny tréningov?</span>
                    <span class="text-[#888888] text-2xl">+</span>
                </div>

                <div class="rounded-xl bg-[#111111] border border-[#222222] p-6 flex justify-between items-center cursor-pointer hover:border-[#333333] transition">
                    <span class="text-white font-semibold">Aké platobné metódy akceptujete?</span>
                    <span class="text-[#888888] text-2xl">+</span>
                </div>
            </div>

            {{-- Show All Link --}}
            <div class="flex justify-center gap-2 pt-4">
                <a href="#" class="text-[#F59E0B] text-sm font-semibold">Zobraziť všetky 4 otázky &#x2193;</a>
            </div>
        </div>

        </div>
    </section>

    {{-- CTA Section --}}
    <section class="bg-[#111111] py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col items-center gap-8">
        <h2 class="font-display font-bold text-[24px] md:text-[36px] tracking-wide text-center text-white">Nenašli ste odpoveď?</h2>
        <p class="text-[#888888] text-center">Neváhajte nás kontaktovať. Radi vám odpovieme na všetky vaše otázky.</p>
        <div class="flex gap-4">
            <a href="#" class="bg-bcz-red text-white rounded-lg px-8 py-4 font-semibold">Kontaktovať nás</a>
            <a href="#" class="bg-[#0A0A0A] border border-[#333333] text-white rounded-lg px-8 py-4 font-semibold">Zavolať</a>
        </div>
        </div>
    </section>
@endsection
