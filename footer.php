</div><!-- .site-main-container -->

<footer id="colophon" class="site-footer">
	<div class="container grid grid-3">
		<div>
			<h3>Despre Proiect</h3>
			<p>Comuna Brezoaele.ro este o inițiativă civică independentă dedicată conectării administrației locale, cetățenilor activi și investitorilor.</p>
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
			<h3>Administrație</h3>
			<p>Dezvoltat cu mândrie pentru Brezoaele de către comunitatea locală.</p>
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
