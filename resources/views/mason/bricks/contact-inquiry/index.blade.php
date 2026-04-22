@php
 $label = brick_trans($label ?? []);
 $title = brick_trans($title ?? []);
 $description = brick_trans($description ?? []);
 $email = $contact_email ?? '';
 $phone = $contact_phone ?? '';
@endphp

<section class="bg-[#0D0D0D] py-[100px]">
 <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
 <div class="flex flex-col lg:flex-row lg:items-start gap-16">
 {{-- Left column --}}
 <div class="lg:w-[38%] shrink-0 flex flex-col gap-8">
 @if($label)
 <div class="flex items-center gap-3">
 <div class="w-8 h-[2px] bg-bcz-red"></div>
 <span class="text-bcz-red text-sm font-semibold uppercase tracking-widest">{{ $label }}</span>
 </div>
 @endif

 @if($title)
 <h2 class="font-display font-bold text-[42px] leading-tight tracking-wide text-white">{{ $title }}</h2>
 @endif

 @if($description)
 <p class="text-[#888888] text-base leading-relaxed">{{ $description }}</p>
 @endif

 @if($email || $phone)
 <div class="flex flex-col gap-4">
 @if($email)
 <div class="flex items-center gap-3">
 <div class="w-10 h-10 bg-[#FF2D2D20] flex items-center justify-center">
 <svg class="w-5 h-5 text-bcz-red"fill="none"viewBox="0 0 24 24"stroke-width="1.5"stroke="currentColor">
 <path stroke-linecap="round"stroke-linejoin="round"d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
 </svg>
 </div>
 <a href="mailto:{{ $email }}"class="text-white hover:text-bcz-red transition">{{ $email }}</a>
 </div>
 @endif

 @if($phone)
 <div class="flex items-center gap-3">
 <div class="w-10 h-10 bg-[#FF2D2D20] flex items-center justify-center">
 <svg class="w-5 h-5 text-bcz-red"fill="none"viewBox="0 0 24 24"stroke-width="1.5"stroke="currentColor">
 <path stroke-linecap="round"stroke-linejoin="round"d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/>
 </svg>
 </div>
 <a href="tel:{{ $phone }}"class="text-white hover:text-bcz-red transition">{{ $phone }}</a>
 </div>
 @endif
 </div>
 @endif
 </div>

 {{-- Right column --}}
 <div class="flex-1 lg:ml-auto lg:max-w-[58%]">
 <div class="bg-[#111111] border border-[#222222] p-8">
 <livewire:inquiry-form />
 </div>
 </div>
 </div>
 </div>
</section>
