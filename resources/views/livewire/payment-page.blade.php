<div class="max-w-[960px] mx-auto">
    @if($isCompleted)
        {{-- Already paid state --}}
        <div class="bg-[#111111] border border-[#222222] rounded-2xl p-8 md:p-12 flex flex-col items-center gap-6 text-center">
            <div class="w-16 h-16 rounded-full bg-[#22C55E]/10 flex items-center justify-center">
                <svg class="w-8 h-8 text-[#22C55E]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path d="M20 6 9 17l-5-5"/>
                </svg>
            </div>
            <h1 class="font-display text-3xl md:text-4xl font-bold text-white">
                @if($payment->status === \App\Enums\PaymentStatusEnum::COMPLETED)
                    Platba bola uhradená
                @else
                    Platba bola vrátená
                @endif
            </h1>
            <p class="text-[#888888] text-sm max-w-md">
                @if($payment->status === \App\Enums\PaymentStatusEnum::COMPLETED)
                    Táto platba už bola úspešne spracovaná. Potvrdenie ti bolo odoslané na email.
                @else
                    Táto platba bola vrátená. Ak máš otázky, kontaktuj svoj tím.
                @endif
            </p>
            <div class="bg-[#0A0A0A] border border-[#222222] rounded-xl p-5 w-full max-w-sm">
                <div class="flex justify-between items-center">
                    <span class="text-[#888888] text-sm">Suma</span>
                    <span class="text-[#22C55E] text-xl font-bold">{{ $this->formattedAmount }}</span>
                </div>
            </div>
        </div>
    @else
        {{-- Payment form --}}
        <div class="bg-[#111111] border border-[#222222] rounded-2xl overflow-hidden">
            <div class="flex flex-col lg:flex-row">
                {{-- Left panel --}}
                <div class="lg:w-1/2 p-6 md:p-8 flex flex-col gap-6 lg:border-r border-[#222222]">
                    {{-- Badge --}}
                    <div class="inline-flex items-center gap-2 bg-[#FF2D2D]/10 rounded-full px-4 py-2 self-start">
                        <svg class="w-3.5 h-3.5 text-[#FF2D2D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <rect x="2" y="4" width="20" height="16" rx="2"/>
                            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                        </svg>
                        <span class="text-[#FF2D2D] text-xs font-semibold">Platba cez odkaz</span>
                    </div>

                    {{-- Title --}}
                    <h1 class="font-display text-[28px] md:text-[32px] font-bold text-white leading-tight">
                        {{ $this->title }}
                    </h1>

                    {{-- Description --}}
                    <p class="text-[#888888] text-sm leading-relaxed">
                        Vyber spôsob platby a uhraď sumu.
                    </p>

                    {{-- Divider --}}
                    <div class="w-full h-px bg-[#222222]"></div>

                    {{-- Summary box --}}
                    <div class="bg-[#0A0A0A] border border-[#222222] rounded-xl p-5 flex flex-col gap-3">
                        <span class="text-[#666666] text-xs font-semibold tracking-wider uppercase">Súhrn platby</span>

                        <div class="flex justify-between items-center py-1.5">
                            <span class="text-[#888888] text-sm">Typ</span>
                            <span class="text-white text-sm font-medium">{{ $this->payableTypeLabel }}</span>
                        </div>

                        @if($this->payableName)
                            <div class="flex justify-between items-center py-1.5">
                                <span class="text-[#888888] text-sm">Názov</span>
                                <span class="text-white text-sm font-medium text-right">{{ $this->payableName }}</span>
                            </div>
                        @endif

                        @if($payment->payable_type === 'membership' && $payment->payable?->season)
                            <div class="flex justify-between items-center py-1.5">
                                <span class="text-[#888888] text-sm">Sezóna</span>
                                <span class="text-white text-sm font-medium">{{ $payment->payable->season->name }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between items-center py-1.5">
                            <span class="text-[#888888] text-sm">Meno</span>
                            <span class="text-white text-sm font-medium">{{ $payment->user?->first_name }} {{ $payment->user?->last_name }}</span>
                        </div>

                        @if($payment->payer_email)
                            <div class="flex justify-between items-center py-1.5">
                                <span class="text-[#888888] text-sm">E-mail</span>
                                <span class="text-white text-sm font-medium">{{ $payment->payer_email }}</span>
                            </div>
                        @endif

                        {{-- Divider --}}
                        <div class="w-full h-px bg-[#222222]"></div>

                        {{-- Total --}}
                        <div class="flex justify-between items-center pt-1">
                            <span class="text-white text-sm font-medium">Celkom</span>
                            <span class="text-[#FF2D2D] text-[22px] font-bold">{{ $this->formattedAmount }}</span>
                        </div>
                    </div>
                </div>

                {{-- Right panel --}}
                @php
                    $paymentMethodModels = $this->resolvedMethods;
                @endphp
                <div class="lg:w-1/2 p-6 md:p-8 flex flex-col gap-5">
                    <h2 class="text-white text-base font-semibold">Vyber spôsob platby</h2>

                    {{-- Payment method cards --}}
                    <div class="flex flex-col gap-3">
                        @if(in_array('gopay', $this->enabledMethods))
                            <button wire:click="selectMethod('gopay')" class="w-full flex items-center gap-4 px-5 py-4 bg-[#0A0A0A] transition cursor-pointer {{ $selectedMethod === 'gopay' ? 'border-2 border-[#FF2D2D]' : 'border border-[#333333] hover:border-[#555555]' }}">
                                <svg class="w-[22px] h-[22px] shrink-0 {{ $selectedMethod === 'gopay' ? 'text-[#FF2D2D]' : 'text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                                    <line x1="1" y1="10" x2="23" y2="10"/>
                                </svg>
                                <div class="flex flex-col items-start gap-0.5 flex-1">
                                    <span class="text-white text-sm font-semibold">{{ $paymentMethodModels->get('gopay')?->title ?? 'Platba kartou' }}</span>
                                    <span class="text-[#666666] text-xs text-left">{!! strip_tags($paymentMethodModels->get('gopay')?->description ?? '', '<b><i><a>') !!}</span>
                                </div>
                                <div class="w-5 h-5 rounded-full flex items-center justify-center {{ $selectedMethod === 'gopay' ? 'border-2 border-[#FF2D2D]' : 'border-2 border-[#333333]' }}">
                                    @if($selectedMethod === 'gopay')
                                        <div class="w-2.5 h-2.5 rounded-full bg-[#FF2D2D]"></div>
                                    @endif
                                </div>
                            </button>
                        @endif

                        @if(in_array('bank_transfer', $this->enabledMethods))
                            <button wire:click="selectMethod('bank_transfer')" class="w-full flex items-center gap-4 px-5 py-4 bg-[#0A0A0A] transition cursor-pointer {{ $selectedMethod === 'bank_transfer' ? 'border-2 border-[#FF2D2D]' : 'border border-[#333333] hover:border-[#555555]' }}">
                                <svg class="w-[22px] h-[22px] shrink-0 {{ $selectedMethod === 'bank_transfer' ? 'text-[#FF2D2D]' : 'text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11M20 10v11M8 14v3M12 14v3M16 14v3"/>
                                </svg>
                                <div class="flex flex-col items-start gap-0.5 flex-1">
                                    <span class="text-white text-sm font-semibold">{{ $paymentMethodModels->get('bank_transfer')?->title ?? 'Bankovy prevod' }}</span>
                                    <span class="text-[#666666] text-xs text-left">{!! strip_tags($paymentMethodModels->get('bank_transfer')?->description ?? '', '<b><i><a>') !!}</span>
                                </div>
                                <div class="w-5 h-5 rounded-full flex items-center justify-center {{ $selectedMethod === 'bank_transfer' ? 'border-2 border-[#FF2D2D]' : 'border-2 border-[#333333]' }}">
                                    @if($selectedMethod === 'bank_transfer')
                                        <div class="w-2.5 h-2.5 rounded-full bg-[#FF2D2D]"></div>
                                    @endif
                                </div>
                            </button>
                        @endif

                        @if(in_array('cash', $this->enabledMethods))
                            <button wire:click="selectMethod('cash')" class="w-full flex items-center gap-4 px-5 py-4 bg-[#0A0A0A] transition cursor-pointer {{ $selectedMethod === 'cash' ? 'border-2 border-[#FF2D2D]' : 'border border-[#333333] hover:border-[#555555]' }}">
                                <svg class="w-[22px] h-[22px] shrink-0 {{ $selectedMethod === 'cash' ? 'text-[#FF2D2D]' : 'text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <rect x="2" y="6" width="20" height="12" rx="1"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <div class="flex flex-col items-start gap-0.5 flex-1">
                                    <span class="text-white text-sm font-semibold">{{ $paymentMethodModels->get('cash')?->title ?? 'Hotovost' }}</span>
                                    <span class="text-[#666666] text-xs text-left">{!! strip_tags($paymentMethodModels->get('cash')?->description ?? '', '<b><i><a>') !!}</span>
                                </div>
                                <div class="w-5 h-5 rounded-full flex items-center justify-center {{ $selectedMethod === 'cash' ? 'border-2 border-[#FF2D2D]' : 'border-2 border-[#333333]' }}">
                                    @if($selectedMethod === 'cash')
                                        <div class="w-2.5 h-2.5 rounded-full bg-[#FF2D2D]"></div>
                                    @endif
                                </div>
                            </button>
                        @endif
                    </div>

                    {{-- Bank transfer details (inline) --}}
                    @if($selectedMethod === 'bank_transfer')
                        <div class="w-full rounded-xl bg-[#0A0A0A] border border-[#FF2D2D]/20 p-5 flex flex-col gap-5">
                            <div class="flex flex-col sm:flex-row gap-5">
                                <div class="flex flex-col gap-2.5 flex-1">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-[#FF2D2D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                            <path d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11M20 10v11M8 14v3M12 14v3M16 14v3"/>
                                        </svg>
                                        <span class="text-white text-[13px] font-semibold">{{ __('payments.bank_transfer.details_title') }}</span>
                                    </div>

                                    @if($payment->team?->bank_account_iban)
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[#666666] text-[11px] font-medium">{{ __('payments.bank_transfer.iban') }}</span>
                                            <span class="text-white text-xs font-bold tracking-wide">{{ $payment->team->bank_account_iban }}</span>
                                        </div>
                                    @endif

                                    @if($payment->variable_symbol)
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[#666666] text-[11px] font-medium">{{ __('payments.bank_transfer.variable_symbol') }}</span>
                                            <span class="text-[#FF2D2D] text-xs font-bold">{{ $payment->variable_symbol }}</span>
                                        </div>
                                    @endif

                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[#666666] text-[11px] font-medium">{{ __('payments.bank_transfer.amount') }}</span>
                                        <span class="text-white text-xs font-bold">{{ $this->formattedAmount }}</span>
                                    </div>

                                    @if($payment->team?->bank_account_name)
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[#666666] text-[11px] font-medium">{{ __('payments.bank_transfer.recipient') }}</span>
                                            <span class="text-[#AAAAAA] text-xs font-semibold">{{ $payment->team->bank_account_name }}</span>
                                        </div>
                                    @endif

                                    @php $bankNote = $payment->payable?->getQrPaymentNote(); @endphp
                                    @if($bankNote)
                                        <div class="flex items-start gap-1.5">
                                            <span class="text-[#666666] text-[11px] font-medium shrink-0">{{ __('payments.bank_transfer.note') }}</span>
                                            <span class="text-[#AAAAAA] text-xs font-semibold">{{ $bankNote }}</span>
                                        </div>
                                    @endif
                                </div>

                                @if($qrCodeImage)
                                    <div class="flex flex-col items-center gap-1.5">
                                        <div class="w-[100px] h-[100px] rounded-lg bg-white flex items-center justify-center p-1.5">
                                            <img src="data:image/png;base64,{{ $qrCodeImage }}" alt="QR" class="w-full h-full">
                                        </div>
                                        <span class="text-[#666666] text-[10px] font-medium">{{ __('payments.bank_transfer.scan_qr') }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="rounded-lg bg-[#FF2D2D]/[0.03] border border-[#FF2D2D]/[0.12] p-2.5 flex flex-col gap-1.5 w-full">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3 h-3 text-[#FF2D2D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <line x1="12" y1="16" x2="12" y2="12"/>
                                        <line x1="12" y1="8" x2="12.01" y2="8"/>
                                    </svg>
                                    <span class="text-[#FF2D2D] text-[11px] font-semibold">{{ __('payments.bank_transfer.instructions_title') }}</span>
                                </div>
                                @php
                                    $btInstructions = $paymentMethodModels->get('bank_transfer')?->instructions;
                                    $hasBtInstructions = $btInstructions && trim(strip_tags($btInstructions)) !== '';
                                @endphp
                                @if($hasBtInstructions)
                                    <div class="text-left text-[#888888] text-[10px] prose-sm">{!! $btInstructions !!}</div>
                                @else
                                    <p class="text-left text-[#888888] text-[10px]">{{ __('payments.bank_transfer.instruction_use_vs') }}</p>
                                    <p class="text-left text-[#888888] text-[10px]">{{ __('payments.bank_transfer.instruction_processing') }}</p>
                                    <p class="text-left text-[#888888] text-[10px]">{{ __('payments.bank_transfer.instruction_confirmation') }}</p>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Cash instructions (inline) --}}
                    @if($selectedMethod === 'cash')
                        <div class="rounded-xl bg-[#0A0A0A] border border-[#FF2D2D]/20 p-5 flex flex-col gap-4 w-full">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#FF2D2D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <rect x="2" y="6" width="20" height="12" rx="1"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <span class="text-white text-[13px] font-semibold">{{ __('payments.cash.instructions_title') }}</span>
                            </div>

                            <div class="flex items-center gap-2.5 rounded-lg bg-[#FF2D2D]/[0.06] px-4 py-3 w-full">
                                <span class="text-[#888888] text-xs font-medium">{{ __('payments.bank_transfer.amount_to_pay') }}</span>
                                <span class="text-[#FF2D2D] text-base font-bold">{{ $this->formattedAmount }}</span>
                            </div>

                            @if($paymentMethodModels->get('cash')?->instructions)
                                <div class="text-left text-[#AAAAAA] text-xs font-medium prose-sm">{!! $paymentMethodModels->get('cash')->instructions !!}</div>
                            @else
                                <div class="flex flex-col gap-2.5">
                                    @foreach([
                                        'Kontaktuj svoj tím pre dohodnutie termínu.',
                                        'Priprav si presnú sumu v hotovosti.',
                                        'Po zaplatení dostaneš potvrdenie.',
                                    ] as $index => $stepText)
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-[22px] h-[22px] rounded-full bg-[#FF2D2D]/[0.12] flex items-center justify-center shrink-0">
                                                <span class="text-[#FF2D2D] text-[10px] font-bold">{{ $index + 1 }}</span>
                                            </div>
                                            <span class="text-[#AAAAAA] text-xs font-medium">{{ $stepText }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if($payment->team?->contact_phone || $payment->team?->contact_email)
                                <div class="w-full h-px bg-[#222222]"></div>
                                <div class="flex items-center gap-3 w-full">
                                    <svg class="w-4 h-4 text-[#FF2D2D] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                    <div class="flex flex-col gap-0.5 flex-1">
                                        <span class="text-[#AAAAAA] text-[11px] font-semibold">{{ $payment->team->getTranslation('name', 'sk') }}</span>
                                        <span class="text-[#666666] text-[10px]">
                                            @if($payment->team->contact_phone){{ $payment->team->contact_phone }}@endif
                                            @if($payment->team->contact_phone && $payment->team->contact_email) &middot; @endif
                                            @if($payment->team->contact_email){{ $payment->team->contact_email }}@endif
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Error message --}}
                    @if($errorMessage)
                        <div class="rounded-lg bg-[#FF2D2D]/10 border border-[#FF2D2D]/20 px-4 py-3">
                            <span class="text-[#FF2D2D] text-sm">{{ $errorMessage }}</span>
                        </div>
                    @endif

                    {{-- Divider --}}
                    <div class="w-full h-px bg-[#222222]"></div>

                    {{-- Pay button (only for GoPay) --}}
                    @if($selectedMethod === 'gopay')
                        <button
                            wire:click="pay"
                            wire:loading.attr="disabled"
                            class="w-full h-[50px] bg-[#FF2D2D] hover:bg-red-700 transition flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="pay" class="text-white text-sm font-bold">Zaplatiť {{ $this->formattedAmount }}</span>
                            <span wire:loading wire:target="pay" class="text-white text-sm font-bold">Spracovávam...</span>
                        </button>
                    @endif

                    {{-- Footer note --}}
                    @if($selectedMethod === 'gopay')
                    <div class="flex items-center justify-center gap-1.5">
                        <svg class="w-3 h-3 text-[#22C55E]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        <span class="text-[#666666] text-[10px]">Platba spracovaná cez GoPay &middot; SSL šifrované</span>
                    </div>
                    @endif

                    {{-- Bottom note --}}
                    <p class="text-[#555555] text-xs text-center leading-relaxed">
                        Po úspešnej platbe ti príde potvrdzujúci email. Členstvo/registrácia bude aktivovaná okamžite.
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
