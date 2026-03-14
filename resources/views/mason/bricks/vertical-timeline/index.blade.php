<section class="bg-bcz-dark py-20">
    <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col gap-12">
        {{-- Header --}}
        <div class="flex items-end justify-between">
            <div class="flex flex-col gap-4">
                @if(! empty($label))
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-[3px] bg-bcz-red"></div>
                        <span class="text-bcz-red text-xs font-bold tracking-[3px]">{{ brick_trans($label) }}</span>
                    </div>
                @endif

                @if(! empty($title))
                    <h2 class="font-display font-bold text-[24px] md:text-[36px] lg:text-[48px] tracking-wide">{{ brick_trans($title) }}</h2>
                @endif
            </div>
        </div>

        {{-- Items --}}
        @if(! empty($items))
            <div class="flex flex-col">
                @foreach($items as $item)
                    <div class="flex gap-10 py-6 border-t border-[#1A1A1A]">
                        @if(! empty($item['year']))
                            <span class="font-display font-bold text-4xl text-bcz-red tracking-wide w-[100px] shrink-0">{{ $item['year'] }}</span>
                        @endif
                        <div class="flex flex-col gap-2">
                            @if(! empty($item['title']))
                                <h3 class="text-white text-lg font-bold">{{ brick_trans($item['title']) }}</h3>
                            @endif
                            @if(! empty($item['description']))
                                <p class="text-[#888888] text-sm leading-[1.6]">{{ brick_trans($item['description']) }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
