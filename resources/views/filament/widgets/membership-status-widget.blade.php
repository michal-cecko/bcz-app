<x-filament-widgets::widget class="h-full">
    <x-filament::section class="h-full">
        @php $membership = $this->membership; @endphp

        @if($membership)
            {{-- Header: Členstvo + status --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-identification" class="h-6 w-6 text-gray-400" />
                    <span class="text-base font-semibold text-gray-900 dark:text-white">Členstvo</span>
                </div>
                <x-filament::badge :color="$membership->status->getColor()" size="lg">
                    {{ $membership->status->getLabel() }}
                </x-filament::badge>
            </div>

            {{-- Stats row --}}
            <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Sezóna</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $membership->season?->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Poplatok</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                        @if($membership->is_free)
                            Zadarmo
                        @else
                            {{ number_format((float) $membership->fee_amount, 2) }} {{ $membership->fee_currency }}
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Platné od</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $membership->starts_at?->format('d.m.Y') ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Platné do</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $membership->ends_at?->format('d.m.Y') ?? '-' }}</p>
                </div>
            </div>

            {{-- Payment flow for PENDING memberships --}}
            @if($membership->status === \App\Enums\MembershipStatusEnum::PENDING && !$membership->is_free)
                @php
                    $team = \Filament\Facades\Filament::getTenant();
                    $enabledMethods = $team?->payment_methods_enabled ?? ['bank_transfer', 'cash'];
                    $season = $membership->season;
                @endphp
                <div class="mt-6 border-t border-gray-200 pt-5 dark:border-gray-700">
                    <p class="mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">Spôsob platby:</p>

                    <div class="space-y-2">
                        @if(in_array('stripe', $enabledMethods))
                            <label class="flex cursor-pointer items-center gap-3 rounded-lg border px-4 py-3 transition
                                {{ $paymentMethod === 'stripe' ? 'border-primary-500 bg-primary-50 dark:bg-primary-500/10' : 'border-gray-200 dark:border-gray-700' }}">
                                <input type="radio" wire:model.live="paymentMethod" value="stripe" class="text-primary-600">
                                <x-filament::icon icon="heroicon-o-credit-card" class="h-5 w-5 text-gray-500" />
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Kartou (Stripe)</span>
                            </label>
                        @endif

                        @if(in_array('bank_transfer', $enabledMethods))
                            <label class="flex cursor-pointer items-center gap-3 rounded-lg border px-4 py-3 transition
                                {{ $paymentMethod === 'bank_transfer' ? 'border-primary-500 bg-primary-50 dark:bg-primary-500/10' : 'border-gray-200 dark:border-gray-700' }}">
                                <input type="radio" wire:model.live="paymentMethod" value="bank_transfer" class="text-primary-600">
                                <x-filament::icon icon="heroicon-o-building-library" class="h-5 w-5 text-gray-500" />
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Bankový prevod</span>
                                <span class="text-xs text-gray-400">Platba na účet tímu</span>
                            </label>

                            @if($paymentMethod === 'bank_transfer')
                                <div class="ml-8 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                                    <div class="flex items-start gap-4">
                                        <div class="flex-1 space-y-2 text-sm">
                                            <p class="font-medium text-gray-900 dark:text-white">Platobné údaje pre bankový prevod</p>
                                            @if($team?->bank_account_iban)
                                                <p class="text-gray-600 dark:text-gray-400">
                                                    IBAN: <span class="font-mono font-medium text-gray-900 dark:text-white">{{ $team->bank_account_iban }}</span>
                                                </p>
                                            @endif
                                            @if($season?->variable_symbol)
                                                <p class="text-gray-600 dark:text-gray-400">
                                                    Variabilný symbol: <span class="font-mono font-medium text-primary-600 dark:text-primary-400">{{ $season->variable_symbol }}</span>
                                                </p>
                                            @endif
                                            <p class="text-gray-600 dark:text-gray-400">
                                                Suma: <span class="font-medium text-gray-900 dark:text-white">{{ number_format((float) $membership->fee_amount, 2) }} {{ $membership->fee_currency }}</span>
                                            </p>
                                            @if($team?->bank_account_name)
                                                <p class="text-gray-600 dark:text-gray-400">
                                                    Príjemca: <span class="font-medium text-gray-900 dark:text-white">{{ $team->bank_account_name }}</span>
                                                </p>
                                            @endif
                                        </div>
                                        @php $qr = $this->qrCodes; @endphp
                                        @if($qr['sk'] ?? null)
                                            <div class="flex flex-col items-center gap-1">
                                                <div class="rounded-lg bg-white p-1.5">
                                                    <img src="data:image/png;base64,{{ $qr['sk'] }}" alt="QR" class="h-24 w-24">
                                                </div>
                                                <span class="text-[10px] text-gray-400">Pay by Square</span>
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
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Hotovosť</span>
                                <span class="text-xs text-gray-400">V hotovosti</span>
                            </label>

                            @if($paymentMethod === 'cash')
                                <div class="ml-8 rounded-lg border border-success-200 bg-success-50 p-4 dark:border-success-700 dark:bg-success-500/10">
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-2 rounded-lg bg-success-100 px-3 py-2 dark:bg-success-500/20">
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Suma k úhrade:</span>
                                            <span class="text-sm font-bold text-success-700 dark:text-success-300">{{ number_format((float) $membership->fee_amount, 2) }} {{ $membership->fee_currency }}</span>
                                        </div>
                                        <div class="flex items-start gap-2">
                                            <x-filament::icon icon="heroicon-m-information-circle" class="mt-0.5 h-5 w-5 flex-shrink-0 text-success-600 dark:text-success-400" />
                                            <p class="text-sm text-success-700 dark:text-success-300">
                                                Platbu odovzdajte trénerovi na najbližšom tréningu.
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
                            Pre registráciu na tréningy vyžadujúce členstvo je potrebné uhradiť členský poplatok.
                        </p>
                    </div>

                    @if($paymentMethod === 'stripe' && in_array('stripe', $enabledMethods))
                        <div class="mt-4 flex justify-end">
                            <x-filament::button color="danger" icon="heroicon-m-credit-card">
                                Zaplatiť {{ number_format((float) $membership->fee_amount, 2) }} {{ $membership->fee_currency }}
                            </x-filament::button>
                        </div>
                    @endif
                </div>
            @endif
        @else
            {{-- No active season at all --}}
            <div class="flex items-center gap-3">
                <x-filament::icon icon="heroicon-o-identification" class="h-5 w-5 text-gray-400" />
                <span class="text-lg font-semibold text-gray-900 dark:text-white">Členstvo</span>
            </div>
            <div class="mt-4 flex items-start gap-3 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                <x-filament::icon icon="heroicon-m-information-circle" class="mt-0.5 h-5 w-5 flex-shrink-0 text-gray-400" />
                <p class="text-sm text-gray-500 dark:text-gray-400">V tomto tíme momentálne nie je aktívna sezóna.</p>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
