<div>
    {{-- Topbar Banner --}}
    @if($topbarHtml)
        <div class="relative w-full" wire:key="topbar-{{ $topbarId }}">
            {!! $topbarHtml !!}
            <button
                wire:click="dismissTopbar"
                class="absolute right-4 top-1/2 -translate-y-1/2 text-white/50 hover:text-white transition-colors z-10"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    {{-- Floating Banner --}}
    @if($floatingHtml)
        <div
            x-data="{ show: false }"
            x-init="setTimeout(() => show = true, 2000)"
            x-show="show"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-4"
            x-cloak
            class="fixed bottom-4 right-4 z-50"
            wire:key="floating-{{ $floatingId }}"
        >
            <div class="relative">
                {!! $floatingHtml !!}
                <button
                    wire:click="dismissFloating"
                    @click="show = false"
                    class="absolute right-3 top-3 z-10 transition-colors {{ $floatingIsLight ? 'w-8 h-8 flex items-center justify-center rounded-lg bg-[#F5F5F5] text-[#888888] hover:text-[#333333]' : 'text-[#555555] hover:text-white' }}"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    @endif

    {{-- Popup Banner --}}
    @if($popupHtml)
        <div
            x-data="{ open: false }"
            x-init="setTimeout(() => open = true, 3000)"
            x-cloak
            wire:key="popup-{{ $popupId }}"
        >
            <template x-teleport="body">
                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    @keydown.escape.window="open = false; $wire.dismissPopup()"
                    style="position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);z-index:100;"
                    class="flex items-center justify-center p-4"
                >
                    <div
                        @click.outside="open = false; $wire.dismissPopup()"
                        x-show="open"
                        x-transition:enter="transition ease-out duration-300 delay-100"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="bg-[#111111] border border-[#222222] rounded-[20px] overflow-hidden w-[460px] max-w-full relative"
                    >
                        {{-- Close button --}}
                        <button
                            @click="open = false; $wire.dismissPopup()"
                            class="absolute right-4 top-4 text-[#666666] hover:text-white transition-colors z-10"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>

                        {{-- Popup content --}}
                        <div class="p-8" @click="if($event.target.classList.contains('banner-dismiss')) { open = false; $wire.dismissPopup(); }">
                            {!! $popupHtml !!}
                        </div>
                    </div>
                </div>
            </template>
        </div>
    @endif
</div>
