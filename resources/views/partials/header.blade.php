{{-- Rebranding Banner --}}
<div class="w-full h-8 bg-[#1A1A1A] flex items-center justify-center gap-2 text-[11px]">
    <span class="text-bcz-muted hidden sm:inline">Nová značka, rovnaká vášeň:</span>
    <span class="text-bcz-dim hidden sm:inline">Street Workout Kysuce</span>
    <span class="text-bcz-muted font-bold hidden sm:inline">→</span>
    <img src="/logo/logo-horizontal-short-white.svg" alt="BCZ Club" class="h-3.5">
</div>

{{-- Header --}}
<header x-data="{ mobileOpen: false }" class="w-full bg-bcz-dark sticky top-0 z-50 border-b border-bcz-border/30">
    <div class="max-w-[1440px] mx-auto h-16 lg:h-20 flex items-center justify-between px-5 md:px-10 lg:px-20">
        <a href="/" class="flex items-center">
            <img src="/logo/logo-horizontal-short-white.svg" alt="BCZ Club" class="h-8 lg:h-11">
        </a>

        {{-- Desktop Nav --}}
        <nav class="hidden xl:flex items-center gap-6">
            <a href="{{ route('about') }}" class="text-bcz-muted text-xs font-medium tracking-widest hover:text-white transition-colors">O NÁS</a>
            <a href="#pillars" class="text-bcz-muted text-xs font-medium tracking-widest hover:text-white transition-colors">SÚŤAŽE</a>
            <a href="{{ route('treningy') }}" class="text-bcz-muted text-xs font-medium tracking-widest hover:text-white transition-colors">TRÉNINGY V ČADCI</a>
            <a href="#" class="text-bcz-muted text-xs font-medium tracking-widest hover:text-white transition-colors">TRÉNINGY V BANSKEJ BYSTRICI</a>
            <a href="#pillars" class="text-bcz-muted text-xs font-medium tracking-widest hover:text-white transition-colors">VYSTÚPENIA</a>
            <a href="#footer" class="text-bcz-muted text-xs font-medium tracking-widest hover:text-white transition-colors">KONTAKT</a>
        </nav>

        <div class="flex items-center gap-4">
            <a href="/admin" class="hidden md:block bg-bcz-red text-white text-xs font-bold tracking-widest px-7 py-3.5 hover:bg-red-700 transition-colors">
                PRIDAJ SA
            </a>

            {{-- Hamburger Button --}}
            <button @click="mobileOpen = !mobileOpen" class="xl:hidden flex flex-col justify-center items-center w-10 h-10 gap-1.5">
                <span :class="mobileOpen ? 'rotate-45 translate-y-[4px]' : ''" class="block w-6 h-0.5 bg-white transition-transform duration-300"></span>
                <span :class="mobileOpen ? 'opacity-0' : ''" class="block w-6 h-0.5 bg-white transition-opacity duration-300"></span>
                <span :class="mobileOpen ? '-rotate-45 -translate-y-[4px]' : ''" class="block w-6 h-0.5 bg-white transition-transform duration-300"></span>
            </button>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div
        x-show="mobileOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        x-cloak
        class="xl:hidden bg-bcz-dark border-t border-bcz-border/30 px-5 pb-6"
    >
        <nav class="flex flex-col gap-4 pt-4">
            <a href="{{ route('about') }}" class="text-bcz-muted text-sm font-medium tracking-widest hover:text-white transition-colors py-1">O NÁS</a>
            <a href="#pillars" class="text-bcz-muted text-sm font-medium tracking-widest hover:text-white transition-colors py-1">SÚŤAŽE</a>
            <a href="{{ route('treningy') }}" class="text-bcz-muted text-sm font-medium tracking-widest hover:text-white transition-colors py-1">TRÉNINGY V ČADCI</a>
            <a href="#" class="text-bcz-muted text-sm font-medium tracking-widest hover:text-white transition-colors py-1">TRÉNINGY V BANSKEJ BYSTRICI</a>
            <a href="#pillars" class="text-bcz-muted text-sm font-medium tracking-widest hover:text-white transition-colors py-1">VYSTÚPENIA</a>
            <a href="#footer" class="text-bcz-muted text-sm font-medium tracking-widest hover:text-white transition-colors py-1">KONTAKT</a>
            <a href="/admin" class="md:hidden bg-bcz-red text-white text-sm font-bold tracking-widest px-7 py-3.5 hover:bg-red-700 transition-colors text-center mt-2">
                PRIDAJ SA
            </a>
        </nav>
    </div>
</header>
