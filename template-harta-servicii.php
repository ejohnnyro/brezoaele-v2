<?php
/**
 * Template Name: Hartă Servicii & Satelit
 *
 * @package Brezoaele_V2
 */

get_header();
?>

<main id="primary" class="site-main" style="padding: 40px 0; background-color: var(--color-bg);">
	<div class="container">
		
		<header class="page-header" style="margin-bottom: 30px; text-align: center;">
			<h1 class="page-title" style="font-size: 2.5rem; margin-bottom: 8px;">Harta Satelit a Serviciilor</h1>
			<p style="color: var(--color-text-muted);">Explorează producătorii locali, instituțiile publice și obiectivele de interes din comuna Brezoaele.</p>
			<div style="width: 50px; height: 3px; background-color: var(--color-primary); margin: 12px auto 0 auto; border-radius: 3px;"></div>
		</header>

		<div style="background: #ffffff; padding: 12px 20px; border: 1px solid var(--color-border); border-radius: var(--border-radius-md); margin-bottom: 20px; display: flex; justify-content: center; gap: 24px; flex-wrap: wrap; font-size: 0.85rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: var(--shadow-sm);">
			<div style="display: flex; align-items: center; gap: 8px;">
				<span style="width: 12px; height: 12px; background-color: #047857; display: inline-block; border: 1px solid var(--color-border); border-radius: 50%;"></span>
				<span>Fermieri & Producători Locali</span>
			</div>
			<div style="display: flex; align-items: center; gap: 8px;">
				<span style="width: 12px; height: 12px; background-color: #0284c7; display: inline-block; border: 1px solid var(--color-border); border-radius: 50%;"></span>
				<span>Instituții Publice / Utilități</span>
			</div>
			<div style="display: flex; align-items: center; gap: 8px;">
				<span style="width: 12px; height: 12px; background-color: #d97706; display: inline-block; border: 1px solid var(--color-border); border-radius: 50%;"></span>
				<span>Afaceri & Servicii Diverse</span>
			</div>
		</div>

		<!-- Containerul Hărții -->
		<div id="map" style="height: 600px; width: 100%; border: 1px solid var(--color-border); border-radius: var(--border-radius-lg); box-shadow: var(--shadow-md); z-index: 10; overflow: hidden;"></div>

	</div>
</main>

<?php
get_footer();
