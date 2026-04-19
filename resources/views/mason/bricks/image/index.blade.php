@php $media = brick_media($image ?? null); @endphp
<figure>
 @if($media->url)
 <img src="{{ $media->url }}"alt="{{ brick_trans($alt ?? []) ?: $media->alt }}"class="w-full">
 @endif
 @if(! empty($caption) || $media->caption)
 <figcaption class="mt-2 text-sm text-gray-500 text-center">{{ brick_trans($caption ?? []) ?: $media->caption }}</figcaption>
 @endif
</figure>
