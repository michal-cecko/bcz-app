@if(! empty($label) || ! empty($title) || ! empty($cards))
<section class="bg-[#111111] py-[100px]">
    <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
    <div class="flex flex-col gap-[60px]">
        @if(! empty($label) || ! empty($title) || ! empty($subtitle))
            <div class="flex flex-col items-center gap-4">
                @if(! empty($label))
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-0.5 bg-bcz-red"></div>
                        <span class="text-bcz-red text-xs font-bold tracking-widest">{{ brick_trans($label) }}</span>
                    </div>
                @endif
                @if(! empty($title))
                    <h2 class="font-display font-bold text-5xl tracking-wide">{{ brick_trans($title) }}</h2>
                @endif
                @if(! empty($subtitle))
                    <p class="text-[#888888] text-lg text-center max-w-[600px]">{{ brick_trans($subtitle) }}</p>
                @endif
            </div>
        @endif

        @if(! empty($cards))
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-{{ min(count($cards), 4) }} gap-6">
                @foreach($cards as $card)
                    @php
                        $accentColor = $card['accent_color'] ?? '#FF2D2D';
                        $cardUrl = brick_link(['link_type' => $card['card_link_type'] ?? '', 'link_model_id' => $card['card_link_model_id'] ?? '', 'link_url' => $card['card_link_url'] ?? '']);
                        $cardTag = $cardUrl ? 'a' : 'div';
                    @endphp
                    <{{ $cardTag }} @if($cardUrl) href="{{ $cardUrl }}" @endif class="bg-bcz-dark border rounded-2xl overflow-hidden flex flex-col group transition-all duration-300 {{ $cardUrl ? 'hover:border-opacity-80 hover:-translate-y-1 hover:shadow-lg hover:shadow-black/20' : '' }}" style="border-color: {{ $card['border_color'] ?? '#222222' }}">
                        @if(! empty($card['image']))
                            <div class="w-full h-[220px] overflow-hidden">
                                <img src="{{ brick_media_url($card['image']) }}" alt="{{ brick_trans($card['title'] ?? []) }}" class="w-full h-full object-cover {{ $cardUrl ? 'group-hover:scale-105 transition-transform duration-500' : '' }}">
                            </div>
                        @endif
                        <div class="p-6 flex flex-col gap-4 {{ empty($card['image']) ? 'min-h-[200px] lg:min-h-[280px] p-8 gap-5' : '' }}">
                            @if(! empty($card['icon']))
                                <div class="size-12 rounded-xl flex items-center justify-center self-start" style="background-color: {{ $accentColor }}20">
                                    <x-dynamic-component :component="$card['icon']" class="w-6 h-6" style="color: {{ $accentColor }}" />
                                </div>
                            @endif
                            @if(! empty($card['title']))
                                <h3 class="font-display text-[28px] font-bold tracking-wide">{{ brick_trans($card['title']) }}</h3>
                            @endif
                            @if(! empty($card['card_subtitle']))
                                <span class="text-sm font-semibold" style="color: {{ $accentColor }}">{{ brick_trans($card['card_subtitle']) }}</span>
                            @endif
                            @if(! empty($card['description']))
                                <p class="text-[#888888] text-sm leading-relaxed">{!! brick_trans($card['description']) !!}</p>
                            @endif
                            @if(! empty($card['features']))
                                <div class="flex flex-col gap-2 mt-2">
                                    @foreach($card['features'] as $feature)
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs" style="color: {{ $accentColor }}">&#10003;</span>
                                            <span class="text-[#AAAAAA] text-[13px]">{{ brick_trans($feature) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            @if($cardUrl)
                                <div class="mt-auto pt-2">
                                    <span class="text-sm font-semibold inline-flex items-center gap-2 group-hover:gap-3 transition-all" style="color: {{ $accentColor }}">
                                        {{ __('Zistiť viac') }}
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                    </span>
                                </div>
                            @endif
                        </div>
                    </{{ $cardTag }}>
                @endforeach
            </div>
        @endif
    </div>
    </div>
</section>
@endif
