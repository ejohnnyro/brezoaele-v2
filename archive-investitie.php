<?php
/**
 * The template for displaying archive pages for investment projects (investitii).
 *
 * @package Brezoaele_V2
 */

get_header();
?>

<main id="primary" class="site-main" style="padding: 40px 0; background-color: var(--color-bg);">
	<div class="container">
		
		<header class="page-header" style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
			<div>
				<h1 class="page-title" style="font-size: 2.5rem; margin-bottom: 6px; font-weight: 800; font-family: var(--font-heading);">Investiții și Dezvoltare Locală</h1>
				<p style="color: var(--color-text-muted); font-size: 0.95rem;">Urmărește stadiul proiectelor de infrastructură, modernizare și dezvoltare din comuna Brezoaele.</p>
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
					
					// Stabilim o culoare de badge sau border în funcție de stadiul proiectului
					$is_completed = ( strtolower( $stadiu ) === 'finalizat' || strtolower( $stadiu ) === 'realizat' );
			?>
					<article class="card" style="display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid <?php echo $is_completed ? 'var(--color-primary)' : 'var(--color-secondary)'; ?>;">
						<div>
							<?php if ( has_post_thumbnail() ) : ?>
								<a href="<?php the_permalink(); ?>" style="display: block; transition: opacity 0.2s ease;" onmouseover="this.style.opacity='0.95';" onmouseout="this.style.opacity='1';">
									<div class="card-image-wrapper" style="margin-bottom: 12px; border-radius: var(--border-radius-md); overflow: hidden; aspect-ratio: 16/9; border: 1px solid var(--color-border);">
										<?php the_post_thumbnail( 'medium', array( 'style' => 'width: 100%; height: 100%; object-fit: cover;' ) ); ?>
									</div>
								</a>
							<?php endif; ?>

							<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
								<span style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); letter-spacing: 0.5px;">
									Investiție Locală
								</span>
								<?php if ( ! empty( $stadiu ) ) : ?>
									<span style="background-color: <?php echo $is_completed ? 'var(--color-primary-light)' : '#fef3c7'; ?>; color: <?php echo $is_completed ? 'var(--color-primary-dark)' : '#d97706'; ?>; font-size: 0.7rem; font-weight: 800; padding: 2px 8px; border: 1px solid <?php echo $is_completed ? 'var(--color-primary-light)' : '#fef3c7'; ?>; border-radius: 30px; text-transform: uppercase;">
										<?php echo esc_html( $stadiu ); ?>
									</span>
								<?php endif; ?>
							</div>

							<h3 style="margin: 6px 0 10px 0; font-size: 1.15rem; line-height: 1.25; font-weight: 800; font-family: var(--font-heading);">
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
							<a href="<?php the_permalink(); ?>" class="btn btn-primary" style="width: 100%;">Vezi Detalii Proiect</a>
						</div>
					</article>
			<?php
				endwhile;
			else :
			?>
				<div style="grid-column: 1 / -1; text-align: center; padding: 40px 0; background-color: var(--color-card); border: 1px solid var(--color-border); border-radius: var(--border-radius-lg);">
					<div style="font-size: 3rem; margin-bottom: 12px;">🚜</div>
					<h3>Momentan nu sunt proiecte înregistrate</h3>
					<p style="color: var(--color-text-muted); font-size: 0.95rem;">Urmează să fie publicate proiectele din comună.</p>
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
