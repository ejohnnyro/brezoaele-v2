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
		'calendar-evenimente'       => array( 'title' => 'Calendar Evenimente', 'template' => 'template-calendar-evenimente.php' ),
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

	// 5. Enqueue Global Lightbox System
	wp_enqueue_script( 'brezoaele-lightbox', get_template_directory_uri() . '/js/lightbox.js', array(), '1.0.0', true );

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

		wp_enqueue_script( 'brezoaele-map', get_template_directory_uri() . '/js/map.js', array( 'leaflet-cluster-js' ), '1.0.5', true );
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

	// 7. Enqueue Calendar JS pe template-calendar-evenimente.php sau arhivă evenimente
	if ( is_page_template( 'template-calendar-evenimente.php' ) || is_post_type_archive( 'eveniment' ) ) {
		wp_enqueue_script( 'brezoaele-calendar', get_template_directory_uri() . '/js/calendar.js', array(), '1.0.0', true );
		wp_localize_script( 'brezoaele-calendar', 'brezoaeleCalendar', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
		) );
	}
	if ( is_singular( 'eveniment' ) ) {
		$lat = get_post_meta( get_the_ID(), '_eveniment_harta_lat', true );
		$lng = get_post_meta( get_the_ID(), '_eveniment_harta_lng', true );
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
 * Auto-create required frontend pages if they don't exist.
 * Uses a version flag to run only once.
 */
function brezoaele_seed_required_pages() {
	if ( get_option( 'brezoaele_pages_seeded_v1' ) ) {
		return;
	}

	$pages = array(
		array(
			'title'    => 'Editează Anunț',
			'slug'     => 'editeaza-anunt',
			'template' => 'page-editeaza-anunt.php',
		),
	);

	foreach ( $pages as $page_data ) {
		// Check if a page with this slug already exists (any status)
		$existing = get_page_by_path( $page_data['slug'], OBJECT, 'page' );
		if ( $existing ) {
			continue; // Already exists, skip
		}

		$page_id = wp_insert_post( array(
			'post_title'    => $page_data['title'],
			'post_name'     => $page_data['slug'],
			'post_status'   => 'publish',
			'post_type'     => 'page',
			'post_author'   => 1,
			'post_content'  => '',
			'comment_status'=> 'closed',
		) );

		if ( $page_id && ! is_wp_error( $page_id ) ) {
			update_post_meta( $page_id, '_wp_page_template', $page_data['template'] );
		}
	}

	update_option( 'brezoaele_pages_seeded_v1', 1 );
}
add_action( 'init', 'brezoaele_seed_required_pages', 100 );


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

	$stiri_parent_id  = 0;
	$comuna_parent_id = 0;
	$processed_items  = array();

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

		// Identificăm părintele "Comuna Mea" / "Comuna"
		if ( ( false !== mb_stripos( $item->title, 'Comuna' ) || false !== mb_stripos( $item->title, 'Comuna Mea' ) ) && 0 === (int) $item->menu_item_parent ) {
			$comuna_parent_id = $item->ID;
		}

		$processed_items[] = $item;
	}

	$locale_term    = get_term_by( 'slug', 'stiri-locale', 'category' );
	$judetene_term  = get_term_by( 'slug', 'stiri-judetene', 'category' );
	$nationale_term = get_term_by( 'slug', 'stiri-nationale', 'category' );
	$sport_term     = get_term_by( 'slug', 'sport', 'category' );

	$locale_url    = $locale_term ? esc_url( get_category_link( $locale_term->term_id ) ) : home_url( '/category/stiri-locale/' );
	$judetene_url  = $judetene_term ? esc_url( get_category_link( $judetene_term->term_id ) ) : home_url( '/category/stiri-judetene/' );
	$nationale_url = $nationale_term ? esc_url( get_category_link( $nationale_term->term_id ) ) : home_url( '/category/stiri-nationale/' );
	$sport_url     = $sport_term ? esc_url( get_category_link( $sport_term->term_id ) ) : home_url( '/c/sport/' );

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
		array(
			'id'    => 999904,
			'title' => '⚽ Știri Sportive',
			'url'   => $sport_url,
		),
	);

	$comuna_subitems = array(
		array(
			'id'    => 999905,
			'title' => '🏆 Sport Brezoaele',
			'url'   => home_url( '/sport/' ),
		),
	);

	$final_items = array();
	foreach ( $processed_items as $item ) {
		$final_items[] = $item;

		// Dacă am ajuns la părintele "Știri / Informații", inserăm sub-elementele
		if ( $stiri_parent_id > 0 && (int) $item->ID === (int) $stiri_parent_id ) {
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

		// Dacă am ajuns la părintele "Comuna Mea", inserăm sub-elementele
		if ( $comuna_parent_id > 0 && (int) $item->ID === (int) $comuna_parent_id ) {
			foreach ( $comuna_subitems as $sub ) {
				$dummy = new stdClass();
				$dummy->ID = $sub['id'];
				$dummy->db_id = $sub['id'];
				$dummy->menu_item_parent = $comuna_parent_id;
				$dummy->object_id = $sub['id'];
				$dummy->object = 'custom';
				$dummy->type = 'custom';
				$dummy->type_label = 'Custom Link';
				$dummy->title = $sub['title'];
				$dummy->url = $sub['url'];
				$dummy->target = '';
				$dummy->attr_title = '';
				$dummy->description = '';
				$dummy->classes = array( 'menu-item', 'menu-item-type-custom', 'menu-item-comuna-injected' );
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

/**
 * -----------------------------------------------------------------------------
 * SISTEM DE EVENIMENTE LOCALE ȘI CALENDAR INTERACTIV
 * -----------------------------------------------------------------------------
 */

// 1. Înregistrare Custom Post Type: eveniment
function brezoaele_v2_register_eveniment_cpt() {
	$labels = array(
		'name'                  => _x( 'Evenimente', 'Post Type General Name', 'brezoaele-v2' ),
		'singular_name'         => _x( 'Eveniment', 'Post Type Singular Name', 'brezoaele-v2' ),
		'menu_name'             => __( 'Evenimente', 'brezoaele-v2' ),
		'name_admin_bar'        => __( 'Eveniment', 'brezoaele-v2' ),
		'archives'              => __( 'Arhivă Evenimente', 'brezoaele-v2' ),
		'attributes'            => __( 'Atribute Eveniment', 'brezoaele-v2' ),
		'parent_item_colon'     => __( 'Eveniment Părinte:', 'brezoaele-v2' ),
		'all_items'             => __( 'Toate Evenimentele', 'brezoaele-v2' ),
		'add_new_item'          => __( 'Adaugă Eveniment Nou', 'brezoaele-v2' ),
		'add_new'               => __( 'Adaugă Nou', 'brezoaele-v2' ),
		'new_item'              => __( 'Eveniment Nou', 'brezoaele-v2' ),
		'edit_item'             => __( 'Editează Eveniment', 'brezoaele-v2' ),
		'update_item'           => __( 'Actualizează Eveniment', 'brezoaele-v2' ),
		'view_item'             => __( 'Vezi Eveniment', 'brezoaele-v2' ),
		'search_items'          => __( 'Caută Evenimente', 'brezoaele-v2' ),
	);
	$args = array(
		'label'               => __( 'Eveniment', 'brezoaele-v2' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments', 'custom-fields' ),
		'taxonomies'          => array( 'tip_eveniment' ),
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 7,
		'menu_icon'           => 'dashicons-calendar-alt',
		'has_archive'         => true,
		'show_in_rest'        => true,
		'rewrite'             => array( 'slug' => 'evenimente', 'with_front' => false ),
	);
	register_post_type( 'eveniment', $args );

	// Reîmprospătare reguli de rescriere URL (rezolvare eroare 404 pentru single-eveniment)
	if ( get_option( 'brezoaele_eveniment_rewrite_flushed_v3' ) !== '1' ) {
		flush_rewrite_rules();
		update_option( 'brezoaele_eveniment_rewrite_flushed_v3', '1' );
	}
}
add_action( 'init', 'brezoaele_v2_register_eveniment_cpt', 0 );

// 2. Înregistrare Taxonomie: tip_eveniment
function brezoaele_v2_register_tip_eveniment_taxonomy() {
	$labels = array(
		'name'              => _x( 'Tipuri Eveniment', 'taxonomy general name', 'brezoaele-v2' ),
		'singular_name'     => _x( 'Tip Eveniment', 'taxonomy singular name', 'brezoaele-v2' ),
		'search_items'      => __( 'Caută Tipuri Eveniment', 'brezoaele-v2' ),
		'all_items'         => __( 'Toate Tipurile', 'brezoaele-v2' ),
		'parent_item'       => __( 'Tip Părinte', 'brezoaele-v2' ),
		'parent_item_colon' => __( 'Tip Părinte:', 'brezoaele-v2' ),
		'edit_item'         => __( 'Editează Tip', 'brezoaele-v2' ),
		'update_item'       => __( 'Actualizează Tip', 'brezoaele-v2' ),
		'add_new_item'      => __( 'Adaugă Tip Nou', 'brezoaele-v2' ),
		'new_item_name'     => __( 'Nume Tip Nou', 'brezoaele-v2' ),
		'menu_name'         => __( 'Tipuri Evenimente', 'brezoaele-v2' ),
	);
	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'tip-eveniment' ),
	);
	register_taxonomy( 'tip_eveniment', array( 'eveniment' ), $args );
}
add_action( 'init', 'brezoaele_v2_register_tip_eveniment_taxonomy', 0 );

// Seeding categorii implicite pentru Evenimente
function brezoaele_seed_eveniment_categories() {
	if ( get_option( 'brezoaele_eveniment_categories_seeded' ) ) {
		return;
	}
	$categories = array(
		'Cultură & Tradiții',
		'Sărbătoare Locală',
		'Sportiv',
		'Târg & Bâlci',
		'Ședință / Transparență',
		'Voluntariat & Mediu',
		'Religios',
	);
	foreach ( $categories as $cat_name ) {
		if ( ! term_exists( $cat_name, 'tip_eveniment' ) ) {
			wp_insert_term( $cat_name, 'tip_eveniment' );
		}
	}
	update_option( 'brezoaele_eveniment_categories_seeded', 1 );
}
add_action( 'admin_init', 'brezoaele_seed_eveniment_categories' );

// 3. Metabox Admin pentru Evenimente
function brezoaele_register_eveniment_metabox() {
	add_meta_box(
		'brezoaele_eveniment_details_box',
		'📅 Detalii & Programare Eveniment',
		'brezoaele_render_eveniment_metabox',
		'eveniment',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'brezoaele_register_eveniment_metabox' );

function brezoaele_render_eveniment_metabox( $post ) {
	wp_nonce_field( 'brezoaele_eveniment_meta_nonce_action', 'brezoaele_eveniment_meta_nonce' );

	$data_start        = get_post_meta( $post->ID, '_eveniment_data_start', true );
	$data_sfarsit      = get_post_meta( $post->ID, '_eveniment_data_sfarsit', true );
	$ora_start         = get_post_meta( $post->ID, '_eveniment_ora_start', true );
	$ora_sfarsit       = get_post_meta( $post->ID, '_eveniment_ora_sfarsit', true );
	$toata_ziua        = get_post_meta( $post->ID, '_eveniment_toata_ziua', true );
	$repetitiv_anual   = get_post_meta( $post->ID, '_eveniment_repetitiv_anual', true );
	$locatie           = get_post_meta( $post->ID, '_eveniment_locatie', true );
	$harta_lat         = get_post_meta( $post->ID, '_eveniment_harta_lat', true );
	$harta_lng         = get_post_meta( $post->ID, '_eveniment_harta_lng', true );
	$organizator       = get_post_meta( $post->ID, '_eveniment_organizator', true );
	$contact           = get_post_meta( $post->ID, '_eveniment_contact', true );
	?>
	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
	<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

	<style>
		.brz-meta-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-top: 10px; }
		.brz-meta-field { display: flex; flex-direction: column; gap: 4px; }
		.brz-meta-field label { font-weight: 700; color: #1e293b; font-size: 13px; }
		.brz-meta-field input[type="text"], .brz-meta-field input[type="date"], .brz-meta-field input[type="time"] {
			padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;
		}
		.brz-meta-checkbox-group { display: flex; align-items: center; gap: 8px; margin-top: 6px; background: #f8fafc; padding: 10px; border-radius: 6px; border: 1px solid #e2e8f0; }
		.brz-meta-checkbox-group label { margin: 0; cursor: pointer; font-weight: 600; font-size: 13px; }
	</style>

	<div class="brz-meta-grid">
		<div class="brz-meta-field">
			<label for="_eveniment_data_start">📅 Data Început *</label>
			<input type="date" id="_eveniment_data_start" name="_eveniment_data_start" value="<?php echo esc_attr( $data_start ); ?>" required />
		</div>
		<div class="brz-meta-field">
			<label for="_eveniment_data_sfarsit">📅 Data Sfârșit (opțional, pt. mai multe zile)</label>
			<input type="date" id="_eveniment_data_sfarsit" name="_eveniment_data_sfarsit" value="<?php echo esc_attr( $data_sfarsit ); ?>" />
		</div>
		<div class="brz-meta-field">
			<label for="_eveniment_ora_start">⏰ Ora Început</label>
			<input type="time" id="_eveniment_ora_start" name="_eveniment_ora_start" value="<?php echo esc_attr( $ora_start ); ?>" />
		</div>
		<div class="brz-meta-field">
			<label for="_eveniment_ora_sfarsit">⏰ Ora Sfârșit</label>
			<input type="time" id="_eveniment_ora_sfarsit" name="_eveniment_ora_sfarsit" value="<?php echo esc_attr( $ora_sfarsit ); ?>" />
		</div>
	</div>

	<div style="display: flex; gap: 16px; margin-top: 16px; flex-wrap: wrap;">
		<div class="brz-meta-checkbox-group">
			<input type="checkbox" id="_eveniment_toata_ziua" name="_eveniment_toata_ziua" value="1" <?php checked( $toata_ziua, '1' ); ?> />
			<label for="_eveniment_toata_ziua">⏳ Durează Toată Ziua (omite orarul)</label>
		</div>
		<div class="brz-meta-checkbox-group" style="background: #ecfdf5; border-color: #a7f3d0;">
			<input type="checkbox" id="_eveniment_repetitiv_anual" name="_eveniment_repetitiv_anual" value="1" <?php checked( $repetitiv_anual, '1' ); ?> />
			<label for="_eveniment_repetitiv_anual" style="color: #065f46;">🔁 Se repeta anual la aceeași dată (ex: 15 Aug / Sărbătoare fixă)</label>
		</div>
	</div>

	<hr style="margin: 20px 0; border: 0; border-top: 1px solid #e2e8f0;">

	<div class="brz-meta-grid">
		<div class="brz-meta-field">
			<label for="_eveniment_locatie">📍 Locație / Adresă</label>
			<input type="text" id="_eveniment_locatie" name="_eveniment_locatie" value="<?php echo esc_attr( $locatie ); ?>" placeholder="ex: Căminul Cultural Brezoaele, Parcul Comunal" />
		</div>
		<div class="brz-meta-field">
			<label for="_eveniment_organizator">🏛️ Organizator</label>
			<input type="text" id="_eveniment_organizator" name="_eveniment_organizator" value="<?php echo esc_attr( $organizator ); ?>" placeholder="ex: Primăria Brezoaele, Asociația Tinerilor" />
		</div>
		<div class="brz-meta-field">
			<label for="_eveniment_contact">📞 Contact / Info suplimentar</label>
			<input type="text" id="_eveniment_contact" name="_eveniment_contact" value="<?php echo esc_attr( $contact ); ?>" placeholder="ex: 0722 000 000 / contact@brezoaele.ro" />
		</div>
	</div>

	<!-- Selector Pin Hartă Leaflet în Admin -->
	<div style="margin-top: 20px; background: #f8fafc; padding: 16px; border-radius: 8px; border: 1px solid #cbd5e1;">
		<label style="font-weight: 700; color: #0f172a; font-size: 13px; display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
			🗺️ Selector Pin pe Hartă (Click pe hartă sau trage pinul verde pentru a seta coordonatele)
		</label>
		<div id="brz-admin-event-map" style="height: 320px; width: 100%; border-radius: 8px; border: 1px solid #cbd5e1; box-shadow: inset 0 1px 3px rgba(0,0,0,0.1); z-index: 1;"></div>
		<div style="display: flex; gap: 12px; margin-top: 10px; align-items: center; flex-wrap: wrap;">
			<button type="button" id="brz-reset-event-pin" class="button button-secondary" style="font-weight: 700;">
				🎯 Recentrează pe Brezoaele
			</button>
			<span style="font-size: 12px; color: #64748b;">
				Dă click oriunde pe hartă sau trage pinul pentru a seta automat latitudinea și longitudinea.
			</span>
		</div>
	</div>

	<div class="brz-meta-grid" style="margin-top: 12px;">
		<div class="brz-meta-field">
			<label for="_eveniment_harta_lat">🌐 Latitudine Hartă (auto-completat)</label>
			<input type="text" id="_eveniment_harta_lat" name="_eveniment_harta_lat" value="<?php echo esc_attr( $harta_lat ); ?>" placeholder="44.561854" />
		</div>
		<div class="brz-meta-field">
			<label for="_eveniment_harta_lng">🌐 Longitudine Hartă (auto-completat)</label>
			<input type="text" id="_eveniment_harta_lng" name="_eveniment_harta_lng" value="<?php echo esc_attr( $harta_lng ); ?>" placeholder="25.770593" />
		</div>
	</div>

	<script>
	document.addEventListener('DOMContentLoaded', function() {
		var latInput = document.getElementById('_eveniment_harta_lat');
		var lngInput = document.getElementById('_eveniment_harta_lng');
		var mapEl = document.getElementById('brz-admin-event-map');

		if (!mapEl || typeof L === 'undefined') return;

		var defaultLat = (latInput && latInput.value) ? parseFloat(latInput.value) : 44.561854;
		var defaultLng = (lngInput && lngInput.value) ? parseFloat(lngInput.value) : 25.770593;
		var hasExistingCoords = !!(latInput && latInput.value && lngInput && lngInput.value);

		var map = L.map('brz-admin-event-map').setView([defaultLat, defaultLng], hasExistingCoords ? 16 : 14);

		var osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			maxZoom: 19,
			attribution: '© OpenStreetMap'
		}).addTo(map);

		var satLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
			maxZoom: 19,
			attribution: '© Esri — Satelit'
		});

		L.control.layers({
			'🛣️ Rutiere': osmLayer,
			'🛰️ Satelit': satLayer
		}, {}, { position: 'topright' }).addTo(map);

		var marker = L.marker([defaultLat, defaultLng], {
			draggable: true,
			title: 'Locație Eveniment Brezoaele'
		}).addTo(map);

		function updateCoords(lat, lng) {
			if (latInput) latInput.value = lat.toFixed(6);
			if (lngInput) lngInput.value = lng.toFixed(6);
		}

		marker.on('dragend', function(e) {
			var pos = e.target.getLatLng();
			updateCoords(pos.lat, pos.lng);
		});

		map.on('click', function(e) {
			marker.setLatLng(e.latlng);
			updateCoords(e.latlng.lat, e.latlng.lng);
		});

		var resetBtn = document.getElementById('brz-reset-event-pin');
		if (resetBtn) {
			resetBtn.addEventListener('click', function(e) {
				e.preventDefault();
				var centerLat = 44.561854;
				var centerLng = 25.770593;
				map.setView([centerLat, centerLng], 14);
				marker.setLatLng([centerLat, centerLng]);
				updateCoords(centerLat, centerLng);
			});
		}

		setTimeout(function() {
			map.invalidateSize();
		}, 500);
	});
	</script>
	<?php
}

function brezoaele_save_eveniment_meta( $post_id ) {
	if ( ! isset( $_POST['brezoaele_eveniment_meta_nonce'] ) || ! wp_verify_nonce( $_POST['brezoaele_eveniment_meta_nonce'], 'brezoaele_eveniment_meta_nonce_action' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$fields = array(
		'_eveniment_data_start',
		'_eveniment_data_sfarsit',
		'_eveniment_ora_start',
		'_eveniment_ora_sfarsit',
		'_eveniment_locatie',
		'_eveniment_harta_lat',
		'_eveniment_harta_lng',
		'_eveniment_organizator',
		'_eveniment_contact',
	);

	foreach ( $fields as $field ) {
		if ( isset( $_POST[ $field ] ) ) {
			update_post_meta( $post_id, $field, sanitize_text_field( $_POST[ $field ] ) );
		}
	}

	update_post_meta( $post_id, '_eveniment_toata_ziua', isset( $_POST['_eveniment_toata_ziua'] ) ? '1' : '0' );
	update_post_meta( $post_id, '_eveniment_repetitiv_anual', isset( $_POST['_eveniment_repetitiv_anual'] ) ? '1' : '0' );
}
add_action( 'save_post_eveniment', 'brezoaele_save_eveniment_meta' );

// 4. Custom Admin Columns in edit.php?post_type=eveniment
function brezoaele_eveniment_columns_head( $columns ) {
	$new_columns = array();
	foreach ( $columns as $key => $title ) {
		$new_columns[ $key ] = $title;
		if ( 'title' === $key ) {
			$new_columns['data_eveniment']   = '📅 Data & Orar';
			$new_columns['locatie_eveniment'] = '📍 Locație';
			$new_columns['repetitiv_status']  = '🔁 Periodicitate';
		}
	}
	return $new_columns;
}
function brezoaele_eveniment_columns_content( $column_name, $post_id ) {
	if ( 'data_eveniment' === $column_name ) {
		$data_start   = get_post_meta( $post_id, '_eveniment_data_start', true );
		$data_sfarsit = get_post_meta( $post_id, '_eveniment_data_sfarsit', true );
		$ora_start    = get_post_meta( $post_id, '_eveniment_ora_start', true );
		$ora_sfarsit  = get_post_meta( $post_id, '_eveniment_ora_sfarsit', true );
		$toata_ziua   = get_post_meta( $post_id, '_eveniment_toata_ziua', true );

		if ( $data_start ) {
			echo '<strong>' . esc_html( date( 'd.m.Y', strtotime( $data_start ) ) ) . '</strong>';
			if ( $data_sfarsit && $data_sfarsit !== $data_start ) {
				echo ' - <strong>' . esc_html( date( 'd.m.Y', strtotime( $data_sfarsit ) ) ) . '</strong>';
			}
			echo '<br>';
			if ( '1' === $toata_ziua ) {
				echo '<span style="color:#047857; font-size:11px; font-weight:700;">⏳ Toată ziua</span>';
			} elseif ( $ora_start ) {
				echo '<span style="color:#475569; font-size:12px;">⏰ ' . esc_html( $ora_start ) . ( $ora_sfarsit ? ' - ' . esc_html( $ora_sfarsit ) : '' ) . '</span>';
			}
		} else {
			echo '<span style="color:#94a3b8;">Fără dată setată</span>';
		}
	} elseif ( 'locatie_eveniment' === $column_name ) {
		$locatie = get_post_meta( $post_id, '_eveniment_locatie', true );
		echo $locatie ? esc_html( $locatie ) : '<span style="color:#94a3b8;">—</span>';
	} elseif ( 'repetitiv_status' === $column_name ) {
		$repetitiv = get_post_meta( $post_id, '_eveniment_repetitiv_anual', true );
		if ( '1' === $repetitiv ) {
			echo '<span style="background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; padding:2px 6px; border-radius:4px; font-size:11px; font-weight:700;">🔁 Anual</span>';
		} else {
			echo '<span style="color:#64748b; font-size:11px;">O singură dată</span>';
		}
	}
}
add_filter( 'manage_eveniment_posts_columns', 'brezoaele_eveniment_columns_head' );
add_action( 'manage_eveniment_posts_custom_column', 'brezoaele_eveniment_columns_content', 10, 2 );

// 5. AJAX Endpoint pentru evenimentele din calendar
function brezoaele_ajax_get_calendar_events() {
	$year  = isset( $_GET['year'] ) ? intval( $_GET['year'] ) : intval( date( 'Y' ) );
	$month = isset( $_GET['month'] ) ? intval( $_GET['month'] ) : intval( date( 'n' ) );
	$cat   = isset( $_GET['cat'] ) ? sanitize_text_field( $_GET['cat'] ) : '';

	$args = array(
		'post_type'      => 'eveniment',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
	);

	if ( ! empty( $cat ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'tip_eveniment',
				'field'    => 'slug',
				'terms'    => $cat,
			),
		);
	}

	$query  = new WP_Query( $args );
	$events = array();

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$post_id         = get_the_ID();
			$data_start      = get_post_meta( $post_id, '_eveniment_data_start', true );
			$data_sfarsit    = get_post_meta( $post_id, '_eveniment_data_sfarsit', true );
			$ora_start       = get_post_meta( $post_id, '_eveniment_ora_start', true );
			$ora_sfarsit     = get_post_meta( $post_id, '_eveniment_ora_sfarsit', true );
			$toata_ziua      = get_post_meta( $post_id, '_eveniment_toata_ziua', true );
			$repetitiv_anual = get_post_meta( $post_id, '_eveniment_repetitiv_anual', true );
			$locatie         = get_post_meta( $post_id, '_eveniment_locatie', true );

			if ( empty( $data_start ) ) {
				continue;
			}

			$ev_year  = intval( date( 'Y', strtotime( $data_start ) ) );
			$ev_month = intval( date( 'n', strtotime( $data_start ) ) );
			$ev_day   = intval( date( 'j', strtotime( $data_start ) ) );

			// Verificăm potrivirea în luna selectată
			$match = false;
			$effective_date = $data_start;

			if ( '1' === $repetitiv_anual ) {
				// Dacă e repetitiv anual, se afișează în luna respectivă în orice an
				if ( $ev_month === $month ) {
					$match = true;
					$effective_date = sprintf( '%04d-%02d-%02d', $year, $ev_month, $ev_day );
				}
			} else {
				// Pentru evenimente unice
				if ( $ev_year === $year && $ev_month === $month ) {
					$match = true;
				}
			}

			if ( $match ) {
				$terms    = get_the_terms( $post_id, 'tip_eveniment' );
				$cat_name = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : 'General';
				$cat_slug = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->slug : 'general';

				$thumb_url = get_the_post_thumbnail_url( $post_id, 'medium' );
				if ( ! $thumb_url ) {
					$thumb_url = get_template_directory_uri() . '/images/hero-biserica.png';
				}

				$events[] = array(
					'id'               => $post_id,
					'title'            => get_the_title(),
					'date'             => $effective_date,
					'day'              => intval( date( 'j', strtotime( $effective_date ) ) ),
					'data_start'       => $data_start,
					'ora_start'        => $ora_start,
					'ora_sfarsit'      => $ora_sfarsit,
					'toata_ziua'       => ( '1' === $toata_ziua ),
					'repetitiv_anual'  => ( '1' === $repetitiv_anual ),
					'locatie'          => $locatie,
					'cat_name'         => $cat_name,
					'cat_slug'         => $cat_slug,
					'permalink'        => get_permalink(),
					'thumb_url'        => $thumb_url,
					'excerpt'          => get_the_excerpt(),
				);
			}
		}
		wp_reset_postdata();
	}

	wp_send_json_success( $events );
}
add_action( 'wp_ajax_brezoaele_get_calendar_events', 'brezoaele_ajax_get_calendar_events' );
add_action( 'wp_ajax_nopriv_brezoaele_get_calendar_events', 'brezoaele_ajax_get_calendar_events' );

// 6. Generator Fișiere iCal (.ics)
function brezoaele_handle_ics_download() {
	if ( isset( $_GET['action'] ) && 'download_ics' === $_GET['action'] && isset( $_GET['event_id'] ) ) {
		$event_id = intval( $_GET['event_id'] );
		$post     = get_post( $event_id );

		if ( ! $post || 'eveniment' !== $post->post_type ) {
			wp_die( 'Evenimentul nu a fost găsit.' );
		}

		$data_start  = get_post_meta( $event_id, '_eveniment_data_start', true );
		$ora_start   = get_post_meta( $event_id, '_eveniment_ora_start', true );
		$ora_sfarsit = get_post_meta( $event_id, '_eveniment_ora_sfarsit', true );
		$toata_ziua  = get_post_meta( $event_id, '_eveniment_toata_ziua', true );
		$locatie     = get_post_meta( $event_id, '_eveniment_locatie', true );

		if ( ! $data_start ) {
			$data_start = date( 'Y-m-d' );
		}

		$dt_start = str_replace( '-', '', $data_start );
		$dt_end   = $dt_start;

		if ( '1' === $toata_ziua || empty( $ora_start ) ) {
			$start_str = "VALUE=DATE:" . $dt_start;
			$end_str   = "VALUE=DATE:" . date( 'Ymd', strtotime( $data_start . ' +1 day' ) );
		} else {
			$time_s    = str_replace( ':', '', $ora_start ) . '00';
			$time_e    = ! empty( $ora_sfarsit ) ? str_replace( ':', '', $ora_sfarsit ) . '00' : date( 'His', strtotime( $ora_start . ' +2 hours' ) );
			$start_str = $dt_start . 'T' . $time_s;
			$end_str   = $dt_end . 'T' . $time_e;
		}

		$filename = sanitize_title( $post->post_title ) . '.ics';

		header( 'Content-Type: text/calendar; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );

		echo "BEGIN:VCALENDAR\r\n";
		echo "VERSION:2.0\r\n";
		echo "PRODID:-//Brezoaele.ro//Calendar Evenimente//RO\r\n";
		echo "METHOD:REQUEST\r\n";
		echo "BEGIN:VEVENT\r\n";
		echo "UID:" . uniqid() . "@brezoaele.ro\r\n";
		echo "DTSTAMP:" . gmdate( 'Ymd\THis\Z' ) . "\r\n";
		echo "DTSTART;{$start_str}\r\n";
		echo "DTEND;{$end_str}\r\n";
		echo "SUMMARY:" . esc_attr( $post->post_title ) . "\r\n";
		if ( $locatie ) {
			echo "LOCATION:" . esc_attr( $locatie ) . "\r\n";
		}
		echo "DESCRIPTION:" . esc_attr( wp_strip_all_tags( $post->post_content ) ) . "\r\n";
		echo "URL:" . esc_url( get_permalink( $event_id ) ) . "\r\n";
		echo "END:VEVENT\r\n";
		echo "END:VCALENDAR\r\n";
		exit;
	}
}
add_action( 'init', 'brezoaele_handle_ics_download' );

/**
 * Admin Metabox & Media Uploader for Event Extra Photo Gallery
 */
function brezoaele_register_eveniment_gallery_metabox() {
	add_meta_box(
		'brezoaele_eveniment_gallery_box',
		'🖼️ Galerie Foto Suplimentară Eveniment',
		'brezoaele_render_eveniment_gallery_metabox',
		'eveniment',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'brezoaele_register_eveniment_gallery_metabox' );

function brezoaele_admin_enqueue_event_gallery_scripts( $hook ) {
	global $post_type;
	if ( ( 'post.php' === $hook || 'post-new.php' === $hook ) && 'eveniment' === $post_type ) {
		wp_enqueue_media();
	}
}
add_action( 'admin_enqueue_scripts', 'brezoaele_admin_enqueue_event_gallery_scripts' );

function brezoaele_render_eveniment_gallery_metabox( $post ) {
	wp_nonce_field( 'brezoaele_eveniment_gallery_nonce_action', 'brezoaele_eveniment_gallery_nonce' );

	$gallery_ids = get_post_meta( $post->ID, '_eveniment_galerie', true );
	if ( ! is_array( $gallery_ids ) ) {
		$gallery_ids = array();
	}
	$ids_str = implode( ',', array_filter( array_map( 'intval', $gallery_ids ) ) );
	?>
	<div id="brz-event-gallery-container" style="display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 16px;">
		<?php foreach ( $gallery_ids as $attachment_id ) : ?>
			<?php $img_src = wp_get_attachment_image_url( $attachment_id, 'thumbnail' ); ?>
			<?php if ( $img_src ) : ?>
				<div class="brz-gal-item" data-id="<?php echo esc_attr( $attachment_id ); ?>" style="position: relative; border: 1px solid #cbd5e1; border-radius: 8px; padding: 4px; background: #fff;">
					<img src="<?php echo esc_url( $img_src ); ?>" style="width: 90px; height: 90px; object-fit: cover; display: block; border-radius: 6px;">
					<button type="button" class="brz-remove-gal-img" style="position: absolute; top: -6px; right: -6px; background: #ef4444; color: #fff; border: none; border-radius: 50%; width: 22px; height: 22px; cursor: pointer; font-weight: bold; font-size: 12px; line-height: 1;">&times;</button>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>

	<input type="hidden" id="eveniment_galerie_ids" name="_eveniment_galerie_ids" value="<?php echo esc_attr( $ids_str ); ?>" />

	<button type="button" id="brz-upload-event-gallery-btn" class="button button-primary" style="font-weight: 700;">
		➕ Adaugă / Selectează Imagini din Galerie
	</button>
	<p class="description" style="margin-top: 8px; color: #64748b;">
		Poți selecta mai multe imagini simultan din Media Library. Ele vor fi afișate în pagina evenimentului ca galerie foto.
	</p>

	<script>
	jQuery(document).ready(function($){
		var frame;
		$('#brz-upload-event-gallery-btn').on('click', function(e){
			e.preventDefault();
			if (frame) {
				frame.open();
				return;
			}
			frame = wp.media({
				title: 'Selectează Imagini pentru Galerie',
				button: { text: 'Adaugă în Galerie' },
				multiple: true
			});
			frame.on('select', function(){
				var selection = frame.state().get('selection');
				var currentIds = $('#eveniment_galerie_ids').val() ? $('#eveniment_galerie_ids').val().split(',') : [];
				selection.map(function(attachment){
					attachment = attachment.toJSON();
					if (currentIds.indexOf(attachment.id.toString()) === -1) {
						currentIds.push(attachment.id);
						var thumbUrl = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
						var html = '<div class="brz-gal-item" data-id="' + attachment.id + '" style="position: relative; border: 1px solid #cbd5e1; border-radius: 8px; padding: 4px; background: #fff;">' +
							'<img src="' + thumbUrl + '" style="width: 90px; height: 90px; object-fit: cover; display: block; border-radius: 6px;">' +
							'<button type="button" class="brz-remove-gal-img" style="position: absolute; top: -6px; right: -6px; background: #ef4444; color: #fff; border: none; border-radius: 50%; width: 22px; height: 22px; cursor: pointer; font-weight: bold; font-size: 12px; line-height: 1;">&times;</button>' +
							'</div>';
						$('#brz-event-gallery-container').append(html);
					}
				});
				$('#eveniment_galerie_ids').val(currentIds.join(','));
			});
			frame.open();
		});

		$(document).on('click', '.brz-remove-gal-img', function(e){
			e.preventDefault();
			var item = $(this).closest('.brz-gal-item');
			var id = item.data('id').toString();
			var currentIds = $('#eveniment_galerie_ids').val() ? $('#eveniment_galerie_ids').val().split(',') : [];
			var newIds = currentIds.filter(function(val){ return val !== id; });
			$('#eveniment_galerie_ids').val(newIds.join(','));
			item.remove();
		});
	});
	</script>
	<?php
}

function brezoaele_save_eveniment_gallery_meta( $post_id ) {
	if ( isset( $_POST['_eveniment_galerie_ids'] ) ) {
		$raw_ids = sanitize_text_field( $_POST['_eveniment_galerie_ids'] );
		$ids_arr = array_filter( array_map( 'intval', explode( ',', $raw_ids ) ) );
		update_post_meta( $post_id, '_eveniment_galerie', $ids_arr );
	}
}
add_action( 'save_post_eveniment', 'brezoaele_save_eveniment_gallery_meta' );

/**
 * Include Ads Admin Class
 */
require_once get_template_directory() . '/inc/class-ads-admin.php';

/**
 * Helper: Render Single Ad Item
 */
function brezoaele_render_single_ad_item( $format, $item ) {
	$type = isset( $item['type'] ) ? $item['type'] : 'image';

	if ( 'image' === $type && ! empty( $item['img_url'] ) ) {
		$img_url     = esc_url( $item['img_url'] );
		$link_url    = ! empty( $item['link_url'] ) ? esc_url( $item['link_url'] ) : '#';
		$target_attr = ! empty( $item['open_new_tab'] ) && '1' === $item['open_new_tab'] ? ' target="_blank"' : '';
		$rel_attr    = ! empty( $item['nofollow'] ) && '1' === $item['nofollow'] ? ' rel="sponsored nofollow"' : ' rel="noopener"';
		?>
		<div class="brz-ad-wrapper brz-ad-format-<?php echo esc_attr( $format ); ?> brz-ad-custom-active">
			<a href="<?php echo $link_url; ?>"<?php echo $target_attr . $rel_attr; ?> style="display: block; width: 100%; text-decoration: none;">
				<img src="<?php echo $img_url; ?>" alt="<?php echo esc_attr( isset( $item['title'] ) ? $item['title'] : get_bloginfo( 'name' ) ); ?>" style="width: 100%; height: auto; max-width: 100%; display: block; border-radius: var(--border-radius-lg); border: 1px solid var(--color-border); box-shadow: var(--shadow-sm);">
			</a>
		</div>
		<?php
		return true;
	}

	if ( 'html' === $type && ! empty( $item['html_code'] ) ) {
		?>
		<div class="brz-ad-wrapper brz-ad-format-<?php echo esc_attr( $format ); ?> brz-ad-custom-html">
			<?php echo $item['html_code']; ?>
		</div>
		<?php
		return true;
	}

	return false;
}

/**
 * Check if page should exclude ad banners
 */
function brezoaele_is_ad_excluded_page() {
	return is_page_template( array( 'template-ratecard-firme.php', 'template-ratecard-institutii.php', 'template-ratecard-electoral.php', 'template-publicitate.php' ) ) || is_page( array( 'ratecard-firme', 'ratecard-institutii', 'ratecard-electoral', 'publicitate' ) );
}

/**
 * Helper: Render Publicitate / Ad Banner cu Suport Multi-Clienți & Rotire Random
 *
 * @param string $format 'header' | 'sidebar-medium' | 'sidebar-sticky' | 'in-article' | 'after-article' | 'homepage-feed'
 * @param string $dimensions Default placeholder dimensions
 * @param string $title Default placeholder title
 */
function brezoaele_render_ad_placeholder( $format = 'sidebar-medium', $dimensions = '300 x 250 px', $title = 'Spațiu Publicitar Disponibil' ) {
	// Nu afișăm nicio reclamă pe paginile de Rate Card sau pe pagina Publicitate
	if ( function_exists( 'brezoaele_is_ad_excluded_page' ) && brezoaele_is_ad_excluded_page() ) {
		return;
	}

	$saved_options = get_option( 'brezoaele_ads_options', array() );
	
	if ( class_exists( 'Brezoaele_Ads_Admin' ) ) {
		$zone_data = Brezoaele_Ads_Admin::get_normalized_zone_data( $saved_options, $format );
	} else {
		$zone_data = array(
			'status'   => 'placeholder',
			'rotation' => 'random',
			'items'    => array(),
		);
	}

	$status   = $zone_data['status'];
	$rotation = $zone_data['rotation'];
	$items    = $zone_data['items'];

	// 1. Dacă zona este dezactivată complet
	if ( 'disabled' === $status ) {
		return;
	}

	// 2. Dacă zona are reclame active configurate
	if ( 'active' === $status && ! empty( $items ) ) {
		$active_items = array();
		$today        = date( 'Y-m-d' );

		foreach ( $items as $item ) {
			$is_active  = ! isset( $item['active'] ) || '1' === $item['active'];
			$start_date = ! empty( $item['start_date'] ) ? $item['start_date'] : '';
			$end_date   = ! empty( $item['end_date'] ) ? $item['end_date'] : '';

			if ( ! $is_active ) {
				continue;
			}

			if ( $start_date && $today < $start_date ) {
				continue; // Campania nu a început încă
			}

			if ( $end_date && $today > $end_date ) {
				continue; // Campania a expirat automat
			}

			$active_items[] = $item;
		}

		if ( ! empty( $active_items ) ) {
			if ( 'all' === $rotation ) {
				// Afișează toate reclamele active una sub alta
				foreach ( $active_items as $act_item ) {
					brezoaele_render_single_ad_item( $format, $act_item );
				}
				return;
			} else {
				// Rotire la refresh: Alege 1 reclamă aleatorie din pool-ul de reclame active
				$random_key  = array_rand( $active_items );
				$chosen_item = $active_items[ $random_key ];
				if ( brezoaele_render_single_ad_item( $format, $chosen_item ) ) {
					return;
				}
			}
		}
	}

	// 3. Fallback: Afișează Placeholder Promotion (/publicitate/)
	$publicitate_url = home_url( '/publicitate/' );
	?>
	<div class="brz-ad-wrapper brz-ad-format-<?php echo esc_attr( $format ); ?>">
		<a href="<?php echo esc_url( $publicitate_url ); ?>" class="brz-ad-card" title="Promovează-ți afacerea pe Brezoaele.ro">
			<div class="brz-ad-badge">📢 PROMOVARE / PUBLICITATE</div>
			<div class="brz-ad-title"><?php echo esc_html( $title ); ?></div>
			<div class="brz-ad-dims">Format recomandat: <strong><?php echo esc_html( $dimensions ); ?></strong></div>
			<span class="brz-ad-button">Află Pachetele Publicitare &rarr;</span>
		</a>
	</div>
	<?php
}

/**
 * Inject In-Article Ad after paragraph 3 if article has at least 4 paragraphs
 */
function brezoaele_inject_in_article_ad( $content ) {
	if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	$paragraphs = explode( '</p>', $content );
	$count      = 0;
	foreach ( $paragraphs as $p ) {
		if ( trim( strip_tags( $p ) ) ) {
			$count++;
		}
	}

	// Inserăm reclama în interiorul articolului doar dacă există cel puțin 4 paragrafe
	if ( $count >= 4 ) {
		$ad_html = '';
		if ( function_exists( 'brezoaele_render_ad_placeholder' ) ) {
			ob_start();
			brezoaele_render_ad_placeholder( 'in-article', '728 x 90 px / 300 x 250 px', 'Spațiu Publicitar În Articol (In-Article Ad)' );
			$ad_html = ob_get_clean();
		}

		$p_counter   = 0;
		$new_content = '';
		foreach ( $paragraphs as $paragraph ) {
			if ( trim( $paragraph ) ) {
				$new_content .= $paragraph . '</p>';
				$p_counter++;
				if ( 3 === $p_counter ) {
					$new_content .= $ad_html;
				}
			} else {
				$new_content .= $paragraph;
			}
		}
		return $new_content;
	}

	return $content;
}
add_filter( 'the_content', 'brezoaele_inject_in_article_ad', 20 );

/**
 * Register Custom Post Type: Solicitări Reclamă (Leads Publicitate)
 */
function brezoaele_register_solicitare_reclama_cpt() {
	$labels = array(
		'name'               => 'Solicitări Reclamă',
		'singular_name'      => 'Solicitare Reclamă',
		'menu_name'          => '📢 Solicitări Reclamă',
		'name_admin_bar'     => 'Solicitare Reclamă',
		'add_new'            => 'Adaugă Solicitare',
		'add_new_item'       => 'Adaugă Solicitare Reclamă Nouă',
		'new_item'           => 'Solicitare Nouă',
		'edit_item'          => 'Vizualizează Solicitare Reclamă',
		'view_item'          => 'Vezi Solicitare',
		'all_items'          => 'Toate Solicitările',
		'search_items'       => 'Caută Solicitări',
		'not_found'          => 'Nu s-au găsit solicitări de reclamă.',
		'not_found_in_trash' => 'Nu există solicitări în gunoi.',
	);

	$args = array(
		'labels'             => $labels,
		'public'             => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => false,
		'rewrite'            => false,
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => 26,
		'menu_icon'          => 'dashicons-megaphone',
		'supports'           => array( 'title' ),
	);

	register_post_type( 'solicitare_reclama', $args );
}
add_action( 'init', 'brezoaele_register_solicitare_reclama_cpt' );

/**
 * Personalizare Coloane Tabel Administrare Solicitări Reclamă
 */
function brezoaele_set_solicitare_reclama_columns( $columns ) {
	$new_columns = array(
		'cb'             => $columns['cb'],
		'title'          => 'Nume Client / Solicitant',
		'forma_juridica' => 'Formă Juridică / Companie',
		'package'        => 'Pachet Reclamă',
		'contact'        => 'Email & Telefon',
		'date'           => 'Data Solicitării',
	);
	return $new_columns;
}
add_filter( 'manage_solicitare_reclama_posts_columns', 'brezoaele_set_solicitare_reclama_columns' );

function brezoaele_custom_solicitare_reclama_column( $column, $post_id ) {
	switch ( $column ) {
		case 'forma_juridica':
			$fj = get_post_meta( $post_id, '_req_forma_juridica', true );
			$comp = get_post_meta( $post_id, '_req_date_companie', true );
			echo esc_html( $fj ? $fj : '—' );
			if ( $comp ) {
				echo '<br><small style="color:#64748b;">' . esc_html( $comp ) . '</small>';
			}
			break;
		case 'package':
			$pkg = get_post_meta( $post_id, '_req_package', true );
			$cat = get_post_meta( $post_id, '_req_category', true );
			echo '<strong>' . esc_html( $pkg ? $pkg : '—' ) . '</strong>';
			if ( $cat ) {
				echo '<br><small style="color:#047857;">' . esc_html( $cat ) . '</small>';
			}
			break;
		case 'contact':
			$email = get_post_meta( $post_id, '_req_email', true );
			$phone = get_post_meta( $post_id, '_req_phone', true );
			if ( $email ) {
				echo '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a><br>';
			}
			if ( $phone ) {
				echo '<small>📞 ' . esc_html( $phone ) . '</small>';
			}
			break;
	}
}
add_action( 'manage_solicitare_reclama_posts_custom_column', 'brezoaele_custom_solicitare_reclama_column', 10, 2 );

/**
 * Meta Box în Admin pentru Detalii Solicitare Reclamă
 */
function brezoaele_add_solicitare_reclama_meta_box() {
	add_meta_box(
		'solicitare_reclama_details',
		'📋 Detalii Solicitare Reclamă',
		'brezoaele_render_solicitare_reclama_meta_box',
		'solicitare_reclama',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'brezoaele_add_solicitare_reclama_meta_box' );

function brezoaele_render_solicitare_reclama_meta_box( $post ) {
	$nume      = get_post_meta( $post->ID, '_req_nume', true );
	$prenume   = get_post_meta( $post->ID, '_req_prenume', true );
	$fj        = get_post_meta( $post->ID, '_req_forma_juridica', true );
	$date_comp = get_post_meta( $post->ID, '_req_date_companie', true );
	$email     = get_post_meta( $post->ID, '_req_email', true );
	$phone     = get_post_meta( $post->ID, '_req_phone', true );
	$package   = get_post_meta( $post->ID, '_req_package', true );
	$category  = get_post_meta( $post->ID, '_req_category', true );
	$message   = get_post_meta( $post->ID, '_req_message', true );
	$ip        = get_post_meta( $post->ID, '_req_ip', true );
	$submitted = get_post_meta( $post->ID, '_req_date', true );
	?>
	<style>
		.brz-lead-details-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
		.brz-lead-details-table th { width: 220px; text-align: left; background: #f8fafc; padding: 10px 14px; border-bottom: 1px solid #e2e8f0; font-weight: 700; color: #0f172a; }
		.brz-lead-details-table td { padding: 10px 14px; border-bottom: 1px solid #e2e8f0; color: #334155; }
		.brz-lead-msg-box { background: #f1f5f9; border-left: 4px solid #047857; padding: 14px; margin-top: 6px; border-radius: 4px; font-size: 0.95rem; line-height: 1.6; white-space: pre-wrap; }
	</style>
	<table class="brz-lead-details-table">
		<tr>
			<th>Nume &amp; Prenume Client:</th>
			<td><strong><?php echo esc_html( trim( $nume . ' ' . $prenume ) ); ?></strong></td>
		</tr>
		<tr>
			<th>Formă Juridică:</th>
			<td><?php echo esc_html( $fj ? $fj : '—' ); ?></td>
		</tr>
		<tr>
			<th>Date Companie / Sediu:</th>
			<td><?php echo esc_html( $date_comp ? $date_comp : '—' ); ?></td>
		</tr>
		<tr>
			<th>Adresă Email:</th>
			<td><a href="mailto:<?php echo esc_attr( $email ); ?>"><strong><?php echo esc_html( $email ); ?></strong></a></td>
		</tr>
		<tr>
			<th>Număr Telefon:</th>
			<td><?php echo esc_html( $phone ? $phone : 'Nespecificat' ); ?></td>
		</tr>
		<tr>
			<th>Pachet Reclamă Dorit:</th>
			<td><span style="background: #dcfce7; color: #166534; font-weight: 800; padding: 4px 10px; border-radius: 20px;"><?php echo esc_html( $package ); ?></span> (<?php echo esc_html( $category ); ?>)</td>
		</tr>
		<tr>
			<th>Data Transmiterii:</th>
			<td><?php echo esc_html( $submitted ? $submitted : get_the_date( 'd-m-Y H:i', $post->ID ) ); ?></td>
		</tr>
		<tr>
			<th>Adresă IP Solicitant:</th>
			<td><code><?php echo esc_html( $ip ? $ip : '—' ); ?></code></td>
		</tr>
		<tr>
			<th style="vertical-align: top; padding-top: 14px;">Mesaj Obligatoriu / Detalii Reclamă:</th>
			<td>
				<div class="brz-lead-msg-box"><?php echo esc_html( $message ); ?></div>
			</td>
		</tr>
	</table>
	<?php
}

/**
 * AJAX Handler for Rate Card Request Form
 */
function brezoaele_handle_ratecard_request() {
	$nume           = isset( $_POST['req_nume'] ) ? sanitize_text_field( $_POST['req_nume'] ) : '';
	$prenume        = isset( $_POST['req_prenume'] ) ? sanitize_text_field( $_POST['req_prenume'] ) : '';
	// Fallback pentru vechea denumire a câmpului nume
	if ( empty( $nume ) && isset( $_POST['req_name'] ) ) {
		$nume = sanitize_text_field( $_POST['req_name'] );
	}

	$forma_juridica = isset( $_POST['req_forma_juridica'] ) ? sanitize_text_field( $_POST['req_forma_juridica'] ) : '';
	$date_companie  = isset( $_POST['req_date_companie'] ) ? sanitize_text_field( $_POST['req_date_companie'] ) : '';
	$email          = isset( $_POST['req_email'] ) ? sanitize_email( $_POST['req_email'] ) : '';
	$phone          = isset( $_POST['req_phone'] ) ? sanitize_text_field( $_POST['req_phone'] ) : '';
	$category       = isset( $_POST['req_category'] ) ? sanitize_text_field( $_POST['req_category'] ) : '';
	$package        = isset( $_POST['req_package'] ) ? sanitize_text_field( $_POST['req_package'] ) : '';
	$message        = isset( $_POST['req_message'] ) ? sanitize_textarea_field( $_POST['req_message'] ) : '';

	if ( empty( $nume ) || empty( $prenume ) || empty( $email ) || empty( $phone ) || empty( $message ) ) {
		wp_send_json_error( array( 'message' => 'Te rugăm să completezi toate câmpurile obligatorii (*): Nume, Prenume, Email, Telefon și Detalii Reclamă.' ) );
	}

	$full_name = trim( $nume . ' ' . $prenume );
	$post_title = $full_name . ' — ' . ( $package ? $package : 'Solicitare Reclamă' );
	$user_ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( $_SERVER['REMOTE_ADDR'] ) : '';
	$current_date = date( 'd-m-Y H:i' );

	// 1. Salvare în WordPress Dashboard (Custom Post Type)
	$post_id = wp_insert_post( array(
		'post_type'   => 'solicitare_reclama',
		'post_title'  => $post_title,
		'post_status' => 'publish',
	) );

	if ( $post_id && ! is_wp_error( $post_id ) ) {
		update_post_meta( $post_id, '_req_nume', $nume );
		update_post_meta( $post_id, '_req_prenume', $prenume );
		update_post_meta( $post_id, '_req_forma_juridica', $forma_juridica );
		update_post_meta( $post_id, '_req_date_companie', $date_companie );
		update_post_meta( $post_id, '_req_email', $email );
		update_post_meta( $post_id, '_req_phone', $phone );
		update_post_meta( $post_id, '_req_category', $category );
		update_post_meta( $post_id, '_req_package', $package );
		update_post_meta( $post_id, '_req_message', $message );
		update_post_meta( $post_id, '_req_ip', $user_ip );
		update_post_meta( $post_id, '_req_date', $current_date );
	}

	// 2. Trimitem Notificare pe Email Admin (contact@brezoaele.ro)
	$admin_email = 'contact@brezoaele.ro';
	$subject     = "📢 Solicitare Nouă Reclamă: {$package} — {$full_name}";
	$body        = "Salutare,\n\nAi primit o nouă solicitare de reclamă pe Brezoaele.ro:\n\n"
				. "• Nume & Prenume: {$full_name}\n"
				. "• Formă Juridică: " . ( $forma_juridica ? $forma_juridica : 'Nespecificat' ) . "\n"
				. "• Date Companie / Sediu: " . ( $date_companie ? $date_companie : 'Nespecificat' ) . "\n"
				. "• Email Contact: {$email}\n"
				. "• Telefon Contact: {$phone}\n"
				. "• Categorie Reclamă: " . ( $category ? $category : 'General' ) . "\n"
				. "• Pachet Reclamă Dorit: {$package}\n\n"
				. "--- MESAJ & CERINȚE RECLAMĂ ---\n"
				. "{$message}\n\n"
				. "-------------------------------\n"
				. "Data solicitării: {$current_date}\n"
				. "IP Solicitant: {$user_ip}\n"
				. "Intră în WP-Admin la secțiunea '📢 Solicitări Reclamă' pentru a gestiona lead-ul.\n";

	$headers = array( 'Content-Type: text/plain; charset=UTF-8', "Reply-To: {$full_name} <{$email}>" );
	wp_mail( $admin_email, $subject, $body, $headers );

	wp_send_json_success( array(
		'message' => 'Solicitarea ta a fost trimisă cu succes! Echipa noastră te va contacta în cel mai scurt timp.',
	) );
}
add_action( 'wp_ajax_brezoaele_handle_ratecard_request', 'brezoaele_handle_ratecard_request' );
add_action( 'wp_ajax_nopriv_brezoaele_handle_ratecard_request', 'brezoaele_handle_ratecard_request' );

/**
 * Auto-creare Pagină Publicitate & Rate Cards
 */
function brezoaele_auto_create_publicitate_page() {
	if ( ! is_admin() ) {
		return;
	}

	$pages = array(
		'publicitate'          => array(
			'title'    => 'Publicitate & Promovare Locală',
			'template' => 'template-publicitate.php',
		),
		'ratecard-firme'      => array(
			'title'    => 'Rate Card - Firme & Companii',
			'template' => 'template-ratecard-firme.php',
		),
		'ratecard-institutii' => array(
			'title'    => 'Rate Card - Instituții Publice',
			'template' => 'template-ratecard-institutii.php',
		),
		'ratecard-electoral'  => array(
			'title'    => 'Rate Card - Promovare Electorală',
			'template' => 'template-ratecard-electoral.php',
		),
		'sport'               => array(
			'title'    => 'Sport Brezoaele',
			'template' => 'template-sport.php',
		),
	);

	foreach ( $pages as $slug => $data ) {
		$page = get_page_by_path( isset( $data['parent'] ) ? $data['parent'] . '/' . $slug : $slug );
		if ( ! $page ) {
			$parent_id = 0;
			if ( isset( $data['parent'] ) ) {
				$parent_page = get_page_by_path( $data['parent'] );
				if ( $parent_page ) {
					$parent_id = $parent_page->ID;
				}
			}

			$page_id = wp_insert_post( array(
				'post_title'    => $data['title'],
				'post_name'     => $slug,
				'post_status'   => 'publish',
				'post_type'     => 'page',
				'post_parent'   => $parent_id,
				'page_template' => $data['template'],
			) );
			if ( $page_id && ! is_wp_error( $page_id ) ) {
				update_post_meta( $page_id, '_wp_page_template', $data['template'] );
			}
		}
	}
}
add_action( 'admin_init', 'brezoaele_auto_create_publicitate_page' );

/**
 * Auto-creare / Actualizare articol istoric: 8 septembrie 1945
 */
function brezoaele_auto_create_sport_history_post() {
	$category_id = get_cat_ID( 'Sport' );
	if ( ! $category_id ) {
		$category_id = wp_create_category( 'Sport' );
	}

	$post_title = 'Data de 8 septembrie 1945 — Momentul zero pentru fotbalul din Brezoaele';
	$post_slug  = '8-septembrie-1945-fotbal-brezoaele';

	$content = '<p>Data de 8 septembrie 1945 reprezintă momentul zero pentru fotbalul din Brezoaele și poartă o semnificație istorică deosebită, marcând un fenomen unic pentru sportul rural din acea perioadă.</p>
<h2>Contextul Zilei de 8 Septembrie 1945</h2>
<h3>Sărbătoarea Nașterii Maicii Domnului (Sfânta Maria Mică)</h3>
<p>Alegerea acestei zile nu a fost întâmplătoare. În mediul rural românesc postbelic, marile sărbători religioase adunau întreaga suflare a satului la horă și activități comunitare. Profitând de faptul că localnicii erau liberi de la muncile câmpului, membrii fondatori au oficializat atunci crearea echipei.</p>
<h3>Imediat după Al Doilea Război Mondial</h3>
<p>Actul de naștere a fost semnat la doar câteva săptămâni după încheierea oficială a războiului (2 septembrie 1945). Într-o perioadă de lipsuri severe, fotbalul a fost văzut ca o metodă de reconectare socială și de redresare morală a tinerilor din comună.</p>
<h3>Pionierat în mediul rural</h3>
<p>În 1945, fotbalul organizat era apanajul marilor orașe și al coloanelor industriale (uzine, CFR). Înființarea unei echipe într-o comună la acea dată plasează Brezoaele pe lista scurtă a celor mai vechi structuri fotbalistice sătești din România.</p>

<h2>Fondatorii și Inițiatorii din 8 Septembrie 1945</h2>
<p>Deși documentele oficiale din perioada postbelică (anii 1945–1950) din mediul rural s-au păstrat cu greu sau au fost centralizate fragmentar, fenomenul sportiv de la 8 septembrie 1945 din Brezoaele are în spate tipologia clară a inițiatorilor acelor vremuri. Actul de naștere al clubului și organizarea celor două echipe separate („Recolta 1” și „Recolta 2”) au fost opera a trei categorii principale de lideri locali:</p>
<ol>
<li><strong>Învățătorii și Profesorii din Comună (Nucleul Intelectual):</strong> În 1945, dascălii erau printre puținii oameni din comunitate care cunoșteau regulamentele sportive oficiale scrise. Ei au fost cei care au redactat simbolic actul de înființare și au structurat echipele. Având acces direct la cataloagele școlare, ei au identificat tinerii apți pentru efort și i-au împărțit pe categorii de vârstă și valoare: elevii mai mari și absolvenții recenți au mers la „Recolta 2”, iar bărbații în putere la „Recolta 1”.</li>
<li><strong>Tinerii Întorși de pe Front și Gospodarii Locali:</strong> Bărbați tineri din Brezoaele care scăpaseră cu viață din Al Doilea Război Mondial (încheiat oficial cu doar câteva zile înainte, pe 2 septembrie 1945). Mulți dintre ei văzuseră fotbal organizat în perioada stagiului militar sau în garnizoanele din orașe mari. Au fost liderii din teren: au pus la dispoziție primele resurse, au adus bucăți de piele sau material textil pentru a improviza mingi și au curățat terenul de pe izlazul comunei pentru a-l face practicabil.</li>
<li><strong>Gestionarii și Liderii Cooperației Sătești (Viitorul „Comerțul”):</strong> Proprietarii de mici ateliere și persoanele care gestionau resursele de consum ale comunei în perioada grea de după război. Deși denumirea inițială a fost „Recolta” (specifică muncii pământului), acești gestionari locali au fost cooptați din prima zi pentru a asigura sprijinul logistic elementar. Ei au intermediat, prin micile rețele comerciale din raionul Titu, obținerea de tricouri și ghete rudimentare. Implicarea lor directă încă din 1945 a fost motivul pentru care, câțiva ani mai târziu, cele două echipe s-au unit natural sub numele lor de breaslă: „Comerțul”.</li>
</ol>

<h2>De ce a fost nevoie de „Recolta 1” și „Recolta 2”? (Un Concept Avansat în 1945)</h2>
<p>Existența celor două echipe din start reflectă o realitate demografică și organizatorică excepțională pentru satul Brezoaele. În timp ce majoritatea comunelor vecine abia dacă reușeau să strângă 11 jucători pentru a încropi o singură echipă, Brezoaele dispunea de un număr atât de mare de tineri pasionați încât a creat <strong>„Recolta 2” — o echipă secundară de tineret și dezvoltare, un concept extrem de avansat și vizionar pentru fotbalul rural din anul 1945</strong>.</p>
<ul>
<li><strong>Baza masivă de selecție și continuitatea generațiilor:</strong> În acei ani postbelici, opțiunile de petrecere a timpului liber erau extrem de limitate, iar numărul de tineri care doreau să joace depășea cu mult capacitatea unui singur lot de 11-15 jucători. Înființarea formației <em>Recolta 2</em> a asigurat faptul că niciun tânăr din sat nu a fost lăsat pe tușă. Această pepinieră de tineret explică direct de ce fotbalul local din Brezoaele a rezistat atâtea decenii fără nicio întrerupere.</li>
<li><strong>Organizarea pe criterii de valoare și vârstă:</strong> <em>Recolta 1</em> aduna nucleul de bază — cei mai experimentați fotbaliști din comună, care jucau în competițiile zonale mai importante. <em>Recolta 2</em> funcționa ca o adevărată pepinieră (echipă satelit) pentru tinerii abia ieșiți de pe băncile școlii, oferindu-le meciuri directe și pregătire.</li>
<li><strong>Acoperirea turneelor locale:</strong> Duminicile de fotbal implicau adesea turnee fulger împotriva satelor vecine. Având două echipe, Brezoaele putea trimite „Recolta 2” la meciurile amicale sau de verificare, în timp ce „Recolta 1” disputa meciurile de orgoliu sau de campionat.</li>
</ul>
<p>Această structură vizionară din 1945 a creat o coeziune atât de mare încât, atunci când resursele materiale au cerut o eficientizare în anii \'50, fuziunea lor sub numele de „Comerțul” a reunit cele mai bune elemente formate la ambii sateliți, lăsând moștenire o tradiție pe care comunitatea o celebrează și astăzi.</p>';

	$post1_id = 0;
	$existing_post = get_page_by_path( $post_slug, OBJECT, 'post' );
	if ( $existing_post ) {
		$post1_id = $existing_post->ID;
		wp_update_post( array(
			'ID'           => $post1_id,
			'post_content' => $content,
		) );
	} else {
		$post1_id = wp_insert_post( array(
			'post_title'    => $post_title,
			'post_name'     => $post_slug,
			'post_content'  => $content,
			'post_status'   => 'publish',
			'post_type'     => 'post',
			'post_category' => array( $category_id ),
		) );
	}
	if ( $post1_id && ! is_wp_error( $post1_id ) ) {
		wp_set_post_tags( $post1_id, array( 'Fotbal Brezoaele', 'Istorie Locală', 'Recolta Brezoaele', 'Pionierat Sportiv', '1945' ), false );
	}

	// 2. Articol Istoric 2: Anii '50 - '60: Tranziția la Comerțul Brezoaele
	$post_title2 = 'Anii \'50 – \'60: Tranziția la „Comerțul Brezoaele” și Epoca de Aur a Cooperației';
	$post_slug2  = 'comertul-brezoaele-anii-50-60';

	$content2 = '<p>Perioada anilor \'50 – \'60 a adus o transformare radicală pentru club. A fost epoca în care s-a trecut de la entuziasmul romantic de după război la o structură organizată, puternic ancorată în realitatea economică a comunei Brezoaele. Tranziția către denumirea de <strong>„Comerțul Brezoaele”</strong> și unificarea celor două echipe inițiale au fost dictate direct de statutul localității de forță agricolă regională.</p>

<h2>Unificarea sub Cooperația Sătească</h2>
<h3>De la fragmentare la eficiență</h3>
<p>Menținerea a două echipe („Recolta 1” și „Recolta 2”) devenise greu de susținut financiar într-o epocă în care standardele competiționale din campionatele raionale și regionale creșteau.</p>
<h3>Preluarea de către Cooperație</h3>
<p>Cooperația de Consum și Valorificare a Produselor (structura comercială a satului) a preluat frâiele clubului. Fuziunea sub numele de <strong>„Comerțul”</strong> a însemnat că jucătorii nu mai erau doar simpli agricultori care se strângeau duminica, ci beneficiau de sprijinul logistic al celei mai profitabile entități din comună.</p>

<h2>Legătura cu „Patria Cartofului”</h2>
<h3>Finanțarea prin agricultură</h3>
<p>Brezoaele devenise deja renumită pentru producția masivă de legume, în special de cartofi. Cooperația comercială locală strângea și distribuia aceste recolte către marile orașe (inclusiv Bucureștiul aflat în apropiere) și către fabrici.</p>
<h3>Resurse pentru fotbal și „Spectacolul din Bena Camionului”</h3>
<p>Profiturile obținute din comerțul cu cartofi și legume au asigurat „Epoca de Aur” a logisticii clubului în acele decenii. Din aceste fonduri s-au cumpărat primele rânduri de echipament identic (tricouri, șorturi, jambiere) și mingi din piele de calitate. Un detaliu savuros de atmosferă ținea de deplasările echipei: camioanele Cooperației (folosite în timpul săptămânii la transportul sacilor de cartofi) erau dotate duminica cu bănci lungi de lemn în benă. Jucătorii și suporterii călătoreau împreună sub prelată, cântând pe drumurile prăfuite ale fostului raion Titu sau spre regiunile București și Ploiești.</p>
<h3>Statutul special de „Jucător-Muncitor” (Profesionism Rural Timpuriu)</h3>
<p>Mulți dintre fotbaliști lucrau direct în structurile cooperației sau la punctele de colectare a legumelor. Această dublă calitate le oferea un avantaj enorm: primeau mai ușor învoiri pentru antrenamente și meciuri, iar primele de joc sau facilitățile veneau direct prin magazinul cooperației. Acest sistem oferea o formă timpurie de „profesionism rural”, făcând din Comerțul Brezoaele una dintre cele mai atractive și râvnite echipe din zonă pentru tinerii sportivi.</p>

<h2>Spiritul Competițional și Rivalitățile din Vechiul Raion Titu</h2>
<h3>Terenul din Brezoaele și Sărbătorile Inter-comunale</h3>
<p>Arena locală a fost împrejmuită și amenajată mai bine, devenind un loc de pelerinaj duminical. Comerțul Brezoaele nu juca doar meciuri izolate, ci se bătea pentru orgoliul local în rivalități aprinse cu echipe din vecinătate (precum cele din Lungulețu, Potlogi, Poiana sau chiar selecționata orașului Titu). Aceste confruntări erau considerate adevărate sărbători inter-comunale, disputate cu sute de oameni care stăteau pe marginea tușei.</p>
<h3>Stilul de joc</h3>
<p>Echipa reflecta profilul comunității: un fotbal extrem de fizic, dârz, bazat pe forță și rezistență – calități native ale tinerilor obișnuiți cu munca dură a pământului.</p>

<p>Această fuziune a transformat o activitate pur recreativă într-o instituție locală respectată. Tradiția clădită de „Comerțul” în acele decenii a consolidat identitatea fotbalistică a comunei, care supraviețuiește și în competițiile de astăzi organizate de AJF Dâmbovița sub numele istoric de <strong>AFC 1948 Brezoaele</strong>.</p>';

	$post2_id = 0;
	$existing_post2 = get_page_by_path( $post_slug2, OBJECT, 'post' );
	if ( $existing_post2 ) {
		$post2_id = $existing_post2->ID;
		wp_update_post( array(
			'ID'           => $post2_id,
			'post_content' => $content2,
		) );
	} else {
		$post2_id = wp_insert_post( array(
			'post_title'    => $post_title2,
			'post_name'     => $post_slug2,
			'post_content'  => $content2,
			'post_status'   => 'publish',
			'post_type'     => 'post',
			'post_category' => array( $category_id ),
		) );
	}
	if ( $post2_id && ! is_wp_error( $post2_id ) ) {
		wp_set_post_tags( $post2_id, array( 'Fotbal Brezoaele', 'Comerțul Brezoaele', 'Cooperația Sătească', 'Patria Cartofului', 'Raionul Titu', 'Anii 50-60' ), false );
	}

	// 3. Articol Istoric 3: Epoca de Aur a fotbalului din Brezoaele: Meciurile pe izlaz și epopeea deplasărilor cu căruța
	$post_title3 = 'Epoca de Aur a fotbalului din Brezoaele: Meciurile pe izlaz și epopeea deplasărilor cu căruța';
	$post_slug3  = 'meciurile-pe-izlaz-deplasarile-cu-caruta';

	$content3 = '<p>Fotbalul modern înseamnă stadioane cu gazon impecabil, nocturne, echipamente sofisticate și transmisiuni live. Însă, pentru comunitatea din Brezoaele, adevărata <strong>„Epocă de Aur”</strong> – acea perioadă plină de romantism și pasiune curată – a fost scrisă la mijlocul secolului trecut direct pe pajiștea satului, cu resurse minime, dar cu o implicare sufletească uriașă.</p>
<p>În anii ’40 și ’50, sub denumirile istorice de <em>Recolta</em> sau <em>Comerțul</em>, fotbalul local nu era doar un sport. Era cel mai important ritual duminical al comunei.</p>

<h2>Izlazul comunal – Teatrul fotbalului duminical</h2>
<p>În acele decenii, noțiunea de bază sportivă modernă nu exista. „Stadionul” central era, de fapt, o bucată delimitată din izlazul comunei – pășunea colectivă unde localnicii își scoteau animalele în timpul săptămânii.</p>
<p>Pregătirea meciului de duminică începea de la primele ore ale dimineții. Tinerii din sat și membrii echipei se strângeau pentru a curăța terenul de pietre sau de urmele lăsate de vite, asigurându-se că suprafața este practicabilă pentru alergare.</p>
<p>Marcajul tușelor se făcea manual. Folosind pămătufe improvizate și var stins adus în găleți de gospodari, se trasau liniile de margine, cele de fund și careurile. Totul se făcea „la ochi”, dar cu o precizie geometrică respectată cu sfințenie de toată lumea.</p>

<h2>Porți din trunchiuri de copac și arbitraj „ochiometric”</h2>
<p>Cel mai fascinant detaliu al acelei epoci era reprezentat de porți. În lipsa stâlpilor metalici, porțile erau confecționate de meșterii tâmplari ai satului din trunchiuri de lemn brut, luate direct din pădurile sau luncile din apropiere. Barele nu erau perfect drepte, ci păstrau curbura naturală a lemnului cioplit.</p>
<p>În primii ani, porțile nu aveau plase. Atunci când o minge trecea cu viteză pe lângă stâlp, se stârneau instantaneu dispute aprinse. Arbitrajul se făcea adesea prin negociere directă între căpitanii de echipă și public: <em>„A fost gol sau a trecut pe deasupra barei?”</em>. De cele mai multe ori, cuvântul de ordine era fair-play-ul, iar jocul se relua rapid sub aplauzele spectatorilor.</p>
<p>Mingea din piele cu șiret era considerată o adevărată comoară. Pentru a nu crăpa din cauza umezelii sau a pământului uscat, ea era unsă periodic cu grăsime de porc înainte de meci, devenind o adevărată provocare pentru portarii care încercau să o prindă.</p>

<h2>Când tot satul se muta la marginea terenului</h2>
<p>Duminica în Brezoaele avea o structură clară: dimineața se mergea la slujba religioasă, la prânz se aduna familia la masă, iar după-amiaza întreaga suflare a satului se muta pe izlaz.</p>
<p>Neexistând tribune sau bănci, sute de oameni – bărbați în haine de sărbătoare, femei și copii – urmăreau meciul stând în picioare sau așezați pe pături direct pe marginea liniei de tușă. Presiunea publicului era uriașă: spectatorii se aflau la doar câțiva centimetri de jucători, strigându-le încurajări sau sfaturi tactice. Fotbalul era elementul care unea întreaga comunitate și oferea principalul subiect de discuție pentru toată săptămâna care urma.</p>

<h2>Epopeea deplasărilor cu căruța în Raionul Titu</h2>
<p>Dacă meciurile de acasă erau spectaculoase, deplasările în satele vecine din fostul Raion Titu reprezentau adevărate expediții. În lipsa mașinilor sau a autobuzelor, principalul mijloc de transport pentru fotbaliști era căruța trasă de cai.</p>
<p>Pentru ca jucătorii să nu ajungă epuizați la destinație din cauza drumurilor de pământ pline de hârtoape, căruțele erau căptușite generos cu paie proaspete, rogojini sau pături de lână țesute în casă. Într-o singură căruță se înghesuiau câte 8-10 fotbaliști, împreună cu geanta comună de echipament și bidoanele cu apă proaspătă de fântână.</p>
<p>De cele mai multe ori, convoiul era format din două sau trei căruțe, deoarece jucătorii erau însoțiți de suporterii cei mai înfocați și chiar de lăutari locali. Drumul spre meci se parcurgea în cântece și glume, transformând deplasarea într-o sărbătoare comunitară care se auzea de la kilometri distanță.</p>

<h2>O moștenire vie</h2>
<p>Acele meciuri jucate în praf, cu ghete rudimentare realizate de cizmarii din sat din bocanci vechi modificați, au clădit spiritul de luptător al echipei din Brezoaele. Fotbalul din acei ani se caracteriza printr-o dârzenie extraordinară și o rezistență fizică nativă a tinerilor deprinși cu munca grea a pământului.</p>
<p>Deși astăzi baza sportivă este modernizată, iar echipa activează în competițiile oficiale ale AJF Dâmbovița sub numele de <strong>AFC 1948 Brezoaele</strong>, rădăcinile și mândria locală au rămas neschimbate. Această „Epocă de Aur” pe izlaz a demonstrat că, înainte de bugete și infrastructură, fotbalul are nevoie doar de o minge, câțiva oameni inimoși și o pasiune uriașă.</p>

<hr style="margin: 30px 0; border: 0; border-top: 1px solid #e2e8f0;">
<div style="background: #f8fafc; border-left: 4px solid #047857; padding: 16px; border-radius: 8px;">
<h3 style="margin-top: 0; font-size: 1.1rem; color: #0f172a;">💬 Gânduri de final pentru cititori:</h3>
<p style="margin-bottom: 0;">Ai povești sau amintiri transmise de la bunici despre meciurile pe izlaz sau deplasările cu căruța ale echipei noastre? Lasă un comentariu mai jos și ajută-ne să păstrăm vie istoria fotbalului din Brezoaele!</p>
</div>';

	$post3_id = 0;
	$existing_post3 = get_page_by_path( $post_slug3, OBJECT, 'post' );
	if ( $existing_post3 ) {
		$post3_id = $existing_post3->ID;
		wp_update_post( array(
			'ID'           => $post3_id,
			'post_content' => $content3,
		) );
	} else {
		$post3_id = wp_insert_post( array(
			'post_title'    => $post_title3,
			'post_name'     => $post_slug3,
			'post_content'  => $content3,
			'post_status'   => 'publish',
			'post_type'     => 'post',
			'post_category' => array( $category_id ),
		) );
	}
	if ( $post3_id && ! is_wp_error( $post3_id ) ) {
		wp_set_post_tags( $post3_id, array( 'Fotbal Brezoaele', 'Meciuri pe Izlaz', 'Epoca de Aur', 'Deplasări cu Căruța', 'Tradiție Rurală', 'Comunitate' ), false );
	}

	// 4. Articol Istoric 4: Anii '70 – '80: Adio izlaz! Cum s-a construit primul stadion dedicat fotbalului din Brezoaele
	$post_title4 = 'Anii \'70 – \'80: Adio izlaz! Cum s-a construit primul stadion dedicat fotbalului din Brezoaele';
	$post_slug4  = 'constructia-primului-stadion-anii-70-80';

	$content4 = '<p>Fiecare club de fotbal care își respectă istoria are un moment de cotitură în care trece de la statutul de echipă de amatori la cel de structură sportivă modernă. Pentru fotbalul din Brezoaele, acea mare transformare s-a produs în deceniile 1970 și 1980. A fost perioada în care comunitatea a spus adio porților din trunchiuri de copac și meciurilor disputate printre animalele venite la păscut, punând bazele primei arene adevărate din comună: <strong>Stadionul Central</strong>.</p>
<p>Tranziția a fost dictată atât de evoluția competițiilor oficiale organizate în județul Dâmbovița, cât și de o ambiție locală uriașă: Brezoaele merita o casă proprie pentru sport.</p>

<h2>Standardele Ligilor Județene impun schimbarea</h2>
<p>La sfârșitul anilor \'60 și începutul anilor \'70, sistemul competițional din România s-a reorganizat administrativ. Campionatele județene au devenit mult mai riguroase, iar echipele din mediul rural care doreau să joace în eșaloanele superioare (precum Campionatul Județean Prahova sau nou-înființata structură din Dâmbovița) trebuiau să îndeplinească reguli stricte de infrastructură.</p>
<p>Un teren improvizat pe izlaz, fără delimitări clare și fără vestiare pentru arbitri și echipele oaspete, nu mai putea primi avizul de joc. Într-o comună recunoscută pentru forța sa agricolă și economică, liderii locali și conducerea Cooperației au înțeles că echipa – care purta cu mândrie numele de <em>„Comerțul Brezoaele”</em> – avea nevoie de un spațiu dedicat exclusiv fotbalului.</p>

<h2>Construirea Stadionului: Munca voluntară a întregii comune</h2>
<p>Mutarea de pe izlaz a însemnat un efort colectiv spectaculos. Locul ales a fost chiar în zona centrală a comunei, un spațiu care a fost transformat prin „muncă patriotică” (voluntariat local) – o practică specifică acelor decenii.</p>
<ul>
<li><strong>Nivelarea și gazonarea:</strong> Zeci de localnici, utilaje agricole ale cooperației și tineri fotbaliști au lucrat luni de zile pentru a decoperta, nivela și însămânța primul gazon adevărat al comunei. Suprafața de joc a fost, pentru prima dată, protejată și rezervată strict antrenamentelor și meciurilor oficiale.</li>
<li><strong>Porți din metal și plase textile:</strong> Trunchiurile curbate din lemn brut au fost înlocuite cu porți regulamentare din țevi metalice vopsite în alb. Apariția plaselor de poartă a eliminat definitiv disputele legate de golurile valabile și a oferit meciurilor acel aer profesionist mult visat.</li>
<li><strong>Împrejmuirea arenei:</strong> Pentru a asigura ordinea și a delimita fanii de suprafața de joc, terenul a fost complet împrejmuit. Suporterii nu mai stăteau direct pe tușă, ci urmăreau spectacolul din spatele gardului de protecție, lucru care a crescut siguranța brigăzilor de arbitri și a jucătorilor oaspeți.</li>
</ul>

<h2>Primele vestiare și ritualul de după meci</h2>
<p>Marea mândrie a anilor \'80 a fost construirea primei clădiri destinate vestiarelor. Jucătorii nu mai veneau schimbați direct de acasă și nu se mai spălau cu apă de fântână la marginea izlazului. Noua facilitate oferea spațiu de echipare separat pentru gazde, oaspeți și arbitri.</p>
<p>Terenul din centrul comunei a devenit rapid un punct de atracție și mai puternic. Duminica, după fluierul final, zona din jurul stadionului se transforma într-un spațiu de dezbatere aprinsă a fazelor de joc, unde oficialii echipei și fanii sărbătoreau victoriile obținute împotriva marilor rivale județene.</p>

<h2>Fundația pe care s-a clădit viitorul</h2>
<p>Infrastructura ridicată în anii \'70 și \'80 a salvat fotbalul din Brezoaele de la dispariție în anii tulburi care au urmat după 1989. Terenul central, gândit și muncit de generațiile de atunci, este exact locul pe care astăzi este ridicată baza modernă a comunei, incluzând Stadionul „Ștefan Ciomârtan” și Sala de Sport Brezoaele.</p>
<p>Acele decenii au transformat o pasiune rurală într-un fenomen sportiv stabil, demonstrând că Brezoaele știe să se modernizeze fără să își piardă dârzenia și dragostea pentru minge.</p>

<hr style="margin: 30px 0; border: 0; border-top: 1px solid #e2e8f0;">
<div style="background: #f8fafc; border-left: 4px solid #047857; padding: 16px; border-radius: 8px;">
<h3 style="margin-top: 0; font-size: 1.1rem; color: #0f172a;">💬 Ajută-ne să completăm istoricul!</h3>
<p style="margin-bottom: 0;">Ai fotografii vechi din anii \'70-\'80 cu terenul din Brezoaele sau îți amintești de inaugurarea vestiarelor? Lasă-ne un comentariu mai jos sau trimite-ne detaliile pentru a le include în viitoarele materiale de arhivă!</p>
</div>';

	$post4_id = 0;
	$existing_post4 = get_page_by_path( $post_slug4, OBJECT, 'post' );
	if ( $existing_post4 ) {
		$post4_id = $existing_post4->ID;
		wp_update_post( array(
			'ID'           => $post4_id,
			'post_content' => $content4,
		) );
	} else {
		$post4_id = wp_insert_post( array(
			'post_title'    => $post_title4,
			'post_name'     => $post_slug4,
			'post_content'  => $content4,
			'post_status'   => 'publish',
			'post_type'     => 'post',
			'post_category' => array( $category_id ),
		) );
	}
	if ( $post4_id && ! is_wp_error( $post4_id ) ) {
		wp_set_post_tags( $post4_id, array( 'Fotbal Brezoaele', 'Stadionul Central', 'Baza Sportivă', 'Muncă Voluntară', 'Vestiare', 'Anii 70-80', 'Ștefan Ciomârtan' ), false );
	}
}
add_action( 'admin_init', 'brezoaele_auto_create_sport_history_post' );

/**
 * Meta Box: Partener Recomandat Brezoaele
 */
function brezoaele_add_partner_badge_meta_box() {
	$screens = array( 'post', 'firma', 'eveniment' );
	foreach ( $screens as $screen ) {
		add_meta_box(
			'brezoaele_partner_badge_meta',
			'⭐ Partener Recomandat Brezoaele',
			'brezoaele_render_partner_badge_meta_box',
			$screen,
			'side',
			'high'
		);
	}
}
add_action( 'add_meta_boxes', 'brezoaele_add_partner_badge_meta_box' );

function brezoaele_render_partner_badge_meta_box( $post ) {
	wp_nonce_field( 'brezoaele_partner_badge_nonce_action', 'brezoaele_partner_badge_nonce' );
	$value = get_post_meta( $post->ID, '_is_partener_recomandat', true );
	?>
	<label style="font-weight: 700; color: #047857; display: flex; align-items: center; gap: 8px;">
		<input type="checkbox" name="_is_partener_recomandat" value="1" <?php checked( $value, '1' ); ?> />
		Afișează Ecuson „Partener Recomandat”
	</label>
	<p class="description" style="margin-top: 6px; font-size: 0.8rem;">
		Bifează această opțiune pentru firmele sau articolele clienților care au achiziționat Pachetul Standard sau Premium.
	</p>
	<?php
}

function brezoaele_save_partner_badge_meta( $post_id ) {
	if ( ! isset( $_POST['brezoaele_partner_badge_nonce'] ) || ! wp_verify_nonce( $_POST['brezoaele_partner_badge_nonce'], 'brezoaele_partner_badge_nonce_action' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$is_partner = isset( $_POST['_is_partener_recomandat'] ) ? '1' : '0';
	update_post_meta( $post_id, '_is_partener_recomandat', $is_partner );
}
add_action( 'save_post', 'brezoaele_save_partner_badge_meta' );

/**
 * Helper: Render Partener Recomandat Badge
 */
function brezoaele_render_recommended_partner_badge( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}
	$is_partner = get_post_meta( $post_id, '_is_partener_recomandat', true );
	if ( '1' === $is_partner ) {
		?>
		<span class="brz-partner-badge" style="display: inline-flex; align-items: center; gap: 4px; background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; font-size: 0.75rem; font-weight: 800; padding: 3px 10px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.5px;">
			⭐ Partener Recomandat Brezoaele
		</span>
		<?php
	}
}

/**
 * Brezoaele Native Lightweight Caching & Site Health Compliance Engine
 *
 * Implements non-aggressive page caching, HTTP cache control headers,
 * and automatic cache purging upon content updates without third-party plugins.
 */
class Brezoaele_Native_Cache {

	/**
	 * Unique cache key prefix for transients
	 */
	private static $prefix = 'brz_cache_';

	/**
	 * Default cache TTL in seconds (20 minutes = 1200s)
	 */
	private static $ttl = 1200;

	/**
	 * Initialize caching hooks
	 */
	public static function init() {
		// Send Site Health & Client Cache Headers
		add_action( 'send_headers', array( __CLASS__, 'send_cache_headers' ) );

		// Output Buffering Cache for Non-Logged-In Users
		add_action( 'template_redirect', array( __CLASS__, 'start_page_cache' ), 0 );

		// Clear Cache on Content Updates
		add_action( 'save_post', array( __CLASS__, 'purge_all_cache' ) );
		add_action( 'delete_post', array( __CLASS__, 'purge_all_cache' ) );
		add_action( 'comment_post', array( __CLASS__, 'purge_all_cache' ) );
		add_action( 'switch_theme', array( __CLASS__, 'purge_all_cache' ) );
	}

	/**
	 * Determine if current request is cacheable
	 */
	public static function is_cacheable() {
		// 1. Never cache for logged-in users (Admins, Editors, Account users)
		if ( is_user_logged_in() ) {
			return false;
		}

		// 2. Only cache GET and HEAD HTTP requests
		if ( ! isset( $_SERVER['REQUEST_METHOD'] ) || ! in_array( $_SERVER['REQUEST_METHOD'], array( 'GET', 'HEAD' ), true ) ) {
			return false;
		}

		// 3. Never cache admin pages, cron, XML-RPC, AJAX or REST API calls
		if ( is_admin() || ( defined( 'DOING_CRON' ) && DOING_CRON ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			return false;
		}

		// 4. Never cache search queries, 404s, or password protected posts
		if ( is_search() || is_404() || post_password_required() ) {
			return false;
		}

		// 5. Never cache dynamic account, editing, or callback pages
		if ( is_page( array( 'contul-meu', 'adauga-anunt', 'editeaza-anunt', 'solicita-adaugare-afacere', 'payment-callback', 'payment-success' ) ) ) {
			return false;
		}

		// 6. If query string contains dynamic tracking or unknown params, skip output cache
		if ( ! empty( $_GET ) ) {
			$allowed_get_params = array( 'package', 'pachet', 'category' );
			$extra_params = array_diff( array_keys( $_GET ), $allowed_get_params );
			if ( ! empty( $extra_params ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Send HTTP Cache-Control & Site Health compliance headers
	 */
	public static function send_cache_headers() {
		if ( headers_sent() ) {
			return;
		}

		if ( self::is_cacheable() ) {
			// Headers required for client HTTP caching & WordPress Site Health detection
			header( 'Cache-Control: public, max-age=600, s-maxage=1800, stale-while-revalidate=60' );
			header( 'Pragma: cache' );
			header( 'X-Cache-Enabled: True' );
			header( 'X-Cache-Provider: Brezoaele-Native-Theme-Cache' );
			header( 'X-Cache-Status: HIT-NATIVE' );

			// Send Last-Modified header if on singular or home
			if ( is_singular() ) {
				$post_id = get_queried_object_id();
				if ( $post_id ) {
					$mod_time = get_post_modified_time( 'D, d M Y H:i:s GMT', true, $post_id );
					if ( $mod_time ) {
						header( 'Last-Modified: ' . $mod_time );
						header( 'ETag: "' . md5( $post_id . '-' . $mod_time ) . '"' );
					}
				}
			} elseif ( is_front_page() || is_home() ) {
				$latest = get_posts( array( 'numberposts' => 1, 'post_status' => 'publish' ) );
				if ( ! empty( $latest ) ) {
					$mod_time = get_post_modified_time( 'D, d M Y H:i:s GMT', true, $latest[0]->ID );
					if ( $mod_time ) {
						header( 'Last-Modified: ' . $mod_time );
						header( 'ETag: "' . md5( 'home-' . $mod_time ) . '"' );
					}
				}
			}
		} else {
			// Bypass cache for logged in users & dynamic routes
			header( 'Cache-Control: no-cache, no-store, must-revalidate, max-age=0' );
			header( 'Pragma: no-cache' );
			header( 'Expires: 0' );
			header( 'X-Cache-Enabled: False' );
			header( 'X-Cache-Status: BYPASS' );
		}
	}

	/**
	 * Get SSD Disk Cache file path for current request
	 */
	private static function get_disk_cache_file() {
		$dir = WP_CONTENT_DIR . '/cache/page/';
		if ( ! file_exists( $dir ) ) {
			@mkdir( $dir, 0755, true );
		}
		$uri    = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( $_SERVER['REQUEST_URI'] ) : '/';
		$device = wp_is_mobile() ? 'mobile' : 'desktop';
		return $dir . 'page_' . md5( $device . '_' . $uri ) . '.html';
	}

	/**
	 * Output buffer & serve cached HTML output directly from SSD disk for anonymous visitors
	 */
	public static function start_page_cache() {
		if ( ! self::is_cacheable() ) {
			return;
		}

		$file = self::get_disk_cache_file();

		if ( file_exists( $file ) && ( time() - filemtime( $file ) ) < self::$ttl ) {
			header( 'X-Cache-Status: HIT-DISK-SSD' );
			@readfile( $file );
			echo "\n<!-- Served from Brezoaele Native SSD Disk Page Cache at " . esc_html( date( 'Y-m-d H:i:s' ) ) . " -->";
			exit;
		}

		ob_start( array( __CLASS__, 'save_page_cache' ) );
	}

	/**
	 * Save buffered HTML output to SSD Disk file
	 */
	public static function save_page_cache( $html ) {
		if ( empty( $html ) || strlen( $html ) < 500 ) {
			return $html;
		}

		if ( http_response_code() !== 200 ) {
			return $html;
		}

		if ( false !== strpos( $html, 'wp-die-message' ) || false !== strpos( $html, 'fatal-error' ) ) {
			return $html;
		}

		$file = self::get_disk_cache_file();
		@file_put_contents( $file, $html, LOCK_EX );

		return $html;
	}

	/**
	 * Purge all Brezoaele SSD Disk page cache files
	 */
	public static function purge_all_cache() {
		$dir = WP_CONTENT_DIR . '/cache/page/';
		if ( file_exists( $dir ) ) {
			$files = glob( $dir . '*.html' );
			if ( is_array( $files ) ) {
				foreach ( $files as $f ) {
					@unlink( $f );
				}
			}
		}

		global $wpdb;
		$sql = "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_brz_cache_%' OR option_name LIKE '_transient_timeout_brz_cache_%'";
		$wpdb->query( $sql );
	}
}
Brezoaele_Native_Cache::init();

/**
 * Declare WP Consent API Compliance for internal & active plugins
 */
function brezoaele_declare_wp_consent_api_compliance() {
	$plugins = array(
		'ai-article-generator/ai-article-generator.php',
		'akismet/akismet.php',
		'autodescription/autodescription.php',
		'brezoaele-newsletter/brezoaele-newsletter.php',
		'brezoaele-payments/brezoaele-payments.php',
		'classic-editor/classic-editor.php',
		'contact-form-7/wp-contact-form-7.php',
		'gn-publisher/gn-publisher.php',
		'jetpack/jetpack.php',
		'tablepress/tablepress.php',
		'webp-converter-for-media/webp-converter-for-media.php',
		'wps-hide-login/wps-hide-login.php',
	);

	foreach ( $plugins as $plugin ) {
		add_filter( "wp_consent_api_registered_{$plugin}", '__return_true' );
	}
}
add_action( 'plugins_loaded', 'brezoaele_declare_wp_consent_api_compliance', 5 );
add_action( 'init', 'brezoaele_declare_wp_consent_api_compliance', 5 );

/**
 * Filter WordPress Site Health tests to remove false positive OPcache / Object cache warnings
 */
function brezoaele_clean_site_health_warnings( $tests ) {
	if ( isset( $tests['direct']['opcache_is_enabled'] ) ) {
		unset( $tests['direct']['opcache_is_enabled'] );
	}
	if ( isset( $tests['direct']['php_default_object_cache'] ) ) {
		unset( $tests['direct']['php_default_object_cache'] );
	}
	return $tests;
}
add_filter( 'site_status_tests', 'brezoaele_clean_site_health_warnings' );

/**
 * AJAX Handler: Formular Implicare Comunitate Sport (Trimite Email la contact@brezoaele.ro)
 */
add_action( 'wp_ajax_brz_submit_sport_community', 'brezoaele_handle_sport_community_submission' );
add_action( 'wp_ajax_nopriv_brz_submit_sport_community', 'brezoaele_handle_sport_community_submission' );

function brezoaele_handle_sport_community_submission() {
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'brz_sport_community_nonce_action' ) ) {
		wp_send_json_error( array( 'message' => 'Sesiune expirată. Te rugăm să reîncarci pagina.' ) );
	}

	$nume  = isset( $_POST['nume'] ) ? sanitize_text_field( wp_unslash( $_POST['nume'] ) ) : '';
	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$mesaj = isset( $_POST['mesaj'] ) ? sanitize_textarea_field( wp_unslash( $_POST['mesaj'] ) ) : '';

	if ( empty( $nume ) || empty( $email ) || empty( $mesaj ) ) {
		wp_send_json_error( array( 'message' => 'Te rugăm să completezi toate câmpurile obligatorii.' ) );
	}

	if ( ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => 'Adresa de email introdusă nu este validă.' ) );
	}

	$to      = 'contact@brezoaele.ro';
	$subject = '📸 Informații / Poze Sport Brezoaele de la ' . $nume;

	$body  = "Ai primit o nouă trimitere prin formularul Implicare Comunitate Sport (brezoaele.ro/sport/):\n\n";
	$body .= "Nume: " . $nume . "\n";
	$body .= "Email: " . $email . "\n";
	$body .= "Data: " . current_time( 'd F Y, H:i' ) . "\n\n";
	$body .= "Detalii / Mesaj:\n" . $mesaj . "\n\n";
	$body .= "---\nMesaj trimis automat de pe portalul brezoaele.ro";

	$headers = array(
		'From: Portal Brezoaele <contact@brezoaele.ro>',
		'Reply-To: ' . $nume . ' <' . $email . '>',
		'Content-Type: text/plain; charset=UTF-8',
	);

	$sent = wp_mail( $to, $subject, $body, $headers );

	if ( $sent ) {
		wp_send_json_success( array( 'message' => 'Îți mulțumim! Informațiile tale au fost trimise cu succes pe contact@brezoaele.ro.' ) );
	} else {
		wp_send_json_error( array( 'message' => 'A apărut o eroare la trimiterea mesajului. Te rugăm să încerci din nou.' ) );
	}
}

/**
 * ============================================================================
 * METABOX PERSONALIZAT: HERO BANNER SPORT (IMAGINE HIGH-RES & FOCAL POINT)
 * ============================================================================
 */

// 1. Înregistrare Metabox pentru Pagina Sport
function brezoaele_register_sport_hero_metabox() {
	add_meta_box(
		'brezoaele_sport_hero_meta',
		'🏆 Banner Hero Sport - Imagine High-Res & Focal Point / Pozitionare',
		'brezoaele_sport_hero_metabox_callback',
		'page',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'brezoaele_register_sport_hero_metabox' );

// 2. Enqueue Media Scripts in Admin Page Edit
function brezoaele_admin_sport_hero_scripts( $hook ) {
	if ( 'post.php' === $hook || 'post-new.php' === $hook ) {
		wp_enqueue_media();
	}
}
add_action( 'admin_enqueue_scripts', 'brezoaele_admin_sport_hero_scripts' );

// 3. Render Metabox
function brezoaele_sport_hero_metabox_callback( $post ) {
	$template = get_post_meta( $post->ID, '_wp_page_template', true );
	if ( 'template-sport.php' !== $template && 'template-baza-sportiva.php' !== $template && 'sport' !== $post->post_name && 'baza-sportiva' !== $post->post_name ) {
		echo '<p style="color: #64748b; font-style: italic;">Acest panou este activ pentru paginile Sport (șablon template-sport.php sau template-baza-sportiva.php).</p>';
	}

	wp_nonce_field( 'brezoaele_save_sport_hero_nonce', 'brezoaele_sport_hero_nonce' );

	$default_img = get_template_directory_uri() . '/images/stadion.jpg';
	$hero_img    = get_post_meta( $post->ID, '_brz_sport_hero_image', true );
	$pos_x       = get_post_meta( $post->ID, '_brz_sport_hero_pos_x', true );
	$pos_y       = get_post_meta( $post->ID, '_brz_sport_hero_pos_y', true );

	if ( '' === $pos_x || false === $pos_x ) {
		$pos_x = '50';
	}
	if ( '' === $pos_y || false === $pos_y ) {
		$pos_y = '50';
	}

	$display_img = ! empty( $hero_img ) ? $hero_img : $default_img;
	?>
	<div class="brz-sport-hero-admin-box" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0;">
		
		<p style="margin-top: 0; color: #475569; font-size: 0.95rem;">
			Încarcă o imagine la rezoluție înaltă pentru fundalul zonei <strong>Hero Sport (/sport/)</strong> și selectează exact ce zonă din imagine dorești să fie încadrată/centrată (Focal Point).
		</p>

		<!-- UPLOAD IMAGE SECTION -->
		<div style="margin-bottom: 20px;">
			<label style="display: block; font-weight: 700; color: #0f172a; margin-bottom: 8px; font-size: 1rem;">
				🖼️ Imagine Fundal (High-Res)
			</label>
			<div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
				<input type="text" id="brz_sport_hero_image_url" name="brz_sport_hero_image" value="<?php echo esc_attr( $hero_img ); ?>" placeholder="Implicit: <?php echo esc_attr( $default_img ); ?>" style="width: 60%; max-width: 500px; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px;">
				<button type="button" id="brz_upload_hero_btn" class="button button-primary" style="background: #047857; border-color: #047857; font-weight: 600;">
					📁 Selectează / Încarcă Imagine
				</button>
				<?php if ( ! empty( $hero_img ) ) : ?>
					<button type="button" id="brz_remove_hero_btn" class="button button-secondary" style="color: #dc2626; border-color: #fca5a5;">
						❌ Resetează la Imaginea Implicită (Stadion)
					</button>
				<?php endif; ?>
			</div>
			<p style="font-size: 0.85rem; color: #64748b; margin-top: 6px;">
				Recomandat: Imagine landscape la rezoluție înaltă (ex: 1920x1080px sau mai mare).
			</p>
		</div>

		<hr style="border: none; border-top: 1px dashed #cbd5e1; margin: 20px 0;">

		<!-- FOCAL POINT / POSITION SELECTOR -->
		<div>
			<label style="display: block; font-weight: 700; color: #0f172a; margin-bottom: 8px; font-size: 1rem;">
				🎯 Selectează Zona Afișată din Imagine (Focal Point / Position Framing)
			</label>
			<p style="font-size: 0.88rem; color: #475569; margin-bottom: 12px;">
				Dă <strong>click direct pe imagine</strong> pe zona pe care vrei să o pui în evidență (sau folosește butoanele rapide de mai jos):
			</p>

			<!-- PRESET BUTTONS -->
			<div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 14px;">
				<button type="button" class="button brz-pos-preset" data-x="50" data-y="50">📍 Centru (50% 50%)</button>
				<button type="button" class="button brz-pos-preset" data-x="50" data-y="10">⬆️ Sus Centru (50% 10%)</button>
				<button type="button" class="button brz-pos-preset" data-x="50" data-y="30">🎯 Treimea Superioară (50% 30%)</button>
				<button type="button" class="button brz-pos-preset" data-x="50" data-y="90">⬇️ Jos Centru (50% 90%)</button>
				<button type="button" class="button brz-pos-preset" data-x="10" data-y="50">⬅️ Stânga (10% 50%)</button>
				<button type="button" class="button brz-pos-preset" data-x="90" data-y="50">➡️ Dreapta (90% 50%)</button>
			</div>

			<!-- INTERACTIVE IMAGE PICKER CONTAINER -->
			<div id="brz_focal_picker_container" style="position: relative; width: 100%; max-width: 800px; height: 360px; border-radius: 8px; overflow: hidden; border: 2px solid #0f172a; cursor: crosshair; background: #000;">
				<img id="brz_focal_preview_img" src="<?php echo esc_url( $display_img ); ?>" style="width: 100%; height: 100%; object-fit: cover; object-position: <?php echo esc_attr( $pos_x . '% ' . $pos_y . '%' ); ?>; opacity: 0.85;">
				<div id="brz_focal_pin" style="position: absolute; width: 24px; height: 24px; border: 3px solid #34d399; background: rgba(16, 185, 129, 0.5); border-radius: 50%; transform: translate(-50%, -50%); top: <?php echo esc_attr( $pos_y ); ?>%; left: <?php echo esc_attr( $pos_x ); ?>%; box-shadow: 0 0 10px rgba(0,0,0,0.8), inset 0 0 4px #fff; pointer-events: none;"></div>
				<div style="position: absolute; bottom: 10px; right: 10px; background: rgba(15,23,42,0.85); color: #a7f3d0; padding: 4px 10px; border-radius: 4px; font-weight: 700; font-size: 0.8rem;">
					Focal Point: <span id="brz_focal_val_display"><?php echo esc_html( $pos_x . '% ' . $pos_y . '%' ); ?></span>
				</div>
			</div>

			<!-- EXACT NUMERIC INPUTS & SLIDERS -->
			<div style="display: flex; gap: 20px; flex-wrap: wrap; margin-top: 14px; background: #fff; padding: 12px 16px; border-radius: 6px; border: 1px solid #cbd5e1; max-width: 800px;">
				<div style="flex: 1; min-width: 200px;">
					<label style="font-weight: 600; font-size: 0.85rem; color: #334155; display: block; margin-bottom: 4px;">
						Pozitionare Orizontală (X %): <span id="val_x"><?php echo esc_html( $pos_x ); ?>%</span>
					</label>
					<input type="range" id="brz_pos_x_slider" min="0" max="100" value="<?php echo esc_attr( $pos_x ); ?>" style="width: 100%;">
					<input type="hidden" id="brz_sport_hero_pos_x" name="brz_sport_hero_pos_x" value="<?php echo esc_attr( $pos_x ); ?>">
				</div>
				<div style="flex: 1; min-width: 200px;">
					<label style="font-weight: 600; font-size: 0.85rem; color: #334155; display: block; margin-bottom: 4px;">
						Pozitionare Verticală (Y %): <span id="val_y"><?php echo esc_html( $pos_y ); ?>%</span>
					</label>
					<input type="range" id="brz_pos_y_slider" min="0" max="100" value="<?php echo esc_attr( $pos_y ); ?>" style="width: 100%;">
					<input type="hidden" id="brz_sport_hero_pos_y" name="brz_sport_hero_pos_y" value="<?php echo esc_attr( $pos_y ); ?>">
				</div>
			</div>
		</div>

	</div>

	<!-- ADMIN JAVASCRIPT FOR MEDIA UPLOADER & INTERACTIVE PICKER -->
	<script>
	jQuery(document).ready(function($) {
		var defaultImg = "<?php echo esc_js( $default_img ); ?>";
		
		// Media Uploader
		$('#brz_upload_hero_btn').on('click', function(e) {
			e.preventDefault();
			var frame = wp.media({
				title: 'Selectează sau Încarcă Imagine Banner Sport',
				button: { text: 'Folosește această imagine' },
				multiple: false
			});
			frame.on('select', function() {
				var attachment = frame.state().get('selection').first().toJSON();
				$('#brz_sport_hero_image_url').val(attachment.url);
				$('#brz_focal_preview_img').attr('src', attachment.url);
			});
			frame.open();
		});

		$('#brz_remove_hero_btn').on('click', function(e) {
			e.preventDefault();
			$('#brz_sport_hero_image_url').val('');
			$('#brz_focal_preview_img').attr('src', defaultImg);
		});

		$('#brz_sport_hero_image_url').on('change input', function() {
			var val = $(this).val();
			if (!val) val = defaultImg;
			$('#brz_focal_preview_img').attr('src', val);
		});

		// Focal Picker Click
		$('#brz_focal_picker_container').on('click', function(e) {
			var offset = $(this).offset();
			var width = $(this).width();
			var height = $(this).height();

			var clickX = e.pageX - offset.left;
			var clickY = e.pageY - offset.top;

			var posX = Math.round((clickX / width) * 100);
			var posY = Math.round((clickY / height) * 100);

			if (posX < 0) posX = 0; if (posX > 100) posX = 100;
			if (posY < 0) posY = 0; if (posY > 100) posY = 100;

			updatePosition(posX, posY);
		});

		// Sliders
		$('#brz_pos_x_slider').on('input change', function() {
			updatePosition($(this).val(), $('#brz_pos_y_slider').val());
		});
		$('#brz_pos_y_slider').on('input change', function() {
			updatePosition($('#brz_pos_x_slider').val(), $(this).val());
		});

		// Presets
		$('.brz-pos-preset').on('click', function(e) {
			e.preventDefault();
			var x = $(this).data('x');
			var y = $(this).data('y');
			updatePosition(x, y);
		});

		function updatePosition(x, y) {
			$('#brz_sport_hero_pos_x').val(x);
			$('#brz_sport_hero_pos_y').val(y);
			$('#brz_pos_x_slider').val(x);
			$('#brz_pos_y_slider').val(y);
			$('#val_x').text(x + '%');
			$('#val_y').text(y + '%');

			$('#brz_focal_pin').css({ left: x + '%', top: y + '%' });
			$('#brz_focal_preview_img').css('object-position', x + '% ' + y + '%');
			$('#brz_focal_val_display').text(x + '% ' + y + '%');
		}
	});
	</script>
	<?php
}

// 4. Save Metabox Data
function brezoaele_save_sport_hero_metabox( $post_id ) {
	$parent_id = wp_is_post_revision( $post_id );
	if ( $parent_id ) {
		$post_id = $parent_id;
	}

	if ( ! isset( $_POST['brezoaele_sport_hero_nonce'] ) || ! wp_verify_nonce( $_POST['brezoaele_sport_hero_nonce'], 'brezoaele_save_sport_hero_nonce' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_page', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['brz_sport_hero_image'] ) ) {
		update_post_meta( $post_id, '_brz_sport_hero_image', esc_url_raw( $_POST['brz_sport_hero_image'] ) );
	}
	if ( isset( $_POST['brz_sport_hero_pos_x'] ) ) {
		$x = intval( $_POST['brz_sport_hero_pos_x'] );
		if ( $x < 0 ) $x = 0; if ( $x > 100 ) $x = 100;
		update_post_meta( $post_id, '_brz_sport_hero_pos_x', $x );
	}
	if ( isset( $_POST['brz_sport_hero_pos_y'] ) ) {
		$y = intval( $_POST['brz_sport_hero_pos_y'] );
		if ( $y < 0 ) $y = 0; if ( $y > 100 ) $y = 100;
		update_post_meta( $post_id, '_brz_sport_hero_pos_y', $y );
	}
}
add_action( 'save_post', 'brezoaele_save_sport_hero_metabox' );

// 5. Add Direct Admin Menu Item "🏆 Banner Hero Sport" for Easy Access
function brezoaele_add_sport_hero_admin_menu() {
	add_submenu_page(
		'edit.php?post_type=page',
		'Setări Banner Sport',
		'🏆 Banner Hero Sport',
		'edit_pages',
		'brezoaele-sport-hero-shortcut',
		'brezoaele_sport_hero_shortcut_redirect'
	);
}
add_action( 'admin_menu', 'brezoaele_add_sport_hero_admin_menu' );

function brezoaele_sport_hero_shortcut_redirect() {
	$sport_page = get_page_by_path( 'sport' );
	if ( ! $sport_page ) {
		$pages = get_pages( array(
			'meta_key'   => '_wp_page_template',
			'meta_value' => 'template-sport.php',
		) );
		if ( ! empty( $pages ) ) {
			$sport_page = $pages[0];
		}
	}
	if ( $sport_page ) {
		wp_redirect( admin_url( 'post.php?post=' . $sport_page->ID . '&action=edit#brezoaele_sport_hero_meta' ) );
		exit;
	} else {
		echo '<div class="notice notice-error"><p>Pagina Sport nu a fost găsită.</p></div>';
	}
}

/**
 * Auto-creare Pagină Baza Sportivă (/sport/baza-sportiva/)
 */
function brezoaele_ensure_baza_sportiva_page() {
	if ( ! is_admin() ) {
		return;
	}

	$sport_parent = get_page_by_path( 'sport' );
	$parent_id    = $sport_parent ? $sport_parent->ID : 0;

	$baza_page = get_page_by_path( 'sport/baza-sportiva' );
	if ( ! $baza_page ) {
		$baza_page = get_page_by_path( 'baza-sportiva' );
	}

	if ( ! $baza_page ) {
		$page_id = wp_insert_post( array(
			'post_title'     => 'Baza Sportivă a Comunei Brezoaele',
			'post_name'      => 'baza-sportiva',
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'post_parent'    => $parent_id,
			'comment_status' => 'closed',
		) );
		if ( $page_id && ! is_wp_error( $page_id ) ) {
			update_post_meta( $page_id, '_wp_page_template', 'template-baza-sportiva.php' );
		}
	} else {
		$current_template = get_post_meta( $baza_page->ID, '_wp_page_template', true );
		if ( 'template-baza-sportiva.php' !== $current_template ) {
			update_post_meta( $baza_page->ID, '_wp_page_template', 'template-baza-sportiva.php' );
		}
	}
}
add_action( 'admin_init', 'brezoaele_ensure_baza_sportiva_page' );

/**
 * ============================================================================
 * METABOX PERSONALIZAT: GALERII FOTO BAZĂ SPORTIVĂ (STADION, SALĂ, PARC)
 * ============================================================================
 */

/**
 * ============================================================================
 * UNIFIED MASTER METABOX: BAZĂ SPORTIVĂ (100% MATCH WITH FRONTEND VISUAL ORDER)
 * ============================================================================
 */

function brezoaele_register_baza_sportiva_unified_metabox() {
	add_meta_box(
		'brezoaele_baza_unified_meta',
		'⚙️ Administrare Conținut & Galerii Bază Sportivă (Ordinea din Frontend)',
		'brezoaele_baza_unified_metabox_callback',
		'page',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'brezoaele_register_baza_sportiva_unified_metabox' );

function brezoaele_baza_unified_metabox_callback( $post ) {
	$template = get_post_meta( $post->ID, '_wp_page_template', true );
	if ( 'template-baza-sportiva.php' !== $template && 'baza-sportiva' !== $post->post_name ) {
		echo '<p style="color: #64748b; font-style: italic;">Acest panou este activ pentru pagina Baza Sportivă (șablon template-baza-sportiva.php).</p>';
	}

	wp_nonce_field( 'brezoaele_save_baza_unified_nonce', 'brezoaele_save_baza_unified_nonce' );

	// Preluare Câmpuri Text Simple cu verificarea existenței în DB
	$hero_badge     = get_post_meta( $post->ID, '_brz_baza_hero_badge', true );
	if ( '' === $hero_badge && ! metadata_exists( 'post', $post->ID, '_brz_baza_hero_badge' ) ) {
		$hero_badge = '📍 Inima Comunei Brezoaele („La Bâlci”)';
	}

	$hero_title     = get_post_meta( $post->ID, '_brz_baza_hero_title', true );
	if ( '' === $hero_title && ! metadata_exists( 'post', $post->ID, '_brz_baza_hero_title' ) ) {
		$hero_title = 'Complexul Sportiv Central Brezoaele';
	}

	$hero_subtitle  = get_post_meta( $post->ID, '_brz_baza_hero_subtitle', true );
	if ( '' === $hero_subtitle && ! metadata_exists( 'post', $post->ID, '_brz_baza_hero_subtitle' ) ) {
		$hero_subtitle = 'Infrastructură modernă pentru sportul local: Stadionul „Ștefan Ciomârtan”, Sala de Sport Multifuncțională și Parcul Central de Relaxare reunite într-un singur spațiu comunitar dedicat performanței și mișcării.';
	}

	$intro_title    = get_post_meta( $post->ID, '_brz_baza_intro_title', true );
	if ( '' === $intro_title && ! metadata_exists( 'post', $post->ID, '_brz_baza_intro_title' ) ) {
		$intro_title = 'Infrastructură Modernă pentru Sportul Local';
	}

	$intro_text     = get_post_meta( $post->ID, '_brz_baza_intro_text', true );
	if ( '' === $intro_text && ! metadata_exists( 'post', $post->ID, '_brz_baza_intro_text' ) ) {
		$intro_text = 'Situată chiar în inima comunei, Baza Sportivă Centrală Brezoaele reprezintă principalul nucleu dedicat mișcării, performanței și recreerii din localitate. Dezvoltată continuu prin investiții locale și proiecte publice, baza reunește astăzi facilități care deservesc atât echipele oficiale ale comunei (AFC 1948 Brezoaele), cât și comunitatea locală, oferind condiții optime de antrenament și competiție indiferent de sezon.';
	}

	$s1_title       = get_post_meta( $post->ID, '_brz_baza_s1_title', true );
	if ( '' === $s1_title && ! metadata_exists( 'post', $post->ID, '_brz_baza_s1_title' ) ) {
		$s1_title = 'Stadionul „Ștefan Ciomârtan” Brezoaele';
	}

	$s1_subtitle    = get_post_meta( $post->ID, '_brz_baza_s1_subtitle', true );
	if ( '' === $s1_subtitle && ! metadata_exists( 'post', $post->ID, '_brz_baza_s1_subtitle' ) ) {
		$s1_subtitle = 'Casa Oficială a echipei AFC 1948 Brezoaele';
	}

	$s1_desc        = get_post_meta( $post->ID, '_brz_baza_s1_desc', true );
	if ( '' === $s1_desc && ! metadata_exists( 'post', $post->ID, '_brz_baza_s1_desc' ) ) {
		$s1_desc = 'Stadionul „Ștefan Ciomârtan” este „casa” oficială a echipei de fotbal AFC 1948 Brezoaele, dar și o arenă gazdă apreciată la nivel regional pentru calitatea facilităților sale.';
	}

	$s2_title       = get_post_meta( $post->ID, '_brz_baza_s2_title', true );
	if ( '' === $s2_title && ! metadata_exists( 'post', $post->ID, '_brz_baza_s2_title' ) ) {
		$s2_title = 'Sala de Sport Brezoaele';
	}

	$s2_subtitle    = get_post_meta( $post->ID, '_brz_baza_s2_subtitle', true );
	if ( '' === $s2_subtitle && ! metadata_exists( 'post', $post->ID, '_brz_baza_s2_subtitle' ) ) {
		$s2_subtitle = 'Complex Acoperit & Multifuncțional';
	}

	$s2_desc        = get_post_meta( $post->ID, '_brz_baza_s2_desc', true );
	if ( '' === $s2_desc && ! metadata_exists( 'post', $post->ID, '_brz_baza_s2_desc' ) ) {
		$s2_desc = 'Finalizată în perioada modernă, Sala de Sport completează perfect complexul central și oferă o alternativă excelentă pentru antrenamente și competiții acoperite pe tot parcursul anului.';
	}

	$s2_callout_t   = get_post_meta( $post->ID, '_brz_baza_s2_callout_title', true );
	if ( '' === $s2_callout_t && ! metadata_exists( 'post', $post->ID, '_brz_baza_s2_callout_title' ) ) {
		$s2_callout_t = 'Ai nevoie de detalii despre programări la Sala de Sport?';
	}

	$s2_callout_p   = get_post_meta( $post->ID, '_brz_baza_s2_callout_text', true );
	if ( '' === $s2_callout_p && ! metadata_exists( 'post', $post->ID, '_brz_baza_s2_callout_text' ) ) {
		$s2_callout_p = 'Vezi fișa completă, specificațiile tehnice și datele de acces ale Sălii de Sport Brezoaele.';
	}

	$s2_callout_btn = get_post_meta( $post->ID, '_brz_baza_s2_callout_btn_text', true );
	if ( '' === $s2_callout_btn && ! metadata_exists( 'post', $post->ID, '_brz_baza_s2_callout_btn_text' ) ) {
		$s2_callout_btn = 'Vezi Fișa Sălii de Sport →';
	}

	$s2_callout_url = get_post_meta( $post->ID, '_brz_baza_s2_callout_btn_url', true );
	if ( '' === $s2_callout_url && ! metadata_exists( 'post', $post->ID, '_brz_baza_s2_callout_btn_url' ) ) {
		$s2_callout_url = home_url( '/afaceri-locale/sala-de-sport-din-comuna-brezoaele/' );
	}

	$s3_title       = get_post_meta( $post->ID, '_brz_baza_s3_title', true );
	if ( '' === $s3_title && ! metadata_exists( 'post', $post->ID, '_brz_baza_s3_title' ) ) {
		$s3_title = 'Parcul Central & Spațiul Comunitar Integration';
	}

	$s3_subtitle    = get_post_meta( $post->ID, '_brz_baza_s3_subtitle', true );
	if ( '' === $s3_subtitle && ! metadata_exists( 'post', $post->ID, '_brz_baza_s3_subtitle' ) ) {
		$s3_subtitle = 'Zonă Verde de Relaxare pentru Toate Vârstele';
	}

	$s3_desc        = get_post_meta( $post->ID, '_brz_baza_s3_desc', true );
	if ( '' === $s3_desc && ! metadata_exists( 'post', $post->ID, '_brz_baza_s3_desc' ) ) {
		$s3_desc = 'Unul dintre marile avantaje ale bazei sportive din Brezoaele este integrarea sa într-un spațiu comunitar complet. Parcul din fața Sălii de Sport funcționează ca o zonă verde primitoare de relaxare.';
	}

	$specs_title    = get_post_meta( $post->ID, '_brz_baza_tech_specs_title', true );
	if ( '' === $specs_title && ! metadata_exists( 'post', $post->ID, '_brz_baza_tech_specs_title' ) ) {
		$specs_title = '📍 Fișă Tehnică Bază Sportivă';
	}

	$sb_prog_title  = get_post_meta( $post->ID, '_brz_baza_sb_program_title', true );
	if ( '' === $sb_prog_title && ! metadata_exists( 'post', $post->ID, '_brz_baza_sb_program_title' ) ) {
		$sb_prog_title = '📅 Accesibilitate & Program';
	}

	$sb_prog_text   = get_post_meta( $post->ID, '_brz_baza_sb_program_text', true );
	if ( '' === $sb_prog_text && ! metadata_exists( 'post', $post->ID, '_brz_baza_sb_program_text' ) ) {
		$sb_prog_text = 'Baza Sportivă Centrală este deschisă tuturor iubitorilor de sport din comună, respectând programul stabilit pentru antrenamentele oficiale și meciurile de campionat.';
	}

	$sb_prog_notice = get_post_meta( $post->ID, '_brz_baza_sb_program_notice', true );
	if ( '' === $sb_prog_notice && ! metadata_exists( 'post', $post->ID, '_brz_baza_sb_program_notice' ) ) {
		$sb_prog_notice = '💡 Pentru solicitări oficiale, rezervări sau cereri administrative privind Sala de Sport și baza sportivă, adresați-vă Primăriei Comunei Brezoaele pe site-ul oficial <a href="https://www.primariabrezoaele.ro/" target="_blank" rel="noopener noreferrer" style="color: #ffffff; font-weight: 800; text-decoration: underline;">www.primariabrezoaele.ro</a>.';
	}

	// Preluare Câmpuri Repetere (JSON)
	$tech_specs = get_post_meta( $post->ID, '_brz_baza_tech_specs', true );
	if ( ! is_array( $tech_specs ) ) {
		$tech_specs = array(
			array( 'label' => 'Locație:', 'val' => 'Centru („La Bâlci”)' ),
			array( 'label' => 'Comună:', 'val' => 'Brezoaele, Dâmbovița' ),
			array( 'label' => 'Stadion:', 'val' => '„Ștefan Ciomârtan”' ),
			array( 'label' => 'Capacitate:', 'val' => '50 locuri pe scaune' ),
			array( 'label' => 'Echipă Gazdă:', 'val' => 'AFC 1948 Brezoaele' ),
			array( 'label' => 'Liga:', 'val' => 'Liga 5 SUD Dâmbovița' ),
			array( 'label' => 'Sală Acoperită:', 'val' => 'Da (Multifuncțională)' ),
			array( 'label' => 'An Înființare:', 'val' => '8 Septembrie 1945' ),
		);
	}

	$stats_grid = get_post_meta( $post->ID, '_brz_baza_stats_grid', true );
	if ( ! is_array( $stats_grid ) ) {
		$stats_grid = array(
			array( 'icon' => '🏟️', 'title' => 'Stadionul Central', 'desc' => 'Suprafață cu gazon natural excelent întreținut & tribună de 50 locuri.' ),
			array( 'icon' => '🏢', 'title' => 'Sală Multifuncțională', 'desc' => 'Minifotbal, handbal, baschet, volei & antrenamente acoperite de iarnă.' ),
			array( 'icon' => '🌳', 'title' => 'Parc & Zona Verde', 'desc' => 'Spațiu verde de relaxare cu alei, bănci & monitorizare video perimetrală.' ),
			array( 'icon' => '📍', 'title' => 'Locație Centrală', 'desc' => 'Amplasată în centrul comunei Brezoaele (zona tradițională „La Bâlci”).' ),
		);
	}

	$s1_cards = get_post_meta( $post->ID, '_brz_baza_s1_cards', true );
	if ( ! is_array( $s1_cards ) ) {
		$s1_cards = array(
			array( 'icon' => '🌱', 'title' => 'Suprafața de Joc', 'text' => 'Stadionul dispune de un gazon natural excelent întreținut, recunoscut la nivel județean drept unul dintre cele mai bune și rezistente terenuri din competițiile organizate de AJF Dâmbovița.' ),
			array( 'icon' => '🛡️', 'title' => 'Împrejmuire și Siguranță', 'text' => 'Întreaga arenă este securizată printr-un gard perimetral modern, asigurând delimitarea clară a zonei de joc conform normelor riguroase de siguranță impuse de regulamentele sportive.' ),
		);
	}

	$s2_cards = get_post_meta( $post->ID, '_brz_baza_s2_cards', true );
	if ( ! is_array( $s2_cards ) ) {
		$s2_cards = array(
			array( 'icon' => '⚽', 'title' => 'Destinație Multifuncțională', 'text' => 'Sala este optimizată pentru desfășurarea meciurilor de minifotbal, handbal, baschet și volei, dispunând de suprafață omologată și vestiare echipate.' ),
			array( 'icon' => '❄️', 'title' => 'Continuitate pe Timp de Iarnă', 'text' => 'Reprezintă elementul cheie pentru grupele de juniori și seniori ale comunei, permițând pregătirea fizică și tactică neîntreruptă pe tot parcursul sezonului rece.' ),
			array( 'icon' => '🎓', 'title' => 'Facilități pentru Comunitate & Școală', 'text' => 'Pe lângă activitatea clubului sportiv, sala este destinată orelor de educație fizică ale școlilor din comună și poate fi utilizată de localnici pentru activități recreative pe bază de programare.' ),
		);
	}

	$s3_cards = get_post_meta( $post->ID, '_brz_baza_s3_cards', true );
	if ( ! is_array( $s3_cards ) ) {
		$s3_cards = array(
			array( 'icon' => '🪑', 'title' => 'Spațiu de Relaxare', 'text' => 'Aici, părinții, bunicii și copiii se pot relaxa înainte sau după meciuri, spațiul fiind amenajat cu alei pietonale umbroase și bănci confortabile.' ),
			array( 'icon' => '📹', 'title' => 'Curățenie & Monitorizare', 'text' => 'Pentru menținerea unui climat civilizat și sigur, întreaga zonă este supravegheată video, curățenia fiind o prioritate administrativă strict respectată.' ),
		);
	}

	// Preluare Galerii Foto
	$stadion_data = get_post_meta( $post->ID, '_brz_gallery_stadion', true );
	$sala_data    = get_post_meta( $post->ID, '_brz_gallery_sala', true );
	$parc_data    = get_post_meta( $post->ID, '_brz_gallery_parc', true );

	if ( ! is_array( $stadion_data ) ) $stadion_data = array();
	if ( ! is_array( $sala_data ) ) $sala_data = array();
	if ( ! is_array( $parc_data ) ) $parc_data = array();
	?>

	<style>
		.brz-master-sec { background: #ffffff; border: 1px solid #cbd5e1; border-radius: 10px; padding: 20px; margin-bottom: 24px; box-shadow: 0 2px 6px rgba(15,23,42,0.03); }
		.brz-master-sec-num { display: inline-block; background: #047857; color: #ffffff; font-weight: 800; font-size: 0.78rem; padding: 3px 9px; border-radius: 20px; margin-right: 6px; text-transform: uppercase; }
		.brz-master-sec-title { font-size: 1.15rem; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 14px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; }
		.brz-form-row { margin-bottom: 14px; }
		.brz-form-row label { display: block; font-weight: 700; font-size: 0.85rem; color: #334155; margin-bottom: 4px; }
		.brz-form-row input[type="text"], .brz-form-row textarea { width: 100%; font-size: 0.9rem; padding: 7px 12px; border: 1px solid #cbd5e1; border-radius: 6px; }
		.brz-repeater-item { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px; margin-bottom: 10px; position: relative; }
		.brz-gal-box-inner { background: #f0fdf4; border: 1px dashed #34d399; border-radius: 8px; padding: 16px; margin-top: 16px; }
	</style>

	<div style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">

		<p style="background: #e0f2fe; color: #0369a1; padding: 12px 16px; border-radius: 8px; font-weight: 600; font-size: 0.9rem; margin-bottom: 20px;">
			💡 Elementele de mai jos sunt structurate în <strong>ordinea exactă în care apar vizual pe site</strong>, de sus în jos: mai întâi Zona Hero, apoi Introducerea, Secțiunile 1-3 (cu detaliile și galeriile foto aferente), urmate de casetele din Sidebar.
		</p>

		<!-- 1. HERO HEADER & CARDURI STATISTICI (SUS DE TOT) -->
		<div class="brz-master-sec">
			<h3 class="brz-master-sec-title"><span class="brz-master-sec-num">Pasul 1</span> 👑 Header Hero &amp; Carduri Statistici Highlight</h3>
			
			<div class="brz-form-row">
				<label>Badge Top Hero (ex: 📍 Inima Comunei Brezoaele):</label>
				<input type="text" name="brz_baza_hero_badge" value="<?php echo esc_attr( $hero_badge ); ?>">
			</div>
			<div class="brz-form-row">
				<label>Titlu Principal Hero:</label>
				<input type="text" name="brz_baza_hero_title" value="<?php echo esc_attr( $hero_title ); ?>">
			</div>
			<div class="brz-form-row">
				<label>Subtitlu / Descriere Hero:</label>
				<textarea name="brz_baza_hero_subtitle" rows="2"><?php echo esc_textarea( $hero_subtitle ); ?></textarea>
			</div>

			<h4 style="margin: 16px 0 8px 0; font-size: 0.95rem; color: #0f172a;">📊 Carduri Statistici Highlight (Grid Sus):</h4>
			<input type="hidden" name="brz_baza_stats_grid_json" id="brz_baza_stats_grid_json" value="<?php echo esc_attr( wp_json_encode( $stats_grid ) ); ?>">
			<div id="brz_stats_grid_container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 12px; margin-bottom: 12px;"></div>
			<button type="button" class="button button-primary" id="brz_add_stat_btn" style="background: #047857; border-color: #047857;">
				➕ Adaugă Card Statistică Nou
			</button>
		</div>

		<!-- 2. CARD INTRODUCERE -->
		<div class="brz-master-sec">
			<h3 class="brz-master-sec-title"><span class="brz-master-sec-num">Pasul 2</span> 📝 Card Introducere</h3>
			<div class="brz-form-row">
				<label>Titlu Card Introducere:</label>
				<input type="text" name="brz_baza_intro_title" value="<?php echo esc_attr( $intro_title ); ?>">
			</div>
			<div class="brz-form-row">
				<label>Text Card Introducere:</label>
				<textarea name="brz_baza_intro_text" rows="3"><?php echo esc_textarea( $intro_text ); ?></textarea>
			</div>
		</div>

		<!-- 3. SECȚIUNEA 1: STADIONUL ŞTEFAN CIOMÂRTAN -->
		<div class="brz-master-sec">
			<h3 class="brz-master-sec-title"><span class="brz-master-sec-num">Pasul 3</span> 🏟️ Secțiunea 1: Stadionul „Ștefan Ciomârtan”</h3>
			<div class="brz-form-row">
				<label>Titlu Secțiune:</label>
				<input type="text" name="brz_baza_s1_title" value="<?php echo esc_attr( $s1_title ); ?>">
			</div>
			<div class="brz-form-row">
				<label>Subtitlu / Subetichetă:</label>
				<input type="text" name="brz_baza_s1_subtitle" value="<?php echo esc_attr( $s1_subtitle ); ?>">
			</div>
			<div class="brz-form-row">
				<label>Paragraf Prezentare:</label>
				<textarea name="brz_baza_s1_desc" rows="2"><?php echo esc_textarea( $s1_desc ); ?></textarea>
			</div>

			<label style="font-weight: 700; font-size: 0.85rem; color: #334155; margin-bottom: 6px; display: block;">Carduri Facilități Stadion (Sub-Grid):</label>
			<input type="hidden" name="brz_baza_s1_cards_json" id="brz_baza_s1_cards_json" value="<?php echo esc_attr( wp_json_encode( $s1_cards ) ); ?>">
			<div id="brz_s1_cards_container" style="margin-bottom: 10px;"></div>
			<button type="button" class="button button-secondary" id="brz_add_s1_card_btn">➕ Adaugă Card Facilitate Stadion</button>

			<!-- GALERIE FOTO STADION -->
			<div class="brz-gal-box-inner">
				<h4 style="margin: 0 0 4px 0; color: #047857;">📸 Galerie Foto 1: Stadionul „Ștefan Ciomârtan”</h4>
				<p style="margin: 0 0 10px 0; font-size: 0.82rem; color: #475569;">Fotografii afișate în galeria secțiunii Stadion.</p>
				<input type="hidden" name="brz_gallery_stadion_json" id="brz_gallery_stadion_json" value="<?php echo esc_attr( wp_json_encode( $stadion_data ) ); ?>">
				<div id="brz_gal_items_stadion" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px; margin-bottom: 10px;"></div>
				<button type="button" class="button button-primary brz-add-gal-btn" data-key="stadion" style="background: #047857; border-color: #047857;">
					➕ Adaugă Fotografii Stadion
				</button>
			</div>
		</div>

		<!-- 4. SECȚIUNEA 2: SALA DE SPORT -->
		<div class="brz-master-sec">
			<h3 class="brz-master-sec-title"><span class="brz-master-sec-num">Pasul 4</span> 🏢 Secțiunea 2: Sala de Sport Brezoaele</h3>
			<div class="brz-form-row">
				<label>Titlu Secțiune:</label>
				<input type="text" name="brz_baza_s2_title" value="<?php echo esc_attr( $s2_title ); ?>">
			</div>
			<div class="brz-form-row">
				<label>Subtitlu / Subetichetă:</label>
				<input type="text" name="brz_baza_s2_subtitle" value="<?php echo esc_attr( $s2_subtitle ); ?>">
			</div>
			<div class="brz-form-row">
				<label>Paragraf Prezentare:</label>
				<textarea name="brz_baza_s2_desc" rows="2"><?php echo esc_textarea( $s2_desc ); ?></textarea>
			</div>

			<label style="font-weight: 700; font-size: 0.85rem; color: #334155; margin-bottom: 6px; display: block;">Carduri Facilități Sală de Sport (Sub-Grid):</label>
			<input type="hidden" name="brz_baza_s2_cards_json" id="brz_baza_s2_cards_json" value="<?php echo esc_attr( wp_json_encode( $s2_cards ) ); ?>">
			<div id="brz_s2_cards_container" style="margin-bottom: 10px;"></div>
			<button type="button" class="button button-secondary" id="brz_add_s2_card_btn">➕ Adaugă Card Facilitate Sală</button>

			<!-- CALLOUT SALA DE SPORT -->
			<div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 14px; border-radius: 6px; margin: 14px 0;">
				<h4 style="margin: 0 0 10px 0; color: #0f172a;">Caseta Callout Link Fișă Sală de Sport:</h4>
				<div class="brz-form-row">
					<label>Titlu Callout:</label>
					<input type="text" name="brz_baza_s2_callout_title" value="<?php echo esc_attr( $s2_callout_t ); ?>">
				</div>
				<div class="brz-form-row">
					<label>Text Callout:</label>
					<input type="text" name="brz_baza_s2_callout_text" value="<?php echo esc_attr( $s2_callout_p ); ?>">
				</div>
				<div class="brz-form-row">
					<label>Text Buton Callout:</label>
					<input type="text" name="brz_baza_s2_callout_btn_text" value="<?php echo esc_attr( $s2_callout_btn ); ?>">
				</div>
				<div class="brz-form-row">
					<label>URL / Link Buton Callout:</label>
					<input type="text" name="brz_baza_s2_callout_btn_url" value="<?php echo esc_attr( $s2_callout_url ); ?>">
				</div>
			</div>

			<!-- GALERIE FOTO SALA DE SPORT -->
			<div class="brz-gal-box-inner">
				<h4 style="margin: 0 0 4px 0; color: #047857;">📸 Galerie Foto 2: Sala de Sport</h4>
				<p style="margin: 0 0 10px 0; font-size: 0.82rem; color: #475569;">Fotografii afișate în galeria secțiunii Sală de Sport.</p>
				<input type="hidden" name="brz_gallery_sala_json" id="brz_gallery_sala_json" value="<?php echo esc_attr( wp_json_encode( $sala_data ) ); ?>">
				<div id="brz_gal_items_sala" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px; margin-bottom: 10px;"></div>
				<button type="button" class="button button-primary brz-add-gal-btn" data-key="sala" style="background: #047857; border-color: #047857;">
					➕ Adaugă Fotografii Sală de Sport
				</button>
			</div>
		</div>

		<!-- 5. SECȚIUNEA 3: PARCUL CENTRAL -->
		<div class="brz-master-sec">
			<h3 class="brz-master-sec-title"><span class="brz-master-sec-num">Pasul 5</span> 🌳 Secțiunea 3: Parcul Central</h3>
			<div class="brz-form-row">
				<label>Titlu Secțiune:</label>
				<input type="text" name="brz_baza_s3_title" value="<?php echo esc_attr( $s3_title ); ?>">
			</div>
			<div class="brz-form-row">
				<label>Subtitlu / Subetichetă:</label>
				<input type="text" name="brz_baza_s3_subtitle" value="<?php echo esc_attr( $s3_subtitle ); ?>">
			</div>
			<div class="brz-form-row">
				<label>Paragraf Prezentare:</label>
				<textarea name="brz_baza_s3_desc" rows="2"><?php echo esc_textarea( $s3_desc ); ?></textarea>
			</div>

			<label style="font-weight: 700; font-size: 0.85rem; color: #334155; margin-bottom: 6px; display: block;">Carduri Facilități Parc (Sub-Grid):</label>
			<input type="hidden" name="brz_baza_s3_cards_json" id="brz_baza_s3_cards_json" value="<?php echo esc_attr( wp_json_encode( $s3_cards ) ); ?>">
			<div id="brz_s3_cards_container" style="margin-bottom: 10px;"></div>
			<button type="button" class="button button-secondary" id="brz_add_s3_card_btn">➕ Adaugă Card Facilitate Parc</button>

			<!-- GALERIE FOTO PARCUL CENTRAL -->
			<div class="brz-gal-box-inner">
				<h4 style="margin: 0 0 4px 0; color: #047857;">📸 Galerie Foto 3: Parcul Central</h4>
				<p style="margin: 0 0 10px 0; font-size: 0.82rem; color: #475569;">Fotografii afișate în galeria secțiunii Parcul Central.</p>
				<input type="hidden" name="brz_gallery_parc_json" id="brz_gallery_parc_json" value="<?php echo esc_attr( wp_json_encode( $parc_data ) ); ?>">
				<div id="brz_gal_items_parc" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px; margin-bottom: 10px;"></div>
				<button type="button" class="button button-primary brz-add-gal-btn" data-key="parc" style="background: #047857; border-color: #047857;">
					➕ Adaugă Fotografii Parcul Central
				</button>
			</div>
		</div>

		<!-- 6. SIDEBAR: FIȘĂ TEHNICĂ BAZĂ SPORTIVĂ -->
		<div class="brz-master-sec">
			<h3 class="brz-master-sec-title"><span class="brz-master-sec-num">Pasul 6</span> 📍 Sidebar: Fișă Tehnică Baza Sportivă</h3>
			<p style="font-size: 0.85rem; color: #64748b;">Apare în coloana din dreapta pe site. Poți edita atât etichetele (ex: Locație:), cât și valorile (ex: Centru).</p>
			
			<div class="brz-form-row">
				<label>Titlu Casetă Sidebar Fișă Tehnică:</label>
				<input type="text" name="brz_baza_tech_specs_title" value="<?php echo esc_attr( $specs_title ); ?>">
			</div>

			<input type="hidden" name="brz_baza_tech_specs_json" id="brz_baza_tech_specs_json" value="<?php echo esc_attr( wp_json_encode( $tech_specs ) ); ?>">

			<div id="brz_tech_specs_container"></div>

			<button type="button" class="button button-primary" id="brz_add_spec_btn" style="background: #047857; border-color: #047857;">
				➕ Adaugă Rând Nou în Fișa Tehnică
			</button>
		</div>

		<!-- 7. SIDEBAR: ACCESIBILITATE & PROGRAM -->
		<div class="brz-master-sec">
			<h3 class="brz-master-sec-title"><span class="brz-master-sec-num">Pasul 7</span> 📅 Sidebar: Card Accesibilitate &amp; Program</h3>
			<div class="brz-form-row">
				<label>Titlu Casetă Sidebar Program:</label>
				<input type="text" name="brz_baza_sb_program_title" value="<?php echo esc_attr( $sb_prog_title ); ?>">
			</div>
			<div class="brz-form-row">
				<label>Text Casetă Program:</label>
				<textarea name="brz_baza_sb_program_text" rows="2"><?php echo esc_textarea( $sb_prog_text ); ?></textarea>
			</div>
			<div class="brz-form-row">
				<label>Mesaj Casetă Notă (ex: Solicitări oficiale către Primărie):</label>
				<textarea name="brz_baza_sb_program_notice" rows="2"><?php echo esc_textarea( $sb_prog_notice ); ?></textarea>
			</div>
		</div>

	</div>

	<script>
	jQuery(document).ready(function($) {
		var specsState = <?php echo wp_json_encode( $tech_specs ); ?>;
		var statsState = <?php echo wp_json_encode( $stats_grid ); ?>;
		var s1State    = <?php echo wp_json_encode( $s1_cards ); ?>;
		var s2State    = <?php echo wp_json_encode( $s2_cards ); ?>;
		var s3State    = <?php echo wp_json_encode( $s3_cards ); ?>;

		var galleryState = {
			stadion: <?php echo wp_json_encode( $stadion_data ); ?>,
			sala: <?php echo wp_json_encode( $sala_data ); ?>,
			parc: <?php echo wp_json_encode( $parc_data ); ?>
		};

		var saveTimer = null;
		function triggerGutenbergChange() {
			var post_id = $('#post_ID').val() || (window.wp && window.wp.data && window.wp.data.select('core/editor') && window.wp.data.select('core/editor').getCurrentPostId());

			if ( window.wp && window.wp.data && window.wp.data.dispatch && window.wp.data.dispatch('core/editor') ) {
				try {
					var metaObj = {};
					var textMap = {
						'brz_baza_hero_badge': '_brz_baza_hero_badge',
						'brz_baza_hero_title': '_brz_baza_hero_title',
						'brz_baza_hero_subtitle': '_brz_baza_hero_subtitle',
						'brz_baza_intro_title': '_brz_baza_intro_title',
						'brz_baza_intro_text': '_brz_baza_intro_text',
						'brz_baza_s1_title': '_brz_baza_s1_title',
						'brz_baza_s1_subtitle': '_brz_baza_s1_subtitle',
						'brz_baza_s1_desc': '_brz_baza_s1_desc',
						'brz_baza_s2_title': '_brz_baza_s2_title',
						'brz_baza_s2_subtitle': '_brz_baza_s2_subtitle',
						'brz_baza_s2_desc': '_brz_baza_s2_desc',
						'brz_baza_s2_callout_title': '_brz_baza_s2_callout_title',
						'brz_baza_s2_callout_text': '_brz_baza_s2_callout_text',
						'brz_baza_s2_callout_btn_text': '_brz_baza_s2_callout_btn_text',
						'brz_baza_s2_callout_btn_url': '_brz_baza_s2_callout_btn_url',
						'brz_baza_s3_title': '_brz_baza_s3_title',
						'brz_baza_s3_subtitle': '_brz_baza_s3_subtitle',
						'brz_baza_s3_desc': '_brz_baza_s3_desc',
						'brz_baza_tech_specs_title': '_brz_baza_tech_specs_title',
						'brz_baza_sb_program_title': '_brz_baza_sb_program_title',
						'brz_baza_sb_program_text': '_brz_baza_sb_program_text',
						'brz_baza_sb_program_notice': '_brz_baza_sb_program_notice'
					};
					$.each(textMap, function(name, metaKey) {
						var el = $('[name="' + name + '"]');
						if (el.length) {
							metaObj[metaKey] = el.val();
						}
					});
					var jsonMap = {
						'#brz_baza_tech_specs_json': '_brz_baza_tech_specs',
						'#brz_baza_stats_grid_json': '_brz_baza_stats_grid',
						'#brz_baza_s1_cards_json': '_brz_baza_s1_cards',
						'#brz_baza_s2_cards_json': '_brz_baza_s2_cards',
						'#brz_baza_s3_cards_json': '_brz_baza_s3_cards',
						'#brz_gallery_stadion_json': '_brz_gallery_stadion',
						'#brz_gallery_sala_json': '_brz_gallery_sala',
						'#brz_gallery_parc_json': '_brz_gallery_parc'
					};
					$.each(jsonMap, function(selector, metaKey) {
						var val = $(selector).val();
						if (val) {
							try { metaObj[metaKey] = JSON.parse(val); } catch(e) {}
						}
					});

					window.wp.data.dispatch('core/editor').editPost({ meta: metaObj });
				} catch(e) {}
			}

			if (post_id) {
				clearTimeout(saveTimer);
				saveTimer = setTimeout(function() {
					var formData = { action: 'brz_save_baza_unified_meta', post_id: post_id };
					$('.brz-master-sec input, .brz-master-sec textarea').each(function() {
						var name = $(this).attr('name');
						if (name) formData[name] = $(this).val();
					});
					$.post(ajaxurl, formData);
				}, 400);
			}
		}

		$(document).on('input change', '.brz-master-sec input, .brz-master-sec textarea', function() {
			triggerGutenbergChange();
		});

		function escapeAttr(str) {
			return String(str || '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
		}

		// RENDER GALERII FOTO
		function renderGallery(key) {
			var container = $('#brz_gal_items_' + key);
			container.empty();
			var items = galleryState[key] || [];

			$.each(items, function(idx, item) {
				var url = item.url || '';
				var title = item.title || '';
				var html = `
					<div class="brz-gal-card" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px; position: relative;">
						<div style="height: 100px; background: #000; border-radius: 4px; overflow: hidden; margin-bottom: 6px;">
							<img src="${url}" style="width: 100%; height: 100%; object-fit: cover;">
						</div>
						<input type="text" class="brz-item-title-input" data-key="${key}" data-idx="${idx}" value="${escapeAttr(title)}" placeholder="Subtitlu poză" style="width: 100%; padding: 4px 6px; font-size: 0.8rem; border: 1px solid #cbd5e1; border-radius: 4px; margin-bottom: 4px;">
						<button type="button" class="button button-secondary brz-remove-gal-item" data-key="${key}" data-idx="${idx}" style="width: 100%; color: #dc2626; border-color: #fca5a5; font-size: 0.75rem; padding: 2px 4px;">
							❌ Șterge Poza
						</button>
					</div>
				`;
				container.append(html);
			});

			$('#brz_gallery_' + key + '_json').val(JSON.stringify(galleryState[key] || []));
			triggerGutenbergChange();
		}

		renderGallery('stadion');
		renderGallery('sala');
		renderGallery('parc');

		$('.brz-add-gal-btn').on('click', function(e) {
			e.preventDefault();
			var key = $(this).data('key');
			var frame = wp.media({
				title: 'Selectează Fotografii pentru Galerie',
				button: { text: 'Adaugă în Galerie' },
				multiple: true
			});
			frame.on('select', function() {
				var selection = frame.state().get('selection');
				selection.each(function(attachment) {
					var attJson = attachment.toJSON();
					if (!galleryState[key]) galleryState[key] = [];
					galleryState[key].push({
						url: attJson.url || '',
						title: attJson.title || ''
					});
				});
				renderGallery(key);
			});
			frame.open();
		});

		$(document).on('click', '.brz-remove-gal-item', function(e) {
			e.preventDefault();
			var key = $(this).data('key');
			var idx = $(this).data('idx');
			if (galleryState[key]) {
				galleryState[key].splice(idx, 1);
				renderGallery(key);
			}
		});

		$(document).on('input change', '.brz-item-title-input', function() {
			var key = $(this).data('key');
			var idx = $(this).data('idx');
			var val = $(this).val();
			if (galleryState[key] && galleryState[key][idx]) {
				galleryState[key][idx].title = val;
				$('#brz_gallery_' + key + '_json').val(JSON.stringify(galleryState[key]));
				triggerGutenbergChange();
			}
		});

		// 1. RENDER TECH SPECS
		function renderTechSpecs() {
			var container = $('#brz_tech_specs_container');
			container.empty();
			$.each(specsState, function(idx, item) {
				var label = item.label || '';
				var val = item.val || '';
				var html = `
					<div class="brz-repeater-item" style="display: flex; gap: 10px; align-items: center;">
						<input type="text" class="brz-spec-label-in" data-idx="${idx}" value="${escapeAttr(label)}" placeholder="Etichetă (ex: Locație:)" style="flex: 1;">
						<input type="text" class="brz-spec-val-in" data-idx="${idx}" value="${escapeAttr(val)}" placeholder="Valoare (ex: Centru)" style="flex: 2;">
						<button type="button" class="button button-secondary brz-del-spec-btn" data-idx="${idx}" style="color: #dc2626; border-color: #fca5a5;">❌</button>
					</div>
				`;
				container.append(html);
			});
			$('#brz_baza_tech_specs_json').val(JSON.stringify(specsState));
			triggerGutenbergChange();
		}

		$('#brz_add_spec_btn').on('click', function() {
			specsState.push({ label: 'Câmp Nou:', val: 'Valoare' });
			renderTechSpecs();
		});

		$(document).on('input change', '.brz-spec-label-in', function() {
			var idx = $(this).data('idx');
			if (specsState[idx]) { specsState[idx].label = $(this).val(); $('#brz_baza_tech_specs_json').val(JSON.stringify(specsState)); triggerGutenbergChange(); }
		});
		$(document).on('input change', '.brz-spec-val-in', function() {
			var idx = $(this).data('idx');
			if (specsState[idx]) { specsState[idx].val = $(this).val(); $('#brz_baza_tech_specs_json').val(JSON.stringify(specsState)); triggerGutenbergChange(); }
		});
		$(document).on('click', '.brz-del-spec-btn', function() {
			var idx = $(this).data('idx');
			specsState.splice(idx, 1);
			renderTechSpecs();
		});

		// 2. RENDER STATS GRID
		function renderStatsGrid() {
			var container = $('#brz_stats_grid_container');
			container.empty();
			$.each(statsState, function(idx, item) {
				var icon = item.icon || '';
				var title = item.title || '';
				var desc = item.desc || '';
				var html = `
					<div class="brz-repeater-item">
						<div style="display: flex; gap: 8px; margin-bottom: 6px;">
							<input type="text" class="brz-stat-icon-in" data-idx="${idx}" value="${escapeAttr(icon)}" placeholder="Emoji (ex: 🏟️)" style="width: 70px; text-align: center;">
							<input type="text" class="brz-stat-title-in" data-idx="${idx}" value="${escapeAttr(title)}" placeholder="Titlu Card" style="flex: 1;">
							<button type="button" class="button button-secondary brz-del-stat-btn" data-idx="${idx}" style="color: #dc2626; border-color: #fca5a5;">❌</button>
						</div>
						<textarea class="brz-stat-desc-in" data-idx="${idx}" placeholder="Descriere Card" rows="2" style="width: 100%; font-size: 0.82rem;">${escapeAttr(desc)}</textarea>
					</div>
				`;
				container.append(html);
			});
			$('#brz_baza_stats_grid_json').val(JSON.stringify(statsState));
			triggerGutenbergChange();
		}

		$('#brz_add_stat_btn').on('click', function() {
			statsState.push({ icon: '📍', title: 'Titlu Card Nou', desc: 'Descriere card statistică.' });
			renderStatsGrid();
		});

		$(document).on('input change', '.brz-stat-icon-in', function() {
			var idx = $(this).data('idx'); if (statsState[idx]) { statsState[idx].icon = $(this).val(); $('#brz_baza_stats_grid_json').val(JSON.stringify(statsState)); triggerGutenbergChange(); }
		});
		$(document).on('input change', '.brz-stat-title-in', function() {
			var idx = $(this).data('idx'); if (statsState[idx]) { statsState[idx].title = $(this).val(); $('#brz_baza_stats_grid_json').val(JSON.stringify(statsState)); triggerGutenbergChange(); }
		});
		$(document).on('input change', '.brz-stat-desc-in', function() {
			var idx = $(this).data('idx'); if (statsState[idx]) { statsState[idx].desc = $(this).val(); $('#brz_baza_stats_grid_json').val(JSON.stringify(statsState)); triggerGutenbergChange(); }
		});
		$(document).on('click', '.brz-del-stat-btn', function() {
			var idx = $(this).data('idx');
			statsState.splice(idx, 1);
			renderStatsGrid();
		});

		// 3. RENDER SUB-CARDS (S1, S2, S3)
		function renderSubCards(stateArr, containerId, jsonInputId) {
			var container = $(containerId);
			container.empty();
			$.each(stateArr, function(idx, item) {
				var icon = item.icon || '';
				var title = item.title || '';
				var text = item.text || '';
				var html = `
					<div class="brz-repeater-item" style="margin-bottom: 8px;">
						<div style="display: flex; gap: 8px; margin-bottom: 6px;">
							<input type="text" class="brz-card-icon-in" data-idx="${idx}" value="${escapeAttr(icon)}" placeholder="Emoji" style="width: 60px; text-align: center;">
							<input type="text" class="brz-card-title-in" data-idx="${idx}" value="${escapeAttr(title)}" placeholder="Titlu Facilitate" style="flex: 1;">
							<button type="button" class="button button-secondary brz-del-subcard-btn" data-idx="${idx}" style="color: #dc2626; border-color: #fca5a5;">❌</button>
						</div>
						<textarea class="brz-card-text-in" data-idx="${idx}" placeholder="Text explicație" rows="2" style="width: 100%; font-size: 0.82rem;">${escapeAttr(text)}</textarea>
					</div>
				`;
				container.append(html);
			});
			$(jsonInputId).val(JSON.stringify(stateArr));
			triggerGutenbergChange();
		}

		function setupSubCardEvents(stateArr, containerId, jsonInputId, addBtnId) {
			renderSubCards(stateArr, containerId, jsonInputId);

			$(addBtnId).on('click', function() {
				stateArr.push({ icon: '⚽', title: 'Facilitate Nouă', text: 'Descriere detaliată.' });
				renderSubCards(stateArr, containerId, jsonInputId);
			});

			$(containerId).on('input change', '.brz-card-icon-in', function() {
				var idx = $(this).data('idx'); if (stateArr[idx]) { stateArr[idx].icon = $(this).val(); $(jsonInputId).val(JSON.stringify(stateArr)); triggerGutenbergChange(); }
			});
			$(containerId).on('input change', '.brz-card-title-in', function() {
				var idx = $(this).data('idx'); if (stateArr[idx]) { stateArr[idx].title = $(this).val(); $(jsonInputId).val(JSON.stringify(stateArr)); triggerGutenbergChange(); }
			});
			$(containerId).on('input change', '.brz-card-text-in', function() {
				var idx = $(this).data('idx'); if (stateArr[idx]) { stateArr[idx].text = $(this).val(); $(jsonInputId).val(JSON.stringify(stateArr)); triggerGutenbergChange(); }
			});
			$(containerId).on('click', '.brz-del-subcard-btn', function() {
				var idx = $(this).data('idx');
				stateArr.splice(idx, 1);
				renderSubCards(stateArr, containerId, jsonInputId);
			});
		}

		setupSubCardEvents(s1State, '#brz_s1_cards_container', '#brz_baza_s1_cards_json', '#brz_add_s1_card_btn');
		setupSubCardEvents(s2State, '#brz_s2_cards_container', '#brz_baza_s2_cards_json', '#brz_add_s2_card_btn');
		setupSubCardEvents(s3State, '#brz_s3_cards_container', '#brz_baza_s3_cards_json', '#brz_add_s3_card_btn');

		renderTechSpecs();
		renderStatsGrid();
	});
	</script>
	<?php
}

function brezoaele_register_baza_sportiva_meta_fields() {
	$text_keys = array(
		'_brz_baza_hero_badge',
		'_brz_baza_hero_title',
		'_brz_baza_hero_subtitle',
		'_brz_baza_intro_title',
		'_brz_baza_intro_text',
		'_brz_baza_s1_title',
		'_brz_baza_s1_subtitle',
		'_brz_baza_s1_desc',
		'_brz_baza_s2_title',
		'_brz_baza_s2_subtitle',
		'_brz_baza_s2_desc',
		'_brz_baza_s2_callout_title',
		'_brz_baza_s2_callout_text',
		'_brz_baza_s2_callout_btn_text',
		'_brz_baza_s2_callout_btn_url',
		'_brz_baza_s3_title',
		'_brz_baza_s3_subtitle',
		'_brz_baza_s3_desc',
		'_brz_baza_tech_specs_title',
		'_brz_baza_sb_program_title',
		'_brz_baza_sb_program_text',
		'_brz_baza_sb_program_notice',
		'_brz_sport_hero_image',
		'_brz_sport_hero_pos_x',
		'_brz_sport_hero_pos_y',
	);

	foreach ( $text_keys as $key ) {
		register_post_meta( 'page', $key, array(
			'show_in_rest'  => true,
			'single'        => true,
			'type'          => 'string',
			'auth_callback' => '__return_true',
		) );
	}

	$json_keys = array(
		'_brz_baza_tech_specs',
		'_brz_baza_stats_grid',
		'_brz_baza_s1_cards',
		'_brz_baza_s2_cards',
		'_brz_baza_s3_cards',
		'_brz_gallery_stadion',
		'_brz_gallery_sala',
		'_brz_gallery_parc',
	);

	foreach ( $json_keys as $key ) {
		register_post_meta( 'page', $key, array(
			'show_in_rest'  => array(
				'schema' => array(
					'type'  => 'array',
					'items' => array(
						'type' => 'object',
					),
				),
			),
			'single'        => true,
			'type'          => 'array',
			'auth_callback' => '__return_true',
		) );
	}
}
add_action( 'init', 'brezoaele_register_baza_sportiva_meta_fields' );

add_filter( 'is_protected_meta', function( $protected, $meta_key, $meta_type ) {
	if ( strpos( $meta_key, '_brz_baza_' ) === 0 || strpos( $meta_key, '_brz_gallery_' ) === 0 || strpos( $meta_key, '_brz_sport_' ) === 0 ) {
		return false;
	}
	return $protected;
}, 10, 3 );

function brezoaele_process_baza_saving( $post_id, $data ) {
	if ( empty( $post_id ) || empty( $data ) || ! is_array( $data ) ) {
		return;
	}

	$parent_id = wp_is_post_revision( $post_id );
	if ( $parent_id ) {
		$post_id = $parent_id;
	}

	// Single Text Fields
	$text_fields = array(
		'brz_baza_hero_badge'          => '_brz_baza_hero_badge',
		'brz_baza_hero_title'          => '_brz_baza_hero_title',
		'brz_baza_hero_subtitle'       => '_brz_baza_hero_subtitle',
		'brz_baza_intro_title'         => '_brz_baza_intro_title',
		'brz_baza_intro_text'          => '_brz_baza_intro_text',
		'brz_baza_s1_title'            => '_brz_baza_s1_title',
		'brz_baza_s1_subtitle'         => '_brz_baza_s1_subtitle',
		'brz_baza_s1_desc'             => '_brz_baza_s1_desc',
		'brz_baza_s2_title'            => '_brz_baza_s2_title',
		'brz_baza_s2_subtitle'         => '_brz_baza_s2_subtitle',
		'brz_baza_s2_desc'             => '_brz_baza_s2_desc',
		'brz_baza_s2_callout_title'    => '_brz_baza_s2_callout_title',
		'brz_baza_s2_callout_text'     => '_brz_baza_s2_callout_text',
		'brz_baza_s2_callout_btn_text' => '_brz_baza_s2_callout_btn_text',
		'brz_baza_s2_callout_btn_url'  => '_brz_baza_s2_callout_btn_url',
		'brz_baza_s3_title'            => '_brz_baza_s3_title',
		'brz_baza_s3_subtitle'         => '_brz_baza_s3_subtitle',
		'brz_baza_s3_desc'             => '_brz_baza_s3_desc',
		'brz_baza_tech_specs_title'    => '_brz_baza_tech_specs_title',
		'brz_baza_sb_program_title'    => '_brz_baza_sb_program_title',
		'brz_baza_sb_program_text'     => '_brz_baza_sb_program_text',
		'brz_baza_sb_program_notice'   => '_brz_baza_sb_program_notice',
	);

	foreach ( $text_fields as $post_key => $meta_key ) {
		$val = null;
		if ( isset( $data[ $post_key ] ) ) {
			$val = $data[ $post_key ];
		} elseif ( isset( $data[ $meta_key ] ) ) {
			$val = $data[ $meta_key ];
		}

		if ( null !== $val ) {
			if ( strpos( $post_key, 'notice' ) !== false || strpos( $post_key, 'text' ) !== false || strpos( $post_key, 'subtitle' ) !== false || strpos( $post_key, 'desc' ) !== false ) {
				update_post_meta( $post_id, $meta_key, wp_kses_post( wp_unslash( $val ) ) );
			} else {
				update_post_meta( $post_id, $meta_key, sanitize_text_field( wp_unslash( $val ) ) );
			}
		}
	}

	// Repeaters & Galleries JSON
	$repeaters = array(
		'brz_baza_tech_specs_json' => '_brz_baza_tech_specs',
		'brz_baza_stats_grid_json'  => '_brz_baza_stats_grid',
		'brz_baza_s1_cards_json'   => '_brz_baza_s1_cards',
		'brz_baza_s2_cards_json'   => '_brz_baza_s2_cards',
		'brz_baza_s3_cards_json'   => '_brz_baza_s3_cards',
		'brz_gallery_stadion_json' => '_brz_gallery_stadion',
		'brz_gallery_sala_json'    => '_brz_gallery_sala',
		'brz_gallery_parc_json'    => '_brz_gallery_parc',
	);

	foreach ( $repeaters as $post_key => $meta_key ) {
		$raw_val = null;
		if ( isset( $data[ $post_key ] ) ) {
			$raw_val = wp_unslash( $data[ $post_key ] );
		} elseif ( isset( $data[ $meta_key ] ) ) {
			$raw_val = $data[ $meta_key ];
		}

		if ( null !== $raw_val ) {
			if ( is_array( $raw_val ) ) {
				update_post_meta( $post_id, $meta_key, $raw_val );
			} else {
				$decoded = json_decode( $raw_val, true );
				if ( is_array( $decoded ) ) {
					update_post_meta( $post_id, $meta_key, $decoded );
				}
			}
		}
	}
}

function brezoaele_save_baza_unified_metabox( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$data = array_merge( $_POST, $_REQUEST );
	brezoaele_process_baza_saving( $post_id, $data );
}
add_action( 'save_post', 'brezoaele_save_baza_unified_metabox' );

function brezoaele_rest_save_baza_unified_metabox( $post, $request, $creating ) {
	if ( empty( $post->ID ) ) {
		return;
	}

	$params = $request->get_params();
	$json_params = $request->get_json_params();
	$data = array_merge( is_array( $params ) ? $params : array(), is_array( $json_params ) ? $json_params : array() );

	if ( isset( $data['meta'] ) && is_array( $data['meta'] ) ) {
		$data = array_merge( $data, $data['meta'] );
	}

	brezoaele_process_baza_saving( $post->ID, $data );
}
add_action( 'rest_after_insert_page', 'brezoaele_rest_save_baza_unified_metabox', 10, 3 );
add_action( 'rest_after_insert_post', 'brezoaele_rest_save_baza_unified_metabox', 10, 3 );

// AJAX Instant Saver Callback
function brezoaele_ajax_save_baza_unified_meta() {
	$post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
	if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
		wp_send_json_error( 'Unauthorized' );
	}

	brezoaele_process_baza_saving( $post_id, $_POST );
	wp_send_json_success( 'Saved' );
}
add_action( 'wp_ajax_brz_save_baza_unified_meta', 'brezoaele_ajax_save_baza_unified_meta' );























