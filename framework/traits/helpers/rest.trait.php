<?php

/**
 * Trait cjwpbldr_helpers_rest
 */
trait cjwpbldr_helpers_rest {
	/**
	 * @param       $data
	 * @param array $request_data
	 *
	 * @return false|mixed|string|void
	 */
	public function jsonError( $data, $request_data = [] ) {
		$output = [
			'error' => 1,
			'messages' => $data,
			'request_data' => $request_data,
		];

		return json_encode( $output );
	}

	/**
	 * @param       $data
	 * @param array $request_data
	 *
	 * @return false|mixed|string|void
	 */
	public function jsonSuccess( $data, $request_data = [] ) {
		$output = [
			'success' => 1,
			'data' => $data,
			'request_data' => $request_data,
		];

		return json_encode( $output );
	}

	/**
	 * @param $array
	 *
	 * @return string
	 */
	public function jsonEncode( $array ) {
		return esc_html( wp_json_encode( $array ) );
	}

	/**
	 * @param $request
	 * @param $capability
	 *
	 * @return bool
	 */
	public function checkApiRoutePermissions( $request, $capability ) {
		if( $capability == '' ) {
			return true;
		}
		if( $capability != '' && $this->apiUserCan( $request, $capability ) ) {
			return true;
		}

		return false;
	}

	/**
	 * @param $request
	 *
	 * @return array
	 */
	public function apiRequestParams( $request ) {
		$data['get_params'] = (is_array( $request->get_params() )) ? $request->get_params() : [];
		$data['get_body_params'] = (is_array( $request->get_body_params() )) ? $request->get_body_params() : [];
		$data['get_json_params'] = (is_array( $request->get_json_params() )) ? $request->get_json_params() : [];
		$data['get_query_params'] = (is_array( $request->get_query_params() )) ? $request->get_query_params() : [];
		$data['get_url_params'] = (is_array( $request->get_url_params() )) ? $request->get_url_params() : [];
		$data['get_file_params'] = (is_array( $request->get_file_params() )) ? $request->get_file_params() : [];
		foreach( $data as $key => $value ) {
			if( gettype( $value ) == 'string' ) {
				$value = @json_decode( $value, 1 );
				$data[ $key ] = $value;
			}
		}
		$return = array_merge( $data['get_params'], $data['get_body_params'], $data['get_json_params'], $data['get_query_params'], $data['get_url_params'], $data['get_file_params'] );

		return $return;
	}

	/**
	 * @param string $endpoint
	 *
	 * @return string
	 */
	public function apiUrl( $endpoint = '' ) {
		$url = trailingslashit( rest_url( $this->config( 'plugin-info', 'page_slug' ) ) );
		if( $endpoint == '' ) {
			return $url;
		} else {
			return $url . $endpoint;
		}
	}

	/**
	 * @param null $var
	 *
	 * @return array|mixed
	 */
	public function apiMethodsArray( $var = null ) {
		$methods_array = [
			'get' => WP_REST_Server::READABLE,
			'post' => WP_REST_Server::CREATABLE,
			'put' => WP_REST_Server::EDITABLE,
			'patch' => WP_REST_Server::EDITABLE,
			'delete' => WP_REST_Server::DELETABLE,
			'all' => WP_REST_Server::ALLMETHODS,
		];

		return (is_null( $var )) ? $methods_array : $methods_array[ $var ];
	}

	/**
	 * @return string
	 */
	public function apiSecret() {
		$api_secret_key = sha1( md5( $this->site_url . wp_salt( 'nonce' ) ) );

		return strtoupper( $api_secret_key );
	}

	/**
	 * @param null $var
	 *
	 * @return array|false
	 */
	public function apiHeaders( $var = null ) {
		$headers = getallheaders();

		return (is_null( $var )) ? $headers : $headers[ $var ];
	}

	/**
	 * @param       $request
	 * @param array $data
	 * @param array $links
	 *
	 * @return mixed|WP_REST_Response
	 */
	public function apiResponse( $request, $data = [], $links = [] ) {
		$core_links = [];
		$core_links['base']['href'] = $this->apiUrl();
		$core_links['self']['href'] = rest_url( $request->get_route() );
		$res['data'] = $data;
		$res['request_data'] = $this->apiRequestParams( $request );
		$res['_links'] = array_merge( $core_links, $links );
		$response = new WP_REST_Response( $res );
		$response = rest_ensure_response( $response );

		return $response;
	}

	/**
	 * @param       $request
	 * @param       $code
	 * @param       $message
	 * @param       $status
	 * @param array $data
	 *
	 * @return WP_Error
	 */
	public function apiError( $request, $code, $message, $status, $data = [] ) {
		$core_links = [];
		$core_links['_links']['self']['href'] = rest_url( $request->get_route() );
		$core_links['_links']['base']['href'] = $this->apiUrl();
		$status_data = [
			'code' => $code,
			'status' => $status,
			'errors' => $message,
			'request_data' => $data,
			'post_data' => $data,
		];
		$status_data = array_merge( $status_data, $core_links );

		return new WP_Error( 'api_error', $message, $status_data );
	}

	/**
	 * @return string
	 */
	public function generateToken() {
		$api_secret_key = sha1( time() . $this->site_url );
		$token = bin2hex( openssl_random_pseudo_bytes( 16 ) );

		return $token . $api_secret_key;
	}

	/**
	 * @param $user_id
	 *
	 * @return string
	 */
	public function setUserToken( $user_id ) {
		$token_key = 'X-Authorization';
		$token_expire_key = 'X-Authorization-Expires';
		$token_expire = 4;
		$token = $this->generateToken();
		update_user_meta( $user_id, $token_key, $token );
		update_user_meta( $user_id, $token_expire_key, strtotime( "{$token_expire} hours" ) );
		$this->setCookie( 'cjwpbldr_token', $token );

		return $token;
	}

	/**
	 * @param       $token
	 * @param array $fields
	 *
	 * @return array
	 */
	public function getUserByToken( $token, $fields = [] ) {
		$token_key = 'X-Authorization';
		$additional_fields = ['access_token'];
		if( ! empty( $fields ) ) {
			$additional_fields = array_merge( $additional_fields, $fields );
		}
		$user_info = $this->getUserByMeta( $token_key, $token, 'data', $additional_fields );
		if( ! isset( $user_info['access_token'] ) ) {
			return [];
		}

		return $user_info;
	}

	/**
	 * @param $user_id
	 */
	public function extendUserToken( $user_id ) {
		$token_expire_key = 'X-Authorization-Expires';
		$token_expire = 4;
		$token_expire = ($token_expire == '') ? 2 : $token_expire;
		update_user_meta( $user_id, $token_expire_key, $token_expire );
	}

	/**
	 * Get response from internal API Routes.
	 *
	 * NOTE: Give only the name of the route.
	 * Example: posts
	 *
	 * @param        $route  | string | name of the API route
	 * @param        $method | string | method of the route
	 * @param        $params | array | Array of parameters. Eg: [ 'posts_per_page' => 12 ]
	 *
	 * @return mixed
	 * @since 1.8.3
	 */
	public function getInternalApiResponse( $route, $method = 'GET', $params = [] ) {

		if( '' == $route && '' == $method ) {
			return [];
		}

		$api_response = [];

		$route = '/cjwpbldr/' . $route;
		$request = new WP_REST_Request( $method, $route );

		if( $this->isValidatedArray( $params ) ) {
			$request->set_query_params( $params );
		}

		$response = rest_do_request( $request );
		$server = rest_get_server();
		$data = $server->response_to_data( $response, false );

		if( $this->isValidatedArray( $data['data'], true, 'success' ) ) {
			$api_response = $data['data']['success'];
		}

		return $api_response;
	}

}