@extends('layouts.public')

@section('title', 'BCZ Open 2026 — Registrácia | BCZ Club')

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
                    <a href="#" class="text-[#666666] text-[11px] font-medium tracking-[2px] hover:text-white transition-colors">SÚŤAŽE</a>
                    <span class="text-[#444444] text-[11px]">/</span>
                    <span class="text-bcz-red text-[11px] font-medium tracking-[2px]">REGISTRÁCIA</span>
                </div>

                {{-- Badge --}}
                <span class="bg-bcz-red text-white text-xs font-bold px-3.5 py-1.5 rounded-md w-fit">SÚŤAŽ</span>

                {{-- Title --}}
                <h1 class="font-display font-bold text-[24px] md:text-[36px] lg:text-[48px] tracking-wide text-white">BCZ Open 2026 — Street Workout</h1>

                {{-- Meta --}}
                <div class="flex flex-wrap items-center gap-4 text-[#888888] text-sm">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                        15. marca 2026
                    </span>
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                        Bratislava, Slovensko
                    </span>
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        24 súťažiacich
                    </span>
                </div>
            </div>
        </div>
    </section>

    {{-- Tab Bar --}}
    <section class="bg-[#111111] border-b border-[#222222]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex items-center gap-0 overflow-x-auto scrollbar-hide">
                <a href="{{ route('sutaz.popis') }}" class="px-6 py-4 text-[13px] font-semibold tracking-[1px] text-[#666666] hover:text-white transition-colors whitespace-nowrap border-b-2 border-transparent hover:border-[#333333]">POPIS</a>
                <a href="{{ route('sutaz.harmonogram') }}" class="px-6 py-4 text-[13px] font-semibold tracking-[1px] text-[#666666] hover:text-white transition-colors whitespace-nowrap border-b-2 border-transparent hover:border-[#333333]">HARMONOGRAM</a>
                <a href="{{ route('sutaz.vysledky') }}" class="px-6 py-4 text-[13px] font-semibold tracking-[1px] text-[#666666] hover:text-white transition-colors whitespace-nowrap border-b-2 border-transparent hover:border-[#333333]">VÝSLEDKY</a>
                <a href="{{ route('sutaz.registracia') }}" class="px-6 py-4 text-[13px] font-semibold tracking-[1px] text-bcz-red whitespace-nowrap border-b-2 border-bcz-red">REGISTRÁCIA</a>
            </div>
        </div>
    </section>

    {{-- Info Strip --}}
    <section class="bg-bcz-dark py-8 border-b border-[#222222]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                {{-- Card 1 --}}
                <div class="bg-[#111111] border border-[#222222] rounded-xl p-5 flex flex-col gap-2">
                    <span class="text-[#666666] text-[11px] font-medium tracking-[1.5px]">DÁTUM</span>
                    <span class="text-white text-[15px] font-semibold">15. marca 2026</span>
                </div>
                {{-- Card 2 --}}
                <div class="bg-[#111111] border border-[#222222] rounded-xl p-5 flex flex-col gap-2">
                    <span class="text-[#666666] text-[11px] font-medium tracking-[1.5px]">MIESTO</span>
                    <span class="text-white text-[15px] font-semibold">Bratislava</span>
                </div>
                {{-- Card 3 --}}
                <div class="bg-[#111111] border border-[#222222] rounded-xl p-5 flex flex-col gap-2">
                    <span class="text-[#666666] text-[11px] font-medium tracking-[1.5px]">KATEGÓRIE</span>
                    <span class="text-white text-[15px] font-semibold">4 kategórie</span>
                </div>
                {{-- Card 4 --}}
                <div class="bg-[#111111] border border-[#222222] rounded-xl p-5 flex flex-col gap-2">
                    <span class="text-[#666666] text-[11px] font-medium tracking-[1.5px]">KAPACITA</span>
                    <span class="text-white text-[15px] font-semibold">24 miest</span>
                </div>
                {{-- Card 5 --}}
                <div class="bg-[#111111] border border-[#222222] rounded-xl p-5 flex flex-col gap-2">
                    <span class="text-[#666666] text-[11px] font-medium tracking-[1.5px]">POPLATOK</span>
                    <span class="text-white text-[15px] font-semibold">25 &euro;</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Content Area --}}
    <section class="bg-bcz-dark py-[60px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col lg:flex-row gap-10 lg:gap-12">

                {{-- Form Column --}}
                <div class="flex-1 flex flex-col gap-8">
                    {{-- Header --}}
                    <div class="flex flex-col gap-3">
                        <h2 class="font-display font-bold text-[32px] tracking-wide text-white">Registrácia na súťaž</h2>
                        <p class="text-[#888888] text-[16px] leading-[1.7]">
                            Vyplňte registračný formulár a zabezpečte si miesto na BCZ Open 2026. Po odoslaní formulára vám príde potvrdzujúci email s ďalšími inštrukciami.
                        </p>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="flex flex-col gap-3">
                        <div class="flex items-center justify-between">
                            <span class="text-[#AAAAAA] text-sm font-medium">Krok 1 z 3 — Osobné údaje</span>
                            <span class="text-bcz-red text-sm font-bold">33%</span>
                        </div>
                        <div class="w-full h-2 bg-[#222222] rounded-full overflow-hidden">
                            <div class="w-1/3 h-full bg-bcz-red rounded-full"></div>
                        </div>
                    </div>

                    {{-- Form Card --}}
                    <div class="bg-[#111111] rounded-2xl p-6 lg:p-8 border border-[#222222]">
                        <form class="flex flex-col gap-8">
                            {{-- Osobné údaje Section --}}
                            <div class="flex flex-col gap-6">
                                <h3 class="font-display font-bold text-[20px] tracking-wide text-white">Osobné údaje</h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    {{-- Meno --}}
                                    <div class="flex flex-col gap-2">
                                        <label for="meno" class="text-[#AAAAAA] text-[13px] font-medium">Meno <span class="text-bcz-red">*</span></label>
                                        <input type="text" id="meno" name="meno" placeholder="Zadajte vaše meno" class="bg-bcz-dark border border-[#333333] rounded-lg p-3.5 text-white text-[14px] placeholder-[#555555] focus:border-bcz-red focus:outline-none transition-colors">
                                    </div>

                                    {{-- Priezvisko --}}
                                    <div class="flex flex-col gap-2">
                                        <label for="priezvisko" class="text-[#AAAAAA] text-[13px] font-medium">Priezvisko <span class="text-bcz-red">*</span></label>
                                        <input type="text" id="priezvisko" name="priezvisko" placeholder="Zadajte vaše priezvisko" class="bg-bcz-dark border border-[#333333] rounded-lg p-3.5 text-white text-[14px] placeholder-[#555555] focus:border-bcz-red focus:outline-none transition-colors">
                                    </div>

                                    {{-- Email --}}
                                    <div class="flex flex-col gap-2">
                                        <label for="email" class="text-[#AAAAAA] text-[13px] font-medium">Email <span class="text-bcz-red">*</span></label>
                                        <input type="email" id="email" name="email" placeholder="vas@email.com" class="bg-bcz-dark border border-[#333333] rounded-lg p-3.5 text-white text-[14px] placeholder-[#555555] focus:border-bcz-red focus:outline-none transition-colors">
                                    </div>

                                    {{-- Telefón --}}
                                    <div class="flex flex-col gap-2">
                                        <label for="telefon" class="text-[#AAAAAA] text-[13px] font-medium">Telefón <span class="text-bcz-red">*</span></label>
                                        <input type="tel" id="telefon" name="telefon" placeholder="+421 9XX XXX XXX" class="bg-bcz-dark border border-[#333333] rounded-lg p-3.5 text-white text-[14px] placeholder-[#555555] focus:border-bcz-red focus:outline-none transition-colors">
                                    </div>

                                    {{-- Dátum narodenia --}}
                                    <div class="flex flex-col gap-2">
                                        <label for="datum_narodenia" class="text-[#AAAAAA] text-[13px] font-medium">Dátum narodenia <span class="text-bcz-red">*</span></label>
                                        <input type="date" id="datum_narodenia" name="datum_narodenia" class="bg-bcz-dark border border-[#333333] rounded-lg p-3.5 text-white text-[14px] placeholder-[#555555] focus:border-bcz-red focus:outline-none transition-colors">
                                    </div>

                                    {{-- Klub --}}
                                    <div class="flex flex-col gap-2">
                                        <label for="klub" class="text-[#AAAAAA] text-[13px] font-medium">Klub</label>
                                        <input type="text" id="klub" name="klub" placeholder="Názov vášho klubu (voliteľné)" class="bg-bcz-dark border border-[#333333] rounded-lg p-3.5 text-white text-[14px] placeholder-[#555555] focus:border-bcz-red focus:outline-none transition-colors">
                                    </div>
                                </div>
                            </div>

                            {{-- Divider --}}
                            <div class="w-full h-px bg-[#222222]"></div>

                            {{-- Súťažné informácie Section --}}
                            <div class="flex flex-col gap-6">
                                <h3 class="font-display font-bold text-[20px] tracking-wide text-white">Súťažné informácie</h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    {{-- Kategória --}}
                                    <div class="flex flex-col gap-2">
                                        <label for="kategoria" class="text-[#AAAAAA] text-[13px] font-medium">Kategória <span class="text-bcz-red">*</span></label>
                                        <select id="kategoria" name="kategoria" class="bg-bcz-dark border border-[#333333] rounded-lg p-3.5 text-white text-[14px] focus:border-bcz-red focus:outline-none transition-colors appearance-none">
                                            <option value="" disabled selected class="text-[#555555]">Vyberte kategóriu</option>
                                            <option value="juniori">Juniori (14–17 rokov)</option>
                                            <option value="seniori">Seniori (18–35 rokov)</option>
                                            <option value="masters">Masters (35+ rokov)</option>
                                            <option value="zeny">Ženy (open)</option>
                                        </select>
                                    </div>

                                    {{-- Váhová kategória --}}
                                    <div class="flex flex-col gap-2">
                                        <label for="vahova_kategoria" class="text-[#AAAAAA] text-[13px] font-medium">Váhová kategória <span class="text-bcz-red">*</span></label>
                                        <select id="vahova_kategoria" name="vahova_kategoria" class="bg-bcz-dark border border-[#333333] rounded-lg p-3.5 text-white text-[14px] focus:border-bcz-red focus:outline-none transition-colors appearance-none">
                                            <option value="" disabled selected class="text-[#555555]">Vyberte váhovú kategóriu</option>
                                            <option value="do-70">Do 70 kg</option>
                                            <option value="do-80">Do 80 kg</option>
                                            <option value="do-90">Do 90 kg</option>
                                            <option value="nad-90">Nad 90 kg</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Textarea --}}
                                <div class="flex flex-col gap-2">
                                    <label for="poznamka" class="text-[#AAAAAA] text-[13px] font-medium">Skúsenosti / Poznámka</label>
                                    <textarea id="poznamka" name="poznamka" rows="4" placeholder="Popíšte vaše skúsenosti, dosiahnuté výsledky alebo pridajte poznámku pre organizátorov..." class="bg-bcz-dark border border-[#333333] rounded-lg p-3.5 text-white text-[14px] placeholder-[#555555] focus:border-bcz-red focus:outline-none transition-colors resize-none"></textarea>
                                </div>
                            </div>

                            {{-- Divider --}}
                            <div class="w-full h-px bg-[#222222]"></div>

                            {{-- GDPR Checkbox --}}
                            <x-gdpr-checkbox model="gdpr" />

                            {{-- Buttons --}}
                            <div class="flex flex-col sm:flex-row gap-4">
                                <button type="submit" class="flex-1 bg-bcz-red hover:bg-red-700 text-white font-bold text-[14px] tracking-[1px] rounded-lg px-8 py-4 transition-colors">
                                    ODOSLAŤ REGISTRÁCIU
                                </button>
                                <button type="button" class="w-full sm:w-auto bg-[#222222] hover:bg-[#333333] text-[#AAAAAA] font-semibold text-[14px] rounded-lg px-6 py-4 transition-colors">
                                    Uložiť koncept
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="w-full lg:w-[360px] shrink-0 flex flex-col gap-6">

                    {{-- Stav registrácie Card --}}
                    <div class="bg-[#111111] border border-[#222222] rounded-xl p-6 flex flex-col gap-5">
                        <span class="text-[#666666] text-xs font-bold tracking-[2px]">STAV REGISTRÁCIE</span>
                        <div class="flex flex-col gap-4">
                            <div class="flex justify-between items-center">
                                <span class="text-[#666666] text-sm">Stav</span>
                                <span class="bg-[#22C55E]/20 text-[#22C55E] text-xs font-bold px-3 py-1 rounded-full">Otvorená</span>
                            </div>
                            <div class="w-full h-px bg-[#222222]"></div>
                            <div class="flex justify-between">
                                <span class="text-[#666666] text-sm">Uzávierka</span>
                                <span class="text-white text-sm font-semibold">28.2.2026</span>
                            </div>
                            <div class="w-full h-px bg-[#222222]"></div>
                            <div class="flex flex-col gap-2">
                                <div class="flex justify-between">
                                    <span class="text-[#666666] text-sm">Kapacita</span>
                                    <span class="text-bcz-red text-sm font-bold">18 / 24</span>
                                </div>
                                <div class="w-full h-1.5 bg-[#222222] rounded-full overflow-hidden">
                                    <div class="w-3/4 h-full bg-bcz-red rounded-full"></div>
                                </div>
                            </div>
                            <div class="w-full h-px bg-[#222222]"></div>
                            <div class="flex justify-between">
                                <span class="text-[#666666] text-sm">Poplatok</span>
                                <span class="text-white text-sm font-semibold">25 &euro;</span>
                            </div>
                        </div>
                    </div>

                    {{-- Požiadavky Card --}}
                    <div class="bg-[#111111] border border-[#222222] rounded-xl p-6 flex flex-col gap-5">
                        <span class="text-[#666666] text-xs font-bold tracking-[2px]">POŽIADAVKY</span>
                        <div class="flex flex-col gap-3">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-[#22C55E] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
                                <span class="text-[#AAAAAA] text-[14px]">Minimálny vek 14 rokov</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-[#22C55E] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
                                <span class="text-[#AAAAAA] text-[14px]">Platné lekárske potvrdenie</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-[#22C55E] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
                                <span class="text-[#AAAAAA] text-[14px]">Uhradený registračný poplatok</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-[#22C55E] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
                                <span class="text-[#AAAAAA] text-[14px]">Súhlas so spracovaním údajov</span>
                            </div>
                        </div>
                    </div>

                    {{-- Dôležité termíny Card --}}
                    <div class="bg-[#111111] border border-[#222222] rounded-xl p-6 flex flex-col gap-5">
                        <span class="text-[#666666] text-xs font-bold tracking-[2px]">DÔLEŽITÉ TERMÍNY</span>
                        <div class="flex flex-col gap-4">
                            <div class="flex items-start gap-3">
                                <div class="w-2 h-2 rounded-full bg-bcz-red mt-1.5 shrink-0"></div>
                                <div class="flex flex-col">
                                    <span class="text-white text-[14px] font-semibold">Otvorenie registrácie</span>
                                    <span class="text-[#666666] text-[13px]">1. januára 2026</span>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-2 h-2 rounded-full bg-bcz-red mt-1.5 shrink-0"></div>
                                <div class="flex flex-col">
                                    <span class="text-white text-[14px] font-semibold">Uzávierka registrácie</span>
                                    <span class="text-[#666666] text-[13px]">28. februára 2026</span>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-2 h-2 rounded-full bg-bcz-red mt-1.5 shrink-0"></div>
                                <div class="flex flex-col">
                                    <span class="text-white text-[14px] font-semibold">Oficiálne váženie</span>
                                    <span class="text-[#666666] text-[13px]">14. marca 2026</span>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-2 h-2 rounded-full bg-bcz-red mt-1.5 shrink-0"></div>
                                <div class="flex flex-col">
                                    <span class="text-white text-[14px] font-semibold">Deň súťaže</span>
                                    <span class="text-[#666666] text-[13px]">15. marca 2026</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Kontakt Card --}}
                    <div class="bg-bcz-red/10 border border-bcz-red/40 rounded-xl p-6 flex flex-col gap-5">
                        <span class="text-bcz-red text-xs font-bold tracking-[2px]">KONTAKT</span>
                        <div class="flex flex-col gap-4">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-bcz-red shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                <a href="mailto:sutaz@bczclub.sk" class="text-white text-[14px] hover:text-bcz-red transition-colors">sutaz@bczclub.sk</a>
                            </div>
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-bcz-red shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                <a href="tel:+421901234567" class="text-white text-[14px] hover:text-bcz-red transition-colors">+421 901 234 567</a>
                            </div>
                        </div>
                        <p class="text-[#AAAAAA] text-[13px] leading-[1.6]">
                            V prípade otázok ohľadom registrácie nás neváhajte kontaktovať.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection
