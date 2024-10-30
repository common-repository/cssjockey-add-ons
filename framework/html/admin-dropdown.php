<?php
$admin_menus_items = apply_filters( 'cjwpbldr_admin_menu', $this->helpers->config( 'admin-menu' ) );
$plugin_slug = $this->helpers->config( 'plugin-info', 'page_slug', '' );
$page_slug = 'cjwpbldr';
$dev_links = apply_filters( 'cjwpbldr_admin_dropdown_dev_links', [] );
?>
<div class="cjwpbldr-admin-menu">
    <ul>
        <li class="logo">
            <a href="<?php echo $this->helpers->callbackUrl( 'core-welcome' ) ?>">
				<?php echo $this->helpers->logo( 22, 'logo-on-light' ); ?>
            </a>
        </li>
		<?php
		foreach( $admin_menus_items as $key => $admin_menu_item ) {
			if( ! is_array( $admin_menu_item ) ) {
				if( ! strpos( $key, 'core-' ) ) {
					$page_slug = $_GET['page'];
				}
				//echo '<li><a href="' . $this->helpers->callbackUrl( $key, null, $page_slug ) . '">' . $admin_menu_item . '</a></li>';
			}
			if( is_array( $admin_menu_item ) && ! empty( $admin_menu_item ) ) {
				echo '<li class="cj-has-sub-menu">';
				echo '<a href="#">' . $admin_menu_item['label'] . ' <span class="cj-ml-8 cj-opacity-50"><i class="fa fa-angle-down"></i></span></a>';
				echo '<ul>';
				$menu_array_items = $admin_menu_item['items'];
				foreach( $menu_array_items as $index => $menu_array_item ) {
					if( ! is_array( $menu_array_item ) ) {
						echo '<li><a href="' . $this->helpers->callbackUrl( $index, null, $page_slug ) . '">' . $menu_array_item . '</a></li>';
					}
					if( is_array( $menu_array_item ) && ! empty( $menu_array_item ) ) {
						$sub_menu = $menu_array_item;
						echo '<li><a href="#"><span class="cj-is-pulled-right"><i class="fa fa-angle-right cj-opacity-50"></i></span>' . $sub_menu['label'] . '</a>';
						echo '<ul>';
						foreach( $sub_menu['items'] as $sub_menu_key => $sub_menu_label ) {
							echo '<li><a href="' . $this->helpers->callbackUrl( $sub_menu_key, null, $page_slug ) . '">' . $sub_menu_label . '</a></li>';
						}
						echo '</ul>';
						echo '</li>';
					}
				}
				echo '</ul>';
				echo '</li>';
			}
		} ?>
		<?php
		if( class_exists( 'cjwpbldr_dev_setup' ) && ! empty( $dev_links ) ) { ?>
            <li class="cj-is-pulled-right cj-has-dropdown">
                <a href="#"><i class="fa fa-code cj-mr-5"></i> <?php _e( 'DEV', 'wp-builder-locale' ) ?></a>
                <ul>
					<?php foreach( $dev_links as $title => $url ) { ?>
                        <li><a href="<?php echo $url; ?>"><?php echo $title; ?></a></li>
					<?php } ?>
                </ul>
            </li>
		<?php } ?>
        <li class="cj-is-pulled-right cj-has-dropdown">
            <a href="#"><i class="fa fa-life-ring cj-mr-5"></i> <?php _e( 'Help & Support', 'wp-builder-locale' ) ?></a>
            <ul>
                <li><a target="_blank" href="<?php echo $this->helpers->config( 'plugin-info', 'support_url' ) ?>"><?php _e( 'Submit a ticket', 'wp-builder-locale' ) ?></a></li>
                <li><a target="_blank" href="<?php echo $this->helpers->config( 'plugin-info', 'hire_us_url' ) ?>"><?php _e( 'Customization', 'wp-builder-locale' ) ?></a></li>
                <li><a target="_blank" href="<?php echo $this->helpers->config( 'plugin-info', 'contact_url' ) ?>"><?php _e( 'Contact Us', 'wp-builder-locale' ) ?></a></li>
            </ul>
        </li>
        <li class="cj-is-pulled-right">
            <a href="<?php echo $this->helpers->callbackUrl( 'core-welcome', true ) . 'cjwpbldr-action=check-product-updates&redirect=' . urlencode( $this->helpers->callbackUrl( 'core-cloud', true ) . 'show=upgrades' ) ?>"><?php _e( 'Check for Upgrades', 'wp-builder-locale' ) ?></a>
        </li>
    </ul>
</div>