@php
    $event = $registration->event;
    $latestPayment = $registration->payments->sortByDesc('created_at')->first();
    $locale = app()->getLocale();
@endphp

<div class="space-y-6">
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('member.events.event') }}</p>
            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                {{ $event?->getTranslation('title', $locale) ?: $event?->getTranslation('title', 'sk') ?: '-' }}
            </p>
        </div>
        <div>
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('member.events.date') }}</p>
            <p class="text-sm text-gray-900 dark:text-white">{{ $event?->date?->format('d.m.Y') ?? '-' }}</p>
        </div>
        <div>
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('member.events.place') }}</p>
            <p class="text-sm text-gray-900 dark:text-white">{{ $event?->city ?? '-' }}</p>
        </div>
        <div>
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('member.events.registration_status') }}</p>
            <x-filament::badge :color="$registration->status->getColor()">
                {{ $registration->status->getLabel() }}
            </x-filament::badge>
        </div>
        <div>
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('member.events.registered_at') }}</p>
            <p class="text-sm text-gray-900 dark:text-white">{{ $registration->registered_at?->format('d.m.Y H:i') ?? '-' }}</p>
        </div>
    </section>

    @if($latestPayment)
        <section>
            <h3 class="mb-2 text-sm font-semibold text-gray-900 dark:text-white">{{ __('member.events.payment') }}</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('member.events.payment_status') }}</p>
                    <x-filament::badge :color="$latestPayment->status->getColor()">
                        {{ $latestPayment->status->getLabel() }}
                    </x-filament::badge>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('member.events.amount') }}</p>
                    <p class="text-sm text-gray-900 dark:text-white">
                        {{ number_format((float) $latestPayment->amount, 2) }} {{ $latestPayment->currency }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('member.events.variable_symbol') }}</p>
                    <p class="font-mono text-sm font-semibold text-primary-600 dark:text-primary-400">
                        {{ $latestPayment->formattedVariableSymbol() ?? '-' }}
                    </p>
                </div>
            </div>
        </section>
    @endif
</div>
