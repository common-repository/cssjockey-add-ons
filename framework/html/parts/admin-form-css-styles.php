<?php
$text_style_options = array(
	array(
		'type' => 'color',
		'id' => $option['id'] . '[color]',
		'label' => __( 'Text Color', 'wp-builder-locale' ),
		'default' => '#666666',
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'number',
		'id' => $option['id'] . '[font-size]',
		'label' => __( 'Font Size', 'wp-builder-locale' ),
		'default' => '',
		'suffix' => 'px',
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'number',
		'id' => $option['id'] . '[line-height]',
		'label' => __( 'Line Height', 'wp-builder-locale' ),
		'default' => '',
		'suffix' => 'px',
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'select',
		'id' => $option['id'] . '[font-weight]',
		'label' => __( 'Font Weight', 'wp-builder-locale' ),
		'default' => '',
		'suffix' => '',
		'options' => $this->arrays( 'font-weight' ), // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'select',
		'id' => $option['id'] . '[font-style]',
		'label' => __( 'Font Style', 'wp-builder-locale' ),
		'default' => '',
		'suffix' => '',
		'options' => $this->arrays( 'font-style' ), // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'select',
		'id' => $option['id'] . '[text-decoration]',
		'label' => __( 'Text Decoration', 'wp-builder-locale' ),
		'default' => '',
		'suffix' => '',
		'options' => $this->arrays( 'text-decoration' ), // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'select',
		'id' => $option['id'] . '[font-variant]',
		'label' => __( 'Font Variant', 'wp-builder-locale' ),
		'default' => '',
		'suffix' => '',
		'options' => $this->arrays( 'font-variant' ), // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'select',
		'id' => $option['id'] . '[text-transform]',
		'label' => __( 'Text Transform', 'wp-builder-locale' ),
		'default' => '',
		'suffix' => '',
		'options' => $this->arrays( 'text-transform' ), // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'select',
		'id' => $option['id'] . '[font-family]',
		'label' => __( 'Font Family', 'wp-builder-locale' ),
		'default' => '',
		'suffix' => '',
		'options' => $this->getGoogleFonts(), // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'select',
		'id' => $option['id'] . '[text-align]',
		'label' => __( 'Text Align', 'wp-builder-locale' ),
		'default' => '',
		'suffix' => '',
		'options' => $this->arrays( 'text-align' ), // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'number',
		'id' => $option['id'] . '[text-indent]',
		'label' => __( 'Text Indent', 'wp-builder-locale' ),
		'default' => '',
		'suffix' => 'px',
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'number',
		'id' => $option['id'] . '[letter-spacing]',
		'label' => __( 'Letter Spacing', 'wp-builder-locale' ),
		'default' => '',
		'suffix' => 'px',
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'number',
		'id' => $option['id'] . '[word-spacing]',
		'label' => __( 'Word Spacing', 'wp-builder-locale' ),
		'default' => '',
		'suffix' => 'px',
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'select',
		'id' => $option['id'] . '[white-space]',
		'label' => __( 'White Space', 'wp-builder-locale' ),
		'default' => '',
		'suffix' => '',
		'options' => $this->arrays( 'white-space' ), // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'text',
		'id' => $option['id'] . '[text-shadow]',
		'label' => __( 'Text Shadow', 'wp-builder-locale' ),
		'default' => '',
		'suffix' => '',
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	),
);
$decoration_style_options = array(
	array(
		'type' => 'color',
		'id' => $option['id'] . 'background-color',
		'label' => __( 'Background Color', 'wp-builder-locale' ),
		'default' => 'none',
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'files',
		'id' => $option['id'] . 'background-image',
		'label' => __( 'Background Image', 'wp-builder-locale' ),
		'default' => '',
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'select',
		'id' => $option['id'] . 'background-position',
		'label' => __( 'Background Position', 'wp-builder-locale' ),
		'default' => '',
		'options' => $this->arrays( 'background-position' ), // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'select',
		'id' => $option['id'] . 'background-repeat',
		'label' => __( 'Background Repeat', 'wp-builder-locale' ),
		'default' => '',
		'options' => $this->arrays( 'background-repeat' ), // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'select',
		'id' => $option['id'] . 'background-size',
		'label' => __( 'Background Size', 'wp-builder-locale' ),
		'default' => '',
		'options' => $this->arrays( 'background-size' ), // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'select',
		'id' => $option['id'] . 'background-attachment',
		'label' => __( 'Background Size', 'wp-builder-locale' ),
		'default' => '',
		'options' => $this->arrays( 'background-attachment' ), // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'text',
		'id' => $option['id'] . 'box-shadow',
		'label' => __( 'Box Shadow', 'wp-builder-locale' ),
		'default' => '',
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'text',
		'id' => $option['id'] . 'opacity',
		'label' => __( 'Opacity', 'wp-builder-locale' ),
		'default' => '',
		'suffix' => '%',
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'css-shorthand',
		'id' => $option['id'] . 'border',
		'label' => __( 'Borders', 'wp-builder-locale' ),
		'default' => '',
		'suffix' => '',
		'params' => array('placeholder' => '1px solid #ddd'),
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	),
);
$layout_style_options = array(
	array(
		'type' => 'css-shorthand',
		'id' => $option['id'] . 'margin',
		'label' => __( 'Margin', 'wp-builder-locale' ),
		'params' => array('placeholder' => '10px'),
		'default' => '',
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'css-shorthand',
		'id' => $option['id'] . 'padding',
		'label' => __( 'Padding', 'wp-builder-locale' ),
		'params' => array('placeholder' => '10px'),
		'default' => '',
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'select',
		'id' => $option['id'] . 'position',
		'label' => __( 'Position', 'wp-builder-locale' ),
		'default' => '',
		'options' => $this->arrays( 'position' ), // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'css-shorthand',
		'id' => $option['id'] . 'position',
		'label' => __( 'Position #', 'wp-builder-locale' ),
		'params' => array('placeholder' => 'px or auto'),
		'default' => '',
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'number',
		'id' => $option['id'] . 'width',
		'label' => __( 'Width', 'wp-builder-locale' ),
		'suffix' => 'px',
		'default' => '',
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'number',
		'id' => $option['id'] . 'height',
		'label' => __( 'Height', 'wp-builder-locale' ),
		'suffix' => 'px',
		'default' => '',
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'select',
		'id' => $option['id'] . 'display',
		'label' => __( 'Display', 'wp-builder-locale' ),
		'suffix' => 'px',
		'default' => '',
		'options' => $this->arrays( 'display' ), // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'select',
		'id' => $option['id'] . 'float',
		'label' => __( 'Float', 'wp-builder-locale' ),
		'suffix' => '',
		'default' => '',
		'options' => $this->arrays( 'float' ), // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'select',
		'id' => $option['id'] . 'clear',
		'label' => __( 'Clear', 'wp-builder-locale' ),
		'suffix' => '',
		'default' => '',
		'options' => $this->arrays( 'clear' ), // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'select',
		'id' => $option['id'] . 'visibility',
		'label' => __( 'Visibility', 'wp-builder-locale' ),
		'suffix' => '',
		'default' => '',
		'options' => $this->arrays( 'visibility' ), // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'select',
		'id' => $option['id'] . 'overflow',
		'label' => __( 'Overflow', 'wp-builder-locale' ),
		'suffix' => '',
		'default' => '',
		'options' => $this->arrays( 'overflow' ), // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'select',
		'id' => $option['id'] . 'overflow-x',
		'label' => __( 'Overflow X', 'wp-builder-locale' ),
		'suffix' => '',
		'default' => '',
		'options' => $this->arrays( 'overflow' ), // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'select',
		'id' => $option['id'] . 'overflow-y',
		'label' => __( 'Overflow Y', 'wp-builder-locale' ),
		'suffix' => '',
		'default' => '',
		'options' => $this->arrays( 'overflow' ), // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'number',
		'id' => $option['id'] . 'z-index',
		'label' => __( 'Z-Index', 'wp-builder-locale' ),
		'suffix' => '',
		'default' => '',
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	),
);
$custom_style_options = array(
	array(
		'type' => 'code-css',
		'id' => $option['id'] . 'custom_css',
		'label' => __( 'Custom CSS', 'wp-builder-locale' ),
		'default' => '',
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'type' => 'text',
		'id' => $option['id'] . 'custom_classes',
		'label' => __( 'Custom CSS Classes', 'wp-builder-locale' ),
		'params' => array('placeholder' => 'classname, classname ...'),
		'default' => '',
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	),
);
?>

<a data-toggle="class" data-classes="cj-is-active" data-target="#cj-css-styles-<?php echo $form_field_id; ?>" class="cj-button cj-is-info"><i class="fa fa-css3 cj-mr-5"></i> <?php _e( 'CSS Styles', 'wp-builder-locale' ) ?></a>


<div id="cj-css-styles-<?php echo $form_field_id; ?>" class="cj-modal">
    <div data-toggle="class" data-classes="cj-is-active" data-target="#cj-css-styles-<?php echo $form_field_id; ?>" class="cj-modal-background"></div>
    <div class="cj-modal-card cj-width-50">
        <header class="cj-modal-card-head">
            <p class="cj-modal-card-title"><?php _e('CSS Styles', 'wp-builder-locale' ) ?></p>
            <button data-toggle="class" data-classes="cj-is-active" data-target="#cj-css-styles-<?php echo $form_field_id; ?>" class="cj-delete"></button>
        </header>
        <section class="cj-modal-card-body cj-p-0">
            <div class="cj-content">
                <div class="">
                    <div class="cj-field cj-has-addons cj-m-0">
                        <p class="cj-control cj-m-0">
                            <a data-toggle="element" data-target=".text-styles" class="cj-button cj-is-info cj-no-borders cj-is-active">
                                <span class="cj-icon cj-is-small"><i class="fa fa-align-left"></i></span><span><?php _e( 'Text Styles', 'wp-builder-locale' ) ?></span>
                            </a>
                            <a data-toggle="element" data-target=".decoration-styles" class="cj-button cj-is-info cj-no-borders">
                                <span class="cj-icon cj-is-small"><i class="fa fa-paint-brush"></i></span><span><?php _e( 'Decoration', 'wp-builder-locale' ) ?></span>
                            </a>
                            <a data-toggle="element" data-target=".layout-styles" class="cj-button cj-is-info cj-no-borders">
                                <span class="cj-icon cj-is-small"><i class="fa fa-columns"></i></span><span><?php _e( 'Layout', 'wp-builder-locale' ) ?></span>
                            </a>
                            <a data-toggle="element" data-target=".custom-css" class="cj-button cj-is-info cj-no-borders">
                                <span class="cj-icon cj-is-small"><i class="fa fa-code"></i></span><span><?php _e( 'Custom CSS', 'wp-builder-locale' ) ?></span>
                            </a>
                        </p>
                    </div>
                </div>

                <div class="cj-toggle-group">
                    <div class="text-styles cj-is-active">
				        <?php echo $this->renderAdminForm( $text_style_options ); ?>
                    </div>
                    <div class="decoration-styles">
				        <?php echo $this->renderAdminForm( $decoration_style_options ); ?>
                    </div>
                    <div class="layout-styles">
				        <?php echo $this->renderAdminForm( $layout_style_options ); ?>
                    </div>
                    <div class="custom-css">
				        <?php echo $this->renderAdminForm( $custom_style_options ); ?>
                    </div>
                </div>


            </div>
        </section>
    </div>
</div>