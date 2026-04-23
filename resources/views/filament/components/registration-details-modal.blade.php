@php
    $event = $registration->event;
    $latestPayment = $registration->payments->sortByDesc('created_at')->first();
    $locale = app()->getLocale();
@endphp

<div class="space-y-6">
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Podujatie</p>
            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                {{ $event?->getTranslation('title', $locale) ?: $event?->getTranslation('title', 'sk') ?: '-' }}
            </p>
        </div>
        <div>
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Dátum</p>
            <p class="text-sm text-gray-900 dark:text-white">{{ $event?->date?->format('d.m.Y') ?? '-' }}</p>
        </div>
        <div>
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Miesto</p>
            <p class="text-sm text-gray-900 dark:text-white">{{ $event?->city ?? '-' }}</p>
        </div>
        <div>
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Stav registrácie</p>
            <x-filament::badge :color="$registration->status->getColor()">
                {{ $registration->status->getLabel() }}
            </x-filament::badge>
        </div>
        <div>
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Zaregistrované</p>
            <p class="text-sm text-gray-900 dark:text-white">{{ $registration->registered_at?->format('d.m.Y H:i') ?? '-' }}</p>
        </div>
    </section>

    @if($latestPayment)
        <section>
            <h3 class="mb-2 text-sm font-semibold text-gray-900 dark:text-white">Platba</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Stav platby</p>
                    <x-filament::badge :color="$latestPayment->status->getColor()">
                        {{ $latestPayment->status->getLabel() }}
                    </x-filament::badge>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Suma</p>
                    <p class="text-sm text-gray-900 dark:text-white">
                        {{ number_format((float) $latestPayment->amount, 2) }} {{ $latestPayment->currency }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Variabilný symbol</p>
                    <p class="font-mono text-sm font-semibold text-primary-600 dark:text-primary-400">
                        {{ $latestPayment->formattedVariableSymbol() ?? '-' }}
                    </p>
                </div>
            </div>
        </section>
    @endif
</div>
