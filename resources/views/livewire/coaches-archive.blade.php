<div>
    {{-- Filter Section --}}
    <div class="bg-[#0D0D0D] rounded-xl p-8 md:p-10 mb-8">
        <div class="flex flex-col gap-6">
            {{-- Search --}}
            <div class="relative">
                <span class="absolute left-5 top-1/2 -translate-y-1/2 text-[#666666]"><svg class="w-5 h-5 text-[#666666]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg></span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="{{ __('coaches_archive.search_placeholder') }}" class="bg-[#111111] border border-[#222222] text-white text-[15px] rounded-lg pl-12 pr-5 py-4 focus:border-bcz-red focus:ring-0 outline-none w-full placeholder-[#666666]">
            </div>

            {{-- Category Tabs --}}
            <div class="flex flex-wrap gap-3">
                <button wire:click="$set('categoryFilter', '')" class="{{ $categoryFilter === '' ? 'bg-bcz-red text-white' : 'bg-[#111111] border border-[#333333] text-[#CCCCCC]' }} text-sm font-medium rounded-lg px-5 py-2.5 transition-colors cursor-pointer">
                    {{ __('coaches_archive.all') }}
                </button>
                @foreach($categories as $category)
                    <button wire:click="$set('categoryFilter', '{{ $category->id }}')" class="{{ $categoryFilter === $category->id ? 'bg-bcz-red text-white' : 'bg-[#111111] border border-[#333333] text-[#CCCCCC]' }} text-sm font-medium rounded-lg px-5 py-2.5 transition-colors cursor-pointer">
                        {{ $category->getTranslation('name', app()->getLocale()) }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Results Header --}}
    <div class="flex items-center justify-between mb-8">
        <span class="text-[#888888] text-sm">{{ __('coaches_archive.showing', ['count' => $coaches->total()]) }}</span>
    </div>

    {{-- Coaches Grid --}}
    @if($coaches->isNotEmpty())
        @php $locale = app()->getLocale(); @endphp
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($coaches as $coach)
                <div class="bg-[#111111] border border-[#222222] rounded-2xl overflow-hidden flex flex-col">
                    {{-- Image --}}
                    <div class="w-full h-[280px] overflow-hidden">
                        @if($coach->coachProfile?->getFirstMediaUrl('biography_image'))
                            <img src="{{ $coach->coachProfile->getFirstMediaUrl('biography_image') }}" alt="{{ $coach->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-[#1A1A1A] flex items-center justify-center">
                                <span class="text-bcz-red font-display font-bold text-6xl">{{ mb_substr($coach->name, 0, 2) }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="p-6 flex flex-col gap-4 flex-1">
                        {{-- Name + Category Badge --}}
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex flex-col gap-1">
                                <h3 class="text-white text-xl font-bold">{{ $coach->name }}</h3>
                            </div>
                            @if($coach->coachedTrainings->isNotEmpty())
                                @php $firstCat = $coach->coachedTrainings->first()?->sportCategory; @endphp
                                @if($firstCat)
                                    <span class="text-[10px] font-bold tracking-wider bg-purple-500/20 text-purple-400 rounded-md px-3 py-1.5 shrink-0">{{ mb_strtoupper($firstCat->getTranslation('name', $locale)) }}</span>
                                @endif
                            @endif
                        </div>

                        {{-- Certifications / Skills --}}
                        @if($coach->certifications->isNotEmpty())
                            <div class="flex flex-wrap gap-2">
                                @foreach($coach->certifications->take(3) as $cert)
                                    <span class="text-xs bg-[#1A1A1A] text-[#888888] rounded px-2.5 py-1">{{ $cert->getTranslation('name', $locale) }}</span>
                                @endforeach
                            </div>
                        @endif

                        {{-- CTA --}}
                        <a href="{{ route('coach.show', $coach) }}" class="flex items-center justify-center gap-2 bg-[#0A0A0A] border border-[#333333] text-white text-sm font-semibold rounded-lg py-3.5 mt-auto hover:border-[#555555] transition-colors">
                            {{ __('coaches_archive.view_profile') }}
                            <span>→</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($coaches->hasPages())
            <div class="mt-12">
                {{ $coaches->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-20">
            <p class="text-[#888888] text-lg">{{ __('coaches_archive.no_results') }}</p>
        </div>
    @endif
</div>
