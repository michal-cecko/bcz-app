<div class="mt-4">
    <x-filament::button
        wire:click="{{ $method }}"
        icon="heroicon-o-paper-airplane"
        color="primary"
    >
        {{ $label ?? 'Ulozit a poziadat o schvalenie' }}
    </x-filament::button>
</div>
