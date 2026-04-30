@if($sponsors->isNotEmpty())
 @php
 // Pre-compute display data once per sponsor (logo URL, orientation, tag label)
 // so we can render the same item twice — once normally, once as an aria-hidden
 // clone — to make the mobile marquee loop seamlessly.
 $sponsorItems = $sponsors->map(function ($sponsor) {
 $logoMedia = $sponsor->getFirstMedia('logo');
 $logoSize = $logoMedia ? @getimagesize($logoMedia->getPath()) : false;
 return [
 'sponsor' => $sponsor,
 'logoUrl' => $logoMedia?->getUrl(),
 'tagLabel' => $sponsor->tag?->getLabel(),
 'isPortrait' => $logoSize && $logoSize[1] > $logoSize[0],
 ];
 });
 @endphp
 <div class="sponsors-strip overflow-hidden md:overflow-visible py-10">
 <div class="sponsors-track flex items-center md:justify-center md:flex-wrap flex-nowrap w-max md:w-auto">
 @foreach([false, true] as $isClone)
 @foreach($sponsorItems as $item)
 <a
 href="{{ $item['sponsor']->link ?? '#' }}"
 @class([
 'group flex flex-col items-center gap-3 transition-all shrink-0 [max-width:10rem] [padding-inline:1rem]',
 'sponsors-clone' => $isClone,
 ])
 target="_blank"
 rel="noopener"
 @if($isClone) aria-hidden="true" tabindex="-1" @endif
 >
 @if($item['logoUrl'])
 <img src="{{ $item['logoUrl'] }}" alt="{{ $isClone ? '' : $item['sponsor']->name }}" class="{{ $item['isPortrait'] ? 'h-[3.6rem]' : 'h-20' }} w-auto object-contain filter grayscale opacity-60 transition-all duration-300 group-hover:grayscale-0 group-hover:opacity-100">
 @else
 <div class="h-20 px-6 flex items-center justify-center bg-bcz-dark">
 <span class="text-bcz-border text-sm font-bold tracking-wider">{{ $item['sponsor']->name }}</span>
 </div>
 @endif
 <span class="text-sm font-semibold text-white/80 group-hover:text-white transition-colors text-center">{{ $item['sponsor']->name }}</span>
 @if($item['tagLabel'])
 <span class="text-xs text-white/40 -mt-2 uppercase tracking-wider text-center">{{ $item['tagLabel'] }}</span>
 @endif
 </a>
 @endforeach
 @endforeach
 </div>
 </div>

 <style>
 @media (max-width: 767px) {
 .sponsors-track {
 animation: sponsors-marquee 15s linear infinite;
 }
 }
 @media (min-width: 768px) {
 .sponsors-clone { display: none; }
 }
 @keyframes sponsors-marquee {
 from { transform: translateX(0); }
 to { transform: translateX(-50%); }
 }
 @media (prefers-reduced-motion: reduce) {
 .sponsors-track { animation: none; }
 }
 </style>
@endif
