<?php
header( "Content-type: text/javascript; charset=utf-8" );
$root_dir = explode( 'wp-content', dirname( __FILE__ ) );
require_once $root_dir[0] . '/wp-load.php';
$helpers = cjwpbldr_helpers::getInstance();
$js_files = get_option( 'cjwpbldr_combined_scripts', true );
$js = '';
if( is_array( $js_files ) && ! empty( $js_files ) ) {
	foreach( $js_files as $key => $file ) {
		$js .= file_get_contents( $file );
	}
}
if( $js != '' ) {
	if( ! $helpers->isLocal() ) {
		$expires = 60 * 60 * 24; // how long to cache in secs..
		header( "Pragma: public" );
		header( "Cache-Control: maxage=" . $expires );
		header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', time() + $expires ) . ' GMT' );
	}
	echo $js;
}
