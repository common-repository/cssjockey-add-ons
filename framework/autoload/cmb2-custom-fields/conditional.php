<?php
	add_action( 'cmb2_render_conditional', function ( $field, $escaped_value, $object_id, $object_type, $field_type ) {

		// make sure we assign each part of the value we need.
		$value       = wp_parse_args( $escaped_value, array(
			'conditional' => '',
			'options'     => '',
			'default'     => '',
		) );
		$screenshots = ( isset( $field->args['options'] ) && is_array( $field->args['options'] ) ) ? $field->args['options'] : [];
		$selected    = get_post_meta( $object_id, $field->args['id'] . '_selected', true );
		$defaults    = ( get_post_meta( $object_id, $field->args['id'], true ) != '' ) ? get_post_meta( $object_id, $field->args['id'], true ) : [];

		ob_start();
		$opts = '';
		if ( ! isset( $field->args['options_disable_blank'] ) ) {
			$opts = '<option value=""></option>';
		}
		foreach ( $field->args['options'] as $opt => $val ) {
			if ( $selected != '' && $opt == $selected ) {
				$opts .= '<option selected value="' . $opt . '">' . $val . '</option>';
			} else {
				$opts .= '<option value="' . $opt . '">' . $val . '</option>';
			}
		}
		?>
        <div class="cssjockey-ui">
            <div class="cjwpbldr-form" style="border: none;">
				<?php
					$field_html[] = '<div class="cj-form-dropdown">';
					$field_html[] = '<div class="cj-form-field cj-has-group-items" data-group-name=' . $field->args['group'] . '>';
					$field_html[] = '<select id="' . $field->args['id'] . '" name="' . $field->args['name'] . '" data-tags="true" class="selectize">';
					$field_html[] = $opts;
					$field_html[] = '</select>';
					$field_html[] = ( array_key_exists( 'info', $field->args ) ) ? '<div class="cj-help">' . $field->args['info'] . '</div>' : '';
					$field_html[] = '</div>';
					$field_html[] = '</div>';

					$group_options = $this->helpers->compileGroupItems( $field->args );

					if ( ! empty( $group_options ) ) {
						$field_html[] = '<div class="group-fields">';
						foreach ( $group_options as $g_key => $g_option ) {
							if ( ! empty( $defaults ) ) {
							    if( array_key_exists( $g_option['default_id'], $defaults['items'][ $selected ] ) ){
								    $default = $defaults['items'][ $selected ][ $g_option['default_id'] ];
                                }else{
								    $default = $g_option['default'];
                                }
							} else {
								$default = $g_option['default'];
							}
							$field_html[] = '<div class="cj-field ' . $g_option['container_class'] . '">';
							$field_html[] = '<label class="cj-label">' . $g_option['label'] . '</label>';
							$field_html[] = '<p class="cj-control">';
							//$field_html[] = $this->helpers->formField( $g_option );
							$field_html[] = $this->helpers->renderAdminFormField( array(
								'id'      => $g_option['id'],
								'type'    => $g_option['type'],
								'default' => $default,
								'options' => ( array_key_exists( 'options', $g_option ) ) ? $g_option['options'] : []
							) );
							$field_html[] = '</p>';
							if ( isset( $g_option['info'] ) ) {
								$field_html[] = '<div class="cj-help">' . $g_option['info'] . '</div>';
							}
							$field_html[] = '</div>';
						}
						$field_html[] = '</div>';
					}

					echo implode( ' ', $field_html );
				?>
            </div>
        </div>
		<?php
	}, 10, 5 );