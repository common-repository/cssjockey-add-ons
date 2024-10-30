<?php
if( ! class_exists( 'cjwpbldr_addons_setup' ) ) {
	class cjwpbldr_addons_setup {
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
			add_action( 'admin_init', [$this, 'deleteAddon'] );
			$this->loadAddons();
			add_action( 'admin_notices', [$this, 'checkDependencies'], 1 );
		}

		public function loadAddons() {
			// framework modules
			$directory_path = sprintf( '%s/modules', $this->helpers->root_dir );
			if( is_dir( $directory_path ) ) {
				$dirs = preg_grep( '/^([^.])/', scandir( $directory_path ) );
				if( is_array( $dirs ) && ! empty( $dirs ) ) {
					foreach( $dirs as $key => $dir_name ) {
						$file_path = sprintf( '%s/%s/index.php', $directory_path, $dir_name );
						$framework_file_path = sprintf( '%s/%s/wp-builder.php', $directory_path, $dir_name );
						if( file_exists( $file_path ) && file_exists( $framework_file_path ) ) {
							require_once $file_path;
						}
					}
				}
			}

			// plugins addons
			$active_plugins = get_option( 'active_plugins' );
			$directory_path = sprintf( '%s', WP_PLUGIN_DIR );
			if( is_dir( $directory_path ) ) {
				$dirs = preg_grep( '/^([^.])/', scandir( $directory_path ) );
				if( is_array( $dirs ) && ! empty( $dirs ) ) {
					foreach( $dirs as $key => $dir_name ) {
						if( $this->helpers->containString( 'addon-', $dir_name ) ) {
							$file_path = $directory_path . '/' . $dir_name;
							$plugin_name = $dir_name . '/init.php';
							if( in_array( $plugin_name, $active_plugins ) ) {
								$addons_file_path = sprintf( '%s/init.php', $file_path );
								$framework_file_path = sprintf( '%s/%s/wp-builder.php', $directory_path, $dir_name );
								if( file_exists( $addons_file_path ) && file_exists( $framework_file_path ) ) {
									require_once $addons_file_path;
								}
							}
						}
					}
				}
			}

			// wp-content modules
			$directory_path = sprintf( '%s/addons', $this->helpers->cjwpbldr_content_path );
			if( is_dir( $directory_path ) ) {
				$dirs = preg_grep( '/^([^.])/', scandir( $directory_path ) );
				if( is_array( $dirs ) && ! empty( $dirs ) ) {
					foreach( $dirs as $key => $dir_name ) {
						$file_path = sprintf( '%s/%s/index.php', $directory_path, $dir_name );
						$framework_file_path = sprintf( '%s/%s/wp-builder.php', $directory_path, $dir_name );
						if( file_exists( $file_path ) && file_exists( $framework_file_path ) ) {
							require_once $file_path;
						}
					}
				}
			}
		}

		public function checkDependencies() {
			$installed_products = $this->helpers->installedProducts();
			$messages = [];
			foreach( $installed_products as $key => $installed_product ) {
				if( isset( $installed_product['dependencies'] ) && ! empty( $installed_product['dependencies'] ) ) {
					$parent_product_name = $installed_product['name'];
					if( is_array( $installed_product['dependencies'] ) && ! empty( $installed_product['dependencies'] ) ) {
						foreach( $installed_product['dependencies'] as $dependency_key => $dependency ) {
							// get type of product
							$dependency_info = $this->helpers->moduleConfig( $dependency, 'info' );
							if( empty( $dependency_info ) ) {
								$messages[] = sprintf( __( '<b>%s</b> requires <b>%s</b> to be installed and activated.', 'wp-builder-locale' ), $parent_product_name, $dependency );
							}
						}
					}
				}
			}

			if( ! empty( $messages ) ) {
				$title = $this->helpers->config( 'plugin-info', 'name' );
				$cloud_url = $this->helpers->callbackUrl( 'core-cloud', true ) . 'show=add-ons';
				$upload_url = $this->helpers->callbackUrl( 'core-cloud', true ) . 'show=upload';
				$content = '<p>' . implode( '<br>', $messages ) . '</p>';
				$content .= sprintf( __( '<p><a href="%s" title="WP Builder Cloud">Click here</a> to install required products via WP Builder Cloud or <a href="%s" title="Upload Products">Click here</a> to upload zip file or restore Envato purchases.</p>', 'wp-builder-locale' ), $cloud_url, $upload_url );
				echo $this->helpers->adminNotice( 'cjwpbldr-dependencies', 'error', $content, $title, false );
			}
		}

		public function deleteAddon() {
			if( isset( $_GET['cjwpbldr-remove-addon'] ) && $_GET['cjwpbldr-remove-addon'] != '' ) {
				$addon_info = $this->helpers->addonInfo( '', $_GET['cjwpbldr-remove-addon'] );
				$this->helpers->deleteDirectory( $addon_info['addon_path'] );
				$redirect = $this->helpers->callbackUrl( 'core-welcome', '', 'cjwpbldr' );
				wp_redirect( $redirect );
				die();
			}
		}

	}

	cjwpbldr_addons_setup::getInstance();
}