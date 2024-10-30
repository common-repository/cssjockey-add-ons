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
	'form_heading_text' => '',
	'form_content' => '',
	'form_field_size' => '',
	'form_success_message' => '',
	'form_redirect_url' => '',
	'form_show_name_fields' => '',
	'form_first_name_label' => '',
	'form_first_name_placeholder' => '',
	'form_last_name_label' => '',
	'form_last_name_placeholder' => '',
	'form_user_email_label' => '',
	'form_user_email_placeholder' => '',
	'form_user_login_label' => '',
	'form_user_login_placeholder' => '',
	'form_user_pass_label' => '',
	'form_user_pass_placeholder' => '',
	'form_user_pass_confirmation_label' => '',
	'form_user_pass_confirmation_placeholder' => '',
	'form_button_text' => '',
	'form_button_color' => '',
	'form_login_link_text' => '',
	'form_login_link_url' => '',
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

?>
<form action="" method="post" class="cjwpbldr-register-form" data-redirect-url="<?php echo $defaults['form_redirect_url']; ?>">
	<?php if( $defaults['form_heading_text'] != '' ) { ?>
        <h4 class="cj-mt-0 cj-mb-0" <?php echo $this->blockInlineEdit( $defaults['block_info'], [$option_prefix . 'form_heading_text'] ); ?>><?php echo $defaults['form_heading_text']; ?></h4>
	<?php } ?>
	<?php if( $defaults['form_content'] != '' ) { ?>
        <p class="cj-mt-0 cj-mb-15" <?php echo $this->blockInlineEdit( $defaults['block_info'], [$option_prefix . 'form_content'] ); ?>><?php echo $defaults['form_content']; ?></p>
	<?php } ?>
	<?php if( $defaults['form_show_name_fields'] == 'yes' ) { ?>
        <div class="cj-field cj-is-grouped">
            <div class="cj-control cj-is-expanded">
                <label class="cj-label" <?php echo $this->blockInlineEdit( $defaults['block_info'], [$option_prefix . 'form_first_name_label', $option_prefix .  'form_first_name_placeholder', $option_prefix .  'form_field_size'] ); ?>>
					<?php if( $defaults['form_first_name_label'] != '' ) { ?>
                        <span class="cj-inline-block cj-mb-5">
                            <?php echo $defaults['form_first_name_label']; ?>
                        </span> <br>
					<?php } ?>
                    <input type="text" name="first_name" placeholder="<?php echo $defaults['form_first_name_placeholder']; ?>" value="" required class="cj-input cj-is-<?php echo $defaults['form_field_size']; ?>"/>
                </label>
            </div>
            <div class="cj-control cj-is-expanded">
                <label class="cj-label" <?php echo $this->blockInlineEdit( $defaults['block_info'], [$option_prefix . 'form_last_name_label', $option_prefix .  'form_last_name_placeholder', $option_prefix .  'form_field_size'] ); ?>>
					<?php if( $defaults['form_last_name_label'] != '' ) { ?>
                        <span class="cj-inline-block cj-mb-5">
                            <?php echo $defaults['form_last_name_label']; ?>
                        </span> <br>
					<?php } ?>
                    <input type="text" name="last_name" placeholder="<?php echo $defaults['form_last_name_placeholder']; ?>" value="" required class="cj-input cj-is-<?php echo $defaults['form_field_size']; ?>"/>
                </label>
            </div>
        </div>
	<?php } ?>

    <div class="cj-field">
        <div class="cj-control cj-is-expanded">
            <label class="cj-label" <?php echo $this->blockInlineEdit( $defaults['block_info'], [$option_prefix . 'form_user_login_label', $option_prefix .  'form_user_login_placeholder', $option_prefix .  'form_field_size'] ); ?>>
				<?php if( $defaults['form_user_login_label'] != '' ) { ?>
                    <span class="cj-inline-block cj-mb-5">
                        <?php echo $defaults['form_user_login_label']; ?>
                    </span> <br>
				<?php } ?>
                <input type="text" name="user_login" placeholder="<?php echo $defaults['form_user_login_placeholder']; ?>" value="" required class="cj-input cj-is-<?php echo $defaults['form_field_size']; ?>"/>
            </label>
        </div>
    </div>

    <div class="cj-field">
        <div class="cj-control cj-is-expanded">
            <label class="cj-label" <?php echo $this->blockInlineEdit( $defaults['block_info'], [$option_prefix . 'form_user_email_label', $option_prefix .  'form_user_email_placeholder', $option_prefix .  'form_field_size'] ); ?>>
				<?php if( $defaults['form_user_email_label'] != '' ) { ?>
                    <span class="cj-inline-block cj-mb-5">
                        <?php echo $defaults['form_user_email_label']; ?>
                    </span> <br>
				<?php } ?>
                <input type="text" name="user_email" placeholder="<?php echo $defaults['form_user_email_placeholder']; ?>" value="" required class="cj-input cj-is-<?php echo $defaults['form_field_size']; ?>"/>
            </label>
        </div>
    </div>

    <div class="cj-field cj-is-grouped">
        <div class="cj-control cj-is-expanded" <?php echo $this->blockInlineEdit( $defaults['block_info'], [$option_prefix . 'form_user_pass_label', $option_prefix .  'form_user_pass_placeholder', $option_prefix .  'form_field_size'] ); ?>>
			<?php echo ($defaults['form_user_pass_label'] !== '') ? '<label class="cj-label">' . $defaults['form_user_pass_label'] . '</label>' : ''; ?>
            <input name="user_pass" type="password" class="cj-input cj-is-<?php echo $defaults['form_field_size']; ?>" placeholder="<?php echo $defaults['form_user_pass_placeholder'] ?>" required>
        </div>
        <div class="cj-control cj-is-expanded" <?php echo $this->blockInlineEdit( $defaults['block_info'], [$option_prefix . 'form_user_pass_confirmation_label', $option_prefix .  'form_user_pass_confirmation_placeholder', $option_prefix .  'form_field_size'] ); ?>>
			<?php echo ($defaults['form_user_pass_confirmation_label'] !== '') ? '<label class="cj-label">' . $defaults['form_user_pass_confirmation_label'] . '</label>' : ''; ?>
            <input name="user_pass_confirmation" type="password" class="cj-input cj-is-<?php echo $defaults['form_field_size']; ?>" placeholder="<?php echo $defaults['form_user_pass_confirmation_placeholder'] ?>" required>
        </div>
    </div>
    <div class="cj-field cj-is-grouped cj-mt-20" <?php echo $this->blockInlineEdit( $defaults['block_info'], [$option_prefix . 'form_button_color', $option_prefix .  'form_button_text', $option_prefix .  'form_field_size', $option_prefix .  'form_password_link_url', $option_prefix .  'form_password_link_text', $option_prefix .  'form_login_link_url', $option_prefix .  'form_login_link_text'] ); ?>>
        <div class="cj-control cj-is-expanded">
            <button type="submit" class="cj-button cj-is-<?php echo $defaults['form_button_color']; ?> cj-is-<?php echo $defaults['form_field_size']; ?>"><?php echo $defaults['form_button_text']; ?></button>
        </div>
        <div class="cj-control cj-is-expanded cj-text-right">
            <a href="<?php echo $defaults['form_login_link_url']; ?>" title="<?php echo $defaults['form_login_link_text']; ?>" class="cj-inline-block cj-ml-10 <?php echo $button_links_top_margin[ $defaults['form_field_size'] ]; ?>"><?php echo $defaults['form_login_link_text']; ?></a>
        </div>
    </div>
    <input type="hidden" name="verify_email" value="1">
    <input type="hidden" name="notify" value="admin">
    <input type="hidden" name="auto_login" value="1">
    <input type="hidden" name="success_message" value="<?php echo $defaults['form_success_message']; ?>">
    <input type="hidden" name="current_url" value="<?php echo $this->currentUrl(); ?>">
    <input type="hidden" name="redirect_url" value="<?php echo ($defaults['form_redirect_url'] != '') ? $defaults['form_redirect_url'] : wp_login_url(); ?>">
</form>