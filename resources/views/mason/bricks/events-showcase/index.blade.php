@if(! empty($events) && $events->count() > 0)
<section class="py-[100px]">
 <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
 {{-- Header --}}
 <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-6 mb-12">
 <div class="flex flex-col gap-3">
 @if(! empty($label))
 <div class="flex items-center gap-3">
 <div class="w-10 h-0.5 bg-bcz-red"></div>
 <span class="text-bcz-red text-xs font-bold tracking-widest">{{ brick_trans($label) }}</span>
 </div>
 @endif
 @if(! empty($title))
 <h2 class="font-display font-bold text-5xl tracking-wide text-white">{{ brick_trans($title) }}</h2>
 @endif
 </div>
 @php
 $viewAllText = brick_trans($view_all_text ?? []);
 $viewAllUrl = $view_all_url ?? '/eventy';
 @endphp
 @if($viewAllText)
 <a href="{{ $viewAllUrl }}"class="inline-flex items-center gap-2 border border-bcz-red text-bcz-red px-6 py-3 text-sm font-semibold hover:bg-bcz-red hover:text-white transition shrink-0">
 {{ $viewAllText }}
 <svg xmlns="http://www.w3.org/2000/svg"class="w-4 h-4"fill="none"viewBox="0 0 24 24"stroke="currentColor"stroke-width="2"><path stroke-linecap="round"stroke-linejoin="round"d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
 </a>
 @endif
 </div>

 {{-- Event Cards Grid --}}
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
 @foreach($events as $event)
 @php
 $category = $event->eventCategory;
 $categoryColor = $category?->color ?? '#FF2D2D';
 $categoryTitle = $category ? $category->getTranslation('title', app()->getLocale()) : '';
 $eventTitle = $event->getTranslation('title', app()->getLocale());
 $eventDescription = $event->getTranslation('card_description', app()->getLocale());
 $eventImage = $event->getFirstMediaUrl('card_image') ?: null;
 $eventDate = $event->date
 ? $event->date->translatedFormat('F Y')
 : '';
 $eventCity = $event->city ?? '';
 $dateLine = collect([$eventDate, $eventCity])->filter()->implode(' · ');
 @endphp
 <a href="/eventy/{{ $event->slug }}"class="bg-[#111111] overflow-hidden group hover:ring-1 hover:ring-[#333333] transition">
 {{-- Image --}}
 @if($eventImage)
 <div class="h-[200px] w-full overflow-hidden">
 <img src="{{ $eventImage }}"alt="{{ $eventTitle }}"class="h-full w-full object-cover group-hover:scale-105 transition duration-500">
 </div>
 @else
 <div class="h-[200px] w-full bg-[#1a1a1a] flex items-center justify-center">
 <svg xmlns="http://www.w3.org/2000/svg"class="w-10 h-10 text-[#333333]"fill="none"viewBox="0 0 24 24"stroke="currentColor"stroke-width="1"><path stroke-linecap="round"stroke-linejoin="round"d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/></svg>
 </div>
 @endif

 {{-- Content --}}
 <div class="p-5 flex flex-col gap-3">
 @if($categoryTitle)
 <div>
 <span class="inline-flex items-center px-2.5 py-1 text-[10px] font-bold tracking-wider uppercase"
 style="background-color: {{ $categoryColor }}20; color: {{ $categoryColor }};">
 {{ $categoryTitle }}
 </span>
 </div>
 @endif
 <h3 class="text-white text-lg font-bold">{{ $eventTitle }}</h3>
 @if($eventDescription)
 <p class="text-[#888888] text-[13px] leading-relaxed line-clamp-3">{{ $eventDescription }}</p>
 @endif
 @if($dateLine)
 <span class="text-[#666666] text-xs">{{ $dateLine }}</span>
 @endif
 </div>
 </a>
 @endforeach
 </div>
 </div>
</section>
@endif
