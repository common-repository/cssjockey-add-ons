<?php
$form_settings = [
	'input_class' => 'cj-input cj-is-medium',
	'button_class' => 'cj-button cj-is-medium cj-is-primary',
	'button_text' => __( 'Send message', 'wp-builder-locale' ),
	'label_first_name' => __( 'First name', 'wp-builder-locale' ),
	'label_last_name' => __( 'Last name', 'wp-builder-locale' ),
	'label_email' => __( 'Email address', 'wp-builder-locale' ),
	'label_message' => __( 'Message', 'wp-builder-locale' ),
	'placeholder_first_name' => __( 'Enter your first name', 'wp-builder-locale' ),
	'placeholder_last_name' => __( 'Enter your last name', 'wp-builder-locale' ),
	'placeholder_email' => __( 'Enter your email address', 'wp-builder-locale' ),
	'placeholder_message' => __( 'Enter your message here', 'wp-builder-locale' ),
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
<div class="cjwpbldr-vue-contact-form cj-is-hidden" v-cloak>
    <form method="post" @submit.prevent="submitForm">
        <div v-if="form_success !== ''" class="cj-notification cj-is-success" v-html="form_success"></div>
        <div class="cj-field cj-is-grouped">
            <div class="cj-control cj-is-expanded">
				<?php if( isset( $form_settings['label_first_name'] ) && $form_settings['label_first_name'] != '' ) { ?>
                    <label class="cj-label"><?php echo $form_settings['label_first_name']; ?></label>
				<?php } ?>
                <input v-model="form.first_name" type="text" :class="{'cj-is-danger': form_errors.first_name !== undefined}" class="<?php echo $form_settings['input_class']; ?>" placeholder="<?php echo $form_settings['placeholder_first_name']; ?>" required>
                <div v-if="form_errors.first_name !== undefined" class="cj-help cj-color-danger" v-html="form_errors.first_name"></div>
            </div>
            <div class="cj-control cj-is-expanded">
				<?php if( isset( $form_settings['label_last_name'] ) && $form_settings['label_last_name'] != '' ) { ?>
                    <label class="cj-label"><?php echo $form_settings['label_last_name']; ?></label>
				<?php } ?>
                <input v-model="form.last_name" type="text" :class="{'cj-is-danger': form_errors.last_name !== undefined}" class="<?php echo $form_settings['input_class']; ?>" placeholder="<?php echo $form_settings['placeholder_last_name']; ?>" required>
                <div v-if="form_errors.last_name !== undefined" class="cj-help cj-color-danger" v-html="form_errors.last_name"></div>
            </div>
        </div>
        <div class="cj-field cj-is-grouped">
            <div class="cj-control cj-is-expanded">
				<?php if( isset( $form_settings['label_email'] ) && $form_settings['label_email'] != '' ) { ?>
                    <label class="cj-label"><?php echo $form_settings['label_email']; ?></label>
				<?php } ?>
                <input v-model="form.email" type="email" :class="{'cj-is-danger': form_errors.email !== undefined}" class="<?php echo $form_settings['input_class']; ?>" placeholder="<?php echo $form_settings['placeholder_email']; ?>" required>
                <div v-if="form_errors.email !== undefined" class="cj-help cj-color-danger" v-html="form_errors.email"></div>
            </div>
        </div>
        <div class="cj-field cj-is-grouped">
            <div class="cj-control cj-is-expanded">
				<?php if( isset( $form_settings['label_message'] ) && $form_settings['label_message'] != '' ) { ?>
                    <label class="cj-label"><?php echo $form_settings['label_message']; ?></label>
				<?php } ?>
                <textarea v-model="form.message" cols="30" rows="5" :class="{'cj-is-danger': form_errors.message !== undefined}" class="cj-textarea" placeholder="<?php echo $form_settings['placeholder_message']; ?>" required></textarea>
                <div v-if="form_errors.message !== undefined" class="cj-help cj-color-danger" v-html="form_errors.message"></div>
            </div>
        </div>
        <div class="cj-field cj-is-grouped">
            <div class="cj-control cj-is-expanded">
                <button type="submit" :class="{'cj-is-loading' : loading === 'processing-form'}" class="<?php echo $form_settings['button_class']; ?>"><?php echo $form_settings['button_text'] ?></button>
            </div>
        </div>
    </form>
</div>