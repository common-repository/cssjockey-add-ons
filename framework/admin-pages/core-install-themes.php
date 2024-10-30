<?php
//delete_transient( 'cjwpbldr_install_theme_step' );
$theme_data = get_transient( 'cjwpbldr_install_theme' );

if( isset( $_GET['cjwpbldr-action'] ) && $_GET['cjwpbldr-action'] == 'cancel-installer' ) {
	delete_transient( 'cjwpbldr_install_theme' );
	delete_transient( 'cjwpbldr_install_theme_step' );
	wp_redirect( $this->helpers->callbackUrl( 'core-welcome' ) );
	die();
}

if( isset( $_GET['cjwpbldr-action'] ) && $_GET['cjwpbldr-action'] == 'skip-installer-step' && isset( $_GET['count'] ) && $_GET['count'] != '' ) {
	$next_step = (int) $_GET['count'] + 1;
	if( $next_step > 7 ) {
		delete_transient( 'cjwpbldr_install_theme' );
		delete_transient( 'cjwpbldr_install_theme_step' );
		wp_redirect( $this->helpers->site_url );
	} else {
		set_transient( 'cjwpbldr_install_theme_step', $next_step, 84600 );
		wp_redirect( $this->helpers->callbackUrl( 'core-install-themes' ) );
	}
	die();
}

$install_theme_step = get_transient( 'cjwpbldr_install_theme_step' );
$active_step = ( ! $install_theme_step) ? 0 : (int) $install_theme_step;

$active_step_data = $theme_data['steps'][ $active_step ];

if( ! isset( $theme_data['steps'][ $active_step ] ) ) {
	set_transient( 'cjwpbldr_install_theme_step', $active_step + 1, 84600 );
	wp_redirect( $this->helpers->callbackUrl( 'core-install-themes' ) );
	die();
}
?>
<div class="cj-content">
    <div class="cj-box cj-br-0 cj-m-0">
        <div class="cj-is-pulled-right"><?php _e( 'Step: ', 'wp-builder-locale' ) ?> <?php echo (int) $active_step ?> / <?php echo count( $theme_data['steps'] ); ?></div>
        <h3 class="cj-m-0"><?php _e( 'Demo Site Installer', 'wp-builder-locale' ) ?> <span class="cj-ph-5">&rarr;</span> <span class="cj-opacity-70"><?php echo $active_step_data['step_name'] ?></span></h3>
    </div>
	<?php require_once sprintf( '%s/install-themes/step-%s.php', dirname( __FILE__ ), $active_step ); ?>
</div>

