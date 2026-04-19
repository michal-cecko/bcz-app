<section class="py-16">
 <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
 <div class="flex flex-col gap-3 mb-10">
 <div class="flex items-center gap-3">
 <div class="w-10 h-0.5 bg-bcz-red"></div>
 <span class="text-bcz-red text-xs font-bold tracking-widest">{{ __('bricks.competition.timetable_label') }}</span>
 </div>
 <h2 class="font-display font-bold text-3xl md:text-4xl tracking-wide text-white">
 {{ $event->getTranslation('title', app()->getLocale()) }}
 </h2>
 </div>

 <div class="bg-[#111111] border border-[#1A1A1A] p-6">
 <div class="flex flex-col gap-1">
 @foreach($entries as $entry)
 <div class="flex items-center gap-4 py-3 {{ !$loop->last ? 'border-b border-[#1A1A1A]' : '' }}">
 <span class="text-bcz-dim text-sm w-16 shrink-0 font-mono">
 {{ $entry->scheduled_time?->format('H:i') }}
 </span>

 <span class="text-white text-sm flex-1">
 {{ $entry->getTranslation('title', app()->getLocale()) }}
 </span>

 @if($entry->status->value === 'finished')
 <span class="text-xs px-2.5 py-1 rounded-full bg-green-500/10 text-green-500 font-semibold">
 {{ __('bricks.competition.status_finished') }}
 </span>
 @elseif($entry->status->value === 'in_progress')
 <span class="text-xs px-2.5 py-1 rounded-full bg-yellow-500/10 text-yellow-500 font-semibold flex items-center gap-1.5">
 <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 animate-pulse"></span>
 {{ __('bricks.competition.status_in_progress') }}
 </span>
 @endif

 @if($entry->actual_start_time && $entry->actual_end_time)
 <span class="text-[#444444] text-xs">
 {{ $entry->actual_start_time->format('H:i') }} - {{ $entry->actual_end_time->format('H:i') }}
 </span>
 @endif
 </div>
 @endforeach
 </div>
 </div>
 </div>
</section>
