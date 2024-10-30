<?php
$form_settings = [
	'input_class' => 'cj-input cj-is-medium',
	'button_class' => 'cj-button cj-is-medium cj-is-primary',
	'button_text' => __( 'Send verification email', 'wp-builder-locale' ),
	'label_user' => __( 'Username or email address', 'wp-builder-locale' ),
	'placeholder_user' => __( 'Enter your user or email address', 'wp-builder-locale' ),
	'cancel_title' => __( 'Sign In', 'wp-builder-locale' ),
	'cancel_link' => '#cancel',
	'cancel_link_class' => 'cj-button cj-is-medium cj-ml-5 show-login-form',
];
if( is_array( $settings ) && ! empty( $settings ) ) {
	foreach( $settings as $setting_key => $setting_value ) {
		$form_settings[ $setting_key ] = $setting_value;
	}
}
if( $show_vars ) {
	echo '<pre>';
	print_r( $form_settings );
	echo '</pre>';
}
?>
<div class="cjwpbldr-vue-auth-reset-password-form cj-is-hidden" v-cloak>
    <div v-if="form_success !== ''" class="cj-notification cj-is-success" v-html="form_success"></div>
    <form method="post" @submit.prevent="submitForm">
        <div class="cj-field cj-is-grouped">
            <div class="cj-control cj-is-expanded">
				<?php if( isset( $form_settings['label_user'] ) && $form_settings['label_user'] != '' ) { ?>
                    <label class="cj-label">
						<?php echo $form_settings['label_user']; ?>
                    </label>
				<?php } ?>
                <input v-model="form.user" type="text" :class="{'cj-is-danger': form_errors.user !== undefined}" class="<?php echo $form_settings['input_class']; ?>" placeholder="<?php echo $form_settings['placeholder_user']; ?>" required>
                <div v-if="form_errors.user !== undefined" class="cj-help cj-color-danger" v-html="form_errors.user"></div>
            </div>
        </div>
        <div class="cj-field cj-is-grouped">
            <div class="cj-control">
                <button type="submit" :class="{'cj-is-loading' : loading === 'processing-form'}" class="<?php echo $form_settings['button_class']; ?>"><?php echo $form_settings['button_text'] ?></button>
				<?php if( $form_settings['cancel_title'] != '' ) { ?>
                    <a href="<?php echo $form_settings['cancel_link']; ?>" title="<?php echo $form_settings['cancel_title']; ?>" class="<?php echo $form_settings['cancel_link_class']; ?>">
						<?php echo $form_settings['cancel_title']; ?>
                    </a>
				<?php } ?>
            </div>
        </div>
    </form>
</div>