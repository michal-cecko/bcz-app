<x-filament-panels::page>
    {{-- Tabs --}}
    <div class="flex gap-2">
        <x-filament::button
            :color="$tab === 'upcoming' ? 'primary' : 'gray'"
            size="sm"
            wire:click="$set('tab', 'upcoming')"
        >
            Nadchádzajúce
        </x-filament::button>
        <x-filament::button
            :color="$tab === 'past' ? 'primary' : 'gray'"
            size="sm"
            wire:click="$set('tab', 'past')"
        >
            Minulé
        </x-filament::button>
    </div>

    {{-- Events List --}}
    @if($events->isEmpty())
        <x-filament::section>
            <div class="flex flex-col items-center gap-2 py-8 text-center">
                <x-filament::icon icon="heroicon-o-calendar-days" class="h-10 w-10 text-gray-300 dark:text-gray-600" />
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $tab === 'upcoming' ? 'Žiadne nadchádzajúce podujatia.' : 'Žiadne minulé podujatia.' }}
                </p>
            </div>
        </x-filament::section>
    @else
        <div class="space-y-3">
            @foreach($events as $event)
                @php
                    $userRegistration = $event->registrations->first();
                @endphp
                <x-filament::section>
                    <div class="flex items-start gap-4">
                        {{-- Date Badge --}}
                        <div class="flex h-14 w-14 flex-shrink-0 flex-col items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700">
                            <span class="text-lg font-bold leading-none text-gray-900 dark:text-white">{{ $event->date?->format('d') }}</span>
                            <span class="text-xs uppercase text-gray-500 dark:text-gray-400">{{ $event->date?->translatedFormat('M') }}</span>
                        </div>

                        {{-- Event Info --}}
                        <div class="min-w-0 flex-1">
                            <h3 class="font-semibold text-gray-900 dark:text-white">
                                {{ $event->getTranslation('title', app()->getLocale()) ?: $event->getTranslation('title', 'sk') }}
                            </h3>
                            <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                                @if($event->eventCategory)
                                    <span>{{ $event->eventCategory->getTranslation('title', app()->getLocale()) ?: $event->eventCategory->getTranslation('title', 'sk') }}</span>
                                @endif
                                @if($event->city)
                                    <span class="flex items-center gap-1">
                                        <x-filament::icon icon="heroicon-m-map-pin" class="h-3 w-3" />
                                        {{ $event->city }}
                                    </span>
                                @endif
                                @if($event->date)
                                    <span>{{ $event->date->format('d.m.Y') }}@if($event->date_end && !$event->date_end->isSameDay($event->date)) - {{ $event->date_end->format('d.m.Y') }}@endif</span>
                                @endif
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex flex-shrink-0 items-center gap-2">
                            @if($userRegistration)
                                <x-filament::badge color="success">Registrovaný</x-filament::badge>
                            @elseif($tab === 'upcoming')
                                <x-filament::button color="danger" size="sm" tag="a" href="{{ $event->getLinkUrl() }}">
                                    Registrovať sa
                                </x-filament::button>
                            @endif
                        </div>
                    </div>
                </x-filament::section>
            @endforeach
        </div>
    @endif
</x-filament-panels::page>
