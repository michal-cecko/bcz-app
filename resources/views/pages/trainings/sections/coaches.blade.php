    @if($training->coaches->isNotEmpty())
        <section class="bg-[#111111] py-20">
            <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col gap-12">
                {{-- Header --}}
                <div class="flex flex-col items-center gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                        <span class="text-bcz-red text-xs font-bold tracking-[3px]">{{ __('training_detail.coach_label') }}</span>
                        <div class="w-10 h-0.5 bg-bcz-red"></div>
                    </div>
                    <h2 class="font-display font-bold text-4xl tracking-wide">{{ __('training_detail.coach_title') }}</h2>
                </div>

                {{-- Coach Cards --}}
                @php $coachCount = $training->coaches->count(); @endphp
                <div class="grid grid-cols-1 {{ $coachCount >= 2 ? 'lg:grid-cols-2' : '' }} gap-6 {{ $coachCount === 1 ? 'mx-auto' : '' }}" @if($coachCount === 1) style="max-width: 680px" @endif>
                    @foreach($training->coaches as $coach)
                        @php
                            $roleEnum = \App\Enums\CoachRoleEnum::tryFrom($coach->pivot->role);
                            $isMain = $coach->pivot->role === 'main';
                        @endphp
                        <div class="bg-[#0A0A0A] border border-[#222222] rounded-2xl flex flex-col">
                            {{-- Coach Image with Badge --}}
                            <div class="relative w-full h-[250px] shrink-0 overflow-hidden rounded-t-2xl">
                                @if($coach->coachProfile?->getFirstMediaUrl('biography_image'))
                                    <img src="{{ $coach->coachProfile->getFirstMediaUrl('biography_image') }}" alt="{{ $coach->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-[#1A1A1A] flex items-center justify-center">
                                        <span class="text-bcz-red font-display font-bold text-6xl">{{ mb_substr($coach->name, 0, 2) }}</span>
                                    </div>
                                @endif
                                {{-- Role Badge --}}
                                @if($roleEnum)
                                    <div class="absolute top-4 left-4 {{ $isMain ? 'bg-bcz-red' : 'bg-[#333333]' }} rounded-md px-4 py-2">
                                        <span class="text-white text-[10px] font-bold tracking-[2px]">{{ mb_strtoupper($roleEnum->getLabel()) }}</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Coach Info --}}
                            <div class="flex flex-col gap-5 px-7 pb-[36px] pt-6 flex-1">
                                <div class="flex flex-col gap-1">
                                    <h3 class="font-display font-bold text-[28px] tracking-[0.5px]">{{ mb_strtoupper($coach->name) }}</h3>
                                    @if($roleEnum)
                                        <span class="text-bcz-red text-xs font-medium tracking-[1px]">{{ $roleEnum->getLabel() }}</span>
                                    @endif
                                </div>

                                @if($coach->coachProfile?->getTranslation('biography', $locale))
                                    <div class="text-[#888888] text-[15px] leading-[1.7] space-y-4">{!! $coach->coachProfile->getTranslation('biography', $locale) !!}</div>
                                @endif

                                {{-- Certifications --}}
                                @if($coach->certifications->isNotEmpty())
                                    <div class="flex flex-wrap gap-3">
                                        @foreach($coach->certifications as $cert)
                                            <div class="bg-[#222222] px-3.5 py-2 rounded">
                                                <span class="text-[#AAAAAA] text-[11px] font-medium">{{ $cert->getTranslation('name', $locale) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- CTA --}}
                                <a href="{{ route('coach.show', $coach) }}" class="flex items-center gap-2 text-bcz-red text-xs font-bold tracking-wider hover:text-red-400 transition-colors group/cta">
                                    {{ __('coach_detail.view_profile') }}
                                    <svg class="w-4 h-4 group-hover/cta:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
