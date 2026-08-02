<?php
/**
 * Template Name: Solicită Adăugare Afacere pe Hartă
 * @package Brezoaele_V2
 */

// Enqueue Leaflet CSS & JS for interactive pin picking
add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_style( 'leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', array(), '1.9.4' );
	wp_enqueue_script( 'leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), '1.9.4', true );
} );

get_header();

$is_logged_in = is_user_logged_in();
$current_user = wp_get_current_user();
$errors       = array();
$order_placed = false;
$placed_order = null;

// Process form submission
if ( $is_logged_in && $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['submit_firma'] ) ) {
	if ( ! isset( $_POST['firma_nonce'] ) || ! wp_verify_nonce( $_POST['firma_nonce'], 'submit_firma_action' ) ) {
		$errors[] = 'Token de securitate invalid. Te rugăm să încerci din nou.';
	}

	$title        = sanitize_text_field( $_POST['post_title'] ?? '' );
	$category     = sanitize_text_field( $_POST['categorie'] ?? 'Servicii' );
	$content      = wp_kses_post( $_POST['post_content'] ?? '' );
	$telefon      = sanitize_text_field( $_POST['telefon'] ?? '' );
	$adresa       = sanitize_text_field( $_POST['adresa'] ?? '' );
	$website      = sanitize_url( $_POST['website'] ?? '' );
	$lat          = sanitize_text_field( $_POST['latitude'] ?? '44.561854' );
	$lng          = sanitize_text_field( $_POST['longitude'] ?? '25.770593' );
	$payment_method = sanitize_text_field( $_POST['payment_method'] ?? 'card' ); // 'card', 'op', 'cash'

	// Fiscal Billing Details
	$billing_type = sanitize_text_field( $_POST['billing_type'] ?? 'pj' );
	$fname        = sanitize_text_field( $_POST['fname'] ?? $current_user->first_name );
	$lname        = sanitize_text_field( $_POST['lname'] ?? $current_user->last_name );
	$email        = sanitize_email( $_POST['email'] ?? $current_user->user_email );
	$phone        = sanitize_text_field( $_POST['phone'] ?? $telefon );
	$address      = sanitize_textarea_field( $_POST['address'] ?? $adresa );
	$city         = sanitize_text_field( $_POST['city'] ?? '' );
	$county       = sanitize_text_field( $_POST['county'] ?? '' );
	$company_name = sanitize_text_field( $_POST['company_name'] ?? '' );
	$company_cui  = sanitize_text_field( $_POST['company_cui'] ?? '' );
	$company_reg  = sanitize_text_field( $_POST['company_reg'] ?? '' );

	if ( empty( $title ) ) {
		$errors[] = 'Numele afacerii este obligatoriu.';
	}
	if ( empty( $content ) ) {
		$errors[] = 'Descrierea afacerii este obligatorie.';
	}
	if ( empty( $phone ) || empty( $email ) ) {
		$errors[] = 'Telefonul și Email-ul de contact/facturare sunt obligatorii.';
	}
	if ( 'pj' === $billing_type && ( empty( $company_name ) || empty( $company_cui ) ) ) {
		$errors[] = 'Denumirea firmei și CUI-ul sunt obligatorii pentru persoanele juridice.';
	}

	if ( empty( $errors ) ) {
		// Save CPT 'firma' in pending status until payment confirmation
		$firma_id = wp_insert_post( array(
			'post_title'   => $title,
			'post_content' => $content,
			'post_status'  => 'pending',
			'post_type'    => 'firma',
			'post_author'  => $current_user->ID,
		) );

		if ( $firma_id && ! is_wp_error( $firma_id ) ) {
			update_post_meta( $firma_id, '_firma_categorie', $category );
			update_post_meta( $firma_id, '_firma_telefon', $telefon );
			update_post_meta( $firma_id, '_firma_adresa', $adresa );
			update_post_meta( $firma_id, '_firma_website', $website );
			update_post_meta( $firma_id, '_firma_lat', $lat );
			update_post_meta( $firma_id, '_firma_lng', $lng );

			require_once ABSPATH . 'wp-admin/includes/image.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/media.php';

			// 1. Handle main logo/image upload (Featured Image) with max 1080x1080 auto-resizing
			if ( ! empty( $_FILES['firma_image']['name'] ) ) {
				$editor = wp_get_image_editor( $_FILES['firma_image']['tmp_name'] );
				if ( ! is_wp_error( $editor ) ) {
					$editor->resize( 1080, 1080, false );
					$editor->set_quality( 85 );
					$editor->save( $_FILES['firma_image']['tmp_name'] );
				}
				$attach_id = media_handle_upload( 'firma_image', $firma_id );
				if ( ! is_wp_error( $attach_id ) ) {
					set_post_thumbnail( $firma_id, $attach_id );
				}
			}

			// 2. Handle additional gallery photos (Up to 5 photos)
			if ( ! empty( $_FILES['firma_gallery']['name'][0] ) ) {
				$gallery_files = $_FILES['firma_gallery'];
				$gallery_ids   = array();
				$count         = min( count( $gallery_files['name'] ), 5 );

				for ( $i = 0; $i < $count; $i++ ) {
					if ( $gallery_files['name'][ $i ] ) {
						$file = array(
							'name'     => $gallery_files['name'][ $i ],
							'type'     => $gallery_files['type'][ $i ],
							'tmp_name' => $gallery_files['tmp_name'][ $i ],
							'error'    => $gallery_files['error'][ $i ],
							'size'     => $gallery_files['size'][ $i ],
						);

						$editor = wp_get_image_editor( $file['tmp_name'] );
						if ( ! is_wp_error( $editor ) ) {
							$editor->resize( 1080, 1080, false );
							$editor->set_quality( 85 );
							$editor->save( $file['tmp_name'] );
						}

						$_FILES['single_gal_photo'] = $file;
						$attach_id = media_handle_upload( 'single_gal_photo', $firma_id );
						if ( ! is_wp_error( $attach_id ) ) {
							$gallery_ids[] = $attach_id;
						}
					}
				}

				if ( ! empty( $gallery_ids ) ) {
					update_post_meta( $firma_id, '_firma_galerie', $gallery_ids );
				}
			}

			// Create Order in DB
			$order_data = array(
				'item_type'    => 'afacere_harta',
				'item_id'      => $firma_id,
				'amount'       => 149.00,
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

			$order_id = Brezoaele_Orders_DB::create_order( $order_data );

			if ( 'card' === $payment_method ) {
				// EuPlatesc Gateway Redirect
				$timestamp  = gmdate( 'YmdHis' );
				$nonce      = md5( uniqid( rand(), true ) );
				$order_desc = 'Adaugare Afacere Harta Brezoaele #' . $firma_id;

				$sig_params = array(
					'amount'     => '149.00',
					'curr'       => 'RON',
					'invoice_id' => $order_id,
					'order_desc' => $order_desc,
					'merch_id'   => Brezoaele_EuPlatesc::get_mid(),
					'timestamp'  => $timestamp,
					'nonce'      => $nonce,
				);

				$fp_hash = strtoupper( Brezoaele_EuPlatesc::euplatesc_mac( $sig_params ) );

				$gateway_fields = array(
					'amount'                => '149.00',
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

				?>
				<!DOCTYPE html>
				<html>
				<head><title>Redirecting to EuPlatesc Gateway...</title></head>
				<body style="background:#0f172a; color:#fff; font-family:sans-serif; text-align:center; padding-top:100px;">
					<h2>Te redirecționăm către procesatorul securizat EuPlatesc.ro...</h2>
					<p>Suma de plată abonament anual: <strong>149.00 RON</strong></p>
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
				// OP or Cash Order Confirmation Screen & Email
				$order_placed = true;
				$placed_order = array(
					'order_id'       => $order_id,
					'method'         => $payment_method,
					'title'          => $title,
					'amount'         => '149.00',
					'email'          => $email,
				);

				// Send Email Notification
				$subject = '[Brezoaele.ro] Confirmare înregistrare afacere #' . $order_id;
				$body    = "Salutare $fname,\n\nAm înregistrat solicitarea ta de adăugare pe Harta Serviciilor Brezoaele pentru afacerea \"$title\".\n\nIdentificator Unic Comandă: $order_id\nSuma de plată: 149.00 RON\n\n";

				if ( 'op' === $payment_method ) {
					$body .= "INSTRUCȚIUNI PLATĂ PRIN TRANSFER BANCAR (OP):\n"
						  . "Beneficiar: ECOMPLEX.RO SRL\n"
						  . "Bancă: Banca Transilvania\n"
						  . "IBAN: RO70BTRLRONCRT0CK9121401\n"
						  . "Detalii plată / Referință: $order_id\n\n"
						  . "După efectuarea transferului bancar, afacerea ta va fi activată pe hartă pe o perioadă de 365 de zile.\n\n";
				} else {
					$body .= "INSTRUCȚIUNI PLATĂ CASH / NUMERAR:\n"
						  . "Te rugăm să achiți suma de 149 LEI la reprezentantul local menționând numărul de comandă: $order_id.\n\n";
				}

				$body .= "Îți mulțumim că sprijini comunitatea locală Brezoaele.ro!\nEchipa Brezoaele.ro";
				wp_mail( $email, $subject, $body );
			}
		}
	}
}
?>

<!-- Leaflet CSS Fallback -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<main id="primary" class="site-main" style="padding: 40px 0; background-color: var(--color-bg);">
	<div class="container">

		<?php if ( $order_placed && $placed_order ) : ?>
			<!-- ECRAN CONFIRMARE PLATĂ OP / CASH -->
			<div class="card" style="max-width: 750px; margin: 0 auto; padding: 40px; background: #ffffff; border: 2px solid #047857; border-radius: var(--border-radius-lg); box-shadow: var(--shadow-lg);">
				<div style="text-align: center; margin-bottom: 24px;">
					<div style="font-size: 3.5rem; margin-bottom: 8px;">🎉</div>
					<h1 style="font-size: 1.8rem; font-weight: 900; font-family: var(--font-heading); color: #047857; margin-bottom: 6px;">
						Comandă Înregistrată cu Succes!
					</h1>
					<p style="color: #64748b; font-size: 0.95rem;">
						Afacerea ta <strong>"<?php echo esc_html( $placed_order['title'] ); ?>"</strong> a fost preluată în sistem.
					</p>
				</div>

				<div style="background: #f8fafc; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 20px; margin-bottom: 24px;">
					<div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 12px;">
						<span style="font-size: 0.85rem; font-weight: 700; color: #475569; text-transform: uppercase;">Cod Identificator Comandă:</span>
						<span style="font-size: 1.1rem; font-weight: 900; color: #0f172a; font-family: monospace; background: #e2e8f0; padding: 4px 10px; border-radius: 4px;"><?php echo esc_html( $placed_order['order_id'] ); ?></span>
					</div>
					<div style="display: flex; justify-content: space-between; align-items: center;">
						<span style="font-size: 0.85rem; font-weight: 700; color: #475569; text-transform: uppercase;">Suma de plată:</span>
						<span style="font-size: 1.25rem; font-weight: 900; color: #047857;"><?php echo esc_html( $placed_order['amount'] ); ?> RON</span>
					</div>
				</div>

				<?php if ( 'op' === $placed_order['method'] ) : ?>
					<div style="background: #f0fdf4; border: 1.5px solid #bbf7d0; border-radius: 8px; padding: 20px; margin-bottom: 24px;">
						<h3 style="font-size: 1.1rem; font-weight: 800; color: #166534; margin-top: 0; margin-bottom: 12px;">
							🏦 Date Bancare pentru Transfer OP:
						</h3>
						<table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
							<tr style="border-bottom: 1px solid #dcfce7;">
								<td style="padding: 8px 0; color: #15803d; font-weight: 700;">Beneficiar:</td>
								<td style="padding: 8px 0; font-weight: 800; color: #0f172a;">ECOMPLEX.RO SRL</td>
							</tr>
							<tr style="border-bottom: 1px solid #dcfce7;">
								<td style="padding: 8px 0; color: #15803d; font-weight: 700;">Banca:</td>
								<td style="padding: 8px 0; font-weight: 800; color: #0f172a;">Banca Transilvania</td>
							</tr>
							<tr style="border-bottom: 1px solid #dcfce7;">
								<td style="padding: 8px 0; color: #15803d; font-weight: 700;">Cont IBAN:</td>
								<td style="padding: 8px 0; font-weight: 900; color: #047857; font-family: monospace; font-size: 1.05rem;">RO70BTRLRONCRT0CK9121401</td>
							</tr>
							<tr>
								<td style="padding: 8px 0; color: #15803d; font-weight: 700;">Detalii plată (Referință):</td>
								<td style="padding: 8px 0; font-weight: 900; color: #b45309; font-family: monospace;"><?php echo esc_html( $placed_order['order_id'] ); ?></td>
							</tr>
						</table>
						<p style="font-size: 0.8rem; color: #166534; margin-top: 12px; margin-bottom: 0;">
							ℹ️ Te rugăm să menționezi numărul de comandă <strong><?php echo esc_html( $placed_order['order_id'] ); ?></strong> la detalii plată pe ordinul de plată.
						</p>
					</div>
				<?php else : ?>
					<div style="background: #fffbeb; border: 1.5px solid #fde047; border-radius: 8px; padding: 20px; margin-bottom: 24px;">
						<h3 style="font-size: 1.1rem; font-weight: 800; color: #92400e; margin-top: 0; margin-bottom: 8px;">
							💵 Instrucțiuni Plată Cash / Numerar:
						</h3>
						<p style="font-size: 0.9rem; color: #78350f; margin: 0;">
							Te rugăm să achiți suma de <strong>149 LEI</strong> la reprezentantul local menționând codul de identificare <strong><?php echo esc_html( $placed_order['order_id'] ); ?></strong>.
						</p>
					</div>
				<?php endif; ?>

				<div style="text-align: center;">
					<a href="<?php echo esc_url( home_url( '/contul-meu/' ) ); ?>" class="btn btn-primary" style="padding: 12px 24px; font-weight: 800;">
						👤 Mergi în Panou Contul Meu &rarr;
					</a>
				</div>
			</div>

		<?php // Form visible to all; login check happens at submit button ?>

			<header class="page-header" style="margin-bottom: 24px;">
				<h1 class="page-title" style="font-size: 2.2rem; font-weight: 900; font-family: var(--font-heading); margin-bottom: 6px;">
					🗺️ Adaugă Afacerea ta pe Harta Serviciilor Brezoaele
				</h1>
				<p style="color: var(--color-text-muted); font-size: 0.95rem; margin: 0;">
					Promovează-ți firma sau serviciile locale către comunitatea din Brezoaele și împrejurimi.
				</p>
			</header>

			<?php if ( ! empty( $errors ) ) : ?>
				<div style="padding: 16px; background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; border-radius: 8px; font-size: 0.9rem; margin-bottom: 24px;">
					<ul>
						<?php foreach ( $errors as $err ) : ?>
							<li>❌ <?php echo esc_html( $err ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<!-- GRID CU 2 COLOANE: STANGA FORMULAR & MAP PIN PICKER, DREAPTA CARD BENEFICII -->
			<div style="display: grid; grid-template-columns: 1fr; gap: 24px;" class="business-registration-grid">
				<style>
					@media (min-width: 992px) {
						.business-registration-grid {
							grid-template-columns: 7fr 5fr !important;
						}
					}
				</style>

				<!-- COLOANA STANGA: FORMULAR INREGISTRARE -->
				<div>
					<form method="post" enctype="multipart/form-data" class="card" style="padding: 28px; background: #ffffff; border: 1px solid var(--color-border); border-radius: var(--border-radius-lg); display: flex; flex-direction: column; gap: 20px;">
						<?php wp_nonce_field( 'submit_firma_action', 'firma_nonce' ); ?>

						<!-- DETALII AFACERE -->
						<h3 style="font-size: 1.15rem; font-weight: 800; font-family: var(--font-heading); margin: 0; padding-bottom: 8px; border-bottom: 2px solid #e2e8f0; color: #0f172a;">
							🏢 Detalii Afacere / Serviciu Local
						</h3>

						<div>
							<label for="post_title" style="display: block; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); margin-bottom: 4px;">Denumire Afacere *</label>
							<input type="text" id="post_title" name="post_title" required placeholder="Ex: Vulcanizare Mobilă Brezoaele, Farmacia Bella..." style="width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; box-sizing: border-box;">
						</div>

						<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
							<div>
								<label for="categorie" style="display: block; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); margin-bottom: 4px;">Categorie Serviciu *</label>
								<select id="categorie" name="categorie" style="width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; background:#fff;">
									<option value="Comerț & Magazine">Comerț & Magazine</option>
									<option value="Construcții & Reparații">Construcții & Reparații</option>
									<option value="Sănătate & Farmacii">Sănătate & Farmacii</option>
									<option value="Auto & Transport">Auto & Transport</option>
									<option value="Agricultură & Producători">Agricultură & Producători</option>
									<option value="Servicii Utilitare">Servicii Utilitare</option>
								</select>
							</div>
							<div>
								<label for="telefon" style="display: block; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); margin-bottom: 4px;">Telefon Contact *</label>
								<input type="tel" id="telefon" name="telefon" required placeholder="07xx xxx xxx" style="width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; box-sizing: border-box;">
							</div>
						</div>

						<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
							<div>
								<label for="adresa" style="display: block; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); margin-bottom: 4px;">Adresă / Locație Fizică</label>
								<input type="text" id="adresa" name="adresa" placeholder="Str. Principală nr. 24, Brezoaele" style="width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; box-sizing: border-box;">
							</div>
							<div>
								<label for="website" style="display: block; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); margin-bottom: 4px;">Website / Pagină Facebook</label>
								<input type="url" id="website" name="website" placeholder="https://..." style="width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; box-sizing: border-box;">
							</div>
						</div>

						<div>
							<label for="post_content" style="display: block; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); margin-bottom: 4px;">Descriere Afacere & Servicii Oferite *</label>
							<textarea id="post_content" name="post_content" rows="4" required placeholder="Descrie serviciile oferite, programul de lucru..." style="width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; box-sizing: border-box;"></textarea>
						</div>

						<!-- SELECTOR INTERACTIV PIN PE HARTA LEAFLET -->
						<div style="border-top: 1px dashed #cbd5e1; padding-top: 16px;">
							<label style="display: block; font-size: 0.85rem; font-weight: 800; text-transform: uppercase; color: #047857; margin-bottom: 4px;">
								📍 Poziționează Afacerea pe Hartă (Click sau Drag Pin)
							</label>
							<p style="font-size: 0.78rem; color: #64748b; margin-bottom: 8px;">
								Dă click oriunde pe hartă sau trage pin-ul roșu pe locația exactă. Coordonatele se completează automat!
							</p>
							
							<div id="pin-picker-map" style="width: 100%; height: 260px; border-radius: 8px; border: 2px solid #cbd5e1; margin-bottom: 10px; z-index: 1;"></div>

							<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
								<div>
									<label style="font-size: 0.72rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Latitudine</label>
									<input type="text" id="latitude" name="latitude" value="44.561854" readonly style="width: 100%; padding: 6px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.82rem; background: #f8fafc;">
								</div>
								<div>
									<label style="font-size: 0.72rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Longitudine</label>
									<input type="text" id="longitude" name="longitude" value="25.770593" readonly style="width: 100%; padding: 6px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.82rem; background: #f8fafc;">
								</div>
							</div>
						</div>

						<!-- IMAGINE REPREZENTATIVA & GALERIE -->
						<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
							<div>
								<label for="firma_image" style="display: block; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); margin-bottom: 4px;">Logo / Foto Reprezentativă</label>
								<input type="file" id="firma_image" name="firma_image" accept="image/*" style="width: 100%; padding: 8px; border: 1.5px dashed #cbd5e1; border-radius: 8px; background: #f8fafc; font-size:0.8rem;">
							</div>
							<div>
								<label for="firma_gallery" style="display: block; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); margin-bottom: 4px;">Galerie Foto (Max 5 Poze)</label>
								<input type="file" id="firma_gallery" name="firma_gallery[]" multiple accept="image/*" style="width: 100%; padding: 8px; border: 1.5px dashed #cbd5e1; border-radius: 8px; background: #f8fafc; font-size:0.8rem;">
							</div>
						</div>

						<!-- ALEGERE METODĂ DE PLATĂ -->
						<div style="border-top: 2px solid #e2e8f0; padding-top: 18px; margin-top: 10px;">
							<h3 style="font-size: 1.15rem; font-weight: 800; font-family: var(--font-heading); margin-bottom: 12px; color: #0f172a;">
								💳 Alege Metoda de Plată (149 LEI / an)
							</h3>

							<div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 16px;">
								<label style="border: 2px solid #047857; padding: 12px 16px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: space-between; background: #f0fdf4;">
									<div style="display: flex; align-items: center; gap: 10px;">
										<input type="radio" name="payment_method" value="card" checked id="pay-card">
										<strong style="color: #065f46; font-size: 0.95rem;">💳 Card Online (EuPlatesc.ro)</strong>
									</div>
									<span style="font-size: 0.75rem; background: #10b981; color: #fff; padding: 2px 8px; border-radius: 4px; font-weight: 800;">INSTANT</span>
								</label>

								<label style="border: 2px solid #cbd5e1; padding: 12px 16px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: space-between; background: #f8fafc;">
									<div style="display: flex; align-items: center; gap: 10px;">
										<input type="radio" name="payment_method" value="op" id="pay-op">
										<strong style="color: #1e293b; font-size: 0.95rem;">🏦 Ordin de Plată / Transfer Bancar (OP)</strong>
									</div>
									<span style="font-size: 0.75rem; background: #0284c7; color: #fff; padding: 2px 8px; border-radius: 4px; font-weight: 800;">BT IBAN</span>
								</label>

								<label style="border: 2px solid #cbd5e1; padding: 12px 16px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: space-between; background: #f8fafc;">
									<div style="display: flex; align-items: center; gap: 10px;">
										<input type="radio" name="payment_method" value="cash" id="pay-cash">
										<strong style="color: #1e293b; font-size: 0.95rem;">💵 Cash / Numerar</strong>
									</div>
									<span style="font-size: 0.75rem; background: #d97706; color: #fff; padding: 2px 8px; border-radius: 4px; font-weight: 800;">LA SEDIU</span>
								</label>
							</div>
						</div>

						<!-- FORMULAR DATE FACTURARE FISCALA -->
						<div style="border-top: 2px solid #e2e8f0; padding-top: 18px;">
							<h3 style="font-size: 1.05rem; font-weight: 800; font-family: var(--font-heading); margin-bottom: 12px; color: #0f172a;">
								🧾 Date de Facturare Fiscală
							</h3>

							<div style="margin-bottom: 14px;">
								<label style="font-weight: 800; font-size: 0.88rem; margin-right: 18px; cursor: pointer;">
									<input type="radio" name="billing_type" value="pj" checked id="bill-pj"> 🏢 Persoană Juridică (FIRMĂ / PFA)
								</label>
								<label style="font-weight: 800; font-size: 0.88rem; cursor: pointer;">
									<input type="radio" name="billing_type" value="pf" id="bill-pf"> 👤 Persoană Fizică
								</label>
							</div>

							<!-- Campuri Firme (PJ) -->
							<div id="pj-fields" style="background: #f8fafc; padding: 14px; border-radius: 8px; border: 1px solid #cbd5e1; margin-bottom: 14px;">
								<div style="margin-bottom: 10px;">
									<label style="display: block; font-size: 0.75rem; font-weight: 800; color: #475569;">Denumire Societate / Firmă *</label>
									<input type="text" name="company_name" placeholder="Ex: SC SERVICII BREZOAELE SRL" style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 6px;">
								</div>
								<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
									<div>
										<label style="display: block; font-size: 0.75rem; font-weight: 800; color: #475569;">CUI / CIF *</label>
										<input type="text" name="company_cui" placeholder="RO12345678" style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 6px;">
									</div>
									<div>
										<label style="display: block; font-size: 0.75rem; font-weight: 800; color: #475569;">Nr. Reg. Comertului</label>
										<input type="text" name="company_reg" placeholder="J15/123/2020" style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 6px;">
									</div>
								</div>
							</div>

							<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
								<div>
									<label style="display: block; font-size: 0.75rem; font-weight: 800; color: #475569;">Nume Persoană Contact / Facturare *</label>
									<input type="text" name="lname" required value="<?php echo esc_attr( $current_user->last_name ); ?>" style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 6px;">
								</div>
								<div>
									<label style="display: block; font-size: 0.75rem; font-weight: 800; color: #475569;">Prenume *</label>
									<input type="text" name="fname" required value="<?php echo esc_attr( $current_user->first_name ); ?>" style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 6px;">
								</div>
							</div>

							<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
								<div>
									<label style="display: block; font-size: 0.75rem; font-weight: 800; color: #475569;">Email Factură *</label>
									<input type="email" name="email" required value="<?php echo esc_attr( $current_user->user_email ); ?>" style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 6px;">
								</div>
								<div>
									<label style="display: block; font-size: 0.75rem; font-weight: 800; color: #475569;">Telefon Factură *</label>
									<input type="tel" name="phone" required style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 6px;">
								</div>
							</div>

							<div style="margin-bottom: 12px;">
								<label style="display: block; font-size: 0.75rem; font-weight: 800; color: #475569;">Adresă Sediu Social / Domiciliu *</label>
								<input type="text" name="address" required placeholder="Strada, Număr, Oraș, Județ..." style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 6px;">
							</div>
						</div>

						<div style="background: #f0fdf4; border: 1px solid #bbf7d0; padding: 16px; border-radius: 8px; display: flex; flex-direction: column; gap: 12px;">
							<div style="display: flex; justify-content: space-between; align-items: center;">
								<div>
									<strong style="color: #166534; font-size: 1.1rem;">Abonament Anual: 149 LEI / an</strong>
									<div style="font-size: 0.78rem; color: #15803d;">Plată securizată ECOMPLEX.RO SRL</div>
								</div>
							<?php if ( $is_logged_in ) : ?>
								<button type="submit" name="submit_firma" class="btn btn-primary" style="padding: 12px 20px; font-weight: 800; font-size: 0.95rem; border-radius: 8px;">
									🚀 Trimite & Continuă &rarr;
								</button>
							<?php else : ?>
								<div style="flex: 1; text-align: right;">
									<p style="font-size: 0.88rem; font-weight: 700; color: #065f46; margin: 0 0 8px 0;">
										🔐 Conectează-te pentru a trimite formularul completat.
									</p>
									<a href="<?php echo esc_url( home_url( '/contul-meu/?redirect_to=' . urlencode( get_permalink() ) ) ); ?>" class="btn btn-primary" style="padding: 10px 20px; font-weight: 800; font-size: 0.9rem; display: inline-block;">
										👤 Conectează-te / Creează Cont Gratuit &rarr;
									</a>
								</div>
							<?php endif; ?>
							</div>
						</div>
					</form>
				</div>

				<!-- COLOANA DREAPTA: CARD BENEFICII AFACERE PE HARTA -->
				<div>
					<div class="card" style="padding: 24px; background: #ffffff; border: 2px solid #047857; border-radius: var(--border-radius-lg); box-shadow: var(--shadow-md); position: sticky; top: 90px;">
						
						<div style="text-align: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 16px; margin-bottom: 20px;">
							<span style="background: #d1fae5; color: #047857; font-weight: 900; font-size: 0.75rem; text-transform: uppercase; padding: 4px 12px; border-radius: 20px;">
								⭐ VIZIBILITATE LOCALĂ TOTALĂ
							</span>
							<div style="font-size: 2.25rem; font-weight: 900; color: #0f172a; margin-top: 10px; font-family: var(--font-heading);">
								149 LEI <span style="font-size: 0.9rem; font-weight: 600; color: #64748b;">/ an (365 zile)</span>
							</div>
							<p style="font-size: 0.8rem; color: #64748b; margin-top: 4px;">
								Echivalentul a doar <strong>~12.4 LEI / lună</strong> pentru promovarea afacerii tale în comună.
							</p>
						</div>

						<h4 style="font-size: 0.95rem; font-weight: 800; text-transform: uppercase; color: #0f172a; margin-bottom: 14px;">
							Ce include pachetul de vizibilitate:
						</h4>

						<ul style="list-style: none; padding: 0; margin: 0 0 20px 0; display: flex; flex-direction: column; gap: 12px; font-size: 0.88rem;">
							<li style="display: flex; align-items: flex-start; gap: 10px;">
								<span style="color: #047857; font-weight: 900; font-size: 1.1rem; line-height: 1;">✅</span>
								<div><strong>Pin Personalizat pe Harta Satelit</strong>: Poziționare exactă a afacerii cu iconiță și marcaj pe harta comunei Brezoaele.</div>
							</li>
							<li style="display: flex; align-items: flex-start; gap: 10px;">
								<span style="color: #047857; font-weight: 900; font-size: 1.1rem; line-height: 1;">✅</span>
								<div><strong>Profil Public Complet & Galerie Foto</strong>: Denumire firmă, Descriere, Telefoane, Galerie de până la 5 poze.</div>
							</li>
							<li style="display: flex; align-items: flex-start; gap: 10px;">
								<span style="color: #047857; font-weight: 900; font-size: 1.1rem; line-height: 1;">✅</span>
								<div><strong>Plată Flexibilă (Card, OP, Cash)</strong>: Plătești securizat cu cardul, transfer bancar BT sau numerar.</div>
							</li>
							<li style="display: flex; align-items: flex-start; gap: 10px;">
								<span style="color: #047857; font-weight: 900; font-size: 1.1rem; line-height: 1;">✅</span>
								<div><strong>Recomandare Prioritară către Cetățeni</strong>: Indexare în directorul local pentru locuitorii care caută meșteri sau servicii.</div>
							</li>
							<li style="display: flex; align-items: flex-start; gap: 10px;">
								<span style="color: #047857; font-weight: 900; font-size: 1.1rem; line-height: 1;">✅</span>
								<div><strong>Notificare de Reînnoire Automată</strong>: Te anunțăm prin email înainte de expirare pentru a păstra continuitatea.</div>
							</li>
						</ul>

						<!-- INFORMARE FISCALA ECOMPLEX.RO SRL -->
						<div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 14px; border-radius: 8px; font-size: 0.78rem; color: #475569; line-height: 1.4;">
							<strong>🧾 Informare Fiscală & Facturare:</strong><br/>
							Facturarea este realizată de către <strong>ECOMPLEX.RO SRL</strong>, societate neplătitoare de TVA (facturare cu TVA 0%). Factura fiscală se poate introduce în contabilitate sub formă de cheltuială deductibilă (fără TVA deductibil).
						</div>
					</div>
				</div>
			</div>

			<!-- JAVASCRIPT FOR LEAFLET PIN PICKER MAP & PAYMENT METHOD TOGGLE -->
			<script>
			document.addEventListener('DOMContentLoaded', function() {
				// Initialize Leaflet Map centered directly on Brezoaele
				var defaultLat = 44.561854;
				var defaultLng = 25.770593;
				
				var map = L.map('pin-picker-map').setView([defaultLat, defaultLng], 14);

				L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
					maxZoom: 19,
					attribution: '© OpenStreetMap'
				}).addTo(map);

				// Red Marker for Pin Picking
				var marker = L.marker([defaultLat, defaultLng], {
					draggable: true,
					title: 'Mută pinul pe locația afacerii'
				}).addTo(map);

				function updateCoords(lat, lng) {
					document.getElementById('latitude').value = lat.toFixed(6);
					document.getElementById('longitude').value = lng.toFixed(6);
				}

				marker.on('dragend', function(e) {
					var position = marker.getLatLng();
					updateCoords(position.lat, position.lng);
				});

				map.on('click', function(e) {
					marker.setLatLng(e.latlng);
					updateCoords(e.latlng.lat, e.latlng.lng);
				});

				// Toggle PJ/PF fields
				var billPf = document.getElementById('bill-pf');
				var billPj = document.getElementById('bill-pj');
				var pjFields = document.getElementById('pj-fields');

				function updateBill() {
					if (billPj.checked) {
						pjFields.style.display = 'block';
					} else {
						pjFields.style.display = 'none';
					}
				}

				if (billPf && billPj) {
					billPf.addEventListener('change', updateBill);
					billPj.addEventListener('change', updateBill);
				}
			});
			</script>



	</div>
</main>

<?php
get_footer();
