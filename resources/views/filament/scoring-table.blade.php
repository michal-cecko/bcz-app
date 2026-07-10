<div
    x-data="{
        roundId: @js($roundId),
        roundType: @js($roundType),
        scoringFormat: @js($scoringFormat ?? 'points'),
        competitors: @js($competitors),
        parts: @js($parts),
        scores: @js($scores),
        battles: @js($battles),
        scoresPublished: @js($scoresPublished),
        advanceCount: @js($advanceCount ?? null),
        nextRoundName: @js($nextRoundName ?? null),
        isStale: @js($isStale ?? false),
        previousRoundName: @js($previousRoundName ?? null),
        regenerating: false,
        search: '',
        sortBy: 'order',
        sortDir: 'asc',
        savingCount: 0,

        // ── Qualification ──────────────────────────────
        getScore(partId, userId) {
            return this.scores[partId]?.[userId] ?? '';
        },
        setScore(partId, userId, value) {
            if (!this.scores[partId]) this.scores[partId] = {};
            this.scores[partId][userId] = value === '' ? null : parseFloat(value);
            this.persist('persistScore', partId, userId, value === '' ? null : value);
        },
        getTotal(userId) {
            let total = 0;
            for (const partId in this.scores) {
                const v = parseFloat(this.scores[partId]?.[userId]);
                if (!isNaN(v)) total += v;
            }
            return total;
        },
        formatTotal(userId) {
            const t = this.getTotal(userId);
            return t > 0 ? t.toFixed(2) : '-';
        },
        get rankedByScore() {
            return [...this.competitors]
                .map(c => ({ id: c.id, total: this.getTotal(c.id) }))
                .filter(c => c.total > 0)
                .sort((a, b) => b.total - a.total);
        },
        getRank(userId) {
            const idx = this.rankedByScore.findIndex(c => c.id === userId);
            return idx === -1 ? null : idx + 1;
        },
        get filteredCompetitors() {
            let list = [...this.competitors];
            if (this.search) {
                const q = this.search.toLowerCase();
                list = list.filter(c => c.name.toLowerCase().includes(q));
            }
            list.sort((a, b) => {
                let va, vb;
                switch (this.sortBy) {
                    case 'name':
                        va = a.name.toLowerCase(); vb = b.name.toLowerCase(); break;
                    case 'total':
                        va = this.getTotal(a.id); vb = this.getTotal(b.id); break;
                    case 'order':
                        va = a.order; vb = b.order; break;
                    default:
                        va = parseFloat(this.scores[this.sortBy]?.[a.id]) || 0;
                        vb = parseFloat(this.scores[this.sortBy]?.[b.id]) || 0;
                }
                const cmp = va < vb ? -1 : va > vb ? 1 : 0;
                return this.sortDir === 'asc' ? cmp : -cmp;
            });
            return list;
        },
        rowClass(userId) {
            const rank = this.getRank(userId);
            if (rank === 1) return 'bg-yellow-100 dark:bg-yellow-400/15';
            if (rank === 2) return 'bg-slate-200 dark:bg-slate-300/15';
            if (rank === 3) return 'bg-orange-200/70 dark:bg-amber-700/25';
            return '';
        },
        isAdvancing(userId) {
            if (!this.advanceCount) return false;
            const rank = this.getRank(userId);
            return rank !== null && rank <= this.advanceCount;
        },
        advanceTooltip() {
            return this.nextRoundName ? `Postupuje do: ${this.nextRoundName}` : '';
        },

        // ── Battle ─────────────────────────────────────
        setBattlePartScore(battleId, partId, side, value) {
            const battle = this.battles.find(b => b.id === battleId);
            if (!battle) return;
            const map = side === 'a' ? (battle.partScoresA ??= {}) : (battle.partScoresB ??= {});
            if (value === '' || value === null) {
                delete map[partId];
            } else {
                map[partId] = parseFloat(value);
            }
            const total = this.getBattleSideTotal(battle, side);
            if (side === 'a') battle.totalA = total;
            else battle.totalB = total;
            this.persist('persistBattlePartScore', battleId, partId, side, value === '' ? null : value);
        },
        getBattlePartScore(battle, partId, side) {
            const map = side === 'a' ? battle.partScoresA : battle.partScoresB;
            if (!map) return '';
            const v = map[partId];
            return (v === undefined || v === null) ? '' : v;
        },
        getBattleSideTotal(battle, side) {
            const map = side === 'a' ? battle.partScoresA : battle.partScoresB;
            if (!map) return null;
            let total = 0;
            let any = false;
            for (const partId in map) {
                const v = parseFloat(map[partId]);
                if (!isNaN(v)) { total += v; any = true; }
            }
            return any ? total : null;
        },
        formatBattleTotal(battle, side) {
            const t = this.getBattleSideTotal(battle, side);
            return t === null ? '—' : t.toFixed(2);
        },
        setBattlePartWinner(battleId, key, side) {
            const battle = this.battles.find(b => b.id === battleId);
            if (!battle) return;
            if (!battle.partWinners) battle.partWinners = {};
            if (side === '' || side === null) {
                delete battle.partWinners[key];
            } else {
                battle.partWinners[key] = side;
            }
            this.persist('persistBattlePartWinner', battleId, key, side || null);
        },
        getCycleCount(battle) {
            let max = battle.cycles || 1;
            const pw = battle.partWinners || {};
            Object.keys(pw).forEach(k => {
                const m = k.match(/__c(\d+)$/);
                if (m) max = Math.max(max, parseInt(m[1]));
            });
            return max;
        },
        getCycleKey(partId, cycle) {
            return cycle === 1 ? partId : partId + '__c' + cycle;
        },
        addCycle(battleId) {
            const battle = this.battles.find(b => b.id === battleId);
            if (!battle) return;
            if (!battle.cycles) battle.cycles = this.getCycleCount(battle);
            battle.cycles++;
        },
        removeCycle(battle) {
            const count = this.getCycleCount(battle);
            if (count <= 1) return;
            this.parts.forEach(part => {
                const key = this.getCycleKey(part.id, count);
                if (battle.partWinners && battle.partWinners[key] !== undefined) {
                    this.setBattlePartWinner(battle.id, key, '');
                }
                if (battle.partScoresA && battle.partScoresA[key] !== undefined) {
                    this.setBattlePartScore(battle.id, key, 'a', '');
                }
                if (battle.partScoresB && battle.partScoresB[key] !== undefined) {
                    this.setBattlePartScore(battle.id, key, 'b', '');
                }
            });
            battle.cycles = count - 1;
        },
        getWinner(battle) {
            if (this.scoringFormat === 'points') {
                const mapA = battle.partScoresA || {};
                const mapB = battle.partScoresB || {};
                const complete = this.parts.every(p => {
                    const va = parseFloat(mapA[p.id]);
                    const vb = parseFloat(mapB[p.id]);
                    return !isNaN(va) && !isNaN(vb);
                });
                if (!complete) return null;
                const a = this.getBattleSideTotal(battle, 'a');
                const b = this.getBattleSideTotal(battle, 'b');
                if (a == null || b == null) return null;
                if (a > b) return 'a';
                if (b > a) return 'b';
                return 'draw';
            }
            if (this.scoringFormat === 'coach_decision') {
                const pw = battle.partWinners || {};
                const vals = Object.values(pw);
                if (vals.length === 0) return null;
                const aWins = vals.filter(v => v === 'a').length;
                const bWins = vals.filter(v => v === 'b').length;
                const draws = vals.filter(v => v === 'draw').length;
                const aScore = aWins + draws;
                const bScore = bWins + draws;
                if (aScore > bScore) return 'a';
                if (bScore > aScore) return 'b';
                const totalExpected = this.parts.length * this.getCycleCount(battle);
                if (vals.length >= totalExpected) return 'draw';
                return null;
            }
            return null;
        },
        getResultLabel(battle) {
            const w = this.getWinner(battle);
            if (w === 'a') return battle.nameA;
            if (w === 'b') return battle.nameB;
            if (w === 'draw') return 'Remíza';
            return '-';
        },
        getPartWinsLabel(battle) {
            const pw = battle.partWinners || {};
            const vals = Object.values(pw);
            if (vals.length === 0) return '-';
            const aWins = vals.filter(v => v === 'a').length;
            const bWins = vals.filter(v => v === 'b').length;
            const draws = vals.filter(v => v === 'draw').length;
            return (aWins + draws) + ':' + (bWins + draws);
        },
        get filteredBattles() {
            let list = [...this.battles];
            if (this.search) {
                const q = this.search.toLowerCase();
                list = list.filter(b => b.nameA.toLowerCase().includes(q) || b.nameB.toLowerCase().includes(q));
            }
            return list;
        },

        // ── Shared ─────────────────────────────────────
        toggleSort(column) {
            if (this.sortBy === column) {
                if (this.sortDir === 'asc') { this.sortDir = 'desc'; }
                else { this.sortBy = 'order'; this.sortDir = 'asc'; }
            } else {
                this.sortBy = column; this.sortDir = 'asc';
            }
        },
        async togglePublished() {
            await this.persist('persistScoresPublished', this.roundId, this.scoresPublished);
        },
        async persist(method, ...args) {
            this.savingCount++;
            try { await this.$wire.call(method, ...args); }
            catch (e) { console.error('Save failed:', e); }
            finally { this.savingCount--; }
        },
        async regenerate() {
            if (this.regenerating) return;
            this.regenerating = true;
            try {
                await this.$wire.call('regenerateNextRound', this.roundId);
            } catch (e) {
                console.error('Regenerate failed:', e);
            } finally {
                this.regenerating = false;
            }
        },
        handleRoundBattlesRefreshed(event) {
            const detail = event?.detail ?? {};
            const payload = Array.isArray(detail) ? detail[0] : detail;
            if (!payload || payload.roundId !== this.roundId) return;
            const scoring = payload.scoring;
            if (!scoring) return;
            if (Array.isArray(scoring.battles)) this.battles = scoring.battles;
            if ('isStale' in scoring) this.isStale = !!scoring.isStale;
        },
    }"
    wire:ignore
    x-on:round-battles-refreshed.window="handleRoundBattlesRefreshed($event)"
    class="space-y-2"
>
    {{-- Toolbar --}}
    <div class="flex items-center gap-3">
        <label class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-600 dark:text-gray-400 cursor-pointer">
            <div class="relative">
                <input type="checkbox" x-model="scoresPublished" @change="togglePublished()" class="sr-only peer">
                <div class="w-8 h-4 bg-gray-200 dark:bg-gray-700 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-primary-600"></div>
            </div>
            Zverejniť body
        </label>
        <div class="flex-1"></div>
        <div x-show="savingCount > 0" x-transition class="text-xs text-primary-500 flex items-center gap-1">
            <svg class="h-3 w-3 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            Ukladá sa...
        </div>
        <div class="relative">
            <svg class="pointer-events-none absolute left-2 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
            <input type="text" x-model.debounce.200ms="search" placeholder="Hľadať..."
                class="block w-52 rounded border border-gray-300 bg-white py-1.5 pl-8 pr-3 text-sm text-gray-900 placeholder-gray-400 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500">
        </div>
    </div>

    {{-- QUALIFICATION TABLE --}}
    <template x-if="roundType === 'qualification'">
        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="w-8 px-3 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">#</th>
                        <th @click="toggleSort('name')" class="cursor-pointer select-none px-3 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                            Súťažiaci <span x-show="sortBy === 'name'" x-text="sortDir === 'asc' ? '↑' : '↓'" class="ml-0.5"></span>
                        </th>
                        <template x-for="part in parts" :key="part.id">
                            <th @click="toggleSort(part.id)" class="cursor-pointer select-none px-3 py-2 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                                <span x-text="part.name"></span> <span x-show="sortBy === part.id" x-text="sortDir === 'asc' ? '↑' : '↓'" class="ml-0.5"></span>
                            </th>
                        </template>
                        <th @click="toggleSort('total')" class="cursor-pointer select-none px-3 py-2 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                            Spolu <span x-show="sortBy === 'total'" x-text="sortDir === 'asc' ? '↑' : '↓'" class="ml-0.5"></span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    <template x-for="(c, idx) in filteredCompetitors" :key="c.id">
                        <tr :class="rowClass(c.id)">
                            <td class="px-3 py-2 text-gray-400 tabular-nums" x-text="idx + 1"></td>
                            <td class="px-3 py-2 font-medium">
                                <div class="flex items-center gap-1.5">
                                    <span x-text="c.name"></span>
                                    <span
                                        x-show="isAdvancing(c.id)"
                                        :title="advanceTooltip()"
                                        class="inline-flex items-center gap-0.5 rounded-full bg-emerald-100 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-3 w-3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 4.5 7.5 7.5-7.5 7.5" />
                                        </svg>
                                        Postupuje
                                    </span>
                                </div>
                            </td>
                            <template x-for="part in parts" :key="part.id">
                                <td class="px-3 py-2 text-right">
                                    <input type="number" step="0.01" :value="getScore(part.id, c.id)" @change="setScore(part.id, c.id, $event.target.value)"
                                        class="w-20 rounded border border-gray-300 bg-transparent px-2 py-1 text-right text-sm tabular-nums focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600">
                                </td>
                            </template>
                            <td class="px-3 py-2 text-right font-semibold tabular-nums" x-text="formatTotal(c.id)"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <div x-show="filteredCompetitors.length === 0" class="p-4 text-center text-xs text-gray-500 dark:text-gray-400">Žiadni súťažiaci.</div>
        </div>
    </template>

    {{-- STALE BATTLE WARNING --}}
    <template x-if="roundType === 'battle' && isStale">
        <div class="flex items-start gap-3 rounded-lg border border-amber-200 bg-amber-50 p-3 dark:border-amber-700/40 dark:bg-amber-900/20">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5 shrink-0 text-amber-600 dark:text-amber-400 mt-0.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.008v.008H12v-.008Z" />
            </svg>
            <div class="flex-1">
                <p class="text-sm font-semibold text-amber-900 dark:text-amber-200">Battle sú zastarané</p>
                <p class="mt-0.5 text-xs text-amber-800 dark:text-amber-300">
                    <span x-show="previousRoundName">Poradie postupujúcich z kola „<span x-text="previousRoundName"></span>" sa zmenilo.</span>
                    Odporúčame battle regenerovať, aby odrážali aktuálnych postupujúcich.
                </p>
            </div>
            <button
                type="button"
                @click="regenerate()"
                :disabled="regenerating"
                class="shrink-0 rounded-md bg-amber-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-1 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-amber-500 dark:hover:bg-amber-400 dark:focus:ring-offset-gray-900"
            >
                <span x-show="!regenerating">Regenerovať</span>
                <span x-show="regenerating">Regeneruje sa…</span>
            </button>
        </div>
    </template>

    {{-- BATTLE TABLE — POINTS MODE --}}
    <template x-if="roundType === 'battle' && scoringFormat === 'points'">
        <div class="space-y-3">
            <template x-for="b in filteredBattles" :key="b.id">
                <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3 bg-gray-50 px-3 py-2 dark:bg-gray-800">
                        <span class="text-xs font-medium tabular-nums text-gray-400" x-text="'#' + b.bracket"></span>
                        <span class="flex-1"></span>
                        <span x-show="getWinner(b) === 'draw'" class="inline-block rounded-full bg-orange-100 px-2 py-0.5 text-xs font-semibold text-orange-700 dark:bg-orange-900/30 dark:text-orange-400">Remíza</span>
                        <span x-show="getWinner(b) === 'a' || getWinner(b) === 'b'" class="text-xs font-semibold text-amber-600 dark:text-amber-400" x-text="getResultLabel(b)"></span>
                        <span x-show="getWinner(b) === null" class="text-xs text-gray-400">—</span>
                    </div>
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50/50 dark:bg-gray-800/50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Strana</th>
                                <template x-for="part in parts" :key="part.id">
                                    <th class="px-3 py-2 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400" x-text="part.name"></th>
                                </template>
                                <th class="px-3 py-2 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Spolu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            <template x-for="side in ['a', 'b']" :key="side">
                                <tr :class="getWinner(b) === side ? 'bg-amber-50 dark:bg-amber-500/10' : ''">
                                    <td class="px-3 py-2 font-medium" :class="getWinner(b) === side ? 'text-amber-600 dark:text-amber-400 font-bold' : ''">
                                        <span class="text-xs uppercase tracking-wide text-gray-400 mr-2" x-text="'Strana ' + side.toUpperCase()"></span>
                                        <span x-text="side === 'a' ? b.nameA : b.nameB"></span>
                                    </td>
                                    <template x-for="part in parts" :key="part.id">
                                        <td class="px-3 py-2 text-right">
                                            <input
                                                type="number"
                                                step="0.01"
                                                :value="getBattlePartScore(b, part.id, side)"
                                                @change="setBattlePartScore(b.id, part.id, side, $event.target.value)"
                                                class="w-20 rounded border border-gray-300 bg-transparent px-2 py-1 text-right text-sm tabular-nums focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600"
                                            >
                                        </td>
                                    </template>
                                    <td class="px-3 py-2 text-right font-semibold tabular-nums"
                                        :class="getWinner(b) === side ? 'text-amber-600 dark:text-amber-400' : ''"
                                        x-text="formatBattleTotal(b, side)"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </template>
            <div x-show="filteredBattles.length === 0" class="rounded-lg border border-gray-200 p-4 text-center text-xs text-gray-500 dark:border-gray-700 dark:text-gray-400">Žiadne battle.</div>
        </div>
    </template>

    {{-- BATTLE TABLE — COACH DECISION MODE --}}
    <template x-if="roundType === 'battle' && scoringFormat === 'coach_decision'">
        <div class="space-y-3">
            <template x-for="b in filteredBattles" :key="b.id">
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    {{-- Battle header --}}
                    <div class="flex items-center gap-3 px-3 py-2 bg-gray-50 dark:bg-gray-800">
                        <span class="text-xs text-gray-400 tabular-nums font-medium" x-text="'#' + b.bracket"></span>
                        <span class="font-semibold text-sm" :class="getWinner(b) === 'a' ? 'text-amber-600 dark:text-amber-400' : ''" x-text="b.nameA"></span>
                        <span class="text-xs text-gray-400">vs</span>
                        <span class="font-semibold text-sm" :class="getWinner(b) === 'b' ? 'text-amber-600 dark:text-amber-400' : ''" x-text="b.nameB"></span>
                        <div class="flex-1"></div>
                        <span x-show="getWinner(b) === 'draw'" class="inline-block rounded-full bg-orange-100 px-2 py-0.5 text-xs text-orange-700 font-semibold dark:bg-orange-900/30 dark:text-orange-400">
                            <span x-text="getPartWinsLabel(b)"></span> Remíza
                        </span>
                        <span x-show="getWinner(b) === 'a' || getWinner(b) === 'b'" class="text-xs font-semibold">
                            <span class="text-gray-500" x-text="getPartWinsLabel(b)"></span>
                            <span class="text-amber-600 dark:text-amber-400" x-text="getResultLabel(b)"></span>
                        </span>
                        <span x-show="getWinner(b) === null" class="text-xs text-gray-400">-</span>
                    </div>

                    {{-- Cycles --}}
                    <template x-for="cycle in getCycleCount(b)" :key="cycle">
                        <div class="border-t border-gray-100 dark:border-gray-800">
                            <div class="flex items-center gap-2 px-3 py-1.5" :class="getCycleCount(b) > 1 ? 'bg-gray-25' : ''">
                                <span x-show="getCycleCount(b) > 1" class="text-xs text-gray-400 font-medium w-14 shrink-0" x-text="'Kolo ' + cycle"></span>
                                <div class="flex flex-wrap items-center gap-2 flex-1">
                                    <template x-for="part in parts" :key="part.id">
                                        <div class="flex flex-col items-center gap-0.5">
                                            <span class="text-xs text-gray-400" x-text="part.name"></span>
                                            <select
                                                :value="(b.partWinners || {})[getCycleKey(part.id, cycle)] ?? ''"
                                                @change="setBattlePartWinner(b.id, getCycleKey(part.id, cycle), $event.target.value)"
                                                class="rounded border border-gray-300 bg-transparent py-1 pl-2 pr-6 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600"
                                            >
                                                <option value="">—</option>
                                                <option value="a" :selected="(b.partWinners || {})[getCycleKey(part.id, cycle)] === 'a'" x-text="b.nameA"></option>
                                                <option value="b" :selected="(b.partWinners || {})[getCycleKey(part.id, cycle)] === 'b'" x-text="b.nameB"></option>
                                                <option value="draw" :selected="(b.partWinners || {})[getCycleKey(part.id, cycle)] === 'draw'">Remíza</option>
                                            </select>
                                        </div>
                                    </template>
                                </div>
                                <button type="button" x-show="cycle === getCycleCount(b) && cycle > 1" @click="removeCycle(b)"
                                    class="shrink-0 rounded p-1 text-gray-400 hover:bg-danger-50 hover:text-danger-600 dark:hover:bg-danger-500/10 dark:hover:text-danger-400"
                                    title="Odstrániť toto kolo">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166M18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.02-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                </button>
                            </div>
                        </div>
                    </template>

                    {{-- Add cycle button (visible on draw) --}}
                    <div x-show="getWinner(b) === 'draw'" class="border-t border-gray-100 dark:border-gray-800 px-3 py-2">
                        <button type="button" @click="addCycle(b.id)"
                            class="flex items-center gap-1 text-xs font-medium text-orange-600 hover:text-orange-700 dark:text-orange-400 dark:hover:text-orange-300">
                            <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                            Pridať ďalšie kolo
                        </button>
                    </div>
                </div>
            </template>
            <div x-show="filteredBattles.length === 0" class="p-4 text-center text-xs text-gray-500 dark:text-gray-400">Žiadne battle.</div>
        </div>
    </template>
</div>
