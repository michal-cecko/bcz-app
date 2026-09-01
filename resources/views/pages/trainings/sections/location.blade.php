    @if($placeName || $training->place_address || $gatheringPlace)
        <section class="bg-[#111111] py-20">
            <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col gap-12">
                {{-- Header --}}
                <div class="flex flex-col items-center gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                        <span class="text-bcz-red text-xs font-normal tracking-wider">{{ __('training_detail.location_label') }}</span>
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                    </div>
                    <h2 class="font-display font-bold text-4xl tracking-wide text-center">{{ __('training_detail.location_title') }}</h2>
                    @if($placeName)
                        <p class="text-[#888888] text-base text-center">{{ $placeName }}</p>
                    @endif
                </div>

                {{-- Content --}}
                <div class="flex flex-col lg:flex-row gap-10">
                    {{-- Map --}}
                    @if($training->latitude && $training->longitude)
                        <div class="flex-1 h-[350px] rounded-xl overflow-hidden bg-[#1A1A1A]">
                            <iframe
                                src="https://maps.google.com/maps?q={{ $training->latitude }},{{ $training->longitude }}&z=15&output=embed"
                                class="w-full h-full border-0"
                                allowfullscreen
                                loading="lazy"
                            ></iframe>
                        </div>
                    @endif

                    {{-- Location Details --}}
                    <div class="w-full lg:w-[400px] shrink-0 flex flex-col gap-6">
                        @if($training->place_address)
                            <div class="bg-[#0A0A0A] border border-[#222222] rounded-xl p-6 flex flex-col gap-4">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-bcz-red shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>
                                    </svg>
                                    <span class="text-white text-sm font-bold">{{ __('training_detail.location_address') }}</span>
                                </div>
                                <div class="flex flex-col gap-1">
                                    @if($placeName)
                                        <p class="text-white text-sm">{{ $placeName }}</p>
                                    @endif
                                    <p class="text-[#888888] text-sm">{{ $training->place_address }}</p>
                                </div>
                            </div>
                        @endif

                        @if($gatheringPlace)
                            <div class="bg-[#0A0A0A] border border-[#222222] rounded-xl p-6 flex flex-col gap-4">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-bcz-red shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                    <span class="text-white text-sm font-bold">{{ __('training_detail.location_meeting_title') }}</span>
                                </div>
                                <p class="text-[#888888] text-sm leading-relaxed">{{ $gatheringPlace }}</p>
                            </div>
                        @endif

                        @if($training->latitude && $training->longitude)
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $training->latitude }},{{ $training->longitude }}" target="_blank" rel="noopener" class="flex items-center justify-center gap-3 bg-bcz-red text-white text-base font-semibold rounded-lg px-6 py-4 hover:bg-red-700 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="3 11 22 2 13 21 11 13 3 11"/>
                                </svg>
                                {{ __('training_detail.location_open_maps') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @endif
