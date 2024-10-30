<?php
$meta_keys = $this->metaKeysArray( $option['table'] );
$default_values = array(
	'label' => (isset( $option['default']['label'] )) ? $option['default']['label'] : '',
	'meta_key' => (isset( $option['default']['meta_key'] )) ? $option['default']['meta_key'] : '',
);
?>
<div class="cj-clearfix">
    <div class="cj-mb-0 cj-one_third">
        <input name="<?php echo $option['id'] ?>[label]" type="text" class="cj-input" value="<?php echo $default_values['label']; ?>" placeholder="<?php _e( 'Label', 'wp-builder-locale' ) ?>">
    </div>
    <div class="cj-mb-0 cj-two_third cj-last">
        <select name="<?php echo $option['id'] ?>[meta_key]" id="" class="selectize" placeholder="<?php _e( 'Select meta key', 'wp-builder-locale' ) ?>">
			<?php
			if( ! empty( $meta_keys ) ) {
				echo '<option value=""></option>';
				foreach( $meta_keys as $m_key => $m_value ) {
					if( $default_values['meta_key'] == $m_key ) {
						echo '<option selected value="' . $m_key . '">' . $m_value . '</option>';
					} else {
						echo '<option value="' . $m_key . '">' . $m_value . '</option>';
					}
				}
			}
			?>
        </select>
    </div>
</div>