<div class="grid md:grid-cols-2 gap-8 items-center">
    <div class="{{ ($image_position ?? 'left') === 'right' ? 'md:order-2' : '' }}">
        @php $media = brick_media($image ?? null); @endphp
        @if($media->url)
            <img src="{{ $media->url }}" alt="{{ brick_trans($alt ?? []) ?: $media->alt }}" class="w-full rounded-lg">
        @endif
    </div>
    <div class="prose prose-lg max-w-none">
        {!! brick_trans($text ?? []) !!}
    </div>
</div>
