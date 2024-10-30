<?php
$helpers = cjwpbldr_helpers::getInstance();
$wp_comments = cjwpbldr_wp_comments::getInstance();
if( post_password_required() ) {
	return;
}
$discussion = $wp_comments->getDiscussionData();
$navigation_args = [
	'prev_text' => __( '<span class="cj-icon cj-is-small"><i class="fa fa-angle-left"></i></span> Previous', 'wp-builder-locale' ),
	'next_text' => __( 'Next <span class="cj-icon cj-is-small"><i class="fa fa-angle-right"></i></span>', 'wp-builder-locale' ),
];
?>
<div id="comments" class="<?php echo comments_open() ? 'comments-area' : 'comments-area comments-closed'; ?>">
	<?php if( have_comments() ) : ?>
        <h3 class="comments-title">
			<?php
			if( comments_open() ) {
				if( have_comments() ) {
					_e( 'Join the Conversation', 'wp-builder-locale' );
				} else {
					_e( 'Leave a comment', 'wp-builder-locale' );
				}
			} else {
				if( '1' == $discussion->responses ) {
					/* translators: %s: post title */
					printf( _x( 'One reply on &ldquo;%s&rdquo;', 'comments title', 'wp-builder-locale' ), get_the_title() );
				} else {
					printf(
					/* translators: 1: number of comments, 2: post title */
						_nx(
							'<small class="cj-fs-16 cj-opacity-70">%1$s reply on:</small> <br> %2$s',
							'<small class="cj-fs-16 cj-opacity-70">%1$s replies on:</small> <br> %2$s',
							$discussion->responses,
							'comments title',
							'wp-builder-locale'
						),
						number_format_i18n( $discussion->responses ),
						get_the_title()
					);
				}
			}
			?>
        </h3>
		<?php the_comments_navigation( $navigation_args ); ?>
        <ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style' => 'ol',
					'short_ping' => true,
					'avatar_size' => 125,
					'reply_text' => __( '<span class="cj-button cj-is-small">Respond</span>', 'wp-builder-locale' ),
					'format' => 'html5'
				)
			);
			?>
        </ol><!-- .comment-list -->
		<?php the_comments_navigation( $navigation_args ); ?>

	<?php endif; // Check for have_comments(). ?>

	<?php
	// If comments are closed and there are comments, let's leave a little note, shall we?
	if( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
		?>
        <p class="no-comments"><?php _e( 'Comments are closed.', 'wp-builder-locale' ); ?></p>
	<?php endif; ?>

	<?php comment_form( $helpers->commentsFormArgs() ); ?>

</div><!-- .comments-area -->