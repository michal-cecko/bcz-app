@php
    $centered = ($layout ?? 'left') === 'centered';
    $hasImage = ! empty($background_image);
@endphp

<section class="relative w-full {{ $hasImage ? ($centered ? 'h-[400px] md:h-[500px] lg:h-[600px]' : 'h-[500px] md:h-[650px] lg:h-[800px]') : ($centered ? 'py-[80px] md:py-[100px]' : '') }} overflow-hidden">
    @if($hasImage)
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ brick_media_url($background_image) }}')"></div>
        <div class="absolute inset-0 bg-gradient-to-{{ $centered ? 'b' : 't' }} from-bcz-dark {{ $centered ? 'via-transparent' : '' }} to-{{ $centered ? 'bcz-dark' : 'transparent' }}"></div>
    @endif

    <div class="relative w-full {{ $hasImage ? 'h-full' : '' }} flex flex-col {{ $centered ? 'items-center justify-center gap-8' : ($hasImage ? 'justify-end pb-20' : '') }} {{ $hasImage ? 'pt-[120px]' : 'pt-[140px] pb-[80px]' }}">
        <div class="{{ $centered ? 'flex flex-col items-center gap-8' : '' }} max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 w-full">

        @if(! empty($breadcrumb))
            <div class="flex items-center gap-3 {{ $centered ? 'mb-6' : 'mb-8' }}">
                @foreach($breadcrumb as $i => $crumb)
                    @if($i > 0)
                        <span class="text-[#444444] text-[11px]">/</span>
                    @endif
                    @if(! empty($crumb['url']))
                        <a href="{{ $crumb['url'] }}" class="text-bcz-muted text-[11px] font-medium tracking-widest hover:text-white transition-colors">{{ brick_trans($crumb['text'] ?? []) }}</a>
                    @else
                        <span class="text-bcz-red text-[11px] font-medium tracking-widest">{{ brick_trans($crumb['text'] ?? []) }}</span>
                    @endif
                @endforeach
            </div>
        @endif

        @if(! empty($badge))
            <div class="flex items-center gap-3 px-5 py-2.5 border border-bcz-red bg-bcz-red/10 w-fit {{ $centered ? '' : 'mb-8' }}">
                <span class="w-2 h-2 rounded-full bg-bcz-red"></span>
                <span class="text-bcz-red text-xs font-bold tracking-widest">{{ brick_trans($badge) }}</span>
            </div>
        @endif

        @if(! empty($title) || ! empty($title_accent))
            <div class="{{ $centered ? '' : 'mb-8' }}">
                @if(! empty($title))
                    <h1 class="font-display font-bold {{ $centered ? 'text-[36px] md:text-[56px] lg:text-[80px]' : 'text-[40px] md:text-[64px] lg:text-[96px]' }} leading-[0.95] tracking-wide {{ $centered ? 'text-center' : '' }}">{{ brick_trans($title) }}</h1>
                @endif
                @if(! empty($title_accent))
                    <h1 class="font-display font-bold {{ $centered ? 'text-[36px] md:text-[56px] lg:text-[80px]' : 'text-[40px] md:text-[64px] lg:text-[96px]' }} leading-[0.95] tracking-wide text-bcz-red {{ $centered ? 'text-center' : '' }}">{{ brick_trans($title_accent) }}</h1>
                @endif
            </div>
        @endif

        @if(! empty($subtitle))
            <p class="text-[#AAAAAA] text-xl {{ $centered ? 'text-center max-w-[700px]' : 'max-w-[600px] mb-8' }}">{{ brick_trans($subtitle) }}</p>
        @endif

        @php
            $ctaHref = brick_link(['link_type' => $cta_link_type ?? '', 'link_model_id' => $cta_link_model_id ?? '', 'link_url' => $cta_link_url ?? '']) ?? brick_trans($cta_url ?? []);
            $secondaryCtaHref = brick_link(['link_type' => $secondary_cta_link_type ?? '', 'link_model_id' => $secondary_cta_link_model_id ?? '', 'link_url' => $secondary_cta_link_url ?? '']) ?? brick_trans($secondary_cta_url ?? []);
        @endphp
        @if(! empty($cta_text) || ! empty($secondary_cta_text))
            <div class="flex flex-col sm:flex-row {{ $centered ? 'items-center' : 'items-start sm:items-center' }} gap-5 {{ $centered ? 'mt-4' : '' }}">
                @if(! empty($cta_text) && $ctaHref)
                    <a href="{{ $ctaHref }}" class="bg-bcz-red text-white text-sm font-bold tracking-widest px-9 py-4.5 flex items-center gap-3 hover:bg-red-700 transition-colors">
                        {{ brick_trans($cta_text) }}
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                @endif
                @if(! empty($secondary_cta_text) && $secondaryCtaHref)
                    <a href="{{ $secondaryCtaHref }}" class="border-2 border-white text-white text-sm font-bold tracking-widest px-9 py-4.5 flex items-center gap-3 hover:bg-white/10 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        {{ brick_trans($secondary_cta_text) }}
                    </a>
                @endif
            </div>
        @endif

        </div>

        @if($centered && ! empty($scroll_text))
            <div class="flex flex-col items-center gap-2 mt-4">
                <span class="text-[#555555] text-[10px] font-medium tracking-widest">{{ brick_trans($scroll_text) }}</span>
                <svg class="w-5 h-5 text-[#555555]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
        @endif
    </div>
</section>
