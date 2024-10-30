<?php
if( ! class_exists( 'cjwpbldr_ajax' ) ) {
	class cjwpbldr_ajax {
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
			add_action( 'wp_ajax_nopriv_get_google_addresses', array($this, 'getGoogleAddresses') );
			add_action( 'wp_ajax_get_google_addresses', array($this, 'getGoogleAddresses') );

			add_action( 'wp_ajax_nopriv_get_google_addresses_by_lat_lng', array($this, 'getGoogleAddressesByLatLng') );
			add_action( 'wp_ajax_get_google_addresses_by_lat_lng', array($this, 'getGoogleAddressesByLatLng') );

			add_action( 'wp_ajax_nopriv_cjwpbldr_query_posts', array($this, 'queryPostsCallback') );
			add_action( 'wp_ajax_cjwpbldr_query_posts', array($this, 'queryPostsCallback') );
			add_action( 'wp_ajax_nopriv_cjwpbldr_query_users', array($this, 'queryUsersCallback') );
			add_action( 'wp_ajax_cjwpbldr_query_users', array($this, 'queryUsersCallback') );
		}

		public function getGoogleAddresses() {
			$string = $_REQUEST['term'];
			$result = $this->helpers->getAddressFromGoogle( $string, $_REQUEST['filter'] );
			echo json_encode( $result );
			die();
		}

		public function getGoogleAddressesByLatLng() {
			$result = $this->helpers->getAddressByCoords( $_REQUEST['lat'], $_REQUEST['lng'] );
			echo json_encode( $result );
			die();
		}

		public function queryPostsCallback() {
			$query_args = (isset( $_REQUEST['args'] ) && is_array( $_REQUEST['args'] )) ? $_REQUEST['args'] : array();
			$return = array();
			$query_args['paged'] = (isset( $query_args['paged'] )) ? $query_args['paged'] : 1;
			$the_query = new WP_Query( $query_args );
			$count = - 1;
			$posts = array();
			if( $the_query->have_posts() ) {
				$count = - 1;
				while( $the_query->have_posts() ) {
					$count ++;
					$the_query->the_post();
					global $post;
					$post_info = $this->helpers->postInfo( $post->ID );
					$post_info['sort_order'] = strtotime( $post_info['post_date'] );
					$posts[ $count ] = $post_info;
				}
				if( is_array( $posts ) && ! empty( $posts ) ) {
					$return['posts'] = $posts;
					$return['post_count'] = $the_query->post_count;
					$return['total_posts'] = (int) $the_query->found_posts;

					$return['pagination'] = array();
					// pagination data
					$total_pages = $the_query->max_num_pages;
					$current_page = (int) $the_query->query['paged'];
					$next_page = $the_query->query['paged'] + 1;
					$previous_page = $the_query->query['paged'] - 1;
					if( $next_page > $total_pages ) {
						$next_page = null;
					}
					if( $previous_page <= 0 ) {
						$previous_page = null;
					}
					$return['pagination']['total_pages'] = $total_pages;
					$return['pagination']['current_page'] = $current_page;
					$return['pagination']['next_page'] = $next_page;
					$return['pagination']['previous_page'] = $previous_page;
					$return = apply_filters( 'modify_cjwpbldr_query_posts', $return );
				} else {
					$return = array();
				}
				wp_reset_postdata();
			} else {
				$return = array();
			}
			echo json_encode( $return );
			die();
		}

		public function queryUsersCallback() {

			$return = [];

			$query_args = (isset( $_REQUEST['args'] ) && is_array( $_REQUEST['args'] )) ? $_REQUEST['args'] : array();
			$return = array();
			$query_args['paged'] = (isset( $query_args['paged'] )) ? $query_args['paged'] : 1;

			$query_args['number'] = (isset( $query_args['number'] )) ? $query_args['number'] : 10;
			$query_args['offset'] = (isset( $query_args['offset'] )) ? $query_args['offset'] : 0;
			if( $query_args['paged'] > 1 ) {
				$query_args['offset'] = $query_args['number'] * $query_args['paged'];
			}
			if( $query_args['page'] > 1 ) {
				$query_args['offset'] = $query_args['number'] * $query_args['page'];
			}

			$the_query = new WP_User_Query( $query_args );
			$users = [];
			if( ! empty( $the_query->get_results() ) ) {
				foreach( $the_query->get_results() as $user ) {
					$users[] = $this->helpers->userInfo( $user->ID );
				}
			}

			if( ! empty( $users ) ) {
				$return['users'] = $users;
				$return['total_users'] = (int) $the_query->get_total();

				$return['pagination'] = $this->helpers->pagination( 10, $the_query->get_total(), $query_args['paged'] );
			}

			echo json_encode( $return );

			/*$users = get_users( $_POST );
			$return = array();
			if( ! empty( $users ) ) {
				foreach( $users as $key => $user ) {
					$user_info = $this->helpers->userInfo( $user->ID );
					$return[] = $user_info;
				}
			}
			echo json_encode( $return );*/
			die();
		}

	}

	cjwpbldr_ajax::getInstance();
}