<form action="" method="post" class="cjwpbldr-set-password-form" data-redirect-url="<?php echo $defaults['form_redirect_url']; ?>">
	<?php if( $defaults['form_set_password_heading_text'] != '' ) { ?>
        <h4 class="cj-mt-0 cj-mb-0" <?php echo $this->blockInlineEdit( $defaults['block_info'], [$option_prefix . 'form_set_password_heading_text'] ); ?>><?php echo $defaults['form_set_password_heading_text']; ?></h4>
	<?php } ?>
	<?php if( $defaults['form_set_password_content'] != '' ) { ?>
        <p class="cj-mt-0 cj-mb-15" <?php echo $this->blockInlineEdit( $defaults['block_info'], [$option_prefix . 'form_set_password_content'] ); ?>><?php echo $defaults['form_set_password_content']; ?></p>
	<?php } ?>
    <div class="cj-field cj-is-grouped">
        <div class="cj-control cj-is-expanded" <?php echo $this->blockInlineEdit( $defaults['block_info'], [$option_prefix . 'form_user_pass_label', 'form_user_pass_placeholder', 'form_field_size'] ); ?>>
			<?php echo ($defaults['form_user_pass_label'] !== '') ? '<label class="cj-label">' . $defaults['form_user_pass_label'] . '</label>' : ''; ?>
            <input name="user_pass" type="password" class="cj-input cj-is-<?php echo $defaults['form_field_size']; ?>" placeholder="<?php echo $defaults['form_user_pass_placeholder'] ?>" required>
        </div>
    </div>
    <div class="cj-field cj-is-grouped">
        <div class="cj-control cj-is-expanded" <?php echo $this->blockInlineEdit( $defaults['block_info'], [$option_prefix . 'form_user_pass_confirmation_label', 'form_user_pass_confirmation_placeholder', 'form_field_size'] ); ?>>
			<?php echo ($defaults['form_user_pass_confirmation_label'] !== '') ? '<label class="cj-label">' . $defaults['form_user_pass_confirmation_label'] . '</label>' : ''; ?>
            <input name="user_pass_confirmation" type="password" class="cj-input cj-is-<?php echo $defaults['form_field_size']; ?>" placeholder="<?php echo $defaults['form_user_pass_confirmation_placeholder'] ?>" required>
        </div>
    </div>

    <div class="cj-field cj-is-grouped cj-mt-20" <?php echo $this->blockInlineEdit( $defaults['block_info'], [$option_prefix . 'form_button_color', 'form_set_password_button_text', 'form_field_size'] ); ?>>
        <div class="cj-control cj-is-expanded">
            <button type="submit" class="cj-button cj-is-<?php echo $defaults['form_button_color']; ?> cj-is-<?php echo $defaults['form_field_size']; ?>"><?php echo $defaults['form_set_password_button_text']; ?></button>
        </div>
    </div>
    <input type="hidden" name="salt" value="<?php echo $_GET['key']; ?>" class="cj-input">
    <input type="hidden" name="redirect_url" value="<?php echo $defaults['form_redirect_url']; ?>" class="cj-input">
</form>