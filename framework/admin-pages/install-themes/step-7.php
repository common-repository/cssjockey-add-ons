<?php
if( isset( $_POST['submit-step-form'] ) ) {
	foreach( $active_step_data['data'] as $key => $data ) {
		$new_post_data = $data['post_data'];
		$check_existing = $this->helpers->postInfo( $new_post_data['post_name'] );
		if( empty( $check_existing ) ) {
			$new_post_info = $this->helpers->createPost( $new_post_data );
		}
	}
	delete_transient( 'cjwpbldr_install_theme' );
	delete_transient( 'cjwpbldr_install_theme_step' );
	wp_redirect( $this->helpers->site_url );
	die();
}

$this->helpers->enableFrameworkFeature( 'faqs' );
$team_members = $this->helpers->getPosts( [
	'post_type' => 'wpb-faqs',
	'posts_per_page' => 10,
	'post_status' => 'publish',
] );
if( count( $team_members ) > 0 ) {
	delete_transient( 'cjwpbldr_install_theme' );
	delete_transient( 'cjwpbldr_install_theme_step' );
	wp_redirect( $this->helpers->site_url );
	die();
}
?>
<hr class="cj-m-0">
<div class="cj-box cj-br-0 cj-m-0 cj-pv-10 cj-bg-light">
    <p>In this step we will create a few faq items similar to the demo site.</p>
</div>
<form action="" method="post">
    <hr class="cj-m-0">
    <div class="cj-box cj-br-0">
        <div class="cj-level">
            <div class="cj-level-left">
                <div class="cj-level-item">
                    <button name="submit-step-form" type="submit" class="cj-button cj-is-primary">Create FAQs</button>
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
</form>