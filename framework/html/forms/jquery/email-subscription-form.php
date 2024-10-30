<?php
$defaults = [
	'form_show_name_fields' => '',
	'form_field_size' => '',

	'form_first_name_label' => '',
	'form_first_name_placeholder' => '',
	'form_last_name_label' => '',
	'form_last_name_placeholder' => '',
	'form_email_address_label' => '',
	'form_email_address_placeholder' => '',

	'form_button_text' => '',
	'form_button_color' => '',
	'form_success_message' => '',
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

?>
<form action="" method="post" class="cjwpbldr-subscribe-form" data-success-message="<?php echo $defaults['form_success_message']; ?>">
	<?php if( $defaults['form_show_name_fields'] == 'yes' ) { ?>
        <div class="cj-field cj-is-grouped">
            <div class="cj-control cj-is-expanded">
                <label class="cj-label" <?php echo $this->blockInlineEdit( $defaults['block_info'], ['form_first_name_label', 'form_first_name_placeholder', 'form_field_size'] ); ?>>
					<?php if( $defaults['form_first_name_label'] != '' ) { ?>
                        <span class="cj-inline-block cj-mb-5">
                            <?php echo $defaults['form_first_name_label']; ?>
                        </span> <br>
					<?php } ?>
                    <input type="text" name="first_name" placeholder="<?php echo $defaults['form_first_name_placeholder']; ?>" value="" required class="cj-input cj-is-<?php echo $defaults['form_field_size']; ?>"/>
                </label>
            </div>
            <div class="cj-control cj-is-expanded">
                <label class="cj-label" <?php echo $this->blockInlineEdit( $defaults['block_info'], ['form_last_name_label', 'form_last_name_placeholder', 'form_field_size'] ); ?>>
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
    <div class="cj-field cj-is-grouped">
        <div class="cj-control cj-is-expanded">
            <label class="cj-label" <?php echo $this->blockInlineEdit( $defaults['block_info'], ['form_email_address_label', 'form_email_address_placeholder', 'form_field_size'] ); ?>>
				<?php if( $defaults['form_email_address_label'] != '' ) { ?>
                    <span class="cj-inline-block cj-mb-5">
                        <?php echo $defaults['form_email_address_label']; ?>
                    </span> <br>
				<?php } ?>
                <input type="text" name="email_address" placeholder="<?php echo $defaults['form_email_address_placeholder']; ?>" value="" required class="cj-input cj-is-<?php echo $defaults['form_field_size']; ?>"/>
            </label>
        </div>
        <div class="cj-control" <?php echo $this->blockInlineEdit( $defaults['block_info'], ['form_button_text', 'form_button_color', 'form_field_size'] ); ?>>
            <label class="cj-label">
				<?php if( $defaults['form_email_address_label'] != '' ) { ?>
                    <span class="cj-inline-block cj-mb-5">
                        &nbsp;
                    </span> <br>
				<?php } ?>
                <button type="submit" class="cj-button cj-is-<?php echo $defaults['form_button_color']; ?> cj-is-<?php echo $defaults['form_field_size']; ?>"><?php echo $defaults['form_button_text']; ?></button>
            </label>
        </div>
    </div>
</form>