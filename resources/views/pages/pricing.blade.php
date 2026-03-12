@extends('layouts.public')
@section('title', $page?->getTranslation('title', app()->getLocale()) ?? 'Cenník')

@section('content')
@php
    $locale = app()->getLocale();
    $currencySymbol = $currency === 'CZK' ? 'Kč' : '€';
    $tierHighlight = ['free' => true, 'pro' => true]; // red border + glow
@endphp

{{-- Hero --}}
<section class="bg-bcz-dark px-5 md:px-10 lg:px-20 pt-20 pb-10 lg:pt-28 lg:pb-16">
    <div class="max-w-[1440px] mx-auto flex flex-col items-center gap-8 text-center">
        <span class="inline-block bg-bcz-red/[0.12] text-bcz-red text-[11px] font-bold tracking-[2px] px-5 py-2 rounded-full">
            {{ ['sk' => 'CENNÍK', 'en' => 'PRICING', 'cs' => 'CENÍK'][$locale] ?? 'CENNÍK' }}
        </span>
        <h1 class="font-thunder text-4xl md:text-5xl lg:text-[56px] font-bold text-white leading-none">
            {{ ['sk' => 'Vyberte si plán pre váš klub', 'en' => 'Choose a plan for your club', 'cs' => 'Vyberte si plán pro váš klub'][$locale] ?? 'Vyberte si plán pre váš klub' }}
        </h1>
        <p class="text-bcz-muted text-base md:text-lg max-w-xl">
            {{ ['sk' => 'Jednoduché a transparentné ceny. Vyskúšajte 2 mesiace zadarmo.', 'en' => 'Simple and transparent pricing. Try 2 months for free.', 'cs' => 'Jednoduché a transparentní ceny. Vyzkoušejte 2 měsíce zdarma.'][$locale] ?? 'Jednoduché a transparentné ceny. Vyskúšajte 2 mesiace zadarmo.' }}
        </p>
        {{-- Billing toggle --}}
        <div x-data="{ yearly: false }" x-ref="billingToggle" class="flex flex-col items-center gap-6 w-full">
            <div class="inline-flex bg-[#111111] border border-[#222222] rounded-full p-1">
                <button @click="yearly = false" :class="!yearly ? 'bg-bcz-red text-white' : 'text-bcz-muted'" class="px-6 py-2.5 rounded-full text-sm font-medium transition-all cursor-pointer">
                    {{ ['sk' => 'Mesačne', 'en' => 'Monthly', 'cs' => 'Měsíčně'][$locale] ?? 'Mesačne' }}
                </button>
                <button @click="yearly = true" :class="yearly ? 'bg-bcz-red text-white' : 'text-bcz-muted'" class="px-6 py-2.5 rounded-full text-sm font-medium transition-all cursor-pointer">
                    {{ ['sk' => 'Ročne', 'en' => 'Yearly', 'cs' => 'Ročně'][$locale] ?? 'Ročne' }}
                    <span class="text-[10px] ml-1 opacity-70">-17%</span>
                </button>
            </div>

            {{-- Pricing Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 w-full max-w-[1440px] mt-4">
                @foreach($plans as $plan)
                    @php
                        $tier = $plan->tier->value;
                        $isHighlighted = in_array($tier, ['free', 'pro']);
                        $price = $plan->prices->firstWhere('currency_code', $currency);
                        $monthly = $price?->price_monthly ?? 0;
                        $yearly = $price?->price_yearly ?? 0;
                        $features = $plan->getTranslation('features', $locale) ?: $plan->getTranslation('features', 'sk');
                        $desc = $plan->getTranslation('description', $locale) ?: $plan->getTranslation('description', 'sk');
                        $name = $plan->getTranslation('name', $locale) ?: $plan->getTranslation('name', 'sk');
                        $badgeLabels = [
                            'free' => ['sk' => '2 MESIACE ZADARMO', 'en' => '2 MONTHS FREE', 'cs' => '2 MĚSÍCE ZDARMA'],
                            'starter' => ['sk' => 'Pre malé kluby', 'en' => 'For small clubs', 'cs' => 'Pro malé kluby'],
                            'pro' => ['sk' => 'Najobľúbenejší', 'en' => 'Most popular', 'cs' => 'Nejoblíbenější'],
                            'enterprise' => ['sk' => 'Plná sila', 'en' => 'Full power', 'cs' => 'Plná síla'],
                        ];
                        $badge = $badgeLabels[$tier][$locale] ?? $badgeLabels[$tier]['sk'] ?? '';
                        $btnLabels = [
                            'free' => ['sk' => 'Vyskúšať zadarmo', 'en' => 'Try for free', 'cs' => 'Vyzkoušet zdarma'],
                            'starter' => ['sk' => 'Vybrať Starter', 'en' => 'Choose Starter', 'cs' => 'Vybrat Starter'],
                            'pro' => ['sk' => 'Vybrať Pro', 'en' => 'Choose Pro', 'cs' => 'Vybrat Pro'],
                            'enterprise' => ['sk' => 'Kontaktujte nás', 'en' => 'Contact us', 'cs' => 'Kontaktujte nás'],
                        ];
                        $btnText = $btnLabels[$tier][$locale] ?? $btnLabels[$tier]['sk'] ?? '';
                    @endphp
                    <div class="flex flex-col gap-6 p-8 rounded-2xl {{ $isHighlighted ? 'bg-bcz-dark border-2 border-bcz-red shadow-[0_0_30px_rgba(255,45,45,0.2)]' : 'bg-[#111111] border border-[#222222]' }}">
                        {{-- Badge --}}
                        <span class="self-start text-[11px] font-semibold tracking-[1px] px-3.5 py-1.5 rounded-full {{ $isHighlighted ? 'bg-bcz-red text-white' : 'bg-bcz-red/[0.12] text-bcz-red' }}">
                            {{ $badge }}
                        </span>

                        {{-- Name --}}
                        <span class="font-thunder text-[28px] font-bold tracking-[2px] {{ $isHighlighted ? 'text-bcz-red' : 'text-white' }}">
                            {{ strtoupper($name) }}
                        </span>

                        {{-- Price --}}
                        <div class="flex items-end gap-1">
                            <span class="font-thunder text-5xl font-bold text-white" x-text="yearly ? '{{ number_format($yearly, 0) }}' : '{{ number_format($monthly, 0) }}'">{{ number_format($monthly, 0) }}</span>
                            <span class="text-bcz-dim text-sm font-medium mb-1.5" x-text="yearly ? '{{ $currencySymbol }}/{{ ['sk' => 'rok', 'en' => 'year', 'cs' => 'rok'][$locale] ?? 'rok' }}' : '{{ $currencySymbol }}/{{ ['sk' => 'mesiac', 'en' => 'month', 'cs' => 'měsíc'][$locale] ?? 'mesiac' }}'">{{ $currencySymbol }}/{{ ['sk' => 'mesiac', 'en' => 'month', 'cs' => 'měsíc'][$locale] ?? 'mesiac' }}</span>
                        </div>

                        @if($monthly > 0)
                            <p class="text-bcz-dim text-xs -mt-4" x-show="!yearly">
                                {{ ['sk' => 'alebo', 'en' => 'or', 'cs' => 'nebo'][$locale] ?? 'alebo' }} {{ number_format($yearly, 0) }} {{ $currencySymbol }}/{{ ['sk' => 'rok', 'en' => 'year', 'cs' => 'rok'][$locale] ?? 'rok' }} ({{ ['sk' => 'ušetríte', 'en' => 'save', 'cs' => 'ušetříte'][$locale] ?? 'ušetríte' }} ~17%)
                            </p>
                            <p class="text-bcz-dim text-xs -mt-4" x-show="yearly" x-cloak>
                                {{ ['sk' => 'alebo', 'en' => 'or', 'cs' => 'nebo'][$locale] ?? 'alebo' }} {{ number_format($monthly, 0) }} {{ $currencySymbol }}/{{ ['sk' => 'mesiac', 'en' => 'month', 'cs' => 'měsíc'][$locale] ?? 'mesiac' }}
                            </p>
                        @endif

                        {{-- Description --}}
                        <p class="text-bcz-muted text-sm">{{ $desc }}</p>

                        <div class="w-full h-px {{ $isHighlighted ? 'bg-[#333333]' : 'bg-[#222222]' }}"></div>

                        {{-- Features --}}
                        <ul class="flex flex-col gap-3.5 flex-1">
                            @foreach($features as $feature)
                                <li class="flex items-center gap-2.5 text-[#AAAAAA] text-[13px]">
                                    <svg class="w-4 h-4 text-bcz-red shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    {{ $feature }}
                                </li>
                            @endforeach
                        </ul>

                        {{-- CTA Button --}}
                        <a href="/admin" class="flex items-center justify-center py-3.5 rounded-[10px] text-sm font-bold text-white transition-colors {{ $isHighlighted ? 'bg-bcz-red hover:bg-red-700' : 'border border-[#333333] hover:border-[#555555]' }}">
                            {{ $btnText }}
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- Mason brick content (Why BCZ, FAQ, CTA etc.) --}}
@if($renderedContent)
    {!! $renderedContent !!}
@endif
@endsection
