@php
    $allRounds = $detail?->rounds ?? collect();
    $publishedRounds = $allRounds->filter(fn($r) => $r->scores_published);
    $hasBattle = $allRounds->contains(fn($r) => $r->isBattle());
@endphp

@if($allRounds->isNotEmpty())

{{-- TOP 3 Podium (only when competition is finished AND every round in category is published) --}}
@if($status === 'finished')
@php
    $podiumCategories = $allRounds
        ->groupBy('athlete_category_id')
        ->filter(fn ($catRounds) => $catRounds->isNotEmpty() && $catRounds->every(fn ($r) => $r->scores_published))
        ->map(function ($catRounds) {
            $finalRound = $catRounds->sortByDesc('sort_order')->first();
            if (!$finalRound) return null;
            if ($finalRound->isBattle()) {
                $qualRound = $catRounds->first(fn($r) => $r->isQualification());
                if (!$qualRound) return null;
                $round = $qualRound;
            } else {
                $round = $finalRound;
            }
            $results = [];
            foreach ($round->parts as $part) {
                foreach ($part->results as $result) {
                    if (!isset($results[$result->user_id])) {
                        $results[$result->user_id] = ['user' => $result->user, 'total' => 0, 'place' => $result->place];
                    }
                    $results[$result->user_id]['total'] += (float) $result->score;
                }
            }
            return ['category' => $round->athleteCategory, 'podium' => collect($results)
                ->sortByDesc('total')
                ->sortBy(fn ($r) => $r['place'] ?? PHP_INT_MAX)
                ->values()
                ->take(3)];
        })->filter()->values();
    $podiumGenders = $podiumCategories->groupBy(fn($c) => $c['category']?->gender?->value ?? 'other');
@endphp
@if($podiumCategories->isNotEmpty())
<div class="flex flex-col gap-12 mb-16">
    <div class="flex items-center gap-4">
        <svg class="w-8 h-8 text-[#FFD700]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>
        <h2 class="text-white text-[32px] font-bold" style="font-family: 'Thunder', sans-serif; letter-spacing: 0.5px;">TOP 3 VÝSLEDKY</h2>
    </div>
    @foreach($podiumGenders as $gender => $genderCategories)
    @if($podiumGenders->count() > 1)
    <div class="flex items-center gap-3">
        <div class="flex-1 h-px bg-[#333333]"></div>
        <span class="text-[#555555] text-[11px] font-bold font-sans tracking-[3px]">{{ mb_strtoupper($gender === 'male' ? __('event_detail.gender_m') : ($gender === 'female' ? __('event_detail.gender_f') : 'OSTATNÉ')) }}</span>
        <div class="flex-1 h-px bg-[#333333]"></div>
    </div>
    @endif
    @foreach($genderCategories as $catData)
    <div class="flex flex-col gap-6">
        <div class="flex items-center gap-3">
            <div class="w-[3px] h-5 rounded-sm bg-[#FF2D2D]"></div>
            <span class="text-white text-xl font-bold font-sans">{{ $catData['category']?->getTranslation('name', $locale) ?? '' }}</span>
        </div>
        <div class="flex items-end gap-5">
            @foreach($catData['podium'] as $i => $competitor)
            @php
                $place = $i + 1;
                $medalColor = match($place) { 1 => '#FFD700', 2 => '#C0C0C0', 3 => '#CD7F32', default => '#888888' };
                $borderColor = match($place) { 1 => '#FFD70040', 2 => '#C0C0C030', 3 => '#CD7F3230', default => '#22222200' };
                $cardPadding = match($place) { 1 => 'py-10 px-6', 2 => 'py-8 px-6', 3 => 'py-6 px-5', default => 'py-6 px-5' };
                $iconSize = match($place) { 1 => 'w-11 h-11', 2 => 'w-9 h-9', 3 => 'w-7 h-7', default => 'w-7 h-7' };
                $nameSize = match($place) { 1 => 'text-xl font-bold', 2 => 'text-base font-semibold', 3 => 'text-sm font-semibold', default => 'text-sm font-semibold' };
                $placeLabel = match($place) { 1 => __('event_detail.podium_1st'), 2 => __('event_detail.podium_2nd'), 3 => __('event_detail.podium_3rd'), default => '' };
            @endphp
            <div class="flex-1 flex flex-col items-center gap-4 rounded-2xl bg-[#111111] {{ $cardPadding }}" style="border: {{ $place === 1 ? '2px' : '1px' }} solid {{ $borderColor }};">
                <svg class="{{ $iconSize }}" style="color: {{ $medalColor }};" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>
                <span class="text-white {{ $nameSize }} font-sans text-center">{{ $competitor['user']?->name ?? '—' }}</span>
                <span class="text-sm font-bold font-sans" style="color: {{ $medalColor }};">{{ $placeLabel }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
    @endforeach
</div>
@endif
@endif

@php
    /**
     * Split a category's rounds into sequential stages:
     *  - consecutive battle-type rounds merge into ONE bracket stage,
     *  - every score round is its own stage.
     * Each stage's tab identity is its (first) round's own name.
     */
    $buildStages = function ($catRounds) {
        $stages = collect();
        foreach ($catRounds->sortBy('sort_order')->values() as $round) {
            $isBattle = $round->isBattle();
            $last = $stages->last();
            if ($isBattle && $last && $last['battle']) {
                $last['rounds']->push($round);
            } else {
                $stages->push(['battle' => $isBattle, 'key' => (string) $round->name, 'rounds' => collect([$round])]);
            }
        }
        return $stages;
    };

    $categoryStages = $allRounds->groupBy('athlete_category_id')->map($buildStages);

    // Global, ordered, de-duplicated tab list. Same-named stages across categories share one tab.
    $tabsRaw = collect();
    foreach ($categoryStages as $stages) {
        foreach ($stages as $stage) {
            $tabsRaw->push(['key' => $stage['key'], 'minSort' => $stage['rounds']->min('sort_order')]);
        }
    }
    $tabs = $tabsRaw->groupBy('key')
        ->map(fn ($g, $key) => ['key' => (string) $key, 'label' => (string) $key, 'minSort' => $g->min('minSort')])
        ->sortBy('minSort')
        ->values();
    $firstTabKey = $tabs->first()['key'] ?? null;
@endphp

{{-- Results Sub Nav — one tab per stage, in competition order --}}
<div class="flex flex-col gap-5 mb-10" x-init="resultsTab = @js($firstTabKey)">
    <h2 class="text-white text-[32px] font-bold" style="font-family: 'Thunder', sans-serif; letter-spacing: 0.5px;">{{ __('event_detail.results_title') }}</h2>
    @if($tabs->count() > 1)
    <div class="flex items-center gap-2 flex-wrap">
        @foreach($tabs as $tab)
        <button @click="resultsTab = @js($tab['key'])"
            :class="resultsTab === @js($tab['key']) ? 'bg-[#FF2D2D] text-white' : 'bg-[#1A1A1A] text-[#666666] hover:text-white'"
            class="px-5 py-2 rounded-full text-sm font-semibold font-sans transition-colors">
            {{ $tab['label'] }}
        </button>
        @endforeach
    </div>
    @endif
</div>

{{-- Sequential results per category --}}
<div class="flex flex-col gap-12">
@foreach($allRounds->groupBy('athlete_category_id') as $catId => $catRounds)
@php
    $category = $catRounds->first()->athleteCategory;
    $categoryLabel = $category?->getTranslation('name', $locale) ?? __('event_detail.results_general');
    $stages = $categoryStages->get($catId);
    $categoryTabKeys = $stages->pluck('key')->values()->all();
@endphp

<template x-if="@js($categoryTabKeys).includes(resultsTab)">
<div class="flex flex-col gap-8">
    <div class="flex items-center gap-3">
        <div class="flex-1 h-px bg-[#333333]"></div>
        <span class="text-[#555555] text-[11px] font-bold font-sans tracking-[3px]">{{ mb_strtoupper($categoryLabel) }}</span>
        <div class="flex-1 h-px bg-[#333333]"></div>
    </div>

    @foreach($stages as $stageIdx => $stage)
    <template x-if="resultsTab === @js($stage['key'])">
    @if(! $stage['battle'])
    {{-- SCORE stage — one score table per round --}}
    <div class="flex flex-col gap-8">
    @foreach($stage['rounds'] as $roundLoopIdx => $round)
    @php
        $isPublished = $round->scores_published;
        $advanceCount = $round->nextRound?->competitor_count;
        $hasFollowingBattle = $catRounds->contains(fn($r) => $r->isBattle() && $r->sort_order > $round->sort_order);
        $showStatusColumn = $round->nextRound !== null;

        if ($isPublished) {
            $compData = [];
            foreach ($round->parts as $part) {
                foreach ($part->results as $result) {
                    if (!isset($compData[$result->user_id])) {
                        $compData[$result->user_id] = ['user' => $result->user, 'parts' => [], 'total' => 0, 'place' => $result->place];
                    }
                    $compData[$result->user_id]['parts'][$part->id] = $result->score;
                    $compData[$result->user_id]['total'] += (float) $result->score;
                }
            }
            $competitors = collect($compData)
                ->sortByDesc('total')
                ->sortBy(fn ($c) => $c['place'] ?? PHP_INT_MAX)
                ->values();
        } else {
            $competitors = $round->getOrderedCompetitors()->map(fn($reg, $i) => ['user' => $reg->user, 'parts' => [], 'total' => 0, 'place' => $i + 1])->values();
        }

        // Only the competitors who advanced from the previous round belong here: a battle round
        // feeds its winners, a score round feeds the top competitors by score. A first round is
        // an open field (null → no restriction).
        $advancedIds = $round->advancingCompetitorIds($catRounds);
        if ($advancedIds !== null) {
            $competitors = $competitors
                ->filter(fn ($c) => $advancedIds->contains($c['user']?->id))
                ->values()
                ->map(function ($c, $i) use ($isPublished) {
                    if (! $isPublished) {
                        $c['place'] = $i + 1;
                    }

                    return $c;
                })
                ->values();
        }
    @endphp

    @if($competitors->isNotEmpty())
    <div class="rounded-xl border border-[#222222] overflow-hidden">
        <div class="bg-[#111111] px-5 py-3.5 flex items-center" style="border-left: 3px solid #FF2D2D;">
            <span class="text-white text-base font-bold font-sans">{{ $round->name }}</span>
        </div>
        <div class="bg-[#0D0D0D] flex items-center px-5 py-2.5 border-b border-[#1A1A1A]">
            <span class="text-[#555555] text-[11px] font-normal font-sans tracking-wider w-[30px]">#</span>
            <span class="text-[#555555] text-[11px] font-normal font-sans tracking-wider flex-1">{{ mb_strtoupper(__('event_detail.name')) }}</span>
            @if($isPublished)
            @foreach($round->parts as $part)
            <span class="text-[#555555] text-[11px] font-normal font-sans tracking-wider w-[70px] text-right">{{ mb_strtoupper($part->getTranslation('name', $locale)) }}</span>
            @endforeach
            <span class="text-[#555555] text-[11px] font-normal font-sans tracking-wider w-[70px] text-right">{{ mb_strtoupper(__('event_detail.score')) }}</span>
            @if($showStatusColumn)
            <span class="text-[#555555] text-[11px] font-normal font-sans tracking-wider w-[100px] text-right">{{ mb_strtoupper(__('event_detail.status_label')) }}</span>
            @endif
            @endif
        </div>
        @foreach($competitors as $i => $comp)
        @php
            $place = $comp['place'] ?? ($i + 1);
            $isAdvancing = $isPublished && $advanceCount && $place <= $advanceCount && $hasFollowingBattle;
            $isBelowCutoff = $isPublished && $advanceCount && $place > $advanceCount;
            $placeColor = match(true) {
                !$isPublished => '#888888',
                $place === 1 => '#FFD700', $place === 2 => '#C0C0C0', $place === 3 => '#CD7F32',
                $isAdvancing => '#CCCCCC', default => '#666666',
            };
        @endphp
        @if($isPublished && $advanceCount && $place == $advanceCount + 1 && $hasFollowingBattle)
        <div class="flex flex-col items-center gap-1 py-2 px-5">
            <span class="text-[#FF2D2D] text-xs font-semibold font-sans">&middot; &middot; &middot; Top {{ $advanceCount }} {{ __('event_detail.results_advance_battle', ['round' => $round->nextRound?->name ?? __('event_detail.results_bracket')]) }} &middot; &middot; &middot;</span>
            <div class="w-full h-[2px]" style="background: linear-gradient(90deg, transparent, #FF2D2D, transparent);"></div>
        </div>
        @endif
        <div class="flex items-center px-5 py-3 border-b border-[#1A1A1A] {{ $isAdvancing ? 'bg-[#FF2D2D10]' : '' }} {{ $isBelowCutoff ? 'opacity-50' : '' }}">
            <span class="text-sm font-bold font-sans w-[30px]" style="color: {{ $placeColor }};">{{ $place }}</span>
            <span class="text-sm font-sans flex-1 {{ $isAdvancing ? 'text-white font-semibold' : ($isBelowCutoff ? 'text-[#888888]' : 'text-white font-medium') }}">{{ $comp['user']?->name ?? '—' }}</span>
            @if($isPublished)
            @foreach($round->parts as $part)
            <span class="text-sm font-sans w-[70px] text-right {{ $isBelowCutoff ? 'text-[#666666]' : 'text-[#CCCCCC]' }}">{{ isset($comp['parts'][$part->id]) ? number_format((float) $comp['parts'][$part->id], 1) : '—' }}</span>
            @endforeach
            <span class="text-sm font-bold font-sans w-[70px] text-right {{ $isBelowCutoff ? 'text-[#888888]' : 'text-white' }}">{{ number_format($comp['total'], 1) }}</span>
            @if($showStatusColumn)
            <div class="w-[100px] text-right">
                @if($isAdvancing)
                <span class="inline-flex items-center gap-1.5 bg-[#22C55E20] text-[#22C55E] text-xs font-semibold font-sans px-3 py-1 rounded-full">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                    Battle
                </span>
                @elseif($isBelowCutoff)
                <span class="text-[#666666] text-xs font-sans">{{ __('event_detail.results_eliminated') }}</span>
                @endif
            </div>
            @endif
            @endif
        </div>
        @endforeach
    </div>
    @endif
    @endforeach
    </div>

    @else
    {{-- BATTLE stage — bracket. The "finále / o 1./3. miesto" labels + medals apply
         only when this bracket is the category's last stage (i.e. the competition
         genuinely ends in a battle, not when a score finále follows it). --}}
    <div>
        <h3 class="text-white text-2xl font-bold mb-6" style="font-family: 'Thunder', sans-serif; letter-spacing: 0.5px;">
            {{ $stage['rounds']->first()->name }}
        </h3>
        @php
            $battleRoundsOrdered = $stage['rounds']->sortBy('sort_order')->values();
            $finaleRound = $battleRoundsOrdered->last();
            $bracketIsCategoryFinale = $finaleRound
                && ! $catRounds->contains(fn ($r) => $r->sort_order > $finaleRound->sort_order);
            $placements = [];
            if ($bracketIsCategoryFinale && $finaleRound && $finaleRound->scores_published && $finaleRound->battles->isNotEmpty()) {
                foreach ($finaleRound->battles as $battle) {
                    if ($battle->winner_side === null) continue;
                    $winnerSide = $battle->winner_side === 'a' ? 'sideA' : 'sideB';
                    $loserSide = $battle->winner_side === 'a' ? 'sideB' : 'sideA';
                    $base = $battle->bracket_position === 1 ? 1 : 3;
                    foreach ($battle->{$winnerSide} as $c) { $placements[$c->user_id] = $base; }
                    foreach ($battle->{$loserSide} as $c) { $placements[$c->user_id] = $base + 1; }
                }

                // Tied 3rd for semifinal losers when the finale doesn't have a 3rd-place battle yet
                if ($finaleRound->battles->count() < 2 && $battleRoundsOrdered->count() >= 2) {
                    $semi = $battleRoundsOrdered->get($battleRoundsOrdered->count() - 2);
                    if ($semi && $semi->scores_published) {
                        foreach ($semi->battles as $battle) {
                            if ($battle->winner_side === null) continue;
                            $loserSide = $battle->winner_side === 'a' ? 'sideB' : 'sideA';
                            foreach ($battle->{$loserSide} as $c) {
                                $placements[$c->user_id] = $placements[$c->user_id] ?? 3;
                            }
                        }
                    }
                }
            }

            $medalColor = fn (?int $p) => match ($p) { 1 => '#FFD700', 2 => '#C0C0C0', 3 => '#CD7F32', default => null };
        @endphp
        <div class="flex items-start gap-12 overflow-x-auto pb-4">
        @foreach($battleRoundsOrdered as $roundIdx => $round)
            @php
                $isPublished = $round->scores_published;
                $prevRound = $battleRoundsOrdered->get($roundIdx - 1);
                $prevRoundName = $prevRound?->name ?? '';
                $roundLabel = mb_strtoupper($round->name);

                $partAbbrevs = $round->parts->map(function ($part) use ($locale) {
                    $name = trim($part->getTranslation('name', $locale));
                    $words = preg_split('/\s+/', $name) ?: [];
                    $abbrev = collect($words)
                        ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
                        ->filter()
                        ->implode('');

                    return ['full' => $name, 'abbrev' => $abbrev ?: '·'];
                });
                $isCoachDecision = $round->scoring_format?->value === 'coach_decision';
            @endphp
            <div class="flex flex-col gap-3 min-w-[300px] flex-1">
                <div class="flex flex-col gap-1">
                    <span class="text-[11px] font-bold font-sans tracking-[2px] {{ $isPublished ? 'text-[#FF2D2D]' : 'text-[#555555]' }}">{{ $roundLabel }}</span>
                    @if($partAbbrevs->isNotEmpty())
                    <div class="flex items-center gap-1 px-4 text-[10px] font-sans text-[#666666] uppercase tracking-wider">
                        <span class="flex-1"></span>
                        @foreach($partAbbrevs as $part)
                        <span class="w-7 text-center cursor-help" title="{{ $part['full'] }}">{{ $part['abbrev'] }}</span>
                        @endforeach
                        <span class="w-12 text-right">{{ mb_strtoupper(__('event_detail.score')) }}</span>
                    </div>
                    @endif
                </div>

                @php
                    $isFinaleRound = $bracketIsCategoryFinale && $finaleRound && $round->id === $finaleRound->id;
                @endphp
                @if($round->battles->isNotEmpty())
                @foreach($round->battles as $battle)
                @php
                    $aIsWinner = $battle->winner_side === 'a';
                    $bIsWinner = $battle->winner_side === 'b';
                    $aPlacements = $battle->sideA->pluck('user_id')->map(fn ($id) => $placements[$id] ?? null)->filter()->values();
                    $bPlacements = $battle->sideB->pluck('user_id')->map(fn ($id) => $placements[$id] ?? null)->filter()->values();
                    $topMedalInBattle = $isFinaleRound
                        ? collect([$aPlacements, $bPlacements])->flatten()->filter(fn ($p) => $p <= 3)->sort()->first()
                        : null;
                    $battleBorderColor = $topMedalInBattle ? $medalColor($topMedalInBattle).'55' : '#222222';
                    $battleSubLabel = null;
                    if ($isFinaleRound && $round->battles->count() >= 2) {
                        $battleSubLabel = $battle->bracket_position === 1
                            ? __('event_detail.bracket_final_label')
                            : __('event_detail.bracket_third_place_label');
                    }
                @endphp
                @if($battleSubLabel)
                <div class="text-[10px] font-semibold font-sans tracking-[2px] text-[#666666] uppercase mb-1 mt-2">{{ $battleSubLabel }}</div>
                @endif
                <div class="rounded-lg overflow-hidden" style="border: 1px solid {{ $battleBorderColor }};">
                    @foreach(['a' => 'sideA', 'b' => 'sideB'] as $sideKey => $sideRel)
                    @php
                        $isWinnerSide = $isPublished && $battle->winner_side === $sideKey;
                        $firstPlace = $isFinaleRound && $battle->{$sideRel}->first()
                            ? ($placements[$battle->{$sideRel}->first()->user_id] ?? null)
                            : null;
                        $sidePlacementColor = $medalColor($firstPlace);
                        $sideLabel = $sideKey === 'a' ? $battle->getCompetitorALabel() : $battle->getCompetitorBLabel();
                        $sideScore = $isPublished ? ($sideKey === 'a' ? $battle->side_a_score : $battle->side_b_score) : null;
                    @endphp
                    <div class="flex items-center gap-1 px-4 py-2.5 {{ $isWinnerSide ? 'bg-[#111111]' : '' }} {{ ! $loop->first ? 'border-t border-[#1A1A1A]' : '' }}">
                        <div class="flex-1 flex items-center gap-2 min-w-0">
                            @if($firstPlace && $firstPlace <= 3)
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full text-[10px] font-bold font-sans shrink-0" style="background-color: {{ $sidePlacementColor }}1A; color: {{ $sidePlacementColor }}CC;" title="{{ $firstPlace }}. miesto">{{ $firstPlace }}</span>
                            @endif
                            @if($sidePlacementColor)
                            <span class="text-[13px] font-sans truncate {{ $isWinnerSide ? 'font-semibold' : '' }}" style="color: {{ $sidePlacementColor }}CC;">{{ $sideLabel }}</span>
                            @else
                            <span class="text-[13px] font-sans truncate {{ $isWinnerSide ? 'text-white font-semibold' : 'text-[#888888]' }}">{{ $sideLabel }}</span>
                            @endif
                        </div>
                        @if($partAbbrevs->isNotEmpty())
                        @foreach($round->parts as $part)
                        @php
                            $pw = $isPublished ? (($battle->part_winners ?? [])[$part->id] ?? null) : null;
                            $partMark = match($pw) {
                                $sideKey => ['text' => 'W', 'cls' => 'text-[#FF2D2D] font-bold'],
                                'draw' => ['text' => 'X', 'cls' => 'text-[#888888]'],
                                null => ['text' => '—', 'cls' => 'text-[#333333]'],
                                default => ['text' => 'L', 'cls' => 'text-[#444444]'],
                            };
                        @endphp
                        <span class="w-7 text-center text-[11px] font-sans {{ $partMark['cls'] }}" title="{{ $part->getTranslation('name', $locale) }}">{{ $partMark['text'] }}</span>
                        @endforeach
                        @endif
                        <span class="w-12 text-right text-[13px] font-sans tabular-nums {{ $isWinnerSide ? 'text-[#FF2D2D] font-bold' : 'text-[#555555]' }}">
                            {{ $sideScore !== null ? ($isCoachDecision ? (int) $sideScore : number_format((float) $sideScore, 1)) : '—' }}
                        </span>
                    </div>
                    @endforeach
                </div>
                @endforeach
                @else
                @php
                    $battleCount = $round->battles->count();
                    if ($battleCount === 0 && $prevRound) { $battleCount = max(1, intdiv($prevRound->battles->count(), 2)); }
                    $battleCount = max($battleCount, 1);
                @endphp
                @for($b = 0; $b < $battleCount; $b++)
                <div class="rounded-lg border border-[#222222] overflow-hidden opacity-50">
                    <div class="flex items-center justify-between px-4 py-2.5 bg-[#111111]">
                        <span class="text-[13px] font-sans text-[#555555] italic">@if($prevRoundName){{ __('event_detail.results_winner_of') }} {{ $prevRoundName }} {{ $b * 2 + 1 }}@else TBD @endif</span>
                        <span class="text-[13px] font-sans text-[#333333]">&mdash;</span>
                    </div>
                    <div class="flex items-center justify-between px-4 py-2.5 border-t border-[#1A1A1A]">
                        <span class="text-[13px] font-sans text-[#555555] italic">@if($prevRoundName){{ __('event_detail.results_winner_of') }} {{ $prevRoundName }} {{ $b * 2 + 2 }}@else TBD @endif</span>
                        <span class="text-[13px] font-sans text-[#333333]">&mdash;</span>
                    </div>
                </div>
                @endfor
                @endif
            </div>
        @endforeach
        </div>
    </div>
    @endif
    </template>
    @endforeach
</div>
</template>
@endforeach
</div>

@else
<div class="flex flex-col items-center justify-center py-20 text-center">
<svg class="w-12 h-12 text-[#333333] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M8 21h8m-4-4v4m-4.65-4a8 8 0 1 1 9.3 0"/><path d="M12 11V5l4 2"/></svg>
<h3 class="text-[#888888] text-lg font-bold font-sans mb-2">{{ __('event_detail.results_not_available') }}</h3>
<p class="text-[#666666] text-sm font-sans">{{ __('event_detail.results_not_available_desc') }}</p>
</div>
@endif
