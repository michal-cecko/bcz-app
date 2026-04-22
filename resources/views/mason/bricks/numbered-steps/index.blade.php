@if(! empty($steps))
<section class="bg-[#0D0D0D] pb-[100px]">
 <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
 <div class="flex flex-col gap-12">
 @if(! empty($label) || ! empty($title) || ! empty($subtitle))
 <div class="flex flex-col items-center gap-4">
 @if(! empty($label))
 <div class="flex items-center gap-3">
 <div class="w-6 h-0.5 bg-bcz-red"></div>
 <span class="text-bcz-red text-xs font-bold tracking-widest">{{ brick_trans($label) }}</span>
 </div>
 @endif
 @if(! empty($title))
 <h2 class="font-display font-bold text-5xl tracking-wide">{{ brick_trans($title) }}</h2>
 @endif
 @if(! empty($subtitle))
 <p class="text-[#888888] text-lg text-center max-w-[600px]">{{ brick_trans($subtitle) }}</p>
 @endif
 </div>
 @endif

 <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
 @foreach($steps as $index => $step)
 <div class="bg-[#111111] border border-[#222222] p-6 flex flex-col gap-4">
 <div class="w-12 h-12 rounded-full bg-bcz-red text-white flex items-center justify-center font-bold text-lg">
 {{ $index + 1 }}
 </div>
 @if(! empty($step['title']))
 <h3 class="text-white text-xl font-bold">{{ brick_trans($step['title']) }}</h3>
 @endif
 @if(! empty($step['description']))
 <p class="text-[#888888] text-sm leading-relaxed">{!! brick_trans($step['description']) !!}</p>
 @endif
 </div>
 @endforeach
 </div>
 </div>
 </div>
</section>
@endif
