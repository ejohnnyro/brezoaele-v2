<?php
/**
 * The template for displaying a single classified ad (anunt).
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
			
			// Preluăm metadatele
			$pret    = get_post_meta( get_the_ID(), '_anunt_pret', true );
			$telefon = get_post_meta( get_the_ID(), '_anunt_telefon', true );
			$locatie = get_post_meta( get_the_ID(), '_anunt_locatie', true );
			$nume    = get_post_meta( get_the_ID(), '_anunt_nume', true );
		?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> style="max-width: 960px; margin: 0 auto;">
				
				<header class="entry-header" style="margin-bottom: 24px;">
					<div style="margin-bottom: 6px;">
						<span style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: var(--color-primary-dark); background-color: var(--color-primary-light); padding: 2px 6px; border: 1px solid var(--color-border); margin-right: 6px;">
							Anunț Vecin
						</span>
						<?php
						$terms = get_the_terms( get_the_ID(), 'categorie_anunt' );
						if ( $terms && ! is_wp_error( $terms ) ) {
							echo '<span style="color: var(--color-text-muted); font-size: 0.85rem;">în</span> ';
							foreach ( $terms as $term ) {
								echo '<span style="font-weight:700; color:var(--color-primary-dark); font-size:0.85rem;">' . esc_html( $term->name ) . '</span> ';
							}
						}
						?>
					</div>
					<?php the_title( '<h1 class="entry-title" style="font-size: 2.25rem; margin-bottom: 6px; font-weight: 900; font-family: var(--font-heading); line-height: 1.2;">', '</h1>' ); ?>
					<div style="font-size: 0.8rem; color: var(--color-text-muted); font-weight: 600;">
						Publicat la <?php echo get_the_date(); ?>
					</div>
				</header>

				<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px; align-items: start;">
					
					<!-- Stânga: Imagine și Descriere -->
					<div>
						<?php if ( has_post_thumbnail() ) : ?>
							<div style="margin-bottom: 16px; border: 2px solid var(--color-border); overflow: hidden;">
								<?php the_post_thumbnail( 'large', array( 'style' => 'width:100%; height:auto; display:block;' ) ); ?>
							</div>
						<?php endif; ?>

						<div class="card" style="padding: 20px; font-size: 0.95rem; line-height: 1.6;">
							<h3 style="margin-bottom: 12px; border-bottom: 2px solid var(--color-border); padding-bottom: 6px; font-size: 1.15rem; font-weight: 800; font-family: var(--font-heading);">DESCRIERE ANUNȚ</h3>
							<div class="entry-content">
								<?php the_content(); ?>
							</div>
						</div>
					</div>

					<!-- Dreapta: Informații Economice & Contact -->
					<div style="display: flex; flex-direction: column; gap: 16px;">
						
						<!-- Caseta Preț -->
						<div class="card" style="padding: 20px; text-align: center; border-top: 4px solid var(--color-primary);">
							<span style="font-size: 0.75rem; text-transform: uppercase; color: var(--color-text-muted); font-weight: 800; letter-spacing: 0.5px;">Preț Solicitat</span>
							<div style="font-size: 2rem; font-weight: 900; color: var(--color-primary-dark); margin: 6px 0;">
								<?php echo ! empty( $pret ) ? esc_html( $pret ) . ' RON' : 'Negociabil'; ?>
							</div>
						</div>

						<!-- Caseta Contact -->
						<div class="card" style="padding: 20px;">
							<h3 style="font-size: 1.1rem; margin-bottom: 16px; border-bottom: 2px solid var(--color-border); padding-bottom: 6px; font-weight: 800; font-family: var(--font-heading);">CONTACT VÂNZĂTOR</h3>
							
							<ul style="list-style: none; display: flex; flex-direction: column; gap: 12px; padding: 0;">
								<?php if ( ! empty( $nume ) ) : ?>
									<li style="display: flex; align-items: center; gap: 10px;">
										<span style="font-size: 1.3rem; line-height: 1;">👤</span>
										<div>
											<div style="font-size: 0.75rem; color: var(--color-text-muted); line-height: 1.1;">Nume Contact</div>
											<div style="font-weight: 700; font-size: 0.95rem; color: var(--color-text-dark);"><?php echo esc_html( $nume ); ?></div>
										</div>
									</li>
								<?php endif; ?>

								<?php if ( ! empty( $locatie ) ) : ?>
									<li style="display: flex; align-items: center; gap: 10px;">
										<span style="font-size: 1.3rem; line-height: 1;">📍</span>
										<div>
											<div style="font-size: 0.75rem; color: var(--color-text-muted); line-height: 1.1;">Locație</div>
											<div style="font-weight: 700; font-size: 0.95rem; color: var(--color-text-dark);"><?php echo esc_html( $locatie ); ?></div>
										</div>
									</li>
								<?php endif; ?>

								<?php if ( ! empty( $telefon ) ) : ?>
									<li style="margin-top: 6px; border-top: 1px dashed var(--color-border); padding-top: 12px; display: flex; flex-direction: column; gap: 6px;">
										<div style="font-size: 0.75rem; color: var(--color-text-muted); line-height: 1.1;">Telefon Vânzător</div>
										<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $telefon ) ); ?>" class="btn btn-primary" style="display: block; text-align: center; font-size: 0.85rem; width: 100%;">
											📞 SUNĂ ACUM: <?php echo esc_html( $telefon ); ?>
										</a>
									</li>
								<?php endif; ?>
							</ul>
						</div>

					</div>
				</div>

			</article>
		<?php
		endwhile;
		?>
	</div>
</main>

<?php
get_footer();
