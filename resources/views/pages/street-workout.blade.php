@extends('layouts.public')

@section('title', 'Street Workout & Kalistenika | BCZ Club')

@section('content')
    {{-- Hero Section --}}
    <section class="relative w-full h-[600px] overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1628257491475-7c586ef2799c?w=1440&q=80')] bg-cover bg-center"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-bcz-dark via-transparent to-bcz-dark"></div>

        <div class="relative w-full h-full flex flex-col items-center justify-center">
            <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col items-center justify-center gap-6 pt-[120px]">
                {{-- Breadcrumb --}}
                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" class="text-bcz-muted text-[11px] font-medium tracking-widest hover:text-white transition-colors">DOMOV</a>
                    <span class="text-[#444444] text-[11px]">/</span>
                    <span class="text-bcz-muted text-[11px] font-medium tracking-widest">KATEGÓRIE</span>
                    <span class="text-[#444444] text-[11px]">/</span>
                    <span class="text-bcz-red text-[11px] font-medium tracking-widest">STREET WORKOUT</span>
                </div>

                {{-- Badge --}}
                <span class="bg-[#3B82F6]/20 text-[#3B82F6] text-xs font-bold tracking-widest rounded-full px-4 py-2">KATEGÓRIA</span>

                {{-- Title --}}
                <h1 class="font-display font-bold text-[28px] md:text-[40px] lg:text-[56px] leading-[0.95] tracking-wide text-center">STREET WORKOUT &amp; KALISTENIKA</h1>

                {{-- Subtitle --}}
                <p class="text-[#CCCCCC] text-[24px] text-center">
                    Ovládni svoje telo. Ovládni gravitáciu.
                </p>
            </div>
        </div>
    </section>

    {{-- About Section --}}
    <section class="bg-bcz-dark py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col gap-12">
                {{-- Header --}}
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-0.5 bg-[#3B82F6]"></div>
                        <span class="text-[#3B82F6] text-xs font-bold tracking-widest">O ŠPORTE</span>
                        <div class="w-10 h-0.5 bg-[#3B82F6]"></div>
                    </div>
                    <h2 class="font-display font-bold text-[24px] md:text-[32px] lg:text-[42px] tracking-wide">ČO JE STREET WORKOUT?</h2>
                </div>

            {{-- Two columns --}}
            <div class="flex flex-col lg:flex-row gap-10 lg:gap-20">
                {{-- Left --}}
                <div class="flex-1 flex flex-col gap-6">
                    <p class="text-[#CCCCCC] text-lg leading-[1.7]">
                        Street workout, známy aj ako kalistenika, je forma silového tréningu využívajúca vlastnú váhu tela. Cvičíš na hrazdách, bradlách a iných zariadeniach - vonku, v parkoch, kdekoľvek.
                    </p>

                    <p class="text-[#888888] text-lg leading-[1.7]">
                        Kombinuje silu, vytrvalosť a estetiku pohybu. Od základných cvikov ako zhyby a kliky, až po pokročilé prvky ako front lever, planche či muscle up - vždy je kam rásť.
                    </p>

                    <blockquote class="border-l-[3px] border-[#3B82F6] pl-6 text-[#3B82F6] italic text-lg leading-[1.7]">
                        "Tvoje telo je tvojou posiľňovňou. Jediné čo potrebuješ, je vôľa začať."
                    </blockquote>
                </div>

                {{-- Right --}}
                <div class="w-full lg:w-[500px] shrink-0">
                    <img src="https://images.unsplash.com/photo-1758875569440-4abefff43277?w=600&q=80" alt="Street Workout" class="w-full h-[400px] object-cover">
                </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Strength Section --}}
    <section class="bg-[#111111] py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col gap-12">
                {{-- Header --}}
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-0.5 bg-[#3B82F6]"></div>
                        <span class="text-[#3B82F6] text-xs font-bold tracking-widest">SILA &amp; KONTROLA</span>
                        <div class="w-10 h-0.5 bg-[#3B82F6]"></div>
                    </div>
                    <h2 class="font-display font-bold text-[24px] md:text-[32px] lg:text-[42px] tracking-wide">OVLÁDNI GRAVITÁCIU</h2>
                    <p class="text-[#888888] text-lg max-w-[700px]">
                        Street workout ti dá kontrolu nad vlastným telom, akú si nikdy nemal
                    </p>
                </div>

            {{-- 4-column grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Card 1: Dumbbell --}}
                <div class="bg-bcz-dark border border-[#222222] rounded-2xl p-8 flex flex-col gap-5">
                    <div class="size-14 bg-[#3B82F6]/20 flex items-center justify-center rounded-xl">
                        <svg class="w-7 h-7 text-[#3B82F6]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.4 14.4 9.6 9.6"/><path d="M18.657 21.485a2 2 0 1 1-2.829-2.828l-1.767 1.768a2 2 0 1 1-2.829-2.829l6.364-6.364a2 2 0 1 1 2.829 2.829l-1.768 1.767a2 2 0 1 1 2.828 2.829z"/><path d="m21.5 21.5-1.4-1.4"/><path d="M3.9 3.9 2.5 2.5"/><path d="M6.404 12.768a2 2 0 1 1-2.829-2.829l1.768-1.767a2 2 0 1 1-2.828-2.829l2.828-2.828a2 2 0 1 1 2.829 2.828l1.767-1.768a2 2 0 1 1 2.829 2.829z"/></svg>
                    </div>
                    <h3 class="text-white text-xl font-bold">Čistá sila</h3>
                    <p class="text-[#888888] text-sm leading-relaxed">
                        Vybuduješ funkčnú silu bez strojov a závaží. Tvoje telo je jediné náradie, ktoré potrebuješ.
                    </p>
                </div>

                {{-- Card 2: Scale --}}
                <div class="bg-bcz-dark border border-[#222222] rounded-2xl p-8 flex flex-col gap-5">
                    <div class="size-14 bg-[#3B82F6]/20 flex items-center justify-center rounded-xl">
                        <svg class="w-7 h-7 text-[#3B82F6]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/><path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/><path d="M7 21h10"/><path d="M12 3v18"/><path d="M3 7h2c2 0 5-1 7-2 2 1 5 2 7 2h2"/></svg>
                    </div>
                    <h3 class="text-white text-xl font-bold">Rovnováha</h3>
                    <p class="text-[#888888] text-sm leading-relaxed">
                        Naučíš sa ovládať svoje telo v náročných pozíciách. Rovnováha a stabilita sa stanú tvojou druhou prirodzenosťou.
                    </p>
                </div>

                {{-- Card 3: Zap --}}
                <div class="bg-bcz-dark border border-[#222222] rounded-2xl p-8 flex flex-col gap-5">
                    <div class="size-14 bg-[#3B82F6]/20 flex items-center justify-center rounded-xl">
                        <svg class="w-7 h-7 text-[#3B82F6]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"/></svg>
                    </div>
                    <h3 class="text-white text-xl font-bold">Vytrvalosť</h3>
                    <p class="text-[#888888] text-sm leading-relaxed">
                        High-rep sety a kombinácie cvikov ti dajú vytrvalosť, ktorá ťa bude sprevádzať celý deň.
                    </p>
                </div>

                {{-- Card 4: Target --}}
                <div class="bg-bcz-dark border border-[#222222] rounded-2xl p-8 flex flex-col gap-5">
                    <div class="size-14 bg-[#3B82F6]/20 flex items-center justify-center rounded-xl">
                        <svg class="w-7 h-7 text-[#3B82F6]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>
                    </div>
                    <h3 class="text-white text-xl font-bold">Estetika</h3>
                    <p class="text-[#888888] text-sm leading-relaxed">
                        Statické prvky ako planche či front lever nie sú len o sile - sú to diela pohybového umenia.
                    </p>
                </div>
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
                    <div class="w-10 h-0.5 bg-[#3B82F6]"></div>
                    <span class="text-[#3B82F6] text-xs font-bold tracking-widest">PRVKY &amp; CVIKY</span>
                    <div class="w-10 h-0.5 bg-[#3B82F6]"></div>
                </div>
                <h2 class="font-display font-bold text-[24px] md:text-[32px] lg:text-[42px] tracking-wide">ČO SA NAUČÍŠ</h2>
                <p class="text-[#888888] text-lg max-w-[700px]">
                    Od základov až po pokročilé prvky - každý začína niekde. Naši tréneri ťa prevedú celou cestou.
                </p>
            </div>

            {{-- ZÁKLADY --}}
            <div class="flex flex-col gap-6">
                <div class="flex items-center gap-4">
                    <h3 class="font-display font-bold text-2xl tracking-wide text-[#22C55E]">ZÁKLADY</h3>
                    <span class="text-[#888888] text-sm">Začiatočnícke cviky</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    {{-- Pull-up --}}
                    <div class="bg-[#111111] border border-[#22C55E40] rounded-xl overflow-hidden">
                        <div class="w-full h-[140px] bg-[url('https://images.unsplash.com/photo-1669323149885?w=800&q=80')] bg-cover bg-center"></div>
                        <div class="p-4 flex flex-col gap-2">
                            <h4 class="text-white text-base font-bold">Pull-up (Zhyb)</h4>
                            <p class="text-[#888888] text-sm leading-relaxed">Základný cvik pre silu chrbta a bicepsov. Ťaháš sa hore k hrazde.</p>
                        </div>
                    </div>

                    {{-- Dip --}}
                    <div class="bg-[#111111] border border-[#22C55E40] rounded-xl overflow-hidden">
                        <div class="w-full h-[140px] bg-[url('https://images.unsplash.com/photo-1758875569440?w=800&q=80')] bg-cover bg-center"></div>
                        <div class="p-4 flex flex-col gap-2">
                            <h4 class="text-white text-base font-bold">Dip (Klik na bradlách)</h4>
                            <p class="text-[#888888] text-sm leading-relaxed">Posiľňuje tricepsy a hrudník. Spúšťaš sa a tlačíš nahor na bradlách.</p>
                        </div>
                    </div>

                    {{-- Push-up --}}
                    <div class="bg-[#111111] border border-[#22C55E40] rounded-xl overflow-hidden">
                        <div class="w-full h-[140px] bg-[url('https://images.unsplash.com/photo-1683758575782?w=800&q=80')] bg-cover bg-center"></div>
                        <div class="p-4 flex flex-col gap-2">
                            <h4 class="text-white text-base font-bold">Push-up (Klik)</h4>
                            <p class="text-[#888888] text-sm leading-relaxed">Klasický cvik na hrudník, ramená a tricepsy. Základ každého tréningu.</p>
                        </div>
                    </div>

                    {{-- Australian Pull-up --}}
                    <div class="bg-[#111111] border border-[#22C55E40] rounded-xl overflow-hidden">
                        <div class="p-4 flex flex-col gap-2">
                            <h4 class="text-white text-base font-bold">Australian Pull-up</h4>
                            <p class="text-[#888888] text-sm leading-relaxed">Horizontálne ťahanie. Ideálne pre prípravu na plný zhyb.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STREDNÉ --}}
            <div class="flex flex-col gap-6">
                <div class="flex items-center gap-4">
                    <h3 class="font-display font-bold text-2xl tracking-wide text-[#F59E0B]">STREDNÉ</h3>
                    <span class="text-[#888888] text-sm">Mierne pokročilé prvky</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    {{-- Muscle-up --}}
                    <div class="bg-[#111111] border border-[#F59E0B40] rounded-xl overflow-hidden">
                        <div class="w-full h-[140px] bg-[url('https://images.unsplash.com/photo-1669504243706?w=800&q=80')] bg-cover bg-center"></div>
                        <div class="p-4 flex flex-col gap-2">
                            <h4 class="text-white text-base font-bold">Muscle-up</h4>
                            <p class="text-[#888888] text-sm leading-relaxed">Kombinácia zhybu a dipu. Explozívny prechod cez hrazdu. Míľnik každého atléta.</p>
                        </div>
                    </div>

                    {{-- L-sit --}}
                    <div class="bg-[#111111] border border-[#F59E0B40] rounded-xl overflow-hidden">
                        <div class="w-full h-[140px] bg-[url('https://images.unsplash.com/photo-1548294363?w=800&q=80')] bg-cover bg-center"></div>
                        <div class="p-4 flex flex-col gap-2">
                            <h4 class="text-white text-base font-bold">L-sit</h4>
                            <p class="text-[#888888] text-sm leading-relaxed">Statická pozícia s nohami v pravom uhle. Vyžaduje silu jadra a tricepsov.</p>
                        </div>
                    </div>

                    {{-- Handstand --}}
                    <div class="bg-[#111111] border border-[#F59E0B40] rounded-xl overflow-hidden">
                        <div class="w-full h-[140px] bg-[url('https://images.unsplash.com/photo-1530014114591?w=800&q=80')] bg-cover bg-center"></div>
                        <div class="p-4 flex flex-col gap-2">
                            <h4 class="text-white text-base font-bold">Handstand (Stojka)</h4>
                            <p class="text-[#888888] text-sm leading-relaxed">Stoj na rukách. Základ pre mnohé pokročilé prvky. Vyžaduje rovnováhu a silu ramien.</p>
                        </div>
                    </div>

                    {{-- Pistol Squat --}}
                    <div class="bg-[#111111] border border-[#F59E0B40] rounded-xl overflow-hidden">
                        <div class="w-full h-[140px] bg-[url('https://images.unsplash.com/photo-1654512590463?w=800&q=80')] bg-cover bg-center"></div>
                        <div class="p-4 flex flex-col gap-2">
                            <h4 class="text-white text-base font-bold">Pistol Squat</h4>
                            <p class="text-[#888888] text-sm leading-relaxed">Drep na jednej nohe. Vyžaduje silu nôh, rovnováhu a flexibilitu.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- POKROČILÉ --}}
            <div class="flex flex-col gap-6">
                <div class="flex items-center gap-4">
                    <h3 class="font-display font-bold text-2xl tracking-wide text-[#EF4444]">POKROČILÉ</h3>
                    <span class="text-[#888888] text-sm">Pokročilé statické prvky</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    {{-- Front Lever --}}
                    <div class="bg-[#111111] border border-[#EF444440] rounded-xl overflow-hidden">
                        <div class="w-full h-[140px] bg-[url('https://images.unsplash.com/photo-1630415188026?w=800&q=80')] bg-cover bg-center"></div>
                        <div class="p-4 flex flex-col gap-2">
                            <h4 class="text-white text-base font-bold">Front Lever</h4>
                            <p class="text-[#888888] text-sm leading-relaxed">Horizontálne držanie tela pod hrazdou. Extrémna sila chrbta a jadra.</p>
                        </div>
                    </div>

                    {{-- Back Lever --}}
                    <div class="bg-[#111111] border border-[#EF444440] rounded-xl overflow-hidden">
                        <div class="w-full h-[140px] bg-[url('https://images.unsplash.com/photo-1634788699029?w=800&q=80')] bg-cover bg-center"></div>
                        <div class="p-4 flex flex-col gap-2">
                            <h4 class="text-white text-base font-bold">Back Lever</h4>
                            <p class="text-[#888888] text-sm leading-relaxed">Horizontálne držanie chrbtom dole. Vyžaduje flexibilitu ramien a silu.</p>
                        </div>
                    </div>

                    {{-- Planche --}}
                    <div class="bg-[#111111] border border-[#EF444440] rounded-xl overflow-hidden">
                        <div class="w-full h-[140px] bg-[url('https://images.unsplash.com/photo-1767611121720?w=800&q=80')] bg-cover bg-center"></div>
                        <div class="p-4 flex flex-col gap-2">
                            <h4 class="text-white text-base font-bold">Planche</h4>
                            <p class="text-[#888888] text-sm leading-relaxed">Vodorovný stoj na rukách bez opory. Vrchol sily ramien a jadra.</p>
                        </div>
                    </div>

                    {{-- Human Flag --}}
                    <div class="bg-[#111111] border border-[#EF444440] rounded-xl overflow-hidden">
                        <div class="w-full h-[140px] bg-[url('https://images.unsplash.com/photo-1680759170077?w=800&q=80')] bg-cover bg-center"></div>
                        <div class="p-4 flex flex-col gap-2">
                            <h4 class="text-white text-base font-bold">Human Flag</h4>
                            <p class="text-[#888888] text-sm leading-relaxed">Vlajka - bočné držanie tela na tyči. Ikonický prvok street workouta.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- EXPERT --}}
            <div class="flex flex-col gap-6">
                <div class="flex items-center gap-4">
                    <h3 class="font-display font-bold text-2xl tracking-wide text-[#8B5CF6]">EXPERT</h3>
                    <span class="text-[#888888] text-sm">Majstrovské prvky</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    {{-- Iron Cross --}}
                    <div class="bg-[#111111] border border-[#8B5CF640] rounded-xl overflow-hidden">
                        <div class="w-full h-[140px] bg-[url('https://images.unsplash.com/photo-1583155778358?w=800&q=80')] bg-cover bg-center"></div>
                        <div class="p-4 flex flex-col gap-2">
                            <h4 class="text-white text-base font-bold">Iron Cross</h4>
                            <p class="text-[#888888] text-sm leading-relaxed">Železný kríž na kruhoch. Legendárny gymnastický prvok vyžadujúci extrémnu silu.</p>
                        </div>
                    </div>

                    {{-- Victorian --}}
                    <div class="bg-[#111111] border border-[#8B5CF640] rounded-xl overflow-hidden">
                        <div class="w-full h-[140px] bg-[url('https://images.unsplash.com/photo-1655842556824?w=800&q=80')] bg-cover bg-center"></div>
                        <div class="p-4 flex flex-col gap-2">
                            <h4 class="text-white text-base font-bold">Victorian</h4>
                            <p class="text-[#888888] text-sm leading-relaxed">Obrátený kríž. Telo hore nohami v horizontálnej pozícii. Extrémne náročné.</p>
                        </div>
                    </div>

                    {{-- Full Planche --}}
                    <div class="bg-[#111111] border border-[#8B5CF640] rounded-xl overflow-hidden">
                        <div class="w-full h-[140px] bg-[url('https://images.unsplash.com/photo-1767611121720?w=800&q=80')] bg-cover bg-center"></div>
                        <div class="p-4 flex flex-col gap-2">
                            <h4 class="text-white text-base font-bold">Full Planche</h4>
                            <p class="text-[#888888] text-sm leading-relaxed">Plná planche s natiahnutými nohami. Vrchol sily a kontroly tela.</p>
                        </div>
                    </div>

                    {{-- One Arm Pull-up --}}
                    <div class="bg-[#111111] border border-[#8B5CF640] rounded-xl overflow-hidden">
                        <div class="w-full h-[140px] bg-[url('https://images.unsplash.com/photo-1623947061710?w=800&q=80')] bg-cover bg-center"></div>
                        <div class="p-4 flex flex-col gap-2">
                            <h4 class="text-white text-base font-bold">One Arm Pull-up</h4>
                            <p class="text-[#888888] text-sm leading-relaxed">Zhyb na jednej ruke. Symbol čistej sily. Vyžaduje roky trénovania.</p>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="relative w-full h-[500px] overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1760552146091-d5e5ac533be9?w=1440&q=80')] bg-cover bg-center"></div>
        <div class="absolute inset-0 bg-bcz-dark/80"></div>

        <div class="relative w-full h-full flex flex-col items-center justify-center">
            <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col items-center justify-center gap-8">
                {{-- Badge --}}
                <span class="bg-[#3B82F6] text-white text-xs font-bold tracking-widest rounded-full px-4 py-2">ZAČNI TRÉNOVAŤ</span>

                {{-- Title --}}
                <h2 class="font-display font-bold text-[28px] md:text-[40px] lg:text-[56px] tracking-wide text-center">POSUŇ SVOJE LIMITY</h2>

                {{-- Subtitle --}}
                <p class="text-bcz-muted text-lg text-center max-w-[700px]">
                    Naše tréningy sú vhodné pre všetky úrovne. Či si úplný začiatočník alebo chceš dotiahnuť pokročilé prvky, máme pre teba miesto.
                </p>

                {{-- Buttons --}}
                <div class="flex flex-col sm:flex-row items-center gap-5">
                    <a href="#" class="bg-[#3B82F6] text-white text-sm font-bold tracking-widest px-9 py-4.5 flex items-center gap-3 hover:bg-blue-600 transition-colors">
                        SKUPINOVÉ TRÉNINGY
                    </a>
                    <a href="#" class="border-2 border-white text-white text-sm font-bold tracking-widest px-9 py-4.5 flex items-center gap-3 hover:bg-white/10 transition-colors">
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
                        <div class="w-10 h-0.5 bg-[#3B82F6]"></div>
                        <span class="text-[#3B82F6] text-xs font-bold tracking-widest">GALÉRIA</span>
                        <div class="w-10 h-0.5 bg-[#3B82F6]"></div>
                    </div>
                    <h2 class="font-display font-bold text-[24px] md:text-[32px] lg:text-[42px] tracking-wide">SILA V AKCII</h2>
                    <p class="text-[#888888] text-lg">Inšpirácia z tréningov a súťaží</p>
                </div>

                {{-- 3-column masonry --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    {{-- Column 1 --}}
                    <div class="flex flex-col gap-4">
                        <div class="w-full h-[280px] rounded-xl overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1738523686787?w=800&q=80" alt="Street Workout Gallery" class="w-full h-full object-cover">
                        </div>
                        <div class="w-full h-[200px] rounded-xl overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1758521959549?w=800&q=80" alt="Street Workout Gallery" class="w-full h-full object-cover">
                        </div>
                    </div>

                    {{-- Column 2 --}}
                    <div class="flex flex-col gap-4">
                        <div class="w-full h-[200px] rounded-xl overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1688521011206?w=800&q=80" alt="Street Workout Gallery" class="w-full h-full object-cover">
                        </div>
                        <div class="w-full h-[280px] rounded-xl overflow-hidden">
                            <img src="https://images.unsplash.com/flagged/photo-1550701103?w=800&q=80" alt="Street Workout Gallery" class="w-full h-full object-cover">
                        </div>
                    </div>

                    {{-- Column 3 --}}
                    <div class="flex flex-col gap-4">
                        <div class="w-full h-[240px] rounded-xl overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1638820722152?w=800&q=80" alt="Street Workout Gallery" class="w-full h-full object-cover">
                        </div>
                        <div class="w-full h-[240px] rounded-xl overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1751569819488?w=800&q=80" alt="Street Workout Gallery" class="w-full h-full object-cover">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
