<?php
if( ! class_exists( 'cjwpbldr_api_cloud' ) ) {
	class cjwpbldr_api_cloud {

		public $helpers, $addon_dir, $routes, $api_url;

		private static $instance;

		public static function getInstance() {
			if( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function __construct() {
			$this->helpers = cjwpbldr_helpers::getInstance();
			$this->api_url = rest_url( 'cjwpbldr' ) . '/';
			$this->routes = [
				'core-products-installed' => [
					'endpoint' => 'core/products/installed',
					'name' => __( 'Get installed products', 'wp-builder-locale' ),
					'methods' => [
						'get' => [$this, 'getInstalledProducts'], // callback function
						'post' => [$this, 'getInstalledProducts'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, 'manage_options' );
					},
				],
				'core-products-install' => [
					'endpoint' => 'core/products/install',
					'name' => __( 'Download and Extract products', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'downloadAndInstallProduct'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, 'manage_options' );
					},
				],
				'core-products-remove' => [
					'endpoint' => 'core/products/remove',
					'name' => __( 'Remove Product', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'removeInstalledProduct'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, 'manage_options' );
					},
				],
				'core-products-check-upgrades' => [
					'endpoint' => 'core/products/check/upgrades',
					'name' => __( 'Check Product Upgrades', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'checkProductUpgrades'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, 'manage_options' );
					},
				],
				'core-products-upgradable' => [
					'endpoint' => 'core/products/upgradable',
					'name' => __( 'Get upgradable products', 'wp-builder-locale' ),
					'methods' => [
						'get' => [$this, 'getUpgradableProducts'], // callback function
						'post' => [$this, 'getUpgradableProducts'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, 'manage_options' );
					},
				],
			];
			add_filter( 'cjwpbldr_register_api_route', [$this, 'registerRoute'] );
		}

		public function registerRoute( $routes ) {
			$routes = array_merge( $routes, $this->routes );

			return $routes;
		}

		public function getInstalledProducts( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );
			$with_keys = (isset( $post_data['with_keys'] )) ? true : false;
			$return = [];
			$installed_products = $this->helpers->installedProducts( 'all', $with_keys );
			if( ! empty( $installed_products ) ) {
				foreach( $installed_products as $key => $installed_product ) {
					$return[ $installed_product['slug'] ] = $installed_product;
				}
			}
			if( isset( $post_data['slug'] ) ) {
				$return = (isset( $return[ $post_data['slug'] ] )) ? $return[ $post_data['slug'] ] : [];
			}
			$data['success'] = $return;

			return $this->helpers->apiResponse( $request, $data );
		}

		public function downloadAndInstallProduct( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );
			if( ! isset( $post_data['download_url'] ) ) {
				return $this->helpers->apiError( $request, 406, __( 'You must specify download_url.', 'wp-builder-locale' ), 406, $post_data );
			}
			$this->helpers->installedProducts();
			$response = $this->helpers->downloadAndInstallProduct( $post_data['download_url'] );
			sleep( 1 );

			if( isset( $response['errors'] ) ) {
				$data = $response;

				return $this->helpers->apiResponse( $request, $data );
			}

			if( $response ) {
				$this->helpers->installedProducts();
				$data = $response;

				return $this->helpers->apiResponse( $request, $data );
			}
			$message = __( 'Could not install, please refresh this page to try again or contact our support team for help.', 'wp-builder-locale' );

			return $this->helpers->apiError( $request, 406, $message, 406, $post_data );
		}

		public function removeInstalledProduct( $request ) {

			$post_data = $this->helpers->apiRequestParams( $request );

			if( ! isset( $post_data['product_slug'] ) || isset( $post_data['product_slug'] ) && $post_data['product_slug'] == '' ) {
				$message = __( 'You must specify product_slug.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $message, 406, $post_data );
			}

			$installed_products = $this->helpers->installedProducts( 'all' );
			$installed_products_array = [];
			if( ! empty( $installed_products ) ) {
				foreach( $installed_products as $key => $product ) {
					$installed_products_array[] = $product['slug'];
				}
			}

			if( ! in_array( $post_data['product_slug'], $installed_products_array ) ) {
				$message = __( 'No product found with specified key.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $message, 406, $post_data );
			}
			$result = $this->helpers->removeProduct( $post_data['product_slug'] );
			if( is_array( $result ) && isset( $result['error'] ) ) {
				return $this->helpers->apiError( $request, 406, $result['error'], 406, $post_data );
			}

			$data['success'] = $installed_products;

			return $this->helpers->apiResponse( $request, $data );
		}

		public function checkProductUpgrades( $request ) {
			$return = [];
			$all_products = $this->helpers->installedProducts( 'all', true, true );
			$installed_products_data = [];
			$installed_products_chunks = array_chunk( $all_products, 10, false );
			$premium_license_key = get_transient( '_wp_builder_pro_key' );
			$check_upgrade_data = [];
			foreach( $installed_products_chunks as $chunk_key => $installed_products ) {
				if( is_array( $installed_products ) && ! empty( $installed_products ) ) {
					foreach( $installed_products as $key => $installed_product ) {
						if( $installed_product['item_id'] != 'NA' ) {
							$array_key = 'installed_products';
							$check_upgrade_data[ $array_key ][ $installed_product['slug'] ]['installed_version'] = $installed_product['version'];
							$check_upgrade_data[ $array_key ][ $installed_product['slug'] ]['license_key'] = $premium_license_key;
							$installed_products_data[ $installed_product['slug'] ] = $check_upgrade_data[ $array_key ][ $installed_product['slug'] ];
						}
					}
					$url = $this->helpers->config( 'plugin-info', 'cjwpbldr_api_url' ) . '/addon-wpbuilder-shop/cloud/check-updates';
					$response = $this->helpers->wpRemotePost( $url, $check_upgrade_data, ['no-auth' => true] );
					if( gettype( $response ) == 'string' ) {
						$response = json_decode( $response, 1 );
					}
					if( isset( $response['data'] ) && ! empty( $response['data'] ) ) {
						foreach( $response['data'] as $r_key => $r_value ) {
							$license_key = 0;
							if( $premium_license_key ) {
								$license_key = $premium_license_key;
							}
							if( $r_value['license_key'] !== '' ) {
								$license_key = $r_value['license_key'];
							}
							set_transient( '_' . $r_key . '_key', $license_key, 86400 );
							$return[ $r_key ] = $r_value;
							$installed_products_data[ $r_key ] = $r_value;
						}
					}
				}
			}

			if( $premium_license_key != '' ) {
				$url = $this->helpers->config( 'plugin-info', 'cjwpbldr_api_url' ) . '/addon-wpbuilder-shop/cloud/update-license-info';
				$this->helpers->wpRemotePost( $url, [
					'license_key' => $premium_license_key
				], ['no-auth' => true] );
			}

			set_transient( '_cjwpbldr_installed_products', $installed_products_data, 86400 );
			delete_transient( '_cj_upgradable_products' );
			if( ! empty( $return ) ) {
				$upgradeable_products = [];
				$installed_products = $this->helpers->installedProducts( 'all', true, true );
				foreach( $return as $key => $value ) {
					if( isset( $value['upgrade_available'] ) && version_compare( $value['upgrade_available'], $installed_products[ $key ]['version'], '>' ) ) {
						$upgradeable_products[ $key ] = $value;
					}
				}
				if( count( $upgradeable_products ) > 0 ) {
					set_transient( '_cj_upgradable_products', $upgradeable_products, 86400 );
				}
			}
			$data['success'] = $return;

			return $this->helpers->apiResponse( $request, $data );
		}

		public function getUpgradableProducts( $request ) {
			$return['success'] = $this->helpers->upgradeableProducts();

			return $this->helpers->apiResponse( $request, $return );
		}

	}

	cjwpbldr_api_cloud::getInstance();
}