<x-filament-panels::page>
    {{-- Registered trainings (current season) --}}
    <x-filament::section>
        <x-slot name="heading">Moje tréningy</x-slot>
        {{ $this->table }}
    </x-filament::section>

    {{-- Available trainings --}}
    <x-filament::section>
        <x-slot name="heading">Dostupné tréningy</x-slot>
        <x-slot name="description">Tréningy, na ktoré sa môžete zaregistrovať.</x-slot>
        {{ $this->content(\Filament\Schemas\Schema::make($this)) }}
    </x-filament::section>

    {{-- History --}}
    <x-filament::section collapsible collapsed>
        <x-slot name="heading">História tréningov</x-slot>
        <x-slot name="description">Tréningy z predchádzajúcich sezón a zrušené registrácie.</x-slot>
        {{ $this->historyTable }}
    </x-filament::section>
</x-filament-panels::page>
