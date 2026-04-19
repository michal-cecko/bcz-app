<section class="relative w-full h-[450px] md:h-[580px] lg:h-[700px] overflow-hidden">
 @if(! empty($background_image))
 <div class="absolute inset-0 bg-cover bg-center"style="background-image: url('{{ brick_media_url($background_image) }}')"></div>
 @endif
 <div class="absolute inset-0"style="background: linear-gradient(180deg, #0A0A0A 0%, #0A0A0A44 30%, #0A0A0A88 60%, #0A0A0A 100%)"></div>

 <div class="relative w-full h-full flex flex-col justify-end pb-20">
 <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 w-full flex flex-col gap-6">
 {{-- Breadcrumbs --}}
 @if(! empty($breadcrumb))
 <div class="flex items-center gap-2 text-xs">
 @foreach($breadcrumb as $i => $crumb)
 @if($i > 0)
 <span class="text-[#444444]">/</span>
 @endif
 @if(! empty($crumb['url']))
 <a href="{{ $crumb['url'] }}"class="text-[#888888] hover:text-white transition-colors">{{ brick_trans($crumb['text'] ?? []) }}</a>
 @else
 <span class="text-bcz-red">{{ brick_trans($crumb['text'] ?? []) }}</span>
 @endif
 @endforeach
 </div>
 @endif

 {{-- Badge --}}
 @if(! empty($badge))
 <div class="bg-bcz-red px-4 py-1.5 w-fit">
 <span class="text-white text-[11px] font-bold tracking-[2px]">{{ brick_trans($badge) }}</span>
 </div>
 @endif

 {{-- Title --}}
 @if(! empty($title))
 <h1 class="font-display font-bold text-[40px] md:text-[64px] lg:text-[96px] leading-[0.95] tracking-wide uppercase">{{ brick_trans($title) }}</h1>
 @endif

 {{-- Subtitle --}}
 @if(! empty($subtitle))
 <span class="text-bcz-red text-lg font-medium tracking-wider">{{ brick_trans($subtitle) }}</span>
 @endif
 </div>
 </div>
</section>
