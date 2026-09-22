<?php
/**
 * Template Name: Pagină Publicitate & Pachete Promovare
 * Description: Prezentare pachete de publicitate pe categorii (Firme, Instituții Publice, Electoral) și solicitare Rate Card PDF.
 *
 * @package Brezoaele_V2
 */

get_header();
?>

<style>
/* Stiluri Pagină Publicitate */
.brz-pub-hero {
	background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
	color: #ffffff;
	padding: 50px 0;
	border-radius: var(--border-radius-lg);
	margin-bottom: 40px;
	box-shadow: var(--shadow-lg);
	position: relative;
	overflow: hidden;
}

.brz-pub-hero::before {
	content: "";
	position: absolute;
	top: -50%;
	right: -20%;
	width: 500px;
	height: 500px;
	background: radial-gradient(circle, rgba(4, 120, 87, 0.25) 0%, rgba(0, 0, 0, 0) 70%);
	border-radius: 50%;
	pointer-events: none;
}

.brz-pub-hero-inner {
	position: relative;
	z-index: 2;
	text-align: center;
	max-width: 800px;
	margin: 0 auto;
	padding: 0 20px;
}

.brz-pub-hero-badge {
	display: inline-block;
	background: rgba(4, 120, 87, 0.2);
	color: #6ee7b7;
	border: 1px solid #047857;
	font-weight: 800;
	font-size: 0.8rem;
	padding: 4px 14px;
	border-radius: 30px;
	text-transform: uppercase;
	letter-spacing: 1px;
	margin-bottom: 16px;
}

.brz-pub-hero-title {
	font-size: 2.5rem;
	font-weight: 900;
	color: #ffffff;
	margin-bottom: 14px;
	line-height: 1.2;
}

.brz-pub-hero-title span {
	color: #34d399;
}

.brz-pub-hero-desc {
	font-size: 1.1rem;
	color: #94a3b8;
	line-height: 1.6;
	margin-bottom: 30px;
}

.brz-pub-stats-grid {
	display: grid;
	grid-template-columns: repeat(3, 1fr);
	gap: 20px;
	margin-top: 30px;
}

.brz-pub-stat-card {
	background: rgba(255, 255, 255, 0.05);
	border: 1px solid rgba(255, 255, 255, 0.1);
	border-radius: var(--border-radius-md);
	padding: 16px;
	backdrop-filter: blur(5px);
}

.brz-pub-stat-val {
	font-size: 1.8rem;
	font-weight: 900;
	color: #34d399;
	display: block;
}

.brz-pub-stat-lbl {
	font-size: 0.85rem;
	color: #cbd5e1;
	font-weight: 600;
}

/* Navigare Categorii Tab-uri */
.brz-pub-tabs-wrapper {
	display: flex;
	justify-content: center;
	gap: 12px;
	margin-bottom: 40px;
	flex-wrap: wrap;
}

.brz-pub-tab-btn {
	background: #ffffff;
	border: 2px solid var(--color-border);
	color: var(--color-text-dark);
	padding: 14px 24px;
	border-radius: var(--border-radius-md);
	font-weight: 800;
	font-size: 1rem;
	cursor: pointer;
	transition: all 0.2s ease;
	display: flex;
	align-items: center;
	gap: 10px;
	box-shadow: var(--shadow-sm);
}

.brz-pub-tab-btn:hover {
	border-color: var(--color-primary);
	color: var(--color-primary);
}

.brz-pub-tab-btn.active {
	background: var(--color-primary);
	border-color: var(--color-primary);
	color: #ffffff;
	box-shadow: var(--shadow-md);
}

/* Structură Grilă Pachete */
.brz-pub-packages-grid {
	display: grid;
	grid-template-columns: repeat(3, 1fr);
	gap: 24px;
	margin-bottom: 50px;
}

.brz-pub-pkg-card {
	background: #ffffff;
	border: 1px solid var(--color-border);
	border-radius: var(--border-radius-lg);
	padding: 30px 24px;
	box-shadow: var(--shadow-md);
	display: flex;
	flex-direction: column;
	justify-content: space-between;
	position: relative;
	transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.brz-pub-pkg-card:hover {
	transform: translateY(-4px);
	box-shadow: var(--shadow-lg);
}

.brz-pub-pkg-card.featured {
	border: 2px solid var(--color-primary);
	background: linear-gradient(180deg, #f0fdf4 0%, #ffffff 100px);
}

.brz-pub-pkg-badge {
	position: absolute;
	top: -12px;
	right: 20px;
	background: var(--color-primary);
	color: #ffffff;
	font-size: 0.75rem;
	font-weight: 900;
	padding: 4px 12px;
	border-radius: 20px;
	text-transform: uppercase;
	letter-spacing: 0.5px;
}

.brz-pub-pkg-title {
	font-size: 1.5rem;
	font-weight: 900;
	color: var(--color-text-dark);
	margin-bottom: 6px;
}

.brz-pub-pkg-subtitle {
	font-size: 0.88rem;
	color: var(--color-text-muted);
	margin-bottom: 20px;
	line-height: 1.4;
}

.brz-pub-pkg-list {
	list-style: none;
	padding: 0;
	margin: 0 0 24px;
	display: flex;
	flex-direction: column;
	gap: 12px;
}

.brz-pub-pkg-list li {
	font-size: 0.92rem;
	color: #334155;
	line-height: 1.45;
	display: flex;
	align-items: flex-start;
	gap: 8px;
}

.brz-pub-pkg-list li::before {
	content: "✓";
	color: var(--color-primary);
	font-weight: 900;
	font-size: 1rem;
	flex-shrink: 0;
}

.brz-pub-pkg-btn {
	width: 100%;
	text-align: center;
	padding: 14px;
	font-weight: 800;
	font-size: 0.9rem;
	border-radius: var(--border-radius-md);
	cursor: pointer;
	border: none;
	transition: all 0.2s ease;
}

/* Responsive Rules */
@media (max-width: 900px) {
	.brz-pub-stats-grid {
		grid-template-columns: 1fr;
	}
	.brz-pub-packages-grid {
		grid-template-columns: 1fr;
	}
	.brz-pub-hero-title {
		font-size: 1.8rem;
	}
}
</style>

<main id="primary" class="site-main" style="padding: 40px 0; background-color: var(--color-bg);">
	<div class="container">

		<!-- HERO BANNER PROMOVARE -->
		<section class="brz-pub-hero">
			<div class="brz-pub-hero-inner">
				<span class="brz-pub-hero-badge">📢 Promovare &amp; Publicitate Online</span>
				<h1 class="brz-pub-hero-title">
					Promovează-ți brandul în <span>Dâmbovița și la nivel național</span>
				</h1>
				<p class="brz-pub-hero-desc">
					Conectează-ți afacerea, instituția sau campania cu zeci de mii de cititori activi, decidenți locali și public din județul Dâmbovița și din întreaga țară.
				</p>

				<div class="brz-pub-stats-grid">
					<div class="brz-pub-stat-card">
						<span class="brz-pub-stat-val">50.000+</span>
						<span class="brz-pub-stat-lbl">Afișări &amp; Cititori Lunari</span>
					</div>
					<div class="brz-pub-stat-card">
						<span class="brz-pub-stat-val">Dâmbovița</span>
						<span class="brz-pub-stat-lbl">Arie de Acoperire &amp; Național</span>
					</div>
					<div class="brz-pub-stat-card">
						<span class="brz-pub-stat-val">Impact Direct</span>
						<span class="brz-pub-stat-lbl">Audiență Conectată &amp; Decidenți</span>
					</div>
				</div>
			</div>
		</section>

		<!-- SELECTOR TAB-URI CATEGORII PACHETE -->
		<div class="brz-pub-tabs-wrapper">
			<button type="button" class="brz-pub-tab-btn active" data-category="firme">
				💼 Firme &amp; Antreprenori
			</button>
			<button type="button" class="brz-pub-tab-btn" data-category="institutii">
				🏛️ Instituții Publice &amp; ONG
			</button>
			<button type="button" class="brz-pub-tab-btn" data-category="electoral">
				🗳️ Promovare Electorală / Partide
			</button>
		</div>

		<!-- CONTAINER PACHETE CATEGORIA A: FIRME & ANTREPRENORI -->
		<div id="cat-firme" class="brz-pub-category-content">
			<div class="brz-pub-packages-grid">
				
				<!-- BASIC FIRME -->
				<div class="brz-pub-pkg-card">
					<div>
						<h3 class="brz-pub-pkg-title">Basic Firme</h3>
						<p class="brz-pub-pkg-subtitle">Ideal pentru legumicultori, magazine locale și meșteșugari.</p>
						<ul class="brz-pub-pkg-list">
							<li>Listing Verificat pe <strong>Harta Satelit &amp; Ghidul Afacerilor</strong>.</li>
							<li>Galerie Foto (5 imagini) + Date Contact &amp; Program.</li>
							<li>Banner Sidebar Dreapta (300 x 250 px) rotativ în știri.</li>
							<li>Anunț Premium în secțiunea Anunțuri.</li>
						</ul>
					</div>
					<a href="<?php echo esc_url( home_url( '/ratecard-firme/?package=' . urlencode( 'Basic Firme' ) ) ); ?>" class="brz-pub-pkg-btn btn btn-primary" style="text-decoration: none;">
						👁️ Vezi Rate Card
					</a>
				</div>

				<!-- STANDARD FIRME (POPULAR) -->
				<div class="brz-pub-pkg-card featured">
					<span class="brz-pub-pkg-badge">POPULAR</span>
					<div>
						<h3 class="brz-pub-pkg-title">Standard Firme</h3>
						<p class="brz-pub-pkg-subtitle">Recomandat companiilor mijlocii și furnizorilor de servicii.</p>
						<ul class="brz-pub-pkg-list">
							<li>Tot ce include pachetul <strong>Basic Firme</strong>.</li>
							<li>Banner Header Principal (728 x 90 / 320 x 100) pe tot site-ul.</li>
							<li>Publicare articol advertorial (redactat de noi sau publicarea unui articol deja redactat de client).</li>
							<li>Ecuson vizual de identificare: „Partener Recomandat Brezoaele”.</li>
						</ul>
					</div>
					<a href="<?php echo esc_url( home_url( '/ratecard-firme/?package=' . urlencode( 'Standard Firme' ) ) ); ?>" class="brz-pub-pkg-btn btn btn-primary" style="text-decoration: none;">
						👁️ Vezi Rate Card
					</a>
				</div>

				<!-- PREMIUM FIRME -->
				<div class="brz-pub-pkg-card">
					<div>
						<h3 class="brz-pub-pkg-title">Premium Firme</h3>
						<p class="brz-pub-pkg-subtitle">Vizibilitate maximă și brand dominance în întreaga comună.</p>
						<ul class="brz-pub-pkg-list">
							<li>Tot ce include pachetul <strong>Standard Firme</strong>.</li>
							<li>Banner Sticky Sidebar Fix (300 x 600 px) în articole.</li>
							<li>Banner Billboard Prima Pagină (970 x 250 px).</li>
							<li>Inserție Dedicată în Newsletter-ul Săptămânal al Comunei.</li>
							<li>2 Advertoriale de Prezentare pe lună.</li>
						</ul>
					</div>
					<a href="<?php echo esc_url( home_url( '/ratecard-firme/?package=' . urlencode( 'Premium Firme' ) ) ); ?>" class="brz-pub-pkg-btn btn btn-primary" style="text-decoration: none;">
						👁️ Vezi Rate Card
					</a>
				</div>

			</div>
		</div>

		<!-- CONTAINER PACHETE CATEGORIA B: INSTITUȚII PUBLICE & ONG -->
		<div id="cat-institutii" class="brz-pub-category-content" style="display: none;">
			<div class="brz-pub-packages-grid">
				
				<!-- BASIC INSTITUȚII -->
				<div class="brz-pub-pkg-card">
					<div>
						<h3 class="brz-pub-pkg-title">Basic Instituții</h3>
						<p class="brz-pub-pkg-subtitle">Informații publice, anunturi oficiale și transparență locală.</p>
						<ul class="brz-pub-pkg-list">
							<li>Publicare Anunțuri Oficiale, Hotărâri Consiliu &amp; Înștiințări.</li>
							<li>Pagină Dedicată de Prezentare Instituție cu program audiențe.</li>
							<li>Notificare scurtă în Newsletter-ul local.</li>
						</ul>
					</div>
					<a href="<?php echo esc_url( home_url( '/ratecard-institutii/?package=' . urlencode( 'Basic Instituții' ) ) ); ?>" class="brz-pub-pkg-btn btn btn-primary" style="text-decoration: none;">
						👁️ Vezi Rate Card
					</a>
				</div>

				<!-- STANDARD INSTITUȚII -->
				<div class="brz-pub-pkg-card featured">
					<span class="brz-pub-pkg-badge">RECOMANDAT</span>
					<div>
						<h3 class="brz-pub-pkg-title">Standard Instituții</h3>
						<p class="brz-pub-pkg-subtitle">Comunicare publică activă și campanii de interes local.</p>
						<ul class="brz-pub-pkg-list">
							<li>Tot ce include pachetul <strong>Basic Instituții</strong>.</li>
							<li>Publicare Rapoarte Activitate &amp; Comunicate în Prima Pagină.</li>
							<li>Banner Header sau In-Article pentru campanii publice (utilități, dezinsecție, școală).</li>
							<li>2 Comunicate de Presă lunare trimise prin newsletter.</li>
						</ul>
					</div>
					<a href="<?php echo esc_url( home_url( '/ratecard-institutii/?package=' . urlencode( 'Standard Instituții' ) ) ); ?>" class="brz-pub-pkg-btn btn btn-primary" style="text-decoration: none;">
						👁️ Vezi Rate Card
					</a>
				</div>

				<!-- PREMIUM INSTITUȚII -->
				<div class="brz-pub-pkg-card">
					<div>
						<h3 class="brz-pub-pkg-title">Premium Instituții</h3>
						<p class="brz-pub-pkg-subtitle">Consultare cetățenească directă și transparență totală.</p>
						<ul class="brz-pub-pkg-list">
							<li>Tot ce include pachetul <strong>Standard Instituții</strong>.</li>
							<li>Secțiune Dedicată de <strong>Dezbateri &amp; Sondaje Cetățenești</strong> pe Forum.</li>
							<li>Banner Sticky Permanent de Informare Publică.</li>
							<li>Raport lunar detaliat cu numărul de cetățeni informați.</li>
						</ul>
					</div>
					<a href="<?php echo esc_url( home_url( '/ratecard-institutii/?package=' . urlencode( 'Premium Instituții' ) ) ); ?>" class="brz-pub-pkg-btn btn btn-primary" style="text-decoration: none;">
						👁️ Vezi Rate Card
					</a>
				</div>

			</div>
		</div>

		<!-- CONTAINER PACHETE CATEGORIA C: PROMOVARE ELECTORALĂ -->
		<div id="cat-electoral" class="brz-pub-category-content" style="display: none;">
			<div class="brz-pub-packages-grid">
				
				<!-- BASIC ELECTORAL -->
				<div class="brz-pub-pkg-card">
					<div>
						<h3 class="brz-pub-pkg-title">Basic Electoral</h3>
						<p class="brz-pub-pkg-subtitle">Prezență electorală de bază conform normelor AEP.</p>
						<ul class="brz-pub-pkg-list">
							<li>1 Advertorial / Comunicat Electoral („Promovare Politică”).</li>
							<li>Banner Medium Rectangle (300 x 250 px) în sidebar-ul de știri.</li>
							<li>Profil Candidat / Formațiune Politică.</li>
						</ul>
					</div>
					<a href="<?php echo esc_url( home_url( '/ratecard-electoral/?package=' . urlencode( 'Basic Electoral' ) ) ); ?>" class="brz-pub-pkg-btn btn btn-primary" style="text-decoration: none;">
						👁️ Vezi Rate Card
					</a>
				</div>

				<!-- STANDARD ELECTORAL -->
				<div class="brz-pub-pkg-card featured">
					<span class="brz-pub-pkg-badge">IMPACT LOCAL</span>
					<div>
						<h3 class="brz-pub-pkg-title">Standard Electoral</h3>
						<p class="brz-pub-pkg-subtitle">Acoperire puternică în întreaga comună și județ.</p>
						<ul class="brz-pub-pkg-list">
							<li>Tot ce include pachetul <strong>Basic Electoral</strong>.</li>
							<li>2 Advertoriale / Programe Politice de Prezentare.</li>
							<li>Banner Header Global (728 x 90 / 320 x 100) cu vizibilitate 100%.</li>
							<li>Inserție electorală dedicată în Newsletter-ul Comunei.</li>
						</ul>
					</div>
					<a href="<?php echo esc_url( home_url( '/ratecard-electoral/?package=' . urlencode( 'Standard Electoral' ) ) ); ?>" class="brz-pub-pkg-btn btn btn-primary" style="text-decoration: none;">
						👁️ Vezi Rate Card
					</a>
				</div>

				<!-- PREMIUM ELECTORAL -->
				<div class="brz-pub-pkg-card">
					<div>
						<h3 class="brz-pub-pkg-title">Premium Electoral</h3>
						<p class="brz-pub-pkg-subtitle">Dominanță electorală totală și promovare multimedia.</p>
						<ul class="brz-pub-pkg-list">
							<li>Tot ce include pachetul <strong>Standard Electoral</strong>.</li>
							<li>Banner Billboard Prima Pagină (970 x 250) + Banner Sticky (300 x 600).</li>
							<li>Banner In-Article în toate articolele din comună.</li>
							<li>Integrare Material Video de Prezentare Furnizat de Client.</li>
						</ul>
					</div>
					<a href="<?php echo esc_url( home_url( '/ratecard-electoral/?package=' . urlencode( 'Premium Electoral' ) ) ); ?>" class="brz-pub-pkg-btn btn btn-primary" style="text-decoration: none;">
						👁️ Vezi Rate Card
					</a>
				</div>

			</div>
		</div>

	</div>
</main>

<script>
document.addEventListener("DOMContentLoaded", function() {
	// Comutare Tab-uri Categorii
	const tabBtns = document.querySelectorAll(".brz-pub-tab-btn");
	const catContents = {
		'firme': document.getElementById('cat-firme'),
		'institutii': document.getElementById('cat-institutii'),
		'electoral': document.getElementById('cat-electoral')
	};

	tabBtns.forEach(btn => {
		btn.addEventListener('click', function() {
			tabBtns.forEach(b => b.classList.remove('active'));
			this.classList.add('active');
			const catKey = this.getAttribute('data-category');

			Object.keys(catContents).forEach(key => {
				if (catContents[key]) {
					catContents[key].style.display = (key === catKey) ? 'block' : 'none';
				}
			});
		});
	});
});
</script>

<?php
get_footer();

