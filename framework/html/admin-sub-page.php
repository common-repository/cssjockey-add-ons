<?php
$page_class = '';
if( is_array( $_GET ) ) {
	$page_class = implode( ' ', $_GET );
}
if( ! isset( $_GET['page'] ) ) {
	wp_redirect( $this->helpers->callbackUrl( 'core-welcome' ) );
	exit;
}
if( ! isset( $_GET['callback'] ) ) {
	wp_redirect( $this->helpers->callbackUrl( 'welcome', null, $_GET['page'] ) );
	exit;
}
$callback = $_GET['callback'];
$callback_array = explode( '~', $_GET['callback'] );
$addon_info = $this->helpers->addonInfo( null, $_GET['page'] );
$file_path = sprintf( '%s/admin-pages/%s.php', $addon_info['addon_path'], $callback );
$messages = '';
if( isset( $_POST['save_admin_options'] ) ) {
	$form_data = $_POST;
	if( file_exists( $file_path ) ) {
		$option_file_name = str_replace( '.php', '', basename( $file_path ) );
		$option_files = $this->helpers->config( 'option-files', null, $addon_info['addon_path'] );
		if( in_array( $option_file_name, $option_files ) ) {
			$form_fields = include($file_path);
			if( is_array( $form_fields ) && ! empty( $form_fields ) ) {
				foreach( $form_fields as $field_key => $field_value ) {
					if( in_array( $field_value['type'], $this->helpers->selectFields() ) ) {
						$form_data[ $field_value['id'] ] = (isset( $_POST[ $field_value['id'] ] )) ? $_POST[ $field_value['id'] ] : '';
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
}
$saved_options = $this->helpers->getOption();
?>
<div class="wrap" id="cjwpbldr-admin-page">
    <div class="cj-mb-20">
        <h1 class="cj-title">
            <a href="<?php echo $this->helpers->callbackUrl( 'core-welcome' ); ?>" style="text-decoration: none; color: #222222;"><?php echo $this->helpers->cjwpbldr_title; ?></a>
            <span style="color: #999999; padding: 0 6px; font-size: 18px; font-weight: normal;">&rarr;</span>
            <span><?php echo $this->helpers->addon_info['addon_name'] ?></span>
        </h1>
    </div>
    <div class="cssjockey-ui">
		<?php require 'admin-dropdown.php'; ?>
        <div id="cjwpbldr-admin-settings-page" class="<?php echo $page_class; ?>">
			<?php
			$option_file_name = str_replace( '.php', '', basename( $file_path ) );
			if( ! file_exists( $file_path ) ) {
				$content = sprintf( __( '<b>File not found:</b><br>%s', 'wp-builder-locale' ), str_replace( ABSPATH, '/', $file_path ) );
				echo $this->helpers->alert( 'danger', $content );
			}
			if( file_exists( $file_path ) ) {

				$option_files = $this->helpers->config( 'option-files', null, $addon_info['addon_path'] );
				$option_files = (!is_array($option_files)) ? [] : $option_files;
				if( in_array( $option_file_name, $option_files ) ) {
					$form_fields = include($file_path);
					if( is_array( $form_fields ) && ! empty( $form_fields ) ) {
						foreach( $form_fields as $field_key => $field_value ) {
							$form_fields[ $field_key ]['default'] = $this->helpers->getOption( $field_value['id'], $field_value['default'] );
						}
						$form_fields['submit-button'] = array(
							'type' => 'submit',
							'id' => 'save_admin_options',
							'label' => '',
							'label_suffix' => '',
							'info' => '',
							'params' => array('class' => 'cj-button cj-is-primary'),
							'suffix' => '',
							'default' => esc_attr__( 'Save Settings', 'wp-builder-locale' ),
							'options' => '', // array in case of dropdown, checkbox and radio buttons
						);
						echo '<div class="' . $addon_info['addon_id'] . '-admin-page cjwpbldr-admin-settings-page admin-page-' . $callback . '">';
						echo '<form class="cj-options-form" action="" method="post">';
						echo $messages;
						echo $this->helpers->renderAdminForm( $form_fields );
						echo '</form>';
						echo '</div>';
					}
				} else {
					echo '<div class="' . $addon_info['addon_id'] . '-admin-page cjwpbldr-admin-settings admin-page-' . $callback . '">';
					require $file_path;
					echo '</div>';
				}
			}
			?>
        </div>
    </div>
</div>
