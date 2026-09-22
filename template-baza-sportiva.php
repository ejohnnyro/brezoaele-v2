<?php
/**
 * Template Name: Prezentare Baza Sportivă Brezoaele (/sport/baza-sportiva/)
 * Description: Pagina de prezentare detaliată a Complexului Sportiv Central Brezoaele (Stadionul Ștefan Ciomârtan, Sala de Sport Multifuncțională & Parcul Central).
 *
 * @package Brezoaele_V2
 */

get_header();
?>

<style>
/* Stiluri Prezentare Baza Sportivă Brezoaele */
.brz-baza-hero {
	position: relative;
	background-size: cover;
	background-position: center;
	background-repeat: no-repeat;
	color: #ffffff;
	padding: 54px 20px 60px;
}
.brz-baza-hero-container {
	max-width: 1200px;
	margin: 0 auto;
}
.brz-baza-back-link {
	display: inline-flex;
	align-items: center;
	gap: 6px;
	color: #a7f3d0;
	font-weight: 700;
	font-size: 0.88rem;
	text-decoration: none;
	margin-bottom: 16px;
	transition: color 0.2s ease;
}
.brz-baza-back-link:hover {
	color: #ffffff;
	text-decoration: underline;
}
.brz-baza-badge {
	background: rgba(16, 185, 129, 0.25);
	color: #34d399;
	border: 1px solid rgba(52, 211, 153, 0.4);
	padding: 5px 14px;
	border-radius: 20px;
	font-weight: 800;
	font-size: 0.8rem;
	text-transform: uppercase;
	display: inline-block;
	margin-bottom: 12px;
}
.brz-baza-title {
	font-size: 2.6rem;
	font-weight: 900;
	margin: 0 0 12px 0;
	line-height: 1.2;
	background: linear-gradient(to right, #ffffff, #a7f3d0);
	-webkit-background-clip: text;
	-webkit-text-fill-color: transparent;
}
.brz-baza-subtitle {
	font-size: 1.15rem;
	color: #cbd5e1;
	margin: 0 0 28px 0;
	max-width: 850px;
	line-height: 1.6;
}

/* HIGHLIGHT STATS GRID */
.brz-baza-stats-grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
	gap: 16px;
	max-width: 1200px;
	margin: 0 auto;
}
.brz-baza-stat-card {
	background: rgba(255, 255, 255, 0.08);
	backdrop-filter: blur(8px);
	border: 1px solid rgba(255, 255, 255, 0.15);
	border-radius: 12px;
	padding: 18px 20px;
}
.brz-baza-stat-icon {
	font-size: 1.8rem;
	margin-bottom: 8px;
}
.brz-baza-stat-title {
	font-size: 1.05rem;
	font-weight: 800;
	color: #ffffff;
	margin-bottom: 4px;
}
.brz-baza-stat-desc {
	font-size: 0.85rem;
	color: #94a3b8;
	line-height: 1.4;
}

/* MAIN LAYOUT */
.brz-baza-main {
	max-width: 1200px;
	margin: 40px auto 60px;
	padding: 0 20px;
}
.brz-baza-grid {
	display: grid;
	grid-template-columns: 1fr 340px;
	gap: 36px;
}
@media (max-width: 992px) {
	.brz-baza-grid {
		grid-template-columns: 1fr;
	}
	.brz-baza-title {
		font-size: 2rem;
	}
}

/* CONTENT SECTIONS */
.brz-baza-intro-card {
	background: #f8fafc;
	border: 1px solid #e2e8f0;
	border-left: 4px solid #047857;
	border-radius: 12px;
	padding: 24px 28px;
	margin-bottom: 32px;
}
.brz-baza-intro-title {
	font-size: 1.35rem;
	font-weight: 900;
	color: #0f172a;
	margin: 0 0 12px 0;
}
.brz-baza-intro-text {
	font-size: 1.02rem;
	color: #334155;
	line-height: 1.7;
	margin: 0;
}

.brz-baza-section-block {
	background: #ffffff;
	border: 1px solid #e2e8f0;
	border-radius: 16px;
	padding: 30px;
	margin-bottom: 32px;
	box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
}
.brz-baza-section-header {
	display: flex;
	align-items: center;
	gap: 12px;
	margin-bottom: 18px;
	padding-bottom: 14px;
	border-bottom: 2px solid #f1f5f9;
}
.brz-baza-section-icon {
	width: 46px;
	height: 46px;
	border-radius: 12px;
	background: #ecfdf5;
	color: #047857;
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 1.5rem;
}
.brz-baza-section-title {
	font-size: 1.5rem;
	font-weight: 900;
	color: #0f172a;
	margin: 0;
}
.brz-baza-text-p {
	font-size: 1rem;
	color: #334155;
	line-height: 1.7;
	margin-bottom: 18px;
}

/* CARDS SUB-GRID */
.brz-feature-subgrid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
	gap: 18px;
	margin-top: 20px;
}
.brz-feature-card {
	background: #f8fafc;
	border: 1px solid #e2e8f0;
	border-radius: 12px;
	padding: 20px;
	transition: transform 0.2s ease, border-color 0.2s ease;
}
.brz-feature-card:hover {
	transform: translateY(-2px);
	border-color: #a7f3d0;
}
.brz-feature-title {
	font-size: 1.08rem;
	font-weight: 800;
	color: #0f172a;
	margin: 0 0 8px 0;
	display: flex;
	align-items: center;
	gap: 8px;
}
.brz-feature-text {
	font-size: 0.92rem;
	color: #475569;
	line-height: 1.55;
	margin: 0;
}

/* CALLOUT BOX FOR SALA DE SPORT */
.brz-sala-callout {
	background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
	color: #ffffff;
	border-radius: 12px;
	padding: 22px 26px;
	margin-top: 24px;
	display: flex;
	align-items: center;
	justify-content: space-between;
	flex-wrap: wrap;
	gap: 16px;
}
.brz-sala-callout-text h4 {
	margin: 0 0 6px 0;
	font-size: 1.15rem;
	font-weight: 800;
	color: #ffffff;
}
.brz-sala-callout-text p {
	margin: 0;
	font-size: 0.88rem;
	color: #94a3b8;
}
.brz-sala-btn {
	background: #047857;
	color: #ffffff !important;
	font-weight: 800;
	font-size: 0.88rem;
	padding: 10px 18px;
	border-radius: 8px;
	text-decoration: none;
	transition: background 0.2s ease;
	white-space: nowrap;
}
.brz-sala-btn:hover {
	background: #059669;
}

/* GALLERY CONTAINER */
.brz-gallery-grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
	gap: 16px;
	margin-top: 20px;
}
.brz-gallery-item {
	position: relative;
	border-radius: 12px;
	overflow: hidden;
	height: 200px;
	background: #e2e8f0;
}
.brz-gallery-item img {
	width: 100%;
	height: 100%;
	object-fit: cover;
	transition: transform 0.3s ease;
}
.brz-gallery-item:hover img {
	transform: scale(1.05);
}
.brz-gallery-caption {
	position: absolute;
	bottom: 0;
	left: 0;
	right: 0;
	background: linear-gradient(transparent, rgba(15, 23, 42, 0.85));
	color: #ffffff;
	padding: 12px 14px 8px;
	font-size: 0.82rem;
	font-weight: 700;
}
.brz-section-gallery-title {
	font-size: 0.95rem;
	font-weight: 800;
	color: #0f172a;
	margin: 22px 0 12px 0;
	display: flex;
	align-items: center;
	gap: 8px;
	padding-top: 18px;
	border-top: 1px dashed #e2e8f0;
}

/* SIDEBAR STYLES */
.brz-baza-sidebar-box {
	background: #ffffff;
	border: 1px solid #e2e8f0;
	border-radius: 16px;
	padding: 24px;
	margin-bottom: 24px;
	box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
}
.brz-sidebar-title {
	font-size: 1.15rem;
	font-weight: 900;
	color: #0f172a;
	margin: 0 0 16px 0;
	padding-bottom: 10px;
	border-bottom: 2px solid #f1f5f9;
}
.brz-info-list {
	list-style: none;
	padding: 0;
	margin: 0;
}
.brz-info-list li {
	display: flex;
	justify-content: space-between;
	align-items: flex-start;
	gap: 10px;
	font-size: 0.88rem;
	padding: 10px 0;
	border-bottom: 1px dashed #e2e8f0;
}
.brz-info-list li:last-child {
	border-bottom: none;
}
.brz-info-label {
	color: #64748b;
	font-weight: 600;
}
.brz-info-val {
	color: #0f172a;
	font-weight: 800;
	text-align: right;
}

/* SECTION GALLERY SLIDER & WRAPPER STYLES */
.brz-section-gallery-wrapper {
	margin-top: 24px;
	padding-top: 18px;
	border-top: 1px dashed #e2e8f0;
}
.brz-gallery-header-row {
	display: flex;
	align-items: center;
	justify-content: space-between;
	flex-wrap: wrap;
	gap: 12px;
	margin-bottom: 16px;
}
.brz-gallery-nav-controls {
	display: flex;
	align-items: center;
	gap: 10px;
}
.brz-gallery-page-indicator {
	font-size: 0.82rem;
	color: #64748b;
	font-weight: 600;
	background: #f1f5f9;
	padding: 4px 10px;
	border-radius: 12px;
	border: 1px solid #e2e8f0;
}
.brz-gal-slide-btn {
	background: #047857;
	color: #ffffff;
	border: none;
	width: 36px;
	height: 36px;
	border-radius: 50%;
	font-size: 1.3rem;
	cursor: pointer;
	display: flex;
	align-items: center;
	justify-content: center;
	transition: all 0.2s ease;
	line-height: 1;
	box-shadow: 0 2px 6px rgba(4, 120, 87, 0.25);
}
.brz-gal-slide-btn:hover:not(:disabled) {
	background: #059669;
	transform: scale(1.08);
}
.brz-gal-slide-btn:disabled {
	background: #cbd5e1;
	color: #94a3b8;
	box-shadow: none;
	cursor: not-allowed;
	opacity: 0.6;
}

.brz-gallery-grid-slider {
	display: grid;
	grid-template-columns: repeat(4, 1fr);
	gap: 16px;
	transition: opacity 0.2s ease, transform 0.2s ease;
}
@media (max-width: 992px) {
	.brz-gallery-grid-slider {
		grid-template-columns: repeat(2, 1fr);
	}
}
@media (max-width: 576px) {
	.brz-gallery-grid-slider {
		display: flex !important;
		overflow-x: auto !important;
		scroll-snap-type: x mandatory !important;
		-webkit-overflow-scrolling: touch !important;
		gap: 12px !important;
		padding-bottom: 8px !important;
		margin-left: 0 !important;
		margin-right: 0 !important;
	}
	.brz-gallery-grid-slider::-webkit-scrollbar {
		height: 4px;
	}
	.brz-gallery-grid-slider::-webkit-scrollbar-track {
		background: #f1f5f9;
		border-radius: 4px;
	}
	.brz-gallery-grid-slider::-webkit-scrollbar-thumb {
		background: #047857;
		border-radius: 4px;
	}
	.brz-gallery-grid-slider .brz-gallery-item {
		flex: 0 0 82% !important;
		max-width: 82% !important;
		scroll-snap-align: start !important;
		height: 210px !important;
	}
}

/* LIGHTBOX MODAL STYLES */
.brz-gallery-item {
	cursor: zoom-in;
}
.brz-lightbox-overlay {
	position: fixed;
	top: 0;
	left: 0;
	width: 100vw;
	height: 100vh;
	background: rgba(15, 23, 42, 0.92);
	backdrop-filter: blur(10px);
	z-index: 999999;
	display: flex;
	align-items: center;
	justify-content: center;
	opacity: 0;
	pointer-events: none;
	transition: opacity 0.25s ease;
}
.brz-lightbox-overlay.active {
	opacity: 1;
	pointer-events: auto;
}
.brz-lightbox-content {
	position: relative;
	max-width: 90vw;
	max-height: 85vh;
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;
	transform: scale(0.92);
	transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}
.brz-lightbox-overlay.active .brz-lightbox-content {
	transform: scale(1);
}
.brz-lightbox-img {
	max-width: 100%;
	max-height: 80vh;
	border-radius: 12px;
	box-shadow: 0 20px 40px rgba(0, 0, 0, 0.7);
	object-fit: contain;
	border: 1px solid rgba(255, 255, 255, 0.15);
}
.brz-lightbox-caption {
	margin-top: 14px;
	color: #e2e8f0;
	font-size: 0.98rem;
	font-weight: 700;
	text-align: center;
	background: rgba(15, 23, 42, 0.75);
	border: 1px solid rgba(255, 255, 255, 0.15);
	padding: 8px 20px;
	border-radius: 20px;
	max-width: 85%;
	backdrop-filter: blur(6px);
}
.brz-lightbox-close {
	position: absolute;
	top: 20px;
	right: 24px;
	background: rgba(255, 255, 255, 0.15);
	color: #ffffff;
	border: 1px solid rgba(255, 255, 255, 0.3);
	width: 44px;
	height: 44px;
	border-radius: 50%;
	font-size: 1.5rem;
	cursor: pointer;
	display: flex;
	align-items: center;
	justify-content: center;
	transition: all 0.2s ease;
	z-index: 1000000;
}
.brz-lightbox-close:hover {
	background: rgba(239, 68, 68, 0.85);
	border-color: #ef4444;
	transform: scale(1.1);
}
.brz-lightbox-nav {
	position: absolute;
	top: 50%;
	transform: translateY(-50%);
	background: rgba(255, 255, 255, 0.15);
	color: #ffffff;
	border: 1px solid rgba(255, 255, 255, 0.3);
	width: 48px;
	height: 48px;
	border-radius: 50%;
	font-size: 1.8rem;
	cursor: pointer;
	display: flex;
	align-items: center;
	justify-content: center;
	transition: all 0.2s ease;
	z-index: 1000000;
	user-select: none;
}
.brz-lightbox-nav:hover {
	background: rgba(4, 120, 87, 0.85);
	border-color: #34d399;
	transform: translateY(-50%) scale(1.1);
}
.brz-lightbox-prev {
	left: 24px;
}
.brz-lightbox-next {
	right: 24px;
}
@media (max-width: 768px) {
	.brz-lightbox-nav {
		width: 40px;
		height: 40px;
		font-size: 1.4rem;
	}
	.brz-lightbox-prev { left: 10px; }
	.brz-lightbox-next { right: 10px; }
}
</style>

<?php
// Hero Background setup
$default_hero_bg = get_template_directory_uri() . '/images/stadion.jpg';
$hero_bg_url     = get_post_meta( get_the_ID(), '_brz_sport_hero_image', true );
$pos_x           = get_post_meta( get_the_ID(), '_brz_sport_hero_pos_x', true );
$pos_y           = get_post_meta( get_the_ID(), '_brz_sport_hero_pos_y', true );

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

// Fetch Dynamic Galleries
$gal_stadion = get_post_meta( get_the_ID(), '_brz_gallery_stadion', true );
$gal_sala    = get_post_meta( get_the_ID(), '_brz_gallery_sala', true );
$gal_parc    = get_post_meta( get_the_ID(), '_brz_gallery_parc', true );

// Fetch Dynamic Content Meta Box Data with Fallback Defaults
$hero_badge    = get_post_meta( get_the_ID(), '_brz_baza_hero_badge', true );
if ( empty( $hero_badge ) ) $hero_badge = '📍 Inima Comunei Brezoaele („La Bâlci”)';

$hero_title    = get_post_meta( get_the_ID(), '_brz_baza_hero_title', true );
if ( empty( $hero_title ) ) $hero_title = 'Complexul Sportiv Central Brezoaele';

$hero_subtitle = get_post_meta( get_the_ID(), '_brz_baza_hero_subtitle', true );
if ( empty( $hero_subtitle ) ) $hero_subtitle = 'Infrastructură modernă pentru sportul local: Stadionul „Ștefan Ciomârtan”, Sala de Sport Multifuncțională și Parcul Central de Relaxare reunite într-un singur spațiu comunitar dedicat performanței și mișcării.';

$intro_title   = get_post_meta( get_the_ID(), '_brz_baza_intro_title', true );
if ( empty( $intro_title ) ) $intro_title = 'Infrastructură Modernă pentru Sportul Local';

$intro_text    = get_post_meta( get_the_ID(), '_brz_baza_intro_text', true );
if ( empty( $intro_text ) ) $intro_text = 'Situată chiar în inima comunei, <strong>Baza Sportivă Centrală Brezoaele</strong> reprezintă principalul nucleu dedicat mișcării, performanței și recreerii din localitate. Dezvoltată continuu prin investiții locale și proiecte publice, baza reunește astăzi facilități care deservesc atât echipele oficiale ale comunei (AFC 1948 Brezoaele), cât și comunitatea locală, oferind condiții optime de antrenament și competiție indiferent de sezon.';

// Section 1
$s1_title      = get_post_meta( get_the_ID(), '_brz_baza_s1_title', true );
if ( empty( $s1_title ) ) $s1_title = 'Stadionul „Ștefan Ciomârtan” Brezoaele';

$s1_subtitle   = get_post_meta( get_the_ID(), '_brz_baza_s1_subtitle', true );
if ( empty( $s1_subtitle ) ) $s1_subtitle = 'Casa Oficială a echipei AFC 1948 Brezoaele';

$s1_desc       = get_post_meta( get_the_ID(), '_brz_baza_s1_desc', true );
if ( empty( $s1_desc ) ) $s1_desc = 'Stadionul „Ștefan Ciomârtan” este „casa” oficială a echipei de fotbal AFC 1948 Brezoaele, dar și o arenă gazdă apreciată la nivel regional pentru calitatea facilităților sale.';

$raw_s1_cards  = get_post_meta( get_the_ID(), '_brz_baza_s1_cards', true );
if ( empty( $raw_s1_cards ) || ! is_array( $raw_s1_cards ) ) {
	$s1_cards_list = array(
		array( 'icon' => '🌱', 'title' => 'Suprafața de Joc', 'text' => 'Stadionul dispune de un gazon natural excelent întreținut, recunoscut la nivel județean drept unul dintre cele mai bune și rezistente terenuri din competițiile organizate de AJF Dâmbovița.' ),
		array( 'icon' => '🛡️', 'title' => 'Împrejmuire și Siguranță', 'text' => 'Întreaga arenă este securizată printr-un gard perimetral modern, asigurând delimitarea clară a zonei de joc conform normelor riguroase de siguranță impuse de regulamentele sportive.' ),
	);
} else {
	$s1_cards_list = $raw_s1_cards;
}

// Section 2
$s2_title      = get_post_meta( get_the_ID(), '_brz_baza_s2_title', true );
if ( empty( $s2_title ) ) $s2_title = 'Sala de Sport Brezoaele';

$s2_subtitle   = get_post_meta( get_the_ID(), '_brz_baza_s2_subtitle', true );
if ( empty( $s2_subtitle ) ) $s2_subtitle = 'Complex Acoperit & Multifuncțional';

$s2_desc       = get_post_meta( get_the_ID(), '_brz_baza_s2_desc', true );
if ( empty( $s2_desc ) ) $s2_desc = 'Finalizată în perioada modernă, Sala de Sport completează perfect complexul central și oferă o alternativă excelentă pentru antrenamente și competiții acoperite pe tot parcursul anului.';

$raw_s2_cards  = get_post_meta( get_the_ID(), '_brz_baza_s2_cards', true );
if ( empty( $raw_s2_cards ) || ! is_array( $raw_s2_cards ) ) {
	$s2_cards_list = array(
		array( 'icon' => '⚽', 'title' => 'Destinație Multifuncțională', 'text' => 'Sala este optimizată pentru desfășurarea meciurilor de minifotbal, handbal, baschet și volei, dispunând de suprafață omologată și vestiare echipate.' ),
		array( 'icon' => '❄️', 'title' => 'Continuitate pe Timp de Iarnă', 'text' => 'Reprezintă elementul cheie pentru grupele de juniori și seniori ale comunei, permițând pregătirea fizică și tactică neîntreruptă pe tot parcursul sezonului rece.' ),
		array( 'icon' => '🎓', 'title' => 'Facilități pentru Comunitate & Școală', 'text' => 'Pe lângă activitatea clubului sportiv, sala este destinată orelor de educație fizică ale școlilor din comună și poate fi utilizată de localnici pentru activități recreative pe bază de programare.' ),
	);
} else {
	$s2_cards_list = $raw_s2_cards;
}

$s2_callout_title = get_post_meta( get_the_ID(), '_brz_baza_s2_callout_title', true );
if ( empty( $s2_callout_title ) ) $s2_callout_title = 'Ai nevoie de detalii despre programări la Sala de Sport?';

$s2_callout_text = get_post_meta( get_the_ID(), '_brz_baza_s2_callout_text', true );
if ( empty( $s2_callout_text ) ) $s2_callout_text = 'Vezi fișa completă, specificațiile tehnice și datele de acces ale Sălii de Sport Brezoaele.';

$s2_callout_btn_text = get_post_meta( get_the_ID(), '_brz_baza_s2_callout_btn_text', true );
if ( empty( $s2_callout_btn_text ) ) $s2_callout_btn_text = 'Vezi Fișa Sălii de Sport →';

$s2_callout_btn_url = get_post_meta( get_the_ID(), '_brz_baza_s2_callout_btn_url', true );
if ( empty( $s2_callout_btn_url ) ) $s2_callout_btn_url = home_url( '/afaceri-locale/sala-de-sport-din-comuna-brezoaele/' );

// Section 3
$s3_title      = get_post_meta( get_the_ID(), '_brz_baza_s3_title', true );
if ( empty( $s3_title ) ) $s3_title = 'Parcul Central & Spațiul Comunitar Integration';

$s3_subtitle   = get_post_meta( get_the_ID(), '_brz_baza_s3_subtitle', true );
if ( empty( $s3_subtitle ) ) $s3_subtitle = 'Zonă Verde de Relaxare pentru Toate Vârstele';

$s3_desc       = get_post_meta( get_the_ID(), '_brz_baza_s3_desc', true );
if ( empty( $s3_desc ) ) $s3_desc = 'Unul dintre marile avantaje ale bazei sportive din Brezoaele este integrarea sa într-un spațiu comunitar complet. Parcul din fața Sălii de Sport funcționează ca o zonă verde primitoare de relaxare.';

$raw_s3_cards  = get_post_meta( get_the_ID(), '_brz_baza_s3_cards', true );
if ( empty( $raw_s3_cards ) || ! is_array( $raw_s3_cards ) ) {
	$s3_cards_list = array(
		array( 'icon' => '🪑', 'title' => 'Spațiu de Relaxare', 'text' => 'Aici, părinții, bunicii și copiii se pot relaxa înainte sau după meciuri, spațiul fiind amenajat cu alei pietonale umbroase și bănci confortabile.' ),
		array( 'icon' => '📹', 'title' => 'Curățenie & Monitorizare', 'text' => 'Pentru menținerea unui climat civilizat și sigur, întreaga zonă este supravegheată video, curățenia fiind o prioritate administrativă strict respectată.' ),
	);
} else {
	$s3_cards_list = $raw_s3_cards;
}

// Stats Grid
$raw_stats = get_post_meta( get_the_ID(), '_brz_baza_stats_grid', true );
if ( empty( $raw_stats ) || ! is_array( $raw_stats ) ) {
	$stats_grid_list = array(
		array( 'icon' => '🏟️', 'title' => 'Stadionul Central', 'desc' => 'Suprafață cu gazon natural excelent întreținut & tribună de 50 locuri.' ),
		array( 'icon' => '🏢', 'title' => 'Sală Multifuncțională', 'desc' => 'Minifotbal, handbal, baschet, volei & antrenamente acoperite de iarnă.' ),
		array( 'icon' => '🌳', 'title' => 'Parc & Zona Verde', 'desc' => 'Spațiu verde de relaxare cu alei, bănci & monitorizare video perimetrală.' ),
		array( 'icon' => '📍', 'title' => 'Locație Centrală', 'desc' => 'Amplasată în centrul comunei Brezoaele (zona tradițională „La Bâlci”).' ),
	);
} else {
	$stats_grid_list = $raw_stats;
}

// Tech Specs (Fișă Tehnică Sidebar)
$specs_title   = get_post_meta( get_the_ID(), '_brz_baza_tech_specs_title', true );
if ( empty( $specs_title ) ) $specs_title = '📍 Fișă Tehnică Bază Sportivă';

$raw_specs     = get_post_meta( get_the_ID(), '_brz_baza_tech_specs', true );
if ( empty( $raw_specs ) || ! is_array( $raw_specs ) ) {
	$tech_specs_list = array(
		array( 'label' => 'Locație:', 'val' => 'Centru („La Bâlci”)' ),
		array( 'label' => 'Comună:', 'val' => 'Brezoaele, Dâmbovița' ),
		array( 'label' => 'Stadion:', 'val' => '„Ștefan Ciomârtan”' ),
		array( 'label' => 'Capacitate:', 'val' => '50 locuri pe scaune' ),
		array( 'label' => 'Echipă Gazdă:', 'val' => 'AFC 1948 Brezoaele' ),
		array( 'label' => 'Liga:', 'val' => 'Liga 5 SUD Dâmbovița' ),
		array( 'label' => 'Sală Acoperită:', 'val' => 'Da (Multifuncțională)' ),
		array( 'label' => 'An Înființare:', 'val' => '8 Septembrie 1945' ),
	);
} else {
	$tech_specs_list = $raw_specs;
}

// Sidebar Program Card
$sb_prog_title  = get_post_meta( get_the_ID(), '_brz_baza_sb_program_title', true );
if ( empty( $sb_prog_title ) ) $sb_prog_title = '📅 Accesibilitate & Program';

$sb_prog_text   = get_post_meta( get_the_ID(), '_brz_baza_sb_program_text', true );
if ( empty( $sb_prog_text ) ) $sb_prog_text = 'Baza Sportivă Centrală este deschisă tuturor iubitorilor de sport din comună, respectând programul stabilit pentru antrenamentele oficiale și meciurile de campionat.';

$sb_prog_notice = get_post_meta( get_the_ID(), '_brz_baza_sb_program_notice', true );
if ( empty( $sb_prog_notice ) ) $sb_prog_notice = '💡 Pentru solicitări oficiale, rezervări sau cereri administrative privind Sala de Sport și baza sportivă, adresați-vă Primăriei Comunei Brezoaele pe site-ul oficial <a href="https://www.primariabrezoaele.ro/" target="_blank" rel="noopener noreferrer" style="color: #ffffff; font-weight: 800; text-decoration: underline;">www.primariabrezoaele.ro</a>.';

/**
 * Helper function: Render section gallery slider with max 4 items per page and slide controls
 */
function brezoaele_render_section_gallery_slider( $gallery_id, $title, $custom_items, $default_items ) {
	$list = ( ! empty( $custom_items ) && is_array( $custom_items ) ) ? $custom_items : $default_items;
	if ( ! empty( $list ) && is_array( $list ) ) {
		shuffle( $list );
	}
	$total_items = count( $list );
	$items_per_page = 4;
	$total_pages = ceil( $total_items / $items_per_page );
	if ( $total_pages < 1 ) $total_pages = 1;
	?>
	<div class="brz-section-gallery-wrapper" data-gallery-id="<?php echo esc_attr( $gallery_id ); ?>" data-total-pages="<?php echo esc_attr( $total_pages ); ?>">
		
		<div class="brz-gallery-header-row">
			<div class="brz-section-gallery-title">
				<?php echo esc_html( $title ); ?>
			</div>
			<?php if ( $total_pages > 1 ) : ?>
				<div class="brz-gallery-nav-controls">
					<span class="brz-gallery-page-indicator">
						Pagina <strong class="brz-current-page">1</strong> din <span class="brz-total-pages"><?php echo esc_html( $total_pages ); ?></span>
					</span>
					<button type="button" class="brz-gal-slide-btn brz-gal-prev" disabled aria-label="Înapoi">&lsaquo;</button>
					<button type="button" class="brz-gal-slide-btn brz-gal-next" aria-label="Înainte">&rsaquo;</button>
				</div>
			<?php endif; ?>
		</div>

		<!-- RAW JSON STORAGE FOR ALL ITEMS IN THIS SECTION GALLERY -->
		<script type="application/json" class="brz-gallery-raw-json"><?php echo wp_json_encode( array_values( $list ) ); ?></script>

		<div class="brz-gallery-grid-slider" id="brz_grid_<?php echo esc_attr( $gallery_id ); ?>">
			<?php
			$page1_items = array_slice( $list, 0, $items_per_page );
			foreach ( $page1_items as $idx => $img_item ) :
				$url     = isset( $img_item['url'] ) ? $img_item['url'] : '';
				$caption = isset( $img_item['title'] ) ? $img_item['title'] : '';
				?>
				<div class="brz-gallery-item" data-idx="<?php echo esc_attr( $idx ); ?>" data-full-src="<?php echo esc_url( $url ); ?>">
					<img src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( $caption ); ?>" loading="lazy">
					<?php if ( ! empty( $caption ) ) : ?>
						<div class="brz-gallery-caption"><?php echo esc_html( $caption ); ?></div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>

	</div>
	<?php
}
?>

<!-- HERO HEADER BAZA SPORTIVĂ -->
<section class="brz-baza-hero" style="background-image: linear-gradient(135deg, rgba(15, 23, 42, 0.90) 0%, rgba(30, 41, 59, 0.85) 60%, rgba(4, 120, 87, 0.80) 100%), url('<?php echo esc_url( $hero_bg_url ); ?>'); background-position: <?php echo $bg_position_style; ?>;">
	<div class="brz-baza-hero-container">
		<a href="<?php echo esc_url( home_url( '/sport/' ) ); ?>" class="brz-baza-back-link">
			&larr; Înapoi la Portalul Sport Brezoaele (/sport/)
		</a>
		<br>
		<span class="brz-baza-badge">
			<?php echo esc_html( $hero_badge ); ?>
		</span>
		<h1 class="brz-baza-title">
			<?php echo esc_html( $hero_title ); ?>
		</h1>
		<p class="brz-baza-subtitle">
			<?php echo esc_html( $hero_subtitle ); ?>
		</p>

		<!-- HIGHLIGHT STATS GRID -->
		<div class="brz-baza-stats-grid">
			<?php foreach ( $stats_grid_list as $st ) :
				$st_icon  = isset( $st['icon'] ) ? $st['icon'] : '';
				$st_title = isset( $st['title'] ) ? $st['title'] : '';
				$st_desc  = isset( $st['desc'] ) ? $st['desc'] : '';
				?>
				<div class="brz-baza-stat-card">
					<?php if ( ! empty( $st_icon ) ) : ?>
						<div class="brz-baza-stat-icon"><?php echo esc_html( $st_icon ); ?></div>
					<?php endif; ?>
					<div class="brz-baza-stat-title"><?php echo esc_html( $st_title ); ?></div>
					<div class="brz-baza-stat-desc"><?php echo esc_html( $st_desc ); ?></div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- MAIN CONTENT CONTAINER -->
<main class="brz-baza-main">

	<div class="brz-baza-grid">

		<!-- COLOANA STÂNGĂ: PREZENTARE DETALIATĂ -->
		<div>

			<!-- CARD INTRODUCERE -->
			<div class="brz-baza-intro-card">
				<h2 class="brz-baza-intro-title">
					<?php echo esc_html( $intro_title ); ?>
				</h2>
				<p class="brz-baza-intro-text">
					<?php echo wp_kses_post( $intro_text ); ?>
				</p>
			</div>

			<!-- SECTIUNEA 1: STADIONUL STEFAN CIOMARTAN -->
			<div class="brz-baza-section-block">
				<div class="brz-baza-section-header">
					<div class="brz-baza-section-icon">🏟️</div>
					<div>
						<h2 class="brz-baza-section-title"><?php echo esc_html( $s1_title ); ?></h2>
						<?php if ( ! empty( $s1_subtitle ) ) : ?>
							<span style="font-size: 0.85rem; color: #64748b; font-weight: 700;"><?php echo esc_html( $s1_subtitle ); ?></span>
						<?php endif; ?>
					</div>
				</div>

				<p class="brz-baza-text-p">
					<?php echo wp_kses_post( $s1_desc ); ?>
				</p>

				<div class="brz-feature-subgrid">
					<?php foreach ( $s1_cards_list as $card ) :
						$c_icon  = isset( $card['icon'] ) ? $card['icon'] : '';
						$c_title = isset( $card['title'] ) ? $card['title'] : '';
						$c_text  = isset( $card['text'] ) ? $card['text'] : '';
						?>
						<div class="brz-feature-card">
							<h3 class="brz-feature-title"><?php echo esc_html( trim( $c_icon . ' ' . $c_title ) ); ?></h3>
							<p class="brz-feature-text"><?php echo wp_kses_post( $c_text ); ?></p>
						</div>
					<?php endforeach; ?>
				</div>

				<!-- GALERIE FOTO SECTIUNEA 1: STADION -->
				<?php
				$default_stadion = array(
					array( 'url' => get_template_directory_uri() . '/images/stadion.jpg', 'title' => '🏟️ Arena & Gazonul Natural' ),
					array( 'url' => get_template_directory_uri() . '/images/stadion.jpg', 'title' => '🛡️ Gard Perimetral & Tribună (50 locuri)' ),
					array( 'url' => get_template_directory_uri() . '/images/stadion.jpg', 'title' => '🌱 Suprafața de Joc Liga 5' ),
				);
				brezoaele_render_section_gallery_slider( 'stadion', '📸 Galerie Foto – ' . $s1_title, $gal_stadion, $default_stadion );
				?>
			</div>

			<!-- SECTIUNEA 2: SALA DE SPORT -->
			<div class="brz-baza-section-block">
				<div class="brz-baza-section-header">
					<div class="brz-baza-section-icon" style="background: #e0f2fe; color: #0284c7;">🏢</div>
					<div>
						<h2 class="brz-baza-section-title"><?php echo esc_html( $s2_title ); ?></h2>
						<?php if ( ! empty( $s2_subtitle ) ) : ?>
							<span style="font-size: 0.85rem; color: #64748b; font-weight: 700;"><?php echo esc_html( $s2_subtitle ); ?></span>
						<?php endif; ?>
					</div>
				</div>

				<p class="brz-baza-text-p">
					<?php echo wp_kses_post( $s2_desc ); ?>
				</p>

				<div class="brz-feature-subgrid">
					<?php foreach ( $s2_cards_list as $card ) :
						$c_icon  = isset( $card['icon'] ) ? $card['icon'] : '';
						$c_title = isset( $card['title'] ) ? $card['title'] : '';
						$c_text  = isset( $card['text'] ) ? $card['text'] : '';
						?>
						<div class="brz-feature-card">
							<h3 class="brz-feature-title"><?php echo esc_html( trim( $c_icon . ' ' . $c_title ) ); ?></h3>
							<p class="brz-feature-text"><?php echo wp_kses_post( $c_text ); ?></p>
						</div>
					<?php endforeach; ?>
				</div>

				<!-- CALLOUT SALA DE SPORT -->
				<div class="brz-sala-callout">
					<div class="brz-sala-callout-text">
						<h4><?php echo esc_html( $s2_callout_title ); ?></h4>
						<p><?php echo esc_html( $s2_callout_text ); ?></p>
					</div>
					<a href="<?php echo esc_url( $s2_callout_btn_url ); ?>" class="brz-sala-btn">
						<?php echo esc_html( $s2_callout_btn_text ); ?>
					</a>
				</div>

				<!-- GALERIE FOTO SECTIUNEA 2: SALA DE SPORT -->
				<?php
				$default_sala = array(
					array( 'url' => get_template_directory_uri() . '/images/stadion.jpg', 'title' => '🏢 Interior Sala Multifuncțională' ),
					array( 'url' => get_template_directory_uri() . '/images/stadion.jpg', 'title' => '⚽ Suprafață Omologată Minifotbal & Handbal' ),
					array( 'url' => get_template_directory_uri() . '/images/stadion.jpg', 'title' => '🎓 Zona de Antrenamente & Educație Fizică' ),
				);
				brezoaele_render_section_gallery_slider( 'sala', '📸 Galerie Foto – ' . $s2_title, $gal_sala, $default_sala );
				?>
			</div>

			<!-- SECTIUNEA 3: PARCUL CENTRAL -->
			<div class="brz-baza-section-block">
				<div class="brz-baza-section-header">
					<div class="brz-baza-section-icon" style="background: #fef3c7; color: #d97706;">🌳</div>
					<div>
						<h2 class="brz-baza-section-title"><?php echo esc_html( $s3_title ); ?></h2>
						<?php if ( ! empty( $s3_subtitle ) ) : ?>
							<span style="font-size: 0.85rem; color: #64748b; font-weight: 700;"><?php echo esc_html( $s3_subtitle ); ?></span>
						<?php endif; ?>
					</div>
				</div>

				<p class="brz-baza-text-p">
					<?php echo wp_kses_post( $s3_desc ); ?>
				</p>

				<div class="brz-feature-subgrid">
					<?php foreach ( $s3_cards_list as $card ) :
						$c_icon  = isset( $card['icon'] ) ? $card['icon'] : '';
						$c_title = isset( $card['title'] ) ? $card['title'] : '';
						$c_text  = isset( $card['text'] ) ? $card['text'] : '';
						?>
						<div class="brz-feature-card">
							<h3 class="brz-feature-title"><?php echo esc_html( trim( $c_icon . ' ' . $c_title ) ); ?></h3>
							<p class="brz-feature-text"><?php echo wp_kses_post( $c_text ); ?></p>
						</div>
					<?php endforeach; ?>
				</div>

				<!-- GALERIE FOTO SECTIUNEA 3: PARCUL CENTRAL -->
				<?php
				$default_parc = array(
					array( 'url' => get_template_directory_uri() . '/images/stadion.jpg', 'title' => '🌳 Zone Verzi de Relaxare' ),
					array( 'url' => get_template_directory_uri() . '/images/stadion.jpg', 'title' => '🪑 Alei Pietonale & Bănci Umbroase' ),
					array( 'url' => get_template_directory_uri() . '/images/stadion.jpg', 'title' => '📹 Spațiu Supravegheat & Amenajat' ),
				);
				brezoaele_render_section_gallery_slider( 'parc', '📸 Galerie Foto – ' . $s3_title, $gal_parc, $default_parc );
				?>
			</div>

		</div>

		<!-- COLOANA DREAPTĂ: SIDEBAR INFORMATIV & ACCESS -->
		<div>

			<!-- CARD LOCALIZARE & CONTACT -->
			<div class="brz-baza-sidebar-box">
				<h3 class="brz-sidebar-title"><?php echo esc_html( $specs_title ); ?></h3>
				<ul class="brz-info-list">
					<?php foreach ( $tech_specs_list as $spec ) :
						$sp_label = isset( $spec['label'] ) ? $spec['label'] : '';
						$sp_val   = isset( $spec['val'] ) ? $spec['val'] : '';
						if ( empty( $sp_label ) && empty( $sp_val ) ) continue;
						?>
						<li>
							<span class="brz-info-label"><?php echo esc_html( $sp_label ); ?></span>
							<span class="brz-info-val"><?php echo esc_html( $sp_val ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>

			<!-- CARD ACCESIBILITATE & PROGRAM -->
			<div class="brz-baza-sidebar-box" style="background: linear-gradient(135deg, #047857 0%, #065f46 100%); color: #ffffff;">
				<h3 class="brz-sidebar-title" style="color: #ffffff; border-bottom-color: rgba(255,255,255,0.2);">
					<?php echo esc_html( $sb_prog_title ); ?>
				</h3>
				<p style="font-size: 0.88rem; color: #e2e8f0; line-height: 1.5; margin-bottom: 14px;">
					<?php echo wp_kses_post( $sb_prog_text ); ?>
				</p>
				<div style="background: rgba(255,255,255,0.1); padding: 12px; border-radius: 8px; font-size: 0.82rem; color: #a7f3d0; line-height: 1.4;">
					<?php echo wp_kses_post( $sb_prog_notice ); ?>
				</div>
			</div>
			</div>

			<!-- CARD NAVIGARE RAPIDĂ -->
			<div class="brz-baza-sidebar-box">
				<h3 class="brz-sidebar-title">🔗 Navigare Sport Brezoaele</h3>
				<a href="<?php echo esc_url( home_url( '/sport/' ) ); ?>" style="display: block; color: #047857; font-weight: 800; text-decoration: none; font-size: 0.9rem; margin-bottom: 10px;">
					🏆 Portal / Hub Sport Brezoaele &rarr;
				</a>
				<a href="<?php echo esc_url( home_url( '/c/sport/' ) ); ?>" style="display: block; color: #047857; font-weight: 800; text-decoration: none; font-size: 0.9rem; margin-bottom: 10px;">
					📰 Noutăți &amp; Cronici Sportive (/c/sport/) &rarr;
				</a>
				<a href="<?php echo esc_url( home_url( '/afaceri-locale/sala-de-sport-din-comuna-brezoaele/' ) ); ?>" style="display: block; color: #047857; font-weight: 800; text-decoration: none; font-size: 0.9rem;">
					🏢 Fișa Sălii de Sport Brezoaele &rarr;
				</a>
			</div>

		</div>

	</div>

</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
	const itemsPerPage = 4;

	// SLIDER & LAZY LOAD BATCHING
	document.querySelectorAll('.brz-section-gallery-wrapper').forEach(function(wrapper) {
		const galId = wrapper.dataset.galleryId;
		const totalPages = parseInt(wrapper.dataset.totalPages, 10) || 1;
		const rawJsonEl = wrapper.querySelector('.brz-gallery-raw-json');
		if (!rawJsonEl) return;

		let items = [];
		try {
			items = JSON.parse(rawJsonEl.textContent);
		} catch(e) {}

		let currentPage = 1;
		const grid = wrapper.querySelector('.brz-gallery-grid-slider');
		const prevBtn = wrapper.querySelector('.brz-gal-prev');
		const nextBtn = wrapper.querySelector('.brz-gal-next');
		const currentPgEl = wrapper.querySelector('.brz-current-page');

		function renderPage(page, animate) {
			if (page < 1) page = 1;
			if (page > totalPages) page = totalPages;
			currentPage = page;

			if (currentPgEl) currentPgEl.textContent = currentPage;
			if (prevBtn) prevBtn.disabled = (currentPage === 1);
			if (nextBtn) nextBtn.disabled = (currentPage === totalPages);

			if (animate && grid) {
				grid.style.opacity = '0';
				grid.style.transform = 'translateY(6px)';
			}

			setTimeout(function() {
				if (!grid) return;
				grid.innerHTML = '';

				const startIdx = (currentPage - 1) * itemsPerPage;
				const pageItems = items.slice(startIdx, startIdx + itemsPerPage);

				pageItems.forEach(function(imgItem, offsetIdx) {
					const globalIdx = startIdx + offsetIdx;
					const url = imgItem.url || '';
					const title = imgItem.title || '';

					const itemEl = document.createElement('div');
					itemEl.className = 'brz-gallery-item';
					itemEl.dataset.idx = globalIdx;
					itemEl.dataset.fullSrc = url;

					let captionHtml = title ? `<div class="brz-gallery-caption">${escapeHtml(title)}</div>` : '';
					itemEl.innerHTML = `<img src="${escapeHtml(url)}" alt="${escapeHtml(title)}" loading="lazy">${captionHtml}`;

					grid.appendChild(itemEl);
				});

				if (animate && grid) {
					grid.style.opacity = '1';
					grid.style.transform = 'translateY(0)';
					grid.scrollLeft = 0;
				}
			}, animate ? 150 : 0);
		}

		if (prevBtn) {
			prevBtn.addEventListener('click', function(e) {
				e.preventDefault();
				if (currentPage > 1) {
					renderPage(currentPage - 1, true);
				}
			});
		}

		if (nextBtn) {
			nextBtn.addEventListener('click', function(e) {
				e.preventDefault();
				if (currentPage < totalPages) {
					renderPage(currentPage + 1, true);
				}
			});
		}
	});

	function escapeHtml(str) {
		return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
	}
});
</script>

<?php
get_footer();
