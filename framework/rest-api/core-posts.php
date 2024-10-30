<?php
if( ! class_exists( 'cjwpbldr_api_core_posts' ) ) {
	class cjwpbldr_api_core_posts {

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
				'core-posts' => [
					'endpoint' => 'posts',
					'name' => __( 'Get posts', 'wp-builder-locale' ),
					'methods' => [
						'get' => [$this, 'getPosts'], // callback function
						'post' => [$this, 'getPosts'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, '' );
					},
				],
				'core-posts-get' => [
					'endpoint' => 'posts/get',
					'name' => __( 'Get post', 'wp-builder-locale' ),
					'methods' => [
						'get' => [$this, 'getPost'], // callback function
						'post' => [$this, 'getPost'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, '' );
					},
				],
				'core-posts-get-by-meta' => [
					'endpoint' => 'posts/get-by-meta',
					'name' => __( 'Get posts by meta', 'wp-builder-locale' ),
					'methods' => [
						'get' => [$this, 'getPostsByMeta'], // callback function
						'post' => [$this, 'getPostsByMeta'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, '' );
					},
				],
				'core-posts-add' => [
					'endpoint' => 'posts/add',
					'name' => __( 'Create a post', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'createPost'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, 'publish_posts' );
					},
				],
				'core-posts-update' => [
					'endpoint' => 'posts/update',
					'name' => __( 'Update post', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'updatePost'], // callback function
					],
					'permissions' => function ( $request ) {
						return ($this->helpers->checkApiRoutePermissions( $request, 'edit_posts' ) || $this->helpers->checkApiRoutePermissions( $request, 'edit_others_posts' ) || $this->helpers->checkApiRoutePermissions( $request, 'edit_published_posts' ));
					},
				],
				'core-posts-delete' => [
					'endpoint' => 'posts/delete',
					'name' => __( 'Delete post', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'deletePost'], // callback function
					],
					'permissions' => function ( $request ) {
						return (
							$this->helpers->checkApiRoutePermissions( $request, 'delete_posts' ) ||
							$this->helpers->checkApiRoutePermissions( $request, 'delete_others_posts' ) ||
							$this->helpers->checkApiRoutePermissions( $request, 'delete_published_posts' ) ||
							$this->helpers->checkApiRoutePermissions( $request, 'delete_private_posts' ));
					},
				],
				'core-posts-terms' => [
					'endpoint' => 'posts/terms',
					'name' => __( 'Get post terms', 'wp-builder-locale' ),
					'methods' => [
						'get' => [$this, 'getPostTerms'], // callback function
						'post' => [$this, 'getPostTerms'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, '' );
					},
				],
				'core-posts-terms-add' => [
					'endpoint' => 'posts/terms/add',
					'name' => __( 'Add post term', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'addPostTerm'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, 'manage_categories' );
					},
				],
				'core-posts-terms-delete' => [
					'endpoint' => 'posts/terms/delete',
					'name' => __( 'Delete post term', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'deletePostTerm'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, 'manage_categories' );
					},
				],
				'core-images' => [
					'endpoint' => 'images',
					'name' => __( 'Get images', 'wp-builder-locale' ),
					'methods' => [
						'get' => [$this, 'getImages'], // callback function
						'post' => [$this, 'getImages'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, '' );
					},
				],
			];
			add_filter( 'cjwpbldr_register_api_route', [$this, 'registerRoute'] );
		}

		public function registerRoute( $routes ) {
			$routes = array_merge( $routes, $this->routes );

			return $routes;
		}

		public function getPosts( $request ) {
			$return = [];
			$post_data = $this->helpers->apiRequestParams( $request );
			$post_data['page'] = (isset( $post_data['page'] )) ? $post_data['page'] : 1;
			$post_data['page'] = ($post_data['page'] == 0) ? 1 : $post_data['page'];
			$page = (isset( $post_data['page'] )) ? $post_data['page'] : 1;
			$number = get_option( 'posts_per_page' );
			if( isset( $post_data['posts_per_page'] ) ) {
				$number = $post_data['posts_per_page'];
			}
			if( isset( $post_data['query_vars']['posts_per_page'] ) ) {
				$number = $post_data['query_vars']['posts_per_page'];
			}
			$paged = (get_query_var( 'paged' )) ? get_query_var( 'paged' ) : $page;
			$offset = ($paged - 1) * $number;
			$query_vars = [
				'post_status' => 'publish',
				'post_type' => 'post'
			];

			if( isset( $post_data['query_vars'] ) && ! empty( $post_data['query_vars'] ) ) {
				foreach( $post_data['query_vars'] as $q_key => $q_value ) {
					$query_vars[ $q_key ] = $q_value;
				}
			}
			$query_vars['number'] = $number;
			$query_vars['offset'] = $offset;

			$total_query_vars = $query_vars;
			unset( $total_query_vars['paged'] );
			unset( $total_query_vars['number'] );
			unset( $total_query_vars['offset'] );

			$posts_query = new WP_Query( $query_vars );
			$total_posts_query = new WP_Query( $total_query_vars );
			$return['total'] = $total_posts_query->found_posts;
			$return['posts_per_page'] = $posts_query->query['number'];

			$total_pages = ceil( $return['total'] / $number );
			$current_page = (int) $paged;
			$next_page = ($current_page < $total_pages) ? $current_page + 1 : 0;
			$prev_page = ($current_page <= $total_pages && $current_page > 0) ? $current_page - 1 : 0;

			$return['pagination']['total_pages'] = $total_pages;
			$return['pagination']['current_page'] = $current_page;
			$return['pagination']['next_page'] = $next_page;
			$return['pagination']['prev_page'] = $prev_page;

			$return_fields_array = $this->helpers->post_info_fields;
			$additional_fields = [];
			if( isset( $post_data['fields'] ) && is_array( $post_data['fields'] ) && ! empty( $post_data['fields'] ) ) {
				$additional_fields = $post_data['fields'];
			}
			$return_fields_array = array_merge( $return_fields_array, $additional_fields );
			$found_posts = [];
			if( $posts_query->have_posts() ) {
				$count = - 1;
				while( $posts_query->have_posts() ) : $posts_query->the_post();
					$count ++;
					global $post;
					$found_posts[ $count ] = $this->helpers->postInfo( $post->ID, [], $return_fields_array );
				endwhile;
				wp_reset_postdata();
			}
			$return['posts'] = $found_posts;

			$return['query_vars'] = $query_vars;

			$links = [];
			$output['success'] = $return;

			return $this->helpers->apiResponse( $request, $output, $links );
		}

		public function getPostsByMeta( $request ) {
			$return = [];
			$post_data = $this->helpers->apiRequestParams( $request );

			if( ! isset( $post_data['meta_key'] ) || isset( $post_data['meta_key'] ) && $post_data['meta_key'] == '' ) {
				$message = __( 'You must specify meta_key', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $message, 406, $post_data );
			}

			$meta_value = (isset( $post_data['meta_value'] )) ? $post_data['meta_value'] : '';
			$return_fields = (isset( $post_data['fields'] )) ? $post_data['fields'] : [];

			if( isset( $post_data['single'] ) ) {
				$output['success'] = $this->helpers->getPostByMetaKey( $post_data['meta_key'], $meta_value, 'data', $return_fields );
			} else {
				$output['success'] = $this->helpers->getPostsByMetaKey( $post_data['meta_key'], $meta_value, 'data', $return_fields );
			}

			return $this->helpers->apiResponse( $request, $output );
		}

		public function getPost( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );

			if( ! isset( $post_data['post'] ) ) {
				$errors = __( 'You must specify post id or slug.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$return_fields_array = $this->helpers->post_info_fields;
			$additional_fields = [];
			if( isset( $post_data['fields'] ) && is_array( $post_data['fields'] ) && ! empty( $post_data['fields'] ) ) {
				$additional_fields = $post_data['fields'];
			}
			$return_fields_array = array_merge( $return_fields_array, $additional_fields );

			$return['success'] = $this->helpers->postInfo( $post_data['post'], [], $return_fields_array );

			return $this->helpers->apiResponse( $request, $return );
		}

		public function createPost( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );
			$api_user_info = $this->helpers->getApiUserInfo( $request );
			$user_info = $this->helpers->userInfo( $api_user_info['ID'], '*' );
			$post_data['post_author'] = ( ! empty( $user_info ) && $user_info['ID'] != '') ? $user_info['ID'] : $post_data['post_author'];
			$new_post_info = $this->helpers->createPost( $post_data );
			if( isset( $new_post_info['errors'] ) ) {
				return $this->helpers->apiError( $request, 406, $new_post_info['errors'], 406, $post_data );
			}
			$return_fields_array = $this->helpers->post_info_fields;
			$additional_fields = [];
			if( isset( $post_data['fields'] ) && is_array( $post_data['fields'] ) && ! empty( $post_data['fields'] ) ) {
				$additional_fields = $post_data['fields'];
			}
			$return_fields_array = array_merge( $return_fields_array, $additional_fields );
			$post_info = $this->helpers->postInfo( $new_post_info['ID'], [], $return_fields_array );

			$return['success'] = $post_info;

			return $this->helpers->apiResponse( $request, $return );
		}

		public function updatePost( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );

			if( ! isset( $post_data['post'] ) ) {
				$errors = __( 'You must specify post id or slug to update the post.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}
			$post_info = $this->helpers->postInfo( $post_data['post'] );
			if( empty( $post_info ) ) {
				$errors = __( 'No post found.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$update_data = $post_data;
			unset( $update_data['post'] );
			unset( $update_data['ID'] );

			$post_info = $this->helpers->updatePostInfo( $post_data['post'], $update_data );

			$return_fields = $this->helpers->post_info_fields;
			if( isset( $post_data['fields'] ) && ! empty( $post_data['fields'] ) ) {
				$return_fields = array_merge( $return_fields, $post_data['fields'] );
			}

			$return['success'] = $this->helpers->postInfo( $post_info['ID'], [], $return_fields );

			return $this->helpers->apiResponse( $request, $return );
		}

		public function deletePost( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );

			if( ! isset( $post_data['post'] ) ) {
				$errors = __( 'You must specify post id or slug to update the post.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}
			$post_info = $this->helpers->postInfo( $post_data['post'] );
			if( empty( $post_info ) ) {
				$errors = __( 'No post found.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			wp_delete_post( $post_info['ID'], true );

			$return['success'] = __( 'Post removed successfully.', 'wp-builder-locale' );

			return $this->helpers->apiResponse( $request, $return );
		}

		public function getPostTerms( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );

			if( ! isset( $post_data['post'] ) || isset( $post_data['post'] ) && $post_data['post'] == '' ) {
				$message = __( 'You must specify post ID or slug.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $message, 406, $post_data );
			}

			$post_info = $this->helpers->postInfo( $post_data['post'] );
			if( empty( $post_info ) ) {
				$errors = __( 'No post found.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$post_info = $this->helpers->postInfo( $post_data['post'] );

			if( isset( $post_data['taxonomy'] ) && $post_data['taxonomy'] != '' ) {
				if( isset( $post_info['taxonomies'][ $post_data['taxonomy'] ] ) ) {
					$return['success'] = $post_info['taxonomies'][ $post_data['taxonomy'] ];

					return $this->helpers->apiResponse( $request, $return );
				} else {
					$errors = __( 'Taxonomy not found.', 'wp-builder-locale' );

					return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
				}
			}

			$return['success'] = $post_info['taxonomies'];

			return $this->helpers->apiResponse( $request, $return );
		}

		public function addPostTerm( $request ) {

			$post_data = $this->helpers->apiRequestParams( $request );

			$rules = [
				'post' => 'required',
				'taxonomy' => 'required',
				'term' => 'required',
			];
			$messages = [
				'post' => 'You must specify post ID or slug.',
				'taxonomy' => 'You must specify a taxonomy.',
				'term' => 'You must specify a term to add.',
			];

			$errors = $this->helpers->validateWithGump( $rules, $post_data, $messages, [] );

			if( ! empty( $errors ) ) {
				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$post_info = $this->helpers->postInfo( $post_data['post'] );
			if( empty( $post_info ) ) {
				$errors = __( 'No post found.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$post_info = $this->helpers->postInfo( $post_data['post'] );
			wp_add_object_terms( $post_info['ID'], $post_data['term'], $post_data['taxonomy'] );
			$post_info = $this->helpers->postInfo( $post_data['post'] );
			$return['success'] = $post_info['taxonomies'][ $post_data['taxonomy'] ];

			return $this->helpers->apiResponse( $request, $return );
		}

		public function deletePostTerm( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );

			$rules = [
				'post' => 'required',
				'taxonomy' => 'required',
				'term' => 'required',
			];

			$messages = [
				'post' => __( 'You must specify post ID or slug.', 'wp-builder-locale' ),
				'taxonomy' => __( 'You must specify a taxonomy.', 'wp-builder-locale' ),
				'term' => __( 'You must specify a term to add.', 'wp-builder-locale' ),
			];

			$errors = $this->helpers->validateWithGump( $rules, $post_data, $messages, [] );

			if( ! empty( $errors ) ) {
				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$post_info = $this->helpers->postInfo( $post_data['post'] );

			if( empty( $post_info ) ) {
				$errors = __( 'No post found.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$post_info = $this->helpers->postInfo( $post_data['post'] );
			$term = get_term_by( 'slug', $post_data['term'], $post_data['taxonomy'] );

			if( $term ) {
				wp_remove_object_terms( $post_info['ID'], $term->term_id, $post_data['taxonomy'] );
				$post_info = $this->helpers->postInfo( $post_data['post'] );
			}
			$return['success'] = $post_info['taxonomies'][ $post_data['taxonomy'] ];

			return $this->helpers->apiResponse( $request, $return );
		}

		public function getImages( $request ) {
			$return = [];
			$post_data = $this->helpers->apiRequestParams( $request );
			$post_data['page'] = (isset( $post_data['page'] )) ? $post_data['page'] : 1;
			$post_data['page'] = ($post_data['page'] == 0) ? 1 : $post_data['page'];
			$page = (isset( $post_data['page'] )) ? $post_data['page'] : 1;
			$number = get_option( 'posts_per_page' );
			if( isset( $post_data['posts_per_page'] ) ) {
				$number = $post_data['posts_per_page'];
			}
			if( isset( $post_data['query_vars']['posts_per_page'] ) ) {
				$number = $post_data['query_vars']['posts_per_page'];
			}
			$paged = (get_query_var( 'paged' )) ? get_query_var( 'paged' ) : $page;
			$offset = ($paged - 1) * $number;
			$query_vars = [
				'post_status' => 'publish',
				'post_type' => 'post'
			];

			if( isset( $post_data['query_vars'] ) && ! empty( $post_data['query_vars'] ) ) {
				foreach( $post_data['query_vars'] as $q_key => $q_value ) {
					$query_vars[ $q_key ] = $q_value;
				}
			}
			$query_vars['number'] = $number;
			$query_vars['offset'] = $offset;

			$total_query_vars = $query_vars;
			unset( $total_query_vars['paged'] );
			unset( $total_query_vars['number'] );
			unset( $total_query_vars['offset'] );

			$posts_query = new WP_Query( $query_vars );
			$total_posts_query = new WP_Query( $total_query_vars );
			$return['total'] = $total_posts_query->found_posts;
			$return['posts_per_page'] = $posts_query->query['number'];

			$total_pages = ceil( $return['total'] / $number );
			$current_page = (int) $paged;
			$next_page = ($current_page < $total_pages) ? $current_page + 1 : 0;
			$prev_page = ($current_page <= $total_pages && $current_page > 0) ? $current_page - 1 : 0;

			$return['pagination']['total_pages'] = $total_pages;
			$return['pagination']['current_page'] = $current_page;
			$return['pagination']['next_page'] = $next_page;
			$return['pagination']['prev_page'] = $prev_page;

			$return_fields_array = $this->helpers->post_info_fields;
			$additional_fields = [];
			if( isset( $post_data['fields'] ) && is_array( $post_data['fields'] ) && ! empty( $post_data['fields'] ) ) {
				$additional_fields = $post_data['fields'];
			}
			$return_fields_array = array_merge( $return_fields_array, $additional_fields );
			$found_posts = [];
			if( $posts_query->have_posts() ) {
				$count = - 1;
				while( $posts_query->have_posts() ) : $posts_query->the_post();
					$count ++;
					global $post;
					$found_posts[ $count ] = $this->helpers->postInfo( $post->ID, [], $return_fields_array );
				endwhile;
				wp_reset_postdata();
			}
			$return['posts'] = $found_posts;

			$return['query_vars'] = $query_vars;

			$links = [];
			$output['success'] = $return;

			return $this->helpers->apiResponse( $request, $output, $links );
		}
	}

	cjwpbldr_api_core_posts::getInstance();
}