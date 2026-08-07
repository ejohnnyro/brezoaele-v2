<?php
/**
 * Template Name: Adaugă Anunț
 * @package Brezoaele_V2
 */

get_header();

$is_logged_in = is_user_logged_in();
$current_user = wp_get_current_user();
$errors       = array();
$success_msg  = '';

// Process form submission — only when logged in
if ( $is_logged_in && $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['submit_anunt'] ) ) {
	if ( ! isset( $_POST['anunt_nonce'] ) || ! wp_verify_nonce( $_POST['anunt_nonce'], 'submit_anunt_action' ) ) {
		$errors[] = 'Token de securitate invalid. Te rugăm să încerci din nou.';
	}

	$title        = sanitize_text_field( $_POST['post_title'] ?? '' );
	$category     = intval( $_POST['anunt_category'] ?? 0 );
	$content      = wp_kses_post( $_POST['post_content'] ?? '' );
	$pret         = sanitize_text_field( $_POST['pret'] ?? '' );
	$telefon      = sanitize_text_field( $_POST['telefon'] ?? '' );
	$locatie      = sanitize_text_field( $_POST['locatie'] ?? 'Brezoaele' );
	$package      = sanitize_text_field( $_POST['package'] ?? 'free' );

	// Fiscal billing details
	$billing_type = sanitize_text_field( $_POST['billing_type'] ?? 'pf' );
	$fname        = sanitize_text_field( $_POST['fname'] ?? $current_user->first_name );
	$lname        = sanitize_text_field( $_POST['lname'] ?? $current_user->last_name );
	$email        = sanitize_email( $_POST['email'] ?? $current_user->user_email );
	$phone        = sanitize_text_field( $_POST['phone'] ?? $telefon );
	$address      = sanitize_textarea_field( $_POST['address'] ?? '' );
	$city         = sanitize_text_field( $_POST['city'] ?? '' );
	$county       = sanitize_text_field( $_POST['county'] ?? '' );
	$company_name = sanitize_text_field( $_POST['company_name'] ?? '' );
	$company_cui  = sanitize_text_field( $_POST['company_cui'] ?? '' );
	$company_reg  = sanitize_text_field( $_POST['company_reg'] ?? '' );

	if ( empty( $title ) ) {
		$errors[] = 'Titlul anunțului este obligatoriu.';
	}
	if ( empty( $content ) ) {
		$errors[] = 'Descrierea anunțului este obligatorie.';
	}
	if ( 'premium' === $package && ( empty( $fname ) || empty( $email ) || empty( $phone ) ) ) {
		$errors[] = 'Datele de facturare (Nume, Email, Telefon) sunt obligatorii pentru pachetul Premium.';
	}

	if ( empty( $errors ) ) {
		$post_status = ( 'premium' === $package ) ? 'pending' : 'publish';

		$anunt_id = wp_insert_post( array(
			'post_title'   => $title,
			'post_content' => $content,
			'post_status'  => $post_status,
			'post_type'    => 'anunt',
			'post_author'  => $current_user->ID,
		) );

		if ( $anunt_id && ! is_wp_error( $anunt_id ) ) {
			if ( $category > 0 ) {
				wp_set_post_terms( $anunt_id, array( $category ), 'categorie_anunt' );
			}
			update_post_meta( $anunt_id, '_anunt_pret', $pret );
			update_post_meta( $anunt_id, '_anunt_telefon', $telefon );
			update_post_meta( $anunt_id, '_anunt_locatie', $locatie );

			// Handle photo uploads (up to 10 for premium, 3 for free)
			$max_photos = ( 'premium' === $package ) ? 10 : 3;
			if ( ! empty( $_FILES['anunt_photos']['name'][0] ) ) {
				require_once ABSPATH . 'wp-admin/includes/image.php';
				require_once ABSPATH . 'wp-admin/includes/file.php';
				require_once ABSPATH . 'wp-admin/includes/media.php';

				$files = $_FILES['anunt_photos'];
				$count = min( count( $files['name'] ), $max_photos );
				for ( $i = 0; $i < $count; $i++ ) {
					if ( $files['name'][ $i ] ) {
						$file = array(
							'name'     => $files['name'][ $i ],
							'type'     => $files['type'][ $i ],
							'tmp_name' => $files['tmp_name'][ $i ],
							'error'    => $files['error'][ $i ],
							'size'     => $files['size'][ $i ],
						);

						// Redimensionare & Compresie Automată la Max 1080x1080px (raport de aspect păstrat)
						$editor = wp_get_image_editor( $file['tmp_name'] );
						if ( ! is_wp_error( $editor ) ) {
							$editor->resize( 1080, 1080, false );
							$editor->set_quality( 85 );
							$editor->save( $file['tmp_name'] );
						}

						$_FILES['single_photo'] = $file;
						$attach_id = media_handle_upload( 'single_photo', $anunt_id );
						if ( ! is_wp_error( $attach_id ) && 0 === $i ) {
							set_post_thumbnail( $anunt_id, $attach_id );
						}
					}
				}
			}

			if ( 'premium' === $package ) {
				// Create order & redirect to EuPlatesc (10 LEI)
				$order_data = array(
					'item_type'    => 'anunt_premium',
					'item_id'      => $anunt_id,
					'amount'       => 10.00,
					'currency'     => 'RON',
					'billing_type' => $billing_type,
					'fname'        => $fname,
					'lname'        => $lname,
					'email'        => $email,
					'phone'        => $phone,
					'address'      => $address,
					'city'         => $city,
					'county'       => $county,
					'company_name' => $company_name,
					'company_cui'  => $company_cui,
					'company_reg'  => $company_reg,
				);

				$order_id  = Brezoaele_Orders_DB::create_order( $order_data );
				$timestamp = gmdate( 'YmdHis' );
				$nonce     = md5( uniqid( rand(), true ) );
				$order_desc= 'Anunt Premium Brezoaele.ro #' . $anunt_id;

				$sig_params = array(
					'amount'     => '10.00',
					'curr'       => 'RON',
					'invoice_id' => $order_id,
					'order_desc' => $order_desc,
					'merch_id'   => Brezoaele_EuPlatesc::get_mid(),
					'timestamp'  => $timestamp,
					'nonce'      => $nonce,
				);

				$fp_hash = strtoupper( Brezoaele_EuPlatesc::euplatesc_mac( $sig_params ) );

				$gateway_fields = array(
					'amount'                => '10.00',
					'curr'                  => 'RON',
					'invoice_id'            => $order_id,
					'order_desc'            => $order_desc,
					'merch_id'              => Brezoaele_EuPlatesc::get_mid(),
					'timestamp'             => $timestamp,
					'nonce'                 => $nonce,
					'fp_hash'               => $fp_hash,
					'fname'                 => $fname,
					'lname'                 => $lname,
					'email'                 => $email,
					'phone'                 => $phone,
					'company'               => $company_name,
					'cui'                   => $company_cui,
					'backURL'               => home_url( '/payment-success/' ),
					'ExtraData[silenturl]'  => home_url( '/payment-callback/' ),
					'ExtraData[successurl]' => home_url( '/payment-success/' ),
					'ExtraData[failedurl]'  => home_url( '/contul-meu/?payment=failed' ),
				);

				// Render auto-submit form to EuPlatesc
				?>
				<!DOCTYPE html>
				<html>
				<head><title>Redirecting to EuPlatesc...</title></head>
				<body style="background:#0f172a; color:#fff; font-family:sans-serif; text-align:center; padding-top:100px;">
					<h2>Te redirecționăm către procesatorul securizat EuPlatesc.ro...</h2>
					<p>Suma de plată: <strong>10.00 RON</strong></p>
					<form id="euForm" method="POST" action="<?php echo esc_url( Brezoaele_EuPlatesc::get_gateway_url() ); ?>">
						<?php foreach ( $gateway_fields as $k => $v ) : ?>
							<input type="hidden" name="<?php echo esc_attr( $k ); ?>" value="<?php echo esc_attr( $v ); ?>">
						<?php endforeach; ?>
					</form>
					<script>document.getElementById('euForm').submit();</script>
				</body>
				</html>
				<?php
				exit;
			} else {
				$success_msg = 'Anunțul tău gratuit a fost publicat cu succes!';
			}
		}
	}
}
?>

<main id="primary" class="site-main" style="padding: 40px 0; background-color: var(--color-bg);">
	<div class="container">

		<?php // Form visible to all; login check happens at submit button ?>

			<header class="page-header" style="margin-bottom: 24px;">
				<h1 class="page-title" style="font-size: 2.2rem; font-weight: 900; font-family: var(--font-heading); margin-bottom: 6px;">
					📢 Adaugă un Anunț Nou
				</h1>
				<p style="color: var(--color-text-muted); font-size: 0.95rem; margin: 0;">
					Completează detaliile de mai jos pentru a-ți publica anunțul pe Piața Locală Brezoaele.
				</p>
			</header>

			<?php if ( ! empty( $success_msg ) ) : ?>
				<div style="padding: 16px; background: #ecfdf5; border: 1px solid #a7f3d0; color: #047857; border-radius: 8px; font-weight: 700; font-size: 0.95rem; margin-bottom: 24px;">
					✔ <?php echo esc_html( $success_msg ); ?> <a href="<?php echo esc_url( home_url( '/anunturi/' ) ); ?>" style="text-decoration: underline;">Vezi piața locală &rarr;</a>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $errors ) ) : ?>
				<div style="padding: 16px; background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; border-radius: 8px; font-size: 0.9rem; margin-bottom: 24px;">
					<ul>
						<?php foreach ( $errors as $err ) : ?>
							<li>❌ <?php echo esc_html( $err ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<!-- GRID 2 COLOANE: STANGA FORMULAR, DREAPTA CARD BENEFICII PACHETE -->
			<style>
				.ad-registration-grid {
					display: grid;
					grid-template-columns: 1fr;
					gap: 24px;
				}
				@media (min-width: 992px) {
					.ad-registration-grid { grid-template-columns: 7fr 5fr; }
					.ad-benefits-col { order: 0; }
					.ad-form-col     { order: 0; }
				}
				@media (max-width: 991px) {
					.ad-benefits-col { order: -1; }
				}
				/* eMAG-style collapse — MOBILE ONLY */
				.ad-benefits-collapsible {
					position: relative;
					transition: max-height 0.4s ease;
				}
				.ad-benefits-fade {
					display: none; /* hidden on desktop */
					position: absolute;
					bottom: 0; left: 0; right: 0; height: 80px;
					background: linear-gradient(to bottom, rgba(255,255,255,0), #ffffff 90%);
					pointer-events: none;
					transition: opacity 0.3s ease;
				}
				.ad-toggle-btn {
					display: none;
					width: 100%; background: none; border: none;
					color: #b45309; font-weight: 800; font-size: 0.9rem;
					cursor: pointer; padding: 10px 0 4px; text-align: center;
				}
				@media (max-width: 991px) {
					.ad-benefits-collapsible { max-height: 220px; overflow: hidden; }
					.ad-benefits-collapsible.expanded { max-height: 1000px; }
					.ad-benefits-collapsible.expanded .ad-benefits-fade { opacity: 0; }
					.ad-benefits-fade { display: block; }
					.ad-toggle-btn { display: block; }
				}
				/* PF/PJ stacked on mobile */
				.billing-type-opts { display: flex; flex-wrap: wrap; gap: 8px 16px; margin-bottom: 14px; }
				@media (max-width: 640px) {
					.billing-type-opts { flex-direction: column; gap: 12px; }
				}
				/* 2-col billing grids responsive */
				.ad-form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 12px; }
				/* also ensure selects never overflow on mobile */
				.ad-form-grid-2 select, .ad-form-grid-2 input { max-width: 100%; box-sizing: border-box; }
				@media (max-width: 640px) {
					.ad-form-grid-2 { grid-template-columns: 1fr; }
				}
				/* Submit full-width on mobile */
				.ad-submit-btn { width: 100%; padding: 14px; font-weight: 800; font-size: 1rem; border-radius: 8px; text-align: center; }
				.ad-login-prompt { padding: 20px; background: #f0fdf4; border: 2px solid #047857; border-radius: 10px; }
				.ad-login-prompt p { font-size: 0.95rem; font-weight: 700; color: #065f46; margin: 0 0 12px; }
				.ad-login-prompt .btn { display: block; width: 100%; text-align: center; padding: 12px 20px; font-weight: 800; font-size: 0.95rem; box-sizing: border-box; }
			</style>
			<div class="ad-registration-grid">

				<!-- COLOANA STANGA: FORMULAR -->
				<div class="ad-form-col">
					<form method="post" enctype="multipart/form-data" class="card" style="padding: 28px; background: #ffffff; border: 1px solid var(--color-border); border-radius: var(--border-radius-lg); display: flex; flex-direction: column; gap: 20px;">
						<?php wp_nonce_field( 'submit_anunt_action', 'anunt_nonce' ); ?>

						<!-- SELECTIE PACHET -->
						<div>
							<label style="display: block; font-size: 0.85rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-dark); margin-bottom: 10px;">
								⭐ Alege Pachetul de Afișare:
							</label>
							<div class="ad-form-grid-2" style="gap: 14px;">
								
								<!-- Pachet Gratuit -->
								<label style="border: 2px solid #cbd5e1; border-radius: 10px; padding: 14px; cursor: pointer; display: flex; flex-direction: column; justify-content: space-between; background: #fafafa; transition: border-color 0.2s;" id="pkg-free-box">
									<div>
										<input type="radio" name="package" value="free" checked style="margin-right: 6px;" id="pkg-free">
										<strong style="font-size: 0.95rem; color: #0f172a;">📢 Gratuit (0 LEI)</strong>
										<ul style="font-size: 0.78rem; color: #64748b; margin-top: 6px; padding-left: 16px; line-height: 1.4;">
											<li>30 de zile valabilitate</li>
											<li>Maxim 3 poze</li>
										</ul>
									</div>
								</label>

								<!-- Pachet Premium -->
								<label style="border: 2px solid #f59e0b; border-radius: 10px; padding: 14px; cursor: pointer; display: flex; flex-direction: column; justify-content: space-between; background: #fffbeb; transition: border-color 0.2s;" id="pkg-premium-box">
									<div>
										<input type="radio" name="package" value="premium" style="margin-right: 6px;" id="pkg-premium">
										<strong style="font-size: 0.95rem; color: #b45309;">⭐ PREMIUM (10 LEI)</strong>
										<ul style="font-size: 0.78rem; color: #92400e; margin-top: 6px; padding-left: 16px; line-height: 1.4;">
											<li><strong>Fixat în capul listei</strong></li>
											<li>Până la 10 poze HD</li>
										</ul>
									</div>
								</label>
							</div>
						</div>

						<!-- DETALII ANUNT -->
						<div>
							<label for="post_title" style="display: block; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); margin-bottom: 4px;">Titlu Anunț *</label>
							<input type="text" id="post_title" name="post_title" required placeholder="Ex: Vând teren intravilan Brezoaele 1000 mp..." style="width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; box-sizing: border-box;">
						</div>

						<div class="ad-form-grid-2" style="gap: 16px;">
							<div>
								<label for="anunt_category" style="display: block; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); margin-bottom: 4px;">Categorie Anunț</label>
								<?php
								wp_dropdown_categories( array(
									'show_option_none' => '-- Alege Categorie / Subcategorie --',
									'taxonomy'         => 'categorie_anunt',
									'name'             => 'anunt_category',
									'id'               => 'anunt_category',
									'class'            => 'widefat',
									'style'            => 'width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; box-sizing: border-box; max-width: 100%;',
									'hide_empty'       => 0,
									'hierarchical'     => 1,
									'orderby'          => 'name',
									'order'            => 'ASC',
								) );
								?>

							</div>
							<div>
								<label for="pret" style="display: block; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); margin-bottom: 4px;">Preț (LEI / EUR)</label>
								<input type="text" id="pret" name="pret" placeholder="Ex: 15.000 EUR sau Negociabil" style="width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; box-sizing: border-box;">
							</div>
						</div>

						<div class="ad-form-grid-2" style="gap: 16px;">
							<div>
								<label for="telefon" style="display: block; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); margin-bottom: 4px;">Telefon Contact *</label>
								<input type="tel" id="telefon" name="telefon" required placeholder="07xx xxx xxx" style="width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; box-sizing: border-box;">
							</div>
							<div>
								<label for="locatie" style="display: block; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); margin-bottom: 4px;">Locație</label>
								<input type="text" id="locatie" name="locatie" value="Brezoaele" style="width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; box-sizing: border-box;">
							</div>
						</div>

						<div>
							<label for="post_content" style="display: block; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); margin-bottom: 4px;">Descriere Anunț *</label>
							<textarea id="post_content" name="post_content" rows="5" required placeholder="Descrie detaliat anunțul tău..." style="width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; box-sizing: border-box;"></textarea>
						</div>

						<div>
							<label for="anunt_photos" style="display: block; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); margin-bottom: 4px;">Imagini (Selecție Multiplă)</label>
							<input type="file" id="anunt_photos" name="anunt_photos[]" multiple accept="image/*" style="width: 100%; padding: 10px; border: 1.5px dashed #cbd5e1; border-radius: 8px; background: #f8fafc;">
							<div id="photo-limit-hint" style="font-size: 0.78rem; color: #64748b; margin-top: 4px;">
								ℹ️ Pachet Gratuit: Permise <strong>maxim 3 fotografii</strong>. Pozele sunt optimizate și redimensionate automat la max 1080x1080px.
							</div>
						</div>

						<!-- FORMULAR DATE FACTURARE FISCALA (SE AFISEAZA LA PREMIUM) -->
						<div id="billing-section" style="display: none; border-top: 2px solid #e2e8f0; padding-top: 18px; margin-top: 6px;">
							<h3 style="font-size: 1.05rem; font-weight: 800; font-family: var(--font-heading); margin-bottom: 12px; color: #0f172a;">
								🧾 Date Facturare Fiscală (EuPlatesc.ro)
							</h3>

							<div class="billing-type-opts">
								<label style="font-weight: 700; font-size: 0.85rem; display: flex; align-items: center; gap: 6px;">
									<input type="radio" name="billing_type" value="pf" checked id="bill-pf"> Persoană Fizică
								</label>
								<label style="font-weight: 700; font-size: 0.85rem; display: flex; align-items: center; gap: 6px;">
									<input type="radio" name="billing_type" value="pj" id="bill-pj"> Persoană Juridică (Firmă / PFA)
								</label>
							</div>

							<div class="ad-form-grid-2">
								<div>
									<label style="display: block; font-size: 0.75rem; font-weight: 800; color: #64748b;">Nume *</label>
									<input type="text" name="lname" value="<?php echo esc_attr( $current_user->last_name ); ?>" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
								</div>
								<div>
									<label style="display: block; font-size: 0.75rem; font-weight: 800; color: #64748b;">Prenume *</label>
									<input type="text" name="fname" value="<?php echo esc_attr( $current_user->first_name ); ?>" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
								</div>
							</div>

							<div class="ad-form-grid-2">
								<div>
									<label style="display: block; font-size: 0.75rem; font-weight: 800; color: #64748b;">Email Factură *</label>
									<input type="email" name="email" value="<?php echo esc_attr( $current_user->user_email ); ?>" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
								</div>
								<div>
									<label style="display: block; font-size: 0.75rem; font-weight: 800; color: #64748b;">Telefon Factură *</label>
									<input type="tel" name="phone" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
								</div>
							</div>

							<!-- Campuri Firme (PJ) -->
							<div id="pj-fields" style="display: none; background: #f8fafc; padding: 14px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 12px;">
								<div style="margin-bottom: 10px;">
									<label style="display: block; font-size: 0.75rem; font-weight: 800; color: #64748b;">Denumire Societate / Firmă *</label>
									<input type="text" name="company_name" placeholder="Ex: SC EXEMPLU SRL" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px;">
								</div>
								<div class="ad-form-grid-2">
									<div>
										<label style="display: block; font-size: 0.75rem; font-weight: 800; color: #64748b;">CUI / CIF *</label>
										<input type="text" name="company_cui" placeholder="RO12345678" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px;">
									</div>
									<div>
										<label style="display: block; font-size: 0.75rem; font-weight: 800; color: #64748b;">Nr. Reg. Comertului</label>
										<input type="text" name="company_reg" placeholder="J15/123/2020" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px;">
									</div>
								</div>
							</div>

							<div style="margin-bottom: 12px;">
								<label style="display: block; font-size: 0.75rem; font-weight: 800; color: #64748b;">Adresă Sediu / Domiciliu *</label>
								<input type="text" name="address" placeholder="Strada, Număr, Bloc..." style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px;">
							</div>
						</div>

						<?php if ( $is_logged_in ) : ?>
						<button type="submit" name="submit_anunt" class="btn btn-primary" style="padding: 14px; font-weight: 800; font-size: 1rem; border-radius: 8px;">
							🚀 Salvează & Publică Anunțul &rarr;
						</button>
					<?php else : ?>
						<div style="padding: 20px; background: #f0fdf4; border: 2px solid #047857; border-radius: 10px; text-align: center;">
							<p style="font-size: 0.95rem; font-weight: 700; color: #065f46; margin: 0 0 12px 0;">
								🔐 Pentru a publica anunțul completat, este necesar să te conectezi în cont sau să îți creezi un cont gratuit.
							</p>
							<a href="<?php echo esc_url( home_url( '/contul-meu/?redirect_to=' . urlencode( get_permalink() ) ) ); ?>" class="btn btn-primary" style="padding: 12px 24px; font-weight: 800; font-size: 0.95rem; display: inline-block;">
								👤 Conectează-te / Creează Cont Gratuit &rarr;
							</a>
						</div>
					<?php endif; ?>
				</form>
			</div>

				<!-- COLOANA DREAPTA: CARD BENEFICII ANUNTURI PREMIUM VS GRATUIT -->
				<div class="ad-benefits-col">
					<div class="card" style="padding: 24px; background: #ffffff; border: 2px solid #f59e0b; border-radius: var(--border-radius-lg); box-shadow: var(--shadow-md); position: sticky; top: 90px;">

						<!-- Header preț — mereu vizibil -->
						<div style="text-align: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 16px; margin-bottom: 18px;">
							<span style="background: #fef3c7; color: #b45309; font-weight: 900; font-size: 0.75rem; text-transform: uppercase; padding: 4px 12px; border-radius: 20px; border: 1px solid #fde047;">
								⭐ BENEFICII PACHET PREMIUM
							</span>
							<div style="font-size: 2rem; font-weight: 900; color: #b45309; margin-top: 10px; font-family: var(--font-heading);">
								10 LEI <span style="font-size: 0.85rem; font-weight: 600; color: #78350f;">/ 30 de zile</span>
							</div>
							<p style="font-size: 0.8rem; color: #78350f; margin-top: 4px;">
								Evidențiere maximă pentru vânzări rapide în Brezoaele.
							</p>
						</div>

						<!-- Corp colapsabil (eMAG-style) — trunchiat pe mobil -->
						<div class="ad-benefits-collapsible" id="ad-benefits-body">
							<div class="ad-benefits-fade"></div>

							<h4 style="font-size: 0.9rem; font-weight: 800; text-transform: uppercase; color: #0f172a; margin-bottom: 12px;">
								De ce să alegi Pachetul Premium (10 LEI):
							</h4>

							<ul style="list-style: none; padding: 0; margin: 0 0 20px 0; display: flex; flex-direction: column; gap: 12px; font-size: 0.85rem;">
								<li style="display: flex; align-items: flex-start; gap: 10px;">
									<span style="color: #f59e0b; font-weight: 900; font-size: 1.1rem; line-height: 1;">📌</span>
									<div><strong>Fixat în Capul Listei (Sticky Top)</strong>: Anunțul rămâne primul în listă pe toată durata celor 30 de zile.</div>
								</li>
								<li style="display: flex; align-items: flex-start; gap: 10px;">
									<span style="color: #f59e0b; font-weight: 900; font-size: 1.1rem; line-height: 1;">⭐</span>
									<div><strong>Ecuson &amp; Card Evidențiat</strong>: Bordură aurie și ecuson vizibil ⭐ PREMIUM pentru atragerea privirilor.</div>
								</li>
								<li style="display: flex; align-items: flex-start; gap: 10px;">
									<span style="color: #f59e0b; font-weight: 900; font-size: 1.1rem; line-height: 1;">📸</span>
									<div><strong>Până la 10 Fotografii HD</strong>: Galerie extinsă de imagini (față de doar 3 poze la pachetul gratuit).</div>
								</li>
								<li style="display: flex; align-items: flex-start; gap: 10px;">
									<span style="color: #f59e0b; font-weight: 900; font-size: 1.1rem; line-height: 1;">💬</span>
									<div><strong>Buton Direct Contact WhatsApp</strong>: Cumpărătorii te pot contacta cu 1-click cu mesaj pre-completat.</div>
								</li>
								<li style="display: flex; align-items: flex-start; gap: 10px;">
									<span style="color: #f59e0b; font-weight: 900; font-size: 1.1rem; line-height: 1;">🖼️</span>
									<div><strong>Compresie Automată 1080x1080px</strong>: Pozele sunt optimizate automat pe server fără pierdere de aspect ratio.</div>
								</li>
							</ul>

							<div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 14px; border-radius: 8px; font-size: 0.78rem; color: #475569; line-height: 1.4;">
								<strong>🧾 Informare Fiscală &amp; Facturare:</strong><br/>
								Facturarea este realizată de către <strong>ECOMPLEX.RO SRL</strong>, societate neplătitoare de TVA (TVA 0%). Factura fiscală se poate introduce în contabilitate sub formă de cheltuială deductibilă.
							</div>
						</div>
						<button class="ad-toggle-btn" id="ad-toggle-btn" type="button" aria-expanded="false">
							▼ Arată toate beneficiile
						</button>

					</div>
				</div>
			</div>

			<script>
			jQuery(document).ready(function($) {
				function togglePackage() {
					if ($('#pkg-premium').is(':checked')) {
						$('#billing-section').slideDown(200);
						$('#photo-limit-hint').html('ℹ️ Pachet ⭐ PREMIUM: Permise <strong>până la 10 fotografii HD</strong>. Imaginile sunt optimizate și redimensionate automat la max 1080x1080px (raport de aspect păstrat).');
					} else {
						$('#billing-section').slideUp(200);
						$('#photo-limit-hint').html('ℹ️ Pachet Gratuit: Permise <strong>maxim 3 fotografii</strong>. Imaginile sunt optimizate și redimensionate automat la max 1080x1080px (raport de aspect păstrat).');
					}
				}
				function togglePJ() {
					if ($('#bill-pj').is(':checked')) {
						$('#pj-fields').slideDown(200);
					} else {
						$('#pj-fields').slideUp(200);
					}
				}

				$('input[name="package"]').on('change', togglePackage);
				$('input[name="billing_type"]').on('change', togglePJ);
				togglePackage();
				togglePJ();

				// eMAG-style collapse for benefits card (mobile only)
				var adBody = document.getElementById('ad-benefits-body');
				var adBtn  = document.getElementById('ad-toggle-btn');
				if (adBtn && adBody) {
					adBtn.addEventListener('click', function() {
						var exp = adBody.classList.toggle('expanded');
						adBtn.setAttribute('aria-expanded', exp);
						adBtn.textContent = exp ? '▲ Ascunde' : '▼ Arată toate beneficiile';
					});
				}
			});
			</script>

	</div>
</main>

<?php
get_footer();
