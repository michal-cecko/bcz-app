import './bootstrap';

import flatpickr from 'flatpickr';
import { Slovak } from 'flatpickr/dist/l10n/sk.js';
import { Czech } from 'flatpickr/dist/l10n/cs.js';

const htmlLang = (document.documentElement.lang || 'sk').toLowerCase();
if (htmlLang.startsWith('sk')) {
    flatpickr.localize(Slovak);
} else if (htmlLang.startsWith('cs')) {
    flatpickr.localize(Czech);
}

window.flatpickr = flatpickr;

window.prettyPicker = function ({ options, multiple, placeholder, searchPlaceholder, emptyLabel }) {
    return {
        options,
        multiple,
        placeholder,
        searchPlaceholder,
        emptyLabel,
        open: false,
        search: '',
        // `value` is x-modelable-bound; Livewire syncs it via wire:model.live.
        // Single mode: string (or empty). Multi mode: array.
        value: multiple ? [] : '',

        get selected() {
            if (this.multiple) {
                if (!Array.isArray(this.value)) return [];
                return this.value.map((v) => String(v));
            }
            if (this.value === null || this.value === undefined || this.value === '') return [];
            return [String(this.value)];
        },

        labelFor(value) {
            return this.options[value] ?? value;
        },

        isSelected(value) {
            return this.selected.includes(String(value));
        },

        select(value) {
            const stringValue = String(value);
            if (this.multiple) {
                const current = Array.isArray(this.value) ? [...this.value] : [];
                const idx = current.findIndex((v) => String(v) === stringValue);
                if (idx === -1) {
                    current.push(stringValue);
                } else {
                    current.splice(idx, 1);
                }
                this.value = current;
            } else {
                this.value = this.value === stringValue ? '' : stringValue;
                this.close();
            }
        },

        remove(value) {
            if (!this.multiple) {
                this.value = '';
                return;
            }
            this.value = (Array.isArray(this.value) ? this.value : []).filter(
                (v) => String(v) !== String(value),
            );
        },

        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.$nextTick(() => this.$refs.searchInput?.focus());
            } else {
                this.search = '';
            }
        },

        close() {
            this.open = false;
            this.search = '';
        },

        filteredOptions() {
            const query = this.search.trim().toLowerCase();
            if (!query) return this.options;
            const result = {};
            for (const [value, label] of Object.entries(this.options)) {
                if (String(label).toLowerCase().includes(query)) {
                    result[value] = label;
                }
            }
            return result;
        },
    };
};

window.countdown = function (targetIso) {
    return {
        days: 0,
        hours: 0,
        minutes: 0,
        seconds: 0,
        interval: null,

        start() {
            this.update();
            this.interval = setInterval(() => this.update(), 1000);
        },

        update() {
            const now = new Date().getTime();
            const target = new Date(targetIso).getTime();
            const diff = Math.max(0, target - now);

            this.days = Math.floor(diff / (1000 * 60 * 60 * 24));
            this.hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            this.minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            this.seconds = Math.floor((diff % (1000 * 60)) / 1000);

            if (diff <= 0 && this.interval) {
                clearInterval(this.interval);
                window.location.reload();
            }
        },

        destroy() {
            if (this.interval) clearInterval(this.interval);
        },
    };
};
