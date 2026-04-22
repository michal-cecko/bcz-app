<section class="bg-[#0D0D0D] pb-[60px]">
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
 <div class="bg-[#111111] border border-[#222222] p-8 max-w-[700px] w-full flex flex-col">
 @foreach($rows as $i => $row)
 <div class="flex items-center justify-between py-4 {{ $i < count($rows) - 1 ? 'border-b border-bcz-border' : '' }}">
 <span class="text-[#888888] text-sm">{{ brick_trans($row['label'] ?? []) }}</span>
 <button
 type="button"
 x-data="{ copied: false }"
 x-on:click="
 const text = @js(brick_trans($row['value'] ?? []));
 const onSuccess = () => {
 copied = true;
 setTimeout(() => { copied = false; }, 2000);
 };
 if (navigator.clipboard && navigator.clipboard.writeText) {
 navigator.clipboard.writeText(text).then(onSuccess);
 } else {
 const ta = document.createElement('textarea');
 ta.value = text;
 ta.style.position = 'fixed';
 ta.style.opacity = '0';
 document.body.appendChild(ta);
 ta.select();
 document.execCommand('copy');
 document.body.removeChild(ta);
 onSuccess();
 }"
 class="relative flex items-center gap-3 group cursor-pointer"
 >
 <span class="text-sm font-semibold {{ ! empty($row['highlight']) ? 'text-bcz-red' : 'text-white' }}">{{ brick_trans($row['value'] ?? []) }}</span>
 <span
 class="absolute -top-8 right-0 text-white text-xs px-2 py-1 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"
 :class="copied ? 'bg-green-600' : 'bg-[#333333]'"
 x-text="copied ? '{{ __('Skopírované!') }}' : '{{ __('Kopírovať') }}'"
 ></span>
 <svg class="w-4 h-4 text-bcz-subtle group-hover:text-white transition-colors shrink-0"xmlns="http://www.w3.org/2000/svg"fill="none"viewBox="0 0 24 24"stroke-width="1.5"stroke="currentColor">
 <path stroke-linecap="round"stroke-linejoin="round"d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9.75a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184"/>
 </svg>
 </button>
 </div>
 @endforeach
 </div>
 @endif
 </div>
</section>
