<?php
if( ! class_exists( 'cjwpbldr_rest_api_support' ) ) {
    class cjwpbldr_rest_api_support {

        public $helpers, $routes, $api_namespace, $methods_array;

        private static $instance;

        public static function getInstance() {
            if( ! isset( self::$instance ) ) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        public function __construct() {
            $this->helpers = cjwpbldr_helpers::getInstance();
            $this->api_namespace = 'cjwpbldr';
            $this->methods_array = $this->helpers->apiMethodsArray();
            $this->routes = [];
            add_action( 'init', [$this, 'includeApiRoutes'] );
            add_action( 'init', [$this, 'setApiAccessToken'] );
            add_action( 'wp_login', [$this, 'wpLoginSetUserBasicAuthKey'], 10, 2 );
            add_action( 'clear_auth_cookie', [$this, 'removeApiAccessToken'] );
            add_filter( 'rest_pre_serve_request', [$this, 'modifyRequestHeaders'] );
            add_filter( 'determine_current_user', [$this, 'jsonBasicAuthHandler'], 20 );
            add_filter( 'json_authentication_errors', [$this, 'jsonBasicAuthError'] );
            add_action( 'rest_api_init', [$this, 'registerApiRoutes'], 99 );
            add_action( 'admin_notices', [$this, 'checkUserSalt'] );
            add_action( 'admin_init', [$this, 'createPersonalToken'] );
        }

        public function checkUserSalt() {
            if( is_user_logged_in() ) {
                $current_user = wp_get_current_user();
                $user_salt = get_user_meta( $current_user->ID, 'cjwpbldr_auth_token', true );
                if( $user_salt == '' ) {
                    $logout_url = wp_logout_url( admin_url() );
                    $content = sprintf( __( 'Please <a href="%s" target="_parent">logout</a> and log in again to configure API access tokens.', 'wp-builder-locale' ), $logout_url );
                    $this->helpers->adminNotice( 'cjwpbldr_check_user_salt', 'error', $content, $this->helpers->config( 'plugin-info', 'name' ), false );
                }
            }
        }

        public function setApiAccessToken() {
            $current_user = wp_get_current_user();
            if( $current_user->ID > 0 ) {
                $user_token = get_user_meta( $current_user->ID, 'X-Authorization', true );
                if( $user_token == '' ) {
                    $this->helpers->setUserToken( $current_user->ID );
                    $user_token = get_user_meta( $current_user->ID, 'X-Authorization', true );
                }
                $this->helpers->setCookie( 'cjwpbldr_token', $user_token, (3600 * 24), '/' );
                $this->helpers->setCookie( 'current_uid', $current_user->ID, (3600 * 24), '/' );
            } else {
                $this->helpers->deleteCookie( 'cjwpbldr_token' );
                $this->helpers->deleteCookie( 'current_uid' );
            }
        }

        public function removeApiAccessToken() {
            $current_user = wp_get_current_user();
            delete_user_meta( $current_user->ID, 'X-Authorization' );
            delete_user_meta( $current_user->ID, 'X-Authorization-Expires' );
            $this->helpers->deleteCookie( 'cjwpbldr_token' );
        }

        public function registerApiRoutes() {
            $api_routes = apply_filters( 'cjwpbldr_register_api_route', $this->routes );
            $all_routes_keys = [];
            foreach( $api_routes as $key => $route_data ) {
                foreach( $route_data['methods'] as $method_key => $callback ) {
                    $option_key = '/' . $this->api_namespace . '/' . $route_data['endpoint'];
                    $all_routes_keys[] = $option_key;
                    $args['methods'] = $this->helpers->apiMethodsArray( $method_key );
                    $args['callback'] = $callback;
                    $args['args'] = (isset( $route_data['args'] )) ? $route_data['args'] : [];
                    $args['permission_callback'] = $route_data['permissions'];
                    register_rest_route( $this->api_namespace, $route_data['endpoint'], $args, false );
                }
            }
        }

        public function wpLoginSetUserBasicAuthKey( $user_id, $user ) {
            if( $user ) {
                $log = '';
                $pwd = '';
                if( isset( $_POST['log'] ) ) {
                    $log = $_POST['log'];
                }
                if( isset( $_POST['pwd'] ) ) {
                    $pwd = $_POST['pwd'];
                }
                if( isset( $_POST['user_login'] ) ) {
                    $log = $_POST['user_login'];
                }
                if( isset( $_POST['user_pass'] ) ) {
                    $pwd = $_POST['user_pass'];
                }
                if( $log != '' && $pwd != '' ) {
                    update_user_meta( $user->ID, 'cjwpbldr_auth_token', base64_encode( $log . ":" . $pwd ) );
                    update_user_meta( $user->ID, 'cjwpbldr_auth_token', base64_encode( $log . ":" . $pwd ) );
                    update_user_meta( $user->ID, 'cjwpbldr_user_salt', base64_encode( $pwd ) );
                    if( ! get_user_meta( $user->ID, 'X-Authorization', true ) ) {
                        $token = $this->helpers->setUserToken( $user->ID );
                        $this->helpers->setCookie( 'cjwpbldr_token', $token, (3600 * 24), '/', '' );
                    }
                    update_user_meta( $user->ID, 'X-Authorization-Expires', strtotime( '+2 hours' ) );
                }
            }
        }

        public function modifyRequestHeaders( $value ) {

            $value .= header( 'Access-Control-Allow-Headers: X-Referer, X-WP-Nonce, X-Authorization-Token, X-Authorization, Authorization, Referer, Origin, X-Requested-With, Content-Type, Accept' );

            return $value;
        }

        public function jsonBasicAuthHandler( $user ) {
            global $wp_json_basic_auth_error;
            $wp_json_basic_auth_error = null;
            // Don't authenticate twice
            if( ! empty( $user ) ) {
                return $user;
            }
            $token = $this->getAuthorizationHeaderToken();
            if( is_null( $token ) ) {
                return $user;
            }
            $user = $this->helpers->getUserByMeta( 'X-Authorization', $token, 'data' );
            if( ! empty( $user ) ) {
                $cjwpbldr_auth_token = get_user_meta( $user['ID'], 'cjwpbldr_auth_token', true );
                $user_login_data = explode( ':', base64_decode( $cjwpbldr_auth_token ) );
                $username = $user_login_data[0];
                $password = $user_login_data[1];
                remove_filter( 'determine_current_user', 'jsonBasicAuthHandler', 20 );
                $user = wp_authenticate( $username, $password );
                add_filter( 'determine_current_user', 'jsonBasicAuthHandler', 20 );
                if( is_wp_error( $user ) ) {
                    $wp_json_basic_auth_error = $user;

                    return null;
                }
                $wp_json_basic_auth_error = true;

                return $user->ID;
            }

            return null;
        }

        function jsonBasicAuthError( $error ) {
            if( ! empty( $error ) ) {
                return $error;
            }
            global $wp_json_basic_auth_error;

            return $wp_json_basic_auth_error;
        }

        public function includeApiRoutes() {
            $core_rest_api_routes = $this->helpers->root_dir . '/framework/rest-api';
            $dirs = preg_grep( '/^([^.])/', scandir( $core_rest_api_routes ) );
            foreach( $dirs as $key => $file_name ) {
                $file_path = $core_rest_api_routes . '/' . $file_name;
                if( ! is_dir( $file_path ) && file_exists( $file_path ) ) {
                    require $file_path;
                }
            }
        }

        public function getAuthorizationHeaderToken() {
            if( ! empty( $_SERVER['HTTP_AUTHORIZATION'] ) ) {
                return wp_unslash( $_SERVER['HTTP_AUTHORIZATION'] );
            }
            if( function_exists( 'getallheaders' ) ) {
                $headers = getallheaders();
                foreach( $headers as $key => $value ) {
                    if( strtolower( $key ) === 'x-authorization-token' ) {
                        return $value;
                    }
                }
            }

            return null;
        }

        public function createPersonalToken() {
            $api_token = get_option( 'cjwpbldr_api_token', false );
            if( ! $api_token ) {
                $api_token = $this->helpers->generateToken();
                update_option( 'cjwpbldr_api_token', $api_token );
            }
        }

    }

    cjwpbldr_rest_api_support::getInstance();
}
