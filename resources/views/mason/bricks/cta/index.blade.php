@php
    $ctaHref = brick_link(['link_type' => $button_link_type ?? '', 'link_model_id' => $button_link_model_id ?? '', 'link_url' => $button_link_url ?? '']) ?? brick_trans($button_url ?? []);
@endphp
<section class="rounded-xl px-8 py-12 text-center" style="background-color: {{ $background_color ?? '#1f2937' }}; color: white;">
    @if(! empty($title))
        <h2 class="text-3xl font-bold mb-4">{{ brick_trans($title) }}</h2>
    @endif
    @if(! empty($description))
        <div class="text-lg mb-8 opacity-90 prose prose-invert mx-auto">{!! brick_trans($description) !!}</div>
    @endif
    @if(! empty($button_text) && $ctaHref)
        <a href="{{ $ctaHref }}" class="inline-block bg-white font-semibold px-8 py-3 rounded-lg hover:bg-gray-100 transition" style="color: {{ $background_color ?? '#1f2937' }};">
            {{ brick_trans($button_text) }}
        </a>
    @endif
</section>
