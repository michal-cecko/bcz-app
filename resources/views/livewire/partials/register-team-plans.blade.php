<div class="space-y-8">
    <div class="text-center space-y-2">
        <h1 class="font-display text-4xl font-bold text-white tracking-wide">Začínate s plným PRO</h1>
        <p class="text-bcz-muted text-sm">Prvé 2 mesiace úplne zadarmo · Potom si vyberiete plán</p>
    </div>

    {{-- Billing Toggle --}}
    <div class="flex justify-center">
        <div class="flex bg-[#111111] border border-bcz-border rounded-lg">
            <button
                wire:click="$set('billingPeriod', 'monthly')"
                class="px-5 py-2.5 text-xs font-semibold transition-colors {{ $billingPeriod === 'monthly' ? 'bg-bcz-red text-white' : 'text-bcz-dim hover:text-white' }}"
            >
                Mesačne
            </button>
            <button
                wire:click="$set('billingPeriod', 'yearly')"
                class="px-5 py-2.5 text-xs font-medium transition-colors {{ $billingPeriod === 'yearly' ? 'bg-bcz-red text-white' : 'text-bcz-dim hover:text-white' }}"
            >
                Ročne (-17%)
            </button>
        </div>
    </div>

    {{-- Plan Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- PRO Trial Card --}}
        <div class="rounded-xl bg-[#111111] border-2 border-bcz-red p-6 space-y-5 relative overflow-hidden" style="box-shadow: 0 0 40px rgba(255,45,45,0.2)">
            <div class="absolute inset-0 bg-gradient-to-b from-bcz-red/15 to-transparent pointer-events-none"></div>
            <div class="relative space-y-5">
                <span class="inline-block bg-bcz-red/30 text-bcz-red text-[10px] font-bold tracking-widest px-3.5 py-1.5 rounded-xl">AUTOMATICKY AKTIVOVANÉ</span>
                <p class="font-display text-[28px] font-bold text-white tracking-wide">PRO TRIAL</p>
                <p class="text-bcz-red text-[13px] font-semibold">Toto dostanete hneď po registrácii</p>
                <div class="flex items-end gap-1">
                    <span class="font-display text-4xl font-bold text-white">0 €</span>
                    <span class="text-bcz-dim text-sm mb-1">/ 2 mesiace</span>
                </div>
                <div class="space-y-2.5">
                    <div class="flex items-center gap-2"><svg class="w-4 h-4 text-bcz-red flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-bcz-muted text-xs">Plný 30-dňový prístup</span></div>
                    <div class="flex items-center gap-2"><svg class="w-4 h-4 text-bcz-red flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-bcz-muted text-xs">Neobmedzené funkcie & úlohy</span></div>
                    <div class="flex items-center gap-2"><svg class="w-4 h-4 text-bcz-red flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-bcz-muted text-xs">Bez kreditnej karty</span></div>
                    <div class="flex items-center gap-2"><svg class="w-4 h-4 text-bcz-red flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-bcz-muted text-xs">Emailová podpora</span></div>
                    <div class="flex items-center gap-2"><svg class="w-4 h-4 text-bcz-red flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-bcz-muted text-xs">7-dní GAR. Peňa</span></div>
                </div>
                <p class="text-bcz-dim text-[11px] italic">Po 2 mesiacoch si vyberiete plán · Bez záväzkov</p>
            </div>
        </div>

        {{-- Dynamic Plan Cards --}}
        @foreach($this->plans as $plan)
            @php
                $price = $plan->getPriceForCurrency('EUR', $billingPeriod);
                $isSelected = $selectedPlanId === $plan->id;
                $tierLabel = strtoupper($plan->tier->value);
            @endphp
            <div
                wire:click="selectPlan('{{ $plan->id }}')"
                class="rounded-xl bg-[#111111] border {{ $isSelected ? 'border-bcz-red border-2' : 'border-bcz-border' }} p-6 space-y-5 cursor-pointer hover:border-bcz-muted transition-colors"
            >
                <p class="text-bcz-dim text-[10px] font-bold tracking-[1.5px]">PO SKÚŠOBNEJ DOBE</p>
                @if($plan->tier === \App\Enums\PlanTierEnum::PRO)
                    <span class="inline-block bg-bcz-red text-white text-[10px] font-bold tracking-widest px-2.5 py-1 rounded-xl">OBĽÚBENÝ</span>
                @endif
                <p class="font-display text-[28px] font-bold text-white tracking-wide">{{ $tierLabel }}</p>
                <div class="flex items-end gap-1">
                    <span class="font-display text-4xl font-bold text-white">{{ $price ? number_format($price, 0) : '?' }} €</span>
                    <span class="text-bcz-dim text-sm mb-1">/ {{ $billingPeriod === 'monthly' ? 'mes' : 'rok' }}</span>
                </div>
                @if($plan->features)
                    <div class="space-y-2.5">
                        @foreach(array_slice($plan->getTranslation('features', app()->getLocale()) ?? [], 0, 5) as $feature)
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-bcz-red flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span class="text-bcz-muted text-xs">{{ $feature }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Buttons --}}
    <div class="flex items-center justify-center gap-4">
        <button wire:click="previousStep" class="flex items-center gap-2 border border-bcz-border text-bcz-muted text-sm font-medium h-12 px-8 hover:border-bcz-muted transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Späť
        </button>
        <button wire:click="nextStep" class="flex items-center gap-2 bg-bcz-red text-white text-sm font-bold h-12 px-12 hover:bg-red-700 transition-colors">
            Pokračovať s PRO
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </button>
    </div>

    <p class="text-bcz-dim text-xs text-center">Všetci začínajú s PRO · 2 mesiace zadarmo · Žiadna kreditná karta</p>
</div>
