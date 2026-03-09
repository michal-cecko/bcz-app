<div class="flex flex-col lg:flex-row gap-8 lg:gap-20">
    {{-- LEFT - Form --}}
    <div class="flex-1 flex flex-col gap-8">
        @if($submitted)
            <div class="rounded-xl bg-[#111111] border border-green-800 p-8 text-center flex flex-col items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-green-900/50 flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white">Správa bola odoslaná</h3>
                <p class="text-[#CCCCCC]">Ďakujeme, ozveme sa vám čo najskôr.</p>
            </div>
        @else
            <form wire:submit="submit" class="flex flex-col gap-8">
                {{-- Reason chips --}}
                @if($showReason)
                    <div x-data="{ selected: @entangle('reason') }" class="flex flex-col gap-2">
                        <label class="text-white text-sm font-semibold">Dôvod kontaktu (voliteľné)</label>
                        <div class="flex flex-wrap gap-3">
                            @foreach($reasons as $reasonOption)
                                <button
                                    type="button"
                                    x-on:click="selected = '{{ $reasonOption->value }}'"
                                    :class="selected === '{{ $reasonOption->value }}'
                                        ? 'border-bcz-red bg-[#FF2D2D15] text-white'
                                        : 'border-[#333333] bg-[#111111] text-[#CCCCCC] hover:border-bcz-red'"
                                    class="rounded-lg border px-4 py-2.5 text-sm cursor-pointer transition"
                                >
                                    {{ $reasonOption->getLabel() }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Name + Email row --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="flex flex-col gap-2">
                        <label class="text-white text-sm font-semibold">Meno a priezvisko</label>
                        <input wire:model="name" type="text" class="bg-[#111111] border border-[#333333] rounded-lg px-4 py-3.5 text-white text-[15px] placeholder-[#666666] w-full focus:border-bcz-red focus:ring-0 focus:outline-none" placeholder="Meno a priezvisko">
                        @error('name') <p class="text-bcz-red text-sm">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-white text-sm font-semibold">E-mail</label>
                        <input wire:model="email" type="email" class="bg-[#111111] border border-[#333333] rounded-lg px-4 py-3.5 text-white text-[15px] placeholder-[#666666] w-full focus:border-bcz-red focus:ring-0 focus:outline-none" placeholder="E-mail">
                        @error('email') <p class="text-bcz-red text-sm">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Phone --}}
                @if($showPhone)
                    <div class="flex flex-col gap-2">
                        <label class="text-white text-sm font-semibold">Telefón (voliteľné)</label>
                        <input wire:model="phone" type="tel" class="bg-[#111111] border border-[#333333] rounded-lg px-4 py-3.5 text-white text-[15px] placeholder-[#666666] w-full focus:border-bcz-red focus:ring-0 focus:outline-none" placeholder="+421">
                        @error('phone') <p class="text-bcz-red text-sm">{{ $message }}</p> @enderror
                    </div>
                @endif

                {{-- Message --}}
                <div class="flex flex-col gap-2">
                    <label class="text-white text-sm font-semibold">Správa</label>
                    <textarea wire:model="message" class="bg-[#111111] border border-[#333333] rounded-lg px-4 py-3.5 text-white text-[15px] placeholder-[#666666] w-full h-40 resize-none focus:border-bcz-red focus:ring-0 focus:outline-none" placeholder="Vaša správa..."></textarea>
                    @error('message') <p class="text-bcz-red text-sm">{{ $message }}</p> @enderror
                </div>

                {{-- Submit --}}
                <button type="submit" wire:loading.attr="disabled" wire:target="submit" class="bg-bcz-red text-white rounded-lg px-8 py-4 font-semibold flex items-center justify-center gap-2.5 w-fit hover:bg-red-700 transition disabled:opacity-50">
                    <span wire:loading.remove wire:target="submit">Odoslať správu &rarr;</span>
                    <span wire:loading wire:target="submit">Odosielam...</span>
                </button>
            </form>
        @endif
    </div>

    {{-- RIGHT - Sidebar --}}
    @if($hasSidebar)
        <div class="w-full lg:w-[400px] flex flex-col gap-8">
            {{-- Contact Info Card --}}
            <div class="rounded-2xl bg-[#111111] p-8 border border-[#222222] flex flex-col gap-6">
                <h3 class="text-[22px] font-bold text-white">Kontaktné údaje</h3>

                @if($contactEmail)
                    <div class="flex items-center gap-4">
                        <div class="rounded-xl bg-[#1A1A1A] w-11 h-11 flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#888888]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[#888888] text-[13px]">E-mail</span>
                            <a href="mailto:{{ $contactEmail }}" class="text-white text-base font-semibold hover:text-bcz-red transition">{{ $contactEmail }}</a>
                        </div>
                    </div>
                @endif

                @if($contactPhone)
                    <div class="flex items-center gap-4">
                        <div class="rounded-xl bg-[#1A1A1A] w-11 h-11 flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#888888]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[#888888] text-[13px]">Telefón</span>
                            <a href="tel:{{ $contactPhone }}" class="text-white text-base font-semibold hover:text-bcz-red transition">{{ $contactPhone }}</a>
                        </div>
                    </div>
                @endif

                @if($contactLocation)
                    <div class="flex items-center gap-4">
                        <div class="rounded-xl bg-[#1A1A1A] w-11 h-11 flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#888888]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[#888888] text-[13px]">Lokalita</span>
                            <span class="text-white text-base font-semibold">{{ $contactLocation }}</span>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Social Card --}}
            @if($socialInstagram || $socialFacebook || $socialYoutube)
                <div class="rounded-2xl bg-[#111111] p-8 border border-[#222222]">
                    <h3 class="text-white text-lg font-bold mb-4">Sledujte nás</h3>
                    <div class="flex gap-3">
                        @if($socialInstagram)
                            <a href="{{ $socialInstagram }}" target="_blank" rel="noopener" class="w-12 h-12 bg-[#1A1A1A] rounded-xl flex items-center justify-center text-[#888888] hover:text-white transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                            </a>
                        @endif
                        @if($socialFacebook)
                            <a href="{{ $socialFacebook }}" target="_blank" rel="noopener" class="w-12 h-12 bg-[#1A1A1A] rounded-xl flex items-center justify-center text-[#888888] hover:text-white transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                        @endif
                        @if($socialYoutube)
                            <a href="{{ $socialYoutube }}" target="_blank" rel="noopener" class="w-12 h-12 bg-[#1A1A1A] rounded-xl flex items-center justify-center text-[#888888] hover:text-white transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Quick Response Card --}}
            @if($responseText)
                <div class="rounded-xl bg-[#FF2D2D10] p-6 border border-[#FF2D2D30]">
                    <div class="flex flex-col gap-2">
                        <span class="text-lg">⚡</span>
                        <span class="text-white font-bold">Rýchla odpoveď</span>
                        <p class="text-[#CCCCCC] text-sm leading-relaxed">{{ $responseText }}</p>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
