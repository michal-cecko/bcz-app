@props(['model' => 'gdprAgreed', 'withTerms' => false])

@php
    $privacyUrl = url('/ochrana-osobnych-udajov');
    $termsUrl = url('/podmienky-pouzivania');
    $privacyLink = '<a href="' . $privacyUrl . '" target="_blank" class="text-bcz-red hover:underline">' . __('consent.privacy_policy') . '</a>';
    $termsLink = '<a href="' . $termsUrl . '" target="_blank" class="text-bcz-red hover:underline">' . __('consent.terms_of_use') . '</a>';

    $text = $withTerms
        ? __('consent.gdpr_checkbox_and_terms', ['privacy_link' => $privacyLink, 'terms_link' => $termsLink])
        : __('consent.gdpr_checkbox', ['privacy_link' => $privacyLink]);
@endphp

<div class="flex items-start gap-3">
    <input
        type="checkbox"
        wire:model="{{ $model }}"
        id="{{ $model }}"
        class="mt-1 w-4 h-4 rounded-none border-[#333333] bg-bcz-dark text-bcz-red focus:ring-bcz-red focus:ring-offset-0 shrink-0 cursor-pointer"
    >
    <label for="{{ $model }}" class="text-[#888888] text-[13px] leading-[1.6] cursor-pointer">
        {!! $text !!} <span class="text-bcz-red">*</span>
    </label>
</div>
@error($model)
    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
@enderror
