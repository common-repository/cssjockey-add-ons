<?php
if( ! class_exists( 'cjwpbldr_navmenu_setup' ) ) {
	class cjwpbldr_navmenu_setup {
		private static $instance;
		public $helpers;

		public static function getInstance() {
			if( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function __construct() {
			$this->helpers = cjwpbldr_helpers::getInstance();
			add_action( 'admin_init', [$this, 'navMenuMetaboxLinks'] );
			add_action( 'admin_init', [$this, 'navMenuMetaBox'], 99999 );

			require_once 'nav-menu-edit-fields-walker.php';
			add_filter( 'wp_setup_nav_menu_item', [$this, 'addCustomNavFields'] );
			add_action( 'wp_update_nav_menu_item', [$this, 'updateCustomNavFields'], 10, 3 );
			add_filter( 'wp_edit_nav_menu_walker', [$this, 'editWalker'], 10, 2 );
		}

		public function navMenuMetaBox() {
			add_meta_box(
				'cjwpbldr_nav_menus_metabox',
				$this->helpers->config( 'plugin-info', 'name' ),
				[$this, 'navMenusMetaBoxUi'],
				'nav-menus',
				'side',
				'high'
			);
		}

		public function navMenuMetaboxLinks() {
			add_filter( 'cjwpbldr_nav_menu_metabox_links', function ( $links ) {
				$links['cjwpbldr-metabox-block-heading'] = [
					'object_id' => 'cjwpbldr-metabox-block-heading',
					'type' => 'cjwpbldr-metabox-heading',
					'label' => __( 'Works with all UI Blocks that has navigation menu options.', 'wp-builder-locale' ),
					'title' => '',
					'url' => '',
				];
				/*$links['cjwpbldr-navbar-logo'] = array(
					'object_id' => 'cjwpbldr-navbar-logo',
					'type' => 'custom',
					'classes' => 'cjwpbldr-navbar-logo',
					'label' => __( 'Logo', 'wp-builder-locale' ),
					'title' => __( 'NavBar Logo', 'wp-builder-locale' ),
					'url' => $this->helpers->site_url,
				);*/
				$links['cjwpbldr-navbar-divider'] = [
					'object_id' => 'cjwpbldr-navbar-divider',
					'type' => 'custom',
					'classes' => 'cjwpbldr-navbar-divider',
					'label' => __( 'Divider', 'wp-builder-locale' ),
					'title' => '[Nav Bar Divider]',
					'url' => '#',
				];
				$links['cjwpbldr-navbar-button'] = [
					'object_id' => 'cjwpbldr-navbar-button',
					'label' => __( 'Button', 'wp-builder-locale' ),
					'title' => 'Button Text',
					'url' => '#button-link',
					'type' => 'custom',
					'classes' => 'cjwpbldr-navbar-button',
				];
				$links['cjwpbldr-navbar-social-link'] = [
					'object_id' => 'cjwpbldr-navbar-social-link',
					'label' => __( 'Social Link', 'wp-builder-locale' ),
					'title' => 'Provider',
					'url' => '#provider-url',
					'type' => 'custom',
					'classes' => 'cjwpbldr-navbar-social-link',
				];
				$links['cjwpbldr-navbar-icon-link'] = [
					'object_id' => 'cjwpbldr-navbar-icon-link',
					'label' => __( 'Icon Link', 'wp-builder-locale' ),
					'title' => '',
					'url' => '#url',
					'type' => 'custom',
					'classes' => 'cjwpbldr-navbar-icon-link',
				];
				$links['cjwpbldr-navbar-search-form'] = [
					'object_id' => 'cjwpbldr-navbar-search-form',
					'type' => 'custom',
					'classes' => 'cjwpbldr-navbar-search-form',
					'label' => __( 'Search Form', 'wp-builder-locale' ),
					'title' => 'Search Form',
					'url' => '#none',
				];
				$page_for_posts = get_option( 'page_for_posts' );
				$blog_page_url = ($page_for_posts) ? get_permalink( $page_for_posts ) : $this->helpers->site_url;
				$links['cjwpbldr-navbar-blog'] = [
					'object_id' => 'cjwpbldr-navbar-blog',
					'type' => 'custom',
					'classes' => 'cjwpbldr-navbar-blog',
					'label' => __( 'Blog (with latest posts)', 'wp-builder-locale' ),
					'title' => 'Blog (with latest posts)',
					'url' => $blog_page_url,
				];

				$links['cjwpbldr-navbar-avatar'] = [
					'object_id' => 'cjwpbldr-navbar-avatar',
					'type' => 'custom',
					'classes' => 'cjwpbldr-navbar-avatar',
					'label' => __( 'User or Guest Avatar', 'wp-builder-locale' ),
					'title' => 'User or Guest Avatar',
					'url' => $blog_page_url,
				];
				/*$links['cjwpbldr-navbar-blog-mega-menu'] = array(
					'object_id' => 'cjwpbldr-navbar-blog-mega-menu',
					'label' => __( 'Blog (mega menu)', 'wp-builder-locale' ),
					'title' => 'Blog (mega menu)',
					'url' => $blog_page_url,
					'type' => 'custom',
					'classes' => 'cjwpbldr-navbar-blog-mega-menu',
				);*/

				// woocommerce
				if( class_exists( 'woocommerce' ) ) {
					$cart_link = get_permalink( get_option( 'woocommerce_cart_page_id' ) );
					$links['cjwpbldr-navbar-wc-cart-link'] = [
						'object_id' => 'cjwpbldr-navbar-wc-cart-link',
						'type' => 'custom',
						'classes' => 'cjwpbldr-navbar-wc-cart-link',
						'label' => __( 'WC Cart Link (with icon and total)', 'wp-builder-locale' ),
						'title' => 'Shop',
						'url' => $cart_link,
					];
					$links['cjwpbldr-navbar-wc-mega-menu'] = [
						'object_id' => 'cjwpbldr-navbar-wc-mega-menu',
						'type' => 'custom',
						'classes' => 'cjwpbldr-navbar-wc-mega-menu',
						'label' => __( 'WC (mega menu)', 'wp-builder-locale' ),
						'title' => 'Shop',
						'url' => $cart_link,
					];
				}

				return $links;
			}, 1 );
		}

		public function navMenusMetaBoxUi() {
			$nav_menus = [];
			$menus = apply_filters( 'cjwpbldr_nav_menu_metabox_links', $nav_menus );
			if( empty( $menus ) ) {
				echo __( 'No items found.', 'wp-builder-locale' );
			}
			require_once sprintf( '%s/html/nav-menu-metabox.php', $this->helpers->framework_dir );
		}

		public function addCustomNavFields( $menu_item ) {
			if( isset( $menu_item->ID ) ) {

				//$menu_item->url = get_post_meta( $menu_item->ID, '_menu_item_url', true );

				// logo
				$menu_item->cjwpbldr_logo_url = get_post_meta( $menu_item->ID, '_menu_item_cjwpbldr_logo_url', true );

				// visibility
				$menu_item->cjwpbldr_visibility_roles = get_post_meta( $menu_item->ID, '_menu_item_cjwpbldr_visibility_roles', true );
				$menu_item->cjwpbldr_visibility_devices = get_post_meta( $menu_item->ID, '_menu_item_cjwpbldr_visibility_devices', true );

				// button values
				$menu_item->cjwpbldr_button_style = get_post_meta( $menu_item->ID, '_menu_item_cjwpbldr_button_style', true );
				$menu_item->cjwpbldr_button_size = get_post_meta( $menu_item->ID, '_menu_item_cjwpbldr_button_size', true );
				$menu_item->cjwpbldr_button_extra_style = get_post_meta( $menu_item->ID, '_menu_item_cjwpbldr_button_extra_style', true );

				// blog (with latest posts)
				$menu_item->cjwpbldr_blog_posts_number = get_post_meta( $menu_item->ID, '_menu_item_cjwpbldr_blog_posts_number', true );
				$menu_item->cjwpbldr_blog_posts_rss_link = get_post_meta( $menu_item->ID, '_menu_item_cjwpbldr_blog_posts_rss_link', true );

				// blog (mega menu)
				$menu_item->cjwpbldr_navbar_blog_mega_menu_thumbs = get_post_meta( $menu_item->ID, '_menu_item_cjwpbldr_navbar_blog_mega_menu_thumbs', true );

				// social link
				$menu_item->cjwpbldr_social_icon = get_post_meta( $menu_item->ID, '_menu_item_cjwpbldr_social_icon', true );
				$menu_item->cjwpbldr_social_icon_style = get_post_meta( $menu_item->ID, '_menu_item_cjwpbldr_social_icon_style', true );
				$menu_item->cjwpbldr_social_icon_gap = get_post_meta( $menu_item->ID, '_menu_item_cjwpbldr_social_icon_gap', true );

				// icon link
				$menu_item->cjwpbldr_link_icon = get_post_meta( $menu_item->ID, '_menu_item_cjwpbldr_link_icon', true );
				$menu_item->cjwpbldr_link_icon_position = get_post_meta( $menu_item->ID, '_menu_item_cjwpbldr_link_icon_position', true );
			}

			return $menu_item;
		}

		function updateCustomNavFields( $menu_id, $menu_item_db_id, $args ) {

			// menu url
			if( isset( $_REQUEST['menu-item-url'] ) && is_array( $_REQUEST['menu-item-url'] ) && isset( $_REQUEST['menu-item-url'][ $menu_item_db_id ] ) ) {
				$value = $_REQUEST['menu-item-url'][ $menu_item_db_id ];
				update_post_meta( $menu_item_db_id, '_menu_item_url', $value );
			}

			// visibility
			//delete_post_meta( $menu_item_db_id, '_menu_item_cjwpbldr_logo_url' );
			if( isset( $_REQUEST['menu-item-cjwpbldr-logo-url'] ) && $_REQUEST['menu-item-cjwpbldr-logo-url'] != '' ) {
				$value = $_REQUEST['menu-item-cjwpbldr-logo-url'];
				update_post_meta( $menu_item_db_id, '_menu_item_cjwpbldr_logo_url', $value );
			}

			// visibility
			delete_post_meta( $menu_item_db_id, '_menu_item_cjwpbldr_visibility_roles' );
			delete_post_meta( $menu_item_db_id, '_menu_item_cjwpbldr_visibility_devices' );
			if( isset( $_REQUEST['menu-item-cjwpbldr-visibility-roles'] ) && is_array( $_REQUEST['menu-item-cjwpbldr-visibility-roles'] ) && isset( $_REQUEST['menu-item-cjwpbldr-visibility-roles'][ $menu_item_db_id ] ) ) {
				$value = $_REQUEST['menu-item-cjwpbldr-visibility-roles'][ $menu_item_db_id ];
				update_post_meta( $menu_item_db_id, '_menu_item_cjwpbldr_visibility_roles', $value );
			}
			if( isset( $_REQUEST['menu-item-cjwpbldr-visibility-devices'] ) && is_array( $_REQUEST['menu-item-cjwpbldr-visibility-devices'] ) && isset( $_REQUEST['menu-item-cjwpbldr-visibility-devices'][ $menu_item_db_id ] ) ) {
				$value = $_REQUEST['menu-item-cjwpbldr-visibility-devices'][ $menu_item_db_id ];
				update_post_meta( $menu_item_db_id, '_menu_item_cjwpbldr_visibility_devices', $value );
			}

			// Button Settings
			if( isset( $_REQUEST['menu-item-cjwpbldr-button-style'] ) && is_array( $_REQUEST['menu-item-cjwpbldr-button-style'] ) && isset( $_REQUEST['menu-item-cjwpbldr-button-style'][ $menu_item_db_id ] ) ) {
				$value = $_REQUEST['menu-item-cjwpbldr-button-style'][ $menu_item_db_id ];
				update_post_meta( $menu_item_db_id, '_menu_item_cjwpbldr_button_style', $value );
			}
			if( isset( $_REQUEST['menu-item-cjwpbldr-button-size'] ) && is_array( $_REQUEST['menu-item-cjwpbldr-button-size'] ) && isset( $_REQUEST['menu-item-cjwpbldr-button-size'][ $menu_item_db_id ] ) ) {
				$value = $_REQUEST['menu-item-cjwpbldr-button-size'][ $menu_item_db_id ];
				update_post_meta( $menu_item_db_id, '_menu_item_cjwpbldr_button_size', $value );
			}
			if( isset( $_REQUEST['menu-item-cjwpbldr-button-extra-style'] ) && is_array( $_REQUEST['menu-item-cjwpbldr-button-extra-style'] ) && isset( $_REQUEST['menu-item-cjwpbldr-button-extra-style'][ $menu_item_db_id ] ) ) {
				$value = $_REQUEST['menu-item-cjwpbldr-button-extra-style'][ $menu_item_db_id ];
				update_post_meta( $menu_item_db_id, '_menu_item_cjwpbldr_button_extra_style', $value );
			}

			// Blog (with latest posts)
			if( isset( $_REQUEST['menu-item-cjwpbldr-blog-posts-number'] ) && is_array( $_REQUEST['menu-item-cjwpbldr-blog-posts-number'] ) && isset( $_REQUEST['menu-item-cjwpbldr-blog-posts-number'][ $menu_item_db_id ] ) ) {
				$value = $_REQUEST['menu-item-cjwpbldr-blog-posts-number'][ $menu_item_db_id ];
				update_post_meta( $menu_item_db_id, '_menu_item_cjwpbldr_blog_posts_number', $value );
			}
			if( isset( $_REQUEST['menu-item-cjwpbldr-blog-posts-rss-link'] ) && is_array( $_REQUEST['menu-item-cjwpbldr-blog-posts-rss-link'] ) && isset( $_REQUEST['menu-item-cjwpbldr-blog-posts-rss-link'][ $menu_item_db_id ] ) ) {
				$value = $_REQUEST['menu-item-cjwpbldr-blog-posts-rss-link'][ $menu_item_db_id ];
				update_post_meta( $menu_item_db_id, '_menu_item_cjwpbldr_blog_posts_rss_link', $value );
			}

			// Blog (mega menu)
			if( isset( $_REQUEST['menu-item-cjwpbldr-navbar-blog-mega-menu-thumbs'] ) && is_array( $_REQUEST['menu-item-cjwpbldr-navbar-blog-mega-menu-thumbs'] ) && isset( $_REQUEST['menu-item-cjwpbldr-navbar-blog-mega-menu-thumbs'][ $menu_item_db_id ] ) ) {
				$value = $_REQUEST['menu-item-cjwpbldr-navbar-blog-mega-menu-thumbs'][ $menu_item_db_id ];
				update_post_meta( $menu_item_db_id, '_menu_item_cjwpbldr_navbar_blog_mega_menu_thumbs', $value );
			}

			// Social Icon
			if( isset( $_REQUEST['menu-item-cjwpbldr-social-icon'] ) && is_array( $_REQUEST['menu-item-cjwpbldr-social-icon'] ) && isset( $_REQUEST['menu-item-cjwpbldr-social-icon'][ $menu_item_db_id ] ) ) {
				$value = $_REQUEST['menu-item-cjwpbldr-social-icon'][ $menu_item_db_id ];
				update_post_meta( $menu_item_db_id, '_menu_item_cjwpbldr_social_icon', $value );
			}
			if( isset( $_REQUEST['menu-item-cjwpbldr-social-icon-style'] ) && is_array( $_REQUEST['menu-item-cjwpbldr-social-icon-style'] ) && isset( $_REQUEST['menu-item-cjwpbldr-social-icon-style'][ $menu_item_db_id ] ) ) {
				$value = $_REQUEST['menu-item-cjwpbldr-social-icon-style'][ $menu_item_db_id ];
				update_post_meta( $menu_item_db_id, '_menu_item_cjwpbldr_social_icon_style', $value );
			}
			if( isset( $_REQUEST['menu-item-cjwpbldr-social-icon-gap'] ) && is_array( $_REQUEST['menu-item-cjwpbldr-social-icon-gap'] ) && isset( $_REQUEST['menu-item-cjwpbldr-social-icon-gap'][ $menu_item_db_id ] ) ) {
				$value = $_REQUEST['menu-item-cjwpbldr-social-icon-gap'][ $menu_item_db_id ];
				update_post_meta( $menu_item_db_id, '_menu_item_cjwpbldr_social_icon_gap', $value );
			}

			// link icon
			if( isset( $_REQUEST['menu-item-cjwpbldr-link-icon'] ) && is_array( $_REQUEST['menu-item-cjwpbldr-link-icon'] ) && isset( $_REQUEST['menu-item-cjwpbldr-link-icon'][ $menu_item_db_id ] ) ) {
				$value = $_REQUEST['menu-item-cjwpbldr-link-icon'][ $menu_item_db_id ];
				update_post_meta( $menu_item_db_id, '_menu_item_cjwpbldr_link_icon', $value );
			}
			if( isset( $_REQUEST['menu-item-cjwpbldr-link-icon-position'] ) && is_array( $_REQUEST['menu-item-cjwpbldr-link-icon-position'] ) && isset( $_REQUEST['menu-item-cjwpbldr-link-icon-position'][ $menu_item_db_id ] ) ) {
				$value = $_REQUEST['menu-item-cjwpbldr-link-icon-position'][ $menu_item_db_id ];
				update_post_meta( $menu_item_db_id, '_menu_item_cjwpbldr_link_icon_position', $value );
			}
		}

		function editWalker( $walker, $menu_id ) {
			return 'cjwpbldr_bulma_navbar_edit_fields_walker';
		}

	}

	cjwpbldr_navmenu_setup::getInstance();
}