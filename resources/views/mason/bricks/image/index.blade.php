<figure>
    @if(! empty($image))
        <img src="{{ Storage::url($image) }}" alt="{{ $alt ?? '' }}" class="w-full rounded-lg">
    @endif
    @if(! empty($caption))
        <figcaption class="mt-2 text-sm text-gray-500 text-center">{{ $caption }}</figcaption>
    @endif
</figure>
