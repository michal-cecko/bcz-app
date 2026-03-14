<section class="py-[60px]">
    <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col items-center gap-6 lg:gap-10">
        {{-- Title --}}
        @if(! empty($title))
            <div class="flex flex-col items-center gap-4">
                <h2 class="font-display font-bold text-[32px] tracking-wide">{{ brick_trans($title) }}</h2>
                @if(! empty($subtitle))
                    <p class="text-[#888888] text-center">{{ brick_trans($subtitle) }}</p>
                @endif
            </div>
        @endif

        {{-- Cards --}}
        @if(! empty($cards))
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($cards as $card)
                    @php $color = $card['color'] ?? '#3B82F6'; @endphp
                    <div class="rounded-2xl bg-[#111111] p-8 border border-[#222222] flex flex-col gap-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white" style="background-color: {{ $color }}">
                                @if(! empty($card['icon']))
                                    <x-filament::icon :icon="$card['icon']" class="w-6 h-6" />
                                @else
                                    <x-filament::icon icon="heroicon-o-information-circle" class="w-6 h-6" />
                                @endif
                            </div>
                            <h3 class="text-white text-xl font-semibold">{{ brick_trans($card['title'] ?? []) }}</h3>
                        </div>

                        @if(! empty($card['subtitle']))
                            <p class="text-[#888888] text-sm">{{ brick_trans($card['subtitle'] ?? []) }}</p>
                        @endif

                        {{-- Steps --}}
                        @if(! empty($card['steps']))
                            <div class="flex flex-col gap-4">
                                @foreach($card['steps'] as $stepIndex => $step)
                                    <div class="flex gap-3">
                                        <div class="w-6 h-6 rounded-xl flex items-center justify-center text-white text-xs font-bold shrink-0" style="background-color: {{ $color }}">{{ $stepIndex + 1 }}</div>
                                        <span class="text-[#CCCCCC] text-sm leading-relaxed">{{ brick_trans($step['text'] ?? []) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if(! empty($card['button_text']))
                            @php $buttonHref = brick_link(['link_type' => $card['button_link_type'] ?? '', 'link_model_id' => $card['button_link_model_id'] ?? '', 'link_url' => $card['button_link_url'] ?? '']) ?? '#'; @endphp
                            <a href="{{ $buttonHref }}" class="text-white rounded-lg py-3.5 w-full text-center font-semibold block" style="background-color: {{ $color }}">{{ brick_trans($card['button_text'] ?? []) }}</a>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
