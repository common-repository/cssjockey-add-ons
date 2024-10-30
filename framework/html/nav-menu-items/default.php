<a href="<?php echo $item->url ?>" class="<?php echo implode( ' ', $classes ); ?>" <?php echo $target . ' ' . $title_tag . ' ' . $rel; ?>>
	<?php echo $args->before ?>
    <span class="link-title"><?php echo $title . $arrow_down ?></span>
    <?php if( $depth > 0 && $description != '' ) { ?>
        <span class="link-description"><?php echo $description; ?></span>
    <?php } ?>
	<?php echo $args->after ?>
</a>