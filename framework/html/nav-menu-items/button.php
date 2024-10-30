<?php
$button_classes = [];
$button_classes['cj-button'] = 'cj-button';
if( isset( $item->cjwpbldr_button_style ) ) {
	$button_classes['button_style'] = $item->cjwpbldr_button_style;
}
if( isset( $item->cjwpbldr_button_size ) ) {
	$button_classes['button_size'] = $item->cjwpbldr_button_size;
}
if( isset( $item->cjwpbldr_button_extra_style ) ) {
	$button_classes['button_extra_style'] = $item->cjwpbldr_button_extra_style;
}
$classes = array_filter( $classes );
?>
<a href="<?php echo $item->url ?>" class="<?php echo implode( ' ', $classes ) . implode( ' ', $button_classes ); ?>" <?php echo $target . ' ' . $title_tag . ' ' . $rel; ?>>
	<?php echo $args->before ?>
	<?php echo $title . $arrow_down ?>
	<?php echo $args->after ?>
</a>


