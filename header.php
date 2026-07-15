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
            <button id="search-toggle-btn" class="search-toggle" aria-label="Caută pe site">
                🔍
            </button>
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

<!-- Cookie Consent Banner -->
<div id="cookie-consent-banner" class="cookie-banner" style="display: none; position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%) translateY(100px); width: calc(100% - 40px); max-width: 800px; background-color: var(--color-card); border: 1px solid var(--color-border); border-radius: var(--border-radius-lg); box-shadow: var(--shadow-lg); z-index: 9998; padding: 20px; box-sizing: border-box; transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.4s ease; opacity: 0;">
    <div style="display: flex; flex-direction: column; gap: 16px;">
        <div style="display: flex; align-items: flex-start; gap: 12px;">
            <span style="font-size: 1.75rem; line-height: 1; flex-shrink: 0;">🍪</span>
            <div style="flex-grow: 1;">
                <h3 style="font-family: var(--font-heading); font-weight: 800; font-size: 1.1rem; margin-top: 0; margin-bottom: 6px; color: var(--color-text-dark);">Respectăm confidențialitatea ta</h3>
                <p style="color: var(--color-text-muted); font-size: 0.88rem; margin: 0; line-height: 1.5;">
                    Utilizăm cookie-uri proprii și de la terți pentru a-ți asigura o experiență optimă pe site, pentru a analiza traficul și pentru a-ți oferi conținut adaptat. Poți accepta toate cookie-urile, le poți refuza sau poți alege categoriile specifice dorite. Pentru mai multe detalii, consultă <a href="<?php echo esc_url( home_url( '/politica-de-cookies/' ) ); ?>" style="color: var(--color-primary-dark); font-weight: 700; text-decoration: underline;">Politica de Cookies</a>.
                </p>
            </div>
        </div>
        <div style="display: flex; flex-wrap: wrap; justify-content: flex-end; gap: 10px; border-top: 1px solid var(--color-border); padding-top: 14px; margin-top: 4px;">
            <button id="cookie-settings-btn" class="btn btn-secondary" style="font-size: 0.85rem; padding: 10px 18px;">Personalizează</button>
            <button id="cookie-reject-btn" class="btn btn-secondary" style="font-size: 0.85rem; padding: 10px 18px; color: #dc2626;">Refuză Tot</button>
            <button id="cookie-accept-btn" class="btn btn-primary" style="font-size: 0.85rem; padding: 10px 18px; background-color: var(--color-primary);">Acceptă Tot</button>
        </div>
    </div>
</div>

<!-- Cookie Preferences Modal -->
<div id="cookie-preferences-modal" class="cookie-modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); z-index: 9999; justify-content: center; align-items: center; opacity: 0; transition: opacity 0.3s ease;">
    <div class="cookie-modal-card card" style="width: 100%; max-width: 640px; margin: 20px; max-height: calc(100vh - 40px); overflow-y: auto; padding: 24px; box-sizing: border-box; position: relative; transform: scale(0.95); transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);">
        <!-- Close Button -->
        <button id="cookie-modal-close" style="position: absolute; top: 16px; right: 16px; background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--color-text-muted); padding: 4px; line-height: 1;">
            &times;
        </button>

        <h3 style="font-family: var(--font-heading); font-weight: 900; font-size: 1.35rem; margin-top: 0; margin-bottom: 8px; color: var(--color-text-dark); border-bottom: 2px solid var(--color-border); padding-bottom: 10px;">Setări Preferințe Cookie-uri</h3>
        <p style="color: var(--color-text-muted); font-size: 0.88rem; line-height: 1.5; margin-bottom: 20px;">
            Alege categoriile de module cookie pe care ești de acord să le stocăm în browserul tău. Cookie-urile esențiale sunt necesare pentru funcționarea portalului și nu pot fi dezactivate.
        </p>

        <!-- Preference Categories -->
        <div style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 24px;">
            <!-- Category: Essential -->
            <div style="border: 1px solid var(--color-border); border-radius: var(--border-radius-md); padding: 14px; background-color: #fafafa; display: flex; justify-content: space-between; align-items: flex-start; gap: 16px;">
                <div style="flex-grow: 1;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                        <span style="font-weight: 800; font-size: 0.95rem; color: var(--color-text-dark);">Necesare / Esențiale</span>
                        <span style="background-color: #e2e8f0; color: #475569; font-size: 0.65rem; font-weight: 800; padding: 2px 6px; border-radius: 30px; text-transform: uppercase;">Obligatoriu</span>
                    </div>
                    <p style="color: var(--color-text-muted); font-size: 0.8rem; margin: 0; line-height: 1.4;">
                        Asigură funcționalități de bază (sesiunea de utilizator, completarea formularelor securizate cu nonces și memorarea preferințelor GDPR). Baza legală: Obligație Legală / Interes Legitim.
                    </p>
                </div>
                <div class="cookie-switch-wrapper" style="margin-top: 4px;">
                    <input type="checkbox" id="cookie-category-essential" checked disabled style="display:none;" />
                    <label class="cookie-switch" style="background-color: var(--color-primary); cursor: not-allowed;"></label>
                </div>
            </div>

            <!-- Category: Analytics -->
            <div style="border: 1px solid var(--color-border); border-radius: var(--border-radius-md); padding: 14px; background-color: #fafafa; display: flex; justify-content: space-between; align-items: flex-start; gap: 16px;">
                <div style="flex-grow: 1;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                        <span style="font-weight: 800; font-size: 0.95rem; color: var(--color-text-dark);">Statistici / Analitice</span>
                        <span style="background-color: var(--color-primary-light); color: var(--color-primary-dark); font-size: 0.65rem; font-weight: 800; padding: 2px 6px; border-radius: 30px; text-transform: uppercase;">Consimțământ</span>
                    </div>
                    <p style="color: var(--color-text-muted); font-size: 0.8rem; margin: 0; line-height: 1.4;">
                        Ne permit să numărăm vizitele și sursele de trafic pentru a măsura și îmbunătăți performanța portalului (Google Analytics). Datele sunt procesate anonim.
                    </p>
                </div>
                <div class="cookie-switch-wrapper" style="margin-top: 4px;">
                    <input type="checkbox" id="cookie-category-analytics" style="display:none;" />
                    <label for="cookie-category-analytics" class="cookie-switch-label"></label>
                </div>
            </div>

            <!-- Category: Marketing -->
            <div style="border: 1px solid var(--color-border); border-radius: var(--border-radius-md); padding: 14px; background-color: #fafafa; display: flex; justify-content: space-between; align-items: flex-start; gap: 16px;">
                <div style="flex-grow: 1;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                        <span style="font-weight: 800; font-size: 0.95rem; color: var(--color-text-dark);">Marketing & Publicitate</span>
                        <span style="background-color: var(--color-primary-light); color: var(--color-primary-dark); font-size: 0.65rem; font-weight: 800; padding: 2px 6px; border-radius: 30px; text-transform: uppercase;">Consimțământ</span>
                    </div>
                    <p style="color: var(--color-text-muted); font-size: 0.8rem; margin: 0; line-height: 1.4;">
                        Sunt folosite pentru a urmări vizitatorii pe site-uri web (ex: Meta Pixel/Facebook) pentru a afișa reclame relevante și conținut integrat din rețelele sociale.
                    </p>
                </div>
                <div class="cookie-switch-wrapper" style="margin-top: 4px;">
                    <input type="checkbox" id="cookie-category-marketing" style="display:none;" />
                    <label for="cookie-category-marketing" class="cookie-switch-label"></label>
                </div>
            </div>
        </div>

        <!-- Section: Dynamic Cookie Detection Details -->
        <div style="margin-top: 24px; border-top: 1px solid var(--color-border); padding-top: 20px;">
            <h4 style="font-family: var(--font-heading); font-weight: 800; font-size: 1rem; margin-top: 0; margin-bottom: 8px; color: var(--color-text-dark); display: flex; align-items: center; gap: 6px;">
                🔍 Modul Inteligent de Detecție Active Cookies
            </h4>
            <p style="color: var(--color-text-muted); font-size: 0.78rem; margin-top: 0; margin-bottom: 12px; line-height: 1.4;">
                Tabelul de mai jos este generat automat. Modulul detectează în timp real cookie-urile stocate în acest moment în navigatorul tău și le atribuie baza legală și scopul aferent.
            </p>
            <div style="overflow-x: auto; max-height: 200px; border: 1px solid var(--color-border); border-radius: var(--border-radius-md);">
                <table id="cookie-detection-table" style="width: 100%; border-collapse: collapse; font-size: 0.75rem; text-align: left; background: #ffffff;">
                    <thead>
                        <tr style="background-color: #f1f5f9; border-bottom: 1px solid var(--color-border); font-weight: 800; color: var(--color-text-dark);">
                            <th style="padding: 10px 12px;">Nume</th>
                            <th style="padding: 10px 12px;">Domeniu</th>
                            <th style="padding: 10px 12px;">Categorie</th>
                            <th style="padding: 10px 12px;">Scop și Bază Legală</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Table contents populated dynamically via JS -->
                        <tr>
                            <td colspan="4" style="padding: 16px; text-align: center; color: var(--color-text-muted); font-style: italic;">
                                Se scanează cookie-urile active...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer Modal Buttons -->
        <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid var(--color-border); padding-top: 16px; margin-top: 20px;">
            <button id="cookie-save-preferences-btn" class="btn btn-primary" style="font-size: 0.85rem; padding: 10px 20px; width: 100%;">
                Salvează Preferințele Selectate
            </button>
        </div>
    </div>
</div>

<!-- Global Content Wrapper (Boxed Layout) -->
<div class="container site-main-container">
