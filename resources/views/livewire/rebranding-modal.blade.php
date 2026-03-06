<div
    @if($shouldShow)
        x-init="setTimeout(() => $wire.call('showModal'), 8000)"
    @endif
>
    <x-filament-actions::modals />
</div>
