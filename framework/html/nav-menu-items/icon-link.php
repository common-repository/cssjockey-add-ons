<a href="<?php echo $item->url ?>" class="<?php echo implode( ' ', $classes ); ?>" <?php echo $target . ' ' . $title_tag . ' ' . $rel; ?>>
	<?php echo $args->before ?>
	<?php
	if( $item->cjwpbldr_link_icon != '' && $item->cjwpbldr_link_icon_position == 'left' ) {
		echo '<span class="cj-icon cj-is-small cj-mr-5"><i class="' . $item->cjwpbldr_link_icon . '"></i></span>';
	}
	?>
	<?php echo '<span>' . apply_filters( 'the_title', $title, $item->ID ) . '</span>' ?>
	<?php
	if( $item->cjwpbldr_link_icon != '' && $item->cjwpbldr_link_icon_position == 'right' ) {
		echo '<span class="cj-icon cj-is-small cj-ml-5"><i class="' . $item->cjwpbldr_link_icon . '"></i></span>';
	}
	?>
	<?php echo $args->after ?>
</a>
