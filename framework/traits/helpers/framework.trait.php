<?php

use MatthiasMullie\Minify;
use ScssPhp\ScssPhp\Compiler;

/**
 * Trait cjwpbldr_helpers_framework
 */
trait cjwpbldr_helpers_framework {

	/**
	 * @return bool
	 */
	public function isTheme() {
		if( $this->containString( dirname( $this->root_dir ), get_template_directory() ) ) {
			return true;
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public function hasTheme() {
		$theme_check_path = get_stylesheet_directory() . '/extend/framework/autoload/core-setup.php';
		if( file_exists( $theme_check_path ) ) {
			return true;
		}

		return false;
	}

	/**
	 * @param        $name
	 * @param string $value
	 * @param int    $expire
	 * @param string $path
	 * @param string $domain
	 * @param null   $secure
	 * @param bool   $httponly
	 *
	 * @return bool
	 */
	public function setCookie( $name, $value = '', $expire = 86400, $path = '/', $domain = '', $secure = null, $httponly = false ) {
		$_COOKIE[ $name ] = $value;
		$expire = time() + $expire;

		$secure = (is_null( $secure )) ? is_ssl() : $secure;

		return setcookie( $name, $value, $expire, $path, $domain, $secure, $httponly );
	}

	/**
	 * @param     $name
	 * @param int $default
	 *
	 * @return int|mixed
	 */
	public function getCookie( $name, $default = 0 ) {
		return (isset( $_COOKIE[ $name ] )) ? $_COOKIE[ $name ] : $default;
	}

	/**
	 * @param $name
	 *
	 * @return bool
	 */
	public function deleteCookie( $name ) {
		$this->setCookie( $name, 0, 0 );

		return true;
	}

	/**
	 * @param $slug
	 *
	 * @return string
	 */
	public function getAddonPath( $slug ) {
		$directory_path = sprintf( '%s/%s', WP_PLUGIN_DIR, $slug );
		$wpbldr_file_path = sprintf( '%s/wp-builder.php', $directory_path );
		if( is_dir( $directory_path ) && file_exists( $wpbldr_file_path ) ) {
			return $directory_path;
		}
		$directory_path = sprintf( '%s/modules/%s', $this->root_dir, $slug );
		$wpbldr_file_path = sprintf( '%s/wp-builder.php', $directory_path );
		if( is_dir( $directory_path ) && file_exists( $wpbldr_file_path ) ) {
			return $directory_path;
		}
		$directory_path = sprintf( '%s/%s', dirname( $this->root_dir ), $slug );
		$wpbldr_file_path = sprintf( '%s/wp-builder.php', $directory_path );
		if( is_dir( $directory_path ) && file_exists( $wpbldr_file_path ) ) {
			return $directory_path;
		}
		$directory_path = sprintf( '%s/addons/%s', $this->cjwpbldr_content_path, $slug );
		$wpbldr_file_path = sprintf( '%s/wp-builder.php', $directory_path );
		if( is_dir( $directory_path ) && file_exists( $wpbldr_file_path ) ) {
			return $directory_path;
		}

		return '';
	}

	/**
	 * @param $slug
	 *
	 * @return bool|mixed
	 */
	public function isProductActive( $slug ) {
		/*$premium_key = get_option( '_cj_premium_key', false );
		if( $premium_key ) {
			return $premium_key;
		}*/
		$key = get_transient( '_cjlkey_' . $slug );

		return (strlen( $key ) < 10) ? false : $key;
	}

	/**
	 * @param null   $var
	 * @param string $addon_slug_or_path
	 * @param bool   $include_content
	 *
	 * @return array|mixed
	 */
	public function addonInfo( $var = null, $addon_slug_or_path = '', $include_content = false ) {
		if( $addon_slug_or_path !== '' && ! is_dir( $addon_slug_or_path ) ) {
			$addon_slug_or_path = $this->getAddonPath( $addon_slug_or_path );
		}
		$file_path = sprintf( '%s/config/info.php', $addon_slug_or_path );
		$return = [];
		if( file_exists( $file_path ) ) {
			$return = include $file_path;
			if( $include_content ) {
				$return['features'] = [];
				$return['content'] = '';
				$features_file_path = $return['addon_path'] . '/config/publish/features.php';
				if( file_exists( $features_file_path ) ) {
					$return['features'] = include $features_file_path;
				}
				$content_file_path = $return['addon_path'] . '/config/publish/content.md';
				if( file_exists( $content_file_path ) ) {
					$parse_mark_down = new Parsedown();
					$return['content'] = $parse_mark_down->text( $this->getFileContents( $content_file_path ) );
				}
			}

			return ( ! is_null( $var ) && isset( $return[ $var ] )) ? $return[ $var ] : $return;
		}

		return $return;
	}

	/**
	 * @param null   $type
	 * @param null   $var
	 * @param string $addon_dir
	 *
	 * @return array|mixed|string
	 */
	public function config( $type = null, $var = null, $addon_dir = '' ) {
		$config_dir = $this->config_dir;
		if( $addon_dir !== '' ) {
			$config_dir = sprintf( '%s/config', $addon_dir );
		}
		$config_dirs = [];
		if( is_dir( $config_dir ) && file_exists( $config_dir ) ) {
			$config_dirs = preg_grep( '/^([^.])/', scandir( $config_dir ) );
		}
		$return = [];
		if( is_array( $config_dirs ) && ! empty( $config_dirs ) ) {
			$config = [];
			foreach( $config_dirs as $index => $file ) {
				$file_path = sprintf( '%s/%s', $config_dir, $file );
				if( is_file( $file_path ) ) {
					$config = require sprintf( '%s/%s', $config_dir, $file );
				}
				if( is_array( $config ) && ! empty( $config ) && strpos( $file, '.php' ) ) {
					$file_name = str_replace( '.php', '', $file );
					foreach( $config as $key => $value ) {
						$return[ $file_name ][ $key ] = $value;
					}
				}
			}
		}
		if( is_null( $type ) ) {
			return $return;
		}
		if( ! is_null( $type ) && is_null( $var ) ) {
			return (isset( $return[ $type ] )) ? $return[ $type ] : '';
		}
		if( ! is_null( $type ) && ! is_null( $var ) ) {
			return (isset( $return[ $type ] ) && isset( $return[ $type ][ $var ] )) ? $return[ $type ][ $var ] : '';
		}
	}

	/**
	 * @return array
	 */
	public function installedAddons() {
		$addons = [];
		$inbuilt_modules = sprintf( '%s/modules', $this->root_dir );
		if( is_dir( $inbuilt_modules ) ) {
			$dirs = preg_grep( '/^([^.])/', scandir( $inbuilt_modules ) );
			if( is_array( $dirs ) && ! empty( $dirs ) ) {
				foreach( $dirs as $key => $value ) {
					if( strpos( $value, 'addon-' ) === 0 && is_dir( $inbuilt_modules . '/' . $value ) ) {
						$module_config = $this->moduleConfig( $value, 'info' );
						if( ! empty( $module_config ) ) {
							$addons[ $value ] = $module_config;
						}
					}
				}
			}
		}

		$network_plugins = get_site_option( 'active_sitewide_plugins', [] );
		if( ! empty( $network_plugins ) ) {
			foreach( $network_plugins as $active_plugin => $code ) {
				if( $this->containString( 'addon-', $active_plugin ) ) {
					$plugin_slug = explode( '/', $active_plugin )[0];
					$module_config = $this->moduleConfig( $plugin_slug, 'info' );
					if( ! empty( $module_config ) ) {
						$addons[ $plugin_slug ] = $this->moduleConfig( $plugin_slug, 'info' );
					}
				}
			}
		}

		$active_plugins = get_option( 'active_plugins', [] );
		if( ! empty( $active_plugins ) ) {
			foreach( $active_plugins as $key => $active_plugin ) {
				if( $this->containString( 'addon-', $active_plugin ) ) {
					$plugin_slug = explode( '/', $active_plugin )[0];
					$module_config = $this->moduleConfig( $plugin_slug, 'info' );
					if( ! empty( $module_config ) ) {
						$addons[ $plugin_slug ] = $this->moduleConfig( $plugin_slug, 'info' );
					}
				}
			}
		}

		$wp_content_modules = sprintf( '%s/addons', $this->cjwpbldr_content_path );
		if( is_dir( $wp_content_modules ) ) {
			$dirs = preg_grep( '/^([^.])/', scandir( $wp_content_modules ) );
			if( is_array( $dirs ) && ! empty( $dirs ) ) {
				foreach( $dirs as $key => $value ) {
					if( strpos( $value, 'addon-' ) === 0 && is_dir( $wp_content_modules . '/' . $value ) ) {
						$module_config = $this->moduleConfig( $value, 'info' );
						if( ! empty( $module_config ) ) {
							$addons[ $value ] = $module_config;
						}
					}
				}
			}
		}

		return $addons;
	}

	/**
	 * @param      $slug
	 * @param      $type
	 * @param null $var
	 *
	 * @return array|mixed
	 */
	public function moduleConfig( $slug, $type, $var = null ) {
		$path = '';
		$config_file_path = sprintf( '%s/modules/%s/config/%s.php', $this->root_dir, $slug, $type );
		$path = (file_exists( $config_file_path )) ? $config_file_path : $path;
		$config_file_path = sprintf( '%s/%s/config/%s.php', dirname( $this->root_dir ), $slug, $type );
		$path = (file_exists( $config_file_path )) ? $config_file_path : $path;
		$config_file_path = sprintf( '%s/addons/%s/config/%s.php', $this->cjwpbldr_content_path, $slug, $type );
		$path = (file_exists( $config_file_path )) ? $config_file_path : $path;
		$config_file_path = sprintf( '%s/%s/config/%s.php', WP_PLUGIN_DIR, $slug, $type );
		$path = (file_exists( $config_file_path )) ? $config_file_path : $path;
		if( $path == '' ) {
			return [];
		}
		$data = include $path;
		if( $type == 'info' ) {
			$data['screenshot'] = 'http://placehold.it/900x540/0f0f0f/aaaaaa&text=SCREENSHOT';
			$screenshot = explode( $slug, $path )[0] . $slug . '/screenshot.png';
			if( file_exists( $screenshot ) ) {
				$data['screenshot'] = str_replace( ABSPATH, site_url( '/' ), $screenshot );
			}
			$screenshot = explode( $slug, $path )[0] . $slug . '/screenshot.jpg';
			if( file_exists( $screenshot ) ) {
				$data['screenshot'] = str_replace( ABSPATH, site_url( '/' ), $screenshot );
			}
		}

		return ( ! is_null( $var ) && isset( $data[ $var ] )) ? $data[ $var ] : $data;
	}

	/**
	 * @param string $file_name
	 * @param null   $query_string
	 * @param string $addon_page_slug
	 *
	 * @return string
	 */
	public function callbackUrl( $file_name = 'welcome', $query_string = null, $addon_page_slug = '' ) {
		$admin_page_url = admin_url( 'admin.php?page=' . $this->config( 'plugin-info', 'page_slug' ) );
		if( $addon_page_slug !== '' ) {
			$admin_page_url = admin_url( 'admin.php?page=' . $addon_page_slug );
		}
		$return = $this->queryString( $admin_page_url ) . "callback=" . $file_name;

		return (is_null( $query_string )) ? $return : $this->queryString( $return );
	}

	/**
	 *
	 */
	public function hookGoogleApiToItems() {
		$slugs = $this->installedProducts( 'all', false );
		$items = [];
		foreach( $slugs as $key => $item ) {
			if( $item['pas'] == '0' ) {
				$items[] = $item['slug'];
			}
		}

		echo '<div class="cjwpbldr-google-api-hooks" style="display: none;">';
		echo implode( '|', $items );
		echo '</div>';
	}

	/**
	 * @return bool
	 */
	public function isLocal() {
		$dev_path = sprintf( '%s/autoload/dev-setup.php', $this->framework_dir );

		return (file_exists( $dev_path ));
	}

	/**
	 * @param null   $size
	 * @param string $type
	 * @param string $return
	 * @param string $styles
	 *
	 * @return string
	 */
	public function logo( $size = null, $type = 'logo-on-light', $return = 'image', $styles = '' ) {
		$url = $this->assets_url . '/svg/' . $type . '.svg';
		if( $return != 'image' ) {
			return $url;
		}
		$title = get_bloginfo( 'name' );

		return sprintf( '<img src="%s" width="%s" title="%s" alt="%s" style="line-height: 0; %s" />', $url, $size, $title, $title, $styles );
	}

	/**
	 * @param string $class
	 * @param        $messages
	 * @param null   $heading
	 * @param bool   $close_btn
	 * @param string $close_btn_url
	 *
	 * @return string
	 */
	public function alert( $class = 'danger', $messages, $heading = null, $close_btn = false, $close_btn_url = '' ) {
		$return = '';
		$return .= '<div class="cj-notification cj-is-' . $class . '">';
		if( $close_btn && $close_btn_url == '' ) {
			$return .= '<a href="#" class="cj-close cj-delete cj-m-10" data-dismiss="alert" aria-label="close"></a>';
		}
		if( $close_btn && $close_btn_url != '' ) {
			$return .= '<a href="' . $close_btn_url . '" class="cj-delete cj-m-10" aria-label="close"></a>';
		}
		$return .= ( ! is_null( $heading )) ? '<div class="cj-mb-3"><strong>' . $heading . '</strong></div>' : '';
		$return .= is_array( $messages ) ? implode( '<br>', $messages ) : $messages;
		$return .= '</div>';

		return $return;
	}

	/**
	 * @param        $option
	 * @param array  $selected_value
	 * @param string $selected
	 *
	 * @return array
	 */
	public function compileGroupItems( $option, $selected_value = [], $selected = '' ) {
		$group_options = [];
		if( isset( $option['items'] ) && is_array( $option['items'] ) && ! empty( $option['items'] ) ) {
			foreach( $option['items'] as $option_key => $item_options ) {
				if( is_array( $item_options ) && ! empty( $item_options ) ) {
					foreach( $item_options as $item_key => $item_array ) {
						$item_array['container_class'] = '';
						$item_array['id'] = "{$option['id']}[items][{$option_key}][{$item_array['id']}]";
						$item_array['container_class'] = 'group-item group-item-' . $option['group'] . ' group-' . $option['group'] . '-' . $option_key;
						if( ! empty( $selected_value ) && '' != $selected ) {


							if(
								is_array( $selected_value['items'] ) &&
								isset( $selected_value['items'][ $selected ] ) &&
								is_array( $selected_value['items'][ $selected ] ) &&
								array_key_exists( $item_key, $selected_value['items'][ $selected ] ) &&
								$selected_value['items'][ $selected ][ $item_key ] != $item_array['default']
							) {
								$item_array['default'] = $selected_value['items'][ $selected ][ $item_key ];
							}
						}
						$group_options[ $item_key ] = $item_array;
						if( isset( $item_array['items'] ) ) {
							$group_options[ $item_key ][ $item_array['id'] ] = $this->compileGroupItems( $item_array );
						}
					}
				}
			}
		}

		return $group_options;
	}

	/**
	 * @param null $var
	 *
	 * @return array|mixed
	 */
	public function userScssVariables( $var = null ) {
		$return = [
			'content-heading' => [
				'type' => 'sub-heading',
				'id' => 'content-heading',
				'label' => ucwords( __( 'Content', 'wp-builder-locale' ) ),
				'default' => '',
				'suffix' => '',
			],
			'family-primary' => [
				'type' => 'font-family',
				'id' => 'family-primary',
				'label' => ucwords( __( 'Body font family', 'wp-builder-locale' ) ),
				'default' => ($this->isTheme() || $this->isLocal()) ? 'Lato' : 'inherit',
			],
			'family-code' => [
				'type' => 'font-family',
				'id' => 'family-code',
				'label' => ucwords( __( 'Code font family', 'wp-builder-locale' ) ),
				'default' => 'Source Code Pro',
			],
			'body-size' => [
				'type' => 'number',
				'id' => 'body-size',
				'label' => ucwords( __( 'Font Size', 'wp-builder-locale' ) ),
				'default' => '16',
				'suffix' => 'px',
			],
			'small-font-size' => [
				'type' => 'number',
				'id' => 'small-font-size',
				'label' => ucwords( __( 'Small Font Size', 'wp-builder-locale' ) ),
				'default' => '14',
				'suffix' => 'px',
			],
			'content-headings-heading' => [
				'type' => 'sub-heading',
				'id' => 'content-headings-heading',
				'label' => ucwords( __( 'Headings', 'wp-builder-locale' ) ),
				'default' => '',
				'suffix' => '',
			],
			'family-headings' => [
				'type' => 'font-family',
				'id' => 'family-headings',
				'label' => ucwords( __( 'Headings font family', 'wp-builder-locale' ) ),
				'default' => ($this->isTheme() || $this->isLocal()) ? 'Nunito Sans' : 'inherit',
			],
			'weight-headings' => [
				'type' => 'dropdown',
				'id' => 'weight-headings',
				'label' => ucwords( __( 'Headings font weight', 'wp-builder-locale' ) ),
				'default' => 'bold',
				'options' => $this->arrays( 'font-weight' ),
			],
			'heading-size-1' => [
				'type' => 'number',
				'id' => 'heading-size-1',
				'suffix' => 'px',
				'label' => ucwords( __( 'H1 Size', 'wp-builder-locale' ) ),
				'default' => '48',
			],
			'heading-size-2' => [
				'type' => 'number',
				'id' => 'heading-size-2',
				'suffix' => 'px',
				'label' => ucwords( __( 'H2 Size', 'wp-builder-locale' ) ),
				'default' => '42',
			],
			'heading-size-3' => [
				'type' => 'number',
				'id' => 'heading-size-3',
				'suffix' => 'px',
				'label' => ucwords( __( 'H3 Size', 'wp-builder-locale' ) ),
				'default' => '36',
			],
			'heading-size-4' => [
				'type' => 'number',
				'id' => 'heading-size-4',
				'suffix' => 'px',
				'label' => ucwords( __( 'H4 Size', 'wp-builder-locale' ) ),
				'default' => '30',
			],
			'heading-size-5' => [
				'type' => 'number',
				'id' => 'heading-size-5',
				'suffix' => 'px',
				'label' => ucwords( __( 'H5 Size', 'wp-builder-locale' ) ),
				'default' => '24',
			],
			'heading-size-6' => [
				'type' => 'number',
				'id' => 'heading-size-6',
				'suffix' => 'px',
				'label' => ucwords( __( 'H6 Size', 'wp-builder-locale' ) ),
				'default' => '18',
			],
			'titles-heading' => [
				'type' => 'sub-heading',
				'id' => 'titles-heading',
				'label' => ucwords( __( 'Titles', 'wp-builder-locale' ) ),
				'default' => '',
				'suffix' => '',
			],
			'title-family' => [
				'type' => 'font-family',
				'id' => 'title-family',
				'label' => ucwords( __( 'Title font family', 'wp-builder-locale' ) ),
				'default' => ($this->isTheme() || $this->isLocal()) ? 'Nunito Sans' : 'inherit',
			],
			'title-weight' => [
				'type' => 'dropdown',
				'id' => 'title-weight',
				'label' => ucwords( __( 'Title font weight', 'wp-builder-locale' ) ),
				'default' => 'bold',
				'options' => $this->arrays( 'font-weight' ),
			],
			'section-title-size' => [
				'type' => 'number',
				'id' => 'section-title-size',
				'suffix' => 'px',
				'label' => ucwords( __( 'Title Font Size', 'wp-builder-locale' ) ),
				'default' => '48',
			],
			'subtitle-family' => [
				'type' => 'font-family',
				'id' => 'subtitle-family',
				'label' => ucwords( __( 'Sub-Title font family', 'wp-builder-locale' ) ),
				'default' => ($this->isTheme() || $this->isLocal()) ? 'Lato' : 'inherit',
			],
			'subtitle-weight' => [
				'type' => 'dropdown',
				'id' => 'subtitle-weight',
				'label' => ucwords( __( 'Sub-Title font weight', 'wp-builder-locale' ) ),
				'default' => 'normal',
				'options' => $this->arrays( 'font-weight' ),
			],
			'subtitle-opacity' => [
				'type' => 'dropdown',
				'id' => 'subtitle-opacity',
				'label' => ucwords( __( 'Sub-Title Opacity', 'wp-builder-locale' ) ),
				'default' => '70',
				'options' => [
					'10' => '10',
					'20' => '20',
					'30' => '30',
					'40' => '40',
					'50' => '50',
					'60' => '60',
					'70' => '70',
					'80' => '80',
					'90' => '90',
					'100' => '100',
				],
			],
			'section-subtitle-size' => [
				'type' => 'number',
				'id' => 'section-subtitle-size',
				'suffix' => 'px',
				'label' => ucwords( __( 'Title Font Size', 'wp-builder-locale' ) ),
				'default' => '24',
			],
			'brand-colors-heading' => [
				'type' => 'sub-heading',
				'id' => 'brand-colors-heading',
				'label' => ucwords( __( 'Brand', 'wp-builder-locale' ) ),
				'default' => '',
				'suffix' => '',
			],
			'primary' => [
				'id' => 'primary',
				'type' => 'color',
				'label' => ucwords( __( 'Color primary', 'wp-builder-locale' ) ),
				'default' => '#017BFF',
			],
			'primary-invert' => [
				'id' => 'primary-invert',
				'type' => 'color',
				'label' => ucwords( __( 'Color primary invert', 'wp-builder-locale' ) ),
				'default' => '#ffffff',
			],
			'success' => [
				'id' => 'success',
				'type' => 'color',
				'label' => ucwords( __( 'Color success', 'wp-builder-locale' ) ),
				'default' => '#28A745',
			],
			'success-invert' => [
				'id' => 'success-invert',
				'type' => 'color',
				'label' => ucwords( __( 'Color success invert', 'wp-builder-locale' ) ),
				'default' => '#ffffff',
			],
			'danger' => [
				'id' => 'danger',
				'type' => 'color',
				'label' => ucwords( __( 'Color danger', 'wp-builder-locale' ) ),
				'default' => '#DD3545',
			],
			'danger-invert' => [
				'id' => 'danger-invert',
				'type' => 'color',
				'label' => ucwords( __( 'Color danger invert', 'wp-builder-locale' ) ),
				'default' => '#ffffff',
			],
			'info' => [
				'id' => 'info',
				'type' => 'color',
				'label' => ucwords( __( 'Color info', 'wp-builder-locale' ) ),
				'default' => '#18A2B8',
			],
			'info-invert' => [
				'id' => 'info-invert',
				'type' => 'color',
				'label' => ucwords( __( 'Color info invert', 'wp-builder-locale' ) ),
				'default' => '#ffffff',
			],
			'warning' => [
				'id' => 'warning',
				'type' => 'color',
				'label' => ucwords( __( 'Color warning', 'wp-builder-locale' ) ),
				'default' => '#FFC009',
			],
			'warning-invert' => [
				'id' => 'warning-invert',
				'type' => 'color',
				'label' => ucwords( __( 'Color warning invert', 'wp-builder-locale' ) ),
				'default' => '#0F0F0F',
			],
			'dark' => [
				'id' => 'dark',
				'type' => 'color',
				'label' => ucwords( __( 'Color dark', 'wp-builder-locale' ) ),
				'default' => '#0F0F0F',
			],
			'dark-invert' => [
				'id' => 'dark-invert',
				'type' => 'color',
				'label' => ucwords( __( 'Color dark invert', 'wp-builder-locale' ) ),
				'default' => '#ffffff',
			],
			'light' => [
				'id' => 'light',
				'type' => 'color',
				'label' => ucwords( __( 'Color light', 'wp-builder-locale' ) ),
				'default' => '#F8F9FA',
			],
			'light-invert' => [
				'id' => 'light-invert',
				'type' => 'color',
				'label' => ucwords( __( 'Color light invert', 'wp-builder-locale' ) ),
				'default' => '#0F0F0F',
			],
			'radius-heading' => [
				'type' => 'sub-heading',
				'id' => 'radius-heading',
				'label' => ucwords( __( 'Radius', 'wp-builder-locale' ) ),
				'default' => '',
				'suffix' => '',
			],
			'radius' => [
				'id' => 'radius',
				'type' => 'number',
				'label' => ucwords( __( 'Radius', 'wp-builder-locale' ) ),
				'default' => '4',
				'suffix' => 'px',
			],
			'radius-small' => [
				'id' => 'radius-small',
				'type' => 'number',
				'label' => ucwords( __( 'Radius small', 'wp-builder-locale' ) ),
				'default' => '3',
				'suffix' => 'px',
			],
			'radius-large' => [
				'id' => 'radius-large',
				'type' => 'number',
				'label' => ucwords( __( 'Radius large', 'wp-builder-locale' ) ),
				'default' => '6',
				'suffix' => 'px',
			],
			'input-radius' => [
				'id' => 'input-radius',
				'type' => 'number',
				'label' => ucwords( __( 'Input radius', 'wp-builder-locale' ) ),
				'default' => '4',
				'suffix' => 'px',
			],

		];

		return (is_null( $var )) ? $return : $return[ $var ];
	}

	/**
	 *
	 */
	public function compileCoreScssFiles() {
		$bulma_scss_file = $this->cjwpbldr_content_path . '/ui/ui-base.min.css';
		$bulma_scss_content = "@import 'ui-base'";
		$helpers_scss_file = $this->cjwpbldr_content_path . '/ui/helpers.min.css';
		$helpers_scss_content = "@import 'helpers'";
		try {
			// bulma
			$bulma_compiled_css = $this->compileScss( $bulma_scss_content );
			$this->putFileContents( $bulma_scss_file, $bulma_compiled_css );
			//$this->minifyCssFile( $bulma_scss_file );

			// helpers
			$helpers_compiled_css = $this->compileScss( $helpers_scss_content );
			$this->putFileContents( $helpers_scss_file, $helpers_compiled_css );
			//$this->minifyCssFile( $helpers_scss_file );

			return true;
		} catch( Exception $e ) {
			return $e->getMessage();
		}
	}

	/**
	 * @param string $path
	 *
	 * @return bool|string
	 */
	public function minifyCssFile( $path = '' ) {
		if( $path == '' ) {
			return false;
		}
		try {
			$minifier = new Minify\CSS( $path );
			$minifier->minify( $path );
		} catch( Exception $e ) {
			return $e->getMessage();
		}
	}

	/**
	 * @param string $block_id
	 */
	public function compileBlocksScss( $block_id = '' ) {
		if( $block_id == '' ) {
			$blocks = $this->getPostsByMetaKey( '_ui_block_class_name', '', 'data' );
			if( is_array( $blocks ) && ! empty( $blocks ) ) {
				foreach( $blocks as $key => $block_info ) {
					$class_name = $block_info['_ui_block_class_name'];
					if( class_exists( $class_name ) ) {
						$class_instance = $class_name::getInstance();
						$block_path = $class_instance->info( 'path' );
						$file = $block_path . '/style.scss';
						if( file_exists( $file ) ) {
							//$scss = '@import "global/mixins";';
							ob_start();
							require $file;
							$scss = ob_get_clean();
							$css = $this->compileScss( $scss );
							update_post_meta( $block_info['ID'], '_block_css', $css );
						}
					}
				}
			}
		} else {
			$block_info = $this->postInfo( $block_id );
			$class_name = $block_info['_ui_block_class_name'];
			if( class_exists( $class_name ) ) {
				$class_instance = $class_name::getInstance();
				$block_path = $class_instance->info( 'path' );
				$file = $block_path . '/style.scss';
				if( file_exists( $file ) ) {
					//$scss = '@import "global/mixins";';
					ob_start();
					require $file;
					$scss = ob_get_clean();
					$css = $this->compileScss( $scss );
					update_post_meta( $block_info['ID'], '_block_css', $css );
				}
			}
		}
	}

	/**
	 * @param string $slug
	 *
	 * @return bool
	 */
	public function compileAddonScss( $slug = '' ) {
		if( $slug != '' ) {
			$addon_path = $this->addonInfo( 'addon_path', $slug );
			$scss_files_dir_path = trailingslashit( trailingslashit( $addon_path ) . 'assets/scss' );
			$dirs = preg_grep( '/^([^.])/', scandir( $scss_files_dir_path ) );
			if( ! empty( $dirs ) ) {
				foreach( $dirs as $key => $folder_name ) {
					$scss_file_path = sprintf( '%s/scss/main.scss', $scss_files_dir_path . $folder_name );
					if( file_exists( $scss_file_path ) ) {
						ob_start();
						require_once $scss_file_path;
						$scss_code = ob_get_clean();
						$scss_code = str_replace( '@import "../../../../../source/sass/global/imports";', '', $scss_code );
						$import_paths = [];
						$import_paths[ $slug . '-' . $key ] = dirname( $scss_file_path );
						$css_code = $this->compileScss( $scss_code, $import_paths );
						$css_file_path = sprintf( '%s/assets/css/%s.min.css', $addon_path, $folder_name );
						$this->putFileContents( $css_file_path, $css_code );
						//$this->minifyCssFile( $css_file_path );
					}
				}
			}
		}

		return true;
	}

	/**
	 * @return bool
	 */
	public function compileAddonsScss() {
		$installed_addons = $this->installedProducts( 'addons' );
		if( ! empty( $installed_addons ) ) {
			foreach( $installed_addons as $key => $addon_info ) {
				$slug = $addon_info['slug'];
				$this->compileAddonScss( $slug );
			}
		}

		return true;
	}

	/**
	 * @param       $scss
	 * @param array $additional_import_paths
	 *
	 * @return string
	 */
	public function compileScss( $scss, $additional_import_paths = [] ) {
		$source_folder = $this->framework_dir . '/sass/';
		$variables[] = '';
		$user_variables = '';
		$sass_core_variables = $this->coreScssVariables();
		$scss_compiler = new Compiler();
		$import_paths = array_merge( [
			'core' => $source_folder,
			'bulma' => $source_folder . 'bulma',
			'global' => $source_folder . 'global',
		], $additional_import_paths );
		$scss_compiler->setImportPaths( $import_paths );
		$saved_variables = $this->getOption( 'core_sass_variables' );
		if( is_array( $saved_variables ) && ! empty( $saved_variables ) ) {
			foreach( $saved_variables as $var_key => $var_value ) {
				$var_value = (is_numeric( $var_value )) ? $var_value . 'px' : $var_value;
				if( $this->containString( 'opacity', $var_key ) ) {
					$var_value = str_replace( 'px', '', $var_value );
				}
				$variables[] = '$' . $var_key . ':' . $var_value . ' !default;';
			}
			$user_variables = implode( ' ', array_filter( $variables ) );
		}
		$scss_to_compile = $user_variables;
		$scss_to_compile .= $sass_core_variables;
		$scss_to_compile .= $scss;

		try {
			// bulma
			$css = $scss_compiler->compile( $scss_to_compile );

			return $css;
		} catch( Exception $e ) {
			return $e->getMessage();
		}
	}

	/**
	 * @return false|string
	 */
	public function coreScssVariables() {
		ob_start();
		$file_path = sprintf( '%s/sass/global/_imports.scss', $this->framework_dir );
		if( file_exists( $file_path ) ) {
			require_once $file_path;
		}

		return ob_get_clean();
	}

	/**
	 * @param array  $shortcode_details
	 * @param string $content
	 *
	 * @return string
	 */
	public function generateShortcodeTag( $shortcode_details = [], $content = '' ) {

		if( empty( $shortcode_details ) ) {
			return '';
		}

		if( $content == '' ) {
			$content = __( '..content goes here..', 'wp-builder-locale' );
		}

		$shortcode_options_string = [];
		if( is_array( $shortcode_details['options'] ) ) {
			foreach( $shortcode_details['options'] as $options_key => $options_value ) {
				if( $options_value['type'] !== 'sub-heading' ) {
					$default_value = (is_array( $options_value['default'] )) ? implode( '|', $options_value['default'] ) : $options_value['default'];
					$shortcode_options_string[] = ' ' . $options_value['id'] . '=' . '"' . $default_value . '"';
				}
			}
		}
		$shortcode_tag = '';
		$shortcode_tag .= '[';
		$shortcode_tag .= $shortcode_details['info']['tag'];
		$shortcode_tag .= implode( '', $shortcode_options_string );
		$shortcode_tag .= ']';
		if( ! $shortcode_details['info']['single'] ) {
			$shortcode_tag .= $content;
			$shortcode_tag .= '[/' . $shortcode_details['info']['tag'] . ']';
		}

		return $shortcode_tag;
	}

	/**
	 * @param int $length
	 * @param int $split_length
	 *
	 * @return string
	 */
	public function generateLicenseKey( $length = 32, $split_length = 8 ) {
		return implode( '-', str_split( substr( strtoupper( md5( time() . rand( 1000, 9999 ) ) ), 0, $length ), $split_length ) );
	}

	/**
	 * @param bool   $trim_query_string
	 * @param bool   $add_query_separator
	 * @param string $additional_query_params
	 *
	 * @return string
	 */
	public function currentUrl( $trim_query_string = false, $add_query_separator = false, $additional_query_params = '' ) {
		$pageURL = (isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on') ? "https://" : "http://";
		$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		if( ! $trim_query_string ) {
			$return_url = $pageURL;
		} else {
			$url = explode( '?', $pageURL );
			$return_url = $url[0];
		}
		if( $additional_query_params != '' ) {
			$return_url = $this->queryString( $return_url );
			$return_url = $return_url . $additional_query_params;
		}

		$return_url = str_replace( site_url(), $this->site_url, $return_url );

		return ($add_query_separator) ? $this->queryString( $return_url ) : $return_url;
	}

	/**
	 * @param null $var
	 *
	 * @return mixed
	 */
	public function getServerInfo( $var = null ) {
		return (is_null( $var )) ? $_SERVER : $_SERVER[ $var ];
	}

	/**
	 * @return bool|mixed
	 */
	public function getIPAddress() {
		if( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			// They are using a proxy, so we grab the forwarded for IP
			$theirIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif( isset( $_SERVER['REMOTE_ADDR'] ) ) {
			// No proxy, grab the normal IP
			$theirIP = $_SERVER['REMOTE_ADDR'];
		} else {
			// No IP (unlikely).
			$theirIP = false;
		}

		return $theirIP;
	}

	/**
	 * @return string
	 */
	public function httpUserAgent() {
		$agent = strtolower( $_SERVER['HTTP_USER_AGENT'] );
		if( stristr( $agent, 'msie' ) ) {
			return 'msie';
		} elseif( stristr( $agent, 'firefox' ) ) {
			return 'firefox';
		} elseif( stristr( $agent, 'chrome' ) ) {
			return 'chrome';
		} elseif( stristr( $agent, 'safari' ) ) {
			return 'safari';
		} elseif( stristr( $agent, 'opera' ) ) {
			return 'opera';
		} else {
			return 'unknown';
		}
	}

	/**
	 * @param $url
	 * @param $data
	 *
	 * @return bool|string
	 */
	public function cUrl( $url, $data ) {
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		$server_output = curl_exec( $ch );
		curl_close( $ch );

		return $server_output;
	}

	/**
	 * @param      $name
	 * @param      $type
	 * @param      $content
	 * @param null $title
	 * @param bool $dismissable
	 * @param int  $dismiss_duration
	 *
	 * @return bool
	 */
	public function adminNotice( $name, $type, $content, $title = null, $dismissable = true, $dismiss_duration = 86400 ) {
		if( ! is_admin() ) {
			return false;
		}

		if( isset( $_COOKIE[ 'cj-dismiss-notice-' . $name ] ) && $_COOKIE[ 'cj-dismiss-notice-' . $name ] == 1 ) {
			return false;
		}

		$current_user = wp_get_current_user();
		if( ! current_user_can( 'manage_options' ) ) {
			return false;
		}
		$dismissed_notices = [];
		if( $current_user->ID > 0 ) {
			$dismissed_notices = get_user_meta( $current_user->ID, 'cjwpbldr_dismissed_notices', true );
			if( ! is_array( $dismissed_notices ) ) {
				$dismissed_notices = [];
			}
		}
		if( isset( $dismissed_notices[ 'notice-' . $name ] ) ) {
			return false;
		}

		$type = str_replace( 'cj-', '', $type );

		$type_fix = [
			'primary' => 'success',
			'danger' => 'error',
			'warning' => 'warning',
			'info' => 'info',
			'error' => 'error',
			'success' => 'success',
		];

		$display = [];
		$dismiss_class = '';
		$dismiss_duration_data = '';
		if( $dismissable ) {
			$dismiss_class = ' is-dismissible cj-is-dismissible';
			$dismiss_duration_data = 'data-dismiss-duration="' . $dismiss_duration . '"';
		}

		$notice_class = (isset( $type_fix[ $type ] )) ? $type_fix[ $type ] : 'info';

		$display[] = '<div id="notice-' . $name . '" ' . $dismiss_duration_data . ' class="cssjockey-ui cjwpbldr-notice notice notice-' . $notice_class . $dismiss_class . ' ">';
		$display[] = '<div class="cj-pt-10 cj-pb-10">';
		$display[] = ( ! is_null( $title )) ? '<div class="cj-text-bold cj-mb-5 cj-fs-18"><b>' . $title . '</b></div>' : '';
		$display[] = $content;
		$display[] = '</div>';
		$display[] = '</div>';

		echo implode( '', $display );

		return false;
	}

	/**
	 * @param string $var
	 * @param bool   $options
	 *
	 * @return array
	 */
	public function installedUiBlocks( $var = 'class', $options = true ) {
		$return = [];
		$classes = get_declared_classes();
		if( is_array( $classes ) && ! empty( $classes ) ) {
			foreach( $classes as $key => $class_name ) {
				if( $this->containString( 'cjwpbldr_uib_', $class_name ) && ! $this->containString( '_metabox', $class_name ) ) {
					$class_instance = $class_name::getInstance();
					$class_info = $class_instance->info;
					$array_key = ($var == 'class') ? $class_name : basename( $class_info['path'] );
					$class_info['uib_id'] = basename( $class_info['path'] );
					$return[ $array_key ] = $class_info;
					if( ! $options ) {
						unset( $return[ $array_key ]['options'] );
					}
				}
			}
		}

		return $return;
	}

	/**
	 * @param string $type
	 * @param bool   $with_keys
	 * @param bool   $include_framework
	 *
	 * @return array
	 */
	public function installedProducts( $type = 'all', $with_keys = false, $include_framework = false ) {
		$installed_addons = $this->installedAddons();
		$installed_blocks = $this->installedUiBlocks();
		$return = [];
		$return['framework'] = [];
		$return['addons'] = [];
		$return['blocks'] = [];

		$installed_products_with_images = get_transient( '_cjwpbldr_installed_products' );

		// framework
		$framework_info = $this->config( 'plugin-info' );
		$return['framework']['wp-builder']['type'] = 'framework';
		$return['framework']['wp-builder']['last_updated'] = (isset( $framework_info['last_updated'] )) ? $framework_info['last_updated'] : date( 'M d, Y' );
		$return['framework']['wp-builder']['item_id'] = 'wp-builder';
		$return['framework']['wp-builder']['envato_id'] = '';
		$return['framework']['wp-builder']['id'] = 'wp-builder';
		$return['framework']['wp-builder']['slug'] = 'wp-builder';
		$return['framework']['wp-builder']['group'] = 'framework';
		$return['framework']['wp-builder']['path'] = str_replace( ABSPATH, '/', $this->root_dir );
		$return['framework']['wp-builder']['name'] = $framework_info['name'];
		$return['framework']['wp-builder']['image'] = (isset( $framework_info['screenshot'] )) ? $framework_info['screenshot'] : $this->default_post_featured_image_url;
		$return['framework']['wp-builder']['version'] = $framework_info['version'];
		$return['framework']['wp-builder']['description'] = $framework_info['description'];
		$return['framework']['wp-builder']['docs_url'] = $framework_info['docs_url'];
		$return['framework']['wp-builder']['demo_url'] = (isset( $framework_info['demo_url'] )) ? $framework_info['demo_url'] : '';
		$return['framework']['wp-builder']['envato_id'] = (isset( $framework_info['envato_id'] )) ? $framework_info['envato_id'] : '';
		$return['framework']['wp-builder']['envato_url'] = (isset( $framework_info['envato_url'] )) ? $framework_info['envato_url'] : '';
		$return['framework']['wp-builder']['dependencies'] = '';
		$l_key = get_transient( '_wp_builder_pro_key' );
		$return['framework']['wp-builder']['pas'] = ($l_key) ? 1 : 0;
		if( $with_keys ) {
			$return['framework']['wp-builder']['license_key'] = ($l_key) ? $l_key : '';
		}
		if( ! $include_framework ) {
			$return['framework'] = [];
		}

		if( ! empty( $installed_addons ) ) {
			foreach( $installed_addons as $key => $value ) {
				$return['addons'][ $value['addon_id'] ]['type'] = 'addon';
				$return['addons'][ $value['addon_id'] ]['last_updated'] = (isset( $value['last_updated'] )) ? $value['last_updated'] : date( 'M d, Y' );
				$return['addons'][ $value['addon_id'] ]['item_id'] = $value['item_id'];
				$return['addons'][ $value['addon_id'] ]['envato_id'] = $value['envato_id'];
				$return['addons'][ $value['addon_id'] ]['id'] = $value['addon_id'];
				$return['addons'][ $value['addon_id'] ]['slug'] = $value['addon_id'];
				$return['addons'][ $value['addon_id'] ]['group'] = 'addon';
				$return['addons'][ $value['addon_id'] ]['path'] = str_replace( ABSPATH, '/', $value['addon_path'] );
				$return['addons'][ $value['addon_id'] ]['name'] = $value['addon_name'];
				$return['addons'][ $value['addon_id'] ]['image'] = (isset( $value['screenshot'] )) ? $value['screenshot'] : $this->default_post_featured_image_url;
				$return['addons'][ $value['addon_id'] ]['version'] = $value['addon_version'];
				$return['addons'][ $value['addon_id'] ]['description'] = $value['addon_description'];
				$return['addons'][ $value['addon_id'] ]['docs_url'] = $value['docs_url'];
				$return['addons'][ $value['addon_id'] ]['demo_url'] = (isset( $value['demo_url'] )) ? $value['demo_url'] : '';
				$return['addons'][ $value['addon_id'] ]['envato_id'] = (isset( $value['envato_id'] )) ? $value['envato_id'] : '';
				$return['addons'][ $value['addon_id'] ]['envato_url'] = (isset( $value['envato_url'] )) ? $value['envato_url'] : '';
				$return['addons'][ $value['addon_id'] ]['dependencies'] = $value['dependencies'];
				$l_key = get_transient( '_wp_builder_pro_key' );
				$return['addons'][ $value['addon_id'] ]['pas'] = ($l_key) ? 1 : 0;
				if( $with_keys ) {
					$return['addons'][ $value['addon_id'] ]['license_key'] = ($l_key) ? $l_key : '';
				}
			}
		}

		if( ! empty( $installed_blocks ) ) {
			foreach( $installed_blocks as $key => $value ) {
				$return['blocks'][ $value['uib_id'] ]['type'] = 'block';
				$return['blocks'][ $value['uib_id'] ]['item_id'] = (isset( $value['item_id'] )) ? $value['item_id'] : 'not-required';
				$return['blocks'][ $value['uib_id'] ]['last_updated'] = (isset( $value['last_updated'] )) ? $value['last_updated'] : date( 'M d, Y' );
				$return['blocks'][ $value['uib_id'] ]['envato_id'] = (isset( $value['envato_id'] )) ? $value['envato_id'] : 'not-required';
				$return['blocks'][ $value['uib_id'] ]['id'] = $value['uib_id'];
				$return['blocks'][ $value['uib_id'] ]['slug'] = $value['uib_id'];
				$return['blocks'][ $value['uib_id'] ]['group'] = $value['group'];
				$return['blocks'][ $value['uib_id'] ]['path'] = str_replace( ABSPATH, '/', $value['path'] );
				$return['blocks'][ $value['uib_id'] ]['name'] = $value['name'];
				$return['blocks'][ $value['uib_id'] ]['image'] = $value['screenshot'];
				$return['blocks'][ $value['uib_id'] ]['version'] = $value['version'];
				$return['blocks'][ $value['uib_id'] ]['description'] = $value['description'];
				$return['blocks'][ $value['uib_id'] ]['docs_url'] = (isset( $value['docs_url'] )) ? $value['docs_url'] : '';
				$return['blocks'][ $value['uib_id'] ]['demo_url'] = (isset( $value['demo_url'] )) ? $value['demo_url'] : '';
				$return['blocks'][ $value['uib_id'] ]['pro_version_url'] = 'not-required';
				$return['blocks'][ $value['uib_id'] ]['envato_id'] = (isset( $value['envato_id'] )) ? $value['envato_id'] : '';
				$return['blocks'][ $value['uib_id'] ]['envato_url'] = (isset( $value['envato_url'] )) ? $value['envato_url'] : '';
				$return['blocks'][ $value['uib_id'] ]['dependencies'] = (isset( $value['dependencies'] )) ? $value['dependencies'] : 'not-required';
				$l_key = get_transient( '_wp_builder_pro_key' );
				$return['blocks'][ $value['uib_id'] ]['pas'] = ($l_key) ? 1 : 0;
				if( $with_keys ) {
					$return['blocks'][ $value['uib_id'] ]['license_key'] = $l_key;
				}
			}
		}

		if( $type == 'all' && $with_keys ) {
			return array_merge( $return['framework'], $return['addons'], $return['blocks'] );
		}

		if( $type == 'slugs' ) {
			$all_products = array_merge( array_values( $return['framework'] ), array_values( $return['addons'] ), array_values( $return['blocks'] ) );
			$return = [];
			foreach( $all_products as $key => $value ) {
				$return[] = $value['slug'];
			}

			return $return;
		}

		if( $type == 'all' ) {
			return array_merge( array_values( $return['framework'] ), array_values( $return['addons'] ), array_values( $return['blocks'] ) );
		}
		if( $type == 'addons' ) {
			return array_values( $return['addons'] );
		}
		if( $type == 'blocks' ) {
			return array_values( $return['blocks'] );
		}

		return $return;
	}

	/**
	 * @param $slug
	 *
	 * @return array|mixed
	 */
	public function installedProduct( $slug ) {
		$installed_products = $this->installedProducts();
		if( is_array( $installed_products ) && ! empty( $installed_products ) ) {
			foreach( $installed_products as $index => $product ) {
				if( $product['slug'] == $slug ) {
					return $product;
				}
			}
		}

		return [];
	}

	/**
	 * @return array|mixed
	 */
	public function upgradeableProducts() {
		$return = [];

		$products = get_transient( '_cj_upgradable_products' );
		if( $products ) {
			$return = $products;
		}

		return $return;
	}

	/**
	 * @return mixed
	 */
	public function checkProductUpgrades() {
		delete_transient( '_cj_upgradable_products' );
		delete_transient( '_cjwpbldr_ping' );
		$url = rest_url( 'cjwpbldr' ) . '/core/products/check/upgrades';

		return $this->wpRemotePost( $url );
	}

	/**
	 * @return bool
	 */
	public function upgradeFramework() {
		$imports_path = $this->cjwpbldr_content_path . '/imports';
		$imports_installer_file_path = $this->cjwpbldr_content_path . '/imports/wp-builder-installer.zip';
		if( file_exists( $imports_installer_file_path ) ) {
			$wpbuilder_folder_path = $imports_path . '/wp-builder';
			if( ! is_dir( $wpbuilder_folder_path ) ) {
				$this->createDirectory( $wpbuilder_folder_path );
			}
			$this->unzipFile( $imports_installer_file_path, $wpbuilder_folder_path );
			// theme
			if( $this->isTheme() ) {
				$parent_theme_path = $wpbuilder_folder_path . '/theme/wp-builder.zip';
				$child_theme_path = $wpbuilder_folder_path . '/theme/wp-builder-child.zip';
				if( file_exists( $parent_theme_path ) ) {
					$this->unzipFile( $parent_theme_path, WP_CONTENT_DIR . '/themes/wp-builder' );
				}
				if( file_exists( $child_theme_path ) ) {
					$this->unzipFile( $child_theme_path, WP_CONTENT_DIR . '/themes/wp-builder-child' );
				}
			} else {
				$plugin_path = $wpbuilder_folder_path . '/plugin/wp-builder.zip';
				$this->unzipFile( $plugin_path, WP_PLUGIN_DIR . '/wp-builder' );
			}

			$this->deleteDirectory( $wpbuilder_folder_path );
			sleep( 0.5 );
			$this->deleteFile( $imports_installer_file_path );
		}

		return true;
	}

	/**
	 * @return bool
	 */
	public function installTemplates() {
		$templates_path = $this->cjwpbldr_content_path . '/templates';
		$ui_blocks_path = $this->cjwpbldr_content_path . '/blocks';
		$dirs = preg_grep( '/^([^.])/', scandir( $templates_path ) );
		if( is_array( $dirs ) && ! empty( $dirs ) ) {
			foreach( $dirs as $key => $dir ) {
				$install_template_folder_path = $templates_path . '/' . $dir;
				if( is_dir( $install_template_folder_path ) ) {
					$template_json_file = $install_template_folder_path . '/wpb-template.json';
					if( file_exists( $template_json_file ) ) {
						$json_data = $this->getFileContents( $template_json_file );
						$json_data = json_decode( $json_data, 1 );
						$template_blocks_dir = dirname( $template_json_file );
						$block_dirs = preg_grep( '/^([^.])/', scandir( $template_blocks_dir ) );
						if( is_array( $block_dirs ) && ! empty( $block_dirs ) ) {
							foreach( $block_dirs as $block_key => $block_file ) {
								$block_file_path = $template_blocks_dir . '/' . $block_file;
								if( $this->containString( 'uib', $block_file_path ) && $this->containString( 'zip', $block_file_path ) ) {
									$block_destination_path = $ui_blocks_path . '/' . $block_file;
									$block_destination_path = str_replace( '.zip', '', $block_destination_path );
									$this->unzipFile( $block_file_path, $block_destination_path );
									$block_init_file = $block_destination_path . '/init.php';
									if( file_exists( $block_init_file ) ) {
										require_once $block_init_file;
									}
								}
							}
							$this->createUiBlocks();
							sleep( 2 );
						}

						//$template_title = ucwords( trim( str_replace( ['.json', '-'], ' ', $json_data['file_name'] ) ) );
						$template_title = $json_data['template_name'];
						$template_title = $this->uniqueColumnValue( $template_title, 'posts', 'post_title', ' - ' );

						$template_ui_blocks = [];

						if( is_array( $json_data['template_blocks'] ) && ! empty( $json_data['template_blocks'] ) ) {
							foreach( $json_data['template_blocks'] as $template_block_key => $template_block_value ) {

								$template_block_post = $this->getPostByMetaKey( '_ui_block_class_name', $template_block_value['class_name'] );
								if( ! empty( $template_block_post ) ) {
									$template_block_post = $this->cloneUiBlock( $template_block_post['ID'] );
								}
								$template_block_post['post_title'] = str_replace( ' - Copy', '', $template_block_post['post_title'] );
								$updated_title['post_title'] = $template_block_post['post_title'] . ' - ' . $template_title;
								$this->updatePostInfo( $template_block_post['ID'], $updated_title );
								if( isset( $template_block_value['settings'] ) && ! empty( $template_block_value['settings'] ) ) {
									foreach( $template_block_value['settings'] as $block_settings_key => $block_settings_value ) {
										$block_settings_option_name = '_block_settings_' . $block_settings_key;
										$block_settings_option_value = $block_settings_value;
										update_post_meta( $template_block_post['ID'], $block_settings_option_name, $block_settings_option_value );
										update_post_meta( $template_block_post['ID'], $block_settings_option_name . '_preview', $block_settings_option_value );
									}
								}
								if( class_exists( $template_block_value['class_name'] ) ) {
									$block_class_instance = $template_block_value['class_name']::getInstance();
									$block_class_info = $block_class_instance->info();
									unset( $block_class_info['path'] );
									unset( $block_class_info['options'] );
									$template_ui_blocks[ $template_block_key ] = [
										'ID' => $template_block_post['ID'],
										'post_name' => $template_block_post['post_name'],
										'post_title' => $template_block_post['post_title'],
										'post_content' => $template_block_post['post_content'],
										'_core_ui_block' => $template_block_post['_core_ui_block'],
										'info' => $block_class_info,
									];
								}
							}
						}
						$new_template_post_info = [
							'post_title' => $template_title . ' - NEW',
							'post_content' => 'imported from - ' . basename( $template_json_file ),
							'post_type' => 'cjwpbldr-templates',
							'post_status' => 'publish',
							'_cj_imported' => 1,
							'_cjwpbldr_ui_blocks_preview' => $template_ui_blocks,
							'_cjwpbldr_ui_blocks' => $template_ui_blocks,
							'_cj_imported_file_name' => basename( $template_json_file ),
						];
						$this->createPost( $new_template_post_info );
						if( is_dir( $install_template_folder_path ) ) {
							$this->deleteDirectory( $install_template_folder_path );
						}
					}
				}
			}
		}

		return true;
	}

	/**
	 * @return bool
	 */
	public function installThemes() {
		$imports_path = $this->cjwpbldr_content_path . '/imports/';
		$blocks_path = $this->cjwpbldr_content_path . '/blocks/';
		$dirs = preg_grep( '/^([^.])/', scandir( $imports_path ) );

		if( ! empty( $dirs ) ) {
			foreach( $dirs as $key => $dir ) {
				$imports_file_path = $imports_path . $dir;
				if( ! is_dir( $imports_file_path ) && ! $this->containString( '.php', $dir ) ) {
					$imports_file_info = pathinfo( $imports_file_path );
					if( $imports_file_info['extension'] == 'zip' ) {
						$unzip_path = str_replace( '.zip', '/', $imports_file_path );
						$this->unzipFile( $imports_file_path, $unzip_path );
						if( ! file_exists( $unzip_path . 'cjwpbldr-theme.json' ) ) {
							$this->deleteDirectory( $unzip_path );
							$this->deleteFile( $imports_file_path );

							return false;
						}
						if( file_exists( $unzip_path . 'cjwpbldr-theme.json' ) ) {
							$theme_dirs = preg_grep( '/^([^.])/', scandir( $unzip_path ) );
							if( is_array( $theme_dirs ) && ! empty( $theme_dirs ) ) {
								foreach( $theme_dirs as $theme_dir_key => $theme_dir ) {
									$block_path = $unzip_path . $theme_dir;
									if( $this->containString( 'uib-', $theme_dir ) ) {
										$this->copyDirectory( $block_path, $blocks_path . $theme_dir );
									}
								}
							}
						}

						$cjwpbldr_builder_ui_blocks = cjwpbldr_builder_ui_blocks::getInstance();
						$cjwpbldr_builder_ui_blocks->loadUiBlocks();
						$this->createUiBlocks();
						sleep( 1 );
						$theme_json = $unzip_path . 'cjwpbldr-theme.json';
						ob_start();
						require_once $theme_json;
						$theme_json = json_decode( ob_get_clean(), 1 );
						// create templates
						if( is_array( $theme_json['templates'] ) && ! empty( $theme_json['templates'] ) ) {
							foreach( $theme_json['templates'] as $template_key => $template_data ) {
								$new_template_post_data = $template_data;
								unset( $new_template_post_data['blocks'] );
								$new_template_post_data['post_type'] = 'cjwpbldr-templates';
								$new_template_post_data['post_content'] = $new_template_post_data['post_name'];
								$new_template_post_data['post_status'] = 'publish';
								$new_template_post_data['cjwpbldr-theme'] = $theme_json['site_slug'];
								// get and assign blocks
								$new_template_blocks = [];
								if( isset( $template_data['blocks'] ) && ! empty( $template_data['blocks'] ) ) {
									foreach( $template_data['blocks'] as $template_blocks_data_key => $template_blocks_data ) {
										$template_block_info = $this->getPostByMetaKey( '_ui_block_class_name', $template_blocks_data['_ui_block_class_name'], 'data' );
										if( class_exists( $template_blocks_data['_ui_block_class_name'] ) ) {
											$block_class = $template_blocks_data['_ui_block_class_name']::getInstance();
											$block_class_info = $block_class->info;
											unset( $block_class_info['options'] );
											unset( $block_class_info['path'] );
											unset( $block_class_info['version'] );
											$new_template_blocks[ $template_blocks_data_key ]['ID'] = $template_block_info['ID'];
											$new_template_blocks[ $template_blocks_data_key ]['post_name'] = $template_block_info['post_name'];
											$new_template_blocks[ $template_blocks_data_key ]['post_title'] = $template_block_info['post_title'];
											$new_template_blocks[ $template_blocks_data_key ]['post_content'] = $template_block_info['post_content'];
											$new_template_blocks[ $template_blocks_data_key ]['_core_ui_block'] = $template_block_info['_core_ui_block'];
											$new_template_blocks[ $template_blocks_data_key ]['info'] = $block_class_info;
											$this->updatePostInfo( $template_block_info['ID'], $template_blocks_data['settings'] );
										}
									}
								}
								$new_template_post_data['_cjwpbldr_ui_blocks'] = $new_template_blocks;
								$new_template_post_data['_cjwpbldr_ui_blocks_preview'] = $new_template_blocks;
								$check_existing = $this->dbGet( 'posts', '*', [
									'post_name' => $new_template_post_data['post_name'],
									'post_status' => 'publish',
								] );
								if( ! $check_existing ) {
									$this->createPost( $new_template_post_data );
								} else {
									$this->updatePostInfo( $check_existing['ID'], $new_template_post_data );
								}
							}
						}

						if( isset( $theme_json['site_content'] ) && ! empty( $theme_json['site_content'] ) ) {
							$site_content = $theme_json['site_content'];
							foreach( $site_content as $post_type => $posts ) {
								foreach( $posts as $post_key => $post_data ) {
									$check_existing = $this->dbGet( 'posts', 'ID', ['post_name' => $post_data['post_name']] );
									if( ! $check_existing ) {
										$post_data['post_type'] = $post_type;
										$post_data['post_status'] = 'publish';
										$post_data['cjwpbldr-theme'] = $theme_json['site_slug'];
										if( isset( $post_data['taxonomies'] ) && ! empty( $post_data['taxonomies'] ) ) {
											foreach( $post_data['taxonomies'] as $post_tax_key => $post_taxonomies ) {
												foreach( $post_taxonomies as $tax_key => $post_taxonomy ) {
													$post_data['set_post_terms'][ $post_taxonomy['term_data']['taxonomy'] ] = $post_taxonomy['term_name'];
												}
											}
										}
										unset( $post_data['taxonomies'] );

										// find and assign template
										if( isset( $post_data['_cjwpbldr_use_template_singular'] ) && $post_data['_cjwpbldr_use_template_singular'] != '' ) {
											$template = $this->dbGet( 'postmeta', '*', [
												'meta_key' => 'template_export_id',
												'meta_value' => $post_data['_cjwpbldr_use_template_singular'],
											] );
											if( $template ) {
												$post_data['_cjwpbldr_use_template_singular'] = $template['post_id'];
											}
										}

										$new_post_data = $this->createPost( $post_data );
										if( isset( $post_data['featured_image'] ) && $post_data['featured_image'] != '' ) {
											$name = basename( $post_data['featured_image'] );
											$this->setPostFeaturedImageFromUrl( $new_post_data['ID'], $post_data['featured_image'], $name, true );
										}
									}
								}
							}
						}
						$this->deleteDirectory( $unzip_path );
						$this->deleteFile( $imports_file_path );
					}
				}
			}
		}

		return true;
	}

	/**
	 * @param $download_url
	 *
	 * @return array|bool
	 */
	public function downloadAndInstallProduct( $download_url ) {

		$destination_path = $this->cjwpbldr_content_path;
		$file_name = $this->removeQueryStringVar( $download_url, 'k' );
		$file_name = explode( '=', $file_name )[1];

		if( $this->containString( 'addon-', $file_name ) ) {
			$addon_info = $this->addonInfo( null, $file_name );
			if( is_array( $addon_info ) && isset( $addon_info['addon_path'] ) ) {
				$destination_path = trailingslashit( dirname( $addon_info['addon_path'] ) );
			} else {
				$destination_path = $this->cjwpbldr_content_path . '/addons/';
			}
			if( ! is_dir( $destination_path ) ) {
				$this->createDirectory( $destination_path );
			}

			$file_path = "{$destination_path}{$file_name}.zip";
			$zip_file_content = $this->wpRemoteGet( $download_url, '', 'body' );

			if( isset( $zip_file_content['errors'] ) ) {
				return $zip_file_content;
			}

			$this->putFileContents( $file_path, $zip_file_content );
			$this->unzipFile( $file_path, $destination_path );
			$this->deleteFile( $file_path );

			if( is_dir( $destination_path . $file_name ) ) {
				$return['success'] = 'installed';

				return $return;
			} else {
				$return['errors'] = 'Could not install, please check your server error logs or contact our support team for help.';

				return $return;
			}
		}

		if( $this->containString( 'uib-', $file_name ) ) {
			$destination_path = $this->cjwpbldr_content_path . '/blocks/' . $file_name;
			$file_path = "{$this->cjwpbldr_content_path}/blocks/{$file_name}.zip";

			$zip_file_content = $this->wpRemoteGet( $download_url, '', 'body' );
			$this->putFileContents( $file_path, $zip_file_content );
			$this->unzipFile( $file_path, $destination_path );
			$this->deleteFile( $file_path );

			if( class_exists( 'cjwpbldr_builder_ui_blocks' ) ) {
				$cjwpbldr_builder_ui_blocks = cjwpbldr_builder_ui_blocks::getInstance();
				$cjwpbldr_builder_ui_blocks->loadUiBlocks();
				$cjwpbldr_builder_ui_blocks->createUiBlocks();
			}

			$return = ['message' => __( 'Installation Successful.', 'wp-builder-locale' )];

			return $return;
		}

		if( $this->containString( 'wpb-template-', $file_name ) ) {
			$destination_path = $this->cjwpbldr_content_path . '/templates/' . $file_name;
			$file_path = "{$this->cjwpbldr_content_path}/templates/{$file_name}.zip";

			$zip_file_content = $this->wpRemoteGet( $download_url, '', 'body' );
			$this->putFileContents( $file_path, $zip_file_content );
			$this->unzipFile( $file_path, $destination_path );
			$this->deleteFile( $file_path );

			if( file_exists( $destination_path . '/wpb-template.json' ) ) {
				sleep( 1 );
				$this->installTemplates();
				$this->checkProductUpgrades();
			} else {
				$this->deleteDirectory( $destination_path );
			}
			$template_url = admin_url( 'edit.php?post_type=cjwpbldr-templates' );
			$return = ['redirect' => $template_url];

			return $return;
		}

		if( $this->containString( '-theme', $file_name ) ) {
			$theme_folder_name = $file_name;
			$theme_folder_name_path = $this->cjwpbldr_content_path . '/imports/';
			$zip_file_content = $this->wpRemoteGet( $download_url, '', 'body' );

			$file_path = $theme_folder_name_path . "/{$file_name}.zip";
			$this->putFileContents( $file_path, $zip_file_content );
			$this->unzipFile( $file_path, $theme_folder_name_path );
			ob_start();
			require_once sprintf( '%s/cjwpbldr-theme.json', $theme_folder_name_path );
			$json_data = ob_get_clean();
			delete_transient( 'cjwpbldr_install_theme' );
			set_transient( 'cjwpbldr_install_theme', json_decode( $json_data, true ), 84600 );
			//$this->deleteDirectory( $theme_folder_name_path );
			$this->deleteFile( $theme_folder_name_path . 'cjwpbldr-theme.json' );
			$this->deleteFile( $file_path );
			$return = ['redirect' => $this->callbackUrl( 'core-install-themes' )];

			return $return;
		}
	}

	/**
	 * @param $product_slug
	 *
	 * @return array|bool
	 */
	public function removeProduct( $product_slug ) {
		$return = false;
		$installed_products = $this->installedProducts( 'all', true );

		if( isset( $installed_products[ $product_slug ] ) ) {
			$product_details = $installed_products[ $product_slug ];
			if( is_array( $product_details ) && ! empty( $product_details ) ) {
				// remove license key for the product
				delete_transient( '_' . $product_details['slug'] . '_key' );
				// remove blocks if uib
				if( $this->containString( 'uib-', $product_details['slug'] ) ) {
					$posts = $this->getPostsByMetaKey( '_product_slug', $product_details['slug'], 'data' );
					if( ! empty( $posts ) ) {
						foreach( $posts as $post ) {
							wp_delete_post( $post['ID'], true );
						}
					}
				}
				// deactivate plugins if addon
				if( $this->containString( 'addon-', $product_details['slug'] ) ) {
					$active_plugins = get_option( 'active_plugins' );
					foreach( $active_plugins as $plugin_index => $plugin_name ) {
						if( $this->containString( $product_details['slug'], $plugin_name ) ) {
							$error_msg = sprintf( __( '%s is installed in the plugins directory, please deactivate and uninstall it form the <a href="%s" target="_blank"><b>Plugins</b></a> page.', 'lang-textdomain' ), $product_details['name'], admin_url( 'plugins.php' ) );

							return ['error' => $error_msg];
						}
					}
				}
				// delete directory
				$dir_path = ABSPATH . $product_details['path'];
				$dir_path = str_replace( '//', '/', $dir_path );
				if( is_dir( $dir_path ) ) {
					$deleted = $this->deleteDirectory( $dir_path );

					return $deleted;
				}

				return ['error' => 'Something went wrong, please refresh this page and try again.'];
			}
		}

		return $return;
	}

	/**
	 * @param $add_content
	 */
	public function addContentToWpFooter( $add_content ) {
		add_filter( 'cjwpbldr_wp_footer_filter', function ( $content ) use ( $add_content ) {
			$content[] = $add_content;

			return $content;
		} );
	}

	/**
	 * @return array
	 */
	public function activeAddons() {
		$activated_plugins = $this->getActivePlugins();
		$installed_plugins = [];
		if( is_array( $activated_plugins ) ) {
			foreach( $activated_plugins as $key => $plugin ) {
				$plugin = explode( '/', $plugin );
				$plugin = $plugin[0];
				$installed_plugins[ $plugin ] = $plugin;
			}
		}

		// in-built modules
		$installed_inbuilt_modules = [];
		$modules_path = $this->root_dir . '/modules/';
		if( is_dir( $modules_path ) ) {
			$module_dirs = preg_grep( '/^([^.])/', scandir( $modules_path ) );
			if( is_array( $module_dirs ) && ! empty( $module_dirs ) ) {
				foreach( $module_dirs as $key => $module_dir ) {
					$module_path = $modules_path . $module_dir;
					if( is_dir( $module_path ) ) {
						$installed_inbuilt_modules[ $module_dir ] = $module_dir;
					}
				}
			}
		}

		return array_merge( $installed_inbuilt_modules, $installed_plugins );
	}

	/**
	 * @param       $data
	 * @param array $store
	 *
	 * @return false|mixed|string|void
	 */
	public function jsonData( $data, $store = [] ) {
		$output = [
			'success' => 1,
			'data' => $data,
			'store' => $store,
		];

		return json_encode( $output );
	}

	/**
	 * @param $slug
	 *
	 * @return mixed
	 */
	public function getProductLicenseKey( $slug ) {
		return get_transient( '_license_key_' . $slug );
	}

	/**
	 * @param string $file_name
	 * @param array  $settings
	 * @param bool   $show_vars
	 *
	 * @return false|string
	 */
	public function vueFormHtml( $file_name = '', $settings = [], $show_vars = false ) {
		$html_path = trailingslashit( $this->framework_dir ) . 'html/forms/vue/';
		$file_path = $html_path . $file_name . '.php';
		if( ! file_exists( $file_path ) ) {
			return sprintf( __( 'File (%s) not found.', 'wp-builder-locale' ), $file_name );
		}
		ob_start();
		include $file_path;

		return ob_get_clean();
	}

	/**
	 * @param string $file_name
	 * @param array  $settings
	 * @param bool   $show_vars
	 * @param string $option_prefix
	 *
	 * @return false|string
	 */
	public function jqueryFormHtml( $file_name = '', $settings = [], $show_vars = false, $option_prefix = '' ) {
		$html_path = trailingslashit( $this->framework_dir ) . 'html/forms/jquery/';
		$file_path = $html_path . $file_name . '.php';
		if( ! file_exists( $file_path ) ) {
			return sprintf( __( 'File (%s) not found.', 'wp-builder-locale' ), $file_name );
		}
		ob_start();
		include $file_path;

		return ob_get_clean();
	}

	/**
	 * @param string $brand_color
	 * @param bool   $invert
	 *
	 * @return string
	 */
	public function getColorHex( $brand_color = 'primary', $invert = false ) {
		$saved_variables = $this->saved_options['core_sass_variables'];
		$invert = ($invert) ? '-invert' : '';
		if( isset( $saved_variables[ $brand_color . $invert ] ) ) {
			return $saved_variables[ $brand_color . $invert ];
		}

		return '';
	}

	/**
	 * @param      $product
	 * @param bool $recreate_images
	 */
	public function createProductJson( $product, $recreate_images = false ) {
		$json_file_path = ABSPATH . $product['path'] . '/info.json';
		$json_file_path = str_replace( '//', '/', $json_file_path );
		// create screenshots
		$source_path = dirname( $json_file_path ) . '/screenshot.png';
		if( file_exists( $source_path ) && $recreate_images ) {
			$screenshot_path = dirname( $json_file_path ) . '/screenshot.jpg';
			$thumbnail_path = dirname( $json_file_path ) . '/thumbnail.jpg';
			$this->imageResize( $source_path, [1200, 1200], false, 70, $screenshot_path );
			$this->imageResize( $source_path, [300, 300], false, 70, $thumbnail_path );
		}
		$json_info = $product;
		unset( $json_info['path'] );
		unset( $json_info['image'] );
		$this->putFileContents( $json_file_path, json_encode( $json_info ) );

		$read_me_source = '';
		$read_me_dest = '';

		if( $product['type'] == 'framework' ) {
			$read_me_source = $this->root_dir . '/source/code-snippets/read-me/framework/read-me.htm';
			$read_me_dest = $this->root_dir . '/read-me.htm';
		}
		if( $product['type'] == 'addon' ) {
			$read_me_source = $this->root_dir . '/source/code-snippets/read-me/addons/read-me.htm';
			$read_me_dest = str_replace( '//', '/', ABSPATH . $product['path'] ) . '/read-me.htm';
		}
		if( $product['type'] == 'block' ) {
			$read_me_source = $this->root_dir . '/source/code-snippets/read-me/ui-blocks/read-me.htm';
			$read_me_dest = str_replace( '//', '/', ABSPATH . $product['path'] ) . '/read-me.htm';
		}
		if( file_exists( $read_me_source ) ) {
			$this->copyFile( $read_me_source, $read_me_dest );
		}
	}

	/**
	 * @param $feature
	 *
	 * @return array
	 */
	public function enableFrameworkFeature( $feature ) {
		$core_features = $this->saved_options['core_features'];
		$framework_features = [];
		foreach( $core_features as $key => $core_feature ) {
			$framework_features[ $core_feature ] = $core_feature;
		}
		$update_framework_features = array_merge( array_values( $framework_features ), [$feature] );
		$this->updateOption( 'core_features', $update_framework_features );

		return $update_framework_features;
	}

	public function getTemplateContent( $post_info, $template_post_info ) {
		$blocks = $template_post_info['_cjwpbldr_ui_blocks'];
		ob_start();
		foreach( $blocks as $key => $block_data ) {
			$class = $block_data['info']['class_name']::getInstance();
			$class_info = $class->info;
			$block_info = $this->postInfo( $block_data['ID'] );
			echo $this->renderUiBlock( $block_info, $class_info );
		}
		return ob_get_clean();
	}

}