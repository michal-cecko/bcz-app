<x-filament-panels::page>
    {{-- Registered trainings (current season) --}}
    <x-filament::section>
        <x-slot name="heading">Moje treningy</x-slot>
        {{ $this->table }}
    </x-filament::section>

    {{-- Available trainings --}}
    <x-filament::section>
        <x-slot name="heading">Dostupne treningy</x-slot>
        <x-slot name="description">Treningy, na ktore sa mozete zaregistrovat.</x-slot>
        {{ $this->content(\Filament\Schemas\Schema::make($this)) }}
    </x-filament::section>

    {{-- History --}}
    <x-filament::section collapsible collapsed>
        <x-slot name="heading">Historia treningov</x-slot>
        <x-slot name="description">Treningy z predchadzajucich sezon a zrusene registracie.</x-slot>
        {{ $this->historyTable }}
    </x-filament::section>
</x-filament-panels::page>
