import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';

L.Icon.Default.mergeOptions({ iconRetinaUrl: markerIcon2x, iconUrl: markerIcon, shadowUrl: markerShadow });
delete L.Icon.Default.prototype._getIconUrl;

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-tour-map]').forEach((el) => {
        let data;
        try {
            data = JSON.parse(el.dataset.tourMap);
        } catch (e) {
            return;
        }
        if (!data) {
            return;
        }

        const map = L.map(el, { scrollWheelZoom: false });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 18,
        }).addTo(map);

        let bounds = null;

        if (el.dataset.tourMapType === 'route' && data.points && data.points.length) {
            const polyline = L.polyline(data.points, { color: '#2563eb', weight: 4 }).addTo(map);
            L.marker(data.points[0]).addTo(map);
            if (data.points.length > 1) {
                L.marker(data.points[data.points.length - 1]).addTo(map);
            }
            bounds = polyline.getBounds();
        } else if (data.lat && data.lng) {
            L.marker([data.lat, data.lng]).addTo(map);
        } else {
            map.setView([0, 0], 2);
        }

        function fitToContent() {
            if (bounds) {
                map.fitBounds(bounds, { padding: [20, 20] });
            } else if (data.lat && data.lng) {
                map.setView([data.lat, data.lng], 12);
            }
        }

        // The Map tab is hidden until clicked, so Leaflet initializes into
        // a zero-size container — recompute tiles and re-fit the view once
        // it's actually shown (or resized) with real dimensions.
        new ResizeObserver(() => {
            map.invalidateSize();
            fitToContent();
        }).observe(el);

        fitToContent();
    });
});
