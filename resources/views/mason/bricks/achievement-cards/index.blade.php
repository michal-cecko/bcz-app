<section class="bg-[#111111] py-20">
 <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col gap-12">
 {{-- Header --}}
 <div class="flex flex-col gap-4">
 @if(! empty($label))
 <div class="flex items-center gap-3">
 <div class="w-8 h-[3px] bg-bcz-red"></div>
 <span class="text-bcz-red text-xs font-bold tracking-[3px]">{{ brick_trans($label) }}</span>
 </div>
 @endif

 @if(! empty($title))
 <h2 class="font-display font-bold text-[24px] md:text-[36px] lg:text-[48px] tracking-wide">{{ brick_trans($title) }}</h2>
 @endif

 @if(! empty($description))
 <p class="text-[#888888] text-base leading-[1.6] max-w-[700px]">{{ brick_trans($description) }}</p>
 @endif
 </div>

 {{-- Cards --}}
 @if(! empty($cards))
 @foreach(array_chunk($cards, 3) as $row)
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
 @foreach($row as $card)
 <div class="bg-bcz-dark border border-[#222222] p-6 flex flex-col gap-4">
 @if(! empty($card['year']))
 <span class="text-bcz-red text-xs font-semibold">{{ $card['year'] }}</span>
 @endif

 @if(! empty($card['title']))
 <h3 class="font-display font-bold text-[32px] tracking-wide">{{ brick_trans($card['title']) }}</h3>
 @endif

 @if(! empty($card['description']))
 <p class="text-[#888888] text-[13px] leading-[1.6]">{!! brick_trans($card['description']) !!}</p>
 @endif

 @if(! empty($card['badge_text']))
 @php
 $badgeType = $card['badge_type'] ?? 'gold';
 $badgeBg = match($badgeType) {
 'gold' => 'bg-[#FFD70020]',
 default => 'bg-[#C0C0C020]',
 };
 $badgeText = match($badgeType) {
 'gold' => 'text-[#FFD700]',
 default => 'text-[#C0C0C0]',
 };
 @endphp
 <div class="{{ $badgeBg }} px-3 py-1 w-fit">
 <span class="{{ $badgeText }} text-[11px] font-bold tracking-wider">{{ brick_trans($card['badge_text']) }}</span>
 </div>
 @endif
 </div>
 @endforeach
 </div>
 @endforeach
 @endif
 </div>
</section>
