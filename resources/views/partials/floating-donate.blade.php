{{-- Floating 2% z dane Window --}}
<div
    x-data="{ open: true, visible: false }"
    x-init="setTimeout(() => visible = true, 500)"
    x-show="open"
    x-cloak
    class="fixed bottom-4 right-4 left-4 sm:left-auto sm:right-6 sm:bottom-6 z-50"
>
    <div
        x-show="visible"
        x-transition:enter="transition ease-out duration-500"
        x-transition:enter-start="opacity-0 translate-y-8"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-8"
        class="w-full sm:w-[420px] bg-white rounded-2xl p-6 sm:p-8 shadow-[0_20px_60px_rgba(0,0,0,0.25)] flex flex-col gap-5 sm:gap-6"
    >
        {{-- Header --}}
        <div class="flex items-center justify-between w-full">
            <div class="flex items-center gap-3">
                {{-- Heart icon --}}
                <div class="w-12 h-12 rounded-xl bg-[#FF2D2D15] flex items-center justify-center">
                    <svg class="w-6 h-6 text-bcz-red" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                    </svg>
                </div>
                {{-- 2% badge --}}
                <span class="bg-bcz-red text-white text-sm font-bold px-3 py-1.5 rounded-full">2%</span>
            </div>
            {{-- Close button --}}
            <button
                @click="visible = false; setTimeout(() => open = false, 300)"
                class="w-8 h-8 rounded-lg bg-[#F5F5F5] flex items-center justify-center hover:bg-gray-200 transition-colors"
            >
                <svg class="w-4 h-4 text-[#888888]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M18 6L6 18M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Title --}}
        <h3 class="font-display text-2xl font-bold text-[#0A0A0A] tracking-wide">Darujte nám 2% z dane</h3>

        {{-- Description --}}
        <p class="text-[#666666] text-[15px] leading-relaxed">
            Podporíte rozvoj parkouru na Slovensku a pomôžete nám vychovávať ďalšiu generáciu športovcov.
        </p>

        {{-- Stats --}}
        <div class="flex gap-4 w-full">
            <div class="flex-1 bg-[#F5F5F5] rounded-xl p-4 flex flex-col items-center gap-1">
                <span class="font-display text-[28px] font-bold text-bcz-red tracking-wide">500+</span>
                <span class="text-[#888888] text-xs font-medium">detí ročne</span>
            </div>
            <div class="flex-1 bg-[#F5F5F5] rounded-xl p-4 flex flex-col items-center gap-1">
                <span class="font-display text-[28px] font-bold text-bcz-red tracking-wide">10+</span>
                <span class="text-[#888888] text-xs font-medium">rokov</span>
            </div>
        </div>

        {{-- CTA Button --}}
        <a href="{{ route('dva-percenta') }}" class="w-full bg-bcz-red text-white text-[13px] font-bold tracking-[2px] py-4 px-6 rounded-xl flex items-center justify-center gap-2.5 hover:bg-red-700 transition-colors">
            ZÍSKAŤ TLAČIVO
            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </a>

        {{-- Info text --}}
        <p class="text-[#999999] text-[11px] text-center">
            IČO: 42 195 250 • Právna forma: občianske združenie
        </p>
    </div>
</div>
