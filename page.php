<?php
/**
 * The template for displaying all pages.
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
				
				<header class="entry-header" style="margin-bottom: 30px; text-align: center;">
					<?php the_title( '<h1 class="entry-title" style="font-size: 2.5rem; margin-bottom: 12px; font-weight: 800; font-family: var(--font-heading);">', '</h1>' ); ?>
					<div style="width: 50px; height: 3px; background-color: var(--color-primary); margin: 0 auto; border-radius: 3px;"></div>
				</header>

				<div class="card" style="padding: 24px; font-size: 1rem; line-height: 1.7;">
					<div class="entry-content">
						<?php the_content(); ?>
					</div>
				</div>

			</article>
		<?php
		endwhile;
		?>
	</div>
</main>

<?php
get_footer();
