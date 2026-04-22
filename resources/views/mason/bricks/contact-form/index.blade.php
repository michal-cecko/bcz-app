@php $heading = brick_trans($heading ?? []); @endphp
<section class="pb-[60px]">
 <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20">
 @if($heading)
 <h2 class="font-display font-bold text-[28px] tracking-wide mb-8">{{ $heading }}</h2>
 @endif
 <livewire:contact-form
 :show-reason="$show_reason ?? true"
 :show-phone="$show_phone ?? true"
 :contact-email="$contact_email ?? ''"
 :contact-phone="$contact_phone ?? ''"
 :contact-location="$contact_location ?? ''"
 :response-text="$response_text ?? ''"
 />
 </div>
</section>
