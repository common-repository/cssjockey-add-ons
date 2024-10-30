<?php

/**
 * Trait cjwpbldr_helpers_posts
 */
trait cjwpbldr_helpers_posts {

	/**
	 * @param array $post_data
	 * @param bool  $bypass_check
	 *
	 * @return array
	 */
	public function createPost( $post_data = [], $bypass_check = false ) {
		$current_user = wp_get_current_user();
		$rules = [
			'post_title' => 'required',
			'post_content' => 'required',
		];
		$messages = [
			'post_title' => __( 'Post title field is required.', 'wp-builder-locale' ),
			'post_content' => __( 'Post content field is required.', 'wp-builder-locale' ),
		];

		$post_data['post_author'] = (isset( $post_data['post_author'] )) ? $post_data['post_author'] : $current_user->ID;
		$post_data['post_date'] = (isset( $post_data['post_date'] )) ? $post_data['post_date'] : current_time( 'mysql' );
		$post_data['post_date_gmt'] = current_time( 'mysql', 1 );
		$post_data['post_content'] = (isset( $post_data['post_content'] )) ? $post_data['post_content'] : '';
		$post_data['post_title'] = (isset( $post_data['post_title'] )) ? $post_data['post_title'] : '';
		$post_data['post_excerpt'] = (isset( $post_data['post_excerpt'] )) ? $post_data['post_excerpt'] : $this->trimChars( $post_data['post_content'], 160, '' );
		$post_data['post_status'] = (isset( $post_data['post_status'] )) ? $post_data['post_status'] : 'draft';
		$post_data['post_type'] = (isset( $post_data['post_type'] )) ? $post_data['post_type'] : 'post';
		$post_data['comment_status'] = (isset( $post_data['comment_status'] )) ? $post_data['comment_status'] : 'open';
		$post_data['ping_status'] = (isset( $post_data['ping_status'] )) ? $post_data['ping_status'] : 'open';
		$post_data['post_password'] = (isset( $post_data['post_password'] )) ? $post_data['post_password'] : '';
		$post_data['post_name'] = (isset( $post_data['post_name'] )) ? $post_data['post_name'] : sanitize_title( $post_data['post_title'] );
		$post_data['post_parent'] = (isset( $post_data['post_parent'] )) ? $post_data['post_parent'] : 0;
		$post_data['menu_order'] = (isset( $post_data['menu_order'] )) ? $post_data['menu_order'] : 0;
		$post_data['post_category'] = (isset( $post_data['post_category'] )) ? $post_data['post_category'] : '';
		$post_data['tags_input'] = (isset( $post_data['tags_input'] )) ? $post_data['tags_input'] : '';
		$post_data['set_post_terms'] = (isset( $post_data['set_post_terms'] )) ? $post_data['set_post_terms'] : '';
		$post_data['add_post_terms'] = (isset( $post_data['add_post_terms'] )) ? $post_data['add_post_terms'] : '';

		$errors = $this->validateWithGump( $rules, $post_data, $messages );
		if( ! empty( $errors ) ) {
			return ['errors' => $errors];
		}

		if( ! $bypass_check ) {
			$exists = get_page_by_title( $post_data['post_title'], ARRAY_A, $post_data['post_type'] );

			if( ! is_null( $exists ) ) {
				return ['errors' => __( 'Post with this title already exists.', 'wp-builder-locale' )];
			}
		}

		$new_post_id = wp_insert_post( $post_data, true );

		if( is_wp_error( $new_post_id ) ) {
			return ['errors' => $new_post_id->get_error_messages()];
		}
		$this->updatePostInfo( $new_post_id, $post_data );
		$post_info = $this->postInfo( $new_post_id );

		return $post_info;
	}

	/**
	 * @param $post_type
	 *
	 * @return array|object|string|null
	 */
	public function getPostsFromCustomPostType( $post_type, $number_of_posts = '' ) {
		global $wpdb;

		$limit = ('' != $number_of_posts) ? ' LIMIT ' . $number_of_posts : '';

		$query_str = "SELECT * FROM $wpdb->posts WHERE $wpdb->posts.post_type = '{$post_type}' AND $wpdb->posts.post_status = 'publish' ORDER BY $wpdb->posts.post_date DESC {$limit}";
		$query_str = apply_filters( 'cssjockey_custom_post_type_query', $query_str );
		$custom_post_types_posts = $wpdb->get_results( $query_str, ARRAY_A );
		/**
		 * Sample Format of data returned
		 * Array
		 * (
		 * [0] => Array
		 * (
		 * [ID] => 47
		 * [post_author] => 1
		 * [post_date] => 2016-10-19 10:13:19
		 * [post_date_gmt] => 2016-10-19 10:13:19
		 * [post_content] =>
		 * [post_title] => Frontend Auth ~ Set new password
		 * [post_excerpt] =>
		 * [post_status] => publish
		 * [comment_status] => closed
		 * [ping_status] => closed
		 * [post_password] =>
		 * [post_name] => cjwpbldr_frontend_auth_do_set_new_password
		 * [to_ping] =>
		 * [pinged] =>
		 * [post_modified] => 2016-10-19 10:13:19
		 * [post_modified_gmt] => 2016-10-19 10:13:19
		 * [post_content_filtered] =>
		 * [post_parent] => 0
		 * [guid] => https://cssjockey.com/cjfm/cjwpbldr-forms/frontend-auth-set-new-password/
		 * [menu_order] => 0
		 * [post_type] => cj-custom-forms
		 * [post_mime_type] =>
		 * [comment_count] => 0
		 * )
		 * )
		 */
		if( is_array( $custom_post_types_posts ) && ! empty( $custom_post_types_posts ) ) {
			return $custom_post_types_posts;
		} else {
			return '';
		}
	}

	/**
	 * @param       $post_id_or_slug
	 * @param array $filters
	 * @param array $return_fields
	 *
	 * @return array
	 */
	public function postInfo( $post_id_or_slug, $filters = [], $return_fields = [] ) {
		global $wpdb;
		$postID = (is_numeric( $post_id_or_slug )) ? $post_id_or_slug : 'slug';
		$return = [];
		$post_data_fields = (isset( $filters['post_data_fields'] )) ? implode( ',', $filters['post_data_fields'] ) : '*';

		if( $postID != 'slug' ) {
			$post_data = $wpdb->get_row( "SELECT {$post_data_fields} FROM $wpdb->posts WHERE ID = '{$post_id_or_slug}'", ARRAY_A );
		} else {
			$post_data = $wpdb->get_row( "SELECT {$post_data_fields} FROM $wpdb->posts WHERE post_name = '{$post_id_or_slug}'", ARRAY_A );
		}
		if( ! is_array( $post_data ) ) {
			return [];
		}
		foreach( $post_data as $key => $value ) {
			$return[ $key ] = (is_serialized( $value )) ? unserialize( $value ) : $value;
		}

		$post_meta_fields = (isset( $filters['post_meta_fields'] ) && is_array( $filters['post_meta_fields'] )) ? '"' . implode( '","', $filters['post_meta_fields'] ) . '"' : null;
		if( ! is_null( $post_meta_fields ) ) {
			$post_meta = $wpdb->get_results( "SELECT * FROM $wpdb->postmeta WHERE post_id = '{$post_data['ID']}' AND meta_key IN ($post_meta_fields)", ARRAY_A );
		} else {
			$post_meta = $wpdb->get_results( "SELECT * FROM $wpdb->postmeta WHERE post_id = '{$post_data['ID']}'", ARRAY_A );
		}

		if( is_array( $post_meta ) ) {
			foreach( $post_meta as $meta ) {
				$meta_value = $meta['meta_value'];
				if( is_serialized( $meta['meta_value'] ) ) {
					$meta_value = @unserialize( $meta['meta_value'] );
				}
				$return[ $meta['meta_key'] ] = $meta_value;
			}
		}
		$return['post_excerpt_full'] = $return['post_excerpt'];
		$return['post_excerpt'] = ($post_data['post_excerpt'] == '') ? $this->trimChars( strip_shortcodes( strip_tags( $post_data['post_content'] ) ), 120, '..' ) : $post_data['post_excerpt'];
		$return['post_excerpt'] = $this->trimChars( $return['post_excerpt'], 120, '...' );
		$return['post_content_plain'] = strip_tags( strip_shortcodes( $post_data['post_content'] ) );
		$return['post_content_parsed'] = do_shortcode( $post_data['post_content'] );
		//$return['post_excerpt'] = $this->trimChars($return['post_excerpt'], 120, '...');
		$return['permalink'] = get_permalink( $post_data['ID'] );

		if( has_post_thumbnail( $return['ID'] ) ) {
			$thumb_id = get_post_thumbnail_id( $return['ID'] );
			$full_url = wp_get_attachment_image_src( $thumb_id, 'full', true );
			$return['featured_image_url'] = $full_url[0];
			$return['featured_images'] = [];
			if( function_exists( 'get_intermediate_image_sizes' ) ) {
				$image_sizes = get_intermediate_image_sizes();
				foreach( $image_sizes as $image_key => $image_size ) {
					$image = wp_get_attachment_image_src( $thumb_id, $image_size, true );
					$return['featured_images'][ $image_size ] = $image[0];
				}
			}
		} else {
			$default_post_thumb = $this->default_post_featured_image_url;
			$return['featured_image_url'] = $default_post_thumb;
		}

		$return['featured_image_attachment_id'] = get_post_thumbnail_id( $return['ID'] );

		$return['attached_images'] = $this->getPostImages( $post_data['ID'] );

		$return['post_author_name'] = '';
		$return['post_author_posts_url'] = '';
		$return['post_author_link'] = '';
		$return['post_author_gravatar'] = '';
		if( isset( $post_data['post_author'] ) ) {
			$post_author_info = $this->userInfo( $post_data['post_author'] );
			if( is_array( $post_author_info ) && ! empty( $post_author_info ) ) {
				$return['post_author_name'] = (isset( $post_author_info['display_name'] )) ? $post_author_info['display_name'] : '';
				$return['post_author_posts_url'] = (isset( $post_author_info['posts_link_url'] )) ? $post_author_info['posts_link_url'] : '';
				$return['post_author_link'] = '<a href="' . $post_author_info['posts_link_url'] . '" title="' . $post_author_info['display_name'] . '">' . $post_author_info['display_name'] . '</a>';
				$return['post_author_gravatar'] = $post_author_info['gravatar'];
			}
		}
		$return['taxonomies'] = [];
		$post_taxonomies = $this->getPostTaxonomies( $post_data['ID'] );
		$taxonomy_links = [];
		if( ! empty( $post_taxonomies ) ) {
			foreach( $post_taxonomies as $key => $post_taxonomy ) {
				$terms = wp_get_object_terms( $post_data['ID'], $post_taxonomy );
				$count = - 1;
				foreach( $terms as $t_key => $term ) {
					$count ++;
					$term_link = get_term_link( $term );
					$return['taxonomies'][ $post_taxonomy ][ $count ]['term_id'] = $term->term_id;
					$return['taxonomies'][ $post_taxonomy ][ $count ]['term_name'] = $term->name;
					$return['taxonomies'][ $post_taxonomy ][ $count ]['term_url'] = $term_link;
					$return['taxonomies'][ $post_taxonomy ][ $count ]['term_slug'] = $term->slug;
					$return['taxonomies'][ $post_taxonomy ][ $count ]['term_permalink'] = '<a href="' . $term_link . '" title="' . $term->name . '">' . $term->name . '</a>';
					$return['taxonomies'][ $post_taxonomy ][ $count ]['term_data'] = $term;

					if( $count == 0 ) {
						$return['taxonomy'][ $post_taxonomy ][ $count ]['term_id'] = $term->term_id;
						$return['taxonomy'][ $post_taxonomy ][ $count ]['term_name'] = $term->name;
					}
					$taxonomy_links[] = '<a href="' . get_term_link( $term ) . '" title="' . $term->name . '">' . $term->name . '</a>';
					$return['taxonomy_links'][ $post_taxonomy ] = ( ! empty( $taxonomy_links )) ? implode( ', ', $taxonomy_links ) : '';
				}
			}
		}

		$return['category_links'] = ( ! empty( $taxonomy_links )) ? implode( ', ', $taxonomy_links ) : '';

		if( isset( $filters['html_fields'] ) ) {
			foreach( $filters['html_fields'] as $key => $field ) {
				if( isset( $return[ $field ] ) ) {
					$return[ $field ] = wpautop( $return[ $field ] );
				}
			}
		}
		$return['post_content'] = wpautop( $return['post_content'], true );
		$return['post_excerpt'] = esc_attr( $return['post_excerpt'] );
		$return['comments_string'] = '';
		if( $return['comment_count'] == 0 ) {
			$return['comments_string'] = __( 'No comments', 'wp-builder-locale' );
		} elseif( $return['comment_count'] == 1 ) {
			$return['comments_string'] = __( '1 Comment', 'wp-builder-locale' );
		} elseif( $return['comment_count'] > 1 ) {
			$return['comments_string'] = sprintf( __( '%s Comments', 'wp-builder-locale' ), $return['comment_count'] );
		}
		$return['comments_link'] = $return['permalink'] . '#comments';

		if( isset( $return['_wp_attached_file'] ) ) {
			$upload_dir = wp_upload_dir();
			$return['_wp_attached_file_url'] = trailingslashit( $upload_dir['baseurl'] ) . $return['_wp_attached_file'];
			if( function_exists( 'get_intermediate_image_sizes' ) ) {
				$image_sizes = get_intermediate_image_sizes();
				foreach( $image_sizes as $image_key => $image_size ) {
					$image = wp_get_attachment_image_src( $return['ID'], $image_size, true );
					$return['media_files'][ $image_size ] = $image[0];
				}
			}
		}

		if( $return_fields == '*' || $return_fields == 'all' ) {
			return $return;
		}
		if( is_array( $return_fields ) && in_array( 'all', $return_fields ) ) {
			return $return;
		}
		if( is_array( $return_fields ) && in_array( 'all', $return_fields ) ) {
			return $return;
		}

		if( ! empty( $return_fields ) ) {
			$return_data = [];
			foreach( $return_fields as $r_key => $r_value ) {
				if( $this->isValidatedArray( $return, true, $r_value ) ) {
					$return_data[ $r_value ] = $return[ $r_value ];
				}
			}

			return $return_data;
		} else {
			return $return;
		}
	}

	/**
	 * @param $object_id
	 *
	 * @return array
	 */
	public function getPostTaxonomies( $object_id ) {
		$return = [];
		$relationships = $this->dbSelect( 'term_relationships', '*', [
			'object_id' => $object_id
		] );
		if( ! empty( $relationships ) ) {
			foreach( $relationships as $key => $relationship ) {
				$taxonomy = $this->dbGet( 'term_taxonomy', 'taxonomy', [
					'term_taxonomy_id' => $relationship['term_taxonomy_id']
				] );
				if( ! empty( $taxonomy ) ) {
					$return[] = $taxonomy['taxonomy'];
				}
			}
		}

		return $return;
	}

	/**
	 * @param        $post_info
	 * @param string $taxonomy
	 * @param array  $link_attributes
	 * @param string $sep
	 *
	 * @return string
	 */
	public function getPostTaxonomyLinks( $post_info, $taxonomy = 'category', $link_attributes = [], $sep = '' ) {
		$return = [];
		$attributes = [];
		if( is_array( $link_attributes ) && ! empty( $link_attributes ) ) {
			foreach( $link_attributes as $key => $value ) {
				$attributes[] = "{$key}='{$value}'";
			}
		}
		$attributes = implode( ' ', $attributes );
		if( isset( $post_info['taxonomies'][ $taxonomy ] ) && is_array( $post_info['taxonomies'][ $taxonomy ] ) && ! empty( $post_info['taxonomies'][ $taxonomy ] ) ) {
			foreach( $post_info['taxonomies'][ $taxonomy ] as $key => $term ) {
				$return[] = '<a ' . $attributes . ' href="' . $term['term_url'] . '" title="' . $term['term_name'] . '">' . $term['term_name'] . '</a>';
			}
		}

		return implode( $sep, $return );
	}

	/**
	 * @param $post_id
	 *
	 * @return array
	 */
	public function getPostMeta( $post_id ) {
		global $wpdb;
		$result = $wpdb->get_results( "SELECT * FROM $wpdb->postmeta WHERE post_id = '{$post_id}'" );
		$return = [];
		if( ! empty( $result ) ) {
			foreach( $result as $key => $value ) {
				$meta_value = (is_serialized( $value->meta_value )) ? unserialize( $value->meta_value ) : $value->meta_value;
				$return[ $value->meta_key ] = $meta_value;
			}
		}

		return $return;
	}

	/**
	 * @param string $string
	 * @param array  $post_info
	 *
	 * @return mixed|string
	 */
	public function parseMetaString( $string = 'Posted by %%author%% on %%date%% %%time%% in %%terms%% | %%comments%%', $post_info = [] ) {
		if( is_array( $post_info ) && ! empty( $post_info ) ) {
			$post_date = $this->wpDate( $post_info['post_date'], 'date' );
			$post_time = $this->wpDate( $post_info['post_date'], 'time' );
			$edit_link = get_edit_post_link( $post_info['ID'], 'EDIT' );
			$string = str_replace( '%%author%%', '<span class="cj-post-meta-item cj-meta-author-link">' . $post_info['post_author_link'] . '</span>', $string );
			$string = str_replace( '%%date%%', '<span class="cj-post-meta-item cj-meta-date">' . $post_date . '</span>', $string );
			$string = str_replace( '%%time%%', '<span class="cj-post-meta-item cj-meta-time">' . $post_time . '</span>', $string );
			$string = str_replace( '%%terms%%', '<span class="cj-post-meta-item cj-meta-terms">' . $post_info['category_links'] . '</span>', $string );
			$string = str_replace( '%%comments%%', '<span class="cj-post-meta-item cj-meta-comments-link"><a href="' . $post_info['permalink'] . '#comments" title="' . $post_info['comments_string'] . '">' . $post_info['comments_string'] . '</a></span>', $string );
			if( current_user_can( 'publish_posts' ) ) {
				$string .= ' ';
				$string .= sprintf( __( '<a href="%s" target="_blank" rel="nofollow"> | Edit %s</a>', 'wp-builder-locale' ), $edit_link, $post_info['post_type'] );
			}

			return $string;
		} else {
			return '';
		}
	}

	/**
	 * @param        $post_id
	 * @param        $data
	 * @param string $return_fields
	 *
	 * @return array
	 */
	public function updatePostInfo( $post_id, $data, $return_fields = 'all' ) {
		global $wpdb;
		$prefix = (is_multisite()) ? $wpdb->base_prefix : $wpdb->prefix;
		$table_name = $prefix . 'posts';
		$posts_table_fields = [];
		$post_data = [];
		$exclude_posts_table_fields = ['ID'];
		foreach( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
			$posts_table_fields[] = $column_name;
			if( ! in_array( $column_name, $exclude_posts_table_fields ) ) {
				if( isset( $data[ $column_name ] ) ) {
					$post_data[ $column_name ] = $data[ $column_name ];
				}
			}
		}
		if( is_array( $post_data ) ) {
			$post_data['ID'] = $post_id;
			wp_update_post( $post_data );
		}
		if( is_array( $data ) && ! empty( $data ) ) {
			foreach( $data as $meta_key => $meta_value ) {
				if( ! in_array( $meta_key, $posts_table_fields ) ) {
					update_post_meta( $post_id, $meta_key, $meta_value );
				}
			}
		}

		if( isset( $data['set_post_terms'] ) && ! empty( $data['set_post_terms'] ) ) {
			foreach( $data['set_post_terms'] as $post_taxonomy => $term ) {
				wp_set_object_terms( $post_id, $term, $post_taxonomy, false );
			}
		}
		if( isset( $data['add_post_terms'] ) && ! empty( $data['add_post_terms'] ) ) {
			foreach( $data['add_post_terms'] as $post_taxonomy => $term ) {
				wp_set_object_terms( $post_id, $term, $post_taxonomy, true );
			}
		}

		return $this->postInfo( $post_id, [], $return_fields );
	}

	/**
	 * @param $comment_id
	 * @param $data
	 *
	 * @return array
	 */
	public function updateCommentInfo( $comment_id, $data ) {
		global $wpdb;
		$prefix = (is_multisite()) ? $wpdb->base_prefix : $wpdb->prefix;
		$table_name = $prefix . 'comments';
		$comments_table_fields = [];
		$comment_data = [];
		$exclude_comments_table_fields = ['comment_ID'];
		foreach( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
			$comments_table_fields[] = $column_name;
			if( ! in_array( $column_name, $exclude_comments_table_fields ) ) {
				if( isset( $data[ $column_name ] ) ) {
					$comment_data[ $column_name ] = $data[ $column_name ];
				}
			}
		}
		if( is_array( $comment_data ) ) {
			$comment_data['comment_ID'] = $comment_id;
			//$comment_data['comment_content'] = esc_html( $comment_data['comment_content'] );
			wp_update_comment( $comment_data );
		}
		foreach( $data as $meta_key => $meta_value ) {
			if( ! in_array( $meta_key, $comments_table_fields ) ) {
				update_comment_meta( $comment_id, $meta_key, $meta_value );
			}
		}

		return $this->commentInfo( $comment_id );
	}

	/**
	 * @param $post_info
	 *
	 * @return int[]|WP_Post[]
	 */
	public function relatedPosts( $post_info ) {
		$tax_query = [];
		foreach( $post_info['taxonomies'] as $key => $terms ) {
			$term_ids = [];
			foreach( $terms as $t_key => $term ) {
				$term_ids[] = $term['term_id'];
			}
			$tax_query[] = [
				'taxonomy' => $key,
				'field' => 'id',
				'terms' => $term_ids,
				'operator' => 'IN' //Or 'AND' or 'NOT IN'
			];
		}
		$related = get_posts( [
			'post_type' => $post_info['post_type'],
			'numberposts' => 12,
			'post__not_in' => [$post_info['ID']],
			'tax_query' => [
				[
					'taxonomy' => 'cj_freebies_category',
					'field' => 'id',
					'terms' => $term_ids,
					'operator' => 'IN' //Or 'AND' or 'NOT IN'
				],
			],
		] );

		return $related;
	}

	/**
	 * @param array $query_vars
	 * @param int   $page
	 * @param array $return_fields
	 *
	 * @return mixed
	 */
	public function queryPosts( $query_vars = [], $page = 1, $return_fields = [] ) {
		$number = get_option( 'posts_per_page' );
		if( isset( $query_vars['posts_per_page'] ) ) {
			$number = $query_vars['posts_per_page'];
		}
		$paged = (get_query_var( 'paged' )) ? get_query_var( 'paged' ) : $page;
		if( isset( $query_vars['paged'] ) ) {
			$paged = $query_vars['paged'];
		}
		$offset = ($paged - 1) * $number;
		if( empty( $query_vars ) ) {
			$query_vars = [
				'post_status' => 'publish',
				'post_type' => 'post'
			];
		}

		if( isset( $query_vars ) && is_array( $query_vars ) && ! empty( $query_vars ) ) {
			foreach( $query_vars as $q_key => $q_value ) {
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

		$filters = [];
		if( isset( $return_fields ) && is_array( $return_fields ) && ! empty( $return_fields ) ) {
			$filters = $return_fields;
		}

		$return['posts'] = [];
		if( $posts_query->have_posts() ) {
			$count = - 1;
			while( $posts_query->have_posts() ) : $posts_query->the_post();
				$count ++;
				global $post;
				$return['posts'][ $count ] = $this->postInfo( $post->ID, [], $filters );
			endwhile;
			wp_reset_postdata();
		}

		return $return;
	}

	/**
	 * @param       $args
	 * @param null  $r_key
	 * @param null  $r_value
	 * @param array $fields
	 *
	 * @return array
	 */
	public function getPosts( $args, $r_key = null, $r_value = null, $fields = [] ) {
		$posts_query = get_posts( $args );
		$posts = [];
		if( ! empty( $posts_query ) ) {
			foreach( $posts_query as $key => $post ) {
				if( isset( $post->$r_key ) && isset( $post->$r_value ) ) {
					$posts[] = (array) $post;
				} else {
					$posts[] = $this->postInfo( $post->ID, [], $fields );
				}
			}
		}

		if( ! empty( $posts ) && ! is_null( $r_key ) && ! is_null( $r_value ) ) {
			$return = [];
			foreach( $posts as $k => $v ) {
				$return[ $v[ $r_key ] ] = $v[ $r_value ];
			}

			return $return;
		}

		return $posts;
	}

	/**
	 * @param        $meta_key
	 * @param string $meta_value
	 * @param string $output
	 * @param array  $return_fields
	 * @param int    $limit
	 *
	 * @return array|object|null
	 */
	public function getPostsByMetaKey( $meta_key, $meta_value = '', $output = 'meta', $return_fields = [], $limit = 0 ) {
		global $wpdb;
		$return = [];
		$limit = ($limit > 0) ? ' LIMIT ' . $limit . ' ' : '';
		if( $meta_value != '' ) {
			$posts = $wpdb->get_results( "SELECT * FROM $wpdb->postmeta WHERE meta_key = '{$meta_key}' AND meta_value = '{$meta_value}' {$limit}", 'ARRAY_A' );
		} else {
			$posts = $wpdb->get_results( "SELECT * FROM $wpdb->postmeta WHERE meta_key = '{$meta_key}' {$limit}", 'ARRAY_A' );
		}
		if( ! empty( $posts ) && $output == 'meta' ) {
			return $posts;
		}
		if( ! empty( $posts ) && $output != 'meta' ) {
			foreach( $posts as $key => $post_meta ) {
				$return[] = $this->postInfo( $post_meta['post_id'], [], $return_fields );
			}

			return $return;
		}
	}

	/**
	 * @param        $meta_key
	 * @param null   $meta_value
	 * @param string $output
	 * @param array  $return_fields
	 *
	 * @return array|object|void|null
	 */
	public function getPostByMetaKey( $meta_key, $meta_value = null, $output = 'data', $return_fields = [] ) {
		global $wpdb;
		if( ! is_null( $meta_value ) ) {
			$post_meta = $wpdb->get_row( "SELECT * FROM $wpdb->postmeta WHERE meta_key = '{$meta_key}' AND meta_value = '{$meta_value}'", 'ARRAY_A' );
		} else {
			$post_meta = $wpdb->get_row( "SELECT * FROM $wpdb->postmeta WHERE meta_key = '{$meta_key}'", 'ARRAY_A' );
		}

		$return = (is_null( $post_meta )) ? [] : $post_meta;

		if( $output == 'data' && ! is_null( $post_meta['post_id'] ) ) {
			$return = $this->postInfo( $post_meta['post_id'], [], $return_fields );
		}

		return $return;
	}

	/**
	 * @param array  $post_info
	 * @param string $taxonomy
	 * @param string $return
	 * @param string $html_sep
	 *
	 * @return array|mixed|string
	 */
	public function postTaxonomies( $post_info = [], $taxonomy = 'category', $return = 'array', $html_sep = ', ' ) {
		$data = $post_info['taxonomies'][ $taxonomy ];
		if( empty( $data ) ) {
			return [];
		}
		if( $return == 'array' ) {
			return $data;
		}
		if( $return == 'html' ) {
			$return = [];
			foreach( $data as $key => $value ) {
				$return[] = '<a href="' . $value['url'] . '" title="' . $value['name'] . '">' . $value['name'] . '</a>';
			}

			return implode( $html_sep, $return );
		}
	}

	/**
	 * @param string $taxonomy
	 * @param string $return
	 * @param string $html_sep
	 *
	 * @return array|string
	 */
	public function getTerms( $taxonomy = 'category', $return = 'array', $html_sep = ', ' ) {
		$terms = get_terms( $taxonomy );
		if( is_wp_error( $terms ) ) {
			return ['error' => $terms->get_error_message()];
		}
		$data = [];
		if( empty( $terms ) ) {
			return ($return == 'array') ? [] : '';
		}
		if( ! empty( $terms ) ) {
			$term_links = [];
			foreach( $terms as $key => $term ) {
				$data[ $term->term_id ]['term_id'] = $term->term_id;
				$data[ $term->term_id ]['name'] = $term->name;
				$data[ $term->term_id ]['slug'] = $term->slug;
				$data[ $term->term_id ]['term'] = $term;
				$url = get_term_link( $term );
				$data[ $term->term_id ]['url'] = $url;
				$term_links[] = '<a href="' . $url . '" title="' . $term->name . '">' . $term->name . '</a>';
			}

			return ($return == 'array') ? $data : implode( $html_sep, $term_links );
		}
	}

	/**
	 * @param        $term
	 * @param string $taxonomy
	 *
	 * @return array|WP_Error|WP_Term|null
	 */
	public function getTerm( $term, $taxonomy = 'category' ) {
		$term_info = get_term( $term, $taxonomy, ARRAY_A );
		$term_info['url'] = get_term_link( $term_info['term_id'] );

		return $term_info;
	}

	/**
	 * @param string $taxonomy
	 * @param array  $args
	 * @param string $key_item
	 * @param bool   $none
	 *
	 * @return array
	 */
	public function getTermsArray( $taxonomy = 'category', $args = [], $key_item = 'slug', $none = true ) {
		$atts = [
			'taxonomy' => $taxonomy,
			'hide_empty' => false,
		];
		if( ! empty( $args ) ) {
			foreach( $args as $arg_key => $arg_value ) {
				$atts[ $arg_key ] = $arg_value;
			}
		}

		$terms = get_terms( $atts );
		if( is_wp_error( $terms ) ) {
			return ['error' => $terms->get_error_message()];
		}

		if( $none ) {
			$data = ['' => 'None'];
		}

		if( empty( $terms ) ) {
			return [];
		}
		if( ! empty( $terms ) ) {
			foreach( $terms as $key => $term ) {
				$data[ $term->$key_item ] = $term->name;
			}
		}

		return $data;
	}

	/**
	 * @param       $post_id
	 * @param bool  $resize
	 * @param array $size
	 * @param bool  $crop
	 * @param int   $quality
	 *
	 * @return bool|string
	 */
	public function featuredImage( $post_id, $resize = true, $size = [1024, 1024], $crop = true, $quality = 100 ) {
		if( has_post_thumbnail( $post_id ) ) {
			$thumbnail = get_the_post_thumbnail( $post_id, 'full' );
			$thumbnail = $this->imageSrc( $thumbnail );
			if( $resize ) {
				$thumbnail = $this->imageResize( $thumbnail, $size, $crop, $quality );
			}
			if( ! is_null( $thumbnail ) ) {
				return $thumbnail;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * @param $post_id
	 *
	 * @return bool|string
	 */
	public function featuredImageUrl( $post_id ) {
		if( has_post_thumbnail( $post_id ) ) {
			$thumbnail = get_the_post_thumbnail( $post_id, 'full' );
			$thumbnail = $this->imageSrc( $thumbnail );
			if( ! is_null( $thumbnail ) ) {
				return $thumbnail;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * @param $post_id
	 *
	 * @return array
	 */
	public function getPostImages( $post_id ) {
		global $wpdb;
		$images = [];
		$query = $wpdb->get_results( "SELECT * FROM $wpdb->posts WHERE post_parent = '{$post_id}' AND post_type = 'attachment' AND post_mime_type LIKE 'image%'" );
		if( ! empty( $query ) ) {
			foreach( $query as $key => $value ) {
				$images[ $value->ID ] = $value->guid;
			}
		}

		return $images;
	}

	/**
	 * @param $var
	 *
	 * @return false|string
	 */
	public function getPostUrl( $var ) {
		global $wpdb;
		$postID = (is_numeric( $var )) ? $var : 'slug';
		$return = null;
		if( $postID != 'slug' ) {
			$post_data = $wpdb->get_row( "SELECT * FROM $wpdb->posts WHERE ID = '{$var}'", ARRAY_A );
		} else {
			$post_data = $wpdb->get_row( "SELECT * FROM $wpdb->posts WHERE post_name = '{$var}'", ARRAY_A );
		}

		return get_permalink( $post_data['ID'] );
	}

	/**
	 * @param      $post_id
	 * @param null $taxonomy
	 *
	 * @return null
	 */
	public function postTerms( $post_id, $taxonomy = null ) {
		$return = null;
		$post = get_post( $post_id );
		if( ! is_null( $taxonomy ) ) {
			$return[ $taxonomy ] = wp_get_post_terms( $post_id, $taxonomy );
		} else {
			$post_taxonomies = get_object_taxonomies( $post );
			foreach( $post_taxonomies as $key => $taxonomy ) {
				$return[ $taxonomy ] = wp_get_post_terms( $post_id, $taxonomy );
			}
		}

		return $return;
	}

	/**
	 * @param       $comment_id
	 * @param array $fields
	 * @param array $user_info_fields
	 * @param array $post_info_fields
	 *
	 * @return array
	 */
	public function commentInfo( $comment_id, $fields = ['comment_ID', 'comment_content', 'comment_content_filtered', 'comment_excerpt', 'comment_date', 'comment_post_ID', 'comment_type', 'user_id'], $user_info_fields = [], $post_info_fields = [] ) {
		global $wpdb;
		$comments_table_data = $wpdb->get_row( "SELECT * FROM $wpdb->comments WHERE comment_ID = '{$comment_id}'", 'ARRAY_A' );
		$comment_data = [];
		if( is_array( $comments_table_data ) && ! empty( $comments_table_data ) ) {
			foreach( $comments_table_data as $key => $value ) {
				$value = (is_serialized( $value )) ? unserialize( $value ) : $value;
				$comment_data[ $key ] = $value;
			}
			$comment_data['comment_excerpt'] = $comment_data['comment_content'];
		}

		$comment_meta = [];
		$comment_meta_data = $wpdb->get_results( "SELECT * FROM $wpdb->commentmeta WHERE comment_ID = '{$comment_id}'", 'ARRAY_A' );
		if( ! empty( $comment_meta_data ) ) {
			foreach( $comment_meta_data as $key => $meta ) {
				if( ! in_array( $meta['meta_key'], array_keys( $comment_data ) ) ) {
					$meta['meta_value'] = (is_serialized( $meta['meta_value'] )) ? @unserialize( $meta['meta_value'] ) : $meta['meta_value'];
					$comment_meta[ $meta['meta_key'] ] = $meta['meta_value'];
				}
			}
		}

		$comment_data = array_merge( $comment_data, $comment_meta );

		$comment_data['post_info'] = [];
		$comment_data['user_info'] = [];
		if( ! empty( $comment_data ) ) {
			$post_info_array = $this->postInfo( $comment_data['comment_post_ID'] );
			$user_info_array = $this->userInfo( $comment_data['comment_author_email'] );

			if( ! empty( $post_info_array ) ) {
				foreach( $post_info_fields as $post_info_fields_key => $post_info_field ) {
					$comment_data['post_info'][ $post_info_field ] = $post_info_array[ $post_info_field ];
				}
			}
			if( ! empty( $user_info_array ) ) {
				foreach( $user_info_fields as $user_info_fields_key => $user_info_field ) {
					$comment_data['user_info'][ $user_info_field ] = $user_info_array[ $user_info_field ];
				}
			}
		}

		$return = $comment_data;
		$return['comment_content_filtered'] = $return['comment_content'];
		//$return['comment_content'] = preg_replace( '/(<[^>]+) style=".*?"/i', '$1', $return['comment_content'] );
		//$return['comment_content'] = wpautop( $return['comment_content'], true );
		//$return['comment_content_filtered'] = htmlspecialchars( $return['comment_content_filtered'], ENT_HTML5 );
		//$return['comment_content_filtered'] = wp_strip_all_tags( $return['comment_content'], false );

		if( empty( $fields ) ) {
			return $return;
		} else {
			$return_fields = [];
			foreach( $fields as $f_key => $f_value ) {
				$return_fields[ $f_value ] = $return[ $f_value ];
			}

			return $return_fields;
		}
	}

	/**
	 * @param        $meta_key
	 * @param string $meta_value
	 * @param string $output
	 *
	 * @return array|object|null
	 */
	public function getCommentsByMetaKey( $meta_key, $meta_value = '', $output = 'meta' ) {
		global $wpdb;
		$return = [];
		if( $meta_value != '' ) {
			$comments = $wpdb->get_results( "SELECT * FROM $wpdb->commentmeta WHERE meta_key = '{$meta_key}' AND meta_value = '{$meta_value}'", 'ARRAY_A' );
		} else {
			$comments = $wpdb->get_results( "SELECT * FROM $wpdb->commentmeta WHERE meta_key = '{$meta_key}'", 'ARRAY_A' );
		}
		if( ! empty( $comments ) && $output == 'meta' ) {
			return $comments;
		}
		if( ! empty( $comments ) && $output != 'meta' ) {
			foreach( $comments as $key => $comment_meta ) {
				$return[] = $this->commentInfo( $comment_meta['comment_id'], [], [], [] );
			}

			return $return;
		}
	}

	/**
	 * @param $post_id
	 * @param $string
	 *
	 * @return mixed
	 */
	public function processPostVariables( $post_id, $string ) {
		$post_info = $this->postInfo( $post_id );
		if( ! empty( $post_info ) ) {
			foreach( $post_info as $key => $value ) {
				$value = (is_serialized( $value )) ? unserialize( $value ) : $value;
				if( ! $this->containsStrArray( $value ) ) {
					$value = (is_array( $value )) ? implode( '|', $value ) : $value;
					$string = str_replace( '%%' . $key . '%%', $value, $string );
				}
			}
		}

		return $string;
	}

	/**
	 * @param null $date
	 *
	 * @return string
	 */
	public function generatePostsDayLink( $date = null ) {
		$timestamp = time();
		if( ! is_null( $date ) ) {
			$timestamp = strtotime( $date );
		}

		return get_day_link( date( 'Y', $timestamp ), date( 'm', $timestamp ), date( 'd', $timestamp ) );
	}

	/**
	 * @param string $taxonomy
	 * @param array  $params
	 *
	 * @return array
	 */
	public function getTaxonomies( $taxonomy = 'category', $params = [] ) {
		$taxonomies = [$taxonomy];
		$args = [
			'orderby' => 'name',
			'order' => 'ASC',
			'hide_empty' => false,
			'include' => [],
			'exclude' => [],
			'exclude_tree' => [],
			'number' => '',
			'offset' => '',
			'fields' => 'all',
			'name' => '',
			'slug' => '',
			'hierarchical' => false,
			'search' => '',
			'name__like' => '',
			'description__like' => '',
			'pad_counts' => false,
			'get' => '',
			'child_of' => 0,
			'parent' => 0,
			'childless' => false,
			'cache_domain' => 'core',
			'update_term_meta_cache' => true,
			'meta_query' => '',
		];

		if( ! empty( $params ) ) {
			foreach( $params as $key => $value ) {
				$args[ $key ] = $value;
			}
		}

		$terms = get_terms( $taxonomies, $args );
		$options = [];
		foreach( $terms as $key => $term ) {
			$term_array = (array) $term;
			$options[ $key ] = $term_array;
			$options[ $key ]['permalink'] = get_term_link( $term, $taxonomy );
		}

		return $options;
	}

	/**
	 * @param string $taxonomy
	 * @param array  $params
	 * @param string $output
	 *
	 * @return array
	 */
	public function getTaxonomyArray( $taxonomy = 'category', $params = [], $output = 'name' ) {
		$taxonomies = [$taxonomy];
		$args = [
			'orderby' => 'name',
			'order' => 'ASC',
			'hide_empty' => false,
			'include' => [],
			'exclude' => [],
			'exclude_tree' => [],
			'number' => '',
			'offset' => '',
			'fields' => 'all',
			'name' => '',
			'slug' => '',
			'hierarchical' => false,
			'search' => '',
			'name__like' => '',
			'description__like' => '',
			'pad_counts' => false,
			'get' => '',
			'child_of' => 0,
			'parent' => 0,
			'childless' => false,
			'cache_domain' => 'core',
			'update_term_meta_cache' => true,
			'meta_query' => '',
		];

		if( ! empty( $params ) ) {
			foreach( $params as $key => $value ) {
				$args[ $key ] = $value;
			}
		}

		$terms = get_terms( $taxonomies, $args );

		if( $output == 'links' ) {
			$links = [];
			foreach( $terms as $term ) {
				$links[ $term->slug ] = '<a href="' . get_term_link( $term, $taxonomy ) . '" title="' . $term->name . '">' . $term->name . '</a>';
			}

			return $links;
		}

		$options = [];
		if( ! is_wp_error( $terms ) ) {
			foreach( $terms as $term ) {
				$options[ $term->slug ] = $term->name;
			}
		}

		return $options;
	}

	/**
	 * @param array $tax_args
	 * @param array $terms_args
	 *
	 * @return array
	 */
	public function taxonomyTermsArray( $tax_args = [], $terms_args = [] ) {
		$return = [];
		$taxonomies_array = [];
		$taxonomies = get_taxonomies( $tax_args );
		if( ! empty( $taxonomies ) ) {
			foreach( $taxonomies as $tax_key => $value ) {
				$taxonomies_array[] = $tax_key;
			}
		}

		if( empty( $taxonomies_array ) ) {
			return [];
		}

		$terms = get_terms( $taxonomies_array, $terms_args );
		if( empty( $terms ) ) {
			return [];
		}

		foreach( $terms as $key => $term ) {
			$t_key = $term->slug . '@' . $term->taxonomy;
			$t_val = $term->slug . ' (' . $term->taxonomy . ')';

			$return[ $t_key ] = $t_val;
		}

		return $return;
	}

	/**
	 * @param $var
	 *
	 * @return array
	 */
	public function getTaxonomiesOption( $var ) {
		$return = [];
		$array = explode( '|', $var );
		foreach( $array as $array_key => $array_value ) {
			$terms_array = explode( '@', $array_value );
			$term_slug = $terms_array[0];
			$taxonomy = $terms_array[1];
			$term = get_term_by( 'slug', $term_slug, $taxonomy );
			$term_link = get_term_link( $term );
			$return[ $term_slug ]['term_id'] = $term->term_id;
			$return[ $term_slug ]['term_name'] = $term->name;
			$return[ $term_slug ]['term_url'] = $term_link;
			$return[ $term_slug ]['term_slug'] = $term->slug;
			$return[ $term_slug ]['term_permalink'] = '<a href="' . $term_link . '" title="' . $term->name . '">' . $term->name . '</a>';
			$return[ $term_slug ]['term_data'] = $term;
		}

		return $return;
	}

	/**
	 * @param        $post_id
	 * @param string $image_url
	 * @param string $image_name_with_extension
	 * @param bool   $replace
	 *
	 * @return array|bool
	 */
	public function setPostFeaturedImageFromUrl( $post_id, $image_url = '', $image_name_with_extension = 'featured-image.png', $replace = false ) {

		//If a post ID is not an integer or post ID is less than equal to 0
		if( $post_id <= 0 ) {
			//Bail Out!
			return false;
		}

		//If image URL is empty, bail out!
		if( '' == $image_url ) {
			return false;
		}

		//Check if an image is a valid image URL
		$url = $image_url;
		if( ! is_ssl() ) {
			$url = str_replace( "https://", "http://", $url );
		}
		/*if( ! getimagesize( $url ) ) {
			$result = [
				'error' => __(
					'Not a valid image. Please provide a valid image URL link.',
					'cjwpbldr'
				)
			];

			return $result;
		}*/

		if( $replace ) {
			$attachment_id = get_post_thumbnail_id( $post_id );
			if( $attachment_id ) {
				wp_delete_attachment( $attachment_id, true );
			}
		}

		$arrContextOptions = [
			"ssl" => [
				"verify_peer" => false,
				"verify_peer_name" => false,
			],
		];

		// Add Featured Image to Post
		$upload_dir = wp_upload_dir(); // Set upload folder
		$image_data = file_get_contents( $url, false, stream_context_create( $arrContextOptions ) ); // Get image data
		$unique_file_name = wp_unique_filename( $upload_dir['path'], $image_name_with_extension ); // Generate unique name
		$filename = basename( $unique_file_name ); // Create image file name

		// Check folder permission and define file location
		if( wp_mkdir_p( $upload_dir['path'] ) ) {
			$file = $upload_dir['path'] . '/' . $filename;
		} else {
			$file = $upload_dir['basedir'] . '/' . $filename;
		}

		// Create the image file on the server
		file_put_contents( $file, $image_data );

		// Check image file type
		$wp_filetype = wp_check_filetype( $filename, null );

		// Set attachment data
		$attachment = [
			'post_mime_type' => $wp_filetype['type'],
			'post_title' => sanitize_file_name( $filename ),
			'post_content' => '',
			'post_status' => 'inherit'
		];

		// Create the attachment
		$attach_id = wp_insert_attachment( $attachment, $file, $post_id );

		//If image is saved as an attachment successfully
		if( $attach_id ) {
			// Include image.php
			require_once(ABSPATH . 'wp-admin/includes/image.php');

			// Define attachment metadata
			$attach_data = wp_generate_attachment_metadata( $attach_id, $file );

			// Assign metadata to attachment
			wp_update_attachment_metadata( $attach_id, $attach_data );

			// And finally assign featured image to post
			$image_post_meta_id = set_post_thumbnail( $post_id, $attach_id );

			//If image is set as featured image
			if( $image_post_meta_id ) {

				//Return the attachment id and post meta attachment id as an array
				$result = [$attach_id => $image_post_meta_id];
			} else {

				//Return an error if image is saved as attachment but could not be attached
				//as a featured image of the post
				$result = [
					'error' => __(
						'Image saved as attachment but could not be saved as an featured image. Please try again later',
						'cjwpbldr'
					)
				];
			}
		} else {

			//return an error if the image is saved not saved attached in the first place
			$result = [
				'error' => __(
					'Image could not be saved as an attachment. Please try again later.',
					'cjwpbldr'
				)
			];
		}

		return $result;
	}

	/**
	 * @param string $key_value
	 * @param string $name_value
	 * @param string $post_type
	 * @param array  $args
	 *
	 * @return array
	 */
	public function postsArray( $key_value = 'ID', $name_value = 'post_title', $post_type = 'post', $args = [] ) {
		$query_args = [
			'orderby' => 'post_date',
			'posts_per_page' => - 1,
			'post_type' => $post_type
		];
		if( ! empty( $args ) ) {
			foreach( $args as $key => $arg ) {
				$query_args[ $key ] = $arg;
			}
		}
		$posts = get_posts( $query_args );
		$return = [
			'' => __( 'None', 'wp-builder-locale' )
		];
		if( is_array( $posts ) && ! empty( $posts ) ) {
			foreach( $posts as $key => $post ) {
				$post_info = $this->postInfo( $post->ID );
				$return[ $post_info[ $key_value ] ] = $post_info[ $name_value ];
			}
		}

		return $return;
	}

	/**
	 * @param string $key_value
	 * @param string $name_value
	 * @param string $post_type
	 * @param array  $args
	 *
	 * @return array
	 */
	public function pagesArray( $key_value = 'ID', $name_value = 'post_title', $post_type = 'page', $args = [] ) {
		$query_args = [
			'orderby' => 'post_date',
			'posts_per_page' => - 1,
			'post_type' => $post_type
		];
		if( ! empty( $args ) ) {
			foreach( $args as $key => $arg ) {
				$query_args[ $key ] = $arg;
			}
		}
		$posts = get_posts( $query_args );
		$return = [
			'' => __( 'None', 'wp-builder-locale' )
		];
		if( is_array( $posts ) && ! empty( $posts ) ) {
			foreach( $posts as $key => $post ) {
				$post_info = $this->postInfo( $post->ID );
				$return[ $post_info[ $key_value ] ] = $post_info[ $name_value ];
			}
		}

		return $return;
	}

	/**
	 * @return array
	 */
	public function postTypesArray() {
		$post_types = get_post_types();
		$return = [];
		if( is_array( $post_types ) && ! empty( $post_types ) ) {
			if( ($key = array_search( 'cjwpbldr-ui-blocks', $post_types )) !== false ) {
				unset( $post_types[ $key ] );
			}
			if( ($key = array_search( 'cjwpbldr-templates', $post_types )) !== false ) {
				unset( $post_types[ $key ] );
			}
			if( ($key = array_search( 'cjwpbldr-portfolio', $post_types )) !== false ) {
				unset( $post_types[ $key ] );
			}
			foreach( $post_types as $key => $value ) {
				$return[ $post_types[ $key ] ] = $post_types[ $value ];
			}
		}

		return $return;
	}

	/**
	 * @param string $key_value
	 * @param string $name_value
	 * @param string $post_type
	 *
	 * @return array
	 */
	public function categoriesArray( $key_value = 'cat_ID', $name_value = 'cat_name', $post_type = 'post' ) {
		$return = [];
		$categories = get_categories( ['type' => $post_type, 'order' => 'ASC'] );
		if( is_array( $categories ) && ! empty( $categories ) ) {
			foreach( $categories as $key => $category ) {
				$return[ $category->$key_value ] = $category->$name_value;
			}
		}

		return $return;
	}

	/**
	 * @return array
	 */
	public function taxonomiesArray() {
		$return = [];
		$taxonomies = get_taxonomies();
		if( is_array( $taxonomies ) && ! empty( $taxonomies ) ) {
			if( ($key = array_search( 'cjwpbldr-blocks-group', $taxonomies )) !== false ) {
				unset( $taxonomies[ $key ] );
			}
			foreach( $taxonomies as $key => $taxonomy ) {
				$return[ $taxonomies[ $key ] ] = $taxonomies[ $taxonomy ];
			}
		}

		return $return;
	}

	/**
	 * @param string $key_value
	 * @param string $name_value
	 *
	 * @return array
	 */
	public function tagsArray( $key_value = 'term_id', $name_value = 'name' ) {
		$return = [];
		$tags = get_tags();
		if( is_array( $tags ) && ! empty( $tags ) ) {
			foreach( $tags as $key => $tag ) {
				$return[ $tag->$key_value ] = $tag->$name_value;
			}
		}

		return $return;
	}

	/**
	 * @param string $key_value
	 * @param string $name_value
	 * @param string $taxonomy
	 * @param array  $args
	 *
	 * @return array
	 */
	public function termsArray( $key_value = 'term_id', $name_value = 'name', $taxonomy = '', $args = [] ) {
		$return = [];

		if( $taxonomy != '' ) {
			$terms = get_terms( $taxonomy, $args );
		} else {
			$terms = get_terms();
		}
		if( is_array( $terms ) && ! empty( $terms ) ) {
			foreach( $terms as $key => $term ) {
				$return[ $term->$key_value ] = $term->$name_value;
			}
		}

		return $return;
	}

}