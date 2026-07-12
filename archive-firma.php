<?php
/**
 * The template for displaying archive pages for firms (afaceri-locale).
 *
 * @package Brezoaele_V2
 */

get_header();
?>

<main id="primary" class="site-main" style="padding: 40px 0; background-color: var(--color-bg);">
	<div class="container">
		
		<header class="page-header" style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
			<div>
				<h1 class="page-title" style="font-size: 2.5rem; margin-bottom: 6px; font-weight: 800; font-family: var(--font-heading);">Ghidul Afacerilor & Producătorilor</h1>
				<p style="color: var(--color-text-muted); font-size: 0.95rem;">Descoperă serviciile locale și cumpără legume proaspete direct de la producătorii din comuna Brezoaele.</p>
			</div>
			<a href="<?php echo esc_url( home_url( '/harta-servicii' ) ); ?>" class="btn btn-primary">
				🗺️ Vezi pe Hartă
			</a>
		</header>

		<!-- Filtre Categorii -->
		<div style="margin-bottom: 24px; display: flex; gap: 10px; flex-wrap: wrap;">
			<a href="<?php echo esc_url( get_post_type_archive_link( 'firma' ) ); ?>" class="btn btn-primary" style="padding: 6px 12px; font-size: 0.8rem;">Toate</a>
			
			<?php
			$terms = get_terms( array(
				'taxonomy'   => 'tip_afacere',
				'hide_empty' => false,
			) );
			foreach ( $terms as $term ) :
				$term_link = get_term_link( $term );
				if ( ! is_wp_error( $term_link ) ) :
			?>
					<a href="<?php echo esc_url( $term_link ); ?>" class="btn btn-secondary-outline" style="background-color: #ffffff; padding: 6px 12px; font-size: 0.8rem; color: var(--color-text-dark);">
						<?php echo esc_html( $term->name ); ?> (<?php echo $term->count; ?>)
					</a>
			<?php
				endif;
			endforeach;
			?>
		</div>

		<div class="grid grid-3">
			<?php
			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
					
					// Preluăm tipul afacerii din taxonomie
					$business_types = get_the_terms( get_the_ID(), 'tip_afacere' );
					$is_producer    = false;
					$type_label     = 'Afacere';
					
					if ( $business_types && ! is_wp_error( $business_types ) ) {
						$type_label = $business_types[0]->name;
						if ( in_array( $business_types[0]->slug, array( 'producator', 'legumicultor' ) ) ) {
							$is_producer = true;
						}
					}
					
					// Preluăm metadatele
					$telefon = get_post_meta( get_the_ID(), '_locatie_telefon', true );
					$program = get_post_meta( get_the_ID(), '_locatie_program', true );
			?>
					<article class="card" style="display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid <?php echo $is_producer ? 'var(--color-primary)' : 'var(--color-border)'; ?>;">
						<div>
							<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
								<span style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); letter-spacing: 0.5px;">
									<?php echo esc_html( $type_label ); ?>
								</span>
								<?php if ( $is_producer ) : ?>
									<span style="background-color: var(--color-primary-light); color: var(--color-primary-dark); font-size: 0.7rem; font-weight: 800; padding: 2px 8px; border: 1px solid var(--color-primary-light); border-radius: 30px; text-transform: uppercase;">
										🚜 Producător
									</span>
								<?php endif; ?>
							</div>

							<h3 style="margin: 6px 0 10px 0; font-size: 1.15rem; line-height: 1.25; font-weight: 800; font-family: var(--font-heading);">
								<a href="<?php the_permalink(); ?>" style="color: var(--color-text-dark); text-decoration: none;"><?php the_title(); ?></a>
							</h3>
							
							<p style="color: var(--color-text-muted); font-size: 0.85rem; margin-bottom: 16px; line-height: 1.5;">
								<?php echo wp_trim_words( get_the_excerpt(), 18, '...' ); ?>
							</p>
						</div>

						<div style="border-top: 1px solid var(--color-border); padding-top: 12px; margin-top: 8px; font-size: 0.8rem; color: var(--color-text-muted);">
							<?php if ( ! empty( $program ) ) : ?>
								<div style="margin-bottom: 4px;">
									🕒 <b>Program:</b> <?php echo esc_html( $program ); ?>
								</div>
							<?php endif; ?>
							<?php if ( ! empty( $telefon ) ) : ?>
								<div style="margin-bottom: 10px;">
									📞 <b>Telefon:</b> <?php echo esc_html( $telefon ); ?>
								</div>
							<?php endif; ?>
							<a href="<?php the_permalink(); ?>" class="btn btn-primary" style="width: 100%;">Vezi Profil Complet</a>
						</div>
					</article>
			<?php
				endwhile;
			else :
			?>
				<div style="grid-column: 1 / -1; text-align: center; padding: 40px 0; background-color: var(--color-card); border: 1px solid var(--color-border); border-radius: var(--border-radius-lg);">
					<div style="font-size: 3rem; margin-bottom: 12px;">🏢</div>
					<h3>Momentan nu sunt afaceri înregistrate</h3>
					<p style="color: var(--color-text-muted); font-size: 0.95rem;">Adaugă primele afaceri din panoul de administrare.</p>
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
