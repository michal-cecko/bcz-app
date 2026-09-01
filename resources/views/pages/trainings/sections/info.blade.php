    <section class="bg-[#0A0A0A] py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col lg:flex-row gap-20">
            {{-- Left: About --}}
            <div class="flex-1 flex flex-col gap-8">
                {{-- Label --}}
                <div class="flex items-center gap-3">
                    <div class="w-10 h-0.5 bg-bcz-red"></div>
                    <span class="text-bcz-red text-xs font-bold tracking-[3px]">{{ __('training_detail.about_label') }}</span>
                </div>

                {{-- Title --}}
                <h2 class="font-display font-bold text-[40px] tracking-wide leading-none whitespace-pre-line">{{ __('training_detail.about_title') }}</h2>

                {{-- Description --}}
                @if($description)
                    <div class="text-[#888888] text-[17px] leading-[1.7] space-y-4">
                        @foreach(explode("\n", $description) as $paragraph)
                            @if(trim($paragraph))
                                <p>{{ $paragraph }}</p>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Right: Details Card --}}
            <div class="w-full lg:w-[420px] shrink-0 bg-[#111111] border border-[#222222] p-8 flex flex-col gap-6">
                <span class="text-bcz-red text-xs font-bold tracking-[2px]">{{ __('training_detail.details_title') }}</span>

                {{-- Detail Rows --}}
                <div class="flex flex-col gap-5">
                    @if($training->sportCategory)
                        <div class="flex items-center justify-between">
                            <span class="text-[#666666] text-sm">{{ __('training_detail.detail_category') }}</span>
                            <span class="text-white text-sm font-semibold">{{ $training->sportCategory->getTranslation('name', $locale) }}</span>
                        </div>
                    @endif
                    @if($training->age_range)
                        <div class="flex items-center justify-between">
                            <span class="text-[#666666] text-sm">{{ __('training_detail.detail_age_group') }}</span>
                            <span class="text-white text-sm font-semibold">{{ $training->age_range }}</span>
                        </div>
                    @endif
                    <div class="flex items-center justify-between">
                        <span class="text-[#666666] text-sm">{{ __('training_detail.detail_gender') }}</span>
                        <span class="text-white text-sm font-semibold">
                            @if($training->gender)
                                {{ $training->gender->translation() }}
                            @else
                                {{ __('training_detail.all_genders') }}
                            @endif
                        </span>
                    </div>
                    @if($schedules->isNotEmpty())
                        @foreach($schedules as $schedule)
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] text-sm">{{ __('training_detail.days.' . $schedule->day) }}</span>
                                <span class="text-white text-sm font-semibold">
                                    @if($schedule->start_time)
                                        {{ \Illuminate\Support\Str::substr($schedule->start_time, 0, 5) }}
                                        @if($training->duration_minutes)
                                            - {{ \Carbon\Carbon::createFromFormat('H:i:s', $schedule->start_time)->addMinutes($training->duration_minutes)->format('H:i') }}
                                        @endif
                                    @endif
                                </span>
                            </div>
                        @endforeach
                    @elseif($timeRange)
                        <div class="flex items-center justify-between">
                            <span class="text-[#666666] text-sm">{{ __('training_detail.detail_time') }}</span>
                            <span class="text-white text-sm font-semibold">{{ $timeRange }}</span>
                        </div>
                    @endif
                    @if($placeName)
                        <div class="flex items-center justify-between">
                            <span class="text-[#666666] text-sm">{{ __('training_detail.detail_place') }}</span>
                            <span class="text-white text-sm font-semibold">{{ $placeName }}</span>
                        </div>
                    @endif
                    @if($training->city)
                        <div class="flex items-center justify-between">
                            <span class="text-[#666666] text-sm">{{ __('training_detail.detail_city') }}</span>
                            <span class="text-white text-sm font-semibold">{{ $training->city->getTranslation('name', $locale) ?: $training->city->getTranslation('name', 'sk') }}</span>
                        </div>
                    @endif
                    @if($training->pricing_type)
                        <div class="flex items-center justify-between">
                            <span class="text-[#666666] text-sm">{{ __('training_detail.detail_price') }}</span>
                            @if($training->pricing_type === \App\Enums\TrainingPricingTypeEnum::FREE)
                                <span class="text-emerald-500 text-sm font-semibold">{{ __('training_detail.pricing_free') }}</span>
                            @elseif($training->pricing_type === \App\Enums\TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED)
                                <span class="text-blue-400 text-sm font-semibold">{{ __('training_detail.pricing_membership') }}</span>
                            @elseif($training->pricing_type === \App\Enums\TrainingPricingTypeEnum::PAID && $training->price_amount)
                                <span class="text-white text-sm font-semibold">{{ number_format($training->price_amount, 2, ',', ' ') }} {{ $training->currency ?? '€' }}</span>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Capacity Section --}}
                @if($training->max_capacity)
                    <div class="h-px bg-[#222222]"></div>

                    <div class="flex flex-col gap-3">
                        <div class="flex items-center justify-between">
                            <span class="text-[#666666] text-sm">{{ __('training_detail.capacity_label') }}</span>
                            <span class="{{ $capacityColor }} text-sm font-semibold">
                                @if($remaining > 0)
                                    {{ $remaining }}/{{ $training->max_capacity }} {{ __('training_detail.capacity_spots') }}
                                @else
                                    {{ __('training_detail.capacity_full') }}
                                @endif
                            </span>
                        </div>
                        <div class="w-full h-2 bg-[#222222] rounded-full">
                            <div class="h-full {{ $barColor }} rounded-full transition-all" style="width: {{ $capacityPercent }}%"></div>
                        </div>
                        @if($remaining !== null && $remaining > 0 && $remaining <= 5)
                            <p class="{{ $capacityColor }} text-xs font-medium">
                                {{ trans_choice('training_detail.capacity_remaining', $remaining, ['count' => $remaining]) }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>
