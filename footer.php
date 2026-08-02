</div><!-- .site-main-container -->

<?php
$about_title = get_option( 'brezoaele_v2_footer_about_title', get_theme_mod( 'brezoaele_v2_footer_about_title', 'Despre Proiect' ) );
$about_text  = get_option( 'brezoaele_v2_footer_about_text', get_theme_mod( 'brezoaele_v2_footer_about_text', 'Comuna Brezoaele.ro este o inițiativă civică independentă dedicată conectării administrației locale, cetățenilor activi și investitorilor.' ) );
$admin_title = get_option( 'brezoaele_v2_footer_admin_title', get_theme_mod( 'brezoaele_v2_footer_admin_title', 'Administrație' ) );
$admin_text  = get_option( 'brezoaele_v2_footer_admin_text', get_theme_mod( 'brezoaele_v2_footer_admin_text', 'Dezvoltat cu mândrie pentru Brezoaele de către comunitatea locală.' ) );

if ( empty( $about_title ) ) { $about_title = 'Despre Proiect'; }
if ( empty( $about_text ) ) { $about_text = 'Comuna Brezoaele.ro este o inițiativă civică independentă dedicată conectării administrației locale, cetățenilor activi și investitorilor.'; }
if ( empty( $admin_title ) ) { $admin_title = 'Administrație'; }
if ( empty( $admin_text ) ) { $admin_text = 'Dezvoltat cu mândrie pentru Brezoaele de către comunitatea locală.'; }
?>

<footer id="colophon" class="site-footer">
	<div class="container grid grid-3">
		<div>
			<h3><?php echo esc_html( $about_title ); ?></h3>
			<p><?php echo nl2br( wp_kses_post( $about_text ) ); ?></p>
		</div>
		<div>
			<h3>Utile</h3>
			<ul>
				<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Contact</a></li>
				<li><a href="<?php echo esc_url( home_url( '/program-microbuz-brezoaele-bucuresti/' ) ); ?>">Mersul Microbuzelor</a></li>
				<li><a href="<?php echo esc_url( home_url( '/termeni-si-conditii-de-utilizare/' ) ); ?>">Termeni și Condiții</a></li>
				<li><a href="<?php echo esc_url( home_url( '/politica-de-confidentialitate/' ) ); ?>">Politica de Confidențialitate</a></li>
				<li><a href="<?php echo esc_url( home_url( '/politica-de-cookies/' ) ); ?>">Politica de Cookies</a></li>
				<li><a href="#" id="open-cookie-settings-footer">Setări Cookies</a></li>
			</ul>
		</div>
		<div>
			<h3><?php echo esc_html( $admin_title ); ?></h3>
			<p><?php echo nl2br( wp_kses_post( $admin_text ) ); ?></p>
		</div>
	</div>
	<div class="footer-bottom">
		<div class="container">
			<p>&copy; <?php echo date( 'Y' ); ?> <?php bloginfo( 'name' ); ?>. Toate drepturile rezervate.</p>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
