/**
 * ResaDZ Analytics Tracking
 * Tracks user interactions: clicks on phone, WhatsApp, reserve buttons, etc.
 */
const ResaDZTracking = {
    // API endpoint
    apiUrl: '/api/tracking',

    // Session ID (stored in localStorage)
    sessionId: null,

    // Heartbeat interval (every 2 minutes)
    heartbeatInterval: 120000,

    /**
     * Initialize tracking
     */
    init() {
        this.sessionId = this.getOrCreateSessionId();
        this.setupEventListeners();
        this.startHeartbeat();
        console.log('ResaDZ Tracking initialized');
    },

    /**
     * Get or create session ID
     */
    getOrCreateSessionId() {
        let sessionId = localStorage.getItem('resadz_session_id');
        if (!sessionId) {
            sessionId = 'sess_' + Math.random().toString(36).substring(2, 15) + Date.now().toString(36);
            localStorage.setItem('resadz_session_id', sessionId);
        }
        return sessionId;
    },

    /**
     * Track a click event
     */
    trackClick(eventType, data = {}) {
        const payload = {
            event_type: eventType,
            vehicle_id: data.vehicleId || null,
            loueur_id: data.loueurId || null,
            metadata: {
                page_url: window.location.href,
                session_id: this.sessionId,
                ...data.metadata
            }
        };

        // Use sendBeacon for reliability (works even when page is closing)
        if (navigator.sendBeacon) {
            const blob = new Blob([JSON.stringify(payload)], { type: 'application/json' });
            navigator.sendBeacon(this.apiUrl + '/click', blob);
        } else {
            // Fallback to fetch
            fetch(this.apiUrl + '/click', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(payload)
            }).catch(err => console.warn('Tracking error:', err));
        }
    },

    /**
     * Track phone click
     */
    trackPhone(vehicleId, loueurId, phoneNumber) {
        this.trackClick('phone', {
            vehicleId,
            loueurId,
            metadata: { phone: phoneNumber }
        });
    },

    /**
     * Track WhatsApp click
     */
    trackWhatsApp(vehicleId, loueurId, phoneNumber) {
        this.trackClick('whatsapp', {
            vehicleId,
            loueurId,
            metadata: { phone: phoneNumber }
        });
    },

    /**
     * Track reserve button click
     */
    trackReserve(vehicleId, loueurId) {
        this.trackClick('reserve', { vehicleId, loueurId });
    },

    /**
     * Track view details click
     */
    trackViewDetails(vehicleId, loueurId) {
        this.trackClick('view_details', { vehicleId, loueurId });
    },

    /**
     * Track share click
     */
    trackShare(vehicleId, loueurId, platform) {
        this.trackClick('share', {
            vehicleId,
            loueurId,
            metadata: { platform }
        });
    },

    /**
     * Setup automatic event listeners
     */
    setupEventListeners() {
        document.addEventListener('click', (e) => {
            const target = e.target.closest('[data-track]');
            if (!target) return;

            const trackType = target.dataset.track;
            const vehicleId = target.dataset.vehicleId || null;
            const loueurId = target.dataset.loueurId || null;
            const phone = target.dataset.phone || null;
            const platform = target.dataset.platform || null;

            switch (trackType) {
                case 'phone':
                    this.trackPhone(vehicleId, loueurId, phone);
                    break;
                case 'whatsapp':
                    this.trackWhatsApp(vehicleId, loueurId, phone);
                    break;
                case 'reserve':
                    this.trackReserve(vehicleId, loueurId);
                    break;
                case 'view_details':
                    this.trackViewDetails(vehicleId, loueurId);
                    break;
                case 'share':
                    this.trackShare(vehicleId, loueurId, platform);
                    break;
                default:
                    this.trackClick(trackType, { vehicleId, loueurId });
            }
        });
    },

    /**
     * Start heartbeat to track real-time visitors
     */
    startHeartbeat() {
        // Send initial heartbeat
        this.sendHeartbeat();

        // Send heartbeat every 2 minutes
        setInterval(() => this.sendHeartbeat(), this.heartbeatInterval);

        // Send heartbeat when page becomes visible again
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') {
                this.sendHeartbeat();
            }
        });
    },

    /**
     * Send heartbeat to server
     */
    sendHeartbeat() {
        fetch(this.apiUrl + '/heartbeat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                session_id: this.sessionId,
                page_url: window.location.href
            })
        }).catch(() => {}); // Silently fail
    }
};

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => ResaDZTracking.init());
} else {
    ResaDZTracking.init();
}
