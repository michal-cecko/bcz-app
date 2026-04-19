<div
    x-data="{
        roundId: @js($roundId),
        competitors: @js($competitors),
        savingCount: 0,

        moveUp(index) {
            if (index <= 0) return;
            const item = this.competitors.splice(index, 1)[0];
            this.competitors.splice(index - 1, 0, item);
            this.save();
        },
        moveDown(index) {
            if (index >= this.competitors.length - 1) return;
            const item = this.competitors.splice(index, 1)[0];
            this.competitors.splice(index + 1, 0, item);
            this.save();
        },
        moveToTop(index) {
            if (index <= 0) return;
            const item = this.competitors.splice(index, 1)[0];
            this.competitors.unshift(item);
            this.save();
        },
        moveToBottom(index) {
            if (index >= this.competitors.length - 1) return;
            const item = this.competitors.splice(index, 1)[0];
            this.competitors.push(item);
            this.save();
        },
        async save() {
            this.savingCount++;
            try {
                const order = this.competitors.map(c => c.id);
                await this.$wire.call('persistCompetitorOrder', this.roundId, order);
            } catch (e) {
                console.error('Save failed:', e);
            } finally {
                this.savingCount--;
            }
        },
    }"
    wire:ignore
    class="space-y-2"
>
    <div class="flex items-center justify-between">
        <p class="text-xs text-gray-500 dark:text-gray-400">
            Poradie určuje, kto súťaží ako prvý. Zmeny sa ukladajú automaticky.
        </p>
        <div x-show="savingCount > 0" x-transition class="text-xs text-primary-500 flex items-center gap-1">
            <svg class="h-3 w-3 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            Ukladá sa...
        </div>
    </div>

    <div class="rounded-lg border border-gray-200 dark:border-gray-700 divide-y divide-gray-100 dark:divide-gray-800">
        <template x-for="(c, idx) in competitors" :key="c.id">
            <div class="flex items-center gap-3 px-3 py-2.5 text-sm hover:bg-gray-50 dark:hover:bg-gray-800/50">
                <span class="w-6 text-center text-gray-400 tabular-nums font-medium" x-text="idx + 1"></span>
                <span class="flex-1 font-medium" x-text="c.name"></span>
                <div class="flex items-center gap-0.5">
                    <button
                        type="button"
                        @click="moveToTop(idx)"
                        :disabled="idx === 0"
                        class="p-1 rounded text-gray-400 hover:text-gray-700 hover:bg-gray-100 dark:hover:text-gray-200 dark:hover:bg-gray-700 disabled:opacity-25 disabled:cursor-not-allowed"
                        title="Na začiatok"
                    >
                        <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l7.5-7.5 7.5 7.5M4.5 18.75l7.5-7.5 7.5 7.5" /></svg>
                    </button>
                    <button
                        type="button"
                        @click="moveUp(idx)"
                        :disabled="idx === 0"
                        class="p-1 rounded text-gray-400 hover:text-gray-700 hover:bg-gray-100 dark:hover:text-gray-200 dark:hover:bg-gray-700 disabled:opacity-25 disabled:cursor-not-allowed"
                        title="Hore"
                    >
                        <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" /></svg>
                    </button>
                    <button
                        type="button"
                        @click="moveDown(idx)"
                        :disabled="idx === competitors.length - 1"
                        class="p-1 rounded text-gray-400 hover:text-gray-700 hover:bg-gray-100 dark:hover:text-gray-200 dark:hover:bg-gray-700 disabled:opacity-25 disabled:cursor-not-allowed"
                        title="Dole"
                    >
                        <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                    </button>
                    <button
                        type="button"
                        @click="moveToBottom(idx)"
                        :disabled="idx === competitors.length - 1"
                        class="p-1 rounded text-gray-400 hover:text-gray-700 hover:bg-gray-100 dark:hover:text-gray-200 dark:hover:bg-gray-700 disabled:opacity-25 disabled:cursor-not-allowed"
                        title="Na koniec"
                    >
                        <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 5.25l-7.5 7.5-7.5-7.5M19.5 11.25l-7.5 7.5-7.5-7.5" /></svg>
                    </button>
                </div>
            </div>
        </template>
    </div>

    <div x-show="competitors.length === 0" class="p-4 text-center text-xs text-gray-500 dark:text-gray-400">
        Žiadni súťažiaci v tejto kategórii.
    </div>
</div>
