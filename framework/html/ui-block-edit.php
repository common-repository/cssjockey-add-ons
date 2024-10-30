<?php
require_once 'previews/header.php';
if( is_multisite() ) {
	if( ! current_user_can( 'manage_options' ) ) {
		wp_redirect( get_bloginfo('url') );
		die();
	}
} else {
	if( ! current_user_can( 'install_themes' ) ) {
		wp_redirect( get_bloginfo('url') );
		die();
	}
}

$post_info = [];
if( ! isset( $_GET['id'] ) && ! isset( $_GET['jira-id'] ) ) {
	wp_redirect( get_bloginfo('url') );
	die();
}
$block_id = (isset( $_GET['id'] )) ? $_GET['id'] : '';

if( isset( $_GET['jira-id'] ) ) {
	$class_name = sprintf( 'cjwpbldr_uib_%s', $_GET['jira-id'] );
	if( class_exists( $class_name ) ) {
		$post = $this->helpers->getPostByMetaKey( '_ui_block_class_name', $class_name );
		$block_id = $post['ID'];
	}
}

$post_info = $this->helpers->postInfo( $block_id );
if( empty( $post_info ) || ! isset( $post_info['_ui_block_class_name'] ) ) {
	wp_redirect( get_bloginfo('url') );
	die();
}
$class_name = $post_info['_ui_block_class_name'];
$class = $class_name::getInstance();

if( $this->helpers->isLocal() ) {
	$scss_file = $class->info( 'path' ) . '/style.scss';
	if( file_exists( $scss_file ) ) {
		ob_start();
		require $scss_file;
		$scss_content = ob_get_clean();
		$compiled_css = $this->helpers->compileScss( $scss_content );
		update_post_meta( $post_info['ID'], '_block_css', $compiled_css );
	}
	$js_file = $class->info( 'path' ) . '/script.min.js';
	if( file_exists( $js_file ) ) {
		ob_start();
		require $js_file;
		$js_content = ob_get_clean();
		update_post_meta( $post_info['ID'], '_block_js', $js_content );
	}
	$post_info = $this->helpers->postInfo( $block_id );
}
// enable block inline editing
$post_info['inline-editing'] = true;
echo '<div id="cjwpbldr-ui-block-preview-' . $post_info['ID'] . '" class="cjwpbldr-ui-block-preview-container">';
echo $this->helpers->renderUiBlock( $post_info, $class->info );
echo '</div>';

if( isset( $_GET['spacer'] ) ) {
	echo '<div class="cj-section cj-is-' . $_GET['spacer'] . ' cj-is-large"><div class="cj-p-100"></div></div>';
}
if( $this->helpers->isLocal() ) {
	$bg_color = (isset( $_GET['bg'] ) && $_GET['bg'] != '') ? " cj-bg-{$_GET['bg']} cj-has-bg-{$_GET['bg']}" : "";
	if( isset( $_GET['space-below'] ) ) {
		echo '<div class="cj-section cj-is-large ' . $bg_color . '">&nbsp;</div>';
	}
}
wp_footer();
require_once 'ui-block-previews-footer.php';
?>

</body>
</html>