<?php
if( isset( $_POST['submit-step-form'] ) ) {
	foreach( $active_step_data['data'] as $key => $value ) {
		if( $key == 'page_on_front' ) {
			$custom_home_page = $this->helpers->getPostByMetaKey( 'export_id', $value );
			if( ! empty( $custom_home_page ) ) {
				update_option( 'page_on_front', $custom_home_page['ID'] );
				update_option( 'show_on_front', 'page' );
			}
		}
		if( $key == 'page_for_posts' ) {
			$custom_blog_page = $this->helpers->getPostByMetaKey( 'export_id', $value );
			if( ! empty( $custom_blog_page ) ) {
				update_option( 'page_for_posts', $custom_blog_page['ID'] );
				update_option( 'show_on_front', 'page' );
			}
		}
	}
	set_transient( 'cjwpbldr_install_theme_step', 4, 84600 );
	wp_redirect( $this->helpers->callbackUrl( 'core-install-themes' ) );
	die();
}
?>
<hr class="cj-m-0">
<div class="cj-box cj-br-0 cj-m-0 cj-pv-10 cj-bg-light">
    <p>In this step we will set custom homepage and page for posts as per the demo site.</p>
</div>

<?php
$custom_home_page = [];
$custom_blog_page = [];

$page_on_front = get_option( 'page_on_front' );
$page_for_posts = get_option( 'page_for_posts' );

foreach( $active_step_data['data'] as $key => $value ) {
	if( $key == 'page_on_front' ) {
		$custom_home_page = $this->helpers->getPostByMetaKey( 'export_id', $value );
	}
	if( $key == 'page_for_posts' ) {
		$custom_blog_page = $this->helpers->getPostByMetaKey( 'export_id', $value );
	}
}
?>

<?php if( ! empty( $custom_home_page ) ) { ?>
    <hr class="cj-m-0">
    <div class="cj-box cj-br-0 cj-m-0 cj-pv-10">
        <b>Set page on front: </b><?php echo $custom_home_page['post_title'] ?>
    </div>
<?php } ?>

<?php if( ! empty( $custom_blog_page ) ) { ?>
    <hr class="cj-m-0">
    <div class="cj-box cj-br-0 cj-m-0 cj-pv-10">
        <b>Set page for posts: </b><?php echo $custom_blog_page['post_title'] ?>
    </div>
<?php } ?>

<hr class="cj-m-0">
<div class="cj-box cj-br-0">
    <div class="cj-level">
        <div class="cj-level-left">
            <div class="cj-level-item">
                <form action="" method="post">
                    <button name="submit-step-form" type="submit" class="cj-button cj-is-primary">Set Custom Pages</button>
                </form>
            </div>
            <div class="cj-level-item">
                <a href="<?php echo admin_url( 'admin.php?page=cjwpbldr&callback=core-install-themes&cjwpbldr-action=skip-installer-step&count=' . $active_step ); ?>" class="cj-button cj-is-light">Skip this step</a>
            </div>
        </div>
        <div class="cj-level-right">
            <div class="cj-level-item">
                <a href="<?php echo admin_url( 'admin.php?page=cjwpbldr&callback=core-install-themes&cjwpbldr-action=cancel-installer' ); ?>" class="cj-button cj-is-dark">Cancel</a>
            </div>
        </div>
    </div>
</div>
