<section class="bg-bcz-dark py-[100px]">
    <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col lg:flex-row gap-10 lg:gap-20">
        @php $media = brick_media($image ?? null); @endphp
        @if($media->url)
            <div class="w-full lg:w-[480px] lg:shrink-0">
                <img src="{{ $media->url }}" alt="{{ $media->alt ?: brick_trans($name_line1 ?? []) . ' ' . brick_trans($name_line2 ?? []) }}" class="w-full h-[580px] object-cover">
            </div>
        @endif

        <div class="flex-1 flex flex-col justify-center gap-8">
            @if(! empty($label))
                <div class="flex items-center gap-3">
                    <div class="w-8 h-[3px] bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[3px]">{{ brick_trans($label) }}</span>
                </div>
            @endif

            @if(! empty($name_line1) || ! empty($name_line2))
                <h2 class="font-display font-bold text-[32px] md:text-[48px] lg:text-[64px] leading-none tracking-wide">{{ brick_trans($name_line1 ?? []) }}<br>{{ brick_trans($name_line2 ?? []) }}</h2>
            @endif

            @if(! empty($subtitle))
                <span class="text-bcz-red text-base font-medium tracking-wider">{!! brick_trans($subtitle) !!}</span>
            @endif

            @if(! empty($bio))
                <p class="text-[#888888] text-base leading-[1.7]">{!! brick_trans($bio) !!}</p>
            @endif

            @if(! empty($bio2))
                <p class="text-[#888888] text-base leading-[1.7]">{!! brick_trans($bio2) !!}</p>
            @endif

            @if(! empty($stats))
                <div class="flex flex-wrap gap-8">
                    @foreach($stats as $stat)
                        <div class="flex flex-col gap-1">
                            <span class="font-display font-bold text-[28px] md:text-[40px] lg:text-[56px] text-bcz-red tracking-wide">{{ $stat['number'] }}</span>
                            <span class="text-[#666666] text-sm font-medium">{{ brick_trans($stat['label'] ?? []) }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            @php
                $ctaHref = brick_link(['link_type' => $cta_link_type ?? '', 'link_model_id' => $cta_link_model_id ?? '', 'link_url' => $cta_link_url ?? '']) ?? brick_trans($cta_url ?? []);
            @endphp
            @if(! empty($cta_text) && $ctaHref)
                <a href="{{ $ctaHref }}" class="flex items-center gap-2 text-bcz-red text-[13px] font-bold tracking-widest hover:gap-3 transition-all">
                    {{ brick_trans($cta_text) }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            @endif
        </div>
    </div>
</section>
