<?php
/**
 * Template Name: Editează Anunț
 * @package Brezoaele_V2
 *
 * URL: /editeaza-anunt/?anunt_id=123
 */

get_header();

$is_logged_in = is_user_logged_in();
$current_user = wp_get_current_user();
$errors       = array();
$success_msg  = '';

// Get the announcement ID to edit
$anunt_id = intval( $_GET['anunt_id'] ?? 0 );
$anunt    = $anunt_id ? get_post( $anunt_id ) : null;

// Security: must be logged in, post must exist, must be owner, must be 'anunt' type
$can_edit = (
	$is_logged_in &&
	$anunt &&
	'anunt' === $anunt->post_type &&
	(int) $anunt->post_author === (int) $current_user->ID
);

// Process form submission (update)
if ( $can_edit && $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['update_anunt'] ) ) {
	if ( ! isset( $_POST['anunt_edit_nonce'] ) || ! wp_verify_nonce( $_POST['anunt_edit_nonce'], 'edit_anunt_action_' . $anunt_id ) ) {
		$errors[] = 'Token de securitate invalid. Te rugăm să încerci din nou.';
	}

	$title    = sanitize_text_field( $_POST['post_title'] ?? '' );
	$category = intval( $_POST['anunt_category'] ?? 0 );
	$content  = wp_kses_post( $_POST['post_content'] ?? '' );
	$pret     = sanitize_text_field( $_POST['pret'] ?? '' );
	$telefon  = sanitize_text_field( $_POST['telefon'] ?? '' );
	$locatie  = sanitize_text_field( $_POST['locatie'] ?? 'Brezoaele' );

	if ( empty( $title ) ) {
		$errors[] = 'Titlul anunțului este obligatoriu.';
	}
	if ( empty( $content ) ) {
		$errors[] = 'Descrierea anunțului este obligatorie.';
	}

	if ( empty( $errors ) ) {
		// Update post
		wp_update_post( array(
			'ID'           => $anunt_id,
			'post_title'   => $title,
			'post_content' => $content,
		) );

		// Update taxonomy
		if ( $category > 0 ) {
			wp_set_post_terms( $anunt_id, array( $category ), 'categorie_anunt' );
		}

		// Update meta
		update_post_meta( $anunt_id, '_anunt_pret', $pret );
		update_post_meta( $anunt_id, '_anunt_telefon', $telefon );
		update_post_meta( $anunt_id, '_anunt_locatie', $locatie );

		// Handle new photo uploads (append to existing gallery)
		$is_premium = get_post_meta( $anunt_id, '_anunt_is_premium', true );
		$max_photos = ( '1' === $is_premium ) ? 10 : 3;

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

					// Auto-compress to max 1080x1080px (preserve aspect ratio)
					$editor = wp_get_image_editor( $file['tmp_name'] );
					if ( ! is_wp_error( $editor ) ) {
						$editor->resize( 1080, 1080, false );
						$editor->set_quality( 85 );
						$editor->save( $file['tmp_name'] );
					}

					$_FILES['single_photo'] = $file;
					$attach_id = media_handle_upload( 'single_photo', $anunt_id );
					// Set first new upload as featured if no thumbnail set yet
					if ( ! is_wp_error( $attach_id ) && ! has_post_thumbnail( $anunt_id ) ) {
						set_post_thumbnail( $anunt_id, $attach_id );
					}
				}
			}
		}

		// Handle featured image change
		if ( ! empty( $_FILES['featured_image']['name'] ) && ! $_FILES['featured_image']['error'] ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/media.php';

			$feat_editor = wp_get_image_editor( $_FILES['featured_image']['tmp_name'] );
			if ( ! is_wp_error( $feat_editor ) ) {
				$feat_editor->resize( 1080, 1080, false );
				$feat_editor->set_quality( 85 );
				$feat_editor->save( $_FILES['featured_image']['tmp_name'] );
			}
			$_FILES['single_featured'] = $_FILES['featured_image'];
			$feat_attach_id = media_handle_upload( 'single_featured', $anunt_id );
			if ( ! is_wp_error( $feat_attach_id ) ) {
				set_post_thumbnail( $anunt_id, $feat_attach_id );
			}
		}

		$success_msg = 'Anunțul a fost actualizat cu succes!';
		// Refresh post data
		$anunt = get_post( $anunt_id );
	}
}

// Pre-populate existing values
$existing_title   = $anunt ? $anunt->post_title : '';
$existing_content = $anunt ? $anunt->post_content : '';
$existing_pret    = $anunt ? get_post_meta( $anunt_id, '_anunt_pret', true ) : '';
$existing_telefon = $anunt ? get_post_meta( $anunt_id, '_anunt_telefon', true ) : '';
$existing_locatie = $anunt ? get_post_meta( $anunt_id, '_anunt_locatie', true ) : 'Brezoaele';
$existing_cats    = $anunt ? wp_get_post_terms( $anunt_id, 'categorie_anunt', array( 'fields' => 'ids' ) ) : array();
$existing_cat_id  = ! empty( $existing_cats ) ? $existing_cats[0] : 0;
$is_premium       = $anunt ? get_post_meta( $anunt_id, '_anunt_is_premium', true ) : '0';
$max_photos       = ( '1' === $is_premium ) ? 10 : 3;
?>

<main id="primary" class="site-main" style="padding: 40px 0; background-color: var(--color-bg);">
	<div class="container">

		<?php if ( ! $is_logged_in ) : ?>
			<div class="card" style="max-width: 600px; margin: 0 auto; padding: 40px 30px; text-align: center; background: #ffffff; border: 1px solid var(--color-border); border-radius: var(--border-radius-lg); box-shadow: 0 10px 25px rgba(0,0,0,0.04);">
				<div style="font-size: 3rem; margin-bottom: 12px;">🔒</div>
				<h1 style="font-size: 1.6rem; font-weight: 900; font-family: var(--font-heading); margin-bottom: 8px;">
					Autentificare Necesară
				</h1>
				<p style="color: var(--color-text-muted); font-size: 0.95rem; margin-bottom: 24px;">
					Trebuie să fii conectat în contul tău pentru a edita un anunț.
				</p>
				<a href="<?php echo esc_url( home_url( '/contul-meu/' ) ); ?>" class="btn btn-primary" style="padding: 12px 24px; font-weight: 800; font-size: 0.95rem;">
					Conectează-te →
				</a>
			</div>

		<?php elseif ( ! $can_edit ) : ?>
			<div class="card" style="max-width: 600px; margin: 0 auto; padding: 40px 30px; text-align: center; background: #ffffff; border: 1px solid #fca5a5; border-radius: var(--border-radius-lg);">
				<div style="font-size: 3rem; margin-bottom: 12px;">⛔</div>
				<h1 style="font-size: 1.4rem; font-weight: 900; font-family: var(--font-heading); margin-bottom: 8px; color: #991b1b;">
					Anunț inexistent sau acces interzis
				</h1>
				<p style="color: var(--color-text-muted); font-size: 0.9rem; margin-bottom: 20px;">
					Nu ai permisiunea de a edita acest anunț sau acesta nu există.
				</p>
				<a href="<?php echo esc_url( home_url( '/contul-meu/' ) ); ?>" class="btn btn-primary" style="padding: 10px 20px; font-weight: 800;">
					← Înapoi la Contul Meu
				</a>
			</div>

		<?php else : ?>

			<header class="page-header" style="margin-bottom: 24px;">
				<div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 6px;">
					<a href="<?php echo esc_url( home_url( '/contul-meu/' ) ); ?>" style="color: var(--color-primary); font-weight: 700; font-size: 0.85rem; text-decoration: none;">
						← Contul Meu
					</a>
					<span style="color: var(--color-border);">/</span>
					<span style="font-size: 0.85rem; color: var(--color-text-muted);">Editează Anunț</span>
				</div>
				<h1 class="page-title" style="font-size: 2rem; font-weight: 900; font-family: var(--font-heading); margin-bottom: 6px;">
					✏️ Editează Anunțul
				</h1>
				<p style="color: var(--color-text-muted); font-size: 0.95rem; margin: 0;">
					Actualizează detaliile anunțului: <strong><?php echo esc_html( $existing_title ); ?></strong>
				</p>
			</header>

			<?php if ( ! empty( $success_msg ) ) : ?>
				<div style="padding: 16px; background: #ecfdf5; border: 1px solid #a7f3d0; color: #047857; border-radius: 8px; font-weight: 700; font-size: 0.95rem; margin-bottom: 24px;">
					✔ <?php echo esc_html( $success_msg ); ?> <a href="<?php echo esc_url( get_permalink( $anunt_id ) ); ?>" style="text-decoration: underline; margin-left: 8px;">Vezi anunțul publicat →</a>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $errors ) ) : ?>
				<div style="padding: 16px; background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; border-radius: 8px; font-size: 0.9rem; margin-bottom: 24px;">
					<ul style="margin: 0; padding-left: 16px;">
						<?php foreach ( $errors as $err ) : ?>
							<li>❌ <?php echo esc_html( $err ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<!-- FORM -->
			<div style="display: grid; grid-template-columns: 1fr; gap: 24px;" class="edit-anunt-grid">
				<style>
					@media (min-width: 992px) {
						.edit-anunt-grid {
							grid-template-columns: 7fr 5fr !important;
						}
					}
				</style>

				<!-- LEFT: EDIT FORM -->
				<div>
					<form method="post" enctype="multipart/form-data" class="card" style="padding: 28px; background: #ffffff; border: 1px solid var(--color-border); border-radius: var(--border-radius-lg); display: flex; flex-direction: column; gap: 20px;">
						<?php wp_nonce_field( 'edit_anunt_action_' . $anunt_id, 'anunt_edit_nonce' ); ?>

						<?php if ( '1' === $is_premium ) : ?>
							<div style="background: #fefce8; border: 1px solid #fde047; padding: 10px 14px; border-radius: 8px; font-size: 0.85rem; color: #b45309; font-weight: 700;">
								⭐ Anunț PREMIUM — Permise până la 10 fotografii HD.
							</div>
						<?php else : ?>
							<div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 10px 14px; border-radius: 8px; font-size: 0.85rem; color: #475569;">
								📢 Anunț Gratuit — Permise maxim 3 fotografii.
							</div>
						<?php endif; ?>

						<!-- TITLU -->
						<div>
							<label for="post_title" style="display: block; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); margin-bottom: 4px;">Titlu Anunț *</label>
							<input type="text" id="post_title" name="post_title" required
								value="<?php echo esc_attr( $existing_title ); ?>"
								style="width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; box-sizing: border-box;">
						</div>

						<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
							<!-- CATEGORIE -->
							<div>
								<label for="anunt_category" style="display: block; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); margin-bottom: 4px;">Categorie</label>
								<?php
								wp_dropdown_categories( array(
									'show_option_none' => '-- Alege Categorie --',
									'option_none_value'=> 0,
									'taxonomy'         => 'categorie_anunt',
									'name'             => 'anunt_category',
									'id'               => 'anunt_category',
									'selected'         => $existing_cat_id,
									'style'            => 'width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem;',
									'hide_empty'       => 0,
									'hierarchical'     => 1,
									'orderby'          => 'name',
									'order'            => 'ASC',
								) );
								?>
							</div>
							<!-- PRET -->
							<div>
								<label for="pret" style="display: block; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); margin-bottom: 4px;">Preț (LEI / EUR)</label>
								<input type="text" id="pret" name="pret"
									value="<?php echo esc_attr( $existing_pret ); ?>"
									placeholder="Ex: 15.000 EUR sau Negociabil"
									style="width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; box-sizing: border-box;">
							</div>
						</div>

						<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
							<!-- TELEFON -->
							<div>
								<label for="telefon" style="display: block; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); margin-bottom: 4px;">Telefon Contact *</label>
								<input type="tel" id="telefon" name="telefon" required
									value="<?php echo esc_attr( $existing_telefon ); ?>"
									placeholder="07xx xxx xxx"
									style="width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; box-sizing: border-box;">
							</div>
							<!-- LOCATIE -->
							<div>
								<label for="locatie" style="display: block; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); margin-bottom: 4px;">Locație</label>
								<input type="text" id="locatie" name="locatie"
									value="<?php echo esc_attr( $existing_locatie ); ?>"
									style="width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; box-sizing: border-box;">
							</div>
						</div>

						<!-- DESCRIERE -->
						<div>
							<label for="post_content" style="display: block; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); margin-bottom: 4px;">Descriere Anunț *</label>
							<textarea id="post_content" name="post_content" rows="6" required
								style="width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; box-sizing: border-box;"><?php echo esc_textarea( $existing_content ); ?></textarea>
						</div>

						<!-- IMAGINE REPREZENTATIVA (înlocuire) -->
						<div>
							<label for="featured_image" style="display: block; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); margin-bottom: 4px;">
								🖼️ Înlocuiește Imaginea Principală (opțional)
							</label>
							<?php if ( has_post_thumbnail( $anunt_id ) ) : ?>
								<div style="margin-bottom: 8px;">
									<?php echo get_the_post_thumbnail( $anunt_id, array( 120, 80 ), array( 'style' => 'border-radius: 6px; border: 1px solid #e2e8f0;' ) ); ?>
									<span style="font-size: 0.78rem; color: #64748b; margin-left: 8px;">Imaginea curentă</span>
								</div>
							<?php endif; ?>
							<input type="file" id="featured_image" name="featured_image" accept="image/*"
								style="width: 100%; padding: 10px; border: 1.5px dashed #cbd5e1; border-radius: 8px; background: #f8fafc;">
							<div style="font-size: 0.78rem; color: #64748b; margin-top: 4px;">
								Lasă gol dacă nu vrei să schimbi. Compresie automată la max 1080x1080px.
							</div>
						</div>

						<!-- FOTOGRAFII SUPLIMENTARE (adăugare) -->
						<div>
							<label for="anunt_photos" style="display: block; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); margin-bottom: 4px;">
								📸 Adaugă Fotografii Noi (opțional)
							</label>
							<?php
							// Show existing gallery photos
							$gallery_ids = get_posts( array(
								'post_type'      => 'attachment',
								'post_parent'    => $anunt_id,
								'post_mime_type' => 'image',
								'posts_per_page' => -1,
								'fields'         => 'ids',
							) );
							if ( ! empty( $gallery_ids ) ) :
							?>
								<div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 10px;">
									<?php foreach ( $gallery_ids as $gid ) :
										$img_url = wp_get_attachment_image_url( $gid, array( 80, 60 ) );
										if ( $img_url ) :
									?>
										<img src="<?php echo esc_url( $img_url ); ?>" alt="" style="width: 80px; height: 60px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0;">
									<?php endif; endforeach; ?>
								</div>
								<div style="font-size: 0.78rem; color: #64748b; margin-bottom: 6px;">
									Fotografie existente în galerie (<?php echo count( $gallery_ids ); ?>/<?php echo $max_photos; ?>).
								</div>
							<?php endif; ?>
							<input type="file" id="anunt_photos" name="anunt_photos[]" multiple accept="image/*"
								style="width: 100%; padding: 10px; border: 1.5px dashed #cbd5e1; border-radius: 8px; background: #f8fafc;">
							<div style="font-size: 0.78rem; color: #64748b; margin-top: 4px;">
								Pozele noi se adaugă la galeria existentă. Compresie automată la max 1080x1080px.
							</div>
						</div>

						<button type="submit" name="update_anunt" class="btn btn-primary" style="padding: 14px; font-weight: 800; font-size: 1rem; border-radius: 8px;">
							💾 Salvează Modificările →
						</button>

					</form>
				</div>

				<!-- RIGHT: STATUS CARD -->
				<div>
					<div class="card" style="padding: 24px; background: #ffffff; border: 1px solid var(--color-border); border-radius: var(--border-radius-lg); position: sticky; top: 90px; box-shadow: var(--shadow-md);">

						<h3 style="font-size: 1rem; font-weight: 800; font-family: var(--font-heading); margin: 0 0 16px 0; padding-bottom: 12px; border-bottom: 2px solid #e2e8f0;">
							📋 Starea Anunțului
						</h3>

						<ul style="list-style: none; padding: 0; margin: 0 0 20px 0; display: flex; flex-direction: column; gap: 10px; font-size: 0.88rem;">
							<li style="display: flex; justify-content: space-between;">
								<span style="color: #475569; font-weight: 600;">Status:</span>
								<span style="font-weight: 800; color: <?php echo 'publish' === $anunt->post_status ? '#047857' : '#b45309'; ?>">
									<?php echo 'publish' === $anunt->post_status ? '✅ Publicat' : '⏳ În așteptare'; ?>
								</span>
							</li>
							<li style="display: flex; justify-content: space-between;">
								<span style="color: #475569; font-weight: 600;">Pachet:</span>
								<span style="font-weight: 800; color: <?php echo '1' === $is_premium ? '#b45309' : '#475569'; ?>">
									<?php echo '1' === $is_premium ? '⭐ PREMIUM' : '📢 Gratuit'; ?>
								</span>
							</li>
							<?php
							$expires = get_post_meta( $anunt_id, '_anunt_premium_expires', true );
							if ( '1' === $is_premium && ! empty( $expires ) ) :
							?>
								<li style="display: flex; justify-content: space-between;">
									<span style="color: #475569; font-weight: 600;">Expiră la:</span>
									<span style="font-weight: 800;"><?php echo esc_html( date( 'd.m.Y', strtotime( $expires ) ) ); ?></span>
								</li>
							<?php endif; ?>
							<li style="display: flex; justify-content: space-between;">
								<span style="color: #475569; font-weight: 600;">Fotografii max.:</span>
								<span style="font-weight: 800;"><?php echo $max_photos; ?> poze</span>
							</li>
						</ul>

						<div style="display: flex; flex-direction: column; gap: 10px;">
							<a href="<?php echo esc_url( get_permalink( $anunt_id ) ); ?>" target="_blank" class="btn" style="background: #f1f5f9; color: #1e293b; border: 1px solid #cbd5e1; font-weight: 700; font-size: 0.85rem; padding: 10px 16px; border-radius: 8px; text-decoration: none; text-align: center;">
								👁️ Vezi Anunțul Publicat
							</a>
							<a href="<?php echo esc_url( home_url( '/contul-meu/' ) ); ?>" class="btn" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; font-weight: 700; font-size: 0.85rem; padding: 10px 16px; border-radius: 8px; text-decoration: none; text-align: center;">
								← Înapoi la Contul Meu
							</a>
							<?php if ( '1' !== $is_premium ) : ?>
								<a href="<?php echo esc_url( home_url( '/adauga-anunt/?upgrade_id=' . $anunt_id ) ); ?>" class="btn" style="background: #fef3c7; color: #92400e; border: 1px solid #fde047; font-weight: 800; font-size: 0.85rem; padding: 10px 16px; border-radius: 8px; text-decoration: none; text-align: center;">
									⭐ Promovează la PREMIUM (10 LEI)
								</a>
							<?php endif; ?>
						</div>

					</div>
				</div>
			</div>

		<?php endif; ?>

	</div>
</main>

<?php
get_footer();
