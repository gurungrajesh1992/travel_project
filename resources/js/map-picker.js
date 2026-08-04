import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';

L.Icon.Default.mergeOptions({ iconRetinaUrl: markerIcon2x, iconUrl: markerIcon, shadowUrl: markerShadow });
delete L.Icon.Default.prototype._getIconUrl;

const DEFAULT_CENTER = [27.9881, 86.925]; // Everest region — just a sane default view, not tied to any tour

document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('tour-map-picker');
    if (!container) {
        return;
    }

    const canvas = document.getElementById('tour-map-canvas');
    const typeInput = document.getElementById('map-type-input');
    const dataInput = document.getElementById('map-data-input');
    const searchInput = document.getElementById('map-search-input');
    const searchBtn = document.getElementById('map-search-btn');
    const clearBtn = document.getElementById('map-clear-btn');
    const undoBtn = document.getElementById('map-undo-btn');
    const routeHint = document.getElementById('map-route-hint');
    const modeButtons = document.querySelectorAll('.map-mode-btn');

    let mode = container.dataset.initialType || 'point';

    let initialData = null;
    try {
        initialData = JSON.parse(container.dataset.initialData || 'null');
    } catch (e) {
        initialData = null;
    }

    let point = mode === 'point' && initialData ? initialData : null;
    let routePoints = mode === 'route' && initialData && initialData.points ? initialData.points : [];

    const map = L.map(canvas).setView(point ? [point.lat, point.lng] : DEFAULT_CENTER, point ? 12 : 7);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 18,
    }).addTo(map);

    // The "Map" card starts collapsed, so Leaflet initializes into a
    // zero-size container and only loads a partial tile grid. Recompute
    // once the card (or anything else) resizes it to its real size.
    new ResizeObserver(() => map.invalidateSize()).observe(canvas);

    let marker = null;
    let polyline = null;
    let routeMarkers = [];

    function updateModeButtons() {
        modeButtons.forEach((btn) => {
            const active = btn.dataset.mapMode === mode;
            btn.classList.toggle('bg-primary', active);
            btn.classList.toggle('text-primary-content', active);
            btn.classList.toggle('border-primary', active);
            btn.classList.toggle('border-gray-300', !active);
        });
        undoBtn.classList.toggle('hidden', mode !== 'route');
        routeHint.classList.toggle('hidden', mode !== 'route');
    }

    function syncHiddenInput() {
        typeInput.value = mode;

        if (mode === 'point') {
            dataInput.value = point ? JSON.stringify(point) : '';
        } else {
            dataInput.value = routePoints.length ? JSON.stringify({ points: routePoints }) : '';
        }
    }

    function placeMarker(lat, lng) {
        point = { lat, lng };
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng]).addTo(map);
        }
        syncHiddenInput();
    }

    function redrawRoute() {
        routeMarkers.forEach((m) => map.removeLayer(m));
        routeMarkers = routePoints.map((p) => L.circleMarker(p, { radius: 5, color: '#2563eb', fillOpacity: 1 }).addTo(map));

        if (polyline) {
            map.removeLayer(polyline);
            polyline = null;
        }
        if (routePoints.length > 1) {
            polyline = L.polyline(routePoints, { color: '#2563eb', weight: 3 }).addTo(map);
        }
        syncHiddenInput();
    }

    function clearMode() {
        if (mode === 'point') {
            if (marker) {
                map.removeLayer(marker);
                marker = null;
            }
            point = null;
        } else {
            routePoints = [];
            redrawRoute();
        }
        syncHiddenInput();
    }

    if (mode === 'point' && point) {
        placeMarker(point.lat, point.lng);
    }
    if (mode === 'route' && routePoints.length) {
        redrawRoute();
        if (polyline) {
            map.fitBounds(polyline.getBounds());
        }
    }

    map.on('click', (e) => {
        if (mode === 'point') {
            placeMarker(e.latlng.lat, e.latlng.lng);
        } else {
            routePoints.push([e.latlng.lat, e.latlng.lng]);
            redrawRoute();
        }
    });

    modeButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            mode = btn.dataset.mapMode;
            updateModeButtons();
            syncHiddenInput();
        });
    });

    clearBtn.addEventListener('click', clearMode);

    undoBtn.addEventListener('click', () => {
        routePoints.pop();
        redrawRoute();
    });

    async function doSearch() {
        const query = searchInput.value.trim();
        if (!query) {
            return;
        }

        searchBtn.disabled = true;
        const originalLabel = searchBtn.textContent;
        searchBtn.textContent = 'Searching...';

        try {
            const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&limit=1&q=${encodeURIComponent(query)}`);
            const results = await res.json();

            if (!results.length) {
                alert('Location not found. Try a different search term.');
                return;
            }

            const lat = parseFloat(results[0].lat);
            const lng = parseFloat(results[0].lon);
            map.setView([lat, lng], 12);

            if (mode === 'point') {
                placeMarker(lat, lng);
            }
        } catch (e) {
            alert('Search failed — check your connection and try again.');
        } finally {
            searchBtn.disabled = false;
            searchBtn.textContent = originalLabel;
        }
    }

    searchBtn.addEventListener('click', doSearch);
    searchInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            doSearch();
        }
    });

    updateModeButtons();
    syncHiddenInput();
});
