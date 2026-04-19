@extends('layouts.public')

@section('title', $event->getTranslation('title', app()->getLocale()) . ' | BCZ Club')

@php
 $locale = app()->getLocale();
 $categoryColor = $event->eventCategory?->color ?? '#FF2D2D';
 $categoryName = $event->eventCategory?->getTranslation('title', $locale) ?? '';
 $title = $event->getTranslation('title', $locale);
 $description = $event->getTranslation('card_description', $locale);
 $heroImage = $event->getFirstMediaUrl('detail_image') ?: $event->getFirstMediaUrl('card_image');
 $galleryImages = $event->getMedia('gallery');
 $detail = $event->competitionDetail;
 $org = $event->organization;
 $status = $event->status;
 $eventTz = $event->getTimezone();

 $statusLabel = match($status) {
 'registering' => __('event_detail.status_registration_open'),
 'in_progress' => __('event_detail.status_in_progress'),
 'finished' => __('event_detail.status_finished'),
 'countdown', 'upcoming' => __('event_detail.status_upcoming'),
 default => __('event_detail.status_upcoming'),
 };

 $statusColor = match($status) {
 'registering' => '#22C55E',
 'in_progress' => '#F59E0B',
 'finished' => '#666666',
 default => $categoryColor,
 };

 $location = collect([$event->place_name, $event->city, $event->country])->filter()->join(', ');
@endphp

@section('content')
 @unless($event->is_published)
     <x-preview-banner />
 @endunless
 {{-- Hero Section --}}
 <section class="relative w-full h-[500px] overflow-hidden">
 @if($heroImage)
 <div class="absolute inset-0">
 <img src="{{ $heroImage }}"alt="{{ $title }}"class="w-full h-full object-cover">
 </div>
 @else
 <div class="absolute inset-0 bg-[#1A1A1A]"></div>
 @endif
 <div class="absolute inset-0 bg-gradient-to-b from-[#0A0A0A]/0 to-[#0A0A0A]"></div>

 <div class="relative w-full h-full flex flex-col justify-end gap-4 max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 pb-[60px]">
 {{-- Breadcrumb --}}
 <div class="flex items-center gap-2">
 <a href="{{ route('home') }}"class="text-[#888888] text-sm font-normal font-sans hover:text-white transition-colors">{{ __('event_detail.home') }}</a>
 <span class="text-[#444444] text-sm">/</span>
 <a href="{{ route('sutaze') }}"class="text-[#888888] text-sm font-normal font-sans hover:text-white transition-colors">{{ __('event_detail.competitions') }}</a>
 <span class="text-[#444444] text-sm">/</span>
 <span class="text-sm font-normal font-sans"style="color: {{ $categoryColor }}">{{ $title }}</span>
 </div>

 {{-- Category Badge --}}
 @if($categoryName)
 <div class="w-fit px-3.5 py-1.5"style="background-color: {{ $categoryColor }}">
 <span class="text-white text-xs font-bold tracking-wide">{{ mb_strtoupper($categoryName) }}</span>
 </div>
 @endif

 {{-- Title --}}
 <h1 class="font-display font-bold text-[36px] md:text-[48px] lg:text-[56px] tracking-wide text-white leading-tight">
 {{ $title }}
 </h1>

 </div>
 </section>

 {{-- Info Strip --}}
 <section class="bg-[#0A0A0A]">
 <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 mt-6">
 <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
 {{-- Date --}}
 <div class="bg-[#111111] border border-[#222222] p-5 flex flex-col gap-1.5">
 <span class="text-[#888888] text-xs font-sans">{{ __('event_detail.date') }}</span>
 <span class="text-white text-sm font-bold font-sans">
 @if($event->date)
 {{ $event->date->translatedFormat('d. M Y') }}
 @if($event->date_end && !$event->date->isSameDay($event->date_end))
 &mdash; {{ $event->date_end->translatedFormat('d. M Y') }}
 @endif
 @else
 &mdash;
 @endif
 </span>
 </div>

 {{-- Location --}}
 <div class="bg-[#111111] border border-[#222222] p-5 flex flex-col gap-1.5">
 <span class="text-[#888888] text-xs font-sans">{{ __('event_detail.place') }}</span>
 <span class="text-white text-sm font-bold font-sans">{{ $location ?: '&mdash;' }}</span>
 </div>

 {{-- Format --}}
 <div class="bg-[#111111] border border-[#222222] p-5 flex flex-col gap-1.5">
 <span class="text-[#888888] text-xs font-sans">{{ __('event_detail.format') }}</span>
 <span class="text-white text-sm font-bold font-sans">
 @if($detail?->rounds->isNotEmpty())
 @php
 $hasBattles = $detail->rounds->contains(fn ($r) => $r->advancement_type === \App\Enums\RoundAdvancementTypeEnum::BATTLE_WINNER);
 $hasPoints = $detail->rounds->contains(fn ($r) => $r->scoring_format === \App\Enums\ScoringFormatEnum::POINTS);
 @endphp
 @if($hasBattles && $hasPoints) {{ __('event_detail.format_battle_and_points') }} @elseif($hasBattles) {{ __('event_detail.format_battle') }} @elseif($hasPoints) {{ __('event_detail.format_points') }} @else {{ __('event_detail.format_mix') }} @endif
 @else
 &mdash;
 @endif
 </span>
 </div>

 {{-- Categories --}}
 <div class="bg-[#111111] border border-[#222222] p-5 flex flex-col gap-1.5">
 <span class="text-[#888888] text-xs font-sans">{{ __('event_detail.categories') }}</span>
 <span class="text-white text-sm font-bold font-sans">
 @if($detail?->athleteCategories->isNotEmpty())
 {{ $detail->athleteCategories->map(fn ($c) => $c->getTranslation('name', $locale))->join(', ') }}
 @else
 &mdash;
 @endif
 </span>
 </div>

 {{-- Status --}}
 <div class="bg-[#111111] border border-[#222222] p-5 flex flex-col gap-1.5">
 <span class="text-[#888888] text-xs font-sans">{{ __('event_detail.status') }}</span>
 @if($status === 'registering' && $org?->registration_closes_at && $org->registration_closes_at->isFuture())
 <div x-data="countdown('{{ $org->registration_closes_at->toIso8601String() }}')" x-init="start()" class="flex flex-col gap-1">
     <span class="text-sm font-bold font-sans" style="color: {{ $statusColor }}">{{ $statusLabel }}</span>
     <span class="text-bcz-muted text-xs font-mono tabular-nums" x-text="(days > 0 ? days + 'd ' : '') + hours.toString().padStart(2,'0') + 'h ' + minutes.toString().padStart(2,'0') + 'm ' + seconds.toString().padStart(2,'0') + 's'"></span>
 </div>
 @else
 <span class="text-sm font-bold font-sans" style="color: {{ $statusColor }}">{{ $statusLabel }}</span>
 @endif
 </div>
 </div>
 </div>
 </section>

 {{-- Live Status Bar (only when in_progress) --}}
 @if($status === 'in_progress')
 @php
     $currentEntry = $detail?->timetableEntries->firstWhere('status', \App\Enums\TimetableEntryStatusEnum::IN_PROGRESS);
     $totalEntries = $detail?->timetableEntries->count() ?? 0;
     $finishedEntries = $detail?->timetableEntries->where('status', \App\Enums\TimetableEntryStatusEnum::FINISHED)->count() ?? 0;

     // Sub-progress within current entry (competitor/battle position)
     $subProgress = 0.5; // default: halfway through non-round entries
     if ($currentEntry?->type === \App\Enums\TimetableEntryTypeEnum::COMPETITION_ROUND && $currentEntry->competitionRound) {
         if ($currentEntry->isBattleRound()) {
             $totalBattles = $currentEntry->competitionRound->battles->count();
             $currentBattleIndex = $currentEntry->current_battle_id
                 ? $currentEntry->competitionRound->battles->search(fn ($b) => $b->id === $currentEntry->current_battle_id)
                 : 0;
             $subProgress = $totalBattles > 0 ? ($currentBattleIndex + 0.5) / $totalBattles : 0.5;
         } else {
             $totalCompetitors = $currentEntry->getOrderedCompetitors()->count();
             $currentIndex = $currentEntry->current_competitor_index ?? 0;
             $subProgress = $totalCompetitors > 0 ? ($currentIndex + 0.5) / $totalCompetitors : 0.5;
         }
     }
     $progressPct = $totalEntries > 0 ? min(100, round((($finishedEntries + $subProgress) / $totalEntries) * 100)) : 0;
     $delayMinutes = $currentEntry?->getDelayMinutes() ?? 0;
     $performerLabel = $currentEntry?->getCurrentPerformerLabel();
 @endphp
 <section class="bg-[#111111]">
 <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 py-8 flex flex-col gap-5">
     {{-- Top row: live badge + event name | time + delay --}}
     <div class="flex items-center justify-between gap-4 flex-wrap">
         <div class="flex items-center gap-4">
             <div class="flex items-center gap-1.5 bg-[#FF2D2D] rounded-full px-3.5 py-1.5">
                 <span class="w-2 h-2 rounded-full animate-[dot-pulse_1s_ease-in-out_infinite] bg-white"></span>
                 <span class="text-white text-xs font-bold tracking-wider font-sans">LIVE</span>
             </div>
             <style>@keyframes dot-pulse{0%,100%{background-color:#fff}50%{background-color:#FF2D2D}}</style>
             @if($currentEntry)
             <div class="flex flex-col">
                 <span class="text-white text-base font-semibold font-sans">{{ $currentEntry->getTranslation('title', $locale) }}</span>
                 @if($performerLabel)
                 <span class="text-[#FF2D2D] text-sm font-semibold font-sans">{{ $performerLabel }}</span>
                 @endif
             </div>
             @endif
         </div>
         <div class="flex items-center gap-4">
             <span class="text-white text-2xl font-bold font-sans" x-data x-text="new Date().toLocaleTimeString('{{ $locale }}', {hour:'2-digit', minute:'2-digit', timeZone:'{{ $eventTz }}'})"></span>
             @if($delayMinutes > 0)
             <span class="bg-[#FF2D2D20] text-[#FF2D2D] text-xs font-semibold font-sans rounded-full px-3 py-1">{{ __('event_detail.live_delay', ['minutes' => $delayMinutes]) }}</span>
             @endif
         </div>
     </div>
     {{-- Progress bar --}}
     <div class="flex flex-col gap-2">
         <div class="flex items-center justify-between">
             <span class="text-[#888888] text-xs font-sans">{{ __('event_detail.live_day_progress') }}</span>
             <span class="text-[#888888] text-xs font-sans">{{ $progressPct }}%</span>
         </div>
         <div class="w-full h-1.5 rounded-full bg-[#1A1A1A] overflow-hidden">
             <div class="h-full rounded-full bg-[#FF2D2D]" style="width: {{ $progressPct }}%"></div>
         </div>
     </div>
 </div>
 </section>
 @endif

 {{-- Tab Bar + Content --}}
 @php
 $tabs = [
     'popis' => __('event_detail.tab_description'),
     'harmonogram' => __('event_detail.tab_timetable'),
 ];
 if ($detail?->rounds->isNotEmpty()) {
     $tabs['vysledky'] = __('event_detail.tab_results');
 }
 if ($status !== 'finished') {
     $tabs['registracia'] = __('event_detail.tab_registration');
 }
 $tabKeys = json_encode(array_keys($tabs));
 @endphp
 <section id="competition-tabs" class="bg-[#0A0A0A]" x-data="{
     validTabs: {{ $tabKeys }},
     tab: '',
     init() {
         const h = window.location.hash.replace('#', '');
         this.tab = this.validTabs.includes(h) ? h : 'popis';
         window.addEventListener('hashchange', () => {
             const t = location.hash.replace('#', '');
             if (this.validTabs.includes(t)) this.tab = t;
         });
     },
     setTab(t) { this.tab = t; history.replaceState(null, '', '#' + t); },
 }">
 <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 pt-[40px] pb-[60px]">
 {{-- Tab Bar --}}
 <div class="flex items-center gap-0 bg-[#111111] border-b border-[#222222] mb-10 overflow-x-auto">
 @foreach($tabs as $key => $label)
 <button
 x-on:click="setTab('{{ $key }}')"
 :class="tab === '{{ $key }}' ? 'border-b-[3px] text-white' : 'border-b-[3px] border-transparent text-[#888888] hover:text-white'"
 class="px-6 py-4 text-sm font-bold font-sans whitespace-nowrap transition-colors"
 :style="tab === '{{ $key }}' ? 'border-color: {{ $categoryColor }}' : ''"
 >
 {!! $label !!}
 </button>
 @endforeach
 </div>

 {{-- Tab: Popis --}}
 <div x-show="tab === 'popis'"x-cloak>
 <div class="flex flex-col lg:flex-row gap-16">
 {{-- Main Content --}}
 <div class="flex-1 min-w-0 flex flex-col gap-4">
 @if($renderedContent)
 {!! $renderedContent !!}
 @elseif($description)
 <div class="flex flex-col gap-6">
 <h2 class="font-display font-bold text-[32px] tracking-wide text-white">{{ __('event_detail.about_competition') }}</h2>
 <p class="text-[#CCCCCC] text-base leading-relaxed font-sans">{{ $description }}</p>
 </div>
 @endif

 {{-- Súťažné kategórie --}}
 @if($detail?->athleteCategories->isNotEmpty())
 <div class="flex flex-col gap-6 mt-8">
 <h2 class="font-display font-bold text-[24px] tracking-wide text-white">{{ __('event_detail.athlete_categories_title') }}</h2>
 <div class="flex flex-col">
 {{-- Table Header --}}
 <div class="hidden lg:flex items-center border-b border-[#333333] pr-4">
     <div class="w-[160px] shrink-0 py-2.5 pl-5">
         <span class="text-[#666666] text-[11px] font-semibold font-sans tracking-wider">{{ __('event_detail.categories') }}</span>
     </div>
     <div class="flex-1 flex gap-2 py-2.5">
         <span class="flex-1 text-[#666666] text-[11px] font-semibold font-sans tracking-wider">{{ __('event_detail.weight') }}</span>
         <span class="flex-1 text-[#666666] text-[11px] font-semibold font-sans tracking-wider">{{ __('event_detail.age') }}</span>
         <span class="flex-1 text-[#666666] text-[11px] font-semibold font-sans tracking-wider">{{ __('event_detail.format') }}</span>
         <span class="flex-1 text-[#666666] text-[11px] font-semibold font-sans tracking-wider">{{ __('event_detail.advancement') }}</span>
     </div>
     <div class="w-[130px] shrink-0"></div>
 </div>
 {{-- Table Rows --}}
 @foreach($detail->athleteCategories->sortBy('sort_order') as $cat)
 @php
     $catRounds = $detail->rounds->where('athlete_category_id', $cat->id);
     $catRegistered = $event->registrations->filter(fn ($r) => ($r->athlete_category_id ?? null) === $cat->id)->count();
     $advanceCount = $catRounds->max('advance_count');
     $hasBattle = $catRounds->contains(fn ($r) => $r->advancement_type === \App\Enums\RoundAdvancementTypeEnum::BATTLE_WINNER);
     $hasPoints = $catRounds->contains(fn ($r) => $r->scoring_format === \App\Enums\ScoringFormatEnum::POINTS);
     $formatLabel = $hasBattle && $hasPoints ? __('event_detail.format_battle_and_points') : ($hasBattle ? __('event_detail.format_battle') : ($hasPoints ? __('event_detail.format_points') : '&mdash;'));
     $weightLabel = $cat->min_weight && $cat->max_weight ? __('event_detail.weight_range', ['min' => $cat->min_weight, 'max' => $cat->max_weight]) : ($cat->max_weight ? __('event_detail.weight_to', ['max' => $cat->max_weight]) : ($cat->min_weight ? __('event_detail.weight_from', ['min' => $cat->min_weight]) : __('event_detail.weight_none')));
     $ageLabel = $cat->min_age && $cat->max_age ? __('event_detail.age_range', ['min' => $cat->min_age, 'max' => $cat->max_age]) : ($cat->min_age ? __('event_detail.age_from', ['min' => $cat->min_age]) : '&mdash;');
 @endphp
 {{-- Desktop: table row --}}
 <div class="hidden lg:flex items-center bg-[#111111] border-b border-[#222222] pr-4">
     <div class="w-[160px] shrink-0 py-3 pl-5">
         <span class="text-base font-bold font-sans" style="color: {{ $categoryColor }}">{{ $cat->getTranslation('name', $locale) }}</span>
     </div>
     <div class="flex-1 flex gap-2 py-3">
         <span class="flex-1 text-white text-sm font-semibold font-sans">{{ $weightLabel }}</span>
         <span class="flex-1 text-white text-sm font-semibold font-sans">{!! $ageLabel !!}</span>
         <span class="flex-1 text-white text-sm font-semibold font-sans">{!! $formatLabel !!}</span>
         <span class="flex-1 text-sm font-semibold font-sans" style="color: #22C55E">
             @if($advanceCount)
                 {{ __('event_detail.top_advance', ['count' => $advanceCount]) }} &rarr; {{ __('event_detail.format_battle') }}
             @else
                 &mdash;
             @endif
         </span>
     </div>
     <div class="w-[130px] shrink-0 flex justify-center">
         <span class="text-xs font-semibold rounded-full px-3 py-1" style="color: {{ $categoryColor }}; background-color: {{ $categoryColor }}20">
             {{ trans_choice('event_detail.participants_count', $catRegistered) }}
         </span>
     </div>
 </div>
 {{-- Mobile: stacked card --}}
 <div class="lg:hidden bg-[#111111] border-b border-[#222222] p-4 flex flex-col gap-3">
     <div class="flex items-center justify-between">
         <span class="text-base font-bold font-sans" style="color: {{ $categoryColor }}">{{ $cat->getTranslation('name', $locale) }}</span>
         <span class="text-xs font-semibold rounded-full px-3 py-1" style="color: {{ $categoryColor }}; background-color: {{ $categoryColor }}20">
             {{ trans_choice('event_detail.participants_count', $catRegistered) }}
         </span>
     </div>
     <div class="grid grid-cols-2 gap-3">
         <div class="flex flex-col gap-0.5">
             <span class="text-[#666666] text-[11px] font-sans tracking-wider">{{ __('event_detail.weight') }}</span>
             <span class="text-white text-sm font-semibold font-sans">{{ $weightLabel }}</span>
         </div>
         <div class="flex flex-col gap-0.5">
             <span class="text-[#666666] text-[11px] font-sans tracking-wider">{{ __('event_detail.age') }}</span>
             <span class="text-white text-sm font-semibold font-sans">{!! $ageLabel !!}</span>
         </div>
         <div class="flex flex-col gap-0.5">
             <span class="text-[#666666] text-[11px] font-sans tracking-wider">{{ __('event_detail.format') }}</span>
             <span class="text-white text-sm font-semibold font-sans">{!! $formatLabel !!}</span>
         </div>
         @if($advanceCount)
         <div class="flex flex-col gap-0.5">
             <span class="text-[#666666] text-[11px] font-sans tracking-wider">{{ __('event_detail.advancement') }}</span>
             <span class="text-sm font-semibold font-sans" style="color: #22C55E">{{ __('event_detail.top_advance', ['count' => $advanceCount]) }}</span>
         </div>
         @endif
     </div>
 </div>
 @endforeach
 </div>
 </div>
 @endif

 {{-- Disciplíny & Porotcovia --}}
 @if($detail?->disciplines->isNotEmpty())
 <div class="flex flex-col gap-5 mt-8">
 <h3 class="text-white text-lg font-bold font-sans">{{ __('event_detail.disciplines_judges_title') }}</h3>
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
 @foreach($detail->disciplines->sortBy('sort_order') as $disc)
 @php $judge = $detail->judges->firstWhere('pivot.discipline_id', $disc->id); @endphp
 <div class="bg-[#0A0A0A] border border-[#222222] rounded-xl overflow-hidden flex flex-col">
     @if($judge?->getFirstMediaUrl('profile_image'))
     <div class="w-full h-[160px] overflow-hidden">
         <img src="{{ $judge->getFirstMediaUrl('profile_image') }}" alt="{{ $judge->name }}" class="w-full h-full object-cover">
     </div>
     @elseif($disc->getFirstMediaUrl('image'))
     <div class="w-full h-[160px] overflow-hidden">
         <img src="{{ $disc->getFirstMediaUrl('image') }}" alt="{{ $disc->getTranslation('name', $locale) }}" class="w-full h-full object-cover">
     </div>
     @endif
     <div class="p-4 flex flex-col gap-3">
         <div class="flex items-center gap-2">
             @if($disc->icon)
             <x-dynamic-component :component="$disc->icon" class="w-4 h-4 shrink-0" style="color: {{ $categoryColor }}" />
             @endif
             <span class="text-[11px] font-bold tracking-wider font-sans" style="color: {{ $categoryColor }}">{{ mb_strtoupper($disc->getTranslation('name', $locale)) }}</span>
         </div>
         @if($judge)
         <div class="flex items-center gap-2">
             <span class="text-white text-[15px] font-bold font-sans">{{ $judge->name }}</span>
             @if($judge->country_code)
             <span class="text-[#666666] text-[11px] font-semibold tracking-wider font-sans">{{ $judge->country_code }}</span>
             @endif
         </div>
         <span class="text-[#666666] text-xs font-sans">{{ __('event_detail.judge') }}</span>
         @endif
         @if($disc->getTranslation('description', $locale))
         <p class="text-[#888888] text-[13px] font-sans leading-relaxed">{{ $disc->getTranslation('description', $locale) }}</p>
         @endif
         @if($judge?->judge_profile_approved_at)
         <a href="{{ route('judge.show', $judge) }}" class="flex items-center gap-1.5" style="color: {{ $categoryColor }}">
             <span class="text-xs font-semibold font-sans">{{ __('event_detail.judge_profile_link') }}</span>
             <span class="text-xs">&rarr;</span>
         </a>
         @endif
     </div>
 </div>
 @endforeach
 </div>
 </div>
 @endif

 {{-- Systém bodovania --}}
 @if($detail?->rounds->isNotEmpty())
 <div class="flex flex-col gap-5 mt-8">
 <h3 class="text-white text-lg font-bold font-sans">{{ __('event_detail.scoring_system_title') }}</h3>
 <div class="flex flex-col gap-4">
 @foreach($detail->rounds->unique(fn ($r) => $r->name . $r->scoring_format?->value) as $round)
 <div class="flex gap-3">
     <span class="text-sm font-bold font-sans shrink-0" style="color: {{ $categoryColor }}">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
     <div class="flex flex-col gap-1">
         <span class="text-white text-sm font-semibold font-sans">{{ $round->name }}</span>
         <p class="text-[#888888] text-[13px] font-sans" style="line-height: 1.4">
             @if($round->scoring_format === \App\Enums\ScoringFormatEnum::POINTS)
                 {{ __('event_detail.format_points') }}
                 @if($round->parts->count() > 0) &middot; {{ $round->parts->count() }} {{ mb_strtolower(__('event_detail.disciplines')) }} @endif
                 @if($round->advance_count) &middot; {{ __('event_detail.top_advance', ['count' => $round->advance_count]) }} @endif
             @elseif($round->advancement_type === \App\Enums\RoundAdvancementTypeEnum::BATTLE_WINNER)
                 {{ __('event_detail.format_battle') }}
                 @if($round->battle_size) &middot; 1v1 @endif
             @endif
         </p>
     </div>
 </div>
 @endforeach
 </div>
 </div>
 @endif
 </div>

 {{-- Sidebar --}}
 <div class="w-full lg:w-[320px] shrink-0 flex flex-col gap-6 lg:sticky lg:top-24 lg:self-start">
 {{-- Info Card --}}
 <div class="bg-[#111111] border border-[#222222] p-6 flex flex-col gap-5">
 <h3 class="text-white text-lg font-bold font-sans">{{ __('event_detail.competition_info') }}</h3>
 <div class="w-full h-px bg-[#222222]"></div>

 {{-- Type --}}
 <div class="flex items-center justify-between gap-4">
 <span class="text-[#888888] text-sm font-sans">{{ __('event_detail.type') }}</span>
 <span class="text-sm font-sans text-right"style="color: {{ $categoryColor }}">{{ $categoryName }}</span>
 </div>

 {{-- Organizer --}}
 @if($event->team)
 <div class="flex items-center justify-between gap-4">
 <span class="text-[#888888] text-sm font-sans">{{ __('event_detail.organizer') }}</span>
 <span class="text-white text-sm font-sans text-right">{{ $event->team->name }}</span>
 </div>
 @endif

 {{-- Disciplines --}}
 @if($detail?->disciplines->isNotEmpty())
 <div class="flex items-center justify-between gap-4">
 <span class="text-[#888888] text-sm font-sans">{{ __('event_detail.disciplines') }}</span>
 <span class="text-white text-sm font-sans text-right">{{ $detail->disciplines->map(fn ($d) => $d->getTranslation('name', $locale))->join(', ') }}</span>
 </div>
 @endif

 {{-- Registration Fee (hide when finished) --}}
 @if($status !== 'finished' && $detail?->registrationFees->isNotEmpty())
 <div class="flex items-center justify-between gap-4">
 <span class="text-[#888888] text-sm font-sans">{{ __('event_detail.registration_fee') }}</span>
 <span class="text-sm font-bold font-sans text-right"style="color: {{ $categoryColor }}">
 @php $firstFee = $detail->registrationFees->first(); @endphp
 {{ __('event_detail.fee_from') }} {{ number_format($firstFee->amount, 2) }} {{ $firstFee->currency }}
 </span>
 </div>
 @endif
 </div>

 {{-- CTA Card --}}
 <div class="p-6 flex flex-col gap-4" style="background-color: {{ $categoryColor }}10; border: 1px solid {{ $categoryColor }}40">
 @if($status === 'finished')
 <h3 class="text-white text-base font-bold font-sans">{{ __('event_detail.cta_finished_title') }}</h3>
 <p class="text-[#AAAAAA] text-sm font-sans leading-relaxed">{{ __('event_detail.cta_finished_description') }}</p>
 <a href="{{ route('sutaze') }}" class="flex items-center justify-center gap-2 text-white text-sm font-semibold font-sans py-3 w-full" style="background-color: {{ $categoryColor }}">
 {{ __('event_detail.cta_finished_button') }}
 <span>&#8594;</span>
 </a>
 @else
 <h3 class="text-white text-base font-bold font-sans">{{ __('event_detail.cta_competition') }}</h3>
 <p class="text-[#AAAAAA] text-sm font-sans leading-relaxed">{{ __('event_detail.cta_register_description') }}</p>
 @if($status === 'registering' && $org?->external_link)
 <a href="{{ $org->external_link }}" target="_blank" rel="noopener" class="flex items-center justify-center gap-2 text-white text-sm font-semibold font-sans py-3 w-full" style="background-color: {{ $categoryColor }}">
 {{ __('event_detail.register') }}
 <span>&#8594;</span>
 </a>
 @else
 <button x-on:click="setTab('registracia'); document.getElementById('competition-tabs').scrollIntoView({ behavior: 'smooth' })" class="flex items-center justify-center gap-2 text-white text-sm font-semibold font-sans py-3 w-full" style="background-color: {{ $categoryColor }}">
 {{ __('event_detail.register') }}
 <span>&#8594;</span>
 </button>
 @endif
 @endif
 </div>

 @include('pages.events._share')
 </div>
 </div>

 {{-- Gallery --}}
 @if($galleryImages->isNotEmpty())
 <div class="mt-16">
 <div class="flex flex-col gap-8">
 <div class="flex items-end justify-between">
 <div class="flex flex-col gap-3">
 <div class="flex items-center gap-2">
 <div class="w-6 h-0.5"style="background-color: {{ $categoryColor }}"></div>
 <span class="text-xs font-bold tracking-[2px]"style="color: {{ $categoryColor }}">{{ __('event_detail.gallery') }}</span>
 </div>
 <h2 class="font-display font-bold text-[36px] tracking-wide text-white">{{ __('event_detail.photos_from_event') }}</h2>
 </div>
 </div>
 <div
     x-data="{
         lightbox: false,
         current: 0,
         images: @js($galleryImages->map(fn ($img) => ['url' => $img->getUrl(), 'name' => $img->name])->values()->all()),
         open(i) { this.current = i; this.lightbox = true; },
         close() { this.lightbox = false; },
         next() { this.current = (this.current + 1) % this.images.length; },
         prev() { this.current = (this.current - 1 + this.images.length) % this.images.length; },
     }"
     x-on:keydown.escape.window="close()"
     x-on:keydown.arrow-right.window="lightbox && next()"
     x-on:keydown.arrow-left.window="lightbox && prev()"
 >
 <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
 @foreach($galleryImages as $image)
 <div class="overflow-hidden bg-[#1A1A1A] aspect-[4/3] cursor-pointer" x-on:click="open({{ $loop->index }})">
 <img src="{{ $image->getUrl() }}" alt="{{ $image->name }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
 </div>
 @endforeach
 </div>

 {{-- Lightbox --}}
 <div x-show="lightbox" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/90" x-on:click.self="close()">
     {{-- Close --}}
     <button x-on:click="close()" class="absolute top-6 right-6 text-white/60 hover:text-white transition-colors z-10">
         <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
     </button>
     {{-- Prev --}}
     <button x-on:click="prev()" class="absolute left-4 md:left-8 text-white/60 hover:text-white transition-colors z-10">
         <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
     </button>
     {{-- Next --}}
     <button x-on:click="next()" class="absolute right-4 md:right-8 text-white/60 hover:text-white transition-colors z-10">
         <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
     </button>
     {{-- Image --}}
     <img :src="images[current]?.url" :alt="images[current]?.name" class="max-h-[85vh] max-w-[90vw] object-contain">
     {{-- Counter --}}
     <div class="absolute bottom-6 left-1/2 -translate-x-1/2 text-white/60 text-sm font-sans">
         <span x-text="current + 1"></span> / <span x-text="images.length"></span>
     </div>
 </div>
 </div>
 </div>
 </div>
 @endif

 {{-- More Competitions --}}
 @if($moreEvents->isNotEmpty())
 <div class="mt-16">
 <div class="flex flex-col gap-8">
 <div class="flex items-end justify-between">
 <div class="flex flex-col gap-3">
 <div class="flex items-center gap-2">
 <div class="w-6 h-0.5"style="background-color: {{ $categoryColor }}"></div>
 <span class="text-xs font-bold tracking-[2px]"style="color: {{ $categoryColor }}">{{ __('event_detail.more_competitions_label') }}</span>
 </div>
 <h2 class="font-display font-bold text-[36px] tracking-wide text-white">{{ __('event_detail.more_competitions_title') }}</h2>
 </div>
 <a href="{{ route('sutaze') }}"class="flex items-center gap-2 border px-6 py-3 text-sm font-semibold transition-colors hover:border-current"style="color: {{ $categoryColor }}; border-color: {{ $categoryColor }}">
 {{ __('event_detail.all_competitions') }}
 <span>&#8594;</span>
 </a>
 </div>
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
 @foreach($moreEvents as $related)
 @php $relColor = $related->eventCategory->color ?? $categoryColor; @endphp
 <a href="{{ route('event.show', $related) }}"class="bg-[#111111] overflow-hidden group hover:border-[#333333] transition-colors flex flex-col">
 <div class="relative w-full h-[180px] bg-[#1A1A1A] overflow-hidden">
 @if($related->getFirstMediaUrl('card_image'))
 <img src="{{ $related->getFirstMediaUrl('card_image') }}"alt="{{ $related->getTranslation('title', $locale) }}"class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
 @endif
 </div>
 <div class="flex flex-col gap-3 p-5">
 <div class="flex items-center justify-between">
 @if($related->eventCategory)
 <span class="text-[11px] font-bold tracking-wider px-2.5 py-1"style="color: {{ $relColor }}; background-color: {{ $relColor }}20">
 {{ mb_strtoupper($related->eventCategory->getTranslation('title', $locale)) }}
 </span>
 @endif
 @if($related->date)
 <span class="text-[#666666] text-xs font-sans">{{ $related->date->format('Y') }}</span>
 @endif
 </div>
 <h3 class="text-white text-lg font-bold font-sans">{{ $related->getTranslation('title', $locale) }}</h3>
 @if($related->city)
 <div class="flex items-center gap-1.5 text-[#666666] text-xs font-sans">
 <svg class="w-3.5 h-3.5 shrink-0"fill="none"stroke="currentColor"viewBox="0 0 24 24"stroke-width="2"stroke-linecap="round"stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12"cy="10"r="3"/></svg>
 {{ collect([$related->city, $related->country])->filter()->join(', ') }}
 </div>
 @endif
 </div>
 </a>
 @endforeach
 </div>
 </div>
 </div>
 @endif
 </div>

 {{-- Tab: Harmonogram --}}
 <div x-show="tab === 'harmonogram'" x-cloak>
 @if($detail?->timetableEntries->isNotEmpty())
 <div class="flex flex-col gap-8">
     {{-- Header --}}
     <div class="flex items-center justify-between">
         <h2 class="font-display font-bold text-[28px] tracking-wide text-white">{{ __('event_detail.timetable_title') }}</h2>
         @if($event->date)
         <span class="text-[#888888] text-sm font-sans">{{ $event->date->translatedFormat('l, j. F Y') }}</span>
         @endif
     </div>

     {{-- Timeline list --}}
     <div class="flex flex-col">
     @foreach($detail->timetableEntries as $entry)
     @php
         $isLive = $entry->status === \App\Enums\TimetableEntryStatusEnum::IN_PROGRESS;
         $isDone = $entry->status === \App\Enums\TimetableEntryStatusEnum::FINISHED;
         $isPending = $entry->status === \App\Enums\TimetableEntryStatusEnum::PENDING;
         $entryDesc = $entry->getTranslation('description', $locale);
         $entryDelay = $entry->getDelayMinutes();
         $isRound = $entry->type === \App\Enums\TimetableEntryTypeEnum::COMPETITION_ROUND;
         $roundCategory = $entry->competitionRound?->athleteCategory;
     @endphp
     <div class="flex items-start gap-0 py-4 {{ !$loop->last ? 'border-b' : '' }} {{ $isLive ? 'border-[#FF2D2D30] bg-[#FF2D2D08]' : 'border-[#1A1A1A]' }}">
         {{-- Time --}}
         <span class="text-sm font-sans min-w-[60px] pt-0.5 {{ $isLive ? 'text-[#FF2D2D] font-bold' : ($isDone ? 'text-[#22C55E]' : 'text-[#555555]') }}">{{ $event->toLocalTime($entry->scheduled_time)?->format('H:i') }}</span>

         {{-- Dot --}}
         <div class="flex items-start justify-center w-6 pt-1.5">
             @if($isLive)
             <span class="w-3.5 h-3.5 rounded-full bg-[#FF2D2D] ring-[3px] ring-[#FF2D2D40]"></span>
             @elseif($isDone)
             <span class="w-3 h-3 rounded-full bg-[#22C55E]"></span>
             @else
             <span class="w-3 h-3 rounded-full bg-[#333333] ring-1 ring-[#444444]"></span>
             @endif
         </div>

         {{-- Content --}}
         <div class="flex-1 flex flex-col gap-0.5 pl-3">
             <div class="flex items-center gap-3">
                 <span class="text-[15px] font-sans {{ $isLive ? 'text-white font-semibold' : ($isDone ? 'text-[#AAAAAA] font-medium' : 'text-[#666666] font-medium') }}">{{ $entry->getTranslation('title', $locale) }}</span>
                 @if($isLive && $entryDelay > 0)
                 <span class="bg-[#FF2D2D20] text-[#FF2D2D] text-[11px] font-semibold font-sans rounded-full px-2.5 py-0.5">{{ __('event_detail.live_delay', ['minutes' => $entryDelay]) }}</span>
                 @endif
             </div>
             @if($isLive)
             @php $livePerformer = $entry->getCurrentPerformerLabel(); @endphp
             <span class="text-[13px] font-medium font-sans text-[#FF2D2D]">{{ $livePerformer ?? ($roundCategory ? $roundCategory->getTranslation('name', $locale) . ' · ' : '') . __('event_detail.timetable_in_progress') }}</span>
             @elseif($entryDesc)
             <span class="text-[13px] font-sans {{ $isDone ? 'text-[#555555]' : 'text-[#444444]' }}">{{ $entryDesc }}</span>
             @endif
         </div>
     </div>
     @endforeach
     </div>
 </div>
 @else
 <div class="flex flex-col items-center justify-center py-20 text-center">
 <svg class="w-12 h-12 text-[#333333] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
 <h3 class="text-[#888888] text-lg font-bold font-sans mb-2">{{ __('event_detail.timetable_not_published') }}</h3>
 <p class="text-[#666666] text-sm font-sans">{{ __('event_detail.timetable_not_published_desc') }}</p>
 </div>
 @endif
 </div>

 {{-- Tab: Vysledky --}}
 <div x-show="tab === 'vysledky'" x-cloak x-data="{ resultsTab: 'qualification' }">
 @include('pages.events._results-tab')
 </div>

 {{-- Tab: Registracia --}}
 <div x-show="tab === 'registracia'"x-cloak>

 {{-- Countdown: registration not yet open --}}
 @if(in_array($status, ['countdown', 'upcoming']) && $org?->registration_opens_at && $org->registration_opens_at->isFuture())
 <x-registration-countdown :target-date="$org->registration_opens_at" :accent-color="$categoryColor" />
 @endif

 <div class="flex flex-col lg:flex-row gap-16">
 <div class="flex-1 min-w-0 flex flex-col gap-8">

 {{-- Closing countdown when registration is open --}}
 @if($status === 'registering' && $org?->registration_closes_at && $org->registration_closes_at->isFuture())
 <div
     x-data="countdown('{{ $org->registration_closes_at->toIso8601String() }}')"
     x-init="start()"
     class="flex items-center gap-3 bg-[#1A1A1A] border border-[#222222] rounded-lg px-5 py-3"
 >
     <span class="text-[#888888] text-xs font-bold tracking-wider shrink-0">{{ __('event_detail.countdown_to_close') }}</span>
     <span class="font-mono font-bold text-lg tabular-nums" style="color: {{ $categoryColor }}">
         <span x-text="days > 0 ? days + 'd ' : ''"></span><span x-text="hours.toString().padStart(2,'0')">00</span>h
         <span x-text="minutes.toString().padStart(2,'0')">00</span>m
         <span x-text="seconds.toString().padStart(2,'0')">00</span>s
     </span>
 </div>
 @endif

 {{-- Registration Status --}}
 <div class="bg-[#111111] border border-[#222222] p-6 flex flex-col gap-5">
 <div class="flex items-center justify-between">
 <h3 class="text-white text-lg font-bold font-sans">{{ __('event_detail.registration_status') }}</h3>
 <span class="rounded-full px-3 py-1 text-xs font-bold"style="color: {{ $statusColor }}; background-color: {{ $statusColor }}15; border: 1px solid {{ $statusColor }}30">
 @if($status === 'registering') {{ __('event_detail.open') }}
 @elseif($status === 'finished') {{ __('event_detail.finished') }}
 @elseif(in_array($status, ['upcoming', 'countdown'])) {{ __('event_detail.soon') }}
 @else {{ __('event_detail.closed') }}
 @endif
 </span>
 </div>

 {{-- Capacity Bar --}}
 @if($org?->max_capacity)
 @php
 $registered = $event->registrations->count();
 $capacity = $org->max_capacity;
 $pct = min(100, round(($registered / $capacity) * 100));
 @endphp
 <div class="flex flex-col gap-2">
 <div class="flex items-center justify-between">
 <span class="text-[#888888] text-sm font-sans">{{ __('event_detail.capacity') }}</span>
 <span class="text-white text-sm font-bold font-sans">{{ max(0, $capacity - $registered) }}</span>
 </div>
 <div class="w-full h-2 rounded-full bg-[#222222] overflow-hidden">
 <div class="h-full rounded-full transition-all"style="width: {{ $pct }}%; background-color: {{ $categoryColor }}"></div>
 </div>
 </div>
 @endif

 {{-- Registration Dates --}}
 @if($org?->registration_opens_at || $org?->registration_closes_at)
 <div class="w-full h-px bg-[#222222]"></div>
 @if($org->registration_opens_at)
 <div class="flex items-center justify-between">
 <span class="text-[#888888] text-sm font-sans">{{ __('event_detail.registration_opening') }}</span>
 <span class="text-white text-sm font-sans">{{ $org->registration_opens_at->translatedFormat('d. F Y, H:i') }}</span>
 </div>
 @endif
 @if($org->registration_closes_at)
 <div class="flex items-center justify-between">
 <span class="text-[#888888] text-sm font-sans">{{ __('event_detail.registration_closing') }}</span>
 <span class="text-white text-sm font-sans">{{ $org->registration_closes_at->translatedFormat('d. F Y, H:i') }}</span>
 </div>
 @endif
 @endif
 </div>

 {{-- Registration Fees Table --}}
 @if($detail?->registrationFees->isNotEmpty())
 <div class="bg-[#111111] border border-[#222222] p-6 flex flex-col gap-4">
 <h3 class="text-white text-lg font-bold font-sans">{{ __('event_detail.registration_fees') }}</h3>
 <div class="flex flex-col gap-2">
 @foreach($detail->registrationFees as $fee)
 <div class="flex items-center justify-between py-2 {{ !$loop->last ? 'border-b border-[#1A1A1A]' : '' }}">
 <span class="text-white text-sm font-sans">
 {{ $fee->athleteCategory ? $fee->athleteCategory->getTranslation('name', $locale) : ($fee->description ?? __('event_detail.standard_fee')) }}
 </span>
 <span class="font-bold text-sm font-sans"style="color: {{ $categoryColor }}">{{ number_format($fee->amount, 2) }} {{ $fee->currency }}</span>
 </div>
 @endforeach
 </div>
 </div>
 @endif

 {{-- Dynamic Registration Form --}}
 @if($status === 'registering' && $org?->registration_form_schema && count($org->registration_form_schema) > 0)
 <div class="bg-[#111111] border border-[#222222] p-6 flex flex-col gap-5">
 <h3 class="text-white text-lg font-bold font-sans">{{ __('event_detail.registration_form') }}</h3>
 <p class="text-[#888888] text-sm font-sans">{{ __('event_detail.registration_form_desc') }}</p>

 <form class="flex flex-col gap-4">
 @foreach($org->registration_form_schema as $field)
 <div class="flex flex-col gap-1.5">
 <label class="text-[#CCCCCC] text-sm font-sans">
 {{ $field['label'] ?? $field['key'] }}
 @if($field['required'] ?? false)
 <span style="color: {{ $categoryColor }}">*</span>
 @endif
 </label>

 @switch($field['type'] ?? 'text_input')
 @case('textarea')
 <textarea name="{{ $field['key'] }}"rows="3"{{ ($field['required'] ?? false) ? 'required' : '' }}
 class="w-full bg-[#0A0A0A] border border-[#333333] px-4 py-3 text-white text-sm font-sans placeholder-[#555555] focus:border-[{{ $categoryColor }}] focus:outline-none transition-colors"
 placeholder="{{ $field['label'] ?? '' }}"></textarea>
 @break
 @case('select')
 <select name="{{ $field['key'] }}"{{ ($field['required'] ?? false) ? 'required' : '' }}
 class="w-full bg-[#0A0A0A] border border-[#333333] px-4 py-3 text-white text-sm font-sans focus:border-[{{ $categoryColor }}] focus:outline-none transition-colors">
 <option value="">{{ __('event_detail.select_placeholder') }}</option>
 @foreach(explode("\n", $field['options'] ?? '') as $option)
 @if(trim($option))
 <option value="{{ trim($option) }}">{{ trim($option) }}</option>
 @endif
 @endforeach
 </select>
 @break
 @case('date_picker')
 @case('birth_date')
 <input type="date"name="{{ $field['key'] }}"{{ ($field['required'] ?? false) ? 'required' : '' }}
 class="w-full bg-[#0A0A0A] border border-[#333333] px-4 py-3 text-white text-sm font-sans focus:border-[{{ $categoryColor }}] focus:outline-none transition-colors">
 @break
 @case('number_input')
 <input type="number"name="{{ $field['key'] }}"{{ ($field['required'] ?? false) ? 'required' : '' }}
 class="w-full bg-[#0A0A0A] border border-[#333333] px-4 py-3 text-white text-sm font-sans placeholder-[#555555] focus:border-[{{ $categoryColor }}] focus:outline-none transition-colors"
 placeholder="{{ $field['label'] ?? '' }}">
 @break
 @case('email')
 <input type="email"name="{{ $field['key'] }}"{{ ($field['required'] ?? false) ? 'required' : '' }}
 class="w-full bg-[#0A0A0A] border border-[#333333] px-4 py-3 text-white text-sm font-sans placeholder-[#555555] focus:border-[{{ $categoryColor }}] focus:outline-none transition-colors"
 placeholder="{{ $field['label'] ?? '' }}">
 @break
 @case('phone')
 <input type="tel"name="{{ $field['key'] }}"{{ ($field['required'] ?? false) ? 'required' : '' }}
 class="w-full bg-[#0A0A0A] border border-[#333333] px-4 py-3 text-white text-sm font-sans placeholder-[#555555] focus:border-[{{ $categoryColor }}] focus:outline-none transition-colors"
 placeholder="{{ $field['label'] ?? '' }}">
 @break
 @case('gender')
 <select name="{{ $field['key'] }}"{{ ($field['required'] ?? false) ? 'required' : '' }}
 class="w-full bg-[#0A0A0A] border border-[#333333] px-4 py-3 text-white text-sm font-sans focus:border-[{{ $categoryColor }}] focus:outline-none transition-colors">
 <option value="">{{ __('event_detail.select_placeholder') }}</option>
 <option value="male">{{ __('event_detail.gender_male') }}</option>
 <option value="female">{{ __('event_detail.gender_female') }}</option>
 </select>
 @break
 @default
 <input type="text"name="{{ $field['key'] }}"{{ ($field['required'] ?? false) ? 'required' : '' }}
 class="w-full bg-[#0A0A0A] border border-[#333333] px-4 py-3 text-white text-sm font-sans placeholder-[#555555] focus:border-[{{ $categoryColor }}] focus:outline-none transition-colors"
 placeholder="{{ $field['label'] ?? '' }}">
 @endswitch
 </div>
 @endforeach

 <button type="submit"class="flex items-center justify-center gap-2 text-white text-base font-bold font-sans py-4 w-full mt-2 transition-opacity hover:opacity-90"style="background-color: {{ $categoryColor }}">
 {{ __('event_detail.submit_registration') }}
 <span>&#8594;</span>
 </button>
 </form>
 </div>
 @endif

 {{-- Register Button (external link) --}}
 @if($status === 'registering' && $org?->external_link)
 <a href="{{ $org->external_link }}"target="_blank"rel="noopener"class="flex items-center justify-center gap-2 text-white text-base font-bold font-sans py-4 w-full max-w-[400px] transition-opacity hover:opacity-90"style="background-color: {{ $categoryColor }}">
 {{ __('event_detail.register') }}
 <span>&#8594;</span>
 </a>
 @endif
 </div>

 {{-- Sidebar --}}
 <div class="w-full lg:w-[320px] shrink-0 flex flex-col gap-6 lg:sticky lg:top-24 lg:self-start">
 <div class="bg-[#111111] border border-[#222222] p-6 flex flex-col gap-5">
 <h3 class="text-white text-lg font-bold font-sans">{{ __('event_detail.information') }}</h3>
 <div class="w-full h-px bg-[#222222]"></div>

 @if($event->date)
 <div class="flex items-center justify-between gap-4">
 <span class="text-[#888888] text-sm font-sans">{{ __('event_detail.date') }}</span>
 <span class="text-white text-sm font-sans text-right">
 {{ $event->date->translatedFormat('d. F Y') }}
 @if($event->date_end && !$event->date->isSameDay($event->date_end))
 &mdash; {{ $event->date_end->translatedFormat('d. F Y') }}
 @endif
 </span>
 </div>
 @endif

 @if($location)
 <div class="flex items-center justify-between gap-4">
 <span class="text-[#888888] text-sm font-sans">{{ __('event_detail.place') }}</span>
 <span class="text-white text-sm font-sans text-right">{{ $location }}</span>
 </div>
 @endif

 @if($detail?->disciplines->isNotEmpty())
 <div class="flex items-center justify-between gap-4">
 <span class="text-[#888888] text-sm font-sans">{{ __('event_detail.disciplines') }}</span>
 <span class="text-white text-sm font-sans text-right">{{ $detail->disciplines->map(fn ($d) => $d->getTranslation('name', $locale))->join(', ') }}</span>
 </div>
 @endif

 @if($detail?->athleteCategories->isNotEmpty())
 <div class="flex items-center justify-between gap-4">
 <span class="text-[#888888] text-sm font-sans">{{ __('event_detail.categories') }}</span>
 <span class="text-white text-sm font-sans text-right">{{ $detail->athleteCategories->map(fn ($c) => $c->getTranslation('name', $locale))->join(', ') }}</span>
 </div>
 @endif
 </div>
 </div>
 </div>
 </div>
 </div>
 </section>
@endsection
