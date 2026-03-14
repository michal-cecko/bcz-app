@extends('layouts.public')

@section('content')
    <section class="bg-[#0A0A0A] pt-[120px] pb-[60px] px-5 md:px-10 lg:px-20">
        <div class="max-w-[1440px] mx-auto flex flex-col items-center gap-6">
            <div class="bg-bcz-red/20 rounded-full px-4 py-2 flex items-center gap-2">
                <svg class="w-6 h-6 text-bcz-red" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" /></svg>
                <span class="text-bcz-red text-xs font-bold">{{ __('teams_archive.hero_badge') }}</span>
            </div>
            <h1 class="font-display font-bold text-[56px] tracking-[1px] text-center">{{ __('teams_archive.hero_title') }}</h1>
            <p class="text-[#888888] text-lg text-center max-w-[650px] leading-relaxed">{{ __('teams_archive.hero_desc') }}</p>
            <div class="flex gap-12 pt-6">
                <div class="flex flex-col items-center gap-1">
                    <span class="font-display font-bold text-4xl tracking-[0.5px] text-bcz-red">{{ $teamCount }}+</span>
                    <span class="text-[#888888] text-sm">{{ __('teams_archive.stat_teams') }}</span>
                </div>
                <div class="flex flex-col items-center gap-1">
                    <span class="font-display font-bold text-4xl tracking-[0.5px] text-bcz-red">50+</span>
                    <span class="text-[#888888] text-sm">{{ __('teams_archive.stat_members') }}</span>
                </div>
                <div class="flex flex-col items-center gap-1">
                    <span class="font-display font-bold text-4xl tracking-[0.5px] text-bcz-red">5+</span>
                    <span class="text-[#888888] text-sm">{{ __('teams_archive.stat_cities') }}</span>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-[#0A0A0A] pb-20 px-5 md:px-10 lg:px-20">
        <div class="max-w-[1440px] mx-auto">
            <livewire:teams-archive />
        </div>
    </section>
@endsection
