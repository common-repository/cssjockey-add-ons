<?php
if( ! class_exists( 'cjwpbldr_theme_setup' ) ) {
	class cjwpbldr_theme_setup {
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
			$this->helpers->isTheme();
			add_action( 'after_setup_theme', [$this, 'themeSetup'] );
			add_action( 'widgets_init', [$this, 'sidebarSetup'] );
			add_action( 'admin_notices', [$this, 'checkAssignedTemplates'] );
			add_action( 'init', [$this, 'addEditorStyles'] );
			add_filter( 'excerpt_more', [$this, 'excerptMore'] );
			add_filter( 'get_the_excerpt', [$this, 'getTheExcerpt'] );
			apply_filters( 'comment_form_default_fields', [$this, 'commentFormFields'] );
			add_filter( 'the_content_more_link', [$this, 'theContentReadMoreLink'] );

			//add_filter( 'post_thumbnail_html', 'removeWidthAttribute', 10 );
			//add_filter( 'image_send_to_editor', 'removeWidthAttribute', 10 );

		}

		public function themeSetup() {

			if( ! $this->helpers->isTheme() ) {
				return false;
			}

			// Add default posts and comments RSS feed links to head.
			add_theme_support( 'automatic-feed-links' );

			/*
			 * Let WordPress manage the document title.
			 * By adding theme support, we declare that this theme does not use a
			 * hard-coded <title> tag in the document head, and expect WordPress to
			 * provide it for us.
			 */
			add_theme_support( 'title-tag' );

			/*
			 * Enable support for Post Thumbnails on posts and pages.
			 *
			 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
			 */
			add_theme_support( 'post-thumbnails' );
			set_post_thumbnail_size( 1568, 9999 );

			// This theme uses wp_nav_menu() in two locations.
			register_nav_menus(
				[//'test-nav-menu' => __( 'Test Navigation Menu', 'wp-builder-locale' ),
				]
			);

			/*
			 * Switch default core markup for search form, comment form, and comments
			 * to output valid HTML5.
			 */
			add_theme_support(
				'html5',
				[
					'search-form',
					'comment-form',
					'comment-list',
					'gallery',
					'caption',
				]
			);

			// Add support for editor styles.
			add_theme_support( 'editor-styles' );

			// Enqueue editor styles.
			add_editor_style( 'style-editor.css' );

			// Add support for responsive embedded content.
			add_theme_support( 'responsive-embeds' );
		}

		public function sidebarSetup() {
			if( ! $this->helpers->isTheme() ) {
				return false;
			}
			$sidebars['cjwpbldr-default-sidebar'] = [
				'name' => __( 'Default Sidebar', 'wp-builder-locale' ),
				'id' => 'cjwpbldr-default-sidebar',
				'description' => __( 'Add widgets here to appear in default sidebar.', 'wp-builder-locale' ),
				'before_widget' => '<section id="%1$s" class="widget cj-widget %2$s">',
				'after_widget' => '</section>',
				'before_title' => '<h2 class="widget-title">',
				'after_title' => '</h2>',
			];
			$sidebars['cjwpbldr-blog-sidebar'] = [
				'name' => __( 'Blog Sidebar', 'wp-builder-locale' ),
				'id' => 'cjwpbldr-blog-sidebar',
				'description' => __( 'Add widgets here to appear in blog sidebar.', 'wp-builder-locale' ),
				'before_widget' => '<section id="%1$s" class="widget cj-widget %2$s">',
				'after_widget' => '</section>',
				'before_title' => '<h2 class="widget-title">',
				'after_title' => '</h2>',
			];
			$sidebars['cjwpbldr-page-sidebar'] = [
				'name' => __( 'Page Sidebar', 'wp-builder-locale' ),
				'id' => 'cjwpbldr-page-sidebar',
				'description' => __( 'Add widgets here to appear in page sidebar.', 'wp-builder-locale' ),
				'before_widget' => '<section id="%1$s" class="widget cj-widget %2$s">',
				'after_widget' => '</section>',
				'before_title' => '<h2 class="widget-title">',
				'after_title' => '</h2>',
			];
			foreach( $sidebars as $key => $sidebar ) {
				register_sidebar( $sidebar );
			}
		}

		public function checkAssignedTemplates() {
			global $wpdb;

			if( ! $this->helpers->is_theme ) {
				return false;
			}
			$saved_templates = [];
			$post_meta_templates = $wpdb->get_results( "SELECT * FROM $wpdb->postmeta WHERE meta_key LIKE '_cjwpbldr_use%'", 'ARRAY_A' );
			if( is_array( $post_meta_templates ) && ! empty( $post_meta_templates ) ) {
				foreach( $post_meta_templates as $key => $meta ) {
					$post_info = $this->helpers->postInfo( $meta['post_id'] );

					if( ! empty( $post_info ) && $meta['meta_value'] == 'on' && $post_info['post_status'] == 'publish' ) {
						$saved_templates[ $meta['meta_key'] ] = $meta['meta_key'];
					}
				}
			}
			// custom homepage
			$custom_homepage = get_option( 'page_on_front' );
			if( $custom_homepage > 0 ) {
				$required_templates['_cjwpbldr_use_template_homepage'] = '_cjwpbldr_use_template_homepage';
			}

			// custom blog page
			$custom_blog_page = get_option( 'page_for_posts' );
			if( $custom_homepage > 0 && $custom_blog_page > 0 ) {
				$required_templates['_cjwpbldr_use_template_blog_page'] = '_cjwpbldr_use_template_blog_page';
			}

			// normal blog page
			if( $custom_homepage == 0 && $custom_blog_page == 0 ) {
				$required_templates['_cjwpbldr_use_template_blog_page'] = '_cjwpbldr_use_template_blog_page';
			}

			$public_taxonomies = get_taxonomies( [
				'public' => true
			] );

			// taxonomies
			if( ! empty( $public_taxonomies ) ) {
				foreach( $public_taxonomies as $public_taxonomy_key => $public_taxonomy ) {
					$taxonomy_info = get_taxonomy( $public_taxonomy );
					if( $taxonomy_info->show_ui && $taxonomy_info->publicly_queryable ) {
						$template_key = '_cjwpbldr_use_template_tax_' . $public_taxonomy;
						$required_templates[ $template_key ] = $template_key;
					}
				}
			}

			// post type
			$exclude_post_types = ['post', 'attachment'];
			$registered_post_types = get_post_types( [
				'public' => true,
				'publicly_queryable' => true,
			] );

			if( ! empty( $registered_post_types ) ) {
				foreach( $registered_post_types as $key => $post_type_name ) {
					if( ! in_array( $post_type_name, $exclude_post_types ) ) {
						$post_type_object = get_post_type_object( $post_type_name );
						if( $post_type_object->publicly_queryable && $post_type_object->show_ui == 1 ) {
							$template_key = '_cjwpbldr_use_template_cpt_' . $post_type_name . '_singular';
							$required_templates[ $template_key ] = $template_key;
							$template_key = '_cjwpbldr_use_template_cpt_' . $post_type_name . '_archive';
							$required_templates[ $template_key ] = $template_key;
						}
					}
				}
			}

			$required_templates['_cjwpbldr_use_template_all_pages'] = '_cjwpbldr_use_template_all_pages';
			$required_templates['_cjwpbldr_use_template_all_posts'] = '_cjwpbldr_use_template_all_posts';
			$required_templates['_cjwpbldr_use_template_attachment'] = '_cjwpbldr_use_template_attachment';
			$required_templates['_cjwpbldr_use_template_author'] = '_cjwpbldr_use_template_author';
			$required_templates['_cjwpbldr_use_template_archives'] = '_cjwpbldr_use_template_archives';
			$required_templates['_cjwpbldr_use_template_search_page'] = '_cjwpbldr_use_template_search_page';
			$required_templates['_cjwpbldr_use_template_404_page'] = '_cjwpbldr_use_template_404_page';
			$templates_not_set_for = array_diff( $saved_templates, $required_templates );
			if( empty( $templates_not_set_for ) ) {
				$templates_not_set_for = array_diff( $required_templates, $saved_templates );
			}
			if( count( $templates_not_set_for ) > 0 ) {
				if( isset( $_GET['cjwpbldr-action'] ) && $_GET['cjwpbldr-action'] == 'auto-generate-templates' ) {
					foreach( $templates_not_set_for as $key => $value ) {
						$meta_key = $value;
						$value = str_replace( '_cjwpbldr_use_template_', '', $value );
						$value = str_replace( 'wpb-', '', $value );
						$value = str_replace( ['_', '-'], ' ', $value );
						$value = str_replace( 'cpt', '', $value );
						$value = str_replace( 'tax', '', $value );
						$post_title = ucwords( $value ) . ' - Template';
						$template_data = [
							'post_title' => $post_title,
							'post_name' => sanitize_title( $post_title ),
							'post_content' => sanitize_title( $post_title ),
							'post_type' => 'cjwpbldr-templates',
							'post_status' => 'publish',
							'_cjwpbldr_ui_blocks' => [],
							'_cjwpbldr_ui_blocks_preview' => [],
						];
						$template_data[ $meta_key ] = 'on';
						$this->helpers->createPost( $template_data );
					}
					$edit_templates_link = admin_url( 'edit.php?post_type=cjwpbldr-templates' );
					wp_redirect( $edit_templates_link );
					die();
				}

				$plugin_name = $this->helpers->config( 'plugin-info', 'name' );
				$template_names = [];
				foreach( $templates_not_set_for as $key => $value ) {
					$value = str_replace( '_cjwpbldr_use_template_', '', $value );
					$value = str_replace( 'tax_', ' Taxonomy ', $value );
					$value = str_replace( 'cpt_', ' Post Type ', $value );
					$value = str_replace( 'cat_', '', $value );
					//$value = str_replace( '-cat', ' Archive', $value );
					$value = str_replace( 'wpb-', '', $value );
					$value = str_replace( ['_', '-'], ' ', $value );
					//$value = str_replace( ['_cjwpbldr_use_template_', '_', '-', 'tax', 'cpt', 'cjwpbldr', 'wpb'], ' ', $value );
					$template_names[ $value ] = '<span class="cj-text-camel cj-text-bold">' . trim( $value ) . '</span>';
				}
				$template_names = implode( ', ', $template_names );
				$message = sprintf( __( 'Templates missing for :<br>%s', 'wp-builder-locale' ), $template_names );
				$auto_generate_template_link = $this->helpers->callbackUrl( 'core-welcome', true ) . 'cjwpbldr-action=auto-generate-templates';
				$set_default_theme_link = $this->helpers->callbackUrl( 'core-cloud', true ) . 'show=themes';
				$message .= '<p class="cj-mt-10">';
				$message .= '<a class="cj-button cj-is-small cj-is-primary" href="' . $auto_generate_template_link . '">' . __( 'Create and Assign Blank Templates', 'wp-builder-locale' ) . '</a>';
				$message .= '<span class="cj-inline-block cj-mh-8 cj-mv-4 cj-text-italic">or</span>';
				$message .= '<a class="cj-button cj-is-small cj-is-primary" href="' . $set_default_theme_link . '">' . __( 'Download and Set Pre-built Theme', 'wp-builder-locale' ) . '</a>';
				$message .= '</p>';
				$this->helpers->adminNotice( 'cjwpbldr-missing-templates', 'error', $message, $plugin_name, false );
			}
		}

		public function addEditorStyles() {
			if( ! $this->helpers->isTheme() ) {
				return false;
			}
			add_editor_style( 'css/editor-style.css' );
		}

		public function excerptMore( $output ) {
			return '...';
		}

		public function getTheExcerpt( $output ) {
			if( has_excerpt() && ! is_attachment() ) {
				$output .= '';
			}

			return $output;
		}

		public function commentFormFields( $fields ) {
			return $fields;
		}

		public function theContentReadMoreLink() {
			return '<a class="more-link" title="' . get_the_title() . '" href="' . get_permalink() . '">' . __( 'Continue reading', 'wp-builder-locale' ) . '</a>';
		}

		public function removeWidthAttribute( $html ) {
			if( $this->helpers->isTheme() ) {
				$html = preg_replace( '/(width|height)="\d*"\s/', "", $html );
			}

			return $html;
		}
	}

	cjwpbldr_theme_setup::getInstance();
}