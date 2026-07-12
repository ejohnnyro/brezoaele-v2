<?php
/**
 * The main template file.
 *
 * @package Brezoaele_V2
 */

get_header();
?>

<main id="primary" class="site-main" style="padding: 40px 0; background-color: var(--color-bg);">
	<div class="container">
		<header class="page-header" style="margin-bottom: 30px; text-align: center;">
			<h1 class="page-title" style="font-size: 2.5rem; font-weight: 800; font-family: var(--font-heading); margin-bottom: 8px;">
				<?php
				if ( is_home() && ! is_front_page() ) {
					single_post_title();
				} elseif ( is_archive() ) {
					the_archive_title();
				} else {
					echo 'Articole';
				}
				?>
			</h1>
			<div style="width: 50px; height: 3px; background-color: var(--color-primary); margin: 12px auto 0 auto; border-radius: 3px;"></div>
		</header>

		<div class="grid grid-3">
			<?php
			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
			?>
					<article class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
						<div>
							<?php if ( has_post_thumbnail() ) : ?>
								<div style="margin-bottom: 12px; border: 1px solid var(--color-border); height: 180px; overflow: hidden;">
									<?php the_post_thumbnail( 'medium', array( 'style' => 'width:100%; height:100%; object-fit: cover;' ) ); ?>
								</div>
							<?php endif; ?>
							
							<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
								<span style="font-size: 0.7rem; font-weight: 800; text-transform: uppercase; color: var(--color-primary-dark); background-color: var(--color-primary-light); padding: 2px 8px; border: 1px solid var(--color-primary-light); border-radius: 30px;">
									<?php
									$categories = get_the_category();
									if ( ! empty( $categories ) ) {
										echo esc_html( $categories[0]->name );
									} else {
										echo 'Știri';
									}
									?>
								</span>
								<span style="font-size: 0.75rem; color: var(--color-text-muted); font-weight: 500;"><?php echo get_the_date('d.m.Y'); ?></span>
							</div>

							<h3 style="margin: 6px 0 10px 0; font-size: 1.15rem; line-height: 1.25; font-weight: 800; font-family: var(--font-heading);">
								<a href="<?php the_permalink(); ?>" style="color: var(--color-text-dark); text-decoration: none;"><?php the_title(); ?></a>
							</h3>
							
							<p style="color: var(--color-text-muted); font-size: 0.85rem; margin-bottom: 16px; line-height: 1.5;">
								<?php echo wp_trim_words( get_the_excerpt(), 18, '...' ); ?>
							</p>
						</div>
						<a href="<?php the_permalink(); ?>" style="font-weight: 800; font-size: 0.8rem; text-transform: uppercase; color: var(--color-primary); text-decoration: underline;">Citește Mai Mult &rarr;</a>
					</article>
			<?php
				endwhile;
			else :
			?>
				<div style="grid-column: 1 / -1; text-align: center; padding: 40px 0; background-color: var(--color-card); border: 1px solid var(--color-border); border-radius: var(--border-radius-lg);">
					<p style="color: var(--color-text-muted); font-size: 0.95rem;">Nu s-au găsit articole în această arhivă.</p>
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
