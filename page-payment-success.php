<?php
/**
 * Template Name: Payment Success Page
 * @package Brezoaele_V2
 */

get_header();
?>

<main id="primary" class="site-main" style="padding: 60px 0; background-color: var(--color-bg);">
	<div class="container" style="max-width: 650px;">
		<div class="card" style="padding: 40px 30px; text-align: center; background: #ffffff; border: 1px solid var(--color-border); border-radius: var(--border-radius-lg); box-shadow: 0 10px 25px rgba(0,0,0,0.05);">
			<div style="font-size: 3.5rem; margin-bottom: 16px;">🎉</div>
			<h1 style="font-size: 1.8rem; font-weight: 900; font-family: var(--font-heading); color: #047857; margin-bottom: 12px;">
				Plată Procesată cu Succes!
			</h1>
			<p style="color: var(--color-text-muted); font-size: 1rem; line-height: 1.6; margin-bottom: 24px;">
				Îți mulțumim! Tranzacția ta a fost autorizată cu succes prin procesatorul securizat EuPlatesc.ro.
			</p>

			<div style="background: #f0fdf4; border: 1px solid #bbf7d0; padding: 16px; border-radius: 8px; font-size: 0.9rem; color: #166534; margin-bottom: 30px; text-align: left;">
				✔ Anunțul sau serviciul tău a fost promovat / activat.<br/>
				✔ Chitanța și detaliile de facturare au fost trimise pe adresa ta de email.<br/>
				✔ Poți descarca factura PDF din panoul tău de control.
			</div>

			<a href="<?php echo esc_url( home_url( '/contul-meu/' ) ); ?>" class="btn btn-primary" style="padding: 14px 28px; font-size: 1rem; font-weight: 800; border-radius: 8px;">
				Mergi la Panoul Tău de Control &rarr;
			</a>
		</div>
	</div>
</main>

<?php
get_footer();
