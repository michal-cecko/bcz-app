<div>
    {{-- Filter Section --}}
    <div class="bg-[#0D0D0D] rounded-xl p-8 md:p-10 mb-8">
        <div class="flex flex-col gap-6">
            {{-- Search --}}
            <div class="relative">
                <span class="absolute left-5 top-1/2 -translate-y-1/2 text-[#666666]"><svg class="w-5 h-5 text-[#666666]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg></span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="{{ __('teams_archive.search_placeholder') }}" class="bg-[#111111] border border-[#222222] text-white text-[15px] pl-12 pr-5 py-4 focus:border-bcz-red focus:ring-0 outline-none w-full placeholder-[#666666]">
            </div>

            @if($this->hasActiveFilters())
                <div class="flex items-center gap-4">
                    <button wire:click="resetFilters" class="text-[#888888] text-sm hover:text-white transition-colors">{{ __('teams_archive.reset') }}</button>
                </div>
            @endif
        </div>
    </div>

    {{-- Results Header --}}
    <div class="flex items-center justify-between mb-8">
        <span class="text-[#888888] text-sm">{{ __('teams_archive.showing', ['count' => $teams->total()]) }}</span>
    </div>

    {{-- Teams Grid --}}
    @if($teams->isNotEmpty())
        @php $locale = app()->getLocale(); @endphp
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($teams as $team)
                <div class="bg-[#111111] border border-[#222222] rounded-2xl overflow-hidden flex flex-col">
                    {{-- Logo --}}
                    <div class="w-full h-[200px] overflow-hidden">
                        @if($team->logo)
                            <img src="{{ Storage::url($team->logo) }}" alt="{{ $team->getTranslation('name', $locale) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-[#1A1A1A] flex items-center justify-center">
                                <span class="text-bcz-red font-display font-bold text-5xl">{{ mb_substr($team->getTranslation('name', $locale), 0, 3) }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="p-6 flex flex-col gap-4 flex-1">
                        <h3 class="text-white text-xl font-bold">{{ $team->getTranslation('name', $locale) }}</h3>

                        @if($team->getTranslation('story', $locale))
                            <p class="text-[#888888] text-sm line-clamp-3">{{ Str::limit(strip_tags($team->getTranslation('story', $locale)), 120) }}</p>
                        @endif

                        <div class="flex items-center gap-4 text-[#888888] text-sm">
                            <span>{{ sk_plural($team->members_count, 'člen', 'členovia', 'členov') }}</span>
                            <span>{{ sk_plural($team->trainings_count, 'tréning', 'tréningy', 'tréningov') }}</span>
                        </div>

                        <a href="{{ route('team.show', $team) }}" class="flex items-center justify-center gap-2 bg-[#0A0A0A] border border-[#333333] text-white text-sm font-semibold rounded-lg py-3.5 mt-auto hover:border-[#555555] transition-colors">
                            {{ __('teams_archive.view_team') }}
                            <span>→</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        @if($teams->hasPages())
            {{ $teams->links() }}
        @endif
    @else
        <div class="text-center py-20">
            <p class="text-[#888888] text-lg">{{ __('teams_archive.no_results') }}</p>
        </div>
    @endif
</div>
