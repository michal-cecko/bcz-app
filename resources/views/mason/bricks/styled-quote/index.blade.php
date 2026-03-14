<section class="bg-bcz-dark py-20">
    <div class="max-w-[1440px] mx-auto px-5 md:px-20 lg:px-40 flex flex-col items-center gap-6">
        <span class="font-display font-bold text-[80px] text-bcz-red leading-[0.5]">&ldquo;</span>

        @if(! empty($quote))
            <p class="text-[#CCCCCC] text-2xl font-light italic text-center leading-[1.6] max-w-[900px]">{{ brick_trans($quote) }}</p>
        @endif

        @if(! empty($attribution))
            <span class="text-[#888888] text-base font-semibold">&mdash; {{ brick_trans($attribution) }}</span>
        @endif
    </div>
</section>
