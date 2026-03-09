@if(! empty($label) || ! empty($title) || ! empty($cards))
<section class="bg-[#111111] py-[100px]">
    <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
    <div class="flex flex-col gap-[60px]">
        @if(! empty($label) || ! empty($title))
            <div class="flex flex-col items-center gap-4">
                @if(! empty($label))
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                        <span class="text-bcz-red text-xs font-bold tracking-widest">{{ brick_trans($label) }}</span>
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                    </div>
                @endif
                @if(! empty($title))
                    <h2 class="font-display font-bold text-5xl tracking-wide">{{ brick_trans($title) }}</h2>
                @endif
            </div>
        @endif

        @if(! empty($cards))
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-{{ min(count($cards), 4) }} gap-6">
                @foreach($cards as $card)
                    <div class="bg-bcz-dark border border-[#222222] p-8 flex flex-col gap-5 min-h-[200px] lg:min-h-[280px]">
                        @if(! empty($card['icon']))
                            <div class="size-14 bg-[#FF2D2D12] flex items-center justify-center self-start">
                                <x-dynamic-component :component="$card['icon']" class="w-7 h-7 text-bcz-red" />
                            </div>
                        @endif
                        @if(! empty($card['title']))
                            <h3 class="text-white text-xl font-bold">{{ brick_trans($card['title']) }}</h3>
                        @endif
                        @if(! empty($card['description']))
                            <p class="text-[#888888] text-sm leading-relaxed">{!! brick_trans($card['description']) !!}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    </div>
</section>
@endif
