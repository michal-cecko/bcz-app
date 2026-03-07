@props([
    'label' => null,
])

<div class="mason-unregistered-brick">
    <x-filament::icon icon="heroicon-o-exclamation-triangle" />
    <p>{{ trans('mason::mason.unregistered_brick', ['label' => $label]) }}</p>
</div>
