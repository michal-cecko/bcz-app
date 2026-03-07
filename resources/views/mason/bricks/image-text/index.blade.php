<div class="grid md:grid-cols-2 gap-8 items-center">
    <div class="{{ ($image_position ?? 'left') === 'right' ? 'md:order-2' : '' }}">
        @if(! empty($image))
            <img src="{{ Storage::url($image) }}" alt="{{ $alt ?? '' }}" class="w-full rounded-lg">
        @endif
    </div>
    <div class="prose prose-lg max-w-none">
        {!! $text ?? '' !!}
    </div>
</div>
