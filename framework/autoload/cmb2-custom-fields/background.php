<?php
add_action( 'cmb2_render_background', function ( $field, $escaped_value, $object_id, $object_type, $field_type ) {

	$brand_colors = $this->helpers->arrays( 'brand-colors' );
	$brand_colors['white'] = 'White';

	$disabledOptions = (isset($field->args['disabled'])) ? $field->args['disabled'] : [];

	// make sure we assign each part of the value we need.
	$value = wp_parse_args( $escaped_value, array(
		'image' => '',
		'gradient_start' => '',
		'gradient_end' => '',
		'gradient_degree' => '',
		'background_color' => '',
		'opacity' => '',
		'overlay' => '',
		'text_color' => '',
		'headings_color' => '',
		'links_color' => '',
	) );
	ob_start();
	?>
    <div class="cssjockey-ui">
        <div class="cj-content">
            <!-- For Background Image -->
			<?php if( is_array($disabledOptions) && !in_array('image', $disabledOptions) ){ ?>
                <div class="cj-message cj-p-15 cj-mb-10 cj-bd cj-bd-light">
                    <div class="cj-field">
                        <div class="cj-control">
                            <label class="cj-label cj-fs-14"><?php _e( 'Background Image', 'wp-builder-locale' ) ?></label>
                            <span class="cmb-upload">
                            <?php echo $field_type->file( array(
	                            'name' => $field_type->_name( '[image]' ),
	                            'value' => $value['image'],
	                            'default' => '',
                            ) ); ?>
                        </span>
                        </div>
                    </div>
                </div>
			<?php } ?>

            <!-- For Gradient settings -->
			<?php if( is_array($disabledOptions) && !in_array('gradient', $disabledOptions) ){ ?>
                <div class="cj-message cj-p-15 cj-mb-10 cj-bd cj-bd-light">
                    <div class="cj-field cj-is-grouped">
                        <div class="cj-control cj-is-expanded">
                            <label class="cj-label cj-fs-14"><?php _e( 'Gradient Start Color', 'wp-builder-locale' ) ?></label>
							<?php
							echo $this->helpers->renderAdminFormField( array(
								'id' => $field_type->_name( '[gradient_start]' ),
								'type' => 'color',
								'default' => $value['gradient_start'],
							) );
							?>
                        </div>
                        <div class="cj-control cj-is-expanded">
                            <label class="cj-label cj-fs-14"><?php _e( 'Gradient End Color', 'wp-builder-locale' ) ?></label>
							<?php
							echo $this->helpers->renderAdminFormField( array(
								'id' => $field_type->_name( '[gradient_end]' ),
								'type' => 'color',
								'default' => $value['gradient_end'],
							) );
							?>
                        </div>
                        <div class="cj-control cj-is-expanded">
                            <label class="cj-label cj-fs-14"><?php _e( 'Gradient Direction', 'wp-builder-locale' ) ?></label>
							<?php
							echo $this->helpers->renderAdminFormField( array(
								'id' => $field_type->_name( '[gradient_degree]' ),
								'type' => 'number',
								'default' => $value['gradient_degree'],
							) );
							?>
                        </div>
                    </div>
                </div>
			<?php } ?>

            <!-- For Background Color -->
			<?php if( is_array($disabledOptions) && !in_array('background_color', $disabledOptions) ){ ?>
                <div class="cj-message cj-p-15 cj-mb-10 cj-bd cj-bd-light">
                    <div class="cj-field">
                        <div class="cj-control">
                            <label class="cj-label cj-fs-14"><?php _e( 'Background Color', 'wp-builder-locale' ) ?></label>
							<?php
							echo $this->helpers->renderAdminFormField( array(
								'id' => $field_type->_name( '[background_color]' ),
								'type' => 'select',
								'default' => $value['background_color'],
								'options' => $brand_colors,
							) );
							?>
                        </div>
                    </div>
                </div>
			<?php } ?>

            <!-- For Overlay Settings -->
			<?php if( is_array($disabledOptions) && !in_array('overlay', $disabledOptions) ){ ?>
                <div class="cj-message cj-p-15 cj-mb-10 cj-bd cj-bd-light">
                    <div class="cj-field cj-is-grouped">
                        <div class="cj-control cj-is-expanded">
                            <label class="cj-label cj-fs-14"><?php _e( 'Overlay Style', 'wp-builder-locale' ) ?></label>
							<?php
							echo $this->helpers->renderAdminFormField( array(
								'id' => $field_type->_name( '[overlay]' ),
								'type' => 'select',
								'default' => $value['overlay'],
								'options' => $brand_colors,
							) );
							?>
                        </div>
                        <div class="cj-control cj-is-expanded">
                            <label class="cj-label cj-fs-14"><?php _e( 'Overlay Opacity', 'wp-builder-locale' ) ?></label>
							<?php
							echo $this->helpers->renderAdminFormField( array(
								'id' => $field_type->_name( '[opacity]' ),
								'type' => 'select',
								'default' => $value['opacity'],
								'options' => $this->helpers->arrays( 'opacity' ),
							) );
							?>
                        </div>
                    </div>
                </div>
			<?php } ?>


            <!-- For Text Color -->
			<?php if( is_array($disabledOptions) && !in_array('text_color', $disabledOptions) ){ ?>
                <div class="cj-message cj-p-15 cj-mb-10 cj-bd cj-bd-light">
                    <div class="cj-field cj-is-grouped">
                        <div class="cj-control cj-is-expanded">
                            <label class="cj-label cj-fs-14"><?php _e( 'Text Color', 'wp-builder-locale' ) ?></label>
							<?php
							echo $this->helpers->renderAdminFormField( array(
								'id' => $field_type->_name( '[text_color]' ),
								'type' => 'select',
								'class' => 'selectize',
								'default' => $value['text_color'],
								'options' => $brand_colors
							) );
							?>
                        </div>
                        <div class="cj-control cj-is-expanded">
                            <label class="cj-label cj-fs-14"><?php _e( 'Headings Color', 'wp-builder-locale' ) ?></label>
							<?php
							echo $this->helpers->renderAdminFormField( array(
								'id' => $field_type->_name( '[headings_color]' ),
								'type' => 'select',
								'class' => 'selectize',
								'default' => $value['headings_color'],
								'options' => $brand_colors
							) );
							?>
                        </div>
                        <div class="cj-control cj-is-expanded">
                            <label class="cj-label cj-fs-14"><?php _e( 'Links Color', 'wp-builder-locale' ) ?></label>
							<?php
							echo $this->helpers->renderAdminFormField( array(
								'id' => $field_type->_name( '[links_color]' ),
								'type' => 'select',
								'class' => 'selectize',
								'default' => $value['links_color'],
								'options' => $brand_colors
							) );
							?>
                        </div>
                    </div>
                </div>
			<?php } ?>
        </div>
    </div>
	<?php
}, 10, 5 );
