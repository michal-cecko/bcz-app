<div>
 {{-- Upcoming Competitions --}}
 @if($upcoming->isNotEmpty())
 <div class="flex flex-col gap-12">
 <div class="flex flex-col items-center gap-4">
 <div class="flex items-center gap-3">
 <div class="w-10 h-0.5 bg-bcz-red"></div>
 <span class="text-bcz-red text-xs font-bold tracking-[3px]">NADCHÁDZAJÚCE</span>
 <div class="w-10 h-0.5 bg-bcz-red"></div>
 </div>
 <h2 class="font-display font-bold text-[24px] md:text-[36px] lg:text-[48px] tracking-wide text-white text-center">NAJBLIŽŠIE SÚŤAŽE</h2>
 <p class="text-[#666666] text-lg text-center">Sledujte náš kalendár súťaží a príďte nás povzbudiť.</p>
 </div>

 <div class="flex flex-col gap-4">
 @foreach($upcoming as $competition)
 <a href="{{ route('event.show', $competition) }}"wire:navigate class="bg-[#111111] border border-[#222222] overflow-hidden flex flex-col md:flex-row hover:border-[#333333] transition-colors group">
 <div class="w-full md:w-[140px] {{ $loop->first ? 'bg-bcz-red' : 'bg-[#1A1A1A]' }} flex flex-col items-center justify-center py-6 md:py-0 shrink-0">
 @if($competition->date)
 <span class="font-display font-bold text-[36px] leading-none text-white">{{ $competition->date->format('d') }}</span>
 <span class="{{ $loop->first ? 'text-white/80' : 'text-[#888888]' }} text-[13px] font-semibold tracking-wider">{{ mb_strtoupper($competition->date->translatedFormat('M Y')) }}</span>
 @endif
 </div>
 <div class="flex-1 flex flex-col gap-3 p-6 md:p-8 justify-center">
 <h3 class="font-display font-bold text-[24px] md:text-[28px] tracking-wide text-white">{{ $competition->getTranslation('title', app()->getLocale()) }}</h3>
 @if($competition->competitionDetail?->disciplines->isNotEmpty())
 <div class="flex flex-wrap gap-2">
 @foreach($competition->competitionDetail->disciplines as $discipline)
 <span class="bg-bcz-red/[0.12] text-bcz-red text-[10px] font-bold tracking-wider px-3 py-1.5">{{ mb_strtoupper($discipline->getTranslation('name', app()->getLocale())) }}</span>
 @endforeach
 </div>
 @endif
 @if($competition->city || $competition->country)
 <div class="flex items-center gap-2 text-[#888888] text-sm">
 <svg class="w-4 h-4"fill="none"stroke="currentColor"viewBox="0 0 24 24"stroke-width="2"stroke-linecap="round"stroke-linejoin="round"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12"cy="10"r="3"/></svg>
 <span>{{ collect([$competition->city, $competition->country])->filter()->join(', ') }}</span>
 </div>
 @endif
 </div>
 <div class="flex items-center px-6 pb-6 md:pb-0 md:pr-8 justify-center">
 <span class="{{ $loop->first ? 'bg-bcz-red text-white' : 'border border-[#444444] text-white hover:border-bcz-red' }} text-[12px] font-bold tracking-wider px-6 py-3 transition-colors whitespace-nowrap">DETAIL</span>
 </div>
 </a>
 @endforeach
 </div>
 </div>
 @else
 <div class="text-center py-20">
 <p class="text-[#666666] text-lg">Momentálne nie sú naplánované žiadne súťaže.</p>
 </div>
 @endif
</div>
