<?php
/**
 * The template for displaying a single business/producer page.
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
			$telefon  = get_post_meta( get_the_ID(), '_locatie_telefon', true );
			$program  = get_post_meta( get_the_ID(), '_locatie_program', true );
			$website  = get_post_meta( get_the_ID(), '_locatie_website', true );
			$email    = get_post_meta( get_the_ID(), '_locatie_email', true );
			$persoana = get_post_meta( get_the_ID(), '_locatie_persoana_contact', true );
			$lat      = get_post_meta( get_the_ID(), '_locatie_lat', true );
			$lng      = get_post_meta( get_the_ID(), '_locatie_lng', true );
			
			$terms      = get_the_terms( get_the_ID(), 'tip_afacere' );
			$type_label = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : 'Afacere Locală';
		?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> style="max-width: 960px; margin: 0 auto;">
				
				<header class="entry-header" style="margin-bottom: 24px; text-align: center;">
					<div style="margin-bottom: 6px;">
						<span style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: var(--color-primary-dark); background-color: var(--color-primary-light); padding: 2px 8px; border: 1px solid var(--color-primary-light); border-radius: 30px; letter-spacing: 0.5px;">
							<?php echo esc_html( $type_label ); ?>
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

				<!-- Informații Contact & Localizare (Grid 2 coloane) -->
				<?php if ( ! empty( $program ) || ! empty( $telefon ) || ! empty( $website ) || ! empty( $email ) || ! empty( $persoana ) || ( ! empty( $lat ) && ! empty( $lng ) ) ) : ?>
					<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 16px; margin-bottom: 24px; align-items: stretch;">
						
						<!-- Caseta Contact -->
						<?php if ( ! empty( $program ) || ! empty( $telefon ) || ! empty( $website ) || ! empty( $email ) || ! empty( $persoana ) ) : ?>
							<div class="card" style="padding: 20px; display: flex; flex-direction: column; justify-content: center;">
								<h3 style="font-size: 1.1rem; margin-bottom: 16px; border-bottom: 2px solid var(--color-border); padding-bottom: 6px; font-weight: 800; font-family: var(--font-heading);">DATE DE CONTACT</h3>
								
								<ul style="list-style: none; display: flex; flex-direction: column; gap: 12px; padding: 0; margin: 0;">
									<?php if ( ! empty( $program ) ) : ?>
										<li style="display: flex; align-items: center; gap: 10px;">
											<span style="font-size: 1.3rem; line-height: 1;">🕒</span>
											<div>
												<div style="font-size: 0.75rem; color: var(--color-text-muted); line-height: 1.1;">Program de lucru</div>
												<div style="font-weight: 700; font-size: 0.95rem; color: var(--color-text-dark);"><?php echo esc_html( $program ); ?></div>
											</div>
										</li>
									<?php endif; ?>

									<?php if ( ! empty( $telefon ) ) : ?>
										<li style="display: flex; align-items: center; gap: 10px;">
											<span style="font-size: 1.3rem; line-height: 1;">📞</span>
											<div>
												<div style="font-size: 0.75rem; color: var(--color-text-muted); line-height: 1.1;">Număr Telefon</div>
												<div style="font-weight: 700; font-size: 0.95rem;">
													<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $telefon ) ); ?>" style="color: var(--color-primary-dark); text-decoration: underline;">
														<?php echo esc_html( $telefon ); ?>
													</a>
												</div>
											</div>
										</li>
									<?php endif; ?>

									<?php if ( ! empty( $email ) ) : ?>
										<li style="display: flex; align-items: center; gap: 10px;">
											<span style="font-size: 1.3rem; line-height: 1;">✉️</span>
											<div>
												<div style="font-size: 0.75rem; color: var(--color-text-muted); line-height: 1.1;">Adresă Email</div>
												<div style="font-weight: 700; font-size: 0.95rem;">
													<a href="mailto:<?php echo esc_attr( $email ); ?>" style="color: var(--color-primary-dark); text-decoration: underline;">
														<?php echo esc_html( $email ); ?>
													</a>
												</div>
											</div>
										</li>
									<?php endif; ?>

									<?php if ( ! empty( $website ) ) : ?>
										<li style="display: flex; align-items: center; gap: 10px;">
											<span style="font-size: 1.3rem; line-height: 1;">🌐</span>
											<div>
												<div style="font-size: 0.75rem; color: var(--color-text-muted); line-height: 1.1;">Website / Pagina Socială</div>
												<div style="font-weight: 700; font-size: 0.95rem;">
													<a href="<?php echo esc_url( $website ); ?>" target="_blank" rel="noopener noreferrer" style="color: var(--color-primary-dark); text-decoration: underline;">
														Vizitează Pagina &rarr;
													</a>
												</div>
											</div>
										</li>
									<?php endif; ?>

									<?php if ( ! empty( $persoana ) ) : ?>
										<li style="display: flex; align-items: center; gap: 10px;">
											<span style="font-size: 1.3rem; line-height: 1;">👤</span>
											<div>
												<div style="font-size: 0.75rem; color: var(--color-text-muted); line-height: 1.1;">Persoană de contact</div>
												<div style="font-weight: 700; font-size: 0.95rem; color: var(--color-text-dark);"><?php echo esc_html( $persoana ); ?></div>
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
					<h3 style="margin-bottom: 12px; border-bottom: 2px solid var(--color-border); padding-bottom: 6px; font-size: 1.15rem; font-weight: 800; font-family: var(--font-heading);">DETALII ȘI PREZENTARE</h3>
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
