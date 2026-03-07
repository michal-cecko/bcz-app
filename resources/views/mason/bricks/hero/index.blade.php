<section class="relative flex items-center justify-center min-h-[400px] bg-gray-900 text-white overflow-hidden">
    @if(! empty($background_image))
        <img src="{{ Storage::url($background_image) }}" alt="" class="absolute inset-0 w-full h-full object-cover opacity-50">
    @endif
    <div class="relative z-10 text-center px-6 py-16">
        @if(! empty($title))
            <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ $title }}</h1>
        @endif
        @if(! empty($subtitle))
            <p class="text-xl md:text-2xl text-gray-200 mb-8">{{ $subtitle }}</p>
        @endif
        @if(! empty($cta_text) && ! empty($cta_url))
            <a href="{{ $cta_url }}" class="inline-block bg-white text-gray-900 font-semibold px-8 py-3 rounded-lg hover:bg-gray-100 transition">
                {{ $cta_text }}
            </a>
        @endif
    </div>
</section>
