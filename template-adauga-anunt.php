<?php
/**
 * Template Name: Adaugă Anunț
 *
 * @package Brezoaele_V2
 */

$error   = '';
$success = '';

// Procesare formular la trimitere (POST)
if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['submit_anunt'] ) ) {
	
	// Verificare Nonce pentru securitate
	if ( isset( $_POST['anunt_frontend_nonce'] ) && wp_verify_nonce( $_POST['anunt_frontend_nonce'], 'submit_anunt_action' ) ) {
		
		$title     = sanitize_text_field( $_POST['post_title'] );
		$content   = wp_kses_post( $_POST['post_content'] );
		$pret      = sanitize_text_field( $_POST['anunt_pret'] );
		$telefon   = sanitize_text_field( $_POST['anunt_telefon'] );
		$locatie   = sanitize_text_field( $_POST['anunt_locatie'] );
		$nume      = sanitize_text_field( $_POST['anunt_nume'] );
		$categorie = intval( $_POST['anunt_categorie'] );
		
		// Validare
		if ( empty( $title ) || empty( $content ) || empty( $telefon ) ) {
			$error = 'Te rugăm să completezi câmpurile obligatorii: Titlu, Descriere și Număr de Telefon.';
		} else {
			// Structura postării
			$new_anunt = array(
				'post_title'   => $title,
				'post_content' => $content,
				'post_status'  => 'pending', // În așteptare de aprobare
				'post_type'    => 'anunt',
			);
			
			// Inserare
			$post_id = wp_insert_post( $new_anunt );
			
			if ( $post_id && ! is_wp_error( $post_id ) ) {
				// Salvare câmpuri custom meta
				update_post_meta( $post_id, '_anunt_pret', $pret );
				update_post_meta( $post_id, '_anunt_telefon', $telefon );
				update_post_meta( $post_id, '_anunt_locatie', $locatie );
				update_post_meta( $post_id, '_anunt_nume', $nume );
				
				// Salvare taxonomie (Categorie)
				if ( $categorie > 0 ) {
					wp_set_post_terms( $post_id, array( $categorie ), 'categorie_anunt' );
				}
				
				// Gestionare upload imagine reprezentativă
				if ( ! empty( $_FILES['anunt_imagine']['name'] ) ) {
					require_once( ABSPATH . 'wp-admin/includes/image.php' );
					require_once( ABSPATH . 'wp-admin/includes/file.php' );
					require_once( ABSPATH . 'wp-admin/includes/media.php' );
					
					// Upload și atașare imagine la postare
					$attachment_id = media_handle_upload( 'anunt_imagine', $post_id );
					
					if ( ! is_wp_error( $attachment_id ) ) {
						set_post_thumbnail( $post_id, $attachment_id );
					}
				}
				
				$success = 'Anunțul tău a fost trimis cu succes! Va fi publicat imediat ce va fi verificat de administrator.';
			} else {
				$error = 'A apărut o eroare la salvarea anunțului. Te rugăm să încerci din nou.';
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
				<h1 style="font-size: 2.25rem; margin-bottom: 6px; font-weight: 800; font-family: var(--font-heading);">Adaugă Anunț Gratuit</h1>
				<p style="color: var(--color-text-muted); font-size: 0.95rem;">Completează detaliile de mai jos. Anunțul va fi publicat după ce va fi aprobat.</p>
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
					<?php wp_nonce_field( 'submit_anunt_action', 'anunt_frontend_nonce' ); ?>

					<!-- Titlu Anunț -->
					<div class="flat-form-group">
						<label for="post_title" class="flat-form-label">Titlu Anunț *</label>
						<input type="text" id="post_title" name="post_title" class="flat-form-input" placeholder="Ex: Vând tractor U650 în stare perfectă" required>
					</div>

					<!-- Categorie -->
					<div class="flat-form-group">
						<label for="anunt_categorie" class="flat-form-label">Categorie Anunț</label>
						<select id="anunt_categorie" name="anunt_categorie" class="flat-form-select">
							<option value="0">Selectează Categoria</option>
							<?php
							$categories = get_terms( array(
								'taxonomy'   => 'categorie_anunt',
								'hide_empty' => false,
							) );
							foreach ( $categories as $cat ) {
								echo '<option value="' . esc_attr( $cat->term_id ) . '">' . esc_html( $cat->name ) . '</option>';
							}
							?>
						</select>
					</div>

					<!-- Preț -->
					<div class="flat-form-group">
						<label for="anunt_pret" class="flat-form-label">Preț</label>
						<input type="text" id="anunt_pret" name="anunt_pret" class="flat-form-input" placeholder="Ex: 8 RON/kg sau Negociabil">
					</div>

					<!-- Descriere -->
					<div class="flat-form-group">
						<label for="post_content" class="flat-form-label">Descriere Detaliată *</label>
						<textarea id="post_content" name="post_content" class="flat-form-textarea" rows="5" placeholder="Descrie produsul, serviciul sau oferta în detaliu..." required></textarea>
					</div>

					<!-- Nume Contact -->
					<div class="flat-form-group">
						<label for="anunt_nume" class="flat-form-label">Nume Contact</label>
						<input type="text" id="anunt_nume" name="anunt_nume" class="flat-form-input" placeholder="Numele tău">
					</div>

					<!-- Telefon Contact -->
					<div class="flat-form-group">
						<label for="anunt_telefon" class="flat-form-label">Număr de Telefon Contact *</label>
						<input type="tel" id="anunt_telefon" name="anunt_telefon" class="flat-form-input" placeholder="Ex: 0722000000" required>
					</div>

					<!-- Locație -->
					<div class="flat-form-group">
						<label for="anunt_locatie" class="flat-form-label">Locație</label>
						<input type="text" id="anunt_locatie" name="anunt_locatie" class="flat-form-input" placeholder="Ex: Brezoaele">
					</div>

					<!-- Upload Imagine -->
					<div class="flat-form-group">
						<label for="anunt_imagine" class="flat-form-label">Atașează o Imagine</label>
						<input type="file" id="anunt_imagine" name="anunt_imagine" class="flat-form-file" accept="image/*">
					</div>

					<button type="submit" name="submit_anunt" class="btn btn-primary" style="margin-top: 10px; width: 100%;">
						Trimite Anunțul spre Publicare
					</button>
				</form>
			<?php endif; ?>

		</div>
		
	</div>
</main>

<?php
get_footer();
