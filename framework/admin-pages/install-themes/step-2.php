<?php
if( isset( $_POST['submit-step-form'] ) ) {
	foreach( $active_step_data['data'] as $key => $data ) {
		$new_post_data = $data['post_data'];
		$check_existing = $this->helpers->postInfo( $new_post_data['post_name'] );
		if( empty( $check_existing ) ) {
			if( isset( $new_post_data['template_export_id'] ) ) {
				$template_post_info = $this->helpers->getPostByMetaKey( 'export_id', $new_post_data['template_export_id'] );
				if( isset( $template_post_info['ID'] ) ) {
					$new_post_data['_cjwpbldr_use_template_singular'] = $template_post_info['ID'];
				}
			}
			if( $new_post_data['post_content'] == '' ) {
				$new_post_data['post_content'] = 'Update content for: ' . $new_post_data['post_title'];
			}
			if( $new_post_data['post_excerpt'] == '' ) {
				$new_post_data['post_excerpt'] = 'Update content for: ' . $new_post_data['post_title'];
			}
			$this->helpers->createPost( $new_post_data );
		}
	}
	set_transient( 'cjwpbldr_install_theme_step', 3, 84600 );
	wp_redirect( $this->helpers->callbackUrl( 'core-install-themes' ) );
	die();
}
?>
<hr class="cj-m-0">
<div class="cj-box cj-br-0 cj-m-0 cj-pv-10 cj-bg-light">
    <p>In this step we will create a few pages similar to the demo site.</p>
</div>
<?php
foreach( $active_step_data['data'] as $key => $data ) { ?>
    <hr class="cj-m-0">
    <div class="cj-box cj-br-0 cj-m-0 cj-pv-10">
        <b><?php echo $data['post_data']['post_title'] ?></b>
    </div>
<?php } ?>
<hr class="cj-m-0">
<div class="cj-box cj-br-0">
    <div class="cj-level">
        <div class="cj-level-left">
            <div class="cj-level-item">
                <form action="" method="post">
                    <button name="submit-step-form" type="submit" class="cj-button cj-is-primary">Create Pages</button>
                </form>
            </div>
            <div class="cj-level-item">
                <a href="<?php echo admin_url( 'admin.php?page=cjwpbldr&callback=core-install-themes&cjwpbldr-action=skip-installer-step&count=' . 4 ); ?>" class="cj-button cj-is-light">Skip this step</a>
            </div>
        </div>
        <div class="cj-level-right">
            <div class="cj-level-item">
                <a href="<?php echo admin_url( 'admin.php?page=cjwpbldr&callback=core-install-themes&cjwpbldr-action=cancel-installer' ); ?>" class="cj-button cj-is-dark">Cancel</a>
            </div>
        </div>
    </div>
</div>