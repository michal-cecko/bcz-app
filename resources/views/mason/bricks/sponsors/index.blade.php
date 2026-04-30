@if($sponsors->isNotEmpty())
 <div class="flex items-center justify-center gap-16 flex-wrap py-10">
 @foreach($sponsors as $sponsor)
 @php
 $logoMedia = $sponsor->getFirstMedia('logo');
 $logoUrl = $logoMedia?->getUrl();
 $tagLabel = $sponsor->tag?->getLabel();
 $logoSize = $logoMedia ? @getimagesize($logoMedia->getPath()) : false;
 $isPortrait = $logoSize && $logoSize[1] > $logoSize[0];
 @endphp
 <a href="{{ $sponsor->link ?? '#' }}"class="group flex flex-col items-center gap-3 transition-all"target="_blank"rel="noopener">
 @if($logoUrl)
 <img src="{{ $logoUrl }}"alt="{{ $sponsor->name }}"class="{{ $isPortrait ? 'h-[3.6rem]' : 'h-12' }} w-auto object-contain filter grayscale opacity-60 transition-all duration-300 group-hover:grayscale-0 group-hover:opacity-100">
 @else
 <div class="h-12 px-6 flex items-center justify-center bg-bcz-dark">
 <span class="text-bcz-border text-sm font-bold tracking-wider">{{ $sponsor->name }}</span>
 </div>
 @endif
 <span class="text-sm font-semibold text-white/80 group-hover:text-white transition-colors">{{ $sponsor->name }}</span>
 @if($tagLabel)
 <span class="text-xs text-white/40 -mt-2 uppercase tracking-wider">{{ $tagLabel }}</span>
 @endif
 </a>
 @endforeach
 </div>
@endif
