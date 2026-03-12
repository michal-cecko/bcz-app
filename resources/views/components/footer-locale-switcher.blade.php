@php
    $current = app()->getLocale();
    $names = ['sk' => 'Slovenčina', 'cs' => 'Čeština', 'en' => 'English'];
    $currentName = $names[$current] ?? 'Slovenčina';
@endphp
<div x-data="{ open: false }" @click.outside="open = false" class="relative">
    <button @click="open = !open" class="flex items-center gap-1.5 cursor-pointer">
        <svg class="w-3.5 h-3.5 text-[#555555]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
        <span class="text-[#666666] text-xs font-medium">{{ $currentName }}</span>
        <svg class="w-2.5 h-2.5 text-[#444444]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 15 6-6 6 6"/></svg>
    </button>
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        x-cloak
        class="absolute bottom-full mb-2 -left-1 w-[120px] bg-[#111111] border border-[#222222] rounded-lg py-1.5 shadow-[0_-8px_20px_rgba(0,0,0,0.4)] z-50"
    >
        @foreach(['sk', 'cs', 'en'] as $locale)
            @if($locale !== $current)
                <a href="{{ locale_switch_url($locale) }}" class="block px-3.5 py-2.5 text-[#AAAAAA] text-[13px] font-medium hover:text-white hover:bg-[#1A1A1A] transition-colors">
                    {{ $names[$locale] }}
                </a>
            @endif
        @endforeach
    </div>
</div>
