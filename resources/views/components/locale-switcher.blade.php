@php
    $current = app()->getLocale();
    $labels = ['sk' => 'SK', 'cs' => 'CZ', 'en' => 'EN'];
    $currentLabel = $labels[$current] ?? 'SK';
@endphp
<div x-data="{ open: false }" @click.outside="open = false" class="relative">
    <button @click="open = !open" class="flex items-center gap-1.5 cursor-pointer">
        <svg class="w-4 h-4 text-[#666666]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
        <span class="text-white text-xs font-bold tracking-widest">{{ $currentLabel }}</span>
        <svg class="w-3 h-3 text-[#555555]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
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
        class="absolute top-full mt-2 -left-1 w-[70px] bg-[#111111] border border-[#222222] rounded-lg py-1.5 shadow-[0_8px_20px_rgba(0,0,0,0.4)] z-50"
    >
        @foreach(['sk', 'cs', 'en'] as $locale)
            @if($locale !== $current)
                <a href="{{ locale_switch_url($locale) }}" class="block px-3 py-2 text-[#AAAAAA] text-xs font-medium tracking-widest hover:text-white hover:bg-[#1A1A1A] transition-colors">
                    {{ $labels[$locale] }}
                </a>
            @endif
        @endforeach
    </div>
</div>
