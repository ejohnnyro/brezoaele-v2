/**
 * Map module for Brezoaele V2 Theme.
 * Initializes Leaflet satellite map and renders location markers.
 */
document.addEventListener("DOMContentLoaded", function() {
    const mapDiv = document.getElementById('map');
    if (!mapDiv) {
        return;
    }

    // Coordonate implicite pentru centrul comunei Brezoaele
    const centerLat = 44.5714;
    const centerLon = 25.7936;
    
    // Inițializare hartă Leaflet
    const map = L.map('map').setView([centerLat, centerLon], 14);
    
    // Adăugăm harta din satelit (Esri World Imagery)
    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        maxZoom: 19,
        attribution: 'Tiles &copy; Esri &mdash; Source: Esri and the GIS User Community'
    }).addTo(map);
    
    // Adăugăm etichetele străzilor (CartoDB Light Labels)
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_only_labels/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap &copy; CARTO'
    }).addTo(map);

    // Preluăm pinii din obiectul localizat global
    const pinsData = window.brezoaeleMapData || [];
    
    // Plasare pini pe hartă
    pinsData.forEach(function(pin) {
        let pinColor = '#d97706'; // Galben - Servicii / Firme
        
        if (pin.type === 'producator' || pin.type === 'legumicultor') {
            pinColor = '#047857'; // Verde închis - Producători / Fermieri
        } else if (pin.type === 'institutie' || pin.type === 'scoala' || pin.type === 'utilitate') {
            pinColor = '#0284c7'; // Albastru - Instituții publice
        }
        
        // Popup cu design Flat UI și font Inter
        const popupContent = `
            <div style="font-family: 'Inter', sans-serif; min-width: 200px; color: #0f172a; padding: 2px;">
                <h4 style="font-size: 0.95rem; font-weight: 800; margin: 0 0 4px 0; color: #0f172a; text-transform: uppercase;">${pin.title}</h4>
                <p style="margin: 0 0 10px 0; font-size: 0.8rem; color: #334155; line-height: 1.4;">${pin.excerpt}</p>
                ${pin.program ? `<div style="font-size: 0.75rem; margin-bottom: 2px;">🕒 <b>Program:</b> ${pin.program}</div>` : ''}
                ${pin.telefon ? `<div style="font-size: 0.75rem; margin-bottom: 8px;">📞 <b>Tel:</b> ${pin.telefon}</div>` : ''}
                <a href="${pin.link}" style="display: block; background-color: var(--color-primary); color: #ffffff; text-align: center; padding: 6px 10px; border: 2px solid #0f172a; font-weight: 700; font-size: 0.7rem; text-decoration: none; text-transform: uppercase; box-shadow: 2px 2px 0px #0f172a; transition: 0.1s;">
                    Vezi profil complet
                </a>
            </div>
        `;
        
        // Desenăm un marker circular modern, ascuțit
        L.circleMarker([pin.lat, pin.lng], {
            radius: 8,
            fillColor: pinColor,
            color: '#0f172a',
            weight: 2,
            opacity: 1,
            fillOpacity: 0.95
        })
        .addTo(map)
        .bindPopup(popupContent);
    });
});
