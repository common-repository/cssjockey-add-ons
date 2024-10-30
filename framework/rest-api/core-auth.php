<?php
if( ! class_exists( 'cjwpbldr_api_core_auth' ) ) {
	class cjwpbldr_api_core_auth {

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
				'core-auth-find-user' => [
					'endpoint' => 'auth/find-user',
					'name' => __( 'Login', 'wp-builder-locale' ),
					'methods' => [
						'get' => [$this, 'findUser'], // callback function
						'post' => [$this, 'findUser'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, 'list_users' );
					},
				],
				'core-auth-login' => [
					'endpoint' => 'auth/login',
					'name' => __( 'Login', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'doLogin'], // callback function
					],
					'permissions' => function ( $request ) {
						return true;
					},
				],
				'core-auth-register' => [
					'endpoint' => 'auth/register',
					'name' => __( 'Register', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'doRegister'], // callback function
					],
					'permissions' => function ( $request ) {
						return true;
					},
				],
				'core-auth-complete-registration' => [
					'endpoint' => 'auth/register/complete',
					'name' => __( 'Register', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'verifyEmailAndCreateAccount'], // callback function
					],
					'permissions' => function ( $request ) {
						return true;
					},
				],
				'core-auth-send-password-link' => [
					'endpoint' => 'auth/send-password-link',
					'name' => __( 'Send password email', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'sendResetPasswordMail'], // callback function
					],
					'permissions' => function ( $request ) {
						return true;
					},
				],
				'core-auth-password-reset' => [
					'endpoint' => 'auth/password/reset',
					'name' => __( 'Send password email', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'sendResetPasswordMail'], // callback function
					],
					'permissions' => function ( $request ) {
						return true;
					},
				],
				'core-auth-password-change' => [
					'endpoint' => 'auth/password/change',
					'name' => __( 'Change password with token', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'changePasswordWithKey'], // callback function
					],
					'permissions' => function ( $request ) {
						return true;
					},
				],
				'core-auth-send-verification' => [
					'endpoint' => 'auth/email/send-verification',
					'name' => __( 'Verify an email address for a user', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'emailSendVerification'], // callback function
					],
					'permissions' => function ( $request ) {
						return true;
					},
				],
				'core-auth-logout' => [
					'endpoint' => 'auth/logout',
					'name' => __( 'Logout', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'doLogout'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, 'read' );
					},
				],
				'core-auth-email-verify' => [
					'endpoint' => 'auth/email/verify',
					'name' => __( 'Verify email address with code.', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'verifyEmail'], // callback function
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

		public function findUser( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );

			$user_info = $this->helpers->userInfo( $post_data['user'], ['ID', 'first_name', 'last_name', 'display_name', 'gravatar', 'user_url', 'user_registered'] );
			$return['success'] = $user_info;

			return $this->helpers->apiResponse( $request, $return );
		}

		public function doLogin( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );

			$errors = [];
			if( ! isset( $post_data['user'] ) ) {
				$errors['user'] = __( 'You must enter your username or email address.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			if( isset( $post_data['user'] ) && $post_data['user'] == '' ) {
				$errors['user'] = __( 'You must enter your username or email address.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			if( isset( $post_data['user'] ) && $this->helpers->containString( '@', $post_data['user'] ) && ! $this->helpers->isValidEmail( $post_data['user'] ) ) {
				$errors['user'] = __( 'You must enter a valid email address.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			if( isset( $post_data['user'] ) && $this->helpers->containString( '@', $post_data['user'] ) && $this->helpers->isValidEmail( $post_data['user'] ) && ! email_exists( $post_data['user'] ) ) {
				$errors['user'] = __( 'No account found with this email address.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			if( isset( $post_data['user'] ) && ! $this->helpers->containString( '@', $post_data['user'] ) ) {
				if( ! username_exists( $post_data['user'] ) ) {
					$errors['user'] = __( 'No account found with this username.', 'wp-builder-locale' );

					return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
				}
			}

			if( ! isset( $post_data['user_pass'] ) ) {
				$errors['user_pass'] = __( 'You must enter your password.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			if( $post_data['user_pass'] == '' ) {
				$errors['user_pass'] = __( 'You must enter your password.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$user_info = $this->helpers->userInfo( $post_data['user'], ['access_token'] );

			if( empty( $user_info ) ) {
				$errors['user'] = __( 'Invalid credentials, please try again.', 'wp-builder-locale' );
				$errors['user_pass'] = __( 'Invalid credentials, please try again.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$authenticate = wp_authenticate( $user_info['user_login'], $post_data['user_pass'] );
			if( is_wp_error( $authenticate ) ) {
				$error_message = $authenticate->get_error_message();
				/*$error_message = $authenticate->get_error_message();
				$errors[] = str_replace( '<a ', '<a target="_blank" ', $error_message );*/
				if( ! $this->helpers->containString( 'you entered for the username ', $error_message ) ) {
					$errors['user'] = __( 'Invalid credentials, please try again.', 'wp-builder-locale' );
				}
				$errors['user_pass'] = __( 'The password you entered is not correct, please try again.', 'wp-builder-locale' );
			}

			if( ! empty( $errors ) ) {
				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$login_status = $this->helpers->login( $user_info['user_login'], $post_data['user_pass'] );
			if( ! $login_status ) {
				$errors['user'] = __( 'Invalid username or password.', 'wp-builder-locale' );
				$errors['user_pass'] = __( 'Invalid username or password.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			} else {
				$user_info = $this->helpers->userInfo( $user_info['user_login'], ['access_token'] );
				$this->helpers->setUserToken( $user_info );
				$return['success'] = $this->helpers->userInfo( $user_info['user_login'], ['access_token'] );

				return $this->helpers->apiResponse( $request, $return );
			}
		}

		public function doRegister( $request ) {
			global $wpdb;
			$post_data = $this->helpers->apiRequestParams( $request );
			$errors = [];

			if( isset( $post_data['user_mobile'] ) && $post_data['user_mobile'] !== '' ) {
				$check_user_mobile = $wpdb->get_row( "SELECT * FROM $wpdb->usermeta WHERE meta_key = 'user_mobile' AND meta_value = '{$post_data['user_mobile']}'" );
				if( ! is_null( $check_user_mobile ) ) {
					$errors['user_mobile'] = __( 'Mobile number already exists.', 'wp-builder-locale' );
				}
			}

			if( isset( $post_data['first_name'] ) && $post_data['first_name'] === '' ) {
				$errors['first_name'] = __( 'First name field is required.', 'wp-builder-locale' );
			}
			if( isset( $post_data['last_name'] ) && $post_data['last_name'] === '' ) {
				$errors['last_name'] = __( 'Last name field is required.', 'wp-builder-locale' );
			}
			if( ! isset( $post_data['user_login'] ) && isset( $post_data['auto_user_login'] ) ) {
				$post_data['user_login'] = $this->helpers->uniqueUsername( $post_data['user_email'], '-' );
			}
			if( $post_data['user_login'] === '' ) {
				$errors['user_login'] = __( 'Username field is required.', 'wp-builder-locale' );
			}
			if( $post_data['user_email'] === '' ) {
				$errors['user_email'] = __( 'Email address field is required.', 'wp-builder-locale' );
			}
			if( $post_data['user_pass'] === '' ) {
				$errors['user_pass'] = __( 'Password field is required.', 'wp-builder-locale' );
			}
			if( isset( $post_data['user_pass_confirmation'] ) && $post_data['user_pass_confirmation'] === '' ) {
				$errors['user_pass_confirmation'] = __( 'Confirm Password field is required.', 'wp-builder-locale' );
			}
			if( username_exists( $post_data['user_login'] ) ) {
				$errors['user_login'] = __( 'Username already exists.', 'wp-builder-locale' );
			}
			if( isset( $post_data['user_login'] ) && ! validate_username( $post_data['user_login'] ) ) {
				$errors['user_login'] = __( 'Username is not valid.', 'wp-builder-locale' );
			}
			if( email_exists( $post_data['user_email'] ) ) {
				$errors['user_email'] = __( 'Email address already registered.', 'wp-builder-locale' );
			}
			if( ! $this->helpers->isValidEmail( $post_data['user_email'] ) ) {
				$errors['user_email'] = __( 'Email address is not valid.', 'wp-builder-locale' );
			}

			if( isset( $post_data['user_pass_confirmation'] ) && $post_data['user_pass'] !== $post_data['user_pass_confirmation'] ) {
				$errors['user_pass'] = __( 'Password does not match.', 'wp-builder-locale' );
			}

			if( ! empty( $errors ) ) {
				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			} else {

				if( isset( $post_data['verify_email'] ) ) {

					$current_url = $this->helpers->site_url;
					if( isset( $post_data['current_url'] ) && $post_data['current_url'] != '' ) {
						$current_url = $post_data['current_url'];
					}

					$email_key = sha1( $post_data['user_email'] );
					$verification_key = sha1( implode( '|', $post_data ) . time() );
					$verification_link = $this->helpers->queryString( $current_url ) . "verify-email={$post_data['user_email']}&verify-key=" . $verification_key;
					set_transient( '_verify_email_' . $email_key, $post_data, 3600 * 24 );

					$message = sprintf( __( '<p>Click the link below to verify your email address and complete registration.</p><p><a href="%s">%s</a></p>', 'wp-builder-locale' ), $verification_link, $verification_link );

					$email_data = [
						'to' => $post_data['user_email'],
						'from_name' => $this->helpers->saved_options['core_from_email_name'],
						'from_email' => $this->helpers->saved_options['core_from_email_address'],
						'subject' => __( 'Verify your email address', 'wp-builder-locale' ),
						'message' => $message,
					];

					$this->helpers->sendEmail( $email_data );

					$success_message = __( 'Please check your email for verification link, the link will expire in next 24 hours.', 'wp-builder-locale' );
					if( isset( $post_data['success_message'] ) && $post_data['success_message'] != '' ) {
						$success_message = $post_data['success_message'];
					}
					$data['success'] = $success_message;

					return $this->helpers->apiResponse( $request, $data );
				} else {
					$user_info = $this->createUserAccount( $post_data );
					$return['success'] = $user_info;

					return $this->helpers->apiResponse( $request, $return );
				}
			}
		}

		public function verifyEmailAndCreateAccount( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );
			$account_data = get_transient( '_verify_email_' . sha1( $post_data['user_email'] ) );

			if( is_array( $account_data ) && ! empty( $account_data ) ) {
				if( email_exists( $account_data['user_email'] ) ) {
					$data['errors'] = __( 'Account with this email address is already registered.', 'wp-builder-locale' );

					return $this->helpers->apiResponse( $request, $data );
				}
				if( username_exists( $account_data['user_login'] ) ) {
					$data['errors'] = __( 'Account with this username is already registered.', 'wp-builder-locale' );

					return $this->helpers->apiResponse( $request, $data );
				}

				$user_info = $this->createUserAccount( $account_data );
				$data['success']['user_info'] = $user_info;
				$data['success']['redirect_url'] = $account_data['redirect_url'];

				delete_transient( '_verify_email_' . sha1( $post_data['user_email'] ) );

				return $this->helpers->apiResponse( $request, $data );
			}

			$data['errors'] = __( 'Invalid verification key', 'wp-builder-locale' );

			return $this->helpers->apiResponse( $request, $data );
		}

		public function doLogout( $request ) {
			global $current_user;
			$current_user = wp_get_current_user();
			$post_data = $this->helpers->apiRequestParams( $request );
			$return = [];
			$user_info = $this->helpers->getApiUserInfo( $request );
			if( ! empty( $user_info ) ) {
				$return['user_id'] = $user_info['ID'];
				$sessions = WP_Session_Tokens::get_instance( $user_info['ID'] );
				$sessions->destroy_all();
				delete_user_meta( $user_info['ID'], 'cjwpbldr_auth_token' );
				delete_user_meta( $user_info['ID'], 'cjwpbldr_user_salt' );
				delete_user_meta( $user_info['ID'], 'X-Authorization' );
				delete_user_meta( $user_info['ID'], 'X-Authorization-Expires' );
				$this->helpers->deleteCookie( 'cjwpbldr_token' );
			}
			$redirect_url = (isset( $post_data['redirect'] )) ? $post_data['redirect'] : $this->helpers->site_url;
			$return['redirect'] = $redirect_url;

			return $this->helpers->apiResponse( $request, $return );
		}

		public function sendResetPasswordMail( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );
			$errors = [];

			if( ! isset( $post_data['user'] ) || isset( $post_data['user'] ) && $post_data['user'] == '' ) {
				$errors['user'] = __( 'You must specify username, email address or mobile number.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$user_info = $this->helpers->userInfo( $post_data['user'] );

			if( empty( $user_info ) ) {
				$errors['user'] = __( 'No user found with this username or email address.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$user = get_user_by( 'id', $user_info['ID'] );
			$rp_key = get_password_reset_key( $user );
			$user_login = $user_info['user_login'];
			update_user_meta( $user_info['ID'], 'cjwpbldr_reset_user_pass_key', $rp_key );
			$reset_password_url = (isset( $post_data['redirect_url'] ) && $post_data['redirect_url'] != '') ? $post_data['redirect_url'] : site_url( 'wp-login.php' );
			$rp_link = $this->helpers->queryString( $reset_password_url ) . 'action=rp&key=' . $rp_key . '&login=' . $user_login;

			$subject = sprintf( __( '[%s] Password Reset', 'wp-builder-locale' ), get_bloginfo( 'name' ) );
			$message[] = __( '<p>Someone has requested a password reset for the following account:</p>', 'wp-builder-locale' );
			$message[] = sprintf( __( '<p><b>Site Name:</b> %s</p>', 'wp-builder-locale' ), get_bloginfo( 'name' ) );
			$message[] = sprintf( __( '<p><b>Username:</b> %s</p>', 'wp-builder-locale' ), $user_info['user_login'] );
			$message[] = __( '<p>If this was a mistake, just ignore this email and nothing will happen.</p>', 'wp-builder-locale' );
			$message[] = __( '<p>To reset your password, visit the following address:</p>', 'wp-builder-locale' );
			$message[] = sprintf( '<p><a href="%s">%s</a></p>', $rp_link, $rp_link );
			$email_data = [
				'to' => $user_info['user_email'],
				'from_name' => get_bloginfo( 'name' ),
				'from_email' => get_option( 'admin_email' ),
				'subject' => $subject,
				'message' => implode( '', $message )
			];
			$this->helpers->sendEmail( $email_data );
			$return['success'] = __( 'Check your email for the confirmation link.', 'wp-builder-locale' );

			//$return['success'] = $rp_link;

			return $this->helpers->apiResponse( $request, $return );
		}

		public function changePasswordWithKey( $request ) {
			global $wpdb;
			$return = [];
			$errors = [];
			$post_data = $this->helpers->apiRequestParams( $request );

			$return['success'] = __( 'Password changed successfully.', 'wp-builder-locale' );

			if( ! isset( $post_data['salt'] ) ) {
				$errors['user_pass'] = __( 'Invalid key, please try again.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			if( ! isset( $post_data['user_pass'] ) || isset( $post_data['user_pass'] ) && $post_data['user_pass'] == '' ) {
				$errors['user_pass'] = __( 'Password field is required.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			if( ! isset( $post_data['user_pass_confirmation'] ) || isset( $post_data['user_pass_confirmation'] ) && $post_data['user_pass_confirmation'] == '' ) {
				$errors['user_pass_confirmation'] = __( 'Please confirm your password.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			if( isset( $post_data['user_pass'] ) && isset( $post_data['user_pass_confirmation'] ) && $post_data['user_pass'] !== $post_data['user_pass_confirmation'] ) {
				$errors['user_pass'] = __( 'Password fields does not match confirm your password.', 'wp-builder-locale' );
				$errors['user_pass_confirmation'] = __( 'Password fields does not match confirm your password.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$user_by_key = $wpdb->get_row( "SELECT * FROM $wpdb->usermeta WHERE meta_key = 'cjwpbldr_reset_user_pass_key' AND meta_value = '{$post_data['salt']}'", ARRAY_A );

			if( is_null( $user_by_key ) ) {
				$errors['user_pass'] = __( 'Invalid key, please try again.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$user_info = $this->helpers->userInfo( $user_by_key['user_id'] );

			$user_by_verified_key = check_password_reset_key( $post_data['salt'], $user_info['user_login'] );

			if( $user_by_verified_key->ID == 0 ) {
				$errors['user_pass'] = __( 'Key verification failed, please try again.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			wp_set_password( $post_data['user_pass'], $user_by_key['user_id'] );
			delete_user_meta( $user_by_key['user_id'], 'cjwpbldr_reset_user_pass_key' );
			$return['success'] = __( 'Password changed, You can now login with your new password.', 'wp-builder-locale' );

			if( isset( $post_data['redirect_url'] ) && $post_data['redirect_url'] != '' ) {
				$return['success'] = [];
				$return['success']['redirect'] = $post_data['redirect_url'];
			}

			return $this->helpers->apiResponse( $request, $return );
		}

		public function emailSendVerification( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );

			if( ! isset( $post_data['user_email'] ) ) {
				$errors['user_email'] = __( 'Please specify an email address.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			if( isset( $post_data['user_email'] ) && ! $this->helpers->isValidEmail( $post_data['user_email'] ) ) {
				$errors['user_email'] = __( 'Please specify a valid email address.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$user = $user = get_user_by( 'email', $post_data['user_email'] );
			if( ! $user ) {
				$errors['user_email'] = __( 'No user found with this email address.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$code = rand( 111111, 999999 );
			$user_info = $this->helpers->userInfo( $user->ID );
			update_user_meta( $user_info['ID'], 'cjwpbldr_verify_email_code', $post_data['user_email'] . '#' . $code );
			// send verification email
			$signature = sprintf( __( '%s Team', 'wp-builder-locale' ), get_bloginfo( 'name' ) );
			$message = sprintf( __( '<p>Dear %s,</p><p>Please use this code to verify your email address.</p><p><span style="font-weight: bold; display: inline-block; background: #ededed; padding: 10px;">%s</span></p><p>Regards,<br>%s</p>', 'wp-builder-locale' ), $user_info['display_name'], $code, $signature );

			$from_name = get_bloginfo( 'name' );
			$from_email = get_option( 'admin-email' );

			if( isset( $this->helpers->saved_options['core_from_email_address'] ) && $this->helpers->saved_options['core_from_email_address'] != '' ) {
				$from_email = $this->helpers->saved_options['core_from_email_address'];
			}
			if( isset( $this->helpers->saved_options['core_from_email_name'] ) && $this->helpers->saved_options['core_from_email_name'] != '' ) {
				$from_name = $this->helpers->saved_options['core_from_email_name'];
			}

			$email_data = [
				'to' => $post_data['user_email'],
				'from_name' => $from_name,
				'from_email' => $from_email,
				'subject' => __( 'Verify your email address', 'wp-builder-locale' ),
				'message' => $message,
			];
			$this->helpers->sendEmail( $email_data );
			$return['success'] = __( 'Check your email for verification link.', 'wp-builder-locale' );

			return $this->helpers->apiResponse( $request, $return );
		}

		public function verifyEmail( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );

			if( ! isset( $post_data['user_email'] ) ) {
				$errors['user_email'] = __( 'Please specify an email address.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			if( isset( $post_data['user_email'] ) && ! $this->helpers->isValidEmail( $post_data['user_email'] ) ) {
				$errors['user_email'] = __( 'Please specify a valid email address.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			if( ! isset( $post_data['code'] ) ) {
				$errors['code'] = __( 'Invalid code, please check and try again.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$meta_value = $post_data['user_email'] . '#' . $post_data['code'];
			$check_user = $this->helpers->getUserByMeta( 'cjwpbldr_verify_email_code', $meta_value, 'info' );

			if( empty( $check_user ) ) {
				$errors['code'] = __( 'Invalid code, please check and try again.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			if( ! empty( $errors ) ) {
				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			delete_user_meta( $check_user['ID'], 'cjwpbldr_verify_email_code' );
			$return['success'] = __( 'Thank you for verifying your email address.', 'wp-builder-locale' );

			return $this->helpers->apiResponse( $request, $return );
		}

		/**
		 * @param array $post_data
		 *
		 * @return array
		 */
		public function createUserAccount( array $post_data ) {
			do_action( 'cjwpbldr_before_create_account', $post_data );

			$new_user_data = [
				'user_login' => $post_data['user_login'],
				'user_pass' => $post_data['user_pass'],
				'user_email' => $post_data['user_email'],
			];
			if( isset( $post_data['first_name'] ) && isset( $post_data['last_name'] ) ) {
				$new_user_data['display_name'] = $post_data['first_name'] . ' ' . $post_data['last_name'];
			}

			$new_user_id = wp_insert_user( $new_user_data );

			update_user_meta( $new_user_id, 'first_name', $post_data['first_name'] );
			update_user_meta( $new_user_id, 'last_name', $post_data['last_name'] );
			update_user_meta( $new_user_id, 'display_name', $new_user_data['display_name'] );

			update_user_meta( $new_user_id, 'cjwpbldr_user_salt', base64_encode( $post_data['user_pass'] ) );

			$this->helpers->updateUserInfo( $new_user_id, $post_data );

			do_action( 'cjwpbldr_after_create_account', $new_user_id );
			$notify = (isset( $post_data['notify'] )) ? $post_data['notify'] : 'both';
			wp_new_user_notification( $new_user_id, null, $notify );

			$user_info = $this->helpers->userInfo( $new_user_id, ['access_token'] );

			$authenticate = wp_authenticate( $user_info['user_login'], $post_data['user_pass'] );
			if( is_wp_error( $authenticate ) ) {
				$errors[] = __( 'Invalid credentials, please try again.', 'wp-builder-locale' );
			} else {

				if( isset( $post_data['auto_login'] ) && $post_data['auto_login'] == 1 ) {
					$this->helpers->login( $user_info['user_login'], $post_data['user_pass'] );
				}

				$this->helpers->setUserToken( $user_info );
				$user_info = $this->helpers->userInfo( $post_data['user_login'], ['access_token'] );
			}

			return $user_info;
		}

	}

	cjwpbldr_api_core_auth::getInstance();
}