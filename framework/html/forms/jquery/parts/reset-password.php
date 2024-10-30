<form action="" method="post" class="cjwpbldr-reset-password-form" data-redirect-url="<?php echo $this->currentUrl(); ?>" data-success-message="<?php echo $defaults['form_reset_success_message']; ?>">
	<?php if( $defaults['form_reset_password_heading_text'] != '' ) { ?>
        <h4 class="cj-mt-0 cj-mb-0" <?php echo $this->blockInlineEdit( $defaults['block_info'], [$option_prefix.'form_reset_password_heading_text'] ); ?>><?php echo $defaults['form_reset_password_heading_text']; ?></h4>
	<?php } ?>
	<?php if( $defaults['form_reset_password_content'] != '' ) { ?>
        <p class="cj-mt-0 cj-mb-15" <?php echo $this->blockInlineEdit( $defaults['block_info'], [$option_prefix.'form_reset_password_content'] ); ?>><?php echo $defaults['form_reset_password_content']; ?></p>
	<?php } ?>
    <div class="cj-field">
        <div class="cj-control cj-is-expanded">
            <label class="cj-label" <?php echo $this->blockInlineEdit( $defaults['block_info'], [$option_prefix.'form_user_email_label', 'form_user_email_placeholder', 'form_field_size'] ); ?>>
				<?php if( $defaults['form_user_email_label'] != '' ) { ?>
                    <span class="cj-inline-block cj-mb-5">
                        <?php echo $defaults['form_user_email_label']; ?>
                    </span> <br>
				<?php } ?>
                <input type="text" name="user" placeholder="<?php echo $defaults['form_user_email_placeholder']; ?>" value="" required class="cj-input cj-is-<?php echo $defaults['form_field_size']; ?>"/>
            </label>
        </div>
    </div>
    <div class="cj-field cj-is-grouped cj-mt-20" <?php echo $this->blockInlineEdit( $defaults['block_info'], [$option_prefix.'form_button_color', 'form_reset_button_text', 'form_field_size', 'form_login_link_url', 'form_login_link_text'] ); ?>>
        <div class="cj-control cj-is-expanded">
            <button type="submit" class="cj-button cj-is-<?php echo $defaults['form_button_color']; ?> cj-is-<?php echo $defaults['form_field_size']; ?>"><?php echo $defaults['form_reset_button_text']; ?></button>
        </div>
        <div class="cj-control cj-is-expanded cj-text-right">
            <a href="<?php echo $defaults['form_login_link_url']; ?>" title="<?php echo $defaults['form_login_link_text']; ?>" class="cj-inline-block cj-ml-10 <?php echo $button_links_top_margin[ $defaults['form_field_size'] ]; ?>"><?php echo $defaults['form_login_link_text']; ?></a>
        </div>
    </div>
    <input type="hidden" name="redirect_url" value="<?php echo $this->currentUrl(); ?>" class="cj-input">
    <input type="hidden" name="success_message" value="<?php echo $defaults['form_reset_success_message']; ?>" class="cj-input">
</form>