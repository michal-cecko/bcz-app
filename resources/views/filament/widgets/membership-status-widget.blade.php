<x-filament-widgets::widget class="h-full">
    <x-filament::section class="h-full">
        @php $membership = $this->membership; @endphp

        @if($membership)
            {{-- Header: Členstvo + status --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-identification" class="h-6 w-6 text-gray-400" />
                    <span class="text-base font-semibold text-gray-900 dark:text-white">{{ __('member.membership.title') }}</span>
                </div>
                <x-filament::badge :color="$membership->status->getColor()" size="lg">
                    {{ $membership->status->getLabel() }}
                </x-filament::badge>
            </div>

            {{-- Stats row --}}
            <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('member.membership.season') }}</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $membership->season?->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('member.membership.fee') }}</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                        @if($membership->is_free)
                            {{ __('member.membership.free') }}
                        @else
                            {{ number_format((float) $membership->fee_amount, 2) }} {{ $membership->fee_currency }}
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('member.membership.valid_from') }}</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $membership->starts_at?->format('d.m.Y') ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('member.membership.valid_to') }}</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $membership->ends_at?->format('d.m.Y') ?? '-' }}</p>
                </div>
            </div>

            {{-- Payment flow for PENDING memberships --}}
            @if($membership->status === \App\Enums\MembershipStatusEnum::PENDING && !$membership->is_free)
                @php
                    $team = \Filament\Facades\Filament::getTenant();
                    $enabledMethods = $team?->getEnabledPaymentMethodKeys() ?? [];
                    $season = $membership->season;
                    $pendingPayment = $this->pendingPayment;
                    $variableSymbol = $pendingPayment?->formattedVariableSymbol();
                @endphp
                <div class="mt-6 border-t border-gray-200 pt-5 dark:border-gray-700">
                    <p class="mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('payments.method.select_label') }}</p>

                    <div class="space-y-2">
                        @if(in_array('gopay', $enabledMethods))
                            <label class="flex cursor-pointer items-center gap-3 rounded-lg border px-4 py-3 transition
                                {{ $paymentMethod === 'gopay' ? 'border-primary-500 bg-primary-50 dark:bg-primary-500/10' : 'border-gray-200 dark:border-gray-700' }}">
                                <input type="radio" wire:model.live="paymentMethod" value="gopay" class="text-primary-600">
                                <x-filament::icon icon="heroicon-o-credit-card" class="h-5 w-5 text-gray-500" />
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ __('payments.method.gopay') }}</span>
                            </label>
                        @endif

                        @if(in_array('bank_transfer', $enabledMethods))
                            <label class="flex cursor-pointer items-center gap-3 rounded-lg border px-4 py-3 transition
                                {{ $paymentMethod === 'bank_transfer' ? 'border-primary-500 bg-primary-50 dark:bg-primary-500/10' : 'border-gray-200 dark:border-gray-700' }}">
                                <input type="radio" wire:model.live="paymentMethod" value="bank_transfer" class="text-primary-600">
                                <x-filament::icon icon="heroicon-o-building-library" class="h-5 w-5 text-gray-500" />
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ __('payments.method.bank_transfer') }}</span>
                                <span class="text-xs text-gray-400">{{ __('payments.method.bank_transfer_subtitle') }}</span>
                            </label>

                            @if($paymentMethod === 'bank_transfer')
                                <div class="ml-8 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                                    <div class="flex items-start gap-4">
                                        <div class="flex-1 space-y-2 text-sm">
                                            <p class="font-medium text-gray-900 dark:text-white">{{ __('payments.bank_transfer.details_title') }}</p>
                                            @if($team?->bank_account_iban)
                                                <p class="text-gray-600 dark:text-gray-400">
                                                    {{ __('payments.bank_transfer.iban') }} <span class="font-mono font-medium text-gray-900 dark:text-white">{{ $team->bank_account_iban }}</span>
                                                </p>
                                            @endif
                                            @if($variableSymbol)
                                                <p class="text-gray-600 dark:text-gray-400">
                                                    {{ __('payments.bank_transfer.variable_symbol') }} <span class="font-mono font-medium text-primary-600 dark:text-primary-400">{{ $variableSymbol }}</span>
                                                </p>
                                            @endif
                                            <p class="text-gray-600 dark:text-gray-400">
                                                {{ __('payments.bank_transfer.amount') }} <span class="font-medium text-gray-900 dark:text-white">{{ number_format((float) $membership->fee_amount, 2) }} {{ $membership->fee_currency }}</span>
                                            </p>
                                            @if($team?->bank_account_name)
                                                <p class="text-gray-600 dark:text-gray-400">
                                                    {{ __('payments.bank_transfer.recipient') }} <span class="font-medium text-gray-900 dark:text-white">{{ $team->bank_account_name }}</span>
                                                </p>
                                            @endif
                                            @php $bankNote = $membership->getQrPaymentNote(); @endphp
                                            @if($bankNote)
                                                <p class="text-gray-600 dark:text-gray-400">
                                                    {{ __('payments.bank_transfer.note') }} <span class="font-medium text-gray-900 dark:text-white">{{ $bankNote }}</span>
                                                </p>
                                            @endif
                                        </div>
                                        @php $qr = $this->qrCode; @endphp
                                        @if($qr)
                                            <div class="flex flex-col items-center gap-1">
                                                <div class="rounded-lg bg-white p-1.5">
                                                    <img src="data:image/png;base64,{{ $qr }}" alt="QR Platba" class="h-32 w-32">
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endif

                        @if(in_array('cash', $enabledMethods))
                            <label class="flex cursor-pointer items-center gap-3 rounded-lg border px-4 py-3 transition
                                {{ $paymentMethod === 'cash' ? 'border-primary-500 bg-primary-50 dark:bg-primary-500/10' : 'border-gray-200 dark:border-gray-700' }}">
                                <input type="radio" wire:model.live="paymentMethod" value="cash" class="text-primary-600">
                                <x-filament::icon icon="heroicon-o-banknotes" class="h-5 w-5 text-gray-500" />
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ __('payments.method.cash') }}</span>
                                <span class="text-xs text-gray-400">{{ __('payments.method.cash_subtitle') }}</span>
                            </label>

                            @if($paymentMethod === 'cash')
                                <div class="ml-8 rounded-lg border border-success-200 bg-success-50 p-4 dark:border-success-700 dark:bg-success-500/10">
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-2 rounded-lg bg-success-100 px-3 py-2 dark:bg-success-500/20">
                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('payments.bank_transfer.amount_to_pay') }}</span>
                                            <span class="text-sm font-bold text-success-700 dark:text-success-300">{{ number_format((float) $membership->fee_amount, 2) }} {{ $membership->fee_currency }}</span>
                                        </div>
                                        <div class="flex items-start gap-2">
                                            <x-filament::icon icon="heroicon-m-information-circle" class="mt-0.5 h-5 w-5 flex-shrink-0 text-success-600 dark:text-success-400" />
                                            <p class="text-sm text-success-700 dark:text-success-300">
                                                {{ __('payments.cash.instruction') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>

                    {{-- Warning --}}
                    <div class="mt-4 flex items-start gap-2 rounded-lg border border-warning-200 bg-warning-50 p-3 dark:border-warning-700 dark:bg-warning-500/10">
                        <x-filament::icon icon="heroicon-m-exclamation-triangle" class="mt-0.5 h-5 w-5 flex-shrink-0 text-warning-600 dark:text-warning-400" />
                        <p class="text-sm text-warning-700 dark:text-warning-300">
                            {{ __('member.membership.membership_required_warning') }}
                        </p>
                    </div>

                    @if($paymentMethod === 'gopay' && in_array('gopay', $enabledMethods))
                        <div class="mt-4 flex justify-end">
                            <x-filament::button wire:click="payWithGoPay" color="danger" icon="heroicon-m-credit-card">
                                {{ __('payments.gopay.pay_button', ['amount' => number_format((float) $membership->fee_amount, 2).' '.$membership->fee_currency]) }}
                            </x-filament::button>
                        </div>
                    @endif
                </div>
            @endif
        @else
            @php $tenant = \Filament\Facades\Filament::getTenant(); @endphp
            <div class="flex items-center gap-3">
                <x-filament::icon icon="heroicon-o-identification" class="h-5 w-5 text-gray-400" />
                <span class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('member.membership.title') }}</span>
            </div>
            <div class="mt-4 flex items-start gap-3 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                <x-filament::icon icon="heroicon-m-information-circle" class="mt-0.5 h-5 w-5 flex-shrink-0 text-gray-400" />
                @if(! $tenant)
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('member.membership.no_team_heading') }}</p>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('member.membership.no_active_season') }}</p>
                @endif
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
