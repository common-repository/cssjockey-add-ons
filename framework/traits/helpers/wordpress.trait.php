<?php

/**
 * Trait cjwpbldr_helpers_wordpress
 */
trait cjwpbldr_helpers_wordpress {

	/**
	 * @param null   $date_or_timestamp
	 * @param string $output
	 * @param bool   $timezone
	 *
	 * @return false|string
	 */
	public function wpDate( $date_or_timestamp = null, $output = 'date-time', $timezone = false ) {
		$default_timezone = ini_get( 'date.timezone' );
		if( $default_timezone == '' ) {
			$default_timezone = 'UTC';
		}
		$date_format = get_option( 'date_format' );
		$time_format = get_option( 'time_format' );
		if( is_null( $date_or_timestamp ) ) {
			$date_or_timestamp = time();
		}
		$timestamp = (is_numeric( $date_or_timestamp )) ? $date_or_timestamp : strtotime( $date_or_timestamp );
		if( $timezone ) {
			$timezone_string = get_option( 'timezone_string' );
			$timezone_string = ($timezone_string != '') ? $timezone_string : $default_timezone;
			if( $timezone_string == '' ) {
				$timezone_string = 'UTC';
			}
			date_default_timezone_set( $timezone_string );
			$timestamp = (is_numeric( $date_or_timestamp )) ? $date_or_timestamp : strtotime( $date_or_timestamp );
		}
		switch( $output ) {
			case 'date-time':
				$return = date( $date_format . ' ' . $time_format, $timestamp );
				break;
			case 'date':
				$return = date( $date_format, $timestamp );
				break;
			case 'time':
				$return = date( $time_format, $timestamp );
				break;
			default:
				$return = date( $output, $timestamp );
		}
		date_default_timezone_set( $default_timezone );

		return $return;
	}

	/**
	 *
	 */
	public function setWordPressTimezone() {
		$timezone_string = get_option( 'timezone_string' );
		$system_timezone_string = date_default_timezone_get();
		$set_timezone_string = ($timezone_string == '') ? $system_timezone_string : $timezone_string;
		if( $set_timezone_string == '' ) {
			$set_timezone_string = 'UTC';
		}
		date_default_timezone_set( $set_timezone_string );
	}

	/**
	 * @return string
	 */
	public function getWordPressTimezone() {
		return date_default_timezone_get();
	}

	/**
	 * @return mixed|void
	 */
	public function localizeScripts() {
		$locale_vars = $this->config( 'locale' );
		$module_locale_vars = [];
		$locale_vars = array_merge( $locale_vars, $module_locale_vars );
		$locale_vars['site_url'] = $this->site_url;
		$locale_vars['current_url'] = $this->currentUrl();
		$locale_vars['site_name'] = get_bloginfo( 'name' );
		if( is_admin() ) {
			$locale_vars['activate_url'] = $this->callbackUrl( 'core-cloud', true ) . 'show=activate';
		}
		$locale_vars['cjwpbldr_api_url'] = rest_url( 'cjwpbldr/' );
		$locale_vars['cjwpbldr_is_local'] = $this->isLocal();
		//delete_transient( '_cjwpbldr_next_daily' );
		$ping = get_transient( '_cjwpbldr_ping' );
		$locale_vars['ping'] = $ping;
		$locale_vars['wpApiSettings'] = [
			'root' => esc_url_raw( rest_url() ),
			'nonce' => wp_create_nonce( 'wp_rest' )
		];
		$locale = apply_filters( 'cjwpbldr_localize_scripts', $locale_vars );
		$locale['plugin-info'] = $this->config( 'plugin-info' );
		$locale['cjwpbldr_token'] = $_COOKIE['cjwpbldr_token'];

		return $locale;
	}

	/**
	 * @param       $user_login
	 * @param       $user_email
	 * @param       $user_pass
	 * @param array $all_profile_fields
	 * @param bool  $do_login
	 * @param null  $notify
	 *
	 * @return array|bool|int|WP_Error
	 */
	public function createNewAccount( $user_login, $user_email, $user_pass, $all_profile_fields = [], $do_login = false, $notify = null ) {
		$post_data = $all_profile_fields;
		do_action( 'cjwpbldr_before_register', $post_data );
		$username = $this->uniqueUsername( $user_login, '-' );
		if( ! $this->isStrongPassword( $user_pass ) ) {
			return [
				'errors' => __( 'Password must be at least 6 characters long and must contain one number, one uppercase letter, one lowercase letter and a special character.', 'wp-builder-locale' )
			];
		}
		$new_user_id = wp_create_user( $username, $user_pass, $user_email );
		if( is_wp_error( $new_user_id ) ) {
			$error = $new_user_id->get_error_message();

			return [
				'errors' => $error
			];
		}
		if( ! is_null( $notify ) && function_exists( 'wp_new_user_notification' ) ) {
			wp_new_user_notification( $new_user_id, null, $notify );
		}

		$user_data = [
			'ID' => $new_user_id,
			'user_nicename' => (isset( $post_data['user_nicename'] )) ? $post_data['user_nicename'] : '',
			'user_url' => (isset( $post_data['user_url'] )) ? $post_data['user_url'] : '',
			'display_name' => (isset( $post_data['display_name'] )) ? $post_data['display_name'] : '',
			'role' => (isset( $post_data['user_role'] )) ? $post_data['user_role'] : get_option( 'default_role' ),
		];
		wp_update_user( $user_data );
		foreach( $all_profile_fields as $meta_key => $meta_value ) {
			if( ! in_array( $meta_key, $this->usersTableFields() ) ) {
				$meta_value = (is_array( $meta_value )) ? serialize( $meta_value ) : $meta_value;
				update_user_meta( $new_user_id, $meta_key, esc_attr( $meta_value ) );
			}
		}
		$this->setUserToken( $new_user_id );
		//update_user_meta( $new_user_id, 'cjwpbldr_user_salt', base64_encode( $user_pass ) );
		update_user_meta( $new_user_id, 'cjwpbldr_auth_token', sha1( $user_login . ':' . $user_pass ) );

		do_action( 'cjwpbldr_after_register', $new_user_id );

		if( $do_login ) {
			return $this->login( $user_login, $user_pass );
		}

		return $new_user_id;
	}

	/**
	 * @param $user_login
	 * @param $user_pass
	 *
	 * @return bool|int
	 */
	public function login( $user_login, $user_pass ) {
		$user_info = $this->userInfo( $user_login );
		if( ! empty( $user_info ) ) {
			$user_login = $user_info['user_login'];
		}
		do_action( 'cjwpbldr_before_login', $user_login, $user_pass );
		$login_data = [];
		$login_data['user_login'] = $user_login;
		$login_data['user_password'] = $user_pass;
		$login_data['remember'] = true;
		$user = wp_signon( $login_data, is_ssl() );
		if( is_wp_error( $user ) ) {
			return false;
		} else {
			$this->setUserToken( $user->ID );
			//update_user_meta( $user->ID, 'cjwpbldr_user_salt', base64_encode( $user_pass ) );
			update_user_meta( $user->ID, 'cjwpbldr_auth_token', sha1( $user_login . ':' . $user_pass ) );
			do_action( 'cjwpbldr_after_login', $user_login, $user_pass );

			return $user->ID;
		}
	}

	/**
	 * @param      $user_identifiers
	 * @param null $new_password
	 *
	 * @return bool|int
	 */
	public function loginWithSalt( $user_identifiers, $new_password = null ) {
		$user_info = $this->userInfo( $user_identifiers );
		if( empty( $user_info ) ) {
			return false;
		}
		$password_hash = wp_hash_password( $new_password );
		if( ! isset( $user_info['cjwpbldr_auth_token'] ) || $user_info['cjwpbldr_auth_token'] == '' ) {
			$this->dbUpdate( 'users', ['user_pass' => $password_hash], 'ID', $user_info['ID'] );
			$this->setUserToken( $user_info['ID'] );
			//update_user_meta( $user_info['ID'], 'cjwpbldr_user_salt', base64_encode( $new_password ) );
			update_user_meta( $user_info['ID'], 'cjwpbldr_auth_token', sha1( $user_info['user_login'] . ':' . $new_password ) );
			$user_info = $this->userInfo( $user_identifiers, ['all'] );
		}

		$login_data = explode( ':', base64_decode( $user_info['cjwpbldr_auth_token'] ) );
		$login_data['user_login'] = $login_data[0];
		$login_data['user_password'] = $login_data[1];
		$login_data['remember'] = true;
		$user = wp_signon( $login_data, is_ssl() );

		if( is_wp_error( $user ) ) {
			if( ! function_exists( 'wp_password_change_notification' ) ) {
				function wp_password_change_notification() {
				}
			}
			wp_set_password( $new_password, $user_info['ID'] );
			$this->setUserToken( $user_info['ID'] );
			//update_user_meta( $user_info['ID'], 'cjwpbldr_user_salt', base64_encode( $new_password ) );
			update_user_meta( $user_info['ID'], 'cjwpbldr_auth_token', sha1( $user_info['user_login'] . ':' . $new_password ) );

			return $this->login( $user_info['user_login'], $new_password );
		} else {
			$this->setUserToken( $user->ID );
			//update_user_meta( $user->ID, 'cjwpbldr_user_salt', base64_encode( $login_data[0] ) );
			update_user_meta( $user->ID, 'cjwpbldr_auth_token', sha1( $login_data[0] . ':' . $login_data[1] ) );

			return $user->ID;
		}
	}

	/**
	 * @param        $method
	 * @param        $url
	 * @param array  $post_vars
	 * @param array  $args
	 * @param string $return
	 *
	 * @return array|WP_Error
	 */
	public function wpRemote( $method, $url, $post_vars = [], $args = [], $return = 'body' ) {
		global $current_user;
		$user_info = $this->userInfo( $current_user->ID );
		$args_array = [
			'method' => strtoupper( $method ),
			'timeout' => 120,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => [],
			'body' => $post_vars,
			'cookies' => [],
		];
		if( $this->containString( $this->site_url, $url ) ) {
			$args_array['headers']['X-WP-Nonce'] = wp_create_nonce( 'wp_rest' );
			if( is_array( $user_info ) && isset( $user_info['access_token'] ) && $user_info['access_token'] != '' ) {
				$args_array['headers']['X-Authorization-Token'] = $user_info['access_token'];
			} else {
				$args_array['headers']['X-Authorization-Token'] = '';
			}
		}
		if( ! empty( $args ) ) {
			foreach( $args as $key => $value ) {
				$args_array[ $key ] = $value;
			}
		}
		$args_array['headers']['X-Referer'] = $this->site_url;
		$response = wp_remote_post( $url, $args_array );

		if( is_wp_error( $response ) ) {
			if( is_array( $response->get_error_messages() ) ) {
				return ['errors' => implode( '', $response->get_error_messages() )];
			} else {
				$error_message = $response->get_error_message();

				return ['errors' => $error_message];
			}
		} else {
			if( $return != 'body' ) {
				return $response;
			} else {
				return $response['body'];
			}
		}
	}

	/**
	 * @param        $url
	 * @param array  $post_vars
	 * @param array  $args
	 * @param string $return
	 *
	 * @return array|WP_Error
	 */
	public function wpRemotePost( $url, $post_vars = [], $args = [], $return = 'body' ) {
		global $current_user;
		$user_info = $this->userInfo( $current_user->ID, ['access_token'] );
		$args_array = [
			'method' => 'POST',
			'timeout' => 120,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => [],
			'body' => $post_vars,
			'cookies' => [],
		];
		if( $this->containString( $this->site_url, $url ) && ! isset( $args['no-auth'] ) ) {
			$args_array['headers']['X-WP-Nonce'] = wp_create_nonce( 'wp_rest' );
			if( is_array( $user_info ) && isset( $user_info['access_token'] ) && $user_info['access_token'] != '' ) {
				$args_array['headers']['X-Authorization-Token'] = $user_info['access_token'];
			} else {
				$args_array['headers']['X-Authorization-Token'] = '';
			}
		}
		if( ! empty( $args ) ) {
			foreach( $args as $key => $value ) {
				$args_array[ $key ] = $value;
			}
		}
		$args_array['headers']['X-Referer'] = $this->site_url;

		$response = wp_remote_post( $url, $args_array );

		if( is_wp_error( $response ) ) {
			if( is_array( $response->get_error_messages() ) ) {
				return ['errors' => implode( '', $response->get_error_messages() )];
			} else {
				$error_message = $response->get_error_message();

				return ['errors' => $error_message];
			}
		} else {
			if( $return != 'body' ) {
				return $response;
			} else {
				return $response['body'];
			}
		}
	}

	/**
	 * @param        $url
	 * @param null   $request_vars
	 * @param null   $args
	 * @param string $return
	 *
	 * @return array|WP_Error
	 */
	public function wpRemoteGet( $url, $request_vars = null, $args = null, $return = 'body' ) {
		global $current_user;
		$user_info = $this->userInfo( $current_user->ID, ['access_token'] );
		$args_array = [
			'method' => 'GET',
			'timeout' => 120,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => [],
			'body' => (is_array( $request_vars ) && ! empty( $request_vars )) ? $request_vars : [],
			'cookies' => [],
		];
		if( $this->containString( $this->site_url, $url ) && ! isset( $args['no-auth'] ) ) {
			$args_array['headers']['X-WP-Nonce'] = wp_create_nonce( 'wp_rest' );
			if( is_array( $user_info ) && isset( $user_info['access_token'] ) && $user_info['access_token'] != '' ) {
				$args_array['headers']['X-Authorization-Token'] = $user_info['access_token'];
			} else {
				$args_array['headers']['X-Authorization-Token'] = '';
			}
		}
		if( is_array( $args ) && ! empty( $args ) ) {
			foreach( $args as $key => $value ) {
				$args_array[ $key ] = $value;
			}
		}
		$args_array['headers']['X-Referer'] = $this->site_url;
		$response = wp_remote_get( $url, $args_array );
		if( $return != 'body' ) {
			return $response;
		}

		if( is_wp_error( $response ) ) {
			if( is_array( $response->get_error_messages() ) ) {
				return ['errors' => implode( '', $response->get_error_messages() )];
			} else {
				$error_message = $response->get_error_message();

				return ['errors' => $error_message];
			}
		} else {
			if( $return != 'body' ) {
				return $response;
			} else {
				return $response['body'];
			}
		}
	}

	/**
	 *
	 */
	public function getMenuLocations() {
		$menu_locations = get_registered_nav_menus();
		update_option( 'cjwpbldr_user_menus', $menu_locations );
	}

	/**
	 * @param int $limit
	 * @param     $total_items
	 * @param int $page
	 *
	 * @return mixed
	 */
	public function pagination( $limit = 10, $total_items, $page = 0 ) {
		$current_page = (int) $page;
		$total_pages = ceil( $total_items / $limit );
		$return['limit'] = $limit;
		$return['offset'] = ($page > 0) ? $limit * ($current_page - 1) : 0;
		$return['current_page'] = $current_page;
		$return['total_pages'] = $total_pages;
		if( $total_pages > $current_page ) {
			$return['next_page'] = $page + 1;
		}
		if( $page <= $total_pages && $page > 1 ) {
			$return['previous_page'] = $page - 1;
		}

		return $return;
	}

	/**
	 * @param array  $email_data
	 * @param array  $attachments
	 * @param string $service
	 * @param array  $more_headers
	 *
	 * @return bool
	 */
	public function sendEmail( $email_data = [], $attachments = [], $service = 'wp-mail', $more_headers = [] ) {
		if( empty( $email_data ) ) {
			return false;
		}
		$to = $email_data['to'];
		$from_name = stripcslashes( html_entity_decode( $email_data['from_name'], ENT_QUOTES ) );
		$from = $email_data['from_email'];
		$subject = stripcslashes( html_entity_decode( $email_data['subject'], ENT_QUOTES ) );
		$content = $email_data['message'];
		$content_above = '';
		$content_below = '';
		$msg = apply_filters( 'cjwpbldr_content_above_email', $content_above, 10, 1 );
		$msg .= $content;
		$msg .= apply_filters( 'cjwpbldr_content_below_email', $content_below, 10, 1 );
		$message = $msg;

		if( $service == 'wp-mail' ) {
			$headers[] = "From: {$from_name} <{$from}>";
			$headers[] = "Return-Path: {$from}";
			$headers[] = "MIME-Version: 1.0";

			if( ! empty( $more_headers ) ) {
				foreach( $more_headers as $key => $value ) {
					$headers[] = $value;
				}
			}

			if( isset( $email_data['headers'] ) ) {
				foreach( $email_data['headers'] as $h_key => $h_value ) {
					$headers[] = "{$h_key}: {$h_value}";
				}
			}
			add_filter( 'wp_mail_content_type', [$this, 'setHtmlContentTypeForEmail'] );
			add_filter( 'wp_mail_charset', [$this, 'setCharsetForEmail'] );
			$return = wp_mail( $to, $subject, $message, $headers, $attachments );
			remove_filter( 'wp_mail_content_type', [$this, 'setHtmlContentTypeForEmail'] );
			remove_filter( 'wp_mail_charset', [$this, 'setCharsetForEmail'] );

			return $return;
		} else {
			$headers = "From: {$from_name} <{$from}>" . "\r\n\\";
			$headers .= "Return-Path: {$from}\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=utf-8\r\n";
			if( isset( $email_data['headers'] ) ) {
				foreach( $email_data['headers'] as $h_key => $h_value ) {
					$headers .= "{$h_key}: {$h_value}";
				}
			}

			return mail( $to, $subject, $message, $headers );
		}
	}

	/**
	 * @return string
	 */
	public function setHtmlContentTypeForEmail() {
		return 'text/html';
	}

	/**
	 * @return string
	 */
	public function setCharsetForEmail() {
		return 'utf8';
	}

	/**
	 * @param $to
	 * @param $subject
	 * @param $array
	 */
	public function debugEmail( $to, $subject, $array ) {
		$message = '';
		if( is_array( $array ) ) {
			foreach( $array as $key => $value ) {
				$message .= $key . ' => ' . $value . "\n <br>";
			}
		}
		$email_data = [
			'to' => $to,
			'from_name' => 'Debug Info - ',
			get_bloginfo( 'name' ),
			'from_email' => 'admin@cssjockey.com',
			'subject' => $subject,
			'message' => $message,
		];
		$this->sendEmail( $email_data );
	}

	/**
	 * @param $url
	 *
	 * @return false|string
	 */
	public function getFileIcon( $url ) {
		$mimes = get_allowed_mime_types();
		$file_ext = explode( '.', basename( $url ) );
		if( is_array( $file_ext ) ) {
			$file_ext = $file_ext[ count( $file_ext ) - 1 ];
			foreach( $mimes as $type => $mime ) {
				if( strstr( $type, $file_ext ) !== false ) {
					return wp_mime_type_icon( $mime );
				}
			}
		}

		return '';
	}

	/**
	 * @param $mime_type
	 *
	 * @return bool
	 */
	public function isFileMimeTypeAllowed( $mime_type ) {
		$mimes = get_allowed_mime_types();

		return in_array( $mime_type, $mimes );
	}

	/**
	 * @param      $content
	 * @param bool $auto_p
	 *
	 * @return string
	 */
	public function removeAutoP( $content, $auto_p = false ) {
		if( $auto_p ) { // Possible to use !preg_match('('.WPBMap::getTagsRegexp().')', $content)
			$content = wpautop( preg_replace( '/<\/?p\>/', "\n", $content ) . "\n" );
		}

		return do_shortcode( shortcode_unautop( $content ) );
	}

	/**
	 * @param string $separator
	 *
	 * @return string
	 */
	public function breadcrumbs( $separator = ' &raquo; ' ) {
		/* === OPTIONS === */
		$text['home'] = __( 'Home', 'wp-builder-locale' ); // text for the 'Home' link
		$text['blog'] = __( 'Blog', 'wp-builder-locale' ); // text for the 'Home' link
		$text['category'] = __( 'Archive by Category "%s"', 'wp-builder-locale' ); // text for a category page
		$text['tax'] = __( 'Archive for "%s"', 'wp-builder-locale' ); // text for a taxonomy page
		$text['search'] = __( 'Search Results for "%s"', 'wp-builder-locale' ); // text for a search results page
		$text['tag'] = __( 'Posts Tagged "%s"', 'wp-builder-locale' ); // text for a tag page
		$text['author'] = __( 'Articles Posted by %s', 'wp-builder-locale' ); // text for an author page
		$text['404'] = __( 'Error 404', 'wp-builder-locale' ); // text for the 404 page

		$showCurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show
		$showOnHome = 1; // 1 - show breadcrumbs on the homepage, 0 - don't show
		$delimiter = '<span class="cj-separator">' . $separator . '</span>'; // delimiter between crumbs
		//$delimiter = ' <span class="cj-icon cj-is-small cj-p-5 cj-opacity-50"><i class="fa fa-angle-double-right"></i></span> '; // delimiter between crumbs
		$before = '<span class="cj-is-current">'; // tag before the current crumb
		$after = '</span>'; // tag after the current crumb
		/* === END OF OPTIONS === */

		$display = '';

		global $post;
		$page_for_posts = get_option( 'page_for_posts' );
		$homeLink = get_bloginfo( 'url' ) . '/';
		$blogLink = get_permalink( $page_for_posts );
		$linkBefore = '<span typeof="v:Breadcrumb">';
		$linkAfter = '</span>';
		$linkAttr = ' rel="v:url" property="v:title"';
		$link = $linkBefore . '<a' . $linkAttr . ' href="%1$s">%2$s</a>' . $linkAfter;

		if( is_front_page() && is_home() ) {
			$display .= '<span class="cj-breadcrumbs"><a href="' . $homeLink . '">' . $text['home'] . '</a> ' . $delimiter . ' ' . $text['blog'] . '</span>';
		} elseif( is_front_page() ) {
			if( $showOnHome == 1 ) {
				$display .= '<span class="cj-breadcrumbs"><a href="' . $homeLink . '">' . $text['home'] . '</a></span>';
			}
		} elseif( is_home() ) {
			$display .= '<span class="cj-breadcrumbs"><a href="' . $homeLink . '">' . $text['home'] . '</a> ' . $delimiter . ' ' . $text['blog'] . '</span>';
		} else {
			$display .= '<span class="cj-breadcrumbs" xmlns:v="http://rdf.data-vocabulary.org/#">' . sprintf( $link, $homeLink, $text['home'] ) . $delimiter;

			if( is_category() ) {
				$thisCat = get_category( get_query_var( 'cat' ), false );
				if( $thisCat->parent != 0 ) {
					$cats = get_category_parents( $thisCat->parent, true, $delimiter );
					$cats = str_replace( '<a', $linkBefore . '<a' . $linkAttr, $cats );
					$cats = str_replace( '</a>', '</a>' . $linkAfter, $cats );
					$display .= $cats;
				}
				$display .= $before . sprintf( $text['category'], single_cat_title( '', false ) ) . $after;
			} elseif( is_tax() ) {
				$thisCat = get_category( get_query_var( 'cat' ), false );
				if( $thisCat->parent != 0 ) {
					$cats = get_category_parents( $thisCat->parent, true, $delimiter );
					$cats = str_replace( '<a', $linkBefore . '<a' . $linkAttr, $cats );
					$cats = str_replace( '</a>', '</a>' . $linkAfter, $cats );
					$display .= $cats;
				}
				$display .= $before . sprintf( $text['tax'], single_cat_title( '', false ) ) . $after;
			} elseif( is_search() ) {
				$display .= $before . sprintf( $text['search'], get_search_query() ) . $after;
			} elseif( is_day() ) {
				$display .= sprintf( $link, get_year_link( get_the_time( 'Y' ) ), get_the_time( 'Y' ) ) . $delimiter;
				$display .= sprintf( $link, get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ), get_the_time( 'F' ) ) . $delimiter;
				$display .= $before . get_the_time( 'd' ) . $after;
			} elseif( is_month() ) {
				$display .= sprintf( $link, get_year_link( get_the_time( 'Y' ) ), get_the_time( 'Y' ) ) . $delimiter;
				$display .= $before . get_the_time( 'F' ) . $after;
			} elseif( is_year() ) {
				$display .= $before . get_the_time( 'Y' ) . $after;
			} elseif( is_single() && ! is_attachment() ) {
				if( get_post_type() != 'post' ) {
					$post_type = get_post_type_object( get_post_type() );
					$slug = $post_type->rewrite;
					printf( $link, $homeLink . '/' . $slug['slug'] . '/', $post_type->labels->singular_name );
					if( $showCurrent == 1 ) {
						$display .= $delimiter . $before . get_the_title() . $after;
					}
				} else {
					$cat = get_the_category();
					$cat = $cat[0];
					$cats = get_category_parents( $cat, true, $delimiter );
					if( $showCurrent == 0 ) {
						$cats = preg_replace( "#^(.+)$delimiter$#", "$1", $cats );
					}
					$cats = str_replace( '<a', $linkBefore . '<a' . $linkAttr, $cats );
					$cats = str_replace( '</a>', '</a>' . $linkAfter, $cats );
					$display .= $cats;
					if( $showCurrent == 1 ) {
						$display .= $before . get_the_title() . $after;
					}
				}
			} elseif( ! is_single() && ! is_page() && get_post_type() != 'post' && ! is_404() ) {
				$post_type = get_post_type_object( get_post_type() );
				$display .= $before . $post_type->labels->singular_name . $after;
			} elseif( is_attachment() ) {
				$parent = get_post( $post->post_parent );
				$cat = get_the_category( $parent->ID );
				$cat = $cat[0];
				$cats = get_category_parents( $cat, true, $delimiter );
				$cats = str_replace( '<a', $linkBefore . '<a' . $linkAttr, $cats );
				$cats = str_replace( '</a>', '</a>' . $linkAfter, $cats );
				$display .= $cats;
				printf( $link, get_permalink( $parent ), $parent->post_title );
				if( $showCurrent == 1 ) {
					$display .= $delimiter . $before . get_the_title() . $after;
				}
			} elseif( is_page() && ! $post->post_parent ) {
				if( $showCurrent == 1 ) {
					$display .= $before . get_the_title() . $after;
				}
			} elseif( is_page() && $post->post_parent ) {
				$parent_id = $post->post_parent;
				$breadcrumbs = [];
				while( $parent_id ) {
					$page = get_post( $parent_id );
					$breadcrumbs[] = sprintf( $link, get_permalink( $page->ID ), get_the_title( $page->ID ) );
					$parent_id = $page->post_parent;
				}
				$breadcrumbs = array_reverse( $breadcrumbs );
				for( $i = 0; $i < count( $breadcrumbs ); $i ++ ) {
					$display .= $breadcrumbs[ $i ];
					if( $i != count( $breadcrumbs ) - 1 ) {
						$display .= $delimiter;
					}
				}
				if( $showCurrent == 1 ) {
					$display .= $delimiter . $before . get_the_title() . $after;
				}
			} elseif( is_tag() ) {
				$display .= $before . sprintf( $text['tag'], single_tag_title( '', false ) ) . $after;
			} elseif( is_author() ) {
				global $author;
				$userdata = get_userdata( $author );
				$display .= $before . sprintf( $text['author'], $userdata->display_name ) . $after;
			} elseif( is_404() ) {
				$display .= $before . $text['404'] . $after;
			}

			if( get_query_var( 'paged' ) ) {
				if( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) {
					$display .= ' (';
				}
				$display .= __( 'Page' ) . ' ' . get_query_var( 'paged' );
				if( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) {
					$display .= ')';
				}
			}

			$display .= '</span>';
		}

		return $display;
	}

	/**
	 * @param string $content
	 *
	 * @return string
	 */
	public function getWysiwygOutput( $content = '' ) {
		global $wp_embed;
		$content = $wp_embed->autoembed( $content );
		$content = $wp_embed->run_shortcode( $content );
		$content = wpautop( $content );
		$content = do_shortcode( $content );

		return $content;
	}

	/**
	 * @return bool
	 */
	public function isArchive() {
		global $wp_query;
		if( ! is_front_page() && is_home() ) {
			return true;
		} elseif( is_front_page() ) {
			return false;
		} elseif( is_home() ) {
			return true;
		} elseif( is_archive() || is_date() || is_year() || is_month() || is_day() || is_time() || is_author() || is_category() || is_tag() || is_tax() || is_search() || is_post_type_archive() ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @param string $key_value
	 * @param string $name_value
	 *
	 * @return array
	 */
	public function wpMenusArray( $key_value = 'slug', $name_value = 'name' ) {
		$return = [];
		$menus = wp_get_nav_menus();

		$none = ['' => __( 'None', 'wp-builder-locale' )];

		if( ! empty( $menus ) ) {
			foreach( $menus as $menu ) {
				$return[ $menu->$key_value ] = $menu->$name_value;
			}
		}
		$return = array_merge( $none, $return );

		return $return;
	}

	/**
	 * @return array|mixed|void
	 */
	public function getActivePlugins() {
		if( is_multisite() ) {
			$network_plugins = get_site_option( 'active_sitewide_plugins' );
			$network_plugins = array_flip( $network_plugins );
			$single_plugins = get_option( 'active_plugins' );

			return array_merge( $network_plugins, $single_plugins );
		} else {
			return get_option( 'active_plugins' );
		}
	}

	/**
	 * @param string $menu_slug
	 * @param array  $block_info
	 * @param bool   $is_wrapped
	 *
	 * @return string
	 */
	public function renderNavMenu( $menu_slug = '', $block_info = [], $is_wrapped = true ) {
		if( $menu_slug == '' || (is_array( $menu_slug ) && empty( $menu_slug )) ) {
			if( current_user_can( 'manage_options' ) ) {
				if( ! empty( $block_info ) ) {
					$edit_url = admin_url( 'post.php?post=' . $block_info['ID'] . '&action=edit' );
					$edit_link = '<a title="' . __( 'Select Menu', 'wp-builder-locale' ) . '" target="_blank" href="' . $edit_url . '">' . __( 'Select Menu', 'wp-builder-locale' ) . '</a>';

					return '<div class="cj-navbar-item">' . $edit_link . '</div>';
				} else {
					return '<div class="cj-navbar-item"><a href="#" title="' . __( 'Select Menu', 'wp-builder-locale' ) . '">' . __( 'Select Menu', 'wp-builder-locale' ) . '</a></div>';
				}
			}
		}

		$options = [
			'menu' => $menu_slug,
			'theme_location' => '',
			'container' => false,
			'walker' => cjwpbldr_menu_walker_wp::getInstance(),
			'fallback_cb' => false,
			'depth' => 11,
			'echo' => 0,
			'items_wrap' => ($is_wrapped) ? '<ul class="menu-items">%3$s</ul>' : '%3$s',
		];
		$return[] = wp_nav_menu( $options );

		return implode( "\n", $return );
	}

	/**
	 * getWordPressConditionalTitles()
	 *
	 * This function returns the titles based on the page you are visiting.
	 *
	 * @param array|string $title_prefix
	 * @usage @param $title_prefix = [
	 *                                      'blog' => $title_prefix,
	 *                                      'single-post' => $title_prefix,
	 *                                      'single-page' => $title_prefix,
	 *                                      'post-type-archive' => $title_prefix,
	 *                                      'category' => $title_prefix,
	 *                                      'tag' => $title_prefix,
	 *                                      'tax' => $title_prefix,
	 *                                      'author' => $title_prefix,
	 *                                      'month' => $title_prefix,
	 *                                      'year' => $title_prefix,
	 *                                      'search' => $title_prefix,
	 *                                      'page-404' => $title_prefix,
	 *                                      ];
	 *                                      $title_prefix_args parameter can be an array or a string. If it is string and only one value is given
	 *                                      that will be used for all the prefixes of the title returned by this function
	 *
	 * @param string       $title_separator | This will be used for Month and Day Archive to seprate the dates
	 *                                      For example: Month - Day - Year. The order can be different.
	 *
	 * @return string|void
	 */
	public function getWordPressConditionalTitles( $title_prefix = '', $title_separator = ' - ', $print = false ) {

		global $wp_locale;

		$title = '';

		$title_prefix_args = ['blog' => 'Blog', 'single-post' => '', 'single-page' => '', 'post-type-archive' => '', 'category' => '', 'tag' => '', 'tax' => '', 'author' => '', 'month' => '', 'year' => '', 'search' => '', 'page-404' => '',];

		if( is_array( $title_prefix ) ) {
			$title_prefix_args = $title_prefix;
		} else {
			if( '' != $title_prefix ) {
				$title_prefix_args = [
					'blog' => [
						'prefix' => $title_prefix,
						'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut.'
					],
					'single-post' => $title_prefix,
					'single-page' => $title_prefix,
					'post-type-archive' => $title_prefix,
					'category' => $title_prefix,
					'tag' => $title_prefix,
					'tax' => $title_prefix,
					'author' => $title_prefix,
					'month' => $title_prefix,
					'year' => $title_prefix,
					'search' => $title_prefix,
					'page-404' => $title_prefix,
				];
			}
		}

		if( $print ) {
			echo '<pre>';
			print_r( $title_prefix_args );
			echo '</pre>';
		}

		if( is_home() && ! is_front_page() ) {
			$title = $title_prefix_args['blog'];
		}

		// If there is a post
		if( is_single() ) {
			$title = single_post_title( $title_prefix_args['single-post'], false );
		}

		// If there is a page
		if( is_page() && ! is_front_page() ) {
			$title = single_post_title( $title_prefix_args['single-page'], false );
		}

		// If there's a post type archive
		if( is_post_type_archive() ) {
			$post_type = get_query_var( 'post_type' );
			if( is_array( $post_type ) ) {
				$post_type = reset( $post_type );
			}
			$post_type_object = get_post_type_object( $post_type );
			if( ! $post_type_object->has_archive ) {
				$title = post_type_archive_title( $title_prefix_args['post-type-archive'], false );
			}
		}

		// If there's a category
		if( is_category() ) {
			$title = single_term_title( $title_prefix_args['category'], false );
		}

		// If there's a tag
		if( is_tag() ) {
			$title = single_term_title( $title_prefix_args['tag'], false );
		}

		// If there's a taxonomy
		if( is_tax() ) {
			$term = get_queried_object();
			if( $term ) {
				$tax = get_taxonomy( $term->taxonomy );
				$title = single_term_title( $title_prefix_args['tax'], false );
			}
		}

		// If there's an author
		if( is_author() && ! is_post_type_archive() ) {
			$author = get_queried_object();
			if( $author ) {
				$title = $title_prefix_args['author'] . $author->display_name;
			}
		}

		// Post type archives with has_archive should override terms.
		if( is_post_type_archive() && $post_type_object->has_archive ) {
			$title = post_type_archive_title( $title_prefix_args['post-type-archive'], false );
		}

		// If there's a month
		if( is_archive() && ! empty( $m ) ) {
			$my_year = substr( $m, 0, 4 );
			$my_month = $wp_locale->get_month( substr( $m, 4, 2 ) );
			$my_day = intval( substr( $m, 6, 2 ) );
			$title = $title_prefix_args['month'] . $my_year . ($my_month ? $title_separator . $my_month : '') . ($my_day ? $title_separator . $my_day : '');
		}

		// If there's a year
		if( is_archive() && ! empty( $year ) ) {
			$title = $title_prefix_args['year'] . $year;
			if( ! empty( $monthnum ) ) {
				$title .= $title_separator . $wp_locale->get_month( $monthnum );
			}
			if( ! empty( $day ) ) {
				$title .= $title_separator . zeroise( $day, 2 );
			}
		}

		// If it's a search
		if( is_search() ) {
			/* translators: 1: separator, 2: search phrase */
			$search_title_prefix = ($title_prefix_args['search'] != '') ? $title_prefix_args['search'] : 'Search Results';
			$title = sprintf( __( '%s', 'wp-builder-locale' ), $search_title_prefix );
		}

		// If it's a 404 page
		if( is_404() ) {
			$page_404_title_prefix = ($title_prefix_args['page-404'] != '') ? $title_prefix_args['page-404'] : 'Page Not Found';
			$title = sprintf( __( '%s', 'wp-builder-locale' ), $page_404_title_prefix );
		}

		return $title;
	}

	/**
	 * getWordPressConditionalTitleDesc()
	 *
	 * This function returns the descriptions under titles based on the page you are visiting.
	 *
	 * @param array|string $description
	 * @param bool         $print
	 *
	 * @return string|void
	 * @usage @param $description_args = [
	 *    'blog' => $title_prefix,
	 *    'single-post' => $title_prefix,
	 *    'single-page' => $title_prefix,
	 *    'post-type-archive' => $title_prefix,
	 *    'category' => $title_prefix,
	 *    'tag' => $title_prefix,
	 *    'tax' => $title_prefix,
	 *    'author' => $title_prefix,
	 *    'month' => $title_prefix,
	 *    'year' => $title_prefix,
	 *    'search' => $title_prefix,
	 *    'page-404' => $title_prefix,
	 * ];
	 * $description_args parameter can be an array or a string. If it is string and only one value is given
	 * that will be used for all the description of the title returned by this function
	 *
	 */
	public function getWordPressConditionalTitleDesc( $description = '', $print = false ) {

		$description_args = [
			'blog' => 'Blog',
			'single-post' => '',
			'single-page' => '',
			'post-type-archive' => '',
			'category' => '',
			'tag' => '',
			'tax' => '',
			'author' => '',
			'month' => '',
			'year' => '',
			'search' => '',
			'page-404' => ''
		];

		if( is_array( $description ) ) {
			$description_args = $description;
		} else {
			if( '' != $description ) {
				$description_args = [
					'blog' => $description,
					'single-post' => $description,
					'single-page' => $description,
					'post-type-archive' => $description,
					'category' => $description,
					'tag' => $description,
					'tax' => $description,
					'author' => $description,
					'month' => $description,
					'year' => $description,
					'search' => $description,
					'page-404' => $description,
				];
			}
		}

		if( $print ) {
			echo '<pre>';
			print_r( $description_args );
			echo '</pre>';
		}

		if( is_home() && ! is_front_page() ) {
			$description = $description_args['blog'];
		}

		// If there is a post
		if( is_single() ) {
			$description = $description_args['single-post'];
		}

		// If there is a page
		if( is_page() && ! is_front_page() ) {
			$description = $description_args['single-page'];
		}

		// If there's a post type archive
		if( is_post_type_archive() ) {
			$post_type = get_query_var( 'post_type' );
			if( is_array( $post_type ) ) {
				$post_type = reset( $post_type );
			}
			$post_type_object = get_post_type_object( $post_type );
			if( ! $post_type_object->has_archive ) {
				$description = $description_args['post-type-archive'];
			}
		}

		// If there's a category
		if( is_category() ) {
			$category = get_queried_object();
			$category_desc = category_description( $category->term_id );
			$description = ($category_desc == '') ? $description_args['category'] : strip_tags( $category_desc );
		}

		// If there's a tag
		if( is_tag() ) {
			$tag = get_queried_object();
			$tag_desc = tag_description( $tag->term_id );
			$description = ($tag_desc == '') ? $description_args['tag'] : strip_tags( $tag_desc );
		}

		// If there's a taxonomy
		if( is_tax() ) {
			$term = get_queried_object();
			$term_desc = term_description( $term->term_id, $term->taxonomy );
			if( $term ) {
				$description = ($term_desc == '') ? $description_args['tax'] : strip_tags( $term_desc );
			}
		}

		// If there's an author
		if( is_author() && ! is_post_type_archive() ) {
			$author = get_queried_object();
			if( $author ) {
				$description = $description_args['author'];
			}
		}

		// Post type archives with has_archive should override terms.
		if( is_post_type_archive() && $post_type_object->has_archive ) {
			$description = $description_args['post-type-archive'];
		}

		// If there's a month
		if( is_archive() && ! empty( $m ) ) {
			$description = $description_args['month'];
		}

		// If there's a year
		if( is_archive() && ! empty( $year ) ) {
			$description = $description_args['year'];
		}

		// If it's a search
		if( is_search() ) {
			$description = $description_args['search'];
		}

		// If it's a 404 page
		if( is_404() ) {
			$description = $description_args['page-404'];
		}

		return $description;
	}

	/**
	 * @param array  $text_args
	 * @param string $max_pages
	 * @param string $current_page
	 * @param string $container_classes
	 *
	 * @return string
	 */
	public function numberedPagination( $text_args = [], $max_pages = '', $current_page = '', $container_classes = '' ) {
		global $wp_query;

		if( $wp_query->max_num_pages <= 1 ) {
			return '';
		}

		$prev_text = ( ! empty( $text_args ) && array_key_exists( 'prev_text', $text_args )) ? $text_args['prev_text'] : '<i class="fas fa-angle-left" aria-hidden="true"></i>';
		$next_text = ( ! empty( $text_args ) && array_key_exists( 'next_text', $text_args )) ? $text_args['next_text'] : '<i class="fas fa-angle-right" aria-hidden="true"></i>';
		$before_page_number = ( ! empty( $text_args ) && array_key_exists( 'before_page_number', $text_args )) ? $text_args['before_page_number'] : '<span class="pagination-link">';
		$after_page_number = ( ! empty( $text_args ) && array_key_exists( 'after_page_number', $text_args )) ? $text_args['after_page_number'] : '</span>';

		$max_pages = ('' != $max_pages) ? $max_pages : $wp_query->max_num_pages;
		$current_page = ('' != $current_page) ? $current_page : max( 1, get_query_var( 'paged' ) );

		$big = 999999999; // need an unlikely integer
		$links = paginate_links( [
			'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			'format' => '?paged=%#%',
			'current' => $current_page,
			'total' => $max_pages,
			'type' => 'list',
			'before_page_number' => '',
			'after_page_number' => '',
			'prev_text' => $prev_text,
			'next_text' => $next_text,
		] );

		$links = str_replace( '<a', '<a title="#" ', $links );

		$return[] = '<div class="post-pagination ' . $container_classes . '">';
		$return[] = $links;
		$return[] = '</div>';

		return implode( '', $return );
	}

	/**
	 * @return array
	 */
	public function commentsFormArgs() {
		$commenter = wp_get_current_commenter();
		$req = get_option( 'require_name_email' );
		$aria_req = ($req ? " aria-required='true'" : '');
		$consent = empty( $commenter['comment_author_email'] ) ? '' : ' checked="checked"';

		$fields = [
			'author' => '<div class="cj-field"><label for="author" class="cj-label">' . __( 'Name', 'wp-builder-locale' ) . ' ' . ($req ? '<span class="required">*</span>' : '') . '</label><input class="cj-input" id="author"  name="author" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" ' . $aria_req . '></div>',
			'email' => '<div class="cj-field"><label for="email" class="cj-label">' . __( 'Email address', 'wp-builder-locale' ) . ' ' . ($req ? '<span class="required">*</span>' : '') . '</label><input class="cj-input" id="email" name="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" ' . $aria_req . '></div>',
			'url' => '<div class="cj-field"><label for="url" class="cj-label">' . __( 'Website', 'wp-builder-locale' ) . ' ' . ($req ? '<span class="required">*</span>' : '') . '</label><input class="cj-input" id="url" name="url" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" ' . $aria_req . '></div>',
			'cookies' => '<p class="comment-form-cookies-consent"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"' . $consent . ' />' . '<label class="cj-ml-10" for="wp-comment-cookies-consent">' . __( 'Save my name, email, and website in this browser for the next time I comment.' ) . '</label></p>',
		];
		$args = [
			'id_form' => 'commentform',
			'class_form' => 'comment-form',
			'id_submit' => 'submit',
			'class_submit' => 'cj-button cj-is-primary cj-is-medium',
			'name_submit' => 'submit',
			'title_reply' => __( 'Leave a Reply' ),
			'title_reply_to' => __( 'Leave a Reply to %s' ),
			'cancel_reply_link' => __( 'Cancel Reply' ),
			'label_submit' => __( 'Post Comment' ),
			'format' => 'html5',
			'comment_field' => '<div class="cj-field comment-form-comment"><label class="cj-label" for="comment">' . _x( 'Comment', 'noun' ) . '</label><textarea class="cj-textarea" id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></div>',
			'fields' => apply_filters( 'comment_form_default_fields', $fields ),
		];

		return $args;
	}

	/**
	 * Extract WordPress shortcodes from post_content or any content provided
	 * to the function
	 *
	 * @param string $shortcode_name | The name of the shortcode
	 * @param string $content        | Content to extract the shortcode from
	 * @param bool   $match_all      | Wether to match the occurence of the same shortcode
	 *                               all over the content or not. Defaults to false
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function getShortcodesFromContent( $shortcode_name = '', $content = '', $match_all = false ) {
		if( '' == $shortcode_name && $content == '' ) {
			return [];
		}

		$matches = [];

		/**
		 * Expression explained: Find match starting with a square bracket '\['
		 * proceeding with the name of the shortcode, followed by white space
		 * '\s'. Followed by any set of characters '.*?' finally ending with
		 * a closing square bracket '\]'
		 */
		$expression = '/\[' . $shortcode_name . '\s.*?\]/';

		if( $match_all ) {
			preg_match_all( $expression, $content, $matches );
		} else {
			preg_match( $expression, $content, $matches );
		}

		return $matches;
	}

	/**
	 * This function is hooked to 'comments_template' WordPress filter
	 * in main framework cjwpbldr_helpers class __construct function
	 * and would override the default comments template file path.
	 *
	 * @param $theme_template | Path to custom comments template file
	 *
	 * @return string
	 *
	 * @since 3.0.1
	 */
	public function wpCommentsTemplateOverride( $theme_template ) {

		// Default WP Builder comments template file path
		$theme_template = $this->framework_dir . '/html/wp-comments-template.php';

		// If a template path has been set other than default then
		// include custom path specified
		if( '' != $this->comments_template_path ) {
			$theme_template = $this->comments_template_path;
		}

		return $theme_template;
	}

	/**
	 * Check if we are on a blog page or not
	 *
	 * @return mixed | True or page id selected as blog page else false
	 *
	 * @since 3.0.1
	 */
	public function isBlogPage() {
		if( is_home() && ! is_front_page() ) {

			if( get_option( 'page_for_posts' ) != '' ) {
				return get_option( 'page_for_posts' );
			}

			return true;
		}

		return false;
	}
}
