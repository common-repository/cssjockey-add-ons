<?php
$popular_posts = get_posts( array(
	'post_type' => 'post',
	'posts_per_page' => 4,
	'orderby' => 'comment_count'
) );
$categories = get_categories( array(
	'orderby' => 'count',
	'order' => 'DESC',
) );
?>
<div class="cj-navbar-item cj-has-dropdown cj-is-hoverable cj-is-mega cj-navbar-blog-mega-menu <?php echo 'menu-item-'.$item->post_name; ?>">
    <a href="<?php echo $item->url ?>" class="<?php echo implode( ' ', $classes ); ?>" <?php echo $target . ' ' . $title_tag . ' ' . $rel; ?>>
		<?php echo $args->before; ?>
		<?php echo '<span>' . apply_filters( 'the_title', $title, $item->ID ) . '</span>'; ?>
		<?php echo $args->after; ?>
    </a>
    <div class="cj-navbar-dropdown">
        <div class="cj-m-15">
            <div class="cj-columns cj-is-multiline cj-is-mobile">
                <div class="cj-column cj-is-3-widescreen cj-is-2-desktop cj-is-12-tablet cj-is-12-mobile">
                    <p class="cj-title cj-is-5"><?php _e( 'Popular Posts' ) ?></p>
                    <div class="cj-mega-menu-popular-posts">
						<?php foreach( $popular_posts as $key => $popular_post ) {
							$popular_post_info = $helpers->postInfo( $popular_post->ID );
							$featured_image_url = ($popular_post_info['featured_image_url'] !== '') ? $popular_post_info['featured_image_url'] : '//placehold.it/125x125/CCCCCC/666666&text=THUMB';
							if( strpos( $popular_post_info['featured_image_url'], 'includes/images/media/default.png' ) > 0 ) {
								$featured_image_url = '//placehold.it/125x125/CCCCCC/666666&text=THUMB';
							}
							?>
                            <article class="cj-media">
								<?php if( $item->cjwpbldr_navbar_blog_mega_menu_thumbs !== 'hide' ) { ?>
                                    <figure class="cj-media-left">
                                        <p class="cj-image cj-is-64x64">
                                            <img src="<?php echo $featured_image_url; ?>" alt="<?php echo $popular_post_info['post_title'] ?>" width="64" height="64">
                                        </p>
                                    </figure>
								<?php } ?>
                                <div class="cj-media-content">
                                    <div class="cj-content">
                                        <a href="<?php echo $popular_post_info['permalink'] ?>" title="<?php echo $popular_post_info['post_title'] ?>"><strong><?php echo $popular_post_info['post_title'] ?></strong></a>
                                        <br>
										<?php echo $helpers->trimChars( $popular_post_info['post_excerpt'], 60, '...' ); ?>
                                    </div>
                                </div>
                            </article>
						<?php } ?>
                    </div>
                </div>
                <div class="cj-column cj-is-2-widescreen cj-is-2-desktop cj-is-12-tablet cj-is-12-mobile">
                    <p class="cj-title cj-is-5"><?php _e( 'Categories' ) ?></p>
					<?php
					if( ! empty( $categories ) ) {
						echo '<nav class="cj-panel" style="height: 452px; overflow: hidden; overflow-y: scroll;">';
						$count = 0;
						foreach( $categories as $key => $category ) {
							$count ++;
							$active_class = ($count == 1) ? 'cj-is-active' : '';
							$term_link = get_term_link( $category );
							echo '<a href="' . $term_link . '" data-toggle="class" data-toggle-group=".mega-menu-category" data-sibling-class="cj-is-active" data-target="#mega-menu-category-' . $category->slug . '" data-classes="cj-hidden" class="cj-panel-block cj-relative ' . $active_class . '"><span class="cj-absolute-tr cj-m-10 cj-opacity-50 cj-fs-12">(' . $category->count . ')</span> ' . $category->name . '</a>';
						}
						echo '</nav>';
					}
					?>
                </div>
                <div class="cj-column cj-is-7-widescreen cj-is-8-desktop cj-is-12-tablet cj-is-12-mobile">
					<?php if( ! empty( $categories ) ) { ?>
						<?php
						$count = 0;
						foreach( $categories as $key => $category ) {
							$count ++;
							$hidden_class = ($count > 1) ? 'cj-hidden' : '';
							?>
                            <div id="mega-menu-category-<?php echo $category->slug ?>" class="mega-menu-category <?php echo $hidden_class ?>">
                                <p class="cj-title cj-is-5"><?php echo $category->name ?></p>
                                <div class="cj-columns cj-is-multiline cj-is-mobile">
									<?php
									$category_posts = get_posts( array(
										'post_type' => 'post',
										'posts_per_page' => 8,
										'orderby' => 'post_date',
										'order' => 'DESC',
										'category' => $category->term_id,
									) );
									?>
									<?php if( ! empty( $category_posts ) ) { ?>
										<?php foreach( $category_posts as $category_post_key => $category_post ) {
											$category_post_info = $helpers->postInfo( $category_post->ID );
											$category_post_featured_image_url = ($category_post_info['featured_image_url'] !== '') ? $category_post_info['featured_image_url'] : '//placehold.it/125x125/CCCCCC/666666&text=THUMB';
											if( strpos( $category_post_info['featured_image_url'], 'includes/images/media/default.png' ) > 0 ) {
												$category_post_featured_image_url = '//placehold.it/125x125/CCCCCC/666666&text=THUMB';
											}
											?>
                                            <div class="cj-column cj-is-6">
                                                <article class="cj-media">
													<?php if( $item->cjwpbldr_navbar_blog_mega_menu_thumbs !== 'hide' ) { ?>
                                                        <figure class="cj-media-left">
                                                            <p class="cj-image cj-is-48x48">
                                                                <img src="<?php echo $category_post_featured_image_url ?>" alt="<?php echo $category_post_info['post_title']; ?>" width="48" height="48" style="width: 48px; height: 48px;">
                                                            </p>
                                                        </figure>
													<?php } ?>
                                                    <div class="cj-media-content">
                                                        <div class="cj-content">
                                                            <a href="<?php echo $category_post_info['permalink']; ?>" title="<?php echo $category_post_info['post_title']; ?>"><strong><?php echo $category_post_info['post_title']; ?></strong></a>
                                                            <br>
															<?php echo $helpers->trimChars( $category_post_info['post_excerpt'], 60, '...' ); ?>
                                                        </div>
                                                    </div>
                                                </article>
                                            </div>
										<?php } ?>
                                        <div class="cj-column cj-is-12">
                                            <hr class="cj-mt-0 cj-mb-20">
                                            <div class="">
                                                <a href="<?php echo get_category_feed_link( $category->term_id, 'rss2' ) ?>" class="cj-button cj-is-small cj-is-rss cj-is-pulled-right">
                                                    <span class="cj-icon cj-is-small"><i class="fa fa-rss"></i></span>
                                                </a>
                                                <a href="<?php echo get_term_link( $category ) ?>" class="cj-button cj-is-small cj-is-primary">
													<?php _e( 'View All' ) ?>
                                                </a>
                                            </div>
                                        </div>
									<?php } else { ?>
										<?php _e( 'No posts found.' ) ?>
									<?php } ?>
                                </div>
                            </div>
						<?php } ?>
					<?php } ?>

                </div>
            </div>
        </div>
    </div>
</div>