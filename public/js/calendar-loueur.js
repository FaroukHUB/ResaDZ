/**
 * Calendar Loueur — Airbnb-style monthly calendar
 * Handles: vehicle selection, single click block/unblock,
 * range selection, block panel, stats update, animations
 */
document.addEventListener('alpine:init', () => {
    Alpine.data('calendarLoueur', (config) => ({
        // State
        selectedVehicleId: config.initialVehicle || null,
        vehicles: config.vehicles || [],
        bookingMap: config.bookingMap || {},
        blockMap: config.blockMap || {},
        unavailableMap: config.unavailableMap || {},
        today: config.today || '',
        days: config.days || [],

        // Range selection
        rangeStart: null,
        rangeEnd: null,
        hoverDate: null,
        selecting: false,

        // Block panel
        showPanel: false,
        panelDates: { start: null, end: null },
        blockReason: '',
        blockNote: '',
        reasonOptions: [
            { key: 'entretien', label: 'Entretien', icon: '🔧' },
            { key: 'personnel', label: 'Usage personnel', icon: '🚗' },
            { key: 'hors_plateforme', label: 'Réservé hors plateforme', icon: '📦' },
            { key: 'autre', label: 'Autre', icon: '✏️' },
        ],

        // Animations
        slideDirection: 'right',

        init() {
            // If only one vehicle, auto-select it
            if (this.vehicles.length === 1 && !this.selectedVehicleId) {
                this.selectedVehicleId = this.vehicles[0].id;
            }
        },

        // ── Getters ──

        get selectedVehicle() {
            return this.vehicles.find(v => v.id == this.selectedVehicleId) || null;
        },

        get stats() {
            if (!this.selectedVehicleId) return { available: 0, blocked: 0, booked: 0 };
            let available = 0, blocked = 0, booked = 0;
            this.days.forEach(d => {
                const key = this.selectedVehicleId + '_' + d;
                if (this.bookingMap[key]) booked++;
                else if (this.blockMap[key]) blocked++;
                else if (!this.unavailableMap[key] && !this.isPast(d)) available++;
            });
            return { available, blocked, booked };
        },

        // ── Date helpers ──

        isPast(dateStr) {
            return dateStr < this.today;
        },

        isToday(dateStr) {
            return dateStr === this.today;
        },

        getDayStatus(dateStr) {
            if (!this.selectedVehicleId) return 'no-vehicle';
            const key = this.selectedVehicleId + '_' + dateStr;
            if (this.bookingMap[key]) return 'booked';
            if (this.blockMap[key] && this.blockMap[key].type === 'maintenance') return 'maintenance';
            if (this.blockMap[key]) return 'blocked';
            if (this.unavailableMap[key]) return 'unavailable';
            if (this.isPast(dateStr) && !this.isToday(dateStr)) return 'past';
            return 'available';
        },

        getBookingInfo(dateStr) {
            if (!this.selectedVehicleId) return null;
            const key = this.selectedVehicleId + '_' + dateStr;
            return this.bookingMap[key] || null;
        },

        isInRange(dateStr) {
            if (!this.selecting || !this.rangeStart) return false;
            const end = this.hoverDate || this.rangeEnd;
            if (!end) return false;
            const s = this.rangeStart < end ? this.rangeStart : end;
            const e = this.rangeStart < end ? end : this.rangeStart;
            return dateStr >= s && dateStr <= e;
        },

        // ── Interactions ──

        onDayClick(dateStr) {
            if (!this.selectedVehicleId) return;
            const status = this.getDayStatus(dateStr);

            if (status === 'past' || status === 'booked' || status === 'maintenance' || status === 'unavailable' || status === 'no-vehicle') return;

            // If blocked → single toggle (unblock)
            if (status === 'blocked') {
                this.$wire.toggleBlock(this.selectedVehicleId, dateStr);
                return;
            }

            // Available → start range or single toggle
            if (!this.selecting) {
                // First click → start range
                this.rangeStart = dateStr;
                this.selecting = true;
                this.hoverDate = dateStr;
            } else {
                // Second click → end range
                this.rangeEnd = dateStr;
                this.selecting = false;

                const start = this.rangeStart < dateStr ? this.rangeStart : dateStr;
                const end = this.rangeStart < dateStr ? dateStr : this.rangeStart;

                if (start === end) {
                    // Single day — toggle directly
                    this.$wire.toggleBlock(this.selectedVehicleId, start);
                    this.resetRange();
                } else {
                    // Multi-day — show panel
                    this.panelDates = { start, end };
                    this.blockReason = '';
                    this.blockNote = '';
                    this.showPanel = true;
                }
            }
        },

        onDayHover(dateStr) {
            if (this.selecting) {
                this.hoverDate = dateStr;
            }
        },

        cancelRange() {
            this.resetRange();
        },

        resetRange() {
            this.rangeStart = null;
            this.rangeEnd = null;
            this.hoverDate = null;
            this.selecting = false;
        },

        // ── Panel actions ──

        confirmBlock() {
            if (!this.selectedVehicleId || !this.panelDates.start || !this.panelDates.end) return;
            let reason = '';
            const found = this.reasonOptions.find(r => r.key === this.blockReason);
            if (found) reason = found.label;
            if (this.blockNote) reason += (reason ? ' — ' : '') + this.blockNote;

            this.$wire.blockRange(
                this.selectedVehicleId,
                this.panelDates.start,
                this.panelDates.end,
                reason || 'Bloqué manuellement'
            );
            this.showPanel = false;
            this.resetRange();
        },

        cancelBlock() {
            this.showPanel = false;
            this.resetRange();
        },

        // ── Formatting ──

        formatDate(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr + 'T00:00:00');
            return d.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' });
        },

        formatDateShort(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr + 'T00:00:00');
            return d.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' });
        },
    }));
});
