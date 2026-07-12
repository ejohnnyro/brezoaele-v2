/**
 * Single Map module for Brezoaele V2 Theme.
 * Renders a satellite map for a singular business page.
 */
document.addEventListener("DOMContentLoaded", function() {
    const mapDiv = document.getElementById('single-map');
    if (!mapDiv || !window.brezoaeleSingleMapData) {
        return;
    }

    const data = window.brezoaeleSingleMapData;
    const lat = data.lat;
    const lng = data.lng;
    
    // Inițializează harta centrată pe locație
    const map = L.map('single-map').setView([lat, lng], 16);
    
    // Strat Satelit (Esri World Imagery)
	L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
		maxZoom: 19,
		attribution: 'Esri'
	}).addTo(map);
    
    // Strat Străzi (CartoDB Labels)
	L.tileLayer('https://{s}.basemaps.cartocdn.com/light_only_labels/{z}/{x}/{y}{r}.png', {
		maxZoom: 19
	}).addTo(map);

	// Marker circular Flat UI
	L.circleMarker([lat, lng], {
		radius: 8,
		fillColor: '#047857', // Verde
		color: '#0f172a',
		weight: 2,
		opacity: 1,
		fillOpacity: 0.95
	}).addTo(map).bindPopup(`<b>${data.title}</b>`).openPopup();
});
