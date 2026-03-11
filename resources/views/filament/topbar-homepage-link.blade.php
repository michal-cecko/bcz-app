@php
    $url = config('app.url');
@endphp

<a
    href="{{ $url }}"
    target="_blank"
    class="relative flex items-center justify-center outline-none"
    title="Zobraziť web"
>
    <x-filament::icon
        icon="heroicon-o-globe-alt"
        class="h-6 w-6"
        style="color: #71717b;"
    />
</a>
