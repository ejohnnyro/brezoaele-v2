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
            <button id="search-toggle-btn" class="search-toggle" aria-label="Caută pe site">
                🔍
            </button>
        </nav>
    </div>
</header>

<!-- Fullscreen Search Overlay -->
<div id="fullscreen-search-overlay" class="search-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); z-index: 9999; justify-content: center; align-items: center; opacity: 0; transition: opacity 0.3s ease;">
    <!-- Close Button -->
    <button id="search-close-btn" style="position: absolute; top: 20px; right: 20px; background: none; border: none; font-size: 2rem; cursor: pointer; color: var(--color-text-dark); padding: 10px; transition: transform 0.2s ease;">
        &times;
    </button>
    
    <!-- Search Box Container -->
    <div style="width: 100%; max-width: 600px; padding: 20px; text-align: center; box-sizing: border-box;">
        <h2 style="font-family: var(--font-heading); font-weight: 800; font-size: 1.75rem; margin-bottom: 20px; color: var(--color-text-dark);">Caută pe Portal</h2>
        
        <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" style="position: relative; display: flex; width: 100%;">
            <input type="search" class="search-field" placeholder="Scrie aici ce cauți..." value="" name="s" style="width: 100%; padding: 16px 24px; font-size: 1.1rem; border: 2px solid var(--color-border); border-radius: 50px; outline: none; transition: border-color 0.2s ease, box-shadow: 0 0 0 4px rgba(4, 120, 87, 0.15); box-shadow: var(--shadow-sm); font-family: inherit;" />
            <button type="submit" class="search-submit btn btn-primary" style="position: absolute; right: 6px; top: 6px; bottom: 6px; border-radius: 50px; padding: 0 24px; font-weight: 800; font-size: 0.9rem; cursor: pointer;">
                Caută
            </button>
        </form>
        
        <!-- Sugestii de căutare rapide -->
        <div style="margin-top: 24px; font-size: 0.9rem; color: var(--color-text-muted);">
            <strong>Sugestii rapide:</strong>
            <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 8px; margin-top: 10px;">
                <a href="<?php echo esc_url( home_url( '/c/comunicate/' ) ); ?>" style="background: #f3f4f6; color: var(--color-text-dark); padding: 6px 12px; border-radius: 30px; text-decoration: none; font-weight: 600; font-size: 0.8rem; transition: background 0.2s;" onmouseover="this.style.background='#e5e7eb';" onmouseout="this.style.background='#f3f4f6';">comunicate</a>
                <a href="<?php echo esc_url( home_url( '/harta-servicii/' ) ); ?>" style="background: #f3f4f6; color: var(--color-text-dark); padding: 6px 12px; border-radius: 30px; text-decoration: none; font-weight: 600; font-size: 0.8rem; transition: background 0.2s;" onmouseover="this.style.background='#e5e7eb';" onmouseout="this.style.background='#f3f4f6';">hartă servicii</a>
                <a href="<?php echo esc_url( home_url( '/istoria-comunei-brezoaele/' ) ); ?>" style="background: #f3f4f6; color: var(--color-text-dark); padding: 6px 12px; border-radius: 30px; text-decoration: none; font-weight: 600; font-size: 0.8rem; transition: background 0.2s;" onmouseover="this.style.background='#e5e7eb';" onmouseout="this.style.background='#f3f4f6';">istoria comunei</a>
                <a href="<?php echo esc_url( home_url( '/investitii/' ) ); ?>" style="background: #f3f4f6; color: var(--color-text-dark); padding: 6px 12px; border-radius: 30px; text-decoration: none; font-weight: 600; font-size: 0.8rem; transition: background 0.2s;" onmouseover="this.style.background='#e5e7eb';" onmouseout="this.style.background='#f3f4f6';">investiții</a>
            </div>
        </div>
    </div>
</div>

<!-- Global Content Wrapper (Boxed Layout) -->
<div class="container site-main-container">
