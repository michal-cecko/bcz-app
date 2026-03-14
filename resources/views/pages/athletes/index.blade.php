@extends('layouts.public')

@section('content')
    {{-- Hero --}}
    <section class="bg-[#0A0A0A] pt-[120px] pb-[60px] px-5 md:px-10 lg:px-20">
        <div class="max-w-[1440px] mx-auto flex flex-col items-center gap-6">
            <div class="bg-bcz-red/20 rounded-full px-4 py-2 flex items-center gap-2">
                <svg class="w-6 h-6 text-bcz-red" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M18.75 4.236c.982.143 1.954.317 2.916.52A6.003 6.003 0 0 1 16.27 9.728M18.75 4.236V4.5c0 2.108-.966 3.99-2.48 5.228m0 0a6.003 6.003 0 0 1-4.52-1.978 6.003 6.003 0 0 1-4.52 1.978" /></svg>
                <span class="text-bcz-red text-xs font-bold">{{ __('athletes_archive.hero_badge') }}</span>
            </div>
            <h1 class="font-display font-bold text-[56px] tracking-[1px] text-center">{{ __('athletes_archive.hero_title') }}</h1>
            <p class="text-[#888888] text-lg text-center max-w-[650px] leading-relaxed">{{ __('athletes_archive.hero_desc') }}</p>
            <div class="flex gap-12 pt-6">
                <div class="flex flex-col items-center gap-1">
                    <span class="font-display font-bold text-4xl tracking-[0.5px] text-bcz-red">{{ $athleteCount }}+</span>
                    <span class="text-[#888888] text-sm">{{ __('athletes_archive.stat_athletes') }}</span>
                </div>
                <div class="flex flex-col items-center gap-1">
                    <span class="font-display font-bold text-4xl tracking-[0.5px] text-bcz-red">50+</span>
                    <span class="text-[#888888] text-sm">{{ __('athletes_archive.stat_competitions') }}</span>
                </div>
                <div class="flex flex-col items-center gap-1">
                    <span class="font-display font-bold text-4xl tracking-[0.5px] text-bcz-red">100+</span>
                    <span class="text-[#888888] text-sm">{{ __('athletes_archive.stat_medals') }}</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Archive --}}
    <section class="bg-[#0A0A0A] pb-20 px-5 md:px-10 lg:px-20">
        <div class="max-w-[1440px] mx-auto">
            <livewire:athletes-archive />
        </div>
    </section>
@endsection
