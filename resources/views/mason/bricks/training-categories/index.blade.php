@if(! empty($label) || ! empty($title) || ! empty($subtitle) || $categories->isNotEmpty())
<section class="bg-bcz-dark py-24">
    <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col gap-16">
        <div class="flex flex-col gap-4">
            @if(! empty($label))
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-widest">{{ brick_trans($label) }}</span>
                </div>
            @endif
            @if(! empty($title))
                <h2 class="font-display font-bold text-5xl tracking-wide">{{ brick_trans($title) }}</h2>
            @endif
            @if(! empty($subtitle))
                <p class="text-bcz-dim text-lg">{{ brick_trans($subtitle) }}</p>
            @endif
        </div>

        @if($categories->isNotEmpty())
            @php
                $catCount = $categories->count();
                $widthClass = match(true) {
                    $catCount === 1 => 'md:w-[50%]',
                    $catCount === 2 => 'md:w-[calc(50%-12px)]',
                    $catCount === 3 => 'md:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)]',
                    default => 'md:w-[calc(50%-12px)] lg:w-[calc(25%-18px)]',
                };
            @endphp
            <div class="flex flex-wrap justify-center gap-6">
                @foreach($categories as $i => $category)
                    <div class="bg-bcz-card border border-bcz-border flex flex-col overflow-hidden w-full {{ $widthClass }}">
                        @php $catImgUrl = brick_media_url($category->hero_image); @endphp
                        @if($catImgUrl)
                            <div class="w-full h-[280px] bg-cover bg-center" style="background-image: url('{{ $catImgUrl }}')"></div>
                        @endif
                        <div class="p-8 flex flex-col gap-4">
                            <span class="font-display font-bold text-5xl text-bcz-red/20">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <h3 class="font-display font-bold text-[28px] tracking-wide">{{ $category->name }}</h3>
                            @if($category->description)
                                <p class="text-bcz-muted text-[15px] leading-relaxed">{!! $category->description !!}</p>
                            @endif
                            @if($category->slug)
                                <a href="{{ route('treningy') }}#{{ $category->slug }}" class="flex items-center gap-2 text-bcz-red text-xs font-bold tracking-widest hover:gap-3 transition-all">
                                    {{ __('bricks.training_categories.view_trainings') }}
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @php
            $ctaHref = brick_link(['link_type' => $cta_link_type ?? '', 'link_model_id' => $cta_link_model_id ?? '', 'link_url' => $cta_link_url ?? '']);
            $ctaText = brick_trans($cta_text ?? []);
        @endphp
        @if($ctaText && $ctaHref)
            <div class="flex justify-center">
                <a href="{{ $ctaHref }}" class="inline-flex items-center gap-2 text-bcz-red text-xs font-bold tracking-widest hover:gap-3 transition-all">
                    {{ $ctaText }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        @endif
    </div>
</section>
@endif
