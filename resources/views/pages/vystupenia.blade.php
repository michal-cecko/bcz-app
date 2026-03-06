@extends('layouts.public')

@section('title', 'Akrobatické Vystúpenia - BCZ Club')

@section('content')
    {{-- Hero Section --}}
    <section class="relative w-full h-[700px] overflow-hidden bg-bcz-dark">
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1657268378135-b818f5cbd6ba?w=1080&q=80')] bg-cover bg-center"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-[#0A0A0A] via-[#0A0A0ACC] to-transparent"></div>
        <div class="relative max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 h-full flex flex-col justify-end pb-20 gap-6">
            {{-- Breadcrumbs --}}
            <div class="flex items-center gap-2 text-[13px]">
                <a href="{{ route('home') }}" class="text-bcz-muted hover:text-white transition-colors">Domov</a>
                <span class="text-bcz-muted">›</span>
                <a href="{{ route('vystupenia-workshopy') }}" class="text-bcz-muted hover:text-white transition-colors">Vystúpenia & Workshopy</a>
                <span class="text-bcz-muted">›</span>
                <span class="text-white">Vystúpenia</span>
            </div>

            {{-- Badge --}}
            <div class="bg-bcz-red/10 border border-bcz-red/25 rounded-md px-4 py-2 w-fit">
                <span class="text-bcz-red text-[12px] font-bold tracking-[2px]">AKROBATICKÉ SHOW</span>
            </div>

            {{-- Title --}}
            <h1 class="font-display font-bold text-[48px] md:text-[72px] lg:text-[96px] leading-[0.95] tracking-wide text-white">
                AKROBATICKÉ<br>VYSTÚPENIA
            </h1>

            {{-- Description --}}
            <p class="text-bcz-light text-[18px] md:text-[20px] leading-[1.7] max-w-[750px]">
                Prinášame spektakulárne akrobatické show na vaše eventy, festivaly a firemné podujatia. Dynamické vystúpenia, ktoré zanechajú nezabudnuteľný dojem.
            </p>
        </div>
    </section>

    {{-- About Section --}}
    <section class="bg-[#111111] py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col lg:flex-row items-center gap-16">
                {{-- Left --}}
                <div class="flex flex-col gap-7 flex-1">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                        <span class="text-bcz-red text-[12px] font-bold tracking-[2px]">O VYSTÚPENIACH</span>
                    </div>
                    <h2 class="font-display font-bold text-[32px] md:text-[44px] leading-[1.1] tracking-wide text-white">
                        Dynamické akrobatické<br>show pre každú príležitosť
                    </h2>
                    <p class="text-bcz-muted text-[16px] leading-[1.7]">
                        Naše vystúpenia kombinujú parkour, freerunning a akrobaciu do dychberúcich show, ktoré zaujmú publikum každého veku. Vystupujeme na firemných eventoch, festivaloch, otvoreniach obchodov a špeciálnych príležitostiach po celom Slovensku aj v zahraničí.
                    </p>
                    <p class="text-bcz-dim text-[15px] leading-[1.7]">
                        Každé vystúpenie je unikátne a prispôsobené vašim požiadavkám — od krátkych 5-minútových showtime aktov až po komplexné 30-minútové programy s hudbou, svetlami a interakciou s publikom.
                    </p>
                </div>

                {{-- Right --}}
                <div class="flex-1 w-full">
                    <div class="w-full h-[420px] rounded-2xl bg-[url('https://images.unsplash.com/photo-1762328020356-747b4f66b2bf?w=1080&q=80')] bg-cover bg-center"></div>
                </div>
            </div>
        </div>
    </section>

    {{-- What We Offer Section --}}
    <section class="bg-bcz-dark py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col items-center gap-12">
                {{-- Header --}}
                <div class="flex flex-col items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                        <span class="text-bcz-red text-[12px] font-bold tracking-[2px]">PONUKA</span>
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                    </div>
                    <h2 class="font-display font-bold text-[36px] md:text-[48px] tracking-wide text-white text-center">TYPY VYSTÚPENÍ</h2>
                    <p class="text-bcz-muted text-[16px] text-center">Prispôsobíme vystúpenie presne podľa vašich potrieb a typu podujatia</p>
                </div>

                {{-- Cards Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 w-full">
                    {{-- Card 1 --}}
                    <div class="bg-[#111111] border border-bcz-border rounded-2xl p-8 flex flex-col gap-5">
                        <div class="w-14 h-14 bg-bcz-red/10 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"/></svg>
                        </div>
                        <h3 class="font-display font-bold text-[26px] tracking-wide text-white">Festival Show</h3>
                        <p class="text-bcz-muted text-[14px] leading-[1.6]">Veľké pódiové vystúpenia pre festivaly a open-air eventy. Energické show s hudbou a svetlami pre tisícky divákov.</p>
                    </div>

                    {{-- Card 2 --}}
                    <div class="bg-[#111111] border border-bcz-border rounded-2xl p-8 flex flex-col gap-5">
                        <div class="w-14 h-14 bg-bcz-red/10 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="20" x="4" y="2" rx="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01"/><path d="M16 6h.01"/><path d="M12 6h.01"/><path d="M12 10h.01"/><path d="M12 14h.01"/><path d="M16 10h.01"/><path d="M16 14h.01"/><path d="M8 10h.01"/><path d="M8 14h.01"/></svg>
                        </div>
                        <h3 class="font-display font-bold text-[26px] tracking-wide text-white">Firemné Eventy</h3>
                        <p class="text-bcz-muted text-[14px] leading-[1.6]">Profesionálne corporate show pre firemné akcie, teambuildingy a konferencie. Elegantné a prispôsobené vašej značke.</p>
                    </div>

                    {{-- Card 3 --}}
                    <div class="bg-[#111111] border border-bcz-border rounded-2xl p-8 flex flex-col gap-5">
                        <div class="w-14 h-14 bg-bcz-red/10 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="6" r="3"/><path d="M8.12 8.12 12 12"/><path d="M20 4 8.12 15.88"/><circle cx="6" cy="18" r="3"/><path d="M14.8 14.8 20 20"/></svg>
                        </div>
                        <h3 class="font-display font-bold text-[26px] tracking-wide text-white">Otvorenia & Promá</h3>
                        <p class="text-bcz-muted text-[14px] leading-[1.6]">Efektné slávnostné otvorenia obchodov, nákupných centier a eventov. Nezabudnuteľný prvý dojem pre vašich zákazníkov.</p>
                    </div>

                    {{-- Card 4 --}}
                    <div class="bg-[#111111] border border-bcz-border rounded-2xl p-8 flex flex-col gap-5">
                        <div class="w-14 h-14 bg-bcz-red/10 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5.8 11.3 2 22l10.7-3.79"/><path d="M4 3h.01"/><path d="M22 8h.01"/><path d="M15 2h.01"/><path d="M22 20h.01"/><path d="m22 2-2.24.75a2.9 2.9 0 0 0-1.96 3.12c.1.86-.57 1.63-1.45 1.63h-.38c-.86 0-1.6.6-1.76 1.44L14 10"/><path d="m22 13-.82-.33c-.86-.34-1.82.2-1.98 1.11c-.11.63-.69 1.22-1.3 1.22H12c-2.17.03-3.3-.42-4.1-1.36C7 12.6 5.47 12.18 5 14.13"/></svg>
                        </div>
                        <h3 class="font-display font-bold text-[26px] tracking-wide text-white">Súkromné Podujatia</h3>
                        <p class="text-bcz-muted text-[14px] leading-[1.6]">Unikátne vystúpenia pre narodeniny, svadby, rozlúčky a ďalšie súkromné oslavy. Osobný prístup a show na mieru.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Stats Section --}}
    <section class="bg-[#0D0D0D] py-16">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-wrap justify-around items-center gap-8">
                <div class="flex flex-col items-center gap-2">
                    <span class="font-display font-bold text-[56px] text-bcz-red">100+</span>
                    <span class="text-bcz-muted text-[14px] font-medium tracking-wide">Vystúpení</span>
                </div>
                <div class="w-px h-20 bg-bcz-border hidden md:block"></div>
                <div class="flex flex-col items-center gap-2">
                    <span class="font-display font-bold text-[56px] text-bcz-red">50+</span>
                    <span class="text-bcz-muted text-[14px] font-medium tracking-wide">Festivalov</span>
                </div>
                <div class="w-px h-20 bg-bcz-border hidden md:block"></div>
                <div class="flex flex-col items-center gap-2">
                    <span class="font-display font-bold text-[56px] text-bcz-red">10+</span>
                    <span class="text-bcz-muted text-[14px] font-medium tracking-wide">Krajín</span>
                </div>
                <div class="w-px h-20 bg-bcz-border hidden md:block"></div>
                <div class="flex flex-col items-center gap-2">
                    <span class="font-display font-bold text-[56px] text-bcz-red">500k+</span>
                    <span class="text-bcz-muted text-[14px] font-medium tracking-wide">Divákov</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Recent Events Section --}}
    <section class="bg-[#111111] py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col gap-12">
                {{-- Header --}}
                <div class="flex items-end justify-between">
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-0.5 bg-bcz-red"></div>
                            <span class="text-bcz-red text-[12px] font-bold tracking-[2px]">PORTFÓLIO</span>
                        </div>
                        <h2 class="font-display font-bold text-[36px] md:text-[48px] tracking-wide text-white">Kde sme vystupovali</h2>
                    </div>
                    <a href="{{ route('archiv-podujati') }}" class="hidden md:flex border border-bcz-faint rounded-lg px-6 py-3 text-bcz-muted text-sm font-semibold hover:border-bcz-red hover:text-white transition-colors items-center gap-2">
                        Zobraziť všetky <span>→</span>
                    </a>
                </div>

                {{-- Events Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {{-- Event 1 --}}
                    <div class="bg-[#1A1A1A] border border-bcz-border rounded-2xl overflow-hidden">
                        <div class="w-full h-[220px] bg-[url('https://images.unsplash.com/photo-1768054481058-fee53b8bdfa1?w=1080&q=80')] bg-cover bg-center"></div>
                        <div class="flex flex-col gap-3 p-6">
                            <h3 class="font-display font-bold text-[28px] tracking-wide text-white">Grape Festival 2024</h3>
                            <p class="text-bcz-muted text-[14px] leading-[1.6]">Hlavné pódiové vystúpenie na najväčšom slovenskom hudobnom festivale. 30-minútová akrobatická show pred 15 000 divákmi.</p>
                        </div>
                    </div>

                    {{-- Event 2 --}}
                    <div class="bg-[#1A1A1A] border border-bcz-border rounded-2xl overflow-hidden">
                        <div class="w-full h-[220px] bg-[url('https://images.unsplash.com/photo-1751267638411-ef75bcb0d4d3?w=1080&q=80')] bg-cover bg-center"></div>
                        <div class="flex flex-col gap-3 p-6">
                            <h3 class="font-display font-bold text-[28px] tracking-wide text-white">Chill Open Air 2024</h3>
                            <p class="text-bcz-muted text-[14px] leading-[1.6]">Open-air festival v srdci Vysokých Tatier. Akrobatická show zasadená do nádhernej prírody s výhľadom na hory.</p>
                        </div>
                    </div>

                    {{-- Event 3 --}}
                    <div class="bg-[#1A1A1A] border border-bcz-border rounded-2xl overflow-hidden">
                        <div class="w-full h-[220px] bg-[url('https://images.unsplash.com/photo-1765464822981-9ed8a4b50817?w=1080&q=80')] bg-cover bg-center"></div>
                        <div class="flex flex-col gap-3 p-6">
                            <h3 class="font-display font-bold text-[28px] tracking-wide text-white">Red Bull Street Style 2023</h3>
                            <p class="text-bcz-muted text-[14px] leading-[1.6]">Špeciálne vystúpenie na prestížnej Red Bull akcii v centre Bratislavy. Kombinácia street workout a freerunning pred medzinárodným publikom.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="bg-bcz-dark py-20 px-5">
        <div class="max-w-[1440px] mx-auto lg:px-20">
            <div class="bg-[#111111] border border-bcz-border rounded-3xl p-16 md:p-20 flex flex-col items-center gap-8">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-[12px] font-bold tracking-[2px]">BOOKING</span>
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                </div>
                <h2 class="font-display font-bold text-[36px] md:text-[56px] leading-[1.05] tracking-wide text-white text-center">
                    ZAREZERVUJTE SI<br>VYSTÚPENIE
                </h2>
                <p class="text-bcz-muted text-[17px] leading-[1.7] text-center max-w-[600px]">
                    Chcete oživiť váš event akrobatickou show? Napíšte nám a spoločne vytvoríme nezabudnuteľný zážitok pre vaše publikum.
                </p>
                <div class="flex flex-col sm:flex-row items-center gap-4">
                    <a href="{{ route('kontakt') }}" class="bg-bcz-red px-10 py-4 rounded-lg text-white font-bold text-[14px] tracking-wide hover:bg-bcz-red/90 transition-colors">
                        KONTAKTUJTE NÁS
                    </a>
                    <a href="mailto:info@bfreak.sk" class="border border-bcz-faint px-10 py-4 rounded-lg text-white font-semibold text-[14px] hover:border-bcz-red transition-colors">
                        info@bfreak.sk
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
