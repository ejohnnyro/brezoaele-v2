<?php
/**
 * Template Name: Adaugă Afacere / Producător
 *
 * @package Brezoaele_V2
 */

$error   = '';
$success = '';

// Procesare formular la trimitere (POST)
if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['submit_firma'] ) ) {
	
	// Verificare Nonce pentru securitate
	if ( isset( $_POST['firma_frontend_nonce'] ) && wp_verify_nonce( $_POST['firma_frontend_nonce'], 'submit_firma_action' ) ) {
		
		$title     = sanitize_text_field( $_POST['post_title'] );
		$content   = wp_kses_post( $_POST['post_content'] );
		$telefon   = sanitize_text_field( $_POST['locatie_telefon'] );
		$program   = sanitize_text_field( $_POST['locatie_program'] );
		$email     = sanitize_email( $_POST['locatie_email'] );
		$website   = esc_url_raw( $_POST['locatie_website'] );
		$persoana  = sanitize_text_field( $_POST['locatie_persoana_contact'] );
		$lat       = sanitize_text_field( $_POST['locatie_lat'] );
		$lng       = sanitize_text_field( $_POST['locatie_lng'] );
		$categorie = intval( $_POST['firma_categorie'] );
		
		// Validare
		if ( empty( $title ) || empty( $content ) || empty( $telefon ) || $categorie <= 0 ) {
			$error = 'Te rugăm să completezi câmpurile obligatorii: Numele Afacerii, Categorie, Descriere și Număr de Telefon.';
		} else {
			// Structura postării
			$new_firma = array(
				'post_title'   => $title,
				'post_content' => $content,
				'post_status'  => 'pending', // În așteptare de aprobare administrativă
				'post_type'    => 'firma',
			);
			
			// Inserare
			$post_id = wp_insert_post( $new_firma );
			
			if ( $post_id && ! is_wp_error( $post_id ) ) {
				// Salvare câmpuri custom meta
				update_post_meta( $post_id, '_locatie_telefon', $telefon );
				update_post_meta( $post_id, '_locatie_program', $program );
				if ( ! empty( $email ) ) {
					update_post_meta( $post_id, '_locatie_email', $email );
				}
				if ( ! empty( $website ) ) {
					update_post_meta( $post_id, '_locatie_website', $website );
				}
				if ( ! empty( $persoana ) ) {
					update_post_meta( $post_id, '_locatie_persoana_contact', $persoana );
				}
				if ( ! empty( $lat ) ) {
					update_post_meta( $post_id, '_locatie_lat', $lat );
				}
				if ( ! empty( $lng ) ) {
					update_post_meta( $post_id, '_locatie_lng', $lng );
				}
				
				// Salvare taxonomie (Categorie - tip_afacere)
				wp_set_post_terms( $post_id, array( $categorie ), 'tip_afacere' );
				
				// Gestionare upload imagine reprezentativă (Logo/Cover)
				if ( ! empty( $_FILES['firma_imagine']['name'] ) ) {
					require_once( ABSPATH . 'wp-admin/includes/image.php' );
					require_once( ABSPATH . 'wp-admin/includes/file.php' );
					require_once( ABSPATH . 'wp-admin/includes/media.php' );
					
					// Upload și atașare imagine la postare
					$attachment_id = media_handle_upload( 'firma_imagine', $post_id );
					
					if ( ! is_wp_error( $attachment_id ) ) {
						set_post_thumbnail( $post_id, $attachment_id );
					}
				}
				
				$success = 'Solicitarea de adăugare a fost trimisă cu succes! Afacerea ta va fi publicată pe hartă și în ghid imediat ce va fi verificată de administrator.';
			} else {
				$error = 'A apărut o eroare la salvare. Te rugăm să încerci din nou.';
			}
		}
	} else {
		$error = 'Sesiune expirată, te rugăm să reîncarci pagina și să încerci din nou.';
	}
}

get_header();
?>

<main id="primary" class="site-main" style="padding: 40px 0; background-color: var(--color-bg);">
	<div class="container">
		
		<div class="flat-form-card">
			<header style="margin-bottom: 24px; text-align: center;">
				<h1 style="font-size: 2.25rem; margin-bottom: 6px; font-weight: 800; font-family: var(--font-heading);">Solicită Adăugare Afacere Nouă</h1>
				<p style="color: var(--color-text-muted); font-size: 0.95rem;">Completează detaliile afacerii, serviciului sau activității tale de producător local pentru a apărea pe Harta Satelit.</p>
				<div style="width: 50px; height: 3px; background-color: var(--color-primary); margin: 12px auto 0 auto; border-radius: 3px;"></div>
			</header>

			<?php if ( ! empty( $error ) ) : ?>
				<div class="flat-alert-error">
					⚠️ <?php echo esc_html( $error ); ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $success ) ) : ?>
				<div class="flat-alert-success">
					✔️ <?php echo esc_html( $success ); ?>
				</div>
			<?php else : ?>
				
				<form method="post" enctype="multipart/form-data" class="flat-form">
					<?php wp_nonce_field( 'submit_firma_action', 'firma_frontend_nonce' ); ?>

					<!-- Nume Afacere -->
					<div class="flat-form-group">
						<label for="post_title" class="flat-form-label">Nume Afacere / Producător / Serviciu *</label>
						<input type="text" id="post_title" name="post_title" class="flat-form-input" placeholder="Ex: Farmacia Nouă Brezoaele, Solarul Popescu etc." required>
					</div>

					<!-- Categorie tip_afacere -->
					<div class="flat-form-group">
						<label for="firma_categorie" class="flat-form-label">Categorie *</label>
						<select id="firma_categorie" name="firma_categorie" class="flat-form-select" required>
							<option value="">Selectează Categoria</option>
							<?php
							$categories = get_terms( array(
								'taxonomy'   => 'tip_afacere',
								'hide_empty' => false,
							) );
							foreach ( $categories as $cat ) {
								echo '<option value="' . esc_attr( $cat->term_id ) . '">' . esc_html( $cat->name ) . '</option>';
							}
							?>
						</select>
					</div>

					<!-- Telefon -->
					<div class="flat-form-group">
						<label for="locatie_telefon" class="flat-form-label">Număr de Telefon Contact *</label>
						<input type="tel" id="locatie_telefon" name="locatie_telefon" class="flat-form-input" placeholder="Ex: 0722000000" required>
					</div>

					<!-- Program de Lucru -->
					<div class="flat-form-group">
						<label for="locatie_program" class="flat-form-label">Program de Lucru</label>
						<input type="text" id="locatie_program" name="locatie_program" class="flat-form-input" placeholder="Ex: Luni - Vineri: 08:00 - 20:00, Sâmbătă: Închis">
					</div>

					<!-- Email -->
					<div class="flat-form-group">
						<label for="locatie_email" class="flat-form-label">Adresă Email</label>
						<input type="email" id="locatie_email" name="locatie_email" class="flat-form-input" placeholder="Ex: contact@afacerea-ta.ro">
					</div>

					<!-- Website / Social -->
					<div class="flat-form-group">
						<label for="locatie_website" class="flat-form-label">Website / Pagina Socială</label>
						<input type="url" id="locatie_website" name="locatie_website" class="flat-form-input" placeholder="Ex: https://facebook.com/afacerea-ta">
					</div>

					<!-- Persoană de contact -->
					<div class="flat-form-group">
						<label for="locatie_persoana_contact" class="flat-form-label">Persoană de Contact</label>
						<input type="text" id="locatie_persoana_contact" name="locatie_persoana_contact" class="flat-form-input" placeholder="Numele persoanei de contact / reprezentantului">
					</div>

					<!-- Descriere -->
					<div class="flat-form-group">
						<label for="post_content" class="flat-form-label">Descriere Activitate / Servicii *</label>
						<textarea id="post_content" name="post_content" class="flat-form-textarea" rows="5" placeholder="Descrie serviciile oferite, produsele vândute și alte detalii utile..." required></textarea>
					</div>

					<!-- Coordonate Latitudine (Opțional) -->
					<div class="flat-form-group">
						<label for="locatie_lat" class="flat-form-label">Latitudine (Opțional)</label>
						<input type="text" id="locatie_lat" name="locatie_lat" class="flat-form-input" placeholder="Ex: 44.5714 (lasă gol dacă nu le cunoști)">
					</div>

					<!-- Coordonate Longitudine (Opțional) -->
					<div class="flat-form-group">
						<label for="locatie_lng" class="flat-form-label">Longitudine (Opțional)</label>
						<input type="text" id="locatie_lng" name="locatie_lng" class="flat-form-input" placeholder="Ex: 25.7936 (lasă gol dacă nu le cunoști)">
					</div>

					<!-- Upload Logo/Imagine -->
					<div class="flat-form-group">
						<label for="firma_imagine" class="flat-form-label">Imagine Reprezentativă / Logo</label>
						<input type="file" id="firma_imagine" name="firma_imagine" class="flat-form-file" accept="image/*">
					</div>

					<button type="submit" name="submit_firma" class="btn btn-primary" style="margin-top: 10px; width: 100%;">
						Trimite Solicitarea de Adăugare
					</button>
				</form>
			<?php endif; ?>

		</div>
		
	</div>
</main>

<?php
get_footer();
