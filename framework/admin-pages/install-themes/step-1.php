<?php
if( isset( $_POST['install-site-templates'] ) ) {
	foreach( $active_step_data['data'] as $key => $template_info ) {
		$check_existing = $this->helpers->postInfo( $template_info['post_name'] );
		if( empty( $check_existing ) ) {
			$new_post_data = $template_info;
			$new_post_data['post_title'] = $template_info['post_title_new'];
			$new_post_data['_cjwpbldr_ui_blocks'] = [];
			$new_post_data['_cjwpbldr_ui_blocks_preview'] = [];
			if( ! isset( $template_info['_cjwpbldr_ui_blocks'] ) ) {
				$template_info['_cjwpbldr_ui_blocks'] = [];
			}
			foreach( $template_info['_cjwpbldr_ui_blocks'] as $index => $block_info ) {
				$installed_block_data = $this->helpers->getPostByMetaKey( '_ui_block_class_name', $block_info['info']['class_name'] );
				if( ! $block_info['_core_ui_block'] ) {
					$installed_block_data = $this->helpers->cloneUiBlock( $installed_block_data['ID'] );
				}
				$block_class = $installed_block_data['_ui_block_class_name']::getInstance();
				$block_class_info = $block_class->info;
				if( isset( $installed_block_data['_ui_block_class_name'] ) ) {
					if( is_array( $installed_block_data ) ) {
						$new_post_data['_cjwpbldr_ui_blocks'][ $index ]['ID'] = $installed_block_data['ID'];
						foreach( $block_info['settings'] as $settings_key => $settings_value ) {
							update_post_meta( $installed_block_data['ID'], $settings_key, $settings_value );
						}
					}
				}
				$new_post_data['_cjwpbldr_ui_blocks'][ $index ]['info']['name'] = $block_class_info['name'];
				if( isset( $block_class_info['title'] ) ) {
					$new_post_data['_cjwpbldr_ui_blocks'][ $index ]['info']['title'] = $block_class_info['title'];
				}
				$new_post_data['_cjwpbldr_ui_blocks'][ $index ]['info']['description'] = $block_class_info['description'];
				$new_post_data['_cjwpbldr_ui_blocks'][ $index ]['info']['group'] = $block_class_info['group'];
				$new_post_data['_cjwpbldr_ui_blocks'][ $index ]['info']['version'] = $block_class_info['version'];
				$new_post_data['_cjwpbldr_ui_blocks'][ $index ]['info']['id'] = $block_class_info['id'];
				$new_post_data['_cjwpbldr_ui_blocks'][ $index ]['info']['class_name'] = $block_class_info['class_name'];
				$new_post_data['_cjwpbldr_ui_blocks'][ $index ]['info']['screenshot'] = $block_class_info['screenshot'];
			}
			$new_post_data['_cjwpbldr_ui_blocks_preview'] = $new_post_data['_cjwpbldr_ui_blocks'];
			$new_post_info = $this->helpers->createPost( $new_post_data );
		}
	}
	set_transient( 'cjwpbldr_install_theme_step', 2, 84600 );
	wp_redirect( $this->helpers->callbackUrl( 'core-install-themes' ) );
	die();
}
?>
<hr class="cj-m-0">
<div class="cj-box cj-br-0 cj-m-0 cj-pv-10 cj-bg-light">
    <p>In this step we will create WP Builder Templates similar to our demo site.</p>
</div>
<hr class="cj-m-0">
<?php
foreach( $active_step_data['data'] as $key => $value ) { ?>
    <div class="cj-box cj-m-0 cj-br-0 cj-pv-10">
        <b><?php echo $value['post_title'] ?></b>
    </div>
    <hr class="cj-m-0">
<?php } ?>
<div class="cj-box cj-br-0">
    <div class="cj-level">
        <div class="cj-level-left">
            <div class="cj-level-item">
                <form action="" method="post">
                    <button name="install-site-templates" type="submit" class="cj-button cj-is-primary">Create Templates</button>
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
