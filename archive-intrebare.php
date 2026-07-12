<?php
/**
 * The template for displaying archive pages for questions (comunitate).
 *
 * @package Brezoaele_V2
 */

$error   = '';
$success = '';

// Procesare trimitere subiect nou în forum (POST)
if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['submit_intrebare'] ) ) {
	if ( is_user_logged_in() ) {
		if ( isset( $_POST['intrebare_nonce'] ) && wp_verify_nonce( $_POST['intrebare_nonce'], 'submit_intrebare_action' ) ) {
			$title   = sanitize_text_field( $_POST['intrebare_title'] );
			$content = wp_kses_post( $_POST['intrebare_content'] );

			if ( empty( $title ) || empty( $content ) ) {
				$error = 'Te rugăm să completezi atât titlul, cât și detaliile subiectului.';
			} else {
				$new_post = array(
					'post_title'   => $title,
					'post_content' => $content,
					'post_status'  => 'publish',
					'post_type'    => 'intrebare',
					'post_author'  => get_current_user_id(),
				);

				$post_id = wp_insert_post( $new_post );

				if ( $post_id && ! is_wp_error( $post_id ) ) {
					wp_redirect( get_permalink( $post_id) );
					exit;
				} else {
					$error = 'A apărut o eroare la salvarea discuției. Încearcă din nou.';
				}
			}
		} else {
			$error = 'Sesiune expirată. Reîncarcă pagina.';
		}
	} else {
		$error = 'Trebuie să fii autentificat pentru a propune un subiect.';
	}
}

get_header();
?>

<main id="primary" class="site-main" style="padding: 40px 0; background-color: var(--color-bg);">
	<div class="container">
		
		<header class="page-header" style="margin-bottom: 30px; text-align: center;">
			<h1 class="page-title" style="font-size: 2.5rem; margin-bottom: 8px;">Forumul Comunității</h1>
			<p style="color: var(--color-text-muted); font-size: 1rem; max-width: 600px; margin: 0 auto;">Pune întrebări, cere recomandări de la vecini și participă la dezbaterile locale din Brezoaele.</p>
			<div style="width: 50px; height: 3px; background-color: var(--color-primary); margin: 12px auto 0 auto; border-radius: 3px;"></div>
		</header>

		<!-- Trimitere Erori/Succes -->
		<?php if ( ! empty( $error ) ) : ?>
			<div class="flat-alert-error">
				⚠️ <?php echo esc_html( $error ); ?>
			</div>
		<?php endif; ?>

		<!-- Zona Acțiune Forum (Adăugare / Autentificare) -->
		<div style="text-align: center; margin-bottom: 30px;">
			<?php if ( is_user_logged_in() ) : ?>
				<button id="toggle-form-btn" class="btn btn-primary">
					➕ Propune un Subiect Nou
				</button>
			<?php else : ?>
				<a href="<?php echo esc_url( wp_login_url( get_post_type_archive_link( 'intrebare' ) ) ); ?>" class="btn btn-primary" style="background-color: var(--color-secondary); border-color: var(--color-secondary);">
					🔑 Autentifică-te pentru a deschide un subiect
				</a>
			<?php endif; ?>
		</div>

		<!-- Formularul de Adăugare Subiect (ascuns implicit) -->
		<?php if ( is_user_logged_in() ) : ?>
			<div id="new-topic-form" class="flat-form-card" style="display: none;">
				<h3 class="flat-form-title">Deschide o discuție nouă în comunitate</h3>
				
				<form method="post" class="flat-form">
					<?php wp_nonce_field( 'submit_intrebare_action', 'intrebare_nonce' ); ?>
					
					<div class="flat-form-group">
						<label for="intrebare_title" class="flat-form-label">Titlul discuției / Întrebarea ta *</label>
						<input type="text" id="intrebare_title" name="intrebare_title" class="flat-form-input" required placeholder="Ex: Căutare instalator autorizat">
					</div>
					
					<div class="flat-form-group">
						<label for="intrebare_content" class="flat-form-label">Descrie problema în detaliu *</label>
						<textarea id="intrebare_content" name="intrebare_content" class="flat-form-textarea" rows="5" required placeholder="Oferă toate detaliile necesare pentru ca vecinii să te poată ajuta..."></textarea>
					</div>
					
					<button type="submit" name="submit_intrebare" class="btn btn-primary" style="width: 100%;">
						Postează Subiectul pe Forum
					</button>
				</form>
			</div>
		<?php endif; ?>

		<!-- Listare Subiecte din Forum -->
		<div style="max-width: 800px; margin: 0 auto; display: flex; flex-direction: column; gap: 16px;">
			<?php
			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
					$answers_count = get_comments_number();
			?>
					<article class="card" style="display: flex; justify-content: space-between; align-items: center; gap: 20px; padding: 16px 20px;">
						<div style="flex: 1;">
							<span style="font-size: 0.75rem; color: var(--color-text-muted); font-weight: 500;">
								Întrebare deschisă de <b><?php the_author(); ?></b> la <?php echo get_the_date( 'd.m.Y' ); ?>
							</span>
							<h3 style="margin: 4px 0 0 0; font-size: 1.2rem; font-family: var(--font-heading); line-height: 1.3;">
								<a href="<?php the_permalink(); ?>" style="color: var(--color-text-dark); text-decoration: none;"><?php the_title(); ?></a>
							</h3>
							<p style="color: var(--color-text-muted); font-size: 0.9rem; margin-top: 4px; line-height: 1.4;">
								<?php echo wp_trim_words( get_the_excerpt(), 18, '...' ); ?>
							</p>
						</div>
						
						<div style="text-align: center; background-color: var(--color-primary-light); padding: 10px 14px; border: 1px solid var(--color-primary-light); min-width: 90px; border-radius: var(--border-radius-md); box-shadow: var(--shadow-sm);">
							<span style="font-size: 1.4rem; display: block; font-weight: 900; color: var(--color-primary-dark); line-height: 1;">
								<?php echo $answers_count; ?>
							</span>
							<span style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: var(--color-primary-dark);">
								<?php echo _n( 'Răspuns', 'Răspunsuri', $answers_count, 'brezoaele-v2' ); ?>
							</span>
						</div>
					</article>
			<?php
				endwhile;
			else :
			?>
				<div style="text-align: center; padding: 40px 0; background-color: var(--color-card); border: 1px solid var(--color-border); border-radius: var(--border-radius-lg);">
					<div style="font-size: 3rem; margin-bottom: 12px;">💬</div>
					<h3>Momentan nu sunt discuții active în comunitate</h3>
					<p style="color: var(--color-text-muted); font-size: 0.95rem;">Fii primul care pornește o dezbatere locală!</p>
				</div>
			<?php endif; ?>
		</div>

		<div style="margin-top: 32px; display: flex; justify-content: center;">
			<?php the_posts_pagination( array(
				'mid_size'  => 2,
				'prev_text' => __( '&larr; Înapoi', 'brezoaele-v2' ),
				'next_text' => __( 'Înainte &rarr;', 'brezoaele-v2' ),
			) ); ?>
		</div>

	</div>
</main>

<?php
get_footer();
