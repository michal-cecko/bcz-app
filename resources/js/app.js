import './bootstrap';

import flatpickr from 'flatpickr';
import { Slovak } from 'flatpickr/dist/l10n/sk.js';
import { Czech } from 'flatpickr/dist/l10n/cs.js';
import * as FilePond from 'filepond';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';

const htmlLang = (document.documentElement.lang || 'sk').toLowerCase();
if (htmlLang.startsWith('sk')) {
    flatpickr.localize(Slovak);
} else if (htmlLang.startsWith('cs')) {
    flatpickr.localize(Czech);
}

window.flatpickr = flatpickr;

FilePond.registerPlugin(
    FilePondPluginImagePreview,
    FilePondPluginFileValidateSize,
    FilePondPluginFileValidateType,
);

// Phones (iPhones, and modern Android/Samsung) often shoot HEIC/HEIF, which the
// server's image stack can't decode and browsers can't preview. Convert it to
// JPEG in the browser before upload so the rest of the pipeline only ever sees a
// JPEG. heic2any (~1.5 MB wasm) is imported on demand — only when a HEIC is
// actually selected — so JPEG/PNG users never download it. Android frequently
// reports HEIC with an empty or octet-stream MIME type, so we detect by extension
// as well as MIME.
const isHeic = (file) =>
    /\.(heic|heif)$/i.test(file.name || '') ||
    /image\/(heic|heif)/i.test(file.type || '');

const heicToJpeg = async (file) => {
    if (!isHeic(file)) {
        return file;
    }

    const { default: heic2any } = await import('heic2any');
    const converted = await heic2any({ blob: file, toType: 'image/jpeg', quality: 0.9 });
    const blob = Array.isArray(converted) ? converted[0] : converted;
    const name = (file.name || 'photo').replace(/\.(heic|heif)$/i, '') + '.jpg';

    return new File([blob], name, { type: 'image/jpeg', lastModified: file.lastModified || Date.now() });
};

window.bczFilepond = function ({ statePath, accept = null, maxSizeMb = 10, labelIdle = null }) {
    return {
        pond: null,
        init() {
            const inputEl = this.$el.querySelector('input[type="file"]');
            if (!inputEl) {
                return;
            }
            const livewireRoot = this.$el.closest('[wire\\:id]');
            const $wire = livewireRoot ? window.Livewire.find(livewireRoot.getAttribute('wire:id')) : null;

            if (!$wire) {
                return;
            }

            this.pond = FilePond.create(inputEl, {
                acceptedFileTypes: accept ? accept.split(',').map((s) => s.trim()).filter(Boolean) : null,
                maxFileSize: `${maxSizeMb}MB`,
                allowMultiple: false,
                credits: false,
                imagePreviewMaxHeight: 200,
                labelIdle: labelIdle ?? 'Pretiahnite súbor sem alebo <span class="filepond--label-action">vyberte</span>',
                labelFileLoading: 'Nahrávam',
                labelFileProcessing: 'Nahrávam',
                labelFileProcessingComplete: 'Hotovo',
                labelFileProcessingError: 'Chyba',
                labelTapToCancel: 'klepnutím zrušiť',
                labelTapToRetry: 'klepnutím skúsiť znova',
                labelTapToUndo: 'klepnutím zrušiť',
                labelButtonRemoveItem: 'Odstrániť',
                labelMaxFileSizeExceeded: 'Súbor je príliš veľký',
                labelMaxFileSize: `Maximálna veľkosť: ${maxSizeMb}MB`,
                labelFileTypeNotAllowed: 'Nepovolený typ súboru',
                server: {
                    process: (fieldName, file, metadata, load, error, progress, abort) => {
                        let aborted = false;

                        // Convert HEIC→JPEG first (no-op for normal images), then upload.
                        // FilePond keeps showing its "Nahrávam" state for the whole span.
                        heicToJpeg(file)
                            .then((uploadFile) => {
                                if (aborted) {
                                    return;
                                }

                                $wire.upload(
                                    statePath,
                                    uploadFile,
                                    (uploadedFilename) => load(uploadedFilename),
                                    () => error('Nahrávanie zlyhalo'),
                                    (event) => progress(event.detail.lengthComputable, event.detail.loaded, event.detail.total),
                                );
                            })
                            .catch(() => error('Nahrávanie zlyhalo'));

                        return {
                            abort: () => {
                                aborted = true;
                                abort();
                            },
                        };
                    },
                    revert: (uniqueFileId, load) => {
                        $wire.set(statePath, null);
                        load();
                    },
                },
            });
        },
        destroy() {
            if (this.pond) {
                this.pond.destroy();
                this.pond = null;
            }
        },
    };
};

window.prettyPicker = function ({ options, multiple, placeholder, searchPlaceholder, emptyLabel, statePath, initialValue }) {
    const normalizeInitial = (raw) => {
        if (multiple) {
            if (Array.isArray(raw)) return raw.map((v) => String(v));
            if (raw === null || raw === undefined || raw === '') return [];
            return [String(raw)];
        }
        if (raw === null || raw === undefined) return '';
        return String(raw);
    };

    return {
        options,
        multiple,
        placeholder,
        searchPlaceholder,
        emptyLabel,
        statePath,
        open: false,
        search: '',
        // Local Alpine state. We sync to Livewire imperatively via $wire.set() to
        // avoid the wire:model.live + x-modelable race that swallowed the first click.
        value: normalizeInitial(initialValue),

        init() {
            // Re-sync from server when Livewire pushes a new value (e.g., other
            // form fields change) so the picker doesn't fall out of sync.
            this.$watch('value', (newValue) => {
                if (typeof this.$wire?.set === 'function' && this.statePath) {
                    this.$wire.set(this.statePath, newValue, false);
                }
            });
        },

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
                // Single mode: always commit the click (no toggle-off on same value).
                // Removal is intentional via the chip's X / placeholder reset only.
                this.value = stringValue;
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
            if (!this.open) {
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
