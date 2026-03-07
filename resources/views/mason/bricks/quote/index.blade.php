<blockquote class="border-l-4 border-primary-500 pl-6 py-2">
    @if(! empty($quote))
        <p class="text-xl italic text-gray-700">{{ $quote }}</p>
    @endif
    @if(! empty($attribution))
        <footer class="mt-2 text-sm text-gray-500">&mdash; {{ $attribution }}</footer>
    @endif
</blockquote>
