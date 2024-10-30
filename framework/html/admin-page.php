<?php
$page_class = '';
if( is_array( $_GET ) ) {
	$page_class = implode( ' cjwpbldr-admin-page-', $_GET );
}
$admin_menus = [];
$admin_menu = apply_filters( 'cjwpbldr_admin_dropdown', $admin_menus );

if( ! isset( $_GET['callback'] ) ) {
	wp_redirect( $this->helpers->callbackUrl( 'core-welcome' ) );
	exit;
}
$callback = $_GET['callback'];
$callback_array = explode( '~', $_GET['callback'] );

$installed_addons = $this->helpers->installedAddons();
if( in_array( $callback, array_keys( $installed_addons ) ) ) {
	wp_redirect( $this->helpers->callbackUrl( 'welcome', null, $callback ) );
	exit;
}

if( $callback_array[0] == '' && $_GET['page'] == $this->helpers->config( 'plugin-info', 'page_slug' ) ) {
	wp_redirect( $this->helpers->callbackUrl( 'core-welcome' ) );
	exit;
}
if( strpos( $callback, 'core-' ) === 0 ) {
	$file_path = sprintf( '%s/admin-pages/%s.php', $this->helpers->framework_dir, $callback );
}

$messages = '';
if( isset( $_POST['save_admin_options'] ) ) {
	$form_data = $_POST;
	if( file_exists( $file_path ) ) {
		$option_file_name = str_replace( '.php', '', basename( $file_path ) );
		$option_files = $this->helpers->config( 'option-files' );
		if( in_array( $option_file_name, $option_files ) ) {
			$form_fields = include($file_path);
			foreach( $form_fields as $key => $form_field ) {
				if( in_array( $form_field['type'], $this->helpers->selectFields() ) ) {
					$form_data[ $form_field['id'] ] = (isset( $_POST[ $form_field['id'] ] )) ? $_POST[ $form_field['id'] ] : '';
				}
			}
		}
	}
	unset( $form_data['save_admin_options'] );
	$this->helpers->updateOptions( $form_data );
	$messages = $this->helpers->alert( 'success', __( 'Settings updated.', 'wp-builder-locale' ) );
	wp_redirect( $this->helpers->currentUrl() );
	die();
}
$saved_options = $this->helpers->getOption();
?>
<div class="wrap" id="cjwpbldr-admin-page">
    <h1 class="wp-heading-inline">
		<?php echo $this->helpers->cjwpbldr_title; ?>
    </h1>
	<?php if( ! $this->helpers->is_premium ) { ?>
        <a href="https://gum.co/wp-builder-pro" target="_blank" class="page-title-action button-primary">
			<?php _e( 'Get Pro', 'wp-builder-locale' ) ?>
        </a>
	<?php } ?>
    <hr class="wp-header-end">
    <div class="cssjockey-ui">
        <div class="cj-pt-5"></div>
		<?php require 'admin-dropdown.php'; ?>
        <div id="cjwpbldr-admin-settings-page" class="<?php echo $page_class; ?>">
			<?php if( file_exists( $file_path ) ) {
				$option_file_name = str_replace( '.php', '', basename( $file_path ) );
				$option_files = $this->helpers->config( 'option-files' );
				if( in_array( $option_file_name, $option_files ) ) {
					$form_fields = include($file_path);
					if( is_array( $form_fields ) && ! empty( $form_fields ) ) {
						foreach( $form_fields as $field_key => $field_value ) {
							$form_fields[ $field_key ]['default'] = $this->helpers->getOption( $field_value['id'], $field_value['default'] );
						}

						$form_fields['submit-button'] = [
							'type' => 'submit',
							'id' => 'save_admin_options',
							'label' => '',
							'label_suffix' => '',
							'info' => '',
							'params' => ['class' => 'cj-button cj-is-primary'],
							'suffix' => '',
							'default' => esc_attr__( 'Save Settings', 'wp-builder-locale' ),
							'options' => '', // array in case of dropdown, checkbox and radio buttons
						];
						echo '<form class="cj-options-form" action="" method="post">';
						echo $messages;
						echo $this->helpers->renderAdminForm( $form_fields );
						echo '</form>';
					}
				} else {
					require $file_path;
				}
			} else {
				$content = sprintf( __( '<b>File not found:</b><br>%s', 'wp-builder-locale' ), str_replace( $this->helpers->root_dir, '', $file_path ) );
				echo $this->helpers->alert( 'danger', $content );
			} ?>
        </div>
    </div>
</div>