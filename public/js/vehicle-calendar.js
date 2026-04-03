/**
 * ResaDZ - Calendrier de disponibilité véhicule (FullCalendar)
 */
(function () {
    'use strict';

    const calendarEl = document.getElementById('vehicle-availability-calendar');
    if (!calendarEl) return;

    const slug = calendarEl.dataset.vehicleSlug;
    if (!slug) return;

    // Fetch unavailable dates
    fetch(`/api/vehicles/${slug}/unavailable-dates`)
        .then(r => r.json())
        .then(response => {
            const unavailableDates = response.dates || [];
            const bookings = response.bookings || [];

            // Build events
            const events = [];

            // Group consecutive unavailable dates into ranges
            if (unavailableDates.length > 0) {
                const sorted = [...unavailableDates].sort();
                let rangeStart = sorted[0];
                let rangeEnd = sorted[0];

                for (let i = 1; i < sorted.length; i++) {
                    const prev = new Date(rangeEnd);
                    const curr = new Date(sorted[i]);
                    prev.setDate(prev.getDate() + 1);

                    if (prev.toISOString().slice(0, 10) === sorted[i]) {
                        rangeEnd = sorted[i];
                    } else {
                        events.push({
                            start: rangeStart,
                            end: addDay(rangeEnd),
                            display: 'background',
                            backgroundColor: '#fecaca',
                            classNames: ['unavailable-bg'],
                        });
                        rangeStart = sorted[i];
                        rangeEnd = sorted[i];
                    }
                }
                events.push({
                    start: rangeStart,
                    end: addDay(rangeEnd),
                    display: 'background',
                    backgroundColor: '#fecaca',
                    classNames: ['unavailable-bg'],
                });
            }

            // Bookings as events
            if (bookings.length > 0) {
                bookings.forEach(b => {
                    events.push({
                        start: b.start_date,
                        end: addDay(b.end_date),
                        display: 'background',
                        backgroundColor: '#fecaca',
                    });
                });
            }

            // Init FullCalendar
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'fr',
                height: 'auto',
                headerToolbar: {
                    left: 'prev',
                    center: 'title',
                    right: 'next',
                },
                validRange: {
                    start: new Date().toISOString().slice(0, 10),
                },
                events: events,
                dayCellDidMount: function (info) {
                    const dateStr = info.date.toISOString().slice(0, 10);
                    const today = new Date().toISOString().slice(0, 10);

                    if (dateStr < today) {
                        info.el.style.opacity = '0.4';
                        info.el.style.pointerEvents = 'none';
                        return;
                    }

                    const isUnavailable = unavailableDates.includes(dateStr) ||
                        bookings.some(b => dateStr >= b.start_date && dateStr <= b.end_date);

                    if (isUnavailable) {
                        info.el.style.cursor = 'not-allowed';
                        info.el.title = 'Indisponible';
                    } else {
                        info.el.style.cursor = 'default';
                        info.el.title = 'Disponible';
                        // Add subtle green dot
                        const dot = document.createElement('div');
                        dot.style.cssText = 'width:6px;height:6px;background:#22c55e;border-radius:50%;margin:2px auto 0;';
                        info.el.querySelector('.fc-daygrid-day-frame')?.appendChild(dot);
                    }
                },
            });

            calendar.render();
        })
        .catch(err => console.error('Calendar error:', err));

    function addDay(dateStr) {
        const d = new Date(dateStr);
        d.setDate(d.getDate() + 1);
        return d.toISOString().slice(0, 10);
    }
})();
