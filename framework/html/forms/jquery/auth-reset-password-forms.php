<?php
if( isset( $_GET['verify-email'] ) && $_GET['verify-email'] != '' && isset( $_GET['verify-key'] ) && $_GET['verify-key'] != '' ) {

	$response = $this->wpRemotePost( rest_url( 'cjwpbldr/auth/register/complete' ), [
		'user_email' => $_GET['verify-email'],
		'verification_key' => $_GET['verify-key'],
	], ['no-auth' => true] );
	$response = json_decode( $response, 1 );

	if( isset( $response['data']['success'] ) ) {
		if( isset( $response['data']['success']['redirect_url'] ) ) {
			wp_redirect( $response['data']['success']['redirect_url'] );
			die();
		} else {
			wp_redirect( wp_login_url() );
			die();
		}
	}
	if( isset( $response['data']['errors'] ) ) {
		echo $this->alert( 'danger', $response['data']['errors'] );
	}
}

$defaults = [
	'form_reset_password_heading_text' => '',
	'form_reset_password_content' => '',
	'form_set_password_heading_text' => '',
	'form_set_password_content' => '',
	'form_field_size' => '',
	'form_success_message' => '',
	'form_user_email_label' => '',
	'form_user_email_placeholder' => '',
	'form_reset_button_text' => '',
	'form_button_color' => '',
	'form_login_link_text' => '',
	'form_login_link_url' => '',
	'form_redirect_url' => '',
	'block_info' => [],
];

if( is_array( $settings ) && ! empty( $settings ) ) {
	foreach( $settings as $setting_key => $setting_value ) {
		$defaults[ $setting_key ] = $setting_value;
	}
}

if( $show_vars ) {
	$vars = $defaults;
	unset( $vars['block_info'] );
	echo '<pre>';
	print_r( $vars );
	echo '</pre>';
}

$button_links_top_margin = [
	'small' => 'cj-mt-0 cj-fs-14',
	'default' => 'cj-mt-7',
	'medium' => 'cj-mt-12',
	'large' => 'cj-mt-15',
];

$current_url = $this->currentUrl();
$is_editing = ($this->containString( 'ui-blocks/preview/save', $current_url ));

if( isset( $_GET['action'] ) && $_GET['action'] == 'rp' && isset( $_GET['key'] ) && $_GET['key'] != '' ) {
	require_once 'parts/set-password.php';
} else {
	require_once 'parts/reset-password.php';
	if( $is_editing ) {
		echo '<hr>';
		require_once 'parts/set-password.php';
	}
}