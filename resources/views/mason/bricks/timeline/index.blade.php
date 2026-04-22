@if(! empty($items))
<section class="bg-bcz-dark pb-[100px]">
 <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
 <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-{{ min(count($items), 4) }} gap-0">
 @foreach($items as $i => $item)
 <div class="flex flex-col gap-5 {{ $i < count($items) - 1 ? 'pr-8' : '' }}">
 <span class="font-display font-bold text-[32px] md:text-[48px] lg:text-[64px] text-bcz-red/20 tracking-wide">{{ $item['year'] ?? '' }}</span>
 <div class="flex items-center gap-4">
 <div class="w-4 h-4 rounded-full bg-bcz-red shrink-0"></div>
 @if($i < count($items) - 1)
 <div class="h-0.5 bg-[#222222] flex-1"></div>
 @endif
 </div>
 @if(! empty($item['title']))
 <h3 class="text-white text-xl font-bold">{{ brick_trans($item['title']) }}</h3>
 @endif
 @if(! empty($item['description']))
 <p class="text-[#666666] text-sm leading-relaxed">{!! brick_trans($item['description']) !!}</p>
 @endif
 </div>
 @endforeach
 </div>
 </div>
</section>
@endif
