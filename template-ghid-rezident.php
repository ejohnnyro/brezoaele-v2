<?php
/**
 * Template Name: Ghidul Rezidentului
 *
 * @package Brezoaele_V2
 */

get_header();
?>

<main id="primary" class="site-main" style="padding: 40px 0; background-color: var(--color-bg);">
	<div class="container" style="max-width: 800px;">
		
		<header class="page-header" style="margin-bottom: 30px; text-align: center;">
			<h1 class="page-title" style="font-size: 2.5rem; margin-bottom: 8px;">Ghidul Noului Rezident</h1>
			<p style="color: var(--color-text-muted);">Bine ai venit în Brezoaele! Am reunit aici toate informațiile practice de care ai nevoie pentru a te instala și a te integra rapid în comunitate.</p>
			<div style="width: 50px; height: 3px; background-color: var(--color-primary); margin: 12px auto 0 auto; border-radius: 3px;"></div>
		</header>

		<div class="accordion-group">
			
			<!-- Secțiunea 1: Utilități -->
			<details>
				<summary>🔌 Utilități și Conectare (Apă, Gaze, Electricitate)</summary>
				<div class="accordion-content">
					<p>Pentru racordarea la utilități sau rezolvarea avariilor, folosiți următoarele contacte:</p>
					
					<h4>💧 Apă și Canalizare</h4>
					<p>Operatorul local de apă este responsabil de rețele. Pentru avarii și branșamente noi, contactați biroul administrativ la <b>0245 XXXXXX</b> sau depuneți o cerere la sediu.</p>
					
					<h4>🔥 Gaz Natural</h4>
					<p>Comuna este parțial racordată la rețeaua de gaze. Pentru verificări de proiect sau extinderi de branșament, contactați operatorul zonal.</p>
					
					<h4>⚡ Energie Electrică</h4>
					<p>Distribuitorul regional este responsabil de rețeaua electrică. Deranjamente electrice: <b>0245 929</b> (Distribuție Energie Oltenia).</p>
				</div>
			</details>

			<!-- Secțiunea 2: Taxe și Impozite -->
			<details>
				<summary>⚖️ Taxe Locale și Impozite</summary>
				<div class="accordion-content">
					<p>Plata impozitelor pe clădiri, terenuri și auto se poate face:</p>
					<ul>
						<li>Fizic, la casieria Primăriei Brezoaele.</li>
						<li>Online, prin intermediul platformei naționale <b>Ghișeul.ro</b> (dacă este activat înregistrarea locală).</li>
					</ul>
					<p>Termenele de plată pentru a beneficia de bonificația de 10% sunt 31 martie și 30 septembrie ale fiecărui an.</p>
				</div>
			</details>

			<!-- Secțiunea 3: Educație și Școli -->
			<details>
				<summary>🏫 Educație (Grădinițe, Școli)</summary>
				<div class="accordion-content">
					<p>În comună funcționează unități de învățământ preșcolar și gimnazial:</p>
					
					<h4>📚 Școala Gimnazială Brezoaele</h4>
					<p>Oferă educație pentru clasele I-VIII. Dispune de săli de clasă modernizate, cabinet de informatică și sală de sport recent construită.</p>
					
					<h4>🧸 Grădinița cu Program Normal</h4>
					<p>Pentru copii de vârstă preșcolară, asigurând un program educativ adaptat.</p>
				</div>
			</details>

			<!-- Secțiunea 4: Sănătate -->
			<details>
				<summary>🏥 Sănătate și Farmacii</summary>
				<div class="accordion-content">
					<p>Pentru asistență medicală primară, în comună există:</p>
					<ul>
						<li><b>Dispensar Medical local:</b> Cabinete de medici de familie.</li>
						<li><b>Farmacia Bella Medifarm:</b> Situată în centrul comunei pentru eliberare de medicamente compensate și OTC.</li>
					</ul>
					<p>În caz de urgențe majore, sunați la <b>112</b> sau prezentați-vă la Spitalul Municipal Titu sau unitățile de primiri urgențe din Târgoviște/București.</p>
				</div>
			</details>

			<!-- Secțiunea 5: Gunoaie & Colectare Selectivă -->
			<details>
				<summary>♻️ Salubritate și Colectare Selectivă</summary>
				<div class="accordion-content">
					<p>Colectarea deșeurilor menajere se face săptămânal, conform graficului stabilit de operatorul de salubritate licențiat:</p>
					<ul>
						<li><b>Deșeuri menajere (fracție umedă):</b> Colectare săptămânală din pubelele individuale.</li>
						<li><b>Deșeuri reciclabile (plastic, hârtie, metal):</b> Colectare selectivă bilunară (conform sacilor speciali distribuiți gratuit).</li>
					</ul>
					<p>Este strict interzisă arderea deșeurilor vegetale sau abandonarea acestora pe domeniul public.</p>
				</div>
			</details>

		</div>
		
	</div>
</main>

<?php
get_footer();
