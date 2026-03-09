<div class="relative py-4">
    <hr class="border-gray-300">
    @if(! empty($label))
        <span class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white px-4 text-sm text-gray-500">
            {{ brick_trans($label) }}
        </span>
    @endif
</div>
