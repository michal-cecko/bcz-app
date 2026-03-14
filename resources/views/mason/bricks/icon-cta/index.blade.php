<section class="py-20">
    <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col items-center gap-6">
        <div class="rounded-3xl bg-[#FF2D2D10] border border-[#FF2D2D30] px-5 md:px-10 lg:px-20 py-[60px] w-full flex flex-col items-center gap-6">
            {{-- Icon --}}
            @if(! empty($icon))
                <div class="w-20 h-20 bg-bcz-red rounded-[20px] flex items-center justify-center text-white">
                    <x-filament::icon :icon="$icon" class="w-10 h-10" />
                </div>
            @elseif(! empty($icon_text))
                <div class="w-20 h-20 bg-bcz-red rounded-[20px] flex items-center justify-center font-display font-bold text-[28px] text-white">{{ brick_trans($icon_text) }}</div>
            @endif

            {{-- Title --}}
            @if(! empty($title))
                <h2 class="font-display font-bold text-[24px] md:text-[36px] tracking-wide text-center">{{ brick_trans($title) }}</h2>
            @endif

            {{-- Description --}}
            @if(! empty($description))
                <p class="text-[#CCCCCC] text-center max-w-[600px] leading-relaxed">{{ brick_trans($description) }}</p>
            @endif

            {{-- Buttons --}}
            @if(! empty($primary_button_text) || ! empty($secondary_button_text))
                <div class="flex items-center gap-4">
                    @if(! empty($primary_button_text))
                        @php $primaryHref = brick_link(['link_type' => $primary_button_link_type ?? '', 'link_model_id' => $primary_button_link_model_id ?? '', 'link_url' => $primary_button_link_url ?? '']) ?? '#'; @endphp
                        <a href="{{ $primaryHref }}" class="bg-bcz-red rounded-lg px-8 py-4 font-semibold text-white">{{ brick_trans($primary_button_text) }}</a>
                    @endif
                    @if(! empty($secondary_button_text))
                        @php $secondaryHref = brick_link(['link_type' => $secondary_button_link_type ?? '', 'link_model_id' => $secondary_button_link_model_id ?? '', 'link_url' => $secondary_button_link_url ?? '']) ?? '#'; @endphp
                        <a href="{{ $secondaryHref }}" class="bg-[#111111] border border-[#333333] rounded-lg px-8 py-4 font-semibold text-white">{{ brick_trans($secondary_button_text) }}</a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</section>
