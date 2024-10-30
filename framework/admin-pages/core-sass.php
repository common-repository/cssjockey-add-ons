<?php
$message = '';
if( isset( $_POST['compile_sass'] ) ) {
	$vars_to_save = $_POST;
	unset( $vars_to_save['compile_sass'] );
	$this->helpers->updateOption( 'core_sass_variables', $vars_to_save );
	// $this->helpers->compileCoreScssFiles();
	// $this->helpers->compileBlocksScss();
	// $this->helpers->compileAddonsScss();
	update_option( 'cjwpbldr_ajax_compile_scss', true );
	$message = $this->helpers->alert( 'success', __( 'UI Variables saved successfully.', 'wp-builder-locale' ) );
	/*$compile_scss_url = $this->helpers->callbackUrl( 'core-sass' );
	wp_redirect( $compile_scss_url );
	die();*/
}

$sass_variables = $this->helpers->userScssVariables();

$form_options = [];
/*$form_options['variables-heading'] = array(
	'id' => 'sass-variables-heading',
	'type' => 'sub-heading',
	'default' => __( 'Sass UI Variables', 'wp-builder-locale' ),
	'options' => '', // array in case of dropdown, checkbox and radio buttons
);*/

foreach( $sass_variables as $key => $option ) {
	$form_options[ $option['id'] ] = $option;
}

$reset_button = '<a href="#" class="cj-button cj-is-medium cj-is-dark cj-ml-10">' . __( 'Restore Defaults', 'wp-builder-locale' ) . '</a>';

$form_options['submit'] = [
	'id' => 'compile_sass',
	'type' => 'submit',
	'suffix' => $reset_button,
	'default' => __( 'Update UI Styles', 'wp-builder-locale' ),
	'options' => '', // array in case of dropdown, checkbox and radio buttons
];

$env_messages = '';
$env_messages .= '<ul class="cj-m-0">';

// wp-content folder
if( is_writable( $this->helpers->upload_dir['basedir'] ) ) {
	$env_messages .= '<li class="cj-mb-10"><i class="fa fa-check-circle cj-color-success cj-mr-10 cj-fs-12"></i>' . sprintf( __( '<code>%s</code> is writable.', 'wp-builder-locale' ), str_replace( ABSPATH, '/', $this->helpers->upload_dir['basedir'] ) ) . '</li>';
} else {
	$env_messages .= '<li class="cj-mb-10"><i class="fa fa-times-circle cj-color-danger cj-mr-10 cj-fs-12"></i>' . sprintf( __( '<code>%s</code> not writable.', 'wp-builder-locale' ), str_replace( ABSPATH, '/', $this->helpers->upload_dir['basedir'] ) ) . '</li>';
}

// wp-content folder
if( is_dir( $this->helpers->cjwpbldr_content_path ) ) {
	$env_messages .= '<li class="cj-mb-10"><i class="fa fa-check-circle cj-color-success cj-mr-10 cj-fs-12"></i>' . sprintf( __( '<code>%s</code> found.', 'wp-builder-locale' ), str_replace( ABSPATH, '/', $this->helpers->cjwpbldr_content_path ) ) . '</li>';
} else {
	$env_messages .= '<li class="cj-mb-10"><i class="fa fa-times-circle cj-color-danger cj-mr-10 cj-fs-12"></i>' . sprintf( __( '<code>%s</code> not found.', 'wp-builder-locale' ), str_replace( ABSPATH, '/', $this->helpers->cjwpbldr_content_path ) ) . '</li>';
}

// wp-content folder
if( is_writable( $this->helpers->cjwpbldr_content_path . '/ui' ) ) {
	$env_messages .= '<li class="cj-mb-10"><i class="fa fa-check-circle cj-color-success cj-mr-10 cj-fs-12"></i>' . sprintf( __( '<code>%s</code> is writable.', 'wp-builder-locale' ), str_replace( ABSPATH, '/', $this->helpers->cjwpbldr_content_path . '/ui' ) ) . '</li>';
} else {
	$env_messages .= '<li class="cj-mb-10"><i class="fa fa-times-circle cj-color-danger cj-mr-10 cj-fs-12"></i>' . sprintf( __( '<code>%s</code> not writable.', 'wp-builder-locale' ), str_replace( ABSPATH, '/', $this->helpers->cjwpbldr_content_path . '/ui' ) ) . '</li>';
}

// wp-builder/ui/ui-base.min.css
if( file_exists( $this->helpers->cjwpbldr_content_path . '/ui/ui-base.min.css' ) ) {
	$env_messages .= '<li class="cj-mb-10"><i class="fa fa-check-circle cj-color-success cj-mr-10 cj-fs-12"></i>' . sprintf( __( '<code>%s</code> found.', 'wp-builder-locale' ), str_replace( ABSPATH, '/', $this->helpers->cjwpbldr_content_path . '/ui/ui-base.min.css' ) ) . '</li>';
} else {
	$env_messages .= '<li class="cj-mb-10"><i class="fa fa-times-circle cj-color-danger cj-mr-10 cj-fs-12"></i>' . sprintf( __( '<code>%s</code> not found. Submit the form below to compile the sass files.', 'wp-builder-locale' ), str_replace( ABSPATH, '/', $this->helpers->cjwpbldr_content_path . '/ui/ui-base.min.css' ) ) . '</li>';
}

// wp-builder/ui/helpers.min.css
if( file_exists( $this->helpers->cjwpbldr_content_path . '/ui/helpers.min.css' ) ) {
	$env_messages .= '<li class="cj-mb-5"><i class="fa fa-check-circle cj-color-success cj-mr-10 cj-fs-12"></i>' . sprintf( __( '<code>%s</code> found.', 'wp-builder-locale' ), str_replace( ABSPATH, '/', $this->helpers->cjwpbldr_content_path . '/ui/helpers.min.css' ) ) . '</li>';
} else {
	$env_messages .= '<li class"cj-mb-5"><i class="fa fa-times-circle cj-color-danger cj-mr-10 cj-fs-12"></i>' . sprintf( __( '<code>%s</code> not found. Submit the form below to compile the sass files.', 'wp-builder-locale' ), str_replace( ABSPATH, '/', $this->helpers->cjwpbldr_content_path . '/ui/helpers.min.css' ) ) . '</li>';
}
$env_messages .= '</ul>';

$env_options = [
	[
		'id' => 'env-heading',
		'type' => 'sub-heading',
		'default' => __( 'Environment Tests', 'wp-builder-locale' ),
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	],
	[
		'id' => 'env-info',
		'label' => __( 'Test Results', 'wp-builder-locale' ),
		'label_suffix' => __( 'WP Builder requires access to create and remove files in <code>wp-content</code> directory. Please fix directory permissions if any of the tests fails.', 'wp-builder-locale' ),
		'type' => 'info',
		'default' => $env_messages,
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	]
];
echo $message;
$saved_variables = $this->helpers->getOption( 'core_sass_variables' );
echo '<div class="cj-mb-30">';
echo $this->helpers->renderAdminForm( $env_options );
echo '</div>';
echo '<form action="" method="post">';
echo $this->helpers->renderAdminForm( $form_options, $saved_variables );
echo '</form>';