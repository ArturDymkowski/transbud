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
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listWeek',
                },
                height: 'auto',
                dayMaxEvents: 4,
                events: (info, successCallback, failureCallback) => {
                    this.$wire.getEvents(info.startStr, info.endStr)
                        .then(successCallback)
                        .catch(failureCallback);
                },
                eventClick: (info) => {
                    this.$wire.openTransportSet(parseInt(info.event.id, 10));
                },
            });

            this.calendar.render();

            this.$wire.on('calendar-refresh', () => this.calendar.refetchEvents());
        },

        destroy() {
            this.calendar?.destroy();
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
