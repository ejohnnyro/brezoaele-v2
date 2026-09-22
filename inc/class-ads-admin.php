<?php
/**
 * Brezoaele V2 - Admin Multi-Ad Rotation System & Date Scheduling
 * Allows adding multiple client ads per zone with automatic rotation and campaign expiration dates.
 *
 * @package Brezoaele_V2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Brezoaele_Ads_Admin {

	/**
	 * Defined Ad Zones
	 */
	public static function get_zones() {
		return array(
			'header'         => array(
				'label'       => '1. Header Banner Global (Sub Meniu Navigation)',
				'dimensions'  => '728 x 90 px (Desktop) / 320 x 100 px (Mobil)',
				'description' => 'Apare pe toate paginile site-ului, chiar sub meniul principal de navigare.',
			),
			'sidebar-medium' => array(
				'label'       => '2. Sidebar Banner Sus (Articole & Știri)',
				'dimensions'  => '300 x 250 px (Medium Rectangle)',
				'description' => 'Apare în partea de sus a sidebar-ului din dreapta în paginile de știri.',
			),
			'sidebar-sticky' => array(
				'label'       => '3. Sidebar Banner Sticky (Articole & Știri)',
				'dimensions'  => '300 x 600 px (Half-Page Sticky / Skyscraper)',
				'description' => 'Apare jos în sidebar și rămâne fix pe ecran pe măsură ce utilizatorul derulează articolul.',
			),
			'in-article'     => array(
				'label'       => '4. În Interiorul Articolului (După Paragraful 3)',
				'dimensions'  => '728 x 90 px / 300 x 250 px (In-Article Ad)',
				'description' => 'Apare automat în mijlocul textului după paragraful 3, doar dacă articolul are minim 4 paragrafe.',
			),
			'after-article'  => array(
				'label'       => '5. Sub Articol (Înainte de Comentarii & Newsletter)',
				'dimensions'  => '336 x 280 px / 728 x 90 px',
				'description' => 'Apare la finalul textului din articol, chiar înainte de formularul de newsletter.',
			),
			'homepage-feed'  => array(
				'label'       => '6. Prima Pagină Feed (Între Secțiuni)',
				'dimensions'  => '970 x 250 px (Billboard) / 728 x 90 px',
				'description' => 'Apare pe prima pagină, între secțiunea de producători și secțiunea de forum.',
			),
		);
	}

	/**
	 * Initialize Hooks
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_assets' ) );
	}

	/**
	 * Enqueue WP Media Uploader scripts for Admin
	 */
	public static function enqueue_admin_assets( $hook ) {
		if ( 'toplevel_page_brezoaele-ads' === $hook ) {
			wp_enqueue_media();
		}
	}

	/**
	 * Register Admin Menu Page
	 */
	public static function add_admin_menu() {
		add_menu_page(
			'Administrare Reclame',
			'📢 Reclame Site',
			'manage_options',
			'brezoaele-ads',
			array( __CLASS__, 'render_admin_page' ),
			'dashicons-megaphone',
			30
		);
	}

	/**
	 * Normalize zone data (Backwards compatibility)
	 */
	public static function get_normalized_zone_data( $saved_options, $zone_key ) {
		$default = array(
			'status'   => 'placeholder',
			'rotation' => 'random',
			'items'    => array(),
		);

		if ( ! isset( $saved_options[ $zone_key ] ) || ! is_array( $saved_options[ $zone_key ] ) ) {
			return $default;
		}

		$zone = $saved_options[ $zone_key ];

		// Migrare date vechi (dacă există formatul vechi cu single item)
		if ( isset( $zone['type'] ) || isset( $zone['img_url'] ) || isset( $zone['html_code'] ) ) {
			$old_item = array(
				'title'        => 'Reclamă Client 1',
				'type'         => isset( $zone['type'] ) ? $zone['type'] : 'image',
				'img_url'      => isset( $zone['img_url'] ) ? $zone['img_url'] : '',
				'link_url'     => isset( $zone['link_url'] ) ? $zone['link_url'] : '',
				'html_code'    => isset( $zone['html_code'] ) ? $zone['html_code'] : '',
				'open_new_tab' => isset( $zone['open_new_tab'] ) ? $zone['open_new_tab'] : '1',
				'nofollow'     => isset( $zone['nofollow'] ) ? $zone['nofollow'] : '1',
				'active'       => '1',
				'start_date'   => '',
				'end_date'     => '',
			);
			return array(
				'status'   => isset( $zone['status'] ) ? $zone['status'] : 'active',
				'rotation' => 'random',
				'items'    => array( $old_item ),
			);
		}

		return array(
			'status'   => isset( $zone['status'] ) ? $zone['status'] : 'placeholder',
			'rotation' => isset( $zone['rotation'] ) ? $zone['rotation'] : 'random',
			'items'    => ( isset( $zone['items'] ) && is_array( $zone['items'] ) ) ? $zone['items'] : array(),
		);
	}

	/**
	 * Render Admin Settings Page
	 */
	public static function render_admin_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$zones   = self::get_zones();
		$message = '';

		// Salvare Opțiuni
		if ( isset( $_POST['brezoaele_save_ads_nonce'] ) && wp_verify_nonce( $_POST['brezoaele_save_ads_nonce'], 'brezoaele_save_ads_action' ) ) {
			$new_options = array();
			$raw_options = isset( $_POST['ad_zone'] ) && is_array( $_POST['ad_zone'] ) ? $_POST['ad_zone'] : array();

			foreach ( $zones as $key => $info ) {
				$raw_zone = isset( $raw_options[ $key ] ) ? $raw_options[ $key ] : array();

				$clean_items = array();
				if ( isset( $raw_zone['items'] ) && is_array( $raw_zone['items'] ) ) {
					foreach ( $raw_zone['items'] as $item ) {
						$clean_items[] = array(
							'title'        => isset( $item['title'] ) ? sanitize_text_field( $item['title'] ) : 'Reclamă Client',
							'type'         => isset( $item['type'] ) ? sanitize_text_field( $item['type'] ) : 'image',
							'img_url'      => isset( $item['img_url'] ) ? esc_url_raw( $item['img_url'] ) : '',
							'link_url'     => isset( $item['link_url'] ) ? esc_url_raw( $item['link_url'] ) : '',
							'html_code'    => isset( $item['html_code'] ) ? wp_unslash( $item['html_code'] ) : '',
							'open_new_tab' => isset( $item['open_new_tab'] ) ? '1' : '0',
							'nofollow'     => isset( $item['nofollow'] ) ? '1' : '0',
							'active'       => isset( $item['active'] ) ? '1' : '0',
							'start_date'   => isset( $item['start_date'] ) ? sanitize_text_field( $item['start_date'] ) : '',
							'end_date'     => isset( $item['end_date'] ) ? sanitize_text_field( $item['end_date'] ) : '',
						);
					}
				}

				$new_options[ $key ] = array(
					'status'   => isset( $raw_zone['status'] ) ? sanitize_text_field( $raw_zone['status'] ) : 'placeholder',
					'rotation' => isset( $raw_zone['rotation'] ) ? sanitize_text_field( $raw_zone['rotation'] ) : 'random',
					'items'    => $clean_items,
				);
			}

			update_option( 'brezoaele_ads_options', $new_options );
			$message = 'Setările, datele de expirare și rotirea reclamelor au fost salvate cu succes!';
		}

		$saved_options = get_option( 'brezoaele_ads_options', array() );
		?>

		<div class="wrap" style="max-width: 1150px;">
			<h1 style="font-size: 1.8rem; font-weight: 800; display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
				📢 Panou de Administrare &amp; Programare Reclame (Rotire &amp; Expirare Automată)
			</h1>

			<?php if ( $message ) : ?>
				<div class="notice notice-success is-dismissible">
					<p><strong><?php echo esc_html( $message ); ?></strong></p>
				</div>
			<?php endif; ?>

			<form method="post" action="">
				<?php wp_nonce_field( 'brezoaele_save_ads_action', 'brezoaele_save_ads_nonce' ); ?>

				<div style="margin-bottom: 24px; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
					<h2 style="margin-top: 0; font-size: 1.2rem; color: #0f172a;">ℹ️ Ghid Rotire &amp; Programare Perioadă Campanie</h2>
					<p style="color: #475569; font-size: 0.95rem; margin-bottom: 0; line-height: 1.6;">
						• <strong>Programare &amp; Expirare Automată:</strong> Setează <em>Data Început</em> și <em>Data Încheiere (Expirare)</em> pentru fiecare reclamă. Când expiră perioada (ex: 30 de zile), sistemul oprește automat afișarea reclamei pe site, fără să fie nevoie să o ștergi manual.
						<br>• Apasă pe butonul <code>🗓️ Setează 30 Zile (1 Lună)</code> pentru a seta automat campania pentru 30 de zile începând de azi.
						<br>• <strong>Rotire la Refresh (Random):</strong> Sistemul alege aleatoriu 1 reclamă din lista celor active și neexpirate la fiecare încărcare de pagină.
					</p>
				</div>

				<div style="display: flex; flex-direction: column; gap: 28px;">
					<?php foreach ( $zones as $key => $info ) : ?>
						<?php
						$zone_data = self::get_normalized_zone_data( $saved_options, $key );
						$status    = $zone_data['status'];
						$rotation  = $zone_data['rotation'];
						$items     = $zone_data['items'];
						?>

						<div class="brz-ad-zone-box" data-zone="<?php echo esc_attr( $key ); ?>" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
							
							<!-- Header Zonă -->
							<div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid #e2e8f0; padding-bottom: 14px; margin-bottom: 16px; flex-wrap: wrap; gap: 12px;">
								<div>
									<h3 style="margin: 0; font-size: 1.2rem; color: #0f172a; font-weight: 800;">
										<?php echo esc_html( $info['label'] ); ?>
									</h3>
									<span style="font-size: 0.85rem; color: #64748b; font-weight: 600;">
										Format: <strong><?php echo esc_html( $info['dimensions'] ); ?></strong> — <?php echo esc_html( $info['description'] ); ?>
									</span>
								</div>

								<div style="display: flex; gap: 12px; align-items: center;">
									<!-- Mode Rotire -->
									<div>
										<label style="font-size: 0.8rem; font-weight: 700; color: #475569; display: block;">Mod Rotire:</label>
										<select name="ad_zone[<?php echo esc_attr( $key ); ?>][rotation]" style="font-weight: 600; padding: 4px 8px; border-radius: 4px;">
											<option value="random" <?php selected( $rotation, 'random' ); ?>>🎲 Rotire la Refresh (Random)</option>
											<option value="all" <?php selected( $rotation, 'all' ); ?>>📑 Afișează Toate (În Stivă)</option>
										</select>
									</div>

									<!-- Stare Zonă -->
									<div>
										<label style="font-size: 0.8rem; font-weight: 700; color: #475569; display: block;">Stare Zonă:</label>
										<select name="ad_zone[<?php echo esc_attr( $key ); ?>][status]" class="brz-ad-status-select" style="font-weight: 700; padding: 4px 8px; border-radius: 4px;">
											<option value="placeholder" <?php selected( $status, 'placeholder' ); ?>>📢 Placeholder (/publicitate/)</option>
											<option value="active" <?php selected( $status, 'active' ); ?>>✅ Reclame Clienți Active</option>
											<option value="disabled" <?php selected( $status, 'disabled' ); ?>>🚫 Dezactivat complet</option>
										</select>
									</div>
								</div>
							</div>

							<!-- Lista de Reclame ale Clienților pentru această Zonă -->
							<div class="brz-ad-items-container" style="display: <?php echo 'active' === $status ? 'flex' : 'none'; ?>; flex-direction: column; gap: 16px;">
								<div class="brz-ad-items-list" style="display: flex; flex-direction: column; gap: 16px;">
									<?php if ( ! empty( $items ) ) : ?>
										<?php foreach ( $items as $idx => $item ) : ?>
											<?php self::render_ad_item_card( $key, $idx, $item ); ?>
										<?php endforeach; ?>
									<?php else : ?>
										<!-- Prima Reclamă Implicită -->
										<?php self::render_ad_item_card( $key, 0, array() ); ?>
									<?php endif; ?>
								</div>

								<div style="margin-top: 8px;">
									<button type="button" class="button button-secondary brz-add-ad-btn" data-zone="<?php echo esc_attr( $key ); ?>" style="font-weight: 700;">
										➕ Adaugă Reclamă Nouă în această Zonă
									</button>
								</div>
							</div>

						</div>
					<?php endforeach; ?>
				</div>

				<div style="margin-top: 30px;">
					<button type="submit" class="button button-primary button-hero" style="font-weight: 800; padding: 12px 36px; font-size: 1rem;">
						💾 Salvează Toate Reclamele &amp; Modurile de Rotire
					</button>
				</div>
			</form>
		</div>

		<!-- Template ascuns pentru adăugare reclamă nouă via JS -->
		<div id="brz-ad-item-template" style="display: none;">
			<?php self::render_ad_item_card( '{{ZONE}}', '{{INDEX}}', array() ); ?>
		</div>

		<script>
		jQuery(document).ready(function($){
			// Schimbare stare select zonă
			$('.brz-ad-status-select').on('change', function(){
				var val = $(this).val();
				var itemsContainer = $(this).closest('.brz-ad-zone-box').find('.brz-ad-items-container');
				if (val === 'active') {
					itemsContainer.slideDown(200);
				} else {
					itemsContainer.slideUp(200);
				}
			});

			// Schimbare radio tip reclamă (imagine vs html)
			$(document).on('change', '.brz-ad-type-radio', function(){
				var val = $(this).val();
				var parent = $(this).closest('.brz-ad-item-card');
				if (val === 'image') {
					parent.find('.brz-ad-fields-image').show();
					parent.find('.brz-ad-fields-html').hide();
				} else {
					parent.find('.brz-ad-fields-image').hide();
					parent.find('.brz-ad-fields-html').show();
				}
			});

			// Adăugare reclamă nouă
			$('.brz-add-ad-btn').on('click', function(e){
				e.preventDefault();
				var zoneKey = $(this).data('zone');
				var zoneBox = $(this).closest('.brz-ad-zone-box');
				var itemsList = zoneBox.find('.brz-ad-items-list');
				var newIndex = itemsList.children('.brz-ad-item-card').length;

				var html = $('#brz-ad-item-template').html();
				html = html.replace(/\{\{ZONE\}\}/g, zoneKey);
				html = html.replace(/\{\{INDEX\}\}/g, newIndex);

				itemsList.append(html);
			});

			// Ștergere reclamă
			$(document).on('click', '.brz-remove-ad-btn', function(e){
				e.preventDefault();
				if (confirm('Sigur dorești să ștergi această reclamă?')) {
					$(this).closest('.brz-ad-item-card').remove();
				}
			});

			// Buton rapid Setează 30 Zile de azi
			$(document).on('click', '.brz-set-30days-btn', function(e){
				e.preventDefault();
				var card = $(this).closest('.brz-ad-item-card');
				var today = new Date();
				var yyyy = today.getFullYear();
				var mm = String(today.getMonth() + 1).padStart(2, '0');
				var dd = String(today.getDate()).padStart(2, '0');
				var startDateStr = yyyy + '-' + mm + '-' + dd;

				var endDate = new Date(today.getTime() + (30 * 24 * 60 * 60 * 1000));
				var e_yyyy = endDate.getFullYear();
				var e_mm = String(endDate.getMonth() + 1).padStart(2, '0');
				var e_dd = String(endDate.getDate()).padStart(2, '0');
				var endDateStr = e_yyyy + '-' + e_mm + '-' + e_dd;

				card.find('.brz-input-start-date').val(startDateStr);
				card.find('.brz-input-end-date').val(endDateStr);
			});

			// Media Library Uploader pentru imagini banner
			$(document).on('click', '.brz-upload-img-btn', function(e){
				e.preventDefault();
				var btn = $(this);
				var inputField = btn.siblings('input[type="text"]');
				var frame = wp.media({
					title: 'Selectează Imagine Banner',
					button: { text: 'Folosește această imagine' },
					multiple: false
				});

				frame.on('select', function(){
					var attachment = frame.state().get('selection').first().toJSON();
					inputField.val(attachment.url);
				});

				frame.open();
			});
		});
		</script>
		<?php
	}

	/**
	 * Render Card Reclamă Individuală
	 */
	public static function render_ad_item_card( $zone_key, $idx, $item = array() ) {
		$title        = isset( $item['title'] ) ? $item['title'] : 'Client Nou';
		$type         = isset( $item['type'] ) ? $item['type'] : 'image';
		$img_url      = isset( $item['img_url'] ) ? $item['img_url'] : '';
		$link_url     = isset( $item['link_url'] ) ? $item['link_url'] : '';
		$html_code    = isset( $item['html_code'] ) ? $item['html_code'] : '';
		$open_new_tab = ! isset( $item['open_new_tab'] ) || '1' === $item['open_new_tab'];
		$nofollow     = ! isset( $item['nofollow'] ) || '1' === $item['nofollow'];
		$active       = ! isset( $item['active'] ) || '1' === $item['active'];
		$start_date   = isset( $item['start_date'] ) ? $item['start_date'] : '';
		$end_date     = isset( $item['end_date'] ) ? $item['end_date'] : '';

		$today = date( 'Y-m-d' );
		$status_badge = '';
		if ( ! $active ) {
			$status_badge = '<span style="background: #f1f5f9; color: #64748b; font-size: 0.75rem; font-weight: 800; padding: 2px 8px; border-radius: 4px;">⚫ DEZACTIVATĂ</span>';
		} elseif ( $end_date && $today > $end_date ) {
			$status_badge = '<span style="background: #fef2f2; color: #ef4444; border: 1px solid #fca5a5; font-size: 0.75rem; font-weight: 800; padding: 2px 8px; border-radius: 4px;">🔴 EXPIRATĂ (' . esc_html( $end_date ) . ')</span>';
		} elseif ( $start_date && $today < $start_date ) {
			$status_badge = '<span style="background: #fffbeb; color: #d97706; border: 1px solid #fde68a; font-size: 0.75rem; font-weight: 800; padding: 2px 8px; border-radius: 4px;">⏳ PROGRAMATĂ (începe ' . esc_html( $start_date ) . ')</span>';
		} else {
			$status_badge = '<span style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; font-size: 0.75rem; font-weight: 800; padding: 2px 8px; border-radius: 4px;">🟢 ACTIVĂ PE SITE</span>';
		}
		?>
		<div class="brz-ad-item-card" style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 16px; position: relative;">
			
			<!-- Titlu Client & Status & Buton Ștergere -->
			<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; border-bottom: 1px dashed #cbd5e1; padding-bottom: 10px; flex-wrap: wrap; gap: 10px;">
				<div style="display: flex; align-items: center; gap: 10px;">
					<strong style="font-size: 0.95rem; color: #0f172a;">👤 Client / Nume:</strong>
					<input type="text" name="ad_zone[<?php echo esc_attr( $zone_key ); ?>][items][<?php echo esc_attr( $idx ); ?>][title]" value="<?php echo esc_attr( $title ); ?>" style="font-weight: 700; padding: 4px 8px; border-radius: 4px; border: 1px solid #cbd5e1; width: 240px;" placeholder="ex: Client SC Agro SRL" />
					<?php echo $status_badge; ?>
				</div>

				<div style="display: flex; align-items: center; gap: 14px;">
					<label style="font-weight: 700; color: #059669; font-size: 0.9rem;">
						<input type="checkbox" name="ad_zone[<?php echo esc_attr( $zone_key ); ?>][items][<?php echo esc_attr( $idx ); ?>][active]" value="1" <?php checked( $active ); ?>> Activă
					</label>
					<button type="button" class="button button-link-delete brz-remove-ad-btn" style="color: #ef4444; font-weight: 700; text-decoration: none;">
						🗑️ Șterge
					</button>
				</div>
			</div>

			<!-- Programare Perioadă Campanie (Data Început & Data Expirare) -->
			<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 14px; margin-bottom: 14px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
				<div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
					<div>
						<label style="font-weight: 700; font-size: 0.82rem; color: #475569; display: block;">🗓️ Data Început:</label>
						<input type="date" class="brz-input-start-date" name="ad_zone[<?php echo esc_attr( $zone_key ); ?>][items][<?php echo esc_attr( $idx ); ?>][start_date]" value="<?php echo esc_attr( $start_date ); ?>" style="padding: 4px 8px; border-radius: 4px; border: 1px solid #cbd5e1;" />
					</div>

					<div>
						<label style="font-weight: 700; font-size: 0.82rem; color: #475569; display: block;">🏁 Data Expirare (30 zile):</label>
						<input type="date" class="brz-input-end-date" name="ad_zone[<?php echo esc_attr( $zone_key ); ?>][items][<?php echo esc_attr( $idx ); ?>][end_date]" value="<?php echo esc_attr( $end_date ); ?>" style="padding: 4px 8px; border-radius: 4px; border: 1px solid #cbd5e1;" />
					</div>
				</div>

				<button type="button" class="button button-secondary brz-set-30days-btn" style="font-weight: 700; font-size: 0.8rem;">
					🗓️ Setează 30 Zile (de Azi)
				</button>
			</div>

			<div style="margin-bottom: 12px;">
				<label style="font-weight: 700; margin-right: 16px;">Tip Reclamă:</label>
				<label style="margin-right: 16px; font-weight: 600;">
					<input type="radio" name="ad_zone[<?php echo esc_attr( $zone_key ); ?>][items][<?php echo esc_attr( $idx ); ?>][type]" value="image" class="brz-ad-type-radio" <?php checked( $type, 'image' ); ?>> 🖼️ Banner Imagine + Link
				</label>
				<label style="font-weight: 600;">
					<input type="radio" name="ad_zone[<?php echo esc_attr( $zone_key ); ?>][items][<?php echo esc_attr( $idx ); ?>][type]" value="html" class="brz-ad-type-radio" <?php checked( $type, 'html' ); ?>> 💻 Cod HTML Custom / AdSense / Script
				</label>
			</div>

			<!-- Opțiunea A: Imagine + Link -->
			<div class="brz-ad-fields-image" style="display: <?php echo 'image' === $type ? 'block' : 'none'; ?>;">
				<div style="margin-bottom: 10px;">
					<label style="font-weight: 700; display: block; margin-bottom: 4px; font-size: 0.85rem;">URL Imagine Banner:</label>
					<div style="display: flex; gap: 8px;">
						<input type="text" name="ad_zone[<?php echo esc_attr( $zone_key ); ?>][items][<?php echo esc_attr( $idx ); ?>][img_url]" value="<?php echo esc_url( $img_url ); ?>" style="width: 100%; padding: 6px 10px; border-radius: 4px; border: 1px solid #cbd5e1;" placeholder="https://example.com/banner.jpg" />
						<button type="button" class="button button-secondary brz-upload-img-btn">
							📷 Alege Imagine
						</button>
					</div>
				</div>

				<div style="margin-bottom: 10px;">
					<label style="font-weight: 700; display: block; margin-bottom: 4px; font-size: 0.85rem;">URL Destinație Link (unde duce click-ul):</label>
					<input type="url" name="ad_zone[<?php echo esc_attr( $zone_key ); ?>][items][<?php echo esc_attr( $idx ); ?>][link_url]" value="<?php echo esc_url( $link_url ); ?>" style="width: 100%; padding: 6px 10px; border-radius: 4px; border: 1px solid #cbd5e1;" placeholder="https://afacere-client.ro" />
				</div>

				<div style="display: flex; gap: 20px;">
					<label style="font-weight: 600; font-size: 0.85rem;">
						<input type="checkbox" name="ad_zone[<?php echo esc_attr( $zone_key ); ?>][items][<?php echo esc_attr( $idx ); ?>][open_new_tab]" value="1" <?php checked( $open_new_tab ); ?>> Deschide în tab nou (target="_blank")
					</label>
					<label style="font-weight: 600; font-size: 0.85rem;">
						<input type="checkbox" name="ad_zone[<?php echo esc_attr( $zone_key ); ?>][items][<?php echo esc_attr( $idx ); ?>][nofollow]" value="1" <?php checked( $nofollow ); ?>> Adaugă rel="sponsored nofollow" (SEO)
					</label>
				</div>
			</div>

			<!-- Opțiunea B: Cod HTML Custom -->
			<div class="brz-ad-fields-html" style="display: <?php echo 'html' === $type ? 'block' : 'none'; ?>;">
				<label style="font-weight: 700; display: block; margin-bottom: 4px; font-size: 0.85rem;">Cod HTML / JS / Google AdSense:</label>
				<textarea name="ad_zone[<?php echo esc_attr( $zone_key ); ?>][items][<?php echo esc_attr( $idx ); ?>][html_code]" rows="4" style="width: 100%; font-family: monospace; font-size: 0.85rem; padding: 8px; border-radius: 4px; border: 1px solid #cbd5e1;" placeholder="Introdu codul HTML/JS (ex: <ins class='adsbygoogle' ...></ins>)"><?php echo esc_textarea( $html_code ); ?></textarea>
			</div>
		</div>
		<?php
	}
}

Brezoaele_Ads_Admin::init();
