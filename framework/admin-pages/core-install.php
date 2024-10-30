<?php
$template_file_path = $this->helpers->cjwpbldr_content_path . '/templates/' . $_GET['file-name'];
if( file_exists( $template_file_path ) ) {
	$install_data = $this->helpers->getFileContents( $template_file_path );
	$install_data = json_decode( $install_data, 1 );
	if( is_array( $install_data ) && ! empty( $install_data ) ) {
		if($install_data['install_type'] == 'cjwpbldr-template'){
			require_once 'parts/install-template.php';
		}
	}
}
