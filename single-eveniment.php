<?php
/**
 * Single Event Template - Design Responsiv Desktop & Mobil cu Layout Perfect
 *
 * @package Brezoaele_V2
 */

get_header();

while ( have_posts() ) :
	the_post();

	$post_id         = get_the_ID();
	$data_start      = get_post_meta( $post_id, '_eveniment_data_start', true );
	$data_sfarsit    = get_post_meta( $post_id, '_eveniment_data_sfarsit', true );
	$ora_start       = get_post_meta( $post_id, '_eveniment_ora_start', true );
	$ora_sfarsit     = get_post_meta( $post_id, '_eveniment_ora_sfarsit', true );
	$toata_ziua      = get_post_meta( $post_id, '_eveniment_toata_ziua', true );
	$repetitiv_anual = get_post_meta( $post_id, '_eveniment_repetitiv_anual', true );
	$locatie         = get_post_meta( $post_id, '_eveniment_locatie', true );
	$harta_lat       = get_post_meta( $post_id, '_eveniment_harta_lat', true );
	$harta_lng       = get_post_meta( $post_id, '_eveniment_harta_lng', true );
	$organizator     = get_post_meta( $post_id, '_eveniment_organizator', true );
	$contact         = get_post_meta( $post_id, '_eveniment_contact', true );
	$galerie_ids     = get_post_meta( $post_id, '_eveniment_galerie', true );

	$terms     = get_the_terms( $post_id, 'tip_eveniment' );
	$cat_label = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : 'General';

	$featured_img_url = get_the_post_thumbnail_url( $post_id, 'full' );
	if ( ! $featured_img_url ) {
		$featured_img_url = get_template_directory_uri() . '/images/hero-biserica.png';
	}

	// Calculare Google Calendar Link
	$gcal_title = rawurlencode( get_the_title() );
	$gcal_details = rawurlencode( wp_strip_all_tags( get_the_content() ) );
	$gcal_location = rawurlencode( $locatie );

	$dt_s = $data_start ? str_replace( '-', '', $data_start ) : date( 'Ymd' );
	if ( '1' === $toata_ziua || empty( $ora_start ) ) {
		$dt_e = date( 'Ymd', strtotime( ( $data_start ? $data_start : 'now' ) . ' +1 day' ) );
		$gcal_dates = "{$dt_s}/{$dt_e}";
	} else {
		$t_s = str_replace( ':', '', $ora_start ) . '00';
		$t_e = ! empty( $ora_sfarsit ) ? str_replace( ':', '', $ora_sfarsit ) . '00' : date( 'His', strtotime( $ora_start . ' +2 hours' ) );
		$gcal_dates = "{$dt_s}T{$t_s}/{$dt_s}T{$t_e}";
	}

	$gcal_url = "https://calendar.google.com/calendar/render?action=TEMPLATE&text={$gcal_title}&dates={$gcal_dates}&details={$gcal_details}&location={$gcal_location}";
	$ics_url  = add_query_arg( array( 'action' => 'download_ics', 'event_id' => $post_id ), home_url( '/' ) );
	?>

	<style>
	/* Grid & Layout Responsiv Single Eveniment */
	.brz-single-ev-header {
		margin-bottom: 16px;
	}

	.brz-single-ev-badges {
		display: flex;
		gap: 8px;
		flex-wrap: wrap;
		margin-bottom: 12px;
	}

	.brz-single-ev-title {
		font-size: 2.2rem;
		font-weight: 800;
		color: var(--color-text-dark);
		margin-bottom: 0;
		line-height: 1.25;
	}

	.brz-single-ev-hero-img {
		width: 100%;
		aspect-ratio: 16 / 9;
		border-radius: var(--border-radius-lg);
		overflow: hidden;
		border: 1px solid var(--color-border);
		box-shadow: var(--shadow-md);
		background: #0f172a;
		margin-bottom: 24px;
	}

	.brz-single-ev-hero-img img {
		width: 100%;
		height: 100%;
		object-fit: cover;
		display: block;
	}

	/* Pe Desktop (> 900px): Stânga Conținut (2fr), Dreapta Sidebar (1fr) */
	.brz-single-ev-grid {
		display: grid;
		grid-template-columns: 2fr 1fr;
		gap: 30px;
		align-items: start;
	}

	.brz-single-ev-left-col {
		display: flex;
		flex-direction: column;
	}

	.brz-single-ev-sidebar-card {
		background: #ffffff;
		border-radius: var(--border-radius-lg);
		border: 1px solid var(--color-border);
		box-shadow: var(--shadow-md);
		padding: 24px;
		position: sticky;
		top: 20px;
	}

	/* Stil Galerie Foto */
	.brz-single-ev-gallery-grid {
		display: grid;
		grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
		gap: 12px;
		margin-top: 16px;
	}

	.brz-single-ev-gallery-item {
		height: 120px;
		border-radius: 10px;
		overflow: hidden;
		border: 1px solid var(--color-border);
		box-shadow: var(--shadow-sm);
		transition: transform 0.2s ease, box-shadow 0.2s ease;
		cursor: pointer;
	}

	.brz-single-ev-gallery-item:hover {
		transform: scale(1.03);
		box-shadow: var(--shadow-md);
	}

	.brz-single-ev-gallery-item img {
		width: 100%;
		height: 100%;
		object-fit: cover;
	}

	/* Modificări specifice Mobil (< 900px) */
	@media (max-width: 900px) {
		.brz-single-ev-grid {
			display: flex;
			flex-direction: column;
			gap: 20px;
		}

		.brz-single-ev-left-col {
			display: contents;
		}

		/* Ordinea pe mobil: 
		   1. Titlu Eveniment 
		   2. Imagine Reprezentativă 
		   3. Detalii Eveniment (Sidebar Program + Hartă) 
		   4. Conținut Articol (Galerie foto + Text)
		*/
		.brz-single-ev-header {
			order: 1;
		}

		.brz-single-ev-hero-img {
			order: 2;
			margin-bottom: 0;
		}

		.brz-single-ev-sidebar {
			order: 3;
			width: 100%;
		}

		.brz-single-ev-content-card {
			order: 4;
			width: 100%;
		}

		.brz-single-ev-title {
			font-size: 1.6rem;
		}

		.brz-single-ev-sidebar-card {
			position: static;
		}
	}
	</style>

	<main id="primary" class="site-main" style="padding: 40px 0; background-color: var(--color-bg);">
		<div class="container">

			<!-- Navigare Înapoi -->
			<div style="margin-bottom: 20px;">
				<a href="<?php echo esc_url( home_url( '/calendar-evenimente/' ) ); ?>" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; color: var(--color-primary); text-decoration: none; font-size: 0.9rem;">
					&larr; Înapoi la Calendarul de Evenimente
				</a>
			</div>

			<!-- Grid Principal: Stânga Conținut (2fr), Dreapta Sidebar (1fr) pe Desktop -->
			<div class="brz-single-ev-grid">

				<!-- COLOANA STÂNGĂ PE DESKTOP (2fr): Titlu, Imagine, Conținut & Galerie -->
				<div class="brz-single-ev-left-col">

					<!-- 1. Header (Titlu & Badges Categorie) -->
					<header class="brz-single-ev-header">
						<div class="brz-single-ev-badges">
							<span style="background: var(--color-primary); color: #ffffff; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: 800; text-transform: uppercase;">
								<?php echo esc_html( $cat_label ); ?>
							</span>
							<?php if ( '1' === $repetitiv_anual ) : ?>
								<span style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: 800;">
									🔁 Se repetă anual
								</span>
							<?php endif; ?>
						</div>
						<h1 class="brz-single-ev-title">
							<?php the_title(); ?>
						</h1>
					</header>

					<!-- 2. Banner Imagine Reprezentativă (16:9, încadrată pe latimea coloanei stânga) -->
					<div class="brz-single-ev-hero-img">
						<img src="<?php echo esc_url( $featured_img_url ); ?>" alt="<?php the_title_attribute(); ?>">
					</div>

					<!-- 3. Casetă Conținut Articol & Galerie Foto Suplimentară -->
					<div class="brz-single-ev-content-card" style="background: #ffffff; border-radius: var(--border-radius-lg); border: 1px solid var(--color-border); box-shadow: var(--shadow-md); padding: 30px;">
						
						<!-- Galerie Foto Suplimentară (dacă există) -->
						<?php if ( ! empty( $galerie_ids ) && is_array( $galerie_ids ) ) : ?>
							<div style="margin-bottom: 30px; padding-bottom: 24px; border-bottom: 1px solid var(--color-border);">
								<h3 style="font-size: 1.3rem; font-weight: 800; margin-bottom: 6px; color: var(--color-text-dark); display: flex; align-items: center; gap: 8px;">
									📸 Galerie Foto Eveniment
								</h3>
								<p style="color: var(--color-text-muted); font-size: 0.9rem; margin-bottom: 12px;">Imagini de la desfășurarea evenimentului</p>
								
								<div class="brz-single-ev-gallery-grid">
									<?php foreach ( $galerie_ids as $attachment_id ) : ?>
										<?php
										$full_src  = wp_get_attachment_image_url( $attachment_id, 'full' );
										$thumb_src = wp_get_attachment_image_url( $attachment_id, 'medium' );
										if ( $thumb_src ) :
											?>
											<a href="<?php echo esc_url( $full_src ); ?>" target="_blank" class="brz-single-ev-gallery-item" title="Click pentru mărire">
												<img src="<?php echo esc_url( $thumb_src ); ?>" alt="Galerie <?php the_title_attribute(); ?>">
											</a>
										<?php endif; ?>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endif; ?>

						<!-- Conținut Text Articol -->
						<div class="entry-content" style="font-size: 1.05rem; line-height: 1.7; color: #334155;">
							<?php the_content(); ?>
						</div>

					</div>

				</div>

				<!-- COLOANA DREAPTĂ PE DESKTOP (1fr): Sidebar cu Program, Hartă & Share -->
				<div class="brz-single-ev-sidebar">
					<div class="brz-single-ev-sidebar-card">
						<h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--color-border); color: var(--color-primary);">
							📅 Program & Info Utile
						</h3>

						<ul style="list-style: none; padding: 0; margin: 0 0 24px; display: flex; flex-direction: column; gap: 16px;">
							<li style="display: flex; align-items: flex-start; gap: 12px;">
								<span style="font-size: 1.4rem;">📅</span>
								<div>
									<strong style="display: block; font-size: 0.85rem; color: #64748b; text-transform: uppercase;">Data</strong>
									<span style="font-weight: 800; font-size: 1.05rem; color: var(--color-text-dark);">
										<?php echo esc_html( date( 'd F Y', strtotime( $data_start ) ) ); ?>
										<?php if ( $data_sfarsit && $data_sfarsit !== $data_start ) : ?>
											 - <?php echo esc_html( date( 'd F Y', strtotime( $data_sfarsit ) ) ); ?>
										<?php endif; ?>
									</span>
								</div>
							</li>

							<li style="display: flex; align-items: flex-start; gap: 12px;">
								<span style="font-size: 1.4rem;">⏰</span>
								<div>
									<strong style="display: block; font-size: 0.85rem; color: #64748b; text-transform: uppercase;">Orar</strong>
									<span style="font-weight: 800; font-size: 1.05rem; color: var(--color-text-dark);">
										<?php
										if ( '1' === $toata_ziua ) {
											echo '⏳ Toată ziua';
										} elseif ( $ora_start ) {
											echo esc_html( $ora_start ) . ( $ora_sfarsit ? ' - ' . esc_html( $ora_sfarsit ) : '' );
										} else {
											echo 'Fără orar specificat';
										}
										?>
									</span>
								</div>
							</li>

							<?php if ( $locatie ) : ?>
								<li style="display: flex; align-items: flex-start; gap: 12px;">
									<span style="font-size: 1.4rem;">📍</span>
									<div>
										<strong style="display: block; font-size: 0.85rem; color: #64748b; text-transform: uppercase;">Locație</strong>
										<span style="font-weight: 700; font-size: 0.95rem; color: var(--color-text-dark);">
											<?php echo esc_html( $locatie ); ?>
										</span>
									</div>
								</li>
							<?php endif; ?>

							<?php if ( $organizator ) : ?>
								<li style="display: flex; align-items: flex-start; gap: 12px;">
									<span style="font-size: 1.4rem;">🏛️</span>
									<div>
										<strong style="display: block; font-size: 0.85rem; color: #64748b; text-transform: uppercase;">Organizator</strong>
										<span style="font-weight: 700; font-size: 0.95rem; color: var(--color-text-dark);">
											<?php echo esc_html( $organizator ); ?>
										</span>
									</div>
								</li>
							<?php endif; ?>

							<?php if ( $contact ) : ?>
								<li style="display: flex; align-items: flex-start; gap: 12px;">
									<span style="font-size: 1.4rem;">📞</span>
									<div>
										<strong style="display: block; font-size: 0.85rem; color: #64748b; text-transform: uppercase;">Contact</strong>
										<span style="font-weight: 700; font-size: 0.95rem; color: var(--color-text-dark);">
											<?php echo esc_html( $contact ); ?>
										</span>
									</div>
								</li>
							<?php endif; ?>
						</ul>

						<!-- Butoane Salvare în Calendar -->
						<div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 24px;">
							<a href="<?php echo esc_url( $gcal_url ); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary" style="display: flex; align-items: center; justify-content: center; gap: 8px; font-weight: 800; font-size: 0.85rem; padding: 12px; border-radius: var(--border-radius-md); text-decoration: none;">
								📅 Adaugă în Google Calendar
							</a>
							<a href="<?php echo esc_url( $ics_url ); ?>" class="btn" style="display: flex; align-items: center; justify-content: center; gap: 8px; font-weight: 800; font-size: 0.85rem; padding: 12px; border-radius: var(--border-radius-md); background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; text-decoration: none;">
								📥 Descarcă Fișier iCal (.ics)
							</a>
						</div>

						<!-- HARTĂ LOCAȚIE (Înainte de butoanele de Distribuire) -->
						<?php if ( ! empty( $harta_lat ) && ! empty( $harta_lng ) ) : ?>
							<div style="margin-bottom: 24px; padding-top: 20px; border-top: 1px solid var(--color-border);">
								<h4 style="font-size: 1rem; font-weight: 800; margin-bottom: 10px; display: flex; align-items: center; gap: 6px; color: var(--color-text-dark);">
									📍 Locație pe Hartă
								</h4>
								<div id="single-map" style="height: 240px; width: 100%; border-radius: var(--border-radius-md); border: 1px solid var(--color-border); box-shadow: var(--shadow-sm); overflow: hidden; z-index: 1;"></div>
							</div>
						<?php endif; ?>

						<!-- Distribution Social Media -->
						<div style="padding-top: 16px; border-top: 1px solid var(--color-border);">
							<strong style="display: block; font-size: 0.8rem; color: #64748b; text-transform: uppercase; margin-bottom: 10px; text-align: center;">
								Distribuie Evenimentul
							</strong>
							<div style="display: flex; gap: 8px; justify-content: center;">
								<a href="https://api.whatsapp.com/send?text=<?php echo rawurlencode( get_the_title() . ' - ' . get_permalink() ); ?>" target="_blank" rel="noopener" style="background: #25D366; color: #fff; text-decoration: none; padding: 8px 14px; border-radius: 6px; font-weight: 800; font-size: 0.8rem; display: flex; align-items: center; gap: 6px;">
									💬 WhatsApp
								</a>
								<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo rawurlencode( get_permalink() ); ?>" target="_blank" rel="noopener" style="background: #1877F2; color: #fff; text-decoration: none; padding: 8px 14px; border-radius: 6px; font-weight: 800; font-size: 0.8rem; display: flex; align-items: center; gap: 6px;">
									f Facebook
								</a>
							</div>
						</div>

					</div>
				</div>

			</div>

		</div>
	</main>

	<!-- Script de rezervă inițializare Hartă Leaflet în Sidebar -->
	<?php if ( ! empty( $harta_lat ) && ! empty( $harta_lng ) ) : ?>
		<script>
		document.addEventListener("DOMContentLoaded", function() {
			var mapDiv = document.getElementById('single-map');
			if (mapDiv && typeof L !== 'undefined') {
				var lat = <?php echo floatval( $harta_lat ); ?>;
				var lng = <?php echo floatval( $harta_lng ); ?>;
				
				var map = L.map('single-map').setView([lat, lng], 15);
				
				L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
					maxZoom: 19,
					attribution: '© OpenStreetMap'
				}).addTo(map);

				L.marker([lat, lng], {
					title: <?php echo json_encode( get_the_title() ); ?>
				}).addTo(map).bindPopup('<b>' + <?php echo json_encode( get_the_title() ); ?> + '</b>');

				setTimeout(function() {
					map.invalidateSize();
				}, 400);
			}
		});
		</script>
	<?php endif; ?>

	<?php
endwhile;

get_footer();
