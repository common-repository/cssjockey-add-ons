<a href="<?php echo $item->url ?>" class="<?php echo implode( ' ', $classes ); ?>" <?php echo $target . ' ' . $title_tag . ' ' . $rel; ?>>
	<?php echo $args->before ?>
	<?php if( $depth > 0 && $description !== '' ) {
		echo '<div class="cj-navbar-content"><p class="cj-text-bold">';
	} ?>
	<?php echo '<span>' . apply_filters( 'the_title', $title, $item->ID ) . '</span>' ?>
	<?php if( $depth > 0 && $description !== '' ) {
		echo '</p><p>' . esc_attr( $description ) . '</p></div>';
	} ?>
	<?php echo $args->after ?>
</a>
