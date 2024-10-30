<?php
$form_settings = [
	'input_class' => 'cj-input cj-is-medium',
	'button_class' => 'cj-button cj-is-medium cj-is-primary',
	'button_text' => __( 'Change Password', 'wp-builder-locale' ),
	'label_user_pass' => __( 'Password', 'wp-builder-locale' ),
	'placeholder_user_pass' => __( 'Enter a new password', 'wp-builder-locale' ),
	'label_user_pass_confirmation' => __( 'Confirm password', 'wp-builder-locale' ),
	'placeholder_user_pass_confirmation' => __( 'Type again', 'wp-builder-locale' ),
	'login_title' => __( 'Sign In', 'wp-builder-locale' ),
	'login_link' => '#login',
	'login_link_class' => 'cj-button cj-is-link cj-mt-5  cj-register',
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
<div class="cjwpbldr-vue-auth-change-password-form cj-is-hidden" v-cloak>
    <div v-if="form_success !== ''" class="cj-notification cj-is-success" v-html="form_success"></div>
    <form method="post" @submit.prevent="submitForm">
        <div class="cj-field cj-is-grouped">
            <div class="cj-control cj-is-expanded">
				<?php if( isset( $form_settings['label_user_pass'] ) && $form_settings['label_user_pass'] != '' ) { ?>
                    <label class="cj-label">
						<?php echo $form_settings['label_user_pass']; ?>
                    </label>
				<?php } ?>
                <input v-model="form.user_pass" type="password" :class="{'cj-is-danger': form_errors.user_pass !== undefined}" class="<?php echo $form_settings['input_class']; ?>" placeholder="<?php echo $form_settings['placeholder_user_pass']; ?>" required>
                <div v-if="form_errors.user_pass !== undefined" class="cj-help cj-color-danger" v-html="form_errors.user_pass"></div>
            </div>
        </div>
        <div class="cj-field cj-is-grouped">
            <div class="cj-control cj-is-expanded">
				<?php if( isset( $form_settings['label_user_pass_confirmation'] ) && $form_settings['label_user_pass_confirmation'] != '' ) { ?>
                    <label class="cj-label">
						<?php echo $form_settings['label_user_pass_confirmation']; ?>
                    </label>
				<?php } ?>
                <input v-model="form.user_pass_confirmation" type="password" :class="{'cj-is-danger': form_errors.user_pass_confirmation !== undefined}" class="<?php echo $form_settings['input_class']; ?>" placeholder="<?php echo $form_settings['placeholder_user_pass_confirmation']; ?>" required>
                <div v-if="form_errors.user_pass_confirmation !== undefined" class="cj-help cj-color-danger" v-html="form_errors.user_pass_confirmation"></div>
            </div>
        </div>
        <div class="cj-field cj-is-grouped">
            <div class="cj-control">
                <button type="submit" :class="{'cj-is-loading' : loading === 'processing-form'}" class="<?php echo $form_settings['button_class']; ?>"><?php echo $form_settings['button_text'] ?></button>
				<?php if( $form_settings['login_title'] != '' ) { ?>
                    <a href="<?php echo $form_settings['login_link']; ?>" title="<?php echo $form_settings['login_title']; ?>" class="<?php echo $form_settings['login_link_class']; ?>">
						<?php echo $form_settings['login_title']; ?>
                    </a>
				<?php } ?>
            </div>
        </div>
    </form>
</div>