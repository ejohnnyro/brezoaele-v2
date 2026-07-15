<?php
/**
 * The template for displaying the front page.
 *
 * @package Brezoaele_V2
 */

get_header();

// Preluăm imaginea Hero din Customizer (cu fallback la imaginea implicită din temă)
$hero_bg_url = get_theme_mod( 'brezoaele_v2_hero_bg' );
if ( empty( $hero_bg_url ) ) {
	$hero_bg_url = get_template_directory_uri() . '/images/hero-biserica.png';
}

// -----------------------------------------------------------------------------
// INTEROGARE ARTICOLE PENTRU LAYOUT-UL ASIMETRIC
// -----------------------------------------------------------------------------

// 1. Știrea principală mare (Featured: Amintiri Locale)
$featured_query = new WP_Query( array(
	'category_name'  => 'amintiri-locale',
	'posts_per_page' => 1,
	'post_status'    => 'publish',
) );
$featured_post = null;
if ( $featured_query->have_posts() ) {
	$featured_query->the_post();
	$featured_post = get_post();
	wp_reset_postdata();
} else {
	// Fallback la ultimul articol publicat de pe site
	$featured_query = new WP_Query( array(
		'posts_per_page' => 1,
		'post_status'    => 'publish',
	) );
	if ( $featured_query->have_posts() ) {
		$featured_query->the_post();
		$featured_post = get_post();
		wp_reset_postdata();
	}
}

// Reținem ID-urile excluse pentru a nu dubla articolele
$exclude_ids = array();
if ( $featured_post ) {
	$exclude_ids[] = $featured_post->ID;
}

// 2. Știrea de pe rândul 1 (Voluntariat & Inițiative)
$row1_query = new WP_Query( array(
	'category_name'  => 'voluntariat-initiative',
	'posts_per_page' => 1,
	'post_status'    => 'publish',
) );
$row1_post = null;
if ( $row1_query->have_posts() ) {
	$row1_query->the_post();
	$row1_post = get_post();
	$exclude_ids[] = $row1_post->ID;
	wp_reset_postdata();
} else {
	// Fallback la ultimul articol neafișat încă
	$row1_query = new WP_Query( array(
		'posts_per_page' => 1,
		'post__not_in'   => $exclude_ids,
		'post_status'    => 'publish',
	) );
	if ( $row1_query->have_posts() ) {
		$row1_query->the_post();
		$row1_post = get_post();
		$exclude_ids[] = $row1_post->ID;
		wp_reset_postdata();
	}
}

// 3. Știrea de pe rândul 2 (Fonduri Europene)
$row2_query = new WP_Query( array(
	'category_name'  => 'fonduri-europene',
	'posts_per_page' => 1,
	'post_status'    => 'publish',
) );
$row2_post = null;
if ( $row2_query->have_posts() ) {
	$row2_query->the_post();
	$row2_post = get_post();
	wp_reset_postdata();
} else {
	// Fallback la ultimul articol neafișat încă
	$row2_query = new WP_Query( array(
		'posts_per_page' => 1,
		'post__not_in'   => $exclude_ids,
		'post_status'    => 'publish',
	) );
	if ( $row2_query->have_posts() ) {
		$row2_query->the_post();
		$row2_post = get_post();
		wp_reset_postdata();
	}
}

// -----------------------------------------------------------------------------
// INTEROGARE ANUNȚURI LOCALNIC (CPT 'anunt')
// -----------------------------------------------------------------------------
$ad_query = new WP_Query( array(
	'post_type'      => 'anunt',
	'posts_per_page' => 1,
	'post_status'    => 'publish',
) );
$ad_post = null;
if ( $ad_query->have_posts() ) {
	$ad_query->the_post();
	$ad_post = get_post();
	wp_reset_postdata();
}
?>

<!-- 1. Hero Section (Design Mockup) -->
<section class="flat-hero-container" style="background-image: url('<?php echo esc_url( $hero_bg_url ); ?>');">
	
	<!-- Mască SVG pentru divizarea ecranului în mod concav -->
	<svg style="position: absolute; width: 0; height: 0;" width="0" height="0">
		<defs>
			<clipPath id="hero-clip" clipPathUnits="objectBoundingBox">
				<path d="M 0,0 L 0.53,0 Q 0.42,0.5 0.50,1 L 0,1 Z" />
			</clipPath>
		</defs>
	</svg>

	<!-- Fundalul alb curbat de fundal care ocupă toată înălțimea și stânga ecranului -->
	<div class="hero-curved-overlay-wrapper">
		<div class="hero-curved-overlay"></div>
	</div>

	<div class="container hero-container-content">
		<!-- Conținutul text aliniat la grila paginii -->
		<div class="hero-curved-content">
			<span class="hero-church-badge">
				Portal Civic & Economic
			</span>
			<h1 class="hero-church-title">
				Comuna <span>Brezoaele</span>
			</h1>
			<p class="hero-church-subtitle">
				Platformă independentă pentru digitalizare rapidă, promovarea legumicultorilor locali, investiții și dezbateri cetățenești.
			</p>
			<div class="flat-hero-buttons">
				<a href="<?php echo esc_url( home_url( '/harta-servicii/' ) ); ?>" class="btn btn-primary">
					🗺️ Hartă & Servicii
				</a>
				<a href="<?php echo esc_url( home_url( '/sesizeaza-o-problema/' ) ); ?>" class="btn btn-danger">
					🚨 Sesizează o Problemă
				</a>
			</div>
		</div>

		<!-- Floating Weather Widget on the bottom right of hero visual area -->
		<div class="floating-weather-card">
			<div id="weather-today" style="display: flex; align-items: center; gap: 12px; min-height: 48px;">
				<span style="font-size: 2.2rem; line-height: 1;">☀️</span>
				<div>
					<span style="font-size: 1.1rem; font-weight: 800; display: block; color: var(--color-primary-dark); line-height: 1.2;">21°C</span>
					<span style="font-size: 0.8rem; color: var(--color-text-muted); font-weight: 600;">Majoritar senin</span>
				</div>
			</div>
			<div style="border-top: 1px dashed var(--color-border); padding-top: 8px; display: flex; flex-direction: column; gap: 4px;">
				<a href="<?php echo esc_url( home_url( '/vremea-in-brezoaele/' ) ); ?>" style="font-size: 0.8rem; font-weight: 700; color: var(--color-primary); text-decoration: none; display: flex; align-items: center; gap: 4px;">
					Prognoză pe 7 zile &rarr;
				</a>
				<a href="<?php echo esc_url( home_url( '/program-microbuz-brezoaele-bucuresti/' ) ); ?>" style="font-size: 0.8rem; font-weight: 700; color: var(--color-secondary); text-decoration: none; display: flex; align-items: center; gap: 4px;">
					🚌 Mersul Microbuzelor &rarr;
				</a>
			</div>
		</div>

	</div>
</section>

<!-- 2. Four Feature Cards Section -->
<section style="padding: 40px 0; background-color: #ffffff;">
	<div class="container">
		<div class="feature-grid">
			
			<!-- Card 1: Ghid Rezident -->
			<div class="feature-card">
				<div>
					<div class="feature-card-header">
						<span class="feature-card-icon" style="color: var(--color-primary);">📖</span>
						<h3 class="feature-card-title">Ghid Rezident</h3>
					</div>
					<p class="feature-card-text">
						Utilități, taxe locale, colectare deșeuri și contacte administrative utile din comuna Brezoaele.
					</p>
				</div>
				<a href="<?php echo esc_url( home_url( '/ghid-rezident/' ) ); ?>" class="feature-card-link">
					DESCHIDE GHIDUL &rarr;
				</a>
			</div>

			<!-- Card 2: Anunțuri Locale -->
			<div class="feature-card">
				<div>
					<div class="feature-card-header">
						<span class="feature-card-icon" style="color: var(--color-secondary);">📢</span>
						<h3 class="feature-card-title">Anunțuri Locale</h3>
					</div>
					<p class="feature-card-text">
						Cumpără direct de la producători sau postează gratuit un anunț local de vânzare, cumpărare sau angajare.
					</p>
				</div>
				<a href="<?php echo esc_url( home_url( '/anunturi/' ) ); ?>" class="feature-card-link">
					VEZI ANUNȚURILE &rarr;
				</a>
			</div>

			<!-- Card 3: Ghidul Afacerilor & Producătorilor -->
			<div class="feature-card">
				<div>
					<div class="feature-card-header">
						<span class="feature-card-icon" style="color: var(--color-primary);">🏢</span>
						<h3 class="feature-card-title">Afaceri & Producători</h3>
					</div>
					<p class="feature-card-text">
						Descoperă catalogul magazinelor, firmelor și legumicultorilor din comună pe Harta Satelit.
					</p>
				</div>
				<a href="<?php echo esc_url( home_url( '/afaceri-locale/' ) ); ?>" class="feature-card-link">
					VEZI DIRECTORUL &rarr;
				</a>
			</div>

			<!-- Card 4: Forum Comunitar -->
			<div class="feature-card">
				<div>
					<div class="feature-card-header">
						<span class="feature-card-icon" style="color: var(--color-secondary);">💬</span>
						<h3 class="feature-card-title">Forum Comunitar</h3>
					</div>
					<p class="feature-card-text">
						Pune o întrebare, cere recomandări de meseriași sau propune subiecte de discuție pentru localnici.
					</p>
				</div>
				<a href="<?php echo esc_url( home_url( '/comunitate/' ) ); ?>" class="feature-card-link">
					INTRĂ PE FORUM &rarr;
				</a>
			</div>

		</div>
	</div>
</section>

<!-- 3. Știri, Comunicate & Noutăți Localitate (Asymmetric Section) -->
<section style="padding: 40px 0; background-color: #ffffff;">
	<div class="container">
		
		<div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
			<div>
				<h2 style="font-size: 1.8rem; font-weight: 900; font-family: var(--font-heading); margin: 0; text-transform: none;">Noutăți Locale & Comunicate</h2>
				<p style="color: var(--color-text-muted); font-size: 0.9rem; margin-top: 4px; font-weight: 500;">Fonduri europene, dezvoltare locală, utilități și decizii administrative.</p>
			</div>
			<a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>" class="btn btn-secondary-outline" style="font-size: 0.8rem; padding: 8px 16px;">
				Vezi Toate Știrile
			</a>
		</div>

		<div class="news-container-grid">
			
			<!-- Left Side: Large Cover Article -->
			<?php if ( $featured_post ) : ?>
				<article class="news-featured-large">
					<?php if ( has_post_thumbnail( $featured_post->ID ) ) : ?>
						<?php echo get_the_post_thumbnail( $featured_post->ID, 'large', array( 'class' => 'news-featured-img' ) ); ?>
					<?php else : ?>
						<div class="news-featured-img" style="background-color: var(--color-text-dark); display: flex; align-items: center; justify-content: center; font-size: 4rem;">🏛️</div>
					<?php endif; ?>
					<div class="news-featured-overlay"></div>
					
					<div class="news-featured-content">
						<div class="news-featured-meta">
							<span style="font-size: 0.7rem; font-weight: 800; text-transform: uppercase; color: var(--color-primary-dark); background-color: var(--color-primary-light); padding: 2px 6px; border: 1px solid var(--color-border); border-radius: 4px;">
								<?php
								$cats = get_the_category( $featured_post->ID );
								echo ! empty( $cats ) ? esc_html( $cats[0]->name ) : 'Știri';
								?>
							</span>
							<span style="font-size: 0.75rem; font-weight: 600; color: rgba(255, 255, 255, 0.85);"><?php echo get_the_date( 'd.m.Y', $featured_post->ID ); ?></span>
						</div>
						
						<div>
							<h3 class="news-featured-title">
								<a href="<?php echo esc_url( get_permalink( $featured_post->ID ) ); ?>" style="color: #ffffff; text-decoration: none;"><?php echo esc_html( $featured_post->post_title ); ?></a>
							</h3>
							<p class="news-featured-excerpt">
								<?php echo esc_html( wp_trim_words( $featured_post->post_content, 22, '...' ) ); ?>
							</p>
							<a href="<?php echo esc_url( get_permalink( $featured_post->ID ) ); ?>" class="news-featured-link">
								Citește Articolul &rarr;
							</a>
						</div>
					</div>
				</article>
			<?php endif; ?>

			<!-- Right Side: Two Rows of Smaller Articles -->
			<div class="news-rows-column">
				
				<!-- Row 1: Voluntariat & Inițiative -->
				<?php if ( $row1_post ) : ?>
					<article class="news-row-card">
						<div class="news-row-img-wrapper">
							<?php if ( has_post_thumbnail( $row1_post->ID ) ) : ?>
								<?php echo get_the_post_thumbnail( $row1_post->ID, 'medium', array( 'class' => 'news-row-img' ) ); ?>
							<?php else : ?>
								<div style="background-color: #f1f5f9; display:flex; align-items:center; justify-content:center; width:100%; height:100%; font-size:2rem;">🌱</div>
							<?php endif; ?>
						</div>
						<div class="news-row-body">
							<div>
								<div class="news-row-meta">
									<span style="color: var(--color-primary-dark); font-weight: 800; font-size: 0.7rem; text-transform: uppercase;">
										<?php
										$cats = get_the_category( $row1_post->ID );
										echo ! empty( $cats ) ? esc_html( $cats[0]->name ) : 'Inițiative';
										?>
									</span>
									<span><?php echo get_the_date( 'd.m.Y', $row1_post->ID ); ?></span>
								</div>
								<h3 class="news-row-title">
									<a href="<?php echo esc_url( get_permalink( $row1_post->ID ) ); ?>" style="color: var(--color-text-dark); text-decoration:none;"><?php echo esc_html( $row1_post->post_title ); ?></a>
								</h3>
								<p class="news-row-excerpt">
									<?php echo esc_html( wp_trim_words( $row1_post->post_content, 18, '...' ) ); ?>
								</p>
							</div>
							<a href="<?php echo esc_url( get_permalink( $row1_post->ID ) ); ?>" class="news-row-link">
								Citește Articolul &rarr;
							</a>
						</div>
					</article>
				<?php endif; ?>

				<!-- Row 2: Fonduri Europene / PNRR -->
				<?php if ( $row2_post ) : ?>
					<article class="news-row-card">
						<div class="news-row-img-wrapper">
							<?php if ( has_post_thumbnail( $row2_post->ID ) ) : ?>
								<?php echo get_the_post_thumbnail( $row2_post->ID, 'medium', array( 'class' => 'news-row-img' ) ); ?>
							<?php else : ?>
								<div style="background-color: #f1f5f9; display:flex; align-items:center; justify-content:center; width:100%; height:100%; font-size:2rem;">🇪🇺</div>
							<?php endif; ?>
						</div>
						<div class="news-row-body">
							<div>
								<div class="news-row-meta">
									<span style="color: var(--color-secondary-dark); font-weight: 800; font-size: 0.7rem; text-transform: uppercase;">
										<?php
										$cats = get_the_category( $row2_post->ID );
										echo ! empty( $cats ) ? esc_html( $cats[0]->name ) : 'Fonduri Europene';
										?>
									</span>
									<span><?php echo get_the_date( 'd.m.Y', $row2_post->ID ); ?></span>
								</div>
								<h3 class="news-row-title">
									<a href="<?php echo esc_url( get_permalink( $row2_post->ID ) ); ?>" style="color: var(--color-text-dark); text-decoration:none;"><?php echo esc_html( $row2_post->post_title ); ?></a>
								</h3>
								<p class="news-row-excerpt">
									<?php echo esc_html( wp_trim_words( $row2_post->post_content, 18, '...' ) ); ?>
								</p>
							</div>
							<a href="<?php echo esc_url( get_permalink( $row2_post->ID ) ); ?>" class="news-row-link">
								Citește Articolul &rarr;
							</a>
						</div>
					</article>
				<?php endif; ?>

			</div>

		</div>

	</div>
</section>

<!-- 4. Neighbors' Ads & Producatori Promotion Section -->
<section style="padding: 40px 0; background-color: var(--color-bg);">
	<div class="container">
		
		<div class="ads-section-grid">
			
			<!-- Left Column: Classified Ads -->
			<div>
				<div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px; padding-bottom: 8px;">
					<h2 style="font-size: 1.4rem; font-weight: 900; margin: 0; font-family: var(--font-heading);">Anunțurile Vecinilor</h2>
					<a href="<?php echo esc_url( home_url( '/anunturi/' ) ); ?>" style="font-size: 0.8rem; font-weight: 700; color: var(--color-primary); text-decoration: none;" class="feature-card-link">
						Toate Anunțurile &rarr;
					</a>
				</div>
				
				<?php if ( $ad_post ) : ?>
					<div class="ad-compact-card">
						<div class="ad-compact-img-wrapper">
							<?php if ( has_post_thumbnail( $ad_post->ID ) ) : ?>
								<?php echo get_the_post_thumbnail( $ad_post->ID, 'medium', array( 'class' => 'ad-compact-img' ) ); ?>
							<?php else : ?>
								<div style="background-color: #f1f5f9; display: flex; align-items: center; justify-content: center; width:100%; height:100%; font-size: 3rem;">🥚</div>
							<?php endif; ?>
						</div>
						
						<div class="ad-compact-body">
							<div>
								<div class="ad-compact-header">
									<span class="ad-compact-badge">Anunț</span>
									<?php
									$pret = get_post_meta( $ad_post->ID, '_anunt_pret', true );
									if ( ! empty( $pret ) ) :
									?>
										<span class="ad-compact-price"><?php echo esc_html( $pret ); ?> RON</span>
									<?php endif; ?>
								</div>
								
								<h3 class="ad-compact-title">
									<a href="<?php echo esc_url( get_permalink( $ad_post->ID ) ); ?>" style="color: var(--color-text-dark); text-decoration: none;"><?php echo esc_html( $ad_post->post_title ); ?></a>
								</h3>
								
								<p class="ad-compact-excerpt">
									<?php echo esc_html( wp_trim_words( $ad_post->post_content, 15, '...' ) ); ?>
								</p>
							</div>
							
							<a href="<?php echo esc_url( get_permalink( $ad_post->ID ) ); ?>" class="btn btn-secondary-outline" style="font-size: 0.75rem; padding: 6px 12px; text-align: center;">
								Vezi Anunț
							</a>
						</div>
					</div>
				<?php else : ?>
					<div style="background: #ffffff; border: 1px solid var(--color-border); border-radius: var(--border-radius-lg); padding: 20px; text-align: center;">
						<p style="color: var(--color-text-muted); font-size: 0.9rem; margin: 0;">Momentan nu sunt anunțuri active.</p>
					</div>
				<?php endif; ?>
			</div>

			<!-- Right Column: Marketing Producatori -->
			<div class="producer-cta-card">
				<img src="<?php echo esc_url( get_template_directory_uri() . '/images/producatori-sketch.png' ); ?>" class="producer-cta-bg-img" alt="Schiță Legume">
				
				<div class="producer-cta-content">
					<div>
						<h3 style="font-size: 1.3rem; font-weight: 900; margin-bottom: 10px; font-family: var(--font-heading); color: var(--color-text-dark);">
							Promovare Producători
						</h3>
						<p style="color: var(--color-text-muted); font-size: 0.85rem; line-height: 1.45; margin: 0;">
							Ești legumicultor local în Brezoaele? Pune-ți gospodăria pe <b>Harta Satelit</b> a serviciilor și fă-te vizibil cumpărătorilor din București care vin direct la poartă!
						</p>
					</div>
					
					<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn-primary" style="align-self: flex-start; margin-top: 12px;">
						👤 Înscrie-te Gratuit
					</a>
				</div>
			</div>

		</div>

	</div>
</section>

<!-- 5. Forum Horizontal Teaser Banner -->
<section style="padding: 40px 0; background-color: #ffffff;">
	<div class="container">
		
		<div class="forum-horizontal-banner">
			<!-- Illustration on Left -->
			<img src="<?php echo esc_url( get_template_directory_uri() . '/images/forum-illustration.png' ); ?>" class="forum-banner-img" alt="Ilustrație Forum">
			
			<!-- Text in Middle -->
			<div class="forum-banner-text">
				<h2 class="forum-banner-title">Pune o Întrebare Vecinilor pe Forum</h2>
				<p class="forum-banner-desc">
					Cauți o recomandare, vinzi ceva în comună sau vrei să afli noutăți despre rețelele de utilități? Deschide o discuție simplă pe forum.
				</p>
			</div>
			
			<!-- CTA Button on Right -->
			<a href="<?php echo esc_url( home_url( '/comunitate/' ) ); ?>" class="btn btn-primary">
				💬 Deschide Forumul Local
			</a>
		</div>

	</div>
</section>

<?php
get_footer();
