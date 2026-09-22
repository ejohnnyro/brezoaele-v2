<?php
/**
 * The template for displaying single posts with 2-column Desktop layout and Ad Placeholders.
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
			?>
			
			<div class="brz-single-grid">

				<!-- COLOANA PRINCIPALĂ ARTICOL (STÂNGA PE DESKTOP - 2fr / ~840px) -->
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'brz-single-main-col' ); ?>>
					
					<!-- Header Articol -->
					<header class="entry-header" style="margin-bottom: 24px;">
						<div style="margin-bottom: 8px;">
							<span style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: var(--color-primary-dark); background-color: var(--color-primary-light); padding: 4px 10px; border-radius: 4px; border: 1px solid var(--color-primary-light); letter-spacing: 0.5px;">
								<?php the_category( ', ' ); ?>
							</span>
						</div>
						<?php the_title( '<h1 class="entry-title" style="font-size: 2.2rem; margin-bottom: 12px; font-weight: 900; font-family: var(--font-heading); line-height: 1.25; color: var(--color-text-dark);">', '</h1>' ); ?>
						<div style="font-size: 0.85rem; color: var(--color-text-muted); font-weight: 600;">
							📅 Publicat la <?php echo get_the_date(); ?> • ✍️ De <?php the_author(); ?>
						</div>
					</header>

					<!-- Imagine Reprezentativă -->
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="post-thumbnail" style="margin-bottom: 24px; border: 1px solid var(--color-border); border-radius: var(--border-radius-lg); overflow: hidden; box-shadow: var(--shadow-md);">
							<?php the_post_thumbnail( 'large', array( 'style' => 'width: 100%; height: auto; display: block;' ) ); ?>
						</div>
					<?php endif; ?>

					<!-- Conținut Articol (reclama In-Article se injectează automat după paragraful 3 dacă articolul are minim 4 paragrafe) -->
					<div class="card" style="padding: 30px; font-size: 1.05rem; line-height: 1.7; background: #ffffff; border-radius: var(--border-radius-lg); border: 1px solid var(--color-border); box-shadow: var(--shadow-md);">
						<div class="entry-content">
							<?php the_content(); ?>
						</div>
					</div>

					<!-- Navigare Articole -->
					<nav class="navigation post-navigation" style="margin-top: 24px; padding: 16px 20px; background: #ffffff; border-radius: var(--border-radius-md); border: 1px solid var(--color-border); display: flex; justify-content: space-between; font-weight: 700; font-size: 0.85rem; text-transform: uppercase;">
						<div class="nav-previous">
							<?php previous_post_link( '%link', '&larr; Articol Precedent' ); ?>
						</div>
						<div class="nav-next">
							<?php next_post_link( '%link', 'Articol Următor &rarr;' ); ?>
						</div>
					</nav>

					<!-- SPAȚIU PUBLICITAR SUB ARTICOL (Large Rectangle 336x280 / 728x90) -->
					<?php
					if ( function_exists( 'brezoaele_render_ad_placeholder' ) ) {
						brezoaele_render_ad_placeholder( 'after-article', '336 x 280 px / 728 x 90 px', 'Spațiu Publicitar Final Articol' );
					}
					?>

					<!-- Formular Newsletter sub articol -->
					<?php
					if ( class_exists( 'Brezoaele_NL_Forms' ) ) {
						echo Brezoaele_NL_Forms::render_form( 'single_post', 'Abonează-te la Newsletter-ul Brezoaele', 'Primește direct pe email noutățile locale, comunicatele Primăriei și alertele de utilități.' );
					}

					// Secțiune Comentarii
					if ( comments_open() || get_comments_number() ) :
						comments_template();
					endif;
					?>

				</article>

				<!-- COLOANA SIDEBAR DREAPTA (1fr / ~350px) -->
				<aside class="brz-single-sidebar">
					
					<!-- 1. Banner Publicitar Sidebar Sus (Medium Rectangle 300x250) -->
					<?php
					if ( function_exists( 'brezoaele_render_ad_placeholder' ) ) {
						brezoaele_render_ad_placeholder( 'sidebar-medium', '300 x 250 px', 'Banner Publicitar Sidebar Sus' );
					}
					?>

					<!-- 2. Widget Utilități / Servicii Locale -->
					<div class="brz-single-sidebar-card">
						<h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 14px; padding-bottom: 8px; border-bottom: 2px solid var(--color-border); color: var(--color-primary);">
							🔗 Navigare Rapidă Brezoaele
						</h3>
						<ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 10px; font-size: 0.9rem; font-weight: 700;">
							<li>
								<a href="<?php echo esc_url( home_url( '/calendar-evenimente/' ) ); ?>" style="color: var(--color-text-dark); text-decoration: none; display: flex; align-items: center; gap: 8px;">
									📅 Calendar Evenimente Locale
								</a>
							</li>
							<li>
								<a href="<?php echo esc_url( home_url( '/harta-servicii/' ) ); ?>" style="color: var(--color-text-dark); text-decoration: none; display: flex; align-items: center; gap: 8px;">
									🗺️ Harta Satelit a Serviciilor
								</a>
							</li>
							<li>
								<a href="<?php echo esc_url( home_url( '/comunitate/' ) ); ?>" style="color: var(--color-text-dark); text-decoration: none; display: flex; align-items: center; gap: 8px;">
									💬 Forumul Comunității
								</a>
							</li>
							<li>
								<a href="<?php echo esc_url( home_url( '/investitii/' ) ); ?>" style="color: var(--color-text-dark); text-decoration: none; display: flex; align-items: center; gap: 8px;">
									🏗️ Proiecte de Investiții
								</a>
							</li>
						</ul>
					</div>

					<!-- 3. Banner Publicitar Sidebar Sticky (Half-Page 300x600 / Filmstrip) -->
					<div class="brz-single-sidebar-card sticky-ad" style="padding: 0; background: transparent; border: none; box-shadow: none;">
						<?php
						if ( function_exists( 'brezoaele_render_ad_placeholder' ) ) {
							brezoaele_render_ad_placeholder( 'sidebar-sticky', '300 x 600 px (Half Page Sticky)', 'Banner Sticky Sidebar Fix la Scroll' );
						}
						?>
					</div>

				</aside>

			</div>

			<?php
		endwhile;
		?>
	</div>
</main>

<?php
get_footer();
