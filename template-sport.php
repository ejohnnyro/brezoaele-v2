<?php
/**
 * Template Name: Hub Central Sport Brezoaele (/sport/)
 * Description: Șablon modern tip revistă/portal pe 2 coloane pentru pagina /sport/ - Flux articole din categoria 'sport' (https://brezoaele.ro/c/sport/), sidebar informativ despre Baza Sportivă Ștefan Ciomartan & AFC 1948 Brezoaele, și timeline vizual cu istoria fotbalistică din 1945.
 *
 * @package Brezoaele_V2
 */

get_header();
?>

<style>
/* Stiluri Portal/Magazine Sport Brezoaele */
.brz-sport-hero {
	position: relative;
	background-size: cover;
	background-position: center;
	background-repeat: no-repeat;
	color: #ffffff;
	padding: 48px 20px 54px;
}
.brz-sport-hero-container {
	max-width: 1200px;
	margin: 0 auto;
}
.brz-sport-title {
	font-size: 2.8rem;
	font-weight: 900;
	margin: 10px 0 8px 0;
	background: linear-gradient(to right, #ffffff, #a7f3d0);
	-webkit-background-clip: text;
	-webkit-text-fill-color: transparent;
}
.brz-sport-subtitle {
	font-size: 1.15rem;
	color: #cbd5e1;
	margin: 0 0 24px 0;
	max-width: 800px;
	line-height: 1.6;
}
.brz-pills-row {
	display: flex;
	gap: 12px;
	flex-wrap: wrap;
}
.brz-pill-item {
	background: rgba(255, 255, 255, 0.08);
	border: 1px solid rgba(255, 255, 255, 0.15);
	padding: 8px 16px;
	border-radius: 20px;
	font-size: 0.85rem;
	font-weight: 700;
	color: #a7f3d0;
}

.brz-sport-main {
	max-width: 1200px;
	margin: 0 auto;
	padding: 40px 20px 60px;
}

/* Layout 2 Coloane (Magazine Style) */
.brz-magazine-layout {
	display: grid;
	grid-template-columns: 1fr 340px;
	gap: 36px;
	margin-bottom: 55px;
}
@media (max-width: 960px) {
	.brz-magazine-layout {
		grid-template-columns: 1fr;
	}
}

/* Grid Articole Stânga */
.brz-articles-grid-2col {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
	gap: 20px;
}
.brz-article-card {
	background: #ffffff;
	border: 1px solid #e2e8f0;
	border-radius: 16px;
	overflow: hidden;
	box-shadow: 0 4px 15px rgba(0,0,0,0.03);
	display: flex;
	flex-direction: column;
	transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.brz-article-card:hover {
	transform: translateY(-4px);
	box-shadow: 0 12px 25px rgba(0,0,0,0.08);
}
.brz-article-img {
	height: 170px;
	background: linear-gradient(135deg, #0f172a 0%, #047857 100%);
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 3.5rem;
	color: #fff;
}
.brz-article-img img {
	width: 100%;
	height: 100%;
	object-fit: cover;
}
.brz-article-body {
	padding: 20px;
	flex: 1;
	display: flex;
	flex-direction: column;
	justify-content: space-between;
}
.brz-article-title {
	font-size: 1.1rem;
	font-weight: 800;
	color: #0f172a;
	margin: 6px 0 8px 0;
	line-height: 1.4;
}
.brz-article-excerpt {
	font-size: 0.88rem;
	color: #475569;
	line-height: 1.5;
	margin-bottom: 14px;
}

/* Sidebar Dreapta */
.brz-sidebar-card {
	background: #ffffff;
	border: 1px solid #e2e8f0;
	border-radius: 16px;
	padding: 24px;
	box-shadow: 0 4px 15px rgba(0,0,0,0.03);
	margin-bottom: 24px;
}
.brz-sidebar-spec-list {
	list-style: none;
	padding: 0;
	margin: 14px 0 0 0;
	font-size: 0.88rem;
	color: #475569;
}
.brz-sidebar-spec-list li {
	padding: 8px 0;
	border-bottom: 1px dashed #f1f5f9;
	display: flex;
	justify-content: space-between;
	align-items: center;
}
.brz-sidebar-spec-list li:last-child {
	border-bottom: none;
}

/* Timeline Orizontal Istoric */
.brz-timeline-section {
	background: #ffffff;
	border: 1px solid #e2e8f0;
	border-radius: 20px;
	padding: 32px;
	box-shadow: 0 10px 30px rgba(0,0,0,0.03);
	margin-bottom: 50px;
}
.brz-timeline-grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
	gap: 20px;
	margin-top: 24px;
	position: relative;
}
.brz-timeline-card {
	background: #f8fafc;
	border-top: 4px solid #047857;
	border-radius: 12px;
	padding: 20px;
	position: relative;
}
.brz-timeline-year {
	font-size: 0.8rem;
	font-weight: 900;
	color: #047857;
	text-transform: uppercase;
	margin-bottom: 6px;
	display: inline-block;
	background: #dcfce7;
	padding: 2px 8px;
	border-radius: 10px;
}

.brz-no-posts-box {
	background: #f8fafc;
	border: 1px dashed #cbd5e1;
	border-radius: 16px;
	padding: 24px;
	text-align: center;
	color: #64748b;
	margin-bottom: 24px;
}
</style>

<?php
$default_hero_bg = get_template_directory_uri() . '/images/stadion.jpg';

$hero_bg_url = get_post_meta( get_the_ID(), '_brz_sport_hero_image', true );
$pos_x       = get_post_meta( get_the_ID(), '_brz_sport_hero_pos_x', true );
$pos_y       = get_post_meta( get_the_ID(), '_brz_sport_hero_pos_y', true );

if ( empty( $hero_bg_url ) ) {
	if ( has_post_thumbnail() ) {
		$hero_bg_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
	}
	if ( empty( $hero_bg_url ) ) {
		$hero_bg_url = $default_hero_bg;
	}
}

if ( '' === $pos_x || false === $pos_x ) {
	$pos_x = '50';
}
if ( '' === $pos_y || false === $pos_y ) {
	$pos_y = '50';
}

$bg_position_style = esc_attr( $pos_x . '% ' . $pos_y . '%' );
?>

<!-- HERO HEADER COMPACT -->
<section class="brz-sport-hero" style="background-image: linear-gradient(135deg, rgba(15, 23, 42, 0.85) 0%, rgba(30, 41, 59, 0.80) 60%, rgba(4, 120, 87, 0.75) 100%), url('<?php echo esc_url( $hero_bg_url ); ?>'); background-position: <?php echo $bg_position_style; ?>;">
	<div class="brz-sport-hero-container">
		<span style="background: rgba(16,185,129,0.2); color: #34d399; border: 1px solid rgba(52,211,153,0.4); padding: 4px 14px; border-radius: 20px; font-weight: 800; font-size: 0.8rem; text-transform: uppercase;">
			🏅 Hub Sportiv Comunitar
		</span>
		<h1 class="brz-sport-title">Sport Brezoaele</h1>
		<p class="brz-sport-subtitle">
			Pasiune, Tradiție și Mișcare în Comuna Brezoaele. Descoperă ultimele articole sportive, informațiile despre Baza Sportivă „Ștefan Ciomartan” și istoria fotbalului local fondată în 1945.
		</p>

		<div class="brz-pills-row">
			<div class="brz-pill-item">🏟️ Baza Sportivă „Ștefan Ciomartan” (50 locuri)</div>
			<div class="brz-pill-item">⚽ AFC 1948 Brezoaele (Liga 5 SUD)</div>
			<div class="brz-pill-item">📜 Tradiție din 8 Septembrie 1945</div>
		</div>
	</div>
</section>

<main class="brz-sport-main">

	<!-- MAIN MAGAZINE LAYOUT (2 COLOANE) -->
	<div class="brz-magazine-layout">

		<!-- COLOANA STÂNGĂ: FLUX ARTICOLE DIN CATEGORIA SPORT (/c/sport/) -->
		<div>
			<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px;">
				<h2 style="font-size: 1.5rem; font-weight: 900; color: #0f172a; margin: 0;">
					📰 Noutăți &amp; Cronici Sportive
				</h2>
				<a href="<?php echo esc_url( home_url( '/c/sport/' ) ); ?>" style="color: #047857; font-weight: 800; text-decoration: none; font-size: 0.88rem;">
					Vezi toate în /c/sport/ &rarr;
				</a>
			</div>

			<?php
			$sport_query = new WP_Query( array(
				'category_name'  => 'sport',
				'posts_per_page' => 6,
				'post_status'    => 'publish',
			) );

			if ( $sport_query->have_posts() ) :
			?>
				<div class="brz-articles-grid-2col">
					<?php
					while ( $sport_query->have_posts() ) :
						$sport_query->the_post();
					?>
						<article class="brz-article-card">
							<div class="brz-article-img">
								<?php if ( has_post_thumbnail() ) : ?>
									<?php the_post_thumbnail( 'medium_large' ); ?>
								<?php else : ?>
									⚽
								<?php endif; ?>
							</div>
							<div class="brz-article-body">
								<div>
									<span style="font-size: 0.75rem; color: #047857; font-weight: 800;">
										<?php echo esc_html( get_the_date( 'd F Y' ) ); ?>
									</span>
									<h3 class="brz-article-title">
										<a href="<?php the_permalink(); ?>" style="color: inherit; text-decoration: none;">
											<?php the_title(); ?>
										</a>
									</h3>
									<div class="brz-article-excerpt">
										<?php echo esc_html( wp_trim_words( get_the_excerpt(), 16, '...' ) ); ?>
									</div>
								</div>
								<a href="<?php the_permalink(); ?>" style="color: #047857; font-weight: 800; text-decoration: none; font-size: 0.85rem;">
									Citește articolul &rarr;
								</a>
							</div>
						</article>
					<?php endwhile; wp_reset_postdata(); ?>
				</div>
			<?php else : ?>

				<div class="brz-no-posts-box">
					<div style="font-size: 2.2rem; margin-bottom: 6px;">📢</div>
					<h3 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin: 0 0 4px 0;">
						Categoria Sport (/c/sport/) este gata de articole
					</h3>
					<p style="font-size: 0.85rem; margin: 0; line-height: 1.5;">
						Articolele noi vor apărea automat în această grilă. Iată câteva exemple de subiecte sportive:
					</p>
				</div>

				<div class="brz-articles-grid-2col">
					
					<article class="brz-article-card">
						<div class="brz-article-img">⚽</div>
						<div class="brz-article-body">
							<div>
								<span style="font-size: 0.75rem; color: #047857; font-weight: 800;">LIGA 5 SUD</span>
								<h3 class="brz-article-title">
									Meciuri pe Baza Sportivă „Ștefan Ciomartan”
								</h3>
								<div class="brz-article-excerpt">
									Meciurile oficiale și amicalele echipei locale AFC 1948 Brezoaele...
								</div>
							</div>
							<a href="<?php echo esc_url( home_url( '/c/sport/' ) ); ?>" style="color: #047857; font-weight: 800; text-decoration: none; font-size: 0.85rem;">
								Vezi /c/sport/ &rarr;
							</a>
						</div>
					</article>

					<article class="brz-article-card">
						<div class="brz-article-img" style="background: linear-gradient(135deg, #0284c7 0%, #0f172a 100%);">🌱</div>
						<div class="brz-article-body">
							<div>
								<span style="font-size: 0.75rem; color: #0284c7; font-weight: 800;">JUNIORI U19</span>
								<h3 class="brz-article-title">
									Competițiile de tineret din campionatul județean
								</h3>
								<div class="brz-article-excerpt">
									Noutăți și rezultate din campionatele de juniori ale AJF Dâmbovița...
								</div>
							</div>
							<a href="<?php echo esc_url( home_url( '/c/sport/' ) ); ?>" style="color: #0284c7; font-weight: 800; text-decoration: none; font-size: 0.85rem;">
								Vezi /c/sport/ &rarr;
							</a>
						</div>
					</article>

				</div>

			<?php endif; ?>
		</div>

		<!-- COLOANA DREAPTĂ: SIDEBAR INFORMATIV COMPACT -->
		<div>

			<!-- SIDEBAR CARD 1: COMPLEXUL SPORTIV CENTRAL & INFRASTRUCTURĂ -->
			<div class="brz-sidebar-card">
				<span style="background: #dbeafe; color: #1e40af; font-size: 0.7rem; font-weight: 800; padding: 3px 8px; border-radius: 10px; text-transform: uppercase;">
					Infrastructură Sportivă
				</span>
				<h3 style="font-size: 1.25rem; font-weight: 900; color: #0f172a; margin: 8px 0 4px 0;">
					Complexul Sportiv Central
				</h3>
				<p style="font-size: 0.82rem; color: #64748b; margin: 0 0 10px 0; line-height: 1.4;">
					(Stadionul „Ștefan Ciomârtan” &amp; Sala de Sport – Zona „La Bâlci”)
				</p>

				<ul class="brz-sidebar-spec-list">
					<li>
						<span>📍 Amplasare:</span>
						<strong style="color: #0f172a;">Zona Centrală („La Bâlci”)</strong>
					</li>
					<li>
						<span>🏟️ Teren Mare:</span>
						<strong style="color: #0f172a;">Gazon Natural &amp; 50 Locuri</strong>
					</li>
					<li>
						<span>🏢 Sală Acoperită:</span>
						<strong style="color: #0f172a;">Minifotbal, Handbal, Baschet</strong>
					</li>
					<li>
						<span>🌳 Spațiu Verde:</span>
						<strong style="color: #0f172a;">Parc Comunitar &amp; Alei</strong>
					</li>
				</ul>

				<a href="<?php echo esc_url( home_url( '/sport/baza-sportiva/' ) ); ?>" style="display: block; text-align: center; background: #047857; color: #ffffff; font-weight: 800; font-size: 0.85rem; padding: 10px 14px; border-radius: 8px; text-decoration: none; margin-top: 14px; box-shadow: 0 2px 6px rgba(4, 120, 87, 0.25); transition: background 0.2s ease;">
					🏟️ Descoperă Baza Sportivă &rarr;
				</a>
			</div>

			<!-- SIDEBAR CARD 2: CLUBUL AFC 1948 BREZOAELE -->
			<div class="brz-sidebar-card" style="background: linear-gradient(135deg, #047857 0%, #065f46 100%); color: #ffffff;">
				<span style="background: rgba(255,255,255,0.2); color: #a7f3d0; font-size: 0.7rem; font-weight: 800; padding: 3px 8px; border-radius: 10px; text-transform: uppercase;">
					Fotbal Local
				</span>
				<h3 style="font-size: 1.25rem; font-weight: 900; color: #ffffff; margin: 8px 0 2px 0;">
					AFC 1948 Brezoaele
				</h3>
				<div style="font-size: 0.8rem; color: #a7f3d0; margin-bottom: 12px;">
					(Ex-Comerțul Brezoaele)
				</div>
				<p style="font-size: 0.85rem; color: #e2e8f0; line-height: 1.5; margin: 0;">
					Echipa locală dispută meciurile de acasă pe Baza Sportivă „Ștefan Ciomartan” în <strong>Liga a 5-a SUD</strong> și campionatele de <strong>juniori U19</strong> ale AJF Dâmbovița.
				</p>
			</div>

		</div>

	</div>

	<!-- TIMELINE VIZUAL ORIZONTAL: FILE DE ISTORIE -->
	<section class="brz-timeline-section">
		<div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 8px;">
			<span style="background: #fef3c7; color: #92400e; font-size: 0.75rem; font-weight: 800; padding: 4px 10px; border-radius: 12px; text-transform: uppercase;">
				📜 File de Istorie
			</span>
		</div>
		<h2 style="font-size: 1.6rem; font-weight: 900; color: #0f172a; margin: 0 0 6px 0;">
			Istoria Fotbalului în Brezoaele (1945 – Prezent)
		</h2>
		<p style="font-size: 0.9rem; color: #64748b; margin: 0 0 20px 0;">
			O tradiție de peste șapte decenii născută din pasiunea comunității rurale. Apasă pe fiecare etapă pentru a citi articolele dedicate.
		</p>

		<?php
		$hist_post1 = get_page_by_path( '8-septembrie-1945-fotbal-brezoaele', OBJECT, 'post' );
		$hist_url1  = $hist_post1 ? get_permalink( $hist_post1->ID ) : home_url( '/sport/8-septembrie-1945-fotbal-brezoaele/' );

		$hist_post2 = get_page_by_path( 'comertul-brezoaele-anii-50-60', OBJECT, 'post' );
		$hist_url2  = $hist_post2 ? get_permalink( $hist_post2->ID ) : home_url( '/sport/comertul-brezoaele-anii-50-60/' );

		$hist_post3 = get_page_by_path( 'meciurile-pe-izlaz-deplasarile-cu-caruta', OBJECT, 'post' );
		$hist_url3  = $hist_post3 ? get_permalink( $hist_post3->ID ) : home_url( '/sport/meciurile-pe-izlaz-deplasarile-cu-caruta/' );

		$hist_post4 = get_page_by_path( 'constructia-primului-stadion-anii-70-80', OBJECT, 'post' );
		$hist_url4  = $hist_post4 ? get_permalink( $hist_post4->ID ) : home_url( '/sport/constructia-primului-stadion-anii-70-80/' );
		?>

		<div class="brz-timeline-grid">
			
			<div class="brz-timeline-card" style="display: flex; flex-direction: column; justify-content: space-between;">
				<div>
					<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
						<span class="brz-timeline-year">📅 8 Septembrie 1945</span>
						<span style="font-size: 1.3rem;">🌱</span>
					</div>
					<h3 style="font-size: 1.05rem; font-weight: 800; color: #0f172a; margin: 0 0 6px 0;">
						🌱 Înființarea „Recolta”
					</h3>
					<p style="font-size: 0.83rem; color: #475569; margin: 0 0 14px 0; line-height: 1.5;">
						Momentul zero al fotbalului rural. Selecție masivă cu formațiile <em>Recolta 1</em> și <em>Recolta 2</em> înființate de Sfânta Maria Mică.
					</p>
				</div>
				<a href="<?php echo esc_url( $hist_url1 ); ?>" style="color: #047857; font-weight: 800; text-decoration: none; font-size: 0.82rem; background: #dcfce7; padding: 6px 12px; border-radius: 8px; display: inline-block;">
					📖 Citește articolul istoric (8 Sept 1945) &rarr;
				</a>
			</div>

			<div class="brz-timeline-card" style="border-top-color: #0284c7; display: flex; flex-direction: column; justify-content: space-between;">
				<div>
					<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
						<span class="brz-timeline-year" style="background: #e0f2fe; color: #0369a1;">📅 Anii '50 – '60</span>
						<span style="font-size: 1.3rem;">🥔</span>
					</div>
					<h3 style="font-size: 1.05rem; font-weight: 800; color: #0f172a; margin: 0 0 6px 0;">
						🥔 „Comerțul Brezoaele”
					</h3>
					<p style="font-size: 0.83rem; color: #475569; margin: 0 0 14px 0; line-height: 1.5;">
						Unificarea sub aripa cooperației sătești din comuna renumită pentru agricultură și producția de cartofi.
					</p>
				</div>
				<a href="<?php echo esc_url( $hist_url2 ); ?>" style="color: #0369a1; font-weight: 800; text-decoration: none; font-size: 0.82rem; background: #e0f2fe; padding: 6px 12px; border-radius: 8px; display: inline-block;">
					📖 Citește articolul (Anii '50–'60) &rarr;
				</a>
			</div>

			<div class="brz-timeline-card" style="border-top-color: #d97706; display: flex; flex-direction: column; justify-content: space-between;">
				<div>
					<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
						<span class="brz-timeline-year" style="background: #fef3c7; color: #92400e;">⭐ Epoca de Aur</span>
						<span style="font-size: 1.3rem;">🐎</span>
					</div>
					<h3 style="font-size: 1.05rem; font-weight: 800; color: #0f172a; margin: 0 0 6px 0;">
						🐎 Meciurile pe Izlaz
					</h3>
					<p style="font-size: 0.83rem; color: #475569; margin: 0 0 14px 0; line-height: 1.5;">
						Fotbalul duminical pe terenul improvizat de pe izlaz, porți din lemn brut și deplasări cu căruța în raionul Titu.
					</p>
				</div>
				<a href="<?php echo esc_url( $hist_url3 ); ?>" style="color: #92400e; font-weight: 800; text-decoration: none; font-size: 0.82rem; background: #fef3c7; padding: 6px 12px; border-radius: 8px; display: inline-block;">
					📖 Citește articolul (Meciurile pe izlaz) &rarr;
				</a>
			</div>

			<div class="brz-timeline-card" style="border-top-color: #059669; display: flex; flex-direction: column; justify-content: space-between;">
				<div>
					<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
						<span class="brz-timeline-year" style="background: #dcfce7; color: #166534;">🏟️ Anii '70 – '80</span>
						<span style="font-size: 1.3rem;">🏗️</span>
					</div>
					<h3 style="font-size: 1.05rem; font-weight: 800; color: #0f172a; margin: 0 0 6px 0;">
						🏟️ Adio Izlaz! Primul Stadion
					</h3>
					<p style="font-size: 0.83rem; color: #475569; margin: 0 0 14px 0; line-height: 1.5;">
						Construirea Stadionului Central, nivelarea gazonului, porți metalice și primele vestiare proprii din Brezoaele.
					</p>
				</div>
				<a href="<?php echo esc_url( $hist_url4 ); ?>" style="color: #166534; font-weight: 800; text-decoration: none; font-size: 0.82rem; background: #dcfce7; padding: 6px 12px; border-radius: 8px; display: inline-block;">
					📖 Citește articolul (Stadionul '70-'80) &rarr;
				</a>
			</div>

		</div>
	</section>

	<!-- FORMULAR TRIMITE ȘTIRI / POZE -->
	<section style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff; border-radius: 20px; padding: 32px; display: grid; grid-template-columns: 1fr 1fr; gap: 30px; align-items: center;">
		<div>
			<span style="background: rgba(52, 211, 153, 0.2); color: #34d399; font-size: 0.75rem; font-weight: 800; padding: 4px 10px; border-radius: 20px; text-transform: uppercase;">
				📸 Implicare Comunitate
			</span>
			<h3 style="font-size: 1.5rem; font-weight: 900; margin: 10px 0 6px 0; color: #ffffff;">
				Trimite-ne informații sau poze din fotbalul local!
			</h3>
			<p style="color: #94a3b8; font-size: 0.9rem; margin: 0; line-height: 1.5;">
				Ai poze de la meciurile desfășurate pe Baza Sportivă „Ștefan Ciomartan” sau din istoria echipei Recolta / Comerțul? Trimite-ne un mesaj!
			</p>
		</div>

		<div>
			<form id="brz-sport-community-form" style="background: rgba(255, 255, 255, 0.05); padding: 18px; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.1);">
				<?php wp_nonce_field( 'brz_sport_community_nonce_action', 'brz_sport_community_nonce' ); ?>
				<input type="text" name="nume" placeholder="Numele tău" required style="width: 100%; padding: 10px 14px; margin-bottom: 10px; border-radius: 8px; border: 1px solid #334155; background: #0f172a; color: #fff; font-family: inherit; box-sizing: border-box;" />
				<input type="email" name="email" placeholder="Adresa ta de email" required style="width: 100%; padding: 10px 14px; margin-bottom: 10px; border-radius: 8px; border: 1px solid #334155; background: #0f172a; color: #fff; font-family: inherit; box-sizing: border-box;" />
				<textarea name="mesaj" placeholder="Detalii meci, poze sau amintiri..." rows="3" required style="width: 100%; padding: 10px 14px; margin-bottom: 10px; border-radius: 8px; border: 1px solid #334155; background: #0f172a; color: #fff; font-family: inherit; box-sizing: border-box;"></textarea>
				<button type="submit" id="brz-sport-submit-btn" style="width: 100%; background: #059669; color: #fff; border: none; padding: 11px; border-radius: 8px; font-weight: 800; cursor: pointer; transition: background 0.2s;">
					📤 Trimite Informațiile
				</button>
				<div id="brz-sport-form-response" style="margin-top: 12px; font-weight: 700; font-size: 0.88rem; display: none; padding: 10px 14px; border-radius: 8px;"></div>
			</form>
		</div>
	</section>

	<script>
	document.addEventListener('DOMContentLoaded', function() {
		var form = document.getElementById('brz-sport-community-form');
		if (!form) return;

		form.addEventListener('submit', function(e) {
			e.preventDefault();

			var submitBtn = document.getElementById('brz-sport-submit-btn');
			var responseDiv = document.getElementById('brz-sport-form-response');

			var nume = form.querySelector('[name="nume"]').value.trim();
			var email = form.querySelector('[name="email"]').value.trim();
			var mesaj = form.querySelector('[name="mesaj"]').value.trim();
			var nonce = form.querySelector('#brz_sport_community_nonce').value;

			if (!nume || !email || !mesaj) {
				responseDiv.style.display = 'block';
				responseDiv.style.background = '#fef2f2';
				responseDiv.style.color = '#991b1b';
				responseDiv.style.border = '1px solid #fca5a5';
				responseDiv.textContent = 'Te rugăm să completezi toate câmpurile.';
				return;
			}

			submitBtn.disabled = true;
			submitBtn.textContent = '⏳ Se trimite...';
			responseDiv.style.display = 'none';

			var formData = new FormData();
			formData.append('action', 'brz_submit_sport_community');
			formData.append('nume', nume);
			formData.append('email', email);
			formData.append('mesaj', mesaj);
			formData.append('nonce', nonce);

			fetch('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', {
				method: 'POST',
				body: formData
			})
			.then(function(res) { return res.json(); })
			.then(function(data) {
				submitBtn.disabled = false;
				submitBtn.textContent = '📤 Trimite Informațiile';
				responseDiv.style.display = 'block';

				if (data.success) {
					responseDiv.style.background = '#ecfdf5';
					responseDiv.style.color = '#065f46';
					responseDiv.style.border = '1px solid #6ee7b7';
					responseDiv.textContent = data.data.message || 'Mesaj trimis cu succes!';
					form.reset();
				} else {
					responseDiv.style.background = '#fef2f2';
					responseDiv.style.color = '#991b1b';
					responseDiv.style.border = '1px solid #fca5a5';
					responseDiv.textContent = (data.data && data.data.message) ? data.data.message : 'A apărut o eroare la trimitere.';
				}
			})
			.catch(function(err) {
				submitBtn.disabled = false;
				submitBtn.textContent = '📤 Trimite Informațiile';
				responseDiv.style.display = 'block';
				responseDiv.style.background = '#fef2f2';
				responseDiv.style.color = '#991b1b';
				responseDiv.style.border = '1px solid #fca5a5';
				responseDiv.textContent = 'Eroare de conexiune. Te rugăm să încerci din nou.';
			});
		});
	});
	</script>

</main>

<?php
get_footer();
