<blockquote class="border-l-4 border-bcz-red pl-6 py-2 bg-[#111111] p-6">
 @if(! empty($quote))
 <div class="text-lg italic text-white leading-relaxed">{!! brick_trans($quote) !!}</div>
 @endif
 @if(! empty($attribution))
 <footer class="mt-3 text-sm text-[#888888]">{{ brick_trans($attribution) }}</footer>
 @endif
</blockquote>
