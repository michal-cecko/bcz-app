<div>
    @if($submitted)
        {{-- Success state --}}
        <div class="rounded-xl bg-[#0A0A0A] border border-green-800 p-8 text-center flex flex-col items-center gap-4">
            <div class="w-16 h-16 rounded-full bg-green-900/50 flex items-center justify-center">
                <svg class="w-8 h-8 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-white">Dopyt bol odoslaný</h3>
            <p class="text-[#CCCCCC]">Ďakujeme, ozveme sa vám do 24 hodín.</p>
        </div>
    @else
        <form wire:submit="submit" class="flex flex-col gap-6">
            {{-- Row 1: Name + Email --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-2">
                    <label class="text-white text-sm font-medium">Meno a priezvisko</label>
                    <input wire:model="name" type="text" placeholder="Vaše meno"
                        class="bg-[#0A0A0A] border border-[#333333] rounded-lg px-4 py-3 text-white text-sm placeholder-[#666666] w-full focus:border-bcz-red focus:ring-0 focus:outline-none">
                    @error('name') <p class="text-bcz-red text-xs">{{ $message }}</p> @enderror
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-white text-sm font-medium">Email</label>
                    <input wire:model="email" type="email" placeholder="vas@email.com"
                        class="bg-[#0A0A0A] border border-[#333333] rounded-lg px-4 py-3 text-white text-sm placeholder-[#666666] w-full focus:border-bcz-red focus:ring-0 focus:outline-none">
                    @error('email') <p class="text-bcz-red text-xs">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Row 2: Phone + Service type --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-2">
                    <label class="text-white text-sm font-medium">Telefón</label>
                    <input wire:model="phone" type="tel" placeholder="+421"
                        class="bg-[#0A0A0A] border border-[#333333] rounded-lg px-4 py-3 text-white text-sm placeholder-[#666666] w-full focus:border-bcz-red focus:ring-0 focus:outline-none">
                    @error('phone') <p class="text-bcz-red text-xs">{{ $message }}</p> @enderror
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-white text-sm font-medium">Typ služby</label>
                    <select wire:model="reason"
                        class="bg-[#0A0A0A] border border-[#333333] rounded-lg px-4 py-3 text-white text-sm w-full focus:border-bcz-red focus:ring-0 focus:outline-none appearance-none">
                        <option value="">Vyberte typ služby</option>
                        @foreach($reasons as $reasonOption)
                            <option value="{{ $reasonOption->value }}">{{ $reasonOption->getLabel() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Message --}}
            <div class="flex flex-col gap-2">
                <label class="text-white text-sm font-medium">Správa</label>
                <textarea wire:model="message" rows="4" placeholder="Popíšte váš event, požiadavky a termín..."
                    class="bg-[#0A0A0A] border border-[#333333] rounded-lg px-4 py-3 text-white text-sm placeholder-[#666666] w-full resize-none focus:border-bcz-red focus:ring-0 focus:outline-none"></textarea>
                @error('message') <p class="text-bcz-red text-xs">{{ $message }}</p> @enderror
            </div>

            {{-- Submit --}}
            <button type="submit" wire:loading.attr="disabled" wire:target="submit"
                class="bg-bcz-red text-white rounded-lg px-8 py-3.5 font-semibold flex items-center justify-center gap-2 w-full hover:bg-red-700 transition disabled:opacity-50">
                <span wire:loading.remove wire:target="submit">Odoslať dopyt</span>
                <span wire:loading.remove wire:target="submit">&rarr;</span>
                <span wire:loading wire:target="submit">Odosielam...</span>
            </button>
        </form>
    @endif
</div>
