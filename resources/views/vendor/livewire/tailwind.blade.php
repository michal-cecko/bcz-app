@php
if (! isset($scrollTo)) {
    $scrollTo = 'body';
}

$scrollIntoViewJsSnippet = ($scrollTo !== false)
    ? <<<JS
       (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
    JS
    : '';
@endphp

<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between pt-6">
            <p class="text-sm text-[#888888]">
                {{ __('archive.pagination_showing') }}
                <span class="font-semibold">{{ $paginator->firstItem() }}-{{ $paginator->lastItem() }}</span>
                {{ __('archive.pagination_from') }}
                <span class="font-semibold">{{ $paginator->total() }}</span>
                {{ __('archive.pagination_results') }}
            </p>

            <div class="flex items-center gap-2">
                {{-- First Page --}}
                @if ($paginator->onFirstPage())
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-none bg-[#111111] border border-[#333333] opacity-50 cursor-not-allowed">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#AAAAAA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="11 17 6 12 11 7"></polyline><polyline points="18 17 13 12 18 7"></polyline></svg>
                    </span>
                @else
                    <button type="button" wire:click="gotoPage(1)" wire:loading.attr="disabled" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="inline-flex items-center justify-center w-10 h-10 rounded-none bg-[#111111] border border-[#333333] hover:border-[#555555] transition-colors cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#AAAAAA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="11 17 6 12 11 7"></polyline><polyline points="18 17 13 12 18 7"></polyline></svg>
                    </button>
                @endif

                {{-- Previous Page --}}
                @if ($paginator->onFirstPage())
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-none bg-[#111111] border border-[#333333] opacity-50 cursor-not-allowed">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#AAAAAA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    </span>
                @else
                    <button type="button" wire:click="previousPage" wire:loading.attr="disabled" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="inline-flex items-center justify-center w-10 h-10 rounded-none bg-[#111111] border border-[#333333] hover:border-[#555555] transition-colors cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#AAAAAA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    </button>
                @endif

                {{-- Page Numbers --}}
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-none bg-[#111111] border border-[#333333] text-sm text-[#AAAAAA]">...</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="inline-flex items-center justify-center w-10 h-10 rounded-none bg-[#FF2D2D] text-sm font-semibold text-white">{{ $page }}</span>
                            @else
                                <button type="button" wire:click="gotoPage({{ $page }})" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="inline-flex items-center justify-center w-10 h-10 rounded-none bg-[#111111] border border-[#333333] hover:border-[#555555] text-sm text-[#AAAAAA] transition-colors cursor-pointer">{{ $page }}</button>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page --}}
                @if ($paginator->hasMorePages())
                    <button type="button" wire:click="nextPage" wire:loading.attr="disabled" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="inline-flex items-center justify-center w-10 h-10 rounded-none bg-[#111111] border border-[#333333] hover:border-[#555555] transition-colors cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#AAAAAA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </button>
                @else
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-none bg-[#111111] border border-[#333333] opacity-50 cursor-not-allowed">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#AAAAAA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </span>
                @endif

                {{-- Last Page --}}
                @if ($paginator->hasMorePages())
                    <button type="button" wire:click="gotoPage({{ $paginator->lastPage() }})" wire:loading.attr="disabled" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="inline-flex items-center justify-center w-10 h-10 rounded-none bg-[#111111] border border-[#333333] hover:border-[#555555] transition-colors cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#AAAAAA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="13 17 18 12 13 7"></polyline><polyline points="6 17 11 12 6 7"></polyline></svg>
                    </button>
                @else
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-none bg-[#111111] border border-[#333333] opacity-50 cursor-not-allowed">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#AAAAAA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="13 17 18 12 13 7"></polyline><polyline points="6 17 11 12 6 7"></polyline></svg>
                    </span>
                @endif
            </div>
        </nav>
    @endif
</div>
