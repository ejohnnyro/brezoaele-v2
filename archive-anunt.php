<?php
/**
 * The template for displaying archive pages for adverts.
 *
 * @package Brezoaele_V2
 */

get_header();
?>

<main id="primary" class="site-main" style="padding: 40px 0; background-color: var(--color-bg);">
	<div class="container">
		
		<header class="page-header" style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
			<div>
				<h1 class="page-title" style="font-size: 2.5rem; margin-bottom: 6px; font-weight: 800; font-family: var(--font-heading);">Piața Locală</h1>
				<p style="color: var(--color-text-muted); font-size: 0.95rem;">Vezi toate anunțurile de vânzare, cumpărare sau servicii postate de locuitorii comunei Brezoaele.</p>
			</div>
			<a href="<?php echo esc_url( home_url( '/adauga-anunt' ) ); ?>" class="btn btn-primary">
				➕ Adaugă Anunț Nou
			</a>
		</header>

		<div class="grid grid-3">
			<?php
			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
					
					// Preluăm metadatele
					$pret    = get_post_meta( get_the_ID(), '_anunt_pret', true );
					$locatie = get_post_meta( get_the_ID(), '_anunt_locatie', true );
			?>
					<article class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
						<div>
							<?php if ( has_post_thumbnail() ) : ?>
								<div style="margin-bottom: 12px; border: 1px solid var(--color-border); height: 180px; overflow: hidden;">
									<?php the_post_thumbnail( 'medium', array( 'style' => 'width:100%; height:100%; object-fit: cover;' ) ); ?>
								</div>
							<?php else : ?>
								<div style="margin-bottom: 12px; border: 1px dashed var(--color-border); background-color: #f1f5f9; height: 180px; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: #94a3b8; border-radius: var(--border-radius-md);">
									📦
								</div>
							<?php endif; ?>

							<div style="display: flex; justify-content: space-between; align-items: center; gap: 8px; margin-bottom: 4px;">
								<span style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: var(--color-primary-dark); background-color: var(--color-primary-light); padding: 2px 8px; border: 1px solid var(--color-primary-light); border-radius: 30px;">
									<?php
									$terms = get_the_terms( get_the_ID(), 'categorie_anunt' );
									if ( $terms && ! is_wp_error( $terms ) ) {
										echo esc_html( $terms[0]->name );
									} else {
										echo 'Diverse';
									}
									?>
								</span>
								<?php if ( ! empty( $locatie ) ) : ?>
									<span style="font-size: 0.8rem; color: var(--color-text-muted); font-weight: 600;">📍 <?php echo esc_html( $locatie ); ?></span>
								<?php endif; ?>
							</div>

							<h3 style="margin: 6px 0 10px 0; font-size: 1.15rem; line-height: 1.25; font-weight: 800; font-family: var(--font-heading);">
								<a href="<?php the_permalink(); ?>" style="color: var(--color-text-dark); text-decoration: none;"><?php the_title(); ?></a>
							</h3>
							
							<p style="color: var(--color-text-muted); font-size: 0.85rem; margin-bottom: 16px; line-height: 1.5;">
								<?php echo wp_trim_words( get_the_excerpt(), 15, '...' ); ?>
							</p>
						</div>

						<div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--color-border); padding-top: 12px; margin-top: 10px;">
							<div style="font-weight: 900; font-size: 1.15rem; color: var(--color-primary-dark);">
								<?php echo ! empty( $pret ) ? esc_html( $pret ) . ' RON' : 'Negociabil'; ?>
							</div>
							<a href="<?php the_permalink(); ?>" style="font-weight: 800; font-size: 0.8rem; color: var(--color-text-dark); text-transform: uppercase; text-decoration: underline;">Vezi Detalii &rarr;</a>
						</div>
					</article>
			<?php
				endwhile;
			else :
			?>
				<div style="grid-column: 1 / -1; text-align: center; padding: 40px 0; background-color: var(--color-card); border: 1px solid var(--color-border); border-radius: var(--border-radius-lg);">
					<div style="font-size: 3rem; margin-bottom: 12px;">📣</div>
					<h3>Momentan nu sunt anunțuri active</h3>
					<p style="color: var(--color-text-muted); font-size: 0.95rem; margin-bottom: 20px;">Fii primul care publică un anunț în comunitate!</p>
					<a href="<?php echo esc_url( home_url( '/adauga-anunt' ) ); ?>" class="btn btn-primary">➕ Publică Anunț</a>
				</div>
			<?php endif; ?>
		</div>

		<div style="margin-top: 32px; display: flex; justify-content: center;">
			<?php the_posts_pagination( array(
				'mid_size'  => 2,
				'prev_text' => __( '&larr; Înapoi', 'brezoaele-v2' ),
				'next_text' => __( 'Înainte &rarr;', 'brezoaele-v2' ),
			) ); ?>
		</div>

	</div>
</main>

<?php
get_footer();
