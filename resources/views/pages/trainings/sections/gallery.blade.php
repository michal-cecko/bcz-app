    @php $galleryImages = $training->gallery_images ?? []; @endphp
    @if(count($galleryImages) > 0)
        <section class="bg-[#0A0A0A] py-20">
            <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col gap-12">
                {{-- Header --}}
                <div class="flex flex-col items-center gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                        <span class="text-bcz-red text-xs font-bold tracking-[3px]">{{ __('training_detail.gallery_label') }}</span>
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                    </div>
                    <h2 class="font-display font-bold text-4xl tracking-wide">{{ __('training_detail.gallery_title') }}</h2>
                    <p class="text-[#888888] text-base">{{ __('training_detail.gallery_subtitle') }}</p>
                </div>

                {{-- Masonry Grid --}}
                @php
                    $mediaItems = collect($galleryImages)->map(fn ($path) => (object) [
                        'url' => \Illuminate\Support\Facades\Storage::disk('public')->url($path),
                        'alt' => '',
                        'caption' => '',
                        'type' => 'image',
                    ])->filter(fn ($m) => $m->url)->values();
                    // Repeating tile aspect ratios keep the masonry rhythm at any column count.
                    $tileAspects = ['aspect-[4/3]', 'aspect-square', 'aspect-[3/2]'];
                    $jsData = $mediaItems->map(fn ($m) => ['url' => $m->url, 'alt' => $m->alt, 'caption' => $m->caption]);
                @endphp
                <div
                    x-data="{ lightbox: false, current: 0, items: {{ Js::from($jsData) }} }"
                    @keydown.escape.window="lightbox = false"
                    @keydown.left.window="if(lightbox) current = (current - 1 + items.length) % items.length"
                    @keydown.right.window="if(lightbox) current = (current + 1) % items.length"
                >
                    <div class="columns-1 sm:columns-2 lg:columns-3 gap-5">
                        @foreach($mediaItems as $index => $media)
                            @php
                                $isVideo = ($media->type ?? 'image') === 'video';
                            @endphp
                            <div
                                class="mb-5 break-inside-avoid {{ $tileAspects[$index % count($tileAspects)] }} rounded-lg overflow-hidden bg-[#1A1A1A] cursor-pointer relative group"
                                @if($media->url) @click="current = {{ $index }}; lightbox = true" @endif
                            >
                                @if($media->url)
                                    <img src="{{ $media->url }}" alt="{{ $media->alt ?? '' }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @endif
                                @if($isVideo)
                                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                                        <div class="w-16 h-16 rounded-full bg-bcz-red flex items-center justify-center">
                                            <svg class="w-7 h-7 text-white ml-1" fill="currentColor" viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21"/></svg>
                                        </div>
                                    </div>
                                @endif
                            </div>
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
            </div>
        </section>
    @endif
