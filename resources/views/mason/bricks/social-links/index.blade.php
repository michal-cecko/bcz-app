<section class="bg-[#111111] py-20">
    <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col items-center gap-12">
        {{-- Header --}}
        @if(! empty($label))
            <div class="flex items-center gap-3">
                <div class="w-8 h-[3px] bg-bcz-red"></div>
                <span class="text-bcz-red text-sm font-semibold tracking-[3px]">{{ brick_trans($label) }}</span>
            </div>
        @endif

        @if(! empty($title))
            <h2 class="font-display font-bold text-[32px] md:text-[48px] lg:text-[64px] tracking-wide text-center">{{ brick_trans($title) }}</h2>
        @endif

        @if(! empty($description))
            <p class="text-[#888888] text-lg leading-[1.6] text-center max-w-[700px]">{{ brick_trans($description) }}</p>
        @endif

        {{-- Social Cards --}}
        @if(! empty($socials))
            <div class="flex flex-wrap gap-4 lg:gap-6">
                @foreach($socials as $i => $social)
                    @php
                        $isFirst = $loop->first;
                        $borderClass = $isFirst ? 'border-bcz-red/25 hover:border-bcz-red/50' : 'border-[#333333] hover:border-[#555555]';
                    @endphp
                    <a href="{{ $social['url'] ?? '#' }}" target="_blank" class="flex flex-col items-center gap-3.5 bg-[#151515] rounded-2xl border {{ $borderClass }} px-10 py-7 w-full sm:w-[200px] transition-colors">
                        {{-- Platform Icon --}}
                        @switch($social['platform'] ?? '')
                            @case('website')
                                <svg class="w-9 h-9 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                                @break
                            @case('instagram')
                                <svg class="w-9 h-9 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
                                @break
                            @case('youtube')
                                <svg class="w-9 h-9 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.5 17a24.12 24.12 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49.56 49.56 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24.12 24.12 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49.55 49.55 0 0 1-16.2 0A2 2 0 0 1 2.5 17"/><path d="m10 15 5-3-5-3z"/></svg>
                                @break
                            @case('tiktok')
                                <svg class="w-9 h-9 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"/></svg>
                                @break
                            @case('facebook')
                                <svg class="w-9 h-9 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                                @break
                        @endswitch

                        @if(! empty($social['name']))
                            <span class="text-center text-white text-base font-bold">{{ brick_trans($social['name']) }}</span>
                        @endif

                        @if(! empty($social['handle']))
                            <span class="text-center text-bcz-red text-sm font-semibold">{{ brick_trans($social['handle']) }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Divider --}}
        <div class="w-[120px] h-px bg-[#333333]"></div>

        {{-- Contact --}}
        <div class="flex flex-col md:flex-row items-center gap-8 md:gap-16">
            @if(! empty($email))
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                    <span class="text-[#AAAAAA] text-base font-medium">{{ brick_trans($email) }}</span>
                </div>
            @endif

            @if(! empty($phone))
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    <span class="text-[#AAAAAA] text-base font-medium">{{ brick_trans($phone) }}</span>
                </div>
            @endif
        </div>
    </div>
</section>
