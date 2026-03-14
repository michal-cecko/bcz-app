<div class="min-h-screen bg-bcz-dark flex flex-col lg:flex-row">
    {{-- Left Panel --}}
    <div class="hidden lg:block w-[560px] relative flex-shrink-0 overflow-hidden">
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1769284331730-9955c15e7dac?w=1080&q=80')"></div>
        <div class="absolute inset-0" style="background: linear-gradient(to top, #0A0A0AFF 0%, #0A0A0AFC 35%, #0A0A0AE0 55%, #0A0A0AA0 75%, #0A0A0A55 100%)"></div>
        <div class="absolute inset-0 flex flex-col justify-end p-12 gap-8">
            <span class="inline-flex items-center gap-2 bg-bcz-red/[.125] text-bcz-red text-xs font-semibold px-4 py-2 rounded-full w-fit">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Atletický portál
            </span>
            <h2 class="font-display text-[44px] font-bold text-white leading-tight tracking-wide">Nájdi svoj tím a začni trénovať</h2>
            <p class="text-bcz-muted text-[15px] leading-relaxed max-w-[420px]">Pripoj sa k tímu v tvojom okolí a začni sledovať tréningy, súťaže a výsledky.</p>
            <div class="flex flex-col gap-4">
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
    <div class="flex-1 flex flex-col justify-center px-6 py-12 lg:px-20">
        <div class="max-w-lg mx-auto w-full space-y-8">
            <div>
                <h1 class="font-display text-4xl font-bold text-white tracking-wide">PRIPOJIŤ SA K TÍMU</h1>
                <p class="text-bcz-muted text-sm mt-2">Vyhľadaj tím podľa názvu alebo zadaj pozývací kód</p>
            </div>

            {{-- Search Section --}}
            <div class="space-y-4">
                <label class="text-bcz-lighter text-[13px] font-semibold">Hľadať tím</label>
                <div class="flex items-center gap-3 bg-[#111111] border border-bcz-border rounded-lg h-12 px-4">
                    <svg class="w-[18px] h-[18px] text-bcz-dim flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Názov tímu alebo mesto..."
                        class="bg-transparent border-none text-white text-sm placeholder:text-bcz-dim focus:outline-none focus:ring-0 w-full"
                    >
                </div>
                @if(strlen($search) >= 2)
                    <p class="text-bcz-dim text-xs">{{ $this->teamResults->count() }} {{ $this->teamResults->count() === 1 ? 'výsledok' : 'výsledky' }}</p>
                @endif
            </div>

            {{-- Results --}}
            @if($this->teamResults->count() > 0)
                <div class="space-y-3">
                    @foreach($this->teamResults as $team)
                        <div class="flex items-center gap-4 bg-[#111111] border {{ $selectedTeamId === $team->id ? 'border-bcz-red border-2' : 'border-bcz-border' }} rounded-xl p-4 px-5">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-bcz-red to-red-900 flex items-center justify-center flex-shrink-0">
                                <span class="font-display text-xl font-bold text-white">{{ strtoupper(substr($team->getTranslation('name', 'sk'), 0, 1)) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-white text-[15px] font-bold truncate">{{ $team->getTranslation('name', app()->getLocale()) }}</p>
                                <p class="text-bcz-muted text-xs">{{ $team->members_count }} {{ $team->members_count === 1 ? 'člen' : 'členov' }}</p>
                            </div>
                            @if($requestSent && $selectedTeamId === $team->id)
                                <span class="text-green-500 text-xs font-semibold">Odoslané</span>
                            @elseif($requestError && $selectedTeamId === $team->id)
                                <span class="text-bcz-red text-xs">{{ $requestError }}</span>
                            @else
                                <button
                                    wire:click="selectTeam('{{ $team->id }}')"
                                    class="px-5 py-2.5 rounded-lg text-xs font-bold {{ $selectedTeamId === $team->id ? 'bg-bcz-red text-white' : 'border border-bcz-faint text-bcz-lighter hover:border-bcz-muted' }} transition-colors"
                                >
                                    Požiadať
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Guest request form --}}
            @if($showRequestForm)
                <div class="bg-[#111111] border border-bcz-border rounded-xl p-5 space-y-4">
                    <p class="text-white text-sm font-semibold">Vyplňte vaše údaje</p>
                    <div class="space-y-3">
                        <input
                            type="text"
                            wire:model="requestName"
                            placeholder="Vaše meno"
                            class="w-full bg-[#111111] border border-bcz-border rounded-lg h-11 px-4 text-white text-sm placeholder:text-bcz-dim focus:outline-none focus:ring-1 focus:ring-bcz-red focus:border-bcz-red"
                        >
                        @error('requestName') <span class="text-bcz-red text-xs">{{ $message }}</span> @enderror

                        <input
                            type="email"
                            wire:model="requestEmail"
                            placeholder="Váš email"
                            class="w-full bg-[#111111] border border-bcz-border rounded-lg h-11 px-4 text-white text-sm placeholder:text-bcz-dim focus:outline-none focus:ring-1 focus:ring-bcz-red focus:border-bcz-red"
                        >
                        @error('requestEmail') <span class="text-bcz-red text-xs">{{ $message }}</span> @enderror
                    </div>
                    <button
                        wire:click="submitGuestRequest"
                        class="w-full bg-bcz-red text-white text-sm font-bold rounded-lg h-11 hover:bg-red-700 transition-colors"
                    >
                        Odoslať žiadosť
                    </button>
                </div>
            @endif

            {{-- Request sent message --}}
            @if($requestSent)
                <div class="bg-green-500/10 border border-green-500/30 rounded-xl p-4 text-center">
                    <p class="text-green-500 text-sm font-semibold">Žiadosť bola úspešne odoslaná!</p>
                    <p class="text-bcz-dim text-xs mt-1">Po schválení vám príde notifikácia na email.</p>
                </div>
            @endif

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
                @if($codeSuccess)
                    <div class="bg-green-500/10 border border-green-500/30 rounded-xl p-4 text-center">
                        <p class="text-green-500 text-sm font-semibold">Úspešne ste sa pripojili k tímu!</p>
                        <a href="/admin" class="text-bcz-red text-xs font-semibold hover:underline mt-1 inline-block">Prejsť na dashboard →</a>
                    </div>
                @endif
            </div>

            <p class="text-bcz-dim text-xs text-center">Po schválení tvojej žiadosti ti príde notifikácia na email</p>
        </div>
    </div>
</div>
