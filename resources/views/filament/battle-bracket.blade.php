<div
    x-data="{
        roundId: @js($roundId),
        teamSize: @js($teamSize),
        competitors: @js($competitors),
        battles: @js($battles),
        savingCount: 0,

        addBattle() {
            this.battles.push({
                id: null,
                bracket: this.battles.length + 1,
                sideA: Array(this.teamSize).fill(null),
                sideB: Array(this.teamSize).fill(null),
            });
            this.save();
        },
        removeBattle(index) {
            this.battles.splice(index, 1);
            this.save();
        },
        moveUp(index) {
            if (index <= 0) return;
            const item = this.battles.splice(index, 1)[0];
            this.battles.splice(index - 1, 0, item);
            this.save();
        },
        moveDown(index) {
            if (index >= this.battles.length - 1) return;
            const item = this.battles.splice(index, 1)[0];
            this.battles.splice(index + 1, 0, item);
            this.save();
        },
        updateSlot(battleIndex, side, slotIndex, value) {
            const arr = this.battles[battleIndex][side === 'a' ? 'sideA' : 'sideB'];
            arr[slotIndex] = value || null;
            this.save();
        },
        ensureSideArray(battle, key) {
            if (!Array.isArray(battle[key])) {
                battle[key] = [];
            }
            while (battle[key].length < this.teamSize) battle[key].push(null);
            while (battle[key].length > this.teamSize) battle[key].pop();
            return battle[key];
        },
        isUsed(id, excludeBattle, excludeSide, excludeSlot) {
            if (!id) return false;
            return this.battles.some((b, i) => {
                const sides = ['sideA', 'sideB'];
                return sides.some((key) => {
                    const sideLetter = key === 'sideA' ? 'a' : 'b';
                    return (b[key] || []).some((uid, slot) => {
                        if (i === excludeBattle && sideLetter === excludeSide && slot === excludeSlot) return false;
                        return uid === id;
                    });
                });
            });
        },
        async save() {
            this.savingCount++;
            try {
                const payload = this.battles.map(b => ({
                    id: b.id,
                    bracket: b.bracket,
                    sideA: this.ensureSideArray(b, 'sideA').filter(x => x !== null),
                    sideB: this.ensureSideArray(b, 'sideB').filter(x => x !== null),
                }));
                await this.$wire.call('persistBracket', this.roundId, JSON.parse(JSON.stringify(payload)));
            } catch (e) {
                console.error('Save failed:', e);
            } finally {
                this.savingCount--;
            }
        },
        handleRoundBattlesRefreshed(event) {
            const detail = event?.detail ?? {};
            const payload = Array.isArray(detail) ? detail[0] : detail;
            if (!payload || payload.roundId !== this.roundId) return;
            const bracket = payload.bracket;
            if (!bracket || !Array.isArray(bracket.battles)) return;
            this.battles = bracket.battles.map(b => ({
                id: b.id,
                bracket: b.bracket,
                sideA: Array.isArray(b.sideA) ? [...b.sideA] : [],
                sideB: Array.isArray(b.sideB) ? [...b.sideB] : [],
            }));
            this.battles.forEach(b => { this.ensureSideArray(b, 'sideA'); this.ensureSideArray(b, 'sideB'); });
        },
    }"
    x-init="battles.forEach(b => { ensureSideArray(b, 'sideA'); ensureSideArray(b, 'sideB'); })"
    x-on:round-battles-refreshed.window="handleRoundBattlesRefreshed($event)"
    wire:ignore
    class="space-y-2"
>
    <div class="flex items-center justify-between">
        <p class="text-xs text-gray-500 dark:text-gray-400">
            Nastavte poradie a zloženie jednotlivých battle. Zmeny sa ukladajú automaticky.
        </p>
        <div x-show="savingCount > 0" x-transition class="text-xs text-primary-500 flex items-center gap-1">
            <svg class="h-3 w-3 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            Ukladá sa...
        </div>
    </div>

    <div class="rounded-lg border border-gray-200 dark:border-gray-700 divide-y divide-gray-100 dark:divide-gray-800">
        <template x-for="(battle, idx) in battles" :key="idx">
            <div class="flex items-start gap-3 px-3 py-2.5 text-sm hover:bg-gray-50 dark:hover:bg-gray-800/50">
                {{-- Position --}}
                <span class="w-5 text-center text-gray-400 tabular-nums font-medium shrink-0 pt-1.5" x-text="idx + 1"></span>

                {{-- Side A (multi-slot) --}}
                <div class="flex-1 space-y-1">
                    <template x-for="slot in teamSize" :key="`a-${idx}-${slot - 1}`">
                        <select
                            :value="(battle.sideA || [])[slot - 1] ?? ''"
                            @change="updateSlot(idx, 'a', slot - 1, $event.target.value)"
                            class="w-full rounded border border-gray-300 bg-transparent py-1.5 pl-2 pr-6 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600"
                        >
                            <option value="">-- Vyberte --</option>
                            <template x-for="c in competitors" :key="c.id">
                                <option
                                    :value="c.id"
                                    :selected="((battle.sideA || [])[slot - 1]) === c.id"
                                    x-text="c.name"
                                    :class="isUsed(c.id, idx, 'a', slot - 1) && ((battle.sideA || [])[slot - 1]) !== c.id ? 'text-gray-400' : ''"
                                ></option>
                            </template>
                        </select>
                    </template>
                </div>

                <span class="text-gray-400 font-medium shrink-0 pt-1.5">vs</span>

                {{-- Side B (multi-slot) --}}
                <div class="flex-1 space-y-1">
                    <template x-for="slot in teamSize" :key="`b-${idx}-${slot - 1}`">
                        <select
                            :value="(battle.sideB || [])[slot - 1] ?? ''"
                            @change="updateSlot(idx, 'b', slot - 1, $event.target.value)"
                            class="w-full rounded border border-gray-300 bg-transparent py-1.5 pl-2 pr-6 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600"
                        >
                            <option value="">-- Vyberte --</option>
                            <template x-for="c in competitors" :key="c.id">
                                <option
                                    :value="c.id"
                                    :selected="((battle.sideB || [])[slot - 1]) === c.id"
                                    x-text="c.name"
                                    :class="isUsed(c.id, idx, 'b', slot - 1) && ((battle.sideB || [])[slot - 1]) !== c.id ? 'text-gray-400' : ''"
                                ></option>
                            </template>
                        </select>
                    </template>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-0.5 shrink-0 pt-1">
                    <button type="button" @click="moveUp(idx)" :disabled="idx === 0"
                        class="p-1 rounded text-gray-400 hover:text-gray-700 hover:bg-gray-100 dark:hover:text-gray-200 dark:hover:bg-gray-700 disabled:opacity-25 disabled:cursor-not-allowed" title="Hore">
                        <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" /></svg>
                    </button>
                    <button type="button" @click="moveDown(idx)" :disabled="idx === battles.length - 1"
                        class="p-1 rounded text-gray-400 hover:text-gray-700 hover:bg-gray-100 dark:hover:text-gray-200 dark:hover:bg-gray-700 disabled:opacity-25 disabled:cursor-not-allowed" title="Dole">
                        <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                    </button>
                    <button type="button" @click="removeBattle(idx)"
                        class="p-1 rounded text-red-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30" title="Odstrániť">
                        <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
        </template>
    </div>

    <div x-show="battles.length === 0" class="p-4 text-center text-xs text-gray-500 dark:text-gray-400">
        Žiadne battle. Pridajte prvý battle.
    </div>

    <button
        type="button"
        @click="addBattle()"
        class="flex items-center gap-1 rounded-lg border border-dashed border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-500 hover:border-primary-500 hover:text-primary-500 dark:border-gray-600 dark:hover:border-primary-500"
    >
        <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Pridať battle
    </button>
</div>
