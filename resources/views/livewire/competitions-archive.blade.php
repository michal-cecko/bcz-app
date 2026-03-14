<div>
    {{-- Filters --}}
    <div class="flex flex-wrap gap-3 mb-8">
        <button wire:click="$set('statusFilter', '')" class="px-4 py-2.5 text-sm rounded-lg border transition-colors {{ $statusFilter === '' ? 'bg-bcz-red border-bcz-red text-white' : 'bg-[#111111] border-[#222222] text-[#888888] hover:border-[#444444]' }}">
            {{ __('archive.all') }}
        </button>
        <button wire:click="$set('statusFilter', 'upcoming')" class="px-4 py-2.5 text-sm rounded-lg border transition-colors {{ $statusFilter === 'upcoming' ? 'bg-bcz-red border-bcz-red text-white' : 'bg-[#111111] border-[#222222] text-[#888888] hover:border-[#444444]' }}">
            {{ __('archive.upcoming') }}
        </button>
        <button wire:click="$set('statusFilter', 'finished')" class="px-4 py-2.5 text-sm rounded-lg border transition-colors {{ $statusFilter === 'finished' ? 'bg-bcz-red border-bcz-red text-white' : 'bg-[#111111] border-[#222222] text-[#888888] hover:border-[#444444]' }}">
            {{ __('archive.finished') }}
        </button>
    </div>

    {{-- Upcoming Competitions --}}
    @if($upcoming->isNotEmpty())
        <div class="flex flex-col gap-12 mb-16">
            <div class="flex flex-col items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[3px]">{{ __('archive.upcoming_label') }}</span>
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                </div>
                <h2 class="font-display font-bold text-[24px] md:text-[36px] tracking-wide text-white text-center">{{ __('archive.upcoming_title') }}</h2>
            </div>

            <div class="flex flex-col gap-4">
                @foreach($upcoming as $competition)
                    <a href="{{ route('team.competition.show', [$competition->organizerTeam, $competition]) }}" wire:navigate class="bg-[#111111] border border-[#222222] rounded-2xl overflow-hidden flex flex-col md:flex-row hover:border-[#333333] transition-colors group">
                        <div class="w-full md:w-[140px] {{ $loop->first ? 'bg-bcz-red' : 'bg-[#1A1A1A]' }} flex flex-col items-center justify-center py-6 md:py-0 shrink-0">
                            @if($competition->date_start)
                                <span class="font-display font-bold text-[36px] leading-none text-white">{{ $competition->date_start->format('d') }}</span>
                                <span class="{{ $loop->first ? 'text-white/80' : 'text-[#888888]' }} text-[13px] font-semibold tracking-wider">{{ mb_strtoupper($competition->date_start->translatedFormat('M Y')) }}</span>
                            @endif
                        </div>
                        <div class="flex-1 flex flex-col gap-3 p-6 md:p-8">
                            <h3 class="font-display font-bold text-[24px] md:text-[28px] tracking-wide text-white">{{ $competition->getTranslation('name', app()->getLocale()) }}</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($competition->disciplines as $discipline)
                                    <span class="bg-bcz-red/[0.12] text-bcz-red text-[10px] font-bold tracking-wider px-3 py-1.5 rounded">{{ mb_strtoupper($discipline->getTranslation('name', app()->getLocale())) }}</span>
                                @endforeach
                            </div>
                            @if($competition->city || $competition->country)
                                <div class="flex items-center gap-2 text-[#888888] text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>
                                    <span>{{ collect([$competition->city, $competition->country])->filter()->join(', ') }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex items-center px-6 pb-6 md:pb-0 md:pr-8">
                            <span class="{{ $loop->first ? 'bg-bcz-red text-white' : 'border border-[#444444] text-white hover:border-bcz-red' }} text-[12px] font-bold tracking-wider px-6 py-3 rounded-lg transition-colors whitespace-nowrap">DETAIL</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Finished Competitions --}}
    @if($finished && ($finished instanceof \Illuminate\Pagination\LengthAwarePaginator ? $finished->isNotEmpty() : $finished->isNotEmpty()))
        <div class="flex flex-col gap-12">
            <div class="flex flex-col items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[3px]">{{ __('archive.finished_label') }}</span>
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                </div>
                <h2 class="font-display font-bold text-[24px] md:text-[36px] tracking-wide text-white text-center">{{ __('archive.finished_title') }}</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($finished as $competition)
                    <a href="{{ route('team.competition.show', [$competition->organizerTeam, $competition]) }}" wire:navigate class="bg-[#1A1A1A] border border-[#222222] rounded-2xl overflow-hidden group hover:border-[#333333] transition-colors">
                        <div class="relative w-full h-[200px] bg-[#1A1A1A] overflow-hidden">
                            @if($competition->featured_image)
                                <img src="{{ $competition->featured_image }}" alt="{{ $competition->getTranslation('name', app()->getLocale()) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @endif
                            <div class="absolute top-4 left-4 bg-[#222222] text-[#AAAAAA] text-[11px] font-bold tracking-wider px-3 py-1.5 rounded">{{ __('archive.finished_badge') }}</div>
                        </div>
                        <div class="flex flex-col gap-3 p-6">
                            @if($competition->date_start)
                                <span class="text-[#888888] text-[12px] font-medium tracking-wider">{{ mb_strtoupper($competition->date_start->translatedFormat('F Y')) }}</span>
                            @endif
                            <h3 class="font-display font-bold text-[24px] tracking-wide text-white">{{ $competition->getTranslation('name', app()->getLocale()) }}</h3>
                            @if($competition->city || $competition->country)
                                <div class="flex items-center gap-2 text-bcz-red text-[12px] font-bold">
                                    <span>{{ collect([$competition->city, $competition->country])->filter()->join(', ') }}</span>
                                </div>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

            @if($finished instanceof \Illuminate\Pagination\LengthAwarePaginator && $finished->hasPages())
                <div class="flex justify-center pt-4">
                    {{ $finished->links() }}
                </div>
            @endif
        </div>
    @endif

    @if($upcoming->isEmpty() && (! $finished || $finished->isEmpty()))
        <div class="text-center py-20">
            <p class="text-[#666666] text-lg">{{ __('archive.no_competitions') }}</p>
        </div>
    @endif
</div>
