@props(['items', 'locale' => 'sk'])

<section class="bg-[#111111] py-20">
    <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col gap-12">
        <div class="flex flex-col items-center gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-0.5 bg-bcz-red"></div>
                <span class="text-bcz-red text-xs font-bold tracking-[3px]">{{ __('FOTO') }}</span>
                <div class="w-10 h-0.5 bg-bcz-red"></div>
            </div>
            <h2 class="font-display font-bold text-4xl tracking-[0.5px]">{{ __('GALERIA') }}</h2>
            <p class="text-[#888888] text-base">{{ __('Momenty z treningov a sutazi') }}</p>
        </div>

        <div class="columns-1 md:columns-2 lg:columns-3 gap-5 space-y-5">
            @foreach($items as $item)
                @php $imageUrl = $item->getFirstMediaUrl('image'); @endphp
                @if($imageUrl)
                    <div class="break-inside-avoid group cursor-pointer" x-data="{ open: false }">
                        <div class="relative rounded-lg overflow-hidden" @click="open = true">
                            <img src="{{ $imageUrl }}" alt="{{ $item->getTranslation('description', $locale) }}" class="w-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                                <div class="absolute bottom-4 left-4 right-4">
                                    @if($item->getTranslation('description', $locale))
                                        <p class="text-white text-sm">{{ $item->getTranslation('description', $locale) }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($item->tags && count($item->tags) > 0)
                            <div class="flex flex-wrap gap-2 mt-3">
                                @foreach($item->tags as $tag)
                                    <span class="text-bcz-red text-[11px] font-medium bg-bcz-red/10 px-3 py-1 rounded-full">{{ $tag }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</section>
