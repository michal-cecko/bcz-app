<div id="mason-entry-container">
    @if (empty($blocks))
        <div
            class="mason-entry-empty"
            style="
                min-height: 4rem;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #9ca3af;
            "
        >
            {{ __('mason::mason.entry.empty') }}
        </div>
    @else
        @foreach ($blocks as $block)
            <div
                class="mason-entry-block"
                data-block-index="{{ $block['index'] }}"
                data-brick-id="{{ $block['id'] }}"
            >
                <div class="mason-entry-block-content">
                    {!! $block['html'] ?? '' !!}
                </div>
            </div>
        @endforeach
    @endif
</div>

<script>
    ;(function () {
        // Send ready message when loaded
        window.addEventListener('load', function () {
            window.parent.postMessage({ type: 'ready' }, '*')
        })
    })()
</script>
