<?php
/**
 * Template Name: Contul Meu / Tablou de Bord
 * @package Brezoaele_V2
 */

get_header();

$is_logged_in = is_user_logged_in();
$current_user = wp_get_current_user();

// Handle deletion of an announcement if owner
if ( $is_logged_in && isset( $_GET['action'], $_GET['delete_anunt'] ) && 'delete' === $_GET['action'] ) {
	$anunt_id = intval( $_GET['delete_anunt'] );
	$post     = get_post( $anunt_id );
	if ( $post && (int) $post->post_author === (int) $current_user->ID && 'anunt' === $post->post_type ) {
		wp_delete_post( $anunt_id, true );
		wp_redirect( home_url( '/contul-meu/?msg=deleted' ) );
		exit;
	}
}
?>

<main id="primary" class="site-main" style="padding: 40px 0; background-color: var(--color-bg);">
	<div class="container">

		<?php if ( ! $is_logged_in ) : ?>
			<!-- SECTIUNE CONECTARE & INREGISTRARE (PENTRU VIZITATORI) -->
			<div style="max-width: 520px; margin: 0 auto;">
				<div class="card" style="padding: 30px; background: #ffffff; border: 1px solid var(--color-border); border-radius: var(--border-radius-lg); box-shadow: 0 10px 25px rgba(0,0,0,0.04);">
					
					<div style="text-align: center; margin-bottom: 24px;">
						<div style="font-size: 2.5rem; margin-bottom: 8px;">🔐</div>
						<h1 style="font-size: 1.5rem; font-weight: 900; font-family: var(--font-heading); margin-bottom: 6px;">
							Conectare în Contul Tău
						</h1>
						<p style="color: var(--color-text-muted); font-size: 0.88rem;">
							Accesează contul pentru a publica anunțuri sau pentru a adăuga o afacere pe hartă.
						</p>
					</div>

					<!-- Google Sign-In Button Container -->
					<div style="margin-bottom: 20px;">
						<script src="https://accounts.google.com/gsi/client" async defer></script>
						<div id="g_id_onload"
							 data-client_id="149066189618-jmebknal81p4oa19f6ur6uckhsutaor5.apps.googleusercontent.com"
							 data-callback="handleGoogleLoginResponse"
							 data-auto_prompt="false">
						</div>

						<div class="g_id_signin"
							 data-type="standard"
							 data-shape="rectangular"
							 data-theme="outline"
							 data-text="signin_with"
							 data-size="large"
							 data-logo_alignment="left"
							 data-width="100%">
						</div>
					</div>

					<div style="display: flex; align-items: center; margin: 20px 0; gap: 10px;">
						<div style="flex: 1; height: 1px; background: #e2e8f0;"></div>
						<span style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; text-transform: uppercase;">sau conectare cu email</span>
						<div style="flex: 1; height: 1px; background: #e2e8f0;"></div>
					</div>

					<!-- Formular Conectare Clasic -->
					<form id="brezoaele-login-form" style="display: flex; flex-direction: column; gap: 14px;">
						<div id="auth-error-box" style="display: none; padding: 10px 14px; background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; border-radius: 6px; font-size: 0.85rem;"></div>

						<div>
							<label for="auth_email" style="display: block; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); margin-bottom: 4px;">Email:</label>
							<input type="email" id="auth_email" name="email" required placeholder="adresa@ta.com" style="width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; box-sizing: border-box;">
						</div>

						<div>
							<label for="auth_password" style="display: block; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); margin-bottom: 4px;">Parolă:</label>
							<input type="password" id="auth_password" name="password" required placeholder="••••••••" style="width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; box-sizing: border-box;">
						</div>

						<button type="submit" class="btn btn-primary" style="padding: 12px; font-weight: 800; width: 100%; margin-top: 6px;">
							Conectează-te &rarr;
						</button>
					</form>

					<div style="margin-top: 20px; text-align: center; font-size: 0.85rem; color: var(--color-text-muted);">
						Nu ai un cont încă? <a href="#" id="toggle-auth-mode" style="color: var(--color-primary-dark); font-weight: 800; text-decoration: underline;">Creează cont nou gratuit</a>
					</div>

				</div>
			</div>

			<script>
			function handleGoogleLoginResponse(response) {
				const credential = response.credential;
				// Extract basic info from Google JWT payload
				const base64Url = credential.split('.')[1];
				const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
				const jsonPayload = decodeURIComponent(atob(base64).split('').map(function(c) {
					return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
				}).join(''));
				const payload = JSON.parse(jsonPayload);

				jQuery.post('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
					action: 'brezoaele_google_login',
					security: '<?php echo wp_create_nonce( 'brezoaele_auth_nonce' ); ?>',
					credential: credential,
					email: payload.email,
					name: payload.name
				}, function(res) {
					if (res.success) {
						window.location.reload();
					} else {
						alert(res.data.message || 'Eroare la autentificarea cu Google.');
					}
				});
			}

			jQuery(document).ready(function($) {
				let isRegisterMode = false;
				$('#toggle-auth-mode').on('click', function(e) {
					e.preventDefault();
					isRegisterMode = !isRegisterMode;
					if (isRegisterMode) {
						$(this).text('Conectează-te în contul existent');
						$('#brezoaele-login-form button[type="submit"]').text('Creează Contul Nou →');
						if ($('#fname_field').length === 0) {
							$('#brezoaele-login-form').prepend('<div id="fname_field"><label style="display: block; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); margin-bottom: 4px;">Nume & Prenume:</label><input type="text" id="auth_fname" name="fname" required placeholder="Ion Popescu" style="width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; box-sizing: border-box; margin-bottom: 14px;"></div>');
						}
					} else {
						$(this).text('Creează cont nou gratuit');
						$('#brezoaele-login-form button[type="submit"]').text('Conectează-te →');
						$('#fname_field').remove();
					}
				});

				$('#brezoaele-login-form').on('submit', function(e) {
					e.preventDefault();
					const actionName = isRegisterMode ? 'brezoaele_classic_register' : 'brezoaele_classic_login';
					const formData = $(this).serialize() + '&action=' + actionName + '&security=<?php echo wp_create_nonce( 'brezoaele_auth_nonce' ); ?>';

					$('#auth-error-box').hide();
					$.post('<?php echo admin_url( 'admin-ajax.php' ); ?>', formData, function(res) {
						if (res.success) {
							window.location.href = res.data.redirect || '<?php echo home_url( '/contul-meu/' ); ?>';
						} else {
							$('#auth-error-box').text(res.data.message).show();
						}
					});
				});
			});
			</script>

		<?php else : ?>

			<!-- TABLOU DE BORD PENTRU UTILIZATOR CONECTAT -->
			<header class="page-header" style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
				<div>
					<h1 class="page-title" style="font-size: 2rem; font-weight: 900; font-family: var(--font-heading); margin-bottom: 4px;">
						👋 Salutare, <?php echo esc_html( $current_user->display_name ); ?>!
					</h1>
					<p style="color: var(--color-text-muted); font-size: 0.9rem; margin: 0;">
						Administrează-ți anunțurile, afacerile de pe hartă și facturile de mai jos.
					</p>
				</div>
				<a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>" class="btn" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; font-weight: 700; font-size: 0.85rem; padding: 8px 16px; border-radius: 8px; text-decoration: none;">
					🚪 Deconectare
				</a>
			</header>

			<?php if ( isset( $_GET['msg'] ) && 'deleted' === $_GET['msg'] ) : ?>
				<div style="padding: 12px 18px; background: #ecfdf5; border: 1px solid #a7f3d0; color: #047857; border-radius: 8px; font-weight: 700; font-size: 0.9rem; margin-bottom: 24px;">
					✔ Anunțul a fost șters cu succes.
				</div>
			<?php endif; ?>

			<!-- SECTIUNEA 1: ANUNTURILE MELE -->
			<div class="card" style="padding: 24px; background: #ffffff; border: 1px solid var(--color-border); border-radius: var(--border-radius-lg); margin-bottom: 30px;">
				<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid var(--color-border); padding-bottom: 12px;">
					<h2 style="font-size: 1.25rem; font-weight: 800; font-family: var(--font-heading); margin: 0;">
						📢 Anunțurile Mele
					</h2>
					<a href="<?php echo esc_url( home_url( '/adauga-anunt/' ) ); ?>" class="btn btn-primary" style="padding: 8px 16px; font-weight: 800; font-size: 0.85rem;">
						+ Adaugă Anunț Nou
					</a>
				</div>

				<?php
				$user_anunturi = get_posts( array(
					'post_type'   => 'anunt',
					'author'      => $current_user->ID,
					'post_status' => array( 'publish', 'pending' ),
					'numberposts' => -1,
				) );
				?>

				<?php if ( empty( $user_anunturi ) ) : ?>
					<p style="color: var(--color-text-muted); font-size: 0.9rem; text-align: center; padding: 20px 0;">
						Nu ai publicat niciun anunț până acum.
					</p>
				<?php else : ?>
					<div style="display: flex; flex-direction: column; gap: 14px;">
						<?php foreach ( $user_anunturi as $anunt ) : ?>
							<?php
							$is_premium  = get_post_meta( $anunt->ID, '_anunt_is_premium', true );
							$expires_at  = get_post_meta( $anunt->ID, '_anunt_premium_expires', true );
							$pret        = get_post_meta( $anunt->ID, '_anunt_pret', true );
							?>
							<div style="display: flex; justify-content: space-between; align-items: center; padding: 16px; border: 1px solid #e2e8f0; border-radius: 8px; background: <?php echo $is_premium ? '#fefce8' : '#fafafa'; ?>; flex-wrap: wrap; gap: 12px;">
								<div>
									<div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
										<h3 style="font-size: 1.05rem; font-weight: 800; margin: 0;">
											<a href="<?php echo get_permalink( $anunt->ID ); ?>" style="color: var(--color-text-dark); text-decoration: none;"><?php echo esc_html( $anunt->post_title ); ?></a>
										</h3>
										<?php if ( '1' === $is_premium ) : ?>
											<span style="background: #fef3c7; color: #b45309; border: 1px solid #fde047; padding: 2px 8px; border-radius: 12px; font-size: 0.7rem; font-weight: 800;">⭐ PREMIUM</span>
										<?php endif; ?>
									</div>
									<div style="font-size: 0.8rem; color: var(--color-text-muted);">
										Data publicării: <?php echo get_the_date( 'd.m.Y', $anunt->ID ); ?>
										<?php if ( ! empty( $pret ) ) : ?> | Preț: <strong><?php echo esc_html( $pret ); ?> LEI</strong><?php endif; ?>
										<?php if ( '1' === $is_premium && ! empty( $expires_at ) ) : ?>
											| Expiră Premium la: <strong><?php echo esc_html( date( 'd.m.Y', strtotime( $expires_at ) ) ); ?></strong>
										<?php endif; ?>
									</div>
								</div>

								<div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
									<?php if ( '1' !== $is_premium ) : ?>
										<a href="<?php echo esc_url( home_url( '/adauga-anunt/?upgrade_id=' . $anunt->ID ) ); ?>" class="btn" style="background: #fef3c7; color: #92400e; border: 1px solid #fde047; font-weight: 800; font-size: 0.8rem; padding: 6px 12px; border-radius: 6px; text-decoration: none;">
											⭐ Promovează (10 LEI)
										</a>
									<?php endif; ?>
									<a href="<?php echo esc_url( home_url( '/editeaza-anunt/?anunt_id=' . $anunt->ID ) ); ?>" class="btn" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-weight: 700; font-size: 0.8rem; padding: 6px 12px; border-radius: 6px; text-decoration: none;">
										✏️ Editează
									</a>
									<a href="<?php echo esc_url( home_url( '/contul-meu/?action=delete&delete_anunt=' . $anunt->ID ) ); ?>" onclick="return confirm('Ești sigur că vrei să ștergi acest anunț?');" class="btn" style="background: #fef2f2; color: #991b1b; border: 1px solid #fca5a5; font-weight: 700; font-size: 0.8rem; padding: 6px 12px; border-radius: 6px; text-decoration: none;">
										🗑️ Șterge
									</a>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<!-- SECTIUNEA 2: AFACERILE MELE PE HARTA -->
			<div class="card" style="padding: 24px; background: #ffffff; border: 1px solid var(--color-border); border-radius: var(--border-radius-lg); margin-bottom: 30px;">
				<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid var(--color-border); padding-bottom: 12px;">
					<h2 style="font-size: 1.25rem; font-weight: 800; font-family: var(--font-heading); margin: 0;">
						🗺️ Afacerile Mele pe Harta Serviciilor
					</h2>
					<a href="<?php echo esc_url( home_url( '/solicita-adaugare-afacere/' ) ); ?>" class="btn btn-primary" style="padding: 8px 16px; font-weight: 800; font-size: 0.85rem;">
						+ Adaugă Afacere pe Hartă (149 LEI/an)
					</a>
				</div>

				<?php
				$user_firme = get_posts( array(
					'post_type'   => 'firma',
					'author'      => $current_user->ID,
					'post_status' => array( 'publish', 'pending' ),
					'numberposts' => -1,
				) );
				?>

				<?php if ( empty( $user_firme ) ) : ?>
					<p style="color: var(--color-text-muted); font-size: 0.9rem; text-align: center; padding: 20px 0;">
						Nu ai înregistrat nicio afacere pe harta serviciilor până acum.
					</p>
				<?php else : ?>
					<div style="display: flex; flex-direction: column; gap: 14px;">
						<?php foreach ( $user_firme as $firma ) : ?>
							<?php
							$expires_at = get_post_meta( $firma->ID, '_firma_subscription_expires', true );
							$is_active  = ( ! empty( $expires_at ) && strtotime( $expires_at ) > time() );
							?>
							<div style="display: flex; justify-content: space-between; align-items: center; padding: 16px; border: 1px solid #e2e8f0; border-radius: 8px; background: #fafafa; flex-wrap: wrap; gap: 12px;">
								<div>
									<h3 style="font-size: 1.05rem; font-weight: 800; margin: 0 0 4px 0;">
										<a href="<?php echo get_permalink( $firma->ID ); ?>" style="color: var(--color-text-dark); text-decoration: none;"><?php echo esc_html( $firma->post_title ); ?></a>
									</h3>
									<div style="font-size: 0.8rem; color: var(--color-text-muted);">
										Status Abonament 1 An: 
										<?php if ( $is_active ) : ?>
											<span style="color:#047857; font-weight:800;">✅ Activ (Expiră la <?php echo esc_html( date( 'd.m.Y', strtotime( $expires_at ) ) ); ?>)</span>
										<?php else : ?>
											<span style="color:#b45309; font-weight:800;">⏳ În așteptare / Expirat</span>
										<?php endif; ?>
									</div>
								</div>

								<div>
									<a href="<?php echo esc_url( home_url( '/solicita-adaugare-afacere/?renew_id=' . $firma->ID ) ); ?>" class="btn btn-primary" style="font-weight: 800; font-size: 0.8rem; padding: 6px 12px; border-radius: 6px; text-decoration: none;">
										🔄 Reînnoiește Abonament (149 LEI)
									</a>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<!-- SECTIUNEA 3: ISTORIC COMENZI & FACTURI PDF -->
			<div class="card" style="padding: 24px; background: #ffffff; border: 1px solid var(--color-border); border-radius: var(--border-radius-lg);">
				<h2 style="font-size: 1.25rem; font-weight: 800; font-family: var(--font-heading); margin-bottom: 16px; border-bottom: 2px solid var(--color-border); padding-bottom: 12px;">
					💳 Istoric Comenzi & Facturi Fiscale
				</h2>

				<?php
				$user_orders = Brezoaele_Orders_DB::get_user_orders( $current_user->ID );
				?>

				<?php if ( empty( $user_orders ) ) : ?>
					<p style="color: var(--color-text-muted); font-size: 0.9rem; text-align: center; padding: 20px 0;">
						Nu există nicio comandă înregistrată pe contul tău.
					</p>
				<?php else : ?>
					<table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
						<thead>
							<tr style="border-bottom: 2px solid #e2e8f0; text-align: left;">
								<th style="padding: 10px;">Comandă ID</th>
								<th style="padding: 10px;">Serviciu</th>
								<th style="padding: 10px;">Sumă</th>
								<th style="padding: 10px;">Status</th>
								<th style="padding: 10px;">Factură PDF</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $user_orders as $order ) : ?>
								<tr style="border-bottom: 1px solid #f1f5f9;">
									<td style="padding: 12px 10px; font-weight: 800;"><?php echo esc_html( $order['order_id'] ); ?></td>
									<td style="padding: 12px 10px;">
										<?php echo 'anunt_premium' === $order['item_type'] ? '⭐ Promovare Anunț Premium (10 LEI)' : '🗺️ Adăugare Afacere pe Hartă (149 LEI)'; ?>
									</td>
									<td style="padding: 12px 10px; font-weight: 800;"><?php echo esc_html( $order['amount'] . ' ' . $order['currency'] ); ?></td>
									<td style="padding: 12px 10px;">
										<?php if ( 'active' === $order['status'] ) : ?>
											<span style="color:#047857; font-weight:800;">✅ Achitată</span>
										<?php else : ?>
											<span style="color:#b45309; font-weight:700;">⏳ <?php echo esc_html( strtoupper( $order['status'] ) ); ?></span>
										<?php endif; ?>
									</td>
									<td style="padding: 12px 10px;">
										<?php if ( ! empty( $order['invoice_pdf_path'] ) ) : ?>
											<a href="<?php echo esc_url( Brezoaele_Invoice_Downloader::get_download_url( $order['order_id'] ) ); ?>" target="_blank" class="btn" style="background:#047857; color:#fff; padding:4px 10px; font-size:0.75rem; border-radius:4px; font-weight:700; text-decoration:none;">📄 Descarcă PDF</a>
										<?php else : ?>
											<span style="color:#94a3b8; font-size:0.8rem;">În curs de generare</span>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif; ?>
			</div>

		<?php endif; ?>

	</div>
</main>

<?php
get_footer();
