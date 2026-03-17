<x-filament-widgets::widget>
    <x-filament::section heading="Dostupné tréningy">
        @if($trainings->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400">Momentálne nie sú dostupné žiadne tréningy.</p>
        @else
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($trainings as $training)
                    @php
                        $approvedCount = $training->registrations->where('status', \App\Enums\RegistrationStatusEnum::Approved)->count();
                        $isFull = $training->max_capacity !== null && $approvedCount >= $training->max_capacity;
                    @endphp
                    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="font-semibold text-gray-900 dark:text-white">
                                {{ $training->getTranslation('title', app()->getLocale()) ?: $training->getTranslation('title', 'sk') }}
                            </h3>
                            @if($isFull)
                                <x-filament::badge color="danger">Plný</x-filament::badge>
                            @endif
                        </div>

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

                        @if($training->max_capacity)
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Kapacita: {{ $approvedCount }}/{{ $training->max_capacity }}
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

                        @unless($isFull)
                            <div class="mt-3">
                                <x-filament::button
                                    color="primary"
                                    size="sm"
                                    tag="a"
                                    href="{{ $training->getLinkUrl() }}"
                                >
                                    Registrovať sa
                                </x-filament::button>
                            </div>
                        @endunless
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
