<?php
	add_action( 'cmb2_render_screenshot', function ( $field, $escaped_value, $object_id, $object_type, $field_type ) {

		// make sure we assign each part of the value we need.
		$value    = wp_parse_args( $escaped_value, array(
			'screenshot' => '',
			'options'    => '',
			'default'    => '',
		) );
		$screenshots  = ( isset( $field->args['options'] ) && is_array( $field->args['options'] ) ) ? $field->args['options'] : [];
		$selected = ( $field->value != '' ) ? $field->value : '';
		ob_start();
		?>
        <div class="cssjockey-ui">
            <div class="cjwpbldr-form" style="border:none;">
                <div class="cj-message cj-p-15 cj-mb-10 cj-bd cj-bd-light">
                    <div class="cj-field cj-is-grouped">
                        <div class="cj-control cj-is-expanded">
							<?php
                                if ( is_array( $screenshots ) && ! empty( $screenshots ) ) {
									$field_html = '<div class="cj-columns cj-field-screenshot cj-quick-searchable cj-is-multiline cj-is-mobile" style="border:none">';
									foreach ( $screenshots as $s_key => $screenshot_url ) {
										$checked      = ( $selected == $s_key ) ? 'checked' : '';
										$active_class = ( $selected == $s_key ) ? 'cj-is-active' : '';
									    $field_html .= '<div class="cj-column cj-exclude-full cj-is-4-widescreen cj-is-4-desktop cj-is-6-tablet cj-is-6-mobile cj-pt-15 cj-pb-0">';
										$field_html .= '<label class="' . $active_class . '">';
										$field_html .= '<input type="radio" name="' . $field->args['_name'] . '" value="' . $s_key . '" ' . $checked . '>';
										$field_html .= '<img src="' . $screenshot_url . '" />';
										$field_html .= '</label>';
										$field_html .= '</div>';
									}
									$field_html .= '</div><!-- cj-columns -->';
								}
								echo $field_html;
							?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<?php
	}, 10, 5 );