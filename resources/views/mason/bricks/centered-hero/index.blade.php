<section class="pt-[100px] pb-[60px]">
    <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col items-center gap-6">
        {{-- Badge --}}
        @if(! empty($badge))
            <div class="rounded-full bg-bcz-red px-4 py-2 flex items-center gap-2">
                <span class="text-white text-sm font-bold">{{ brick_trans($badge) }}</span>
            </div>
        @endif

        {{-- Title --}}
        @if(! empty($title))
            <h1 class="font-display font-bold text-[28px] md:text-[40px] lg:text-[56px] tracking-wide text-center">{{ brick_trans($title) }}</h1>
        @endif

        {{-- Subtitle --}}
        @if(! empty($subtitle))
            <p class="text-[#888888] text-lg text-center max-w-[750px] leading-relaxed">{{ brick_trans($subtitle) }}</p>
        @endif

        {{-- Highlight box --}}
        @if(! empty($highlight))
            <div class="rounded-xl bg-[#22C55E20] border border-[#22C55E40] px-6 py-4 flex items-center gap-3">
                <svg class="w-6 h-6 text-[#22C55E] shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                </svg>
                <span class="text-[#22C55E] text-[15px] font-medium">{{ brick_trans($highlight) }}</span>
            </div>
        @endif
    </div>
</section>
