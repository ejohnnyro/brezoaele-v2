<?php
/**
 * Template Name: Rate Card - Firme & Companii
 * Description: Template A4 Printable & Web pentru Oferta de Publicitate Firme.
 *
 * @package Brezoaele_V2
 */

get_header();
?>

<style>
/* CSS Rate Card Document Premium */
.brz-rc-wrapper {
	max-width: 900px;
	margin: 30px auto 60px;
	background: #ffffff;
	border: 1px solid #e2e8f0;
	border-radius: 12px;
	box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
	overflow: hidden;
	font-family: 'Inter', system-ui, -apple-system, sans-serif;
	color: #1e293b;
}

/* Action Bar */
.brz-rc-action-bar {
	background: #0f172a;
	padding: 14px 24px;
	display: flex;
	justify-content: space-between;
	align-items: center;
	color: #ffffff;
	border-bottom: 1px solid #334155;
}

.brz-rc-action-btn {
	background: #047857;
	color: #ffffff;
	border: none;
	padding: 10px 20px;
	border-radius: 6px;
	font-weight: 800;
	font-size: 0.9rem;
	cursor: pointer;
	display: inline-flex;
	align-items: center;
	gap: 8px;
	text-decoration: none;
	transition: background 0.2s ease;
}

.brz-rc-action-btn:hover {
	background: #059669;
	color: #ffffff;
}

/* Document Body */
.brz-rc-doc {
	padding: 40px;
}

.brz-rc-header {
	display: flex;
	justify-content: space-between;
	align-items: flex-start;
	border-bottom: 2px solid #047857;
	padding-bottom: 20px;
	margin-bottom: 30px;
}

.brz-rc-brand-name {
	font-size: 1.8rem;
	font-weight: 900;
	color: #0f172a;
	letter-spacing: -0.5px;
}

.brz-rc-brand-name span {
	color: #047857;
}

.brz-rc-doc-title {
	font-size: 1.3rem;
	font-weight: 800;
	color: #047857;
	margin-top: 4px;
	text-transform: uppercase;
	letter-spacing: 0.5px;
}

.brz-rc-doc-meta {
	text-align: right;
	font-size: 0.85rem;
	color: #64748b;
	line-height: 1.5;
}

/* Highlights Grid */
.brz-rc-metrics {
	display: grid;
	grid-template-columns: repeat(3, 1fr);
	gap: 16px;
	margin-bottom: 30px;
}

.brz-rc-metric-box {
	background: #f8fafc;
	border: 1px solid #e2e8f0;
	border-radius: 8px;
	padding: 16px;
	text-align: center;
}

.brz-rc-metric-num {
	font-size: 1.6rem;
	font-weight: 900;
	color: #047857;
	display: block;
}

.brz-rc-metric-lbl {
	font-size: 0.8rem;
	font-weight: 700;
	color: #475569;
	text-transform: uppercase;
	letter-spacing: 0.5px;
}

/* Tab Switcher Mobil */
.brz-rc-mobile-tabs {
	display: none;
	justify-content: center;
	gap: 8px;
	margin-bottom: 16px;
}

.brz-rc-mobile-tab-btn {
	background: #ffffff;
	border: 1px solid #cbd5e1;
	color: #0f172a;
	padding: 8px 14px;
	border-radius: 6px;
	font-weight: 700;
	font-size: 0.82rem;
	cursor: pointer;
}

.brz-rc-mobile-tab-btn.active {
	background: #047857;
	color: #ffffff;
	border-color: #047857;
}

/* Packages Grid Table */
.brz-rc-table-wrapper {
	margin-bottom: 30px;
}

.brz-rc-table {
	width: 100%;
	border-collapse: collapse;
}

.brz-rc-table th {
	background: #0f172a;
	color: #ffffff;
	padding: 14px;
	text-align: left;
	font-size: 0.9rem;
	font-weight: 800;
	text-transform: uppercase;
}

.brz-rc-table th.featured-col {
	background: #047857;
}

.brz-rc-table td {
	padding: 14px;
	border-bottom: 1px solid #e2e8f0;
	font-size: 0.9rem;
}

.brz-rc-table tr:nth-child(even) {
	background: #f8fafc;
}

.brz-rc-check {
	color: #047857;
	font-weight: 900;
	font-size: 1.1rem;
}

/* Banner Technical Specs */
.brz-rc-specs-box {
	background: #f1f5f9;
	border-radius: 8px;
	padding: 20px;
	margin-bottom: 30px;
}

.brz-rc-specs-title {
	font-size: 1.1rem;
	font-weight: 800;
	color: #0f172a;
	margin-bottom: 12px;
}

.brz-rc-specs-grid {
	display: grid;
	grid-template-columns: repeat(3, 1fr);
	gap: 12px;
	font-size: 0.85rem;
}

.brz-rc-spec-item {
	background: #ffffff;
	padding: 10px;
	border-radius: 6px;
	border: 1px solid #cbd5e1;
}

.brz-rc-spec-item strong {
	color: #047857;
	display: block;
}

/* Footer Terms */
.brz-rc-footer {
	border-top: 1px solid #e2e8f0;
	padding-top: 20px;
	font-size: 0.8rem;
	color: #64748b;
	line-height: 1.5;
	display: flex;
	justify-content: space-between;
	align-items: center;
}

/* Mobile Responsive Rules (Comutator Tab-uri) */
@media (max-width: 768px) {
	.brz-rc-wrapper {
		margin: 10px;
		border-radius: 8px;
	}
	.brz-rc-doc {
		padding: 16px;
	}
	.brz-rc-action-bar {
		flex-direction: column;
		gap: 10px;
		text-align: center;
	}
	.brz-rc-header {
		flex-direction: column;
		gap: 12px;
	}
	.brz-rc-doc-meta {
		text-align: left;
	}
	.brz-rc-metrics {
		grid-template-columns: 1fr;
	}
	.brz-rc-specs-grid {
		grid-template-columns: 1fr;
	}
	.brz-rc-footer {
		flex-direction: column;
		gap: 10px;
		text-align: center;
	}
	.brz-rc-mobile-tabs {
		display: flex;
	}
	.brz-rc-table th, .brz-rc-table td {
		padding: 10px 8px;
		font-size: 0.82rem;
	}
	.brz-rc-table th:nth-child(2), .brz-rc-table td:nth-child(2),
	.brz-rc-table th:nth-child(4), .brz-rc-table td:nth-child(4) {
		display: none;
	}
	.brz-rc-table.show-col-2 th:nth-child(2), .brz-rc-table.show-col-2 td:nth-child(2) {
		display: table-cell;
	}
	.brz-rc-table.show-col-2 th:nth-child(3), .brz-rc-table.show-col-2 td:nth-child(3),
	.brz-rc-table.show-col-2 th:nth-child(4), .brz-rc-table.show-col-2 td:nth-child(4) {
		display: none;
	}
	.brz-rc-table.show-col-3 th:nth-child(3), .brz-rc-table.show-col-3 td:nth-child(3) {
		display: table-cell;
	}
	.brz-rc-table.show-col-3 th:nth-child(2), .brz-rc-table.show-col-3 td:nth-child(2),
	.brz-rc-table.show-col-3 th:nth-child(4), .brz-rc-table.show-col-3 td:nth-child(4) {
		display: none;
	}
	.brz-rc-table.show-col-4 th:nth-child(4), .brz-rc-table.show-col-4 td:nth-child(4) {
		display: table-cell;
	}
	.brz-rc-table.show-col-4 th:nth-child(2), .brz-rc-table.show-col-4 td:nth-child(2),
	.brz-rc-table.show-col-4 th:nth-child(3), .brz-rc-table.show-col-4 td:nth-child(3) {
		display: none;
	}
}

/* @media print Rules for Exporting Clean A4 PDF */
@media print {
	header, footer, .main-navigation, .site-header, .site-footer, .brz-rc-action-bar, #wpadminbar, .brz-rc-mobile-tabs, .brz-rc-form-box {
		display: none !important;
	}
	body {
		background: #ffffff !important;
		margin: 0 !important;
		padding: 0 !important;
	}
	.brz-rc-wrapper {
		border: none !important;
		box-shadow: none !important;
		max-width: 100% !important;
		margin: 0 !important;
		border-radius: 0 !important;
	}
	.brz-rc-doc {
		padding: 20px !important;
	}
	.brz-rc-table th, .brz-rc-table td {
		display: table-cell !important;
	}
}
</style>

<main id="primary" class="site-main" style="padding: 20px 0; background: #f1f5f9;">
	<div class="brz-rc-wrapper">
		
		<!-- ACTION BAR -->
		<div class="brz-rc-action-bar">
			<div style="font-weight: 700; font-size: 0.95rem;">
				📄 Rate Card Oficial — <strong>Firme &amp; Antreprenori</strong>
			</div>
			<div style="display: flex; gap: 10px;">
				<a href="<?php echo esc_url( home_url( '/publicitate/' ) ); ?>" class="brz-rc-action-btn" style="background: #334155;">
					&larr; Înapoi la Pachete
				</a>
			</div>
		</div>

		<!-- DOCUMENT RATE CARD -->
		<div class="brz-rc-doc">
			
			<!-- HEADER DOCUMENT -->
			<div class="brz-rc-header">
				<div>
					<div class="brz-rc-brand-name">Brezoaele<span>.ro</span></div>
					<div class="brz-rc-doc-title">Oferta Servicii Publicitate — Firme &amp; Companii</div>
				</div>
				<div class="brz-rc-doc-meta">
					<strong>Cod Document:</strong> BRZ-RC-FIRME-2026<br>
					<strong>Valabilitate:</strong> Anul 2026<br>
					<strong>Contact:</strong> contact@brezoaele.ro
				</div>
			</div>

			<!-- METRICS HIGHLIGHTS -->
			<div class="brz-rc-metrics">
				<div class="brz-rc-metric-box">
					<span class="brz-rc-metric-num">50.000+</span>
					<span class="brz-rc-metric-lbl">Afișări Lunare Garante</span>
				</div>
				<div class="brz-rc-metric-box">
					<span class="brz-rc-metric-num">Dâmbovița</span>
					<span class="brz-rc-metric-lbl">Acoperire Județeană &amp; Națională</span>
				</div>
				<div class="brz-rc-metric-box">
					<span class="brz-rc-metric-num">100% FAIR</span>
					<span class="brz-rc-metric-lbl">Rotire Egală Impresii AdServer</span>
				</div>
			</div>

			<!-- COMUTATOR TAB-URI PE MOBIL -->
			<div class="brz-rc-mobile-tabs">
				<button type="button" class="brz-rc-mobile-tab-btn" data-col="2">BASIC (149 LEI)</button>
				<button type="button" class="brz-rc-mobile-tab-btn active" data-col="3">STANDARD ⭐ (299 LEI)</button>
				<button type="button" class="brz-rc-mobile-tab-btn" data-col="4">PREMIUM (599 LEI)</button>
			</div>

			<!-- TABEL COMPARATIV PACHETE -->
			<div class="brz-rc-table-wrapper">
				<table class="brz-rc-table show-col-3">
					<thead>
						<tr>
							<th style="width: 37%;">Serviciu / Beneficiu Inclus</th>
							<th style="width: 21%; text-align: center;">
								BASIC<br>
								<div style="font-size: 1rem; color: #10b981; font-weight: 900; margin-top: 4px;">149 LEI<span style="font-size: 0.75rem; font-weight: 600; color: #94a3b8;">/lună</span></div>
								<div style="font-size: 0.72rem; color: #94a3b8; font-weight: 600; text-transform: none;">1.490 lei / an</div>
							</th>
							<th style="width: 21%; text-align: center;" class="featured-col">
								STANDARD ⭐<br>
								<div style="font-size: 1.05rem; color: #ffffff; font-weight: 900; margin-top: 4px;">299 LEI<span style="font-size: 0.75rem; font-weight: 600; color: #a7f3d0;">/lună</span></div>
								<div style="font-size: 0.72rem; color: #a7f3d0; font-weight: 600; text-transform: none;">2.990 lei / an</div>
							</th>
							<th style="width: 21%; text-align: center;">
								PREMIUM<br>
								<div style="font-size: 1rem; color: #10b981; font-weight: 900; margin-top: 4px;">599 LEI<span style="font-size: 0.75rem; font-weight: 600; color: #94a3b8;">/lună</span></div>
								<div style="font-size: 0.72rem; color: #94a3b8; font-weight: 600; text-transform: none;">5.990 lei / an</div>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>Listing Verificat pe Harta Satelit &amp; Ghidul Afacerilor</td>
							<td style="text-align: center;"><span class="brz-rc-check">✓</span></td>
							<td style="text-align: center;"><span class="brz-rc-check">✓</span></td>
							<td style="text-align: center;"><span class="brz-rc-check">✓</span></td>
						</tr>
						<tr>
							<td>Galerie Foto (5 imagini) + Date Contact &amp; Program</td>
							<td style="text-align: center;"><span class="brz-rc-check">✓</span></td>
							<td style="text-align: center;"><span class="brz-rc-check">✓</span></td>
							<td style="text-align: center;"><span class="brz-rc-check">✓</span></td>
						</tr>
						<tr>
							<td>Banner Sidebar Dreapta (300 x 250 px) rotativ în știri</td>
							<td style="text-align: center;"><span class="brz-rc-check">✓</span></td>
							<td style="text-align: center;"><span class="brz-rc-check">✓</span></td>
							<td style="text-align: center;"><span class="brz-rc-check">✓</span></td>
						</tr>
						<tr>
							<td>Anunț Premium în secțiunea Anunțuri</td>
							<td style="text-align: center;">1 Anunț</td>
							<td style="text-align: center;">3 Anunțuri</td>
							<td style="text-align: center;">Nelimitat</td>
						</tr>
						<tr>
							<td>Banner Header Global Principal (728 x 90 / 320 x 100)</td>
							<td style="text-align: center; color: #94a3b8;">—</td>
							<td style="text-align: center;"><span class="brz-rc-check">✓</span></td>
							<td style="text-align: center;"><span class="brz-rc-check">✓</span></td>
						</tr>
						<tr>
							<td>Publicare articol advertorial (redactat de noi sau furnizat de client)</td>
							<td style="text-align: center; color: #94a3b8;">—</td>
							<td style="text-align: center;">1 Advertorial</td>
							<td style="text-align: center;">2 Advertoriale/lună</td>
						</tr>
						<tr>
							<td>Ecuson „Partener Recomandat Brezoaele”</td>
							<td style="text-align: center; color: #94a3b8;">—</td>
							<td style="text-align: center;"><span class="brz-rc-check">✓</span></td>
							<td style="text-align: center;"><span class="brz-rc-check">✓</span></td>
						</tr>
						<tr>
							<td>Banner Sticky Sidebar Fix (300 x 600 px) la scroll</td>
							<td style="text-align: center; color: #94a3b8;">—</td>
							<td style="text-align: center; color: #94a3b8;">—</td>
							<td style="text-align: center;"><span class="brz-rc-check">✓</span></td>
						</tr>
						<tr>
							<td>Banner Billboard Prima Pagină Feed (970 x 250 px)</td>
							<td style="text-align: center; color: #94a3b8;">—</td>
							<td style="text-align: center; color: #94a3b8;">—</td>
							<td style="text-align: center;"><span class="brz-rc-check">✓</span></td>
						</tr>
						<tr>
							<td>Inserție Dedicată în Newsletter-ul Săptămânal</td>
							<td style="text-align: center; color: #94a3b8;">—</td>
							<td style="text-align: center; color: #94a3b8;">—</td>
							<td style="text-align: center;"><span class="brz-rc-check">✓</span></td>
						</tr>
					</tbody>
				</table>
			</div>

			<!-- SPECIFICAȚII TEHNICE BANNERE -->
			<div class="brz-rc-specs-box">
				<div class="brz-rc-specs-title">📐 Specificații Tehnice Bannere Publicitare</div>
				<div class="brz-rc-specs-grid">
					<div class="brz-rc-spec-item">
						<strong>Header Global:</strong>
						728 x 90 px (Desktop)<br>320 x 100 px (Mobil)
					</div>
					<div class="brz-rc-spec-item">
						<strong>Sidebar Dreapta:</strong>
						300 x 250 px (Medium Rectangle)<br>Format JPG / PNG / GIF
					</div>
					<div class="brz-rc-spec-item">
						<strong>Sidebar Sticky:</strong>
						300 x 600 px (Half-Page Fix)<br>Rămâne vizibil la scroll
					</div>
					<div class="brz-rc-spec-item">
						<strong>În-Articol Ad:</strong>
						728 x 90 px sau 300 x 250 px<br>Plasat după paragraful 3
					</div>
					<div class="brz-rc-spec-item">
						<strong>Billboard Feed:</strong>
						970 x 250 px (Prima Pagină)<br>Dimensiune mare de impact
					</div>
					<div class="brz-rc-spec-item">
						<strong>Coduri Acceptate:</strong>
						Imagine + Link (nofollow/target)<br>sau Cod HTML5 / AdSense
					</div>
				</div>
			</div>

			<!-- SECȚIUNE FORMULAR DEDICAT COMANDĂ / SOLICITARE RECLAMĂ -->
			<div id="brz-rc-order-form-box" class="brz-rc-form-box" style="background: #ffffff; border: 2px solid #047857; border-radius: 12px; padding: 30px; margin-top: 30px; box-shadow: 0 4px 20px rgba(4, 120, 87, 0.08);">
				<div style="text-align: center; margin-bottom: 24px;">
					<span style="background: #dcfce7; color: #15803d; font-size: 0.78rem; font-weight: 800; padding: 4px 14px; border-radius: 30px; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block; margin-bottom: 8px;">
						📩 Solicitare Ofertă &amp; Recomandări Publicitate
					</span>
					<h3 style="font-size: 1.5rem; font-weight: 900; color: #0f172a; margin: 0 0 6px 0;">
						Comandă Pachetul Dorit sau Solicită Ofertă Personalizată
					</h3>
					<p style="color: #64748b; font-size: 0.9rem; margin: 0; max-width: 650px; margin: 0 auto; line-height: 1.5;">
						Completează formularul de mai jos. Solicitarea ta va fi înregistrată instantaneu și echipa Brezoaele.ro te va contacta pentru confirmare și detalii tehnice.
					</p>
				</div>

				<form id="brz-ratecard-order-form" method="post">
					<input type="hidden" name="req_category" value="Firme &amp; Antreprenori" />

					<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
						<div>
							<label style="display: block; font-weight: 700; font-size: 0.85rem; margin-bottom: 6px; color: #0f172a;">Nume *</label>
							<input type="text" name="req_nume" required placeholder="ex: Popescu" style="width: 100%; padding: 12px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-family: inherit; font-size: 0.95rem; box-sizing: border-box;" />
						</div>
						<div>
							<label style="display: block; font-weight: 700; font-size: 0.85rem; margin-bottom: 6px; color: #0f172a;">Prenume *</label>
							<input type="text" name="req_prenume" required placeholder="ex: Ion" style="width: 100%; padding: 12px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-family: inherit; font-size: 0.95rem; box-sizing: border-box;" />
						</div>
					</div>

					<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
						<div>
							<label style="display: block; font-weight: 700; font-size: 0.85rem; margin-bottom: 6px; color: #0f172a;">Formă Juridică *</label>
							<select name="req_forma_juridica" required style="width: 100%; padding: 12px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-family: inherit; font-size: 0.95rem; background-color: #fff; box-sizing: border-box;">
								<option value="SRL" selected>S.R.L. (Societate cu Răspundere Limitată)</option>
								<option value="PFA / II / IF">P.F.A. / Î.I. / Î.F. (Persoană Fizică Autorizată)</option>
								<option value="S.A.">S.A. (Societate pe Acțiuni)</option>
								<option value="Persoană Fizică">Persoană Fizică / Producător Agricol</option>
								<option value="Alta">Alta / Personalizat</option>
							</select>
						</div>
						<div>
							<label style="display: block; font-weight: 700; font-size: 0.85rem; margin-bottom: 6px; color: #0f172a;">Date Companie (CUI / Registrul Comerțului / Adresă)</label>
							<input type="text" name="req_date_companie" placeholder="ex: RO12345678, J15/123/2020" style="width: 100%; padding: 12px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-family: inherit; font-size: 0.95rem; box-sizing: border-box;" />
						</div>
					</div>

					<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
						<div>
							<label style="display: block; font-weight: 700; font-size: 0.85rem; margin-bottom: 6px; color: #0f172a;">Adresă Email *</label>
							<input type="email" name="req_email" required placeholder="contact@companie.ro" style="width: 100%; padding: 12px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-family: inherit; font-size: 0.95rem; box-sizing: border-box;" />
						</div>
						<div>
							<label style="display: block; font-weight: 700; font-size: 0.85rem; margin-bottom: 6px; color: #0f172a;">Număr Telefon *</label>
							<input type="tel" name="req_phone" required placeholder="07xx xxx xxx" style="width: 100%; padding: 12px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-family: inherit; font-size: 0.95rem; box-sizing: border-box;" />
						</div>
					</div>

					<div style="margin-bottom: 16px;">
						<label style="display: block; font-weight: 700; font-size: 0.85rem; margin-bottom: 6px; color: #0f172a;">Selector Pachet Reclamă Dorit *</label>
						<select id="brz-pkg-select" name="req_package" required style="width: 100%; padding: 12px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-family: inherit; font-size: 0.95rem; background-color: #fff; box-sizing: border-box;">
							<option value="Basic Firme">Basic Firme — 149 lei/lună</option>
							<option value="Standard Firme" selected>Standard Firme ⭐ — 299 lei/lună (Popular)</option>
							<option value="Premium Firme">Premium Firme — 599 lei/lună</option>
							<option value="Pachet Personalizat Firme">Pachet Personalizat / Alta opțiune</option>
						</select>
					</div>

					<div style="margin-bottom: 20px;">
						<label style="display: block; font-weight: 700; font-size: 0.85rem; margin-bottom: 6px; color: #0f172a;">Mesaj &amp; Detalii Reclamă (Obligatoriu) *</label>
						<textarea name="req_message" required rows="4" placeholder="Scrie obligatoriu ce reclamă dorești (ex: banner header, advertorial de prezentare firmă, anunț promovare), pentru ce perioadă și ce obiective ai..." style="width: 100%; padding: 12px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-family: inherit; font-size: 0.92rem; box-sizing: border-box; resize: vertical;"></textarea>
					</div>

					<button type="submit" id="brz-order-submit-btn" class="brz-rc-action-btn" style="width: 100%; justify-content: center; padding: 16px; font-size: 1rem; border-radius: 8px;">
						🚀 Trimite Solicitarea de Reclamă
					</button>
				</form>

				<div id="brz-order-status-msg" style="display: none; margin-top: 16px; padding: 14px; border-radius: 8px; text-align: center; font-weight: 700; font-size: 0.95rem;"></div>
			</div>

			<!-- FOOTER TERMENI -->
			<div class="brz-rc-footer" style="margin-top: 30px;">
				<div>
					© <?php echo date( 'Y' ); ?> Brezoaele.ro • Servicii furnizate de <strong>ECOMPLEX.RO SRL</strong> (Neplătitor de TVA — TVA 0%). Prețurile afișate sunt finale.
				</div>
				<div>
					<strong>Solicită Ofertă:</strong> contact@brezoaele.ro
				</div>
			</div>

		</div>
	</div>
</main>

<script>
document.addEventListener("DOMContentLoaded", function() {
	const tabs = document.querySelectorAll('.brz-rc-mobile-tab-btn');
	const table = document.querySelector('.brz-rc-table');

	if (tabs.length && table) {
		tabs.forEach(tab => {
			tab.addEventListener('click', function() {
				tabs.forEach(t => t.classList.remove('active'));
				this.classList.add('active');

				table.classList.remove('show-col-2', 'show-col-3', 'show-col-4');
				const col = this.getAttribute('data-col');
				table.classList.add('show-col-' + col);
			});
		});
	}

	// Auto-select pachet din URL query parameter ?package=...
	const urlParams = new URLSearchParams(window.location.search);
	const pkgParam = urlParams.get('package') || urlParams.get('pachet');
	const pkgSelect = document.getElementById('brz-pkg-select');
	if (pkgParam && pkgSelect) {
		for (let i = 0; i < pkgSelect.options.length; i++) {
			if (pkgSelect.options[i].value.toLowerCase().includes(pkgParam.toLowerCase())) {
				pkgSelect.selectedIndex = i;
				break;
			}
		}
	}

	// AJAX Form Submit
	const form = document.getElementById('brz-ratecard-order-form');
	const statusMsg = document.getElementById('brz-order-status-msg');
	const submitBtn = document.getElementById('brz-order-submit-btn');

	if (form) {
		form.addEventListener('submit', function(e) {
			e.preventDefault();
			submitBtn.disabled = true;
			submitBtn.textContent = 'Se trimite solicitarea...';
			statusMsg.style.display = 'none';

			const formData = new FormData(form);
			formData.append('action', 'brezoaele_handle_ratecard_request');

			fetch('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', {
				method: 'POST',
				body: formData
			})
			.then(res => res.json())
			.then(data => {
				submitBtn.disabled = false;
				submitBtn.textContent = '🚀 Trimite Solicitarea de Reclamă';
				statusMsg.style.display = 'block';
				if (data.success) {
					statusMsg.style.background = '#dcfce7';
					statusMsg.style.color = '#15803d';
					statusMsg.style.border = '1px solid #86efac';
					statusMsg.textContent = '✅ ' + (data.data.message || 'Solicitarea a fost trimisă cu succes!');
					form.reset();
				} else {
					statusMsg.style.background = '#fee2e2';
					statusMsg.style.color = '#b91c1c';
					statusMsg.style.border = '1px solid #fca5a5';
					statusMsg.textContent = '❌ ' + (data.data.message || 'A apărut o eroare.');
				}
			})
			.catch(err => {
				submitBtn.disabled = false;
				submitBtn.textContent = '🚀 Trimite Solicitarea de Reclamă';
				statusMsg.style.display = 'block';
				statusMsg.style.background = '#fee2e2';
				statusMsg.style.color = '#b91c1c';
				statusMsg.style.border = '1px solid #fca5a5';
				statusMsg.textContent = '❌ Eroare de rețea. Te rugăm să încerci din nou.';
			});
		});
	}
});
</script>

<?php
get_footer();

