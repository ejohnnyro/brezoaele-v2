<?php
/**
 * Template Name: Trimite Sesizare Civicã
 *
 * @package Brezoaele_V2
 */

$error   = '';
$success = '';

// Procesare formular la trimitere (POST)
if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['submit_sesizare'] ) ) {
	
	// Verificare Nonce pentru securitate
	if ( isset( $_POST['sesizare_frontend_nonce'] ) && wp_verify_nonce( $_POST['sesizare_frontend_nonce'], 'submit_sesizare_action' ) ) {
		
		$nume    = sanitize_text_field( $_POST['sesizare_nume'] );
		$email   = sanitize_email( $_POST['sesizare_email'] );
		$telefon = sanitize_text_field( $_POST['sesizare_telefon'] );
		$title   = sanitize_text_field( $_POST['post_title'] );
		$content = wp_kses_post( $_POST['post_content'] );
		
		// Validare simple
		if ( empty( $nume ) || empty( $email ) || empty( $telefon ) || empty( $title ) || empty( $content ) ) {
			$error = 'Te rugăm să completezi toate câmpurile obligatorii.';
		} elseif ( ! is_email( $email ) ) {
			$error = 'Adresa de email introdusă nu este validă.';
		} else {
			// Structura postării
			$new_sesizare = array(
				'post_title'   => $title,
				'post_content' => $content,
				'post_status'  => 'private', // Întotdeauna salvată privată în baza de date
				'post_type'    => 'sesizare',
			);
			
			// Inserare în baza de date
			$post_id = wp_insert_post( $new_sesizare );
			
			if ( $post_id && ! is_wp_error( $post_id ) ) {
				// Salvare metadate
				update_post_meta( $post_id, '_sesizare_nume', $nume );
				update_post_meta( $post_id, '_sesizare_email', $email );
				update_post_meta( $post_id, '_sesizare_telefon', $telefon );
				update_post_meta( $post_id, '_sesizare_stare', 'Nouă' ); // Starea inițială
				
				// Gestionare upload imagine (dacă există)
				if ( ! empty( $_FILES['sesizare_imagine']['name'] ) ) {
					require_once( ABSPATH . 'wp-admin/includes/image.php' );
					require_once( ABSPATH . 'wp-admin/includes/file.php' );
					require_once( ABSPATH . 'wp-admin/includes/media.php' );
					
					// Upload și atașare imagine la postare
					$attachment_id = media_handle_upload( 'sesizare_imagine', $post_id );
					
					if ( ! is_wp_error( $attachment_id ) ) {
						set_post_thumbnail( $post_id, $attachment_id );
					}
				}
				
				// Trimite notificare prin email către administrator
				$admin_email = get_option( 'admin_email' );
				$subject     = '[Sesizare Civică Nouă] - ' . $title;
				$message     = "O nouă sesizare a fost înregistrată pe portalul Brezoaele.ro:\n\n";
				$message    .= "Titlu: " . $title . "\n";
				$message    .= "Solicitant: " . $nume . "\n";
				$message    .= "Email: " . $email . "\n";
				$message    .= "Telefon: " . $telefon . "\n\n";
				$message    .= "Descrierea problemei:\n" . $_POST['post_content'] . "\n\n";
				$message    .= "Accesează panoul administrativ pentru a gestiona solicitarea.";
				
				wp_mail( $admin_email, $subject, $message );
				
				$success = 'Sesizarea ta a fost înregistrată cu succes în mod securizat! O vom analiza în cel mai scurt timp.';
			} else {
				$error = 'A apărut o eroare la înregistrarea sesizării. Te rugăm să încerci din nou.';
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
				<h1 style="font-size: 2.25rem; margin-bottom: 6px; font-weight: 800; font-family: var(--font-heading);">Sesizează o Problemă</h1>
				<p style="color: var(--color-text-muted); font-size: 0.95rem;">Ai observat o neregulă în comună? Trimite-ne detaliile pentru a fi redirecționate către departamentul corespunzător.</p>
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
					<?php wp_nonce_field( 'submit_sesizare_action', 'sesizare_frontend_nonce' ); ?>

					<!-- Nume Solicitant -->
					<div class="flat-form-group">
						<label for="sesizare_nume" class="flat-form-label">Numele tău complet *</label>
						<input type="text" id="sesizare_nume" name="sesizare_nume" class="flat-form-input" required placeholder="Ex: Popescu Ion">
					</div>

					<!-- Email -->
					<div class="flat-form-group">
						<label for="sesizare_email" class="flat-form-label">Adresa de email *</label>
						<input type="email" id="sesizare_email" name="sesizare_email" class="flat-form-input" required placeholder="Ex: adresa@email.com">
						<span style="font-size: 0.75rem; color: var(--color-text-muted);">Vom folosi adresa de email pentru a-ți trimite actualizări privind statusul sesizării.</span>
					</div>

					<!-- Telefon -->
					<div class="flat-form-group">
						<label for="sesizare_telefon" class="flat-form-label">Număr de telefon contact *</label>
						<input type="tel" id="sesizare_telefon" name="sesizare_telefon" class="flat-form-input" required placeholder="Ex: 0722000000">
					</div>

					<!-- Titlu Sesizare / Localizare -->
					<div class="flat-form-group">
						<label for="post_title" class="flat-form-label">Despre ce este vorba / Localizare *</label>
						<input type="text" id="post_title" name="post_title" class="flat-form-input" required placeholder="Ex: Bec ars pe stâlpul nr. 22 - str. Florilor">
					</div>

					<!-- Descriere Problemă -->
					<div class="flat-form-group">
						<label for="post_content" class="flat-form-label">Descrierea problemei în detaliu *</label>
						<textarea id="post_content" name="post_content" class="flat-form-textarea" rows="5" required placeholder="Te rugăm să descrii problema cât mai clar și exact cu putință..."></textarea>
					</div>

					<!-- Upload Imagine -->
					<div class="flat-form-group">
						<label for="sesizare_imagine" class="flat-form-label">Adaugă o fotografie doveditoare (opțional)</label>
						<input type="file" id="sesizare_imagine" name="sesizare_imagine" class="flat-form-file" accept="image/*">
					</div>

					<button type="submit" name="submit_sesizare" class="btn btn-primary" style="margin-top: 10px; width: 100%;">
						Trimite Sesizarea Securizat
					</button>
				</form>
			<?php endif; ?>

		</div>
		
	</div>
</main>

<?php
get_footer();
