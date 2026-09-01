{{-- Tailwind safelist: text-red-500 text-orange-400 text-emerald-500 bg-red-500 bg-orange-400 bg-emerald-500 --}}
@extends('layouts.public')

@section('title', $training->getTranslation('title', app()->getLocale()) . ' | BCZ Club')

@php
    $locale = app()->getLocale();
    $title = $training->getTranslation('title', $locale);
    $description = $training->getTranslation('description', $locale);
    $placeName = $training->getTranslation('place_name', $locale);
    $gatheringPlace = $training->getTranslation('gathering_place', $locale);
    $heroImage = $training->cardImageUrl();
    $schedules = $training->schedules;
    $timeRange = '';
    if (!$training->is_recurring && $training->start_time) {
        $timeRange = \Illuminate\Support\Str::substr($training->start_time, 0, 5);
        if ($training->duration_minutes) {
            $timeRange .= ' - ' . \Carbon\Carbon::createFromFormat('H:i:s', $training->start_time)->addMinutes($training->duration_minutes)->format('H:i');
        }
    }
    $registeredCount = $training->registrations_count;
    $remaining = $training->max_capacity ? max(0, $training->max_capacity - $registeredCount) : null;
    $capacityPercent = $training->max_capacity ? min(100, round(($registeredCount / $training->max_capacity) * 100)) : 0;
    $capacityColor = match(true) {
        $capacityPercent >= 90 => 'text-red-500',
        $capacityPercent >= 65 => 'text-orange-400',
        default => 'text-emerald-500',
    };
    $barColor = match(true) {
        $capacityPercent >= 90 => 'bg-red-500',
        $capacityPercent >= 65 => 'bg-orange-400',
        default => 'bg-emerald-500',
    };
    $ogImage = $heroImage ?: $training->team?->getFirstMediaUrl('logo');
@endphp

@section('meta_description', seo_description($description))
@if ($ogImage)
    @section('og_image', $ogImage)
@endif
@section('og_type', 'article')

@section('content')
    {{-- Hero Section --}}
    <section class="relative h-[450px] overflow-hidden">
        @if($heroImage)
            <img src="{{ $heroImage }}" alt="{{ $title }}" class="absolute inset-0 w-full h-full object-cover">
        @else
            <div class="absolute inset-0 bg-[#1A1A1A]"></div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-b from-[#0A0A0A] via-transparent to-[#0A0A0A]"></div>

        <div class="relative z-10 flex flex-col items-center justify-center h-full px-5 md:px-20 gap-5">
            {{-- Breadcrumb --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="text-[#666666] text-[11px] font-medium tracking-[2px] hover:text-white transition-colors">{{ __('training_detail.breadcrumb_home') }}</a>
                <span class="text-[#444444] text-[11px]">/</span>
                <a href="{{ route('treningy') }}" class="text-[#666666] text-[11px] font-medium tracking-[2px] hover:text-white transition-colors">{{ __('training_detail.breadcrumb_trainings') }}</a>
                <span class="text-[#444444] text-[11px]">/</span>
                <span class="text-bcz-red text-[11px] font-medium tracking-[2px]">{{ mb_strtoupper($title) }}</span>
            </div>

            {{-- Title --}}
            <h1 class="font-display font-bold text-[40px] md:text-[64px] tracking-wide text-center text-white leading-none">
                {{ $title }}
            </h1>

            {{-- Badge --}}
            @if($training->age_range || $training->sportCategory)
                <div class="flex items-center gap-3 border border-bcz-red bg-bcz-red/10 px-5 py-2.5">
                    <span class="text-bcz-red text-[11px] font-bold tracking-wider">
                        @if($training->age_range){{ $training->age_range }}@endif
                        @if($training->age_range && $training->sportCategory)&nbsp;&middot;&nbsp;@endif
                        @if($training->sportCategory){{ $training->sportCategory->getTranslation('name', $locale) }}@endif
                    </span>
                </div>
            @endif
        </div>
    </section>

    {{-- Remaining sections render in the order configured on the training (admin: Tréning → Poradie sekcií). --}}
    @foreach($training->section_order as $sectionKey)
        @include("pages.trainings.sections.{$sectionKey}")
    @endforeach
@endsection
