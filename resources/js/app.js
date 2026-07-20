import './bootstrap';
// import Alpine from 'alpinejs';

// flatpickr
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';

// window.Alpine = Alpine;
window.flatpickr = flatpickr;

// Alpine.start();

document.addEventListener('alpine:init', () => {
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
