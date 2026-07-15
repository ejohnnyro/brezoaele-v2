<?php
/**
 * The template for displaying all single posts.
 *
 * @package Brezoaele_V2
 */

get_header();
?>

<main id="primary" class="site-main" style="padding: 40px 0; background-color: var(--color-bg);">
	<div class="container">
		<?php
		while ( have_posts() ) :
			the_post();
		?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> style="max-width: 800px; margin: 0 auto;">
				
				<header class="entry-header" style="margin-bottom: 24px; text-align: center;">
					<div style="margin-bottom: 6px;">
						<span style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: var(--color-primary-dark); background-color: var(--color-primary-light); padding: 2px 6px; border: 1px solid var(--color-border); letter-spacing: 0.5px;">
							<?php the_category( ', ' ); ?>
						</span>
					</div>
					<?php the_title( '<h1 class="entry-title" style="font-size: 2.25rem; margin-bottom: 12px; font-weight: 900; font-family: var(--font-heading); line-height: 1.25;">', '</h1>' ); ?>
					<div style="font-size: 0.8rem; color: var(--color-text-muted); font-weight: 600;">
						Publicat la <?php echo get_the_date(); ?> • De <?php the_author(); ?>
					</div>
				</header>

				<?php if ( has_post_thumbnail() ) : ?>
					<div class="post-thumbnail" style="margin-bottom: 24px; border: 1px solid var(--color-border); border-radius: var(--border-radius-lg); overflow: hidden;">
						<?php the_post_thumbnail( 'large', array( 'style' => 'width: 100%; height: auto; display: block;' ) ); ?>
					</div>
				<?php endif; ?>

				<div class="card" style="padding: 24px; font-size: 1rem; line-height: 1.7;">
					<div class="entry-content">
						<?php the_content(); ?>
					</div>
				</div>

				<nav class="navigation post-navigation" style="margin-top: 30px; padding-top: 16px; border-top: 1px solid var(--color-border); display: flex; justify-content: space-between; font-weight: 700; font-size: 0.85rem; text-transform: uppercase;">
					<div class="nav-previous">
						<?php previous_post_link( '%link', '&larr; Articol Precedent' ); ?>
					</div>
					<div class="nav-next">
						<?php next_post_link( '%link', 'Articol Următor &rarr;' ); ?>
					</div>
				</nav>

				<?php
				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;
				?>
			</article>
		<?php
		endwhile;
		?>
	</div>
</main>

<?php
get_footer();
