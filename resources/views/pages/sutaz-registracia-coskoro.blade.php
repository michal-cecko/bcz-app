@extends('layouts.public')

@section('title', 'Registrácia čoskoro - World Freerunning Championship 2026 | BCZ Club')

@section('content')
    {{-- Hero Section --}}
    <section class="relative w-full h-[400px] md:h-[450px] lg:h-[500px] overflow-hidden">
        <div class="absolute inset-0 bg-bcz-dark"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-bcz-dark via-transparent to-bcz-dark"></div>

        <div class="relative w-full h-full flex flex-col items-center justify-center gap-6 max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 pt-[120px]">
            {{-- Breadcrumb --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="text-bcz-muted text-[11px] font-medium tracking-widest hover:text-white transition-colors">DOMOV</a>
                <span class="text-[#444444] text-[11px]">/</span>
                <span class="text-[#888888] text-[11px] font-medium tracking-widest">SÚŤAŽ</span>
                <span class="text-[#444444] text-[11px]">/</span>
                <span class="text-bcz-red text-[11px] font-medium tracking-widest">REGISTRÁCIA</span>
            </div>

            {{-- Badge --}}
            <div class="inline-flex items-center gap-2 bg-bcz-red/10 border border-bcz-red/20 rounded-full px-5 py-2">
                <div class="w-2 h-2 rounded-full bg-bcz-red animate-pulse"></div>
                <span class="text-bcz-red text-xs font-bold tracking-[2px]">REGISTRÁCIA ČOSKORO</span>
            </div>

            {{-- Title --}}
            <div class="flex flex-col items-center">
                <h1 class="font-display font-bold text-[32px] md:text-[48px] lg:text-[72px] leading-[0.95] tracking-wide text-center">WORLD FREERUNNING</h1>
                <h1 class="font-display font-bold text-[32px] md:text-[48px] lg:text-[72px] leading-[0.95] tracking-wide text-bcz-red text-center">CHAMPIONSHIP 2026</h1>
            </div>
        </div>
    </section>

    {{-- Countdown Section --}}
    <section class="bg-[#111111] py-16 lg:py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            <div class="flex flex-col items-center gap-10">
                {{-- Label --}}
                <span class="text-[#888888] text-xs font-bold tracking-[2px]">ODPOČET DO REGISTRÁCIE</span>

                {{-- Timer Boxes --}}
                <div class="flex flex-wrap justify-center items-center gap-4 lg:gap-6">
                    {{-- Days --}}
                    <div class="bg-[#1A1A1A] border border-[#222222] rounded-2xl p-6 lg:p-8 w-[120px] lg:w-[160px] flex flex-col items-center gap-2">
                        <span class="font-display font-bold text-[40px] lg:text-[64px] text-white leading-none">127</span>
                        <span class="text-[#888888] text-xs font-bold tracking-[2px]">DNÍ</span>
                    </div>

                    {{-- Colon --}}
                    <span class="hidden sm:block font-display font-bold text-[32px] lg:text-[48px] text-[#333333] leading-none">:</span>

                    {{-- Hours --}}
                    <div class="bg-[#1A1A1A] border border-[#222222] rounded-2xl p-6 lg:p-8 w-[120px] lg:w-[160px] flex flex-col items-center gap-2">
                        <span class="font-display font-bold text-[40px] lg:text-[64px] text-white leading-none">14</span>
                        <span class="text-[#888888] text-xs font-bold tracking-[2px]">HODÍN</span>
                    </div>

                    {{-- Colon --}}
                    <span class="hidden sm:block font-display font-bold text-[32px] lg:text-[48px] text-[#333333] leading-none">:</span>

                    {{-- Minutes --}}
                    <div class="bg-[#1A1A1A] border border-[#222222] rounded-2xl p-6 lg:p-8 w-[120px] lg:w-[160px] flex flex-col items-center gap-2">
                        <span class="font-display font-bold text-[40px] lg:text-[64px] text-white leading-none">32</span>
                        <span class="text-[#888888] text-xs font-bold tracking-[2px]">MINÚT</span>
                    </div>

                    {{-- Colon --}}
                    <span class="hidden sm:block font-display font-bold text-[32px] lg:text-[48px] text-[#333333] leading-none">:</span>

                    {{-- Seconds --}}
                    <div class="bg-[#1A1A1A] border border-[#222222] rounded-2xl p-6 lg:p-8 w-[120px] lg:w-[160px] flex flex-col items-center gap-2">
                        <span class="font-display font-bold text-[40px] lg:text-[64px] text-white leading-none">45</span>
                        <span class="text-[#888888] text-xs font-bold tracking-[2px]">SEKÚND</span>
                    </div>
                </div>

                {{-- Registration Date --}}
                <div class="flex flex-col items-center gap-2 mt-4">
                    <span class="text-[#888888] text-sm">Registrácia sa otvára</span>
                    <span class="font-display font-bold text-[24px] text-bcz-red tracking-wide">1. januára 2026</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Info Preview Section --}}
    <section class="bg-bcz-dark py-16 lg:py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
            {{-- Section Header --}}
            <div class="flex flex-col gap-4 mb-10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[3px]">INFORMÁCIE O SÚŤAŽI</span>
                </div>
                <h2 class="font-display font-bold text-[32px] leading-none tracking-wide text-white">Základné údaje</h2>
            </div>

            {{-- Info Cards Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
                {{-- Date Card --}}
                <div class="bg-[#111111] border border-[#222222] rounded-2xl p-6 lg:p-8 flex flex-col gap-4">
                    {{-- Calendar Icon --}}
                    <svg class="w-7 h-7 text-bcz-red" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                    </svg>
                    <span class="text-[#888888] text-[11px] font-bold tracking-[2px]">DÁTUM</span>
                    <span class="text-white text-lg font-semibold">15. - 17. marca 2026</span>
                </div>

                {{-- Location Card --}}
                <div class="bg-[#111111] border border-[#222222] rounded-2xl p-6 lg:p-8 flex flex-col gap-4">
                    {{-- Map Pin Icon --}}
                    <svg class="w-7 h-7 text-bcz-red" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                    </svg>
                    <span class="text-[#888888] text-[11px] font-bold tracking-[2px]">MIESTO</span>
                    <span class="text-white text-lg font-semibold">Bratislava, Slovensko</span>
                </div>

                {{-- Categories Card --}}
                <div class="bg-[#111111] border border-[#222222] rounded-2xl p-6 lg:p-8 flex flex-col gap-4">
                    {{-- Trophy Icon --}}
                    <svg class="w-7 h-7 text-bcz-red" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-4.5A3.375 3.375 0 0019.875 10.875h-.375a.375.375 0 01-.375-.375V6.75a.75.75 0 00-.75-.75H5.625a.75.75 0 00-.75.75v3.75a.375.375 0 01-.375.375h-.375A3.375 3.375 0 007.5 14.25v4.5"/>
                    </svg>
                    <span class="text-[#888888] text-[11px] font-bold tracking-[2px]">KATEGÓRIE</span>
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-bcz-red/10 text-bcz-red text-xs font-semibold px-3 py-1 rounded-full">Freestyle</span>
                        <span class="bg-blue-500/10 text-blue-400 text-xs font-semibold px-3 py-1 rounded-full">Speed</span>
                        <span class="bg-amber-500/10 text-amber-400 text-xs font-semibold px-3 py-1 rounded-full">Skill</span>
                    </div>
                </div>

                {{-- Organizer Card --}}
                <div class="bg-[#111111] border border-[#222222] rounded-2xl p-6 lg:p-8 flex flex-col gap-4">
                    {{-- Users Icon --}}
                    <svg class="w-7 h-7 text-bcz-red" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                    </svg>
                    <span class="text-[#888888] text-[11px] font-bold tracking-[2px]">ORGANIZÁTOR</span>
                    <span class="text-white text-lg font-semibold">BCZ Club</span>
                </div>
            </div>
        </div>
    </section>
@endsection
