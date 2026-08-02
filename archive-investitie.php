<?php
/**
 * The template for displaying archive pages for investment projects (investitii).
 *
 * @package Brezoaele_V2
 */

get_header();

$current_stadiu = isset( $_GET['stadiu'] ) ? sanitize_text_field( wp_unslash( $_GET['stadiu'] ) ) : 'toate';
$current_search = isset( $_GET['s_invest'] ) ? sanitize_text_field( wp_unslash( $_GET['s_invest'] ) ) : '';
?>

<main id="primary" class="site-main" style="padding: 40px 0; background-color: var(--color-bg);">
	<div class="container">
		
		<header class="page-header" style="margin-bottom: 30px;">
			<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; margin-bottom: 24px;">
				<div>
					<h1 class="page-title" style="font-size: 2.25rem; margin-bottom: 6px; font-weight: 900; font-family: var(--font-heading);">
						🏗️ Investiții și Dezvoltare Locală
					</h1>
					<p style="color: var(--color-text-muted); font-size: 0.95rem; margin: 0;">
						Urmărește stadiul proiectelor de infrastructură, modernizare și dezvoltare din comuna Brezoaele.
					</p>
				</div>
			</div>

			<!-- Formular Filtrare & Căutare Investiții -->
			<div class="card" style="padding: 20px; background: #ffffff; border: 1px solid var(--color-border); border-radius: var(--border-radius-lg); box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
				<form method="get" action="<?php echo esc_url( home_url( '/investitii/' ) ); ?>" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
					
					<!-- Căutare după Nume/Titlu -->
					<div style="flex: 1; min-width: 240px;">
						<label for="s_invest" style="display: block; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); margin-bottom: 4px;">
							🔍 Caută după titlu / proiect:
						</label>
						<input type="text" id="s_invest" name="s_invest" value="<?php echo esc_attr( $current_search ); ?>" placeholder="Ex: gaze, școală, fotovoltaice..." style="width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; color: #0f172a; box-sizing: border-box;" />
					</div>

					<!-- Filtru după Stadiu -->
					<div style="min-width: 200px;">
						<label for="stadiu" style="display: block; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); margin-bottom: 4px;">
							📊 Filtrează după stadiu:
						</label>
						<select id="stadiu" name="stadiu" style="width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; color: #0f172a; background: #ffffff; box-sizing: border-box;">
							<option value="toate" <?php selected( $current_stadiu, 'toate' ); ?>>Toate Stadiile</option>
							<option value="În derulare" <?php selected( $current_stadiu, 'În derulare' ); ?>>⚡ În derulare (Active)</option>
							<option value="Planificat" <?php selected( $current_stadiu, 'Planificat' ); ?>>📌 Planificate (Pregătire)</option>
							<option value="Finalizat" <?php selected( $current_stadiu, 'Finalizat' ); ?>>✅ Finalizate (Recepționate)</option>
							<option value="Amânat" <?php selected( $current_stadiu, 'Amânat' ); ?>>⏳ Amânate (Temporar)</option>
							<option value="Anulat" <?php selected( $current_stadiu, 'Anulat' ); ?>>❌ Anulate (Stopate)</option>
						</select>
					</div>

					<!-- Butoane Acțiune -->
					<div style="display: flex; gap: 8px; align-self: flex-end; margin-top: 4px;">
						<button type="submit" class="btn btn-primary" style="padding: 10px 20px; font-weight: 800; font-size: 0.9rem; border-radius: 8px;">
							Filtrează &rarr;
						</button>
						<?php if ( 'toate' !== $current_stadiu || ! empty( $current_search ) ) : ?>
							<a href="<?php echo esc_url( home_url( '/investitii/' ) ); ?>" class="btn" style="padding: 10px 14px; background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; font-size: 0.85rem; border-radius: 8px; text-decoration: none; font-weight: 700;">
								✖ Reset
							</a>
						<?php endif; ?>
					</div>
				</form>
			</div>
		</header>

		<div class="grid grid-3">
			<?php
			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
					
					// Preluăm metadatele investiției
					$stadiu = get_post_meta( get_the_ID(), '_investitie_stadiu', true );
					$buget  = get_post_meta( get_the_ID(), '_investitie_buget', true );
					$sursa  = get_post_meta( get_the_ID(), '_investitie_sursa', true );
			?>
					<article class="card" style="display: flex; flex-direction: column; justify-content: space-between; position: relative;">
						<div>
							<?php if ( has_post_thumbnail() ) : ?>
								<a href="<?php the_permalink(); ?>" style="display: block; transition: opacity 0.2s ease;" onmouseover="this.style.opacity='0.95';" onmouseout="this.style.opacity='1';">
									<div class="card-image-wrapper" style="margin-bottom: 14px; border-radius: var(--border-radius-md); overflow: hidden; aspect-ratio: 16/9; border: 1px solid var(--color-border);">
										<?php the_post_thumbnail( 'medium', array( 'style' => 'width: 100%; height: 100%; object-fit: cover;' ) ); ?>
									</div>
								</a>
							<?php endif; ?>

							<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; flex-wrap: wrap; gap: 6px;">
								<span style="font-size: 0.72rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); letter-spacing: 0.5px;">
									🚜 Investiție Locală
								</span>
								<?php echo brezoaele_get_investitie_stadiu_badge( $stadiu ); ?>
							</div>

							<h3 style="margin: 8px 0 10px 0; font-size: 1.15rem; line-height: 1.3; font-weight: 800; font-family: var(--font-heading);">
								<a href="<?php the_permalink(); ?>" style="color: var(--color-text-dark); text-decoration: none;"><?php the_title(); ?></a>
							</h3>
							
							<p style="color: var(--color-text-muted); font-size: 0.85rem; margin-bottom: 16px; line-height: 1.5;">
								<?php echo wp_trim_words( get_the_excerpt(), 18, '...' ); ?>
							</p>
						</div>

						<div style="border-top: 1px solid var(--color-border); padding-top: 12px; margin-top: 8px; font-size: 0.8rem; color: var(--color-text-muted);">
							<?php if ( ! empty( $buget ) ) : ?>
								<div style="margin-bottom: 4px;">
									💰 <b>Buget Valoare:</b> <?php echo esc_html( $buget ); ?>
								</div>
							<?php endif; ?>
							<?php if ( ! empty( $sursa ) ) : ?>
								<div style="margin-bottom: 10px;">
									🏛️ <b>Finanțare:</b> <?php echo esc_html( $sursa ); ?>
								</div>
							<?php endif; ?>
							<a href="<?php the_permalink(); ?>" class="btn btn-primary" style="width: 100%;">Vezi Detalii Proiect &rarr;</a>
						</div>
					</article>
			<?php
				endwhile;
			else :
			?>
				<div style="grid-column: 1 / -1; text-align: center; padding: 40px 0; background-color: var(--color-card); border: 1px solid var(--color-border); border-radius: var(--border-radius-lg);">
					<div style="font-size: 3rem; margin-bottom: 12px;">🔍</div>
					<h3 style="margin-bottom: 6px;">Nu s-au găsit proiecte de investiții</h3>
					<p style="color: var(--color-text-muted); font-size: 0.95rem;">Încearcă să schimbi termenul de căutare sau stadiul selectat.</p>
					<a href="<?php echo esc_url( home_url( '/investitii/' ) ); ?>" class="btn btn-primary" style="margin-top: 12px; display: inline-block;">
						Vezi toate investițiile
					</a>
				</div>
			<?php endif; ?>
		</div>

		<div style="margin-top: 32px; display: flex; justify-content: center;">
			<?php the_posts_pagination( array(
				'mid_size'  => 2,
				'prev_text' => __( '&larr; Înapoi', 'brezoaele-v2' ),
				'next_text' => __( 'Înainte &rarr;', 'brezoaele-v2' ),
			) ); ?>
		</div>

	</div>
</main>

<?php
get_footer();
