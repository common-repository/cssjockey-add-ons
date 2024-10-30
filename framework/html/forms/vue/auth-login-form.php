<?php
$form_settings = [
	'redirect_url' => site_url( '?logged-in=1' ),
	'input_class' => 'cj-input cj-is-medium',
	'button_class' => 'cj-button cj-is-medium cj-is-primary',
	'button_text' => __( 'Sign In', 'wp-builder-locale' ),
	'label_username' => __( 'Username or email address', 'wp-builder-locale' ),
	'label_user_pass' => __( 'Password', 'wp-builder-locale' ),
	'forgot_password_title' => __( 'Forgot password?', 'wp-builder-locale' ),
	'forgot_password_link' => '#reset-password',
	'forgot_password_link_class' => 'show-forgot-password-form',
	'register_title' => __( 'Create an account', 'wp-builder-locale' ),
	'register_link' => '#register',
	'register_link_class' => 'cj-button cj-is-medium cj-ml-5 show-register-form',
	'placeholder_username' => __( 'Enter your username or email address', 'wp-builder-locale' ),
	'placeholder_user_pass' => __( 'Enter your password', 'wp-builder-locale' ),
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
<div class="cjwpbldr-vue-auth-login-form cj-is-hidden" data-redirect-url="<?php echo $form_settings['redirect_url']; ?>" v-cloak>
    <form method="post" @submit.prevent="submitForm">
        <div class="cj-field cj-is-grouped">
            <div class="cj-control cj-is-expanded">
				<?php if( isset( $form_settings['label_username'] ) && $form_settings['label_username'] != '' ) { ?>
                    <label class="cj-label">
						<?php echo $form_settings['label_username']; ?>
                    </label>
				<?php } ?>
                <input v-model="form.user" type="text" :class="{'cj-is-danger': form_errors.user !== undefined}" class="<?php echo $form_settings['input_class']; ?>" placeholder="<?php echo $form_settings['placeholder_username']; ?>" required>
                <div v-if="form_errors.user !== undefined" class="cj-help cj-color-danger" v-html="form_errors.user"></div>
            </div>
        </div>
        <div class="cj-field cj-is-grouped">
            <div class="cj-control cj-is-expanded">
				<?php if( isset( $form_settings['label_user_pass'] ) && $form_settings['label_user_pass'] != '' ) { ?>
                    <label class="cj-label">
						<?php if( $form_settings['forgot_password_title'] != '' ) { ?>
                            <a href="<?php echo $form_settings['forgot_password_link']; ?>" title="<?php echo $form_settings['forgot_password_title'] ?>" class="cj-is-pulled-right cj-mt-2 <?php echo $form_settings['forgot_password_link_class']; ?>"><small class="cj-font-normal"><?php echo $form_settings['forgot_password_title'] ?></small></a>
						<?php } ?>
						<?php echo $form_settings['label_user_pass']; ?>
                    </label>
				<?php } ?>
                <input v-model="form.user_pass" type="password" :class="{'cj-is-danger': form_errors.user_pass !== undefined}" class="<?php echo $form_settings['input_class']; ?>" placeholder="<?php echo $form_settings['placeholder_user_pass']; ?>" required>
                <div v-if="form_errors.user_pass !== undefined" class="cj-help cj-color-danger" v-html="form_errors.user_pass"></div>
            </div>
        </div>
        <div class="cj-field cj-is-grouped">
            <div class="cj-control">
                <button type="submit" :class="{'cj-is-loading' : loading === 'processing-form'}" class="<?php echo $form_settings['button_class']; ?>"><?php echo $form_settings['button_text'] ?></button>
				<?php if( $form_settings['register_title'] != '' ) { ?>
                    <a href="<?php echo $form_settings['register_link']; ?>" title="<?php echo $form_settings['register_title']; ?>" class="<?php echo $form_settings['register_link_class']; ?>">
						<?php echo $form_settings['register_title']; ?>
                    </a>
				<?php } ?>
            </div>
        </div>
    </form>
</div>