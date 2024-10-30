<?php
$defaults = [
	'form_heading_text' => '',
	'form_content' => '',
	'form_field_size' => '',
	'form_redirect_url' => '',
	'form_user_login_label' => '',
	'form_user_pass_label' => '',
	'form_user_login_placeholder' => '',
	'form_user_pass_placeholder' => '',
	'form_button_text' => '',
	'form_button_color' => '',
	'form_password_link_text' => '',
	'form_password_link_url' => '',
	'form_register_link_text' => '',
	'form_register_link_url' => '',
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
<form action="" method="post" class="cjwpbldr-login-form" data-redirect-url="<?php echo $defaults['form_redirect_url']; ?>">
	<?php if( $defaults['form_heading_text'] != '' ) { ?>
        <h4 class="cj-mt-0 cj-mb-0" <?php echo $this->blockInlineEdit( $defaults['block_info'], [$option_prefix . 'form_heading_text'] ); ?>><?php echo $defaults['form_heading_text']; ?></h4>
	<?php } ?>
	<?php if( $defaults['form_content'] != '' ) { ?>
        <p class="cj-mt-0 cj-mb-15" <?php echo $this->blockInlineEdit( $defaults['block_info'], [$option_prefix . 'form_content'] ); ?>><?php echo $defaults['form_content']; ?></p>
	<?php } ?>
    <div class="cj-field">
        <div class="cj-control cj-is-expanded" <?php echo $this->blockInlineEdit( $defaults['block_info'], [$option_prefix . 'form_user_login_label', $option_prefix . 'form_user_login_placeholder', $option_prefix . 'form_field_size'] ); ?>>
			<?php echo ($defaults['form_user_login_label'] !== '') ? '<label class="cj-label">' . $defaults['form_user_login_label'] . '</label>' : ''; ?>
            <input name="user" type="text" class="cj-input cj-is-<?php echo $defaults['form_field_size']; ?>" placeholder="<?php echo $defaults['form_user_login_placeholder'] ?>" required>
        </div>
    </div>
    <div class="cj-field">
        <div class="cj-control cj-is-expanded" <?php echo $this->blockInlineEdit( $defaults['block_info'], [$option_prefix . 'form_user_pass_label', $option_prefix .'form_user_pass_placeholder', $option_prefix .'form_field_size'] ); ?>>
			<?php echo ($defaults['form_user_pass_label'] !== '') ? '<label class="cj-label">' . $defaults['form_user_pass_label'] . '</label>' : ''; ?>
            <input name="user_pass" type="password" class="cj-input cj-is-<?php echo $defaults['form_field_size']; ?>" placeholder="<?php echo $defaults['form_user_pass_placeholder'] ?>" required>
        </div>
    </div>
    <div class="cj-field cj-is-grouped cj-mt-20" <?php echo $this->blockInlineEdit( $defaults['block_info'], [$option_prefix . 'form_button_color', $option_prefix . 'form_button_text', $option_prefix . 'form_field_size', $option_prefix . 'form_password_link_url', $option_prefix . 'form_password_link_text', $option_prefix . 'form_register_link_url', $option_prefix . 'form_register_link_text'] ); ?>>
        <div class="cj-control cj-is-expanded">
            <button type="submit" class="cj-button cj-is-<?php echo $defaults['form_button_color']; ?> cj-is-<?php echo $defaults['form_field_size']; ?>"><?php echo $defaults['form_button_text']; ?></button>
            <a href="<?php echo $defaults['form_password_link_url']; ?>" title="<?php echo $defaults['form_password_link_text']; ?>" class="cj-inline-block cj-ml-10 <?php echo $button_links_top_margin[ $defaults['form_field_size'] ]; ?>"><?php echo $defaults['form_password_link_text']; ?></a>
        </div>
        <div class="cj-control cj-is-expanded cj-text-right">
            <a href="<?php echo $defaults['form_register_link_url']; ?>" title="<?php echo $defaults['form_register_link_text']; ?>" class="cj-inline-block cj-ml-10 <?php echo $button_links_top_margin[ $defaults['form_field_size'] ]; ?>"><?php echo $defaults['form_register_link_text']; ?></a>
        </div>
    </div>
</form>