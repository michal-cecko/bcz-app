@php
    use Filament\Support\Facades\FilamentView;

    $statePath = $getStatePath();
    $bricks = $getBricks();
    $state = $getState();
@endphp

<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div
        @if (FilamentView::hasSpaMode())
            {{-- format-ignore-start --}}x-load="visible || event (x-modal-opened)"{{-- format-ignore-end --}}
        @else
            x-load
        @endif
        x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc("mason-entry", "awcodes/mason") }}"
        x-data="masonEntryComponent({
                    state: @js($state),
                    bricks: @js(array_map(fn ($brick) => is_string($brick) ? $brick : get_class($brick), $bricks)),
                    previewLayout: @js($getPreviewLayout()),
                })"
        id="{{ "mason-entry-wrapper-" . $statePath }}"
        {{
            \Filament\Support\prepare_inherited_attributes($getExtraAttributeBag())->class([
                "mason-entry-wrapper",
            ])
        }}
    >
        <div class="mason">
            <iframe
                x-ref="entryIframe"
                name="{{ "mason-entry-iframe-" . $statePath }}"
                {{
                    \Filament\Support\prepare_inherited_attributes($getExtraInputAttributeBag())->class([
                        "mason-entry-iframe",
                    ])
                }}
            ></iframe>
        </div>
    </div>
</x-dynamic-component>
