<section class="bg-bcz-dark pb-20">
 <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col lg:flex-row gap-10 lg:gap-20">
 {{-- Left --}}
 <div class="flex-1 flex flex-col gap-8">
 @if(! empty($label))
 <div class="flex items-center gap-3">
 <div class="w-8 h-[3px] bg-bcz-red"></div>
 <span class="text-bcz-red text-xs font-bold tracking-[3px]">{{ brick_trans($label) }}</span>
 </div>
 @endif

 @if(! empty($title))
 <h2 class="font-display font-bold text-[28px] md:text-[40px] lg:text-[56px] leading-none tracking-wide">{!! brick_trans($title) !!}</h2>
 @endif

 @if(! empty($text))
 <div class="text-[#AAAAAA] text-base leading-[1.7] flex flex-col gap-8">{!! brick_trans($text ?? []) !!}</div>
 @endif
 </div>

 {{-- Right --}}
 @php $media = brick_media($image ?? null); @endphp
 @if($media->url)
 <div class="w-full lg:w-[500px] shrink-0">
 <img src="{{ $media->url }}"alt="{{ $media->alt }}"class="w-full lg:w-[500px] h-[500px] object-cover">
 </div>
 @endif
 </div>
</section>
