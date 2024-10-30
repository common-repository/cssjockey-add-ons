<?php
$saved_values = $option['default'];
$font_family = array(
	'id' => $option['id'] . '[font-family]',
	'type' => 'dropdown',
	'default' => (isset( $saved_values['font-family'] ) && $saved_values['font-family'] != '') ? $saved_values['font-family'] : '',
	'params' => array(
		'placeholder' => __( 'Select font family', 'wp-builder-locale' ),
		'class' => 'selectize',
	),
	'options' => $this->getGoogleFonts(),
);

$font_size = array(
	'id' => $option['id'] . '[font-size]',
	'type' => 'number',
	'suffix' => __( 'px', 'wp-builder-locale' ),
	'default' => (isset( $saved_values['font-size'] ) && $saved_values['font-size'] != '') ? $saved_values['font-size'] : '',
	'params' => array(
		'placeholder' => __( 'Font size', 'wp-builder-locale' ),
		'min' => '0',
		'max' => '100',
	),
	'options' => '',
);

$line_height = array(
	'id' => $option['id'] . '[line-height]',
	'type' => 'number',
	'suffix' => __( 'px', 'wp-builder-locale' ),
	'default' => (isset( $saved_values['line-height'] ) && $saved_values['line-height'] != '') ? $saved_values['line-height'] : '',
	'params' => array(
		'placeholder' => __( 'Line height', 'wp-builder-locale' ),
		'min' => '0',
		'max' => '100',
	),
	'options' => '',
);

$font_weight = array(
	'id' => $option['id'] . '[font-weight]',
	'type' => 'select',
	'default' => (isset( $saved_values['font-weight'] ) && $saved_values['font-weight'] != '') ? $saved_values['font-weight'] : '',
	'params' => array('placeholder' => __( 'Font weight', 'wp-builder-locale' ), 'class' => 'selectize'),
	'options' => $this->arrays( 'font-weight' ),
);

$font_color = array(
	'id' => $option['id'] . '[color]',
	'type' => 'color',
	'default' => (isset( $saved_values['color'] ) && $saved_values['color'] != '') ? $saved_values['color'] : __( 'transparent', 'wp-builder-locale' ),
);
$font_style = array(
	'id' => $option['id'] . '[font-style]',
	'type' => 'select',
	'default' => (isset( $saved_values['font-style'] ) && $saved_values['font-style'] != '') ? $saved_values['font-style'] : '',
	'params' => array('placeholder' => __( 'Font style', 'wp-builder-locale' ), 'class' => 'selectize'),
	'options' => $this->arrays( 'font-style' ),
);

$text_align = array(
	'id' => $option['id'] . '[text-align]',
	'type' => 'select',
	'default' => (isset( $saved_values['text-align'] ) && $saved_values['text-align'] != '') ? $saved_values['text-align'] : '',
	'params' => array('placeholder' => __( 'Font align', 'wp-builder-locale' ), 'class' => 'selectize'),
	'options' => $this->arrays( 'text-align' ),
);

?>
<div class="cj-mb-10">
    <label class="cj-label"><?php _e( 'Font family', 'wp-builder-locale' ) ?></label>
    <div class="cj-field">
		<?php echo $this->formField( $font_family ) ?>
    </div>
</div>
<div class="cj-clearfix">
    <div class="cj-one_third">
        <label class="cj-label"><?php _e( 'Font size', 'wp-builder-locale' ) ?></label>
        <div class="cj-field cj-has-addons">
            <p class="cj-control">
				<?php echo $this->formField( $font_size ) ?>
            </p>
            <p class="cj-control">
                <button class="cj-button">px</button>
            </p>
        </div>
    </div>
    <div class="cj-one_third">
        <label class="cj-label"><?php _e( 'Line height', 'wp-builder-locale' ) ?></label>
        <div class="cj-field cj-has-addons">
            <p class="cj-control">
				<?php echo $this->formField( $line_height ) ?>
            </p>
            <p class="cj-control">
                <button class="cj-button">px</button>
            </p>
        </div>
    </div>
    <div class="cj-one_third  cj-last">
        <label class="cj-label"><?php _e( 'Text color', 'wp-builder-locale' ) ?></label>
        <div class="cj-field cj-field-color">
            <p class="cj-control">
				<?php echo $this->formField( $font_color ) ?>
            </p>
        </div>
    </div>
    <div class="cj-one_third">
        <label class="cj-label"><?php _e( 'Font weight', 'wp-builder-locale' ) ?></label>
        <div class="cj-field">
            <p class="cj-control">
				<?php echo $this->formField( $font_weight ) ?>
            </p>
        </div>
    </div>
    <div class="cj-one_third">
        <label class="cj-label"><?php _e( 'Font style', 'wp-builder-locale' ) ?></label>
        <div class="cj-field">
            <p class="cj-control">
				<?php echo $this->formField( $font_style ) ?>
            </p>
        </div>
    </div>
    <div class="cj-one_third cj-last">
        <label class="cj-label"><?php _e( 'Text align', 'wp-builder-locale' ) ?></label>
        <div class="cj-field">
            <p class="cj-control">
				<?php echo $this->formField( $text_align ) ?>
            </p>
        </div>
    </div>
</div>