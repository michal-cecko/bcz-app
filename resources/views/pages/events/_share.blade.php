{{-- Share Card --}}
<div class="bg-[#111111] border border-[#222222] p-6 flex flex-col gap-4">
    <h3 class="text-white text-base font-semibold font-sans">Zdieľať</h3>
    <div class="flex items-center gap-3">
        {{-- Facebook --}}
        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
           target="_blank" rel="noopener"
           class="group relative flex items-center justify-center w-10 h-10 bg-[#1A1A1A] hover:bg-[#222222] transition-colors cursor-pointer">
            <span class="absolute -top-9 left-1/2 -translate-x-1/2 px-2 py-1 text-[11px] font-sans whitespace-nowrap bg-[#333333] text-[#CCCCCC] opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">Facebook</span>
            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
        </a>

        {{-- X / Twitter --}}
        <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($title ?? '') }}"
           target="_blank" rel="noopener"
           class="group relative flex items-center justify-center w-10 h-10 bg-[#1A1A1A] hover:bg-[#222222] transition-colors cursor-pointer">
            <span class="absolute -top-9 left-1/2 -translate-x-1/2 px-2 py-1 text-[11px] font-sans whitespace-nowrap bg-[#333333] text-[#CCCCCC] opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">X (Twitter)</span>
            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
        </a>

        {{-- Copy Link --}}
        <button x-data="{ copied: false }"
                x-on:click="
                    const url = window.location.href;
                    const fallback = () => {
                        const ta = document.createElement('textarea');
                        ta.value = url;
                        ta.style.cssText = 'position:fixed;opacity:0';
                        document.body.appendChild(ta);
                        ta.select();
                        document.execCommand('copy');
                        document.body.removeChild(ta);
                    };
                    try {
                        if (navigator.clipboard && window.isSecureContext) {
                            await navigator.clipboard.writeText(url);
                        } else {
                            fallback();
                        }
                    } catch(e) {
                        fallback();
                    }
                    copied = true;
                    setTimeout(() => copied = false, 2000);
                "
                class="group relative flex items-center justify-center w-10 h-10 bg-[#1A1A1A] hover:bg-[#222222] transition-colors cursor-pointer">
            <span x-text="copied ? 'Skopírované!' : 'Kopírovať odkaz'"
                  class="absolute -top-9 left-1/2 -translate-x-1/2 px-2 py-1 text-[11px] font-sans whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"
                  :class="copied ? 'bg-green-600 text-white' : 'bg-[#333333] text-[#CCCCCC]'"
                  x-show="true"></span>
            <svg x-show="!copied" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
            <svg x-show="copied" x-cloak class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        </button>
    </div>
</div>
