<?php
add_action( 'cmb2_render_core_border_styles', function ( $field, $escaped_value, $object_id, $object_type, $field_type ) {

	$brand_colors = $this->helpers->arrays( 'brand-colors' );
	$brand_colors['white'] = 'White';
	$brand_colors['none'] = 'None';

	// make sure we assign each part of the value we need.
	$value = wp_parse_args( $escaped_value, array(
		'border_top_color' => '',
		'border_top_size' => '',
		'border_top_style' => '',
		'border_bottom_color' => '',
		'border_bottom_size' => '',
		'border_bottom_style' => '',
	) );
	ob_start();
	?>

    <div class="cj-columns cj-is-multiline cj-is-mobile">
        <div class="cj-column cj-is-4-fullhd cj-is-4-widescreen cj-is-4-desktop cj-is-12-tablet cj-is-12-mobile">
            <label class="cj-label cj-fs-14"><?php _e( 'Border Top Size', 'wp-builder-locale' ) ?></label>
			<?php
			echo $this->helpers->renderAdminFormField( array(
				'id' => $field_type->_name( '[border_top_size]' ),
				'type' => 'number',
				'params' => [
					'min' => 0,
					'class' => 'cj-is-medium',
					'style' => 'width: 100% !important;',
				],
				'default' => $value['border_top_size'],
			) );
			?>
        </div>
        <div class="cj-column cj-is-4-fullhd cj-is-4-widescreen cj-is-4-desktop cj-is-12-tablet cj-is-12-mobile">
            <label class="cj-label cj-fs-14"><?php _e( 'Border Top Style', 'wp-builder-locale' ) ?></label>
	        <?php
	        echo $this->helpers->renderAdminFormField( array(
		        'id' => $field_type->_name( '[border_top_style]' ),
		        'type' => 'select',
		        'class' => '',
		        'default' => $value['border_top_style'],
		        'options' => $this->helpers->arrays( 'border-style' )
	        ) );
	        ?>
        </div>
        <div class="cj-column cj-is-4-fullhd cj-is-4-widescreen cj-is-4-desktop cj-is-12-tablet cj-is-12-mobile">
            <label class="cj-label cj-fs-14"><?php _e( 'Border Top Color', 'wp-builder-locale' ) ?></label>
	        <?php
	        echo $this->helpers->renderAdminFormField( array(
		        'id' => $field_type->_name( '[border_top_color]' ),
		        'type' => 'select',
		        'class' => '',
		        'default' => $value['border_top_color'],
		        'options' => $brand_colors
	        ) );
	        ?>
        </div>
        <div class="cj-column cj-is-4-fullhd cj-is-4-widescreen cj-is-4-desktop cj-is-12-tablet cj-is-12-mobile">
            <label class="cj-label cj-fs-14"><?php _e( 'Border Bottom Size', 'wp-builder-locale' ) ?></label>
		    <?php
		    echo $this->helpers->renderAdminFormField( array(
			    'id' => $field_type->_name( '[border_bottom_size]' ),
			    'type' => 'number',
			    'params' => [
				    'min' => 0,
				    'class' => 'cj-is-medium',
				    'style' => 'width: 100% !important;',
			    ],
			    'default' => $value['border_bottom_size'],
		    ) );
		    ?>
        </div>
        <div class="cj-column cj-is-4-fullhd cj-is-4-widescreen cj-is-4-desktop cj-is-12-tablet cj-is-12-mobile">
            <label class="cj-label cj-fs-14"><?php _e( 'Border Bottom Style', 'wp-builder-locale' ) ?></label>
		    <?php
		    echo $this->helpers->renderAdminFormField( array(
			    'id' => $field_type->_name( '[border_bottom_style]' ),
			    'type' => 'select',
			    'class' => '',
			    'default' => $value['border_bottom_style'],
			    'options' => $this->helpers->arrays( 'border-style' )
		    ) );
		    ?>
        </div>
        <div class="cj-column cj-is-4-fullhd cj-is-4-widescreen cj-is-4-desktop cj-is-12-tablet cj-is-12-mobile">
            <label class="cj-label cj-fs-14"><?php _e( 'Border Bottom Color', 'wp-builder-locale' ) ?></label>
		    <?php
		    echo $this->helpers->renderAdminFormField( array(
			    'id' => $field_type->_name( '[border_bottom_color]' ),
			    'type' => 'select',
			    'class' => '',
			    'default' => $value['border_bottom_color'],
			    'options' => $brand_colors
		    ) );
		    ?>
        </div>

    </div>



	<?php
}, 10, 5 );
