@props(['media'])

@php
    $allImages = $media->map(fn ($item) => [
        'url' => $item->getUrl(),
        'name' => $item->name,
    ])->values();
@endphp

<section
    class="bg-[#111111] py-20"
    x-data="{
        lightbox: false,
        current: 0,
        images: {{ $allImages->toJson() }},
        open(index) { this.current = index; this.lightbox = true; },
        next() { this.current = (this.current + 1) % this.images.length; },
        prev() { this.current = (this.current - 1 + this.images.length) % this.images.length; },
    }"
    @keydown.escape.window="lightbox = false"
    @keydown.left.window="if (lightbox) prev()"
    @keydown.right.window="if (lightbox) next()"
>
    <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col gap-12">
        <div class="flex flex-col items-center gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-0.5 bg-bcz-red"></div>
                <span class="text-bcz-red text-xs font-bold tracking-[3px]">GALÉRIA</span>
                <div class="w-10 h-0.5 bg-bcz-red"></div>
            </div>
            <h2 class="font-display font-bold text-4xl tracking-[0.5px]">FOTOGALÉRIA</h2>
        </div>

        <div class="columns-1 md:columns-2 lg:columns-3 gap-5 space-y-5">
            @foreach($allImages as $index => $image)
                <div class="break-inside-avoid group cursor-pointer">
                    <div class="relative rounded-xl overflow-hidden" @click="open({{ $index }})">
                        <img src="{{ $image['url'] }}" alt="{{ $image['name'] }}" class="w-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Fullscreen Lightbox --}}
    <template x-teleport="body">
        <div
            x-show="lightbox"
            x-cloak
            x-transition.opacity
            @click.self="lightbox = false"
            class="fixed inset-0 z-[9999] bg-black/95 flex items-center justify-center"
        >
            {{-- Close --}}
            <button @click="lightbox = false" class="absolute top-4 right-4 text-white/60 hover:text-white z-10 transition-colors">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>

            {{-- Previous --}}
            <button @click="prev()" class="absolute left-4 text-white/60 hover:text-white z-10 transition-colors" x-show="images.length > 1">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
            </button>

            {{-- Next --}}
            <button @click="next()" class="absolute right-4 text-white/60 hover:text-white z-10 transition-colors" x-show="images.length > 1">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
            </button>

            {{-- Image --}}
            <img :src="images[current]?.url" :alt="images[current]?.name || ''" class="max-h-[85vh] max-w-[90vw] object-contain rounded-lg">

            {{-- Counter --}}
            <div class="absolute bottom-4 flex flex-col items-center gap-2">
                <span class="text-white/40 text-sm" x-show="images.length > 1" x-text="(current + 1) + ' / ' + images.length"></span>
            </div>
        </div>
    </template>
</section>
