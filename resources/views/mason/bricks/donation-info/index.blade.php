@php
    $bankTitle = brick_trans($bank_title ?? []);
    $qrTitle = brick_trans($qr_title ?? []);
    $qrDescription = brick_trans($qr_description ?? []);
    $usageTitle = brick_trans($usage_title ?? []);
    $usageDescription = brick_trans($usage_description ?? []);
    $taxTitle = brick_trans($tax_title ?? []);
    $taxDescription = brick_trans($tax_description ?? []);
    $taxBtnText = brick_trans($tax_button_text ?? []);
    $taxLinkHref = brick_link(['link_type' => $tax_link_type ?? '', 'link_model_id' => $tax_link_model_id ?? '', 'link_url' => $tax_link_url ?? '']);
    $contactTitle = brick_trans($contact_title ?? []);
    $contactDescription = brick_trans($contact_description ?? []);
@endphp

<section class="py-[60px]">
    <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
        <div class="flex flex-col lg:flex-row gap-10">
            {{-- Left column --}}
            <div class="flex flex-col gap-10 flex-1 min-w-0">
                {{-- Bank details card --}}
                @if(! empty($bank_rows))
                    <div class="rounded-2xl bg-[#111111] border border-[#222222] p-8">
                        @if($bankTitle)
                            <div class="flex items-center gap-4 mb-6">
                                <div class="rounded-xl bg-[#FF2D2D20] w-12 h-12 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-bcz-red" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="20" height="14" x="2" y="5" rx="2"/><path d="M2 10h20"/>
                                    </svg>
                                </div>
                                <h3 class="font-display font-bold text-[24px] tracking-wide">{{ $bankTitle }}</h3>
                            </div>
                        @endif

                        <div class="flex flex-col">
                            @foreach($bank_rows as $index => $row)
                                <div class="flex justify-between py-4 {{ $loop->last ? '' : 'border-b border-[#222222]' }}">
                                    <span class="text-[#888888] text-[14px]">{{ $row['label'] ?? '' }}</span>
                                    <span class="text-white text-[14px] font-semibold">{{ $row['value'] ?? '' }}</span>
                                </div>
                            @endforeach
                        </div>

                        {{-- QR section --}}
                        @if($qrTitle || ! empty($iban_copy))
                            <div class="flex items-start gap-6 pt-6 mt-2 border-t border-[#222222]">
                                <div class="rounded-xl bg-white w-[140px] h-[140px] flex items-center justify-center shrink-0">
                                    <span class="text-[#888888] text-sm font-mono">QR</span>
                                </div>
                                <div class="flex flex-col gap-2">
                                    @if($qrTitle)
                                        <p class="text-white font-semibold text-[16px]">{{ $qrTitle }}</p>
                                    @endif
                                    @if($qrDescription)
                                        <p class="text-[#888888] text-[14px] leading-relaxed">{{ $qrDescription }}</p>
                                    @endif
                                    @if(! empty($iban_copy))
                                        <button
                                            x-data
                                            @click="navigator.clipboard.writeText('{{ $iban_copy }}')"
                                            class="flex items-center gap-2 rounded-lg bg-[#222222] px-4 py-2.5 text-white text-[13px] font-medium w-fit hover:bg-[#333333] transition mt-1"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect width="14" height="14" x="8" y="8" rx="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/>
                                            </svg>
                                            Kopírovať IBAN
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Usage card --}}
                @if(! empty($usage_items))
                    <div class="rounded-2xl bg-[#111111] border border-[#222222] p-8">
                        @if($usageTitle)
                            <div class="flex items-center gap-4 mb-4">
                                <div class="rounded-xl bg-[#22C55E20] w-12 h-12 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-[#22C55E]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                    </svg>
                                </div>
                                <h3 class="font-display font-bold text-[24px] tracking-wide">{{ $usageTitle }}</h3>
                            </div>
                        @endif
                        @if($usageDescription)
                            <p class="text-[#888888] text-[15px] leading-relaxed mb-6">{{ $usageDescription }}</p>
                        @endif

                        <div class="flex flex-col gap-4">
                            @foreach($usage_items as $item)
                                @php $itemColor = $item['color'] ?? '#FF2D2D'; @endphp
                                <div class="flex gap-4">
                                    <div class="rounded-lg w-8 h-8 flex items-center justify-center shrink-0" style="background: {{ $itemColor }}20;">
                                        @if(! empty($item['icon']))
                                            <x-filament::icon :icon="$item['icon']" class="w-4 h-4" style="color: {{ $itemColor }};" />
                                        @else
                                            <div class="w-2 h-2 rounded-full" style="background: {{ $itemColor }};"></div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-white font-semibold text-[16px]">{{ $item['title'] ?? '' }}</p>
                                        <p class="text-[#888888] text-[14px] leading-relaxed">{{ $item['description'] ?? '' }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Right column --}}
            <div class="flex flex-col gap-6 w-full lg:w-[400px] shrink-0">
                {{-- Tax card (2% z dane) --}}
                @if($taxTitle)
                    <div class="rounded-2xl bg-[#FF2D2D10] border border-[#FF2D2D40] p-8">
                        <div class="flex items-center gap-4 mb-5">
                            <div class="rounded-xl bg-bcz-red w-12 h-12 flex items-center justify-center">
                                <span class="text-white font-bold text-lg">2%</span>
                            </div>
                            <h3 class="text-white font-semibold text-[20px]">{{ $taxTitle }}</h3>
                        </div>
                        @if($taxDescription)
                            <p class="text-[#CCCCCC] text-[14px] leading-relaxed mb-5">{{ $taxDescription }}</p>
                        @endif
                        @if($taxLinkHref && $taxBtnText)
                            <a href="{{ $taxLinkHref }}" class="flex items-center justify-center gap-2 rounded-lg bg-bcz-red text-white font-semibold text-[14px] px-6 py-3.5 hover:bg-red-700 transition w-full">
                                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                                </svg>
                                {{ $taxBtnText }}
                            </a>
                        @endif
                    </div>
                @endif

                {{-- Contact card --}}
                @if($contactTitle)
                    <div class="rounded-2xl bg-[#111111] border border-[#222222] p-8">
                        <div class="flex items-center gap-4 mb-5">
                            <div class="rounded-xl bg-[#3B82F620] w-12 h-12 flex items-center justify-center">
                                <svg class="w-5 h-5 text-[#3B82F6]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-white font-semibold text-[20px]">{{ $contactTitle }}</h3>
                        </div>
                        @if($contactDescription)
                            <p class="text-[#888888] text-[14px] leading-relaxed mb-5">{{ $contactDescription }}</p>
                        @endif

                        <div class="flex flex-col gap-3">
                            @if(! empty($contact_email))
                                <div class="flex items-center gap-3">
                                    <svg class="w-[18px] h-[18px] text-[#888888] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                                    </svg>
                                    <span class="text-white text-[14px]">{{ $contact_email }}</span>
                                </div>
                            @endif
                            @if(! empty($contact_phone))
                                <div class="flex items-center gap-3">
                                    <svg class="w-[18px] h-[18px] text-[#888888] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                    </svg>
                                    <span class="text-white text-[14px]">{{ $contact_phone }}</span>
                                </div>
                            @endif
                            @if(! empty($contact_address))
                                <div class="flex items-center gap-3">
                                    <svg class="w-[18px] h-[18px] text-[#888888] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>
                                    </svg>
                                    <span class="text-white text-[14px]">{{ $contact_address }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
