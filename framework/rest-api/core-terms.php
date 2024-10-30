<?php
if( ! class_exists( 'cjwpbldr_api_core_terms' ) ) {
	class cjwpbldr_api_core_terms {
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
				'core-taxonomies' => [
					'endpoint' => 'taxonomies',
					'name' => __( 'Get Taxonomies', 'wp-builder-locale' ),
					'methods' => [
						'get' => [$this, 'getTaxonomies'], // callback function
						'post' => [$this, 'getTaxonomies'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, '' );
					},
				],
				'core-taxonomies/get' => [
					'endpoint' => 'taxonomies/get',
					'name' => __( 'Get Taxonomy', 'wp-builder-locale' ),
					'methods' => [
						'get' => [$this, 'getTaxonomy'], // callback function
						'post' => [$this, 'getTaxonomy'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, '' );
					},
				],
				'core-terms' => [
					'endpoint' => 'terms',
					'name' => __( 'Get Terms', 'wp-builder-locale' ),
					'methods' => [
						'get' => [$this, 'getTerms'], // callback function
						'post' => [$this, 'getTerms'], // callback function
					],
					'permissions' => function ( $request ) {
						return true;
					},
				],
				'core-terms/get' => [
					'endpoint' => 'terms/get',
					'name' => __( 'Get Term', 'wp-builder-locale' ),
					'methods' => [
						'get' => [$this, 'getTerm'], // callback function
						'post' => [$this, 'getTerm'], // callback function
					],
					'permissions' => function ( $request ) {
						return true;
					},
				],
				'core-terms/add' => [
					'endpoint' => 'terms/add',
					'name' => __( 'Add Term', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'addTerm'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, 'manage_categories' );
					},
				],
				'core-terms/update' => [
					'endpoint' => 'terms/update',
					'name' => __( 'Update Term', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'updateTerm'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, 'manage_categories' );
					},
				],
				'core-terms/delete' => [
					'endpoint' => 'terms/delete',
					'name' => __( 'Delete Term', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'deleteTerm'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, 'manage_categories' );
					},
				],
				'core-terms/meta/update' => [
					'endpoint' => 'terms/meta/update',
					'name' => __( 'Update term meta', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'updateTermMeta'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, 'manage_categories' );
					},
				],
				'core-terms/meta/delete' => [
					'endpoint' => 'terms/meta/delete',
					'name' => __( 'Delete term meta', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'deleteTermMeta'], // callback function
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, 'manage_categories' );
					},
				],
			];
			add_filter( 'cjwpbldr_register_api_route', [$this, 'registerRoute'] );
		}

		public function registerRoute( $routes ) {
			$routes = array_merge( $routes, $this->routes );

			return $routes;
		}

		public function getTaxonomies( $request ) {
			$return = [];
			$post_data = $this->helpers->apiRequestParams( $request );

			$taxonomy_args = (isset( $post_data['taxonomy_args'] ) && ! empty( $post_data['taxonomy_args'] )) ? $post_data['taxonomy_args'] : [];
			$term_args = (isset( $post_data['term_args'] ) && ! empty( $post_data['term_args'] )) ? $post_data['term_args'] : [];

			if( isset( $post_data['taxonomy_args'] ) ) {
				foreach( $taxonomy_args as $tax_key => $tax_var ) {
					$tax_var = ($tax_var === 'true') ? true : $tax_var;
					$tax_var = ($tax_var === 'false') ? false : $tax_var;
					$taxonomy_args[ $tax_key ] = $tax_var;
				}
			}
			if( isset( $post_data['term_args'] ) ) {
				foreach( $term_args as $term_key => $term_var ) {
					$term_var = ($term_var === 'true') ? true : $term_var;
					$term_var = ($term_var === 'false') ? false : $term_var;
					$term_args[ $term_key ] = $term_var;
				}
			}

			$taxonomies = get_taxonomies( $taxonomy_args );

			if( ! empty( $taxonomies ) ) {
				$count = - 1;
				foreach( $taxonomies as $key => $taxonomy ) {
					$count ++;
					$return[ $count ] = (array) get_taxonomy( $taxonomy );
					$term_args['taxonomy'] = $taxonomy;
					$terms = get_terms( $term_args );
					if( ! empty( $terms ) ) {
						foreach( $terms as $t_key => $term ) {
							$return[ $count ]['terms'][] = $term;
						}
					}
				}
			}

			$output['success'] = $return;

			return $this->helpers->apiResponse( $request, $output );
		}

		public function getTaxonomy( $request ) {
			$return = [];
			$post_data = $this->helpers->apiRequestParams( $request );

			$rules = [
				'taxonomy' => 'required'
			];

			$errors = $this->helpers->validateWithGump( $rules, $post_data );
			if( ! empty( $errors ) ) {
				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$term_args = (isset( $post_data['term_args'] ) && ! empty( $post_data['term_args'] )) ? $post_data['term_args'] : [];
			if( isset( $post_data['term_args'] ) ) {
				foreach( $term_args as $term_key => $term_var ) {
					$term_var = ($term_var === 'true') ? true : $term_var;
					$term_var = ($term_var === 'false') ? false : $term_var;
					$term_args[ $term_key ] = $term_var;
				}
			}

			$taxonomy = get_taxonomy( $post_data['taxonomy'] );
			if( ! empty( $taxonomy ) ) {
				$return = (array) $taxonomy;
				$term_args['taxonomy'] = $post_data['taxonomy'];
				$terms = get_terms( $term_args );
				if( ! empty( $terms ) ) {
					foreach( $terms as $t_key => $term ) {
						$return['terms'][] = $term;
					}
				}
			}

			$output['success'] = $return;

			return $this->helpers->apiResponse( $request, $output );
		}

		public function getTerms( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );
			$return = [];
			$rules = [
				'taxonomy' => 'required'
			];

			$errors = $this->helpers->validateWithGump( $rules, $post_data );
			if( ! empty( $errors ) ) {
				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$term_args = (isset( $post_data['term_args'] ) && ! empty( $post_data['term_args'] )) ? $post_data['term_args'] : [];
			if( isset( $post_data['term_args'] ) ) {
				foreach( $term_args as $term_key => $term_var ) {
					$term_var = ($term_var === 'true') ? true : $term_var;
					$term_var = ($term_var === 'false') ? false : $term_var;
					$term_args[ $term_key ] = $term_var;
				}
			}

			$term_args['taxonomy'] = $post_data['taxonomy'];

			$terms = get_terms( $term_args );
			if( is_wp_error( $terms ) ) {
				return $this->helpers->apiError( $request, 200, __( 'No terms found', 'wp-builder-locale' ), 200, $post_data );
			}

			$output = [];
			foreach( $terms as $key => $term ) {
				$output[] = $term;
			}

			$return['success'] = $output;

			return $this->helpers->apiResponse( $request, $return );
		}

		public
		function getTerm(
			$request
		) {
			$return = [];
			$post_data = $this->helpers->apiRequestParams( $request );
			$rules = [
				'taxonomy' => 'required',
				'term' => 'required'
			];

			$errors = $this->helpers->validateWithGump( $rules, $post_data );
			if( ! empty( $errors ) ) {
				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$field_type = (is_numeric( $post_data['term'] )) ? 'id' : 'slug';

			$term = get_term_by( $field_type, $post_data['term'], $post_data['taxonomy'], 'ARRAY_A' );

			if( ! $term ) {
				return $this->helpers->apiError( $request, 200, __( 'Term not found.', 'wp-builder-locale' ), 200, $post_data );
			}

			$return['success'] = $term;

			return $this->helpers->apiResponse( $request, $return );
		}

		public
		function addTerm(
			$request
		) {
			$return = [];
			$post_data = $this->helpers->apiRequestParams( $request );
			$rules = [
				'taxonomy' => 'required',
				'term' => 'required'
			];

			$errors = $this->helpers->validateWithGump( $rules, $post_data );
			if( ! empty( $errors ) ) {
				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$exists = term_exists( $post_data['term'], $post_data['taxonomy'] );
			if( ! is_null( $exists ) ) {
				$errors = __( 'Term already exists', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$args = (isset( $post_data['args'] )) ? $post_data['args'] : [];
			$new_term = wp_insert_term( $post_data['term'], $post_data['taxonomy'], $args );

			$new_term_object = get_term_by( 'id', $new_term['term_id'], $post_data['taxonomy'] );
			$return['success'] = $new_term_object;

			return $this->helpers->apiResponse( $request, $return );
		}

		public
		function updateTerm(
			$request
		) {
			$return = [];
			$post_data = $this->helpers->apiRequestParams( $request );
			$rules = [
				'term' => 'required',
				'taxonomy' => 'required'
			];

			$messages = [
				'term' => 'You must specify term_id or slug.',
				'taxonomy' => 'You must specify taxonomy.',
			];

			$errors = $this->helpers->validateWithGump( $rules, $post_data, $messages );

			if( ! isset( $post_data['args'] ) || isset( $post_data['args'] ) && ! is_array( $post_data['args'] ) ) {
				$errors['args'] = __( 'You must specify args[] to update for this term.', 'wp-builder-locale' );
			}

			if( ! empty( $errors ) ) {
				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$field_type = (is_numeric( $post_data['term'] )) ? 'id' : 'slug';
			$term = get_term_by( $field_type, $post_data['term'], $post_data['taxonomy'] );

			$exists = term_exists( $term->term_id, $post_data['taxonomy'] );
			if( is_null( $exists ) ) {
				$errors = __( 'Term not found.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}
			wp_update_term( $term->term_id, $post_data['taxonomy'], $post_data['args'] );
			$return['success'] = get_term_by( $field_type, $post_data['term'], $post_data['taxonomy'], 'ARRAY_A' );

			return $this->helpers->apiResponse( $request, $return );
		}

		public
		function deleteTerm(
			$request
		) {
			$return = [];
			$post_data = $this->helpers->apiRequestParams( $request );
			$rules = [
				'term' => 'required',
				'taxonomy' => 'required'
			];

			$messages = [
				'term' => 'You must specify term_id or slug.',
				'taxonomy' => 'You must specify taxonomy.',
			];

			$errors = $this->helpers->validateWithGump( $rules, $post_data, $messages );

			if( ! empty( $errors ) ) {
				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}

			$field_type = (is_numeric( $post_data['term'] )) ? 'id' : 'slug';
			$term = get_term_by( $field_type, $post_data['term'], $post_data['taxonomy'] );

			if( $term ) {
				wp_delete_term( $term->term_id, $post_data['taxonomy'] );
				$return['success'] = __( 'Term removed successfully.', 'wp-builder-locale' );

				return $this->helpers->apiResponse( $request, $return );
			} else {
				return $this->helpers->apiError( $request, 406, __( 'Term not found', 'wp-builder-locale' ), 406, $post_data );
			}
		}

		public
		function updateTermMeta(
			$request
		) {
			$post_data = $this->helpers->apiRequestParams( $request );
			$rules = [
				'term' => 'required',
				'meta_key' => 'required',
				'meta_value' => 'required'
			];
			$messages = [
				'term' => __( 'You must specify term_id or slug.', 'wp-builder-locale' ),
				'meta_key' => __( 'You must specify meta_key.', 'wp-builder-locale' ),
				'meta_value' => __( 'You must specify meta_value.', 'wp-builder-locale' ),
			];
			$errors = $this->helpers->validateWithGump( $rules, $post_data, $messages );
			if( ! empty( $errors ) ) {
				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}
			$term = get_term( $post_data['term'] );
			update_term_meta( $term->term_id, $post_data['meta_key'], $post_data['meta_value'] );
			$return['success'] = __( 'Term meta updated successfully.', 'wp-builder-locale' );
			$return['term'] = $term;

			return $this->helpers->apiResponse( $request, $return );
		}

		public
		function deleteTermMeta(
			$request
		) {
			$post_data = $this->helpers->apiRequestParams( $request );
			$rules = [
				'term' => 'required',
				'meta_key' => 'required',
			];
			$messages = [
				'term' => __( 'You must specify term_id or slug.', 'wp-builder-locale' ),
				'meta_key' => __( 'You must specify meta_key.', 'wp-builder-locale' ),
			];
			$errors = $this->helpers->validateWithGump( $rules, $post_data, $messages );
			if( ! empty( $errors ) ) {
				return $this->helpers->apiError( $request, 406, $errors, 406, $post_data );
			}
			$term = get_term( $post_data['term'] );

			delete_term_meta( $term->term_id, $post_data['meta_key'] );
			$return['success'] = __( 'Term meta removed successfully.', 'wp-builder-locale' );
			$return['term'] = $term;

			return $this->helpers->apiResponse( $request, $return );
		}
	}

	cjwpbldr_api_core_terms::getInstance();
}
