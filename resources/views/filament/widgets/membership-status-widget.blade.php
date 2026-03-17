<x-filament-widgets::widget>
    <x-filament::section heading="Členstvo">
        @if($membership)
            <div class="flex items-center justify-between gap-4">
                <div class="space-y-1">
                    @if($membership->season)
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Sezóna: <span class="font-medium text-gray-900 dark:text-white">{{ $membership->season->name }}</span>
                        </p>
                    @endif

                    <div class="flex items-center gap-2">
                        <x-filament::badge :color="$membership->status->getColor()">
                            {{ $membership->status->getLabel() }}
                        </x-filament::badge>

                        @if($membership->is_free)
                            <x-filament::badge color="info">Zadarmo</x-filament::badge>
                        @else
                            <span class="text-sm font-medium">
                                {{ number_format((float) $membership->fee_amount, 2) }} {{ $membership->fee_currency }}
                            </span>
                        @endif
                    </div>

                    @if($membership->starts_at || $membership->ends_at)
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $membership->starts_at?->format('d.m.Y') }} - {{ $membership->ends_at?->format('d.m.Y') }}
                        </p>
                    @endif

                    @if($membership->payment_deadline_at)
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Splatnosť: <span class="font-medium">{{ $membership->payment_deadline_at->format('d.m.Y') }}</span>
                        </p>
                    @endif
                </div>

                @if($membership->status === \App\Enums\MembershipStatusEnum::PENDING)
                    <x-filament::button
                        color="warning"
                        tag="a"
                        href="{{ url('/') }}"
                    >
                        Zaplatiť
                    </x-filament::button>
                @endif
            </div>
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400">Nemáte aktívne členstvo.</p>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
