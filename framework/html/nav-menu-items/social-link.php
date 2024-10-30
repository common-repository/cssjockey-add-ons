<a href="<?php echo $item->url ?>" class="<?php echo implode( ' ', $classes ); ?>" <?php echo $target . ' ' . $title_tag . ' ' . $rel; ?>>
	<?php echo $args->before ?>
	<?php
	if( isset( $item->cjwpbldr_social_icon ) && $item->cjwpbldr_social_icon != '' ) {
		$icon_style = '';
		if( isset( $item->cjwpbldr_social_icon_style ) && $item->cjwpbldr_social_icon_style !== 'default' ) {
			$icon_style = $item->cjwpbldr_social_icon_style;
		}
		echo '<span class="cj-icon cj-is-small"><i class="' . $item->cjwpbldr_social_icon . ' ' . $icon_style . ' cj-br-4"></i></span>';
	}
	?>
	<?php echo $args->after ?>
</a>