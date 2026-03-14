@if(! empty($label) || ! empty($title) || ! empty($images))
<section class="bg-bcz-dark py-[100px]">
    <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
    <div class="flex flex-col gap-12">
        @if(! empty($label) || ! empty($title))
            <div class="flex flex-col gap-4">
                @if(! empty($label))
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                        <span class="text-bcz-red text-xs font-bold tracking-[3px]">{{ brick_trans($label) }}</span>
                    </div>
                @endif
                @if(! empty($title))
                    <h2 class="font-display font-bold text-5xl tracking-wide">{{ brick_trans($title) }}</h2>
                @endif
            </div>
        @endif

        @if(! empty($images))
            @php
                $mediaItems = collect($images)->map(fn ($img) => brick_media($img['image'] ?? null))->values();
                $colImages = [[], [], []];
                foreach ($mediaItems as $i => $item) {
                    $colImages[$i % 3][] = $item;
                }
                $ratios = [[7, 5], [5, 7], [6, 6]];
                $jsData = $mediaItems->filter(fn ($m) => $m->url)->values()->map(fn ($m) => ['url' => $m->url, 'alt' => $m->alt ?? '', 'caption' => $m->caption ?? '']);
            @endphp
            <div
                x-data="{ lightbox: false, current: 0, items: {{ Js::from($jsData) }} }"
                @keydown.escape.window="lightbox = false"
                @keydown.left.window="if(lightbox) current = (current - 1 + items.length) % items.length"
                @keydown.right.window="if(lightbox) current = (current + 1) % items.length"
            >
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5" style="height: 500px">
                    @php $globalIdx = 0; @endphp
                    @foreach($colImages as $colIndex => $col)
                        @if(count($col) > 0)
                            <div class="flex flex-col gap-5 h-full">
                                @foreach($col as $imgIndex => $media)
                                    @php $ratio = $ratios[$colIndex][$imgIndex] ?? 6; @endphp
                                    <div
                                        class="rounded-lg overflow-hidden bg-[#1A1A1A] cursor-pointer relative group min-h-0"
                                        style="flex: {{ $ratio }}"
                                        @if($media->url) @click="current = {{ $globalIdx }}; lightbox = true" @endif
                                    >
                                        @if($media->url)
                                            <img src="{{ $media->url }}" alt="{{ $media->alt ?? '' }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        @endif
                                    </div>
                                    @php $globalIdx++; @endphp
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- Lightbox --}}
                <template x-teleport="body">
                    <div x-show="lightbox" x-transition.opacity class="fixed inset-0 z-50 bg-black/95 flex items-center justify-center" @click.self="lightbox = false">
                        <button @click="lightbox = false" class="absolute top-6 right-6 text-white/70 hover:text-white z-10">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                        </button>
                        <a :href="items[current]?.url" download class="absolute top-6 right-20 text-white/70 hover:text-white z-10">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        </a>
                        <button @click.stop="current = (current - 1 + items.length) % items.length" class="absolute left-4 md:left-8 text-white/70 hover:text-white z-10">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                        </button>
                        <button @click.stop="current = (current + 1) % items.length" class="absolute right-4 md:right-8 text-white/70 hover:text-white z-10">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                        </button>
                        <div class="flex flex-col items-center gap-4 max-w-[90vw]">
                            <img :src="items[current]?.url" :alt="items[current]?.alt" class="max-h-[75vh] max-w-full object-contain">
                            <div x-show="items[current]?.caption" class="text-white/80 text-sm text-center max-w-2xl" x-text="items[current]?.caption"></div>
                        </div>
                        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 text-white/50 text-sm" x-text="(current + 1) + ' / ' + items.length"></div>
                    </div>
                </template>
            </div>
        @endif
    </div>
    </div>
</section>
@endif
