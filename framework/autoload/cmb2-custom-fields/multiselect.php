<?php
add_action( 'cmb2_render_multiselect', function ( $field, $escaped_value, $object_id, $object_type, $field_type ) {
	$value = wp_parse_args( $escaped_value, array(
		'options' => '',
		'default' => '',
	) );
	$options = (isset( $field->args['options'] ) && is_array( $field->args['options'] )) ? $field->args['options'] : [];
	$description = (isset( $field->args['description'] ) && $field->args['description'] != '') ? $field->args['description'] : '';
	$selected = (is_array( $field->value )) ? $field->value : [];
	ob_start();
	?>
    <div class="cssjockey-ui">
        <div class="cj-content">
            <div class="cj-message cj-p-15 cj-mb-10 cj-bd cj-bd-light">
                <div class="cj-field">
                    <label for="<?php echo $field->args['_id'] ?>" class="cj-label cj-fs-14"><?php echo $field_type->field->args['name']; ?></label>
                    <div class="cj-control cj-is-expanded">
                        <select style="min-height: 40px;" name="<?php echo $field->args['_name'] . '[]' ?>" id="<?php echo $field->args['_id'] ?>" multiple class="cj-select2 selectize" style="width: 100%;">
							<?php foreach( $options as $key => $option ) { ?>
                                <option <?php echo (in_array( $key, $selected )) ? 'selected' : ''; ?> value="<?php echo $key; ?>"><?php echo $option; ?></option>
								<?php
							} ?>
                        </select>
                    </div>
                </div>
	            <?php
	            if( '' != $description ) {
		            echo '<p>' . $description . '</p>';
	            }
	            ?>
            </div>
        </div>
    </div>
	<?php

}, 10, 5 );