<style>
    @media (min-width: 1024px) {
        .join-wrapper { min-height: 0 !important; height: 60vh; }
    }
</style>
<div class="join-wrapper min-h-screen bg-bcz-dark flex flex-col lg:flex-row">
    {{-- Left Panel --}}
    <div class="hidden lg:block relative flex-shrink-0 overflow-hidden" style="width: 40%">
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1769284331730-9955c15e7dac?w=1080&q=80')"></div>
        <div class="absolute inset-0" style="background: linear-gradient(to top, #0A0A0AFF 0%, #0A0A0AFC 35%, #0A0A0AE0 55%, #0A0A0AA0 75%, #0A0A0A55 100%)"></div>
        <div class="absolute inset-0 flex flex-col justify-end p-12 gap-6">
            @if($team)
                @if($team->getFirstMediaUrl('logo'))
                    <img src="{{ $team->getFirstMediaUrl('logo') }}" alt="{{ $team->getTranslation('name', app()->getLocale()) }}" class="h-28 w-auto object-contain self-start">
                @endif
                <h2 class="font-display text-[48px] font-bold text-white leading-tight tracking-wide">{{ $team->getTranslation('name', app()->getLocale()) }}</h2>
                @if($team->getTranslation('story', app()->getLocale()))
                    <p class="text-bcz-muted text-[15px] leading-relaxed max-w-[420px]">{{ Str::limit(strip_tags($team->getTranslation('story', app()->getLocale())), 150) }}</p>
                @endif
                <div class="flex items-center gap-3 text-bcz-muted text-sm">
                    <svg class="w-5 h-5 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>{{ $team->members_count }} {{ $team->members_count === 1 ? 'člen' : 'členov' }}</span>
                </div>
            @else
                <h2 class="font-display text-[38px] font-bold text-white leading-tight tracking-wide">Pridaj sa k nám</h2>
                <p class="text-bcz-muted text-[15px] leading-relaxed max-w-[420px]">Pripoj sa a začni trénovať.</p>
            @endif
            <div class="flex flex-col gap-3">
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-bcz-red flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span class="text-bcz-muted text-sm">Sleduj tréningy a výsledky</span>
                </div>
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-bcz-red flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span class="text-bcz-muted text-sm">Prihlás sa na súťaže</span>
                </div>
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-bcz-red flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span class="text-bcz-muted text-sm">Sleduj svoju pozíciu v rebríčku</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Panel --}}
    <div class="flex-1 flex flex-col justify-center px-6 py-8 lg:px-20 overflow-y-auto">
        <div class="max-w-lg mx-auto w-full space-y-8">
            @if(! $team)
                <div class="bg-bcz-red/10 border border-bcz-red/30 rounded-xl p-4 text-center">
                    <p class="text-bcz-red text-sm font-semibold">Tím nie je dostupný.</p>
                </div>
            @else
                <div>
                    <h1 class="font-display text-4xl font-bold text-white tracking-wide">PRIDAJ SA</h1>
                    <p class="text-bcz-muted text-sm mt-2">Pripoj sa k tímu {{ $team->getTranslation('name', app()->getLocale()) }}</p>
                </div>

                {{-- Success states --}}
                @if($joinedDirectly)
                    <div class="bg-green-500/10 border border-green-500/30 rounded-xl p-6 text-center space-y-2">
                        <p class="text-green-500 text-sm font-semibold">Úspešne ste sa pripojili k tímu!</p>
                        <a href="/admin" class="text-bcz-red text-xs font-semibold hover:underline inline-block">Prejsť na dashboard &rarr;</a>
                    </div>
                @elseif($requestSent)
                    <div class="bg-green-500/10 border border-green-500/30 rounded-xl p-6 text-center space-y-2">
                        <p class="text-green-500 text-sm font-semibold">Žiadosť bola úspešne odoslaná!</p>
                        <p class="text-bcz-dim text-xs">Po schválení vám príde notifikácia na email.</p>
                    </div>
                @elseif($codeSuccess)
                    <div class="bg-green-500/10 border border-green-500/30 rounded-xl p-6 text-center space-y-2">
                        <p class="text-green-500 text-sm font-semibold">Úspešne ste sa pripojili k tímu!</p>
                        <a href="/admin" class="text-bcz-red text-xs font-semibold hover:underline inline-block">Prejsť na dashboard &rarr;</a>
                    </div>
                @else
                    {{-- Join / Request Section --}}
                    @if($team->join_mode === \App\Enums\TeamJoinModeEnum::OPEN)
                        @auth
                            <div class="space-y-3">
                                <button
                                    wire:click="joinDirectly"
                                    class="w-full bg-bcz-red text-white text-sm font-bold rounded-lg h-12 hover:bg-red-700 transition-colors"
                                >
                                    Pripojiť sa k tímu
                                </button>
                                @if($requestError)
                                    <p class="text-bcz-red text-xs text-center">{{ $requestError }}</p>
                                @endif
                            </div>
                        @else
                            <div class="space-y-4">
                                <div class="space-y-3">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <input
                                                type="text"
                                                wire:model="requestName"
                                                placeholder="Meno"
                                                class="w-full bg-[#111111] border border-bcz-border rounded-lg h-11 px-4 text-white text-sm placeholder:text-bcz-dim focus:outline-none focus:ring-1 focus:ring-bcz-red focus:border-bcz-red"
                                            >
                                            @error('requestName') <span class="text-bcz-red text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <input
                                                type="text"
                                                wire:model="requestSurname"
                                                placeholder="Priezvisko"
                                                class="w-full bg-[#111111] border border-bcz-border rounded-lg h-11 px-4 text-white text-sm placeholder:text-bcz-dim focus:outline-none focus:ring-1 focus:ring-bcz-red focus:border-bcz-red"
                                            >
                                            @error('requestSurname') <span class="text-bcz-red text-xs">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <input
                                        type="email"
                                        wire:model="requestEmail"
                                        placeholder="Váš email"
                                        class="w-full bg-[#111111] border border-bcz-border rounded-lg h-11 px-4 text-white text-sm placeholder:text-bcz-dim focus:outline-none focus:ring-1 focus:ring-bcz-red focus:border-bcz-red"
                                    >
                                    @error('requestEmail') <span class="text-bcz-red text-xs">{{ $message }}</span> @enderror
                                </div>
                                <button
                                    wire:click="submitGuestJoinRequest"
                                    class="w-full bg-bcz-red text-white text-sm font-bold rounded-lg h-12 hover:bg-red-700 transition-colors"
                                >
                                    Pripojiť sa
                                </button>
                                @if($requestError)
                                    <p class="text-bcz-red text-xs text-center">{{ $requestError }}</p>
                                @endif
                            </div>
                        @endauth
                    @else
                        {{-- Approval mode --}}
                        @auth
                            <div class="space-y-3">
                                <button
                                    wire:click="submitJoinRequest"
                                    class="w-full bg-bcz-red text-white text-sm font-bold rounded-lg h-12 hover:bg-red-700 transition-colors"
                                >
                                    Požiadať o pripojenie
                                </button>
                                @if($requestError)
                                    <p class="text-bcz-red text-xs text-center">{{ $requestError }}</p>
                                @endif
                            </div>
                        @else
                            @if($showRequestForm)
                                <div class="bg-[#111111] border border-bcz-border rounded-xl p-5 space-y-4">
                                    <p class="text-white text-sm font-semibold">Vyplňte vaše údaje</p>
                                    <div class="space-y-3">
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <input
                                                    type="text"
                                                    wire:model="requestName"
                                                    placeholder="Meno"
                                                    class="w-full bg-[#111111] border border-bcz-border rounded-lg h-11 px-4 text-white text-sm placeholder:text-bcz-dim focus:outline-none focus:ring-1 focus:ring-bcz-red focus:border-bcz-red"
                                                >
                                                @error('requestName') <span class="text-bcz-red text-xs">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <input
                                                    type="text"
                                                    wire:model="requestSurname"
                                                    placeholder="Priezvisko"
                                                    class="w-full bg-[#111111] border border-bcz-border rounded-lg h-11 px-4 text-white text-sm placeholder:text-bcz-dim focus:outline-none focus:ring-1 focus:ring-bcz-red focus:border-bcz-red"
                                                >
                                                @error('requestSurname') <span class="text-bcz-red text-xs">{{ $message }}</span> @enderror
                                            </div>
                                        </div>

                                        <input
                                            type="email"
                                            wire:model="requestEmail"
                                            placeholder="Váš email"
                                            class="w-full bg-[#111111] border border-bcz-border rounded-lg h-11 px-4 text-white text-sm placeholder:text-bcz-dim focus:outline-none focus:ring-1 focus:ring-bcz-red focus:border-bcz-red"
                                        >
                                        @error('requestEmail') <span class="text-bcz-red text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <button
                                        wire:click="submitJoinRequest"
                                        class="w-full bg-bcz-red text-white text-sm font-bold rounded-lg h-11 hover:bg-red-700 transition-colors"
                                    >
                                        Odoslať žiadosť
                                    </button>
                                    @if($requestError)
                                        <p class="text-bcz-red text-xs text-center">{{ $requestError }}</p>
                                    @endif
                                </div>
                            @else
                                <div class="space-y-3">
                                    <button
                                        wire:click="showGuestRequestForm"
                                        class="w-full bg-bcz-red text-white text-sm font-bold rounded-lg h-12 hover:bg-red-700 transition-colors"
                                    >
                                        Požiadať o pripojenie
                                    </button>
                                </div>
                            @endif
                        @endauth
                    @endif

                    @if($team->join_mode !== \App\Enums\TeamJoinModeEnum::OPEN)
                        {{-- Divider --}}
                        <div class="flex items-center gap-4">
                            <div class="flex-1 h-px bg-bcz-border"></div>
                            <span class="text-bcz-dim text-xs">alebo</span>
                            <div class="flex-1 h-px bg-bcz-border"></div>
                        </div>

                        {{-- Invitation Code --}}
                        <div class="space-y-3">
                            <label class="text-bcz-lighter text-[13px] font-semibold">Pozývací kód</label>
                            <div class="flex items-center gap-3">
                                <div class="flex-1 flex items-center gap-3 bg-[#111111] border border-bcz-border rounded-lg h-12 px-4">
                                    <svg class="w-[18px] h-[18px] text-bcz-dim flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                    <input
                                        type="text"
                                        wire:model="inviteCode"
                                        placeholder="Zadaj pozývací kód..."
                                        class="bg-transparent border-none text-white text-sm placeholder:text-bcz-dim focus:outline-none focus:ring-0 w-full"
                                    >
                                </div>
                                <button
                                    wire:click="redeemCode"
                                    class="bg-bcz-red text-white text-[13px] font-bold rounded-lg h-12 px-7 hover:bg-red-700 transition-colors flex-shrink-0"
                                >
                                    Pripojiť sa
                                </button>
                            </div>
                            @if($codeError)
                                <p class="text-bcz-red text-xs">{{ $codeError }}</p>
                            @endif
                        </div>
                    @endif
                @endif

                {{-- Login link for guests --}}
                @guest
                    <div class="text-center pt-4">
                        <span class="text-bcz-dim text-sm">Už máš účet?</span>
                        <a href="/admin/login" class="text-bcz-red text-sm font-semibold hover:underline ml-1.5">Prihlásiť sa</a>
                    </div>
                @endguest
            @endif
        </div>
    </div>
</div>
