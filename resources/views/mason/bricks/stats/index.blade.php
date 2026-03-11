@php
    $sectionTitle = brick_trans($title ?? []);
    $sectionDescription = brick_trans($description ?? []);
    $badgeText = brick_trans($badge ?? []) ?: null;
    $badgeColor = $badge_color ?? '#22C55E';
    $bgColor = $background_color ?? null;
@endphp

@if(! empty($items))
    <section class="py-[60px] px-5 md:px-10 lg:px-20" @if($bgColor) style="background-color: {{ $bgColor }};" @endif>
        @if($badgeText || $sectionTitle || $sectionDescription)
            <div class="flex flex-col items-center gap-4 mb-8 text-center">
                @if($badgeText)
                    <div class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-[12px] font-bold" style="background: {{ $badgeColor }}20; color: {{ $badgeColor }};">
                        <x-filament::icon icon="heroicon-o-check-badge" class="w-3.5 h-3.5" />
                        {{ $badgeText }}
                    </div>
                @endif
                @if($sectionTitle)
                    <h2 class="font-display font-bold text-[32px] tracking-wide">{{ $sectionTitle }}</h2>
                @endif
                @if($sectionDescription)
                    <p class="text-[#888888] text-[16px] leading-relaxed max-w-[700px]">{{ $sectionDescription }}</p>
                @endif
            </div>
        @endif

        <div class="flex flex-wrap justify-center gap-6">
            @foreach($items as $item)
                @php $color = $item['color'] ?? null; @endphp
                <div class="rounded-xl bg-[#111111] border border-[#222222] p-6 text-center w-[200px]">
                    @if(! empty($item['icon']))
                        <x-dynamic-component :component="$item['icon']" class="w-8 h-8 mx-auto mb-2 text-primary-600" />
                    @endif
                    @if(! empty($item['number']))
                        <div class="font-display font-bold text-[36px] tracking-wide" @if($color) style="color: {{ $color }};" @endif>
                            {{ $item['number'] }}
                        </div>
                    @endif
                    @if(! empty($item['label']))
                        <div class="text-[#888888] text-[14px] mt-2">{{ brick_trans($item['label']) }}</div>
                    @endif
                </div>
            @endforeach
        </div>
    </section>
@endif
