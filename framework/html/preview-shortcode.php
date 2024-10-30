<?php require_once 'previews/header.php';
if( ! current_user_can( 'edit_posts' ) ) {
	wp_redirect( get_bloginfo( 'url' ) );
	die();
}
?>
    <div class="cssjockey-ui">
        <div class="cj-pb-100">
			<?php
			$class_name = $_GET['shortcode'] . '_shortcode';
			if( class_exists( $class_name ) ) {
				$class_instance = $class_name::getInstance();
				$shortcode_class_defaults = $class_instance->defaults;

				$content = '';

				if( isset( $shortcode_class_defaults['info']['single'] ) && ! $shortcode_class_defaults['info']['single'] ) {
					$content = __( '..content goes here..', 'wp-builder-locale' );
					$form_fields['content'] = array(
						'id' => 'content',
						'type' => 'wysiwyg',
						'label' => __( 'Shortcode Content', 'wp-builder-locale' ),
						'default' => '..content goes here..',
						'options' => '', // array in case of dropdown, checkbox and radio buttons
					);
				}
				$form_fields = [];
				foreach( $class_instance->defaults['options'] as $field_key => $field ) {
					$form_fields[ $field_key ] = $field;
				}

				$shortcode_string = $class_instance->helpers->generateShortcodeTag( $shortcode_class_defaults, $content );
				if( isset( $_POST['update_shortcode'] ) ) {
					$post_data = $_POST;
					foreach( $shortcode_class_defaults['options'] as $shortcode_option_key => $shortcode_option ) {
						$shortcode_class_defaults['options'][ $shortcode_option_key ]['default'] = $_POST[ $shortcode_option['id'] ];
					}
					foreach( $form_fields as $form_field_key => $form_field ) {
						if( isset( $_POST[ $form_field['id'] ] ) && $form_field['type'] != 'submit' ) {
							$form_fields[ $form_field_key ]['default'] = $_POST[ $form_field['id'] ];
						}
						if( isset( $_POST['content'] ) ) {
							$content = $_POST['content'];
						}
					}
				}

				$shortcode_string = $class_instance->helpers->generateShortcodeTag( $shortcode_class_defaults, $content );

				$form_fields['submit'] = array(
					'id' => 'update_shortcode',
					'type' => 'submit',
					'label' => '',
					'default' => __( 'Submit', 'wp-builder-locale' ),
					'options' => '', // array in case of dropdown, checkbox and radio buttons
				);
				?>
                <div class="cj-container cj-mb-50">
					<?php echo do_shortcode( $shortcode_string ) ?>
                </div>
                <hr>
                <div class="cj-form cj-mt-50">
                    <form action="" method="post">
                        <div class="cj-container">

                            <div class="cj-box">
                                <div class="cj-content">
                                    <h3 class="cj-title"><?php _e( 'Shortcode Code', 'wp-builder-locale' ) ?></h3>
                                    <div class="cj-field cj-has-addons">
                                        <div class="cj-control cj-is-expanded">
                                            <textarea readonly="readonly" class="cj-textarea cj-is-medium cj-fs-14" data-clipboard-text='<?php echo $shortcode_string; ?>'><?php echo $shortcode_string; ?></textarea>
                                        </div>
                                        <div class="cj-control">
                                            <a data-clipboard-text='<?php echo $shortcode_string; ?>' class="cj-button cj-is-info cj-is-medium"><i class="fa fa-copy"></i></a>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="cj-box">
                                <div class="cj-content">
                                    <h3 class="cj-title"><?php _e( 'Shortcode Options', 'wp-builder-locale' ) ?></h3>
                                </div>
								<?php echo $this->helpers->renderFrontendForm( $form_fields ); ?>
                            </div>
                        </div>
                    </form>
                </div>
			<?php } ?>
        </div>

        <style>
            html {
                margin: 0;
                padding: 0;
            }

            body {
                margin: 0;
                padding: 0;
                background: #efefef;
            }
        </style>

    </div>
<?php require_once 'previews/footer.php' ?>