{{-- Tailwind safelist: text-red-500 text-orange-400 text-emerald-500 bg-red-500 bg-orange-400 bg-emerald-500 --}}
@php
    $dayNames = [
        'monday' => __('bricks.latest_trainings.days.monday'),
        'tuesday' => __('bricks.latest_trainings.days.tuesday'),
        'wednesday' => __('bricks.latest_trainings.days.wednesday'),
        'thursday' => __('bricks.latest_trainings.days.thursday'),
        'friday' => __('bricks.latest_trainings.days.friday'),
        'saturday' => __('bricks.latest_trainings.days.saturday'),
        'sunday' => __('bricks.latest_trainings.days.sunday'),
    ];
@endphp

@if(! empty($label) || ! empty($title) || ! empty($subtitle) || $trainings->isNotEmpty())
<section class="bg-[#111111] py-[100px]">
    <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col gap-[60px]">
        @if(! empty($label) || ! empty($title) || ! empty($subtitle))
            <div class="flex flex-col items-center gap-4">
                @if(! empty($label))
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                        <span class="text-bcz-red text-xs font-bold tracking-widest">{{ brick_trans($label) }}</span>
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                    </div>
                @endif
                @if(! empty($title))
                    <h2 class="font-display font-bold text-5xl tracking-wide">{{ brick_trans($title) }}</h2>
                @endif
                @if(! empty($subtitle))
                    <p class="text-[#666666] text-lg text-center">{{ brick_trans($subtitle) }}</p>
                @endif
            </div>
        @endif

        @if($trainings->isNotEmpty())
            @php
                $count = $trainings->count();
                $widthClass = match(true) {
                    $count === 1 => 'md:w-[50%]',
                    $count === 2 => 'md:w-[calc(50%-12px)]',
                    $count === 3 => 'md:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)]',
                    default => 'md:w-[calc(50%-12px)] lg:w-[calc(25%-18px)]',
                };
            @endphp
            <div class="flex flex-wrap justify-center gap-6">
                @foreach($trainings as $training)
                    @php
                        $registeredCount = $training->registrations->count();
                        $capacityPercent = $training->max_capacity ? min(100, round(($registeredCount / $training->max_capacity) * 100)) : 0;
                        $scheduleDays = collect($training->schedule_days ?? [])->map(fn ($d) => $dayNames[$d] ?? ucfirst($d))->join(', ');
                        $timeRange = '';
                        if ($training->start_time) {
                            $timeRange = \Illuminate\Support\Str::substr($training->start_time, 0, 5);
                            if ($training->duration_minutes) {
                                $timeRange .= ' - ' . \Carbon\Carbon::createFromFormat('H:i:s', $training->start_time)->addMinutes($training->duration_minutes)->format('H:i');
                            }
                        }
                        $coachName = $training->coaches->first()?->name;
                    @endphp
                    <div class="bg-[#0A0A0A] border border-[#222222] flex flex-col p-7 gap-5 w-full {{ $widthClass }}">
                        {{-- Header: Title + Age Badge --}}
                        <div class="flex items-center justify-between gap-2">
                            <h3 class="text-white text-lg font-bold">{{ $training->title }}</h3>
                            @if($training->age_group)
                                <span class="bg-bcz-red/20 text-bcz-red text-[10px] font-bold tracking-wider px-3 py-1.5 shrink-0">{{ $training->age_group }}</span>
                            @endif
                        </div>

                        {{-- Info rows --}}
                        <div class="flex flex-col gap-3">
                            @if($scheduleDays)
                                <div class="flex items-center justify-between">
                                    <span class="text-[#666666] text-sm">{{ __('bricks.latest_trainings.day') }}</span>
                                    <span class="text-white text-sm font-semibold">{{ $scheduleDays }}</span>
                                </div>
                            @endif
                            @if($timeRange)
                                <div class="flex items-center justify-between">
                                    <span class="text-[#666666] text-sm">{{ __('bricks.latest_trainings.time') }}</span>
                                    <span class="text-white text-sm font-semibold">{{ $timeRange }}</span>
                                </div>
                            @endif
                            @if($coachName)
                                <div class="flex items-center justify-between">
                                    <span class="text-[#666666] text-sm">{{ __('bricks.latest_trainings.coach') }}</span>
                                    <span class="text-white text-sm font-semibold">{{ $coachName }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Divider --}}
                        <div class="h-px bg-[#222222]"></div>

                        {{-- Capacity --}}
                        @if($training->max_capacity)
                            @php
                                $remaining = max(0, $training->max_capacity - $registeredCount);
                                $capacityColorClass = match(true) {
                                    $capacityPercent >= 90 => 'text-red-500',
                                    $capacityPercent >= 65 => 'text-orange-400',
                                    default => 'text-emerald-500',
                                };
                                $barColorClass = match(true) {
                                    $capacityPercent >= 90 => 'bg-red-500',
                                    $capacityPercent >= 65 => 'bg-orange-400',
                                    default => 'bg-emerald-500',
                                };
                            @endphp
                            <div class="flex flex-col gap-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-[#666666] text-[13px]">{{ __('bricks.latest_trainings.capacity') }}</span>
                                    <span class="{{ $capacityColorClass }} text-[13px] font-semibold">{{ $remaining > 0 ? $remaining . '/' . $training->max_capacity . ' ' . __('bricks.latest_trainings.spots') : __('archive.full') }}</span>
                                </div>
                                <div class="w-full h-1.5 bg-[#222222] rounded-full">
                                    <div class="h-full {{ $barColorClass }} rounded-full" style="width: {{ $capacityPercent }}%"></div>
                                </div>
                            </div>
                        @endif

                        {{-- CTA Button --}}
                        @if($remaining > 0 || ! $training->max_capacity)
                            <a href="{{ route('team.training.show', [$training->team, $training]) }}" class="flex items-center justify-center bg-bcz-red text-white text-xs font-bold tracking-wider px-6 py-3.5 hover:bg-red-700 transition">
                                {{ __('bricks.latest_trainings.sign_up') }}
                            </a>
                        @else
                            <a href="{{ route('team.training.show', [$training->team, $training]) }}" class="flex items-center justify-center bg-[#222222] text-[#888888] text-xs font-bold tracking-wider px-6 py-3.5 hover:bg-[#333333] transition">
                                Detail
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        @php
            $ctaHref = brick_link(['link_type' => $cta_link_type ?? '', 'link_model_id' => $cta_link_model_id ?? '', 'link_url' => $cta_link_url ?? '']);
            $ctaText = brick_trans($cta_text ?? []);
        @endphp
        @if($ctaText && $ctaHref)
            <div class="flex justify-center">
                <a href="{{ $ctaHref }}" class="inline-flex items-center gap-2 rounded-lg bg-bcz-red text-white font-semibold text-[15px] px-8 py-4 hover:bg-red-700 transition">
                    {{ $ctaText }}
                </a>
            </div>
        @endif
    </div>
</section>
@endif
