<?php
if( ! class_exists( 'cjwpbldr_api_core_comments' ) ) {
	class cjwpbldr_api_core_comments {

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
			$this->routes = array(
				'core-comments' => array(
					'endpoint' => 'comments',
					'name' => __( 'Get Comments', 'wp-builder-locale' ),
					'methods' => array(
						'get' => array($this, 'getComments'), // callback function
						'post' => array($this, 'getComments'), // callback function
					),
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, '' );
					},
				),
				'core-comments-get' => array(
					'endpoint' => 'comments/get',
					'name' => __( 'Get a comment', 'wp-builder-locale' ),
					'methods' => array(
						'get' => array($this, 'getComment'), // callback function
						'post' => array($this, 'getComment'), // callback function
					),
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, '' );
					},
				),
				'core-comments-add' => array(
					'endpoint' => 'comments/add',
					'name' => __( 'Add a comment', 'wp-builder-locale' ),
					'methods' => array(
						'post' => array($this, 'addComment'), // callback function
					),
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, 'read' );
					},
				),
				'core-comments-update' => array(
					'endpoint' => 'comments/update',
					'name' => __( 'Update comment', 'wp-builder-locale' ),
					'methods' => array(
						'post' => array($this, 'updateComment'), // callback function
					),
					'permissions' => function ( $request ) {
						return (
							$this->helpers->checkApiRoutePermissions( $request, 'moderate_comments' ) ||
							$this->isCommentAuthor( $request )
						);
					},
				),
				'core-comments-delete' => array(
					'endpoint' => 'comments/delete',
					'name' => __( 'Delete comment', 'wp-builder-locale' ),
					'methods' => array(
						'post' => array($this, 'deleteComment'), // callback function
					),
					'permissions' => function ( $request ) {
						return (
							$this->helpers->checkApiRoutePermissions( $request, 'moderate_comments' ) ||
							$this->isCommentAuthor( $request )
						);
					},
				),
				'core-comments-update-meta' => array(
					'endpoint' => 'comments/update-meta',
					'name' => __( 'Add comment meta', 'wp-builder-locale' ),
					'methods' => array(
						'post' => array($this, 'updateCommentMeta'), // callback function
					),
					'permissions' => function ( $request ) {
						return (
							$this->helpers->checkApiRoutePermissions( $request, 'moderate_comments' ) ||
							$this->isCommentAuthor( $request )
						);
					},
				),
				'core-comments-delete-meta' => array(
					'endpoint' => 'comments/delete-meta',
					'name' => __( 'Delete comment meta', 'wp-builder-locale' ),
					'methods' => array(
						'post' => array($this, 'deleteCommentMeta'), // callback function
					),
					'permissions' => function ( $request ) {
						return (
							$this->helpers->checkApiRoutePermissions( $request, 'moderate_comments' ) ||
							$this->isCommentAuthor( $request )
						);
					},
				),
			);
			add_filter( 'cjwpbldr_register_api_route', array($this, 'registerRoute') );
		}

		public function registerRoute( $routes ) {
			$routes = array_merge( $routes, $this->routes );

			return $routes;
		}

		public function getComments( $request ) {
			$return = [];
			$post_data = $this->helpers->apiRequestParams( $request );

			$rules = [
				'post' => 'required'
			];

			$errors = $this->helpers->validateWithGump( $rules, $post_data );

			if( ! empty( $errors ) ) {
				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}
			$post_info = $this->helpers->postInfo( $post_data['post'], [], $this->helpers->post_info_fields );
			if( empty( $post_info ) ) {
				return $this->helpers->apiError( $request, 200, __( 'No post found', 'wp-builder-locale' ), 200, $post_data );
			}

			$post_data['page'] = (isset($post_data['page'])) ? $post_data['page'] : 0;

			$post_data['page'] = ($post_data['page'] == 0) ? 1 : $post_data['page'];
			$page = (isset( $post_data['page'] )) ? $post_data['page'] : 1;
			$number = get_option( 'posts_per_page' );
			$paged = (get_query_var( 'paged' )) ? get_query_var( 'paged' ) : $page;
			$offset = ($paged - 1) * $number;
			$query_vars = array(
				'status' => 'approve',
				'type' => 'comment',
				'orderby' => 'comment_date',
				'order' => 'ASC',
			);

			$total_query_vars = $query_vars;
			unset( $total_query_vars['paged'] );
			unset( $total_query_vars['number'] );
			unset( $total_query_vars['offset'] );

			$query_vars['post_id'] = $post_info['ID'];
			$total_query_vars['post_id'] = $post_info['ID'];

			if( isset( $post_data['query_vars'] ) && is_array( $post_data['query_vars'] ) && ! empty( $post_data['query_vars'] ) ) {
				foreach( $post_data['query_vars'] as $q_key => $q_value ) {
					$query_vars[ $q_key ] = $q_value;
				}
			}

			$query_vars['number'] = $number;
			$query_vars['offset'] = $offset;

			$comments_query = get_comments( $query_vars );
			$total_comments_query = get_comments( $total_query_vars );
			$return['total'] = count( $total_comments_query );
			$return['found'] = count( $comments_query );

			$total_pages = ceil( $return['total'] / $number );
			$current_page = (int) $paged;
			$next_page = ($current_page < $total_pages) ? $current_page + 1 : 0;
			$prev_page = ($current_page <= $total_pages && $current_page > 0) ? $current_page - 1 : 0;

			$return['pagination']['total_pages'] = $total_pages;
			$return['pagination']['current_page'] = $current_page;
			$return['pagination']['next_page'] = $next_page;
			$return['pagination']['prev_page'] = $prev_page;

			$return['comments'] = [];
			if( ! empty( $comments_query ) ) {
				foreach( $comments_query as $key => $comment ) {
					$return['comments'][ $key ] = $this->helpers->commentInfo( $comment->comment_ID, null, $this->helpers->user_info_fields_public, $this->helpers->post_info_fields_public );
				}
			}
			$return['query_vars'] = $query_vars;
			$output['success'] = $return;

			return $this->helpers->apiResponse( $request, $output );
		}

		public function getComment( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );
			$rules = [
				'comment_ID' => 'required'
			];
			$messages = [
				'comment_ID' => 'Comment ID field is required.'
			];
			$errors = $this->helpers->validateWithGump( $rules, $post_data, $messages );
			if( ! empty( $errors ) ) {
				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}
			$comment_info = $this->helpers->commentInfo( $post_data['comment_ID'], [], $this->helpers->user_info_fields_public, $this->helpers->post_info_fields_public );
			if( is_null( $comment_info['comment_ID'] ) ) {
				return $this->helpers->apiError( $request, 200, __( 'No comment found', 'wp-builder-locale' ), 200, $post_data );
			}

			$return['success'] = $comment_info;

			return $this->helpers->apiResponse( $request, $return );
		}

		public function addComment( $request ) {
			$return = [];
			$post_data = $this->helpers->apiRequestParams( $request );

			$rules = [
				'post' => 'required',
				'user' => 'required',
				'comment_content' => 'required',
			];

			$messages = [
				'post' => __( 'You must specify post ID or slug.', 'wp-builder-locale' ),
				'user' => __( 'You must specify user ID, user_login or user_email.', 'wp-builder-locale' ),
				'comment_content' => __( 'Comment content field is required', 'wp-builder-locale' ),
			];

			$errors = $this->helpers->validateWithGump( $rules, $post_data, $messages );

			if( ! empty( $errors ) ) {
				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}
			$post_info = $this->helpers->postInfo( $post_data['post'] );
			if( empty( $post_info ) ) {
				return $this->helpers->apiError( $request, 200, __( 'No post found', 'wp-builder-locale' ), 200, $post_data );
			}
			$user_info = $this->helpers->userInfo( $post_data['user'] );
			if( empty( $user_info ) ) {
				return $this->helpers->apiError( $request, 200, __( 'No user found', 'wp-builder-locale' ), 200, $post_data );
			}

			$comment_data = array(
				'comment_post_ID' => $post_info['ID'],
				'comment_author' => $user_info['user_login'],
				'comment_author_email' => $user_info['user_email'],
				'comment_author_url' => $user_info['user_url'],
				'comment_content' => esc_textarea( $post_data['comment_content'] ),
				'user_id' => $user_info['ID'],
				'comment_date' => current_time( 'mysql' ),
			);

			$comment_data['comment_type'] = (isset( $post_data['comment_type'] )) ? $post_data['comment_type'] : 'comment';
			$comment_data['comment_parent'] = (isset( $post_data['comment_parent'] )) ? $post_data['comment_parent'] : 0;
			$comment_data['comment_approved'] = (isset( $post_data['comment_approved'] )) ? $post_data['comment_approved'] : 1;
			$comment_data['comment_author_IP'] = $this->helpers->getRealIpInfo();
			$new_comment = wp_insert_comment( $comment_data );

			$return['success'] = $this->helpers->commentInfo( $new_comment, [], $this->helpers->user_info_fields, $this->helpers->post_info_fields );

			return $this->helpers->apiResponse( $request, $return );
		}

		public function updateComment( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );
			$api_user_info = $this->helpers->getApiUserInfo( $request );
			$rules = [
				'comment_ID' => 'required|numeric'
			];

			$messages = [
				'comment_ID' => __( 'You must specify comment ID.', 'wp-builder-locale' ),
			];

			$errors = $this->helpers->validateWithGump( $rules, $post_data, $messages );

			if( ! empty( $errors ) ) {
				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$comment_info = $this->helpers->commentInfo( $post_data['comment_ID'] );
			if( empty( $comment_info ) ) {
				return $this->helpers->apiError( $request, 200, __( 'No comment found', 'wp-builder-locale' ), 200, $post_data );
			}

			$this->helpers->updateCommentInfo( $post_data['comment_ID'], $post_data );

			update_comment_meta( $post_data['comment_ID'], 'last_edited', $api_user_info['ID'] );
			update_comment_meta( $post_data['comment_ID'], 'edit_history_' . strtotime( date( 'Y-m-d h:i:s' ) ), $api_user_info['ID'] );

			$return['success'] = $this->helpers->commentInfo( $post_data['comment_ID'], [], $this->helpers->user_info_fields, $this->helpers->post_info_fields );

			return $this->helpers->apiResponse( $request, $return );
		}

		public function deleteComment( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );

			$rules = [
				'comment_ID' => 'required|numeric'
			];

			$messages = [
				'comment_ID' => __( 'You must specify comment ID.', 'wp-builder-locale' ),
			];

			$errors = $this->helpers->validateWithGump( $rules, $post_data, $messages );

			if( ! empty( $errors ) ) {
				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$comment = get_comment( $post_data['comment_ID'] );

			if( is_null( $comment ) ) {
				return $this->helpers->apiError( $request, 200, __( 'Comment not found', 'wp-builder-locale' ), 200, $post_data );
			}
			wp_delete_comment( $post_data['comment_ID'], true );
			$return['success'] = __( 'Comment removed successfully.', 'wp-builder-locale' );

			return $this->helpers->apiResponse( $request, $return );
		}

		public function updateCommentMeta( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );

			$rules = [
				'comment_ID' => 'required|numeric',
				'meta_key' => 'required',
				'meta_value' => 'required',
			];

			$messages = [
				'comment_ID' => __( 'You must specify comment ID.', 'wp-builder-locale' ),
				'meta_key' => __( 'You must specify meta key.', 'wp-builder-locale' ),
				'meta_value' => __( 'You must specify meta value.', 'wp-builder-locale' ),
			];

			$errors = $this->helpers->validateWithGump( $rules, $post_data, $messages );

			if( ! empty( $errors ) ) {
				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$comment_info = $this->helpers->commentInfo( $post_data['comment_ID'], [], $this->helpers->user_info_fields, $this->helpers->post_info_fields );
			if( is_null( $comment_info['comment_ID'] ) ) {
				return $this->helpers->apiError( $request, 200, __( 'Comment not found', 'wp-builder-locale' ), 200, $post_data );
			}

			update_comment_meta( $post_data['comment_ID'], $post_data['meta_key'], $post_data['meta_value'] );

			$return['success'] = __( 'Comment meta updated successfully.', 'wp-builder-locale' );
			$return['comment'] = $comment_info;

			return $this->helpers->apiResponse( $request, $return );
		}

		public function deleteCommentMeta( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );

			$rules = [
				'comment_ID' => 'required|numeric',
				'meta_key' => 'required',
			];

			$messages = [
				'comment_ID' => __( 'You must specify comment ID.', 'wp-builder-locale' ),
				'meta_key' => __( 'You must specify meta key.', 'wp-builder-locale' ),
			];

			$errors = $this->helpers->validateWithGump( $rules, $post_data, $messages );

			if( ! empty( $errors ) ) {
				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$comment_info = $this->helpers->commentInfo( $post_data['comment_ID'], [], $this->helpers->user_info_fields, $this->helpers->post_info_fields );
			if( is_null( $comment_info['comment_ID'] ) ) {
				return $this->helpers->apiError( $request, 200, __( 'Comment not found', 'wp-builder-locale' ), 200, $post_data );
			}

			delete_comment_meta( $post_data['comment_ID'], $post_data['meta_key'] );

			$return['success'] = __( 'Comment meta removed successfully.', 'wp-builder-locale' );
			$return['comment'] = $comment_info;

			return $this->helpers->apiResponse( $request, $return );
		}

		public function isCommentAuthor( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );
			if( ! isset( $post_data['comment_ID'] ) ) {
				return false;
			}
			$comment_author = get_comment( $post_data['comment_ID'] );
			if(is_null($comment_author)){
				return false;
			}
			$api_user_info = $this->helpers->getApiUserInfo( $request );

			return ($comment_author->comment_author_email === $api_user_info['user_email']);
		}

	}

	cjwpbldr_api_core_comments::getInstance();
}