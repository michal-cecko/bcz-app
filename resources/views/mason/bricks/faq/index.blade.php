@if(isset($faqs) && $faqs->count())
    @php
        $categories = $faqs->pluck('faqCategory')->filter()->unique('id');
        $faqLinkHref = brick_link(['link_type' => $link_link_type ?? '', 'link_model_id' => $link_link_model_id ?? '', 'link_url' => $link_link_url ?? '']);
        $count = $faqs->count();
        $countLabel = match (true) {
            $count === 1 => '1 otázka',
            $count >= 2 && $count <= 4 => $count . ' otázky',
            default => $count . ' otázok',
        };
    @endphp
    <section class="bg-[#111111] py-20">
        <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col gap-12">
            {{-- Header --}}
            @if(! empty($heading))
                <div class="flex items-center gap-4">
                    <div class="rounded-xl bg-[#FF2D2D20] w-12 h-12 flex items-center justify-center text-xl">
                        ❓
                    </div>
                    <h2 class="font-display font-bold text-[24px] tracking-wide">{{ brick_trans($heading) }}</h2>
                    <span class="bg-[#222222] rounded-full px-3 py-1 text-[#888888] text-xs">{{ $countLabel }}</span>
                </div>
            @endif

            {{-- Category filter + Accordion (hidden in preview mode when limit is set) --}}
            @if($categories->count() > 1 && empty($limit))
                <div x-data="{ filter: 'all', active: null }" class="flex flex-col gap-8">
                    <div class="flex flex-wrap gap-3">
                        <button @click="filter = 'all'; active = null" :class="filter === 'all' ? 'bg-bcz-red text-white' : 'bg-[#222222] text-[#888888] hover:text-white'" class="rounded-lg px-4 py-2 text-sm font-semibold transition">Všetky</button>
                        @foreach($categories as $cat)
                            <button @click="filter = '{{ $cat->id }}'; active = null" :class="filter === '{{ $cat->id }}' ? 'bg-bcz-red text-white' : 'bg-[#222222] text-[#888888] hover:text-white'" class="rounded-lg px-4 py-2 text-sm font-semibold transition">{{ $cat->getTranslation('title', app()->getLocale()) }}</button>
                        @endforeach
                    </div>
                    <div class="flex flex-col gap-4">
                        @foreach($faqs as $index => $faq)
                            <div x-show="filter === 'all'{{ $faq->faqCategory ? " || filter === '{$faq->faqCategory->id}'" : '' }}" class="rounded-xl bg-[#0A0A0A] border border-[#222222] overflow-hidden">
                                <button
                                    @click="active = active === {{ $index }} ? null : {{ $index }}"
                                    class="w-full flex justify-between items-center p-6 text-left"
                                >
                                    <span class="text-white font-semibold">{{ $faq->getTranslation('question', app()->getLocale()) }}</span>
                                    <span class="text-[#666666] text-2xl ml-4 shrink-0" x-text="active === {{ $index }} ? '−' : '+'"></span>
                                </button>
                                <div x-show="active === {{ $index }}" x-cloak class="px-6 pb-6">
                                    <div class="prose prose-sm prose-invert max-w-none text-[#CCCCCC]">
                                        {!! $faq->getTranslation('answer', app()->getLocale()) !!}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                {{-- No categories or single category --}}
                <div x-data="{ active: null }" class="flex flex-col gap-4">
                    @foreach($faqs as $index => $faq)
                        <div class="rounded-xl bg-[#0A0A0A] border border-[#222222] overflow-hidden">
                            <button
                                @click="active = active === {{ $index }} ? null : {{ $index }}"
                                class="w-full flex justify-between items-center p-6 text-left"
                            >
                                <span class="text-white font-semibold">{{ $faq->getTranslation('question', app()->getLocale()) }}</span>
                                <span class="text-[#666666] text-2xl ml-4 shrink-0" x-text="active === {{ $index }} ? '−' : '+'"></span>
                            </button>
                            <div x-show="active === {{ $index }}" x-cloak class="px-6 pb-6">
                                <div class="prose prose-sm prose-invert max-w-none text-[#CCCCCC]">
                                    {!! $faq->getTranslation('answer', app()->getLocale()) !!}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Show all link --}}
            @if($faqLinkHref)
                <div class="flex justify-center items-center gap-2 pt-6 border-t border-[#222222]">
                    <a href="{{ $faqLinkHref }}" class="text-white font-semibold text-[15px] flex items-center gap-2 hover:text-bcz-red transition">
                        {{ brick_trans($link_text ?? []) ?: 'Zobraziť všetky často kladené otázky' }}
                        <span>&rarr;</span>
                    </a>
                </div>
            @endif
        </div>
    </section>
@endif
