<?php
add_action( 'cmb2_render_aos', function ( $field, $escaped_value, $object_id, $object_type, $field_type ) {

	// make sure we assign each part of the value we need.
	$value = wp_parse_args( $escaped_value, array(
		'animation' => '',
		'easing' => '',
		'duration' => '',
		'delay' => '',
	) );
	ob_start();
	?>
    <div class="cssjockey-ui">
        <div class="cj-content">
            <div class="cj-message cj-p-15 cj-mb-10 cj-bd cj-bd-light">
                <div class="cj-field cj-is-grouped">
                    <div class="cj-control cj-is-expanded">
                        <label class="cj-label cj-fs-14"><?php _e( 'Animation Type', 'wp-builder-locale' ) ?></label>
	                    <?php
		                    echo $this->helpers->renderAdminFormField( array(
			                    'id' => $field_type->_name( '[animation]' ),
			                    'type' => 'select',
			                    'default' => $value['animation'],
			                    'options' => $this->helpers->aosAnimationsArray(),
		                    ) );
	                    ?>
                    </div>
                    <div class="cj-control cj-is-expanded">
                        <label class="cj-label cj-fs-14"><?php _e( 'Animation Easing', 'wp-builder-locale' ) ?></label>
	                    <?php
		                    echo $this->helpers->renderAdminFormField( array(
			                    'id' => $field_type->_name( '[easing]' ),
			                    'type' => 'select',
			                    'default' => $value['easing'],
			                    'options' => $this->helpers->aosEasingArray(),
		                    ) );
	                    ?>
                    </div>
                    <div class="cj-control cj-is-expanded">
                        <label class="cj-label cj-fs-14"><?php _e( 'Animation Speed', 'wp-builder-locale' ) ?></label>
		                <?php
			                echo $this->helpers->renderAdminFormField( array(
				                'id' => $field_type->_name( '[duration]' ),
				                'type' => 'number',
				                'default' => $value['duration'],
				                'options' => '',
			                ) );
		                ?>
                    </div>
                    <div class="cj-control cj-is-expanded">
                        <label class="cj-label cj-fs-14"><?php _e( 'Animation Delay', 'wp-builder-locale' ) ?></label>
		                <?php
			                echo $this->helpers->renderAdminFormField( array(
				                'id' => $field_type->_name( '[delay]' ),
				                'type' => 'number',
				                'default' => $value['delay'],
				                'options' => '',
			                ) );
		                ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
	<?php
}, 10, 5 );