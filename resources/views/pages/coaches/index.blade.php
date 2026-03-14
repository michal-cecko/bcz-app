@extends('layouts.public')

@section('content')
    {{-- Hero --}}
    <section class="bg-[#0A0A0A] pt-[120px] pb-[60px] px-5 md:px-10 lg:px-20">
        <div class="max-w-[1440px] mx-auto flex flex-col items-center gap-6">
            <div class="bg-bcz-red/20 rounded-full px-4 py-2 flex items-center gap-2">
                <svg class="w-6 h-6 text-bcz-red" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>
                <span class="text-bcz-red text-xs font-bold">{{ __('coaches_archive.hero_badge') }}</span>
            </div>
            <h1 class="font-display font-bold text-[56px] tracking-[1px] text-center">{{ __('coaches_archive.hero_title') }}</h1>
            <p class="text-[#888888] text-lg text-center max-w-[650px] leading-relaxed">{{ __('coaches_archive.hero_desc') }}</p>
            <div class="flex gap-12 pt-6">
                <div class="flex flex-col items-center gap-1">
                    <span class="font-display font-bold text-4xl tracking-[0.5px] text-bcz-red">{{ $coachCount }}+</span>
                    <span class="text-[#888888] text-sm">{{ __('coaches_archive.stat_coaches') }}</span>
                </div>
                <div class="flex flex-col items-center gap-1">
                    <span class="font-display font-bold text-4xl tracking-[0.5px] text-bcz-red">15+</span>
                    <span class="text-[#888888] text-sm">{{ __('coaches_archive.stat_years') }}</span>
                </div>
                <div class="flex flex-col items-center gap-1">
                    <span class="font-display font-bold text-4xl tracking-[0.5px] text-bcz-red">500+</span>
                    <span class="text-[#888888] text-sm">{{ __('coaches_archive.stat_hours') }}</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Archive --}}
    <section class="bg-[#0A0A0A] pb-20 px-5 md:px-10 lg:px-20">
        <div class="max-w-[1440px] mx-auto">
            <livewire:coaches-archive />
        </div>
    </section>
@endsection
