@extends('layouts.public')

@section('content')
    <section class="bg-[#0A0A0A] pt-[120px] pb-[60px] px-5 md:px-10 lg:px-20">
        <div class="max-w-[1440px] mx-auto flex flex-col items-center gap-6">
            <div class="bg-bcz-red/20 rounded-full px-4 py-2 flex items-center gap-2">
                <svg class="w-6 h-6 text-bcz-red" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0 0 12 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52 2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 0 1-2.031.352 5.988 5.988 0 0 1-2.031-.352c-.483-.174-.711-.703-.59-1.202L18.75 4.971Zm-16.5.52c.99-.203 1.99-.377 3-.52m0 0 2.62 10.726c.122.499-.106 1.028-.589 1.202a5.989 5.989 0 0 1-2.031.352 5.989 5.989 0 0 1-2.031-.352c-.483-.174-.711-.703-.59-1.202L5.25 4.971Z" /></svg>
                <span class="text-bcz-red text-xs font-bold">{{ __('judges_archive.hero_badge') }}</span>
            </div>
            <h1 class="font-display font-bold text-[56px] tracking-[1px] text-center">{{ __('judges_archive.hero_title') }}</h1>
            <p class="text-[#888888] text-lg text-center max-w-[650px] leading-relaxed">{{ __('judges_archive.hero_desc') }}</p>
            <div class="flex gap-12 pt-6">
                <div class="flex flex-col items-center gap-1">
                    <span class="font-display font-bold text-4xl tracking-[0.5px] text-bcz-red">{{ $judgeCount }}+</span>
                    <span class="text-[#888888] text-sm">{{ __('judges_archive.stat_judges') }}</span>
                </div>
                <div class="flex flex-col items-center gap-1">
                    <span class="font-display font-bold text-4xl tracking-[0.5px] text-bcz-red">30+</span>
                    <span class="text-[#888888] text-sm">{{ __('judges_archive.stat_competitions') }}</span>
                </div>
                <div class="flex flex-col items-center gap-1">
                    <span class="font-display font-bold text-4xl tracking-[0.5px] text-bcz-red">10+</span>
                    <span class="text-[#888888] text-sm">{{ __('judges_archive.stat_certifications') }}</span>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-[#0A0A0A] pb-20 px-5 md:px-10 lg:px-20">
        <div class="max-w-[1440px] mx-auto">
            <livewire:judges-archive />
        </div>
    </section>
@endsection
