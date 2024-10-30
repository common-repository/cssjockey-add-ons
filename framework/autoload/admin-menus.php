<?php

if( ! class_exists( 'cjwpbldr_admin_menus' ) ) {
	class cjwpbldr_admin_menus {
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
			add_action( 'init', [$this, 'adminMenuInit'], 10 );
		}

		public function adminMenuInit() {
			add_filter( 'cjwpbldr_admin_menu', [$this, 'adminMenuDropdown'] );
			add_action( 'admin_menu', [$this, 'adminMenu'] );
		}

		public function adminMenuDropdown( $menu ) {
			if( isset( $_GET['page'] ) && $_GET['page'] == $this->helpers->config( 'plugin-info', 'page_slug' ) ) {

				$installed_addons = $this->helpers->installedAddons();
				if( is_array( $installed_addons ) && ! empty( $installed_addons ) ) {
					$items = [];
					foreach( $installed_addons as $installed_addon_key => $installed_addon ) {
						$items[ $installed_addon['addon_id'] ] = $installed_addon['addon_name'];
					}
					$menu['installed-addons'] = [
						'label' => __( 'Installed Add-ons', 'wp-builder-locale' ),
						'items' => $items
					];
				}
			}

			return $menu;
		}

		public function adminMenu() {
			//add_action( 'admin_bar_menu', array($this, 'addonsAdminBarMenus'), 1000 );
			$plugin_info = $this->helpers->config( 'plugin-info' );
			$icon_url = $this->helpers->logo( 32, 'logo-on-dark', 'url' );
			$page_title = $plugin_info['page_title'];
			$menu_title = $plugin_info['menu_title'];
			$upgradeable_products = get_transient( '_cj_upgradable_products' );
			$updates_badge = '';
			if( is_array( $upgradeable_products ) && ! empty( $upgradeable_products ) ) {
				$count = count( $upgradeable_products );
				$updates_badge = '<span class="update-plugins count-' . $count . '"><span class="plugin-count">' . $count . '</span></span>';
			}
			if( $this->helpers->isTheme() ) {
				$page_title = $plugin_info['theme_name'];
				$menu_title = $plugin_info['theme_name'];
			}
			add_menu_page( $page_title, $menu_title . $updates_badge, 'manage_options', $plugin_info['page_slug'], [$this, 'adminMenuPage'], $icon_url );
			add_submenu_page( $plugin_info['page_slug'], __( 'WP Builder Cloud', 'wp-builder-locale' ), __( 'WP Builder Cloud', 'wp-builder-locale' ), 'manage_options', 'cjwpbldr-cloud-redirect', [$this, 'redirectToCloud'] );
		}

		public function adminMenuPage() {
			require sprintf( '%s/html/admin-page.php', $this->helpers->framework_dir );
		}

		public function importTemplate() {
			$location = $this->helpers->callbackUrl( 'core-cloud', true ) . 'show-tab=upload';
			wp_redirect( $location );
			die();
		}

		public function redirectToCloud() {
			if( isset( $_GET['page'] ) && $_GET['page'] == 'cjwpbldr-cloud-redirect' ) {
				wp_redirect( $this->helpers->callbackUrl( 'core-cloud', true ) . 'show=installed' );
				die();
			}
		}
	}

	cjwpbldr_admin_menus::getInstance();
}
