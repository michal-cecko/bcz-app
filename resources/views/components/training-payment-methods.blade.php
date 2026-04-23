@props([
    'enabledMethods' => [],
    'selectedPaymentMethod' => null,
    'feeLabel' => '',
    'feeAmount' => 0,
    'feeCurrency' => 'EUR',
    'team' => null,
    'season' => null,
    'payable' => null,
    'variableSymbol' => null,
    'paymentNote' => null,
    'context' => 'membership',
])

@php
    $isRegistration = $context === 'registration';

    $sourceMethods = null;
    if ($payable && method_exists($payable, 'effectivePaymentMethods')) {
        $sourceMethods = $payable->effectivePaymentMethods();
    }
    if ($sourceMethods === null || $sourceMethods->isEmpty()) {
        $sourceMethods = $team?->enabledPaymentMethods ?? collect();
    }

    $paymentMethodModels = $sourceMethods->keyBy(
        fn ($m) => $m->method instanceof \App\Enums\PaymentMethodEnum ? $m->method->value : $m->method,
    );

    if ($paymentMethodModels->isNotEmpty()) {
        $enabledMethods = $paymentMethodModels->keys()->toArray();
    }
@endphp

{{-- Payment method cards --}}
<div class="w-full flex flex-col gap-3">
    @if(in_array('gopay', $enabledMethods))
        <button wire:click="selectPaymentMethod('gopay')" class="w-full flex items-center gap-4 px-6 py-5 bg-[#0A0A0A] transition {{ $selectedPaymentMethod === 'gopay' ? 'border-2 border-[#FF2D2D]' : 'border border-[#333333] hover:border-[#555555]' }}">
            <svg class="w-[22px] h-[22px] shrink-0 {{ $selectedPaymentMethod === 'gopay' ? 'text-[#FF2D2D]' : 'text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            <div class="flex flex-col items-start gap-0.5 flex-1">
                <span class="text-white text-sm font-semibold">{{ $paymentMethodModels->get('gopay')?->title ?? 'Platba kartou' }}</span>
                <span class="text-[#666666] text-xs">{!! strip_tags($paymentMethodModels->get('gopay')?->description ?? '', '<b><i><a>') !!}</span>
            </div>
            <div class="w-5 h-5 rounded-full flex items-center justify-center {{ $selectedPaymentMethod === 'gopay' ? 'border-2 border-[#FF2D2D]' : 'border-2 border-[#333333]' }}">
                @if($selectedPaymentMethod === 'gopay')<div class="w-2.5 h-2.5 rounded-full bg-[#FF2D2D]"></div>@endif
            </div>
        </button>
    @endif
    @if(in_array('bank_transfer', $enabledMethods))
        <button wire:click="selectPaymentMethod('bank_transfer')" class="w-full flex items-center gap-4 px-6 py-5 bg-[#0A0A0A] transition {{ $selectedPaymentMethod === 'bank_transfer' ? 'border-2 border-[#FF2D2D]' : 'border border-[#333333] hover:border-[#555555]' }}">
            <svg class="w-[22px] h-[22px] shrink-0 {{ $selectedPaymentMethod === 'bank_transfer' ? 'text-[#FF2D2D]' : 'text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11M20 10v11M8 14v3M12 14v3M16 14v3"/></svg>
            <div class="flex flex-col items-start gap-0.5 flex-1">
                <span class="text-white text-sm font-semibold">{{ $paymentMethodModels->get('bank_transfer')?->title ?? 'Bankovy prevod' }}</span>
                <span class="text-[#666666] text-xs">{!! strip_tags($paymentMethodModels->get('bank_transfer')?->description ?? '', '<b><i><a>') !!}</span>
            </div>
            <div class="w-5 h-5 rounded-full flex items-center justify-center {{ $selectedPaymentMethod === 'bank_transfer' ? 'border-2 border-[#FF2D2D]' : 'border-2 border-[#333333]' }}">
                @if($selectedPaymentMethod === 'bank_transfer')<div class="w-2.5 h-2.5 rounded-full bg-[#FF2D2D]"></div>@endif
            </div>
        </button>
    @endif
    @if(in_array('cash', $enabledMethods))
        <button wire:click="selectPaymentMethod('cash')" class="w-full flex items-center gap-4 px-6 py-5 bg-[#0A0A0A] transition {{ $selectedPaymentMethod === 'cash' ? 'border-2 border-[#FF2D2D]' : 'border border-[#333333] hover:border-[#555555]' }}">
            <svg class="w-[22px] h-[22px] shrink-0 {{ $selectedPaymentMethod === 'cash' ? 'text-[#FF2D2D]' : 'text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><rect x="2" y="6" width="20" height="12" rx="1"/><circle cx="12" cy="12" r="3"/></svg>
            <div class="flex flex-col items-start gap-0.5 flex-1">
                <span class="text-white text-sm font-semibold">{{ $paymentMethodModels->get('cash')?->title ?? 'Hotovost' }}</span>
                <span class="text-[#666666] text-xs">{!! strip_tags($paymentMethodModels->get('cash')?->description ?? '', '<b><i><a>') !!}</span>
            </div>
            <div class="w-5 h-5 rounded-full flex items-center justify-center {{ $selectedPaymentMethod === 'cash' ? 'border-2 border-[#FF2D2D]' : 'border-2 border-[#333333]' }}">
                @if($selectedPaymentMethod === 'cash')<div class="w-2.5 h-2.5 rounded-full bg-[#FF2D2D]"></div>@endif
            </div>
        </button>
    @endif
</div>

{{-- GoPay: show pay button --}}
@if($selectedPaymentMethod === 'gopay')
    <button wire:click="handlePayment" class="w-full h-[50px] bg-[#FF2D2D] hover:bg-red-700 transition flex items-center justify-center gap-2">
        <svg class="w-[18px] h-[18px] text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
        <span class="text-white text-sm font-bold">{{ __('training_detail.pay_button', ['price' => $feeLabel]) }}</span>
    </button>
@endif

{{-- Bank transfer: show details inline --}}
@if($selectedPaymentMethod === 'bank_transfer')
    <div class="w-full rounded-xl bg-[#0A0A0A] border border-[#FF2D2D]/20 p-5 flex flex-col gap-5">
        {{-- Payment details + QR code row --}}
        <div class="flex flex-col sm:flex-row gap-5">
            {{-- Left: payment details --}}
            <div class="flex flex-col gap-2.5 flex-1">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#FF2D2D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11M20 10v11M8 14v3M12 14v3M16 14v3"/></svg>
                    <span class="text-white text-[13px] font-semibold">{{ __('training_detail.bank_payment_details') }}</span>
                </div>

                @if($team?->bank_account_iban)
                    <div class="flex items-center gap-1.5">
                        <span class="text-[#666666] text-[11px] font-medium">{{ __('training_detail.bank_iban') }}</span>
                        <span class="text-white text-xs font-bold tracking-wide">{{ $team->bank_account_iban }}</span>
                    </div>
                @endif

                @if($variableSymbol)
                    <div class="flex items-center gap-1.5">
                        <span class="text-[#666666] text-[11px] font-medium">{{ __('training_detail.bank_variable_symbol') }}</span>
                        <span class="text-[#FF2D2D] text-xs font-bold">{{ $variableSymbol }}</span>
                    </div>
                @endif

                <div class="flex items-center gap-1.5">
                    <span class="text-[#666666] text-[11px] font-medium">{{ __('training_detail.bank_amount') }}</span>
                    <span class="text-white text-xs font-bold">{{ number_format((float) $feeAmount, 2, ',', ' ') }} {{ $feeCurrency === 'CZK' ? 'Kč' : '€' }}</span>
                </div>

                @if($team?->bank_account_name)
                    <div class="flex items-center gap-1.5">
                        <span class="text-[#666666] text-[11px] font-medium">{{ __('training_detail.bank_recipient') }}</span>
                        <span class="text-[#AAAAAA] text-xs font-semibold">{{ $team->bank_account_name }}</span>
                    </div>
                @endif

                @if($paymentNote)
                    <div class="flex items-center gap-1.5">
                        <span class="text-[#666666] text-[11px] font-medium">{{ __('training_detail.bank_message') }}</span>
                        <span class="text-[#AAAAAA] text-xs font-semibold">{{ $paymentNote }}</span>
                    </div>
                @elseif($season)
                    <div class="flex items-center gap-1.5">
                        <span class="text-[#666666] text-[11px] font-medium">{{ __('training_detail.bank_message') }}</span>
                        <span class="text-[#AAAAAA] text-xs font-semibold">{{ __('training_detail.bank_message_value', ['season' => $season->name]) }}</span>
                    </div>
                @endif
            </div>

            {{-- Right: QR code --}}
            @if($team?->bank_account_iban)
                @php
                    $qrImage = \App\Services\QrPaymentService::payBySquare(
                        iban: $team->bank_account_iban,
                        amount: (float) $feeAmount,
                        currency: $feeCurrency,
                        variableSymbol: $variableSymbol ?? '',
                    );
                @endphp
                @if($qrImage)
                    <div class="flex flex-col items-center gap-1.5">
                        <div class="w-[100px] h-[100px] rounded-lg bg-white flex items-center justify-center p-1.5">
                            <img src="data:image/png;base64,{{ $qrImage }}" alt="QR" class="w-full h-full">
                        </div>
                        <span class="text-[#666666] text-[10px] font-medium">{{ __('training_detail.bank_scan_qr') }}</span>
                    </div>
                @endif
            @endif
        </div>

        {{-- Instructions box (full width) --}}
        @php
            $bankInstructions = null;
            if ($payable && method_exists($payable, 'effectivePaymentMethodInstructions')) {
                $bankInstructions = $payable->effectivePaymentMethodInstructions('bank_transfer');
            }
            if (! $bankInstructions) {
                $teamBankMethod = $team?->enabledPaymentMethods
                    ?->firstWhere(
                        fn ($m) => ($m->method instanceof \App\Enums\PaymentMethodEnum ? $m->method->value : (string) $m->method) === 'bank_transfer',
                    );
                $teamInstructions = $teamBankMethod?->pivot?->getTranslation('instructions', app()->getLocale(), false);
                if (filled($teamInstructions) && trim(strip_tags($teamInstructions)) !== '') {
                    $bankInstructions = $teamInstructions;
                }
            }
            $hasInstructions = $bankInstructions && trim(strip_tags($bankInstructions)) !== '';
        @endphp
        <div class="rounded-lg bg-[#FF2D2D]/[0.03] border border-[#FF2D2D]/[0.12] p-2.5 flex flex-col gap-1.5 w-full">
            <div class="flex items-center gap-1.5">
                <svg class="w-3 h-3 text-[#FF2D2D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                <span class="text-[#FF2D2D] text-[11px] font-semibold">{{ __('training_detail.bank_instructions_title') }}</span>
            </div>
            @if($hasInstructions)
                <div class="text-left text-[#888888] text-[10px] prose-sm">{!! $bankInstructions !!}</div>
            @else
                <p class="text-left text-[#888888] text-[10px]">{{ __('training_detail.bank_instruction_1') }}</p>
                <p class="text-left text-[#888888] text-[10px]">{{ __('training_detail.bank_instruction_2') }}</p>
                <p class="text-left text-[#888888] text-[10px]">{{ __($isRegistration ? 'training_detail.bank_instruction_3_registration' : 'training_detail.bank_instruction_3') }}</p>
            @endif
        </div>
    </div>
@endif

{{-- Cash: show instructions inline --}}
@if($selectedPaymentMethod === 'cash')
    <div class="rounded-xl bg-[#0A0A0A] border border-[#FF2D2D]/20 p-5 flex flex-col gap-4 w-full">
        {{-- Header --}}
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-[#FF2D2D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><rect x="2" y="6" width="20" height="12" rx="1"/><circle cx="12" cy="12" r="3"/></svg>
            <span class="text-white text-[13px] font-semibold">{{ __('training_detail.cash_instructions_title') }}</span>
        </div>

        {{-- Amount --}}
        <div class="flex items-center gap-2.5 rounded-lg bg-[#FF2D2D]/[0.06] px-4 py-3 w-full">
            <span class="text-[#888888] text-xs font-medium">{{ __('training_detail.cash_amount_label') }}</span>
            <span class="text-[#FF2D2D] text-base font-bold">{{ number_format((float) $feeAmount, 2, ',', ' ') }} {{ $feeCurrency === 'CZK' ? 'Kč' : '€' }}</span>
        </div>

        {{-- Steps --}}
        <div class="flex flex-col gap-2.5">
            @foreach([
                __($isRegistration ? 'training_detail.cash_step_1_registration' : 'training_detail.cash_step_1'),
                __($isRegistration ? 'training_detail.cash_step_2_registration' : 'training_detail.cash_step_2'),
                __($isRegistration ? 'training_detail.cash_step_3_registration' : 'training_detail.cash_step_3'),
            ] as $index => $stepText)
                <div class="flex items-center gap-2.5">
                    <div class="w-[22px] h-[22px] rounded-full bg-[#FF2D2D]/[0.12] flex items-center justify-center shrink-0">
                        <span class="text-[#FF2D2D] text-[10px] font-bold">{{ $index + 1 }}</span>
                    </div>
                    <span class="text-[#AAAAAA] text-xs font-medium">{{ $stepText }}</span>
                </div>
            @endforeach
        </div>

        <div class="w-full h-px bg-[#222222]"></div>

        {{-- Contact info --}}
        @if($team?->contact_phone || $team?->contact_email)
            <div class="flex items-center gap-3 w-full">
                <svg class="w-4 h-4 text-[#FF2D2D] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <div class="flex flex-col gap-0.5 flex-1">
                    <span class="text-[#AAAAAA] text-[11px] font-semibold">{{ $team->name }}</span>
                    <span class="text-[#666666] text-[10px]">
                        @if($team->contact_phone){{ $team->contact_phone }}@endif
                        @if($team->contact_phone && $team->contact_email) · @endif
                        @if($team->contact_email){{ $team->contact_email }}@endif
                    </span>
                </div>
            </div>
        @endif

        {{-- Warning --}}
        <div class="flex items-center gap-2 rounded-lg bg-[#F59E0B]/[0.06] border border-[#F59E0B]/[0.18] px-3 py-2.5 w-full">
            <svg class="w-3 h-3 text-[#F59E0B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            <span class="text-[#F59E0B] text-[10px] font-medium">{{ __($isRegistration ? 'training_detail.cash_warning_registration' : 'training_detail.cash_warning') }}</span>
        </div>
    </div>
@endif

<p class="text-[#555555] text-xs text-center">{{ __('training_detail.payment_auto_approve_note') }}</p>
