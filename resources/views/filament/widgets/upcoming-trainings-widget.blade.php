<x-filament-widgets::widget>
    <x-filament::section heading="Moje tréningy">
        @if($registrations->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400">Nie ste registrovaný na žiadne tréningy.</p>
        @else
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($registrations as $registration)
                    @php $training = $registration->training; @endphp
                    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <h3 class="font-semibold text-gray-900 dark:text-white">
                            {{ $training->getTranslation('title', app()->getLocale()) ?: $training->getTranslation('title', 'sk') }}
                        </h3>

                        @if($training->sportCategory)
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                {{ $training->sportCategory->getTranslation('name', app()->getLocale()) ?: $training->sportCategory->getTranslation('name', 'sk') }}
                            </p>
                        @endif

                        @if($training->schedule_days)
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                                {{ implode(', ', $training->schedule_days) }}
                                @if($training->start_time)
                                    · {{ $training->start_time }}
                                @endif
                            </p>
                        @endif

                        @if($training->coaches->isNotEmpty())
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ $training->coaches->pluck('name')->implode(', ') }}
                            </p>
                        @endif

                        @if($training->place_name)
                            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                                {{ $training->getTranslation('place_name', app()->getLocale()) ?: $training->getTranslation('place_name', 'sk') }}
                            </p>
                        @endif

                        @if($training->pricing_type)
                            <div class="mt-2">
                                <x-filament::badge color="info">
                                    {{ $training->pricing_type->getLabel() }}
                                    @if($training->price_amount)
                                        · {{ number_format((float) $training->price_amount, 2) }} €
                                    @endif
                                </x-filament::badge>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
