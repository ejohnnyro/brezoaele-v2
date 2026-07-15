<?php
/**
 * The template for displaying comments.
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @package Brezoaele_V2
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area card" style="margin-top: 30px; padding: 24px; box-shadow: var(--shadow-sm); border: 1px solid var(--color-border); border-radius: var(--border-radius-lg); background: #ffffff;">

	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title" style="font-size: 1.25rem; font-weight: 800; font-family: var(--font-heading); margin-bottom: 20px; border-bottom: 2px solid var(--color-border); padding-bottom: 8px;">
			<?php
			$comment_count = get_comments_number();
			if ( '1' === $comment_count ) {
				printf(
					/* translators: 1: title. */
					esc_html__( 'Un comentariu la „%1$s”', 'brezoaele-v2' ),
					'<span>' . get_the_title() . '</span>'
				);
			} else {
				printf(
					/* translators: 1: comment count number, 2: title. */
					esc_html( _nx( '%1$s comentariu la „%2$s”', '%1$s comentarii la „%2$s”', $comment_count, 'comments title', 'brezoaele-v2' ) ),
					number_format_i18n( $comment_count ),
					'<span>' . get_the_title() . '</span>'
				);
			}
			?>
		</h2>

		<ol class="comment-list" style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 12px;">
			<?php
			wp_list_comments( array(
				'style'       => 'ol',
				'short_ping'  => true,
				'avatar_size' => 48,
				'callback'    => 'brezoaele_v2_comment_callback'
			) );
			?>
		</ol>

		<?php
		the_comments_navigation();

		// If comments are closed and there are comments, let's leave a little note.
		if ( ! comments_open() ) :
			?>
			<p class="no-comments" style="color: var(--color-text-muted); font-size: 0.9rem; text-align: center; margin-top: 20px;"><?php esc_html_e( 'Comentariile sunt închise.', 'brezoaele-v2' ); ?></p>
			<?php
		endif;

	endif; // Check for have_comments().

	// Customize comment form to look premium with our styling system
	$comment_form_args = array(
		'class_form' => 'flat-form',
		'title_reply' => __( 'Lasă un comentariu', 'brezoaele-v2' ),
		'title_reply_to' => __( 'Răspunde lui %s', 'brezoaele-v2' ),
		'title_reply_before' => '<h3 id="reply-title" class="comment-reply-title" style="font-size: 1.25rem; font-weight: 800; font-family: var(--font-heading); margin-top: 24px; margin-bottom: 16px; border-bottom: 2px solid var(--color-border); padding-bottom: 8px;">',
		'title_reply_after' => '</h3>',
		'submit_button' => '<button name="%1$s" type="submit" id="%2$s" class="%3$s btn btn-primary" style="margin-top: 10px; cursor: pointer;">%4$s</button>',
		'label_submit' => __( 'Postează Comentariul', 'brezoaele-v2' ),
		'comment_field' => '<div class="flat-form-group"><label for="comment" class="flat-form-label">' . _x( 'Comentariu', 'noun' ) . ' *</label><textarea id="comment" name="comment" cols="45" rows="5" class="flat-form-textarea" required></textarea></div>',
		'fields' => array(
			'author' => '<div class="flat-form-group"><label for="author" class="flat-form-label">' . __( 'Nume', 'brezoaele-v2' ) . ' *</label><input id="author" name="author" type="text" value="" size="30" class="flat-form-input" required /></div>',
			'email' => '<div class="flat-form-group"><label for="email" class="flat-form-label">' . __( 'Email (nu va fi publicat)', 'brezoaele-v2' ) . ' *</label><input id="email" name="email" type="email" value="" size="30" class="flat-form-input" required /></div>',
		)
	);

	comment_form( $comment_form_args );
	?>

</div><!-- #comments -->
