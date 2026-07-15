<?php
/**
 * Brezoaele V2 functions and definitions.
 *
 * @package Brezoaele_V2
 */

if ( ! function_exists( 'brezoaele_v2_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 */
	function brezoaele_v2_setup() {
		// Suport pentru titlu dinamic în tag-ul head
		add_theme_support( 'title-tag' );

		// Suport pentru imagini reprezentative (Post Thumbnails)
		add_theme_support( 'post-thumbnails' );

		// Înregistrare meniu principal
		register_nav_menus(
			array(
				'menu-1' => esc_html__( 'Primary Menu', 'brezoaele-v2' ),
			)
		);
	}
endif;
add_action( 'after_setup_theme', 'brezoaele_v2_setup' );

/**
 * Enqueue scripts and styles natively.
 */
function brezoaele_v2_scripts() {
	// 1. Enqueue Google Font Inter cu suport diacritice românești
	wp_enqueue_style( 'brezoaele-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap', array(), null );

	// 2. Enqueue Tema principală
	wp_enqueue_style( 'brezoaele-style', get_stylesheet_uri(), array(), filemtime( get_stylesheet_directory() . '/style.css' ) );

	// 3. Enqueue Weather API script
	wp_enqueue_script( 'brezoaele-weather', get_template_directory_uri() . '/js/weather.js', array(), '1.0.1', true );

	// 4. Enqueue Forms UI script
	wp_enqueue_script( 'brezoaele-forms', get_template_directory_uri() . '/js/forms.js', array(), filemtime( get_template_directory() . '/js/forms.js' ), true );

	// 5. Enqueue Leaflet & Harta Satelit doar pe șablonul dedicat
	if ( is_page_template( 'template-harta-servicii.php' ) ) {
		wp_enqueue_style( 'leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', array(), '1.9.4' );
		wp_enqueue_script( 'leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), '1.9.4', true );

		// Pregătim markerii din baza de date pentru localizare (Firme + Investiții)
		$args = array(
			'post_type'      => array( 'firma', 'investitie' ),
			'posts_per_page' => -1,
			'post_status'    => 'publish',
		);
		$query = new WP_Query( $args );
		$pins  = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$lat     = get_post_meta( get_the_ID(), '_locatie_lat', true );
				$lng     = get_post_meta( get_the_ID(), '_locatie_lng', true );
				$telefon = get_post_meta( get_the_ID(), '_locatie_telefon', true );
				$program = get_post_meta( get_the_ID(), '_locatie_program', true );

				$terms = get_the_terms( get_the_ID(), 'tip_afacere' );
				$type  = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->slug : 'generic';

				if ( get_post_type() === 'investitie' ) {
					$type = 'investitie';
					$telefon = '';
					$program = 'Stadiu: ' . get_post_meta( get_the_ID(), '_investitie_stadiu', true );
				}

				if ( ! empty( $lat ) && ! empty( $lng ) ) {
					$pins[] = array(
						'title'   => get_the_title(),
						'lat'     => floatval( $lat ),
						'lng'     => floatval( $lng ),
						'excerpt' => wp_trim_words( get_the_excerpt(), 18, '...' ),
						'link'    => esc_url( get_permalink() ),
						'telefon' => esc_html( $telefon ),
						'program' => esc_html( $program ),
						'type'    => esc_html( $type ),
					);
				}
			}
			wp_reset_postdata();
		}

		wp_enqueue_script( 'brezoaele-map', get_template_directory_uri() . '/js/map.js', array( 'leaflet-js' ), '1.0.3', true );
		wp_localize_script( 'brezoaele-map', 'brezoaeleMapData', $pins );
	}

	// 6. Enqueue Leaflet & Harta Satelit pe pagina individuală de Afacere sau Investiție (single-firma.php, single-investitie.php)
	if ( is_singular( array( 'firma', 'investitie' ) ) ) {
		$lat = get_post_meta( get_the_ID(), '_locatie_lat', true );
		$lng = get_post_meta( get_the_ID(), '_locatie_lng', true );

		if ( ! empty( $lat ) && ! empty( $lng ) ) {
			wp_enqueue_style( 'leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', array(), '1.9.4' );
			wp_enqueue_script( 'leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), '1.9.4', true );

			wp_enqueue_script( 'brezoaele-single-map', get_template_directory_uri() . '/js/single-map.js', array( 'leaflet-js' ), '1.0.1', true );
			wp_localize_script( 'brezoaele-single-map', 'brezoaeleSingleMapData', array(
				'title'   => get_the_title(),
				'lat'     => floatval( $lat ),
				'lng'     => floatval( $lng ),
			) );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'brezoaele_v2_scripts' );

/**
 * Register Custom Post Type: Anunț
 */
function brezoaele_v2_register_anunt_cpt() {
	$labels = array(
		'name'                  => _x( 'Anunțuri', 'Post Type General Name', 'brezoaele-v2' ),
		'singular_name'         => _x( 'Anunț', 'Post Type Singular Name', 'brezoaele-v2' ),
		'menu_name'             => __( 'Anunțuri', 'brezoaele-v2' ),
		'all_items'             => __( 'Toate Anunțurile', 'brezoaele-v2' ),
		'add_new_item'          => __( 'Adaugă Anunț Nou', 'brezoaele-v2' ),
		'edit_item'             => __( 'Editează Anunț', 'brezoaele-v2' ),
		'update_item'           => __( 'Actualizează Anunț', 'brezoaele-v2' ),
		'view_item'             => __( 'Vezi Anunț', 'brezoaele-v2' ),
		'search_items'          => __( 'Caută Anunț', 'brezoaele-v2' ),
		'not_found'             => __( 'Nu s-au găsit anunțuri', 'brezoaele-v2' ),
	);
	$args = array(
		'label'               => __( 'Anunț', 'brezoaele-v2' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		'taxonomies'          => array( 'categorie_anunt' ),
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-format-aside',
		'has_archive'         => true,
		'show_in_rest'        => true,
		'rewrite'             => array( 'slug' => 'anunturi' ),
	);
	register_post_type( 'anunt', $args );
}
add_action( 'init', 'brezoaele_v2_register_anunt_cpt', 0 );

/**
 * Register Custom Taxonomy: Categorie Anunț
 */
function brezoaele_v2_register_anunt_taxonomy() {
	$labels = array(
		'name'              => _x( 'Categorii Anunțuri', 'Taxonomy General Name', 'brezoaele-v2' ),
		'singular_name'     => _x( 'Categorie Anunț', 'Taxonomy Singular Name', 'brezoaele-v2' ),
		'menu_name'         => __( 'Categorii Anunțuri', 'brezoaele-v2' ),
	);
	$args = array(
		'labels'            => $labels,
		'hierarchical'      => true,
		'public'            => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'categorie-anunt' ),
	);
	register_taxonomy( 'categorie_anunt', array( 'anunt' ), $args );
}
add_action( 'init', 'brezoaele_v2_register_anunt_taxonomy', 0 );

/**
 * Register CPT: Firmă / Producător Local
 */
function brezoaele_v2_register_firma_cpt() {
	$labels = array(
		'name'                  => _x( 'Afaceri & Producători', 'Post Type General Name', 'brezoaele-v2' ),
		'singular_name'         => _x( 'Afacere/Producător', 'Post Type Singular Name', 'brezoaele-v2' ),
		'menu_name'             => __( 'Afaceri Locale', 'brezoaele-v2' ),
		'all_items'             => __( 'Toate Afacerile', 'brezoaele-v2' ),
		'add_new_item'          => __( 'Adaugă Afacere Nouă', 'brezoaele-v2' ),
		'edit_item'             => __( 'Editează Afacere', 'brezoaele-v2' ),
		'view_item'             => __( 'Vezi Afacere', 'brezoaele-v2' ),
	);
	$args = array(
		'label'               => __( 'Afacere', 'brezoaele-v2' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
		'taxonomies'          => array( 'tip_afacere' ),
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 6,
		'menu_icon'           => 'dashicons-store',
		'has_archive'         => true,
		'show_in_rest'        => true,
		'rewrite'             => array( 'slug' => 'afaceri-locale' ),
	);
	register_post_type( 'firma', $args );
}
add_action( 'init', 'brezoaele_v2_register_firma_cpt', 0 );

/**
 * Register Taxonomy: Tip Afacere
 */
function brezoaele_v2_register_tip_afacere_taxonomy() {
	$labels = array(
		'name'              => _x( 'Tipuri Afaceri', 'Taxonomy General Name', 'brezoaele-v2' ),
		'singular_name'     => _x( 'Tip Afacere', 'Taxonomy Singular Name', 'brezoaele-v2' ),
	);
	$args = array(
		'labels'            => $labels,
		'hierarchical'      => true,
		'public'            => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'tip-afacere' ),
	);
	register_taxonomy( 'tip_afacere', array( 'firma' ), $args );
}
add_action( 'init', 'brezoaele_v2_register_tip_afacere_taxonomy', 0 );

/**
 * Register CPT: Investiție
 */
function brezoaele_v2_register_investitie_cpt() {
	$labels = array(
		'name'                  => _x( 'Investiții', 'Post Type General Name', 'brezoaele-v2' ),
		'singular_name'         => _x( 'Investiție', 'Post Type Singular Name', 'brezoaele-v2' ),
		'menu_name'             => __( 'Investiții', 'brezoaele-v2' ),
		'all_items'             => __( 'Toate Proiectele', 'brezoaele-v2' ),
		'add_new_item'          => __( 'Adaugă Proiect Nou', 'brezoaele-v2' ),
		'edit_item'             => __( 'Editează Proiect', 'brezoaele-v2' ),
		'view_item'             => __( 'Vezi Proiect', 'brezoaele-v2' ),
	);
	$args = array(
		'label'               => __( 'Investiție', 'brezoaele-v2' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail', 'comments' ),
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 7,
		'menu_icon'           => 'dashicons-chart-area',
		'has_archive'         => true,
		'show_in_rest'        => true,
		'rewrite'             => array( 'slug' => 'investitii' ),
	);
	register_post_type( 'investitie', $args );
}
add_action( 'init', 'brezoaele_v2_register_investitie_cpt', 0 );

/**
 * Register CPT: Sesizare
 */
function brezoaele_v2_register_sesizare_cpt() {
	$labels = array(
		'name'                  => _x( 'Sesizări Civice', 'Post Type General Name', 'brezoaele-v2' ),
		'singular_name'         => _x( 'Sesizare', 'Post Type Singular Name', 'brezoaele-v2' ),
		'menu_name'             => __( 'Sesizări Civice', 'brezoaele-v2' ),
		'all_items'             => __( 'Toate Sesizările', 'brezoaele-v2' ),
		'add_new_item'          => __( 'Adaugă Sesizare', 'brezoaele-v2' ),
		'edit_item'             => __( 'Editează Sesizare', 'brezoaele-v2' ),
		'view_item'             => __( 'Vezi Sesizare', 'brezoaele-v2' ),
	);
	$args = array(
		'label'               => __( 'Sesizare', 'brezoaele-v2' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail' ),
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 8,
		'menu_icon'           => 'dashicons-warning',
		'has_archive'         => false,
		'publicly_queryable'  => true,
		'show_in_rest'        => true,
		'rewrite'             => array( 'slug' => 'sesizari' ),
	);
	register_post_type( 'sesizare', $args );
}
add_action( 'init', 'brezoaele_v2_register_sesizare_cpt', 0 );

/**
 * Register CPT: Întrebare (Forum)
 */
function brezoaele_v2_register_intrebare_cpt() {
	$labels = array(
		'name'                  => _x( 'Discuții Forum', 'Post Type General Name', 'brezoaele-v2' ),
		'singular_name'         => _x( 'Discuție', 'Post Type Singular Name', 'brezoaele-v2' ),
		'menu_name'             => __( 'Forum Q&A', 'brezoaele-v2' ),
		'all_items'             => __( 'Toate Discuțiile', 'brezoaele-v2' ),
		'add_new_item'          => __( 'Adaugă Discuție Nouă', 'brezoaele-v2' ),
		'edit_item'             => __( 'Editează Discuție', 'brezoaele-v2' ),
		'view_item'             => __( 'Vezi Discuție', 'brezoaele-v2' ),
	);
	$args = array(
		'label'               => __( 'Întrebare', 'brezoaele-v2' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'comments' ),
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 9,
		'menu_icon'           => 'dashicons-bubbles',
		'has_archive'         => true,
		'show_in_rest'        => true,
		'rewrite'             => array( 'slug' => 'comunitate' ),
	);
	register_post_type( 'intrebare', $args );
}
add_action( 'init', 'brezoaele_v2_register_intrebare_cpt', 0 );


/**
 * -----------------------------------------------------------------------------
 * META BOXES (Înregistrare câmpuri personalizate în Admin)
 * -----------------------------------------------------------------------------
 */

function brezoaele_v2_add_meta_boxes() {
	// Metabox Anunțuri
	add_meta_box(
		'brezoaele_v2_anunt_details',
		__( 'Detalii Anunț', 'brezoaele-v2' ),
		'brezoaele_v2_anunt_meta_box_callback',
		'anunt',
		'normal',
		'high'
	);

	// Metabox Afaceri / Locații Satelit & Investiții
	add_meta_box(
		'brezoaele_v2_locatie_details',
		__( 'Metadate Hartă & Locație', 'brezoaele-v2' ),
		'brezoaele_v2_locatie_meta_box_callback',
		array( 'firma', 'investitie' ),
		'normal',
		'high'
	);

	// Metabox Investiții
	add_meta_box(
		'brezoaele_v2_investitie_details',
		__( 'Detalii Proiect Investiție', 'brezoaele-v2' ),
		'brezoaele_v2_investitie_meta_box_callback',
		'investitie',
		'normal',
		'high'
	);

	// Metabox Sesizări Civice
	add_meta_box(
		'brezoaele_v2_sesizare_details',
		__( 'Informații Contact & Stare Sesizare', 'brezoaele-v2' ),
		'brezoaele_v2_sesizare_meta_box_callback',
		'sesizare',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'brezoaele_v2_add_meta_boxes' );

// 1. Callback Metabox Anunț
function brezoaele_v2_anunt_meta_box_callback( $post ) {
	wp_nonce_field( 'brezoaele_v2_save_anunt_meta_action', 'brezoaele_v2_anunt_nonce' );
	$pret    = get_post_meta( $post->ID, '_anunt_pret', true );
	$telefon = get_post_meta( $post->ID, '_anunt_telefon', true );
	$locatie = get_post_meta( $post->ID, '_anunt_locatie', true );
	$nume    = get_post_meta( $post->ID, '_anunt_nume', true );
	?>
	<div style="padding: 10px 0;">
		<p style="margin-bottom: 12px;">
			<label for="anunt_pret"><strong>Preț (ex: 8 RON sau Negociabil):</strong></label><br>
			<input type="text" id="anunt_pret" name="anunt_pret" value="<?php echo esc_attr( $pret ); ?>" class="widefat" style="margin-top:5px; padding:6px; border-radius:0; border:1px solid #72777c;">
		</p>
		<p style="margin-bottom: 12px;">
			<label for="anunt_telefon"><strong>Număr de Telefon:</strong></label><br>
			<input type="text" id="anunt_telefon" name="anunt_telefon" value="<?php echo esc_attr( $telefon ); ?>" class="widefat" style="margin-top:5px; padding:6px; border-radius:0; border:1px solid #72777c;">
		</p>
		<p style="margin-bottom: 12px;">
			<label for="anunt_locatie"><strong>Locație (ex: Brezoaele):</strong></label><br>
			<input type="text" id="anunt_locatie" name="anunt_locatie" value="<?php echo esc_attr( $locatie ); ?>" class="widefat" style="margin-top:5px; padding:6px; border-radius:0; border:1px solid #72777c;">
		</p>
		<p style="margin-bottom: 12px;">
			<label for="anunt_nume"><strong>Nume Contact:</strong></label><br>
			<input type="text" id="anunt_nume" name="anunt_nume" value="<?php echo esc_attr( $nume ); ?>" class="widefat" style="margin-top:5px; padding:6px; border-radius:0; border:1px solid #72777c;">
		</p>
	</div>
	<?php
}

// 2. Callback Metabox Locație / Firmă
function brezoaele_v2_locatie_meta_box_callback( $post ) {
	wp_nonce_field( 'brezoaele_v2_save_locatie_meta_action', 'brezoaele_v2_locatie_nonce' );
	$lat     = get_post_meta( $post->ID, '_locatie_lat', true );
	$lng     = get_post_meta( $post->ID, '_locatie_lng', true );
	$telefon = get_post_meta( $post->ID, '_locatie_telefon', true );
	$program = get_post_meta( $post->ID, '_locatie_program', true );
	$website = get_post_meta( $post->ID, '_locatie_website', true );
	$email   = get_post_meta( $post->ID, '_locatie_email', true );
	$persoana = get_post_meta( $post->ID, '_locatie_persoana_contact', true );
	?>
	<div style="padding: 10px 0;">
		<p style="margin-bottom: 12px;">
			<label for="locatie_lat"><strong>Coordonată Latitudine (ex: 44.5712):</strong></label><br>
			<input type="text" id="locatie_lat" name="locatie_lat" value="<?php echo esc_attr( $lat ); ?>" class="widefat" style="margin-top:5px; padding:6px; border-radius:0; border:1px solid #72777c;">
		</p>
		<p style="margin-bottom: 12px;">
			<label for="locatie_lng"><strong>Coordonată Longitudine (ex: 25.7925):</strong></label><br>
			<input type="text" id="locatie_lng" name="locatie_lng" value="<?php echo esc_attr( $lng ); ?>" class="widefat" style="margin-top:5px; padding:6px; border-radius:0; border:1px solid #72777c;">
		</p>
		<p style="margin-bottom: 12px;">
			<label for="locatie_telefon"><strong>Număr de Telefon:</strong></label><br>
			<input type="text" id="locatie_telefon" name="locatie_telefon" value="<?php echo esc_attr( $telefon ); ?>" class="widefat" style="margin-top:5px; padding:6px; border-radius:0; border:1px solid #72777c;">
		</p>
		<p style="margin-bottom: 12px;">
			<label for="locatie_program"><strong>Program de Funcționare (ex: Luni-Vineri 08:00-16:00):</strong></label><br>
			<input type="text" id="locatie_program" name="locatie_program" value="<?php echo esc_attr( $program ); ?>" class="widefat" style="margin-top:5px; padding:6px; border-radius:0; border:1px solid #72777c;">
		</p>
		<p style="margin-bottom: 12px;">
			<label for="locatie_email"><strong>Adresă Email:</strong></label><br>
			<input type="email" id="locatie_email" name="locatie_email" value="<?php echo esc_attr( $email ); ?>" class="widefat" style="margin-top:5px; padding:6px; border-radius:0; border:1px solid #72777c;">
		</p>
		<p style="margin-bottom: 12px;">
			<label for="locatie_website"><strong>Website / Pagina Socială:</strong></label><br>
			<input type="url" id="locatie_website" name="locatie_website" value="<?php echo esc_attr( $website ); ?>" class="widefat" style="margin-top:5px; padding:6px; border-radius:0; border:1px solid #72777c;">
		</p>
		<p style="margin-bottom: 12px;">
			<label for="locatie_persoana_contact"><strong>Persoană de Contact:</strong></label><br>
			<input type="text" id="locatie_persoana_contact" name="locatie_persoana_contact" value="<?php echo esc_attr( $persoana ); ?>" class="widefat" style="margin-top:5px; padding:6px; border-radius:0; border:1px solid #72777c;">
		</p>
	</div>
	<?php
}

// 3. Callback Metabox Investiție
function brezoaele_v2_investitie_meta_box_callback( $post ) {
	wp_nonce_field( 'brezoaele_v2_save_invest_meta_action', 'brezoaele_v2_invest_nonce' );
	$stadiu = get_post_meta( $post->ID, '_investitie_stadiu', true );
	$buget  = get_post_meta( $post->ID, '_investitie_buget', true );
	$sursa  = get_post_meta( $post->ID, '_investitie_sursa', true );
	?>
	<div style="padding: 10px 0;">
		<p style="margin-bottom: 12px;">
			<label for="investitie_stadiu"><strong>Stadiu Proiect (ex: Planificat, În derulare, Finalizat):</strong></label><br>
			<input type="text" id="investitie_stadiu" name="investitie_stadiu" value="<?php echo esc_attr( $stadiu ); ?>" class="widefat" style="margin-top:5px; padding:6px; border-radius:0; border:1px solid #72777c;">
		</p>
		<p style="margin-bottom: 12px;">
			<label for="investitie_buget"><strong>Buget Estimat (ex: 150.000 EUR):</strong></label><br>
			<input type="text" id="investitie_buget" name="investitie_buget" value="<?php echo esc_attr( $buget ); ?>" class="widefat" style="margin-top:5px; padding:6px; border-radius:0; border:1px solid #72777c;">
		</p>
		<p style="margin-bottom: 12px;">
			<label for="investitie_sursa"><strong>Sursă de Finanțare (ex: Buget local, PNRR):</strong></label><br>
			<input type="text" id="investitie_sursa" name="investitie_sursa" value="<?php echo esc_attr( $sursa ); ?>" class="widefat" style="margin-top:5px; padding:6px; border-radius:0; border:1px solid #72777c;">
		</p>
	</div>
	<?php
}

// 4. Callback Metabox Sesizare
function brezoaele_v2_sesizare_meta_box_callback( $post ) {
	wp_nonce_field( 'brezoaele_v2_save_sesizare_meta_action', 'brezoaele_v2_sesizare_nonce' );
	$nume    = get_post_meta( $post->ID, '_sesizare_nume', true );
	$email   = get_post_meta( $post->ID, '_sesizare_email', true );
	$telefon = get_post_meta( $post->ID, '_sesizare_telefon', true );
	$stare   = get_post_meta( $post->ID, '_sesizare_stare', true );
	?>
	<div style="padding: 10px 0;">
		<p style="margin-bottom: 12px;">
			<label for="sesizare_nume"><strong>Nume Solicitant:</strong></label><br>
			<input type="text" id="sesizare_nume" name="sesizare_nume" value="<?php echo esc_attr( $nume ); ?>" class="widefat" style="margin-top:5px; padding:6px; border-radius:0; border:1px solid #72777c;">
		</p>
		<p style="margin-bottom: 12px;">
			<label for="sesizare_email"><strong>Adresă Email:</strong></label><br>
			<input type="email" id="sesizare_email" name="sesizare_email" value="<?php echo esc_attr( $email ); ?>" class="widefat" style="margin-top:5px; padding:6px; border-radius:0; border:1px solid #72777c;">
		</p>
		<p style="margin-bottom: 12px;">
			<label for="sesizare_telefon"><strong>Telefon Contact:</strong></label><br>
			<input type="text" id="sesizare_telefon" name="sesizare_telefon" value="<?php echo esc_attr( $telefon ); ?>" class="widefat" style="margin-top:5px; padding:6px; border-radius:0; border:1px solid #72777c;">
		</p>
		<p style="margin-bottom: 12px;">
			<label for="sesizare_stare"><strong>Stare Sesizare (ex: Nouă, În analiză, Rezolvată):</strong></label><br>
			<input type="text" id="sesizare_stare" name="sesizare_stare" value="<?php echo esc_attr( $stare ); ?>" class="widefat" style="margin-top:5px; padding:6px; border-radius:0; border:1px solid #72777c;">
		</p>
	</div>
	<?php
}

// 5. Salvare Metabox-uri securizat
function brezoaele_v2_save_post_metadata( $post_id ) {
	// Verificăm salvarea automată
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// 1. Salvare Anunț
	if ( isset( $_POST['brezoaele_v2_anunt_nonce'] ) && wp_verify_nonce( $_POST['brezoaele_v2_anunt_nonce'], 'brezoaele_v2_save_anunt_meta_action' ) ) {
		if ( current_user_can( 'edit_post', $post_id ) ) {
			if ( isset( $_POST['anunt_pret'] ) ) {
				update_post_meta( $post_id, '_anunt_pret', sanitize_text_field( $_POST['anunt_pret'] ) );
			}
			if ( isset( $_POST['anunt_telefon'] ) ) {
				update_post_meta( $post_id, '_anunt_telefon', sanitize_text_field( $_POST['anunt_telefon'] ) );
			}
			if ( isset( $_POST['anunt_locatie'] ) ) {
				update_post_meta( $post_id, '_anunt_locatie', sanitize_text_field( $_POST['anunt_locatie'] ) );
			}
			if ( isset( $_POST['anunt_nume'] ) ) {
				update_post_meta( $post_id, '_anunt_nume', sanitize_text_field( $_POST['anunt_nume'] ) );
			}
		}
	}

	// 2. Salvare Locație
	if ( isset( $_POST['brezoaele_v2_locatie_nonce'] ) && wp_verify_nonce( $_POST['brezoaele_v2_locatie_nonce'], 'brezoaele_v2_save_locatie_meta_action' ) ) {
		if ( current_user_can( 'edit_post', $post_id ) ) {
			if ( isset( $_POST['locatie_lat'] ) ) {
				update_post_meta( $post_id, '_locatie_lat', sanitize_text_field( $_POST['locatie_lat'] ) );
			}
			if ( isset( $_POST['locatie_lng'] ) ) {
				update_post_meta( $post_id, '_locatie_lng', sanitize_text_field( $_POST['locatie_lng'] ) );
			}
			if ( isset( $_POST['locatie_telefon'] ) ) {
				update_post_meta( $post_id, '_locatie_telefon', sanitize_text_field( $_POST['locatie_telefon'] ) );
			}
			if ( isset( $_POST['locatie_program'] ) ) {
				update_post_meta( $post_id, '_locatie_program', sanitize_text_field( $_POST['locatie_program'] ) );
			}
			if ( isset( $_POST['locatie_website'] ) ) {
				update_post_meta( $post_id, '_locatie_website', esc_url_raw( $_POST['locatie_website'] ) );
			}
			if ( isset( $_POST['locatie_email'] ) ) {
				update_post_meta( $post_id, '_locatie_email', sanitize_email( $_POST['locatie_email'] ) );
			}
			if ( isset( $_POST['locatie_persoana_contact'] ) ) {
				update_post_meta( $post_id, '_locatie_persoana_contact', sanitize_text_field( $_POST['locatie_persoana_contact'] ) );
			}
		}
	}

	// 3. Salvare Investiție
	if ( isset( $_POST['brezoaele_v2_invest_nonce'] ) && wp_verify_nonce( $_POST['brezoaele_v2_invest_nonce'], 'brezoaele_v2_save_invest_meta_action' ) ) {
		if ( current_user_can( 'edit_post', $post_id ) ) {
			if ( isset( $_POST['investitie_stadiu'] ) ) {
				update_post_meta( $post_id, '_investitie_stadiu', sanitize_text_field( $_POST['investitie_stadiu'] ) );
			}
			if ( isset( $_POST['investitie_buget'] ) ) {
				update_post_meta( $post_id, '_investitie_buget', sanitize_text_field( $_POST['investitie_buget'] ) );
			}
			if ( isset( $_POST['investitie_sursa'] ) ) {
				update_post_meta( $post_id, '_investitie_sursa', sanitize_text_field( $_POST['investitie_sursa'] ) );
			}
		}
	}

	// 4. Salvare Sesizare
	if ( isset( $_POST['brezoaele_v2_sesizare_nonce'] ) && wp_verify_nonce( $_POST['brezoaele_v2_sesizare_nonce'], 'brezoaele_v2_save_sesizare_meta_action' ) ) {
		if ( current_user_can( 'edit_post', $post_id ) ) {
			if ( isset( $_POST['sesizare_nume'] ) ) {
				update_post_meta( $post_id, '_sesizare_nume', sanitize_text_field( $_POST['sesizare_nume'] ) );
			}
			if ( isset( $_POST['sesizare_email'] ) ) {
				update_post_meta( $post_id, '_sesizare_email', sanitize_email( $_POST['sesizare_email'] ) );
			}
			if ( isset( $_POST['sesizare_telefon'] ) ) {
				update_post_meta( $post_id, '_sesizare_telefon', sanitize_text_field( $_POST['sesizare_telefon'] ) );
			}
			if ( isset( $_POST['sesizare_stare'] ) ) {
				update_post_meta( $post_id, '_sesizare_stare', sanitize_text_field( $_POST['sesizare_stare'] ) );
			}
		}
	}
}
add_action( 'save_post', 'brezoaele_v2_save_post_metadata' );


/**
 * -----------------------------------------------------------------------------
 * ACTIVATION ROUTINES (Regenerare automată reguli permalinks la activarea temei)
 * -----------------------------------------------------------------------------
 */
function brezoaele_v2_theme_activation() {
	// Înregistrăm din nou CPT-urile preventiv
	brezoaele_v2_register_anunt_cpt();
	brezoaele_v2_register_anunt_taxonomy();
	brezoaele_v2_register_firma_cpt();
	brezoaele_v2_register_tip_afacere_taxonomy();
	brezoaele_v2_register_investitie_cpt();
	brezoaele_v2_register_sesizare_cpt();
	brezoaele_v2_register_intrebare_cpt();

	// Flush rules
	flush_rewrite_rules( true );
}
add_action( 'after_switch_theme', 'brezoaele_v2_theme_activation' );

/**
 * -----------------------------------------------------------------------------
 * THEME OPTIONS & CUSTOMIZER (Permite selectarea imaginii Hero din Media Library)
 * -----------------------------------------------------------------------------
 */
function brezoaele_v2_customize_register( $wp_customize ) {
	// Înregistrare Secțiune: Opțiuni Brezoaele V2
	$wp_customize->add_section( 'brezoaele_v2_theme_section', array(
		'title'       => __( 'Setări Brezoaele V2', 'brezoaele-v2' ),
		'priority'    => 30,
		'description' => __( 'Opțiuni de personalizare vizuală pentru portal.', 'brezoaele-v2' ),
	) );

	// Înregistrare Setare: Imagine Fundal Hero
	$wp_customize->add_setting( 'brezoaele_v2_hero_bg', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	) );

	// Înregistrare Control: Selector Imagine
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'brezoaele_v2_hero_bg', array(
		'label'    => __( 'Imagine Fundal Secțiune Hero', 'brezoaele-v2' ),
		'section'  => 'brezoaele_v2_theme_section',
		'settings' => 'brezoaele_v2_hero_bg',
	) ) );
}
add_action( 'customize_register', 'brezoaele_v2_customize_register' );

/**
 * -----------------------------------------------------------------------------
 * QUERY LIMITATION (Limitează paginația arhivei de categorii și blog la 9 articole)
 * -----------------------------------------------------------------------------
 */
function brezoaele_v2_limit_category_posts_per_page( $query ) {
	if ( ! is_admin() && $query->is_main_query() && ( $query->is_category() || $query->is_home() || $query->is_tag() ) ) {
		$query->set( 'posts_per_page', 9 );
	}
}
add_action( 'pre_get_posts', 'brezoaele_v2_limit_category_posts_per_page' );

/**
 * Callback function for custom comments layout and styling.
 */
function brezoaele_v2_comment_callback( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	?>
	<li <?php comment_class( 'comment-item' ); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>" style="padding: 16px; border: 1px solid var(--color-border); border-radius: var(--border-radius-md); background: #fafafa; margin-bottom: 12px; transition: background-color 0.2s ease;">
			<div style="display: flex; align-items: flex-start; gap: 12px; margin-bottom: 8px;">
				<div style="border-radius: 50%; overflow: hidden; border: 1px solid var(--color-border); flex-shrink: 0; width: 44px; height: 44px;">
					<?php echo get_avatar( $comment, $args['avatar_size'] ); ?>
				</div>
				<div style="flex-grow: 1;">
					<div style="font-weight: 800; font-size: 0.95rem; color: var(--color-text-dark);"><?php comment_author_link(); ?></div>
					<div style="font-size: 0.75rem; color: var(--color-text-muted);">
						<?php printf( esc_html__( '%1$s la %2$s', 'brezoaele-v2' ), get_comment_date(), get_comment_time() ); ?>
						<?php edit_comment_link( __( '(Editează)', 'brezoaele-v2' ), '  ', '' ); ?>
					</div>
				</div>
			</div>
			
			<div class="comment-content" style="font-size: 0.9rem; line-height: 1.6; color: var(--color-text-dark);">
				<?php if ( $comment->comment_approved == '0' ) : ?>
					<p style="font-style: italic; color: #b45309; font-size: 0.85rem; margin-bottom: 6px;">⚠️ Comentariul tău este în curs de moderare.</p>
				<?php endif; ?>
				<?php comment_text(); ?>
			</div>
			
			<div class="reply" style="margin-top: 8px; font-weight: 700; font-size: 0.8rem; text-transform: uppercase;">
				<?php
				comment_reply_link( array_merge( $args, array(
					'reply_text' => __( 'Răspunde &rarr;', 'brezoaele-v2' ),
					'depth'      => $depth,
					'max_depth'  => $args['max_depth']
				) ) );
				?>
			</div>
		</div>
	<?php
}

