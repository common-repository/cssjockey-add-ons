<?php
$form_settings = [
	'redirect_url' => site_url( '?registered=1' ),
	'input_class' => 'cj-input cj-is-medium',
	'button_class' => 'cj-button cj-is-medium cj-is-primary',
	'button_text' => __( 'Create an account', 'wp-builder-locale' ),

    'label_first_name' => __( 'First name', 'wp-builder-locale' ),
	'label_last_name' => __( 'Last name', 'wp-builder-locale' ),
	'label_user_login' => __( 'Choose a username', 'wp-builder-locale' ),
	'label_user_email' => __( 'Email address', 'wp-builder-locale' ),
	'label_user_pass' => __( 'Password', 'wp-builder-locale' ),
	'label_user_pass_confirmation' => __( 'Confirm Password', 'wp-builder-locale' ),

    'placeholder_first_name' => __( 'First name', 'wp-builder-locale' ),
	'placeholder_last_name' => __( 'Last name', 'wp-builder-locale' ),
	'placeholder_user_login' => __( 'Choose a username', 'wp-builder-locale' ),
	'placeholder_user_email' => __( 'Email address', 'wp-builder-locale' ),
	'placeholder_user_pass' => __( 'Password', 'wp-builder-locale' ),
	'placeholder_user_pass_confirmation' => __( 'Confirm Password', 'wp-builder-locale' ),

	'login_title' => __( 'Sign In', 'wp-builder-locale' ),
	'login_link' => '#login',
	'login_link_class' => 'cj-button cj-is-medium cj-ml-5 show-login-form',
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
<div class="cjwpbldr-vue-auth-register-form cj-is-hidden" data-redirect-url="<?php echo $form_settings['redirect_url']; ?>" v-cloak>
    <form method="post" @submit.prevent="submitForm">
        <div class="cj-field cj-is-grouped">
            <div class="cj-control cj-is-expanded">
				<?php if( isset( $form_settings['label_first_name'] ) && $form_settings['label_first_name'] != '' ) { ?>
                    <label class="cj-label">
						<?php echo $form_settings['label_first_name']; ?>
                    </label>
				<?php } ?>
                <input v-model="form.first_name" type="text" :class="{'cj-is-danger': form_errors.first_name !== undefined}" class="<?php echo $form_settings['input_class']; ?>" placeholder="<?php echo $form_settings['placeholder_first_name']; ?>" require>
                <div v-if="form_errors.first_name !== undefined" class="cj-help cj-color-danger" v-html="form_errors.first_name"></div>
            </div>
            <div class="cj-control cj-is-expanded">
				<?php if( isset( $form_settings['label_last_name'] ) && $form_settings['label_last_name'] != '' ) { ?>
                    <label class="cj-label">
						<?php echo $form_settings['label_last_name']; ?>
                    </label>
				<?php } ?>
                <input v-model="form.last_name" type="text" :class="{'cj-is-danger': form_errors.last_name !== undefined}" class="<?php echo $form_settings['input_class']; ?>" placeholder="<?php echo $form_settings['placeholder_last_name']; ?>" require>
                <div v-if="form_errors.last_name !== undefined" class="cj-help cj-color-danger" v-html="form_errors.last_name"></div>
            </div>
        </div>
        <div class="cj-field cj-is-grouped">
            <div class="cj-control cj-is-expanded">
				<?php if( isset( $form_settings['label_user_login'] ) && $form_settings['label_user_login'] != '' ) { ?>
                    <label class="cj-label">
						<?php echo $form_settings['label_user_login']; ?>
                    </label>
				<?php } ?>
                <input v-model="form.user_login" type="text" :class="{'cj-is-danger': form_errors.user_login !== undefined}" class="<?php echo $form_settings['input_class']; ?>" placeholder="<?php echo $form_settings['placeholder_user_login']; ?>" require>
                <div v-if="form_errors.user_login !== undefined" class="cj-help cj-color-danger" v-html="form_errors.user_login"></div>
            </div>
        </div>
        <div class="cj-field cj-is-grouped">
            <div class="cj-control cj-is-expanded">
				<?php if( isset( $form_settings['label_user_email'] ) && $form_settings['label_user_email'] != '' ) { ?>
                    <label class="cj-label">
						<?php echo $form_settings['label_user_email']; ?>
                    </label>
				<?php } ?>
                <input v-model="form.user_email" type="text" :class="{'cj-is-danger': form_errors.user_email !== undefined}" class="<?php echo $form_settings['input_class']; ?>" placeholder="<?php echo $form_settings['placeholder_user_email']; ?>" require>
                <div v-if="form_errors.user_email !== undefined" class="cj-help cj-color-danger" v-html="form_errors.user_email"></div>
            </div>
        </div>
        <div class="cj-field cj-is-grouped">
            <div class="cj-control cj-is-expanded">
				<?php if( isset( $form_settings['label_user_pass'] ) && $form_settings['label_user_pass'] != '' ) { ?>
                    <label class="cj-label">
						<?php echo $form_settings['label_user_pass']; ?>
                    </label>
				<?php } ?>
                <input v-model="form.user_pass" type="password" :class="{'cj-is-danger': form_errors.user_pass !== undefined}" class="<?php echo $form_settings['input_class']; ?>" placeholder="<?php echo $form_settings['placeholder_user_pass']; ?>" require>
                <div v-if="form_errors.user_pass !== undefined" class="cj-help cj-color-danger" v-html="form_errors.user_pass"></div>
            </div>
            <div class="cj-control cj-is-expanded">
				<?php if( isset( $form_settings['label_user_pass_confirmation'] ) && $form_settings['label_user_pass_confirmation'] != '' ) { ?>
                    <label class="cj-label">
						<?php echo $form_settings['label_user_pass_confirmation']; ?>
                    </label>
				<?php } ?>
                <input v-model="form.user_pass_confirmation" type="password" :class="{'cj-is-danger': form_errors.user_pass_confirmation !== undefined}" class="<?php echo $form_settings['input_class']; ?>" placeholder="<?php echo $form_settings['placeholder_user_pass_confirmation']; ?>" require>
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