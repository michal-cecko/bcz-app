<div class="min-h-screen bg-bcz-dark">
    @if($step <= 3)
        <div class="flex flex-col lg:flex-row min-h-screen">
            {{-- Left Panel --}}
            <div class="hidden lg:block w-[560px] relative flex-shrink-0 overflow-hidden">
                <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1758274526128-90858f16f554?w=1080&q=80')"></div>
                <div class="absolute inset-0" style="background: linear-gradient(to top, #0A0A0AFF 0%, #0A0A0AFC 35%, #0A0A0AE0 55%, #0A0A0AA0 75%, #0A0A0A55 100%)"></div>
                <div class="absolute inset-0 flex flex-col justify-end p-12 gap-8">
                    <span class="inline-flex items-center gap-2 bg-bcz-red/[.125] text-bcz-red text-xs font-semibold px-4 py-2 rounded-full w-fit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Registrácia tímu
                    </span>
                    <h2 class="font-display text-[44px] font-bold text-white leading-tight tracking-wide">Začnite spravovať váš klub ešte dnes</h2>
                    <p class="text-bcz-muted text-[15px] leading-relaxed max-w-[420px]">Pridajte sa k 500+ tímom, ktoré používajú BCZ Club na správu tréningov, súťaží a členov.</p>
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-bcz-red flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-bcz-muted text-sm">Správa tréningov a športovcov</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-bcz-red flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-bcz-muted text-sm">Organizácia súťaží a vystúpení</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-bcz-red flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-bcz-muted text-sm">Výpočty umiestnení a rebríčkov</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-bcz-red flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-bcz-muted text-sm">Platby a členské poplatky</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Panel --}}
            <div class="flex-1 flex flex-col justify-center px-6 py-12 lg:px-20">
                <div class="max-w-lg mx-auto w-full space-y-10">
                    {{-- Step Indicator --}}
                    @include('livewire.partials.register-team-steps')

                    {{-- Step 1: Account --}}
                    @if($step === 1)
                        <div class="space-y-8">
                            <div>
                                <h1 class="font-display text-4xl font-bold text-white tracking-wide">Vytvorte si účet</h1>
                                <p class="text-bcz-muted text-sm mt-2">Stanete sa vlastníkom tohto tímu</p>
                            </div>
                            <div class="space-y-5">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-1.5">
                                        <label class="text-bcz-muted text-[13px] font-medium">Meno</label>
                                        <input type="text" wire:model="firstName" placeholder="Dominik" class="w-full bg-[#111111] border border-bcz-border h-[46px] px-4 text-white text-sm placeholder:text-bcz-dim focus:outline-none focus:ring-1 focus:ring-bcz-red focus:border-bcz-red">
                                        @error('firstName') <span class="text-bcz-red text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="text-bcz-muted text-[13px] font-medium">Priezvisko</label>
                                        <input type="text" wire:model="lastName" placeholder="Klimek" class="w-full bg-[#111111] border border-bcz-border h-[46px] px-4 text-white text-sm placeholder:text-bcz-dim focus:outline-none focus:ring-1 focus:ring-bcz-red focus:border-bcz-red">
                                        @error('lastName') <span class="text-bcz-red text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-bcz-muted text-[13px] font-medium">Email</label>
                                    <input type="email" wire:model="email" placeholder="dominik@bczclub.sk" class="w-full bg-[#111111] border border-bcz-border h-[46px] px-4 text-white text-sm placeholder:text-bcz-dim focus:outline-none focus:ring-1 focus:ring-bcz-red focus:border-bcz-red">
                                    @error('email') <span class="text-bcz-red text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-bcz-muted text-[13px] font-medium">Heslo</label>
                                    <input type="password" wire:model="password" placeholder="••••••••" class="w-full bg-[#111111] border border-bcz-border h-[46px] px-4 text-white text-sm placeholder:text-bcz-dim focus:outline-none focus:ring-1 focus:ring-bcz-red focus:border-bcz-red">
                                    @error('password') <span class="text-bcz-red text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-bcz-muted text-[13px] font-medium">Potvrdiť heslo</label>
                                    <input type="password" wire:model="passwordConfirmation" placeholder="••••••••" class="w-full bg-[#111111] border border-bcz-border h-[46px] px-4 text-white text-sm placeholder:text-bcz-dim focus:outline-none focus:ring-1 focus:ring-bcz-red focus:border-bcz-red">
                                    @error('passwordConfirmation') <span class="text-bcz-red text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <x-gdpr-checkbox :with-terms="true" />

                            <div class="space-y-5">
                                <button wire:click="nextStep" class="w-full bg-bcz-red text-white text-sm font-bold h-12 flex items-center justify-center gap-2 hover:bg-red-700 transition-colors">
                                    Pokračovať
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>
                                <p class="text-center text-sm">
                                    <span class="text-bcz-dim">Už máte účet?</span>
                                    <a href="/admin/login" class="text-bcz-red font-semibold hover:underline ml-1">Prihlásiť sa</a>
                                </p>
                            </div>
                        </div>
                    @endif

                    {{-- Step 2: Team Details --}}
                    @if($step === 2)
                        <div class="space-y-8">
                            <div>
                                <h1 class="font-display text-4xl font-bold text-white tracking-wide">Detaily tímu</h1>
                                <p class="text-bcz-muted text-sm mt-2">Povedzte nám viac o vašom tíme</p>
                            </div>
                            <div class="space-y-5">
                                <div class="space-y-1.5">
                                    <label class="text-bcz-muted text-[13px] font-medium">Názov tímu *</label>
                                    <input type="text" wire:model="teamName" placeholder="BCZ Club" class="w-full bg-[#111111] border border-bcz-border h-[46px] px-4 text-white text-sm placeholder:text-bcz-dim focus:outline-none focus:ring-1 focus:ring-bcz-red focus:border-bcz-red">
                                    @error('teamName') <span class="text-bcz-red text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-1.5">
                                        <label class="text-bcz-muted text-[13px] font-medium">Meno vlastníka tímu *</label>
                                        <input type="text" wire:model="ownerName" placeholder="Ján Novák" class="w-full bg-[#111111] border border-bcz-border h-[46px] px-4 text-white text-sm placeholder:text-bcz-dim focus:outline-none focus:ring-1 focus:ring-bcz-red focus:border-bcz-red">
                                        @error('ownerName') <span class="text-bcz-red text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="text-bcz-muted text-[13px] font-medium">Email vlastníka *</label>
                                        <input type="email" wire:model="ownerEmail" placeholder="jan@bczclub.sk" class="w-full bg-[#111111] border border-bcz-border h-[46px] px-4 text-white text-sm placeholder:text-bcz-dim focus:outline-none focus:ring-1 focus:ring-bcz-red focus:border-bcz-red">
                                        @error('ownerEmail') <span class="text-bcz-red text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-1.5">
                                        <label class="text-bcz-muted text-[13px] font-medium">Typ športu</label>
                                        <input type="text" wire:model="sportType" placeholder="Street Workout" class="w-full bg-[#111111] border border-bcz-border h-[46px] px-4 text-white text-sm placeholder:text-bcz-dim focus:outline-none focus:ring-1 focus:ring-bcz-red focus:border-bcz-red">
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="text-bcz-muted text-[13px] font-medium">Krajina</label>
                                        <select wire:model="country" class="w-full bg-[#111111] border border-bcz-border h-[46px] px-4 text-white text-sm focus:outline-none focus:ring-1 focus:ring-bcz-red focus:border-bcz-red appearance-none">
                                            <option value="SK">Slovensko</option>
                                            <option value="CZ">Česko</option>
                                            <option value="OTHER">Iná</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-bcz-muted text-[13px] font-medium">Mesto</label>
                                    <input type="text" wire:model="city" placeholder="Čadca" class="w-full bg-[#111111] border border-bcz-border h-[46px] px-4 text-white text-sm placeholder:text-bcz-dim focus:outline-none focus:ring-1 focus:ring-bcz-red focus:border-bcz-red">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-bcz-muted text-[13px] font-medium">Logo tímu (voliteľné)</label>
                                    <label class="flex items-center justify-center gap-2.5 bg-[#111111] border border-bcz-border h-20 cursor-pointer hover:border-bcz-muted transition-colors">
                                        <input type="file" wire:model="logo" class="hidden" accept="image/*">
                                        @if($logo && method_exists($logo, 'isPreviewable') && $logo->isPreviewable())
                                            <img src="{{ $logo->temporaryUrl() }}" alt="Logo" class="h-14 w-14 object-cover rounded-lg">
                                            <span class="text-bcz-dim text-[13px]">Zmeniť obrázok</span>
                                        @elseif($logo)
                                            <svg class="w-[18px] h-[18px] text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            <span class="text-bcz-dim text-[13px]">Súbor nahraný</span>
                                        @else
                                            <svg class="w-[18px] h-[18px] text-bcz-subtle" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                            <span class="text-bcz-dim text-[13px]">Nahrať obrázok alebo pretiahnuť sem</span>
                                        @endif
                                    </label>
                                    @error('logo') <span class="text-bcz-red text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-bcz-muted text-[13px] font-medium">Krátky popis (voliteľné)</label>
                                    <textarea wire:model="description" placeholder="Napíšte niečo o vašom tíme..." rows="3" class="w-full bg-[#111111] border border-bcz-border p-4 text-white text-sm placeholder:text-bcz-dim focus:outline-none focus:ring-1 focus:ring-bcz-red focus:border-bcz-red resize-none"></textarea>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <button wire:click="previousStep" class="flex items-center justify-center gap-2 border border-bcz-border text-bcz-muted text-sm font-medium h-12 px-8 hover:border-bcz-muted transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                    Späť
                                </button>
                                <button wire:click="nextStep" class="flex-1 bg-bcz-red text-white text-sm font-bold h-12 flex items-center justify-center gap-2 hover:bg-red-700 transition-colors">
                                    Pokračovať
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>
                            </div>
                        </div>
                    @endif

                    {{-- Step 3: Plan Selection --}}
                    @if($step === 3)
                        @include('livewire.partials.register-team-plans')
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Step 4: Success --}}
    @if($step === 4)
        <div class="min-h-screen flex flex-col items-center justify-center px-6 py-12 gap-12">
            {{-- Step Indicator --}}
            <div class="w-full max-w-[600px]">
                @include('livewire.partials.register-team-steps')
            </div>

            {{-- Success Icon --}}
            <div class="w-20 h-20 rounded-full bg-green-500/[.125] border border-green-500/[.27] flex items-center justify-center">
                <svg class="w-9 h-9 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>

            {{-- Title --}}
            <div class="text-center space-y-3">
                <h1 class="font-display text-[44px] font-bold text-white tracking-wide">Váš tím je pripravený!</h1>
                <p class="text-bcz-muted text-base leading-relaxed max-w-[500px] mx-auto">Gratulujeme! Tím bol úspešne vytvorený a je pripravený na používanie.</p>
            </div>

            {{-- Summary Card --}}
            <div class="flex items-center gap-10 bg-[#111111] border border-[#1A1A1A] rounded-xl px-8 py-6">
                <div class="text-center space-y-1">
                    <p class="text-bcz-dim text-[11px] font-medium">Tím</p>
                    <p class="text-white text-sm font-semibold">{{ $createdTeamName }}</p>
                </div>
                <div class="w-px h-8 bg-bcz-border"></div>
                <div class="text-center space-y-1">
                    <p class="text-bcz-dim text-[11px] font-medium">Plán</p>
                    <p class="text-bcz-red text-sm font-semibold">{{ $createdPlanName }}</p>
                </div>
                <div class="w-px h-8 bg-bcz-border"></div>
                <div class="text-center space-y-1">
                    <p class="text-bcz-dim text-[11px] font-medium">Šport</p>
                    <p class="text-white text-sm font-semibold">{{ $createdSportType }}</p>
                </div>
                <div class="w-px h-8 bg-bcz-border"></div>
                <div class="text-center space-y-1">
                    <p class="text-bcz-dim text-[11px] font-medium">Vlastník</p>
                    <p class="text-white text-sm font-semibold">{{ $createdOwnerName }}</p>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center gap-4">
                <a href="/admin" class="flex items-center gap-2 bg-bcz-red text-white text-[13px] font-bold px-6 py-3.5 hover:bg-red-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    Pridať prvého člena
                </a>
                <a href="/admin" class="flex items-center gap-2 border border-bcz-border text-bcz-lighter text-[13px] font-medium px-6 py-3.5 hover:border-bcz-muted transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    Preskúmať dashboard
                </a>
            </div>
        </div>
    @endif
</div>
