<?php
if( ! class_exists( 'cjwpbldr_api_core_custom_data' ) ) {
	class cjwpbldr_api_core_custom_data {

		public $helpers, $addon_dir, $routes, $api_url;
		protected $custom_data_class;

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
			$this->custom_data_class = cjwpbldr_custom_data::getInstance();
			$this->routes = array(
				'core-custom-data' => array(
					'endpoint' => 'core/custom-data',
					'name' => __( 'Product Info', 'wp-builder-locale' ),
					'methods' => array(
						'get' => array($this, 'getCustomData'),
						'post' => array($this, 'getCustomData'),
					),
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, 'manage_options' );
					},
				),
				'core-custom-data-add' => array(
					'endpoint' => 'core/custom-data/add',
					'name' => __( 'Product Info', 'wp-builder-locale' ),
					'methods' => array(
						'post' => array($this, 'addCustomData'),
					),
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, '' );
					},
				),
				'core-custom-data-delete' => array(
					'endpoint' => 'core/custom-data/delete',
					'name' => __( 'Product Info', 'wp-builder-locale' ),
					'methods' => array(
						'post' => array($this, 'deleteCustomData'),
					),
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, 'manage_options' );
					},
				),
			);
			add_filter( 'cjwpbldr_register_api_route', array($this, 'registerRoute') );
		}

		public function registerRoute( $routes ) {
			$routes = array_merge( $routes, $this->routes );

			return $routes;
		}

		public function getCustomData( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );
			if( ! isset( $post_data['data_key'] ) || isset( $post_data['data_key'] ) && $post_data['data_key'] == '' ) {
				$message = __( 'You must specify data_key.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $message, 406, $post_data );
			}

			$data['success'] = $this->custom_data_class->getCustomData( $post_data['data_key'] );

			return $this->helpers->apiResponse( $request, $data );
		}

		public function addCustomData( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );
			if( ! isset( $post_data['data_key'] ) || isset( $post_data['data_key'] ) && $post_data['data_key'] == '' ) {
				$message = __( 'You must specify data_key.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $message, 406, $post_data );
			}

			if( ! isset( $post_data['data'] ) || isset( $post_data['data'] ) && $post_data['data'] == '' ) {
				$message = __( 'You must specify data string or array.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $message, 406, $post_data );
			}

			$data['success'] = $this->custom_data_class->addCustomData( $post_data['data_key'], $post_data['data'] );

			return $this->helpers->apiResponse( $request, $data );
		}

		public function deleteCustomData( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );
			if( ! isset( $post_data['data_key'] ) || isset( $post_data['data_key'] ) && $post_data['data_key'] == '' ) {
				$message = __( 'You must specify data_key.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $message, 406, $post_data );
			}

			$data_index = (isset( $post_data['data_index'] )) ? $post_data['data_index'] : '';

			$data['success'] = $this->custom_data_class->deleteCustomData( $post_data['data_key'], $data_index );

			return $this->helpers->apiResponse( $request, $data );
		}
	}

	cjwpbldr_api_core_custom_data::getInstance();
}