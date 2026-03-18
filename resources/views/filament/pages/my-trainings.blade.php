<x-filament-panels::page>
    {{-- Registered trainings --}}
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
</x-filament-panels::page>
