@extends('layouts.public')

@section('title', 'Archív tréningov - BCZ Club')

@section('content')
    {{-- Hero Section --}}
    <section class="bg-bcz-dark py-[60px]">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col items-center gap-6">
        {{-- Breadcrumb --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('home') }}" class="text-[#666666] text-[11px] font-medium tracking-[2px] hover:text-white transition-colors">DOMOV</a>
            <span class="text-[#444444] text-[11px]">/</span>
            <a href="{{ route('treningy') }}" class="text-[#666666] text-[11px] font-medium tracking-[2px] hover:text-white transition-colors">TRÉNINGY</a>
            <span class="text-[#444444] text-[11px]">/</span>
            <span class="text-bcz-red text-[11px] font-medium tracking-[2px]">ARCHÍV</span>
        </div>

        {{-- Title --}}
        <h1 class="font-display font-bold text-[28px] md:text-[40px] lg:text-[56px] tracking-wide text-center">ARCHÍV TRÉNINGOV</h1>

        {{-- Subtitle --}}
        <p class="text-[#888888] text-[18px] text-center max-w-[600px]">
            Nájdi si tréning, ktorý ti vyhovuje - podľa kategórie, dňa alebo miesta konania
        </p>
        </div>
    </section>

    {{-- Filter Section --}}
    <section class="bg-[#111111] py-10">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
        <div class="flex flex-col gap-6">
            {{-- Filter Header --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-bcz-red">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="21" x2="14" y1="4" y2="4"/><line x1="10" x2="3" y1="4" y2="4"/><line x1="21" x2="12" y1="12" y2="12"/><line x1="8" x2="3" y1="12" y2="12"/><line x1="21" x2="16" y1="20" y2="20"/><line x1="12" x2="3" y1="20" y2="20"/><line x1="14" x2="14" y1="2" y2="6"/><line x1="8" x2="8" y1="10" y2="14"/><line x1="16" x2="16" y1="18" y2="22"/>
                    </svg>
                    <span class="text-sm font-semibold tracking-wide">FILTROVAŤ</span>
                </div>
                <button class="flex items-center gap-2 text-[#888888] hover:text-white transition-colors">
                    <span class="text-sm">Zrušiť filtre</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Filter Dropdowns --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Kategória --}}
                <div class="flex flex-col gap-2">
                    <label class="text-[#888888] text-[12px]">Kategória</label>
                    <button class="bg-bcz-dark border border-[#333333] rounded-lg p-3.5 px-4 flex justify-between items-center text-white text-sm">
                        <span>Všetky kategórie</span>
                        <svg class="w-4 h-4 text-[#888888]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6"/>
                        </svg>
                    </button>
                </div>

                {{-- Deň --}}
                <div class="flex flex-col gap-2">
                    <label class="text-[#888888] text-[12px]">Deň</label>
                    <button class="bg-bcz-dark border border-[#333333] rounded-lg p-3.5 px-4 flex justify-between items-center text-white text-sm">
                        <span>Všetky dni</span>
                        <svg class="w-4 h-4 text-[#888888]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6"/>
                        </svg>
                    </button>
                </div>

                {{-- Miesto --}}
                <div class="flex flex-col gap-2">
                    <label class="text-[#888888] text-[12px]">Miesto</label>
                    <button class="bg-bcz-dark border border-[#333333] rounded-lg p-3.5 px-4 flex justify-between items-center text-white text-sm">
                        <span>Všetky miesta</span>
                        <svg class="w-4 h-4 text-[#888888]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Active Filters --}}
            <div class="flex flex-wrap items-center gap-3 md:gap-4">
                <span class="bg-bcz-red/20 text-bcz-red rounded-full px-3 py-2 text-xs flex items-center gap-2">
                    Parkour
                    <svg class="w-3 h-3 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                    </svg>
                </span>
                <span class="bg-bcz-red/20 text-bcz-red rounded-full px-3 py-2 text-xs flex items-center gap-2">
                    Pondelok
                    <svg class="w-3 h-3 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                    </svg>
                </span>
                <span class="bg-bcz-red/20 text-bcz-red rounded-full px-3 py-2 text-xs flex items-center gap-2">
                    Čadca
                    <svg class="w-3 h-3 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                    </svg>
                </span>
            </div>
        </div>
        </div>
    </section>

    {{-- Results Section --}}
    <section class="bg-bcz-dark py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
        <div class="flex flex-col gap-8">
            {{-- Results Header --}}
            <div class="flex items-center justify-between">
                <span class="text-[#888888] text-sm">Nájdených 12 tréningov</span>
                <button class="flex items-center gap-2 text-white text-sm">
                    <span class="text-[#888888]">Zoradiť podľa:</span>
                    <span class="font-semibold">Dátum</span>
                    <svg class="w-4 h-4 text-[#888888]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m6 9 6 6 6-6"/>
                    </svg>
                </button>
            </div>

            {{-- Training Cards Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Card 1: Parkour Basics --}}
                <div class="bg-[#111111] border border-[#222222] rounded-xl overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1654703680091-010855f522e8?w=800&q=80" alt="Parkour Basics" class="w-full h-[180px] object-cover">
                    <div class="p-6 flex flex-col gap-4">
                        <div class="flex items-center gap-2">
                            <span class="bg-bcz-red/20 text-bcz-red text-[10px] font-bold px-3 py-1.5 rounded">Parkour</span>
                            <span class="bg-[#222222] text-[#888888] text-[10px] font-bold px-3 py-1.5 rounded">Začiatočníci</span>
                        </div>
                        <h3 class="text-white text-[20px] font-semibold">Parkour Basics</h3>
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center gap-2 text-[#888888] text-[14px]">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/>
                                </svg>
                                <span>Pondelok, Streda &middot; 16:00 - 17:30</span>
                            </div>
                            <div class="flex items-center gap-2 text-[#888888] text-[14px]">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>
                                </svg>
                                <span>Čadca &middot; Športová hala</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-2">
                            <div class="flex items-center gap-2 text-[#888888] text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                                <span>8/12 miest</span>
                            </div>
                            <a href="#" class="bg-bcz-red rounded-md px-5 py-2.5 text-sm font-semibold text-white hover:bg-bcz-red/90 transition-colors">Rezervovať</a>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Street Workout Advanced --}}
                <div class="bg-[#111111] border border-[#222222] rounded-xl overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1712881387628-9db0b5ebd726?w=800&q=80" alt="Street Workout Advanced" class="w-full h-[180px] object-cover">
                    <div class="p-6 flex flex-col gap-4">
                        <div class="flex items-center gap-2">
                            <span class="bg-[#3B82F6]/20 text-[#3B82F6] text-[10px] font-bold px-3 py-1.5 rounded">Street Workout</span>
                            <span class="bg-[#222222] text-[#888888] text-[10px] font-bold px-3 py-1.5 rounded">Pokročilí</span>
                        </div>
                        <h3 class="text-white text-[20px] font-semibold">Street Workout Advanced</h3>
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center gap-2 text-[#888888] text-[14px]">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/>
                                </svg>
                                <span>Utorok, Štvrtok &middot; 18:00 - 19:30</span>
                            </div>
                            <div class="flex items-center gap-2 text-[#888888] text-[14px]">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>
                                </svg>
                                <span>Bratislava &middot; Outdoor park</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-2">
                            <div class="flex items-center gap-2 text-[#888888] text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                                <span>10/15 miest</span>
                            </div>
                            <a href="#" class="bg-bcz-red rounded-md px-5 py-2.5 text-sm font-semibold text-white hover:bg-bcz-red/90 transition-colors">Rezervovať</a>
                        </div>
                    </div>
                </div>

                {{-- Card 3: Calisthenics Fundamentals --}}
                <div class="bg-[#111111] border border-[#222222] rounded-xl overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1754258167836-6878c54e316c?w=800&q=80" alt="Calisthenics Fundamentals" class="w-full h-[180px] object-cover">
                    <div class="p-6 flex flex-col gap-4">
                        <div class="flex items-center gap-2">
                            <span class="bg-[#22C55E]/20 text-[#22C55E] text-[10px] font-bold px-3 py-1.5 rounded">Kalistenika</span>
                            <span class="bg-[#222222] text-[#888888] text-[10px] font-bold px-3 py-1.5 rounded">Všetky úrovne</span>
                        </div>
                        <h3 class="text-white text-[20px] font-semibold">Calisthenics Fundamentals</h3>
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center gap-2 text-[#888888] text-[14px]">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/>
                                </svg>
                                <span>Streda, Piatok &middot; 17:00 - 18:30</span>
                            </div>
                            <div class="flex items-center gap-2 text-[#888888] text-[14px]">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>
                                </svg>
                                <span>Žilina &middot; Mestský park</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-2">
                            <div class="flex items-center gap-2 text-[#888888] text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                                <span>6/10 miest</span>
                            </div>
                            <a href="#" class="bg-bcz-red rounded-md px-5 py-2.5 text-sm font-semibold text-white hover:bg-bcz-red/90 transition-colors">Rezervovať</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pagination --}}
            <div class="flex items-center justify-center gap-2 pt-8">
                <span class="bg-bcz-red text-white w-10 h-10 rounded-lg flex items-center justify-center font-semibold text-sm">1</span>
                <span class="bg-[#111111] border border-[#333333] text-[#888888] w-10 h-10 rounded-lg flex items-center justify-center text-sm cursor-pointer hover:border-[#555555] transition-colors">2</span>
                <span class="bg-[#111111] border border-[#333333] text-[#888888] w-10 h-10 rounded-lg flex items-center justify-center text-sm cursor-pointer hover:border-[#555555] transition-colors">3</span>
                <span class="bg-[#111111] border border-[#333333] text-[#888888] w-10 h-10 rounded-lg flex items-center justify-center cursor-pointer hover:border-[#555555] transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m9 18 6-6-6-6"/>
                    </svg>
                </span>
            </div>
        </div>
        </div>
    </section>
@endsection
