{{-- Rebranding Banner --}}
@php
    $topbarShowUntil = \App\Models\Setting::get('topbar_show_until');
@endphp
@if($topbarShowUntil && now()->lte(\Carbon\Carbon::parse($topbarShowUntil)))
    <div class="w-full h-8 bg-[#1A1A1A] flex items-center justify-center gap-2 text-[11px]">
        <span class="text-bcz-muted hidden sm:inline">Nová značka, rovnaká vášeň:</span>
        <span class="text-bcz-dim hidden sm:inline">Street Workout Kysuce</span>
        <span class="text-bcz-muted font-bold hidden sm:inline">→</span>
        <img src="/logo/logo-horizontal-short-white.svg" alt="BCZ Club" class="h-3.5">
    </div>
@endif

{{-- Header --}}
<header x-data="{ mobileOpen: false, ctaOpen: false }" class="w-full bg-bcz-dark sticky top-0 z-50 border-b border-bcz-border/30">
    <div class="max-w-[1440px] mx-auto h-16 lg:h-20 flex items-center justify-between px-5 md:px-10 lg:px-20">
        <a href="{{ locale_url('/') }}" class="flex items-center">
            <img src="/logo/logo-horizontal-short-white.svg" alt="BCZ Club" class="h-8 lg:h-11">
        </a>

        {{-- Desktop Nav --}}
        <nav class="hidden xl:flex items-center gap-6">
            @foreach(collect($headerMenu->items ?? [])->sortBy('sort_order') as $item)
                <a href="{{ \App\Services\LinkResolver::resolve($item) ?? ($item['url'] ?? '#') }}" target="{{ $item['target'] ?? '_self' }}" class="text-bcz-muted text-xs font-medium tracking-widest uppercase hover:text-white transition-colors">
                    {{ $item['label_' . app()->getLocale()] ?? $item['label_sk'] }}
                </a>
            @endforeach
        </nav>

        <div class="flex items-center gap-4">
            <div class="hidden xl:block">
                <x-locale-switcher />
            </div>
            <a href="/admin/login" class="hidden md:block text-bcz-muted text-xs font-medium tracking-widest hover:text-white transition-colors">
                Prihlásiť sa
            </a>
            <button @click="ctaOpen = true" class="hidden md:block bg-bcz-red text-white text-xs font-bold tracking-widest px-7 py-3.5 hover:bg-red-700 transition-colors">
                PRIDAJ SA
            </button>

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
            @foreach(collect($headerMenu->items ?? [])->sortBy('sort_order') as $item)
                <a href="{{ \App\Services\LinkResolver::resolve($item) ?? ($item['url'] ?? '#') }}" target="{{ $item['target'] ?? '_self' }}" class="text-bcz-muted text-sm font-medium tracking-widest uppercase hover:text-white transition-colors py-1">
                    {{ $item['label_' . app()->getLocale()] ?? $item['label_sk'] }}
                </a>
            @endforeach
            <button @click="ctaOpen = true; mobileOpen = false" class="md:hidden bg-bcz-red text-white text-sm font-bold tracking-widest px-7 py-3.5 hover:bg-red-700 transition-colors text-center mt-2 w-full">
                PRIDAJ SA
            </button>
            <div class="xl:hidden pt-2">
                <x-locale-switcher />
            </div>
        </nav>
    </div>
    {{-- CTA Modal - Pridaj sa (teleported to body to escape header stacking context) --}}
    <template x-teleport="body">
        <div
            x-show="ctaOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            x-cloak
            @keydown.escape.window="ctaOpen = false"
            style="position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.15);backdrop-filter:blur(4px);z-index:100;"
        class="flex items-center justify-center"
        >
            <div
                @click.outside="ctaOpen = false"
                x-show="ctaOpen"
                x-transition:enter="transition ease-out duration-300 delay-100"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="bg-[#111111] border border-bcz-border rounded-2xl p-10 pb-8 w-[580px] max-w-[calc(100vw-2rem)]"
            >
                {{-- Close Button --}}
                <div class="flex justify-end mb-2">
                    <button @click="ctaOpen = false" class="w-9 h-9 flex items-center justify-center bg-[#1A1A1A] rounded-lg hover:bg-bcz-border transition-colors">
                        <svg class="w-4 h-4 text-bcz-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Title --}}
                <div class="text-center mb-8 space-y-2">
                    <h2 class="font-display text-[32px] font-bold text-white tracking-wide">Začni svoju cestu</h2>
                    <p class="text-bcz-muted text-[15px]">Vyber si, ako chceš začať</p>
                </div>

                {{-- Cards --}}
                <div class="grid grid-cols-2 gap-4 mb-6">
                    {{-- Join Team Card --}}
                    <a href="{{ locale_url('/pridaj-sa') }}" class="group flex flex-col items-center text-center gap-4 bg-bcz-dark border border-bcz-border rounded-xl p-7 hover:border-bcz-red hover:bg-[#0F0F0F] transition-colors">
                        <div class="w-14 h-14 rounded-xl bg-white/[.03] group-hover:bg-bcz-red/10 flex items-center justify-center transition-colors">
                            <svg class="w-7 h-7 text-bcz-lighter group-hover:text-bcz-red transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div class="space-y-2">
                            <p class="font-display text-[22px] font-bold text-white">Pripojiť sa k tímu</p>
                            <p class="text-bcz-muted text-[13px] leading-relaxed">Nájdi svoj tím a pridaj sa ako športovec</p>
                        </div>
                    </a>

                    {{-- Create Team Card --}}
                    <a href="{{ locale_url('/registracia') }}" class="group flex flex-col items-center text-center gap-4 bg-bcz-dark border border-bcz-border rounded-xl p-7 hover:border-bcz-red hover:bg-[#0F0F0F] transition-colors">
                        <div class="w-14 h-14 rounded-xl bg-white/[.03] group-hover:bg-bcz-red/10 flex items-center justify-center transition-colors">
                            <svg class="w-7 h-7 text-bcz-lighter group-hover:text-bcz-red transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="space-y-2">
                            <p class="font-display text-[22px] font-bold text-white">Vytvoriť nový tím</p>
                            <p class="text-bcz-muted text-[13px] leading-relaxed">Zaregistruj svoj tím a začni spravovať tréningy</p>
                        </div>
                    </a>
                </div>

                {{-- Login Link --}}
                <div class="text-center pt-6">
                    <span class="text-bcz-dim text-sm">Už máš účet?</span>
                    <a href="/admin/login" class="text-bcz-red text-sm font-semibold hover:underline ml-1.5">Prihlásiť sa</a>
                </div>
            </div>
        </div>
    </template>
</header>
