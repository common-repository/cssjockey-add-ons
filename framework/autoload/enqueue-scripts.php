<?php
if( ! class_exists( 'cjwpbldr_enqueue_scripts' ) ) {
	class cjwpbldr_enqueue_scripts {
		private static $instance;
		public $helpers, $version, $default_dependencies;

		public static function getInstance() {
			if( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function __construct() {
			$this->helpers = cjwpbldr_helpers::getInstance();
			$this->version = ($this->helpers->isLocal()) ? $this->helpers->uniqueString() : $this->helpers->config( 'plugin-info', 'version' );
			$this->default_dependencies = ['jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable'];

			add_action( 'admin_enqueue_scripts', [$this, 'adminEnqueueStyles'] );
			add_action( 'admin_enqueue_scripts', [$this, 'adminEnqueueScripts'] );
			add_action( 'admin_enqueue_scripts', [$this, 'externalBackendAssets'], 100 );
			add_action( 'admin_enqueue_scripts', [$this, 'addonsAdminScriptsAndStyles'], 100 );

			$core_features = (is_array( $this->helpers->saved_options['core_features'] )) ? $this->helpers->saved_options['core_features'] : [];
			if( count( array_intersect( ['blocks', 'builder'], $core_features ) ) > 0 ) {
				add_action( 'wp_enqueue_scripts', [$this, 'frontendEnqueueStyles'], 9999 );
				add_action( 'wp_enqueue_scripts', [$this, 'frontendEnqueueScripts'], 9999 );
				add_action( 'wp_enqueue_scripts', [$this, 'externalFrontendAssets'], 100 );
			}

			add_action( 'wp_enqueue_scripts', [$this, 'addonsFrontendScriptsAndStyles'], 9999 );

			add_filter( 'clean_url', [$this, 'addAsyncDeferToScripts'], 9999, 1 );

			add_action( 'wp_footer', [$this, 'blockExternalAssets'], 100 );
			add_action( 'wp_head', [$this, 'thirdPartyScripts'] );

			add_filter( 'wp_enqueue_scripts', [$this, 'combineAssets'], 99999 );
		}

		public function adminEnqueueStyles() {
			// global vendors css
			wp_register_style( 'cjwpbldr-vendors-global', $this->helpers->assets_url . '/css/vendors-global.min.css', [], $this->version, 'all' );
			wp_enqueue_style( 'cjwpbldr-vendors-global' );

			// backend vendors css
			wp_register_style( 'cjwpbldr-vendors-backend', $this->helpers->assets_url . '/css/vendors-backend.min.css', [], $this->version, 'all' );
			wp_enqueue_style( 'cjwpbldr-vendors-backend' );

			// main backend css
			wp_register_style( 'cjwpbldr-backend-css', $this->helpers->assets_url . '/css/backend.min.css', [], $this->version, 'all' );
			wp_enqueue_style( 'cjwpbldr-backend-css' );

			// backend helpers css
			wp_register_style( 'cjwpbldr-helpers-backend', $this->helpers->assets_url . '/css/helpers.min.css', [], $this->version, 'all' );
			wp_enqueue_style( 'cjwpbldr-helpers-backend' );
		}

		public function adminEnqueueScripts() {

			wp_enqueue_script( 'media-upload' );
			wp_enqueue_media();

			wp_register_script( 'cjwpbldr-vendors-backend-js', $this->helpers->assets_url . '/js/vendors-backend-min.js', ['jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable'], $this->version, true );
			wp_enqueue_script( 'cjwpbldr-vendors-backend-js' );

			wp_register_script( 'cjwpbldr-backend-helpers-js', $this->helpers->assets_url . '/js/helpers.min.js', ['jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable'], $this->version, true );
			$localize_vars = $this->helpers->localizeScripts();
			wp_enqueue_script( 'cjwpbldr-backend-helpers-js' );
			wp_localize_script( 'cjwpbldr-backend-helpers-js', 'locale', $localize_vars );

			wp_register_script( 'cjwpbldr-backend-js', $this->helpers->assets_url . '/js/backend.min.js', ['jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable'], $this->version, true );
			$localize_vars = $this->helpers->localizeScripts();
			wp_enqueue_script( 'cjwpbldr-backend-js' );
			wp_localize_script( 'cjwpbldr-backend-js', 'locale', $localize_vars );
		}

		public function frontendEnqueueStyles() {

			wp_enqueue_script( 'media-upload' );
			wp_enqueue_media();

			// global vendors css
			wp_register_style( 'cjwpbldr-vendors-global', $this->helpers->assets_url . '/css/vendors-global.min.css', [], $this->version, 'all' );
			wp_enqueue_style( 'cjwpbldr-vendors-global' );

			// google fonts
			$core_sass_variables = $this->helpers->getOption( 'core_sass_variables' );
			$google_fonts = [];
			$google_fonts_array = $this->helpers->getGoogleFonts();
			if( is_array( $google_fonts_array ) && ! empty( $google_fonts_array ) && is_array( $core_sass_variables ) ) {
				foreach( $core_sass_variables as $key => $value ) {
					if( strstr( $key, 'family' ) ) {
						if( in_array( $value, $google_fonts_array ) ) {
							$google_fonts[ $value ] = 'https://fonts.googleapis.com/css?family=' . urlencode( $value );
						}
					}
				}
			}
			if( ! empty( $google_fonts ) ) {
				foreach( $google_fonts as $key => $url ) {
					echo '<link rel="stylesheet" href="' . $url . '&version=' . $this->version . '" media="all">';
					//wp_register_style('cjwpbldr-google-font-' . sanitize_title($key), $url, array(), $item_version, 'all');
					//wp_enqueue_style('cjwpbldr-google-font-' . sanitize_title($key));
				}
			}

			// frontend vendors css
			wp_register_style( 'cjwpbldr-vendors-frontend', $this->helpers->assets_url . '/css/vendors-frontend.min.css', [], $this->version, 'all' );
			wp_enqueue_style( 'cjwpbldr-vendors-frontend' );

			// main frontend css
			if( file_exists( $this->helpers->cjwpbldr_content_path . '/ui/ui-base.min.css' ) && ! isset( $_GET['dev'] ) ) {
				wp_register_style( 'cjwpbldr-ui-frontend', $this->helpers->cjwpbldr_content_url . '/ui/ui-base.min.css', [], $this->version, 'all' );
				wp_enqueue_style( 'cjwpbldr-ui-frontend' );
			} else {
				wp_register_style( 'cjwpbldr-ui-frontend', $this->helpers->assets_url . '/css/ui-base.min.css', [], $this->version, 'all' );
				wp_enqueue_style( 'cjwpbldr-ui-frontend' );
			}
			// main frontend css
			wp_register_style( 'cjwpbldr-frontend', $this->helpers->assets_url . '/css/frontend.min.css', [], $this->version, 'all' );
			wp_enqueue_style( 'cjwpbldr-frontend' );

			// frontend helpers css
			if( file_exists( $this->helpers->cjwpbldr_content_path . '/ui/helpers.min.css' ) && ! isset( $_GET['dev'] ) ) {
				wp_register_style( 'cjwpbldr-ui-helpers-frontend', $this->helpers->cjwpbldr_content_url . '/ui/helpers.min.css', [], $this->version, 'all' );
				wp_enqueue_style( 'cjwpbldr-ui-helpers-frontend' );
			} else {
				wp_register_style( 'cjwpbldr-ui-helpers-frontend', $this->helpers->assets_url . '/css/helpers.min.css', [], $this->version, 'all' );
				wp_enqueue_style( 'cjwpbldr-ui-helpers-frontend' );
			}
		}

		public function frontendEnqueueScripts() {

			wp_register_script( 'cjwpbldr-vendors-frontend-js', $this->helpers->assets_url . '/js/vendors-frontend-min.js', ['jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable'], $this->version, true );
			wp_enqueue_script( 'cjwpbldr-vendors-frontend-js' );

			wp_register_script( 'cjwpbldr-frontend-helpers-js', $this->helpers->assets_url . '/js/helpers.min.js', ['jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable'], $this->version, true );
			$localize_vars = $this->helpers->localizeScripts();
			wp_enqueue_script( 'cjwpbldr-frontend-helpers-js' );
			wp_localize_script( 'cjwpbldr-frontend-helpers-js', 'locale', $localize_vars );

			wp_register_script( 'cjwpbldr-frontend-js', $this->helpers->assets_url . '/js/frontend.min.js', ['jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable'], $this->version, true );
			$localize_vars = $this->helpers->localizeScripts();
			wp_enqueue_script( 'cjwpbldr-frontend-js' );
			wp_localize_script( 'cjwpbldr-frontend-js', 'locale', $localize_vars );
		}

		public function addAsyncDeferToScripts( $url ) {
			return $url;
		}

		public function thirdPartyScripts() {
			$google_recaptcha = $this->helpers->reCaptcha();
			if( $google_recaptcha ) {
				echo $google_recaptcha->getScript();
			}
		}

		public function addonsAdminScriptsAndStyles() {
			$item_version = ($this->helpers->isLocal()) ? time() : $this->helpers->config( 'plugin-info', 'version' );
			$localize_vars = $this->helpers->localizeScripts();
			$addons = $this->helpers->installedAddons();
			if( is_array( $addons ) && ! empty( $addons ) ) {
				foreach( $addons as $key => $addon ) {
					/*vendors-backend scripts*/
					$file_path = $addon['addon_path'] . '/assets/js/vendors-backend-min.js';
					$file_url = str_replace( ABSPATH, site_url( '/' ), $file_path );
					$handle = $addon['addon_id'] . '-vendors-backend-js';
					if( file_exists( $file_path ) ) {
						wp_enqueue_script( $handle, $file_url, $this->default_dependencies, $item_version, true );
						wp_localize_script( $handle, 'locale', $localize_vars );
					}

					/*helpers scripts*/
					$file_path = $addon['addon_path'] . '/assets/js/helpers.min.js';
					$file_url = str_replace( ABSPATH, site_url( '/' ), $file_path );
					$handle = $addon['addon_id'] . '-helpers-js';
					if( file_exists( $file_path ) ) {
						wp_enqueue_script( $handle, $file_url, $this->default_dependencies, $item_version, true );
						wp_localize_script( $handle, 'locale', $localize_vars );
					}

					/*backend scripts*/
					$file_path = $addon['addon_path'] . '/assets/js/backend.min.js';
					$file_url = str_replace( ABSPATH, site_url( '/' ), $file_path );
					$handle = $addon['addon_id'] . '-backend-js';
					if( file_exists( $file_path ) ) {
						wp_enqueue_script( $handle, $file_url, $this->default_dependencies, $item_version, true );
						wp_localize_script( $handle, 'locale', $localize_vars );
					}

					/*vendors-backend css*/
					$file_path = $addon['addon_path'] . '/assets/css/vendors-backend.min.css';
					$file_url = str_replace( ABSPATH, site_url( '/' ), $file_path );
					$handle = $addon['addon_id'] . '-vendors-backend-css';
					if( file_exists( $file_path ) ) {
						wp_register_style( $handle, $file_url, [], $this->version, 'all' );
						wp_enqueue_style( $handle );
					}

					/*helpers css*/
					$file_path = $addon['addon_path'] . '/assets/css/helpers.min.css';
					$file_url = str_replace( ABSPATH, site_url( '/' ), $file_path );
					$handle = $addon['addon_id'] . '-helpers-css';
					if( file_exists( $file_path ) ) {
						wp_register_style( $handle, $file_url, [], $this->version, 'all' );
						wp_enqueue_style( $handle );
					}

					/*backend css*/
					$file_path = $addon['addon_path'] . '/assets/css/backend.min.css';
					$file_url = str_replace( ABSPATH, site_url( '/' ), $file_path );
					$handle = $addon['addon_id'] . '-backend-css';
					if( file_exists( $file_path ) ) {
						wp_register_style( $handle, $file_url, [], $this->version, 'all' );
						wp_enqueue_style( $handle );
					}

					/* external assets */
					$external_assets = $this->helpers->config( 'external-assets', 'backend', $addon['addon_path'] );
					if( is_array( $external_assets ) && ! empty( $external_assets ) ) {
						foreach( $external_assets as $file_key => $file_url ) {
							$path_info = pathinfo( $file_url );
							if( $path_info['extension'] == 'css' ) {
								$handle = sprintf( '%s-external-%s', $addon['addon_id'], sanitize_title( $path_info['basename'] ) );
								wp_register_style( $handle, $file_url, [], $this->version, 'all' );
								wp_enqueue_style( $handle );
							}
							if( $path_info['extension'] == 'js' ) {
								$handle = sprintf( '%s-external-js-%s', $addon['addon_id'], sanitize_title( $path_info['basename'] ) );
								wp_enqueue_script( $handle, $file_url, $this->default_dependencies, $item_version, true );
							}
						}
					}
				}
			}
		}

		public function addonsFrontendScriptsAndStyles() {
			$item_version = ($this->helpers->isLocal()) ? time() : $this->helpers->config( 'plugin-info', 'version' );
			$localize_vars = $this->helpers->localizeScripts();
			$addons = $this->helpers->installedAddons();
			if( is_array( $addons ) && ! empty( $addons ) ) {
				foreach( $addons as $key => $addon ) {

					/* external assets */
					$external_assets = $this->helpers->config( 'external-assets', 'frontend', $addon['addon_path'] );
					if( is_array( $external_assets ) && ! empty( $external_assets ) ) {
						foreach( $external_assets as $file_key => $file_url ) {
							$path_info = pathinfo( $file_url );
							if( $path_info['extension'] == 'css' ) {
								$handle = sprintf( '%s-external-%s', $addon['addon_id'], sanitize_title( $path_info['basename'] ) );
								wp_register_style( $handle, $file_url, [], $this->version, 'all' );
								wp_enqueue_style( $handle );
							}
							if( $path_info['extension'] == 'js' ) {
								$handle = sprintf( '%s-external-js-%s', $addon['addon_id'], sanitize_title( $path_info['basename'] ) );
								wp_enqueue_script( $handle, $file_url, $this->default_dependencies, $item_version, true );
							}
						}
					}

					/*vendors-frontend scripts*/
					$file_path = $addon['addon_path'] . '/assets/js/vendors-frontend-min.js';
					$file_url = str_replace( ABSPATH, site_url( '/' ), $file_path );
					$handle = $addon['addon_id'] . '-vendors-frontend-js';
					if( file_exists( $file_path ) ) {
						wp_enqueue_script( $handle, $file_url, $this->default_dependencies, $item_version, true );
						wp_localize_script( $handle, 'locale', $localize_vars );
					}

					/*helpers scripts*/
					$file_path = $addon['addon_path'] . '/assets/js/helpers.min.js';
					$file_url = str_replace( ABSPATH, site_url( '/' ), $file_path );
					$handle = $addon['addon_id'] . '-helpers-js';
					if( file_exists( $file_path ) ) {
						wp_enqueue_script( $handle, $file_url, $this->default_dependencies, $item_version, true );
						wp_localize_script( $handle, 'locale', $localize_vars );
					}
					/*frontend scripts*/
					$file_path = $addon['addon_path'] . '/assets/js/frontend.min.js';
					$file_url = str_replace( ABSPATH, site_url( '/' ), $file_path );
					$handle = $addon['addon_id'] . '-frontend-js';
					if( file_exists( $file_path ) ) {
						wp_enqueue_script( $handle, $file_url, $this->default_dependencies, $item_version, true );
						wp_localize_script( $handle, 'locale', $localize_vars );
					}

					/* vendors-frontend css */
					$file_path = $addon['addon_path'] . '/assets/css/vendors-frontend.min.css';
					$file_url = str_replace( ABSPATH, site_url( '/' ), $file_path );
					$handle = $addon['addon_id'] . '-vendors-frontend-css';
					if( file_exists( $file_path ) ) {
						wp_register_style( $handle, $file_url, [], $this->version, 'all' );
						wp_enqueue_style( $handle );
					}
					/* helpers css */
					$file_path = $addon['addon_path'] . '/assets/css/helpers.min.css';
					$file_url = str_replace( ABSPATH, site_url( '/' ), $file_path );
					$handle = $addon['addon_id'] . '-frontend-helpers-css';
					if( file_exists( $file_path ) ) {
						wp_register_style( $handle, $file_url, [], $this->version, 'all' );
						wp_enqueue_style( $handle );
					}

					/*frontend css*/
					$file_path = $addon['addon_path'] . '/assets/css/frontend.min.css';
					$file_url = str_replace( ABSPATH, site_url( '/' ), $file_path );
					$handle = $addon['addon_id'] . '-frontend-css';
					if( file_exists( $file_path ) ) {
						wp_register_style( $handle, $file_url, [], $this->version, 'all' );
						wp_enqueue_style( $handle );
					}
				}
			}
		}

		public function externalBackendAssets() {
			$item_version = ($this->helpers->isLocal()) ? time() : $this->helpers->config( 'plugin-info', 'version' );
			$external_assets = $this->helpers->config( 'external-assets', 'backend' );
			if( is_array( $external_assets ) && ! empty( $external_assets ) ) {
				foreach( $external_assets as $key => $url ) {
					$path_info = pathinfo( $url );
					if( $path_info['extension'] == 'css' ) {
						$handle = sprintf( 'cjwpbldr-external-%s', sanitize_title( $path_info['basename'] ) );
						wp_register_style( $handle, $url, [], $this->version, 'all' );
						wp_enqueue_style( $handle );
					}
					if( $path_info['extension'] == 'js' ) {
						$handle = sprintf( 'cjwpbldr-external-js-%s', sanitize_title( $path_info['basename'] ) );
						wp_enqueue_script( $handle, $url, $this->default_dependencies, $item_version, true );
					}
				}
			}
		}

		public function externalFrontendAssets() {
			$item_version = ($this->helpers->isLocal()) ? time() : $this->helpers->config( 'plugin-info', 'version' );
			$external_assets = $this->helpers->config( 'external-assets', 'frontend' );
			if( is_array( $external_assets ) && ! empty( $external_assets ) ) {
				foreach( $external_assets as $key => $url ) {
					$path_info = pathinfo( $url );
					if( $path_info['extension'] == 'css' ) {
						$handle = sprintf( 'cjwpbldr-external-%s', sanitize_title( $path_info['basename'] ) );
						wp_register_style( $handle, $url, [], $this->version, 'all' );
						wp_enqueue_style( $handle );
					}
					if( $path_info['extension'] == 'js' ) {
						$handle = sprintf( 'cjwpbldr-external-js-%s', sanitize_title( $path_info['basename'] ) );
						wp_enqueue_script( $handle, $url, $this->default_dependencies, $item_version, true );
					}
				}
			}
		}

		public function blockExternalAssets() {
			$item_version = ($this->helpers->isLocal()) ? time() : $this->helpers->config( 'plugin-info', 'version' );
			$external_assets = [];
			$external_assets = apply_filters( 'cjwpbldr_external_assets', $external_assets );
			$included_assets = [];
			if( is_array( $external_assets ) && ! empty( $external_assets ) ) {
				foreach( $external_assets as $key => $asset ) {
					$handle = sha1( sanitize_title( $asset ) );
					$included_assets[ $handle ] = $asset;
				}
				if( ! empty( $included_assets ) ) {
					foreach( $included_assets as $handle => $asset ) {
						$type = pathinfo( $asset );
						if( $type['extension'] == 'css' ) {
							echo '<link id="' . $handle . '" rel="stylesheet" href="' . $asset . '?ver=' . $item_version . '" />';
						}
						if( $type['extension'] == 'js' ) {
							echo '<script id="' . $handle . '" src="' . $asset . '?ver=' . $item_version . '"></script>';
						}
					}
				}
			}
		}

		public function combineAssets() {

			$combine_assets = $this->helpers->getOption( 'core_combine_assets', 'no' );

			if( $combine_assets != 'yes' ) {
				return false;
			}

			global $wp_scripts, $wp_styles;
			$combined_scripts = [];
			foreach( $wp_scripts->registered as $handle => $item ) {
				if( ($this->helpers->containString( 'cjwpbldr', $handle ) || $this->helpers->containString( 'addon-', $handle )) && ! $this->helpers->containString( 'cjwpbldr_gutenberg', $handle ) ) {
					$combined_scripts[ $handle ] = str_replace( site_url( '/' ), ABSPATH, $item->src );
				}
			}
			if( ! empty( $combined_scripts ) ) {
				foreach( $combined_scripts as $handle => $value ) {
					wp_dequeue_script( $handle );
				}
				update_option( 'cjwpbldr_combined_scripts', $combined_scripts );
				$path = sprintf( '%s/js/combined-scripts.php', $this->helpers->assets_dir );
				$url = str_replace( ABSPATH, site_url( '/' ), $path );
				if( file_exists( $path ) ) {
					wp_register_script( 'cjwpbldr-combined-scripts', $url, $this->default_dependencies, $this->version, true );
					$localize_vars = $this->helpers->localizeScripts();
					wp_enqueue_script( 'cjwpbldr-combined-scripts' );
					wp_localize_script( 'cjwpbldr-combined-scripts', 'locale', $localize_vars );
				}
			}

			$combined_styles = [];
			foreach( $wp_styles->registered as $handle => $item ) {
				if( ($this->helpers->containString( 'cjwpbldr', $handle ) || $this->helpers->containString( 'addon-', $handle )) && ! $this->helpers->containString( 'cjwpbldr_gutenberg', $handle ) ) {
					wp_dequeue_style( $handle );
					$combined_styles[ $handle ] = str_replace( site_url( '/' ), ABSPATH, $item->src );
				}
			}

			if( ! empty( $combined_styles ) ) {
				foreach( $combined_styles as $handle => $value ) {
					wp_dequeue_style( $handle );
				}
				update_option( 'cjwpbldr_combined_styles', $combined_styles );
				$path = sprintf( '%s/css/combined-styles.php', $this->helpers->assets_dir );
				$url = str_replace( ABSPATH, site_url( '/' ), $path );
				if( file_exists( $path ) ) {
					wp_register_style( 'cjwpbldr-combined-styles', $url, [], $this->version, 'all' );
					wp_enqueue_style( 'cjwpbldr-combined-styles' );
				}
			}
		}

	}

	cjwpbldr_enqueue_scripts::getInstance();
}