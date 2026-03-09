@props(['team'])

<a href="{{ route('team.show', $team) }}"
   class="inline-flex items-center gap-1.5 bg-[#1A1A1A] border border-[#333] rounded-full px-2.5 py-1 text-[11px] text-bcz-muted hover:text-white transition-colors">
    @if($team->getFilamentAvatarUrl())
        <img src="{{ $team->getFilamentAvatarUrl() }}" alt="" class="w-4 h-4 rounded-full object-cover">
    @else
        <span class="w-4 h-4 rounded-full bg-bcz-red flex items-center justify-center text-[8px] font-bold text-white">
            {{ mb_substr($team->getTranslation('name', 'sk'), 0, 1) }}
        </span>
    @endif
    {{ $team->getTranslation('name', app()->getLocale()) }}
</a>
