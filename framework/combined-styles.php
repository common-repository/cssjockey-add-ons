<?php
header( "Content-type: text/css; charset=utf-8" );
$root_dir = explode( 'wp-content', dirname( __FILE__ ) );
require_once $root_dir[0] . '/wp-load.php';
$css_files = get_option( 'cjwpbldr_combined_styles' );
if( is_array( $css_files ) && ! empty( $css_files ) ) {
	$expires = 60 * 60 * 24; // how long to cache in secs..
	header( "Pragma: public" );
	header( "Cache-Control: maxage=" . $expires );
	header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', time() + $expires ) . ' GMT' );
	$css = '';
	foreach( $css_files as $key => $file ) {
		$css .= file_get_contents( $file );
	}
	// comments
	$css = preg_replace( '!/\*.*?\*/!s', '', $css );
	$css = preg_replace( '/\n\s*\n/', "\n", $css );

	// space
	$css = preg_replace( '/[\n\r \t]/', ' ', $css );
	$css = preg_replace( '/ +/', ' ', $css );
	$css = preg_replace( '/ ?([,:;{}]) ?/', '$1', $css );

	// trailing;
	$css = preg_replace( '/;}/', '}', $css );
	echo $css;
}