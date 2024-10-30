<?php
if(!class_exists('woocommerce')){
    return;
}
global $woocommerce;
$currency_symbol = get_woocommerce_currency_symbol();
$currency_position = get_option( 'woocommerce_currency_pos' );
$cart_total = number_format( $woocommerce->cart->cart_contents_total, 2 );
$cart_total_text = $currency_symbol . $cart_total;
if( $currency_position == 'right' ) {
	$cart_total_text = $cart_total . $currency_symbol;
}
$cart_url = $woocommerce->cart->get_cart_url();

?><a href="<?php echo $item->url ?>" class="<?php echo implode( ' ', $classes ); ?>" <?php echo $target . ' ' . $title_tag . ' ' . $rel; ?>>
	<?php echo $args->before ?>
	<?php if( $depth > 0 && $description !== '' ) {
		echo '<div class="cj-navbar-content"><p class="cj-text-bold">';
	} ?>
	<?php echo '<span class="cj-icon cj-is-small cj-mr-5"><i class="fa fa-shopping-cart"></i></span> <span>'.$cart_total_text.'</span>' ?>
	<?php if( $depth > 0 && $description !== '' ) {
		echo '</p><p>' . esc_attr( $description ) . '</p></div>';
	} ?>
	<?php echo $args->after ?>
</a>