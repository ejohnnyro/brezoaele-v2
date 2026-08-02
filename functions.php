<?php
/**
 * Brezoaele V2 functions and definitions.
 *
 * @package Brezoaele_V2
 */



// Include authentication and user role handler
require_once get_template_directory() . '/inc/class-auth.php';

// Auto-load brezoaele-payments classes if plugin is not activated yet
if ( ! class_exists( 'Brezoaele_Orders_DB' ) ) {
	$payments_dir = WP_PLUGIN_DIR . '/brezoaele-payments/';
	if ( file_exists( $payments_dir . 'includes/class-orders-db.php' ) ) {
		require_once $payments_dir . 'includes/class-euplatesc.php';
		require_once $payments_dir . 'includes/class-orders-db.php';
		require_once $payments_dir . 'includes/class-invoice-downloader.php';
		require_once $payments_dir . 'includes/class-admin-settings.php';
		Brezoaele_Orders_DB::init_db();
		Brezoaele_Payments_Admin::init();
		Brezoaele_Invoice_Downloader::init();
	}
}

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
 * Title filter fallback for SEO Framework & custom page templates
 */
function brezoaele_v2_filter_title( $title = '', $sep = '|' ) {
	if ( is_feed() ) {
		return $title;
	}
	if ( is_front_page() || is_home() ) {
		return get_bloginfo( 'name' ) . ' | ' . get_bloginfo( 'description' );
	}
	if ( is_singular() ) {
		$custom_title = get_the_title();
		if ( ! empty( $custom_title ) ) {
			return $custom_title . ' | ';
		}
	}
	return $title;
}
add_filter( 'wp_title', 'brezoaele_v2_filter_title', 10, 2 );
add_filter( 'pre_get_document_title', 'brezoaele_v2_filter_title', 10, 2 );

/**
 * Append Account link to Primary WordPress Menu
 */
function brezoaele_v2_add_account_menu_item( $items, $args ) {
	if ( isset( $args->theme_location ) && 'menu-1' === $args->theme_location ) {
		$label = is_user_logged_in() ? 'Contul Meu' : 'Conectare';
		$url   = home_url( '/contul-meu/' );
		$items .= '<li class="menu-item nav-account-item"><a href="' . esc_url( $url ) . '" title="' . esc_attr( $label ) . '" class="nav-account-link"><span class="menu-account-icon">👤</span><span class="menu-account-text">' . esc_html( $label ) . '</span></a></li>';
	}
	return $items;
}
add_filter( 'wp_nav_menu_items', 'brezoaele_v2_add_account_menu_item', 10, 2 );


/**
 * Auto-ensure required pages for Payments, Auth, and Business Map
 */
function brezoaele_ensure_payment_pages() {
	$pages = array(
		'contul-meu'                => array( 'title' => 'Contul Meu', 'template' => 'page-contul-meu.php' ),
		'payment-callback'          => array( 'title' => 'Payment Callback Webhook', 'template' => 'page-payment-callback.php' ),
		'payment-success'           => array( 'title' => 'Plată Reușită', 'template' => 'page-payment-success.php' ),
		'adauga-anunt'              => array( 'title' => 'Adaugă Anunț', 'template' => 'page-adauga-anunt.php' ),
		'solicita-adaugare-afacere' => array( 'title' => 'Solicită Adăugare Afacere', 'template' => 'page-solicita-adaugare-afacere.php' ),
	);

	foreach ( $pages as $slug => $data ) {
		$page = get_page_by_path( $slug );
		if ( ! $page ) {
			$page_id = wp_insert_post( array(
				'post_title'  => $data['title'],
				'post_name'   => $slug,
				'post_status' => 'publish',
				'post_type'   => 'page',
			) );
			if ( $page_id && ! is_wp_error( $page_id ) ) {
				update_post_meta( $page_id, '_wp_page_template', $data['template'] );
			}
		} else {
			update_post_meta( $page->ID, '_wp_page_template', $data['template'] );
		}
	}
}
add_action( 'init', 'brezoaele_ensure_payment_pages' );


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
		wp_enqueue_style( 'leaflet-cluster-css', 'https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css', array(), '1.4.1' );
		wp_enqueue_style( 'leaflet-cluster-default-css', 'https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css', array(), '1.4.1' );

		wp_enqueue_script( 'leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), '1.9.4', true );
		wp_enqueue_script( 'leaflet-cluster-js', 'https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js', array( 'leaflet-js' ), '1.4.1', true );

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

		wp_enqueue_script( 'brezoaele-map', get_template_directory_uri() . '/js/map.js', array( 'leaflet-cluster-js' ), '1.0.4', true );
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
 * Automatically seed taxonomy 'categorie_anunt' hierarchy
 */
function brezoaele_seed_anunt_categories() {
	if ( get_option( 'brezoaele_anunt_categories_seeded_v1' ) ) {
		return;
	}

	$taxonomy = 'categorie_anunt';
	if ( ! taxonomy_exists( $taxonomy ) ) {
		return;
	}

	$categories_data = array(
		'Auto și Transport' => array(
			'Autoturisme',
			'Motociclete și ATV-uri',
			'Camioane și utilitare',
			'Piese și accesorii auto',
			'Întreținere și service auto (oferte)',
			'Închirieri auto și transport persoane',
		),
		'Imobiliare și Construcții' => array(
			'Vânzare - Terenuri (agricol / intravilan / extravilan)',
			'Vânzare - Case și vile',
			'Vânzare - Spații comerciale',
			'Vânzare - Apartamente',
			'Închiriere - Locuințe (apartamente, case)',
			'Închiriere - Spații comerciale',
			'Închiriere - Terenuri',
			'Construcții și amenajări interioare (oferte)',
			'Materiale de construcție',
		),
		'Agricultură și Creșterea Animalelor' => array(
			'Cereale și oleaginoase',
			'Legume și fructe',
			'Plante medicinale și mirodenii',
			'Vaci și porci',
			'Oi și capre',
			'Păsări de curte',
			'Câini, pisici și alte animale',
			'Furaje și nutrețuri',
			'Utilaje și echipamente agricole',
			'Irigații și sisteme de udare',
			'Servicii agricole (arături, transport, recoltat)',
		),
		'Utilaje, Scule și Echipamente' => array(
			'Utilaje agricole și de construcții',
			'Scule electrice și manuale',
			'Echipamente pentru ateliere și garaje',
			'Utilaje pentru industria alimentară și panificație',
			'Închirieri utilaje și echipamente',
			'Piese de schimb pentru utilaje',
		),
		'Servicii' => array(
			'Instalații sanitare și termice',
			'Electricitate și automatizări',
			'Tâmplărie și mobilier',
			'Zidărie și finisaje',
			'Grădinărit și peisagistică',
			'Curățenie și igienizare',
			'Contabilitate și fiscalitate',
			'Asistență juridică',
			'Consultanță agricolă',
			'Transport și mutări',
			'Reparații și întreținere auto',
			'Sănătate și îngrijire personală',
		),
		'Produse și Bunuri' => array(
			'Mobilier și decorațiuni',
			'Electronice și electrocasnice',
			'Îmbrăcăminte și încălțăminte',
			'Jucării și articole pentru copii',
			'Articole pentru casă și grădină',
			'Produse de îngrijire personală',
			'Lemn de foc, peleți și combustibili',
			'Materiale de construcții și finisaje',
		),
		'Evenimente și Comunitate' => array(
			'Zilele comunei, bâlciuri și târguri',
			'Adunări și întâlniri locale',
			'Petreceri private (nunți, botezuri, aniversări)',
			'Voluntariat și acțiuni de binefacere',
			'Cursuri, ateliere și educație',
			'Excursii și tabere',
			'Spectacole, concerte și proiecții de film',
		),
		'Angajări și Colaborări' => array(
			'Muncă calificată',
			'Muncă necalificată',
			'Funcții administrative și de birou',
			'Căutări de angajați (servicii oferite)',
			'Colaborări și parteneriate',
		),
		'Animale și Animale de Companie' => array(
			'Câini, pisici, rozătoare',
			'Accesorii și hrană pentru animale',
			'Servicii veterinare',
			'Păsări și animale pentru fermă',
			'Hrană și furaje pentru animale',
			'Îngrijire și dresaj',
		),
		'Diverse' => array(
			'Obiecte pierdute/găsite',
			'Donări și schimburi',
			'Anunțuri de căutare („caut...”, „cine face...?”)',
			'Alte anunțuri neîncadrabile',
		),
	);

	foreach ( $categories_data as $parent_name => $children ) {
		$parent_term = term_exists( $parent_name, $taxonomy );
		if ( ! $parent_term ) {
			$parent_term = wp_insert_term( $parent_name, $taxonomy );
		}
		if ( ! is_wp_error( $parent_term ) && isset( $parent_term['term_id'] ) ) {
			$parent_id = $parent_term['term_id'];
			foreach ( $children as $child_name ) {
				if ( ! term_exists( $child_name, $taxonomy ) ) {
					wp_insert_term( $child_name, $taxonomy, array( 'parent' => $parent_id ) );
				}
			}
		}
	}

	update_option( 'brezoaele_anunt_categories_seeded_v1', 1 );
}
add_action( 'init', 'brezoaele_seed_anunt_categories', 99 );


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
		'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments', 'custom-fields' ),
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
 * Admin Metabox for Business Gallery Photos
 */
function brezoaele_register_firma_gallery_metabox() {
	add_meta_box(
		'brezoaele_firma_gallery_box',
		'🖼️ Galerie Foto Suplimentară Afacere',
		'brezoaele_render_firma_gallery_metabox',
		'firma',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'brezoaele_register_firma_gallery_metabox' );

function brezoaele_render_firma_gallery_metabox( $post ) {
	$gallery_ids = get_post_meta( $post->ID, '_firma_galerie', true );
	if ( ! is_array( $gallery_ids ) ) {
		$gallery_ids = array();
	}
	?>
	<div style="display:flex; flex-wrap:wrap; gap:12px; margin-bottom:12px;">
		<?php foreach ( $gallery_ids as $attachment_id ) : ?>
			<?php $img_src = wp_get_attachment_image_url( $attachment_id, 'thumbnail' ); ?>
			<?php if ( $img_src ) : ?>
				<div style="border:1px solid #cbd5e1; border-radius:6px; padding:4px; background:#fff; text-align:center;">
					<img src="<?php echo esc_url( $img_src ); ?>" style="width:80px; height:80px; object-fit:cover; display:block; border-radius:4px; margin-bottom:4px;">
					<span style="font-size:0.7rem; color:#64748b;">ID: <?php echo esc_html( $attachment_id ); ?></span>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
		<?php if ( empty( $gallery_ids ) ) : ?>
			<p style="color:#64748b; font-style:italic;">Nicio poză suplimentară adăugată în galerie.</p>
		<?php endif; ?>
	</div>
	<?php
}


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
			<label for="investitie_stadiu"><strong>Stadiu Proiect (Stadiu implementare):</strong></label><br>
			<select id="investitie_stadiu" name="investitie_stadiu" class="widefat" style="margin-top:5px; padding:6px; border-radius:0; border:1px solid #72777c;">
				<?php
				$stadii_options = array(
					'În derulare' => 'În derulare (Proiect activ)',
					'Planificat'  => 'Planificat (În pregătire / Licitație)',
					'Finalizat'   => 'Finalizat (Recepționat / Încheiat)',
					'Amânat'      => 'Amânat (Sistat temporar)',
					'Anulat'      => 'Anulat (Neaprobat / Stopat)',
				);
				$current_stadiu = ! empty( $stadiu ) ? $stadiu : 'În derulare';
				foreach ( $stadii_options as $val => $lbl ) :
				?>
					<option value="<?php echo esc_attr( $val ); ?>" <?php selected( strtolower( trim( $current_stadiu ) ), strtolower( trim( $val ) ) ); ?>>
						<?php echo esc_html( $lbl ); ?>
					</option>
				<?php endforeach; ?>
			</select>
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

	// --- SETĂRI SUBSOL (FOOTER) ---
	// Titlu Despre Proiect
	$wp_customize->add_setting( 'brezoaele_v2_footer_about_title', array(
		'type'              => 'option',
		'default'           => 'Despre Proiect',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'brezoaele_v2_footer_about_title', array(
		'label'    => __( 'Titlu Coloana 1 Subsol (ex: Despre Proiect)', 'brezoaele-v2' ),
		'section'  => 'brezoaele_v2_theme_section',
		'type'     => 'text',
	) );

	// Text Despre Proiect
	$wp_customize->add_setting( 'brezoaele_v2_footer_about_text', array(
		'type'              => 'option',
		'default'           => 'Comuna Brezoaele.ro este o inițiativă civică independentă dedicată conectării administrației locale, cetățenilor activi și investitorilor.',
		'sanitize_callback' => 'wp_kses_post',
	) );
	$wp_customize->add_control( 'brezoaele_v2_footer_about_text', array(
		'label'    => __( 'Text Coloana 1 Subsol (Despre Proiect)', 'brezoaele-v2' ),
		'section'  => 'brezoaele_v2_theme_section',
		'type'     => 'textarea',
	) );

	// Titlu Administrație
	$wp_customize->add_setting( 'brezoaele_v2_footer_admin_title', array(
		'type'              => 'option',
		'default'           => 'Administrație',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'brezoaele_v2_footer_admin_title', array(
		'label'    => __( 'Titlu Coloana 3 Subsol (ex: Administrație)', 'brezoaele-v2' ),
		'section'  => 'brezoaele_v2_theme_section',
		'type'     => 'text',
	) );

	// Text Administrație
	$wp_customize->add_setting( 'brezoaele_v2_footer_admin_text', array(
		'type'              => 'option',
		'default'           => 'Dezvoltat cu mândrie pentru Brezoaele de către comunitatea locală.',
		'sanitize_callback' => 'wp_kses_post',
	) );
	$wp_customize->add_control( 'brezoaele_v2_footer_admin_text', array(
		'label'    => __( 'Text Coloana 3 Subsol (Administrație)', 'brezoaele-v2' ),
		'section'  => 'brezoaele_v2_theme_section',
		'type'     => 'textarea',
	) );
}
add_action( 'customize_register', 'brezoaele_v2_customize_register' );

/**
 * -----------------------------------------------------------------------------
 * PAGINĂ DEDICATĂ ÎN WP ADMIN PENTRU EDITAREA TEXTELOR DIN SUBSOL (FOOTER)
 * -----------------------------------------------------------------------------
 */
function brezoaele_v2_footer_admin_menu() {
	add_theme_page(
		'Setări Subsol (Footer)',
		'Setări Subsol',
		'manage_options',
		'brezoaele-footer-settings',
		'brezoaele_v2_footer_settings_render'
	);
	add_options_page(
		'Setări Subsol (Footer)',
		'Setări Subsol',
		'manage_options',
		'brezoaele-footer-settings',
		'brezoaele_v2_footer_settings_render'
	);
}
add_action( 'admin_menu', 'brezoaele_v2_footer_admin_menu' );


function brezoaele_v2_footer_settings_render() {
	if ( isset( $_POST['brezoaele_save_footer'] ) && check_admin_referer( 'brezoaele_footer_nonce' ) ) {
		update_option( 'brezoaele_v2_footer_about_title', sanitize_text_field( $_POST['footer_about_title'] ) );
		update_option( 'brezoaele_v2_footer_about_text', wp_kses_post( $_POST['footer_about_text'] ) );
		update_option( 'brezoaele_v2_footer_admin_title', sanitize_text_field( $_POST['footer_admin_title'] ) );
		update_option( 'brezoaele_v2_footer_admin_text', wp_kses_post( $_POST['footer_admin_text'] ) );
		echo '<div class="notice notice-success is-dismissible" style="margin:20px 0;"><p><strong>✅ Setările pentru subsol au fost salvate cu succes!</strong></p></div>';
	}

	$about_title = get_option( 'brezoaele_v2_footer_about_title', 'Despre Proiect' );
	$about_text  = get_option( 'brezoaele_v2_footer_about_text', 'Comuna Brezoaele.ro este o inițiativă civică independentă dedicată conectării administrației locale, cetățenilor activi și investitorilor.' );
	$admin_title = get_option( 'brezoaele_v2_footer_admin_title', 'Administrație' );
	$admin_text  = get_option( 'brezoaele_v2_footer_admin_text', 'Dezvoltat cu mândrie pentru Brezoaele de către comunitatea locală.' );
	?>
	<div class="wrap" style="max-width: 900px; background:#fff; padding:24px; border:1px solid #ccd0d4; border-radius:8px; margin-top:20px;">
		<h1 style="margin-bottom:8px;">⚙️ Setări Texte Subsol (Footer)</h1>
		<p style="color:#64748b; font-size:0.95rem; margin-bottom:24px;">Editează mai jos titlurile și textele din subsolul site-ului Brezoaele.ro. Modificările se salvează direct pe site.</p>
		
		<form method="post" action="">
			<?php wp_nonce_field( 'brezoaele_footer_nonce' ); ?>
			
			<div style="background:#f8fafc; padding:16px; border:1px solid #e2e8f0; border-radius:6px; margin-bottom:20px;">
				<h3 style="margin-top:0; color:#0f172a;">📌 Coloana 1: Despre Proiect</h3>
				<p>
					<label for="footer_about_title"><strong>Titlu Coloana 1:</strong></label><br/>
					<input name="footer_about_title" type="text" id="footer_about_title" value="<?php echo esc_attr( $about_title ); ?>" class="large-text" style="margin-top:4px;">
				</p>
				<p>
					<label for="footer_about_text"><strong>Text Coloana 1:</strong></label><br/>
					<textarea name="footer_about_text" id="footer_about_text" rows="5" class="large-text" style="margin-top:4px; font-family:monospace;"><?php echo esc_textarea( $about_text ); ?></textarea>
					<span class="description">Suportă HTML (ex: <code>&lt;br&gt;</code> pentru rând nou, <code>&lt;strong&gt;text&lt;/strong&gt;</code>, <code>&lt;a href="..."&gt;link&lt;/a&gt;</code>).</span>
				</p>
			</div>

			<div style="background:#f8fafc; padding:16px; border:1px solid #e2e8f0; border-radius:6px; margin-bottom:20px;">
				<h3 style="margin-top:0; color:#0f172a;">🏢 Coloana 3: Administrație</h3>
				<p>
					<label for="footer_admin_title"><strong>Titlu Coloana 3:</strong></label><br/>
					<input name="footer_admin_title" type="text" id="footer_admin_title" value="<?php echo esc_attr( $admin_title ); ?>" class="large-text" style="margin-top:4px;">
				</p>
				<p>
					<label for="footer_admin_text"><strong>Text Coloana 3:</strong></label><br/>
					<textarea name="footer_admin_text" id="footer_admin_text" rows="5" class="large-text" style="margin-top:4px; font-family:monospace;"><?php echo esc_textarea( $admin_text ); ?></textarea>
					<span class="description">Suportă HTML (ex: <code>&lt;br&gt;</code> pentru rând nou, <code>&lt;strong&gt;text&lt;/strong&gt;</code>, <code>&lt;a href="..."&gt;link&lt;/a&gt;</code>).</span>
				</p>
			</div>

			<p class="submit">
				<input type="submit" name="brezoaele_save_footer" id="submit" class="button button-primary button-hero" value="💾 Salvează Modificările Subsol">
			</p>
		</form>
	</div>
	<?php
}




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
	$post = get_post( $comment->comment_post_ID );

	$is_post_author = ( $comment->user_id > 0 && $post && (int) $comment->user_id === (int) $post->post_author );
	
	$parent_author = '';
	if ( $comment->comment_parent > 0 ) {
		$parent_comment = get_comment( $comment->comment_parent );
		if ( $parent_comment ) {
			$parent_author = get_comment_author( $parent_comment );
		}
	}

	$is_reply = ( $depth > 1 );
	?>
	<li <?php comment_class( 'comment-item' ); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>" 
			 style="padding: 18px; border: 1px solid <?php echo $is_reply ? '#cbd5e1' : 'var(--color-border)'; ?>; border-radius: var(--border-radius-md); background: <?php echo $is_reply ? '#f8fafc' : '#ffffff'; ?>; margin-bottom: 14px; box-shadow: <?php echo $is_reply ? 'none' : '0 2px 6px rgba(0,0,0,0.02)'; ?>; position: relative;">
			
			<div style="display: flex; align-items: flex-start; gap: 12px; margin-bottom: 10px;">
				<div style="border-radius: 50%; overflow: hidden; border: 2px solid <?php echo $is_post_author ? '#047857' : '#cbd5e1'; ?>; flex-shrink: 0; width: 44px; height: 44px;">
					<?php echo get_avatar( $comment, $args['avatar_size'] ); ?>
				</div>
				
				<div style="flex-grow: 1;">
					<div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
						<span style="font-weight: 800; font-size: 0.95rem; color: var(--color-text-dark);"><?php comment_author_link(); ?></span>
						
						<?php if ( $is_post_author ) : ?>
							<span style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; padding: 2px 8px; border-radius: 12px; font-size: 0.7rem; font-weight: 800; letter-spacing: 0.3px;">
								✍️ Autor Articol
							</span>
						<?php endif; ?>

						<?php if ( ! empty( $parent_author ) ) : ?>
							<span style="font-size: 0.82rem; color: #047857; font-weight: 700; background: #f0fdf4; padding: 2px 8px; border-radius: 6px; border: 1px solid #bbf7d0;">
								↳ Răspuns pentru <strong><?php echo esc_html( $parent_author ); ?></strong>
							</span>
						<?php endif; ?>
					</div>

					<div style="font-size: 0.75rem; color: var(--color-text-muted); margin-top: 2px;">
						<?php printf( esc_html__( '%1$s la %2$s', 'brezoaele-v2' ), get_comment_date(), get_comment_time() ); ?>
						<?php edit_comment_link( __( '(Editează)', 'brezoaele-v2' ), ' • ', '' ); ?>
					</div>
				</div>
			</div>
			
			<div class="comment-content" style="font-size: 0.92rem; line-height: 1.6; color: var(--color-text-dark);">
				<?php if ( '0' === (string) $comment->comment_approved ) : ?>
					<p style="font-style: italic; color: #b45309; font-size: 0.85rem; margin-bottom: 6px; background: #fefce8; padding: 6px 10px; border-radius: 6px; border: 1px solid #fef08a;">
						⚠️ Comentariul tău este în curs de moderare.
					</p>
				<?php endif; ?>
				<?php comment_text(); ?>
			</div>
			
			<div class="reply" style="margin-top: 10px; font-weight: 700; font-size: 0.8rem; text-transform: uppercase;">
				<?php
				comment_reply_link( array_merge( $args, array(
					'reply_text' => __( '💬 Răspunde →', 'brezoaele-v2' ),
					'depth'      => $depth,
					'max_depth'  => $args['max_depth']
				) ) );
				?>
			</div>
		</div>
	<?php
}


/**
 * ==========================================================================
 * ENDPOINT-URI CUSTOM REST API (Brezoaele Content CRUD & Meta Manager)
 * ==========================================================================
 */

add_action( 'rest_api_init', function () {
	// Endpoint general pentru listare și creare postări
	register_rest_route( 'brezoaele/v1', '/posts', array(
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'brezoaele_rest_list_posts',
			'permission_callback' => 'brezoaele_rest_permission_check',
		),
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'brezoaele_rest_create_post',
			'permission_callback' => 'brezoaele_rest_permission_check',
		)
	) );

	// Endpoint specific pentru citire, actualizare și ștergere postare după ID
	register_rest_route( 'brezoaele/v1', '/post/(?P<id>\d+)', array(
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'brezoaele_rest_get_post',
			'permission_callback' => 'brezoaele_rest_permission_check',
		),
		array(
			'methods'             => WP_REST_Server::EDITABLE,
			'callback'            => 'brezoaele_rest_update_post',
			'permission_callback' => 'brezoaele_rest_permission_check',
		),
		array(
			'methods'             => WP_REST_Server::DELETABLE,
			'callback'            => 'brezoaele_rest_delete_post',
			'permission_callback' => 'brezoaele_rest_permission_check',
		)
	) );
} );

/**
 * Verifică dacă utilizatorul curent este autentificat și are drept de editare
 */
function brezoaele_rest_permission_check() {
	return current_user_can( 'edit_posts' );
}

/**
 * Returnează toate câmpurile meta asociate unei postări, curățate de array-urile imbricate
 */
function brezoaele_get_all_post_meta( $post_id ) {
	$meta = get_post_meta( $post_id );
	$cleaned_meta = array();
	if ( is_array( $meta ) ) {
		foreach ( $meta as $key => $values ) {
			if ( is_array( $values ) && count( $values ) === 1 ) {
				$cleaned_meta[ $key ] = maybe_unserialize( $values[0] );
			} else {
				$cleaned_meta[ $key ] = array_map( 'maybe_unserialize', $values );
			}
		}
	}
	return $cleaned_meta;
}

/**
 * REST Callback: Listare postări dintr-un Custom Post Type
 */
function brezoaele_rest_list_posts( $request ) {
	$post_type = $request->get_param( 'post_type' ) ? sanitize_text_field( $request->get_param( 'post_type' ) ) : 'post';
	$posts_per_page = $request->get_param( 'posts_per_page' ) ? intval( $request->get_param( 'posts_per_page' ) ) : 20;
	$paged = $request->get_param( 'paged' ) ? intval( $request->get_param( 'paged' ) ) : 1;
	$post_status = $request->get_param( 'post_status' ) ? sanitize_text_field( $request->get_param( 'post_status' ) ) : 'publish';

	$args = array(
		'post_type'      => $post_type,
		'posts_per_page' => $posts_per_page,
		'paged'          => $paged,
		'post_status'    => $post_status,
		'orderby'        => 'ID',
		'order'          => 'DESC'
	);

	$query = new WP_Query( $args );
	$posts = array();

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$id = get_the_ID();
			$posts[] = array(
				'ID'          => $id,
				'post_title'  => get_the_title(),
				'post_status' => get_post_status( $id ),
				'post_type'   => get_post_type( $id ),
				'post_date'   => get_the_date( 'Y-m-d H:i:s' ),
				'meta'        => brezoaele_get_all_post_meta( $id )
			);
		}
		wp_reset_postdata();
	}

	return new WP_REST_Response( array(
		'success'     => true,
		'total_posts' => intval( $query->found_posts ),
		'total_pages' => intval( $query->max_num_pages ),
		'posts'       => $posts
	), 200 );
}

/**
 * REST Callback: Citire postare specifică și toate metadatele ei
 */
function brezoaele_rest_get_post( $request ) {
	$id = intval( $request['id'] );
	$post = get_post( $id );

	if ( ! $post ) {
		return new WP_Error( 'post_not_found', 'Postarea solicitată nu a fost găsită.', array( 'status' => 404 ) );
	}

	return new WP_REST_Response( array(
		'success'      => true,
		'ID'           => $post->ID,
		'post_title'   => $post->post_title,
		'post_content' => $post->post_content,
		'post_status'  => $post->post_status,
		'post_type'    => $post->post_type,
		'post_date'    => $post->post_date,
		'meta'         => brezoaele_get_all_post_meta( $post->ID )
	), 200 );
}

/**
 * REST Callback: Creare postare nouă și adăugare meta custom
 */
function brezoaele_rest_create_post( $request ) {
	$params = $request->get_json_params();
	if ( empty( $params ) ) {
		$params = $request->get_body_params();
	}

	$post_type = ! empty( $params['post_type'] ) ? sanitize_text_field( $params['post_type'] ) : 'post';
	$post_title = ! empty( $params['post_title'] ) ? sanitize_text_field( $params['post_title'] ) : '';
	$post_content = ! empty( $params['post_content'] ) ? wp_kses_post( $params['post_content'] ) : '';
	$post_status = ! empty( $params['post_status'] ) ? sanitize_text_field( $params['post_status'] ) : 'publish';

	if ( empty( $post_title ) ) {
		return new WP_Error( 'missing_title', 'Titlul postării este obligatoriu.', array( 'status' => 400 ) );
	}

	$post_id = wp_insert_post( array(
		'post_type'    => $post_type,
		'post_title'   => $post_title,
		'post_content' => $post_content,
		'post_status'  => $post_status,
	) );

	if ( is_wp_error( $post_id ) ) {
		return $post_id;
	}

	// Actualizare câmpuri meta dacă există
	if ( ! empty( $params['meta'] ) && is_array( $params['meta'] ) ) {
		foreach ( $params['meta'] as $key => $value ) {
			$meta_key = sanitize_key( $key );
			if ( is_array( $value ) ) {
				update_post_meta( $post_id, $meta_key, $value );
			} else {
				update_post_meta( $post_id, $meta_key, sanitize_text_field( $value ) );
			}
		}
	}

	return new WP_REST_Response( array(
		'success' => true,
		'ID'      => $post_id,
		'message' => 'Postarea a fost creată cu succes.'
	), 201 );
}

/**
 * REST Callback: Actualizare postare (titlu, conținut, metadate) după ID
 */
function brezoaele_rest_update_post( $request ) {
	$id = intval( $request['id'] );
	$post = get_post( $id );

	if ( ! $post ) {
		return new WP_Error( 'post_not_found', 'Postarea solicitată nu a fost găsită.', array( 'status' => 404 ) );
	}

	$params = $request->get_json_params();
	if ( empty( $params ) ) {
		$params = $request->get_body_params();
	}

	$update_data = array( 'ID' => $id );

	if ( isset( $params['post_title'] ) ) {
		$update_data['post_title'] = sanitize_text_field( $params['post_title'] );
	}
	if ( isset( $params['post_content'] ) ) {
		$update_data['post_content'] = wp_kses_post( $params['post_content'] );
	}
	if ( isset( $params['post_status'] ) ) {
		$update_data['post_status'] = sanitize_text_field( $params['post_status'] );
	}

	if ( count( $update_data ) > 1 ) {
		$result = wp_update_post( $update_data );
		if ( is_wp_error( $result ) ) {
			return $result;
		}
	}

	// Actualizare metadate dacă există în payload
	if ( isset( $params['meta'] ) && is_array( $params['meta'] ) ) {
		foreach ( $params['meta'] as $key => $value ) {
			$meta_key = sanitize_key( $key );
			if ( is_array( $value ) ) {
				update_post_meta( $id, $meta_key, $value );
			} else {
				update_post_meta( $id, $meta_key, sanitize_text_field( $value ) );
			}
		}
	}

	return new WP_REST_Response( array(
		'success' => true,
		'ID'      => $id,
		'message' => 'Postarea a fost actualizată cu succes.'
	), 200 );
}

/**
 * REST Callback: Ștergere postare (mutare în coșul de gunoi) după ID
 */
function brezoaele_rest_delete_post( $request ) {
	$id = intval( $request['id'] );
	$post = get_post( $id );

	if ( ! $post ) {
		return new WP_Error( 'post_not_found', 'Postarea solicitată nu a fost găsită.', array( 'status' => 404 ) );
	}

	$result = wp_trash_post( $id );

	if ( ! $result ) {
		return new WP_Error( 'delete_failed', 'Nu s-a putut șterge postarea.', array( 'status' => 500 ) );
	}

	return new WP_REST_Response( array(
		'success' => true,
		'ID'      => $id,
		'message' => 'Postarea a fost trimisă în coșul de gunoi.'
	), 200 );
}

/**
 * Înregistrare automată categorii de știri (Locale, Județene, Naționale)
 */
add_action( 'init', 'brezoaele_register_stiri_categories' );
function brezoaele_register_stiri_categories() {
	$categories = array(
		'stiri-locale'    => 'Știri Locale',
		'stiri-judetene'  => 'Știri Județene',
		'stiri-nationale' => 'Știri Naționale',
	);

	foreach ( $categories as $slug => $name ) {
		if ( ! get_term_by( 'slug', $slug, 'category' ) ) {
			wp_insert_term( $name, 'category', array( 'slug' => $slug ) );
		}
	}
}

/**
 * Customizare automată meniu principal:
 * 1. Adăugare iconițe emoji pentru TOATE elementele de meniu.
 * 2. Integrare directă a sub-categoriilor de știri (Locale, Județene, Naționale) în meniul existent "Știri / Informații".
 */
add_filter( 'wp_nav_menu_objects', 'brezoaele_customize_nav_menu_objects', 20, 2 );
function brezoaele_customize_nav_menu_objects( $items, $args ) {
	if ( empty( $items ) || ! is_array( $items ) ) {
		return $items;
	}

	$icon_map = array(
		'Acasă'                  => '🏠',
		'Home'                   => '🏠',
		'Prima'                  => '🏠',
		'Știri / Informații'     => '📰',
		'Știri & Informații'     => '📰',
		'Știri'                  => '📰',
		'Informații'             => '📰',
		'Comunicate'             => '📢',
		'Anunțuri'               => '📢',
		'Alerte'                 => '🚨',
		'Utilități'              => '🚨',
		'Fonduri'                => '🏗️',
		'Dezvoltare'             => '🏗️',
		'Proiecte'               => '🏗️',
		'Dezbateri'              => '💬',
		'Opinii'                 => '💬',
		'Sănătate'               => '🏥',
		'Social'                 => '🏥',
		'Educație'               => '🎓',
		'Cultură'                => '🎓',
		'Școală'                 => '🎓',
		'Administrație'          => '🏛️',
		'Locală'                 => '🏛️',
		'Primărie'               => '🏛️',
		'Consiliu'               => '🏛️',
		'Investiții'             => '🏗️',
		'Firme'                  => '🏢',
		'Afaceri'                => '🏢',
		'Servicii'               => '🏢',
		'Hartă'                  => '🗺️',
		'Harta'                  => '🗺️',
		'Sesizări'               => '📣',
		'Reclamații'             => '📣',
		'Ghid'                   => '📖',
		'Rezident'               => '📖',
		'Vremea'                 => '🌤️',
		'Meteo'                  => '🌤️',
		'Contact'                => '📞',
		'Telefon'                => '📞',
		'Forum'                  => '❓',
		'Întrebări'              => '❓',
		'Despre'                 => '📜',
		'Istoric'                => '📜',
		'Documente'              => '📁',
		'Formulare'              => '📁',
		'Taxe'                   => '💳',
		'Impozite'               => '💳',
		'Galerie'                => '🖼️',
		'Foto'                   => '🖼️',
		'Video'                  => '🎬',
	);

	$stiri_parent_id = 0;
	$processed_items = array();

	foreach ( $items as $item ) {
		// Eliminăm "Mers Microbuz" (ID 1962 sau URL/Titlu cu microbuz) din meniu
		if ( (int) $item->ID === 1962 || false !== mb_stripos( $item->title, 'Microbuz' ) || false !== strpos( $item->url, 'microbuz' ) ) {
			continue;
		}

		// Adăugăm iconiță dacă titlul nu conține deja un emoji

		if ( ! preg_match( '/[\x{1F300}-\x{1F9FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', $item->title ) ) {
			$found_icon = false;
			foreach ( $icon_map as $key => $icon ) {
				if ( false !== mb_stripos( $item->title, $key ) ) {
					$item->title = $icon . ' ' . $item->title;
					$found_icon = true;
					break;
				}
			}
			// Dacă niciun cuvânt cheie nu s-a potrivit, aplicăm o iconiță implicită
			if ( ! $found_icon ) {
				$item->title = '📌 ' . $item->title;
			}
		}

		// Identificăm părintele "Știri / Informații" (ID 1942 sau titlu)
		if ( (int) $item->ID === 1942 || ( false !== mb_stripos( $item->title, 'Știri' ) && 0 === (int) $item->menu_item_parent ) ) {
			$stiri_parent_id = $item->ID;
		}

		$processed_items[] = $item;
	}


	// Injectăm cele 3 categorii de știri ca primele sub-elemente din sub-meniul "Știri / Informații"
	if ( $stiri_parent_id > 0 ) {
		$locale_term    = get_term_by( 'slug', 'stiri-locale', 'category' );
		$judetene_term  = get_term_by( 'slug', 'stiri-judetene', 'category' );
		$nationale_term = get_term_by( 'slug', 'stiri-nationale', 'category' );

		$locale_url    = $locale_term ? esc_url( get_category_link( $locale_term->term_id ) ) : home_url( '/category/stiri-locale/' );
		$judetene_url  = $judetene_term ? esc_url( get_category_link( $judetene_term->term_id ) ) : home_url( '/category/stiri-judetene/' );
		$nationale_url = $nationale_term ? esc_url( get_category_link( $nationale_term->term_id ) ) : home_url( '/category/stiri-nationale/' );

		$stiri_subitems = array(
			array(
				'id'    => 999901,
				'title' => '🏡 Știri Locale',
				'url'   => $locale_url,
			),
			array(
				'id'    => 999902,
				'title' => '🏛️ Știri Județene',
				'url'   => $judetene_url,
			),
			array(
				'id'    => 999903,
				'title' => '🇷🇴 Știri Naționale',
				'url'   => $nationale_url,
			),
		);

		$final_items = array();
		foreach ( $processed_items as $item ) {
			$final_items[] = $item;

			// Dacă am ajuns la părintele "Știri / Informații", inserăm sub-elementele chiar după el
			if ( (int) $item->ID === (int) $stiri_parent_id ) {
				foreach ( $stiri_subitems as $sub ) {
					$dummy = new stdClass();
					$dummy->ID = $sub['id'];
					$dummy->db_id = $sub['id'];
					$dummy->menu_item_parent = $stiri_parent_id;
					$dummy->object_id = $sub['id'];
					$dummy->object = 'custom';
					$dummy->type = 'custom';
					$dummy->type_label = 'Custom Link';
					$dummy->title = $sub['title'];
					$dummy->url = $sub['url'];
					$dummy->target = '';
					$dummy->attr_title = '';
					$dummy->description = '';
					$dummy->classes = array( 'menu-item', 'menu-item-type-custom', 'menu-item-stiri-injected' );
					$dummy->xfn = '';
					$dummy->current = false;
					$dummy->current_item_ancestor = false;
					$dummy->current_item_parent = false;

					$final_items[] = $dummy;
				}
			}
		}
		return $final_items;
	}

	return $processed_items;
}

/**
 * Auto-activare plugin Brezoaele Newsletter
 */
add_action( 'init', 'brezoaele_auto_activate_newsletter_plugin' );
function brezoaele_auto_activate_newsletter_plugin() {
	if ( file_exists( WP_PLUGIN_DIR . '/brezoaele-newsletter/brezoaele-newsletter.php' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		if ( ! is_plugin_active( 'brezoaele-newsletter/brezoaele-newsletter.php' ) ) {
			activate_plugin( 'brezoaele-newsletter/brezoaele-newsletter.php' );
		}
	}
}

/**
 * Helper badge stadiu investiții
 */
function brezoaele_get_investitie_stadiu_badge( $stadiu ) {
	if ( empty( $stadiu ) ) {
		$stadiu = 'În derulare';
	}

	$stadiu_clean = trim( $stadiu );
	$bg = '#ecfdf5';
	$color = '#047857';
	$border = '#a7f3d0';
	$icon = '⚡';

	switch ( mb_strtolower( $stadiu_clean ) ) {
		case 'în derulare':
		case 'in derulare':
			$bg = '#ecfdf5';
			$color = '#047857';
			$border = '#a7f3d0';
			$icon = '⚡';
			$stadiu_clean = 'În derulare';
			break;
		case 'planificat':
			$bg = '#eff6ff';
			$color = '#1d4ed8';
			$border = '#bfdbfe';
			$icon = '📌';
			$stadiu_clean = 'Planificat';
			break;
		case 'finalizat':
		case 'realizat':
			$bg = '#f0fdf4';
			$color = '#15803d';
			$border = '#86efac';
			$icon = '✅';
			$stadiu_clean = 'Finalizat';
			break;
		case 'amânat':
		case 'amanat':
			$bg = '#fefce8';
			$color = '#a16207';
			$border = '#fef08a';
			$icon = '⏳';
			$stadiu_clean = 'Amânat';
			break;
		case 'anulat':
			$bg = '#fef2f2';
			$color = '#b91c1c';
			$border = '#fca5a5';
			$icon = '❌';
			$stadiu_clean = 'Anulat';
			break;
	}

	return sprintf(
		'<span class="investitie-badge" style="background-color:%s; color:%s; border:1px solid %s; font-size:0.75rem; font-weight:800; padding:4px 10px; border-radius:30px; text-transform:uppercase; display:inline-flex; align-items:center; gap:4px; letter-spacing:0.5px;">%s %s</span>',
		esc_attr( $bg ),
		esc_attr( $color ),
		esc_attr( $border ),
		$icon,
		esc_html( $stadiu_clean )
	);
}

/**
 * Interogare & Ordonare Arhivă Investiții după Stadiu și Căutare
 * Ordine cerută: În derulare (1), Planificat (2), Finalizat (3), Amânat (4), Anulat (5)
 */
add_action( 'pre_get_posts', 'brezoaele_investitii_archive_query' );
function brezoaele_investitii_archive_query( $query ) {
	if ( ! is_admin() && $query->is_main_query() && ( is_post_type_archive( 'investitie' ) || $query->get( 'post_type' ) === 'investitie' ) ) {
		
		// 1. Căutare după nume / titlu investiție
		if ( isset( $_GET['s_invest'] ) && ! empty( $_GET['s_invest'] ) ) {
			$query->set( 's', sanitize_text_field( wp_unslash( $_GET['s_invest'] ) ) );
		}

		// 2. Filtrare după stadiu
		if ( isset( $_GET['stadiu'] ) && ! empty( $_GET['stadiu'] ) && $_GET['stadiu'] !== 'toate' ) {
			$stadiu_filter = sanitize_text_field( wp_unslash( $_GET['stadiu'] ) );
			$query->set( 'meta_query', array(
				array(
					'key'     => '_investitie_stadiu',
					'value'   => $stadiu_filter,
					'compare' => '=',
				),
			) );
		}
	}
}

add_filter( 'posts_orderby', 'brezoaele_investitii_custom_orderby', 10, 2 );
function brezoaele_investitii_custom_orderby( $orderby, $query ) {
	if ( ! is_admin() && $query->is_main_query() && ( is_post_type_archive( 'investitie' ) || $query->get( 'post_type' ) === 'investitie' ) ) {
		global $wpdb;
		return "FIELD( (SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id={$wpdb->posts}.ID AND meta_key='_investitie_stadiu' LIMIT 1), 'În derulare', 'Planificat', 'Finalizat', 'Amânat', 'Anulat') ASC, {$wpdb->posts}.post_date DESC";
	}
	return $orderby;
}

/**
 * -----------------------------------------------------------------------------
 * ADMIN METABOX & COLUMN FOR PREMIUM AD ANNOUNCEMENT MANAGEMENT
 * -----------------------------------------------------------------------------
 */
function brezoaele_register_anunt_premium_metabox() {
	add_meta_box(
		'brezoaele_anunt_premium_box',
		'⭐ Pachet Premium Anunț',
		'brezoaele_render_anunt_premium_metabox',
		'anunt',
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes', 'brezoaele_register_anunt_premium_metabox' );

function brezoaele_render_anunt_premium_metabox( $post ) {
	wp_nonce_field( 'brezoaele_save_premium_meta', 'brezoaele_premium_meta_nonce' );
	$is_premium = get_post_meta( $post->ID, '_anunt_is_premium', true );
	$expires_at = get_post_meta( $post->ID, '_anunt_premium_expires', true );
	?>
	<p>
		<label for="anunt_is_premium">
			<input type="checkbox" name="anunt_is_premium" id="anunt_is_premium" value="1" <?php checked( $is_premium, '1' ); ?>>
			<strong>Activează ca Anunț ⭐ PREMIUM</strong>
		</label>
	</p>
	<p>
		<label for="anunt_premium_expires"><strong>Data Expirare Premium:</strong></label><br/>
		<input type="date" name="anunt_premium_expires" id="anunt_premium_expires" value="<?php echo esc_attr( $expires_at ); ?>" class="widefat" style="margin-top:4px;">
		<span class="description">Dacă bifezi Premium și nu pui o dată, se va seta automat pentru +30 de zile.</span>
	</p>
	<?php
}

function brezoaele_save_anunt_premium_meta( $post_id ) {
	if ( ! isset( $_POST['brezoaele_premium_meta_nonce'] ) || ! wp_verify_nonce( $_POST['brezoaele_premium_meta_nonce'], 'brezoaele_save_premium_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['anunt_is_premium'] ) && '1' === $_POST['anunt_is_premium'] ) {
		update_post_meta( $post_id, '_anunt_is_premium', '1' );
		$expires = isset( $_POST['anunt_premium_expires'] ) ? sanitize_text_field( $_POST['anunt_premium_expires'] ) : '';
		if ( empty( $expires ) ) {
			$expires = date( 'Y-m-d', strtotime( '+30 days' ) );
		}
		update_post_meta( $post_id, '_anunt_premium_expires', $expires );
	} else {
		update_post_meta( $post_id, '_anunt_is_premium', '0' );
	}
}
add_action( 'save_post_anunt', 'brezoaele_save_anunt_premium_meta' );

// Custom Admin Column for Premium Status in edit.php?post_type=anunt
function brezoaele_anunt_columns_head( $columns ) {
	$columns['premium_status'] = '⭐ Status Premium';
	return $columns;
}
function brezoaele_anunt_columns_content( $column_name, $post_id ) {
	if ( 'premium_status' === $column_name ) {
		$is_premium = get_post_meta( $post_id, '_anunt_is_premium', true );
		$expires_at = get_post_meta( $post_id, '_anunt_premium_expires', true );
		if ( '1' === $is_premium ) {
			echo '<span style="background:#fef3c7; color:#b45309; padding:4px 8px; border-radius:4px; font-weight:800; display:inline-block;">⭐ PREMIUM</span>';
			if ( ! empty( $expires_at ) ) {
				echo '<br/><small style="color:#64748b;">Expiră: ' . esc_html( $expires_at ) . '</small>';
			}
		} else {
			echo '<span style="color:#94a3b8; font-weight:600;">Standard (Gratuit)</span>';
		}
	}
}
add_filter( 'manage_anunt_posts_columns', 'brezoaele_anunt_columns_head' );
add_action( 'manage_anunt_posts_custom_column', 'brezoaele_anunt_columns_content', 10, 2 );









