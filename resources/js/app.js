import './bootstrap';
// import Alpine from 'alpinejs';

// flatpickr
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';

// FullCalendar
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import interactionPlugin from '@fullcalendar/interaction';
import plLocale from '@fullcalendar/core/locales/pl';

// window.Alpine = Alpine;
window.flatpickr = flatpickr;

// Alpine.start();

function formatThousands(value) {
    if (value === null || value === undefined || value === '') {
        return '';
    }

    const [intPart, decPart] = String(value).split('.');
    const formattedInt = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, ' ');

    return decPart !== undefined ? `${formattedInt}.${decPart}` : formattedInt;
}

document.addEventListener('alpine:init', () => {
    Alpine.data('quantityInput', (raw) => ({
        raw,
        display: '',

        init() {
            this.display = formatThousands(this.raw);

            // Keep display in sync whenever `raw` changes for any reason, not just
            // through onInput - e.g. when a Livewire update resets the entangled
            // value from the server (modal reopened for a different record), which
            // otherwise left `display` showing stale text from a previous session.
            this.$watch('raw', (value) => {
                this.display = formatThousands(value);
            });
        },

        onInput(event) {
            const input = event.target;
            const charsFromEnd = input.value.length - input.selectionStart;

            let clean = input.value.replace(',', '.').replace(/[^\d.]/g, '');
            const firstDot = clean.indexOf('.');
            if (firstDot !== -1) {
                clean = clean.slice(0, firstDot + 1) + clean.slice(firstDot + 1).replace(/\./g, '');
            }

            this.raw = clean;
            this.display = formatThousands(clean);

            this.$nextTick(() => {
                const position = Math.max(0, this.display.length - charsFromEnd);
                input.setSelectionRange(position, position);
            });
        },

        // Blocks disallowed keystrokes outright (not just cleans up after), so
        // letters/symbols never even appear in the field.
        onKeydown(event) {
            if (event.ctrlKey || event.metaKey || event.altKey) {
                return;
            }

            const allowedKeys = ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Tab', 'Home', 'End', 'Enter'];
            if (allowedKeys.includes(event.key)) {
                return;
            }

            if ((event.key === '.' || event.key === ',') && !String(this.raw ?? '').includes('.')) {
                return;
            }

            if (/^\d$/.test(event.key)) {
                return;
            }

            event.preventDefault();
        },
    }));

    // Keystroke guard + read-only formatted preview for money inputs bound with
    // wire:model. Livewire owns the actual value natively (that's the fix for the
    // amount-goes-missing bug the quantityInput/@entangle combo had); `preview`
    // is a purely cosmetic, one-way readout that only ever reads the input's
    // current value and never writes back into it, so there's nothing for it to
    // get out of sync with.
    Alpine.data('decimalInput', (initial = '') => ({
        preview: formatThousands(String(initial ?? '')).replace('.', ','),

        onInput(event) {
            const clean = event.target.value.replace(',', '.').replace(/[^\d.]/g, '');
            this.preview = formatThousands(clean).replace('.', ',');
        },

        onKeydown(event) {
            if (event.ctrlKey || event.metaKey || event.altKey) {
                return;
            }

            const allowedKeys = ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Tab', 'Home', 'End', 'Enter'];
            if (allowedKeys.includes(event.key)) {
                return;
            }

            if ((event.key === '.' || event.key === ',') && !/[.,]/.test(event.target.value)) {
                return;
            }

            if (/^\d$/.test(event.key)) {
                return;
            }

            event.preventDefault();
        },
    }));

    window.flatpickr = flatpickr;
    Alpine.data('tableSelection', (selected, idsOnPage, allIds) => ({
        selected,
        idsOnPage,
        allIds,

        isAllPageSelected() {
            return this.idsOnPage.length > 0
                && this.idsOnPage.every(id => this.selected.includes(id));
        },

        togglePage() {
            if (this.isAllPageSelected()) {
                this.selected = this.selected.filter(id => !this.idsOnPage.includes(id));
            } else {
                this.selected = [...new Set([...this.selected, ...this.idsOnPage])];
            }
        },
    }));

    Alpine.data('deliveriesCalendar', () => ({
        calendar: null,

        init() {
            this.calendar = new Calendar(this.$refs.calendarEl, {
                plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
                locale: plLocale,
                timeZone: 'UTC',
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listWeek',
                },
                height: 'auto',
                dayMaxEvents: 4,
                editable: true,
                eventDurationEditable: false,
                events: (info, successCallback, failureCallback) => {
                    this.$wire.getEvents(info.startStr, info.endStr)
                        .then(successCallback)
                        .catch(failureCallback);
                },
                eventClick: (info) => {
                    this.$wire.openTransportSet(parseInt(info.event.id, 10));
                },
                eventDrop: (info) => {
                    const id = parseInt(info.event.id, 10);
                    const startStr = info.event.startStr;
                    const endStr = info.event.end ? info.event.endStr : null;

                    info.revert();

                    this.$wire.openTransportSet(id, startStr, endStr);
                },
            });

            this.calendar.render();

            this.$wire.on('calendar-refresh', () => this.calendar.refetchEvents());
        },

        destroy() {
            this.calendar?.destroy();
        },
    }));

    Alpine.data('multiSelect', (options, selected) => ({
        options,
        selected,
        open: false,
        search: '',

        get filteredOptions() {
            const term = this.search.trim().toLowerCase();

            if (!term) {
                return this.options;
            }

            return this.options.filter(option => option.label.toLowerCase().includes(term));
        },

        labelFor(value) {
            const match = this.options.find(option => String(option.value) === String(value));

            return match ? match.label : value;
        },

        isSelected(value) {
            return this.selected.some(selectedValue => String(selectedValue) === String(value));
        },

        toggleOption(value) {
            this.selected = this.isSelected(value)
                ? this.selected.filter(selectedValue => String(selectedValue) !== String(value))
                : [...this.selected, value];
        },

        toggle() {
            this.open ? this.close() : this.openList();
        },

        openList() {
            this.open = true;
            this.search = '';
            this.$nextTick(() => this.$refs.search && this.$refs.search.focus());
        },

        close() {
            this.open = false;
        },
    }));

    Alpine.data('searchableSelect', (options, selected) => ({
        options,
        selected,
        open: false,
        search: '',

        get filteredOptions() {
            const term = this.search.trim().toLowerCase();

            if (!term) {
                return this.options;
            }

            return this.options.filter(option => option.label.toLowerCase().includes(term));
        },

        get selectedOption() {
            return this.options.find(option => String(option.value) === String(this.selected));
        },

        toggle() {
            this.open ? this.close() : this.openList();
        },

        openList() {
            this.open = true;
            this.search = '';
            this.$nextTick(() => this.$refs.search && this.$refs.search.focus());
        },

        close() {
            this.open = false;
        },

        choose(option) {
            this.selected = option.value;
            this.close();
        },
    }));
});
