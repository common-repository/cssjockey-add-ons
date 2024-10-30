<?php
global $wp_roles, $wp_registered_sidebars;

$wp_builder_key = get_transient( '_wp-builder_key' );
$wp_builder_pro_key = get_transient( '_wp-builder-pro_key' );

$html_fields = ['heading', 'sub-heading'];
$no_control_wrapper = ['min-max', 'min_max', 'gradient'];
$input_class = 'cj-input';

$exclude_control = [];
$text_fields = $this->inputTextFieldTypes();

//$text_fields['submit'] = 'submit';
$select_fields = $this->selectFields();
$select_fields['group'] = 'group';
?>
<div class="cjwpbldr-form">
	<?php
	if( is_array( $form_options ) && ! empty( $form_options ) ) {
		foreach( $form_options as $key => $option ) {

			if( $option['id'] == 'framework-install-products' ) {
				if( strlen( $wp_builder_key ) < 10 && strlen( $wp_builder_pro_key ) < 10 ) {
					$cloud_url = $this->queryString( $this->callbackUrl( 'core-cloud' ) ) . '&show=license';
					$option['default'] = sprintf( __( 'You must activate <a href="%s">WP Builder Cloud</a> to enable this feature.', 'wp-builder-locale' ), $cloud_url );
					$option['info'] = __( 'You can use your WP Builder FREE or PREMIUM license key to activate cloud features.', 'wp-builder-locale' );
				}
			}

			if( isset( $form_defaults ) && is_array( $form_defaults ) && ! empty( $form_defaults ) ) {
				if( isset( $form_defaults[ $option['id'] ] ) ) {
					$option['default'] = $form_defaults[ $option['id'] ];
				}
			}
			if( ! isset( $option['params'] ) ) {
				$option['params'] = [];
			}
			if( ! isset( $option['info'] ) ) {
				$option['info'] = '';
			}
			if( isset( $option['params'] ) && ! is_array( $option['params'] ) ) {
				$option['params'] = [];
			}
			if( ! isset( $option['params']['class'] ) ) {
				$option['params']['class'] = '';
			}
			$pretty_option_id = 'container-' . $this->cleanString( $option['id'] );
			$pretty_option_class = 'field-' . $this->cleanString( $option['id'] );
			$pretty_control_class = 'option-' . $this->cleanString( $option['id'] );

			if( isset( $option['container_class'] ) && $option['container_class'] !== '' ) {
				$pretty_control_class .= ' ' . $option['container_class'];
			}
			$searchable_class = 'cjwpbldr-quick-searchable';
			if( isset( $option['quick-searchable'] ) && $option['quick-searchable'] == false ) {
				$searchable_class = '';
			}
			$show_label = (isset( $option['show_label'] )) ? $option['show_label'] : 1;
			$gap_column_class = (isset( $option['gap_column_class'] )) ? $option['gap_column_class'] : 'cj-is-4 cj-hidden-mobile';
			$label_column_class = (isset( $option['label_column_class'] )) ? $option['label_column_class'] : '';
			$control_column_class = (isset( $option['control_column_class'] )) ? $option['control_column_class'] : '';
			$label_padding = (isset( $option['label_padding'] )) ? $option['label_padding'] : 'cj-p-15';
			$column_padding = (isset( $option['column_padding'] )) ? $option['column_padding'] : 'cj-p-15';
			$show_label_padding = (isset( $option['show_label_padding'] )) ? $option['show_label_padding'] : 1;
			$show_control = (isset( $option['show_control'] )) ? $option['show_control'] : 1;
			$show_control_wrapper = (isset( $option['show_control_wrapper'] )) ? $option['show_control_wrapper'] : 1;
			$show_control_padding = (isset( $option['show_control_padding'] )) ? $option['show_control_padding'] : 1;
			$show_gap = (isset( $option['show_gap'] )) ? $option['show_gap'] : 1;
			$show_info = (isset( $option['show_info'] )) ? $option['show_info'] : 1;
			$pretty_option_class = (isset( $option['option_class'] )) ? $option['option_class'] : $pretty_option_class;

			if( $option['type'] == 'repeatable-group' ) {
				$show_gap = 0;
			}

			if( ! in_array( $option['type'], $this->inputTextFieldTypes() ) ) {
				unset( $option['prefix'] );
				unset( $option['suffix'] );
			}

			$prefix_suffix_class = (isset( $option['prefix'] ) && $option['type'] != 'submit' && $option['prefix'] != '' || isset( $option['suffix'] ) && $option['suffix'] != '') ? 'cj-has-addons' : '';
			//$pretty_control_class = (isset( $option['control_class'] )) ? $option['control_class'] .' '. $pretty_control_class : $pretty_control_class;

			if( ! in_array( $option['type'], $text_fields ) && $option['type'] != 'submit' ) {
				$option['prefix'] = '';
				$option['suffix'] = '';
			}
			if( in_array( $option['type'], $select_fields ) && isset( $option['params'] ) ) {
				$option['params']['class'] = $option['params']['class'] . ' selectize';
				$show_control_wrapper = 0;
			}
			if( $option['type'] == 'button-with-icon' ) {
				$show_control_wrapper = 0;
			}
			if( in_array( $option['type'], $exclude_control ) ) {
				$show_control = 0;
			}
			if( $option['type'] == 'heading' ) {
				$show_label = 0;
				$show_info = 0;
				$show_control_padding = 0;
				$show_gap = 0;
				$searchable_class = '';
				$option['type'] = 'admin-heading';
				$show_control_wrapper = 0;
				$option['search_form'] = ( ! isset( $option['search_form'] )) ? 1 : $option['search_form'];
			}
			if( $option['type'] == 'info-full' ) {
				$show_label = 0;
				$show_info = 0;
				$show_control_padding = 0;
				$show_gap = 0;
				$option['type'] = 'admin-info-full';
				$option['search_form'] = 0;
			}
			if( $option['type'] == 'info' ) {
				$show_gap = 0;
			}
			if( $option['type'] == 'group' ) {
				$show_info = 0;
			}
			if( $option['type'] == 'sub-heading' ) {
				$show_label = 0;
				$show_info = 0;
				$show_control_padding = 0;
				$show_control_wrapper = 0;
				$show_gap = 0;
				$option['type'] = 'admin-heading';
			}
			if( $option['type'] == 'hidden' ) {
				$pretty_option_class = 'cj-hidden';
				$pretty_control_class = 'cj-hidden';
			}
			if( $option['type'] == 'submit' ) {
				$option['params']['class'] = $option['params']['class'] . ' cj-button cj-is-primary';
				$show_control_wrapper = 0;
				$prefix_suffix_class = '';
				$searchable_class = '';
				$show_info = 0;
			}
			if( in_array( $option['type'], $no_control_wrapper ) ) {
				$show_control_wrapper = 0;
				$pretty_option_class = ' cj-is-grouped';
			}
			if( $option['type'] == 'date-range' ) {
				$show_control_wrapper = 0;
				$option['prefix'] = '';
				$option['suffix'] = '';
			}
			if( $option['type'] == 'admin-info-full' ) {
				$show_control_wrapper = 0;
				$show_label = 0;
				$option['prefix'] = '';
				$option['suffix'] = '';
			}
			if( $option['type'] == 'responsive-columns' ) {
				$show_gap = 0;
			}
			if( $option['type'] == 'google-recaptcha' ) {
				$option['info'] = __( '<span class="cj-color-danger">This field only works on frontend.</span>', 'wp-builder-locale' );
			}
			if( $option['type'] == 'gradient' ) {
				$option['prefix'] = '';
				$option['suffix'] = '';
			}
			if( $option['type'] == 'country' ) {
				$option['params']['class'] = 'selectize';
			}

			if( $option['type'] == 'checkbox' || $option['type'] == 'radio'|| $option['type'] == 'checkbox-inline' || $option['type'] == 'radio-inline' ) {
				$option['params']['class'] = ' cj-is-checkradio';
			}

			if( $option['type'] == 'number' && is_string( $option['default'] ) ) {
				$option['default'] = $this->getNumbersFromString( $option['default'] );
			}
			if( $option['type'] == 'roles' && is_array( $option['default'] ) ) {
				if( is_array( $option['default'] ) && in_array( 'none', $option['default'] ) ) {
					$option['default'] = ['none'];
					$this->updateOption( $option['id'], $option['default'] );
				}
				if( in_array( 'everyone', $option['default'] ) ) {
					$option['default'] = ['everyone'];
					$this->updateOption( $option['id'], $option['default'] );
				}
			}
			$show_gap = (isset( $option['show_gap'] )) ? $option['show_gap'] : $show_gap;
			?>
			<?php if( isset( $option['id'] ) ) { ?>

                <div id="<?php echo $pretty_option_id; ?>" class="cj-columns cj-field-<?php echo $option['type']; ?> <?php echo $searchable_class; ?> cj-is-gapless cj-is-multiline <?php echo $pretty_control_class; ?>">
					<?php if( $show_label ) { ?>
                        <div class="cj-column <?php echo $label_column_class; ?> cj-is-narrow">
							<?php if( $show_label_padding ) {
								echo '<div class="' . $label_padding . '">';
							} ?>
                            <div style="width:250px">
								<?php if( isset( $option['label'] ) ) { ?>
                                    <span class="cj-label"><?php echo $option['label'] ?>
										<?php if( isset( $option['label_suffix'] ) && $option['label_suffix'] != '' ) { ?>
                                            <span class="cj-suffix"><?php echo $option['label_suffix'] ?></span>
										<?php } ?>
                                </span>
								<?php } ?>
                            </div>
							<?php if( $show_label_padding ) {
								echo '</div>';
							} ?>
                        </div>
					<?php } ?>
					<?php if( $show_control ) { ?>
                        <div class="cj-column <?php echo $control_column_class; ?>">
							<?php if( $show_control_padding ) {
								echo '<div class="' . $column_padding . '">';
							} ?>
                            <div class="cj-field cj-admin-field <?php echo $pretty_option_class ?> <?php echo $prefix_suffix_class ?>">
								<?php if( $option['type'] != 'submit' && isset( $option['prefix'] ) && $option['prefix'] != '' ) { ?>
                                    <div class="cj-control"><a class="cj-button"><?php echo $option['prefix'] ?></a></div>
								<?php } ?>
								<?php if( $show_control_wrapper == 1 ) {
									echo '<div class="cj-control cj-is-expanded">';
								} ?>

								<?php echo $this->formField( $option ); ?>

								<?php if( $show_control_wrapper == 1 ) {
									echo '</div>';
								} ?>
								<?php if( $option['type'] != 'submit' && isset( $option['suffix'] ) && $option['suffix'] != '' ) { ?>
                                    <div class="cj-control mb-0"><a class="cj-button"><?php echo $option['suffix'] ?></a></div>
								<?php } ?>
                            </div><!--cj-field-->
							<?php if( $show_info && $option['info'] !== '' ) { ?>
                                <div class="cj-help"><?php echo $option['info'] ?></div>
							<?php } ?>

							<?php
							// animation fields extended
							if( isset( $option['aos'] ) ) {
								include 'parts/admin-form-field-aos.php';
							}
							?>
							<?php if( $show_control_padding ) {
								echo '</div>';
							} ?>

                        </div>
					<?php } ?>
					<?php if( $show_gap ) { ?>
                        <!--<div class="cj-column cj-is-gap <?php /*echo $gap_column_class */ ?>">&nbsp;</div>-->
					<?php } ?>
                </div>
			<?php } ?>

		<?php }
	}
	?>
</div>
