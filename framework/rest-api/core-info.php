<?php
if( ! class_exists( 'cjwpbldr_api_core_info' ) ) {
	class cjwpbldr_api_core_info {

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
				'core-info' => [
					'endpoint' => 'core/info',
					'name' => __( 'Product Info', 'wp-builder-locale' ),
					'methods' => [
						'get' => [$this, 'apiInfo'], // callback function
						'post' => [$this, 'apiInfo'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, '' );
					},
				],
				'core-locale' => [
					'endpoint' => 'core/locale',
					'name' => __( 'Localization Strings', 'wp-builder-locale' ),
					'methods' => [
						'get' => [$this, 'getLocale'], // callback function
						'post' => [$this, 'getLocale'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, 'read' );
					},
				],
				'core-scss-compile' => [
					'endpoint' => 'core/scss/compile',
					'name' => __( 'Core compile scss', 'wp-builder-locale' ),
					'description' => '',
					'methods' => [
						'post' => [$this, 'compileScss'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->apiUserCan( $request, 'manage_options' );
					},
				],
				'core-wp-builder-settings' => [
					'endpoint' => 'core/wp-builder-settings',
					'name' => __( 'WP Builder Settings', 'wp-builder-locale' ),
					'methods' => [
						'get' => [$this, 'frameworkOptions'], // callback function
						'post' => [$this, 'frameworkOptions'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, 'manage_options' );
					},
				],
				'core-wp-builder-setting' => [
					'endpoint' => 'core/wp-builder-settings/get',
					'name' => __( 'Get WP Builder Option', 'wp-builder-locale' ),
					'methods' => [
						'get' => [$this, 'getFrameworkOption'], // callback function
						'post' => [$this, 'getFrameworkOption'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->apiUserCan( $request, 'manage_options' );
					},
				],
				'core-get-wp-option' => [
					'endpoint' => 'core/wp-option/get',
					'name' => __( 'Get WordPress Option', 'wp-builder-locale' ),
					'methods' => [
						'get' => [$this, 'wpGetOption'], // callback function
						'post' => [$this, 'wpGetOption'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, 'manage_options' );
					},
				],
				'core-update-wp-option' => [
					'endpoint' => 'core/wp-option/update',
					'name' => __( 'Update WordPress Option', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'wpUpdateOption'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, 'manage_options' );
					},
				],
				'core-delete-wp-option' => [
					'endpoint' => 'core/wp-option/delete',
					'name' => __( 'Delete WordPress Option', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'wpDeleteOption'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, 'manage_options' );
					},
				],
				'core-wp-transient-get' => [
					'endpoint' => 'core/wp-transient/get',
					'name' => __( 'Get WordPress Transient', 'wp-builder-locale' ),
					'methods' => [
						'get' => [$this, 'wpGetTransient'], // callback function
						'post' => [$this, 'wpGetTransient'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, 'manage_options' );
					},
				],
				'core-wp-transient-update' => [
					'endpoint' => 'core/wp-transient/update',
					'name' => __( 'Get WordPress Transient', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'wpUpdateTransient'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, 'manage_options' );
					},
				],
				'core-wp-transient-delete' => [
					'endpoint' => 'core/wp-transient/delete',
					'name' => __( 'Delete WordPress Transient', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'wpDeleteTransient'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, 'manage_options' );
					},
				],
				'core-contact-form-process' => [
					'endpoint' => 'core/contact-form/process',
					'name' => __( 'Contact Form', 'wp-builder-locale' ),
					'description' => '',
					'methods' => [
						'post' => [$this, 'processContactForm'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, '' );
					},
				],
				'core-image-import-from-url' => [
					'endpoint' => 'core/image/import-from-url',
					'name' => __( 'Import image from URL', 'wp-builder-locale' ),
					'description' => '',
					'methods' => [
						'post' => [$this, 'importImageFromUrl'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, 'upload_files' );
					},
				],
				'core-file-upload' => [
					'endpoint' => 'core/file/upload',
					'name' => __( 'Core file upload', 'wp-builder-locale' ),
					'description' => '',
					'methods' => [
						'post' => [$this, 'uploadFileToMedia'], // callback function
					],
					'permissions' => function ( $request ) {
						return true;
					},
				],
				'core-vue-form' => [
					'endpoint' => 'core/vue/form',
					'name' => __( 'Core generate vue form', 'wp-builder-locale' ),
					'description' => '',
					'methods' => [
						'post' => [$this, 'generateVueForm'], // callback function
					],
					'permissions' => function ( $request ) {
						return true;
					},
				],
			];
			add_filter( 'cjwpbldr_register_api_route', [$this, 'registerRoute'] );
		}

		public function registerRoute( $routes ) {
			$routes = array_merge( $routes, $this->routes );

			return $routes;
		}

		public function apiInfo( $request ) {
			$data['success'] = $this->helpers->config( 'plugin-info' );

			return $this->helpers->apiResponse( $request, $data );
		}

		public function getLocale( $request ) {
			$data['success'] = $this->helpers->localizeScripts();

			return $this->helpers->apiResponse( $request, $data );
		}

		public function frameworkOptions( $request ) {
			$data['success'] = $this->helpers->saved_options;

			return $this->helpers->apiResponse( $request, $data );
		}

		public function getFrameworkOption( $request ) {
			$params = $this->helpers->apiRequestParams( $request );
			if( ! isset( $params['option_name'] ) ) {
				return $this->helpers->apiError( $request, 406, __( 'You must specify option_name.', 'wp-builder-locale' ), 406, $params );
			}
			if( isset( $params['option_name'] ) && $params['option_name'] == '' ) {
				return $this->helpers->apiError( $request, 406, __( 'You must specify option_name.', 'wp-builder-locale' ), 406, $params );
			}
			$data['success'] = $this->helpers->saved_options[ $params['option_name'] ];

			return $this->helpers->apiResponse( $request, $data );
		}

		public function wpGetOption( $request ) {
			$params = $this->helpers->apiRequestParams( $request );
			if( ! isset( $params['option_name'] ) ) {
				return $this->helpers->apiError( $request, 406, __( 'You must specify option_name.', 'wp-builder-locale' ), 406, $params );
			}

			$result = get_option( $params['option_name'], false );
			if( ! $result ) {
				return $this->helpers->apiError( $request, 406, __( 'Nothing found.', 'wp-builder-locale' ), 406, $params );
			}
			$data['success'] = $result;

			return $this->helpers->apiResponse( $request, $data );
		}

		public function wpUpdateOption( $request ) {
			$params = $this->helpers->apiRequestParams( $request );
			if( ! isset( $params['option_name'] ) ) {
				return $this->helpers->apiError( $request, 406, __( 'You must specify option_name.', 'wp-builder-locale' ), 406, $params );
			}
			if( ! isset( $params['option_value'] ) ) {
				return $this->helpers->apiError( $request, 406, __( 'You must specify option_value.', 'wp-builder-locale' ), 406, $params );
			}
			update_option( $params['option_name'], $params['option_value'] );
			$data['success'] = get_option( $params['option_name'], false );

			return $this->helpers->apiResponse( $request, $data );
		}

		public function wpDeleteOption( $request ) {
			$params = $this->helpers->apiRequestParams( $request );
			if( ! isset( $params['option_name'] ) ) {
				return $this->helpers->apiError( $request, 406, __( 'You must specify option_name.', 'wp-builder-locale' ), 406, $params );
			}
			$data['success'] = delete_option( $params['option_name'] );

			return $this->helpers->apiResponse( $request, $data );
		}

		public function wpGetTransient( $request ) {
			$params = $this->helpers->apiRequestParams( $request );

			if( ! isset( $params['option_name'] ) ) {
				return $this->helpers->apiError( $request, 406, __( 'You must specify option_name.', 'wp-builder-locale' ), 406, $params );
			}
			$result = get_transient( $params['option_name'] );
			if( ! $result ) {
				return $this->helpers->apiError( $request, 200, __( 'Nothing found.', 'wp-builder-locale' ), 200, $params );
			}
			$data['success'] = $result;

			return $this->helpers->apiResponse( $request, $data );
		}

		public function wpDeleteTransient( $request ) {
			$params = $this->helpers->apiRequestParams( $request );
			if( ! isset( $params['option_name'] ) ) {
				return $this->helpers->apiError( $request, 406, __( 'You must specify option_name.', 'wp-builder-locale' ), 406, $params );
			}
			$result = delete_transient( $params['option_name'] );
			$data['success'] = $result;

			return $this->helpers->apiResponse( $request, $data );
		}

		public function wpUpdateTransient( $request ) {
			$params = $this->helpers->apiRequestParams( $request );

			if( ! isset( $params['option_name'] ) ) {
				return $this->helpers->apiError( $request, 406, __( 'You must specify option_name.', 'wp-builder-locale' ), 406, $params );
			}
			if( ! isset( $params['option_value'] ) ) {
				return $this->helpers->apiError( $request, 406, __( 'You must specify option_value.', 'wp-builder-locale' ), 406, $params );
			}
			$expires = (isset( $params['expires'] )) ? $params['expires'] : 31536000;

			delete_transient( $params['option_name'] );
			$data['success'] = set_transient( $params['option_name'], $params['option_value'], $expires );

			return $this->helpers->apiResponse( $request, $data );
		}

		public function processContactForm( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );
			$errors = [];
			$required_fields = [
				'first_name',
				'last_name',
				'email',
				'message',
			];

			foreach( $required_fields as $key => $field ) {
				if( ! array_key_exists( $field, $post_data ) ) {
					$errors[ $field ] = sprintf( __( 'You must specify %s.', 'wp-builder-locale' ), $field );
				}
				if( array_key_exists( $field, $post_data ) && $post_data[ $field ] == '' ) {
					$errors[ $field ] = sprintf( __( '%s field is required.', 'wp-builder-locale' ), ucwords( str_replace( '_', ' ', $field ) ) );
				}
			}
			if( ! empty( $errors ) ) {
				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			if( isset( $post_data['email'] ) && ! $this->helpers->isValidEmail( $post_data['email'] ) ) {
				$errors['email'] = __( 'Email address is not valid.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$from_name = $post_data['first_name'] . ' ' . $post_data['last_name'];
			$message = nl2br( $post_data['message'] );
			$to_email = (array_key_exists( 'to_email', $post_data ) && $post_data['to_email'] != '') ? $post_data['to_email'] : get_option( 'admin_email' );
			$email_subject = (array_key_exists( 'email_subject', $post_data ) && $post_data['email_subject'] != '') ? $post_data['email_subject'] : sprintf( __( 'Contact via %s!', 'wp-builder-locale' ), get_bloginfo( 'name' ) );

			$email_data = [
				'to' => $to_email,
				'from_name' => $from_name,
				'from_email' => $post_data['email'],
				'subject' => $email_subject,
				'message' => $message,
			];
			$headers[] = 'Reply-To: ' . $from_name . ' <' . $post_data['email'] . '>';

			$this->helpers->sendEmail( $email_data, [], 'wp-mail', $headers );
			$return['success'] = __( '<b>Thank You!</b><br>We have received your message and will get back to you as soon as possible.', 'wp-builder-locale' );

			if( isset( $post_data['post_url'] ) && $post_data['post_url'] != '' ) {
				$this->helpers->wpRemotePost( $post_data['post_url'], $email_data );
				$return['post_sent'] = $post_data;
			}

			return $this->helpers->apiResponse( $request, $return );
		}

		public function importImageFromUrl( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );
			if( ! isset( $post_data['image_url'] ) || isset( $post_data['image_url'] ) && $post_data['image_url'] == '' ) {
				$message = __( 'You must specify image_url.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $message, 406, $post_data );
			}
			$download_url = $post_data['image_url'];

			$upload_dir = wp_upload_dir(); // Set upload folder
			if( wp_mkdir_p( $upload_dir['path'] ) ) {
				$file_path = $upload_dir['path'] . '/';
			} else {
				$file_path = $upload_dir['basedir'] . '/';
			}
			$ext = (isset( $post_data['file_type'] )) ? $post_data['file_type'] : 'jpg';
			$ext = str_replace( '.', '', $ext );
			$unique_file_name = sha1( $this->helpers->uniqueString() );
			$file_name = $file_path . 'wpb-' . $unique_file_name . '.' . $ext;
			if( isset( $post_data['image_name'] ) && $post_data['image_name'] != '' ) {
				$post_data['image_name'] = sanitize_file_name( $post_data['image_name'] );
				$file_name = str_replace( $unique_file_name, $post_data['image_name'], $file_name );
			}
			$data['success'] = $this->helpers->saveImageFromUrl( $download_url, basename( $file_name ) );

			return $this->helpers->apiResponse( $request, $data );
		}

		public function uploadFileToMedia( $request ) {
			$params = $this->helpers->apiRequestParams( $request );
			if( ! isset( $params['file'] ) || isset( $params['file'] ) && $params['file'] == '' ) {
				$message = __( 'You must specify a file.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $message, 406, $params );
			}
			$return = [];
			$upload_dir = (isset( $params['upload_dir'] )) ? $params['upload_dir'] : null;

			$allowed_file_types = (isset( $params['allowed_file_types'] )) ? $params['allowed_file_types'] : null;
			$allowed_file_size = (isset( $params['allowed_file_size'] )) ? ($params['allowed_file_size'] * 1024 * 1024) : null;

			$response = $this->helpers->uploadFile( $_FILES['file'], null, null, $allowed_file_types, $allowed_file_size, 'guid', $upload_dir );

			if( is_array( $response ) && isset( $response['errors'] ) ) {
				return $this->helpers->apiResponse( $request, $response );
			}

			if( is_array( $response ) && isset( $response['path'] ) ) {
				$return['success']['uploaded_file_url'] = $response['path'];
			} else if( is_string( $response ) && $response != '' ) {
				$return['success']['uploaded_file_url'] = $response;
			}

			return $this->helpers->apiResponse( $request, $return );
		}

		public function generateVueForm( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );
			$saved_values = (isset( $post_data['saved_values'] )) ? $post_data['saved_values'] : [];
			$form_options = [];
			if( isset( $post_data['options'] ) ) {
				$form_options = $post_data['options'];
				foreach( $form_options as $key => $option ) {
					$saved_value = (isset( $saved_values[ $option['id'] ] )) ? $saved_values[ $option['id'] ] : '';
					$form_options[ $key ] = $this->helpers->sanitizeFormOption( $option, $saved_value );
				}
			}
			$return['success'] = $form_options;

			return $this->helpers->apiResponse( $request, $return );
		}

		public function compileScss( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );
			$return = [];
			if( isset( $post_data['type'] ) ) {
				if( $post_data['type'] == 'core' ) {
					$this->helpers->compileCoreScssFiles();
					$return['success'] = __( 'Core CSS compiled successfully.', 'wp-builder-locale' );
				}
				if( $post_data['type'] == 'blocks' ) {
					$this->helpers->compileBlocksScss();
					$return['success'] = __( 'Blocks CSS compiled successfully.', 'wp-builder-locale' );
				}
				if( $post_data['type'] == 'addons' ) {
					$this->helpers->compileAddonsScss();
					$return['success'] = __( 'Addons CSS compiled successfully.', 'wp-builder-locale' );
				}
			} else {
				$this->helpers->compileCoreScssFiles();
				$this->helpers->compileBlocksScss();
				$this->helpers->compileAddonsScss();
				delete_option( 'cjwpbldr_ajax_compile_scss' );
				$return['success'] = __( 'CSS compiled successfully.', 'wp-builder-locale' );
			}

			return $this->helpers->apiResponse( $request, $return );
		}

	}

	cjwpbldr_api_core_info::getInstance();
}