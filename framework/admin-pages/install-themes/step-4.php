<?php
if( isset( $_POST['submit-step-form'] ) ) {
	foreach( $active_step_data['data'] as $key => $data ) {
		$new_post_data = $data['post_data'];
		$check_existing = $this->helpers->postInfo( $new_post_data['post_name'] );
		if( empty( $check_existing ) ) {
			$new_post_info = $this->helpers->createPost( $new_post_data );
			if( isset( $_POST['import-images'] ) && $_POST['import-images'] == 'yes' ) {
				$image_name = sha1( basename( $new_post_data['featured_image_url'] ) ) . '.webp';
				$this->helpers->setPostFeaturedImageFromUrl( $new_post_info['ID'], $new_post_data['featured_image_url'], $image_name );
			}
		}
	}
	set_transient( 'cjwpbldr_install_theme_step', 5, 84600 );
	wp_redirect( $this->helpers->callbackUrl( 'core-install-themes' ) );
	die();
}
?>
<hr class="cj-m-0">
<div class="cj-box cj-br-0 cj-m-0 cj-pv-10 cj-bg-light">
    <p>In this step we will create a few portfolio items similar to the demo site.</p>
</div>
<form action="" method="post">
    <hr class="cj-m-0">
    <!--<div class="cj-box cj-br-0 cj-m-0">
        <div class="cj-field cj-m-0">
            <p class="cj-control">
                <input id="import-images" name="import-images" type="checkbox" value="yes" class="cj-is-checkradio">
                <label for="import-images">Import Images? This may take a while.</label>
            </p>
        </div>
    </div>
    <hr class="cj-m-0">-->
    <div class="cj-box cj-br-0 cj-m-0">
        <div class="cj-level">
            <div class="cj-level-left">
                <div class="cj-level-item">
                    <button name="submit-step-form" type="submit" class="cj-button cj-is-primary">Create Portfolio Items</button>
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