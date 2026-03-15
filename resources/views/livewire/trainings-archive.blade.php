<div>
    {{-- Tailwind safelist: text-red-500 text-orange-400 text-emerald-500 --}}
    {{-- Filter Section --}}
    <div class="bg-[#111111] rounded-xl p-8 md:p-10 mb-8">
        {{-- Filter Header --}}
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="21" x2="14" y1="4" y2="4"/><line x1="10" x2="3" y1="4" y2="4"/><line x1="21" x2="12" y1="12" y2="12"/><line x1="8" x2="3" y1="12" y2="12"/><line x1="21" x2="16" y1="20" y2="20"/><line x1="12" x2="3" y1="20" y2="20"/><line x1="14" x2="14" y1="2" y2="6"/><line x1="8" x2="8" y1="10" y2="14"/><line x1="16" x2="16" y1="18" y2="22"/>
                </svg>
                <span class="text-white text-sm font-medium">{{ __('archive.filter') }}</span>
            </div>
            @if($this->hasActiveFilters())
                <button wire:click="resetFilters" class="flex items-center gap-2 text-[#888888] text-sm hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                    </svg>
                    {{ __('archive.reset_filters') }}
                </button>
            @endif
        </div>

        {{-- Filter Dropdowns --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Category Filter --}}
            <div class="flex flex-col gap-3">
                <label class="text-[#888888] text-xs">{{ __('archive.category') }}</label>
                <select wire:model.live="categoryFilter" class="bg-[#0A0A0A] border border-[#333333] text-white text-sm rounded-lg px-4 py-3.5 focus:border-bcz-red focus:ring-0 outline-none w-full appearance-none cursor-pointer">
                    <option value="">{{ __('archive.all_categories') }}</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->getTranslation('name', app()->getLocale()) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Day Filter --}}
            <div class="flex flex-col gap-3">
                <label class="text-[#888888] text-xs">{{ __('archive.day') }}</label>
                <select wire:model.live="dayFilter" class="bg-[#0A0A0A] border border-[#333333] text-white text-sm rounded-lg px-4 py-3.5 focus:border-bcz-red focus:ring-0 outline-none w-full appearance-none cursor-pointer">
                    <option value="">{{ __('archive.all_days') }}</option>
                    @foreach($days as $day)
                        <option value="{{ $day }}">{{ __('archive.days.' . $day) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Location Filter --}}
            <div class="flex flex-col gap-3">
                <label class="text-[#888888] text-xs">{{ __('archive.location') }}</label>
                <select wire:model.live="locationFilter" class="bg-[#0A0A0A] border border-[#333333] text-white text-sm rounded-lg px-4 py-3.5 focus:border-bcz-red focus:ring-0 outline-none w-full appearance-none cursor-pointer">
                    <option value="">{{ __('archive.all_locations') }}</option>
                    @foreach($locations as $location)
                        <option value="{{ $location }}">{{ $location }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Search Filter --}}
            <div class="flex flex-col gap-3">
                <label class="text-[#888888] text-xs">{{ __('archive.search') }}</label>
                <div class="relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-[#888888] pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                    </svg>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="{{ __('archive.search_placeholder') }}" class="bg-[#0A0A0A] border border-[#333333] text-white text-sm rounded-lg pl-10 pr-4 py-3.5 focus:border-bcz-red focus:ring-0 outline-none w-full placeholder-[#555555]">
                </div>
            </div>
        </div>

        {{-- Active Filter Tags --}}
        @if($this->hasActiveFilters())
            <div class="flex flex-wrap gap-3 mt-8">
                @if($categoryFilter)
                    @php $activeCat = $categories->firstWhere('id', $categoryFilter); @endphp
                    @if($activeCat)
                        <button wire:click="$set('categoryFilter', '')" class="inline-flex items-center gap-2 bg-bcz-red/20 text-bcz-red text-xs rounded-full px-3 py-2 hover:bg-bcz-red/30 transition-colors cursor-pointer">
                            {{ $activeCat->getTranslation('name', app()->getLocale()) }}
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                            </svg>
                        </button>
                    @endif
                @endif
                @if($dayFilter)
                    <button wire:click="$set('dayFilter', '')" class="inline-flex items-center gap-2 bg-bcz-red/20 text-bcz-red text-xs rounded-full px-3 py-2 hover:bg-bcz-red/30 transition-colors cursor-pointer">
                        {{ __('archive.days.' . $dayFilter) }}
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                        </svg>
                    </button>
                @endif
                @if($locationFilter)
                    <button wire:click="$set('locationFilter', '')" class="inline-flex items-center gap-2 bg-bcz-red/20 text-bcz-red text-xs rounded-full px-3 py-2 hover:bg-bcz-red/30 transition-colors cursor-pointer">
                        {{ $locationFilter }}
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                        </svg>
                    </button>
                @endif
                @if($search)
                    <button wire:click="$set('search', '')" class="inline-flex items-center gap-2 bg-bcz-red/20 text-bcz-red text-xs rounded-full px-3 py-2 hover:bg-bcz-red/30 transition-colors cursor-pointer">
                        "{{ $search }}"
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                        </svg>
                    </button>
                @endif
            </div>
        @endif
    </div>

    {{-- Results Header --}}
    <div class="flex items-center justify-between mb-8">
        <span class="text-[#888888] text-base">{{ __('archive.found_trainings', ['count' => $trainings->total()]) }}</span>
    </div>

    @if($trainings->isEmpty())
        <div class="text-center py-20">
            <p class="text-[#666666] text-lg">{{ __('archive.no_trainings') }}</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($trainings as $training)
                <a href="{{ route('team.training.show', [$training->team, $training]) }}" wire:navigate class="bg-[#111111] border border-[#222222] rounded-xl overflow-hidden group hover:border-[#333333] transition-colors flex flex-col">
                    {{-- Card Image --}}
                    <div class="h-[180px] bg-[#1A1A1A] overflow-hidden">
                        @if($training->sportCategory?->getFirstMediaUrl('hero_image'))
                            <img src="{{ $training->sportCategory->getFirstMediaUrl('hero_image') }}" alt="{{ $training->getTranslation('title', app()->getLocale()) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-12 h-12 text-[#333333]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    {{-- Card Content --}}
                    <div class="p-6 flex flex-col gap-4 flex-1">
                        {{-- Tags --}}
                        <div class="flex items-center gap-2 flex-wrap">
                            @if($training->sportCategory)
                                <span class="bg-bcz-red/20 text-bcz-red text-[10px] font-normal uppercase px-3 py-1.5 rounded">{{ $training->sportCategory->getTranslation('name', app()->getLocale()) }}</span>
                            @endif
                            @if($training->age_group)
                                <span class="bg-[#222222] text-[#888888] text-[10px] font-normal uppercase px-3 py-1.5 rounded">{{ $training->age_group }}</span>
                            @endif
                        </div>

                        {{-- Title --}}
                        <h3 class="text-white text-xl font-semibold">{{ $training->getTranslation('title', app()->getLocale()) }}</h3>

                        {{-- Info Rows --}}
                        <div class="flex flex-col gap-2 flex-1">
                            @if($training->schedule_days)
                                <div class="flex items-center gap-2 text-[#888888] text-sm">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/>
                                    </svg>
                                    <span>
                                        {{ collect($training->schedule_days)->map(fn ($d) => __('archive.days.' . $d))->join(', ') }}@if($training->start_time), {{ \Illuminate\Support\Str::substr($training->start_time, 0, 5) }}@if($training->duration_minutes) - {{ \Carbon\Carbon::createFromFormat('H:i:s', $training->start_time)->addMinutes($training->duration_minutes)->format('H:i') }}@endif
                                        @endif
                                    </span>
                                </div>
                            @endif
                            @if($training->getTranslation('place_name', app()->getLocale()))
                                <div class="flex items-center gap-2 text-[#888888] text-sm">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>
                                    </svg>
                                    <span>{{ $training->getTranslation('place_name', app()->getLocale()) }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Footer --}}
                        <div class="flex items-center justify-between pt-2">
                            @if($training->max_capacity)
                                @php
                                    $registered = $training->registrations_count;
                                    $remaining = max(0, $training->max_capacity - $registered);
                                    $fillPercent = ($registered / $training->max_capacity) * 100;
                                    $capacityColor = match(true) {
                                        $fillPercent >= 90 => 'text-red-500',
                                        $fillPercent >= 65 => 'text-orange-400',
                                        default => 'text-emerald-500',
                                    };
                                @endphp
                                <div class="flex items-center gap-2 {{ $capacityColor }} text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                    <span>{{ $remaining > 0 ? $remaining . '/' . $training->max_capacity . ' ' . __('archive.spots') : __('archive.full') }}</span>
                                </div>
                            @else
                                <div></div>
                            @endif
                            <span class="bg-bcz-red rounded-md px-5 py-2.5 text-sm font-semibold text-white group-hover:bg-red-700 transition-colors">Detail</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        @if($trainings->hasPages())
            <div class="flex justify-center pt-10">
                {{ $trainings->links() }}
            </div>
        @endif
    @endif
</div>
