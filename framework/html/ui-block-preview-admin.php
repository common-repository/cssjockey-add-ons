<?php
$admin_preview = get_option( 'cjwpbldr-block-admin-preview' );

$url = rest_url( 'cjwpbldr/builder/ui-blocks/form/get' );

$block_info = $this->helpers->postInfo( $post->ID );
$block_class = $block_info['_ui_block_class_name']::getInstance();
$block_class_info = $block_class->info;
$block_settings_options = [];
$block_core_options = [];
$block_custom_options = [];

$all_options = $this->helpers->getBlockFormOptions( $block_info );

foreach( $all_options as $key => $option ) {
	$saved_value = (isset( $block_info[ $option['id'] ] )) ? $block_info[ $option['id'] ] : '';
	$all_options[ $key ] = $this->helpers->sanitizeFormOption( $option, $saved_value );
}

if( ! empty( $all_options ) ) {
	foreach( $all_options as $key => $option ) {
		if( isset( $option['form_tab'] ) && $option['form_tab'] == 'core-settings' ) {
			$block_core_options[] = $option;
		} else if( isset( $option['form_tab'] ) && $option['form_tab'] == 'custom-settings' ) {
			$block_custom_options[] = $option;
		} else {
			$block_settings_options[] = $option;
		}
	}
}
$json_settings_form = $this->helpers->jsonFormOptions( $block_settings_options, $block_info );
$json_core_form = $this->helpers->jsonFormOptions( $block_core_options, $block_info );
$json_custom_form = $this->helpers->jsonFormOptions( $block_custom_options, $block_info );

$reset_link = $this->helpers->queryString( site_url( '/' ) ) . "cjwpbldr-action=reset-ui-block&id={$post->ID}";
$preview_link = $this->helpers->queryString( site_url( '/' ) ) . "cjwpbldr-action=edit-ui-block&id={$post->ID}";
$hide_admin_preview_link = $this->helpers->queryString( site_url( '/' ) ) . "cjwpbldr-action=hide-block-admin-preview&id={$post->ID}";
$show_admin_preview_link = $this->helpers->queryString( site_url( '/' ) ) . "cjwpbldr-action=show-block-admin-preview&id={$post->ID}";

?>
<div class="cssjockey-ui">
	<?php if( $admin_preview == 'show' ) { ?>
        <iframe id="admin-block-preview-iframe" src="<?php echo $this->helpers->queryString( get_bloginfo('url') ) . 'cjwpbldr-action=preview-ui-block&id=' . $post->ID . '&preview-in-admin=1' ?>" width="100%" style="min-height: 500px; max-height: 500px;" class="cjwpbldr-iframe-resize" frameborder="0"></iframe>
        <hr class="cj-mv-20">
	<?php } ?>
    <div class="cj-mt-20">
        <div class="cj-columns cj-is-multiline cj-is-mobile">
            <div class="cj-column cj-is-8-fullhd cj-is-8-widescreen cj-is-12-desktop cj-is-12-tablet cj-is-12-mobile">
                <div class="cj-message cj-is-dark">
                    <div class="cj-message-header cj-has-bg-dark">
                        <p><?php _e( 'Block Settings (Content)', 'wp-builder-locale' ) ?></p>
                    </div>
                    <div class="cj-message-body cj-p-0">
                        <div class="render-vue-form cjwpbldr-admin-edit-block-form">
                            <vue-form options='<?php echo $json_settings_form; ?>'></vue-form>
                        </div>
                    </div>
                </div>

                <div class="cj-message cj-is-dark">
                    <div class="cj-message-header cj-has-bg-dark">
                        <p><?php _e( 'Block Settings (Custom CSS/JS)', 'wp-builder-locale' ) ?></p>
                    </div>
                    <div class="cj-message-body cj-p-0">
                        <div class="render-vue-form cjwpbldr-admin-edit-block-form">
                            <vue-form options='<?php echo $json_custom_form; ?>'></vue-form>
                        </div>
                    </div>
                </div>

            </div>
            <div class="cj-column cj-is-4-fullhd cj-is-4-widescreen cj-is-12-desktop cj-is-12-tablet cj-is-12-mobile">
                <div class="cj-message cj-is-dark">
                    <div class="cj-message-header cj-has-bg-dark">
                        <p><?php _e( 'Block Details', 'wp-builder-locale' ) ?></p>
                    </div>
                    <div class="cj-message-body">
                        <div>
                            <div class="cj-field cj-has-addons">
                                <div class="cj-control cj-is-expanded">
                                    <a href="#" data-clipboard-text="[cjwpbldr_block id='<?php echo $post->ID; ?>']" class="cj-button cj-is-small cj-is-fullwidth"><span class="cj-icon cj-fs-12"><i class="fa fa-copy"></i></span> <span><?php _e( 'Copy Shortcode', 'wp-builder-locale' ) ?></span></a>
                                </div>
                                <div class="cj-control cj-is-expanded">
                                    <a href="<?php echo $preview_link; ?>" target="_blank" class="cj-button cj-is-small cj-is-fullwidth"><span class="cj-icon cj-fs-12"><i class="fa fa-eye"></i></span> <span><?php _e( 'Edit on Frontend', 'wp-builder-locale' ) ?></span></a>
                                </div>
                                <div class="cj-control cj-is-expanded">
                                    <a href="<?php echo $reset_link; ?>" class="cj-button cj-is-small cj-is-fullwidth cj-is-danger"><span class="cj-icon cj-fs-12"><i class="fa fa-sync"></i></span> <span><?php _e( 'Reset Settings', 'wp-builder-locale' ) ?></span></a>
                                </div>
                            </div>
                        </div>
                        <div class="cj-box cj-p-10">
							<?php if( $admin_preview == 'show' ) { ?>
                                <a href="<?php echo $hide_admin_preview_link; ?>" class="cj-button cj-is-light cj-is-small cj-is-pulled-right"><?php _e( 'Hide Admin Preview', 'wp-builder-locale' ) ?></a>
							<?php } else { ?>
                                <a href="<?php echo $show_admin_preview_link; ?>" class="cj-button cj-is-light cj-is-small cj-is-pulled-right"><?php _e( 'Show Admin Preview', 'wp-builder-locale' ) ?></a>
							<?php } ?>
                            <div class="cj-mb-10 cj-fs-14"><b><?php _e( 'SKU :', 'wp-builder-locale' ) ?></b> <?php echo str_replace( 'cjwpbldr_uib_', '', $block_class_info['id'] ); ?></div>
                            <div class="cj-mb-10 cj-fs-14"><b><?php _e( 'Version :', 'wp-builder-locale' ) ?></b> <?php echo $block_class_info['version']; ?></div>
                            <div class="cj-fs-14">
                                <b><?php _e( 'Installation Path :', 'wp-builder-locale' ) ?></b> <br>
                                <span><?php echo str_replace( ABSPATH, '', $block_class_info['path'] ); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="cj-message cj-is-dark">
                    <div class="cj-message-header cj-has-bg-dark">
                        <p><?php _e( 'Block Settings (Display)', 'wp-builder-locale' ) ?></p>
                    </div>
                    <div class="cj-message-body cj-p-0">
                        <div class="render-vue-form cjwpbldr-admin-edit-block-form">
                            <vue-form :options='<?php echo $json_core_form; ?>'></vue-form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>