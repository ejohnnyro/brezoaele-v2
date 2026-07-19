<?php
/**
 * Template Name: Prognoză Meteo Detaliată
 *
 * @package Brezoaele_V2
 */

get_header();
?>

<main id="primary" class="site-main" style="padding: 40px 0; background-color: var(--color-bg);">
	<div class="container" style="max-width: 1320px; margin: 0 auto;">
		
		<header class="page-header" style="margin-bottom: 35px; text-align: center;">
			<h1 class="page-title" style="font-size: 2.5rem; margin-bottom: 8px;">Meteo Brezoaele</h1>
			<p style="color: var(--color-text-muted);">Prognoza meteorologică detaliată pe 7 zile pentru comuna Brezoaele, actualizată în timp real.</p>
			<div style="width: 50px; height: 3px; background-color: var(--color-primary); margin: 12px auto 0 auto; border-radius: 3px;"></div>
		</header>

		<!-- Caseta Vremea Curentă -->
		<div class="card" style="padding: 24px; margin-bottom: 30px; border-top: 4px solid var(--color-primary);">
			<h3 style="font-size: 1.15rem; margin-bottom: 20px; border-bottom: 1px solid var(--color-border); padding-bottom: 8px; font-weight: 800; text-transform: uppercase;">Starea Vremii în Acest Moment</h3>
			
			<div style="display: flex; align-items: center; justify-content: space-around; flex-wrap: wrap; gap: 20px;">
				<div style="text-align: center;">
					<div id="current-emoji" style="font-size: 4rem; line-height: 1; margin-bottom: 6px;">⛅</div>
					<div id="current-desc" style="font-size: 1rem; font-weight: 800; color: var(--color-text-dark);">Se încarcă...</div>
				</div>
				
				<div style="text-align: center;">
					<div id="current-temp" style="font-size: 3.5rem; font-weight: 900; color: var(--color-secondary); line-height: 1;">--°C</div>
					<div style="font-size: 0.8rem; color: var(--color-text-muted); margin-top: 4px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Temperatură aer</div>
				</div>

				<div style="display: flex; flex-direction: column; gap: 8px; min-width: 180px;">
					<div style="display: flex; justify-content: space-between; font-size: 0.9rem; border-bottom: 1px dashed var(--color-border); padding-bottom: 4px;">
						<span>Viteză Vânt:</span>
						<b id="current-wind">-- km/h</b>
					</div>
					<div style="display: flex; justify-content: space-between; font-size: 0.9rem; border-bottom: 1px dashed var(--color-border); padding-bottom: 4px;">
						<span>Direcție Vânt:</span>
						<b id="current-wind-dir">--°</b>
					</div>
					<div style="display: flex; justify-content: space-between; font-size: 0.9rem;">
						<span>Localitate:</span>
						<b>Brezoaele, DB</b>
					</div>
				</div>
			</div>
		</div>

		<!-- Prognoza pe 7 zile -->
		<h3 style="font-size: 1.3rem; margin-bottom: 20px; border-bottom: 2px solid var(--color-border); padding-bottom: 6px; font-weight: 800; text-transform: uppercase;">Următoarele 7 Zile</h3>
		
		<div id="forecast-grid" class="forecast-container">
			<div style="grid-column: 1 / -1; text-align: center; padding: 40px 0; color: var(--color-text-muted); font-weight: 700; width: 100%;">
				Se încarcă prognoza pe 7 zile...
			</div>
		</div>

	</div>
</main>

<?php
get_footer();
