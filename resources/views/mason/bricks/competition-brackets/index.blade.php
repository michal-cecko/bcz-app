<section class="py-16">
 <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
 <div class="flex flex-col gap-3 mb-10">
 <div class="flex items-center gap-3">
 <div class="w-10 h-0.5 bg-bcz-red"></div>
 <span class="text-bcz-red text-xs font-bold tracking-widest">{{ __('bricks.competition.brackets_label') }}</span>
 </div>
 <h2 class="font-display font-bold text-3xl md:text-4xl tracking-wide text-white">
 {{ $event->getTranslation('title', app()->getLocale()) }}
 </h2>
 </div>

 @foreach($roundsWithBattles as $round)
 <div class="mb-10">
 <h3 class="text-white text-lg font-bold mb-5 flex items-center gap-3">
 <span class="w-2 h-2 rounded-full bg-bcz-red"></span>
 {{ $round->name ?? ('Round ' . $round->round_number) }}
 @if($round->athleteCategory)
 <span class="text-bcz-dim text-sm font-normal">{{ $round->athleteCategory->getTranslation('name', app()->getLocale()) }}</span>
 @endif
 </h3>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
 @foreach($round->battles as $battle)
 <div class="bg-[#111111] border border-[#1A1A1A] p-5">
 <div class="text-bcz-dim text-xs mb-3 font-semibold tracking-wider">
 {{ __('bricks.competition.battle') }} #{{ $battle->bracket_position }}
 </div>
 <div class="flex flex-col gap-2">
 {{-- Competitor A --}}
 <div class="flex items-center gap-3 py-2 px-3 {{ $battle->winner_side === 'a' ? 'bg-bcz-red/10 border border-bcz-red/20' : 'bg-[#0A0A0A]' }}">
 @if($battle->winner_side === 'a')
 <svg class="w-4 h-4 text-bcz-red shrink-0"fill="none"stroke="currentColor"viewBox="0 0 24 24"stroke-width="2"stroke-linecap="round"stroke-linejoin="round">
 <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5C7 4 9 7 12 7s5-3 7.5-3a2.5 2.5 0 0 1 0 5H18l-6 11L6 9Z"/>
 </svg>
 @endif
 <span class="text-white text-sm flex-1">
 {{ $battle->getCompetitorALabel() }}
 </span>
 </div>

 <div class="text-center text-[#333333] text-xs font-bold">VS</div>

 {{-- Competitor B --}}
 <div class="flex items-center gap-3 py-2 px-3 {{ $battle->winner_side === 'b' ? 'bg-bcz-red/10 border border-bcz-red/20' : 'bg-[#0A0A0A]' }}">
 @if($battle->winner_side === 'b')
 <svg class="w-4 h-4 text-bcz-red shrink-0"fill="none"stroke="currentColor"viewBox="0 0 24 24"stroke-width="2"stroke-linecap="round"stroke-linejoin="round">
 <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5C7 4 9 7 12 7s5-3 7.5-3a2.5 2.5 0 0 1 0 5H18l-6 11L6 9Z"/>
 </svg>
 @endif
 <span class="text-white text-sm flex-1">
 {{ $battle->getCompetitorBLabel() }}
 </span>
 </div>
 </div>
 </div>
 @endforeach
 </div>
 </div>
 @endforeach
 </div>
</section>
