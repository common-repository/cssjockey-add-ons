<?php
$output = [];
if( isset( $_GET['blocks'] ) && $_GET['blocks'] !== '' ) {
	$block_ids = explode( ',', $_GET['blocks'] );
	if( is_array( $block_ids ) && ! empty( $block_ids ) ) {
		foreach( $block_ids as $key => $block_id ) {
			$class_name = get_post_meta( $block_id, '_ui_block_class_name', true );
			if( $class_name !== '' ) {
				$class_instance = $class_name::getInstance();
				$block_path = $class_instance->info( 'path' );
				$file = $block_path . '/script.min.js';
				if( file_exists( $file ) ) {
					ob_start();
					require $file;
					$output[] = ob_get_clean();
				}
			}
		}
	}
}
if( ! $this->helpers->isLocal() ) {
	$expires = (60 * 60 * 24) * 7; // how long to cache in secs..
	header( "Pragma: public" );
	header( "Cache-Control: maxage=" . $expires );
	header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', time() + $expires ) . ' GMT' );
}
header( "Content-type: text/javascript;" );
if( ! empty( $output ) ) {
	echo implode( '', $output );
}