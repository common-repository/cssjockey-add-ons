<?php
$ui_blocks_to_install = $active_step_data['data'];
$ui_blocks_count_array = $ui_blocks_to_install;
$license_required = false;
$license_key = get_transient( '_wp_builder_pro_key' );
$installed_products = $this->helpers->installedProducts( 'all', true );

foreach( $installed_products as $key => $installed_product ) {
	if( isset( $ui_blocks_count_array[ $key ] ) ) {
		unset( $ui_blocks_count_array[ $key ] );
	}
	if( count( $ui_blocks_count_array ) == 0 ) {
		set_transient( 'cjwpbldr_install_theme_step', 1, 84600 );
		wp_redirect( $this->helpers->callbackUrl( 'core-install-themes' ) );
		die();
	}
}
?>
    <hr class="cj-m-0">
    <div class="cj-box cj-br-0 cj-m-0 cj-pv-10 cj-bg-light">
        <p>
            In this step we will download and install all UI Blocks required to create the website.
            <br>
            This page will refresh automatically once all UI Blocks are installed, in case it doesn't you can refresh this page.
        </p>
    </div>
    <hr class="cj-m-0">
<?php if( count( $ui_blocks_to_install ) > 0 ) { ?>
	<?php foreach( $ui_blocks_to_install as $key => $block_to_install ) { ?>
        <div class="cj-box cj-pv-10 cj-ph-20 cj-br-0 cj-no-box-shadow cj-m-0">
            <div class="cj-level">
                <div class="cj-level-left">
                    <div class="cj-level-item">
						<?php echo $block_to_install['slug']; ?>
                    </div>
                    <div class="cj-level-item">
						<?php if( isset( $installed_products[ $block_to_install['slug'] ] ) ) {
							echo '<span class="cj-icon cj-is-small cj-mr-10"><i class="fa fa-check cj-color-success"></i></span>  <span>Installed</span>';
						} else {
							echo '<span v-cloak class="vue-process-theme-block" data-license-key="' . $license_key . '" data-slug="' . $block_to_install['slug'] . '"><span v-html="msg"></span></span>';
						} ?>
                    </div>
                </div>
                <div class="cj-level-right">
                    <div class="cj-level-item">&nbsp;</div>
                </div>
            </div>
        </div>
        <hr class="cj-m-0">
	<?php } ?>
<?php } ?>