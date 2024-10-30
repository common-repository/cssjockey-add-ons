<?php

if( ! class_exists( 'cjwpbldr_core_setup' ) ) {
	class cjwpbldr_core_setup {
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
			add_filter( 'plugin_action_links_' . plugin_basename( $this->helpers->root_dir . '/init.php' ), [$this, 'pluginActionLinks'] );
			add_action( 'admin_init', [$this, 'setupWpContentFolder'], 10 );
			add_action( 'admin_notices', [$this, 'missingCssFilesNotice'], 10 );
			add_action( 'admin_init', [$this, 'checkProductUpgrades'], 10 );
			add_action( 'admin_init', [$this, 'syncOptions'] );
			add_action( 'init', [$this, 'disableAdminBar'] );
			add_action( 'init', [$this, 'disableAdminBarOnPreviews'] );
			add_action( 'wp_head', [$this, 'wpHead'] );
			add_action( 'wp_footer', [$this, 'googleServicesJs'] );
			add_action( 'admin_footer', [$this, 'googleServicesJs'] );
			add_action( 'template_redirect', [$this, 'showPreviews'] );
			add_action( 'init', [$this, 'runGetActions'] );
			add_action( 'admin_init', [$this, 'runDbMigrations'] );
			add_action( 'init', [$this, 'frontendMods'] );
			add_action( 'init', [$this, 'pluginLocalization'], 1 );
			add_filter( 'upload_mimes', [$this, 'svgAddMimeTypeSupport'] );
			add_filter( 'wp_get_attachment_image_src', [$this, 'imagesOnePixelFix'], 10, 4 );
			add_filter( 'admin_post_thumbnail_html', [$this, 'imageFeaturedImageFix'], 10, 3 );
			add_filter( 'wp_generate_attachment_metadata', [$this, 'svgSkipRegeneration'], 10, 2 );
			add_action( 'get_image_tag', [$this, 'getImageTagOverride'], 10, 6 );
			add_filter( 'the_content', [$this, 'fixShortcodes'] );
			add_action( 'wp_head', [$this, 'redirect'] );
			//add_action( 'save_post', [$this, 'savePostThumbString'] );
			add_filter( 'wp_generate_attachment_metadata', [$this, 'retinaSupportAttachmentMeta'], 10, 2 );
			add_filter( 'delete_attachment', [$this, 'deleteRetinaSupportImages'] );
			add_action( 'admin_head', [$this, 'adminHead'], 99999 );
			add_action( 'admin_footer', [$this, 'adminFooter'], 99999 );
			add_action( 'init', [$this, 'registerTaxonomies'], 0 );
			add_action( 'init', [$this, 'registerPostTypes'], 0 );
			add_action( 'init', [$this, 'registerNavMenus'], 0 );
			add_action( 'body_class', [$this, 'bodyBrowserClass'] );
			add_action( 'wp_footer', [$this, 'printWpFooter'], 99999 );
			add_action( 'admin_notices', [$this, 'productActivateAndUpgrades'], 99999 );
			add_action( 'widgets_init', [$this, 'saveRegisteredSidebars'], 99999 );
		}

		public function checkProductUpgrades() {
			if( current_user_can( 'manage_options' ) && isset( $_GET['cjwpbldr-action'] ) && $_GET['cjwpbldr-action'] == 'check-product-updates' ) {
				$this->helpers->checkProductUpgrades();
				$redirect_url = $this->helpers->callbackUrl( 'core-welcome' );
				if( isset( $_GET['redirect'] ) && $_GET['redirect'] !== '' ) {
					$redirect_url = $_GET['redirect'];
				}
				wp_redirect( $redirect_url );
				die();
			}
		}

		public function bodyBrowserClass( $classes ) {
			$browsers = ['is-iphone', 'is-chrome', 'is-safari', 'is-NS4', 'is-opera', 'is-macIE', 'is-winIE', 'is-gecko', 'is-lynx', 'is-IE', 'is-edge'];
			$classes[] = join( ' ', array_filter( $browsers, function ( $browser ) {
				return (isset( $GLOBALS[ $browser ] )) ? $GLOBALS[ $browser ] : '';
			} ) );

			return $classes;
		}

		public function pluginActionLinks( $links ) {
			$links[] = '<a href="' . esc_url( $this->helpers->callbackUrl( 'core-welcome' ) ) . '">' . __( 'Settings', 'wp-builder-locale' ) . '</a>';

			return $links;
		}

		public function runGetActions() {
			// reset settings
			if( isset( $_GET['cjwpbldr-action'] ) && $_GET['cjwpbldr-action'] == 'reset-settings' && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'] ) ) {
				// delete options
				$this->removeAllSettings();
				wp_redirect( $this->helpers->callbackUrl( 'core-uninstall' ) );
				die();
			} elseif( isset( $_GET['cjwpbldr-action'] ) && $_GET['cjwpbldr-action'] == 'reset-settings' ) {
				wp_die( __( 'Cheating Hun!', 'wp-builder-locale' ) );
			}

			// complete uninstall
			if( isset( $_GET['cjwpbldr-action'] ) && $_GET['cjwpbldr-action'] == 'uninstall' && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'] ) ) {
				// delete options
				$this->removeAllSettings();
				$installed_plugins = get_option( 'active_plugins' );
				$cjwpbldr_plugin_file = basename( $this->helpers->root_dir ) . '/init.php';
				foreach( $installed_plugins as $key => $installed_plugin ) {
					if( $installed_plugin === $cjwpbldr_plugin_file ) {
						unset( $installed_plugins[ $key ] );
					}
				}
				update_option( 'active_plugins', $installed_plugins );
				wp_redirect( admin_url( 'plugins.php' ) );
				die();
			} elseif( isset( $_GET['cjwpbldr-action'] ) && $_GET['cjwpbldr-action'] == 'uninstall' ) {
				wp_die( __( 'Cheating Hun!', 'wp-builder-locale' ) );
			}
		}

		public function showPreviews() {
			if( isset( $_GET['preview'] ) && $_GET['preview'] == 'cjwpbldr-shortcode' && isset( $_GET['shortcode'] ) && $_GET['shortcode'] !== '' ) {
				add_filter( 'show_admin_bar', '__return_false' );
				show_admin_bar( false );
				ob_start();
				require_once sprintf( '%s/html/preview-shortcode.php', $this->helpers->framework_dir );
				echo ob_get_clean();
				die();
			}
		}

		public function setupWpContentFolder() {
			$css_base_file = $this->helpers->cjwpbldr_content_path . '/ui/ui-base.min.css';
			$css_helpers_file = $this->helpers->cjwpbldr_content_path . '/ui/helpers.min.css';

			if( is_writable( $this->helpers->upload_dir['basedir'] ) ) {
				if( ! is_dir( $this->helpers->cjwpbldr_content_path ) ) {
					$this->helpers->createDirectory( $this->helpers->cjwpbldr_content_path );
				}
				if( ! file_exists( $this->helpers->cjwpbldr_content_path . '/index.php' ) ) {
					$this->helpers->putFileContents( $this->helpers->cjwpbldr_content_path . '/index.php', "<?php \n// Silence is golden." );
				}

				if( ! is_dir( $this->helpers->cjwpbldr_content_path . '/exports' ) ) {
					$this->helpers->createDirectory( $this->helpers->cjwpbldr_content_path . '/exports' );
				}
				if( ! file_exists( $this->helpers->cjwpbldr_content_path . '/exports/index.php' ) ) {
					$this->helpers->putFileContents( $this->helpers->cjwpbldr_content_path . '/exports/index.php', "<?php \n// Silence is golden." );
				}

				if( ! is_dir( $this->helpers->cjwpbldr_content_path . '/imports' ) ) {
					$this->helpers->createDirectory( $this->helpers->cjwpbldr_content_path . '/imports' );
				}
				if( ! file_exists( $this->helpers->cjwpbldr_content_path . '/imports/index.php' ) ) {
					$this->helpers->putFileContents( $this->helpers->cjwpbldr_content_path . '/imports/index.php', "<?php \n// Silence is golden." );
				}

				if( ! is_dir( $this->helpers->cjwpbldr_content_path . '/ui' ) ) {
					$this->helpers->createDirectory( $this->helpers->cjwpbldr_content_path . '/ui' );
				}
				if( ! file_exists( $this->helpers->cjwpbldr_content_path . '/ui/index.php' ) ) {
					$this->helpers->putFileContents( $this->helpers->cjwpbldr_content_path . '/ui/index.php', "<?php \n// Silence is golden." );
				}

				if( ! is_dir( $this->helpers->cjwpbldr_content_path . '/blocks' ) ) {
					$this->helpers->createDirectory( $this->helpers->cjwpbldr_content_path . '/blocks' );
				}
				if( ! file_exists( $this->helpers->cjwpbldr_content_path . '/blocks/index.php' ) ) {
					$this->helpers->putFileContents( $this->helpers->cjwpbldr_content_path . '/blocks/index.php', "<?php \n// Silence is golden." );
				}

				if( ! is_dir( $this->helpers->cjwpbldr_content_path . '/templates' ) ) {
					$this->helpers->createDirectory( $this->helpers->cjwpbldr_content_path . '/templates' );
				}
				if( ! file_exists( $this->helpers->cjwpbldr_content_path . '/templates/index.php' ) ) {
					$this->helpers->putFileContents( $this->helpers->cjwpbldr_content_path . '/templates/index.php', "<?php \n// Silence is golden." );
				}

				if( ! is_dir( $this->helpers->cjwpbldr_content_path . '/addons' ) ) {
					$this->helpers->createDirectory( $this->helpers->cjwpbldr_content_path . '/addons' );
				}
				if( ! file_exists( $this->helpers->cjwpbldr_content_path . '/addons/index.php' ) ) {
					$this->helpers->putFileContents( $this->helpers->cjwpbldr_content_path . '/addons/index.php', "<?php \n// Silence is golden." );
				}

				/* test scss auto compile */
				// $this->helpers->updateOption( 'core_sass_variables', '' );
				// $this->helpers->deleteFile( $css_base_file );
				// $this->helpers->deleteFile( $css_helpers_file );

				$saved_sass_variables = $this->helpers->getOption( 'core_sass_variables' );
				if( $saved_sass_variables == '' ) {
					$default_sass_variables = $this->helpers->userScssVariables();
					$variables_to_save = [];
					foreach( $default_sass_variables as $key => $value ) {
						$variables_to_save[ $value['id'] ] = $value['default'];
					}
					$variables_to_save = apply_filters( 'force_core_sass_variables', $variables_to_save );
					$this->helpers->updateOption( 'core_sass_variables', $variables_to_save );
				}
			}
		}

		public function missingCssFilesNotice() {
			$css_base_file = $this->helpers->cjwpbldr_content_path . '/ui/ui-base.min.css';
			$css_helpers_file = $this->helpers->cjwpbldr_content_path . '/ui/helpers.min.css';

			$title = $this->helpers->config( 'plugin-info', 'name' );

			if( ! file_exists( $css_base_file ) || ! file_exists( $css_helpers_file ) ) {
				$compile_scss_url = $this->helpers->callbackUrl( 'core-sass' );
				$content = sprintf( __( '<p>We need to compile the UI SCSS Variables as per your brand. <a href="%s">Click here</a> to specify your brand colours and fonts.</p>', 'wp-builder-locale' ), $compile_scss_url );
				$content .= '<p class="cj-mt-10 cjwpbldr-vue-compile-scss" v-cloak><a @click.prevent="compileScss" href="#" :class="{\'cj-is-loading\': loading}" :disabled="loading" class="cj-button cj-is-small cj-is-success">' . __( 'Compile UI Styles', 'wp-builder-locale' ) . '</a> <span v-if="message" class="cj-ml-10 cj-inline-block cj-mt-6" v-html="message"></span></p>';
				$this->helpers->adminNotice( 'cjwpbldr-missing-ui', 'warning', $content, $title . ' &rarr; ' . __( 'SCSS Compiler', 'wp-builder-locale' ), false );
			}

			$need_scss_compilation = get_option( 'cjwpbldr_ajax_compile_scss', false );

			if( $need_scss_compilation ) {
				$content = __( '<p>UI SCSS Variables has changed.</p>', 'wp-builder-locale' );
				$content .= '<p class="cj-mt-10 cjwpbldr-vue-compile-scss" v-cloak><a @click.prevent="compileScss" href="#" :class="{\'cj-is-loading\': loading}" :disabled="loading" class="cj-button cj-is-small cj-is-success">' . __( 'Compile UI Styles', 'wp-builder-locale' ) . '</a> <span v-if="message" class="cj-ml-10 cj-inline-block cj-mt-6" v-html="message"></span></p>';
				$this->helpers->adminNotice( 'cjwpbldr-missing-ui', 'warning', $content, $title . ' &rarr; ' . __( 'SCSS Compiler', 'wp-builder-locale' ), false );
			}
		}

		public function syncOptions() {
			$option_files = $this->helpers->config( 'option-files' );
			$options_data = [];
			if( is_array( $option_files ) && ! empty( $option_files ) ) {
				$saved_options = $this->helpers->getOption();
				foreach( $option_files as $key => $file ) {
					$core_file_path = $this->helpers->framework_dir . '/admin-pages/' . $file . '.php';
					if( file_exists( $core_file_path ) ) {
						$file_data = include($core_file_path);
						foreach( $file_data as $option_key => $option ) {
							if( isset( $saved_options[ $option['id'] ] ) ) {
								$options_data[ $option['id'] ] = $saved_options[ $option['id'] ];
							} else {
								$options_data[ $option['id'] ] = $option['default'];
							}
						}
					}
				}
			}

			$installed_addons = $this->helpers->installedAddons();
			if( is_array( $installed_addons ) && ! empty( $installed_addons ) ) {
				foreach( $installed_addons as $addon_key => $addon_info ) {
					if( isset( $addon_info['addon_path'] ) ) {
						$helpers_file_path = $addon_info['addon_path'] . '/helpers.php';
						if( file_exists( $helpers_file_path ) ) {
							require $helpers_file_path;
						}
						$option_files = $this->helpers->config( 'option-files', null, $addon_info['addon_path'] );
						if( is_array( $option_files ) && ! empty( $option_files ) ) {
							$saved_options = $this->helpers->getOption();
							foreach( $option_files as $key => $file ) {
								$addon_config_file_path = $addon_info['addon_path'] . '/admin-pages/' . $file . '.php';
								if( file_exists( $addon_config_file_path ) ) {
									$file_data = include($addon_config_file_path);
									if( is_array( $file_data ) && ! empty( $file_data ) ) {
										foreach( $file_data as $option_key => $option ) {
											if( isset( $saved_options[ $option['id'] ] ) ) {
												$options_data[ $option['id'] ] = $saved_options[ $option['id'] ];
											} else {
												$options_data[ $option['id'] ] = $option['default'];
											}
										}
									}
								}
							}
						}
					}
				}
			}
			if( is_array( $options_data ) && ! empty( $options_data ) ) {
				update_option( $this->helpers->options_key, $options_data );
			}

			do_action( 'cjwpbldr_after_options_sync', $options_data );

			$cloud_hash = get_transient( '_cjwpbldr_cloud_hash' );
			if( $cloud_hash ) {
				$products = $this->helpers->installedProducts( 'all', true );
				foreach( $products as $key => $product ) {
					set_transient( "_{$product['slug']}_key", $cloud_hash );
				}
			}
		}

		public function disableAdminBar() {
			if( is_user_logged_in() ) {
				$current_user = wp_get_current_user();
				$roles = $this->helpers->getOption( 'core_admin_bar' );
				if( is_array( $roles ) ) {
					if( in_array( 'everyone', $roles ) ) {
						add_filter( 'show_admin_bar', '__return_false' );
						show_admin_bar( false );
					}
					if( ! is_array( $roles ) && $roles != '' ) {
						$roles = [$roles];
					}
				}
				if( array_key_exists( '0', $current_user->roles ) ) {
					if( is_array( $roles ) && in_array( $current_user->roles[0], $roles ) ) {
						add_filter( 'show_admin_bar', '__return_false' );
						show_admin_bar( false );
					}
				}
			}
		}

		public function disableAdminBarOnPreviews() {
			$disable = [];
			$get_variables = [
				'cjwpbldr-action=edit-template',
				'cjwpbldr-action=edit-ui-block'
			];
			foreach( $get_variables as $key => $variable ) {
				$parse = explode( '=', $variable );
				if( isset( $_GET[ $parse[0] ] ) && $_GET[ $parse[0] ] == $parse[1] ) {
					$disable[] = 1;
				}
			}

			if( ! empty( $disable ) ) {
				add_filter( 'show_admin_bar', '__return_false' );
				show_admin_bar( false );
			}
		}

		public function googleServicesJs() {
			// google maps api key
			$google_maps_api_key = $this->helpers->getOption( 'core_google_maps_key' );
			if( $google_maps_api_key !== '' ) {
				echo '<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=places&key=' . $google_maps_api_key . '" async defer></script>';
			}
			$this->helpers->hookGoogleApiToItems();
		}

		public function wpHead() {

		}

		public function removeAllSettings() {
			delete_option( $this->helpers->options_key );
			delete_option( 'cjwpbldr_combined_styles' );
			delete_option( 'cjwpbldr_combined_scripts' );
		}

		public function runDbMigrations() {
			global $wpdb;
			// delete_option( 'cjwpbldr_db_version' );
			$installed_ver = get_option( "cjwpbldr_db_version" );
			if( $installed_ver == CJWPBLDR_VERSION ) {
				return false;
			}
			if( ! function_exists( 'wp_should_upgrade_global_tables' ) ) {
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			}
			$charset_collate = $wpdb->get_charset_collate();
			$migrations = $this->helpers->config( 'migrations' );
			$installed_addons = $this->helpers->installedAddons();
			if( is_array( $installed_addons ) && ! empty( $installed_addons ) ) {
				foreach( $installed_addons as $key => $installed_addon ) {
					$addon_migrations = $this->helpers->config( 'migrations', null, $installed_addon['addon_path'] );
					if( $addon_migrations != '' ) {
						foreach( $addon_migrations as $migration_key => $query ) {
							$migrations[ $migration_key ] = $query;
						}
					}
				}
			}

			if( is_array( $migrations ) && ! empty( $migrations ) ) {
				foreach( $migrations as $table_name => $query ) {
					if( $query != '' ) {
						$table_name = $this->helpers->wpTableName( $table_name );
						$check = $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" );
						if( is_null( $check ) ) {
							$sql = $query;
							$sql = str_replace( '%TABLE_NAME%', $table_name, $sql );
							$sql = str_replace( '%CHARSET_COLLATE%', $charset_collate, $sql );
							$wpdb->query( $sql );
							update_option( 'cjwpbldr_db_version', CJWPBLDR_VERSION );
						}
					}
				}
			}
		}

		public function frontendMods() {
			add_filter( 'body_class', [$this, 'extendBodyClass'] );
			add_filter( 'nav_menu_css_class', [$this, 'extendNavMenuItemClass'], 999, 2 );
		}

		public function extendBodyClass( $classes ) {
			global $current_user;
			if( empty( $current_user->roles ) ) {
				$new_classes['cj-is-visitor'] = 'cj-is-visitor';
			} else {
				foreach( $current_user->roles as $key => $role ) {
					$new_classes[ 'cj-is-' . $role . '' ] = 'cj-is-' . $role . '';
				}
			}
			if( is_user_logged_in() ) {
				$new_classes['cj-is-logged-in'] = 'cj-is-logged-in';
			} else {
				$new_classes['cj-is-logged-out'] = 'cj-is-logged-out';
			}

			return array_merge( $classes, $new_classes );
		}

		public function extendNavMenuItemClass( $classes, $item ) {

			$classes['cjwpbldr-post-name'] = 'menu-item-' . $item->post_name;
			if( isset( $item->cjwpbldr_visibility_roles ) && ! empty( $item->cjwpbldr_visibility_roles ) ) {
				$classes['cj-visibility-roles'] = implode( ' ', $item->cjwpbldr_visibility_roles );
			}
			if( isset( $item->cjwpbldr_visibility_devices ) && ! empty( $item->cjwpbldr_visibility_devices ) ) {
				$classes['cj-visibility-devices'] = implode( ' ', $item->cjwpbldr_visibility_devices );
			}

			return $classes;
		}

		public function pluginLocalization() {
			$domain = 'wp-builder-locale';
			$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
			$lang_path = $this->helpers->root_dir . '/languages/';
			$mo_path = $lang_path . $domain . '-' . $locale . '.mo';

			load_textdomain( $domain, $mo_path );
			load_plugin_textdomain( $domain, false, $lang_path );

			$installed_addons = $this->helpers->installedAddons();

			if( ! empty( $installed_addons ) ) {
				foreach( $installed_addons as $addon_name => $addon_info ) {
					$domain = $addon_name;
					$mo_path = trailingslashit( $addon_info['addon_path'] ) . 'languages/' . $domain . '-' . $locale . '.mo';
					$lang_path = trailingslashit( $addon_info['addon_path'] ) . 'languages/';
					if( file_exists( $mo_path ) ) {
						load_textdomain( $domain, $mo_path );
						load_plugin_textdomain( $domain, false, $lang_path );
					}
				}
			}
		}

		public function svgAddMimeTypeSupport( $mimes = [] ) {
			$mimes['svgz'] = 'image/svg+xml';
			$mimes['svg'] = 'image/svg+xml';
			$mimes['webp'] = 'image/webp';

			return $mimes;
		}

		public function imagesOnePixelFix( $image, $attachment_id, $size, $icon ) {
			if( get_post_mime_type( $attachment_id ) == 'image/svg+xml' ) {
				$image['1'] = false;
				$image['2'] = false;
			}
			if( get_post_mime_type( $attachment_id ) == 'image/webp' ) {
				$image['1'] = false;
				$image['2'] = false;
			}

			return $image;
		}

		public function imageFeaturedImageFix( $content, $post_id, $thumbnail_id ) {
			$mime = get_post_mime_type( $thumbnail_id );

			if( 'image/svg+xml' === $mime ) {
				$content = sprintf( '<span class="svg">%s</span>', $content );
			}

			if( 'image/webp' === $mime ) {
				$content = sprintf( '<span class="webp">%s</span>', $content );
			}

			return $content;
		}

		public function svgSkipRegeneration( $metadata, $attachment_id ) {
			$mime = get_post_mime_type( $attachment_id );
			if( 'image/svg+xml' === $mime ) {
				$additional_image_sizes = wp_get_additional_image_sizes();
				$svg_path = get_attached_file( $attachment_id );
				$upload_dir = wp_upload_dir();
				// get the path relative to /uploads/ - found no better way:
				$relative_path = str_replace( $upload_dir['basedir'], '', $svg_path );
				$filename = basename( $svg_path );

				$dimensions = $this->svgDimensions( $svg_path );

				if( ! $dimensions ) {
					return $metadata;
				}

				$metadata = [
					'width' => intval( $dimensions['width'] ),
					'height' => intval( $dimensions['height'] ),
					'file' => $relative_path
				];

				// Might come handy to create the sizes array too - But it's not needed for this workaround! Always links to original svg-file => Hey, it's a vector graphic! ;)
				$sizes = [];
				foreach( get_intermediate_image_sizes() as $s ) {
					$sizes[ $s ] = ['width' => '', 'height' => '', 'crop' => false];

					if( isset( $additional_image_sizes[ $s ]['width'] ) ) {
						// For theme-added sizes
						$sizes[ $s ]['width'] = intval( $additional_image_sizes[ $s ]['width'] );
					} else {
						// For default sizes set in options
						$sizes[ $s ]['width'] = get_option( "{$s}_size_w" );
					}

					if( isset( $additional_image_sizes[ $s ]['height'] ) ) {
						// For theme-added sizes
						$sizes[ $s ]['height'] = intval( $additional_image_sizes[ $s ]['height'] );
					} else {
						// For default sizes set in options
						$sizes[ $s ]['height'] = get_option( "{$s}_size_h" );
					}

					if( isset( $additional_image_sizes[ $s ]['crop'] ) ) {
						// For theme-added sizes
						$sizes[ $s ]['crop'] = intval( $additional_image_sizes[ $s ]['crop'] );
					} else {
						// For default sizes set in options
						$sizes[ $s ]['crop'] = get_option( "{$s}_crop" );
					}

					$sizes[ $s ]['file'] = $filename;
					$sizes[ $s ]['mime-type'] = $mime;
				}
				$metadata['sizes'] = $sizes;
			}

			return $metadata;
		}

		protected function svgDimensions( $svg ) {
			$svg = @simplexml_load_file( $svg );
			$width = 0;
			$height = 0;
			if( $svg ) {
				$attributes = $svg->attributes();
				if( isset( $attributes->width, $attributes->height ) ) {
					$width = floatval( $attributes->width );
					$height = floatval( $attributes->height );
				} elseif( isset( $attributes->viewBox ) ) {
					$sizes = explode( ' ', $attributes->viewBox );
					if( isset( $sizes[2], $sizes[3] ) ) {
						$width = floatval( $sizes[2] );
						$height = floatval( $sizes[3] );
					}
				} else {
					return false;
				}
			}

			return [
				'width' => $width,
				'height' => $height,
				'orientation' => ($width > $height) ? 'landscape' : 'portrait'
			];
		}

		public function getImageTagOverride( $html, $id, $alt, $title, $align, $size ) {
			$mime = get_post_mime_type( $id );

			if( 'image/svg+xml' === $mime ) {
				if( is_array( $size ) ) {
					$width = $size[0];
					$height = $size[1];
				} elseif( 'full' == $size && $dimensions = $this->svgDimensions( get_attached_file( $id ) ) ) {
					$width = $dimensions['width'];
					$height = $dimensions['height'];
				} else {
					$width = get_option( "{$size}_size_w", false );
					$height = get_option( "{$size}_size_h", false );
				}

				if( $height && $width ) {
					$html = str_replace( 'width="1" ', sprintf( 'width="%s" ', $width ), $html );
					$html = str_replace( 'height="1" ', sprintf( 'height="%s" ', $height ), $html );
				} else {
					$html = str_replace( 'width="1" ', '', $html );
					$html = str_replace( 'height="1" ', '', $html );
				}

				$html = str_replace( '/>', ' role="img" />', $html );
			}

			return $html;
		}

		public function fixShortcodes( $content ) {
			$array = [
				'<p>[' => '[',
				']</p>' => ']',
				']<br />' => ']',
				']<br>' => ']'
			];

			$content = strtr( $content, $array );

			return $content;
		}

		public function redirect() {
			if( isset( $_GET['cjwpbldr-redirect'] ) && $_GET['cjwpbldr-redirect'] != '' ) {
				$link = urldecode( $_GET['cjwpbldr-redirect'] );
				wp_redirect( $link );
				die();
			}
		}

		public function retinaSupportAttachmentMeta( $metadata, $attachment_id ) {
			foreach( $metadata as $key => $value ) {
				if( is_array( $value ) ) {
					foreach( $value as $image => $attr ) {
						$width = (isset( $attr['width'] )) ? $attr['width'] : '';
						$height = (isset( $attr['height'] )) ? $attr['height'] : '';
						if( is_array( $attr ) ) {
							$this->retinaSupportCreateImages( get_attached_file( $attachment_id ), $width, $height, true );
						}
					}
				}
			}

			return $metadata;
		}

		public function retinaSupportCreateImages( $file, $width, $height, $crop = false ) {
			if( $width || $height ) {
				$resized_file = wp_get_image_editor( $file );
				if( ! is_wp_error( $resized_file ) ) {
					$filename = $resized_file->generate_filename( $width . 'x' . $height . '@2x' );

					$resized_file->resize( $width * 2, $height * 2, $crop );
					$resized_file->save( $filename );

					$info = $resized_file->get_size();

					return [
						'file' => wp_basename( $filename ),
						'width' => $info['width'],
						'height' => $info['height'],
					];
				}
			}

			return false;
		}

		public function deleteRetinaSupportImages( $attachment_id ) {
			$meta = wp_get_attachment_metadata( $attachment_id );
			$upload_dir = wp_upload_dir();
			if( isset( $meta['file'] ) && $meta['file'] != '' ) {
				$path = pathinfo( $meta['file'] );
				foreach( $meta as $key => $value ) {
					if( 'sizes' === $key ) {
						foreach( $value as $sizes => $size ) {
							$original_filename = $upload_dir['basedir'] . '/' . $path['dirname'] . '/' . $size['file'];
							$retina_filename = substr_replace( $original_filename, '@2x.', strrpos( $original_filename, '.' ), strlen( '.' ) );
							if( file_exists( $retina_filename ) ) {
								unlink( $retina_filename );
							}
						}
					}
				}
			}
		}

		public function savePostThumbString( $post_id ) {
			if( wp_is_post_revision( $post_id ) ) {
				return;
			}
			$post_info = $this->helpers->postInfo( $post_id );
			if( isset( $post_info['featured_image_url'] ) && $post_info['featured_image_url'] != '' ) {
				$string = $this->helpers->imgToBase64( $post_info['featured_image_url'] );
				update_post_meta( $post_id, 'featured_image_string', $string );
			}
		}

		public function registerPostTypes() {
			$post_types = $this->helpers->config( 'custom-post-types' );
			if( is_array( $post_types ) && ! empty( $post_types ) ) {
				foreach( $post_types as $key => $post_type ) {
					if( ! empty( $post_type ) && isset( $post_type['labels'] ) ) {
						$labels = $post_type['labels'];
						$post_type_args = $post_type['args'];
						unset( $post_type_args['labels'] );
						$args = $post_type_args;
						$args['labels'] = $labels;
						register_post_type( $key, $args );
					}
				}
			}
		}

		public function registerTaxonomies() {
			$custom_taxonomies = $this->helpers->config( 'custom-taxonomies' );
			if( is_array( $custom_taxonomies ) && ! empty( $custom_taxonomies ) ) {
				foreach( $custom_taxonomies as $key => $taxonomy ) {
					if( ! empty( $taxonomy ) ) {
						$labels = $taxonomy['labels'];
						$taxonomy_args = $taxonomy['args'];
						unset( $taxonomy_args['labels'] );
						$args = $taxonomy_args;
						$args['labels'] = $labels;
						register_taxonomy( $key, $taxonomy['post_types'], $args );
					}
				}
			}
		}

		public function registerNavMenus() {
			$nav_menus = $this->helpers->config( 'custom-nav-menus' );
			if( is_array( $nav_menus ) && ! empty( $nav_menus ) ) {
				foreach( $nav_menus as $key => $nav_menu ) {
					register_nav_menus( $nav_menu );
				}
			}
		}

		public function adminFooter() {
			ob_start();
			require_once sprintf( '%s/html/admin-footer.php', $this->helpers->framework_dir );
			echo ob_get_clean();
		}

		public function adminHead() {
			ob_start();
			require_once sprintf( '%s/html/admin-head.php', $this->helpers->framework_dir );
			echo ob_get_clean();
		}

		public function printWpFooter() {
			$content = ['<!-- hooked wp-footer content -->'];
			$content = apply_filters( 'cjwpbldr_wp_footer_filter', $content );
			echo implode( '', $content );
		}

		public function productActivateAndUpgrades() {
			$title = $this->helpers->config( 'plugin-info', 'name' );
			// upgrades
			$upgrades_available = get_transient( '_cj_upgradable_products' );
			$installed_product_slugs = $this->helpers->installedProducts( 'slugs' );

			if( $upgrades_available && ! empty( $upgrades_available ) ) {
				if( isset( $_GET['callback'] ) ) {
					if( $_GET['callback'] == 'core-welcome' || $_GET['callback'] == 'core-cloud' ) {
						return false;
					}
				}
				$product_slugs = [];
				foreach( $upgrades_available as $key => $item ) {
					if( in_array( $key, $installed_product_slugs ) ) {
						$product_slugs[] = $key;
					}
				}
				$upgrade_button = '<p class="cj-mt-10 cj-mb-0"><a class="cj-button cj-is-small cj-is-info" href="' . $this->helpers->callbackUrl( 'core-cloud', true ) . 'show=upgrades">' . __( 'Upgrade Now', 'wp-builder-locale' ) . '</a></p>';
				if( ! empty( $product_slugs ) ) {
					$message = sprintf( __( 'Upgrades available to for: <span class="cj-ml-5 cj-text-bold">%s</span>', 'wp-builder-locale' ), implode( ', ', $product_slugs ) . $upgrade_button );
					echo $this->helpers->adminNotice( 'cjwpbldr-upgrades', 'info', $message, $title, true );
				}
			}
		}

		public function saveRegisteredSidebars() {
			$registered_sidebars = $GLOBALS['wp_registered_sidebars'];
			$sidebars_array = [];
			if( is_array( $registered_sidebars ) && ! empty( $registered_sidebars ) ) {
				foreach( $registered_sidebars as $key => $registered_sidebar ) {
					$sidebars_array[ $registered_sidebar['id'] ] = $registered_sidebar['name'];
				}
			}
			update_option( 'cjwpbldr_globals_sidebars', $sidebars_array );
		}

	}

	cjwpbldr_core_setup::getInstance();
}