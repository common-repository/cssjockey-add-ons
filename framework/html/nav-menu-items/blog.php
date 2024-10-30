<?php
$blog_posts = get_posts( [
	'post_type' => 'post',
	'posts_per_page' => (isset( $item->cjwpbldr_blog_posts_number ) && $item->cjwpbldr_blog_posts_number > 0) ? $item->cjwpbldr_blog_posts_number : 5,
	'post_status' => 'publish',
] );
?>
<a href="<?php echo $item->url ?>" class="<?php echo implode( ' ', $classes ); ?>" <?php echo $target . ' ' . $title_tag . ' ' . $rel; ?>>
	<?php echo $args->before ?>
    <span class="link-title"><?php echo $title; ?> <i class="menu-arrow fa fa-chevron-<?php echo ($depth == 0) ? 'down' : 'right'; ?>"></i></span>
	<?php if( $depth > 0 && $description != '' ) { ?>
        <span class="link-description"><?php echo $description; ?></span>
	<?php } ?>
	<?php echo $args->after ?>
</a>

<?php if( ! empty( $blog_posts ) ) { ?>
    <ul>
		<?php foreach( $blog_posts as $key => $blog_post ) { ?>
            <li>
                <a href="<?php echo $blog_post->permalink; ?>" title="<?php echo $blog_post->post_title; ?>">
                    <span class="link-title"><?php echo $blog_post->post_title; ?> </span>
					<?php if( $blog_post->post_excerpt != '' ) { ?>
                        <span class="link-description"><?php echo strip_tags(trim($blog_post->post_excerpt)); ?></span>
					<?php } ?>
                </a>
            </li>
		<?php } ?>
    </ul>
<?php } ?>
