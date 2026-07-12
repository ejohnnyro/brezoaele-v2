<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header id="masthead" class="site-header">
    <div class="container header-container">
        <div class="site-logo">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
                <?php
                $custom_logo_id = get_theme_mod( 'custom_logo' );
                $logo_url = '';
                if ( $custom_logo_id ) {
                    $logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
                } else {
                    $logo_url = 'https://brezoaele.ro/wp-content/uploads/2021/06/brezoele-logo.png';
                }

                if ( $logo_url ) : ?>
                    <img src="<?php echo esc_url( $logo_url ); ?>" alt="Logo Comuna Brezoaele">
                <?php else : ?>
                    <div class="site-logo-text">
                        <span class="site-logo-comuna">Comuna</span>
                        <span class="site-logo-brezoaele">Brezoaele</span>
                    </div>
                <?php endif; ?>
            </a>
        </div>

        <nav id="site-navigation" class="main-navigation">
            <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false" aria-label="Meniu Navigare">
                <span class="menu-toggle-icon"></span>
            </button>
            <?php
            wp_nav_menu(
                array(
                    'theme_location' => 'menu-1',
                    'menu_id'        => 'primary-menu',
                    'container'      => false,
                    'fallback_cb'    => '__return_false',
                )
            );
            ?>
        </nav>
    </div>
</header>

<!-- Global Content Wrapper (Boxed Layout) -->
<div class="container site-main-container">
