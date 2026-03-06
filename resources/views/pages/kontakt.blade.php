@extends('layouts.public')

@section('title', 'Kontakt - BCZ Club')

@section('content')
    {{-- Hero Section --}}
    <section class="bg-bcz-dark pt-[120px] pb-[60px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col gap-5">
            <span class="bg-bcz-red text-white text-xs font-bold px-3.5 py-1.5 rounded-md w-fit">KONTAKT</span>
            <h1 class="font-display font-bold text-[28px] md:text-[40px] lg:text-[56px] tracking-wide">Napíšte nám</h1>
            <p class="text-[#888888] text-lg max-w-[600px]">Máte otázku, chcete si dohodnúť tréning alebo spoluprácu? Sme tu pre vás.</p>
        </div>
    </section>

    {{-- Main Section --}}
    <section class="bg-bcz-dark py-[60px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col lg:flex-row gap-8 lg:gap-20">
        {{-- LEFT - Form --}}
        <div class="flex-1 flex flex-col gap-8">
            <h2 class="font-display font-bold text-[28px] tracking-wide">Kontaktný formulár</h2>

            <form class="flex flex-col gap-8">
                {{-- Reason Block --}}
                <div class="flex flex-col gap-2">
                    <label class="text-white text-sm font-semibold">Dôvod kontaktu (voliteľné)</label>
                    <div class="flex flex-wrap gap-3">
                        <label class="rounded-lg border border-[#333333] bg-[#111111] px-4 py-2.5 text-[#CCCCCC] text-sm cursor-pointer hover:border-bcz-red transition">
                            <input type="radio" name="reason" value="sukromny-trening" class="hidden peer">
                            <span class="peer-checked:text-white">Súkromný tréning</span>
                        </label>
                        <label class="rounded-lg border border-bcz-red bg-[#FF2D2D15] px-4 py-2.5 text-white text-sm cursor-pointer hover:border-bcz-red transition">
                            <input type="radio" name="reason" value="vystupenie" checked class="hidden peer">
                            <span>Vystúpenie</span>
                        </label>
                        <label class="rounded-lg border border-[#333333] bg-[#111111] px-4 py-2.5 text-[#CCCCCC] text-sm cursor-pointer hover:border-bcz-red transition">
                            <input type="radio" name="reason" value="workshop" class="hidden peer">
                            <span class="peer-checked:text-white">Workshop</span>
                        </label>
                        <label class="rounded-lg border border-[#333333] bg-[#111111] px-4 py-2.5 text-[#CCCCCC] text-sm cursor-pointer hover:border-bcz-red transition">
                            <input type="radio" name="reason" value="prednaska" class="hidden peer">
                            <span class="peer-checked:text-white">Prednáška</span>
                        </label>
                        <label class="rounded-lg border border-[#333333] bg-[#111111] px-4 py-2.5 text-[#CCCCCC] text-sm cursor-pointer hover:border-bcz-red transition">
                            <input type="radio" name="reason" value="ine" class="hidden peer">
                            <span class="peer-checked:text-white">Iné</span>
                        </label>
                    </div>
                </div>

                {{-- Row 1: Name + Email --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="flex flex-col gap-2">
                        <label class="text-white text-sm font-semibold">Meno a priezvisko</label>
                        <input type="text" name="name" class="bg-[#111111] border border-[#333333] rounded-lg px-4 py-3.5 text-white text-[15px] placeholder-[#666666] w-full" placeholder="Meno a priezvisko">
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-white text-sm font-semibold">E-mail</label>
                        <input type="email" name="email" class="bg-[#111111] border border-[#333333] rounded-lg px-4 py-3.5 text-white text-[15px] placeholder-[#666666] w-full" placeholder="E-mail">
                    </div>
                </div>

                {{-- Phone --}}
                <div class="flex flex-col gap-2">
                    <label class="text-white text-sm font-semibold">Telefón (voliteľné)</label>
                    <input type="tel" name="phone" class="bg-[#111111] border border-[#333333] rounded-lg px-4 py-3.5 text-white text-[15px] placeholder-[#666666] w-full" placeholder="+421">
                </div>

                {{-- Message --}}
                <div class="flex flex-col gap-2">
                    <label class="text-white text-sm font-semibold">Správa</label>
                    <textarea name="message" class="bg-[#111111] border border-[#333333] rounded-lg px-4 py-3.5 text-white text-[15px] placeholder-[#666666] w-full h-40 resize-none" placeholder="Vaša správa..."></textarea>
                </div>

                {{-- Submit --}}
                <button type="submit" class="bg-bcz-red text-white rounded-lg px-8 py-4 font-semibold flex items-center justify-center gap-2.5 w-fit">
                    Odoslať správu
                    <span>→</span>
                </button>
            </form>
        </div>

        {{-- RIGHT - Sidebar --}}
        <div class="w-full lg:w-[400px] flex flex-col gap-8">
            {{-- Info Card --}}
            <div class="rounded-2xl bg-[#111111] p-8 border border-[#222222] flex flex-col gap-6">
                <h3 class="text-[22px] font-bold text-white">Kontaktné údaje</h3>

                {{-- E-mail --}}
                <div class="flex items-center gap-4">
                    <div class="rounded-xl bg-[#1A1A1A] w-11 h-11 flex items-center justify-center text-lg">
                        ✉
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[#888888] text-[13px]">E-mail</span>
                        <span class="text-white text-base font-semibold">info@bfreak.sk</span>
                    </div>
                </div>

                {{-- Phone --}}
                <div class="flex items-center gap-4">
                    <div class="rounded-xl bg-[#1A1A1A] w-11 h-11 flex items-center justify-center text-lg">
                        📞
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[#888888] text-[13px]">Telefón</span>
                        <span class="text-white text-base font-semibold">+421 900 000 000</span>
                    </div>
                </div>

                {{-- Location --}}
                <div class="flex items-center gap-4">
                    <div class="rounded-xl bg-[#1A1A1A] w-11 h-11 flex items-center justify-center text-lg">
                        📍
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[#888888] text-[13px]">Lokalita</span>
                        <span class="text-white text-base font-semibold">Žilina, Slovensko</span>
                    </div>
                </div>
            </div>

            {{-- Social Card --}}
            <div class="rounded-2xl bg-[#111111] p-8 border border-[#222222]">
                <h3 class="text-white text-lg font-bold mb-4">Sledujte nás</h3>
                <div class="flex gap-3">
                    <a href="#" class="w-12 h-12 bg-[#1A1A1A] rounded-xl flex items-center justify-center text-[#888888] text-sm font-bold hover:text-white transition">IG</a>
                    <a href="#" class="w-12 h-12 bg-[#1A1A1A] rounded-xl flex items-center justify-center text-[#888888] text-sm font-bold hover:text-white transition">FB</a>
                    <a href="#" class="w-12 h-12 bg-[#1A1A1A] rounded-xl flex items-center justify-center text-[#888888] text-sm font-bold hover:text-white transition">YT</a>
                    <a href="#" class="w-12 h-12 bg-[#1A1A1A] rounded-xl flex items-center justify-center text-[#888888] text-sm font-bold hover:text-white transition">TT</a>
                </div>
            </div>

            {{-- Response Card --}}
            <div class="rounded-xl bg-[#FF2D2D10] p-6 border border-[#FF2D2D30]">
                <div class="flex flex-col gap-2">
                    <span class="text-lg">⚡</span>
                    <span class="text-white font-bold">Rýchla odpoveď</span>
                    <p class="text-[#CCCCCC] text-sm leading-relaxed">Zvyčajne odpovedáme do 24 hodín. Pre urgentné záležitosti nás kontaktujte telefonicky.</p>
                </div>
            </div>
        </div>
        </div>
    </section>

    {{-- FAQ Section --}}
    <section class="bg-[#111111] py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col gap-12">
            {{-- Header --}}
            <div class="flex items-center gap-4">
                <div class="rounded-xl bg-[#FF2D2D20] w-12 h-12 flex items-center justify-center text-xl">
                    ❓
                </div>
                <h2 class="font-display font-bold text-[24px] tracking-wide">Najčastejšie otázky</h2>
                <span class="bg-[#222222] rounded-full px-3 py-1 text-[#888888] text-xs">3</span>
            </div>

            {{-- FAQ Items --}}
            <div class="flex flex-col gap-4">
                <div class="rounded-xl bg-[#0A0A0A] border border-[#222222] p-6 flex justify-between items-center cursor-pointer">
                    <span class="text-white font-semibold">Ako si môžem objednať súkromný tréning?</span>
                    <span class="text-[#666666] text-2xl">+</span>
                </div>
                <div class="rounded-xl bg-[#0A0A0A] border border-[#222222] p-6 flex justify-between items-center cursor-pointer">
                    <span class="text-white font-semibold">Aké sú vaše ceny za vystúpenia a workshopy?</span>
                    <span class="text-[#666666] text-2xl">+</span>
                </div>
                <div class="rounded-xl bg-[#0A0A0A] border border-[#222222] p-6 flex justify-between items-center cursor-pointer">
                    <span class="text-white font-semibold">Kde presne sa nachádzate a aké sú tréningové hodiny?</span>
                    <span class="text-[#666666] text-2xl">+</span>
                </div>
            </div>

            {{-- Show All Link --}}
            <div class="flex justify-center items-center gap-2 pt-6 border-t border-[#222222]">
                <a href="#" class="text-white font-semibold text-[15px] flex items-center gap-2 hover:text-bcz-red transition">
                    Zobraziť všetky často kladené otázky
                    <span>→</span>
                </a>
            </div>
        </div>
    </section>
@endsection
