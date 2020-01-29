<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
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

<div id="comments" class="comments-area fields">

    <?php
    $args = [
        'title_reply' => 'ASK THE DOCTOR',
        'label_submit' => 'Add Comment',
    ];
    comment_form($args);

	// You can start editing here -- including this comment!
	if ( have_comments() ) :
	?>
		<ol class="comment-list">
			<?php
				wp_list_comments(
					array(
						'callback' => 'his_comment',
						'avatar_size' => 100,
						'style'       => 'ol',
						'short_ping'  => true,
					)
				);

			?>
		</ol>

		<?php
		the_comments_pagination(
			array(
				'prev_text' => '<',
				'next_text' => '>',
			)
		);

	endif; // Check for have_comments().

	// If comments are closed and there are comments, let's leave a little note, shall we?
	if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>

		<p class="no-comments"><?php _e( 'Comments are closed.', 'his-clinic' ); ?></p>
	<?php
	endif;
	?>

</div><!-- #comments -->
