<?php
if( ! class_exists( 'cjwpbldr_api_core_users' ) ) {
	class cjwpbldr_api_core_users {
		public $helpers;
		public $addon_dir;
		public $routes;
		public $api_url;

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
				'core-users-roles' => [
					'endpoint' => 'users/roles',
					'name' => __( 'Get users roles', 'lang-textdomains' ),
					'methods' => [
						'get' => [$this, 'getRoles'], // callback function
						'post' => [$this, 'getRoles'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, 'list_users' );
					},
				],
				'core-users' => [
					'endpoint' => 'users',
					'name' => __( 'Get users', 'lang-textdomains' ),
					'methods' => [
						'get' => [$this, 'getUsers'], // callback function
						'post' => [$this, 'getUsers'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, 'list_users' );
					},
				],
				'core-users-get' => [
					'endpoint' => 'users/get',
					'name' => __( 'Get user info', 'wp-builder-locale' ),
					'methods' => [
						'get' => [$this, 'getUser'], // callback function
						'post' => [$this, 'getUser'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, 'list_users' );
					},
				],
				'core-users-get-by-token' => [
					'endpoint' => 'users/get-by-token',
					'name' => __( 'Get current user info', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'getUserByToken'], // callback function
					],
					'permissions' => function ( $request ) {
						return true;
					},
				],
				'core-users-me' => [
					'endpoint' => 'users/me',
					'name' => __( 'Get current user info', 'wp-builder-locale' ),
					'methods' => [
						'get' => [$this, 'getCurrentUserByToken'], // callback function
						'post' => [$this, 'getCurrentUserByToken'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, '' );
					},
				],
				'core-users-me-update-profile' => [
					'endpoint' => 'users/me/update-profile',
					'name' => __( 'Get current user info', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'updateCurrentUserProfile'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, 'read' );
					},
				],
				'core-users-refresh-token' => [
					'endpoint' => 'users/refresh-token',
					'name' => __( 'Get current user info', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'refreshUserToken'], // callback function
					],
					'permissions' => function ( $request ) {
						return true;
					},
				],
				'core-users-by-meta' => [
					'endpoint' => 'users/by-meta',
					'name' => __( 'Get user(s) by meta', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'getUsersByMeta'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, 'list_users' );
					},
				],
				'core-users-add' => [
					'endpoint' => 'users/add',
					'name' => __( 'Create a new user', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'addUser'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, 'add_users' );
					},
				],
				'core-users-update' => [
					'endpoint' => 'users/update',
					'name' => __( 'Update user info', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'updateUserInfo'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, 'add_users' );
					},
				],
				'core-users-delete' => [
					'endpoint' => 'users/delete',
					'name' => __( 'Delete a user', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'deleteUser'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, 'create_users' );
					},
				],
				'core-users-get-meta' => [
					'endpoint' => 'users/meta',
					'name' => __( 'Get user meta', 'wp-builder-locale' ),
					'methods' => [
						'get' => [$this, 'getUserMeta'], // callback function
						'post' => [$this, 'getUserMeta'], // callback function
					],
					'permissions' => function ( $request ) {
						return ($this->helpers->checkApiRoutePermissions( $request, 'create_users' ) || $this->helpers->apiUserCan( $request, 'read' ));
					},
				],
				'core-users-meta-update' => [
					'endpoint' => 'users/meta/update',
					'name' => __( 'Update user meta', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'updateUserMeta'], // callback function
					],
					'permissions' => function ( $request ) {
						return ($this->helpers->checkApiRoutePermissions( $request, 'create_users' ) || $this->helpers->apiUserCan( $request, 'read' ));
					},
				],
				'core-users-meta-delete' => [
					'endpoint' => 'users/meta/delete',
					'name' => __( 'Delete user meta', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'deleteUserMeta'], // callback function
					],
					'permissions' => function ( $request ) {
						return ($this->helpers->checkApiRoutePermissions( $request, 'create_users' ) || $this->helpers->apiUserCan( $request, 'read' ));
					},
				],
				'core-users-meta-push' => [
					'endpoint' => 'users/meta/push',
					'name' => __( 'Add to user meta array', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'pushUserMeta'], // callback function
					],
					'permissions' => function ( $request ) {
						return ($this->helpers->checkApiRoutePermissions( $request, 'create_users' ) || $this->helpers->apiUserCan( $request, 'read' ));
					},
				],
				'core-users-meta-splice' => [
					'endpoint' => 'users/meta/splice',
					'name' => __( 'Add to user meta array', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'spliceUserMeta'], // callback function
					],
					'permissions' => function ( $request ) {
						return ($this->helpers->checkApiRoutePermissions( $request, 'create_users' ) || $this->helpers->apiUserCan( $request, 'read' ));
					},
				],
			];
			add_filter( 'cjwpbldr_register_api_route', [$this, 'registerRoute'] );
		}

		public function registerRoute( $routes ) {
			$routes = array_merge( $routes, $this->routes );

			return $routes;
		}

		public function getRoles( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );
			global $wp_roles;
			$all_roles = $wp_roles->roles;
			$all_roles = apply_filters( 'editable_roles', $all_roles );
			$return['success'] = $all_roles;

			return $this->helpers->apiResponse( $request, $return );
		}

		public function getUsers( $request ) {
			$return = [];
			$post_data = $this->helpers->apiRequestParams( $request );
			if( ! isset( $post_data['page'] ) ) {
				$post_data['page'] = 1;
			}

			$post_data['page'] = ($post_data['page'] == 0) ? 1 : $post_data['page'];
			$page = (isset( $post_data['page'] )) ? $post_data['page'] : 1;
			$number = get_option( 'posts_per_page' );
			$paged = (get_query_var( 'paged' )) ? get_query_var( 'paged' ) : $page;
			$offset = ($paged - 1) * $number;
			$query_vars = [
				'orderby' => 'registered',
				'order' => 'ASC',
			];
			$post_data['query_vars'] = (gettype( $post_data['query_vars'] ) == 'string') ? json_decode( $post_data['query_vars'], 1 ) : $post_data['query_vars'];
			if( isset( $post_data['query_vars'] ) && is_array( $post_data['query_vars'] ) && ! empty( $post_data['query_vars'] ) ) {
				foreach( $post_data['query_vars'] as $key => $query_var_value ) {
					$query_vars[ $key ] = $query_var_value;
				}
			}

			$query_vars['number'] = $number;
			$query_vars['offset'] = $offset;
			$return['query_vars'] = $query_vars;

			$total_query_vars = $query_vars;
			unset( $total_query_vars['number'] );
			unset( $total_query_vars['offset'] );
			$total_users = get_users( $total_query_vars );
			$total_users_count = count( $total_users );
			$return['total'] = $total_users_count;

			$users = get_users( $query_vars );
			$return['found'] = count( $users );
			$total_pages = ceil( $total_users_count / $number );
			$current_page = (int) $paged;
			$next_page = ($current_page < $total_pages) ? $current_page + 1 : 0;
			$prev_page = ($current_page <= $total_pages && $current_page > 0) ? $current_page - 1 : 0;
			$return['pagination']['total_pages'] = $total_pages;
			$return['pagination']['current_page'] = $current_page;
			$return['pagination']['next_page'] = $next_page;
			$return['pagination']['prev_page'] = $prev_page;
			$return['users'] = [];
			$additional_fields = (isset( $post_data['fields'] ) && ! empty( $post_data['fields'] )) ? $post_data['fields'] : [];
			if( ! empty( $users ) ) {
				$return['users'] = [];
				foreach( $users as $key => $user ) {
					$return['users'][ $key ] = $this->helpers->userInfo( $user->ID, $additional_fields, ['access_token'] );
				}
			}
			$links['pagination']['next'] = $this->helpers->apiUrl() . 'users?page=' . $next_page;
			$links['pagination']['prev'] = $this->helpers->apiUrl() . 'users?page=' . $prev_page;

			$output['success'] = $return;

			return $this->helpers->apiResponse( $request, $output, $links );
		}

		public function getCurrentUserByToken( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );
			$additional_fields = (isset( $post_data['fields'] ) && ! empty( $post_data['fields'] )) ? $post_data['fields'] : [];
			$user_info = $this->helpers->getApiUserInfo( $request, $additional_fields );
			$user_info = $this->helpers->userInfo( $user_info['ID'], $additional_fields );
			if( empty( $user_info ) ) {
				return $this->helpers->apiError( $request, 406, __( 'Invalid access token.', 'wp-builder-locale' ), 406, $post_data );
			}

			return $this->helpers->apiResponse( $request, ['success' => $user_info] );
		}

		public function getUserByToken( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );

			$rules = [
				'token' => 'required'
			];
			$messages = [
				'token' => __( 'You must specify a token.', 'wp-builder-locale' )
			];

			$errors = $this->helpers->validateWithGump( $rules, $post_data, $messages );

			if( ! empty( $errors ) ) {
				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$additional_fields = (isset( $post_data['fields'] ) && ! empty( $post_data['fields'] )) ? $post_data['fields'] : [];
			$user_info = $this->helpers->getUserByToken( $post_data['token'] );
			$user_info = $this->helpers->userInfo( $user_info['ID'], $additional_fields );
			if( empty( $user_info ) ) {
				return $this->helpers->apiError( $request, 406, __( 'Invalid access token.', 'wp-builder-locale' ), 406, $post_data );
			}

			return $this->helpers->apiResponse( $request, ['success' => $user_info] );
		}

		public function getUsersByMeta( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );
			$additional_fields = (isset( $post_data['fields'] ) && ! empty( $post_data['fields'] )) ? $post_data['fields'] : [];

			if( ! isset( $post_data['meta_key'] ) ) {
				$errors = __( 'You must specify meta_key.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}
			$meta_value = (isset( $post_data['meta_value'] )) ? $post_data['meta_value'] : '';
			$results = $this->helpers->getUsersByMeta( $post_data['meta_key'], $meta_value, 'info' );
			$return = [];
			foreach( $results as $key => $result ) {
				$return[] = $this->helpers->userInfo( $result['ID'], $additional_fields );
			}

			return $this->helpers->apiResponse( $request, ['success' => $return] );
		}

		public function getUser( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );
			$additional_fields = (isset( $post_data['fields'] ) && ! empty( $post_data['fields'] )) ? $post_data['fields'] : [];
			$user_info = $this->helpers->userInfo( $post_data['user'], $additional_fields );
			if( empty( $user_info ) ) {
				return $this->helpers->apiError( $request, 406, __( 'No user found.', 'wp-builder-locale' ), 406, $post_data );
			}

			return $this->helpers->apiResponse( $request, ['success' => $user_info] );
		}

		public function refreshUserToken( $request ) {
			$user_info = $this->helpers->getApiUserInfo( $request );
			if( ! empty( $user_info ) ) {
				$new_token = $this->helpers->setUserToken( $user_info['ID'] );
				$user_info = $this->helpers->getUserByToken( $new_token, ['access_token'] );

				return $this->helpers->apiResponse( $request, ['success' => $user_info] );
			}
		}

		public function updateCurrentUserProfile( $request ) {
			$errors = [];
			$post_data = $this->helpers->apiRequestParams( $request );
			$api_user_info = $this->helpers->getApiUserInfo( $request );
			$user_info = $this->helpers->userInfo( $api_user_info['ID'] );

			if( empty( $user_info ) ) {
				$errors['user_login'] = __( 'No user found.', 'wp-builder-locale' );
			}

			if( isset( $post_data['user_email'] ) ) {
				$email_user_info = $this->helpers->userInfo( $post_data['user_email'] );

				// check if email belongs to another user
				if( ! empty( $email_user_info ) && $user_info['ID'] !== $email_user_info['ID'] ) {
					$errors['user_email'] = __( 'Email address belongs to another account.', 'wp-builder-locale' );
				}
			}

			unset( $post_data['user_login'] );

			if( isset( $post_data['user_pass'] ) && isset( $post_data['user_pass_confirmation'] ) ) {
				if( $post_data['user_pass'] !== $post_data['user_pass_confirmation'] ) {
					$errors['user_pass'] = __( 'Password does not match.', 'wp-builder-locale' );
				}
			}

			if( ! empty( $errors ) ) {
				return $this->helpers->apiError( $request, 406, implode( '<br>', $errors ), 406, $user_info );
			}

			$this->helpers->updateUserInfo( $user_info['ID'], $post_data );
			$additional_fields = (isset( $post_data['fields'] ) && ! empty( $post_data['fields'] )) ? $post_data['fields'] : ['access_token'];

			$user_info = $this->helpers->userInfo( $user_info['ID'], $additional_fields );

			$return['success'] = __( 'Information updated successfully.', 'wp-builder-locale' );
			$return['user_info'] = $user_info;
			$return['post_data'] = $post_data;

			return $this->helpers->apiResponse( $request, $return );
		}

		public function updateUserInfo( $request ) {
			$errors = [];
			$post_data = $this->helpers->apiRequestParams( $request );
			$api_user_info = $this->helpers->getApiUserInfo( $request );
			$user_info = $this->helpers->userInfo( $post_data['user_login'] );
			if( empty( $user_info ) ) {
				$errors['user_login'] = __( 'No user found.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			unset( $post_data['user_login'] );

			$this->helpers->updateUserInfo( $user_info['ID'], $post_data );
			$additional_fields = (isset( $post_data['fields'] ) && ! empty( $post_data['fields'] )) ? $post_data['fields'] : [];
			$user_info = $this->helpers->userInfo( $user_info['ID'], $additional_fields, ['access_token'] );

			$return['success'] = __( 'User information updated successfully.', 'wp-builder-locale' );
			$return['user_info'] = $user_info;

			return $this->helpers->apiResponse( $request, $return );
		}

		public function addUser( $request ) {
			$errors = [];
			$additional_fields = (isset( $post_data['fields'] ) && ! empty( $post_data['fields'] )) ? $post_data['fields'] : [];
			$post_data = $this->helpers->apiRequestParams( $request );
			if( ! isset( $post_data['user_email'] ) ) {
				$errors['user_email'] = __( 'Email address field is required.', 'wp-builder-locale' );
			}
			if( isset( $post_data['user_email'] ) && ! $this->helpers->isValidEmail( $post_data['user_email'] ) ) {
				$errors['user_email'] = __( 'Please enter a valid email address.', 'wp-builder-locale' );
			}
			if( email_exists( $post_data['user_email'] ) ) {
				$errors['user_email'] = __( 'Email address already registered.', 'wp-builder-locale' );
			}
			if( ! empty( $errors ) ) {
				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}
			$unique_user_login = $this->helpers->uniqueUsername( $post_data['user_email'] );
			$user_login = (isset( $post_data['user_login'] ) && $post_data['user_login'] != '') ? $post_data['user_login'] : $unique_user_login;
			$user_pass = (isset( $post_data['user_pass'] ) && $post_data['user_pass'] != '') ? $post_data['user_pass'] : wp_generate_password( 12, true );
			$new_user_id = wp_create_user( $unique_user_login, $user_pass, $user_login );

			$this->helpers->updateUserInfo( $new_user_id, $post_data );
			$new_user_info = $this->helpers->userInfo( $new_user_id, $additional_fields, ['access_token'] );

			return $this->helpers->apiResponse( $request, ['success' => $new_user_info] );
		}

		public function deleteUser( $request ) {
			$errors = [];
			$post_data = $this->helpers->apiRequestParams( $request );
			if( ! isset( $post_data['user'] ) ) {
				$errors['user'] = __( 'Missing required fields.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$user_info = $this->helpers->userInfo( $post_data['user'] );
			if( $user_info['user_email'] == get_option( 'admin_email' ) ) {
				$errors['user'] = __( 'Admin account cannot be removed.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			if( empty( $user_info ) ) {
				$errors['user'] = __( 'No user found with the information provided.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$reassign = (isset( $post_data['reassign'] )) ? $post_data['reassign'] : false;
			require_once(ABSPATH . 'wp-admin/includes/user.php');
			wp_delete_user( $user_info['ID'], $reassign );

			$return['success'] = __( 'User removed successfully', 'wp-builder-locale' );

			return $this->helpers->apiResponse( $request, $return );
		}

		public function getUserMeta( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );

			$errors = [];
			if( ! isset( $post_data['user'] ) ) {
				$errors['user'] = __( 'You must specify a valid user_id.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}
			if( ! isset( $post_data['meta_key'] ) ) {
				$errors['meta_key'] = __( 'You must specify a valid meta_key.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$user_info = $this->helpers->userInfo( $post_data['user'] );

			if( empty( $user_info ) ) {
				$errors['user_info'] = __( 'User not found.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$user_meta['success'] = get_user_meta( $user_info['ID'], $post_data['meta_key'], true );

			return $this->helpers->apiResponse( $request, ['success' => $user_meta] );
		}

		public function updateUserMeta( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );

			$rules = [
				'user' => 'required',
				'meta_key' => 'required',
				'meta_value' => 'required',
			];
			$messages = [
				'user' => 'You must specify user ID, username or email address.',
			];

			$errors = $this->helpers->validateWithGump( $rules, $post_data, $messages, [] );

			if( ! empty( $errors ) ) {
				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$user_info = $this->helpers->userInfo( $post_data['user'] );
			if( empty( $user_info ) ) {
				$errors = __( 'No user found.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$meta_value = (is_array( $post_data['meta_value'] )) ? serialize( $post_data['meta_value'] ) : $post_data['meta_value'];
			update_user_meta( $user_info['ID'], $post_data['meta_key'], $meta_value );
			$additional_fields = (isset( $post_data['fields'] ) && ! empty( $post_data['fields'] )) ? $post_data['fields'] : [];
			$return['success'] = $this->helpers->userInfo( $post_data['user'], $additional_fields );

			return $this->helpers->apiResponse( $request, $return );
		}

		public function deleteUserMeta( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );

			$rules = [
				'user' => 'required',
				'meta_key' => 'required',
			];
			$messages = [
				'post' => 'You must specify post ID or slug.',
				'meta_key' => 'You must specify meta key to remove.',
			];

			$errors = $this->helpers->validateWithGump( $rules, $post_data, $messages, [] );

			if( ! empty( $errors ) ) {
				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$user_info = $this->helpers->userInfo( $post_data['user'] );
			if( empty( $user_info ) ) {
				$errors = __( 'No user found.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			delete_user_meta( $user_info['ID'], $post_data['meta_key'] );
			$additional_fields = (isset( $post_data['fields'] ) && ! empty( $post_data['fields'] )) ? $post_data['fields'] : [];
			$return['success'] = $this->helpers->userInfo( $user_info['ID'], $additional_fields, ['access_token'] );

			return $this->helpers->apiResponse( $request, $return );
		}

		public function pushUserMeta( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );

			if( ! isset( $post_data['user'] ) ) {
				$errors['user'] = __( 'You must specify a valid user_id.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			if( isset( $post_data['user'] ) && $post_data['user'] == '' ) {
				$errors['user'] = __( 'You must specify a valid user_id.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}
			if( ! isset( $post_data['meta_key'] ) ) {
				$errors['meta_key'] = __( 'You must specify a valid meta_key.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}
			if( ! isset( $post_data['data'] ) ) {
				$errors['data'] = __( 'You must specify data to push.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$user_info = $this->helpers->userInfo( $post_data['user'] );

			if( empty( $user_info ) ) {
				$errors['user_info'] = __( 'User not found.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$existing = get_user_meta( $user_info['ID'], $post_data['meta_key'], true );
			if( ! is_array( $existing ) ) {
				$existing = [];
			}

			$key = (is_array( $post_data['data'] )) ? json_encode( $post_data['data'] ) : $post_data['data'];
			$key = sha1( $key );
			$existing[ $key ] = $post_data['data'];

			update_user_meta( $user_info['ID'], $post_data['meta_key'], $existing );
			$return['success'] = get_user_meta( $user_info['ID'], $post_data['meta_key'], true );

			return $this->helpers->apiResponse( $request, $return );
		}

		public function spliceUserMeta( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );
			$errors = [];
			if( ! isset( $post_data['user'] ) ) {
				$errors['user'] = __( 'You must specify a valid user_id.', 'wp-builder-locale' );
			}
			if( ! isset( $post_data['meta_key'] ) ) {
				$errors['meta_key'] = __( 'You must specify a valid meta_key.', 'wp-builder-locale' );
			}
			if( ! isset( $post_data['key'] ) ) {
				$errors['key'] = __( 'You must specify a valid key to remove from data.', 'wp-builder-locale' );
			}
			if( ! empty( $errors ) ) {
				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$user_info = $this->helpers->userInfo( $post_data['user'] );

			$user_meta = get_user_meta( $user_info['ID'], $post_data['meta_key'] );

			$data_array = [];
			if( ! empty( $user_meta ) ) {
				foreach( $user_meta as $index => $data_array ) {
					if( isset( $data_array[ $post_data['key'] ] ) ) {
						unset( $data_array[ $post_data['key'] ] );
					}
				}
			}
			update_user_meta( $user_info['ID'], $post_data['meta_key'], $data_array );
			$return['success'] = get_user_meta( $user_info['ID'], $post_data['meta_key'], true );

			return $this->helpers->apiResponse( $request, $return );
		}
	}

	cjwpbldr_api_core_users::getInstance();
}
