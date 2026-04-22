<section class="pb-16">
 <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
 <div class="flex flex-col gap-3 mb-10">
 <div class="flex items-center gap-3">
 <div class="w-10 h-0.5 bg-bcz-red"></div>
 <span class="text-bcz-red text-xs font-bold tracking-widest">{{ __('bricks.competition.results_label') }}</span>
 </div>
 <h2 class="font-display font-bold text-3xl md:text-4xl tracking-wide text-white">
 {{ $event->getTranslation('title', app()->getLocale()) }}
 </h2>
 </div>

 @foreach($detail->athleteCategories as $category)
 @php
 $categoryRounds = $detail->rounds->where('athlete_category_id', $category->id)->sortBy('sort_order');
 @endphp

 @if($categoryRounds->isNotEmpty())
 <div class="mb-10">
 <h3 class="text-white text-xl font-bold mb-5 flex items-center gap-3">
 <span class="w-2 h-2 rounded-full bg-bcz-red"></span>
 {{ $category->getTranslation('name', app()->getLocale()) }}
 </h3>

 @foreach($categoryRounds as $round)
 @php
 $allResults = $round->parts->flatMap(fn ($part) => $part->results)->sortBy('place');
 @endphp

 @if($allResults->isNotEmpty())
 <div class="bg-[#111111] border border-[#1A1A1A] p-6 mb-4">
 <h4 class="text-bcz-dim text-sm font-semibold mb-4">
 {{ $round->name ?? ('Round ' . $round->round_number) }}
 </h4>
 <div class="flex flex-col gap-2">
 @foreach($allResults as $result)
 <div class="flex items-center gap-4 py-2 {{ !$loop->last ? 'border-b border-[#1A1A1A]' : '' }}">
 <span class="w-8 text-center shrink-0 {{ $result->place <= 3 ? 'text-bcz-red font-bold' : 'text-bcz-dim' }} text-sm">
 {{ $result->place ? '#' . $result->place : '-' }}
 </span>
 <span class="text-white text-sm flex-1">
 {{ $result->user?->name ?? __('bricks.competition.unknown_athlete') }}
 </span>
 @if($result->score !== null)
 <span class="text-bcz-dim text-sm font-mono">{{ number_format($result->score, 2) }}</span>
 @endif
 </div>
 @endforeach
 </div>
 </div>
 @endif
 @endforeach
 </div>
 @endif
 @endforeach

 {{-- Rounds without a specific category --}}
 @php
 $uncategorizedRounds = $detail->rounds->whereNull('athlete_category_id')->sortBy('sort_order');
 @endphp

 @foreach($uncategorizedRounds as $round)
 @php
 $allResults = $round->parts->flatMap(fn ($part) => $part->results)->sortBy('place');
 @endphp

 @if($allResults->isNotEmpty())
 <div class="bg-[#111111] border border-[#1A1A1A] p-6 mb-4">
 <h4 class="text-white text-lg font-bold mb-4">
 {{ $round->name ?? ('Round ' . $round->round_number) }}
 </h4>
 <div class="flex flex-col gap-2">
 @foreach($allResults as $result)
 <div class="flex items-center gap-4 py-2 {{ !$loop->last ? 'border-b border-[#1A1A1A]' : '' }}">
 <span class="w-8 text-center shrink-0 {{ $result->place <= 3 ? 'text-bcz-red font-bold' : 'text-bcz-dim' }} text-sm">
 {{ $result->place ? '#' . $result->place : '-' }}
 </span>
 <span class="text-white text-sm flex-1">
 {{ $result->user?->name ?? __('bricks.competition.unknown_athlete') }}
 </span>
 @if($result->score !== null)
 <span class="text-bcz-dim text-sm font-mono">{{ number_format($result->score, 2) }}</span>
 @endif
 </div>
 @endforeach
 </div>
 </div>
 @endif
 @endforeach
 </div>
</section>
