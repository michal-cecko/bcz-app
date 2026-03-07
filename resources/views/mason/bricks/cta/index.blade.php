<section class="rounded-xl px-8 py-12 text-center" style="background-color: {{ $background_color ?? '#1f2937' }}; color: white;">
    @if(! empty($title))
        <h2 class="text-3xl font-bold mb-4">{{ $title }}</h2>
    @endif
    @if(! empty($description))
        <p class="text-lg mb-8 opacity-90">{{ $description }}</p>
    @endif
    @if(! empty($button_text) && ! empty($button_url))
        <a href="{{ $button_url }}" class="inline-block bg-white font-semibold px-8 py-3 rounded-lg hover:bg-gray-100 transition" style="color: {{ $background_color ?? '#1f2937' }};">
            {{ $button_text }}
        </a>
    @endif
</section>
