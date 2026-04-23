@php
    $event = $registration->event;
    $latestPayment = $registration->payments->sortByDesc('created_at')->first();
    $locale = app()->getLocale();

    $schema = $event?->organization?->registration_form_schema ?? [];
    $valuesByKey = $registration->fieldValues->keyBy('field_key');
    $dateTypes = ['birth_date', 'date_picker'];

    $fieldRows = [];
    foreach ($schema as $field) {
        $key = $field['key'] ?? $field['name'] ?? null;
        if (! $key) {
            continue;
        }
        $value = $valuesByKey->get($key)?->value;
        if ($value === null || $value === '') {
            continue;
        }
        $type = $field['type'] ?? null;
        if (in_array($type, $dateTypes, true)) {
            try {
                $value = \Illuminate\Support\Carbon::parse($value)->translatedFormat('j. F Y');
            } catch (\Throwable $e) {
                // keep raw value
            }
        }
        $rawLabel = $field['label'] ?? $key;
        $label = is_array($rawLabel) ? ($rawLabel[$locale] ?? $rawLabel['sk'] ?? $key) : $rawLabel;
        $fieldRows[] = ['label' => $label, 'value' => $value];
    }
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
            <p class="text-sm text-gray-900 dark:text-white">{{ $event?->date?->translatedFormat('j. F Y') ?? '-' }}</p>
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
            <p class="text-sm text-gray-900 dark:text-white">{{ $registration->registered_at?->translatedFormat('j. F Y, H:i') ?? '-' }}</p>
        </div>
    </section>

    @if(! empty($fieldRows))
        <section>
            <h3 class="mb-2 text-sm font-semibold text-gray-900 dark:text-white">{{ __('member.events.submitted_fields') }}</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                @foreach($fieldRows as $row)
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $row['label'] }}</p>
                        <p class="text-sm text-gray-900 dark:text-white whitespace-pre-line">{{ $row['value'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

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
