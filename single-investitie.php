<?php
/**
 * The template for displaying a single investment project page.
 *
 * @package Brezoaele_V2
 */

get_header();
?>

<main id="primary" class="site-main" style="padding: 40px 0; background-color: var(--color-bg);">
	<div class="container">
		<?php
		while ( have_posts() ) :
			the_post();
			
			// Preluăm metadatele investiției
			$stadiu = get_post_meta( get_the_ID(), '_investitie_stadiu', true );
			$buget  = get_post_meta( get_the_ID(), '_investitie_buget', true );
			$sursa  = get_post_meta( get_the_ID(), '_investitie_sursa', true );
			$lat    = get_post_meta( get_the_ID(), '_locatie_lat', true );
			$lng    = get_post_meta( get_the_ID(), '_locatie_lng', true );
		?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> style="max-width: 960px; margin: 0 auto;">
				
				<header class="entry-header" style="margin-bottom: 24px; text-align: center;">
					<div style="margin-bottom: 6px;">
						<span style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: var(--color-primary-dark); background-color: var(--color-primary-light); padding: 2px 8px; border: 1px solid var(--color-primary-light); border-radius: 30px; letter-spacing: 0.5px;">
							🚜 Investiție Locală
						</span>
					</div>
					<?php the_title( '<h1 class="entry-title" style="font-size: 2.25rem; margin-bottom: 12px; font-weight: 900; font-family: var(--font-heading); line-height: 1.2;">', '</h1>' ); ?>
					<div style="width: 50px; height: 3px; background-color: var(--color-primary); margin: 0 auto; border-radius: 3px;"></div>
				</header>

				<!-- Featured Image -->
				<?php if ( has_post_thumbnail() ) : ?>
					<div style="margin-bottom: 24px; border: 1px solid var(--color-border); border-radius: var(--border-radius-lg); overflow: hidden;">
						<?php the_post_thumbnail( 'large', array( 'style' => 'width:100%; height:auto; display:block;' ) ); ?>
					</div>
				<?php endif; ?>

				<!-- Informații Proiect & Localizare (Grid 2 coloane) -->
				<?php if ( ! empty( $stadiu ) || ! empty( $buget ) || ! empty( $sursa ) || ( ! empty( $lat ) && ! empty( $lng ) ) ) : ?>
					<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 16px; margin-bottom: 24px; align-items: stretch;">
						
						<!-- Caseta Detalii Proiect -->
						<?php if ( ! empty( $stadiu ) || ! empty( $buget ) || ! empty( $sursa ) ) : ?>
							<div class="card" style="padding: 20px; display: flex; flex-direction: column; justify-content: center;">
								<h3 style="font-size: 1.1rem; margin-bottom: 16px; border-bottom: 2px solid var(--color-border); padding-bottom: 6px; font-weight: 800; font-family: var(--font-heading);">DETALII INVESTIȚIE</h3>
								
								<ul style="list-style: none; display: flex; flex-direction: column; gap: 12px; padding: 0; margin: 0;">
									<?php if ( ! empty( $stadiu ) ) : ?>
										<li style="display: flex; align-items: center; gap: 10px;">
											<span style="font-size: 1.3rem; line-height: 1;">📊</span>
											<div>
												<div style="font-size: 0.75rem; color: var(--color-text-muted); line-height: 1.1;">Stadiu Implementare</div>
												<div style="font-weight: 700; font-size: 0.95rem; color: var(--color-text-dark);"><?php echo esc_html( $stadiu ); ?></div>
											</div>
										</li>
									<?php endif; ?>

									<?php if ( ! empty( $buget ) ) : ?>
										<li style="display: flex; align-items: center; gap: 10px;">
											<span style="font-size: 1.3rem; line-height: 1;">💰</span>
											<div>
												<div style="font-size: 0.75rem; color: var(--color-text-muted); line-height: 1.1;">Buget Proiect</div>
												<div style="font-weight: 700; font-size: 0.95rem; color: var(--color-text-dark);"><?php echo esc_html( $buget ); ?></div>
											</div>
										</li>
									<?php endif; ?>

									<?php if ( ! empty( $sursa ) ) : ?>
										<li style="display: flex; align-items: center; gap: 10px;">
											<span style="font-size: 1.3rem; line-height: 1;">🏛️</span>
											<div>
												<div style="font-size: 0.75rem; color: var(--color-text-muted); line-height: 1.1;">Sursă Finanțare</div>
												<div style="font-weight: 700; font-size: 0.95rem; color: var(--color-text-dark);"><?php echo esc_html( $sursa ); ?></div>
											</div>
										</li>
									<?php endif; ?>
								</ul>
							</div>
						<?php endif; ?>

						<!-- Caseta Harta Satelit -->
						<?php if ( ! empty( $lat ) && ! empty( $lng ) ) : ?>
							<div class="card" style="padding: 16px; display: flex; flex-direction: column; justify-content: space-between;">
								<h3 style="font-size: 1.1rem; margin-bottom: 12px; padding-bottom: 6px; border-bottom: 1px solid var(--color-border); font-family: var(--font-heading); font-weight: 800;">LOCALIZARE PE SATELIT</h3>
								<div id="single-map" style="height: 250px; border: 1px solid var(--color-border); border-radius: var(--border-radius-md); overflow: hidden; z-index: 10; width: 100%;"></div>
							</div>
						<?php endif; ?>

					</div>
				<?php endif; ?>

				<!-- Descriere completă full-width -->
				<div class="card" style="padding: 20px; font-size: 0.95rem; line-height: 1.7; margin-bottom: 24px;">
					<h3 style="margin-bottom: 12px; border-bottom: 2px solid var(--color-border); padding-bottom: 6px; font-size: 1.15rem; font-weight: 800; font-family: var(--font-heading);">DESCRIERE PROIECT</h3>
					<div class="entry-content">
						<?php the_content(); ?>
					</div>
				</div>

				<?php
				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;
				?>
			</article>
		<?php
		endwhile;
		?>
	</div>
</main>

<?php
get_footer();
