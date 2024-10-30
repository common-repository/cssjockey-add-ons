<?php require_once 'previews/header.php';
$current_template_id = $this->helpers->getTemplateId();
$this->helpers->syncTemplateBlocks( $current_template_id );
if( $current_template_id == 0 ) {
	wp_redirect( admin_url( 'edit.php?post_type=cjwpbldr-templates' ) );
	wp_die();
}
?>
<?php wp_body_open(); ?>
<div class="cssjockey-ui">
    <div id="cjwpbldr-template-<?php echo $current_template_id; ?>" data-template-id="<?php echo $current_template_id; ?>" class="cjwpbldr-template">
        <div class="cjwpbldr-template-blocks-content">
			<?php
			global $current_user;
			$template_info = [];
			$user_role = $this->helpers->userRole( $current_user->ID );
			$template_ui_blocks = [];
			$template_info = $this->helpers->postInfo( $current_template_id );

			$template_ui_blocks = (isset( $template_info['_cjwpbldr_ui_blocks'] ) && is_array( $template_info['_cjwpbldr_ui_blocks'] ) && ! empty( $template_info['_cjwpbldr_ui_blocks'] )) ? $template_info['_cjwpbldr_ui_blocks'] : [];

			if( ! empty( $template_ui_blocks ) ) {
				foreach( $template_ui_blocks as $sort_order => $block ) {
					$block_post_info = $this->helpers->postInfo( $block['ID'] );
					$show_block = false;
					$admin_message = '';
					$block_roles = (isset( $block_post_info['_block_settings_block_visibility'] ) && ! empty( $block_post_info['_block_settings_block_visibility'] )) ? $block_post_info['_block_settings_block_visibility'] : ['everyone'];

					if( empty( $block_roles ) ) {
						$show_block = true;
					}

					if( in_array( 'everyone', $block_roles ) ) {
						$show_block = true;
					} else {
						// visitor
						if( ! $user_role && in_array( 'visitors', $block_roles ) ) {
							$show_block = true;
						}
						// other roles
						if( is_array( $user_role ) && ! empty( array_intersect( $user_role, $block_roles ) ) ) {
							$show_block = true;
						}
					}

					if( current_user_can( 'manage_options' ) ) {
						if( ! $show_block ) {
							$admin_message = $this->helpers->alert( 'info cj-mb-0 cj-p-10 cj-br-0 cj-fs-12 cj-text-center', sprintf( __( '<b>Admin Alert: </b> This block is only visible to %s.', 'wp-builder-locale' ), implode( ', ', $block_roles ) ) );
						}
						$show_block = true;
					}

					if( $show_block ) {
						$class_name = (isset( $block_post_info['_ui_block_class_name'] )) ? $block_post_info['_ui_block_class_name'] : '';
						if( class_exists( $class_name ) ) {
							$block_class = $class_name::getInstance();
							echo $admin_message;
							if( isset( $_GET['show-screenshots'] ) && $_GET['show-screenshots'] == 1 ) {
								echo '<div class="cj-container">';
								echo '<img src="' . $block_class->info['screenshot'] . '" alt="block screenshot" width="100%">';
								echo '</div>';
							} else {
								echo $block_class->render( $block_post_info );
							}
						}
					}
				}

				/*if( isset( $_GET['disable-on-edit'] ) && $_GET['disable-on-edit'] == 1 ) {
                    echo '<a class="cj-overlay cj-opacity-40"></a>';
				}*/
			}
			?>
			<?php if( empty( $template_ui_blocks ) ) { ?>
				<?php if( current_user_can( 'manage_options' ) ) { ?>
                    <div class="cssjockey-ui">
                        <div class="cj-notification cj-is-light cj-mb-0 cj-br-0">
                            <div class="cj-container cj-text-center">
								<?php echo sprintf( __( 'No ui blocks assigned to this template. <a href="%s" target="_blank" title="Edit Template">(Edit Template)</a>', 'wp-builder-locale' ), admin_url( 'post.php?post=' . $template_info['ID'] . '&action=edit' ) ) ?>
                            </div>
                        </div>
                        <hr class="cj-m-0">
                    </div>
				<?php } else { ?>
                    <div class="cssjockey-ui">
                        <div class="cj-notification cj-is-light cj-mb-0 cj-br-0">
                            <div class="cj-container cj-text-center">
								<?php _e( 'This section is under construction.', 'wp-builder-locale' ) ?>
                            </div>
                        </div>
                        <hr class="cj-m-0">
                    </div>
				<?php } ?>
			<?php } ?>
        </div><!-- template preview -->
    </div>

</div>
<?php require_once 'previews/footer.php'; ?>
