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
 $scheduleDays = $training->schedules->map(function ($s) use ($dayNames, $training) {
 $day = $dayNames[$s->day] ?? ucfirst($s->day);
 $time = $s->start_time ? \Illuminate\Support\Str::substr($s->start_time, 0, 5) : '';
 if ($time && $training->duration_minutes) {
 $time .= ' - ' . \Carbon\Carbon::createFromFormat('H:i:s', $s->start_time)->addMinutes($training->duration_minutes)->format('H:i');
 }
 return trim("{$day} {$time}");
 })->join(', ');
 $timeRange = '';
 $coachName = $training->coaches->first()?->name;
 @endphp
 @php
 $placeName = $training->getTranslation('place_name', app()->getLocale()) ?: $training->getTranslation('place_name', 'sk');
 $cityName = $training->city?->getTranslation('name', app()->getLocale()) ?: $training->city?->getTranslation('name', 'sk');
 @endphp
 <div class="bg-[#0A0A0A] border border-[#222222] flex flex-col p-7 gap-5 w-full {{ $widthClass }}">
 {{-- Badges --}}
 <div class="flex items-center gap-2">
 @if($training->age_range)
 <span class="bg-bcz-red/20 text-bcz-red text-[10px] font-bold tracking-wider px-3 py-1.5">{{ $training->age_range }}</span>
 @endif
 @if($training->gender)
 <span class="bg-blue-500/20 text-blue-400 text-[10px] font-bold tracking-wider px-3 py-1.5">{{ $training->gender->getLabel() }}</span>
 @endif
 </div>

 {{-- Title --}}
 <h3 class="text-white text-lg font-bold">{{ $training->title }}</h3>

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
 @if($placeName)
 <div class="flex items-center justify-between">
 <span class="text-[#666666] text-sm">{{ __('bricks.latest_trainings.location') }}</span>
 <span class="text-white text-sm font-semibold text-right">{{ $placeName }}</span>
 </div>
 @endif
 @if($cityName)
 <div class="flex items-center justify-between">
 <span class="text-[#666666] text-sm">{{ __('bricks.latest_trainings.city') }}</span>
 <span class="text-white text-sm font-semibold text-right">{{ $cityName }}</span>
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
 <div class="h-full {{ $barColorClass }} rounded-full"style="width: {{ $capacityPercent }}%"></div>
 </div>
 </div>
 @endif

 {{-- CTA Button --}}
 @php
 $userRegistration = auth()->check()
 ? $training->registrations->where('user_id', auth()->id())->whereNotIn('status', [\App\Enums\RegistrationStatusEnum::Cancelled->value])->first()
 : null;
 @endphp
 @if($userRegistration)
 @php
 $regIsPending = $userRegistration->status === \App\Enums\RegistrationStatusEnum::Pending;
 $regIsApproved = $userRegistration->status === \App\Enums\RegistrationStatusEnum::Approved;
 $brickIsMembershipRequired = $training->pricing_type === \App\Enums\TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED;
 $brickIsPaid = $training->pricing_type === \App\Enums\TrainingPricingTypeEnum::PAID;
 $regNeedsPayment = ($regIsPending && ($brickIsMembershipRequired || $brickIsPaid))
 || ($regIsApproved && $brickIsMembershipRequired && ! auth()->user()?->hasActiveMembershipForTeam($training->team_id))
 || ($regIsApproved && $brickIsPaid && $training->price_amount > 0 && $userRegistration->payments->where('status', \App\Enums\PaymentStatusEnum::COMPLETED)->isEmpty());
 $regNeedsApproval = $regIsPending && ! $brickIsMembershipRequired && ! $brickIsPaid;
 @endphp
 @if($regNeedsApproval)
 <a href="{{ route('team.training.show', [$training->team, $training]) }}"class="flex items-center justify-center gap-2 bg-amber-500/10 border border-amber-500/30 text-amber-500 text-xs font-bold tracking-wider px-6 py-3.5 hover:bg-amber-500/20 transition">
 <svg class="w-4 h-4"fill="none"stroke="currentColor"viewBox="0 0 24 24"stroke-width="2"stroke-linecap="round"stroke-linejoin="round"><circle cx="12"cy="12"r="10"/><polyline points="12 6 12 12 16 14"/></svg>
 {{ __('archive.pending_approval') }}
 </a>
 @elseif($regNeedsPayment)
 <a href="{{ route('team.training.show', [$training->team, $training]) }}"class="flex items-center justify-center gap-2 bg-amber-500/10 border border-amber-500/30 text-amber-500 text-xs font-bold tracking-wider px-6 py-3.5 hover:bg-amber-500/20 transition">
 <svg class="w-4 h-4"fill="none"stroke="currentColor"viewBox="0 0 24 24"stroke-width="2"stroke-linecap="round"stroke-linejoin="round"><rect x="1"y="4"width="22"height="16"rx="2"ry="2"/><line x1="1"y1="10"x2="23"y2="10"/></svg>
 {{ __('archive.pending_payment') }}
 </a>
 @else
 <a href="{{ route('team.training.show', [$training->team, $training]) }}"class="flex items-center justify-center gap-2 bg-emerald-500/10 border border-emerald-500/30 text-emerald-500 text-xs font-bold tracking-wider px-6 py-3.5 hover:bg-emerald-500/20 transition">
 <svg class="w-4 h-4"fill="none"stroke="currentColor"viewBox="0 0 24 24"stroke-width="2"stroke-linecap="round"stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
 {{ __('archive.registered') }}
 </a>
 @endif
 @elseif($remaining > 0 || ! $training->max_capacity)
 <a href="{{ route('team.training.show', [$training->team, $training]) }}"class="flex items-center justify-center bg-bcz-red text-white text-xs font-bold tracking-wider px-6 py-3.5 hover:bg-red-700 transition">
 {{ __('bricks.latest_trainings.sign_up') }}
 </a>
 @else
 <a href="{{ route('team.training.show', [$training->team, $training]) }}"class="flex items-center justify-center bg-[#222222] text-[#888888] text-xs font-bold tracking-wider px-6 py-3.5 hover:bg-[#333333] transition">
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
 <a href="{{ $ctaHref }}"class="inline-flex items-center gap-2 bg-bcz-red text-white font-semibold text-[15px] px-8 py-4 hover:bg-red-700 transition">
 {{ $ctaText }}
 </a>
 </div>
 @endif
 </div>
</section>
@endif
