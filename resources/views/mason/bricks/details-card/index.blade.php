<section class="bg-[#0D0D0D] py-[60px]">
    <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col items-center gap-8">
        {{-- Title --}}
        @if(! empty($title))
            <h2 class="font-display font-bold text-[32px] tracking-wide">{{ brick_trans($title) }}</h2>
        @endif
        @if(! empty($subtitle))
            <p class="text-[#888888] text-center">{{ brick_trans($subtitle) }}</p>
        @endif

        {{-- Card --}}
        @if(! empty($rows))
            <div class="rounded-2xl bg-[#111111] border border-[#222222] p-8 max-w-[700px] w-full flex flex-col gap-4">
                @foreach($rows as $i => $row)
                    <div class="flex justify-between py-4 {{ $i < count($rows) - 1 ? 'border-b border-[#222222]' : '' }}">
                        <span class="text-[#888888]">{{ brick_trans($row['label'] ?? []) }}</span>
                        <span class="{{ ! empty($row['highlight']) ? 'text-bcz-red' : 'text-white' }}">{{ brick_trans($row['value'] ?? []) }}</span>
                    </div>
                @endforeach

                {{-- Copy button --}}
                @if(! empty($show_copy_button))
                    <button
                        onclick="navigator.clipboard.writeText(this.dataset.copy).then(() => { this.querySelector('.copy-label').textContent = '{{ __("Skopírované!") }}'; setTimeout(() => this.querySelector('.copy-label').textContent = '{{ __("Kopírovať všetky údaje") }}', 2000) })"
                        data-copy="{{ collect($rows)->map(fn($r) => brick_trans($r['label'] ?? []) . ': ' . brick_trans($r['value'] ?? []))->implode("\n") }}"
                        class="w-full bg-[#222222] rounded-lg py-3.5 text-center text-white text-sm font-semibold flex items-center justify-center gap-2 hover:bg-[#333333] transition-colors"
                    >
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9.75a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184" />
                        </svg>
                        <span class="copy-label">Kopírovať všetky údaje</span>
                    </button>
                @endif
            </div>
        @endif
    </div>
</section>
