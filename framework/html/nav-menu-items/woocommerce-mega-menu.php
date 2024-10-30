<?php
global $woocommerce;
$currency_symbol = get_woocommerce_currency_symbol();
$currency_position = get_option( 'woocommerce_currency_pos' );
$cart_total = number_format( $woocommerce->cart->cart_contents_total, 2 );
$cart_total_text = $currency_symbol . $cart_total;
if( $currency_position == 'right' ) {
	$cart_total_text = $cart_total . $currency_symbol;
}
$cart_url = $woocommerce->cart->get_cart_url();
?>
<div class="cj-navbar-item cj-has-dropdown cj-is-hoverable cj-is-mega cj-is-active <?php echo 'menu-item-'.$item->post_name; ?>">
    <a href="<?php echo $item->url ?>" class="<?php echo implode( ' ', $classes ); ?>" <?php echo $target . ' ' . $title_tag . ' ' . $rel; ?>>
		<?php echo $args->before; ?>
		<?php echo '<span>' . apply_filters( 'the_title', $title, $item->ID ) . '</span>'; ?>
		<?php echo $args->after; ?>
    </a>
    <div class="cj-navbar-dropdown">
        <div class="cj-m-15">
            <div class="cj-columns cj-is-multiline cj-is-mobile">
                <div class="cj-column cj-is-3-widescreen cj-is-3-desktop cj-is-12-tablet cj-is-12-mobile">
                    <p class="cj-title cj-is-5">
                        <span class="cj-is-pulled-right">
                            <a href="#" class="cj-icon cj-is-small"><i class="fa fa-angle-left"></i></a>
                            <a href="#" class="cj-icon cj-is-small"><i class="fa fa-angle-right"></i></a>
                        </span>
						<?php _e( 'Best Sellers' ) ?>
                    </p>
                    <div class="cj-wc-mega-menu-best-sellers">
                        <div class="cj-cover" style="height: 300px; background-image: url('//placehold.it/200x250/cccccc/aaaaaa&text=PRODUCT+THUMB');"></div>
                        <div class="cj-text-center cj-p-15">
                            <p class="cj-title cj-is-5 cj-text-bold">Product Name</p>
                            <p class="cj-subtitle cj-is-6">$23.00</p>
                        </div>
                        <a href="#" class="cj-button cj-is-primary cj-is-outlined cj-is-block">View all products</a>
                    </div>
                </div>
                <div class="cj-column cj-is-3-widescreen cj-is-3-desktop cj-is-12-tablet cj-is-12-mobile">
                    <p class="cj-title cj-is-5"><?php _e( 'Featured Products' ) ?></p>
					<?php for( $i = 0; $i < 5; $i ++ ) { ?>
                        <article class="cj-media">
                            <figure class="cj-media-left">
                                <p class="cj-image cj-is-64x64">
                                    <img src="http://localhost:4000/images/placeholders/128x128.png">
                                </p>
                            </figure>
                            <div class="cj-media-content">
                                <div class="cj-content">
                                    <p>
                                        <strong>Product Name</strong>
                                        <br>
                                        $23.00
                                    </p>
                                </div>
                            </div>
                        </article>
					<?php } ?>

                </div>
                <div class="cj-column cj-is-2-widescreen cj-is-3-desktop cj-is-12-tablet cj-is-12-mobile">
                    <p class="cj-title cj-is-5"><?php _e( 'Explore' ) ?></p>
                    <ul>
                        <li class="cj-mb-20"><a class="cj-text-bold" href="#">Parent Category</a>
                            <ul class="cj-mt-5 cj-fs-12">
                                <li><a href="#" class="cj-color-dark"><span class="cj-icon cj-is-small"><i class="fa fa-angle-right"></i></span> Child Category</a></li>
                                <li><a href="#" class="cj-color-dark"><span class="cj-icon cj-is-small"><i class="fa fa-angle-right"></i></span> Child Category</a></li>
                                <li><a href="#" class="cj-color-dark"><span class="cj-icon cj-is-small"><i class="fa fa-angle-right"></i></span> Child Category</a></li>
                                <li><a href="#" class="cj-color-dark"><span class="cj-icon cj-is-small"><i class="fa fa-angle-right"></i></span> Child Category</a></li>
                                <li><a href="#" class="cj-color-dark"><span class="cj-icon cj-is-small"><i class="fa fa-angle-right"></i></span> Child Category</a></li>
                            </ul>
                        </li>
                        <li class="cj-mb-20"><a class="cj-text-bold" href="#">Parent Category</a>
                            <ul class="cj-mt-5 cj-fs-12">
                                <li><a href="#" class="cj-color-dark"><span class="cj-icon cj-is-small"><i class="fa fa-angle-right"></i></span> Child Category</a></li>
                                <li><a href="#" class="cj-color-dark"><span class="cj-icon cj-is-small"><i class="fa fa-angle-right"></i></span> Child Category</a></li>
                                <li><a href="#" class="cj-color-dark"><span class="cj-icon cj-is-small"><i class="fa fa-angle-right"></i></span> Child Category</a></li>
                                <li><a href="#" class="cj-color-dark"><span class="cj-icon cj-is-small"><i class="fa fa-angle-right"></i></span> Child Category</a></li>
                                <li><a href="#" class="cj-color-dark"><span class="cj-icon cj-is-small"><i class="fa fa-angle-right"></i></span> Child Category</a></li>
                            </ul>
                        </li>
                        <li class="cj-mb-20"><a class="cj-text-bold" href="#">Parent Category</a>
                            <ul class="cj-mt-5 cj-fs-12">
                                <li><a href="#" class="cj-color-dark"><span class="cj-icon cj-is-small"><i class="fa fa-angle-right"></i></span> Child Category</a></li>
                                <li><a href="#" class="cj-color-dark"><span class="cj-icon cj-is-small"><i class="fa fa-angle-right"></i></span> Child Category</a></li>
                                <li><a href="#" class="cj-color-dark"><span class="cj-icon cj-is-small"><i class="fa fa-angle-right"></i></span> Child Category</a></li>
                                <li><a href="#" class="cj-color-dark"><span class="cj-icon cj-is-small"><i class="fa fa-angle-right"></i></span> Child Category</a></li>
                                <li><a href="#" class="cj-color-dark"><span class="cj-icon cj-is-small"><i class="fa fa-angle-right"></i></span> Child Category</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="cj-column cj-is-3-widescreen cj-is-3-desktop cj-is-12-tablet cj-is-12-mobile">
                    Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget.
                </div>
            </div>
        </div>
    </div>
</div>