/**
 * Weather module for Brezoaele V2 Theme.
 * Fetches data from Open-Meteo API and renders weather widgets.
 */
document.addEventListener("DOMContentLoaded", function() {
    const lat = 44.5714;
    const lon = 25.7936;
    const WMO_CODES = {
        0: { desc: "Cer complet senin", emoji: "☀️" },
        1: { desc: "Majoritar senin", emoji: "🌤️" },
        2: { desc: "Parțial noros", emoji: "⛅" },
        3: { desc: "Cer acoperit", emoji: "☁️" },
        45: { desc: "Ceață", emoji: "🌫️" },
        48: { desc: "Ceață cu chiciură", emoji: "🌫️" },
        51: { desc: "Bură ușoară", emoji: "🌧️" },
        53: { desc: "Bură moderată", emoji: "🌧️" },
        55: { desc: "Bură densă", emoji: "🌧️" },
        61: { desc: "Ploaie slabă", emoji: "🌦️" },
        63: { desc: "Ploaie moderată", emoji: "🌧️" },
        65: { desc: "Ploaie puternică", emoji: "🌧️" },
        71: { desc: "Ninsoare slabă", emoji: "❄️" },
        73: { desc: "Ninsoare moderată", emoji: "❄️" },
        75: { desc: "Ninsoare abundentă", emoji: "❄️" },
        80: { desc: "Averse slabe de ploaie", emoji: "🌦️" },
        81: { desc: "Averse moderate de ploaie", emoji: "🌧️" },
        82: { desc: "Averse torențiale de ploaie", emoji: "⛈️" },
        95: { desc: "Furtună slabă sau moderată", emoji: "⛈️" },
        99: { desc: "Furtună cu grindină", emoji: "⛈️" }
    };

    function getWeatherInfo(code) {
        return WMO_CODES[code] || { desc: "Condiții variabile", emoji: "⛅" };
    }

    // 1. Homepage Weather Widget
    const homeWidget = document.getElementById("weather-today");
    if (homeWidget) {
        const urlHome = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current_weather=true`;
        fetch(urlHome)
            .then(response => response.json())
            .then(data => {
                if (data && data.current_weather) {
                    const temp = Math.round(data.current_weather.temperature);
                    const info = getWeatherInfo(data.current_weather.weathercode);
                    homeWidget.innerHTML = `
                        <span style="font-size: 2.2rem; line-height: 1;">${info.emoji}</span>
                        <div>
                            <span style="font-size: 1.25rem; font-weight: 900; display: block; color: var(--color-text-dark);">${temp}°C</span>
                            <span style="font-size: 0.8rem; color: var(--color-text-muted); font-weight: 600;">${info.desc}</span>
                        </div>
                    `;
                }
            })
            .catch(err => {
                console.error("Eroare meteo homepage:", err);
                homeWidget.innerHTML = '<span style="color: var(--color-text-muted); font-size: 0.85rem;">Prognoză indisponibilă</span>';
            });
    }

    // 2. Detailed Weather Page
    const forecastGrid = document.getElementById("forecast-grid");
    if (forecastGrid) {
        const urlDetail = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current_weather=true&daily=weathercode,temperature_2m_max,temperature_2m_min,rain_sum,windspeed_10m_max&timezone=Europe%2FBucharest`;
        fetch(urlDetail)
            .then(response => response.json())
            .then(data => {
                // Randează vremea curentă detaliată
                if (data.current_weather) {
                    const cw = data.current_weather;
                    const info = getWeatherInfo(cw.weathercode);
                    
                    const tempEl = document.getElementById("current-temp");
                    const descEl = document.getElementById("current-desc");
                    const emojiEl = document.getElementById("current-emoji");
                    const windEl = document.getElementById("current-wind");
                    const windDirEl = document.getElementById("current-wind-dir");

                    if (tempEl) tempEl.innerText = `${Math.round(cw.temperature)}°C`;
                    if (descEl) descEl.innerText = info.desc;
                    if (emojiEl) emojiEl.innerText = info.emoji;
                    if (windEl) windEl.innerText = `${cw.windspeed} km/h`;
                    if (windDirEl) windDirEl.innerText = `${cw.winddirection}°`;
                }

                // Randează prognoza pe 7 zile
                if (data.daily) {
                    forecastGrid.innerHTML = ""; // Golim textul de loading
                    const daysOfWeek = ["Duminică", "Luni", "Marți", "Miercuri", "Joi", "Vineri", "Sâmbătă"];
                    
                    for (let i = 0; i < data.daily.time.length; i++) {
                        const dateObj = new Date(data.daily.time[i]);
                        let dayName = daysOfWeek[dateObj.getDay()];
                        if (i === 0) dayName = "Astăzi";

                        const formattedDate = dateObj.toLocaleDateString('ro-RO', { day: 'numeric', month: 'short' });
                        const info = getWeatherInfo(data.daily.weathercode[i]);
                        const maxTemp = Math.round(data.daily.temperature_2m_max[i]);
                        const minTemp = Math.round(data.daily.temperature_2m_min[i]);
                        const rain = data.daily.rain_sum[i];
                        const wind = Math.round(data.daily.windspeed_10m_max[i]);

                        const cardHtml = `
                            <div class="card" style="padding: 16px; text-align: center; display: flex; flex-direction: column; justify-content: space-between;">
                                <div>
                                    <span style="font-size: 0.75rem; color: var(--color-text-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">
                                        ${formattedDate}
                                    </span>
                                    <h4 style="margin: 4px 0 8px 0; font-size: 1.1rem; font-family: var(--font-heading); font-weight: 800;">${dayName}</h4>
                                    <div style="font-size: 2.8rem; line-height: 1; margin: 10px 0;">${info.emoji}</div>
                                    <div style="font-weight: 700; font-size: 0.9rem; color: var(--color-text-dark); margin-bottom: 10px;">${info.desc}</div>
                                </div>
                                
                                <div style="border-top: 1px dashed var(--color-border); padding-top: 10px; margin-top: 8px; display: flex; flex-direction: column; gap: 4px; font-size: 0.8rem; color: var(--color-text-muted);">
                                    <div style="display: flex; justify-content: space-between;">
                                        <span>Temp:</span>
                                        <span style="color: var(--color-text-dark);">
                                            <b style="color: #ef4444;">${maxTemp}°</b> / <b style="color: #3b82f6;">${minTemp}°</b>
                                        </span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between;">
                                        <span>Precipitații:</span>
                                        <span style="color: var(--color-text-dark); font-weight: 600;">${rain} mm</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between;">
                                        <span>Vânt Max:</span>
                                        <span style="color: var(--color-text-dark); font-weight: 600;">${wind} km/h</span>
                                    </div>
                                </div>
                            </div>
                        `;
                        forecastGrid.innerHTML += cardHtml;
                    }
                }
            })
            .catch(err => {
                console.error("Eroare meteo detaliată:", err);
                forecastGrid.innerHTML = `
                    <div style="grid-column: 1 / -1; text-align: center; padding: 30px 0; color: #ef4444; font-weight: 700;">
                        Nu s-au putut încărca datele prognozei.
                    </div>
                `;
            });
    }
});
