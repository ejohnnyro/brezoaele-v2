<?php
/**
 * The template for displaying a single question/discussion (intrebare).
 *
 * @package Brezoaele_V2
 */

get_header();
?>

<main id="primary" class="site-main" style="padding: 40px 0; background-color: var(--color-bg);">
	<div class="container" style="max-width: 800px;">
		
		<?php
		while ( have_posts() ) :
			the_post();
		?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'card' ); ?> style="padding: 24px; margin-bottom: 30px;">
				<header class="entry-header" style="margin-bottom: 20px; border-bottom: 1px solid var(--color-border); padding-bottom: 12px;">
					<div style="margin-bottom: 6px;">
						<span style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: var(--color-primary-dark); background-color: var(--color-primary-light); padding: 2px 8px; border: 1px solid var(--color-primary-light); border-radius: 30px; letter-spacing: 0.5px;">
							Discuție în Comunitate
						</span>
					</div>
					<?php the_title( '<h1 class="entry-title" style="font-size: 2rem; margin-bottom: 6px; font-weight: 900; font-family: var(--font-heading); line-height: 1.2;">', '</h1>' ); ?>
					<div style="font-size: 0.8rem; color: var(--color-text-muted); font-weight: 600;">
						Subiect deschis de <b><?php the_author(); ?></b> la <?php echo get_the_date(); ?>
					</div>
				</header>

				<div class="entry-content" style="font-size: 1rem; line-height: 1.7; color: var(--color-text-dark);">
					<?php the_content(); ?>
				</div>
			</article>

			<!-- Secțiunea Răspunsuri -->
			<section class="answers-area" style="margin-bottom: 30px;">
				<?php
				$comments_count = get_comments_number();
				?>
				<h3 style="font-size: 1.3rem; margin-bottom: 20px; border-bottom: 1px solid var(--color-border); padding-bottom: 6px; font-weight: 800; text-transform: uppercase;">
					<?php echo $comments_count; ?> <?php echo _n( 'Răspuns', 'Răspunsuri', $comments_count, 'brezoaele-v2' ); ?>
				</h3>

				<?php
				// Preluăm comentariile aprobate
				$comments = get_comments( array(
					'post_id' => get_the_ID(),
					'status'  => 'approve',
					'order'   => 'ASC',
				) );

				if ( ! empty( $comments ) ) :
				?>
					<ol class="comment-list" style="list-style: none; display: flex; flex-direction: column; gap: 16px; padding: 0; margin: 0;">
						<?php foreach ( $comments as $comment ) : ?>
							<li id="comment-<?php comment_ID(); ?>" class="card" style="padding: 16px 20px; border-left: 4px solid var(--color-primary);">
								<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; font-size: 0.8rem; color: var(--color-text-muted); font-weight: 600;">
									<div>
										Răspuns oferit de <b><?php comment_author( $comment ); ?></b>
									</div>
									<div>
										la <?php echo comment_date( 'd.m.Y, H:i', $comment ); ?>
									</div>
								</div>
								<div style="font-size: 0.95rem; line-height: 1.6; color: var(--color-text-dark);">
									<?php comment_text( $comment ); ?>
								</div>
							</li>
						<?php endforeach; ?>
					</ol>
				<?php else : ?>
					<p style="color: var(--color-text-muted); font-style: italic; font-size: 0.95rem;">Nu există răspunsuri deocamdată. Fii primul care oferă un răspuns!</p>
				<?php endif; ?>
			</section>

			<!-- Formular Adăugare Răspuns (Flat UI Forms) -->
			<div class="card" style="padding: 24px;">
				<?php
				$comment_args = array(
					'title_reply'    => __( 'Adaugă Răspunsul tău', 'brezoaele-v2' ),
					'title_reply_to' => __( 'Răspunde lui %s', 'brezoaele-v2' ),
					'label_submit'   => __( 'Postează Răspuns', 'brezoaele-v2' ),
					'comment_field'  => '<div class="flat-form-group"><label for="comment" class="flat-form-label">Răspunsul tău *</label><textarea id="comment" name="comment" class="flat-form-input" cols="45" rows="4" required placeholder="Scrie aici răspunsul sau sfatul tău..."></textarea></div>',
					'submit_button'  => '<input name="%1$s" type="submit" id="%2$s" class="btn btn-primary btn-block" style="margin-top: 10px;" value="%4$s" />',
					'class_submit'   => 'submit',
				);
				comment_form( $comment_args );
				?>
			</div>

		<?php
		endwhile;
		?>

	</div>
</main>

<?php
get_footer();
