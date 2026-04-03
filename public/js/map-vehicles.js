/**
 * ResaDZ - Carte interactive des véhicules par wilaya (Leaflet.js)
 */
(function () {
    'use strict';

    const mapContainer = document.getElementById('resadz-map');
    if (!mapContainer) return;

    // Init map centered on Algeria
    const map = L.map('resadz-map', {
        center: [28.0339, 1.6596],
        zoom: 5,
        minZoom: 4,
        maxZoom: 10,
        zoomControl: true,
        scrollWheelZoom: false,
    });

    // Tile layer - CartoDB Positron (clean & free)
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; <a href="https://carto.com/">CARTO</a>',
        subdomains: 'abcd',
        maxZoom: 19,
    }).addTo(map);

    // Enable scroll zoom on click
    map.on('click', function () {
        map.scrollWheelZoom.enable();
    });
    map.on('mouseout', function () {
        map.scrollWheelZoom.disable();
    });

    // Custom marker icon
    function createIcon(count) {
        const size = count >= 20 ? 44 : count >= 10 ? 38 : count >= 5 ? 32 : 28;
        const color = count >= 20 ? '#dc2626' : count >= 10 ? '#ea580c' : count >= 5 ? '#d97706' : '#059669';

        return L.divIcon({
            html: `<div style="
                background: ${color};
                color: white;
                width: ${size}px;
                height: ${size}px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 800;
                font-size: ${size > 36 ? '14' : '12'}px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.3);
                border: 2px solid white;
                font-family: system-ui, sans-serif;
            ">${count}</div>`,
            className: 'resadz-marker',
            iconSize: [size, size],
            iconAnchor: [size / 2, size / 2],
        });
    }

    // Fetch data and plot markers
    fetch('/api/map/vehicles-by-wilaya')
        .then(r => r.json())
        .then(response => {
            if (!response.success || !response.data) return;

            const markers = L.markerClusterGroup
                ? L.markerClusterGroup()
                : L.layerGroup();

            response.data.forEach(wilaya => {
                const marker = L.marker([wilaya.lat, wilaya.lng], {
                    icon: createIcon(wilaya.vehicles),
                    title: wilaya.name,
                });

                let priceHtml = wilaya.min_price
                    ? `<div style="font-size:13px;color:#d97706;font-weight:700;margin-top:4px;">A partir de ${wilaya.min_price.toLocaleString('fr-FR')} DA/jour</div>`
                    : '';

                let badge = wilaya.has_local
                    ? '<span style="display:inline-block;background:#dcfce7;color:#166534;font-size:11px;padding:2px 8px;border-radius:99px;margin-top:4px;">Loueurs locaux</span>'
                    : '<span style="display:inline-block;background:#dbeafe;color:#1e40af;font-size:11px;padding:2px 8px;border-radius:99px;margin-top:4px;">Livraison nationale</span>';

                marker.bindPopup(`
                    <div style="min-width:180px;font-family:system-ui,sans-serif;">
                        <div style="font-size:16px;font-weight:800;color:#111827;">${wilaya.code} - ${wilaya.name}</div>
                        <div style="font-size:14px;color:#4b5563;margin-top:4px;">
                            <strong>${wilaya.vehicles}</strong> véhicule${wilaya.vehicles > 1 ? 's' : ''} disponible${wilaya.vehicles > 1 ? 's' : ''}
                        </div>
                        ${priceHtml}
                        ${badge}
                        <a href="/vehicules?wilaya=${encodeURIComponent(wilaya.name)}"
                           style="display:block;text-align:center;margin-top:10px;padding:8px 16px;background:#111827;color:white;border-radius:10px;text-decoration:none;font-weight:600;font-size:13px;">
                            Voir les véhicules
                        </a>
                    </div>
                `, { maxWidth: 250 });

                markers.addLayer(marker);
            });

            map.addLayer(markers);

            // Update total counter
            const totalEl = document.getElementById('map-total-wilayas');
            if (totalEl) totalEl.textContent = response.data.length;

            const totalVehiclesEl = document.getElementById('map-total-vehicles');
            if (totalVehiclesEl) {
                const total = response.data.reduce((sum, w) => sum + w.vehicles, 0);
                totalVehiclesEl.textContent = total;
            }
        })
        .catch(err => console.error('Map error:', err));
})();
