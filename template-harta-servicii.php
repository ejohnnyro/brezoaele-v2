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
			<p style="color: var(--color-text-muted); margin-bottom: 16px;">Explorează producătorii locali, instituțiile publice și obiectivele de interes din comuna Brezoaele.</p>
			<div style="display: flex; justify-content: center; gap: 12px; margin-bottom: 20px;">
				<a href="<?php echo esc_url( home_url( '/solicita-adaugare-afacere/' ) ); ?>" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 800; font-size: 0.85rem; padding: 10px 20px; border-radius: var(--border-radius-md);">
					➕ Solicită Adăugare Afacere Nouă
				</a>
			</div>
			<div style="width: 50px; height: 3px; background-color: var(--color-primary); margin: 0 auto; border-radius: 3px;"></div>
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
			<div style="display: flex; align-items: center; gap: 8px;">
				<span style="width: 12px; height: 12px; background-color: #dc2626; display: inline-block; border: 1px solid var(--color-border); border-radius: 50%;"></span>
				<span>Investiții & Proiecte Locale</span>
			</div>
		</div>

		<!-- Containerul Hărții -->
		<div id="map" style="height: 600px; width: 100%; border: 1px solid var(--color-border); border-radius: var(--border-radius-lg); box-shadow: var(--shadow-md); z-index: 10; overflow: hidden; margin-bottom: 30px;"></div>

		<!-- Casetă Căutare Realtime -->
		<div style="margin-top: 40px; margin-bottom: 30px; display: flex; justify-content: center; width: 100%;">
			<div style="position: relative; width: 100%; max-width: 500px;">
				<input type="text" id="business-search-input" placeholder="Caută producători, servicii, instituții..." style="width: 100%; padding: 12px 16px 12px 48px; border: 1px solid var(--color-border); border-radius: var(--border-radius-md); font-size: 1rem; box-shadow: var(--shadow-sm); font-family: var(--font-heading); outline: none; transition: all 0.2s ease;" />
				<span style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); font-size: 1.2rem; pointer-events: none; opacity: 0.7;">🔍</span>
			</div>
		</div>

		<!-- Grid Carduri Afaceri / Servicii -->
		<div class="grid grid-3" id="business-cards-grid">
			<?php
			$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
			if ( is_front_page() ) {
				$paged = ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1;
			}
			$args = array(
				'post_type'      => array( 'firma', 'investitie' ),
				'posts_per_page' => 6,
				'paged'          => $paged,
				'post_status'    => 'publish',
				'orderby'        => 'title',
				'order'          => 'ASC',
			);
			$query = new WP_Query( $args );
			
			if ( $query->have_posts() ) :
				while ( $query->have_posts() ) :
					$query->the_post();
					
					$is_producer  = false;
					$type_label   = 'Afacere';
					$type_slug    = 'generic';
					$border_color = 'var(--color-border)';
					
					if ( get_post_type() === 'investitie' ) {
						$type_label   = 'Proiect / Investiție';
						$type_slug    = 'investitie';
						$border_color = '#dc2626'; // Roșu pentru investiții
						
						$telefon = '';
						$stadiu  = get_post_meta( get_the_ID(), '_investitie_stadiu', true );
						$program = ! empty( $stadiu ) ? 'Stadiu: ' . $stadiu : '';
						$buget   = get_post_meta( get_the_ID(), '_investitie_buget', true );
						if ( ! empty( $buget ) ) {
							$program .= ' | Buget: ' . $buget;
						}
					} else {
						// Preluăm tipul afacerii din taxonomie
						$business_types = get_the_terms( get_the_ID(), 'tip_afacere' );
						if ( $business_types && ! is_wp_error( $business_types ) ) {
							$type_label = $business_types[0]->name;
							$type_slug  = $business_types[0]->slug;
							if ( in_array( $type_slug, array( 'producator', 'legumicultor' ) ) ) {
								$is_producer = true;
								$border_color = 'var(--color-primary)';
							}
						}
						
						// Preluăm metadatele
						$telefon = get_post_meta( get_the_ID(), '_locatie_telefon', true );
						$program = get_post_meta( get_the_ID(), '_locatie_program', true );
					}
			?>
					<article class="card business-card-item" data-title="<?php echo esc_attr( strtolower( get_the_title() ) ); ?>" data-type="<?php echo esc_attr( strtolower( $type_label ) ); ?> <?php echo esc_attr( $type_slug ); ?>" data-excerpt="<?php echo esc_attr( strtolower( get_the_excerpt() ) ); ?>" style="display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid <?php echo $border_color; ?>;">
						<div>
							<?php if ( has_post_thumbnail() ) : ?>
								<a href="<?php the_permalink(); ?>" style="display: block; transition: opacity 0.2s ease;" onmouseover="this.style.opacity='0.95';" onmouseout="this.style.opacity='1';">
									<div class="card-image-wrapper" style="margin-bottom: 12px; border-radius: var(--border-radius-md); overflow: hidden; aspect-ratio: 16/9; border: 1px solid var(--color-border);">
										<?php the_post_thumbnail( 'medium', array( 'style' => 'width: 100%; height: 100%; object-fit: cover;' ) ); ?>
									</div>
								</a>
							<?php endif; ?>

							<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
								<span style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); letter-spacing: 0.5px;">
									<?php echo esc_html( $type_label ); ?>
								</span>
								<?php if ( $is_producer ) : ?>
									<span style="background-color: var(--color-primary-light); color: var(--color-primary-dark); font-size: 0.7rem; font-weight: 800; padding: 2px 8px; border: 1px solid var(--color-primary-light); border-radius: 30px; text-transform: uppercase;">
										🚜 Producător
									</span>
								<?php elseif ( get_post_type() === 'investitie' ) : ?>
									<span style="background-color: #fee2e2; color: #dc2626; font-size: 0.7rem; font-weight: 800; padding: 2px 8px; border: 1px solid #fee2e2; border-radius: 30px; text-transform: uppercase;">
										📊 Proiect
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
									<?php echo ( get_post_type() === 'investitie' ) ? '📊' : '🕒'; ?> <b><?php echo ( get_post_type() === 'investitie' ) ? 'Status & Detalii:' : 'Program:'; ?></b> <?php echo esc_html( $program ); ?>
								</div>
							<?php endif; ?>
							<?php if ( ! empty( $telefon ) ) : ?>
								<div style="margin-bottom: 10px;">
									📞 <b>Telefon:</b> <?php echo esc_html( $telefon ); ?>
								</div>
							<?php endif; ?>
							<a href="<?php the_permalink(); ?>" class="btn btn-primary" style="width: 100%;"><?php echo ( get_post_type() === 'investitie' ) ? 'Vezi Detalii Proiect' : 'Vezi Profil Complet'; ?></a>
						</div>
					</article>
			<?php
				endwhile;
				wp_reset_postdata();
			else :
			?>
				<div style="grid-column: 1 / -1; text-align: center; padding: 40px 0; background-color: var(--color-card); border: 1px solid var(--color-border); border-radius: var(--border-radius-lg);">
					<div style="font-size: 3rem; margin-bottom: 12px;">🏢</div>
					<h3>Momentan nu sunt afaceri înregistrate</h3>
					<p style="color: var(--color-text-muted); font-size: 0.95rem;">Adaugă primele afaceri din panoul de administrare.</p>
				</div>
			<?php endif; ?>
		</div>

		<!-- Mesaj Căutare Fără Rezultate -->
		<div id="no-results-message" style="display: none; text-align: center; padding: 40px 0; background-color: var(--color-card); border: 1px solid var(--color-border); border-radius: var(--border-radius-lg); margin-top: 20px;">
			<div style="font-size: 3rem; margin-bottom: 12px;">🔍</div>
			<h3 style="font-family: var(--font-heading); font-weight: 800;">Nu s-au găsit rezultate</h3>
			<p style="color: var(--color-text-muted); font-size: 0.95rem;">Încearcă să cauți alte cuvinte cheie sau verifică scrierea corectă.</p>
		</div>

		<!-- Paginație Carduri -->
		<div style="margin-top: 32px; display: flex; justify-content: center; width: 100%;" class="navigation pagination">
			<?php 
			echo paginate_links( array(
				'total'     => $query->max_num_pages,
				'current'   => $paged,
				'mid_size'  => 2,
				'prev_text' => __( '&larr; Înapoi', 'brezoaele-v2' ),
				'next_text' => __( 'Înainte &rarr;', 'brezoaele-v2' ),
				'type'      => 'plain',
			) );
			?>
		</div>

	</div>
</main>

<?php
get_footer();
