@if(! empty($label) || ! empty($title) || ! empty($subtitle) || ! empty($people))
<section class="bg-[#111111] py-[100px]">
 <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
 <div class="flex flex-col gap-[60px]">
 @if(! empty($label) || ! empty($title) || ! empty($subtitle))
 <div class="flex flex-col items-center gap-4">
 @if(! empty($label))
 <div class="flex items-center gap-3">
 <div class="w-10 h-0.5 bg-bcz-red"></div>
 <span class="text-bcz-red text-xs font-bold tracking-widest">{{ brick_trans($label) }}</span>
 <div class="w-10 h-0.5 bg-bcz-red"></div>
 </div>
 @endif
 @if(! empty($title))
 <h2 class="font-display font-bold text-5xl tracking-wide">{{ brick_trans($title) }}</h2>
 @endif
 @if(! empty($subtitle))
 <p class="text-[#666666] text-lg text-center">{{ brick_trans($subtitle) }}</p>
 @endif
 </div>
 @endif

 @if(! empty($people))
 <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-{{ min(count($people), 4) }} gap-6">
 @foreach($people as $person)
 @php
 $personImageUrl = brick_media_url($person['image'] ?? null);
 $personUrl = brick_link(['link_type' => $person['person_link_type'] ?? '', 'link_model_id' => $person['person_link_model_id'] ?? '', 'link_url' => $person['person_link_url'] ?? '']);
 $personTag = $personUrl ? 'a' : 'div';
 @endphp
 <{{ $personTag }} @if($personUrl) href="{{ $personUrl }}"@endif class="bg-bcz-dark flex flex-col overflow-hidden group transition-all duration-300 {{ $personUrl ? 'hover:-translate-y-1 hover:shadow-lg hover:shadow-black/20' : '' }}">
 <div class="w-full h-[320px] bg-cover bg-center overflow-hidden"@if($personImageUrl) style="background-image: url('{{ $personImageUrl }}')"@endif>
 @if(! $personImageUrl)
 <div class="w-full h-full bg-[#1A1A1A] flex items-center justify-center">
 <svg class="w-20 h-20 text-[#333333]"fill="currentColor"viewBox="0 0 24 24"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
 </div>
 @endif
 </div>
 <div class="p-6 flex flex-col gap-2">
 @if(! empty($person['name']))
 <h3 class="text-white text-lg font-bold">{{ brick_trans($person['name']) }}</h3>
 @endif
 @if(! empty($person['role']))
 <span class="text-bcz-red text-[11px] font-medium tracking-wider">{{ brick_trans($person['role']) }}</span>
 @endif
 @if(! empty($person['description']))
 <p class="text-[#666666] text-[13px] leading-relaxed mt-1">{!! brick_trans($person['description']) !!}</p>
 @endif
 @if(! empty($person['tags']))
 <div class="flex flex-wrap gap-1 mt-1">
 @foreach($person['tags'] as $tag)
 <span class="text-[10px] bg-[#1A1A1A] text-[#666666] font-medium px-3 py-1.5">{{ $tag }}</span>
 @endforeach
 </div>
 @endif
 </div>
 </{{ $personTag }}>
 @endforeach
 </div>
 @endif

 @php
 $ctaHref = brick_link(['link_type' => $cta_link_type ?? '', 'link_model_id' => $cta_link_model_id ?? '', 'link_url' => $cta_link_url ?? '']);
 $ctaText = brick_trans($cta_text ?? []);
 @endphp
 @if($ctaText && $ctaHref)
 <div class="flex justify-center">
 <a href="{{ $ctaHref }}"class="inline-flex items-center gap-2 bg-bcz-red text-white font-semibold text-[15px] px-8 py-4 hover:bg-red-700 transition">
 {{ $ctaText }}
 </a>
 </div>
 @endif
 </div>
 </div>
</section>
@endif
