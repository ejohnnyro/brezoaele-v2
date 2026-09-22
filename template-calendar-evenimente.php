<?php
/**
 * Template Name: Calendar Evenimente
 *
 * @package Brezoaele_V2
 */

get_header();
?>

<style>
/* CSS specific pentru Calendarul de Evenimente */
.brz-cal-container {
	background: #ffffff;
	border: 1px solid var(--color-border);
	border-radius: var(--border-radius-lg);
	box-shadow: var(--shadow-md);
	padding: 24px;
	margin-bottom: 40px;
}

.brz-cal-header-bar {
	display: flex;
	justify-content: space-between;
	align-items: center;
	flex-wrap: wrap;
	gap: 16px;
	margin-bottom: 24px;
	padding-bottom: 16px;
	border-bottom: 1px solid var(--color-border);
}

.brz-cal-nav {
	display: flex;
	align-items: center;
	gap: 12px;
}

.brz-cal-nav-btn {
	background: #ffffff;
	border: 1px solid var(--color-border);
	border-radius: var(--border-radius-md);
	padding: 8px 16px;
	font-weight: 700;
	font-size: 0.9rem;
	color: var(--color-text-dark);
	cursor: pointer;
	transition: all 0.2s ease;
	display: inline-flex;
	align-items: center;
	gap: 6px;
}

.brz-cal-nav-btn:hover {
	background: var(--color-primary-light);
	color: var(--color-primary);
	border-color: var(--color-primary);
}

.brz-cal-month-title {
	font-size: 1.6rem;
	font-weight: 800;
	color: var(--color-text-dark);
	margin: 0;
	min-width: 200px;
	text-align: center;
}

.brz-cat-filters {
	display: flex;
	gap: 8px;
	flex-wrap: wrap;
	margin-bottom: 24px;
}

.brz-cat-filter-btn {
	background: var(--color-bg);
	border: 1px solid var(--color-border);
	border-radius: 20px;
	padding: 6px 14px;
	font-size: 0.85rem;
	font-weight: 600;
	color: var(--color-text-dark);
	cursor: pointer;
	transition: all 0.2s ease;
}

.brz-cat-filter-btn:hover, .brz-cat-filter-btn.active {
	background: var(--color-primary);
	color: #ffffff;
	border-color: var(--color-primary);
}

.brz-cal-weekdays {
	display: grid;
	grid-template-columns: repeat(7, 1fr);
	gap: 8px;
	text-align: center;
	font-weight: 800;
	font-size: 0.85rem;
	text-transform: uppercase;
	color: var(--color-text-muted);
	margin-bottom: 12px;
	padding: 0 4px;
}

.brz-cal-grid-wrapper {
	display: grid;
	grid-template-columns: repeat(7, 1fr);
	gap: 8px;
	transition: opacity 0.2s ease;
}

.brz-cal-cell {
	min-height: 110px;
	background: #f8fafc;
	border: 1px solid #e2e8f0;
	border-radius: 8px;
	padding: 6px;
	display: flex;
	flex-direction: column;
	position: relative;
	transition: all 0.15s ease;
}

.brz-cal-cell-empty {
	background: transparent;
	border: none;
}

.brz-cal-cell.has-events {
	background: #ffffff;
	cursor: pointer;
	border-color: #cbd5e1;
}

.brz-cal-cell.has-events:hover {
	border-color: var(--color-primary);
	box-shadow: 0 4px 12px rgba(4, 120, 87, 0.08);
}

.brz-cal-cell.brz-cal-today {
	border: 2px solid var(--color-primary);
	background: #f0fdf4;
}

.brz-cal-date-num {
	font-weight: 800;
	font-size: 0.95rem;
	color: #334155;
	margin-bottom: 4px;
}

.brz-cal-today .brz-cal-date-num {
	color: var(--color-primary);
}

.brz-cal-events-list {
	display: flex;
	flex-direction: column;
	gap: 4px;
	overflow: hidden;
}

.brz-event-pill {
	background: #047857;
	color: #ffffff;
	padding: 3px 6px;
	border-radius: 4px;
	font-size: 0.75rem;
	line-height: 1.2;
	font-weight: 600;
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
	transition: transform 0.1s ease;
}

.brz-event-pill:hover {
	transform: scale(1.02);
}

.brz-event-pill.cat-cultura-traditii { background: #0284c7; }
.brz-event-pill.cat-sarbatoare-locala { background: #d97706; }
.brz-event-pill.cat-sportiv { background: #16a34a; }
.brz-event-pill.cat-targ-balci { background: #9333ea; }
.brz-event-pill.cat-sedinta-transparenta { background: #475569; }
.brz-event-pill.cat-voluntariat-mediu { background: #059669; }
.brz-event-pill.cat-religios { background: #b45309; }

.brz-event-more {
	font-size: 0.7rem;
	font-weight: 700;
	color: var(--color-primary);
	margin-top: 2px;
}

/* Modal Detalii Zi */
.brz-modal-backdrop {
	display: none;
	position: fixed;
	top: 0; left: 0; width: 100%; height: 100%;
	background: rgba(15, 23, 42, 0.6);
	backdrop-filter: blur(4px);
	z-index: 9999;
	align-items: center;
	justify-content: center;
	padding: 20px;
}

.brz-modal-box {
	background: #ffffff;
	border-radius: var(--border-radius-lg);
	box-shadow: var(--shadow-lg);
	width: 100%;
	max-width: 550px;
	max-height: 85vh;
	overflow-y: auto;
	padding: 24px;
	position: relative;
	animation: brzModalFade 0.2s ease;
}

@keyframes brzModalFade {
	from { opacity: 0; transform: translateY(12px); }
	to { opacity: 1; transform: translateY(0); }
}

.brz-modal-close-btn {
	position: absolute;
	top: 16px; right: 16px;
	background: #f1f5f9;
	border: none;
	width: 32px; height: 32px;
	border-radius: 50%;
	font-size: 1.2rem;
	cursor: pointer;
	display: flex; align-items: center; justify-content: center;
	color: #64748b;
}

.brz-modal-close-btn:hover { background: #e2e8f0; color: #0f172a; }

.brz-modal-ev-item {
	border-bottom: 1px solid var(--color-border);
	padding-bottom: 16px;
	margin-bottom: 16px;
}

.brz-modal-ev-item:last-child {
	border-bottom: none;
	margin-bottom: 0;
	padding-bottom: 0;
}

.brz-modal-ev-header {
	display: flex;
	justify-content: space-between;
	align-items: center;
	gap: 8px;
	margin-bottom: 6px;
}

.brz-modal-ev-cat {
	font-size: 0.75rem;
	font-weight: 800;
	text-transform: uppercase;
	color: var(--color-primary);
	background: var(--color-primary-light);
	padding: 2px 8px;
	border-radius: 4px;
}

.brz-modal-badges { display: flex; gap: 6px; flex-wrap: wrap; }
.brz-badge-time { font-size: 0.75rem; font-weight: 700; color: #475569; background: #f1f5f9; padding: 2px 6px; border-radius: 4px; }
.brz-badge-repeat { font-size: 0.75rem; font-weight: 700; color: #065f46; background: #ecfdf5; padding: 2px 6px; border-radius: 4px; border: 1px solid #a7f3d0; }

.brz-modal-ev-title { font-size: 1.15rem; font-weight: 800; margin: 4px 0 8px; }
.brz-modal-ev-title a { color: var(--color-text-dark); text-decoration: none; }
.brz-modal-ev-title a:hover { color: var(--color-primary); }

.brz-modal-ev-loc { font-size: 0.85rem; color: #64748b; margin-bottom: 8px; font-weight: 600; }
.brz-modal-ev-desc { font-size: 0.9rem; color: #475569; line-height: 1.5; margin-bottom: 12px; }

.brz-modal-ev-btn {
	display: inline-block;
	background: var(--color-primary);
	color: #ffffff;
	padding: 8px 14px;
	border-radius: var(--border-radius-md);
	font-size: 0.85rem;
	font-weight: 700;
	text-decoration: none;
	transition: background 0.2s ease;
}

.brz-modal-ev-btn:hover { background: var(--color-primary-dark); }

@media (max-width: 768px) {
	.brz-cal-weekdays { font-size: 0.7rem; }
	.brz-cal-cell { min-height: 80px; padding: 4px; }
	.brz-event-pill { font-size: 0.65rem; padding: 2px 4px; }
	.brz-cal-month-title { font-size: 1.2rem; min-width: auto; }
}
</style>

<main id="primary" class="site-main" style="padding: 40px 0; background-color: var(--color-bg);">
	<div class="container">
		
		<header class="page-header" style="margin-bottom: 30px; text-align: center;">
			<h1 class="page-title" style="font-size: 2.5rem; margin-bottom: 8px;">📅 Calendar Evenimente Locale</h1>
			<p style="color: var(--color-text-muted); max-width: 700px; margin: 0 auto 16px;">
				Fii la curent cu toate sărbătorile, târgurile, competițiile sportive și întâlnirile comunitare organizate în comuna Brezoaele.
			</p>
			<?php if ( current_user_can( 'edit_posts' ) ) : ?>
				<div style="display: flex; justify-content: center; gap: 12px; margin-bottom: 20px;">
					<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=eveniment' ) ); ?>" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 800; font-size: 0.85rem; padding: 10px 20px; border-radius: var(--border-radius-md);">
						➕ Adaugă Eveniment Nou (Admin)
					</a>
				</div>
			<?php endif; ?>
			<div style="width: 50px; height: 3px; background-color: var(--color-primary); margin: 0 auto; border-radius: 3px;"></div>
		</header>

		<!-- Filtre Categorii Evenimente -->
		<div class="brz-cat-filters" style="justify-content: center;">
			<button class="brz-cat-filter-btn active" data-cat="">✨ Toate Categoriile</button>
			<?php
			$terms = get_terms( array(
				'taxonomy'   => 'tip_eveniment',
				'hide_empty' => false,
			) );
			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) :
				foreach ( $terms as $term ) :
					?>
					<button class="brz-cat-filter-btn" data-cat="<?php echo esc_attr( $term->slug ); ?>">
						<?php echo esc_html( $term->name ); ?>
					</button>
					<?php
				endforeach;
			endif;
			?>
		</div>

		<!-- Containerul Calendarului -->
		<div class="brz-cal-container">
			<!-- Header Navigare Lună -->
			<div class="brz-cal-header-bar">
				<div class="brz-cal-nav">
					<button id="brz-cal-prev-month" class="brz-cal-nav-btn">◀ Luna Trecută</button>
					<button id="brz-cal-today" class="brz-cal-nav-btn">Azi</button>
					<button id="brz-cal-next-month" class="brz-cal-nav-btn">Luna Viitoare ▶</button>
				</div>
				<h2 id="brz-calendar-month-title" class="brz-cal-month-title">--</h2>
			</div>

			<!-- Zilele săptămânii (Luni - Duminică) -->
			<div class="brz-cal-weekdays">
				<div>Lun</div>
				<div>Mar</div>
				<div>Mie</div>
				<div>Joi</div>
				<div>Vin</div>
				<div>Sâm</div>
				<div>Dum</div>
			</div>

			<!-- Gridul Zilelor (populat dinamic de calendar.js) -->
			<div id="brz-calendar-grid" class="brz-cal-grid-wrapper">
				<!-- Injected by JavaScript -->
			</div>
		</div>

		<!-- Modal Detalii Zi -->
		<div id="brz-day-modal" class="brz-modal-backdrop">
			<div class="brz-modal-box">
				<button id="brz-modal-close" class="brz-modal-close-btn">&times;</button>
				<h3 id="brz-modal-date-title" style="margin-top: 0; margin-bottom: 20px; font-size: 1.3rem; border-bottom: 1px solid var(--color-border); padding-bottom: 12px; color: var(--color-primary);">--</h3>
				<div id="brz-modal-events-list">
					<!-- Injected by JS -->
				</div>
			</div>
		</div>

		<!-- Lista Următoarelor Evenimente Programate -->
		<section style="margin-top: 40px;">
			<div style="text-align: center; margin-bottom: 24px;">
				<h2 style="font-size: 1.8rem; margin-bottom: 6px;">📌 Următoarele Evenimente Programate</h2>
				<p style="color: var(--color-text-muted);">Listă completă a activităților viitoare în comuna Brezoaele</p>
			</div>

			<div class="grid grid-3">
				<?php
				$today_date = date( 'Y-m-d' );
				$args = array(
					'post_type'      => 'eveniment',
					'posts_per_page' => 6,
					'post_status'    => 'publish',
					'meta_key'       => '_eveniment_data_start',
					'orderby'        => 'meta_value',
					'order'          => 'ASC',
				);
				$query = new WP_Query( $args );

				if ( $query->have_posts() ) :
					while ( $query->have_posts() ) :
						$query->the_post();

						$data_start      = get_post_meta( get_the_ID(), '_eveniment_data_start', true );
						$ora_start       = get_post_meta( get_the_ID(), '_eveniment_ora_start', true );
						$toata_ziua      = get_post_meta( get_the_ID(), '_eveniment_toata_ziua', true );
						$repetitiv_anual = get_post_meta( get_the_ID(), '_eveniment_repetitiv_anual', true );
						$locatie         = get_post_meta( get_the_ID(), '_eveniment_locatie', true );

						$terms = get_the_terms( get_the_ID(), 'tip_eveniment' );
						$cat_label = ($terms && !is_wp_error($terms)) ? $terms[0]->name : 'General';

						$thumb = get_the_post_thumbnail_url( get_the_ID(), 'medium' );
						if ( ! $thumb ) {
							$thumb = get_template_directory_uri() . '/images/hero-biserica.png';
						}
						?>
						<div class="card" style="display: flex; flex-direction: column; height: 100%; border-radius: var(--border-radius-lg); overflow: hidden; box-shadow: var(--shadow-md); transition: transform 0.2s ease;">
							<div style="position: relative; height: 180px; overflow: hidden;">
								<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php the_title_attribute(); ?>" style="width: 100%; height: 100%; object-fit: cover;">
								<div style="position: absolute; top: 12px; left: 12px; background: var(--color-primary); color: #fff; padding: 4px 10px; border-radius: 4px; font-weight: 800; font-size: 0.75rem; text-transform: uppercase;">
									<?php echo esc_html( $cat_label ); ?>
								</div>
								<?php if ( '1' === $repetitiv_anual ) : ?>
									<div style="position: absolute; top: 12px; right: 12px; background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; padding: 4px 8px; border-radius: 4px; font-weight: 800; font-size: 0.75rem;">
										🔁 Anual
									</div>
								<?php endif; ?>
							</div>

							<div style="padding: 20px; display: flex; flex-direction: column; flex-grow: 1; background: #fff;">
								<div style="display: flex; align-items: center; gap: 8px; font-size: 0.85rem; font-weight: 700; color: var(--color-primary); margin-bottom: 8px;">
									<span>📅 <?php echo esc_html( date( 'd.m.Y', strtotime( $data_start ) ) ); ?></span>
									<span>•</span>
									<span><?php echo ('1' === $toata_ziua) ? '⏳ Toată ziua' : '⏰ ' . esc_html( $ora_start ); ?></span>
								</div>

								<h3 style="font-size: 1.2rem; font-weight: 800; margin-bottom: 8px; line-height: 1.3;">
									<a href="<?php the_permalink(); ?>" style="color: var(--color-text-dark); text-decoration: none;">
										<?php the_title(); ?>
									</a>
								</h3>

								<?php if ( $locatie ) : ?>
									<p style="font-size: 0.85rem; color: var(--color-text-muted); margin-bottom: 12px; font-weight: 600;">
										📍 <?php echo esc_html( $locatie ); ?>
									</p>
								<?php endif; ?>

								<p style="font-size: 0.9rem; color: #475569; margin-bottom: 16px; flex-grow: 1; line-height: 1.5;">
									<?php echo esc_html( wp_trim_words( get_the_excerpt(), 18, '...' ) ); ?>
								</p>

								<a href="<?php the_permalink(); ?>" class="btn btn-primary" style="text-align: center; font-weight: 800; font-size: 0.85rem; padding: 10px; border-radius: var(--border-radius-md);">
									Vezi Detalii & Program →
								</a>
							</div>
						</div>
					<?php
					endwhile;
					wp_reset_postdata();
				else :
					?>
					<div style="grid-column: 1 / -1; text-align: center; padding: 40px; background: #fff; border-radius: var(--border-radius-lg); border: 1px dashed var(--color-border);">
						<p style="font-size: 1.1rem; color: var(--color-text-muted);">Momentan nu sunt evenimente viitoare programate.</p>
					</div>
				<?php endif; ?>
			</div>
		</section>

	</div>
</main>

<?php
get_footer();
