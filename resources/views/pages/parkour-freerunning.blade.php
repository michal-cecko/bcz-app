@extends('layouts.public')

@section('title', 'Parkour &amp; Freerunning | BCZ Club')

@section('content')
    {{-- Hero Section --}}
    <section class="relative w-full h-[600px] overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1617588927728-5444370992a0?w=1440&q=80')] bg-cover bg-center"></div>
        <div class="absolute inset-0" style="background: linear-gradient(180deg, #0A0A0A 0%, #0A0A0A80 30%, #0A0A0A80 70%, #0A0A0A 100%)"></div>

        <div class="relative w-full h-full flex flex-col items-center justify-center">
            <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col items-center justify-center gap-6 pt-[120px]">
                {{-- Breadcrumb --}}
                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" class="text-bcz-muted text-[11px] font-medium tracking-widest hover:text-white transition-colors">DOMOV</a>
                    <span class="text-[#444444] text-[11px]">/</span>
                    <span class="text-bcz-muted text-[11px] font-medium tracking-widest">KATEGÓRIE</span>
                    <span class="text-[#444444] text-[11px]">/</span>
                    <span class="text-bcz-red text-[11px] font-medium tracking-widest">PARKOUR</span>
                </div>

                {{-- Badge --}}
                <span class="bg-bcz-red/20 text-bcz-red text-xs font-bold tracking-widest rounded-full px-4 py-2">KATEGÓRIA</span>

                {{-- Title --}}
                <h1 class="font-display font-bold text-[32px] md:text-[48px] lg:text-[64px] leading-none tracking-wide text-center">PARKOUR &amp; FREERUNNING</h1>

                {{-- Subtitle --}}
                <p class="text-[#CCCCCC] text-[24px] text-center">Umenie pohybu. Sloboda bez hraníc.</p>
            </div>
        </div>
    </section>

    {{-- About Section --}}
    <section class="bg-bcz-dark py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col gap-12">
                {{-- Label --}}
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                        <span class="text-bcz-red text-xs font-bold tracking-[3px]">O ŠPORTE</span>
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                    </div>
                    <h2 class="font-display font-bold text-[24px] md:text-[32px] lg:text-[42px] leading-none tracking-wide">ČO JE PARKOUR?</h2>
                </div>

                {{-- Two columns --}}
                <div class="flex flex-col lg:flex-row lg:items-start gap-10 lg:gap-20">
                    {{-- Left --}}
                    <div class="flex-1 flex flex-col gap-6">
                        <p class="text-[#CCCCCC] text-lg leading-[1.7]">
                            Parkour je disciplína, ktorá mení spôsob, akým vnímaš svet okolo seba. Každá stena, zábradlie či lavička sa stáva príležitosťou. Každá prekážka výzvou, ktorú môžeš prekonať.
                        </p>

                        <p class="text-[#888888] text-lg leading-[1.7]">
                            Vznikol vo Francúzsku v 80. rokoch a od vtedy sa rozšíril po celom svete. Nie je to len šport - je to filozofia efektívneho pohybu, kde sa učíš prekonávať fyzické aj mentálne bariéry.
                        </p>

                        <blockquote class="border-l-[3px] border-bcz-red pl-6">
                            <p class="text-bcz-red italic text-lg">&quot;Byť silný, aby si bol užitočný.&quot; - David Belle, zakladateľ Parkouru</p>
                        </blockquote>
                    </div>

                    {{-- Right --}}
                    <div class="w-full lg:w-[500px] shrink-0">
                        <img src="https://images.unsplash.com/photo-1721766853378-e3138eca0602?w=600&q=80" alt="Parkour" class="w-full h-[400px] object-cover">
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Freedom Section --}}
    <section class="bg-[#111111] py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col gap-12">
                {{-- Header --}}
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                        <span class="text-bcz-red text-xs font-bold tracking-[3px]">FILOZOFIA</span>
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                    </div>
                    <h2 class="font-display font-bold text-[24px] md:text-[32px] lg:text-[42px] leading-none tracking-wide">SLOBODA V POHYBE</h2>
                    <p class="text-[#888888] text-lg max-w-[700px]">Parkour ti dáva niečo, čo žiadny iný šport nemôže - absolútnu slobodu pohybu</p>
                </div>

                {{-- 4-column grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Card 1: Wind --}}
                <div class="bg-bcz-dark border border-[#222222] rounded-2xl p-8 flex flex-col gap-5">
                    <div class="w-16 h-16 bg-bcz-red/20 rounded-xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-bcz-red" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456z"/>
                        </svg>
                    </div>
                    <h3 class="text-white text-xl font-bold">Bez pravidiel</h3>
                    <p class="text-[#888888] text-sm leading-relaxed">Žiadne ihriská, žiadne vymedzené zóny. Celé mesto je tvoje ihrisko. Trénovať môžeš kdekoľvek a kedykoľvek.</p>
                </div>

                {{-- Card 2: Brain --}}
                <div class="bg-bcz-dark border border-[#222222] rounded-2xl p-8 flex flex-col gap-5">
                    <div class="w-16 h-16 bg-bcz-red/20 rounded-xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-bcz-red" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.383a14.406 14.406 0 01-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 10-7.517 0c.85.493 1.509 1.333 1.509 2.316V18"/>
                        </svg>
                    </div>
                    <h3 class="text-white text-xl font-bold">Mentálna sila</h3>
                    <p class="text-[#888888] text-sm leading-relaxed">Prekonávaš nielen fyzické prekážky, ale aj strach. Učíš sa dôverovať svojmu telu a rozhodovať pod tlakom.</p>
                </div>

                {{-- Card 3: Heart Pulse --}}
                <div class="bg-bcz-dark border border-[#222222] rounded-2xl p-8 flex flex-col gap-5">
                    <div class="w-16 h-16 bg-bcz-red/20 rounded-xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-bcz-red" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                        </svg>
                    </div>
                    <h3 class="text-white text-xl font-bold">Fyzická kondícia</h3>
                    <p class="text-[#888888] text-sm leading-relaxed">Komplexný tréning celého tela. Sila, vytrvalosť, flexibilita a koordinácia - všetko v jednom športe.</p>
                </div>

                {{-- Card 4: Users --}}
                <div class="bg-bcz-dark border border-[#222222] rounded-2xl p-8 flex flex-col gap-5">
                    <div class="w-16 h-16 bg-bcz-red/20 rounded-xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-bcz-red" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-white text-xl font-bold">Komunita</h3>
                    <p class="text-[#888888] text-sm leading-relaxed">Parkour spája ľudí z celého sveta. Zdieľaš progres, motivúješ sa navzájom a vytváraš priateľstvá na celý život.</p>
                </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Why Section --}}
    <section class="bg-bcz-dark py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col lg:flex-row lg:items-start gap-10 lg:gap-20">
            {{-- Left --}}
            <div class="flex-1 flex flex-col gap-8">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[3px]">PREČO ZAČAŤ</span>
                </div>

                <h2 class="font-display font-bold text-[22px] md:text-[28px] lg:text-[36px] leading-tight tracking-wide">ZMENÍ TO TVÔJ POHĽAD NA SVET</h2>

                <p class="text-[#888888] text-lg leading-[1.7]">
                    Keď začneš trénovať parkour, už nikdy nepôjdeš po ulici rovnako. Každá stena, lavička či zábradlie sa stane potenciálnym tréningovým nástrojom. Svet sa ti otvorí úplne novým spôsobom.
                </p>

                <div class="flex flex-col gap-4">
                    @php
                        $checklistItems = [
                            'Zlepšíš si koordináciu a priestorovú orientáciu',
                            'Naučíš sa prekonávať strach a budovať sebadôveru',
                            'Získaš funkčnú silu použiteľnú v reálnom živote',
                            'Staneš sa súčasťou skvelej komunity',
                            'Trénovať môžeš kdekoľvek - nepotrebuješ posiľňovňu',
                        ];
                    @endphp

                    @foreach ($checklistItems as $item)
                        <div class="flex items-center gap-4">
                            <div class="w-6 h-6 rounded-full bg-bcz-red flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                </svg>
                            </div>
                            <span class="text-white text-[15px]">{!! $item !!}</span>
                        </div>
                    @endforeach
                </div>
            </div>

                {{-- Right --}}
                <div class="w-full lg:w-[500px] shrink-0">
                    <img src="https://images.unsplash.com/photo-1562745486-01e13bd17534?w=600&q=80" alt="Parkour tréning" class="w-full h-[450px] object-cover">
                </div>
            </div>
        </div>
    </section>

    {{-- Skills Section --}}
    <section class="bg-bcz-dark py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col gap-12">
                {{-- Header --}}
            <div class="flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[3px]">PRVKY &amp; TRIKY</span>
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                </div>
                <h2 class="font-display font-bold text-[24px] md:text-[32px] lg:text-[42px] leading-none tracking-wide">ČO SA NAUČÍŠ</h2>
                <p class="text-[#888888] text-lg">Od základných pohybov až po akrobatické triky - parkour ponúka nekonečné možnosti progresie.</p>
            </div>

            {{-- ZÁKLADY --}}
            <div class="flex flex-col gap-6">
                <div class="flex items-center gap-4">
                    <span class="bg-[#22C55E]/20 text-[#22C55E] text-xs font-bold tracking-widest rounded-full px-4 py-1.5">ZÁKLADY</span>
                    <span class="text-white text-lg font-semibold">Základné pohyby</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    {{-- Safety Roll --}}
                    <div class="overflow-hidden rounded-xl bg-[#111111] border border-[#22C55E40]">
                        <img src="https://images.unsplash.com/photo-1616431627899?w=800&q=80" alt="Safety Roll" class="w-full h-[140px] object-cover">
                        <div class="p-4 flex flex-col gap-2">
                            <h4 class="text-white font-bold">Safety Roll (Kotúľ)</h4>
                            <p class="text-[#888888] text-sm leading-relaxed">Bezpečný pád a kotúľ. Najdôležitejší prvok pre prevenciu zranení.</p>
                        </div>
                    </div>

                    {{-- Precision Jump --}}
                    <div class="overflow-hidden rounded-xl bg-[#111111] border border-[#22C55E40]">
                        <img src="https://images.unsplash.com/photo-1563387061517?w=800&q=80" alt="Precision Jump" class="w-full h-[140px] object-cover">
                        <div class="p-4 flex flex-col gap-2">
                            <h4 class="text-white font-bold">Precision Jump</h4>
                            <p class="text-[#888888] text-sm leading-relaxed">Presný skok a doskok na určené miesto. Základ pre všetky skoky.</p>
                        </div>
                    </div>

                    {{-- Cat Leap --}}
                    <div class="overflow-hidden rounded-xl bg-[#111111] border border-[#22C55E40]">
                        <img src="https://images.unsplash.com/photo-1649443625001?w=800&q=80" alt="Cat Leap" class="w-full h-[140px] object-cover">
                        <div class="p-4 flex flex-col gap-2">
                            <h4 class="text-white font-bold">Cat Leap (Arm Jump)</h4>
                            <p class="text-[#888888] text-sm leading-relaxed">Skok a chytenie hrany rukami. Základný pohyb pre zdolávanie stien.</p>
                        </div>
                    </div>

                    {{-- Balance --}}
                    <div class="overflow-hidden rounded-xl bg-[#111111] border border-[#22C55E40]">
                        <div class="w-full h-[140px] bg-[#1A1A1A]"></div>
                        <div class="p-4 flex flex-col gap-2">
                            <h4 class="text-white font-bold">Balance (Rovnováha)</h4>
                            <p class="text-[#888888] text-sm leading-relaxed">Chôdza a beh po úzkych plochách. Základná schopnosť každého traceura.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STREDNÉ --}}
            <div class="flex flex-col gap-6">
                <div class="flex items-center gap-4">
                    <span class="bg-[#F59E0B]/20 text-[#F59E0B] text-xs font-bold tracking-widest rounded-full px-4 py-1.5">STREDNÉ</span>
                    <span class="text-white text-lg font-semibold">Vaults &amp; Preskoky</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    {{-- Speed Vault --}}
                    <div class="overflow-hidden rounded-xl bg-[#111111] border border-[#F59E0B40]">
                        <img src="https://images.unsplash.com/photo-1631437341208?w=800&q=80" alt="Speed Vault" class="w-full h-[140px] object-cover">
                        <div class="p-4 flex flex-col gap-2">
                            <h4 class="text-white font-bold">Speed Vault</h4>
                            <p class="text-[#888888] text-sm leading-relaxed">Rýchly preskok jednou rukou. Ideálny pre nízke prekážky pri behu.</p>
                        </div>
                    </div>

                    {{-- Kong Vault --}}
                    <div class="overflow-hidden rounded-xl bg-[#111111] border border-[#F59E0B40]">
                        <img src="https://images.unsplash.com/photo-1586861814230?w=800&q=80" alt="Kong Vault" class="w-full h-[140px] object-cover">
                        <div class="p-4 flex flex-col gap-2">
                            <h4 class="text-white font-bold">Kong Vault</h4>
                            <p class="text-[#888888] text-sm leading-relaxed">Preskok s oboma rukami vpredu. Jeden z najužitočnejších vaultov.</p>
                        </div>
                    </div>

                    {{-- Dash Vault --}}
                    <div class="overflow-hidden rounded-xl bg-[#111111] border border-[#F59E0B40]">
                        <img src="https://images.unsplash.com/photo-1559890663?w=800&q=80" alt="Dash Vault" class="w-full h-[140px] object-cover">
                        <div class="p-4 flex flex-col gap-2">
                            <h4 class="text-white font-bold">Dash Vault</h4>
                            <p class="text-[#888888] text-sm leading-relaxed">Preskok nohami vpredu. Bezpečná technika pre neznáme prekážky.</p>
                        </div>
                    </div>

                    {{-- Wall Run --}}
                    <div class="overflow-hidden rounded-xl bg-[#111111] border border-[#F59E0B40]">
                        <img src="https://images.unsplash.com/photo-1602248349525?w=800&q=80" alt="Wall Run" class="w-full h-[140px] object-cover">
                        <div class="p-4 flex flex-col gap-2">
                            <h4 class="text-white font-bold">Wall Run</h4>
                            <p class="text-[#888888] text-sm leading-relaxed">Beh po stene. Získaj výšku pomocou momentu z behu.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- POKROČILÉ --}}
            <div class="flex flex-col gap-6">
                <div class="flex items-center gap-4">
                    <span class="bg-[#EF4444]/20 text-[#EF4444] text-xs font-bold tracking-widest rounded-full px-4 py-1.5">POKROČILÉ</span>
                    <span class="text-white text-lg font-semibold">Freerunning &amp; Flips</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    {{-- Front Flip --}}
                    <div class="overflow-hidden rounded-xl bg-[#111111] border border-[#EF444440]">
                        <img src="https://images.unsplash.com/photo-1631837251165?w=800&q=80" alt="Front Flip" class="w-full h-[140px] object-cover">
                        <div class="p-4 flex flex-col gap-2">
                            <h4 class="text-white font-bold">Front Flip</h4>
                            <p class="text-[#888888] text-sm leading-relaxed">Salto vpred. Základný akrobatický prvok freerunning-u.</p>
                        </div>
                    </div>

                    {{-- Backflip --}}
                    <div class="overflow-hidden rounded-xl bg-[#111111] border border-[#EF444440]">
                        <img src="https://images.unsplash.com/photo-1761070831434?w=800&q=80" alt="Backflip" class="w-full h-[140px] object-cover">
                        <div class="p-4 flex flex-col gap-2">
                            <h4 class="text-white font-bold">Backflip</h4>
                            <p class="text-[#888888] text-sm leading-relaxed">Salto vzad. Ikonický trik vyžadujúci odvahu a techniku.</p>
                        </div>
                    </div>

                    {{-- Sideflip --}}
                    <div class="overflow-hidden rounded-xl bg-[#111111] border border-[#EF444440]">
                        <img src="https://images.unsplash.com/photo-1753018452010?w=800&q=80" alt="Sideflip" class="w-full h-[140px] object-cover">
                        <div class="p-4 flex flex-col gap-2">
                            <h4 class="text-white font-bold">Sideflip</h4>
                            <p class="text-[#888888] text-sm leading-relaxed">Bočné salto. Efektný trik pre rôzne situácie v teréne.</p>
                        </div>
                    </div>

                    {{-- Webster --}}
                    <div class="overflow-hidden rounded-xl bg-[#111111] border border-[#EF444440]">
                        <img src="https://images.unsplash.com/photo-1659303387110?w=800&q=80" alt="Webster" class="w-full h-[140px] object-cover">
                        <div class="p-4 flex flex-col gap-2">
                            <h4 class="text-white font-bold">Webster</h4>
                            <p class="text-[#888888] text-sm leading-relaxed">Front flip z jednej nohy. Elegantný pohyb z behu.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- EXPERT --}}
            <div class="flex flex-col gap-6">
                <div class="flex items-center gap-4">
                    <span class="bg-[#8B5CF6]/20 text-[#8B5CF6] text-xs font-bold tracking-widest rounded-full px-4 py-1.5">EXPERT</span>
                    <div class="flex-1 h-px bg-[#222222]"></div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    {{-- Gainer --}}
                    <div class="overflow-hidden rounded-xl bg-[#111111] border border-[#8B5CF640]">
                        <img src="https://images.unsplash.com/photo-1762341581971?w=800&q=80" alt="Gainer" class="w-full h-[140px] object-cover">
                        <div class="p-4 flex flex-col gap-2">
                            <h4 class="text-white font-bold">Gainer</h4>
                            <p class="text-[#888888] text-sm leading-relaxed">Salto vzad s rozbehom vpred. Kombinácia behu a backflipu vyžaduje dokonalé načasovanie a odvahu.</p>
                        </div>
                    </div>

                    {{-- Double Flip --}}
                    <div class="overflow-hidden rounded-xl bg-[#111111] border border-[#8B5CF640]">
                        <img src="https://images.unsplash.com/photo-1639998733481?w=800&q=80" alt="Double Flip" class="w-full h-[140px] object-cover">
                        <div class="p-4 flex flex-col gap-2">
                            <h4 class="text-white font-bold">Double Flip</h4>
                            <p class="text-[#888888] text-sm leading-relaxed">Dvojité salto vpred alebo vzad. Vyžaduje extrémnu výšku skoku a rýchlu rotáciu.</p>
                        </div>
                    </div>

                    {{-- Cork --}}
                    <div class="overflow-hidden rounded-xl bg-[#111111] border border-[#8B5CF640]">
                        <img src="https://images.unsplash.com/photo-1737231808328?w=800&q=80" alt="Cork (Corkscrew)" class="w-full h-[140px] object-cover">
                        <div class="p-4 flex flex-col gap-2">
                            <h4 class="text-white font-bold">Cork (Corkscrew)</h4>
                            <p class="text-[#888888] text-sm leading-relaxed">Rotácia cez os tela počas salto. Kombinácia twist a flip vytvára spektakulárny efekt.</p>
                        </div>
                    </div>

                    {{-- Wall Spin --}}
                    <div class="overflow-hidden rounded-xl bg-[#111111] border border-[#8B5CF640]">
                        <img src="https://images.unsplash.com/photo-1543319885?w=800&q=80" alt="Wall Spin" class="w-full h-[140px] object-cover">
                        <div class="p-4 flex flex-col gap-2">
                            <h4 class="text-white font-bold">Wall Spin</h4>
                            <p class="text-[#888888] text-sm leading-relaxed">Horizontálna rotácia odrazom od steny. Vyžaduje precízne načasovanie a kontrolu tela vo vzduchu.</p>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="relative h-[500px] overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1653226046947-3f53a5b6e560?w=1440&q=80')] bg-cover bg-center"></div>
        <div class="absolute inset-0" style="background: linear-gradient(180deg, #0A0A0AEE 0%, #0A0A0ACC 50%, #0A0A0AEE 100%)"></div>

        <div class="relative w-full h-full flex flex-col items-center justify-center">
            <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col items-center justify-center gap-6">
                {{-- Badge --}}
                <span class="bg-bcz-red text-white text-xs font-bold tracking-widest rounded-full px-4 py-2">ZAČNI TRÉNOVAŤ</span>

                {{-- Title --}}
                <h2 class="font-display font-bold text-[28px] md:text-[40px] lg:text-[56px] leading-none tracking-wide text-center">PRIDAJ SA K NÁM</h2>

                {{-- Subtitle --}}
                <p class="text-[#CCCCCC] text-lg text-center max-w-[700px]">
                    Naše skupinové tréningy sú ideálne pre začiatočníkov aj pokročilých. Skúsení tréneri ťa naučia základy bezpečne a efektívne.
                </p>

                {{-- Buttons --}}
                <div class="flex flex-col sm:flex-row items-center gap-5 mt-4">
                    <a href="#" class="bg-bcz-red text-white text-sm font-bold tracking-widest px-9 py-4 rounded-lg hover:bg-red-700 transition-colors">
                        SKUPINOVÉ TRÉNINGY
                    </a>
                    <a href="#" class="border-2 border-white text-white text-sm font-bold tracking-widest px-9 py-4 rounded-lg hover:bg-white/10 transition-colors">
                        SÚKROMNÝ TRÉNING
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Gallery Section --}}
    <section class="bg-[#111111] py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col gap-12">
                {{-- Header --}}
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                        <span class="text-bcz-red text-xs font-bold tracking-[3px]">GALÉRIA</span>
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                    </div>
                    <h2 class="font-display font-bold text-[24px] md:text-[32px] lg:text-[42px] leading-none tracking-wide">UMENIE V POHYBE</h2>
                    <p class="text-[#888888] text-lg">Ukážky z tréningov a súťaží našich atlétov</p>
                </div>

                {{-- 3-column masonry grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {{-- Column 1 --}}
                    <div class="flex flex-col gap-6">
                        <img src="https://images.unsplash.com/photo-1568890834809?w=800&q=80" alt="Parkour galéria" class="w-full h-[280px] object-cover">
                        <img src="https://images.unsplash.com/photo-1739970907722?w=800&q=80" alt="Parkour galéria" class="w-full h-[200px] object-cover">
                    </div>

                    {{-- Column 2 --}}
                    <div class="flex flex-col gap-6">
                        <img src="https://images.unsplash.com/photo-1746532576889?w=800&q=80" alt="Parkour galéria" class="w-full h-[200px] object-cover">
                        <img src="https://images.unsplash.com/photo-1639846583266?w=800&q=80" alt="Parkour galéria" class="w-full h-[280px] object-cover">
                    </div>

                    {{-- Column 3 --}}
                    <div class="flex flex-col gap-6">
                        <img src="https://images.unsplash.com/photo-1676223602274?w=800&q=80" alt="Parkour galéria" class="w-full h-[240px] object-cover">
                        <img src="https://images.unsplash.com/photo-1765788897156?w=800&q=80" alt="Parkour galéria" class="w-full h-[240px] object-cover">
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
