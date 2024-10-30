<?php
global $wp_roles, $wp_registered_sidebars;
$html_fields = array('heading', 'sub-heading');
$no_control_wrapper = array('min-max','min_max', 'gradient');
$input_class = 'cj-input';

$exclude_control = array();
$text_fields = $this->inputTextFieldTypes();

//$text_fields['submit'] = 'submit';
$select_fields = $this->selectFields();
$select_fields['group'] = 'group';
?>
<div class="cjwpbldr-form">
    <div class="cj-columns cj-is-multiline cj-is-mobile">
		<?php
		if( is_array( $form_options ) && ! empty( $form_options ) ) {
			foreach( $form_options as $key => $option ) {
				if( isset( $form_defaults ) && is_array( $form_defaults ) && ! empty( $form_defaults ) ) {
					if( isset( $form_defaults[ $option['id'] ] ) ) {
						$option['default'] = $form_defaults[ $option['id'] ];
					}
				}

				$field_id = 'field-' . $option['id'];
				$field_class = (isset( $option['field_class'] )) ? $option['field_class'] : '';
				$control_class = (isset( $option['control_class'] )) ? $option['control_class'] : '';

				if( array_key_exists( 'column_class', $option ) ) {
					$column_class = $option['column_class'];
				} else if( array_key_exists( 'settings', $option ) ) {
					if( array_key_exists( 'columns', $option['settings'] ) && is_array( $option['settings']['columns'] ) && ! empty( $option['settings']['columns'] ) ) {
						$column_class = implode( ' ', $option['settings']['columns'] );
					} else {
						$column_class = 'cj-is-12-widescreen cj-is-12-desktop cj-is-12-tablet cj-is-12-mobile';
					}
				} else {
					$column_class = 'cj-is-12-widescreen cj-is-12-desktop cj-is-12-tablet cj-is-12-mobile';
				}

				//$column_class = (isset( $option['column_class'] )) ? $option['column_class'] : 'cj-is-12-widescreen cj-is-12-desktop cj-is-12-tablet cj-is-12-mobile';
				$show_control = true;

				$prefix = (isset( $option['prefix'] )) ? $option['prefix'] : '';
				$suffix = (isset( $option['suffix'] )) ? $option['suffix'] : '';

				if( ! in_array( $option['type'], $text_fields ) || $option['type'] == 'date-range' || $option['type'] == 'time-range' ) {
					$prefix = '';
					$suffix = '';
				}

				if( in_array( $option['type'], $text_fields ) && $prefix != '' || $suffix != '' ) {
					$field_class .= ' cj-has-addons';
					$control_class .= ' cj-is-expanded';
				}

				if( $option['type'] == 'hidden' ) {
					$column_class = 'cj-is-hidden';
				}

				if( $option['type'] == 'min-max' || $option['type'] == 'min_max' ) {
					$field_class = 'cj-is-grouped';
					$field_class = 'cj-is-grouped';
					$show_control = false;
				}

				if( in_array( $option['type'], ['file', 'user-avatar', 'user_avatar'] ) ) {
					$option['type'] = 'file-raw';
					//$option['label'] = 'Create media upload vue component for frontend';
				}

				$val_span = '';
				if( $this->isValidatedArray( $option, true, 'validations' ) ) {
					if( $this->isValidatedArray( $option['validations'], true, 'required' ) ) {
						$val_span = '<span class="cj-color-danger">*</span>';
					}
				}
				?>
				<?php if( isset( $option['id'] ) ) { ?>
                    <div class="cj-field-<?php echo $option['type']; ?> cj-column <?php echo $column_class ?>" id="<?php echo strtolower( str_replace( ' ', '-', $option['label'] ) ); ?>">
						<?php
						/**
						 * Custom WP Action to process custom functionality before field label.
						 * This would work on all specified/hooked field types
						 *
						 * @usage cjwpbldr_before_form_field_{$field_type}
						 *
						 *        For example: cjwpbldr_before_form_field_dropdown will run for
						 *        all dropdowns field types
						 *
						 * @param $option | array | Containing information of the field
						 *
						 * @since 1.8.3
						 */
						do_action( "cjwpbldr_before_form_field_{$option['type']}", $option );

						/**
						 * Custom WP Action to process custom functionality before field label.
						 * This would work on all specified/hooked field types and specified
						 * field ID
						 *
						 * @usage cjwpbldr_before_form_field_{$field_type}_{$field_id}
						 *
						 *        For example: cjwpbldr_before_form_field_dropdown_country will run for
						 *        all dropdowns field types and where the ID of the field is 'country'
						 *
						 * @param $option | array | Containing information of the field
						 *
						 * @since 1.8.3
						 */
						do_action( "cjwpbldr_before_form_field_{$option['type']}_{$option['id']}", $option );
						?>
						<?php if( $option['label'] !== '' ) { ?>
                            <label class="cj-label" for="<?php echo $option['id']; ?>">
								<?php echo $option['label']; ?>
								<?php echo $val_span; ?>
                            </label>
						<?php } ?>
                        <div class="cj-field <?php echo $field_class; ?>">
                            <!-- prefix -->
							<?php if( $prefix !== '' ) { ?>
                                <div class="cj-control">
                                    <button class="cj-button"><?php echo $prefix ?></button>
                                </div>
							<?php } ?>

                            <!--control-->
							<?php if($show_control){ ?>
                            <div class="cj-control <?php echo $control_class; ?>">
								<?php } ?>
                                <!--field-->
								<?php echo $this->formField( $option ) ?>

                                <!--control ends-->
								<?php if($show_control){ ?>
                            </div>
						<?php } ?>

                            <!-- suffix -->
							<?php if( $suffix !== '' ) { ?>
                                <div class="cj-control">
                                    <button class="cj-button"><?php echo $suffix ?></button>
                                </div>
							<?php } ?>
                            <!-- info -->
                        </div>
						<?php if( isset( $option['info'] ) && $option['info'] !== '' ) { ?>
                            <div class="cj-help"><?php echo $option['info']; ?></div>
						<?php } ?>
						<?php
						/**
						 * Custom WP Action to process custom functionality after field info.
						 * This would work on all specified/hooked field types
						 *
						 * @usage cjwpbldr_after_form_field_{$field_type}
						 *
						 *        For example: cjwpbldr_after_form_field_dropdown will run for
						 *        all dropdowns field types
						 *
						 * @param $option | array | Containing information of the field
						 *
						 * @since 1.8.3
						 */
						do_action( "cjwpbldr_after_form_field_{$option['type']}", $option );

						/**
						 * Custom WP Action to process custom functionality after field info.
						 * This would work on all specified/hooked field types and specified
						 * field ID
						 *
						 * @usage cjwpbldr_after_form_field_{$fiel_type}_{$field_id}
						 *
						 *        For example: cjwpbldr_after_form_field_dropdown_country will run for
						 *        all dropdowns field types and where the ID of the field is 'country'
						 *
						 * @param $option | array | Containing information of the field
						 *
						 * @since 1.8.3
						 */
						do_action( "cjwpbldr_after_form_field_{$option['type']}_{$option['id']}", $option );
						?>
                    </div>

				<?php } ?>
			<?php } ?>
		<?php } ?>
    </div>
</div>