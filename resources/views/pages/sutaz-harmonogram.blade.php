@extends('layouts.public')

@section('title', 'Harmonogram - World Freerunning Championship 2026 - BCZ Club')

@section('content')
    {{-- Hero Section --}}
    <section class="relative w-full h-[400px] md:h-[450px] lg:h-[500px] overflow-hidden">
        <div class="absolute inset-0 bg-bcz-dark"></div>
        <div class="absolute inset-0" style="background: linear-gradient(180deg, #0A0A0A 0%, #0A0A0A88 40%, #0A0A0A88 60%, #0A0A0A 100%)"></div>

        <div class="relative w-full h-full flex flex-col justify-center max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            {{-- Breadcrumb --}}
            <div class="flex items-center gap-2 text-[11px] mb-6">
                <a href="{{ route('home') }}" class="text-[#888888] tracking-wider hover:text-white transition-colors">DOMOV</a>
                <span class="text-[#666666]">&gt;</span>
                <span class="text-[#888888] tracking-wider">SUTAZE</span>
                <span class="text-[#666666]">&gt;</span>
                <span class="text-bcz-red tracking-wider">WORLD FREERUNNING CHAMPIONSHIP 2026</span>
            </div>

            {{-- Badge --}}
            <span class="bg-bcz-red/20 text-bcz-red text-[11px] font-bold tracking-[2px] px-4 py-2 rounded w-fit mb-5">KOMBINOVANY FORMAT</span>

            {{-- Title --}}
            <h1 class="font-display font-bold text-[48px] md:text-[64px] lg:text-[72px] tracking-wide leading-none">WORLD FREERUNNING<br>CHAMPIONSHIP 2026</h1>
        </div>
    </section>

    {{-- Tab Bar --}}
    <section class="bg-[#111111] border-b border-[#222222]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex gap-0 overflow-x-auto">
                <a href="{{ route('sutaz.popis') }}" class="px-6 py-4 text-[#888888] text-[13px] font-bold tracking-wider border-b-2 border-transparent hover:text-white transition-colors whitespace-nowrap">POPIS</a>
                <a href="{{ route('sutaz.harmonogram') }}" class="px-6 py-4 text-white text-[13px] font-bold tracking-wider border-b-2 border-bcz-red whitespace-nowrap">HARMONOGRAM</a>
                <a href="{{ route('sutaz.vysledky') }}" class="px-6 py-4 text-[#888888] text-[13px] font-bold tracking-wider border-b-2 border-transparent hover:text-white transition-colors whitespace-nowrap">VYSLEDKY</a>
                <a href="{{ route('sutaz.registracia') }}" class="px-6 py-4 text-[#888888] text-[13px] font-bold tracking-wider border-b-2 border-transparent hover:text-white transition-colors whitespace-nowrap">REGISTRACIA</a>
            </div>
        </div>
    </section>

    {{-- Info Strip --}}
    <section class="bg-[#111111] border-b border-[#222222]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 py-6">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                {{-- Date --}}
                <div class="bg-[#1A1A1A] rounded-lg p-4 flex items-center gap-3">
                    <div class="size-10 bg-bcz-red/[0.13] rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[#666666] text-[10px] font-medium tracking-wider">DATUM</span>
                        <span class="text-white text-sm font-semibold">15. marca 2026</span>
                    </div>
                </div>

                {{-- Location --}}
                <div class="bg-[#1A1A1A] rounded-lg p-4 flex items-center gap-3">
                    <div class="size-10 bg-bcz-red/[0.13] rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[#666666] text-[10px] font-medium tracking-wider">MIESTO</span>
                        <span class="text-white text-sm font-semibold">Bratislava, SK</span>
                    </div>
                </div>

                {{-- Format --}}
                <div class="bg-[#1A1A1A] rounded-lg p-4 flex items-center gap-3">
                    <div class="size-10 bg-bcz-red/[0.13] rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[#666666] text-[10px] font-medium tracking-wider">FORMAT</span>
                        <span class="text-white text-sm font-semibold">Kombinovany</span>
                    </div>
                </div>

                {{-- Participants --}}
                <div class="bg-[#1A1A1A] rounded-lg p-4 flex items-center gap-3">
                    <div class="size-10 bg-bcz-red/[0.13] rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[#666666] text-[10px] font-medium tracking-wider">UCASTNIKOV</span>
                        <span class="text-white text-sm font-semibold">128 atletov</span>
                    </div>
                </div>

                {{-- Status --}}
                <div class="bg-[#1A1A1A] rounded-lg p-4 flex items-center gap-3">
                    <div class="size-10 bg-[#22C55E]/[0.13] rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-[#22C55E]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[#666666] text-[10px] font-medium tracking-wider">STAV</span>
                        <span class="text-[#22C55E] text-sm font-semibold">Probiehajuca</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Live Status Bar --}}
    <section class="bg-[#111111] py-8">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col gap-6">
                {{-- Top row --}}
                <div class="flex flex-col md:flex-row md:items-center gap-4 md:gap-6">
                    {{-- LIVE badge --}}
                    <div class="flex items-center gap-2.5 bg-bcz-red rounded px-4 py-2.5 w-fit shrink-0">
                        <span class="relative flex size-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                            <span class="relative inline-flex rounded-full size-2.5 bg-white"></span>
                        </span>
                        <span class="text-white text-[11px] font-bold tracking-[2px]">LIVE</span>
                    </div>

                    {{-- Current event --}}
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 flex-1">
                        <span class="text-white text-base font-semibold">Kvalifikacia &ndash; Kolo 2 | Muzi do 80kg</span>
                        <div class="flex items-center gap-3">
                            <span class="text-[#888888] text-sm font-medium">11:45</span>
                            <span class="bg-[#FF2D2D]/20 text-bcz-red text-[11px] font-bold tracking-wider px-3 py-1.5 rounded">+15 min oneskorenie</span>
                        </div>
                    </div>
                </div>

                {{-- Progress bar --}}
                <div class="flex flex-col gap-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[#888888] text-[11px] font-medium tracking-wider">PRIEBEH DNA</span>
                        <span class="text-white text-[13px] font-bold">45%</span>
                    </div>
                    <div class="w-full h-2 bg-[#222222] rounded-full overflow-hidden">
                        <div class="h-full bg-bcz-red rounded-full" style="width: 45%"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Schedule Timeline --}}
    <section class="bg-bcz-dark py-16 md:py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            {{-- Section header --}}
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10 md:mb-14">
                <div class="flex flex-col gap-2">
                    <h2 class="font-display font-bold text-[28px] tracking-wide">HARMONOGRAM DNA</h2>
                </div>
                <span class="text-[#888888] text-sm font-medium">Sobota, 15. marca 2026</span>
            </div>

            {{-- Timeline --}}
            @php
                $items = [
                    ['time' => '08:00', 'name' => 'Registracia', 'desc' => 'Prezentacia ucastnikov, kontrola dokladov a pridelenie startovnych cisel.', 'status' => 'past'],
                    ['time' => '09:00', 'name' => 'Kvalifikacia &ndash; Kolo 1', 'desc' => 'Freestyle run pre vsetky kategorie. Kazdy zavodnik ma 60 sekund na predvedenie zostavy.', 'status' => 'past'],
                    ['time' => '10:30', 'name' => 'Kvalifikacia &ndash; Kolo 2', 'desc' => 'Druhe kolo kvalifikacie. Muzi do 80kg a zeny. Bodovanie podla obtaznosti a cistoty vykonavania.', 'status' => 'current'],
                    ['time' => '12:00', 'name' => 'Obedna prestavka', 'desc' => 'Prestavka na obed a regeneraciu. K dispozicii food zony a odpocinkova zona.', 'status' => 'future'],
                    ['time' => '12:45', 'name' => 'Kvalifikacia &ndash; Battle', 'desc' => '1v1 battle format. Priame suboje medzi zavodnikmi, rozhodcovia hlasuju v realnom case.', 'status' => 'future'],
                    ['time' => '14:30', 'name' => 'Semifinale', 'desc' => 'Top 16 zavodnikov z kvalifikacie. Zvysene naroky na obtaznost a originalitu.', 'status' => 'future'],
                    ['time' => '16:00', 'name' => 'Freestyle &ndash; Finale', 'desc' => 'Top 8 zavodnikov bojuje o tituly. 90 sekundove zostavy s maximalnou obtaznostou.', 'status' => 'future'],
                    ['time' => '17:30', 'name' => 'Vyhlasenie vysledkov', 'desc' => 'Slavnostne vyhlasenie vitazov, odovzdavanie medaili a cien pre vsetky kategorie.', 'status' => 'future'],
                ];
            @endphp

            <div class="flex flex-col">
                @foreach($items as $index => $item)
                    @php
                        $isLast = $index === count($items) - 1;
                        $timeColor = match($item['status']) {
                            'past' => 'text-[#22C55E]',
                            'current' => 'text-bcz-red',
                            'future' => 'text-[#555555]',
                        };
                        $dotBg = match($item['status']) {
                            'past' => 'bg-[#22C55E]',
                            'current' => 'bg-bcz-red',
                            'future' => 'bg-[#333333]',
                        };
                        $lineBg = match($item['status']) {
                            'past' => 'bg-[#22C55E]/30',
                            'current' => 'bg-bcz-red/30',
                            'future' => 'bg-[#222222]',
                        };
                        $nameColor = $item['status'] === 'current' ? 'text-white' : ($item['status'] === 'past' ? 'text-[#AAAAAA]' : 'text-[#666666]');
                        $descColor = $item['status'] === 'current' ? 'text-[#AAAAAA]' : ($item['status'] === 'past' ? 'text-[#666666]' : 'text-[#444444]');
                        $cardBorder = $item['status'] === 'current' ? 'border-bcz-red/30 bg-bcz-red/[0.04]' : 'border-[#1A1A1A]';
                    @endphp

                    <div class="flex gap-4 md:gap-8">
                        {{-- Time column --}}
                        <div class="w-[60px] md:w-[80px] shrink-0 pt-5">
                            <span class="{{ $timeColor }} font-display font-bold text-lg md:text-xl tracking-wide">{{ $item['time'] }}</span>
                        </div>

                        {{-- Dot & line --}}
                        <div class="flex flex-col items-center shrink-0">
                            <div class="mt-5 relative">
                                @if($item['status'] === 'current')
                                    <span class="absolute -inset-1.5 rounded-full bg-bcz-red/30 animate-ping"></span>
                                    <span class="relative block size-3.5 {{ $dotBg }} rounded-full ring-4 ring-bcz-red/20"></span>
                                @else
                                    <span class="block size-3.5 {{ $dotBg }} rounded-full"></span>
                                @endif
                            </div>
                            @if(!$isLast)
                                <div class="w-0.5 flex-1 min-h-[20px] {{ $lineBg }}"></div>
                            @endif
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 pb-8 {{ $isLast ? '' : '' }}">
                            <div class="rounded-lg border {{ $cardBorder }} p-5">
                                <div class="flex flex-col gap-1.5">
                                    <div class="flex items-center gap-3">
                                        <span class="{{ $nameColor }} text-base md:text-lg font-semibold">{!! $item['name'] !!}</span>
                                        @if($item['status'] === 'current')
                                            <span class="bg-bcz-red/20 text-bcz-red text-[10px] font-bold tracking-wider px-2.5 py-1 rounded">PRAVE PREBIEHA</span>
                                        @elseif($item['status'] === 'past')
                                            <span class="bg-[#22C55E]/10 text-[#22C55E] text-[10px] font-bold tracking-wider px-2.5 py-1 rounded">DOKONCENE</span>
                                        @endif
                                    </div>
                                    <p class="{{ $descColor }} text-[13px] md:text-sm leading-[1.6]">{!! $item['desc'] !!}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
